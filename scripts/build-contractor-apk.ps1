param(
    [switch]$SkipBuild = $false
)

$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $PSScriptRoot
$flutterProject = Join-Path $projectRoot '.mobile\ContractorPortalFlutter'
$sourceApk = Join-Path $flutterProject 'build\app\outputs\flutter-apk\app-release.apk'
$targetDir = Join-Path $projectRoot 'public\downloads'
$targetApk = Join-Path $targetDir 'contractor-portal-latest.apk'
$expectedBuildSignature = 'CONTROL_BUILD_SIG_20260216_1605'

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

        Write-Host 'Cleaning previous Flutter build cache...'
        flutter clean

        Write-Host 'Running flutter pub get after clean...'
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

Add-Type -AssemblyName System.IO.Compression.FileSystem
$zip = [System.IO.Compression.ZipFile]::OpenRead($sourceApk)
try {
    $libEntry = $zip.Entries | Where-Object { $_.FullName -eq 'lib/arm64-v8a/libapp.so' } | Select-Object -First 1

    if (-not $libEntry) {
        throw 'libapp.so not found inside built APK.'
    }

    $ms = New-Object System.IO.MemoryStream
    $stream = $libEntry.Open()
    try {
        $stream.CopyTo($ms)
    }
    finally {
        $stream.Dispose()
    }

    $payload = [System.Text.Encoding]::ASCII.GetString($ms.ToArray())
    if (-not $payload.Contains($expectedBuildSignature)) {
        throw "Build signature verification failed. Expected signature '$expectedBuildSignature' was not found in APK."
    }
}
finally {
    $zip.Dispose()
}

Copy-Item -Path $sourceApk -Destination $targetApk -Force

$apkInfo = Get-Item $targetApk
Write-Host ''
Write-Host 'APK published successfully:'
Write-Host "Path: $($apkInfo.FullName)"
Write-Host "Size: $([math]::Round($apkInfo.Length / 1MB, 2)) MB"
Write-Host "Updated: $($apkInfo.LastWriteTime)"
