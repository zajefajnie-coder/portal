# 🚀 Szybki deployment na Vercel

## 5-minutowy przewodnik

### Krok 1: Baza danych (2 min)

**PlanetScale (Rekomendowane - darmowy plan):**
1. Idź na [planetscale.com](https://planetscale.com) → Sign Up
2. Create database → wybierz plan (Hobby - darmowy)
3. Skopiuj connection string z dashboard
4. Uruchom migracje przez PlanetScale CLI lub przez dashboard

**Lub Railway:**
1. Idź na [railway.app](https://railway.app) → Sign Up
2. New Project → Add MySQL
3. Skopiuj zmienne środowiskowe z zakładki Variables

### Krok 2: Vercel (2 min)

1. Idź na [vercel.com](https://vercel.com) → Sign Up (przez GitHub)
2. New Project → Import Git Repository
3. Wybierz swoje repozytorium
4. Vercel automatycznie wykryje Next.js ✅

### Krok 3: Zmienne środowiskowe (1 min)

W Vercel Dashboard → Settings → Environment Variables, dodaj:

```
DB_HOST=twoj-host
DB_PORT=3306
DB_USER=twoj-user
DB_PASSWORD=twoje-haslo
DB_NAME=portal_modelingowy
NEXTAUTH_URL=https://twoj-projekt.vercel.app
NEXTAUTH_SECRET=wygeneruj-tutaj
```

**Wygeneruj NEXTAUTH_SECRET:**
```bash
openssl rand -base64 32
```

### Krok 4: Deploy!

1. Kliknij **Deploy** w Vercel
2. Poczekaj na build (2-3 minuty)
3. Sprawdź URL: `https://twoj-projekt.vercel.app`

### Krok 5: Migracje bazy danych

Po pierwszym deploymencie, uruchom migracje:

**PlanetScale:**
```bash
npm install -g @planetscale/cli
pscale connect portal_modelingowy main --port 3309
mysql -h 127.0.0.1 -P 3309 -u root -p < lib/schema.sql
```

**Railway:**
- Otwórz MySQL terminal w Railway dashboard
- Wklej zawartość `lib/schema.sql`

**Inny dostawca:**
```bash
mysql -h DB_HOST -u DB_USER -pDB_PASSWORD DB_NAME < lib/schema.sql
```

## ✅ Sprawdź czy działa

1. Otwórz: `https://twoj-projekt.vercel.app`
2. Sprawdź health check: `https://twoj-projekt.vercel.app/api/health`
3. Przetestuj strony: `/regulamin`, `/polityka-prywatnosci`, `/rodo`

## 🆘 Problemy?

**Błąd połączenia z bazą:**
- Sprawdź zmienne środowiskowe w Vercel
- Upewnij się, że baza pozwala na połączenia zewnętrzne
- Dla PlanetScale: użyj SSL

**Błąd "Table doesn't exist":**
- Uruchom migracje (Krok 5)

**Błąd "NEXTAUTH_SECRET":**
- Dodaj `NEXTAUTH_SECRET` w Vercel Environment Variables

Więcej szczegółów: [VERCEL_DEPLOY.md](./VERCEL_DEPLOY.md)

