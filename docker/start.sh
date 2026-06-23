#!/bin/sh
set -e

# serversideup/php lit NGINX_HTTP_PORT pour configurer nginx
# Render fournit PORT dynamiquement
export NGINX_HTTP_PORT=${PORT:-8080}

# Laravel bootstrap
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# serversideup gère nginx + php-fpm via S6 overlay
exec /init
