#!/bin/bash
set -e

# Create all required writable subdirectories (CI4 needs these)
mkdir -p /var/www/html/writable/cache
mkdir -p /var/www/html/writable/logs
mkdir -p /var/www/html/writable/session
mkdir -p /var/www/html/writable/debugbar
mkdir -p /var/www/html/public/uploads/perfil
mkdir -p /var/www/html/public/uploads/lisensa
mkdir -p /var/www/html/public/uploads/sansaun

echo "[startup] Writable directories created."

# Determine CI_ENVIRONMENT (default to development for initial deploy debugging)
CIENV=${CI_ENVIRONMENT:-development}

# Generate .env file from Render environment variables
cat > /var/www/html/.env << ENVEOF
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------

CI_ENVIRONMENT = ${CIENV}

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------

app.baseURL = '${APP_BASE_URL:-https://simaucatar-hris-human-resource.onrender.com/}'

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------

database.default.hostname = ${DB_HOST:-localhost}
database.default.database = ${DB_NAME:-test}
database.default.username = ${DB_USER:-root}
database.default.password = ${DB_PASS:-}
database.default.DBDriver = MySQLi
database.default.port     = ${DB_PORT:-3306}

#--------------------------------------------------------------------
# ENCRYPTION
#--------------------------------------------------------------------

encryption.key = hex2bin:76d541760701986c4da3bcc317cca4e3ddb80339fd8c8ebcb3be6df11479760e

ENVEOF

# Handle SSL if DB_SSL is set to true — use '1' so CI4 reads it as boolean true
if [ "${DB_SSL}" = "true" ] || [ "${DB_SSL}" = "1" ]; then
    echo "database.default.encrypt = 1" >> /var/www/html/.env
fi

echo "[startup] .env file generated:"
cat /var/www/html/.env

# Set correct permissions for writable directories
chown -R www-data:www-data /var/www/html/writable
chmod -R 775 /var/www/html/writable
chown -R www-data:www-data /var/www/html/public/uploads
chmod -R 775 /var/www/html/public/uploads

echo "[startup] Permissions set."

# Run database migrations automatically on startup
cd /var/www/html
echo "[startup] Running migrations..."
php spark migrate --no-interaction 2>&1
MIGRATE_STATUS=$?
if [ $MIGRATE_STATUS -ne 0 ]; then
  echo "[startup] WARNING: Migrations failed with code $MIGRATE_STATUS"
else
  echo "[startup] Migrations complete."
fi

echo "[startup] Running seeder..."
php spark db:seed HrisSeeder --no-interaction 2>&1 || echo "[startup] Seeder already run or failed."

echo "[startup] Starting Apache..."
exec apache2-foreground
