<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\ContractorPayment;
use App\Models\ContractorProduction;
use App\Models\ContractorSalaryCharge;
use App\Models\CustomerPayment;
use App\Models\Employee;
use App\Models\Expense;
use App\Support\ActiveFinancialYear;
use App\Models\MarbleShipment;
use App\Models\PayrollPayment;
use App\Models\SankariStoneSale;
use App\Repositories\MarbleShipmentRepository;
use App\Support\JalaliDate;
use Carbon\Carbon;

class DashboardService
{
    public function __construct(private MarbleShipmentRepository $shipmentRepository) {}

    public function stats(): array
    {
        $activeYear = ActiveFinancialYear::activeYear();
        $yearId = ActiveFinancialYear::activeYearId();
        $isAllYearsMode = ActiveFinancialYear::isAllYearsMode();

        $today = Carbon::today();
        $shamsiMonth = JalaliDate::fromGregorian($today, 'Y/m');

        $shipmentQuery = MarbleShipment::query()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));
        $expenseQuery = Expense::query()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));
        $payrollQuery = PayrollPayment::query()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));
        $paymentQuery = CustomerPayment::query()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));
        $sankariQuery = SankariStoneSale::query()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));
        $contractorProductionQuery = ContractorProduction::query()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));
        $contractorPaymentQuery = ContractorPayment::query()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));
        $contractorSalaryChargeQuery = ContractorSalaryCharge::query()->when($yearId, fn ($q) => $q->where('financial_year_id', $yearId));

        $contractorRoyalties = (float) (clone $contractorProductionQuery)->sum('total_royalty');
        $contractorSalaryCharges = (float) (clone $contractorSalaryChargeQuery)->sum('amount');
        $contractorPaymentsReceived = (float) (clone $contractorPaymentQuery)->sum('amount');
        $contractorReceivable = $contractorRoyalties + $contractorSalaryCharges;

        $totalRevenue = (float) (clone $shipmentQuery)->completed()->sum('total_amount')
            + (float) (clone $sankariQuery)->sum('total_amount')
            + $contractorRoyalties;
        $totalExpenses = (float) (clone $expenseQuery)->sum('amount') + (float) (clone $payrollQuery)->sum('amount');

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
            'employee_count' => Employee::where('status', 'active')->count(),
            'outstanding_balances' => $outstanding,
            'cash_flow' => [
                'income' => (float) (clone $paymentQuery)->sum('amount') + (float) (clone $sankariQuery)->sum('cash_received'),
                'expenses' => $totalExpenses,
                'net' => (float) (clone $paymentQuery)->sum('amount') + (float) (clone $sankariQuery)->sum('cash_received') - $totalExpenses,
            ],
            'contractor_royalty' => [
                'total_royalties' => $contractorRoyalties,
                'dispatch_count' => (clone $contractorProductionQuery)->count(),
                'total_tons' => (float) (clone $contractorProductionQuery)->sum('quantity_ton'),
                'total_salary_charges' => $contractorSalaryCharges,
                'total_receivable' => $contractorReceivable,
                'total_payments' => $contractorPaymentsReceived,
                'outstanding_balance' => $contractorReceivable - $contractorPaymentsReceived,
            ],
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
}
