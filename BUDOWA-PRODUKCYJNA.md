# Instrukcja budowy wersji produkcyjnej

## ✅ Wykonane kroki:

1. ✅ **Zainstalowano zależności npm** - `npm install`
2. ✅ **Zbudowano assets frontend** - `npm run build`

## ⚠️ Wymagane kroki (wymagają Composer):

### Instalacja Composer (jeśli nie jest zainstalowany):

**Windows:**
1. Pobierz Composer z: https://getcomposer.org/download/
2. Uruchom instalator Composer-Setup.exe
3. Alternatywnie, pobierz `composer.phar` i umieść w katalogu projektu

**Lub użyj lokalnego composer.phar:**
```powershell
php composer.phar install --no-dev --optimize-autoloader
```

### Pozostałe kroki budowy produkcyjnej:

Po zainstalowaniu Composer, wykonaj następujące komendy:

```powershell
# 1. Instalacja zależności Composer (produkcja)
composer install --no-dev --optimize-autoloader

# 2. Generowanie klucza aplikacji
php artisan key:generate --force

# 3. Utworzenie linku symbolicznego do storage
php artisan storage:link

# 4. Uruchomienie migracji
php artisan migrate --force

# 5. Optymalizacja Laravel (cache)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Alternatywnie - użyj skryptu:

Jeśli masz zainstalowany Composer, możesz użyć gotowego skryptu:

**Windows PowerShell:**
```powershell
.\build-production.ps1
```

**Linux/Mac:**
```bash
chmod +x build-production.sh
./build-production.sh
```

## Status budowy:

- ✅ Assets frontend zbudowane (CSS i JS są gotowe)
- ⏳ Oczekiwanie na instalację Composer dla pozostałych kroków

## Po zakończeniu budowy:

Uruchom serwer deweloperski:
```bash
php artisan serve
```

Aplikacja będzie dostępna pod adresem: `http://localhost:8000`

## Uwagi:

- Assets frontend są już zbudowane i gotowe do użycia
- Aby aplikacja działała w pełni, potrzebujesz zainstalować zależności PHP przez Composer
- Baza danych musi być skonfigurowana (już jest w `.env`)


