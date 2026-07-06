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
        $fontSource = base_path('storage/fonts');
        $mpdfTemp = storage_path('app/mpdf-tmp');

        File::ensureDirectoryExists($fontTarget);
        File::ensureDirectoryExists($mpdfTemp);

        if (! File::isDirectory($fontSource)) {
            return;
        }

        foreach (File::files($fontSource) as $file) {
            if (strtolower($file->getExtension()) !== 'ttf') {
                continue;
            }

            $destination = $fontTarget.DIRECTORY_SEPARATOR.$file->getFilename();

            if (! File::exists($destination)) {
                File::copy($file->getPathname(), $destination);
            }
        }
    }
}
