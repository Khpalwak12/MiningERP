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

    public function test_months_worked_uses_days_divided_by_thirty(): void
    {
        // 1405/03/01 → 1405/04/15 = 45 days = 1.5 months
        $this->assertSame(1.5, JalaliDate::monthsWorkedSince('1405/03/01', '1405/04/15'));
        $this->assertSame(0.0, JalaliDate::monthsWorkedSince('1405/05/15', '1405/05/01'));
    }

    public function test_partial_month_salary_for_one_and_half_months(): void
    {
        $employee = Employee::factory()->create([
            'salary' => 20000,
            'joining_date' => JalaliDate::toGregorian('1405/03/01'),
            'end_date' => JalaliDate::toGregorian('1405/04/15'),
        ]);

        $summary = $this->service->summary($employee, totalPaid: 0);

        $this->assertSame(1.5, $summary['months_worked']);
        $this->assertSame(30000.0, $summary['total_earned_salary']);
        $this->assertSame(30000.0, $summary['remaining_balance']);
    }

    public function test_accrued_salary_is_months_worked_times_monthly_salary(): void
    {
        $employee = Employee::factory()->create([
            'salary' => 20000,
            'joining_date' => JalaliDate::toGregorian('1405/01/01'),
        ]);

        $asOf = JalaliDate::toGregorian('1405/05/01');
        $summary = $this->service->summary($employee, $asOf, 0);
        $months = JalaliDate::monthsWorkedSince('1405/01/01', '1405/05/01');

        $this->assertSame($months, $summary['months_worked']);
        $this->assertEqualsWithDelta(round($months * 20000, 2), $summary['total_earned_salary'], 0.01);
        $this->assertSame('credit', $summary['payroll_status']);
    }

    public function test_accrued_salary_increases_when_end_date_advances(): void
    {
        $employee = Employee::factory()->create([
            'salary' => 20000,
            'joining_date' => JalaliDate::toGregorian('1405/01/01'),
        ]);

        $earlier = $this->service->summary($employee, JalaliDate::toGregorian('1405/04/15'), 0);
        $later = $this->service->summary($employee, JalaliDate::toGregorian('1405/06/01'), 0);

        $this->assertGreaterThan($earlier['months_worked'], $later['months_worked']);
        $this->assertGreaterThan($earlier['total_earned_salary'], $later['total_earned_salary']);
    }

    public function test_partial_payments_reduce_remaining_balance(): void
    {
        $employee = Employee::factory()->create([
            'salary' => 20000,
            'joining_date' => JalaliDate::toGregorian('1405/03/01'),
            'end_date' => JalaliDate::toGregorian('1405/04/15'),
        ]);

        $summary = $this->service->summary($employee, totalPaid: 10000);

        $this->assertSame(30000.0, $summary['total_earned_salary']);
        $this->assertSame(10000.0, $summary['total_paid_salary']);
        $this->assertSame(20000.0, $summary['remaining_balance']);
        $this->assertSame(0.0, $summary['overpaid_amount']);
        $this->assertSame('credit', $summary['payroll_status']);
    }

    public function test_overpaid_when_total_paid_exceeds_earned_salary(): void
    {
        $employee = Employee::factory()->create([
            'salary' => 20000,
            'joining_date' => JalaliDate::toGregorian('1405/03/01'),
            'end_date' => JalaliDate::toGregorian('1405/04/15'),
        ]);

        $summary = $this->service->summary($employee, totalPaid: 35000);

        $this->assertSame(0.0, $summary['remaining_balance']);
        $this->assertSame(5000.0, $summary['overpaid_amount']);
        $this->assertSame('overpaid', $summary['payroll_status']);
    }

    public function test_settled_when_paid_equals_earned(): void
    {
        $employee = Employee::factory()->create([
            'salary' => 20000,
            'joining_date' => JalaliDate::toGregorian('1405/03/01'),
            'end_date' => JalaliDate::toGregorian('1405/04/15'),
        ]);

        $summary = $this->service->summary($employee, totalPaid: 30000);

        $this->assertSame(0.0, $summary['remaining_balance']);
        $this->assertSame(0.0, $summary['overpaid_amount']);
        $this->assertSame('settled', $summary['payroll_status']);
    }

    public function test_uses_employee_end_date_instead_of_today(): void
    {
        $employee = Employee::factory()->create([
            'salary' => 20000,
            'joining_date' => JalaliDate::toGregorian('1405/03/01'),
            'end_date' => JalaliDate::toGregorian('1405/04/15'),
        ]);

        Carbon::setTestNow(JalaliDate::toGregorian('1405/08/01'));

        $summary = $this->service->summary($employee, totalPaid: 0);

        $this->assertSame(1.5, $summary['months_worked']);
        $this->assertSame(30000.0, $summary['total_earned_salary']);
        $this->assertTrue($summary['has_custom_end_date']);
        $this->assertSame('1405/04/15', $summary['end_date_shamsi']);
    }

    public function test_absence_days_are_deducted_from_earned_salary(): void
    {
        $employee = Employee::factory()->create([
            'salary' => 30000,
            'joining_date' => JalaliDate::toGregorian('1405/03/01'),
            'end_date' => JalaliDate::toGregorian('1405/04/15'),
        ]);

        $employee->absences()->create([
            'absence_date' => JalaliDate::toGregorian('1405/03/10'),
            'days' => 3,
        ]);

        $summary = $this->service->summary($employee, totalPaid: 0);

        // 1.5 months * 30000 = 45000; daily = 1000; 3 days = 3000
        $this->assertSame(3, $summary['absence_days']);
        $this->assertSame(3000.0, $summary['absence_deduction']);
        $this->assertSame(45000.0, $summary['gross_earned_salary']);
        $this->assertSame(42000.0, $summary['total_earned_salary']);
        $this->assertSame(42000.0, $summary['remaining_balance']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
