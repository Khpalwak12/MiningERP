<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Http\Resources\ExpenseResource;
use App\Http\Resources\MarbleShipmentResource;
use App\Services\ReportService;
use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(private ReportService $service)
    {
        $this->middleware('permission:reports.view')->except(['exportExcel', 'exportPdf']);
        $this->middleware('permission:reports.export')->only(['exportExcel', 'exportPdf']);
    }

    public function index(): Response
    {
        return Inertia::render('Reports/Index');
    }

    public function sales(Request $request): Response
    {
        $filters = $request->only(['date_from', 'date_to', 'customer_id', 'status']);
        $data = $this->service->salesReport($filters);

        return Inertia::render('Reports/Sales', [
            'rows' => MarbleShipmentResource::collection($data),
            'filters' => $filters,
        ]);
    }

    public function payments(Request $request): Response
    {
        $filters = $request->only(['date_from', 'date_to', 'customer_id']);
        $data = $this->service->paymentsReport($filters);

        return Inertia::render('Reports/Payments', [
            'rows' => $data,
            'filters' => $filters,
        ]);
    }

    public function expenses(Request $request): Response
    {
        $filters = $request->only(['date_from', 'date_to', 'expense_category_id']);
        $data = $this->service->expensesReport($filters);

        return Inertia::render('Reports/Expenses', [
            'rows' => ExpenseResource::collection($data),
            'filters' => $filters,
        ]);
    }

    public function payroll(Request $request): Response
    {
        $filters = $request->only(['date_from', 'date_to', 'employee_id']);
        $data = $this->service->payrollReport($filters);

        return Inertia::render('Reports/Payroll', [
            'rows' => $data,
            'filters' => $filters,
        ]);
    }

    public function inventory(Request $request): Response
    {
        $filters = $request->only(['low_stock']);
        $data = $this->service->inventoryReport($filters);

        return Inertia::render('Reports/Inventory', [
            'rows' => $data,
            'filters' => $filters,
        ]);
    }

    public function customerBalances(): Response
    {
        $data = $this->service->customerBalancesReport();

        return Inertia::render('Reports/CustomerBalances', [
            'customers' => CustomerResource::collection($data),
        ]);
    }

    public function profitLoss(Request $request): Response
    {
        $filters = $request->only(['date_from', 'date_to']);
        $data = $this->service->profitLossReport($filters);

        return Inertia::render('Reports/ProfitLoss', [
            'report' => $data,
            'filters' => $filters,
        ]);
    }

    public function exportExcel(Request $request, string $type): BinaryFileResponse
    {
        $filters = $request->only(['date_from', 'date_to', 'customer_id', 'employee_id', 'expense_category_id', 'low_stock', 'status']);

        [$headings, $rows] = match ($type) {
            'sales' => $this->salesExportData($filters),
            'payments' => $this->paymentsExportData($filters),
            'expenses' => $this->expensesExportData($filters),
            'payroll' => $this->payrollExportData($filters),
            'inventory' => $this->inventoryExportData($filters),
            'customer-balances' => $this->customerBalancesExportData(),
            'profit-loss' => $this->profitLossExportData($filters),
            default => abort(404),
        };

        return $this->service->exportExcel($type, $filters, $headings, $rows);
    }

    public function exportPdf(Request $request, string $type): \Illuminate\Http\Response
    {
        $filters = $request->only(['date_from', 'date_to', 'customer_id', 'employee_id', 'expense_category_id', 'low_stock', 'status']);

        [$view, $data] = match ($type) {
            'sales' => ['reports.pdf.sales', ['rows' => $this->service->salesReport($filters)]],
            'payments' => ['reports.pdf.payments', ['rows' => $this->service->paymentsReport($filters)]],
            'expenses' => ['reports.pdf.expenses', ['rows' => $this->service->expensesReport($filters)]],
            'payroll' => ['reports.pdf.payroll', ['rows' => $this->service->payrollReport($filters)]],
            'inventory' => ['reports.pdf.inventory', ['rows' => $this->service->inventoryReport($filters)]],
            'customer-balances' => ['reports.pdf.customer-balances', ['rows' => $this->service->customerBalancesReport()]],
            'profit-loss' => ['reports.pdf.profit-loss', ['report' => $this->service->profitLossReport($filters)]],
            default => abort(404),
        };

        return $this->service->exportPdf($type, $filters, $view, $data);
    }

    private function salesExportData(array $filters): array
    {
        $rows = $this->service->salesReport($filters)->map(fn ($row) => [
            JalaliDate::fromGregorian($row->shipment_date),
            $row->customer?->name,
            $row->quantity_ton ?? '—',
            $row->price_per_ton ?? '—',
            $row->total_amount ?? '—',
            __('erp.shipments.statuses.'.$row->status),
        ]);

        return [['Date', 'Customer', 'Quantity (Ton)', 'Price/Ton', 'Total', 'Status'], $rows];
    }

    private function paymentsExportData(array $filters): array
    {
        $rows = $this->service->paymentsReport($filters)->map(fn ($row) => [
            JalaliDate::fromGregorian($row->payment_date),
            $row->customer?->name,
            $row->amount,
            $row->receipt_number ?? '—',
            $row->received_by,
        ]);

        return [['Date', 'Customer', 'Amount', 'Receipt Number', 'Received By'], $rows];
    }

    private function expensesExportData(array $filters): array
    {
        $rows = $this->service->expensesReport($filters)->map(fn ($row) => [
            JalaliDate::fromGregorian($row->expense_date),
            $row->category?->localized_name,
            $row->subcategory ?? '—',
            $row->bill_number ?? '—',
            $row->amount,
        ]);

        return [['Date', 'Category', 'Subcategory', 'Bill Number', 'Amount'], $rows];
    }

    private function payrollExportData(array $filters): array
    {
        $rows = $this->service->payrollReport($filters)->map(fn ($row) => [
            JalaliDate::fromGregorian($row->payment_date),
            $row->employee?->name,
            $row->amount,
            $row->payment_type,
            $row->period_month,
        ]);

        return [['Date', 'Employee', 'Amount', 'Type', 'Period'], $rows];
    }

    private function inventoryExportData(array $filters): array
    {
        $rows = $this->service->inventoryReport($filters)->map(fn ($row) => [
            $row->name,
            $row->sku,
            $row->unit,
            $row->current_stock,
            $row->min_stock,
        ]);

        return [['Name', 'SKU', 'Unit', 'Current Stock', 'Min Stock'], $rows];
    }

    private function customerBalancesExportData(): array
    {
        $rows = $this->service->customerBalancesReport()->map(fn ($row) => [
            $row->name,
            $row->total_sales ?? 0,
            $row->total_payments ?? 0,
            $row->outstanding_balance ?? 0,
        ]);

        return [['Customer', 'Total Sales', 'Total Payments', 'Outstanding'], $rows];
    }

    private function profitLossExportData(array $filters): array
    {
        $report = $this->service->profitLossReport($filters);

        $rows = collect([
            [__('erp.reports.marble_sales'), $report['marble_sales']],
            [__('erp.reports.sankari_sales'), $report['sankari_sales']],
            [__('erp.reports.total_income'), $report['total_income']],
            [__('erp.reports.operating_expenses'), $report['operating_expenses']],
            [__('erp.reports.payroll_expenses'), $report['payroll_expenses']],
            [__('erp.reports.total_expenses'), $report['total_expenses']],
            [__('erp.reports.net_profit'), $report['net_profit']],
        ]);

        return [['Item', 'Amount'], $rows];
    }
}
