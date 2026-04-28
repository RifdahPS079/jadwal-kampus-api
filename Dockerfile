FROM php:8.2-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip libpng-dev \
    && docker-php-ext-install zip pdo pdo_mysql gd

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Clear cache (aman kalau gagal)
RUN php artisan config:clear || true
RUN php artisan cache:clear || true
# Jalankan server (INI YANG BENAR)
CMD php -S 0.0.0.0:$PORT -t public

