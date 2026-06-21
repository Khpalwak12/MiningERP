<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\MarbleShipment;
use App\Models\PayrollPayment;
use App\Models\SankariStoneSale;
use App\Repositories\MarbleShipmentRepository;
use App\Support\JalaliDate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct(private MarbleShipmentRepository $shipmentRepository) {}

    public function stats(): array
    {
        $today = Carbon::today();
        $shamsiMonth = JalaliDate::fromGregorian($today, 'Y/m');

        $monthlyShipments = MarbleShipment::query()
            ->whereBetween('shipment_date', JalaliDate::monthRange($shamsiMonth));

        $totalRevenue = (float) MarbleShipment::completed()->sum('total_amount') + (float) SankariStoneSale::sum('total_amount');
        $totalExpenses = (float) Expense::sum('amount') + (float) PayrollPayment::sum('amount');

        $outstanding = Customer::query()
            ->withSum(['shipments as total_sales' => fn ($q) => $q->completed()], 'total_amount')
            ->withSum('payments as total_payments', 'amount')
            ->get()
            ->sum(fn ($c) => ($c->total_sales ?? 0) - ($c->total_payments ?? 0));

        return [
            'today_production' => $this->shipmentRepository->todayStats(),
            'monthly_production' => $this->shipmentRepository->monthlyStats($shamsiMonth),
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'employee_count' => Employee::where('status', 'active')->count(),
            'outstanding_balances' => $outstanding,
            'cash_flow' => [
                'income' => (float) CustomerPayment::sum('amount') + (float) SankariStoneSale::sum('cash_received'),
                'expenses' => $totalExpenses,
                'net' => (float) CustomerPayment::sum('amount') + (float) SankariStoneSale::sum('cash_received') - $totalExpenses,
            ],
            'recent_transactions' => $this->recentTransactions(),
        ];
    }

    private function recentTransactions(): array
    {
        $shipments = MarbleShipment::with('customer')->latest()->limit(5)->get()->map(fn ($s) => [
            'type' => 'shipment',
            'date' => $s->shipment_date,
            'label' => $s->customer?->name,
            'amount' => $s->total_amount,
        ]);

        $payments = CustomerPayment::with('customer')->latest()->limit(5)->get()->map(fn ($p) => [
            'type' => 'payment',
            'date' => $p->payment_date,
            'label' => $p->customer?->name,
            'amount' => $p->amount,
        ]);

        return $shipments->concat($payments)->sortByDesc('date')->take(10)->values()->all();
    }
}
