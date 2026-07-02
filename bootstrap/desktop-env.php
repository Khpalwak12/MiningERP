<?php

/**
 * Configure writable desktop paths before Laravel boots.
 */
if (filter_var(getenv('MININGERP_DESKTOP') ?: ($_ENV['MININGERP_DESKTOP'] ?? false), FILTER_VALIDATE_BOOL)) {
    $dataPath = getenv('MININGERP_DATA_PATH') ?: ($_ENV['MININGERP_DATA_PATH'] ?? null);

    if (is_string($dataPath) && $dataPath !== '') {
        $dataPath = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $dataPath), DIRECTORY_SEPARATOR);

        $writableDirectories = [
            $dataPath,
            $dataPath.'/tmp',
            $dataPath.'/backups',
            $dataPath.'/exports',
            $dataPath.'/logs',
            $dataPath.'/uploads',
            $dataPath.'/bootstrap/cache',
            $dataPath.'/storage/app/public',
            $dataPath.'/storage/app/private',
            $dataPath.'/storage/framework/cache',
            $dataPath.'/storage/framework/sessions',
            $dataPath.'/storage/framework/views',
            $dataPath.'/storage/logs',
        ];

        foreach ($writableDirectories as $directory) {
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
        }

        $normalize = static fn (string $path): string => str_replace('\\', '/', $path);

        foreach ([
            'APP_CONFIG_CACHE',
            'APP_SERVICES_CACHE',
            'APP_PACKAGES_CACHE',
            'APP_ROUTES_CACHE',
            'APP_EVENTS_CACHE',
        ] as $legacyCacheKey) {
            putenv($legacyCacheKey);
            unset($_ENV[$legacyCacheKey], $_SERVER[$legacyCacheKey]);
        }

        foreach ([
            'LARAVEL_STORAGE_PATH' => $normalize($dataPath.'/storage'),
            'TMP' => $normalize($dataPath.'/tmp'),
            'TEMP' => $normalize($dataPath.'/tmp'),
        ] as $key => $value) {
            putenv($key.'='.$value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }

        if (is_file($dataPath.DIRECTORY_SEPARATOR.'.env')) {
            \Dotenv\Dotenv::createImmutable($dataPath, '.env')->safeLoad();
        }
    }
}
