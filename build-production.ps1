# Skrypt budowy wersji produkcyjnej - Portal Modelingowy

Write-Host "=== Budowa wersji produkcyjnej ===" -ForegroundColor Green

# Krok 1: Instalacja zależności Composer
Write-Host "`n1. Instalacja zależności Composer..." -ForegroundColor Yellow
if (Get-Command composer -ErrorAction SilentlyContinue) {
    composer install --no-dev --optimize-autoloader
} else {
    Write-Host "BŁĄD: Composer nie jest zainstalowany lub nie jest w PATH!" -ForegroundColor Red
    Write-Host "Zainstaluj Composer z: https://getcomposer.org/download/" -ForegroundColor Yellow
    exit 1
}

# Krok 2: Instalacja zależności npm
Write-Host "`n2. Instalacja zależności npm..." -ForegroundColor Yellow
npm install

# Krok 3: Generowanie klucza aplikacji
Write-Host "`n3. Generowanie klucza aplikacji..." -ForegroundColor Yellow
php artisan key:generate --force

# Krok 4: Utworzenie linku symbolicznego do storage
Write-Host "`n4. Utworzenie linku symbolicznego do storage..." -ForegroundColor Yellow
if (Test-Path "public\storage") {
    Remove-Item "public\storage" -Force -Recurse
}
php artisan storage:link

# Krok 5: Uruchomienie migracji
Write-Host "`n5. Uruchomienie migracji..." -ForegroundColor Yellow
php artisan migrate --force

# Krok 6: Budowa assets frontend (produkcja)
Write-Host "`n6. Budowa assets frontend (produkcja)..." -ForegroundColor Yellow
npm run build

# Krok 7: Optymalizacja Laravel
Write-Host "`n7. Optymalizacja Laravel..." -ForegroundColor Yellow
php artisan config:cache
php artisan route:cache
php artisan view:cache

Write-Host "`n=== Budowa zakończona pomyślnie! ===" -ForegroundColor Green
Write-Host "Uruchom serwer: php artisan serve" -ForegroundColor Cyan


