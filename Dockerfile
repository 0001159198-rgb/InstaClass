FROM php:8.3-apache

# Dependências
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libsqlite3-dev \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo_sqlite pdo_pgsql pdo_mysql zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Instala dependências
RUN composer install --no-dev --optimize-autoloader

# Permissões Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# ✅ SQLite no lugar correto (IMPORTANTE)
RUN mkdir -p /var/www/html/storage \
    && touch /var/www/html/storage/database.sqlite \
    && chown www-data:www-data /var/www/html/storage/database.sqlite \
    && chmod 664 /var/www/html/storage/database.sqlite

# Apache rewrite
RUN a2enmod rewrite

COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

# ❌ SEM migrate:fresh (isso quebrava seu banco)
CMD php artisan config:clear \
    && php artisan migrate --force \
    && apache2-foreground