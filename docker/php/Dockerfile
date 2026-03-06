# =========================
# Stage 1: Composer build
# =========================
FROM composer:2 AS composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader


# =========================
# Stage 2: Node build
# =========================
FROM node:20 AS node

WORKDIR /app

COPY package.json package-lock.json ./

RUN npm install

COPY resources resources
COPY vite.config.js vite.config.js
COPY public public

RUN npm run build


# =========================
# Stage 3: Production PHP
# =========================
FROM php:8.2-fpm-alpine

WORKDIR /var/www

# install extensions
RUN apk add --no-cache \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-install pdo pdo_mysql zip

# copy project
COPY . .

# copy composer dependencies
COPY --from=composer /app/vendor /var/www/vendor

# copy built assets
COPY --from=node /app/public/build /var/www/public/build

# permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

USER www-data

EXPOSE 9000

CMD ["php-fpm"]