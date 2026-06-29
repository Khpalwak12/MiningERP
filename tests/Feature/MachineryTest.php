<?php

namespace Tests\Feature;

use App\Models\FinancialYear;
use App\Models\MachineryItem;
use App\Models\User;
use App\Services\DashboardService;
use App\Services\MachineryItemService;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MachineryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_machinery_item_with_currency(): void
    {
        $this->seed();

        $year = FinancialYear::query()->active()->where('is_all_years', false)->firstOrFail();
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        $item = app(MachineryItemService::class)->create([
            'financial_year_id' => $year->id,
            'purchase_date' => now(),
            'item_name' => 'Excavator',
            'bill_number' => 'BILL-100',
            'currency' => MachineryItem::CURRENCY_USD,
            'amount' => 25000,
            'description' => 'New machine',
            'created_by' => $user->id,
        ]);

        $this->assertSame('Excavator', $item->item_name);
        $this->assertSame('BILL-100', $item->bill_number);
        $this->assertSame('USD', $item->currency);
        $this->assertSame(25000.0, (float) $item->amount);
    }

    public function test_dashboard_and_report_show_totals_by_currency(): void
    {
        $this->seed();

        $year = FinancialYear::query()->active()->where('is_all_years', false)->firstOrFail();
        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();
        $service = app(MachineryItemService::class);

        $service->create([
            'financial_year_id' => $year->id,
            'purchase_date' => now(),
            'item_name' => 'Generator',
            'currency' => MachineryItem::CURRENCY_AFN,
            'amount' => 500000,
            'created_by' => $user->id,
        ]);

        $service->create([
            'financial_year_id' => $year->id,
            'purchase_date' => now(),
            'item_name' => 'Truck',
            'currency' => MachineryItem::CURRENCY_USD,
            'amount' => 12000,
            'created_by' => $user->id,
        ]);

        $stats = app(DashboardService::class)->stats();
        $this->assertSame(500000.0, $stats['machinery']['AFN']);
        $this->assertSame(12000.0, $stats['machinery']['USD']);

        $report = app(ReportService::class)->machineryReport();
        $this->assertCount(2, $report);

        $this->actingAs($user)->get(route('machinery.index'))->assertOk();
        $this->actingAs($user)->get(route('reports.machinery'))->assertOk();
    }
}
