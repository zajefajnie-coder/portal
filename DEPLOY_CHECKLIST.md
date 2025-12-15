# ✅ Checklist przed deploymentem na Vercel

## Przed pierwszym deploymentem

### 📋 Kod i repozytorium
- [ ] Kod jest w repozytorium Git (GitHub/GitLab/Bitbucket)
- [ ] Wszystkie zmiany są commitowane i pushowane
- [ ] `.env.local` jest w `.gitignore` (nie commitujemy zmiennych środowiskowych)
- [ ] `node_modules` jest w `.gitignore`

### 🗄️ Baza danych
- [ ] Baza danych MySQL jest skonfigurowana w chmurze (PlanetScale/Railway/inna)
- [ ] Masz dostęp do danych połączenia (host, port, user, password, database)
- [ ] Uruchomiłeś migracje (`lib/schema.sql`) w bazie danych
- [ ] Sprawdziłeś połączenie z bazą lokalnie

### 🔐 Zmienne środowiskowe
- [ ] Wygenerowałeś `NEXTAUTH_SECRET`:
  ```bash
  openssl rand -base64 32
  ```
- [ ] Przygotowałeś listę wszystkich zmiennych środowiskowych:
  - `DB_HOST`
  - `DB_PORT`
  - `DB_USER`
  - `DB_PASSWORD`
  - `DB_NAME`
  - `NEXTAUTH_URL` (będzie to URL Vercel)
  - `NEXTAUTH_SECRET`
  - Opcjonalnie: `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`

### 📝 Dokumenty prawne
- [ ] Uzupełniłeś dane administratora w `/app/regulamin/page.tsx`
- [ ] Uzupełniłeś dane administratora w `/app/polityka-prywatnosci/page.tsx`
- [ ] Uzupełniłeś dane administratora w `/app/rodo/page.tsx`
- [ ] Uzupełniłeś dane administratora w `/app/kontakt/page.tsx`
- [ ] Jeśli masz IOD, uzupełniłeś dane w `/app/rodo/page.tsx`

### 🧪 Testy lokalne
- [ ] Aplikacja działa lokalnie (`npm run dev`)
- [ ] Połączenie z bazą danych działa
- [ ] Wszystkie strony się ładują poprawnie
- [ ] Nie ma błędów w konsoli

## Podczas deploymentu

### 🔧 Konfiguracja Vercel
- [ ] Utworzyłeś projekt na Vercel
- [ ] Połączyłeś repozytorium Git
- [ ] Dodałeś wszystkie zmienne środowiskowe w Vercel Dashboard
- [ ] Ustawiłeś `NEXTAUTH_URL` na URL Vercel (np. `https://twoj-projekt.vercel.app`)
- [ ] Sprawdziłeś czy build się powiódł

### ✅ Po deploymentzie
- [ ] Sprawdziłeś czy aplikacja działa: `https://twoj-projekt.vercel.app`
- [ ] Sprawdziłeś health check: `https://twoj-projekt.vercel.app/api/health`
- [ ] Przetestowałeś wszystkie główne strony:
  - [ ] Strona główna
  - [ ] Regulamin
  - [ ] Polityka Prywatności
  - [ ] RODO
  - [ ] Kontakt
  - [ ] Rejestracja
  - [ ] Logowanie
- [ ] Sprawdziłeś czy baza danych działa (sprawdź logi Vercel)
- [ ] Sprawdziłeś czy nie ma błędów w Vercel Dashboard → Functions

## Konfiguracja domeny (opcjonalnie)

- [ ] Dodałeś domenę w Vercel Dashboard → Settings → Domains
- [ ] Skonfigurowałeś DNS zgodnie z instrukcjami Vercel
- [ ] Zaktualizowałeś `NEXTAUTH_URL` na nową domenę
- [ ] Sprawdziłeś czy SSL działa (automatycznie przez Vercel)

## Monitoring i optymalizacja

- [ ] Włączyłeś Vercel Analytics (opcjonalnie)
- [ ] Sprawdziłeś wydajność w Vercel Dashboard
- [ ] Skonfigurowałeś alerty dla błędów (opcjonalnie)
- [ ] Ustawiłeś backup bazy danych (jeśli możliwe)

## 🔗 Przydatne linki

- [Vercel Dashboard](https://vercel.com/dashboard)
- [Vercel Environment Variables](https://vercel.com/docs/concepts/projects/environment-variables)
- [PlanetScale Dashboard](https://app.planetscale.com)
- [Railway Dashboard](https://railway.app)

## ⚠️ Ważne uwagi

1. **Nigdy nie commituj** `.env.local` lub innych plików ze zmiennymi środowiskowymi
2. **Używaj różnych** `NEXTAUTH_SECRET` dla różnych środowisk (development, preview, production)
3. **Regularnie rób backup** bazy danych
4. **Monitoruj logi** Vercel dla błędów
5. **Sprawdzaj limity** darmowego planu Vercel i bazy danych

