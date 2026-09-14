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
        "storage\fonts\NotoSansArabic-Bold.ttf",
        "resources\fonts\NotoSansArabic-Regular.ttf",
        "resources\fonts\NotoSansArabic-Bold.ttf",
        "app\Support\MpdfConfiguration.php",
        "app\Services\MpdfPdfService.php"
    )

    foreach ($file in $requiredFiles) {
        $path = Join-Path $Destination $file
        if (-not (Test-Path $path)) {
            throw "Missing required bundle file: $file"
        }
    }
}

Write-Host "Preparing Laravel bundle for desktop..." -ForegroundColor Cyan
Copy-LaravelBundle -Source $Root.Path -Destination $LaravelBundle
Write-Host "Preparing Laravel bundle for desktop..." -ForegroundColor Cyan
Copy-LaravelBundle -Source $Root.Path -Destination $LaravelBundle

$fontSources = @(
    (Join-Path $Root.Path "storage\fonts"),
    (Join-Path $Root.Path "resources\fonts")
)
$fontTarget = Join-Path $Desktop "build-resources\fonts"
if (Test-Path $fontTarget) {
    Remove-Item $fontTarget -Recurse -Force
}
New-Item -ItemType Directory -Path $fontTarget -Force | Out-Null
foreach ($source in $fontSources) {
    if (Test-Path $source) {
        Get-ChildItem $source -Filter "*.ttf" | Copy-Item -Destination $fontTarget -Force
    }
}

if (-not (Test-Path (Join-Path $fontTarget "NotoSansArabic-Regular.ttf"))) {
    throw "Missing PDF fonts in desktop build-resources/fonts"
}

Write-Host "Laravel bundle ready at $LaravelBundle" -ForegroundColor Green
