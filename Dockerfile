# Dockerfile for KamateRaho application
# This ensures proper permissions for file uploads in containerized environments

FROM php:8.1-apache

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Install additional tools
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Set proper permissions for all upload directories
RUN mkdir -p /var/www/html/uploads /var/www/html/uploads/credit_cards /var/www/html/uploads/offers \
    && chmod -R 775 /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/uploads

# Make sure the web server can write to the application directory
RUN chown -R www-data:www-data /var/www/html

# Expose port
EXPOSE 80

# Use the default Apache command
CMD ["apache2-foreground"]