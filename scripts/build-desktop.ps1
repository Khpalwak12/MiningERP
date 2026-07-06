$ErrorActionPreference = "Stop"

$Root = Resolve-Path (Join-Path $PSScriptRoot "..")
$Desktop = Join-Path $Root "desktop"
$LaravelBundle = Join-Path $Desktop "build-resources\laravel"

function Copy-LaravelBundle {
    param(
        [string]$Source,
        [string]$Destination
    )

    if (Test-Path $Destination) {
        Remove-Item $Destination -Recurse -Force
    }

    New-Item -ItemType Directory -Path $Destination -Force | Out-Null

    $excludeDirs = @(
        "node_modules",
        ".git",
        "desktop",
        "dist-desktop",
        "tests",
        "storage\logs",
        "storage\framework\cache\data",
        "storage\framework\sessions",
        "storage\framework\views"
    )

  robocopy $Source $Destination /MIR /NFL /NDL /NJH /NJS /NC /NS /NP `
        /XD $excludeDirs `
        /XF ".env" "database\database.sqlite" "public\hot" | Out-Null

    if ($LASTEXITCODE -ge 8) {
        throw "Failed to copy Laravel application files."
    }

    $requiredFiles = @(
        "vendor\autoload.php",
        "public\build\manifest.json",
        ".env.desktop",
        "storage\fonts\NotoSansArabic-Regular.ttf",
        "storage\fonts\NotoSansArabic-Bold.ttf"
    )

    foreach ($file in $requiredFiles) {
        $path = Join-Path $Destination $file
        if (-not (Test-Path $path)) {
            throw "Missing required bundle file: $file"
        }
    }
}

Write-Host "=== Mining ERP Desktop Build ===" -ForegroundColor Cyan
Set-Location $Root

Write-Host "Installing PHP dependencies..." -ForegroundColor Cyan
composer install --no-dev --optimize-autoloader --no-interaction

Write-Host "Building frontend assets..." -ForegroundColor Cyan
if (-not (Test-Path "node_modules")) {
    npm install
}
npm run build

Write-Host "Preparing PHP runtime..." -ForegroundColor Cyan
& (Join-Path $PSScriptRoot "prepare-php-runtime.ps1")

Write-Host "Preparing Laravel bundle..." -ForegroundColor Cyan
Copy-LaravelBundle -Source $Root.Path -Destination $LaravelBundle

Write-Host "Creating installer icon..." -ForegroundColor Cyan
& (Join-Path $PSScriptRoot "create-desktop-icon.ps1")

Write-Host "Updating bundled PHP configuration..." -ForegroundColor Cyan
Copy-Item (Join-Path $Desktop "build-resources\php-template.ini") (Join-Path $Desktop "build-resources\php\php.ini") -Force

Write-Host "Installing Electron dependencies..." -ForegroundColor Cyan
npm install --prefix $Desktop

Write-Host "Building Windows installer (MiningERP Setup.exe)..." -ForegroundColor Cyan
$env:CSC_IDENTITY_AUTO_DISCOVERY = "false"
npm run dist --prefix $Desktop

$installer = Get-ChildItem (Join-Path $Root "dist-desktop") -Filter "MiningERP Setup*.exe" |
    Sort-Object LastWriteTime -Descending |
    Select-Object -First 1

Write-Host ""
if ($installer) {
    Write-Host "Build complete:" -ForegroundColor Green
    Write-Host "  $($installer.FullName)"
} else {
    Write-Host "Build finished. Check dist-desktop for output files." -ForegroundColor Yellow
}
