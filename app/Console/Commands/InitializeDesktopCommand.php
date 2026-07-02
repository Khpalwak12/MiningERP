<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\DesktopApplication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InitializeDesktopCommand extends Command
{
    protected $signature = 'desktop:initialize';

    protected $description = 'Prepare the standalone desktop application (database, storage, seed data)';

    public function handle(): int
    {
        if (! DesktopApplication::isDesktop()) {
            $this->error('Desktop mode is not enabled.');

            return self::FAILURE;
        }

        $dataPath = DesktopApplication::dataPath();

        if (! $dataPath) {
            $this->error('MININGERP_DATA_PATH is not configured.');

            return self::FAILURE;
        }

        $this->ensureDirectories($dataPath);
        $this->ensureEnvironmentFile($dataPath);
        $this->reloadDesktopEnvironment($dataPath);
        $this->ensureDatabaseFile($dataPath);
        $this->ensurePublicStorageLink();

        Artisan::call('migrate', ['--force' => true]);
        $this->line(trim(Artisan::output()));

        $this->clearDesktopBootstrapCache($dataPath);

        if (User::query()->count() === 0) {
            Artisan::call('db:seed', ['--force' => true]);
            $this->line(trim(Artisan::output()));
        }

        $this->info('Desktop application initialized.');

        return self::SUCCESS;
    }

    private function ensureDirectories(string $dataPath): void
    {
        $directories = [
            $dataPath,
            $dataPath.'/backups',
            $dataPath.'/exports',
            $dataPath.'/logs',
            $dataPath.'/uploads',
            $dataPath.'/tmp',
            $dataPath.'/bootstrap/cache',
            $dataPath.'/storage/app/public',
            $dataPath.'/storage/app/private',
            $dataPath.'/storage/framework/cache',
            $dataPath.'/storage/framework/sessions',
            $dataPath.'/storage/framework/views',
            $dataPath.'/storage/logs',
        ];

        foreach ($directories as $directory) {
            if (! File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
        }
    }

    private function ensureEnvironmentFile(string $dataPath): void
    {
        $envPath = $dataPath.'/.env';

        if (File::exists($envPath)) {
            return;
        }

        $templatePath = base_path('.env.desktop');

        if (! File::exists($templatePath)) {
            throw new \RuntimeException('Missing desktop environment template.');
        }

        $contents = File::get($templatePath);
        $contents = str_replace('{{APP_KEY}}', 'base64:'.base64_encode(random_bytes(32)), $contents);
        $contents = str_replace('{{DATA_PATH}}', str_replace('\\', '/', $dataPath), $contents);
        $contents = str_replace('{{DB_DATABASE}}', str_replace('\\', '/', $dataPath.'/database.sqlite'), $contents);
        $contents = str_replace('{{APP_URL}}', rtrim((string) env('APP_URL', 'http://127.0.0.1:19647'), '/'), $contents);

        File::put($envPath, $contents);
    }

    private function ensureDatabaseFile(string $dataPath): void
    {
        $database = $dataPath.'/database.sqlite';

        if (! File::exists($database)) {
            File::put($database, '');
        }
    }

    private function reloadDesktopEnvironment(string $dataPath): void
    {
        $envPath = $dataPath.'/.env';

        if (! File::exists($envPath)) {
            return;
        }

        \Dotenv\Dotenv::createImmutable($dataPath, '.env')->safeLoad();

        foreach ($_ENV as $key => $value) {
            if (! is_string($value)) {
                continue;
            }

            putenv($key.'='.$value);
            $_SERVER[$key] = $value;
        }

        config([
            'app.key' => env('APP_KEY'),
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => env('DB_DATABASE'),
        ]);
    }

    private function clearDesktopBootstrapCache(string $dataPath): void
    {
        $cacheDirectory = $dataPath.'/bootstrap/cache';

        if (! File::isDirectory($cacheDirectory)) {
            return;
        }

        foreach (File::files($cacheDirectory) as $file) {
            File::delete($file->getPathname());
        }
    }

    private function ensurePublicStorageLink(): void
    {
        if (! DesktopApplication::isDesktop()) {
            return;
        }

        $link = public_path('storage');
        $target = storage_path('app/public');

        if (File::exists($link) || ! is_writable(dirname($link))) {
            return;
        }

        File::ensureDirectoryExists(dirname($link));

        if (PHP_OS_FAMILY === 'Windows') {
            $command = sprintf(
                'cmd /c mklink /J %s %s',
                escapeshellarg($link),
                escapeshellarg($target)
            );
            exec($command, $output, $exitCode);

            if ($exitCode !== 0 && ! File::exists($link)) {
                File::copyDirectory($target, $link);
            }

            return;
        }

        if (function_exists('symlink')) {
            @symlink($target, $link);
        }
    }
}
