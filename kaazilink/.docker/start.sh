#!/bin/sh
# ─────────────────────────────────────────────────────────────────────────────
# Kaazilink container startup script
# Runs once per container start, before nginx + php-fpm are launched.
# ─────────────────────────────────────────────────────────────────────────────
set -e

# Render injects PORT; default to 8080 if not set (local Docker testing)
export PORT="${PORT:-8080}"

echo "→ [1/5] Substituting PORT=${PORT} into nginx config..."
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

echo "→ [2/5] Clearing stale caches (config resolved at runtime from env vars)..."
# We do NOT run config:cache here because on Render the env vars are injected
# BEFORE the container starts, so caching is fine — BUT the Docker build layer
# may have cached a stale bootstrap/cache/config.php from a previous image.
# Clearing it forces a fresh resolution every boot, which is safe on free tier.
php artisan config:clear || true
php artisan route:clear  || true
php artisan view:clear   || true
# Now re-cache with the live env vars Render has injected into this container.
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "→ [3/5] Running database migrations..."
php artisan migrate --force

if [ "$RUN_SEEDERS" = "true" ]; then
    echo "Running seeders..."
    php artisan db:seed --class=ServiceAreaSeeder --no-interaction
    php artisan db:seed --class=ProviderSeeder --no-interaction
    php artisan db:seed --class=ReviewSeeder --no-interaction
    echo "Seeders complete."
fi

echo "→ [4/5] Creating storage symlink and ensuring writable directories..."
php artisan storage:link || true
# Ensure Livewire temp upload dir exists + is writable by www-data (php-fpm user).
# This MUST be local disk — R2/S3 streams cannot be opened as PHP resource handles.
# The directory is under the 'local' disk root: storage/app/private/livewire-tmp
mkdir -p /var/www/html/storage/app/private/livewire-tmp
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

echo "→ [5/5] Starting supervisor (nginx + php-fpm)..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
