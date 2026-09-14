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
            'end_date' => $this->end_date?->format('Y-m-d'),
            'end_date_shamsi' => $this->end_date
                ? JalaliDate::fromGregorian($this->end_date)
                : null,
            'status' => $this->status,
            'is_shared_with_contractor' => (bool) $this->is_shared_with_contractor,
            'contractor_salary_share_percent' => $this->is_shared_with_contractor
                ? (float) ($this->contractor_salary_share_percent ?? 50)
                : null,
            'total_paid' => $accrual['total_paid_salary'],
            'remaining_salary' => $accrual['remaining_balance'],
            'monthly_salary' => $accrual['monthly_salary'],
            'current_shamsi_date' => $accrual['end_date_shamsi'],
            'end_date_effective_shamsi' => $accrual['end_date_shamsi'],
            'has_custom_end_date' => $accrual['has_custom_end_date'],
            'months_worked' => $accrual['months_worked'],
            'absence_days' => $accrual['absence_days'],
            'absence_deduction' => $accrual['absence_deduction'],
            'gross_earned_salary' => $accrual['gross_earned_salary'],
            'total_earned_salary' => $accrual['total_earned_salary'],
            'total_paid_salary' => $accrual['total_paid_salary'],
            'remaining_balance' => $accrual['remaining_balance'],
            'overpaid_amount' => $accrual['overpaid_amount'],
            'payroll_status' => $accrual['payroll_status'],
            'absences' => EmployeeAbsenceResource::collection($this->whenLoaded('absences')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
