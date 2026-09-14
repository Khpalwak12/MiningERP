<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use RuntimeException;

class MpdfConfiguration
{
    private const REQUIRED_FONTS = [
        'NotoSansArabic-Regular.ttf',
        'NotoSansArabic-Bold.ttf',
    ];

    /**
     * @return list<string>
     */
    public static function fontDirectories(): array
    {
        $candidates = DesktopApplication::isDesktop()
            ? [
                base_path('storage/fonts'),
                resource_path('fonts'),
                self::desktopExtraFontDirectory(),
                storage_path('fonts'),
            ]
            : [
                config('mpdf.font_dir'),
                storage_path('fonts'),
                base_path('storage/fonts'),
                resource_path('fonts'),
            ];

        $directories = [];

        foreach (array_unique(array_filter($candidates)) as $directory) {
            if (! is_string($directory) || $directory === '') {
                continue;
            }

            $directory = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $directory), DIRECTORY_SEPARATOR);

            if (self::directoryHasRequiredFonts($directory)) {
                $directories[] = $directory;
            }
        }

        if ($directories === []) {
            throw new RuntimeException(
                'PDF fonts are missing from the desktop application bundle. Reinstall Mining ERP using the latest installer.'
            );
        }

        return $directories;
    }

    public static function tempDirectory(): string
    {
        if (DesktopApplication::isDesktop()) {
            $dataPath = DesktopApplication::dataPath();

            if ($dataPath) {
                $temp = $dataPath.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'mpdf-tmp';
                File::ensureDirectoryExists($temp);

                return self::normalizePathForMpdf($temp);
            }
        }

        $temp = storage_path('app/mpdf-tmp');
        File::ensureDirectoryExists($temp);

        return self::normalizePathForMpdf($temp);
    }

    public static function normalizePathForMpdf(string $path): string
    {
        return str_replace('\\', '/', rtrim($path, '/\\')).'/';
    }

    private static function desktopExtraFontDirectory(): ?string
    {
        if (! DesktopApplication::isDesktop()) {
            return null;
        }

        $directory = dirname(base_path()).DIRECTORY_SEPARATOR.'pdf-fonts';

        return is_dir($directory) ? $directory : null;
    }

    private static function directoryHasRequiredFonts(string $directory): bool
    {
        if (! is_dir($directory)) {
            return false;
        }

        foreach (self::REQUIRED_FONTS as $font) {
            if (! is_file($directory.DIRECTORY_SEPARATOR.$font)) {
                return false;
            }
        }

        return true;
    }
}
