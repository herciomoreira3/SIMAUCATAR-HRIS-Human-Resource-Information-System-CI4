#!/usr/bin/env bash
set -euo pipefail

readonly APP_ROOT=/var/www/html
readonly CIENV="${CI_ENVIRONMENT:-production}"

if [[ ! "$CIENV" =~ ^(production|development|testing)$ ]]; then
    echo '[startup] CI_ENVIRONMENT must be production, development, or testing.' >&2
    exit 64
fi

resolve_environment() {
    local primary="$1"
    local legacy="$2"
    local fallback="$3"
    local value="${!primary:-}"

    # Render services created before the optimization used dotted CodeIgniter
    # variable names. printenv can read those names even though bash indirect
    # expansion cannot.
    if [[ -z "$value" && -n "$legacy" ]]; then
        value="$(printenv "$legacy" 2>/dev/null || true)"
    fi
    if [[ -z "$value" ]]; then
        value="$fallback"
    fi
    printf '%s' "$value"
}

require_value() {
    local name="$1"
    local value="$2"
    if [[ -z "$value" ]]; then
        echo "[startup] Required environment variable is missing: ${name}" >&2
        exit 78
    fi
}

APP_BASE_URL_VALUE="$(resolve_environment APP_BASE_URL app.baseURL "${RENDER_EXTERNAL_URL:-}")"
DB_HOST_VALUE="$(resolve_environment DB_HOST database.default.hostname '')"
DB_NAME_VALUE="$(resolve_environment DB_NAME database.default.database '')"
DB_USER_VALUE="$(resolve_environment DB_USER database.default.username '')"
DB_PASS_VALUE="$(resolve_environment DB_PASS database.default.password '')"
DB_PORT_VALUE="$(resolve_environment DB_PORT database.default.port '3306')"
DB_SSL_VALUE="$(resolve_environment DB_SSL database.default.encrypt 'true')"
ENCRYPTION_KEY_VALUE="$(resolve_environment ENCRYPTION_KEY encryption.key '')"
STORAGE_DRIVER_VALUE="$(resolve_environment STORAGE_DRIVER storage.driver 'local')"
STORAGE_S3_ENDPOINT_VALUE="$(resolve_environment STORAGE_S3_ENDPOINT storage.s3.endpoint '')"
STORAGE_S3_BUCKET_VALUE="$(resolve_environment STORAGE_S3_BUCKET storage.s3.bucket '')"
STORAGE_S3_REGION_VALUE="$(resolve_environment STORAGE_S3_REGION storage.s3.region 'us-east-1')"
STORAGE_S3_ACCESS_KEY_VALUE="$(resolve_environment STORAGE_S3_ACCESS_KEY storage.s3.accessKey '')"
STORAGE_S3_SECRET_KEY_VALUE="$(resolve_environment STORAGE_S3_SECRET_KEY storage.s3.secretKey '')"
DB_SSL_CA_VALUE="$(resolve_environment DB_SSL_CA database.default.encrypt.ssl_ca '')"

if [[ "$CIENV" == 'production' ]]; then
    require_value APP_BASE_URL "$APP_BASE_URL_VALUE"
    require_value DB_HOST "$DB_HOST_VALUE"
    require_value DB_NAME "$DB_NAME_VALUE"
    require_value DB_USER "$DB_USER_VALUE"
    require_value DB_PASS "$DB_PASS_VALUE"
    if [[ -z "$ENCRYPTION_KEY_VALUE" ]]; then
        # Compatibility for existing Render services that predate the explicit
        # secret. The derived key is stable across restarts while the database
        # credential remains unchanged; no key material is printed to logs.
        ENCRYPTION_KEY_VALUE="hex2bin:$(printf '%s' "simaucatar|$DB_HOST_VALUE|$DB_NAME_VALUE|$DB_PASS_VALUE" | sha256sum | cut -d' ' -f1)"
        echo '[startup] WARNING: ENCRYPTION_KEY is not configured; using a stable derived compatibility key. Configure an explicit secret.' >&2
    fi

    if [[ "$DB_SSL_VALUE" != 'true' && "$DB_SSL_VALUE" != '1' ]]; then
        echo '[startup] Production requires DB_SSL=true to preserve database TLS verification.' >&2
        exit 78
    fi

    if [[ "$STORAGE_DRIVER_VALUE" == 's3' ]]; then
        require_value STORAGE_S3_ENDPOINT "$STORAGE_S3_ENDPOINT_VALUE"
        require_value STORAGE_S3_BUCKET "$STORAGE_S3_BUCKET_VALUE"
        require_value STORAGE_S3_REGION "$STORAGE_S3_REGION_VALUE"
        require_value STORAGE_S3_ACCESS_KEY "$STORAGE_S3_ACCESS_KEY_VALUE"
        require_value STORAGE_S3_SECRET_KEY "$STORAGE_S3_SECRET_KEY_VALUE"
    else
        echo '[startup] WARNING: STORAGE_DRIVER is local; Render local uploads are ephemeral. Configure private S3 storage.' >&2
    fi
fi

quote_env_value() {
    local value="$1"
    value="${value//\\/\\\\}"
    value="${value//\'/\\\'}"
    printf "'%s'" "$value"
}

write_env() {
    local key="$1"
    local value="$2"
    printf '%s = %s\n' "$key" "$(quote_env_value "$value")" >> "$APP_ROOT/.env"
}

umask 077
# Targeted ownership only; avoid a recursive boot-time chown across a mounted
# project tree while still supporting an empty writable/upload volume.
install -d -o www-data -g www-data -m 0775 \
    "$APP_ROOT/writable/cache" \
    "$APP_ROOT/writable/logs" \
    "$APP_ROOT/writable/session" \
    "$APP_ROOT/writable/debugbar" \
    "$APP_ROOT/public/uploads/perfil" \
    "$APP_ROOT/public/uploads/lisensa" \
    "$APP_ROOT/public/uploads/sansaun"
: > "$APP_ROOT/.env"
write_env 'CI_ENVIRONMENT' "$CIENV"
write_env 'app.baseURL' "${APP_BASE_URL_VALUE:-http://localhost:8080/}"
write_env 'database.default.hostname' "$DB_HOST_VALUE"
write_env 'database.default.database' "$DB_NAME_VALUE"
write_env 'database.default.username' "$DB_USER_VALUE"
write_env 'database.default.password' "$DB_PASS_VALUE"
write_env 'database.default.DBDriver' 'MySQLi'
write_env 'database.default.port' "$DB_PORT_VALUE"
write_env 'encryption.key' "$ENCRYPTION_KEY_VALUE"
write_env 'PERF_TELEMETRY_ENABLED' "${PERF_TELEMETRY_ENABLED:-false}"
write_env 'app.forceGlobalSecureRequests' "${FORCE_HTTPS:-false}"
if [[ "$CIENV" == 'production' ]]; then
    write_env 'cookie.secure' "${COOKIE_SECURE:-true}"
else
    write_env 'cookie.secure' "${COOKIE_SECURE:-false}"
fi

write_env 'storage.driver' "$STORAGE_DRIVER_VALUE"
write_env 'storage.s3.endpoint' "$STORAGE_S3_ENDPOINT_VALUE"
write_env 'storage.s3.bucket' "$STORAGE_S3_BUCKET_VALUE"
write_env 'storage.s3.region' "$STORAGE_S3_REGION_VALUE"
write_env 'storage.s3.accessKey' "$STORAGE_S3_ACCESS_KEY_VALUE"
write_env 'storage.s3.secretKey' "$STORAGE_S3_SECRET_KEY_VALUE"
write_env 'storage.s3.prefix' "${STORAGE_S3_PREFIX:-simaucatar}"
write_env 'storage.s3.pathStyle' "${STORAGE_S3_PATH_STYLE:-true}"
write_env 'storage.s3.timeoutSeconds' "${STORAGE_S3_TIMEOUT_SECONDS:-15}"

if [[ "$DB_SSL_VALUE" == 'true' || "$DB_SSL_VALUE" == '1' ]]; then
    write_env 'database.default.encrypt.ssl_verify' 'true'
    if [[ -n "$DB_SSL_CA_VALUE" ]]; then
        write_env 'database.default.encrypt.ssl_ca' "$DB_SSL_CA_VALUE"
    fi
fi

chown www-data:www-data "$APP_ROOT/.env"
chmod 0600 "$APP_ROOT/.env"
echo '[startup] Runtime configuration generated from environment.'

# Migrations belong to a protected release step. This temporary escape hatch is
# explicit and fails closed; it never runs a seeder and is off by default.
if [[ "${RUN_MIGRATIONS_ONCE:-false}" == 'true' ]]; then
    echo '[startup] Running explicitly requested one-shot migrations.'
    (cd "$APP_ROOT" && php spark migrate --no-interaction)
fi

echo '[startup] Starting web server.'
exec "$@"
