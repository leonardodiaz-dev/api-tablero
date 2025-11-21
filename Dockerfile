# --- Imagen base PHP-FPM ---
FROM php:8.2-fpm

# --- Instalar dependencias del sistema y extensiones PHP ---
RUN apt-get update && apt-get install -y \
    nginx git curl zip unzip libzip-dev libonig-dev libpng-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql zip bcmath gd

# --- Instalar Composer ---
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# --- Configurar directorio de trabajo ---
WORKDIR /var/www/html

# --- Copiar el código ---
COPY . .

# --- Instalar dependencias de PHP ---
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# --- Permisos correctos para Laravel ---
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# --- Configurar Nginx ---
COPY default.conf /etc/nginx/conf.d/default.conf

# --- Copiar start script ---
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# --- Exponer puerto 80 ---
EXPOSE 80

# --- Comando principal ---
CMD ["start.sh"]
