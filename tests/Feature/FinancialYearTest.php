<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialYear;
use App\Models\MarbleShipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialYearTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('email', 'admin@marbleerp.local')->firstOrFail();
    }

    public function test_migration_seeds_closed_and_active_years(): void
    {
        $this->assertDatabaseHas('financial_years', ['name' => '1404', 'status' => 'closed']);
        $this->assertDatabaseHas('financial_years', ['name' => '1405', 'status' => 'active']);
        $this->assertSame(1, FinancialYear::query()->active()->count());
    }

    public function test_creating_active_year_closes_other_active_years(): void
    {
        $response = $this->actingAs($this->admin)->post(route('financial-years.store'), [
            'name' => '1406',
            'start_date' => '1406/01/01',
            'end_date' => '1406/12/29',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('financial-years.index'));

        $this->assertDatabaseHas('financial_years', ['name' => '1405', 'status' => 'closed']);
        $this->assertDatabaseHas('financial_years', ['name' => '1406', 'status' => 'active']);
        $this->assertSame(1, FinancialYear::query()->active()->count());
    }

    public function test_new_expense_is_assigned_to_active_financial_year(): void
    {
        $category = ExpenseCategory::query()->firstOrFail();
        $activeYear = FinancialYear::query()->active()->firstOrFail();

        $response = $this->actingAs($this->admin)->post(route('expenses.store'), [
            'expense_date' => '1405/06/15',
            'expense_category_id' => $category->id,
            'amount' => 500,
            'description' => 'FY test expense',
        ]);

        $response->assertRedirect(route('expenses.index'));

        $expense = Expense::query()->latest('id')->firstOrFail();
        $this->assertSame($activeYear->id, $expense->financial_year_id);
    }

    public function test_cannot_update_expense_in_closed_financial_year(): void
    {
        $closedYear = FinancialYear::query()->where('name', '1404')->firstOrFail();
        $category = ExpenseCategory::query()->firstOrFail();

        $expense = Expense::query()->create([
            'financial_year_id' => $closedYear->id,
            'expense_category_id' => $category->id,
            'expense_date' => now(),
            'amount' => 100,
            'description' => 'Closed year expense',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->put(route('expenses.update', $expense), [
            'expense_date' => '1404/06/15',
            'expense_category_id' => $category->id,
            'amount' => 200,
            'description' => 'Attempted update',
        ]);

        $response->assertSessionHasErrors('financial_year');
        $this->assertSame(100.0, (float) $expense->fresh()->amount);
    }

    public function test_close_financial_year_action(): void
    {
        $activeYear = FinancialYear::query()->active()->firstOrFail();

        $response = $this->actingAs($this->admin)->post(route('financial-years.close', $activeYear));

        $response->assertRedirect(route('financial-years.index'));
        $this->assertDatabaseHas('financial_years', ['id' => $activeYear->id, 'status' => 'closed']);
        $this->assertSame(0, FinancialYear::query()->active()->count());
    }

    public function test_dashboard_loads_when_no_active_financial_year(): void
    {
        FinancialYear::query()->active()->update(['status' => FinancialYear::STATUS_CLOSED]);

        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertSuccessful();
    }

    public function test_activate_financial_year_closes_others_and_switches_active_year(): void
    {
        $year1404 = FinancialYear::query()->where('name', '1404')->firstOrFail();
        $year1405 = FinancialYear::query()->where('name', '1405')->firstOrFail();

        $this->assertTrue($year1405->isActive());

        $response = $this->actingAs($this->admin)->post(route('financial-years.activate', $year1404));

        $response->assertRedirect(route('financial-years.index'));
        $this->assertDatabaseHas('financial_years', ['id' => $year1404->id, 'status' => 'active']);
        $this->assertDatabaseHas('financial_years', ['id' => $year1405->id, 'status' => 'closed']);
        $this->assertSame(1, FinancialYear::query()->active()->count());
        $this->assertSame('1404', FinancialYear::query()->active()->first()->name);
    }

    public function test_activating_year_does_not_change_historical_transactions(): void
    {
        $year1404 = FinancialYear::query()->where('name', '1404')->firstOrFail();
        $year1405 = FinancialYear::query()->where('name', '1405')->firstOrFail();
        $category = ExpenseCategory::query()->firstOrFail();

        $historicalExpense = Expense::query()->create([
            'financial_year_id' => $year1405->id,
            'expense_category_id' => $category->id,
            'expense_date' => now(),
            'amount' => 300,
            'description' => 'Historical 1405 expense',
            'created_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)->post(route('financial-years.activate', $year1404));

        $this->assertSame($year1405->id, $historicalExpense->fresh()->financial_year_id);

        $this->actingAs($this->admin)->post(route('expenses.store'), [
            'expense_date' => '1404/06/15',
            'expense_category_id' => $category->id,
            'amount' => 150,
            'description' => 'New expense after activation',
        ]);

        $newExpense = Expense::query()->latest('id')->firstOrFail();
        $this->assertSame($year1404->id, $newExpense->financial_year_id);
        $this->assertSame($year1405->id, $historicalExpense->fresh()->financial_year_id);
    }

    public function test_dashboard_shows_newly_activated_financial_year(): void
    {
        $year1404 = FinancialYear::query()->where('name', '1404')->firstOrFail();

        $this->actingAs($this->admin)->post(route('financial-years.activate', $year1404));

        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertSuccessful();
        $stats = $response->original->getData()['page']['props']['stats'];
        $this->assertSame('1404', $stats['active_financial_year']);
    }

    public function test_dashboard_marble_sales_reflect_active_financial_year(): void
    {
        $year1405 = FinancialYear::query()->where('name', '1405')->firstOrFail();
        $customer = \App\Models\Customer::query()->firstOrFail();

        MarbleShipment::query()->create([
            'financial_year_id' => $year1405->id,
            'customer_id' => $customer->id,
            'shipment_date' => now(),
            'quantity_ton' => 12,
            'price_per_ton' => 1000,
            'total_amount' => 12000,
            'status' => MarbleShipment::STATUS_COMPLETED,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertSuccessful();
        $stats = $response->original->getData()['page']['props']['stats'];
        $this->assertSame(12000.0, (float) $stats['marble_sales']['sales']);
        $this->assertSame(1, $stats['marble_sales']['trucks']);
        $this->assertSame(12.0, (float) $stats['marble_sales']['tons']);
    }
}
