#!/usr/bin/env sh
set -eu

# Ensure writable directories exist
mkdir -p /var/www/storage/framework/cache \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/storage/framework/testing || true

mkdir -p /var/www/storage/app/public \
         /var/www/storage/app/private || true

mkdir -p /var/www/bootstrap/cache || true

# Fix ownership and permissions for Laravel writable dirs
# Use 775 for dirs and 664 for files so group-writable works with bind mounts
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
find /var/www/storage -type d -exec chmod 775 {} + 2>/dev/null || true
find /var/www/storage -type f -exec chmod 664 {} + 2>/dev/null || true
find /var/www/bootstrap/cache -type d -exec chmod 775 {} + 2>/dev/null || true
find /var/www/bootstrap/cache -type f -exec chmod 664 {} + 2>/dev/null || true

# Optionally set a liberal umask to keep files group-writable
umask 0002

# If vendor is missing, attempt install to avoid runtime surprises (non-fatal)
if [ ! -d /var/www/vendor ] && [ -f /var/www/composer.json ]; then
  composer install --no-interaction --prefer-dist --no-progress || true
fi

# Warm up Laravel cache dirs (non-fatal if artisan missing)
if [ -f /var/www/artisan ]; then
  su-exec www-data php /var/www/artisan config:cache || true
  su-exec www-data php /var/www/artisan route:cache || true
fi

# run php-fpm process by default
if [ "$#" -eq 0 ]; then
  exec php-fpm
else
  # for horizon service
  exec "$@"
fi
