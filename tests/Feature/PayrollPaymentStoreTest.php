<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\FinancialYear;
use App\Models\PayrollPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollPaymentStoreTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('email', 'admin@marbleerp.local')->firstOrFail();
    }

    public function test_payroll_store_succeeds_with_valid_data(): void
    {
        $employee = Employee::query()->firstOrFail();
        $activeYear = FinancialYear::query()->active()->where('is_all_years', false)->firstOrFail();

        $response = $this->actingAs($this->admin)->post(route('payroll.store'), [
            'employee_id' => $employee->id,
            'payment_date' => '1405/06/15',
            'amount' => 5000,
            'payment_type' => 'partial',
            'period_month' => '1405/06',
        ]);

        $response->assertRedirect(route('payroll.index'));

        $payment = PayrollPayment::query()->latest('id')->firstOrFail();
        $this->assertSame($activeYear->id, $payment->financial_year_id);
        $this->assertSame(5000.0, (float) $payment->amount);
        $this->assertSame('1405/06', $payment->period_month);
    }

    public function test_payroll_store_rejects_invalid_period_month_format(): void
    {
        $employee = Employee::query()->firstOrFail();

        $response = $this->actingAs($this->admin)->post(route('payroll.store'), [
            'employee_id' => $employee->id,
            'payment_date' => '1405/06/15',
            'amount' => 5000,
            'payment_type' => 'partial',
            'period_month' => 'invalid',
        ]);

        $response->assertSessionHasErrors('period_month');
        $this->assertSame(0, PayrollPayment::query()->count());
    }

    public function test_payroll_store_shows_financial_year_error_in_all_years_mode(): void
    {
        $allYears = FinancialYear::query()->where('is_all_years', true)->firstOrFail();
        $employee = Employee::query()->firstOrFail();

        $this->actingAs($this->admin)->post(route('financial-years.activate', $allYears));

        $response = $this->actingAs($this->admin)->post(route('payroll.store'), [
            'employee_id' => $employee->id,
            'payment_date' => '1405/06/15',
            'amount' => 5000,
            'payment_type' => 'partial',
        ]);

        $response->assertSessionHasErrors('financial_year');
    }
}
