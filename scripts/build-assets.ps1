[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot
$assetsRoot = Join-Path $projectRoot 'public/assets'
$buildRoot = Join-Path $assetsRoot 'build'

New-Item -ItemType Directory -Force -Path $buildRoot | Out-Null

$sources = @{
    'core-js' = 'js/app.js'
    'core-css' = 'css/app.css'
    'custom-css' = 'css/style-custom.css'
}
$assets = [ordered]@{}

foreach ($entry in $sources.GetEnumerator()) {
    $sourcePath = Join-Path $assetsRoot $entry.Value
    if (-not (Test-Path -LiteralPath $sourcePath -PathType Leaf)) {
        throw "Missing frontend asset: $sourcePath"
    }

    $hash = (Get-FileHash -LiteralPath $sourcePath -Algorithm SHA256).Hash.ToLowerInvariant()
    $extension = [IO.Path]::GetExtension($sourcePath)
    $destinationName = "$($entry.Key).$($hash.Substring(0, 16))$extension"
    $destinationPath = Join-Path $buildRoot $destinationName
    Copy-Item -LiteralPath $sourcePath -Destination $destinationPath -Force
    $assets[$entry.Key] = [ordered]@{
        path = "assets/build/$destinationName"
        sha256 = $hash
    }
}

$manifest = [ordered]@{
    version = 1
    immutable = $true
    assets = $assets
} | ConvertTo-Json -Depth 4
[IO.File]::WriteAllText((Join-Path $assetsRoot 'manifest.json'), $manifest + [Environment]::NewLine, [Text.UTF8Encoding]::new($false))

Write-Output 'Hashed frontend assets written to public/assets/build.'
