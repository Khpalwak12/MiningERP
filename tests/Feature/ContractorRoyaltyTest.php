<?php

namespace Tests\Feature;

use App\Models\ContractorPayment;
use App\Models\ContractorProduction;
use App\Models\FinancialYear;
use App\Models\User;
use App\Services\ContractorProductionService;
use App\Services\ContractorRoyaltyLedgerService;
use App\Services\DashboardService;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractorRoyaltyTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_calculates_total_royalty_and_preserves_rate(): void
    {
        $this->seed();

        $year = FinancialYear::query()->active()->where('is_all_years', false)->firstOrFail();
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        $production = app(ContractorProductionService::class)->create([
            'financial_year_id' => $year->id,
            'production_date' => now(),
            'quantity_ton' => 10,
            'rate_per_ton' => 175,
            'created_by' => $user->id,
        ]);

        $this->assertSame(1750.0, (float) $production->total_royalty);
        $this->assertSame(175.0, (float) $production->rate_per_ton);

        config(['erp.contractor_royalty.default_rate_per_ton' => 200]);

        $second = app(ContractorProductionService::class)->create([
            'financial_year_id' => $year->id,
            'production_date' => now(),
            'quantity_ton' => 5,
            'rate_per_ton' => 200,
            'created_by' => $user->id,
        ]);

        $production->refresh();
        $this->assertSame(175.0, (float) $production->rate_per_ton);
        $this->assertSame(1000.0, (float) $second->total_royalty);
    }

    public function test_ledger_calculates_outstanding_balance(): void
    {
        $this->seed();

        $year = FinancialYear::query()->active()->where('is_all_years', false)->firstOrFail();
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        ContractorProduction::query()->create([
            'financial_year_id' => $year->id,
            'production_date' => now(),
            'quantity_ton' => 100,
            'rate_per_ton' => 150,
            'total_royalty' => 15000,
            'created_by' => $user->id,
        ]);

        ContractorProduction::query()->create([
            'financial_year_id' => $year->id,
            'production_date' => now(),
            'quantity_ton' => 200,
            'rate_per_ton' => 150,
            'total_royalty' => 30000,
            'created_by' => $user->id,
        ]);

        ContractorPayment::query()->create([
            'financial_year_id' => $year->id,
            'payment_date' => now(),
            'amount' => 70000,
            'received_by' => 'Cashier',
            'created_by' => $user->id,
        ]);

        $summary = app(ContractorRoyaltyLedgerService::class)->summary();

        $this->assertSame(45000.0, $summary['total_royalties']);
        $this->assertSame(2, $summary['dispatch_count']);
        $this->assertSame(300.0, $summary['total_tons']);
        $this->assertSame(70000.0, $summary['total_payments']);
        $this->assertSame(-25000.0, $summary['outstanding_balance']);
    }

    public function test_profit_and_loss_uses_royalty_revenue_not_payments(): void
    {
        $this->seed();

        $year = FinancialYear::query()->active()->where('is_all_years', false)->firstOrFail();
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        ContractorProduction::query()->create([
            'financial_year_id' => $year->id,
            'production_date' => now(),
            'quantity_ton' => 1000,
            'rate_per_ton' => 150,
            'total_royalty' => 150000,
            'created_by' => $user->id,
        ]);

        ContractorPayment::query()->create([
            'financial_year_id' => $year->id,
            'payment_date' => now(),
            'amount' => 50000,
            'received_by' => 'Cashier',
            'created_by' => $user->id,
        ]);

        $report = app(ReportService::class)->profitLossReport();
        $ledger = app(ContractorRoyaltyLedgerService::class)->summary();

        $this->assertSame(150000.0, $report['contractor_royalty']);
        $this->assertGreaterThanOrEqual(150000.0, $report['total_income']);
        $this->assertSame(100000.0, $ledger['outstanding_balance']);
    }

    public function test_dashboard_contractor_stats_include_dispatch_count_and_total_tons(): void
    {
        $this->seed();

        $year = FinancialYear::query()->active()->where('is_all_years', false)->firstOrFail();
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        ContractorProduction::query()->create([
            'financial_year_id' => $year->id,
            'production_date' => now(),
            'quantity_ton' => 10.5,
            'rate_per_ton' => 150,
            'total_royalty' => 1575,
            'created_by' => $user->id,
        ]);

        ContractorProduction::query()->create([
            'financial_year_id' => $year->id,
            'production_date' => now(),
            'quantity_ton' => 20,
            'rate_per_ton' => 150,
            'total_royalty' => 3000,
            'created_by' => $user->id,
        ]);

        $stats = app(DashboardService::class)->stats();

        $this->assertSame(4575.0, $stats['contractor_royalty']['total_royalties']);
        $this->assertSame(2, $stats['contractor_royalty']['dispatch_count']);
        $this->assertSame(30.5, $stats['contractor_royalty']['total_tons']);

        $ledgerSummary = app(ContractorRoyaltyLedgerService::class)->summary();
        $this->assertSame($stats['contractor_royalty']['total_royalties'], $ledgerSummary['total_royalties']);
        $this->assertSame($stats['contractor_royalty']['dispatch_count'], $ledgerSummary['dispatch_count']);
        $this->assertSame($stats['contractor_royalty']['total_tons'], $ledgerSummary['total_tons']);
    }

    public function test_shared_employee_payroll_creates_contractor_salary_charge(): void
    {
        $this->seed();

        $year = FinancialYear::query()->active()->where('is_all_years', false)->firstOrFail();
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        $employee = \App\Models\Employee::query()->create([
            'name' => 'Shared Worker',
            'salary' => 30000,
            'joining_date' => now()->subMonth(),
            'status' => 'active',
            'is_shared_with_contractor' => true,
            'contractor_salary_share_percent' => 50,
        ]);

        app(\App\Services\PayrollPaymentService::class)->create([
            'financial_year_id' => $year->id,
            'employee_id' => $employee->id,
            'payment_date' => now(),
            'amount' => 30000,
            'payment_type' => 'full_salary',
            'period_month' => '1404/01',
            'created_by' => $user->id,
        ]);

        $summary = app(ContractorRoyaltyLedgerService::class)->summary();

        $this->assertSame(15000.0, $summary['total_salary_charges']);
        $this->assertSame(15000.0, $summary['outstanding_balance']);

        ContractorPayment::query()->create([
            'financial_year_id' => $year->id,
            'payment_date' => now(),
            'amount' => 10000,
            'received_by' => 'Cashier',
            'created_by' => $user->id,
        ]);

        $summary = app(ContractorRoyaltyLedgerService::class)->summary();
        $this->assertSame(5000.0, $summary['outstanding_balance']);
    }

    public function test_ledger_includes_royalties_and_salary_charges_in_outstanding(): void
    {
        $this->seed();

        $year = FinancialYear::query()->active()->where('is_all_years', false)->firstOrFail();
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        ContractorProduction::query()->create([
            'financial_year_id' => $year->id,
            'production_date' => now(),
            'quantity_ton' => 100,
            'rate_per_ton' => 150,
            'total_royalty' => 15000,
            'created_by' => $user->id,
        ]);

        $employee = \App\Models\Employee::query()->create([
            'name' => 'Shared Worker',
            'salary' => 20000,
            'joining_date' => now()->subMonth(),
            'status' => 'active',
            'is_shared_with_contractor' => true,
            'contractor_salary_share_percent' => 50,
        ]);

        app(\App\Services\PayrollPaymentService::class)->create([
            'financial_year_id' => $year->id,
            'employee_id' => $employee->id,
            'payment_date' => now(),
            'amount' => 20000,
            'payment_type' => 'full_salary',
            'period_month' => '1404/02',
            'created_by' => $user->id,
        ]);

        $summary = app(ContractorRoyaltyLedgerService::class)->summary();

        $this->assertSame(15000.0, $summary['total_royalties']);
        $this->assertSame(10000.0, $summary['total_salary_charges']);
        $this->assertSame(25000.0, $summary['outstanding_balance']);
    }
}
