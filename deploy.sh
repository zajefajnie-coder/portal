#!/bin/bash
# Skrypt wdrożenia na serwer Debian - Portal Modelingowy
# Użycie: ./deploy.sh [user@]83.230.44.103

set -e

SERVER="${1:-root@83.230.44.103}"
APP_DIR="/var/www/portal-modelingowy"
APP_USER="www-data"

echo "=== Wdrożenie Portal Modelingowy na serwer Debian ==="
echo "Serwer: $SERVER"
echo "Katalog aplikacji: $APP_DIR"
echo ""

# Krok 1: Przesłanie plików na serwer
echo "1. Przesyłanie plików na serwer..."
rsync -avz --exclude 'node_modules' --exclude 'vendor' --exclude '.git' \
    --exclude 'storage/logs' --exclude 'storage/framework/cache' \
    --exclude 'storage/framework/sessions' --exclude 'storage/framework/views' \
    --exclude '.env' \
    ./ $SERVER:$APP_DIR/

# Krok 2: Instalacja zależności i konfiguracja na serwerze
echo ""
echo "2. Instalacja zależności i konfiguracja na serwerze..."
ssh $SERVER << 'ENDSSH'
set -e
cd /var/www/portal-modelingowy

# Instalacja zależności Composer (jeśli nie są zainstalowane)
if [ ! -d "vendor" ]; then
    echo "Instalacja zależności Composer..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Instalacja zależności npm (jeśli nie są zainstalowane)
if [ ! -d "node_modules" ]; then
    echo "Instalacja zależności npm..."
    npm install --production
fi

# Budowa assets frontend
echo "Budowa assets frontend..."
npm run build

# Generowanie klucza aplikacji (jeśli nie istnieje)
if [ ! -f ".env" ]; then
    echo "Tworzenie pliku .env..."
    cp .env.example .env
    php artisan key:generate --force
fi

# Utworzenie linku symbolicznego do storage
if [ ! -L "public/storage" ]; then
    echo "Tworzenie linku symbolicznego do storage..."
    php artisan storage:link
fi

# Uruchomienie migracji
echo "Uruchomienie migracji..."
php artisan migrate --force

# Optymalizacja Laravel
echo "Optymalizacja Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ustawienie uprawnień
echo "Ustawianie uprawnień..."
chown -R www-data:www-data /var/www/portal-modelingowy
chmod -R 755 /var/www/portal-modelingowy
chmod -R 775 /var/www/portal-modelingowy/storage
chmod -R 775 /var/www/portal-modelingowy/bootstrap/cache

echo "Wdrożenie zakończone pomyślnie!"
ENDSSH

echo ""
echo "=== Wdrożenie zakończone! ==="
echo "Aplikacja dostępna pod adresem: http://83.230.44.103"

