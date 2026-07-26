# TiDB index evidence gate

No candidate index migration is included in this release. The repository schema,
starter SQL, and production TiDB schema can differ, and no production
`SHOW CREATE TABLE`/`SHOW INDEX` or `EXPLAIN` evidence was supplied.

Before adding exactly one index, run the read-only statements in
`scripts/tidb-index-preflight.sql`, then record the following without row data,
credentials, cluster IDs, or bind values:

| Candidate/index name | Query purpose | Before plan | After plan | Rows/actRows | Decision |
|---|---|---|---|---|---|
| pending | pending | pending | pending | pending | blocked |

The eventual migration must be additive, check by index name and columns, and
its `down()` may drop only the index it created. It must not canonicalize RBAC
data, delete duplicates, or change business data. Stop if the production schema
differs, the index is not selected after healthy statistics, or DDL backfill
affects write latency/errors.
