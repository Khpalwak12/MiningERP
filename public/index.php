<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
$maintenance = __DIR__.'/../storage/framework/maintenance.php';
$desktopDataPath = getenv('MININGERP_DATA_PATH') ?: ($_ENV['MININGERP_DATA_PATH'] ?? null);

if (is_string($desktopDataPath) && $desktopDataPath !== '') {
    $maintenance = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $desktopDataPath), DIRECTORY_SEPARATOR)
        .DIRECTORY_SEPARATOR.'storage'
        .DIRECTORY_SEPARATOR.'framework'
        .DIRECTORY_SEPARATOR.'maintenance.php';
}

if (file_exists($maintenance)) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

require __DIR__.'/../bootstrap/desktop-env.php';
require __DIR__.'/../bootstrap/desktop-application.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

mining_apply_desktop_storage_path($app);

$app->handleRequest(Request::capture());
