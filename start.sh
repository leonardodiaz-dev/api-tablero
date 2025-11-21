#!/bin/bash
# Arrancar PHP-FPM
php-fpm &

# Arrancar Nginx en primer plano
nginx -g "daemon off;"
