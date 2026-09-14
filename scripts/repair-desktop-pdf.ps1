$ErrorActionPreference = "Stop"

$appData = Join-Path $env:APPDATA "MiningERP"
$fontTarget = Join-Path $appData "storage\fonts"
$mpdfTemp = Join-Path $appData "storage\app\mpdf-tmp"

$fontSources = @(
    "C:\Program Files\Mining ERP\Mining ERP\resources\pdf-fonts",
    "C:\Program Files\Mining ERP\Mining ERP\resources\laravel\storage\fonts",
    "C:\Program Files (x86)\Mining ERP\Mining ERP\resources\pdf-fonts",
    "C:\Program Files (x86)\Mining ERP\Mining ERP\resources\laravel\storage\fonts"
)

New-Item -ItemType Directory -Path $fontTarget -Force | Out-Null
New-Item -ItemType Directory -Path $mpdfTemp -Force | Out-Null

$source = $null
foreach ($candidate in $fontSources) {
    if ((Test-Path $candidate) -and (Test-Path (Join-Path $candidate "NotoSansArabic-Regular.ttf"))) {
        $source = $candidate
        break
    }
}

if (-not $source) {
    Write-Host "Could not find bundled PDF fonts in Program Files." -ForegroundColor Red
    Write-Host "Reinstall Mining ERP or run this script after installing the desktop app."
    exit 1
}

Get-ChildItem $source -Filter "*.ttf" | ForEach-Object {
    Copy-Item $_.FullName (Join-Path $fontTarget $_.Name) -Force
    Write-Host "Copied $($_.Name)" -ForegroundColor Green
}

Write-Host ""
Write-Host "PDF repair complete." -ForegroundColor Green
Write-Host "Fonts: $fontTarget"
Write-Host "Temp:  $mpdfTemp"
Write-Host ""
Write-Host "Close Mining ERP completely, then open it again and try PDF export."
