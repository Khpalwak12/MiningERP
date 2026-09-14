<?php

namespace App\Services;

use App\Models\ContractorProduction;
use App\Models\ContractorSalaryCharge;
use App\Models\Customer;
use App\Models\ContractorExpenseCharge;
use App\Models\ContractorPayment;
use App\Models\CustomerPayment;
use App\Models\Employee;
use App\Models\Expense;
use App\Support\ActiveFinancialYear;
use App\Models\FinancialYear;
use App\Models\MachineryItem;
use App\Models\MarbleShipment;
use App\Models\PayrollPayment;
use App\Models\SankariStoneSale;
use App\Repositories\MarbleShipmentRepository;
use App\Support\JalaliDate;
use Carbon\Carbon;

class DashboardService
{
    public function __construct(
        private MarbleShipmentRepository $shipmentRepository,
        private ReportService $reportService,
    ) {}

    public function stats(): array
    {
        $activeYear = ActiveFinancialYear::activeYear();
        $yearId = ActiveFinancialYear::activeYearId();
        $isAllYearsMode = ActiveFinancialYear::isAllYearsMode();

        $today = Carbon::today();
        $shamsiMonth = JalaliDate::fromGregorian($today, 'Y/m');

        $shipmentQuery = MarbleShipment::query()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));
        $expenseQuery = Expense::query()->forCompany()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));
        $payrollQuery = PayrollPayment::query()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));
        $paymentQuery = CustomerPayment::query()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));
        $sankariQuery = SankariStoneSale::query()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));
        $contractorProductionQuery = ContractorProduction::query()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));
        $contractorPaymentQuery = ContractorPayment::query()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));
        $contractorSalaryChargeQuery = ContractorSalaryCharge::query()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));
        $contractorExpenseChargeQuery = ContractorExpenseCharge::query()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));

        $contractorRoyalties = (float) (clone $contractorProductionQuery)->sum('total_royalty');
        $contractorSalaryCharges = (float) (clone $contractorSalaryChargeQuery)->sum('amount');
        $contractorExpenseCharges = (float) (clone $contractorExpenseChargeQuery)->sum('amount');
        $contractorPaymentsReceived = (float) (clone $contractorPaymentQuery)->sum('amount');
        $contractorReceivable = $contractorRoyalties + $contractorSalaryCharges + $contractorExpenseCharges;

        $machineryQuery = MachineryItem::query()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));
        $machineryTotals = [
            'AFN' => (float) (clone $machineryQuery)->where('currency', MachineryItem::CURRENCY_AFN)->sum('amount'),
            'USD' => (float) (clone $machineryQuery)->where('currency', MachineryItem::CURRENCY_USD)->sum('amount'),
        ];

        $totalRevenue = (float) (clone $shipmentQuery)->completed()->sum('total_amount')
            + (float) (clone $sankariQuery)->sum('total_amount')
            + $contractorRoyalties;
        $totalExpenses = (float) (clone $expenseQuery)->sum('amount') + (float) (clone $payrollQuery)->sum('amount');
        $netProfit = $this->reportService->profitLossReport([])['net_profit'];

        $marbleSales = [
            'trucks' => (clone $shipmentQuery)->count(),
            'tons' => (float) (clone $shipmentQuery)->whereNotNull('quantity_ton')->sum('quantity_ton'),
            'sales' => (float) (clone $shipmentQuery)->completed()->sum('total_amount'),
        ];

        $outstanding = Customer::query()
            ->withSum(['shipments as total_sales' => fn ($q) => $q->completed()->when($yearId, fn ($q2) => $q2->where('financial_year_id', $yearId))], 'total_amount')
            ->withSum(['payments as total_payments' => fn ($q) => $q->when($yearId, fn ($q2) => $q2->where('financial_year_id', $yearId))], 'amount')
            ->get()
            ->sum(fn ($c) => ($c->total_sales ?? 0) - ($c->total_payments ?? 0));

        return [
            'is_all_years_mode' => $isAllYearsMode,
            'active_financial_year' => $isAllYearsMode ? null : $activeYear?->name,
            'display_mode' => $isAllYearsMode ? 'all' : ($activeYear ? 'year' : 'none'),
            'marble_sales' => $marbleSales,
            'today_production' => $this->shipmentRepository->todayStats($yearId),
            'monthly_production' => $this->shipmentRepository->monthlyStats($shamsiMonth, $yearId),
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'employee_count' => Employee::where('status', 'active')->count(),
            'outstanding_balances' => $outstanding,
            'factory_payments_received' => (float) (clone $paymentQuery)->sum('amount'),
            'cash_flow' => [
                'income' => (float) (clone $paymentQuery)->sum('amount') + (float) (clone $sankariQuery)->sum('total_amount'),
                'expenses' => $totalExpenses,
                'net' => (float) (clone $paymentQuery)->sum('amount') + (float) (clone $sankariQuery)->sum('total_amount') - $totalExpenses,
            ],
            'contractor_royalty' => [
                'total_royalties' => $contractorRoyalties,
                'dispatch_count' => (clone $contractorProductionQuery)->count(),
                'total_tons' => (float) (clone $contractorProductionQuery)->sum('quantity_ton'),
                'total_salary_charges' => $contractorSalaryCharges,
                'total_expense_charges' => $contractorExpenseCharges,
                'total_receivable' => $contractorReceivable,
                'total_payments' => $contractorPaymentsReceived,
                'outstanding_balance' => $contractorReceivable - $contractorPaymentsReceived,
            ],
            'machinery' => $machineryTotals,
            'income_expense_trend' => $this->incomeExpenseTrend($yearId, $activeYear),
            'recent_transactions' => $this->recentTransactions($yearId),
        ];
    }

    private function recentTransactions(?int $yearId): array
    {
        $shipments = MarbleShipment::with('customer')
            ->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId))
            ->latest()->limit(5)->get()->map(fn ($s) => [
                'type' => 'shipment',
                'date' => $s->shipment_date,
                'label' => $s->customer?->name,
                'amount' => $s->total_amount,
            ]);

        $payments = CustomerPayment::with('customer')
            ->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId))
            ->latest()->limit(5)->get()->map(fn ($p) => [
                'type' => 'payment',
                'date' => $p->payment_date,
                'label' => $p->customer?->name,
                'amount' => $p->amount,
            ]);

        return $shipments->concat($payments)->sortByDesc('date')->take(10)->values()->all();
    }

    public function incomeExpenseTrend(?int $yearId, ?FinancialYear $activeYear = null): array
    {
        $incomeByMonth = [];
        $expenseByMonth = [];

        $applyYear = fn ($query) => $query->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));

        foreach ($applyYear(MarbleShipment::query())->completed()->whereNotNull('total_amount')->get(['shipment_date', 'total_amount']) as $row) {
            $this->addToMonthBucket($incomeByMonth, $row->shipment_date, (float) $row->total_amount);
        }

        foreach ($applyYear(SankariStoneSale::query())->get(['sale_date', 'total_amount']) as $row) {
            $this->addToMonthBucket($incomeByMonth, $row->sale_date, (float) $row->total_amount);
        }

        foreach ($applyYear(ContractorProduction::query())->get(['production_date', 'total_royalty']) as $row) {
            $this->addToMonthBucket($incomeByMonth, $row->production_date, (float) $row->total_royalty);
        }

        foreach ($applyYear(Expense::query()->forCompany())->get(['expense_date', 'amount']) as $row) {
            $this->addToMonthBucket($expenseByMonth, $row->expense_date, (float) $row->amount);
        }

        foreach ($applyYear(PayrollPayment::query())->get(['payment_date', 'amount']) as $row) {
            $this->addToMonthBucket($expenseByMonth, $row->payment_date, (float) $row->amount);
        }

        $months = $this->trendMonthKeys($activeYear, $incomeByMonth, $expenseByMonth);

        return array_map(fn (string $month) => [
            'month' => $month,
            'income' => round($incomeByMonth[$month] ?? 0, 2),
            'expenses' => round($expenseByMonth[$month] ?? 0, 2),
        ], $months);
    }

    private function addToMonthBucket(array &$buckets, Carbon|string|null $date, float $amount): void
    {
        if ($date === null || $amount === 0.0) {
            return;
        }

        $month = JalaliDate::fromGregorian($date, 'Y/m');
        $buckets[$month] = ($buckets[$month] ?? 0) + $amount;
    }

    /**
     * @param  array<string, float>  $incomeByMonth
     * @param  array<string, float>  $expenseByMonth
     * @return array<int, string>
     */
    private function trendMonthKeys(?FinancialYear $activeYear, array $incomeByMonth, array $expenseByMonth): array
    {
        if ($activeYear?->start_date && $activeYear?->end_date) {
            $start = JalaliDate::fromGregorian($activeYear->start_date, 'Y/m');
            $end = JalaliDate::fromGregorian($activeYear->end_date, 'Y/m');

            return $this->shamsiMonthsBetween($start, $end);
        }

        $keys = array_unique(array_merge(array_keys($incomeByMonth), array_keys($expenseByMonth)));
        sort($keys);

        return $keys;
    }

    /**
     * @return array<int, string>
     */
    private function shamsiMonthsBetween(string $startYm, string $endYm): array
    {
        [$startYear, $startMonth] = array_map('intval', explode('/', $startYm));
        [$endYear, $endMonth] = array_map('intval', explode('/', $endYm));

        $months = [];
        $year = $startYear;
        $month = $startMonth;

        while ($year < $endYear || ($year === $endYear && $month <= $endMonth)) {
            $months[] = sprintf('%04d/%02d', $year, $month);
            $month++;

            if ($month > 12) {
                $month = 1;
                $year++;
            }
        }

        return $months;
    }
}
