#!/bin/sh
set -e

# Injecter le PORT (fourni par Render) dans la config nginx
sed -i "s/APP_PORT/${PORT:-8080}/" /etc/nginx/nginx.conf

# Laravel bootstrap
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/app.conf
