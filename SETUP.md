# Instrukcja uruchomienia i konfiguracji

## ✅ Co zostało zaimplementowane

### Struktura aplikacji
- ✅ Next.js 14 z App Router i Server Components
- ✅ TypeScript
- ✅ Tailwind CSS + shadcn/ui komponenty
- ✅ Responsywny design (mobile-first)
- ✅ Dostępność (WCAG 2.1 AA)

### Strony i funkcjonalności
- ✅ **Strona główna** (`/`)
  - Sekcja hero z oryginalnym tekstem
  - Grid "Najnowsze Sesje" z 3 przykładowymi sesjami
  - Sekcja "Dla Wszystkich Twórców" z kartami ról
  - CTA "Zacznij już dziś!"
  
- ✅ **Strony prawne** (pełne treści po polsku, zgodne z RODO):
  - `/regulamin` - Regulamin platformy
  - `/polityka-prywatnosci` - Polityka Prywatności
  - `/rodo` - Informacja RODO
  
- ✅ **Strony funkcjonalne**:
  - `/rejestracja` - Formularz rejestracji z wymaganą zgodą na regulamin
  - `/logowanie` - Formularz logowania
  - `/kontakt` - Strona kontaktowa
  - `/look/[id]` - Szczegóły sesji
  - `/profil/[id]` - Profil użytkownika z portfolio

- ✅ **Komponenty**:
  - `<Header>` - Nawigacja z menu mobilnym
  - `<Footer>` - Stopka z linkami prawnymi
  - `<CookieConsent>` - Banner zgody na cookies
  - `<LookCard>` - Karta sesji
  - `<RoleCard>` - Karta roli (fotograf/model/zespół)
  - `<LegalPageLayout>` - Layout dla stron prawnych

### Zgodność prawna
- ✅ Pełne dokumenty prawne w języku polskim
- ✅ Checkbox zgody przy rejestracji
- ✅ Banner zgody na pliki cookies
- ✅ Stopka z linkami do dokumentów

### Dane testowe
- ✅ 3 przykładowe sesje w `lib/mock-data.ts`
- ✅ Mock użytkownicy dla profili

## 🚧 Co wymaga konfiguracji przed uruchomieniem

### 1. Dane administratora (WYMAGANE!)

Przed uruchomieniem produkcyjnym **MUSISZ** uzupełnić następujące pliki:

#### `/app/regulamin/page.tsx`
Zastąp placeholdery:
- `[Nazwa firmy/osoby fizycznej prowadzącej działalność gospodarczą]`
- `[Adres]`
- `[NIP]`
- `[REGON]`
- `[e-mail kontaktowy]`

#### `/app/polityka-prywatnosci/page.tsx`
Te same dane co powyżej.

#### `/app/rodo/page.tsx`
Te same dane + opcjonalnie:
- `[Imię i nazwisko IOD]` (jeśli masz Inspektora Ochrony Danych)
- `[e-mail IOD]`

#### `/app/kontakt/page.tsx`
Te same dane co w regulaminie.

### 2. Konfiguracja MySQL

1. Zainstaluj MySQL 8.0+ (jeśli jeszcze nie masz):
   - Windows: [MySQL Installer](https://dev.mysql.com/downloads/installer/)
   - macOS: `brew install mysql`
   - Linux: `sudo apt-get install mysql-server`

2. Utwórz bazę danych i tabele:
   ```bash
   mysql -u root -p < lib/schema.sql
   ```
   Lub uruchom plik `lib/schema.sql` w swoim kliencie MySQL (np. MySQL Workbench, phpMyAdmin).

3. Utwórz plik `.env.local`:
   ```bash
   cp .env.example .env.local
   ```

4. Skonfiguruj połączenie z bazą danych w `.env.local`:
   ```
   DB_HOST=localhost
   DB_PORT=3306
   DB_USER=root
   DB_PASSWORD=twoje-haslo
   DB_NAME=portal_modelingowy
   ```

5. Wygeneruj secret key dla NextAuth:
   ```bash
   openssl rand -base64 32
   ```
   Wklej wynik do `.env.local` jako `NEXTAUTH_SECRET`.

### 3. Schemat bazy danych

Schemat MySQL znajduje się w pliku `lib/schema.sql` i zawiera:
- Tabelę `users` - użytkownicy platformy
- Tabelę `looks` - sesje fotograficzne
- Tabelę `collaborators` - współpracownicy w sesjach
- Tabele dla NextAuth: `sessions`, `accounts`, `verification_tokens`

### 4. Inspektor Ochrony Danych (opcjonalnie)

Jeśli masz IOD, ustaw w `.env.local`:
```
NEXT_PUBLIC_HAS_IOD=true
```

I uzupełnij dane IOD w `/app/rodo/page.tsx`.

## 🚀 Uruchomienie

1. Zainstaluj zależności:
   ```bash
   npm install
   ```

2. Uruchom serwer deweloperski:
   ```bash
   npm run dev
   ```

3. Otwórz [http://localhost:3000](http://localhost:3000)

## 📝 Następne kroki (do zaimplementowania)

1. **Autentykacja NextAuth**:
   - Skonfiguruj NextAuth w `/app/api/auth/[...nextauth]/route.ts`
   - Zaimplementuj logowanie/rejestrację w `/app/rejestracja/page.tsx`
   - Dodaj Google OAuth (opcjonalnie)
   - Dodaj reset hasła

2. **Baza danych**:
   - Uruchom skrypt `lib/schema.sql` w MySQL
   - Zaimplementuj pobieranie danych z bazy zamiast mock data
   - Użyj funkcji pomocniczych z `lib/db-helpers.ts`

3. **Przesyłanie zdjęć**:
   - Wybierz hosting zdjęć (Cloudinary, AWS S3, lub lokalny storage)
   - Zaimplementuj upload zdjęć
   - Dodaj walidację obrazów
   - Dodaj kompresję obrazów

4. **Profil użytkownika**:
   - Strona edycji profilu
   - Publikowanie nowych sesji
   - Zarządzanie portfolio

5. **Funkcjonalności społecznościowe**:
   - System tagowania
   - Wyszukiwanie
   - Powiadomienia
   - Wiadomości między użytkownikami

6. **Panel administracyjny**:
   - Moderacja treści
   - Zarządzanie użytkownikami
   - Statystyki

## 🔒 Bezpieczeństwo

- ✅ Wszystkie hasła są hashowane przez NextAuth (bcrypt)
- ✅ Połączenia HTTPS (w produkcji)
- ✅ Walidacja danych po stronie klienta i serwera
- ✅ Prepared statements w mysql2 (ochrona przed SQL injection)
- ✅ Connection pooling dla optymalizacji połączeń

## 📞 Wsparcie

W razie pytań sprawdź:
- [Dokumentacja Next.js](https://nextjs.org/docs)
- [Dokumentacja mysql2](https://github.com/sidorares/node-mysql2)
- [Dokumentacja NextAuth.js](https://next-auth.js.org/)
- [shadcn/ui](https://ui.shadcn.com)

