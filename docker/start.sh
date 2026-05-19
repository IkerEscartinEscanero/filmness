#!/usr/bin/env sh
set -e

cd /var/www/html

mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
touch "${DB_DATABASE:-/var/www/html/storage/database.sqlite}"

php artisan storage:link --force || true
php artisan migrate --force

# Only seed if the database is empty to avoid duplicates on redeployments
FILM_COUNT=$(php artisan tinker --no-interaction --execute="echo \App\Models\Film::count();" 2>/dev/null | tail -1)
if [ "$FILM_COUNT" = "0" ] || [ -z "$FILM_COUNT" ]; then
    php artisan db:seed --force
fi

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"