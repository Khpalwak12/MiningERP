<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialYear;
use App\Models\MarbleShipment;
use App\Models\MineType;
use App\Models\User;
use App\Services\DashboardService;
use App\Support\JalaliDate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardIncomeExpenseChartTest extends TestCase
{
    use RefreshDatabase;

    public function test_income_expense_trend_aggregates_monthly_totals(): void
    {
        $this->seed();

        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();
        $year = FinancialYear::query()->active()->where('is_all_years', false)->firstOrFail();
        $category = ExpenseCategory::query()->firstOrFail();

        MarbleShipment::query()->create([
            'financial_year_id' => $year->id,
            'customer_id' => \App\Models\Customer::query()->firstOrFail()->id,
            'mine_type_id' => MineType::query()->firstOrFail()->id,
            'shipment_date' => JalaliDate::toGregorian('1405/02/10'),
            'quantity_ton' => 10,
            'price_per_ton' => 1000,
            'created_by' => $user->id,
        ]);

        Expense::query()->create([
            'financial_year_id' => $year->id,
            'expense_category_id' => $category->id,
            'expense_date' => JalaliDate::toGregorian('1405/02/15'),
            'amount' => 2500,
            'created_by' => $user->id,
        ]);

        $trend = collect(app(DashboardService::class)->stats()['income_expense_trend']);
        $february = $trend->firstWhere('month', '1405/02');

        $this->assertNotNull($february);
        $this->assertSame(10000.0, $february['income']);
        $this->assertSame(2500.0, $february['expenses']);
    }
}
