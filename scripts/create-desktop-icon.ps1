$ErrorActionPreference = "Stop"

$desktop = Join-Path $PSScriptRoot "..\desktop"
$png = Join-Path $desktop "build-resources\icon.png"
$ico = Join-Path $desktop "build-resources\icon.ico"

if (-not (Test-Path $png)) {
    throw "Missing icon source: $png"
}

Add-Type -AssemblyName System.Drawing
$src = [System.Drawing.Image]::FromFile($png)
$size = 256
$bmp = New-Object System.Drawing.Bitmap $size, $size
$graphics = [System.Drawing.Graphics]::FromImage($bmp)
$graphics.Clear([System.Drawing.Color]::FromArgb(15, 23, 42))
$graphics.DrawImage($src, 0, 0, $size, $size)
$icon = [System.Drawing.Icon]::FromHandle($bmp.GetHicon())
$stream = [System.IO.File]::Create($ico)
$icon.Save($stream)
$stream.Close()
$src.Dispose()
$bmp.Dispose()
$icon.Dispose()

Write-Host "Created $ico" -ForegroundColor Green
