$ErrorActionPreference = "Stop"

$Root = Resolve-Path (Join-Path $PSScriptRoot "..")
$Desktop = Join-Path $Root "desktop"
$PhpTarget = Join-Path $Desktop "build-resources\php"
$LaragonPhpRoot = "C:\laragon\bin\php"
$PhpVersion = "8.3.16"
$PhpZipName = "php-$PhpVersion-Win32-vs16-x64.zip"
$PhpDownloadUrl = "https://windows.php.net/downloads/releases/$PhpZipName"

function Find-LaragonPhp {
    if ($env:MININGERP_BUILD_PHP -and (Test-Path $env:MININGERP_BUILD_PHP)) {
        return (Resolve-Path $env:MININGERP_BUILD_PHP).Path
    }

    if (-not (Test-Path $LaragonPhpRoot)) {
        return $null
    }

    $version = Get-ChildItem $LaragonPhpRoot -Directory |
        Where-Object { $_.Name -like "php-*" } |
        Sort-Object Name -Descending |
        Select-Object -First 1

    if (-not $version) {
        return $null
    }

    $phpExe = Join-Path $version.FullName "php.exe"
    if (-not (Test-Path $phpExe)) {
        return $null
    }

    return $version.FullName
}

function Download-PortablePhp {
    $tempRoot = Join-Path $env:TEMP "miningerp-php-build"
    $zipPath = Join-Path $tempRoot $PhpZipName
    $extractPath = Join-Path $tempRoot "extracted"

    if (Test-Path $tempRoot) {
        Remove-Item $tempRoot -Recurse -Force
    }

    New-Item -ItemType Directory -Path $extractPath -Force | Out-Null
    Write-Host "Downloading portable PHP $PhpVersion..." -ForegroundColor Yellow
    Invoke-WebRequest -Uri $PhpDownloadUrl -OutFile $zipPath
    Expand-Archive -Path $zipPath -DestinationPath $extractPath -Force

    return $extractPath
}

Write-Host "Preparing bundled PHP runtime..." -ForegroundColor Cyan
$phpSource = Find-LaragonPhp

if (-not $phpSource) {
    $phpSource = Download-PortablePhp
}

if (-not (Test-Path (Join-Path $phpSource "php.exe"))) {
    throw "Unable to prepare PHP runtime. Set MININGERP_BUILD_PHP to a folder containing php.exe."
}

if (Test-Path $PhpTarget) {
    Remove-Item $PhpTarget -Recurse -Force
}

New-Item -ItemType Directory -Path $PhpTarget -Force | Out-Null
Copy-Item -Path (Join-Path $phpSource "*") -Destination $PhpTarget -Recurse -Force

$customIni = Join-Path $Desktop "build-resources\php-template.ini"
Copy-Item $customIni (Join-Path $PhpTarget "php.ini") -Force

$phpExe = Join-Path $PhpTarget "php.exe"
$version = & $phpExe -v | Select-Object -First 1
Write-Host "Bundled PHP ready: $version" -ForegroundColor Green
