#!/bin/sh
set -e

cd /var/www/html

# Install PHP dependencies if vendor/ is missing
if [ ! -d "vendor" ]; then
  echo "[entrypoint] Installing Composer dependencies..."
  composer install --no-interaction --no-progress --prefer-dist
else
  echo "[entrypoint] vendor/ exists. Skipping composer install."
fi

# Ensure correct permissions for Apache to write cache/logs if needed
chown -R www-data:www-data /var/www/html || true

# Start Apache in foreground
exec "$@"
