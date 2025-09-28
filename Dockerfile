# Use PHP 8.2 with Apache as base image
FROM php:8.2-apache

# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Create uploads directory and set permissions for Coolify
RUN mkdir -p /var/www/html/uploads/credit_cards /var/www/html/uploads/offers \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/uploads

# Configure PHP upload settings
RUN { \
    echo 'upload_max_filesize = 64M'; \
    echo 'post_max_size = 64M'; \
    echo 'memory_limit = 128M'; \
    echo 'max_execution_time = 300'; \
} > /usr/local/etc/php/conf.d/uploads.ini

# Create a verification script for Coolify deployment
RUN echo '#!/bin/bash\n\
if [ ! -d "/var/www/html/uploads/credit_cards" ]; then\n\
    mkdir -p /var/www/html/uploads/credit_cards\n\
fi\n\
if [ ! -d "/var/www/html/uploads/offers" ]; then\n\
    mkdir -p /var/www/html/uploads/offers\n\
fi\n\
chown -R www-data:www-data /var/www/html/uploads\n\
chmod -R 775 /var/www/html/uploads\n\
apache2-foreground' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Configure Apache for port 3003
RUN { \
    echo 'Listen 3003'; \
    echo '<VirtualHost *:3003>'; \
    echo '    ServerAdmin webmaster@localhost'; \
    echo '    DocumentRoot /var/www/html'; \
    echo '    DirectoryIndex index.php index.html'; \
    echo '    <Directory /var/www/html>'; \
    echo '        Options Indexes FollowSymLinks'; \
    echo '        AllowOverride All'; \
    echo '        Require all granted'; \
    echo '    </Directory>'; \
    echo '    ErrorLog ${APACHE_LOG_DIR}/error.log'; \
    echo '    CustomLog ${APACHE_LOG_DIR}/access.log combined'; \
    echo '</VirtualHost>'; \
} > /etc/apache2/sites-available/000-default.conf \
    && echo 'Listen 3003' > /etc/apache2/ports.conf

# Expose port 3003
EXPOSE 3003

# Set the entrypoint to our verification script
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]