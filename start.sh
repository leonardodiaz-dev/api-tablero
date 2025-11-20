#!/bin/sh

# Iniciar PHP-FPM
php-fpm &

# Iniciar Nginx en primer plano (requerido por Render)
nginx -g "daemon off;"
