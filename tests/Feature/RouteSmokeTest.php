<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_authenticated_module_routes_return_successful_inertia_responses(): void
    {
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        $routes = [
            'dashboard',
            'customers.index',
            'shipments.index',
            'payments.index',
            'sankari.index',
            'expenses.index',
            'employees.index',
            'payroll.index',
            'inventory.index',
            'mine-assets.index',
            'journal-entries.index',
            'accounting.trial-balance',
            'reports.index',
            'reports.sales',
            'reports.payments',
            'reports.expenses',
            'reports.employees',
            'reports.payroll',
            'reports.inventory',
            'reports.mine-assets',
            'reports.daily-production',
            'reports.monthly-production',
            'reports.customer-balances',
            'reports.profit-loss',
            'reports.contractor-production',
            'reports.contractor-payments',
            'reports.contractor-expenses',
            'reports.contractor-ledger',
            'contractor-royalty.ledger',
            'contractor-royalty.productions.index',
            'contractor-royalty.payments.index',
            'financial-years.index',
            'users.index',
            'roles.index',
        ];

        foreach ($routes as $name) {
            $response = $this->actingAs($user)->get(route($name));

            $response->assertSuccessful("Route {$name} failed with status {$response->status()}");
            $response->assertInertia(fn ($page) => $page);
        }
    }
}
