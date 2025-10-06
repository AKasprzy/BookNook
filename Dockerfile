FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git curl unzip libpq-dev libpng-dev libjpeg-dev libfreetype6-dev libzip-dev zip \
    libonig-dev libxml2-dev libicu-dev ca-certificates \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip intl xml

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN usermod -u 1000 www-data || true

COPY composer.json composer.lock* /var/www/html/
RUN composer install --no-interaction --prefer-dist --no-progress || true

COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 9000
CMD ["php-fpm"]
