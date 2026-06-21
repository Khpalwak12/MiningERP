<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\MarbleShipment;
use App\Models\PayrollPayment;
use App\Models\SankariStoneSale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CrudPagesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('email', 'admin@marbleerp.local')->firstOrFail();
    }

    public function test_create_pages_return_successful_inertia_responses(): void
    {
        $routes = [
            ['customers.create', 'Customers/Create'],
            ['shipments.create', 'Shipments/Create'],
            ['payments.create', 'Payments/Create'],
            ['sankari.create', 'Sankari/Create'],
            ['expenses.create', 'Expenses/Create'],
            ['employees.create', 'Employees/Create'],
            ['payroll.create', 'Payroll/Create'],
            ['inventory.create', 'Inventory/Create'],
            ['journal-entries.create', 'Accounting/Create'],
            ['users.create', 'Users/Create'],
            ['roles.create', 'Roles/Create'],
        ];

        foreach ($routes as [$name, $component]) {
            $response = $this->actingAs($this->admin)->get(route($name));

            $response->assertSuccessful("Route {$name} failed with status {$response->status()}");
            $response->assertInertia(fn ($page) => $page->component($component));
        }
    }

    public function test_edit_pages_return_successful_inertia_responses(): void
    {
        $customer = Customer::query()->firstOrFail();
        $shipment = MarbleShipment::query()->create([
            'customer_id' => $customer->id,
            'shipment_date' => now(),
            'driver_name' => 'Test Driver',
            'quantity_ton' => 10,
            'price_per_ton' => 1000,
            'total_amount' => 10000,
            'created_by' => $this->admin->id,
        ]);
        $payment = CustomerPayment::query()->create([
            'customer_id' => $customer->id,
            'payment_date' => now(),
            'amount' => 5000,
            'received_by' => 'Test Receiver',
            'created_by' => $this->admin->id,
        ]);
        $sale = SankariStoneSale::query()->create([
            'sale_date' => now(),
            'truck_count' => 2,
            'price_per_truck' => 5000,
            'total_amount' => 10000,
            'payment_type' => 'cash',
            'cash_received' => 10000,
            'created_by' => $this->admin->id,
        ]);
        $category = \App\Models\ExpenseCategory::query()->whereNull('parent_id')->firstOrFail();
        $expense = Expense::query()->create([
            'expense_category_id' => $category->id,
            'expense_date' => now(),
            'amount' => 1000,
            'description' => 'Test expense',
            'created_by' => $this->admin->id,
        ]);
        $employee = Employee::query()->firstOrFail();
        $payroll = PayrollPayment::query()->create([
            'employee_id' => $employee->id,
            'payment_date' => now(),
            'amount' => 5000,
            'payment_type' => 'partial',
            'created_by' => $this->admin->id,
        ]);
        $inventoryItem = InventoryItem::query()->firstOrFail();
        $journalEntry = JournalEntry::query()->create([
            'entry_date' => now(),
            'reference' => 'TEST-001',
            'description' => 'Test entry',
            'created_by' => $this->admin->id,
        ]);
        JournalEntryLine::query()->create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => Account::query()->firstOrFail()->id,
            'debit' => 100,
            'credit' => 0,
            'description' => 'Debit line',
        ]);
        JournalEntryLine::query()->create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => Account::query()->skip(1)->firstOrFail()->id,
            'debit' => 0,
            'credit' => 100,
            'description' => 'Credit line',
        ]);
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'Test Role']);

        $routes = [
            ['customers.edit', $customer, 'Customers/Edit', 'customer'],
            ['shipments.edit', $shipment, 'Shipments/Edit', 'shipment'],
            ['payments.edit', $payment, 'Payments/Edit', 'payment'],
            ['sankari.edit', $sale, 'Sankari/Edit', 'sankari'],
            ['expenses.edit', $expense, 'Expenses/Edit', 'expense'],
            ['employees.edit', $employee, 'Employees/Edit', 'employee'],
            ['payroll.edit', $payroll, 'Payroll/Edit', 'payroll'],
            ['inventory.edit', $inventoryItem, 'Inventory/Edit', 'inventoryItem'],
            ['journal-entries.edit', $journalEntry, 'Accounting/Edit', 'journalEntry'],
            ['users.edit', $user, 'Users/Edit', 'user'],
            ['roles.edit', $role, 'Roles/Edit', 'role'],
        ];

        foreach ($routes as [$name, $model, $component, $param]) {
            $response = $this->actingAs($this->admin)->get(route($name, [$param => $model]));

            $response->assertSuccessful("Route {$name} failed with status {$response->status()}");
            $response->assertInertia(fn ($page) => $page->component($component));
        }
    }
}
