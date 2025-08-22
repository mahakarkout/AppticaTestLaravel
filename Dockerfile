FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    zip unzip curl git libzip-dev mariadb-client \
    && docker-php-ext-install pdo_mysql zip

# Set working directory
WORKDIR /var/www

# Install Composer manually (since no multistage used)
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Copy Laravel app
COPY . /var/www

# Install PHP dependencies
RUN composer install --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

EXPOSE 9000
