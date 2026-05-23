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

# Garante permissões corretas
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Cria banco SQLite no local correto
RUN mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite

# Permissão do banco
RUN chmod 777 /var/www/html/database/database.sqlite

# Apache rewrite
RUN a2enmod rewrite

# Config Apache (se tiver)
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

# IMPORTANTE: NÃO usar migrate:fresh em produção
CMD php artisan config:clear \
    && php artisan migrate --force \
    && apache2-foreground