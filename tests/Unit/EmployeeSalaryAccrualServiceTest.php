<?php

namespace Tests\Unit;

use App\Models\Employee;
use App\Services\EmployeeSalaryAccrualService;
use App\Support\JalaliDate;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeSalaryAccrualServiceTest extends TestCase
{
    use RefreshDatabase;

    private EmployeeSalaryAccrualService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(EmployeeSalaryAccrualService::class);
    }

    public function test_months_worked_matches_shamsi_month_difference(): void
    {
        $this->assertSame(4, JalaliDate::monthsWorkedSince('1405/01/01', '1405/05/01'));
        $this->assertSame(5, JalaliDate::monthsWorkedSince('1405/01/01', '1405/06/01'));
        $this->assertSame(0, JalaliDate::monthsWorkedSince('1405/05/15', '1405/05/01'));
    }

    public function test_accrued_salary_is_months_worked_times_monthly_salary(): void
    {
        $employee = Employee::factory()->create([
            'salary' => 20000,
            'joining_date' => JalaliDate::toGregorian('1405/01/01'),
        ]);

        $asOf = JalaliDate::toGregorian('1405/05/01');
        $summary = $this->service->summary($employee, $asOf, 0);

        $this->assertSame(4, $summary['months_worked']);
        $this->assertSame(80000.0, $summary['total_earned_salary']);
        $this->assertSame(80000.0, $summary['remaining_balance']);
        $this->assertSame('credit', $summary['payroll_status']);
    }

    public function test_accrued_salary_increases_when_shamsi_month_advances(): void
    {
        $employee = Employee::factory()->create([
            'salary' => 20000,
            'joining_date' => JalaliDate::toGregorian('1405/01/01'),
        ]);

        $summary = $this->service->summary($employee, JalaliDate::toGregorian('1405/06/01'), 0);

        $this->assertSame(5, $summary['months_worked']);
        $this->assertSame(100000.0, $summary['total_earned_salary']);
    }

    public function test_partial_payments_reduce_remaining_balance(): void
    {
        $employee = Employee::factory()->create([
            'salary' => 20000,
            'joining_date' => JalaliDate::toGregorian('1405/01/01'),
        ]);

        $summary = $this->service->summary($employee, JalaliDate::toGregorian('1405/05/01'), 23000);

        $this->assertSame(80000.0, $summary['total_earned_salary']);
        $this->assertSame(23000.0, $summary['total_paid_salary']);
        $this->assertSame(57000.0, $summary['remaining_balance']);
        $this->assertSame(0.0, $summary['overpaid_amount']);
        $this->assertSame('credit', $summary['payroll_status']);
    }

    public function test_overpaid_when_total_paid_exceeds_earned_salary(): void
    {
        $employee = Employee::factory()->create([
            'salary' => 20000,
            'joining_date' => JalaliDate::toGregorian('1405/01/01'),
        ]);

        $summary = $this->service->summary($employee, JalaliDate::toGregorian('1405/05/01'), 90000);

        $this->assertSame(0.0, $summary['remaining_balance']);
        $this->assertSame(10000.0, $summary['overpaid_amount']);
        $this->assertSame('overpaid', $summary['payroll_status']);
    }

    public function test_settled_when_paid_equals_earned(): void
    {
        $employee = Employee::factory()->create([
            'salary' => 20000,
            'joining_date' => JalaliDate::toGregorian('1405/01/01'),
        ]);

        $summary = $this->service->summary($employee, JalaliDate::toGregorian('1405/05/01'), 80000);

        $this->assertSame(0.0, $summary['remaining_balance']);
        $this->assertSame(0.0, $summary['overpaid_amount']);
        $this->assertSame('settled', $summary['payroll_status']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
