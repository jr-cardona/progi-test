FROM php:8.2-fpm

WORKDIR /var/www/progi-test

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    libonig-dev \
    libzip-dev \
    unzip \
    zip \
    p7zip-full

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Allow Composer plugins to run as root/super user
ENV COMPOSER_ALLOW_SUPERUSER 1

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-interaction

# Expose port 9000 and start PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]