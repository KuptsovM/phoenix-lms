FROM php:8.4-fpm AS base

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копирование проекта
COPY ./backend .

RUN composer install --optimize-autoloader --no-dev

# Для разработки добавьте Node.js и npm run dev