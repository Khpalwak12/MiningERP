<?php

namespace App\Support;

class DesktopApplication
{
    public static function isDesktop(): bool
    {
        return filter_var(env('MININGERP_DESKTOP', false), FILTER_VALIDATE_BOOL);
    }

    public static function dataPath(): ?string
    {
        $path = env('MININGERP_DATA_PATH');

        if (! is_string($path) || $path === '') {
            return null;
        }

        return rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
    }

    public static function envFilePath(): ?string
    {
        $dataPath = self::dataPath();

        return $dataPath ? $dataPath.DIRECTORY_SEPARATOR.'.env' : null;
    }
}
