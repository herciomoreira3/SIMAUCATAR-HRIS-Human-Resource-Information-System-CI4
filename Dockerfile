FROM composer:2.7 AS dependencies

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --classmap-authoritative

FROM php:8.3-apache-bookworm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        intl \
        mysqli \
        gd \
        zip \
        opcache

# Enable Apache modules
RUN a2enmod deflate expires headers rewrite

# Configure Apache DocumentRoot to point to /var/www/html/public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set up working directory
WORKDIR /var/www/html

# Copy only the Docker build context (controlled by .dockerignore) and the
# production dependency tree built from composer.lock.
COPY --chown=www-data:www-data . /var/www/html/
COPY --from=dependencies --chown=www-data:www-data /app/vendor /var/www/html/vendor

# Create necessary upload directories
RUN install -d -o www-data -g www-data -m 0775 \
        /var/www/html/writable/cache \
        /var/www/html/writable/logs \
        /var/www/html/writable/session \
        /var/www/html/writable/debugbar \
        /var/www/html/public/uploads/perfil \
        /var/www/html/public/uploads/lisensa \
        /var/www/html/public/uploads/sansaun

# Copy and set up entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 80
EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
