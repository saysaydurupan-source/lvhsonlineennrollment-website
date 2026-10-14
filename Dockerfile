FROM php:8.3-apache

# Install system dependencies and PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache URL rewriting
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copy website source
COPY . /var/www/html

# Set permissions for uploaded files
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80
