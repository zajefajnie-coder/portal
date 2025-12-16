# Portal Modelingowy

Aplikacja społecznościowa dla twórców mody i fotografii wzorowana na arteportia.pl.

## Wymagania

- PHP 8.2+
- MySQL
- Composer
- Node.js & npm

## Instalacja

1. Zainstaluj zależności Composer:
```bash
composer install
```

2. Zainstaluj zależności npm:
```bash
npm install
```

3. Skonfiguruj plik `.env` (już skonfigurowany z danymi bazy danych)

4. Wygeneruj klucz aplikacji:
```bash
php artisan key:generate
```

5. Utwórz link symboliczny do storage:
```bash
php artisan storage:link
```

6. Uruchom migracje:
```bash
php artisan migrate
```

7. Zbuduj assets frontend:
```bash
npm run build
```

Lub w trybie deweloperskim:
```bash
npm run dev
```

## Struktura bazy danych

Aplikacja używa następujących tabel:
- `users` - użytkownicy z dodatkowymi polami (username, bio, location, avatar)
- `roles` - role (photographer, model, makeup_artist, stylist, hairdresser, retoucher)
- `role_user` - relacja many-to-many między użytkownikami a rolami
- `portfolios` - portfolio użytkowników
- `castings` - castingi

## Funkcjonalności

- Rejestracja z wyborem ról
- System portfolio z uploadem zdjęć
- System castingów z filtrowaniem
- Profil użytkownika z możliwością edycji
- Wyszukiwanie portfolio i castingów

## Technologie

- Laravel 11
- Livewire 3
- Alpine.js
- Tailwind CSS
- MySQL


