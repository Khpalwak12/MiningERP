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
            ($this->filters['filter_by'] ?? '') === 'employee' && ! empty($this->filters['filter_value'])
                ? (int) $this->filters['filter_value']
                : null
        );

        $payments = $this->service->payrollReport($this->filters);
        $summary = $this->service->payrollReportSummary($summaries, $payments);

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

        if ($summaries->isNotEmpty()) {
            $summaryRows->push([
                __('erp.reports.totals'),
                '',
                '',
                $summary['total_earned_salary'],
                $summary['total_paid_salary'],
                $summary['remaining_balance'],
                $summary['overpaid_amount'],
                '',
            ]);
        }

        $paymentRows = $payments->map(fn ($row) => [
            JalaliDate::fromGregorian($row->payment_date),
            $row->employee?->name,
            $row->amount,
            $row->payment_type,
        ]);

        if ($payments->isNotEmpty()) {
            $paymentRows->push([
                __('erp.reports.totals'),
                '',
                $summary['payment_amount'],
                '',
            ]);
        }

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
