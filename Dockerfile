# 1. Define a imagem base oficial do PHP 8.3 com o servidor Apache integrado
FROM php:8.3-apache

# 2. Atualiza os pacotes do sistema e instala as ferramentas necessárias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# 3. Instala e ativa as extensões PDO do PHP para conectar ao PostgreSQL (pdo_pgsql)
RUN docker-php-ext-install pdo pdo_pgsql

# Adiciona o Composer para dentro do container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Define a pasta de trabalho onde o projeto vai rodar dentro do servidor Linux
WORKDIR /var/www/html

# 5. Copia absolutamente todos os arquivos do seu projeto atual para dentro do container
COPY . .

# Roda a instalação das dependências do Laravel em modo produção
RUN composer install --no-dev --optimize-autoloader

# 6. Dá permissão para o Apache ler e gravar os arquivos corretamente (evita erros de acesso)
RUN chown -R www-data:www-data /var/www/html

# 7. Ativa o módulo 'rewrite' do Apache (essencial para que as suas rotas e o .htaccess funcionem)
RUN a2enmod rewrite

# 8. Substitui a configuração padrão do Apache pela sua configuração personalizada (da pasta .docker)
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# 9. Informa ao Render que o container vai escutar e receber tráfego na porta padrão 80
EXPOSE 80

# 10. Comando padrão para iniciar o servidor Apache em segundo plano
CMD ["apache2-foreground"]