<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\MpdfPdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportPdfExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_payroll_pdf_export_returns_valid_pdf_in_pashto_locale(): void
    {
        app()->setLocale('ps');

        $user = User::where('email', 'admin@marbleerp.local')->firstOrFail();

        $response = $this->actingAs($user)->get(route('reports.export.pdf', 'payroll'));

        $response->assertSuccessful();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_mpdf_service_renders_pashto_html_with_utf8(): void
    {
        app()->setLocale('ps');

        $html = view('reports.pdf.layout', [
            'reportType' => 'payroll',
            'generatedAt' => '1405/01/01',
            'locale' => 'ps',
            'isRtl' => true,
        ])->render();

        $pdf = app(MpdfPdfService::class)->fromHtml($html, 'ps');

        $this->assertStringStartsWith('%PDF', $pdf);
        $this->assertNotEmpty($pdf);
    }
}
