<?php

namespace App\Support;

class DesktopApplication
{
    public static function isDesktop(): bool
    {
        $value = getenv('MININGERP_DESKTOP');

        if ($value === false) {
            $value = $_ENV['MININGERP_DESKTOP'] ?? $_SERVER['MININGERP_DESKTOP'] ?? false;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL);
    }

    public static function dataPath(): ?string
    {
        $path = getenv('MININGERP_DATA_PATH');

        if ($path === false || $path === null || $path === '') {
            $path = $_ENV['MININGERP_DATA_PATH'] ?? $_SERVER['MININGERP_DATA_PATH'] ?? null;
        }

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
