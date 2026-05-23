FROM php:8.3-apache

# Instalar dependências do sistema e extensões PHP para PostgreSQL
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar projeto Laravel para dentro do container
COPY . /var/www/html

# Diretório de trabalho
WORKDIR /var/www/html

# --- ALTERAÇÃO AQUI: Adicionado --no-scripts ---
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Permissões para as pastas de escrita
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Apontar Apache para /public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80

# No comando final, o container já terá as variáveis de ambiente, então os scripts funcionam
CMD php artisan package:discover --ansi && php artisan migrate --force && apache2-foreground