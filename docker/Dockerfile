FROM php:8.1-fpm AS php8


# Cài các extension cần thiết
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    curl \
    && docker-php-ext-install pdo pdo_mysql zip


# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


# Đặt thư mục làm việc
WORKDIR /var/www/html


# Set quyền cho thư mục
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
