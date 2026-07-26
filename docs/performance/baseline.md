# Performance baseline worksheet

Status: not measured in this workspace. Record production measurements only with
read-only access and without credentials, cookies, employee data, SQL bindings,
or raw query text.

| Item | Value | Evidence |
|---|---|---|
| Commit SHA | pending | `git rev-parse HEAD` |
| Timestamp/timezone | pending | measurement log |
| Render instance/region | UNKNOWN | Render settings |
| TiDB plan/provider/region | UNKNOWN | TiDB overview |
| Cold request | pending | one timestamped sample |
| Warm p50/p95 | pending | 30 sequential samples |
| Request/query count | pending | aggregate telemetry |

Use `tests/performance/http-smoke.ps1` or `tests/performance/http-smoke.sh`
against `/health/live` before measuring authenticated routes separately.
