#!/usr/bin/env sh
set -eu

: "${SIMAU_BASE_URL:?Set SIMAU_BASE_URL to an HTTPS service URL.}"
base_url=${SIMAU_BASE_URL%/}

for sample in $(seq 1 30); do
    curl -sS -o /dev/null \
        -w "sample=${sample} code=%{http_code} dns=%{time_namelookup} connect=%{time_connect} tls=%{time_appconnect} ttfb=%{time_starttransfer} total=%{time_total}\\n" \
        "${base_url}/health/live"
done
