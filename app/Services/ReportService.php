<?php

namespace App\Services;

use App\Exports\ReportExcelExport;
use App\Models\ContractorPayment;
use App\Models\ContractorProduction;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\InventoryMovement;
use App\Models\InventoryItem;
use App\Models\MarbleShipment;
use App\Models\MachineryItem;
use App\Models\MineAsset;
use App\Models\PersonalHomeExpense;
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
        $query = MarbleShipment::query()->with(['customer', 'creator', 'mineType', 'stoneType']);
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

    public function salesReportSummary(Collection $rows): array
    {
        return [
            'quantity_ton' => round($rows->sum(fn ($row) => (float) ($row->quantity_ton ?? 0)), 3),
            'total_amount' => round($rows->sum(fn ($row) => (float) ($row->total_amount ?? 0)), 2),
        ];
    }

    public function paymentsReportSummary(Collection $rows): array
    {
        return ['amount' => $this->sumField($rows, 'amount')];
    }

    public function expensesReportSummary(Collection $rows): array
    {
        return ['amount' => $this->sumField($rows, 'amount')];
    }

    public function employeesReportSummary(Collection $rows): array
    {
        return ['salary' => $this->sumField($rows, 'salary')];
    }

    public function inventoryReportSummary(Collection $rows): array
    {
        return [
            'quantity_in' => round($rows->where('movement_type', 'in')->sum(fn ($row) => (float) $row->quantity), 3),
            'quantity_out' => round($rows->where('movement_type', 'out')->sum(fn ($row) => (float) $row->quantity), 3),
            'quantity' => round($rows->sum(fn ($row) => (float) $row->quantity), 3),
        ];
    }

    public function dailyProductionReportSummary(Collection $rows): array
    {
        return ['quantity_ton' => round($rows->sum(fn ($row) => (float) ($row->quantity_ton ?? 0)), 3)];
    }

    public function monthlyProductionReportSummary(Collection $rows): array
    {
        return [
            'shipment_count' => (int) $rows->sum(fn ($row) => (int) data_get($row, 'shipment_count', 0)),
            'total_tons' => round($rows->sum(fn ($row) => (float) data_get($row, 'total_tons', 0)), 3),
            'total_sales' => round($rows->sum(fn ($row) => (float) data_get($row, 'total_sales', 0)), 2),
        ];
    }

    public function customerBalancesReportSummary(Collection $rows): array
    {
        return [
            'total_sales' => $this->sumField($rows, 'total_sales'),
            'total_payments' => $this->sumField($rows, 'total_payments'),
            'outstanding_balance' => $this->sumField($rows, 'outstanding_balance'),
        ];
    }

    public function contractorProductionReportSummary(Collection $rows): array
    {
        return [
            'quantity_ton' => round($rows->sum(fn ($row) => (float) ($row->quantity_ton ?? 0)), 3),
            'total_royalty' => $this->sumField($rows, 'total_royalty'),
        ];
    }

    public function contractorPaymentsReportSummary(Collection $rows): array
    {
        return ['amount' => $this->sumField($rows, 'amount')];
    }

    public function payrollReportSummary(Collection $summaries, Collection $payments): array
    {
        return [
            'total_earned_salary' => round($summaries->sum(fn ($row) => (float) data_get($row, 'total_earned_salary', 0)), 2),
            'total_paid_salary' => round($summaries->sum(fn ($row) => (float) data_get($row, 'total_paid_salary', 0)), 2),
            'remaining_balance' => round($summaries->sum(fn ($row) => (float) data_get($row, 'remaining_balance', 0)), 2),
            'overpaid_amount' => round($summaries->sum(fn ($row) => (float) data_get($row, 'overpaid_amount', 0)), 2),
            'payment_amount' => $this->sumField($payments, 'amount'),
        ];
    }

    private function sumField(Collection $rows, string $field, int $precision = 2): float
    {
        return round($rows->sum(fn ($row) => (float) (data_get($row, $field) ?? 0)), $precision);
    }

    public function paymentsReport(array $filters = []): Collection
    {
        $query = CustomerPayment::query()->with('customer');
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'payment_date');
        $this->advancedFilter->apply($query, 'payments', $filters['filter_by'] ?? null, $filters['filter_value'] ?? null);

        return $query->orderBy('payment_date')->get();
    }

    public function sankariReport(array $filters = []): Collection
    {
        $query = SankariStoneSale::query()->with('creator');
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'sale_date');
        $this->advancedFilter->apply($query, 'sankari', $filters['filter_by'] ?? null, $filters['filter_value'] ?? null);

        return $query->orderBy('sale_date')->orderBy('id')->get();
    }

    public function sankariReportSummary(Collection $rows): array
    {
        return [
            'truck_count' => (int) $rows->sum('truck_count'),
            'subtotal' => round($rows->sum(fn (SankariStoneSale $sale) => $sale->subtotal()), 2),
            'discount' => round($rows->sum(fn (SankariStoneSale $sale) => (float) $sale->discount), 2),
            'total_amount' => round($rows->sum(fn (SankariStoneSale $sale) => (float) $sale->total_amount), 2),
        ];
    }

    public function expensesReport(array $filters = []): Collection
    {
        $query = Expense::query()->forCompany()->with(['category']);
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'expense_date');
        $this->advancedFilter->apply($query, 'expenses', $filters['filter_by'] ?? null, $filters['filter_value'] ?? null);

        return $query->orderBy('expense_date')->get();
    }

    public function contractorExpensesReport(array $filters = []): Collection
    {
        $query = Expense::query()->forContractor()->with(['category']);
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

    public function contractorProductionReport(array $filters = []): Collection
    {
        $query = ContractorProduction::query()->with('creator');
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'production_date');

        return $query->orderBy('production_date')->get();
    }

    public function contractorPaymentsReport(array $filters = []): Collection
    {
        $query = ContractorPayment::query()->with('creator');
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'payment_date');

        return $query->orderBy('payment_date')->get();
    }

    public function mineAssetsReport(array $filters = []): Collection
    {
        $query = MineAsset::query()->with('creator');
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'registration_date');

        if (! empty($filters['status']) && in_array($filters['status'], [MineAsset::STATUS_USABLE, MineAsset::STATUS_UNUSABLE], true)) {
            $query->where('status', $filters['status']);
        }

        $this->advancedFilter->apply($query, 'mine-assets', $filters['filter_by'] ?? null, $filters['filter_value'] ?? null);

        return $query->orderBy('registration_date')->orderByDesc('id')->get();
    }

    public function personalLedgerReport(array $filters = []): array
    {
        $service = app(PersonalAccountsReportService::class);

        return [
            'summary' => $service->ledgerSummary($filters),
            'transactions' => $service->ledgerTransactions($filters),
            'contact_balances' => $service->contactBalances($filters),
        ];
    }

    public function personalHomeExpensesReport(array $filters = []): Collection
    {
        $query = PersonalHomeExpense::query()->with('creator');
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'expense_date');
        $this->advancedFilter->apply($query, 'personal-home-expenses', $filters['filter_by'] ?? null, $filters['filter_value'] ?? null);

        return $query->orderBy('expense_date')->orderByDesc('id')->get();
    }

    public function machineryReport(array $filters = []): Collection
    {
        $query = MachineryItem::query()->with('creator');
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'purchase_date');

        if (! empty($filters['currency']) && in_array($filters['currency'], [MachineryItem::CURRENCY_AFN, MachineryItem::CURRENCY_USD], true)) {
            $query->where('currency', $filters['currency']);
        }

        $this->advancedFilter->apply($query, 'machinery', $filters['filter_by'] ?? null, $filters['filter_value'] ?? null);

        return $query->orderBy('purchase_date')->orderByDesc('id')->get();
    }

    public function contractorLedgerReport(array $filters = []): array
    {
        $ledgerService = app(ContractorRoyaltyLedgerService::class);

        return [
            'summary' => $ledgerService->summary($filters),
            'transactions' => $ledgerService->transactions($filters),
        ];
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
        $contractorRoyalty = $this->sumInRange(ContractorProduction::query(), $filters, 'production_date', 'total_royalty');
        $expenses = $this->sumInRange(Expense::query()->forCompany(), $filters, 'expense_date', 'amount');
        $payroll = $this->sumInRange(PayrollPayment::query(), $filters, 'payment_date', 'amount');

        $income = $marbleSales + $sankariSales + $contractorRoyalty;
        $totalExpenses = $expenses + $payroll;

        return [
            'marble_sales' => $marbleSales,
            'sankari_sales' => $sankariSales,
            'contractor_royalty' => $contractorRoyalty,
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
