param(
    [string]$WorkspaceRoot = "..\.."
)

$projectRoot = Resolve-Path (Join-Path $PSScriptRoot $WorkspaceRoot)
$destinationDir = Join-Path $projectRoot "public\downloads"
$destinationApk = Join-Path $destinationDir "contractor-portal-latest.apk"

$releaseApk = Join-Path $PSScriptRoot "app\build\outputs\apk\release\app-release.apk"
$debugApk = Join-Path $PSScriptRoot "app\build\outputs\apk\debug\app-debug.apk"

if (-not (Test-Path $destinationDir)) {
    New-Item -ItemType Directory -Path $destinationDir -Force | Out-Null
}

$sourceApk = $null
if (Test-Path $releaseApk) {
    $sourceApk = $releaseApk
} elseif (Test-Path $debugApk) {
    $sourceApk = $debugApk
}

if (-not $sourceApk) {
    Write-Error "No APK found. Build the Android project first (debug or release)."
    exit 1
}

Copy-Item -Path $sourceApk -Destination $destinationApk -Force
Write-Host "APK published to: $destinationApk"
