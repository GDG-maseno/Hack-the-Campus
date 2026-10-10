# ─────────────────────────────────────────────────────────────────────────────
# Kaazilink — Laravel on PHP 8.4 (Alpine) with Nginx + PHP-FPM
# Designed for Render.com free tier
# ─────────────────────────────────────────────────────────────────────────────
FROM php:8.4-fpm-alpine

# ── System packages ──────────────────────────────────────────────────────────
RUN apk add --no-cache \
    nginx \
    supervisor \
    nodejs \
    npm \
    curl \
    zip \
    unzip \
    git \
    gettext \
    # PostgreSQL (pdo_pgsql)
    postgresql-dev \
    # GD (image processing for KYC uploads)
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    # Zip extension
    libzip-dev \
    # Intl
    icu-dev \
    # XML / DOM
    libxml2-dev \
    # mbstring
    oniguruma-dev

# ── PHP extensions ───────────────────────────────────────────────────────────
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        gd \
        zip \
        bcmath \
        intl \
        exif \
        pcntl

# ── Composer ─────────────────────────────────────────────────────────────────
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# ── Custom PHP config ─────────────────────────────────────────────────────────
# Sets upload_tmp_dir and sys_temp_dir to /tmp (always writable in Linux containers).
# Fixes: stream_get_meta_data(tmpfile()) returning false when tmpfile() fails.
COPY .docker/php.ini /usr/local/etc/php/conf.d/99-kaazilink.ini
# Ensure /tmp is writable by www-data (PHP-FPM user)
RUN chmod 1777 /tmp

WORKDIR /var/www/html

# ── PHP dependencies (cached layer — only re-runs if lock file changes) ──────
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --optimize-autoloader

# ── Node / Vite build (cached layer) ─────────────────────────────────────────
COPY package.json package-lock.json vite.config.js ./
COPY resources/ resources/
ARG VITE_APP_NAME=Kaazilink
ENV VITE_APP_NAME=$VITE_APP_NAME
RUN npm ci --ignore-scripts && npm run build

# ── Full application source ───────────────────────────────────────────────────
COPY . .

# ── Finish composer setup ─────────────────────────────────────────────────────
RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi || true

# ── Storage & cache directory structure ──────────────────────────────────────
RUN mkdir -p \
        storage/logs \
        storage/framework/sessions \
        storage/framework/cache/data \
        storage/framework/views \
        storage/app/public \
        storage/app/private/livewire-tmp \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && php artisan config:clear || true

# ── Nginx config (template — PORT is substituted at runtime by start.sh) ─────
COPY .docker/nginx.conf.template /etc/nginx/nginx.conf.template

# ── Supervisor config ─────────────────────────────────────────────────────────
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# ── Startup script ────────────────────────────────────────────────────────────
COPY .docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]
