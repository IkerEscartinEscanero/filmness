#!/usr/bin/env sh
set -e

cd /var/www/html

mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
touch "${DB_DATABASE:-/var/www/html/storage/database.sqlite}"

php artisan migrate --force

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"