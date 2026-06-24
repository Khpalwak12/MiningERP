<?php

namespace App\Services;

use App\Exports\ReportExcelExport;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialYear;
use App\Models\InventoryItem;
use App\Models\MarbleShipment;
use App\Models\PayrollPayment;
use App\Models\SankariStoneSale;
use App\Support\JalaliDate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportService
{
    public function salesReport(array $filters = []): Collection
    {
        $query = MarbleShipment::query()->with(['customer']);
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

        return $query->orderBy('shipment_date')->get();
    }

    public function paymentsReport(array $filters = []): Collection
    {
        $query = CustomerPayment::query()->with('customer');
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'payment_date');

        if (! empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        return $query->orderBy('payment_date')->get();
    }

    public function sankariReport(array $filters = []): Collection
    {
        $query = SankariStoneSale::query();
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'sale_date');

        return $query->orderBy('sale_date')->get();
    }

    public function expensesReport(array $filters = []): Collection
    {
        $query = Expense::query()->with(['category']);
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'expense_date');

        if (! empty($filters['expense_category_id'])) {
            $query->where('expense_category_id', $filters['expense_category_id']);
        }

        return $query->orderBy('expense_date')->get();
    }

    public function payrollReport(array $filters = []): Collection
    {
        $query = PayrollPayment::query()->with('employee');
        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'payment_date');

        if (! empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        return $query->orderBy('payment_date')->get();
    }

    public function inventoryReport(array $filters = []): Collection
    {
        $query = InventoryItem::query();

        if (! empty($filters['low_stock'])) {
            $query->whereColumn('current_stock', '<=', 'min_stock');
        }

        return $query->orderBy('name')->get();
    }

    public function customerBalancesReport(array $filters = []): Collection
    {
        $yearId = $filters['financial_year_id'] ?? FinancialYear::query()->active()->value('id');

        $shipmentConstraint = fn ($q) => $q->completed();
        $paymentConstraint = fn ($q) => $q;

        if ($yearId) {
            $shipmentConstraint = fn ($q) => $q->completed()->where('financial_year_id', $yearId);
            $paymentConstraint = fn ($q) => $q->where('financial_year_id', $yearId);
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

    public function expenseByCategoryReport(array $filters = []): Collection
    {
        $query = Expense::query()
            ->selectRaw('expense_category_id, SUM(amount) as total')
            ->groupBy('expense_category_id');

        $this->applyFinancialYearFilter($query, $filters);
        $this->applyDateFilters($query, $filters, 'expense_date');

        return $query->with('category')->get();
    }

    public function exportExcel(string $reportType, array $filters, array $headings, Collection $rows): BinaryFileResponse
    {
        $filename = "{$reportType}_".now()->format('Ymd_His').'.xlsx';

        return Excel::download(new ReportExcelExport($headings, $rows), $filename);
    }

    public function exportPdf(string $reportType, array $filters, string $view, array $data): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView($view, array_merge($data, [
            'reportType' => $reportType,
            'filters' => $filters,
            'generatedAt' => JalaliDate::fromGregorian(now()),
        ]));

        return $pdf->download("{$reportType}_".now()->format('Ymd_His').'.pdf');
    }

    private function applyFinancialYearFilter($query, array $filters): void
    {
        if (! empty($filters['financial_year_id'])) {
            $query->where('financial_year_id', $filters['financial_year_id']);
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

    public function financialYearsForFilter(): Collection
    {
        return FinancialYear::query()->orderByDesc('start_date')->get();
    }
}
