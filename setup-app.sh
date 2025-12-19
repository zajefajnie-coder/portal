#!/bin/bash
# Skrypt konfiguracji aplikacji na serwerze

set -e

APP_DIR="/var/www/portal-modelingowy"
cd $APP_DIR

echo "=== Konfiguracja aplikacji ==="

# Konfiguracja .env
cp .env.example .env
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=krzyszton_port1/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=krzyszton_port1/' .env
sed -i 's|DB_PASSWORD=.*|DB_PASSWORD=Alicja2025##|' .env

# Generowanie klucza
php artisan key:generate --force

# Instalacja zależności
composer install --no-dev --optimize-autoloader --no-interaction
npm install
npm run build

# Link symboliczny
php artisan storage:link

# Migracje
php artisan migrate --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Uprawnienia
chown -R www-data:www-data $APP_DIR
chmod -R 755 $APP_DIR
chmod -R 775 $APP_DIR/storage
chmod -R 775 $APP_DIR/bootstrap/cache

echo "Konfiguracja zakończona!"
