FROM php:8.2-fpm

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    nginx git curl zip unzip libzip-dev libonig-dev libpng-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip bcmath

# Instalar composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos con cache inteligente
COPY composer.json composer.lock ./

RUN composer install --no-interaction --no-scripts --prefer-dist

# Copiar el resto del proyecto
COPY . .

RUN composer install --no-interaction --prefer-dist

# Configurar permisos
RUN chown -R www-data:www-data storage bootstrap/cache

# Copiar configuración de Nginx
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# Exponer puerto
EXPOSE 80

# Script de inicio: inicia Nginx + PHP-FPM
CMD service nginx start && php-fpm
