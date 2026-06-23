#!/bin/sh
set -e

# Render fournit PORT, serversideup/php utilise NGINX_HTTP_PORT
export NGINX_HTTP_PORT=${PORT:-8080}

# Laravel bootstrap
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Démarre PHP-FPM + Nginx via l'entrypoint serversideup
exec /init
