$ErrorActionPreference = "Stop"
Set-Location (Join-Path $PSScriptRoot "..")

Write-Host "Mining ERP Desktop Builder" -ForegroundColor Cyan
Write-Host ""
Write-Host "This builds the standalone Windows installer (MiningERP Setup.exe)." -ForegroundColor Yellow
Write-Host "Requirements on the build machine only: PHP, Composer, Node.js, Laragon PHP runtime." -ForegroundColor Yellow
Write-Host "End users do not need any of these tools." -ForegroundColor Yellow
Write-Host ""

& (Join-Path $PSScriptRoot "build-desktop.ps1")
