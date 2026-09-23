# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Etapa 1: build dos assets (Tailwind CSS + JS) com Vite
# ---------------------------------------------------------------------------
FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY vite.config.js ./
COPY public ./public

RUN npm run build

# ---------------------------------------------------------------------------
# Etapa 2: dependências PHP (Composer)
# ---------------------------------------------------------------------------
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader

COPY . .
RUN composer dump-autoload --optimize --no-dev

# ---------------------------------------------------------------------------
# Etapa 3: imagem final — PHP-FPM + Nginx + Supervisor num único container
# (imagem mantida pela serversideup, feita para rodar Laravel em produção)
# ---------------------------------------------------------------------------
FROM serversideup/php:8.3-fpm-nginx AS app

ENV PHP_OPCACHE_ENABLE="1" \
    AUTORUN_ENABLED="true" \
    AUTORUN_LARAVEL_MIGRATION="true" \
    AUTORUN_LARAVEL_MIGRATION_FORCE="true" \
    AUTORUN_LARAVEL_STORAGE_LINK="true" \
    AUTORUN_LARAVEL_CONFIG_CACHE="true" \
    AUTORUN_LARAVEL_ROUTE_CACHE="true" \
    AUTORUN_LARAVEL_VIEW_CACHE="true"

# Esta imagem já escuta em 0.0.0.0:8080 (http). No EasyPanel, configure o
# domínio da aplicação apontando para a porta 8080 do contêiner.

USER root

COPY --chown=www-data:www-data . /var/www/html
COPY --from=vendor --chown=www-data:www-data /app/vendor /var/www/html/vendor
COPY --from=assets --chown=www-data:www-data /app/public/build /var/www/html/public/build

USER www-data
