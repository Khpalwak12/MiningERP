<?php

namespace App\Services;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class MpdfPdfService
{
    public function fromView(string $view, array $data = [], ?string $locale = null): string
    {
        $html = View::make($view, $data)->render();

        return $this->fromHtml($html, $locale);
    }

    public function fromHtml(string $html, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $isRtl = $locale === 'ps';

        $defaultConfig = (new ConfigVariables)->getDefaults();
        $fontVariables = (new FontVariables)->getDefaults();

        $fontDir = config('mpdf.font_dir');
        File::ensureDirectoryExists($fontDir);
        File::ensureDirectoryExists(config('mpdf.temp_dir'));

        $fontDirs = array_merge($defaultConfig['fontDir'], [$fontDir]);
        $fontData = array_merge($fontVariables['fontdata'], config('mpdf.font_data'));

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'tempDir' => config('mpdf.temp_dir'),
            'fontDir' => $fontDirs,
            'fontdata' => $fontData,
            'default_font' => config('mpdf.default_font'),
            'directionality' => $isRtl ? 'rtl' : 'ltr',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        $mpdf->WriteHTML($html);

        return $mpdf->Output('', Destination::STRING_RETURN);
    }

    public function downloadFromView(string $filename, string $view, array $data = [], ?string $locale = null): Response
    {
        $pdf = $this->fromView($view, $data, $locale);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
