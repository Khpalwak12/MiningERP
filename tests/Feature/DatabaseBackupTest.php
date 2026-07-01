<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use App\Services\DatabaseBackupService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DatabaseBackupTest extends TestCase
{
    protected User $admin;

    protected string $backupPath;

    protected string $databasePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->backupPath = storage_path('app/testing-backups');
        $this->databasePath = storage_path('app/testing-database.sqlite');

        if (File::isDirectory($this->backupPath)) {
            File::deleteDirectory($this->backupPath);
        }

        if (File::exists($this->databasePath)) {
            File::delete($this->databasePath);
        }

        config([
            'erp.backup.path' => $this->backupPath,
            'database.connections.sqlite.database' => $this->databasePath,
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Artisan::call('migrate:fresh', ['--force' => true]);
        Artisan::call('db:seed', ['--force' => true]);

        $this->admin = User::where('email', 'admin@marbleerp.local')->firstOrFail();
    }

    protected function tearDown(): void
    {
        if (File::isDirectory($this->backupPath)) {
            File::deleteDirectory($this->backupPath);
        }

        if (File::exists($this->databasePath)) {
            File::delete($this->databasePath);
        }

        parent::tearDown();
    }

    public function test_backup_page_is_accessible_to_super_admin(): void
    {
        $response = $this->actingAs($this->admin)->get(route('backups.index'));

        $response->assertSuccessful();
        $response->assertInertia(fn ($page) => $page->component('Backups/Index'));
    }

    public function test_create_backup_writes_zip_to_backup_folder(): void
    {
        $response = $this->actingAs($this->admin)->post(route('backups.store'));

        $response->assertRedirect(route('backups.index'));

        $files = File::files($this->backupPath);
        $this->assertCount(1, $files);
        $this->assertSame('zip', strtolower($files[0]->getExtension()));
    }

    public function test_restore_replaces_database_from_backup(): void
    {
        $customer = Customer::factory()->create([
            'name' => 'Backup Marker Customer',
            'status' => 'active',
        ]);

        $service = app(DatabaseBackupService::class);
        $backup = $service->create();

        $customer->forceDelete();
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);

        $service->restore($this->backupPath.DIRECTORY_SEPARATOR.$backup['filename']);

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Backup Marker Customer',
        ]);
    }
}
