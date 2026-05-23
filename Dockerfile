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

# 🚀 NOVA ESTRATÉGIA: Aponta o banco para dentro da pasta storage do projeto
RUN sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=sqlite/g' .env
RUN sed -i 's/DB_DATABASE=.*/DB_DATABASE=\/var\/www\/html\/storage\/database.sqlite/g' .env

RUN php artisan key:generate

RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan view:clear

# CRIA O ARQUIVO DO BANCO DENTRO DE STORAGE
RUN touch /var/www/html/storage/database.sqlite && chmod 777 /var/www/html/storage/database.sqlite

# Garante permissão absoluta para o Apache (www-data) na pasta storage inteira
RUN chown -R www-data:www-data /var/www/html/storage
RUN chown -R www-data:www-data /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage

RUN a2enmod rewrite

COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

# Força o fresh usando o caminho novo da pasta storage ao iniciar
CMD composer dump-autoload --optimize && php artisan migrate:fresh --force && apache2-foreground