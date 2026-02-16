param(
    [switch]$SkipBuild = $false
)

$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $PSScriptRoot
$flutterProject = Join-Path $projectRoot '.mobile\ContractorPortalFlutter'
$sourceApk = Join-Path $flutterProject 'build\app\outputs\flutter-apk\app-release.apk'
$targetDir = Join-Path $projectRoot 'public\downloads'
$targetApk = Join-Path $targetDir 'contractor-portal-latest.apk'

if (-not (Test-Path $flutterProject)) {
    throw "Flutter project not found at: $flutterProject"
}

if (-not (Test-Path $targetDir)) {
    New-Item -ItemType Directory -Path $targetDir -Force | Out-Null
}

Push-Location $flutterProject
try {
    if (-not $SkipBuild) {
        Write-Host 'Running flutter pub get...'
        flutter pub get

        Write-Host 'Building release APK...'
        flutter build apk --release
    }
}
finally {
    Pop-Location
}

if (-not (Test-Path $sourceApk)) {
    throw "Release APK not found at: $sourceApk"
}

Copy-Item -Path $sourceApk -Destination $targetApk -Force

$apkInfo = Get-Item $targetApk
Write-Host ''
Write-Host 'APK published successfully:'
Write-Host "Path: $($apkInfo.FullName)"
Write-Host "Size: $([math]::Round($apkInfo.Length / 1MB, 2)) MB"
Write-Host "Updated: $($apkInfo.LastWriteTime)"
