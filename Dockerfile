FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
git \
curl \
zip \
unzip \
libpq-dev \
libzip-dev \
&& docker-php-ext-install pdo_pgsql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN cp .env.example .env

# 🚀 CORREÇÃO CRÍTICA: Força o arquivo .env de dentro do container a usar o SQLite gratuito
RUN sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=sqlite/g' .env
RUN sed -i 's/DB_DATABASE=.*/DB_DATABASE=\/tmp\/database.sqlite/g' .env

RUN php artisan key:generate

RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan view:clear

# CRIA O BANCO DE DADOS EM ARQUIVO GRATUITO AQUI:
RUN touch /tmp/database.sqlite && chmod 777 /tmp/database.sqlite

RUN chown -R www-data:www-data /var/www/html/storage
RUN chown -R www-data:www-data /var/www/html/bootstrap/cache

RUN a2enmod rewrite

COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD php artisan migrate --force && apache2-foreground