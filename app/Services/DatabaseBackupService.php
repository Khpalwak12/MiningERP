<?php

namespace App\Services;

use App\Support\DesktopApplication;
use App\Support\JalaliDate;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;
use ZipArchive;

class DatabaseBackupService
{
    private ?string $resolvedMysqlBin = null;

    public function __construct(private MysqlPhpBackupService $mysqlPhpBackup) {}

    public function backupPath(): string
    {
        $path = (string) config('erp.backup.path');

        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }

        return $path;
    }

    public function list(): array
    {
        return collect(File::files($this->backupPath()))
            ->filter(fn (\SplFileInfo $file) => strtolower($file->getExtension()) === 'zip')
            ->sortByDesc(fn (\SplFileInfo $file) => $file->getMTime())
            ->values()
            ->map(fn (\SplFileInfo $file) => $this->describeBackup($file->getPathname()))
            ->all();
    }

    public function create(): array
    {
        $filename = 'mining-erp-backup_'.now()->format('Ymd_His').'.zip';
        $fullPath = $this->backupPath().DIRECTORY_SEPARATOR.$filename;
        $tempDir = $this->makeTempDirectory();

        try {
            $manifest = [
                'app' => config('app.name'),
                'created_at' => now()->toIso8601String(),
                'database_driver' => config('database.default'),
                'laravel_version' => app()->version(),
            ];

            $this->exportDatabase($tempDir);
            $this->exportPublicFiles($tempDir);
            $this->exportDesktopConfiguration($tempDir);
            File::put($tempDir.'/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->createZip($tempDir, $fullPath);
        } finally {
            File::deleteDirectory($tempDir);
        }

        return $this->describeBackup($fullPath);
    }

    public function resolveBackupFile(string $filename): string
    {
        $safeName = basename($filename);
        $path = $this->backupPath().DIRECTORY_SEPARATOR.$safeName;

        if (! File::exists($path)) {
            throw new RuntimeException(__('erp.backup.not_found'));
        }

        return $path;
    }

    public function delete(string $filename): void
    {
        File::delete($this->resolveBackupFile($filename));
    }

    public function restore(string $zipPath): void
    {
        if (! File::exists($zipPath)) {
            throw new RuntimeException(__('erp.backup.not_found'));
        }

        $tempDir = $this->makeTempDirectory();
        $wasDown = app()->isDownForMaintenance();

        try {
            if (! $wasDown) {
                Artisan::call('down', ['--retry' => 60]);
            }

            $this->extractZip($zipPath, $tempDir);
            $manifest = $this->readManifest($tempDir.'/manifest.json');
            $this->assertCompatibleDriver($manifest);
            $this->importDatabase($tempDir, $manifest['database_driver']);
            $this->importPublicFiles($tempDir);
            $this->importDesktopConfiguration($tempDir);

            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
        } finally {
            File::deleteDirectory($tempDir);

            if (! $wasDown) {
                Artisan::call('up');
            }
        }
    }

    private function describeBackup(string $path): array
    {
        $timestamp = filemtime($path) ?: time();

        return [
            'filename' => basename($path),
            'size' => File::size($path),
            'size_human' => $this->formatBytes((int) File::size($path)),
            'created_at' => date('c', $timestamp),
            'created_at_shamsi' => JalaliDate::fromGregorian(date('Y-m-d', $timestamp)),
        ];
    }

    private function makeTempDirectory(): string
    {
        $tempDir = storage_path('app/backup-temp/'.Str::uuid());
        File::makeDirectory($tempDir, 0755, true);

        return $tempDir;
    }

    private function exportDatabase(string $tempDir): void
    {
        File::makeDirectory($tempDir.'/database', 0755, true);

        match (config('database.default')) {
            'sqlite' => $this->exportSqlite($tempDir.'/database/database.sqlite'),
            'mysql', 'mariadb' => $this->exportMysql($tempDir.'/database/dump.sql'),
            default => throw new RuntimeException(__('erp.backup.unsupported_driver')),
        };
    }

    private function sqliteDatabasePath(): string
    {
        $path = (string) config('database.connections.sqlite.database');

        if ($path === ':memory:') {
            throw new RuntimeException(__('erp.backup.memory_not_supported'));
        }

        if (! str_starts_with($path, DIRECTORY_SEPARATOR) && ! preg_match('/^[A-Za-z]:[\\\\\\/]/', $path)) {
            $path = database_path($path);
        }

        return $path;
    }

    private function exportSqlite(string $target): void
    {
        $source = $this->sqliteDatabasePath();

        if (! File::exists($source)) {
            throw new RuntimeException(__('erp.backup.export_failed'));
        }

        try {
            DB::connection()->getPdo()->exec('PRAGMA wal_checkpoint(FULL)');
        } catch (\Throwable) {
            // Ignore when WAL mode is not enabled.
        }

        if (! File::copy($source, $target)) {
            throw new RuntimeException(__('erp.backup.export_failed'));
        }
    }

    private function exportMysql(string $target): void
    {
        if ($this->shouldUsePhpMysqlBackup()) {
            $this->mysqlPhpBackup->export($target);

            return;
        }

        $this->exportMysqlViaCli($target);
    }

    private function exportMysqlViaCli(string $target): void
    {
        $config = $this->mysqlConfig();
        $process = new Process(array_merge(
            [$this->mysqlBinary('mysqldump')],
            $this->mysqlCliConnectionArgs($config),
            [
                '--routines',
                '--triggers',
                '--single-transaction',
                $config['database'],
            ]
        ));

        $process->setTimeout((int) config('erp.backup.timeout', 300));
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(trim($process->getErrorOutput() ?: $process->getOutput()) ?: __('erp.backup.export_failed'));
        }

        File::put($target, $process->getOutput());
    }

    private function shouldUsePhpMysqlBackup(): bool
    {
        $method = config('erp.backup.mysql_method');

        if ($method === 'php') {
            return true;
        }

        if ($method === 'cli') {
            return false;
        }

        return PHP_OS_FAMILY === 'Windows';
    }

    private function importDatabase(string $tempDir, string $driver): void
    {
        match ($driver) {
            'sqlite' => $this->importSqlite($tempDir.'/database/database.sqlite'),
            'mysql', 'mariadb' => $this->importMysql($tempDir.'/database/dump.sql'),
            default => throw new RuntimeException(__('erp.backup.unsupported_driver')),
        };
    }

    private function importSqlite(string $source): void
    {
        if (! File::exists($source)) {
            throw new RuntimeException(__('erp.backup.invalid_archive'));
        }

        $target = $this->sqliteDatabasePath();

        DB::disconnect();
        DB::purge();

        if (File::exists($target)) {
            File::delete($target);
        }

        File::copy($source, $target);
    }

    private function importMysql(string $source): void
    {
        if ($this->shouldUsePhpMysqlBackup()) {
            $this->mysqlPhpBackup->import($source);

            return;
        }

        $config = $this->mysqlConfig();
        $process = new Process(array_merge(
            [$this->mysqlBinary('mysql')],
            $this->mysqlCliConnectionArgs($config),
            [$config['database']]
        ));

        $process->setInput(File::get($source));
        $process->setTimeout((int) config('erp.backup.timeout', 300));
        $process->run();

        if (! $process->isSuccessful()) {
            throw new RuntimeException(trim($process->getErrorOutput() ?: $process->getOutput()) ?: __('erp.backup.import_failed'));
        }
    }

    /**
     * @param  array<string, mixed>  $config
     * @return list<string>
     */
    private function mysqlCliConnectionArgs(array $config): array
    {
        $args = ['-u', (string) $config['username']];

        if (($config['password'] ?? '') !== '') {
            $args[] = '-p'.$config['password'];
        }

        if (! empty($config['unix_socket'])) {
            $args[] = '--socket='.(string) $config['unix_socket'];

            return $args;
        }

        $host = strtolower((string) ($config['host'] ?? '127.0.0.1'));

        if ($host === 'localhost') {
            $host = '127.0.0.1';
        }

        $args[] = '--protocol=TCP';
        $args[] = '-h';
        $args[] = $host;
        $args[] = '-P';
        $args[] = (string) ($config['port'] ?? '3306');

        return $args;
    }

    private function exportPublicFiles(string $tempDir): void
    {
        $source = storage_path('app/public');

        if (! File::isDirectory($source)) {
            return;
        }

        File::copyDirectory($source, $tempDir.'/files/public');
    }

    private function importPublicFiles(string $tempDir): void
    {
        $source = $tempDir.'/files/public';
        $target = storage_path('app/public');

        if (! File::isDirectory($source)) {
            return;
        }

        if (File::isDirectory($target)) {
            File::deleteDirectory($target);
        }

        File::makeDirectory(dirname($target), 0755, true);
        File::copyDirectory($source, $target);
    }

    private function exportDesktopConfiguration(string $tempDir): void
    {
        if (! DesktopApplication::isDesktop()) {
            return;
        }

        $envPath = DesktopApplication::envFilePath();

        if (! $envPath || ! File::exists($envPath)) {
            return;
        }

        File::makeDirectory($tempDir.'/config', 0755, true);
        File::copy($envPath, $tempDir.'/config/.env');
    }

    private function importDesktopConfiguration(string $tempDir): void
    {
        if (! DesktopApplication::isDesktop()) {
            return;
        }

        $source = $tempDir.'/config/.env';
        $target = DesktopApplication::envFilePath();

        if (! File::exists($source) || ! $target) {
            return;
        }

        File::copy($source, $target);
    }

    private function createZip(string $sourceDir, string $destination): void
    {
        $zip = new ZipArchive;

        if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException(__('erp.backup.zip_failed'));
        }

        $files = File::allFiles($sourceDir);

        foreach ($files as $file) {
            $relativePath = Str::after($file->getPathname(), $sourceDir.DIRECTORY_SEPARATOR);
            $zip->addFile($file->getPathname(), str_replace('\\', '/', $relativePath));
        }

        $zip->close();
    }

    private function extractZip(string $zipPath, string $destination): void
    {
        $zip = new ZipArchive;

        if ($zip->open($zipPath) !== true) {
            throw new RuntimeException(__('erp.backup.invalid_archive'));
        }

        $zip->extractTo($destination);
        $zip->close();
    }

    private function readManifest(string $path): array
    {
        if (! File::exists($path)) {
            throw new RuntimeException(__('erp.backup.invalid_archive'));
        }

        $manifest = json_decode(File::get($path), true);

        if (! is_array($manifest) || empty($manifest['database_driver'])) {
            throw new RuntimeException(__('erp.backup.invalid_archive'));
        }

        return $manifest;
    }

    private function assertCompatibleDriver(array $manifest): void
    {
        if ($manifest['database_driver'] !== config('database.default')) {
            throw new RuntimeException(__('erp.backup.driver_mismatch'));
        }
    }

    private function mysqlConfig(): array
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        if (! is_array($config)) {
            throw new RuntimeException(__('erp.backup.unsupported_driver'));
        }

        return $config;
    }

    private function mysqlBinary(string $binary): string
    {
        $directory = $this->mysqlBinaryDirectory();
        $suffix = PHP_OS_FAMILY === 'Windows' ? '.exe' : '';
        $path = $directory.DIRECTORY_SEPARATOR.$binary.$suffix;

        if (! is_file($path)) {
            throw new RuntimeException(__('erp.backup.mysql_tools_missing', ['binary' => $binary]));
        }

        return $path;
    }

    private function mysqlBinaryDirectory(): string
    {
        if ($this->resolvedMysqlBin !== null) {
            return $this->resolvedMysqlBin;
        }

        $configured = config('erp.backup.mysql_bin');

        if ($configured && is_dir($configured)) {
            return $this->resolvedMysqlBin = rtrim((string) $configured, '\\/');
        }

        foreach ($this->mysqlBinaryCandidates() as $directory) {
            $suffix = PHP_OS_FAMILY === 'Windows' ? '.exe' : '';

            if (is_file($directory.DIRECTORY_SEPARATOR.'mysqldump'.$suffix)) {
                return $this->resolvedMysqlBin = $directory;
            }
        }

        throw new RuntimeException(__('erp.backup.mysql_tools_missing', ['binary' => 'mysqldump']));
    }

    /**
     * @return list<string>
     */
    private function mysqlBinaryCandidates(): array
    {
        $candidates = [];

        if (PHP_OS_FAMILY === 'Windows') {
            $laragonRoots = array_values(array_unique(array_filter([
                getenv('LARAGON_ROOT') ?: null,
                'C:\\laragon',
                dirname(base_path(), 2).DIRECTORY_SEPARATOR.'laragon',
            ], fn (?string $path) => is_string($path) && $path !== '' && is_dir($path))));

            foreach ($laragonRoots as $root) {
                $mysqlRoot = $root.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.'mysql';

                if (! is_dir($mysqlRoot)) {
                    continue;
                }

                $matches = glob($mysqlRoot.DIRECTORY_SEPARATOR.'*'.DIRECTORY_SEPARATOR.'bin', GLOB_ONLYDIR) ?: [];
                rsort($matches);
                array_push($candidates, ...$matches);
            }
        }

        return array_values(array_unique($candidates));
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1048576) {
            return round($bytes / 1024, 1).' KB';
        }

        if ($bytes < 1073741824) {
            return round($bytes / 1048576, 1).' MB';
        }

        return round($bytes / 1073741824, 2).' GB';
    }
}
