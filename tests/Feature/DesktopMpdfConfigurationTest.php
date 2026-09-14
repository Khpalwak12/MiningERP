<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\MpdfConfiguration;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DesktopMpdfConfigurationTest extends TestCase
{
    public function test_desktop_mpdf_uses_bundled_fonts_when_appdata_fonts_are_missing(): void
    {
        $dataPath = storage_path('framework/testing-mpdf-'.uniqid());
        File::deleteDirectory($dataPath);
        File::makeDirectory($dataPath.'/storage/fonts', 0755, true);

        try {
            putenv('MININGERP_DESKTOP=1');
            putenv('MININGERP_DATA_PATH='.$dataPath);
            $_ENV['MININGERP_DESKTOP'] = '1';
            $_ENV['MININGERP_DATA_PATH'] = $dataPath;

            $directories = MpdfConfiguration::fontDirectories();

            $this->assertNotEmpty($directories);
            $this->assertTrue(
                collect($directories)->contains(
                    fn (string $directory) => str_ends_with(str_replace('\\', '/', $directory), '/storage/fonts')
                        || str_contains(str_replace('\\', '/', $directory), '/storage/fonts')
                )
            );
        } finally {
            File::deleteDirectory($dataPath);
        }
    }

    public function test_desktop_mpdf_temp_directory_is_writable(): void
    {
        $dataPath = storage_path('framework/testing-mpdf-'.uniqid());
        File::deleteDirectory($dataPath);

        try {
            putenv('MININGERP_DESKTOP=1');
            putenv('MININGERP_DATA_PATH='.$dataPath);
            $_ENV['MININGERP_DESKTOP'] = '1';
            $_ENV['MININGERP_DATA_PATH'] = $dataPath;

            $temp = MpdfConfiguration::tempDirectory();

            $this->assertDirectoryExists(rtrim(str_replace('/', DIRECTORY_SEPARATOR, $temp), DIRECTORY_SEPARATOR));
            $this->assertTrue(is_writable(rtrim(str_replace('/', DIRECTORY_SEPARATOR, $temp), DIRECTORY_SEPARATOR)));
        } finally {
            File::deleteDirectory($dataPath);
        }
    }
}
