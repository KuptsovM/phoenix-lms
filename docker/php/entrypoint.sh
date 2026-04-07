#!/usr/bin/env sh
set -e

cd /var/www/html

echo "[entrypoint] Preparing Laravel app..."

if [ ! -f ".env" ]; then
  echo "[entrypoint] .env not found, copying from .env.example"
  cp .env.example .env
fi

if [ ! -f "vendor/autoload.php" ]; then
  echo "[entrypoint] Installing PHP dependencies"
  composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
fi

if [ ! -f "node_modules/.package-lock.json" ] && [ ! -d "node_modules" ]; then
  echo "[entrypoint] Installing Node dependencies"
  npm install --no-audit --no-fund
fi

if [ ! -f "public/build/manifest.json" ]; then
  echo "[entrypoint] Frontend assets not found, building with Vite"
  npm run build
fi

if ! grep -q "^APP_KEY=base64:" .env; then
  echo "[entrypoint] Generating APP_KEY"
  php artisan key:generate --force
fi

echo "[entrypoint] Waiting for DB connection..."
max_retries=30
retry=1
until php -r "new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); echo 'ok';" >/dev/null 2>&1; do
  if [ "$retry" -ge "$max_retries" ]; then
    echo "[entrypoint] Database is not ready after ${max_retries} attempts"
    exit 1
  fi
  echo "[entrypoint] DB not ready yet (${retry}/${max_retries}), retrying..."
  retry=$((retry + 1))
  sleep 2
done

echo "[entrypoint] Running migrations and seeders..."
php artisan migrate --force
php artisan db:seed --force

echo "[entrypoint] Caching config and routes..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache || true

echo "[entrypoint] Starting php-fpm..."
exec "$@"
