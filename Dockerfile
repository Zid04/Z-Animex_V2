# ── Stage 1 : Build (PHP 8.4 + Node 22) ─────────────────────────────────────
FROM serversideup/php:8.4-fpm-nginx AS builder
USER root

RUN apt-get update \
    && apt-get install -y curl \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --classmap-authoritative

RUN cp .env.example .env && php artisan key:generate
RUN php artisan wayfinder:generate --with-form
RUN npm ci && SKIP_WAYFINDER=1 npm run build

# ── Stage 2 : Production (php:8.4-fpm + nginx + supervisor) ─────────────────
FROM php:8.4-fpm-bookworm

RUN apt-get update \
    && apt-get install -y nginx supervisor libpq-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql opcache zip bcmath pcntl \
    && rm -rf /var/lib/apt/lists/*

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/app.conf
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

WORKDIR /var/www/html

COPY --from=builder /var/www/html/vendor ./vendor
COPY --from=builder /var/www/html/public/build ./public/build
COPY --from=builder /var/www/html/resources/js/wayfinder ./resources/js/wayfinder
COPY . .

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD ["/start.sh"]
