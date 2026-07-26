# HTTP smoke measurement

Set `SIMAU_BASE_URL` to the HTTPS service URL, without a trailing slash. The
scripts call only `/health/live`; they do not send credentials, cookies, or
request bodies. Run a single sample after a known idle period for a cold
candidate, then run 30 warm samples separately.

PowerShell:

```powershell
$env:SIMAU_BASE_URL = 'https://service.example'
./tests/performance/http-smoke.ps1
```

POSIX shell:

```sh
SIMAU_BASE_URL=https://service.example ./tests/performance/http-smoke.sh
```
