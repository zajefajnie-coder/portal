#!/bin/bash
# Skrypt wdrożenia z obsługą hasła SSH
# Wymaga: sshpass (apt install sshpass lub brew install sshpass)

set -e

SERVER="michal@83.230.44.103"
PASSWORD="Alicja2025##"
APP_DIR="/var/www/portal-modelingowy"

echo "=== Wdrożenie Portal Modelingowy na serwer Debian ==="
echo "Serwer: $SERVER"
echo "Katalog aplikacji: $APP_DIR"
echo ""

# Sprawdzenie czy sshpass jest zainstalowany
if ! command -v sshpass &> /dev/null; then
    echo "BŁĄD: sshpass nie jest zainstalowany!"
    echo "Zainstaluj: apt install sshpass (Linux) lub brew install sshpass (Mac)"
    exit 1
fi

# Funkcja do wykonywania komend przez SSH z hasłem
ssh_cmd() {
    sshpass -p "$PASSWORD" ssh -o StrictHostKeyChecking=no "$SERVER" "$@"
}

# Funkcja do kopiowania plików przez SCP z hasłem
scp_cmd() {
    sshpass -p "$PASSWORD" scp -o StrictHostKeyChecking=no -r "$@"
}

# Krok 1: Przesłanie plików na serwer
echo "1. Przesyłanie plików na serwer..."
rsync -avz --exclude 'node_modules' --exclude 'vendor' --exclude '.git' \
    --exclude 'storage/logs' --exclude 'storage/framework/cache' \
    --exclude 'storage/framework/sessions' --exclude 'storage/framework/views' \
    --exclude '.env' \
    -e "sshpass -p '$PASSWORD' ssh -o StrictHostKeyChecking=no" \
    ./ "$SERVER:$APP_DIR/"

# Krok 2: Instalacja zależności i konfiguracja na serwerze
echo ""
echo "2. Instalacja zależności i konfiguracja na serwerze..."
ssh_cmd "bash -s" << 'ENDSSH'
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
sudo chown -R www-data:www-data /var/www/portal-modelingowy
sudo chmod -R 755 /var/www/portal-modelingowy
sudo chmod -R 775 /var/www/portal-modelingowy/storage
sudo chmod -R 775 /var/www/portal-modelingowy/bootstrap/cache

echo "Wdrożenie zakończone pomyślnie!"
ENDSSH

echo ""
echo "=== Wdrożenie zakończone! ==="
echo "Aplikacja dostępna pod adresem: http://83.230.44.103"

