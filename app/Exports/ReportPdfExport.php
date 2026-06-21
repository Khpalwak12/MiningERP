<?php

namespace App\Exports;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ReportPdfExport
{
    public function download(string $reportType, string $view, array $data, array $filters = []): Response
    {
        $pdf = Pdf::loadView($view, array_merge($data, [
            'reportType' => $reportType,
            'filters' => $filters,
            'generatedAt' => \App\Support\JalaliDate::fromGregorian(now()),
        ]));

        return $pdf->download("{$reportType}_".now()->format('Ymd_His').'.pdf');
    }
}
