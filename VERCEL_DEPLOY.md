# Deployment na Vercel - Instrukcja

## 🚀 Krok po kroku

### 1. Przygotowanie repozytorium

Upewnij się, że Twój kod jest w repozytorium Git (GitHub, GitLab, Bitbucket):

```bash
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/twoj-username/portal-modelingowy.git
git push -u origin main
```

### 2. Baza danych MySQL w chmurze

Vercel nie oferuje natywnej bazy MySQL, więc musisz użyć zewnętrznego dostawcy:

#### Opcja A: PlanetScale (Rekomendowane - darmowy plan)
1. Utwórz konto na [planetscale.com](https://planetscale.com)
2. Utwórz nową bazę danych
3. Skopiuj connection string (wygląda jak: `mysql://user:password@host/database`)
4. PlanetScale automatycznie tworzy branchy i migracje

#### Opcja B: Railway
1. Utwórz konto na [railway.app](https://railway.app)
2. Dodaj nowy serwis MySQL
3. Skopiuj dane połączenia z zakładki "Variables"

#### Opcja C: AWS RDS / Google Cloud SQL
Dla większych projektów produkcyjnych.

### 3. Utworzenie projektu na Vercel

1. Przejdź na [vercel.com](https://vercel.com) i zaloguj się
2. Kliknij "Add New Project"
3. Połącz swoje repozytorium Git
4. Vercel automatycznie wykryje Next.js

### 4. Konfiguracja zmiennych środowiskowych

W ustawieniach projektu Vercel, dodaj następujące zmienne środowiskowe:

#### Baza danych MySQL:
```
DB_HOST=twoj-host-mysql
DB_PORT=3306
DB_USER=twoj-user
DB_PASSWORD=twoje-haslo
DB_NAME=portal_modelingowy
```

**Dla PlanetScale:**
- Użyj connection string z PlanetScale dashboard
- Parsuj go na osobne zmienne lub użyj biblioteki `@planetscale/database`

#### NextAuth:
```
NEXTAUTH_URL=https://twoja-domena.vercel.app
NEXTAUTH_SECRET=wygeneruj-losowy-secret-key
```

**Wygeneruj NEXTAUTH_SECRET:**
```bash
openssl rand -base64 32
```

#### Opcjonalne:
```
NEXT_PUBLIC_HAS_IOD=false
GOOGLE_CLIENT_ID=twoj-google-client-id
GOOGLE_CLIENT_SECRET=twoj-google-client-secret
```

### 5. Konfiguracja dla PlanetScale (jeśli używasz)

PlanetScale używa specjalnego connection string. Zaktualizuj `lib/db.ts`:

```typescript
// Dla PlanetScale użyj @planetscale/database zamiast mysql2
import { connect } from '@planetscale/database';

const config = {
  host: process.env.DB_HOST,
  username: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
};

export const connection = connect(config);
```

Lub użyj connection string bezpośrednio:
```typescript
import mysql from 'mysql2/promise';

export function getPool(): mysql.Pool {
  if (!pool) {
    // Dla PlanetScale connection string
    const connectionString = process.env.DATABASE_URL;
    
    if (connectionString) {
      // Parsuj connection string
      const url = new URL(connectionString.replace('mysql://', 'https://'));
      pool = mysql.createPool({
        host: url.hostname,
        port: parseInt(url.port || '3306'),
        user: url.username,
        password: url.password,
        database: url.pathname.slice(1),
        ssl: {
          rejectUnauthorized: true
        }
      });
    } else {
      // Standardowe połączenie
      pool = mysql.createPool({
        host: process.env.DB_HOST || 'localhost',
        // ... reszta konfiguracji
      });
    }
  }
  return pool;
}
```

### 6. Uruchomienie migracji bazy danych

Po pierwszym deploymencie, uruchom migracje:

**Opcja 1: Przez Vercel CLI**
```bash
vercel env pull .env.local
mysql -h $DB_HOST -u $DB_USER -p$DB_PASSWORD $DB_NAME < lib/schema.sql
```

**Opcja 2: Przez PlanetScale CLI**
```bash
pscale connect portal_modelingowy main --port 3309
mysql -h 127.0.0.1 -P 3309 -u root -p < lib/schema.sql
```

**Opcja 3: Przez Railway Dashboard**
- Otwórz MySQL terminal w Railway
- Wklej zawartość `lib/schema.sql`

### 7. Deploy!

1. Vercel automatycznie zbuduje i wdroży aplikację
2. Po zakończeniu otrzymasz URL: `https://twoj-projekt.vercel.app`
3. Sprawdź czy wszystko działa

### 8. Konfiguracja domeny (opcjonalnie)

1. W ustawieniach projektu Vercel → Domains
2. Dodaj swoją domenę (np. `portal-modelingowy.pl`)
3. Skonfiguruj DNS zgodnie z instrukcjami Vercel

## 🔧 Rozwiązywanie problemów

### Błąd: "Cannot connect to database"

**Rozwiązanie:**
- Sprawdź czy zmienne środowiskowe są poprawnie ustawione
- Upewnij się, że baza danych pozwala na połączenia z IP Vercel
- Dla PlanetScale: sprawdź czy używasz SSL

### Błąd: "Table doesn't exist"

**Rozwiązanie:**
- Uruchom migracje bazy danych (patrz krok 6)
- Sprawdź czy `DB_NAME` jest poprawne

### Błąd: "NEXTAUTH_SECRET is not set"

**Rozwiązanie:**
- Dodaj `NEXTAUTH_SECRET` w ustawieniach Vercel
- Wygeneruj nowy secret: `openssl rand -base64 32`

### Timeout przy połączeniu z bazą

**Rozwiązanie:**
- Użyj connection pooling (już zaimplementowane w `lib/db.ts`)
- Sprawdź czy baza danych jest dostępna (nie śpi w free tier)

## 📝 Checklist przed deploymentem

- [ ] Kod jest w repozytorium Git
- [ ] Baza danych MySQL jest skonfigurowana w chmurze
- [ ] Wszystkie zmienne środowiskowe są ustawione w Vercel
- [ ] `NEXTAUTH_URL` wskazuje na domenę Vercel
- [ ] Migracje bazy danych zostały uruchomione
- [ ] Dane administratora są uzupełnione w dokumentach prawnych
- [ ] Testowałeś aplikację lokalnie

## 🔗 Przydatne linki

- [Vercel Documentation](https://vercel.com/docs)
- [PlanetScale Documentation](https://planetscale.com/docs)
- [Railway Documentation](https://docs.railway.app)
- [NextAuth.js Vercel Deployment](https://next-auth.js.org/deployment)

## 💡 Wskazówki

1. **Użyj Vercel Environment Variables** dla różnych środowisk (Preview, Production)
2. **Włącz Vercel Analytics** dla monitorowania wydajności
3. **Skonfiguruj Vercel Cron Jobs** dla zadań okresowych (jeśli potrzebne)
4. **Użyj Vercel Edge Functions** dla szybkich odpowiedzi (opcjonalnie)

