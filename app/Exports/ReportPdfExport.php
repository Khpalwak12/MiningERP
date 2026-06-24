<?php

namespace App\Exports;

use App\Services\MpdfPdfService;
use Illuminate\Http\Response;

class ReportPdfExport
{
    public function __construct(private MpdfPdfService $pdf) {}

    public function download(string $reportType, string $view, array $data, array $filters = []): Response
    {
        $filename = "{$reportType}_".now()->format('Ymd_His').'.pdf';

        return $this->pdf->downloadFromView($filename, $view, array_merge($data, [
            'reportType' => $reportType,
            'filters' => $filters,
            'generatedAt' => \App\Support\JalaliDate::fromGregorian(now()),
            'locale' => app()->getLocale(),
            'isRtl' => app()->getLocale() === 'ps',
        ]));
    }
}
