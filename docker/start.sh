#!/bin/sh
set -e

# Injecter le PORT (fourni par Render) dans la config nginx
sed -i "s/APP_PORT/${PORT:-8080}/" /etc/nginx/nginx.conf

# Migrations de l'application
php artisan migrate --force

# Migration Telescope uniquement si activé
if [ "${TELESCOPE_ENABLED:-false}" = "true" ]; then
    php artisan migrate --force --path=database/migrations/telescope
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/app.conf
