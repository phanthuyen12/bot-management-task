#!/bin/bash

set -e

echo "Waiting for database to be ready..."
while ! nc -z ${DB_HOST:-db} ${DB_PORT:-3306}; do
  sleep 1
done

echo "Database is ready!"

# Generate APP_KEY if not exists
if [ -z "$APP_KEY" ]; then
  echo "Generating APP_KEY..."
  php artisan key:generate --force
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Run seeders
echo "Running seeders..."
php artisan db:seed --force

# Clear cache
echo "Clearing cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Application is ready!"

exec "$@"
