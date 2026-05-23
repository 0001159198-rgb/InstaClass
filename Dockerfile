FROM php:8.3-apache

# Dependências do sistema
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

# Copia projeto
COPY . .

# Instala dependências
RUN composer install --no-dev --optimize-autoloader

# Permissões Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Apache rewrite
RUN a2enmod rewrite

# Config Apache (se você usa vhost customizado)
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

# =========================
# 🚀 CMD FINAL (AQUI ESTÁ A CORREÇÃO)
# =========================
CMD bash -c "php artisan optimize:clear && touch /tmp/database.sqlite && chmod 666 /tmp/database.sqlite && php artisan migrate --force && apache2-foreground"