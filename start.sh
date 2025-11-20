#!/bin/sh

echo "➜ Arrancando PHP-FPM..."
php-fpm --nodaemonize &

echo "➜ Arrancando Nginx..."
nginx -g "daemon off;"
