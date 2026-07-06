<?php

namespace App\Providers;

use App\Support\DesktopApplication;
use App\Support\DesktopAssets;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (! DesktopApplication::isDesktop()) {
            return;
        }

        $dataPath = DesktopApplication::dataPath();

        if (! $dataPath) {
            return;
        }

        $this->app->useStoragePath($dataPath.DIRECTORY_SEPARATOR.'storage');

        config([
            'erp.backup.path' => $dataPath.DIRECTORY_SEPARATOR.'backups',
            'logging.channels.single.path' => $dataPath.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'laravel.log',
            'logging.channels.daily.path' => $dataPath.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'laravel.log',
            'mpdf.font_dir' => $dataPath.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'fonts',
            'mpdf.temp_dir' => $dataPath.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'mpdf-tmp',
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        if (! DesktopApplication::isDesktop()) {
            return;
        }

        DesktopAssets::ensurePdfAssets();

        Route::get('/storage/{path}', function (string $path) {
            abort_unless(Storage::disk('public')->exists($path), 404);

            return Storage::disk('public')->response($path);
        })->where('path', '.*');
    }
}
