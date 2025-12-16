# Instrukcja instalacji - Portal Modelingowy

## Krok 1: Instalacja zależności

### Composer
```bash
composer install
```

### npm
```bash
npm install
```

## Krok 2: Konfiguracja środowiska

Plik `.env` jest już skonfigurowany z danymi bazy danych:
- Baza: `krzyszton_port1`
- Host: `localhost`
- User: `krzyszton_port1`
- Hasło: `Alicja2025##`

## Krok 3: Generowanie klucza aplikacji

```bash
php artisan key:generate
```

## Krok 4: Utworzenie linku symbolicznego do storage

```bash
php artisan storage:link
```

To utworzy link z `public/storage` do `storage/app/public`, co umożliwi dostęp do uploadowanych plików.

## Krok 5: Uruchomienie migracji

```bash
php artisan migrate
```

To utworzy wszystkie tabele w bazie danych:
- users (z dodatkowymi polami: username, bio, location, avatar)
- roles (photographer, model, makeup_artist, stylist, hairdresser, retoucher)
- role_user (relacja many-to-many)
- portfolios
- castings

## Krok 6: Budowa assets frontend

### Tryb produkcyjny:
```bash
npm run build
```

### Tryb deweloperski (z hot reload):
```bash
npm run dev
```

## Krok 7: Uruchomienie serwera deweloperskiego

```bash
php artisan serve
```

Aplikacja będzie dostępna pod adresem: `http://localhost:8000`

## Struktura katalogów dla uploadów

Aplikacja automatycznie utworzy potrzebne katalogi przy pierwszym uploadzie:
- `storage/app/public/uploads/portfolios/` - zdjęcia portfolio
- `storage/app/public/uploads/avatars/` - avatary użytkowników

## Funkcjonalności

### Rejestracja
- Rejestracja z wyborem jednej lub wielu ról
- Role: fotograf, model/modelka, wizażysta, stylista, fryzjer, retuszer

### Portfolio
- Dodawanie portfolio ze zdjęciami
- Przeglądanie portfolio innych użytkowników
- Wyszukiwanie portfolio
- Edycja i usuwanie własnych portfolio

### Castingi
- Tworzenie castingów z wymaganymi rolami
- Filtrowanie castingów po statusie i roli
- Wyszukiwanie castingów
- Edycja i zarządzanie własnymi castingami

### Profil użytkownika
- Edycja danych osobowych
- Dodawanie bio i lokalizacji
- Zmiana hasła

## Uwagi techniczne

- PHP 8.2+ (sprawdź kompatybilność z PHP 8.4)
- Laravel 11
- Livewire 3
- Alpine.js
- Tailwind CSS
- MySQL

## Rozwiązywanie problemów

### Błąd "Class 'DB' not found" w migracji
Upewnij się, że w migracji `2024_01_01_000002_create_roles_table.php` jest import:
```php
use Illuminate\Support\Facades\DB;
```

### Błąd "Storage link already exists"
Usuń istniejący link i utwórz ponownie:
```bash
rm public/storage
php artisan storage:link
```

### Błąd "Vite manifest not found"
Uruchom build frontend:
```bash
npm run build
```


