<?php

namespace App\Services;

use App\Exports\ReportExcelExport;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\InventoryMovement;
use App\Models\InventoryItem;
use App\Models\MarbleShipment;
use App\Models\PayrollPayment;
use App\Models\SankariStoneSale;
use App\Support\ActiveFinancialYear;
use App\Support\JalaliDate;
use App\Support\ReportFilterSummary;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportService
{
    public function __construct(
        private MpdfPdfService $pdf,
        private ReportAdvancedFilterService $advancedFilter,
    ) {}

    public function salesReport(array $filters = []): Collection
    {
        $query = MarbleShipment::query()->with(['customer', 'creator']);
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'shipment_date');

        if (! empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (! empty($filters['status'])) {
            if ($filters['status'] === 'pending') {
                $query->pending();
            } else {
                $query->where('status', $filters['status']);
            }
        }

        $this->advancedFilter->apply($query, 'sales', $filters['filter_by'] ?? null, $filters['filter_value'] ?? null);

        return $query->orderBy('shipment_date')->get();
    }

    public function paymentsReport(array $filters = []): Collection
    {
        $query = CustomerPayment::query()->with('customer');
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'payment_date');
        $this->advancedFilter->apply($query, 'payments', $filters['filter_by'] ?? null, $filters['filter_value'] ?? null);

        return $query->orderBy('payment_date')->get();
    }

    public function expensesReport(array $filters = []): Collection
    {
        $query = Expense::query()->with(['category']);
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'expense_date');
        $this->advancedFilter->apply($query, 'expenses', $filters['filter_by'] ?? null, $filters['filter_value'] ?? null);

        return $query->orderBy('expense_date')->get();
    }

    public function employeesReport(array $filters = []): Collection
    {
        $query = Employee::query();
        $this->applyDateFilters($query, $filters, 'joining_date');
        $this->advancedFilter->apply($query, 'employees', $filters['filter_by'] ?? null, $filters['filter_value'] ?? null);

        return $query->orderBy('name')->get();
    }

    public function payrollReport(array $filters = []): Collection
    {
        $query = PayrollPayment::query()->with(['employee', 'creator']);
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'payment_date');
        $this->advancedFilter->apply($query, 'payroll', $filters['filter_by'] ?? null, $filters['filter_value'] ?? null);

        return $query->orderBy('payment_date')->get();
    }

    public function employeePayrollSummaries(?int $employeeId = null): Collection
    {
        $accrualService = app(EmployeeSalaryAccrualService::class);

        $query = Employee::query()->withPayrollTotal()->orderBy('name');

        if ($employeeId) {
            $query->where('id', $employeeId);
        }

        return $query->get()->map(function (Employee $employee) use ($accrualService) {
            $summary = $accrualService->summary($employee, totalPaid: (float) $employee->total_paid);

            return array_merge([
                'id' => $employee->id,
                'name' => $employee->name,
            ], $summary);
        });
    }

    public function inventoryReport(array $filters = []): Collection
    {
        $query = InventoryMovement::query()->with(['inventoryItem', 'creator']);
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'movement_date');
        $this->advancedFilter->apply($query, 'inventory', $filters['filter_by'] ?? null, $filters['filter_value'] ?? null);

        return $query->orderBy('movement_date')->get();
    }

    public function dailyProductionReport(array $filters = []): Collection
    {
        $query = MarbleShipment::query()->with(['customer', 'creator']);
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'shipment_date');
        $this->advancedFilter->apply($query, 'daily-production', $filters['filter_by'] ?? null, $filters['filter_value'] ?? null);

        return $query->orderBy('shipment_date')->get();
    }

    public function monthlyProductionReport(array $filters = []): Collection
    {
        $query = MarbleShipment::query()->with('creator');
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'shipment_date');

        if (($filters['filter_by'] ?? '') === 'created_by' && ($filters['filter_value'] ?? '') !== '') {
            $term = addcslashes(trim((string) $filters['filter_value']), '%_\\');
            $query->whereHas('creator', fn ($q) => $q->where('name', 'like', '%'.$term.'%'));
        }

        $rows = $query->get()->groupBy(fn (MarbleShipment $shipment) => JalaliDate::fromGregorian($shipment->shipment_date, 'Y/m'))
            ->map(function (Collection $shipments, string $month) {
                return [
                    'month' => $month,
                    'shipment_count' => $shipments->count(),
                    'total_tons' => (float) $shipments->sum(fn (MarbleShipment $s) => (float) ($s->quantity_ton ?? 0)),
                    'total_sales' => (float) $shipments->where('status', MarbleShipment::STATUS_COMPLETED)->sum('total_amount'),
                ];
            })
            ->sortKeys()
            ->values();

        return $this->filterMonthlyProductionRows($rows, $filters);
    }

    public function customerBalancesReport(array $filters = []): Collection
    {
        $yearId = ActiveFinancialYear::isAllYearsMode() ? null : ActiveFinancialYear::activeYearId();

        $shipmentConstraint = fn ($q) => $q->completed();
        $paymentConstraint = fn ($q) => $q;

        if ($yearId) {
            $shipmentConstraint = fn ($q) => $q->completed()->where('financial_year_id', $yearId);
            $paymentConstraint = fn ($q) => $q->where('financial_year_id', $yearId);
        }

        if (! empty($filters['date_from']) || ! empty($filters['date_to'])) {
            $shipmentConstraint = function ($q) use ($shipmentConstraint, $filters) {
                $shipmentConstraint($q);
                if (! empty($filters['date_from'])) {
                    $q->whereDate('shipment_date', '>=', JalaliDate::toGregorian($filters['date_from']));
                }
                if (! empty($filters['date_to'])) {
                    $q->whereDate('shipment_date', '<=', JalaliDate::toGregorian($filters['date_to']));
                }
            };
            $paymentConstraint = function ($q) use ($paymentConstraint, $filters) {
                $paymentConstraint($q);
                if (! empty($filters['date_from'])) {
                    $q->whereDate('payment_date', '>=', JalaliDate::toGregorian($filters['date_from']));
                }
                if (! empty($filters['date_to'])) {
                    $q->whereDate('payment_date', '<=', JalaliDate::toGregorian($filters['date_to']));
                }
            };
        }

        return Customer::query()
            ->withSum(['shipments as total_sales' => $shipmentConstraint], 'total_amount')
            ->withSum(['payments as total_payments' => $paymentConstraint], 'amount')
            ->orderBy('name')
            ->get()
            ->map(function (Customer $customer) {
                $customer->outstanding_balance = (float) ($customer->total_sales ?? 0) - (float) ($customer->total_payments ?? 0);

                return $customer;
            });
    }

    public function profitLossReport(array $filters = []): array
    {
        $marbleSales = $this->sumInRange(
            MarbleShipment::query()->completed(),
            $filters,
            'shipment_date',
            'total_amount'
        );
        $sankariSales = $this->sumInRange(SankariStoneSale::query(), $filters, 'sale_date', 'total_amount');
        $expenses = $this->sumInRange(Expense::query(), $filters, 'expense_date', 'amount');
        $payroll = $this->sumInRange(PayrollPayment::query(), $filters, 'payment_date', 'amount');

        $income = $marbleSales + $sankariSales;
        $totalExpenses = $expenses + $payroll;

        return [
            'marble_sales' => $marbleSales,
            'sankari_sales' => $sankariSales,
            'total_income' => $income,
            'operating_expenses' => $expenses,
            'payroll_expenses' => $payroll,
            'total_expenses' => $totalExpenses,
            'net_profit' => $income - $totalExpenses,
        ];
    }

    public function profitLossMatchesFilter(array $report, array $filters): bool
    {
        if (empty($filters['filter_by']) || $filters['filter_value'] === null || $filters['filter_value'] === '') {
            return true;
        }

        $field = match ($filters['filter_by']) {
            'revenue' => 'total_income',
            'expense' => 'total_expenses',
            'net_profit' => 'net_profit',
            default => null,
        };

        if (! $field) {
            return true;
        }

        return (float) $report[$field] === (float) $filters['filter_value'];
    }

    public function exportExcel(string $reportType, array $filters, array $headings, Collection $rows): BinaryFileResponse
    {
        $filename = "{$reportType}_".now()->format('Ymd_His').'.xlsx';

        return Excel::download(new ReportExcelExport($headings, $rows), $filename);
    }

    public function exportPdf(string $reportType, array $filters, string $view, array $data): \Illuminate\Http\Response
    {
        $filename = "{$reportType}_".now()->format('Ymd_His').'.pdf';

        return $this->pdf->downloadFromView($filename, $view, array_merge($data, [
            'reportType' => $reportType,
            'reportTitle' => __('erp.reports.'.str_replace('-', '_', $reportType)),
            'filters' => $filters,
            'filterSummary' => ReportFilterSummary::forExport($filters),
            'generatedAt' => JalaliDate::fromGregorian(now()),
            'locale' => app()->getLocale(),
            'isRtl' => app()->getLocale() === 'ps',
        ]));
    }

    private function filterMonthlyProductionRows(Collection $rows, array $filters): Collection
    {
        if (empty($filters['filter_by']) || $filters['filter_value'] === null || $filters['filter_value'] === '') {
            return $rows;
        }

        return $rows->filter(function (array $row) use ($filters) {
            $value = $filters['filter_value'];

            return match ($filters['filter_by']) {
                'month' => str_contains($row['month'], (string) $value),
                'quantity' => (float) $row['shipment_count'] === (float) $value,
                'total_tons' => (float) $row['total_tons'] === (float) $value,
                default => true,
            };
        })->values();
    }

    private function applyFinancialYearFilter($query, array $filters = []): void
    {
        if (! ActiveFinancialYear::isAllYearsMode()) {
            $activeId = ActiveFinancialYear::activeYearId();

            if ($activeId) {
                $query->where('financial_year_id', $activeId);
            }
        }
    }

    private function applyDateFilters($query, array $filters, string $column): void
    {
        if (! empty($filters['date_from'])) {
            $query->whereDate($column, '>=', JalaliDate::toGregorian($filters['date_from']));
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate($column, '<=', JalaliDate::toGregorian($filters['date_to']));
        }
    }

    private function sumInRange($query, array $filters, string $dateColumn, string $sumColumn): float
    {
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, $dateColumn);

        return (float) $query->sum($sumColumn);
    }
}
