<?php

namespace App\Services;

use App\Models\Employee;
use App\Support\JalaliDate;
use Carbon\Carbon;

class EmployeeSalaryAccrualService
{
    public function summary(Employee $employee, ?Carbon $asOf = null, ?float $totalPaid = null): array
    {
        $asOf = ($asOf ?? Carbon::today())->copy()->startOfDay();
        $currentShamsi = JalaliDate::fromGregorian($asOf);
        $joiningShamsi = JalaliDate::fromGregorian($employee->joining_date);

        $monthsWorked = $joiningShamsi
            ? JalaliDate::monthsWorkedSince($joiningShamsi, $currentShamsi)
            : 0;

        $monthlySalary = (float) $employee->salary;
        $totalEarned = round($monthsWorked * $monthlySalary, 2);
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
            'current_shamsi_date' => $currentShamsi,
            'months_worked' => $monthsWorked,
            'total_earned_salary' => $totalEarned,
            'total_paid_salary' => $totalPaid,
            'remaining_balance' => $remainingBalance,
            'overpaid_amount' => $overpaidAmount,
            'payroll_status' => $status,
        ];
    }
}
