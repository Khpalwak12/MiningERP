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

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

app()->setLocale('ps');

$user = App\Models\User::query()->where('email', 'admin@marbleerp.local')->first();
if (! $user) {
    fwrite(STDERR, "Admin user not found in desktop database.\n");
    exit(1);
}

auth()->login($user);

$request = Illuminate\Http\Request::create('/reports/export/payroll/pdf', 'GET');
/** @var Illuminate\Http\Response $response */
$response = $app->handle($request);

echo 'HTTP '.$response->getStatusCode().PHP_EOL;

if ($response->getStatusCode() !== 200) {
    echo substr((string) $response->getContent(), 0, 2000).PHP_EOL;
    exit(1);
}

$content = $response->getContent();
echo 'PDF starts with: '.substr($content, 0, 4).PHP_EOL;
echo 'PDF length: '.strlen($content).PHP_EOL;
