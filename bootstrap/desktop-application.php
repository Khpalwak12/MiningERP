<?php

use App\Support\WindowsSafeFilesystem;
use Illuminate\Foundation\Application;

if (! function_exists('mining_apply_desktop_storage_path')) {
    function mining_apply_desktop_storage_path(Application $app): void
    {
        if (! filter_var(getenv('MININGERP_DESKTOP') ?: ($_ENV['MININGERP_DESKTOP'] ?? false), FILTER_VALIDATE_BOOL)) {
            return;
        }

        // Bind before any view/compiler resolves Illuminate\Filesystem\Filesystem.
        $app->singleton('files', fn () => new WindowsSafeFilesystem());

        $dataPath = getenv('MININGERP_DATA_PATH') ?: ($_ENV['MININGERP_DATA_PATH'] ?? null);

        if (! is_string($dataPath) || $dataPath === '') {
            return;
        }

        $dataPath = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $dataPath), DIRECTORY_SEPARATOR);

        $app->useStoragePath($dataPath.DIRECTORY_SEPARATOR.'storage');
        $app->useBootstrapPath($dataPath.DIRECTORY_SEPARATOR.'bootstrap');
    }
}
