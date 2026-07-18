#!/bin/sh
set -e

echo "Running Laravel setup commands..."

php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Laravel setup complete."

exec /start.sh