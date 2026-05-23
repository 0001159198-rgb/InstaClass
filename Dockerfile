FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo_pgsql pdo_mysql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN cp .env.example .env

RUN php artisan key:generate

RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan view:clear

RUN chown -R www-data:www-data /var/www/html/storage
RUN chown -R www-data:www-data /var/www/html/bootstrap/cache

RUN a2enmod rewrite

COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

# Comando padrão do Apache (com o migrate seguro para bancos reais)
CMD composer dump-autoload --optimize && php artisan migrate --force && apache2-foreground