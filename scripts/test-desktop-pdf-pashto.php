<?php

$base = getcwd();
if (! is_file($base.'/vendor/autoload.php')) {
    fwrite(STDERR, "Run this script from the Laravel app directory.\n");
    exit(1);
}

require $base.'/vendor/autoload.php';
require $base.'/bootstrap/desktop-env.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require $base.'/bootstrap/app.php';
if (is_file($base.'/bootstrap/desktop-application.php')) {
    require_once $base.'/bootstrap/desktop-application.php';
    mining_apply_desktop_storage_path($app);
}

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

app()->setLocale('ps');

try {
    $html = view('reports.pdf.layout', [
        'reportType' => 'payroll',
        'reportTitle' => 'Payroll',
        'generatedAt' => '1405/01/01',
        'locale' => 'ps',
        'isRtl' => true,
    ])->render();

    $pdf = app(App\Services\MpdfPdfService::class)->fromHtml($html, 'ps');

    echo 'OK Pashto PDF length: '.strlen($pdf).PHP_EOL;
} catch (Throwable $e) {
    echo 'ERROR: '.$e->getMessage().PHP_EOL;
    echo $e->getFile().':'.$e->getLine().PHP_EOL;
    exit(1);
}
