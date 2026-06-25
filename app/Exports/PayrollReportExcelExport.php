<?php

namespace App\Exports;

use App\Services\ReportService;
use App\Support\JalaliDate;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PayrollReportExcelExport implements WithMultipleSheets
{
    public function __construct(
        private array $filters,
        private ReportService $service,
    ) {}

    public function sheets(): array
    {
        $summaries = $this->service->employeePayrollSummaries(
            ! empty($this->filters['employee_id']) ? (int) $this->filters['employee_id'] : null
        );

        $summaryRows = $summaries->map(fn ($row) => [
            $row['name'],
            $row['monthly_salary'],
            $row['months_worked'],
            $row['total_earned_salary'],
            $row['total_paid_salary'],
            $row['remaining_balance'],
            $row['overpaid_amount'],
            __('erp.payroll_statuses.'.$row['payroll_status']),
        ]);

        $payments = $this->service->payrollReport($this->filters);
        $paymentRows = $payments->map(fn ($row) => [
            JalaliDate::fromGregorian($row->payment_date),
            $row->employee?->name,
            $row->amount,
            $row->payment_type,
        ]);

        return [
            new ReportExcelExport([
                __('erp.fields.employee'),
                __('erp.fields.salary'),
                __('erp.fields.months_worked'),
                __('erp.fields.total_earned_salary'),
                __('erp.fields.total_paid_salary'),
                __('erp.fields.remaining_balance'),
                __('erp.fields.overpaid_amount'),
                __('erp.fields.status'),
            ], $summaryRows),
            new ReportExcelExport([
                __('erp.fields.date'),
                __('erp.fields.employee'),
                __('erp.fields.amount'),
                __('erp.fields.payment_type'),
            ], $paymentRows),
        ];
    }
}
