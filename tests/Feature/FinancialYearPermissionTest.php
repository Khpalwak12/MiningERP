<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FinancialYearPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_financial_year_permissions_exist(): void
    {
        foreach (['view', 'create', 'edit', 'close', 'activate'] as $action) {
            $this->assertDatabaseHas('permissions', ['name' => "financial-year.{$action}"]);
        }
    }

    public function test_super_admin_can_access_financial_years(): void
    {
        $admin = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        $this->assertTrue($admin->hasRole('Super Admin'));
        $this->assertTrue($admin->can('financial-year.view'));

        $this->actingAs($admin)
            ->get(route('financial-years.index'))
            ->assertSuccessful();
    }

    public function test_manager_can_view_but_not_create_financial_years(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('Manager');

        $this->assertTrue($manager->can('financial-year.view'));
        $this->assertFalse($manager->can('financial-year.create'));
        $this->assertFalse($manager->can('financial-year.activate'));

        $this->actingAs($manager)
            ->get(route('financial-years.index'))
            ->assertSuccessful();

        $this->actingAs($manager)
            ->get(route('financial-years.create'))
            ->assertForbidden();

        $user = User::factory()->create();
        $user->assignRole('Manager');

        $closedYear = \App\Models\FinancialYear::query()->where('name', '1404')->firstOrFail();

        $this->actingAs($user)
            ->post(route('financial-years.activate', $closedYear))
            ->assertForbidden();
    }

    public function test_accountant_has_full_financial_year_permissions(): void
    {
        $accountant = User::factory()->create();
        $accountant->assignRole('Accountant');

        foreach (['view', 'create', 'edit', 'close', 'activate'] as $action) {
            $this->assertTrue($accountant->can("financial-year.{$action}"), "Missing financial-year.{$action}");
        }

        $this->actingAs($accountant)
            ->get(route('financial-years.index'))
            ->assertSuccessful();
    }

    public function test_unauthorized_user_receives_forbidden(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Data Entry Operator');

        $this->assertFalse($user->can('financial-year.view'));

        $this->actingAs($user)
            ->get(route('financial-years.index'))
            ->assertForbidden();
    }

    public function test_super_admin_receives_all_permissions_after_permission_seeder(): void
    {
        Permission::query()->where('name', 'like', 'financial-year.%')->delete();
        Role::findByName('Super Admin')->syncPermissions(
            Permission::query()->where('name', 'not like', 'financial-year.%')->pluck('name')
        );

        $this->seed(\Database\Seeders\PermissionSeeder::class);

        $admin = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        $this->assertTrue($admin->can('financial-year.view'));
        $this->assertTrue($admin->can('financial-year.close'));
        $this->assertCount(Permission::count(), Role::findByName('Super Admin')->permissions);
    }
}
