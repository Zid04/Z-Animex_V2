# ── Stage 1 : Build Vite assets ─────────────────────────────────────────────
FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# ── Stage 2 : Composer (prod deps only) ─────────────────────────────────────
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --classmap-authoritative

# ── Stage 3 : Production image ───────────────────────────────────────────────
# serversideup/php inclut PHP 8.4 + Nginx + FPM + toutes les extensions Laravel
# pdo_pgsql, opcache, zip, bcmath, pcntl, redis... déjà compilées
FROM serversideup/php:8.4-fpm-nginx

WORKDIR /var/www/html

COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY --from=assets /app/public/build ./public/build

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD ["/start.sh"]
