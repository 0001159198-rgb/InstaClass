FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
git \
curl \
zip \
unzip \
libpq-dev \
libzip-dev \
&& docker-php-ext-install pdo pdo_pgsql zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

# Estas linhas abaixo criam o esqueleto que vai aceitar as variáveis do painel do Render
RUN cp .env.example .env
RUN php artisan key:generate

RUN chown -R www-data:www-data /var/www/html/storage
RUN chown -R www-data:www-data /var/www/html/bootstrap/cache

RUN a2enmod rewrite

COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["apache2-foreground"]