FROM php:8.2-apache

# instala dependências
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql

# copia o código
COPY . /var/www/html/

WORKDIR /var/www/html/

# instala dependências PHP
RUN curl -sS https://getcomposer.org/installer | php && \
    php composer.phar install --no-interaction --optimize-autoloader

EXPOSE 8080
CMD ["apache2-foreground"]
