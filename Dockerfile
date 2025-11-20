FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    nginx git curl zip unzip libzip-dev libonig-dev libpng-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip bcmath

# Instalar Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar archivos composer primero para cache
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-scripts --prefer-dist || true

# Copiar el resto del código
COPY . .

# Instalar dependencias restantes
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Permisos necesarios
RUN chown -R www-data:www-data storage bootstrap/cache

# Copiar config de Nginx
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copiar script de inicio
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Exponer puerto
EXPOSE 80

# Comando principal
CMD ["start.sh"]
