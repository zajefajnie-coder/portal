# Deployment na Zenbox - Instrukcja

## ⚠️ Ważne informacje

Zenbox to hosting PHP/MySQL, który **nie obsługuje natywnie Node.js**. Mamy dwie opcje:

### Opcja 1: Eksport statyczny (Static Export)
- ✅ Działa na Zenbox
- ❌ Brak SSR (Server-Side Rendering)
- ❌ Brak API Routes
- ✅ Wszystkie strony statyczne działają

### Opcja 2: Node.js przez SSH/VPS (jeśli dostępne)
- ✅ Pełna funkcjonalność Next.js
- ⚠️ Wymaga dostępu SSH i możliwości uruchomienia Node.js

## 🚀 Opcja 1: Eksport statyczny (Rekomendowane dla Zenbox)

### Krok 1: Zbuduj aplikację dla Zenbox

**Windows:**
```bash
build-zenbox.bat
```

**Linux/Mac:**
```bash
chmod +x build-zenbox.sh
./build-zenbox.sh
```

**Lub ręcznie:**
```bash
npm install
npm run build:static
```

To utworzy folder `out` ze statycznymi plikami HTML, CSS i JS gotowymi do przesłania na Zenbox.

### Krok 3: Prześlij pliki na Zenbox

1. Zaloguj się do panelu Zenbox (FTP lub File Manager)
2. Przejdź do katalogu `public_html` (lub odpowiedniego dla Twojej domeny)
3. Prześlij **całą zawartość** folderu `out` do `public_html`
4. Upewnij się, że plik `index.html` jest w głównym katalogu

### Krok 4: Konfiguracja bazy danych MySQL

1. W panelu Zenbox utwórz bazę danych MySQL
2. Uruchom skrypt `lib/schema.sql` w bazie danych (przez phpMyAdmin lub MySQL)
3. Zapisz dane dostępowe:
   - Host (zwykle `localhost` lub `mysql.zenbox.pl`)
   - Nazwa bazy danych
   - Użytkownik
   - Hasło

### Krok 5: Konfiguracja .htaccess (opcjonalnie)

Utwórz plik `.htaccess` w `public_html`:

```apache
# Przekierowania dla Next.js routing
RewriteEngine On
RewriteBase /

# Przekieruj wszystkie żądania do index.html (dla client-side routing)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /index.html [L]

# Kompresja
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>

# Cache
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType image/jpg "access plus 1 year"
  ExpiresByType image/jpeg "access plus 1 year"
  ExpiresByType image/gif "access plus 1 year"
  ExpiresByType image/png "access plus 1 year"
  ExpiresByType text/css "access plus 1 month"
  ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

## 🔧 Opcja 2: Node.js przez SSH (jeśli dostępne)

Jeśli Zenbox oferuje dostęp SSH i możliwość uruchomienia Node.js:

### Krok 1: Zbuduj aplikację lokalnie

```bash
npm run build
```

### Krok 2: Prześlij pliki przez SSH/FTP

Prześlij cały projekt (bez `node_modules` i `.next`).

### Krok 3: Zainstaluj zależności na serwerze

```bash
ssh twoj-user@zenbox.pl
cd public_html
npm install --production
```

### Krok 4: Uruchom aplikację

```bash
npm start
```

Lub użyj PM2 do zarządzania procesem:

```bash
npm install -g pm2
pm2 start npm --name "portal" -- start
pm2 save
pm2 startup
```

## 📝 Konfiguracja zmiennych środowiskowych

Dla eksportu statycznego zmienne środowiskowe muszą być prefiksowane `NEXT_PUBLIC_`:

```env
NEXT_PUBLIC_DB_HOST=localhost
NEXT_PUBLIC_DB_PORT=3306
NEXT_PUBLIC_DB_USER=twoj_user
NEXT_PUBLIC_DB_PASSWORD=twoje_haslo
NEXT_PUBLIC_DB_NAME=portal_modelingowy
```

**UWAGA:** W eksporcie statycznym nie możesz używać zmiennych serwerowych (bez `NEXT_PUBLIC_`), więc połączenie z bazą danych musi być przez API zewnętrzne lub trzeba użyć innego podejścia.

## ⚠️ Ograniczenia eksportu statycznego

1. **Brak API Routes** - `/api/health` nie będzie działać
2. **Brak SSR** - wszystkie strony są renderowane statycznie
3. **Brak Server Components** - wszystko jest Client Component
4. **Brak połączenia z bazą danych bezpośrednio** - trzeba użyć zewnętrznego API

## 🔄 Alternatywne rozwiązanie: Hybrydowe

Możesz użyć:
- **Frontend na Zenbox** (statyczny eksport)
- **Backend API na osobnym serwerze** (np. Railway, Render) który obsługuje Node.js

## 📞 Wsparcie Zenbox

Skontaktuj się z Zenbox, aby potwierdzić:
- Czy mają wsparcie dla Node.js
- Czy oferują dostęp SSH
- Jakie są limity dla aplikacji Node.js

## 🔗 Przydatne linki

- [Next.js Static Export](https://nextjs.org/docs/app/building-your-application/deploying/static-exports)
- [Zenbox Dokumentacja](https://zenbox.pl/pomoc)

