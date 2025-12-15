# Funkcjonalność portalu StageOne v1.1

## Opis ogólny
Portal modelingu StageOne to nowoczesna platforma łącząca artystyczne portfolio (jak arteportia.pl) z funkcjonalnościami społecznościowymi i biznesowymi.

## Technologie
- PHP 8.4 (strict_types, typed properties, match, readonly)
- MySQL 8 (utf8mb4_unicode_ci, prepared statements, indeksy)
- Bootstrap 5.3 (grid, dark mode, responsive utilities)
- JavaScript ES6+ (AJAX/fetch, walidacja w czasie rzeczywistym, lazy loading)

## Wymagania funkcjonalne

### 1. Sesje (Portfolio) – jednostka podstawowa
- Sesja = projekt (np. „Spring Editorial 2025”)
- Pola: tytuł, opis, lokalizacja, data sesji, kategorie (multiselect), okładka, status (public/private/draft)
- Metadane: współpracy (fotograf, wizaż, stylistka – jako linki do profili), sprzęt (np. „Canon R5 + 50mm f/1.2”)
- Galeria: max 50 zdjęć/sesja, upload z podglądem miniatur, drag&drop kolejności

### 2. Zdjęcia wewnątrz sesji
- Pełnoekranowy lightbox (jak arteportia.pl): strzałki, close (ESC), scroll kółkiem
- Dwa systemy:
  - ❤️ „Lubię” (serce) – publiczna liczba, max 1 polubienie/user
  - ⭐ Ocena 1–5 – prywatna średnia, widoczna tylko właścicielowi i adminowi
- Komentarze: tylko do sesji (nie do pojedynczych zdjęć), status pending → moderacja
- Współtwórcy: przypisanie osób (z profilu) do konkretnego zdjęcia

### 3. Profil użytkownika – zakładki
- `Sesje` – grid z miniaturkami (okładkami)
- `Zdjęcia` – wszystkie zdjęcia, filtr po sesjach
- `O mnie` – bio, specjalizacja, lokalizacja, sprzęt, linki (IG, web)
- `Kontakt` – formularz + dane (jeśli publiczne)
- `Obserwujący` / `Obserwowani` – z funkcjami isFollowing(), getFollowCounts()
- `Referencje` – tylko zatwierdzone (approved)
- `Statystyki`: liczba sesji, zdjęć, wyświetleń, średnia ocena, liczba obserwujących

### 4. System społecznościowy
- Obserwowanie (api/follow_user.php)
- Referencje (api/request_reference.php, give_reference.php)
- Wiadomości (AES-256-GCM szyfrowanie)
- Powiadomienia (pełna integracja – createNotification())
- Status online (AJAX co 30s → last_active)

### 5. Castingi (biznes)
- Tworzenie: wymagania (wzrost, rozmiar, lokalizacja), budżet, termin, kategorie
- Aplikacja: upload CV/book, status pending → akceptacja/odrzucenie
- Ulubione castings (toggle_casting_favorite.php)

### 6. Bezpieczeństwo
- Prepared statements (PDO)
- CSRF tokens
- reCAPTCHA v3 (rejestracja, logowanie, kontakt)
- Rate limiting
- Szyfrowanie wiadomości (AES-256-GCM)
- Security headers (CSP, HSTS, XSS-Protection)
- RODO: zgody, eksport danych, cookie banner

### 7. Panel administracyjny & moderacyjny
- Moderacja sesji (approving/rejecting)
- Moderacja komentarzy/referencji
- Zarządzanie użytkownikami, kategoriami, zgłoszeniami
- Uprawnienia moderatorów (moderator_permissions)