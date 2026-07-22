#!/bin/bash
set -e

# Generate .env file from Render environment variables
cat > /var/www/html/.env << EOF
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------

CI_ENVIRONMENT = ${CI_ENVIRONMENT:-production}

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

EOF

# Handle SSL if DB_SSL is set to true
if [ "${DB_SSL}" = "true" ] || [ "${DB_SSL}" = "1" ]; then
    echo "database.default.encrypt = ssl_verify" >> /var/www/html/.env
fi

echo "[startup] .env file generated successfully."

# Set correct permissions for writable directories
chown -R www-data:www-data /var/www/html/writable
chmod -R 775 /var/www/html/writable

# Ensure uploads directory exists and is writable
mkdir -p /var/www/html/public/uploads/perfil
mkdir -p /var/www/html/public/uploads/lisensa
mkdir -p /var/www/html/public/uploads/sansaun
chown -R www-data:www-data /var/www/html/public/uploads
chmod -R 775 /var/www/html/public/uploads

echo "[startup] Permissions set."

# Run database migrations automatically on startup
cd /var/www/html
php spark migrate --no-interaction 2>&1 || echo "[startup] Migrations failed or already up to date."
php spark db:seed HrisSeeder --no-interaction 2>&1 || echo "[startup] Seeder already run or failed."

echo "[startup] Starting Apache..."
exec apache2-foreground
