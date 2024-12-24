# Use the official PHP 8.x image with Apache
FROM php:8.2-apache

# Install additional extensions if needed. For MySQL, the mysqli or pdo_mysql extension is commonly used.
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite if required
RUN a2enmod rewrite

# Copy the application code into the container
COPY src/ /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Expose port 80 (Apache’s default port)
EXPOSE 80

# By default, the parent image automatically starts Apache
