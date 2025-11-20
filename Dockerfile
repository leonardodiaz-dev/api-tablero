FROM php:8.2-fpm

# Instalar extensiones y Nginx
RUN apt-get update && apt-get install -y \
    nginx git curl zip unzip libzip-dev libonig-dev libpng-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip bcmath

# Instalar composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar composer para cache
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-scripts --prefer-dist || true

# Copiar resto del código
COPY . .

RUN composer install --no-interaction --prefer-dist

# Permisos
RUN chown -R www-data:www-data storage bootstrap/cache

# Copiar configuración de Nginx
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# Copiar script de inicio
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["start.sh"]
