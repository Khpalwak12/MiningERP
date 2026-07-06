<?php

namespace Tests\Feature;

use App\Models\FinancialYear;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
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
            $this->assertFileExists($dataPath.'/.desktop-initialized');
            $this->assertFileExists($dataPath.'/storage/fonts/NotoSansArabic-Regular.ttf');
            $this->assertFileExists($dataPath.'/storage/fonts/NotoSansArabic-Bold.ttf');
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

    public function test_desktop_initialize_repairs_corrupt_database(): void
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

            $database = $dataPath.'/database.sqlite';
            File::put($database, '');

            config([
                'database.default' => 'sqlite',
                'database.connections.sqlite.database' => $database,
            ]);
            DB::purge('sqlite');
            DB::reconnect('sqlite');

            Schema::connection('sqlite')->create('financial_years', function ($table) {
                $table->id();
                $table->string('name', 10);
                $table->date('start_date');
                $table->date('end_date');
                $table->string('status')->default('active');
                $table->boolean('is_all_years')->default(false);
                $table->text('notes')->nullable();
                $table->timestamps();
            });

            $this->assertTrue(Schema::connection('sqlite')->hasTable('financial_years'));
            $this->assertFalse(Schema::connection('sqlite')->hasTable('migrations'));

            $this->artisan('desktop:initialize')->assertSuccessful();

            $this->assertTrue(Schema::hasTable('migrations'));
            $this->assertTrue(
                DB::table('migrations')
                    ->where('migration', '2026_06_21_100000_create_financial_years_and_assign_transactions')
                    ->exists()
            );
            $this->assertTrue(User::query()->where('email', 'admin@marbleerp.local')->exists());
            $this->assertSame(0, FinancialYear::query()->realYears()->count());
        } finally {
            File::deleteDirectory($dataPath);
        }
    }
}
