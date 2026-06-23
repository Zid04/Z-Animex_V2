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

# .env temporaire pour les commandes artisan
RUN cp .env.example .env && php artisan key:generate

# Pré-générer les types wayfinder avec PHP (évite que Vite appelle artisan)
RUN php artisan wayfinder:generate --with-form

# Build Vite avec le plugin wayfinder désactivé (types déjà générés ci-dessus)
RUN npm ci && SKIP_WAYFINDER=1 npm run build

# ── Stage 2 : Production image ───────────────────────────────────────────────
FROM serversideup/php:8.4-fpm-nginx
USER root

WORKDIR /var/www/html

COPY --from=builder /var/www/html/vendor ./vendor
COPY --from=builder /var/www/html/public/build ./public/build
COPY --from=builder /var/www/html/resources/js/wayfinder ./resources/js/wayfinder
COPY . .

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

CMD ["/start.sh"]
