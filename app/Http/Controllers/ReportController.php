<?php

namespace App\Http\Controllers;

use App\Exports\PayrollReportExcelExport;
use App\Http\Resources\ContractorPaymentResource;
use App\Http\Resources\ContractorProductionResource;
use App\Http\Resources\CustomerPaymentResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\ExpenseCategoryResource;
use App\Http\Resources\ExpenseResource;
use App\Http\Resources\InventoryMovementResource;
use App\Http\Resources\MarbleShipmentResource;
use App\Http\Resources\MachineryItemResource;
use App\Http\Resources\MineAssetResource;
use App\Http\Resources\PersonalContactResource;
use App\Http\Resources\PersonalHomeExpenseResource;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\ExpenseCategory;
use App\Models\PersonalContact;
use App\Services\ReportService;
use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(private ReportService $service)
    {
        $this->middleware('permission:reports.view')->except(['exportExcel', 'exportPdf', 'contractorProduction', 'contractorPayments', 'contractorExpenses', 'contractorLedger']);
        $this->middleware('permission:contractor-royalty.reports')->only(['contractorProduction', 'contractorPayments', 'contractorExpenses', 'contractorLedger']);
        $this->middleware(function ($request, $next) {
            $type = $request->route('type');
            $contractorTypes = ['contractor-production', 'contractor-payments', 'contractor-expenses', 'contractor-ledger'];

            if (in_array($type, $contractorTypes, true)) {
                abort_unless($request->user()?->can('contractor-royalty.reports'), 403);
            } else {
                abort_unless($request->user()?->can('reports.export'), 403);
            }

            return $next($request);
        })->only(['exportExcel', 'exportPdf']);
    }

    public function index(): Response
    {
        return Inertia::render('Reports/Index');
    }

    public function sales(Request $request): Response
    {
        $filters = $this->reportFilters($request, ['customer_id', 'status']);
        $data = $this->service->salesReport($filters);

        return Inertia::render('Reports/Sales', array_merge(
            $this->filterLookups($filters),
            [
                'rows' => MarbleShipmentResource::collection($data),
                'filters' => $filters,
            ]
        ));
    }

    public function payments(Request $request): Response
    {
        $filters = $this->reportFilters($request);
        $data = $this->service->paymentsReport($filters);

        return Inertia::render('Reports/Payments', array_merge(
            $this->filterLookups($filters),
            [
                'rows' => CustomerPaymentResource::collection($data),
                'filters' => $filters,
            ]
        ));
    }

    public function expenses(Request $request): Response
    {
        $filters = $this->reportFilters($request);
        $data = $this->service->expensesReport($filters);

        return Inertia::render('Reports/Expenses', array_merge(
            $this->filterLookups($filters),
            [
                'rows' => ExpenseResource::collection($data),
                'filters' => $filters,
            ]
        ));
    }

    public function employees(Request $request): Response
    {
        $filters = $this->reportFilters($request);
        $data = $this->service->employeesReport($filters);

        return Inertia::render('Reports/Employees', array_merge(
            $this->filterLookups($filters),
            [
                'rows' => EmployeeResource::collection($data),
                'filters' => $filters,
            ]
        ));
    }

    public function payroll(Request $request): Response
    {
        $filters = $this->reportFilters($request);
        $data = $this->service->payrollReport($filters);

        return Inertia::render('Reports/Payroll', array_merge(
            $this->filterLookups($filters),
            [
                'rows' => $data,
                'summaries' => $this->service->employeePayrollSummaries($this->employeeIdFromFilters($filters)),
                'filters' => $filters,
            ]
        ));
    }

    public function inventory(Request $request): Response
    {
        $filters = $this->reportFilters($request);
        $data = $this->service->inventoryReport($filters);

        return Inertia::render('Reports/Inventory', [
            'rows' => InventoryMovementResource::collection($data),
            'filters' => $filters,
        ]);
    }

    public function mineAssets(Request $request): Response
    {
        $filters = $this->reportFilters($request, ['status']);
        $data = $this->service->mineAssetsReport($filters);

        return Inertia::render('Reports/MineAssets', [
            'rows' => MineAssetResource::collection($data),
            'filters' => $filters,
            'total' => (float) $data->sum('quantity'),
        ]);
    }

    public function personalLedger(Request $request): Response
    {
        $filters = $this->reportFilters($request, ['personal_contact_id', 'currency']);
        $report = $this->service->personalLedgerReport($filters);

        return Inertia::render('Reports/PersonalLedger', array_merge(
            $this->personalAccountFilterLookups($filters),
            [
                'summary' => $report['summary'],
                'transactions' => $report['transactions'],
                'contactBalances' => $report['contact_balances'],
                'filters' => $filters,
            ]
        ));
    }

    public function personalHomeExpenses(Request $request): Response
    {
        $filters = $this->reportFilters($request);
        $data = $this->service->personalHomeExpensesReport($filters);

        return Inertia::render('Reports/PersonalHomeExpenses', [
            'rows' => PersonalHomeExpenseResource::collection($data),
            'filters' => $filters,
            'total' => (float) $data->sum('amount'),
        ]);
    }

    public function machinery(Request $request): Response
    {
        $filters = $this->reportFilters($request, ['currency']);
        $data = $this->service->machineryReport($filters);

        return Inertia::render('Reports/Machinery', [
            'rows' => MachineryItemResource::collection($data),
            'filters' => $filters,
            'totals' => [
                'AFN' => (float) $data->where('currency', 'AFN')->sum('amount'),
                'USD' => (float) $data->where('currency', 'USD')->sum('amount'),
            ],
        ]);
    }

    public function dailyProduction(Request $request): Response
    {
        $filters = $this->reportFilters($request);
        $data = $this->service->dailyProductionReport($filters);

        return Inertia::render('Reports/DailyProduction', [
            'rows' => MarbleShipmentResource::collection($data),
            'filters' => $filters,
        ]);
    }

    public function monthlyProduction(Request $request): Response
    {
        $filters = $this->reportFilters($request);
        $data = $this->service->monthlyProductionReport($filters);

        return Inertia::render('Reports/MonthlyProduction', [
            'rows' => $data,
            'filters' => $filters,
        ]);
    }

    public function customerBalances(Request $request): Response
    {
        $filters = $this->reportFilters($request);
        $data = $this->service->customerBalancesReport($filters);

        return Inertia::render('Reports/CustomerBalances', [
            'customers' => CustomerResource::collection($data),
            'filters' => $filters,
        ]);
    }

    public function profitLoss(Request $request): Response
    {
        $filters = $this->reportFilters($request);
        $data = $this->service->profitLossReport($filters);
        $matchesFilter = $this->service->profitLossMatchesFilter($data, $filters);

        return Inertia::render('Reports/ProfitLoss', [
            'report' => $data,
            'filters' => $filters,
            'matchesFilter' => $matchesFilter,
        ]);
    }

    public function contractorProduction(Request $request): Response
    {
        $filters = $this->reportFilters($request);

        return Inertia::render('Reports/ContractorProduction', [
            'rows' => ContractorProductionResource::collection($this->service->contractorProductionReport($filters)),
            'filters' => $filters,
        ]);
    }

    public function contractorPayments(Request $request): Response
    {
        $filters = $this->reportFilters($request);

        return Inertia::render('Reports/ContractorPayments', [
            'rows' => ContractorPaymentResource::collection($this->service->contractorPaymentsReport($filters)),
            'filters' => $filters,
        ]);
    }

    public function contractorExpenses(Request $request): Response
    {
        $filters = $this->reportFilters($request);
        $data = $this->service->contractorExpensesReport($filters);

        return Inertia::render('Reports/ContractorExpenses', array_merge(
            $this->filterLookups($filters),
            [
                'rows' => ExpenseResource::collection($data),
                'filters' => $filters,
                'total' => (float) $data->sum('amount'),
            ]
        ));
    }

    public function contractorLedger(Request $request): Response
    {
        $filters = $this->reportFilters($request);
        $report = $this->service->contractorLedgerReport($filters);

        return Inertia::render('Reports/ContractorLedger', [
            'summary' => $report['summary'],
            'transactions' => $report['transactions'],
            'filters' => $filters,
        ]);
    }

    public function exportExcel(Request $request, string $type): BinaryFileResponse
    {
        $filters = $this->exportFilters($request, $type);

        if ($type === 'payroll') {
            $filename = 'payroll_'.now()->format('Ymd_His').'.xlsx';

            return Excel::download(new PayrollReportExcelExport($filters, $this->service), $filename);
        }

        [$headings, $rows] = match ($type) {
            'sales' => $this->salesExportData($filters),
            'payments' => $this->paymentsExportData($filters),
            'expenses' => $this->expensesExportData($filters),
            'employees' => $this->employeesExportData($filters),
            'payroll' => abort(404),
            'inventory' => $this->inventoryExportData($filters),
            'mine-assets' => $this->mineAssetsExportData($filters),
            'personal-ledger' => $this->personalLedgerExportData($filters),
            'personal-home-expenses' => $this->personalHomeExpensesExportData($filters),
            'machinery' => $this->machineryExportData($filters),
            'daily-production' => $this->dailyProductionExportData($filters),
            'monthly-production' => $this->monthlyProductionExportData($filters),
            'customer-balances' => $this->customerBalancesExportData($filters),
            'profit-loss' => $this->profitLossExportData($filters),
            'contractor-production' => $this->contractorProductionExportData($filters),
            'contractor-payments' => $this->contractorPaymentsExportData($filters),
            'contractor-expenses' => $this->contractorExpensesExportData($filters),
            'contractor-ledger' => $this->contractorLedgerExportData($filters),
            default => abort(404),
        };

        return $this->service->exportExcel($type, $filters, $headings, $rows);
    }

    public function exportPdf(Request $request, string $type): \Illuminate\Http\Response
    {
        $filters = $this->exportFilters($request, $type);

        [$view, $data] = match ($type) {
            'sales' => ['reports.pdf.sales', ['rows' => $this->service->salesReport($filters)]],
            'payments' => ['reports.pdf.payments', ['rows' => $this->service->paymentsReport($filters)]],
            'expenses' => ['reports.pdf.expenses', ['rows' => $this->service->expensesReport($filters)]],
            'employees' => ['reports.pdf.employees', ['rows' => $this->service->employeesReport($filters)]],
            'payroll' => ['reports.pdf.payroll', [
                'rows' => $this->service->payrollReport($filters),
                'summaries' => $this->service->employeePayrollSummaries($this->employeeIdFromFilters($filters)),
            ]],
            'inventory' => ['reports.pdf.inventory', ['rows' => $this->service->inventoryReport($filters)]],
            'mine-assets' => ['reports.pdf.mine-assets', ['rows' => $this->service->mineAssetsReport($filters)]],
            'personal-ledger' => ['reports.pdf.personal-ledger', array_merge(
                $this->service->personalLedgerReport($filters),
                ['filters' => $filters]
            )],
            'personal-home-expenses' => ['reports.pdf.personal-home-expenses', [
                'rows' => $this->service->personalHomeExpensesReport($filters),
            ]],
            'machinery' => ['reports.pdf.machinery', [
                'rows' => $this->service->machineryReport($filters),
            ]],
            'daily-production' => ['reports.pdf.daily-production', ['rows' => $this->service->dailyProductionReport($filters)]],
            'monthly-production' => ['reports.pdf.monthly-production', ['rows' => $this->service->monthlyProductionReport($filters)]],
            'customer-balances' => ['reports.pdf.customer-balances', ['rows' => $this->service->customerBalancesReport($filters)]],
            'profit-loss' => ['reports.pdf.profit-loss', [
                'report' => $this->service->profitLossReport($filters),
                'matchesFilter' => $this->service->profitLossMatchesFilter(
                    $this->service->profitLossReport($filters),
                    $filters
                ),
            ]],
            'contractor-production' => ['reports.pdf.contractor-production', [
                'rows' => $this->service->contractorProductionReport($filters),
            ]],
            'contractor-payments' => ['reports.pdf.contractor-payments', [
                'rows' => $this->service->contractorPaymentsReport($filters),
            ]],
            'contractor-expenses' => ['reports.pdf.contractor-expenses', [
                'rows' => $this->service->contractorExpensesReport($filters),
            ]],
            'contractor-ledger' => ['reports.pdf.contractor-ledger', array_merge(
                $this->service->contractorLedgerReport($filters),
                ['filters' => $filters]
            )],
            default => abort(404),
        };

        return $this->service->exportPdf($type, $filters, $view, $data);
    }

    private function reportFilters(Request $request, array $extra = []): array
    {
        return $request->only(array_merge(['date_from', 'date_to', 'filter_by', 'filter_value'], $extra));
    }

    private function exportFilters(Request $request, string $type): array
    {
        $extra = match ($type) {
            'sales' => ['customer_id', 'status'],
            'mine-assets' => ['status'],
            'personal-ledger' => ['personal_contact_id', 'currency'],
            'machinery' => ['currency'],
            default => [],
        };

        return $this->reportFilters($request, $extra);
    }

    private function employeeIdFromFilters(array $filters): ?int
    {
        if (($filters['filter_by'] ?? '') === 'employee' && ! empty($filters['filter_value'])) {
            return (int) $filters['filter_value'];
        }

        return null;
    }

    private function filterLookups(array $filters): array
    {
        $selectedCustomer = null;
        if (! empty($filters['customer_id'])) {
            $selectedCustomer = Customer::query()->find($filters['customer_id']);
        } elseif (($filters['filter_by'] ?? '') === 'customer' && ! empty($filters['filter_value'])) {
            $selectedCustomer = Customer::query()->find($filters['filter_value']);
        }

        $selectedEmployee = null;
        if (($filters['filter_by'] ?? '') === 'employee' && ! empty($filters['filter_value'])) {
            $selectedEmployee = Employee::query()->find($filters['filter_value']);
        }

        return [
            'customers' => CustomerResource::collection(
                Customer::query()->where('status', 'active')->orderBy('name')->get()
            ),
            'employees' => EmployeeResource::collection(
                Employee::query()->orderBy('name')->get()
            ),
            'expenseCategories' => ExpenseCategoryResource::collection(ExpenseCategory::listForSelect()),
            'selectedCustomer' => $selectedCustomer ? new CustomerResource($selectedCustomer) : null,
            'selectedEmployee' => $selectedEmployee ? new EmployeeResource($selectedEmployee) : null,
        ];
    }

    private function personalAccountFilterLookups(array $filters): array
    {
        $selectedContact = null;
        if (! empty($filters['personal_contact_id'])) {
            $selectedContact = PersonalContact::query()->find($filters['personal_contact_id']);
        }

        return [
            'contacts' => PersonalContactResource::collection(
                PersonalContact::query()->orderBy('name')->get()
            ),
            'selectedContact' => $selectedContact ? new PersonalContactResource($selectedContact) : null,
        ];
    }

    private function salesExportData(array $filters): array
    {
        $data = $this->service->salesReport($filters);
        $rows = $data->map(fn ($row) => [
            JalaliDate::fromGregorian($row->shipment_date),
            $row->customer?->name,
            $row->quantity_ton ?? '—',
            $row->price_per_ton ?? '—',
            $row->total_amount ?? '—',
            __('erp.shipments.statuses.'.$row->status),
        ]);

        return [[
            __('erp.fields.date'),
            __('erp.fields.customer'),
            __('erp.fields.quantity_ton'),
            __('erp.fields.price_per_ton'),
            __('erp.fields.total_amount'),
            __('erp.fields.status'),
        ], $rows];
    }

    private function paymentsExportData(array $filters): array
    {
        $data = $this->service->paymentsReport($filters);
        $rows = $data->map(fn ($row) => [
            JalaliDate::fromGregorian($row->payment_date),
            $row->customer?->name,
            $row->amount,
            $row->receipt_number ?? '—',
            $row->received_by,
        ]);

        $rows->push([
            __('erp.fields.total_amount'),
            '',
            $data->sum('amount'),
            '',
            '',
        ]);

        return [[
            __('erp.fields.date'),
            __('erp.fields.customer'),
            __('erp.fields.amount'),
            __('erp.fields.receipt_number'),
            __('erp.fields.received_by'),
        ], $rows];
    }

    private function expensesExportData(array $filters): array
    {
        $data = $this->service->expensesReport($filters);
        $rows = $data->map(fn ($row) => [
            JalaliDate::fromGregorian($row->expense_date),
            $row->category?->localized_name,
            $row->subcategory ?? '—',
            $row->bill_number ?? '—',
            $row->amount,
        ]);

        $rows->push([
            __('erp.fields.total_amount'),
            '',
            '',
            '',
            $data->sum('amount'),
        ]);

        return [[
            __('erp.fields.date'),
            __('erp.fields.category'),
            __('erp.fields.subcategory'),
            __('erp.fields.bill_number'),
            __('erp.fields.amount'),
        ], $rows];
    }

    private function employeesExportData(array $filters): array
    {
        $data = $this->service->employeesReport($filters);
        $rows = $data->map(fn ($row) => [
            $row->name,
            $row->father_name ?? '—',
            $row->position ?? '—',
            $row->phone ?? '—',
            __('erp.status.'.$row->status),
            $row->salary,
            JalaliDate::fromGregorian($row->joining_date),
        ]);

        return [[
            __('erp.report_filters.employee_name'),
            __('erp.fields.father_name'),
            __('erp.fields.position'),
            __('erp.fields.phone'),
            __('erp.fields.status'),
            __('erp.fields.salary'),
            __('erp.fields.joining_date'),
        ], $rows];
    }

    private function inventoryExportData(array $filters): array
    {
        $rows = $this->service->inventoryReport($filters)->map(fn ($row) => [
            JalaliDate::fromGregorian($row->movement_date),
            $row->inventoryItem?->name,
            $row->inventoryItem?->sku,
            $row->inventoryItem?->category ?? '—',
            __('erp.movement_types.'.$row->movement_type),
            $row->quantity,
        ]);

        return [[
            __('erp.fields.date'),
            __('erp.fields.name'),
            __('erp.fields.sku'),
            __('erp.fields.category'),
            __('erp.fields.movement_type'),
            __('erp.fields.quantity'),
        ], $rows];
    }

    private function mineAssetsExportData(array $filters): array
    {
        $data = $this->service->mineAssetsReport($filters);
        $rows = $data->map(fn ($row) => [
            JalaliDate::fromGregorian($row->registration_date),
            $row->name,
            $row->related_to ?? '—',
            $row->quantity,
            __('erp.mine_asset_units.'.$row->unit),
            __('erp.mine_asset_statuses.'.$row->status),
            $row->remarks ?? '—',
        ]);

        $rows->push([
            __('erp.fields.total_amount'),
            '',
            '',
            $data->sum('quantity'),
            '',
            '',
            '',
        ]);

        return [[
            __('erp.fields.registration_date'),
            __('erp.fields.name'),
            __('erp.fields.related_to'),
            __('erp.fields.quantity'),
            __('erp.fields.unit'),
            __('erp.fields.status'),
            __('erp.fields.notes'),
        ], $rows];
    }

    private function personalLedgerExportData(array $filters): array
    {
        $report = $this->service->personalLedgerReport($filters);
        $summary = $report['summary'];

        $rows = collect([
            [__('erp.personal_accounts.total_credit_afn'), number_format($summary['total_credit_afn'] ?? 0, 2)],
            [__('erp.personal_accounts.total_payment_afn'), number_format($summary['total_payment_afn'] ?? 0, 2)],
            [__('erp.personal_accounts.balance_afn'), number_format($summary['outstanding_afn'] ?? 0, 2)],
            [__('erp.personal_accounts.total_credit_usd'), number_format($summary['total_credit_usd'] ?? 0, 2)],
            [__('erp.personal_accounts.total_payment_usd'), number_format($summary['total_payment_usd'] ?? 0, 2)],
            [__('erp.personal_accounts.balance_usd'), number_format($summary['outstanding_usd'] ?? 0, 2)],
            ['', ''],
        ]);

        foreach ($report['contact_balances'] as $contact) {
            $rows->push([
                $contact['name'],
                __('erp.personal_contact_types.'.$contact['contact_type']),
                number_format($contact['balance_afn'], 2),
                number_format($contact['balance_usd'], 2),
            ]);
        }

        $rows->push(['', '', '', '']);

        foreach ($report['transactions'] as $row) {
            $typeLabel = $row['type'] === 'credit'
                ? __('erp.personal_transaction_types.credit')
                : __('erp.personal_transaction_types.payment');

            $rows->push([
                $row['date_shamsi'],
                $row['contact_name'],
                $typeLabel,
                $row['currency'],
                $row['credit_amount'] !== null ? number_format($row['credit_amount'], 2) : '',
                $row['payment_amount'] !== null ? number_format($row['payment_amount'], 2) : '',
                $row['description'] ?? '—',
                number_format($row['running_balance'], 2),
            ]);
        }

        return [[
            __('erp.fields.date'),
            __('erp.fields.name'),
            __('erp.fields.type'),
            __('erp.fields.currency'),
            __('erp.personal_transaction_types.credit'),
            __('erp.personal_transaction_types.payment'),
            __('erp.fields.description'),
            __('erp.personal_accounts.running_balance'),
        ], $rows];
    }

    private function personalHomeExpensesExportData(array $filters): array
    {
        $data = $this->service->personalHomeExpensesReport($filters);
        $rows = $data->map(fn ($row) => [
            JalaliDate::fromGregorian($row->expense_date),
            $row->item_name,
            $row->amount,
            $row->description ?? '—',
        ]);

        $rows->push([
            __('erp.fields.total_amount'),
            '',
            $data->sum('amount'),
            '',
        ]);

        return [[
            __('erp.fields.date'),
            __('erp.fields.name'),
            __('erp.fields.amount'),
            __('erp.fields.description'),
        ], $rows];
    }

    private function machineryExportData(array $filters): array
    {
        $data = $this->service->machineryReport($filters);
        $rows = $data->map(fn ($row) => [
            JalaliDate::fromGregorian($row->purchase_date),
            $row->item_name,
            $row->bill_number ?? '—',
            __('erp.currencies.'.$row->currency),
            $row->amount,
            $row->description ?? '—',
        ]);

        $rows->push([
            __('erp.machinery.total_afn'),
            '',
            '',
            '',
            $data->where('currency', 'AFN')->sum('amount'),
            '',
        ]);
        $rows->push([
            __('erp.machinery.total_usd'),
            '',
            '',
            '',
            $data->where('currency', 'USD')->sum('amount'),
            '',
        ]);

        return [[
            __('erp.fields.date'),
            __('erp.fields.name'),
            __('erp.fields.bill_number'),
            __('erp.fields.currency'),
            __('erp.fields.amount'),
            __('erp.fields.description'),
        ], $rows];
    }

    private function dailyProductionExportData(array $filters): array
    {
        $data = $this->service->dailyProductionReport($filters);
        $rows = $data->map(fn ($row) => [
            JalaliDate::fromGregorian($row->shipment_date),
            $row->quantity_ton ?? '—',
            $row->creator?->name ?? '—',
            $row->notes ?? '—',
        ]);

        return [[
            __('erp.fields.date'),
            __('erp.fields.quantity_ton'),
            __('erp.report_filters.created_by'),
            __('erp.fields.notes'),
        ], $rows];
    }

    private function monthlyProductionExportData(array $filters): array
    {
        $data = $this->service->monthlyProductionReport($filters);
        $rows = $data->map(fn ($row) => [
            $row['month'],
            $row['shipment_count'],
            $row['total_tons'],
            $row['total_sales'],
        ]);

        return [[
            __('erp.report_filters.month'),
            __('erp.report_filters.quantity'),
            __('erp.report_filters.total_tons'),
            __('erp.reports.total_sales'),
        ], $rows];
    }

    private function customerBalancesExportData(array $filters): array
    {
        $data = $this->service->customerBalancesReport($filters);
        $rows = $data->map(fn ($row) => [
            $row->name,
            $row->total_sales ?? 0,
            $row->total_payments ?? 0,
            $row->outstanding_balance ?? 0,
        ]);

        $rows->push([
            __('erp.fields.total_amount'),
            $data->sum(fn ($row) => $row->total_sales ?? 0),
            $data->sum(fn ($row) => $row->total_payments ?? 0),
            $data->sum(fn ($row) => $row->outstanding_balance ?? 0),
        ]);

        return [[
            __('erp.fields.customer'),
            __('erp.reports.total_sales'),
            __('erp.reports.total_payments'),
            __('erp.reports.outstanding_balance'),
        ], $rows];
    }

    private function profitLossExportData(array $filters): array
    {
        $report = $this->service->profitLossReport($filters);

        if (! $this->service->profitLossMatchesFilter($report, $filters)) {
            return [[__('erp.fields.description'), __('erp.fields.amount')], collect()];
        }

        $rows = collect([
            [__('erp.reports.marble_sales'), $report['marble_sales']],
            [__('erp.reports.sankari_sales'), $report['sankari_sales']],
            [__('erp.reports.contractor_royalty'), $report['contractor_royalty']],
            [__('erp.reports.total_income'), $report['total_income']],
            [__('erp.reports.operating_expenses'), $report['operating_expenses']],
            [__('erp.reports.payroll_expenses'), $report['payroll_expenses']],
            [__('erp.reports.total_expenses'), $report['total_expenses']],
            [__('erp.reports.net_profit'), $report['net_profit']],
        ]);

        return [[__('erp.fields.description'), __('erp.fields.amount')], $rows];
    }

    private function contractorProductionExportData(array $filters): array
    {
        $data = $this->service->contractorProductionReport($filters);
        $rows = $data->map(fn ($row) => [
            JalaliDate::fromGregorian($row->production_date),
            $row->quantity_ton,
            $row->rate_per_ton,
            $row->total_royalty,
        ]);

        $rows->push([
            __('erp.fields.total_amount'),
            $data->sum('quantity_ton'),
            '',
            $data->sum('total_royalty'),
        ]);

        return [[
            __('erp.fields.date'),
            __('erp.fields.quantity_ton'),
            __('erp.fields.rate_per_ton'),
            __('erp.contractor_royalty.total_royalty'),
        ], $rows];
    }

    private function contractorPaymentsExportData(array $filters): array
    {
        $data = $this->service->contractorPaymentsReport($filters);
        $rows = $data->map(fn ($row) => [
            JalaliDate::fromGregorian($row->payment_date),
            $row->amount,
            $row->receipt_number ?? '—',
            $row->received_by,
        ]);

        $rows->push([
            __('erp.fields.total_amount'),
            $data->sum('amount'),
            '',
            '',
        ]);

        return [[
            __('erp.fields.date'),
            __('erp.fields.amount'),
            __('erp.fields.receipt_number'),
            __('erp.fields.received_by'),
        ], $rows];
    }

    private function contractorExpensesExportData(array $filters): array
    {
        $data = $this->service->contractorExpensesReport($filters);
        $rows = $data->map(fn ($row) => [
            JalaliDate::fromGregorian($row->expense_date),
            $row->category?->localized_name,
            $row->subcategory ?? '—',
            $row->bill_number ?? '—',
            $row->description ?? '—',
            $row->amount,
        ]);

        $rows->push([
            __('erp.fields.total_amount'),
            '',
            '',
            '',
            '',
            $data->sum('amount'),
        ]);

        return [[
            __('erp.fields.date'),
            __('erp.fields.category'),
            __('erp.fields.subcategory'),
            __('erp.fields.bill_number'),
            __('erp.fields.description'),
            __('erp.fields.amount'),
        ], $rows];
    }

    private function contractorLedgerExportData(array $filters): array
    {
        $report = $this->service->contractorLedgerReport($filters);

        $rows = collect([
            [__('erp.contractor_royalty.total_royalties'), '', '', $report['summary']['total_royalties'], ''],
            [__('erp.contractor_royalty.total_salary_charges'), '', '', $report['summary']['total_salary_charges'], ''],
            [__('erp.contractor_royalty.total_expense_charges'), '', '', $report['summary']['total_expense_charges'], ''],
            [__('erp.contractor_royalty.total_payments_received'), '', '', '', $report['summary']['total_payments']],
            [__('erp.contractor_royalty.outstanding_balance'), '', '', $report['summary']['outstanding_balance'], ''],
            ['', '', '', '', ''],
        ]);

        foreach ($report['transactions'] as $row) {
            $typeLabel = match ($row['type']) {
                'royalty' => __('erp.contractor_royalty.royalty_entry'),
                'salary_charge' => __('erp.contractor_royalty.salary_charge_entry'),
                'expense_charge' => __('erp.contractor_royalty.expense_charge_entry'),
                default => __('erp.contractor_royalty.payment_entry'),
            };

            $rows->push([
                $row['date_shamsi'],
                $typeLabel,
                $row['description'],
                $row['royalty_amount'] ?? '',
                $row['payment_amount'] ?? '',
            ]);
        }

        return [[
            __('erp.fields.date'),
            __('erp.fields.type'),
            __('erp.fields.description'),
            __('erp.contractor_royalty.total_royalty'),
            __('erp.fields.amount'),
        ], $rows];
    }
}
