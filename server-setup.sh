#!/bin/bash
# Skrypt konfiguracji serwera - wykonaj na serwerze

set -e

APP_DIR="/home/michal/portal-modelingowy"
cd $APP_DIR

echo "=== Konfiguracja aplikacji ==="

# Konfiguracja .env
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=krzyszton_port1/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=krzyszton_port1/' .env
sed -i 's|DB_PASSWORD=.*|DB_PASSWORD=Alicja2025##|' .env

# Generowanie klucza
php artisan key:generate --force

# Instalacja zależności Composer
php composer.phar install --no-dev --optimize-autoloader --no-interaction

# Instalacja npm (jeśli Node.js jest zainstalowany)
if command -v node &> /dev/null; then
    npm install
    npm run build
else
    echo "UWAGA: Node.js nie jest zainstalowany. Pomiń budowę assets."
fi

# Link symboliczny
php artisan storage:link

# Migracje
php artisan migrate --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Konfiguracja zakończona!"

