# Stage 1: Build dependencies
FROM php:8.2-fpm-alpine AS build

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    postgresql-dev \
    && docker-php-ext-install pdo_pgsql pdo_mysql bcmath gd xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy project files
COPY . .

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Stage 2: Final Runtime
FROM php:8.2-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    nginx \
    postgresql-libs \
    libpng \
    libxml2 \
    && docker-php-ext-install pdo_pgsql pdo_mysql bcmath gd xml

WORKDIR /var/www/html

# Copy from build stage
COPY --from=build /var/www/html /var/www/html

# Copy custom nginx config
COPY nginx.conf /etc/nginx/nginx.conf

# Setup permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
