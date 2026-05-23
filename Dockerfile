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

# 🚀 ESTRATÉGIA INFALÍVEL: Move o banco para a pasta /public, onde o Apache TEM permissão de escrita total
RUN sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=sqlite/g' .env
RUN sed -i 's/DB_DATABASE=.*/DB_DATABASE=\/var\/www\/html\/public\/database.sqlite/g' .env

RUN php artisan key:generate

RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan view:clear

# CRIA O ARQUIVO DO BANCO DIRETO NA PASTA PUBLIC
RUN touch /var/www/html/public/database.sqlite && chmod 777 /var/www/html/public/database.sqlite

# Dá permissão total para o usuário do Apache (www-data) na pasta public
RUN chown -R www-data:www-data /var/www/html/public
RUN chmod -R 777 /var/www/html/public

RUN chown -R www-data:www-data /var/www/html/storage
RUN chown -R www-data:www-data /var/www/html/bootstrap/cache

RUN a2enmod rewrite

COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

# Força o fresh usando o novo caminho público ao iniciar o servidor
CMD composer dump-autoload --optimize && php artisan migrate:fresh --force && apache2-foreground