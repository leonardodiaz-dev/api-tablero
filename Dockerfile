FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libonig-dev libpng-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql zip bcmath

# Instalar Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar todo el código y composer.json
COPY . .

# Instalar dependencias
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Permisos
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Exponer puerto
EXPOSE 80

# Copiar start script
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Comando principal
CMD ["start.sh"]
