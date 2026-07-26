#!/usr/bin/env bash
set -euo pipefail

readonly APP_ROOT=/var/www/html
readonly CIENV="${CI_ENVIRONMENT:-production}"

if [[ ! "$CIENV" =~ ^(production|development|testing)$ ]]; then
    echo '[startup] CI_ENVIRONMENT must be production, development, or testing.' >&2
    exit 64
fi

require_environment() {
    local name="$1"
    if [[ -z "${!name:-}" ]]; then
        echo "[startup] Required environment variable is missing: ${name}" >&2
        exit 78
    fi
}

if [[ "$CIENV" == 'production' ]]; then
    for required in APP_BASE_URL DB_HOST DB_NAME DB_USER DB_PASS DB_PORT DB_SSL ENCRYPTION_KEY; do
        require_environment "$required"
    done

    if [[ "$DB_SSL" != 'true' && "$DB_SSL" != '1' ]]; then
        echo '[startup] Production requires DB_SSL=true to preserve database TLS verification.' >&2
        exit 78
    fi

    if [[ "${STORAGE_DRIVER:-}" != 's3' ]]; then
        echo '[startup] Production requires STORAGE_DRIVER=s3; local storage is ephemeral on Render.' >&2
        exit 78
    fi
    for required in STORAGE_S3_ENDPOINT STORAGE_S3_BUCKET STORAGE_S3_REGION STORAGE_S3_ACCESS_KEY STORAGE_S3_SECRET_KEY; do
        require_environment "$required"
    done
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
write_env 'app.baseURL' "${APP_BASE_URL:-http://localhost:8080/}"
write_env 'database.default.hostname' "${DB_HOST:-localhost}"
write_env 'database.default.database' "${DB_NAME:-test}"
write_env 'database.default.username' "${DB_USER:-root}"
write_env 'database.default.password' "${DB_PASS:-}"
write_env 'database.default.DBDriver' 'MySQLi'
write_env 'database.default.port' "${DB_PORT:-3306}"
write_env 'encryption.key' "${ENCRYPTION_KEY:-}"
write_env 'PERF_TELEMETRY_ENABLED' "${PERF_TELEMETRY_ENABLED:-false}"
write_env 'app.forceGlobalSecureRequests' "${FORCE_HTTPS:-false}"
if [[ "$CIENV" == 'production' ]]; then
    write_env 'cookie.secure' "${COOKIE_SECURE:-true}"
else
    write_env 'cookie.secure' "${COOKIE_SECURE:-false}"
fi

write_env 'storage.driver' "${STORAGE_DRIVER:-local}"
write_env 'storage.s3.endpoint' "${STORAGE_S3_ENDPOINT:-}"
write_env 'storage.s3.bucket' "${STORAGE_S3_BUCKET:-}"
write_env 'storage.s3.region' "${STORAGE_S3_REGION:-us-east-1}"
write_env 'storage.s3.accessKey' "${STORAGE_S3_ACCESS_KEY:-}"
write_env 'storage.s3.secretKey' "${STORAGE_S3_SECRET_KEY:-}"
write_env 'storage.s3.prefix' "${STORAGE_S3_PREFIX:-simaucatar}"
write_env 'storage.s3.pathStyle' "${STORAGE_S3_PATH_STYLE:-true}"
write_env 'storage.s3.timeoutSeconds' "${STORAGE_S3_TIMEOUT_SECONDS:-15}"

if [[ "${DB_SSL:-false}" == 'true' || "${DB_SSL:-false}" == '1' ]]; then
    write_env 'database.default.encrypt.ssl_verify' 'true'
    if [[ -n "${DB_SSL_CA:-}" ]]; then
        write_env 'database.default.encrypt.ssl_ca' "$DB_SSL_CA"
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
