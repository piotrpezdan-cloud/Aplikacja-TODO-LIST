#!/usr/bin/env sh
set -e

cd /var/www

if [ ! -f .env ]; then
  cp .env.example .env
fi

echo "[entrypoint] Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "[entrypoint] Installing Node dependencies..."
npm install

echo "[entrypoint] Building frontend assets..."
npm run build

if ! grep -q '^APP_KEY=base64:' .env; then
  php artisan key:generate --force --no-interaction
fi

until php artisan migrate --force --seed --no-interaction; do
  echo "[entrypoint] Waiting for database..."
  sleep 3
done

echo "[entrypoint] Bootstrapping finished. Starting php-fpm..."
exec php-fpm
