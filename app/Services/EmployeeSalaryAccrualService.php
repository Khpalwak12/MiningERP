<?php

namespace App\Services;

use App\Models\Employee;
use App\Support\JalaliDate;
use Carbon\Carbon;

class EmployeeSalaryAccrualService
{
    public const DAYS_PER_MONTH = 30;

    public function summary(Employee $employee, ?Carbon $asOf = null, ?float $totalPaid = null): array
    {
        $asOf = $this->resolveAsOfDate($employee, $asOf)->copy()->startOfDay();
        $endDateShamsi = JalaliDate::fromGregorian($asOf);
        $joiningShamsi = JalaliDate::fromGregorian($employee->joining_date);

        $monthsWorked = $joiningShamsi
            ? JalaliDate::monthsWorkedSince($joiningShamsi, $endDateShamsi)
            : 0;

        $monthlySalary = (float) $employee->salary;
        $grossEarned = round($monthsWorked * $monthlySalary, 2);

        $absenceDays = $this->totalAbsenceDays($employee);
        $dailyRate = $monthlySalary > 0 ? round($monthlySalary / self::DAYS_PER_MONTH, 2) : 0.0;
        $absenceDeduction = round($absenceDays * $dailyRate, 2);

        $totalEarned = round(max(0, $grossEarned - $absenceDeduction), 2);
        $totalPaid = $totalPaid ?? (float) $employee->total_paid;
        $totalPaid = round($totalPaid, 2);

        $balance = round($totalEarned - $totalPaid, 2);
        $remainingBalance = $balance > 0 ? $balance : 0.0;
        $overpaidAmount = $balance < 0 ? abs($balance) : 0.0;

        $status = match (true) {
            $overpaidAmount > 0 => 'overpaid',
            $remainingBalance > 0 => 'credit',
            default => 'settled',
        };

        return [
            'monthly_salary' => $monthlySalary,
            'joining_date_shamsi' => $joiningShamsi,
            'end_date_shamsi' => $endDateShamsi,
            'current_shamsi_date' => $endDateShamsi,
            'has_custom_end_date' => $employee->end_date !== null,
            'months_worked' => $monthsWorked,
            'absence_days' => $absenceDays,
            'absence_deduction' => $absenceDeduction,
            'gross_earned_salary' => $grossEarned,
            'total_earned_salary' => $totalEarned,
            'total_paid_salary' => $totalPaid,
            'remaining_balance' => $remainingBalance,
            'overpaid_amount' => $overpaidAmount,
            'payroll_status' => $status,
        ];
    }

    private function resolveAsOfDate(Employee $employee, ?Carbon $asOf): Carbon
    {
        if ($asOf !== null) {
            return $asOf;
        }

        if ($employee->end_date !== null) {
            return Carbon::parse($employee->end_date)->startOfDay();
        }

        return Carbon::today();
    }

    private function totalAbsenceDays(Employee $employee): int
    {
        if (array_key_exists('absences_days_sum', $employee->getAttributes())) {
            return (int) $employee->getAttributes()['absences_days_sum'];
        }

        if ($employee->relationLoaded('absences')) {
            return (int) $employee->absences->sum('days');
        }

        return (int) $employee->absences()->sum('days');
    }
}
