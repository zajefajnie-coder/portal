#!/bin/bash
# Skrypt budowy wersji produkcyjnej - Portal Modelingowy

echo "=== Budowa wersji produkcyjnej ==="

# Krok 1: Instalacja zależności Composer
echo ""
echo "1. Instalacja zależności Composer..."
composer install --no-dev --optimize-autoloader

# Krok 2: Instalacja zależności npm
echo ""
echo "2. Instalacja zależności npm..."
npm install

# Krok 3: Generowanie klucza aplikacji
echo ""
echo "3. Generowanie klucza aplikacji..."
php artisan key:generate --force

# Krok 4: Utworzenie linku symbolicznego do storage
echo ""
echo "4. Utworzenie linku symbolicznego do storage..."
rm -f public/storage
php artisan storage:link

# Krok 5: Uruchomienie migracji
echo ""
echo "5. Uruchomienie migracji..."
php artisan migrate --force

# Krok 6: Budowa assets frontend (produkcja)
echo ""
echo "6. Budowa assets frontend (produkcja)..."
npm run build

# Krok 7: Optymalizacja Laravel
echo ""
echo "7. Optymalizacja Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "=== Budowa zakończona pomyślnie! ==="
echo "Uruchom serwer: php artisan serve"


