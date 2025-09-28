# Use PHP 8.2 with Apache as base image
FROM php:8.2-apache

# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache modules
RUN a2enmod rewrite env
RUN a2enmod headers

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
# Create required directories\n\
if [ ! -d "/var/www/html/uploads/credit_cards" ]; then\n\
    mkdir -p /var/www/html/uploads/credit_cards\n\
fi\n\
if [ ! -d "/var/www/html/uploads/offers" ]; then\n\
    mkdir -p /var/www/html/uploads/offers\n\
fi\n\
\n\
# Set permissions\n\
chown -R www-data:www-data /var/www/html/uploads\n\
chmod -R 775 /var/www/html/uploads\n\
\n\
# Update Apache ports.conf with environment variable\n\
echo "Listen ${PORT:-3003}" > /etc/apache2/ports.conf\n\
\n\
# Start Apache\n\
apache2-foreground' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Configure Apache to use environment variables
RUN { \
    echo '# Load environment variables'; \
    echo 'SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1'; \
    echo ''; \
    echo 'Listen ${PORT:-3003}'; \
    echo '<VirtualHost *:${PORT:-3003}>'; \
    echo '    ServerAdmin ${SERVER_ADMIN:-webmaster@localhost}'; \
    echo '    ServerName ${SERVER_NAME:-localhost}'; \
    echo '    DocumentRoot /var/www/html'; \
    echo '    DirectoryIndex index.php index.html'; \
    echo '    <Directory /var/www/html>'; \
    echo '        Options Indexes FollowSymLinks'; \
    echo '        AllowOverride All'; \
    echo '        Require all granted'; \
    echo '        # Pass environment variables to PHP'; \
    echo '        SetEnv APP_ENV ${APP_ENV}'; \
    echo '        SetEnv APP_URL ${APP_URL}'; \
    echo '        SetEnv DB_HOST ${DB_HOST}'; \
    echo '        SetEnv DB_NAME ${DB_NAME}'; \
    echo '        SetEnv DB_USER ${DB_USER}'; \
    echo '        SetEnv DB_PASSWORD ${DB_PASSWORD}'; \
    echo '    </Directory>'; \
    echo '    ErrorLog ${APACHE_LOG_DIR}/error.log'; \
    echo '    CustomLog ${APACHE_LOG_DIR}/access.log combined'; \
    echo '</VirtualHost>'; \
} > /etc/apache2/sites-available/000-default.conf

# Expose port 3003
EXPOSE 3003

# Set the entrypoint to our verification script
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]