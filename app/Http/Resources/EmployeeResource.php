<?php

namespace App\Http\Resources;

use App\Services\EmployeeSalaryAccrualService;
use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $accrual = app(EmployeeSalaryAccrualService::class)->summary(
            $this->resource,
            totalPaid: (float) $this->total_paid,
        );

        return [
            'id' => $this->id,
            'name' => $this->name,
            'father_name' => $this->father_name,
            'phone' => $this->phone,
            'position' => $this->position,
            'salary' => (float) $this->salary,
            'joining_date' => $this->joining_date?->format('Y-m-d'),
            'joining_date_shamsi' => JalaliDate::fromGregorian($this->joining_date),
            'status' => $this->status,
            'total_paid' => $accrual['total_paid_salary'],
            'remaining_salary' => $accrual['remaining_balance'],
            'monthly_salary' => $accrual['monthly_salary'],
            'current_shamsi_date' => $accrual['current_shamsi_date'],
            'months_worked' => $accrual['months_worked'],
            'total_earned_salary' => $accrual['total_earned_salary'],
            'total_paid_salary' => $accrual['total_paid_salary'],
            'remaining_balance' => $accrual['remaining_balance'],
            'overpaid_amount' => $accrual['overpaid_amount'],
            'payroll_status' => $accrual['payroll_status'],
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
