<?php

namespace Tests\Feature;

use App\Models\FinancialYear;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DesktopInitializeTest extends TestCase
{

    public function test_desktop_initialize_creates_database_and_admin_user(): void
    {
        $dataPath = storage_path('framework/testing-desktop-'.uniqid());
        File::deleteDirectory($dataPath);
        File::makeDirectory($dataPath, 0755, true);

        try {
            putenv('MININGERP_DESKTOP=1');
            putenv('MININGERP_DATA_PATH='.$dataPath);
            $_ENV['MININGERP_DESKTOP'] = '1';
            $_ENV['MININGERP_DATA_PATH'] = $dataPath;
            $_SERVER['MININGERP_DESKTOP'] = '1';
            $_SERVER['MININGERP_DATA_PATH'] = $dataPath;

            $this->artisan('desktop:initialize')->assertSuccessful();

            $this->assertFileExists($dataPath.'/.env');
            $this->assertFileExists($dataPath.'/database.sqlite');
            $this->assertTrue(User::query()->where('email', 'admin@marbleerp.local')->exists());
            $this->assertSame(0, FinancialYear::query()->realYears()->count());
        } finally {
            File::deleteDirectory($dataPath);
        }
    }

    public function test_desktop_initialize_is_idempotent(): void
    {
        $dataPath = storage_path('framework/testing-desktop-'.uniqid());
        File::deleteDirectory($dataPath);
        File::makeDirectory($dataPath, 0755, true);

        try {
            putenv('MININGERP_DESKTOP=1');
            putenv('MININGERP_DATA_PATH='.$dataPath);
            $_ENV['MININGERP_DESKTOP'] = '1';
            $_ENV['MININGERP_DATA_PATH'] = $dataPath;

            $this->artisan('desktop:initialize')->assertSuccessful();
            $count = User::query()->count();

            $this->artisan('desktop:initialize')->assertSuccessful();

            $this->assertSame($count, User::query()->count());
        } finally {
            File::deleteDirectory($dataPath);
        }
    }
}
