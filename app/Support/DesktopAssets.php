<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class DesktopAssets
{
    public static function ensurePdfAssets(): void
    {
        if (! DesktopApplication::isDesktop()) {
            return;
        }

        $fontTarget = storage_path('fonts');
        $mpdfTemp = storage_path('app/mpdf-tmp');
        $fontSources = [
            base_path('storage/fonts'),
            resource_path('fonts'),
        ];

        $extraFonts = dirname(base_path()).DIRECTORY_SEPARATOR.'pdf-fonts';
        if (is_dir($extraFonts)) {
            $fontSources[] = $extraFonts;
        }

        File::ensureDirectoryExists($fontTarget);
        File::ensureDirectoryExists($mpdfTemp);

        foreach ($fontSources as $fontSource) {
            if (! File::isDirectory($fontSource)) {
                continue;
            }

            foreach (File::files($fontSource) as $file) {
                if (strtolower($file->getExtension()) !== 'ttf') {
                    continue;
                }

                $destination = $fontTarget.DIRECTORY_SEPARATOR.$file->getFilename();

                if (
                    ! File::exists($destination)
                    || File::lastModified($file->getPathname()) > File::lastModified($destination)
                ) {
                    File::copy($file->getPathname(), $destination);
                }
            }
        }
    }
}
