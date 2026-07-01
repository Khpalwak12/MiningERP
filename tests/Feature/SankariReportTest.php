<?php

namespace Tests\Feature;

use App\Models\FinancialYear;
use App\Models\SankariStoneSale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SankariReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->admin = User::where('email', 'admin@marbleerp.local')->firstOrFail();
    }

    public function test_sankari_report_page_returns_sales_with_summary(): void
    {
        $year = FinancialYear::query()->active()->where('is_all_years', false)->firstOrFail();

        SankariStoneSale::query()->create([
            'financial_year_id' => $year->id,
            'sale_date' => now(),
            'truck_count' => 10,
            'price_per_truck' => 600,
            'discount' => 500,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('reports.sankari'));

        $response->assertSuccessful();
        $response->assertInertia(fn ($page) => $page
            ->component('Reports/Sankari')
            ->has('rows.data', 1)
            ->where('summary.truck_count', 10)
            ->where('summary.discount', 500)
            ->where('summary.total_amount', 5500)
        );
    }

    public function test_sankari_pdf_export_returns_valid_pdf(): void
    {
        $year = FinancialYear::query()->active()->where('is_all_years', false)->firstOrFail();

        SankariStoneSale::query()->create([
            'financial_year_id' => $year->id,
            'sale_date' => now(),
            'truck_count' => 2,
            'price_per_truck' => 1000,
            'discount' => 0,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('reports.export.pdf', 'sankari'));

        $response->assertSuccessful();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }
}
