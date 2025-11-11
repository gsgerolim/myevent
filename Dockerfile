FROM php:8.2-apache

# Instala dependências do sistema e extensões PHP necessárias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
 && docker-php-ext-install pdo pdo_pgsql zip \
 && a2enmod rewrite

# Copia os arquivos do projeto
COPY . /var/www/html/
WORKDIR /var/www/html/

# Instala o Composer e as dependências
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs || true

EXPOSE 80
