# Backend Dockerfile for mediastore (PHP + Apache)
FROM php:8.2-apache

# Install required PHP extensions (pdo_mysql, openssl, sodium, etc.) and utilities
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       libzip-dev libssl-dev libsodium-dev unzip git curl \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-install sodium \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Install Composer (for dependency management)
RUN curl -fsSL https://getcomposer.org/installer -o /tmp/composer-setup.php \
    && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm /tmp/composer-setup.php

# Configure Apache for a typical Slim public/ directory
WORKDIR /var/www/html

# Provide a default DocumentRoot to /var/www/html/public and enable .htaccess
RUN sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#g' /etc/apache2/sites-available/000-default.conf \
    && printf "<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n" >> /etc/apache2/apache2.conf

# Copy application (in compose we mount a volume, but copy ensures container works standalone too)
COPY . /var/www/html

# Add entrypoint to install Composer deps if needed, then start Apache
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]

EXPOSE 80

# Healthcheck script (simple index.php response)
HEALTHCHECK --interval=30s --timeout=5s --retries=5 CMD curl -fsS http://localhost/ || exit 1
