#!/usr/bin/env sh
set -e

cd /var/www/html

mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
touch "${DB_DATABASE:-/var/www/html/storage/database.sqlite}"

# Copy seed media to persistent storage if missing
mkdir -p storage/app/public/posters storage/app/public/logos storage/app/public/profiles
if [ -d public/seed-media/posters ]; then
    cp -n public/seed-media/posters/* storage/app/public/posters/ 2>/dev/null || true
fi
if [ -d public/seed-media/logos ]; then
    cp -n public/seed-media/logos/* storage/app/public/logos/ 2>/dev/null || true
fi

php artisan storage:link --force || true
php artisan migrate --force

# Seed once per persistent volume to avoid duplicates on redeployments
if [ ! -f storage/.seeded ]; then
    php artisan db:seed --force
    touch storage/.seeded
fi

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"