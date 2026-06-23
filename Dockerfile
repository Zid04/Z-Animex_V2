# ── Stage 1 : Composer (prod deps only) ─────────────────────────────────────
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

# ── Stage 2 : Build Vite assets ──────────────────────────────────────────────
# PHP requis ici car wayfinder appelle "php artisan" pendant vite build
FROM node:22-alpine AS assets
RUN apk add --no-cache \
    php83 \
    php83-phar \
    php83-mbstring \
    php83-xml \
    php83-dom \
    php83-tokenizer \
    php83-simplexml \
    php83-xmlwriter \
    php83-ctype \
    php83-json \
    php83-openssl
RUN ln -s /usr/bin/php83 /usr/bin/php

WORKDIR /app
COPY --from=vendor /app ./
RUN cp .env.example .env && php artisan key:generate
RUN npm ci
RUN npm run build

# ── Stage 3 : Production image ───────────────────────────────────────────────
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
