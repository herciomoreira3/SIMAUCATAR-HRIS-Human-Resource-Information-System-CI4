param(
    [string] $BaseUrl = $env:SIMAU_BASE_URL,
    [int] $Samples = 30
)

if ([string]::IsNullOrWhiteSpace($BaseUrl)) {
    throw 'Set SIMAU_BASE_URL or pass -BaseUrl with an HTTPS service URL.'
}

$uri = ($BaseUrl.TrimEnd('/') + '/health/live')
1..$Samples | ForEach-Object {
    $stopwatch = [System.Diagnostics.Stopwatch]::StartNew()
    $response = Invoke-WebRequest -Uri $uri -UseBasicParsing
    $stopwatch.Stop()
    [pscustomobject]@{
        sample = $_
        status = $response.StatusCode
        total_ms = $stopwatch.ElapsedMilliseconds
    }
}
