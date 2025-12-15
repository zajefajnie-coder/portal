# Konfiguracja dla Zenbox - Dane bazy danych

## ✅ Dane dostępowe do bazy danych

```
Nazwa bazy: krzyszton_port1
Host: localhost
Użytkownik: krzyszton_port1
Hasło: Alicja2025##
Wersja MySQL: MariaDB 10.6
```

## 🚀 Kroki do wdrożenia

### 1. Utwórz plik .env.local

Skopiuj `.env.zenbox.example` do `.env.local`:

```bash
cp .env.zenbox.example .env.local
```

Plik `.env.local` już zawiera poprawne dane dla Zenbox.

### 2. Zbuduj aplikację

```bash
# Windows
build-zenbox.bat

# Linux/Mac
./build-zenbox.sh
```

### 3. Uruchom migracje bazy danych

**Opcja A: Przez phpMyAdmin (Rekomendowane)**

1. Zaloguj się do panelu Zenbox
2. Otwórz phpMyAdmin
3. Wybierz bazę danych `krzyszton_port1`
4. Przejdź do zakładki "SQL"
5. Skopiuj zawartość pliku `lib/schema.sql`
6. Wklej i wykonaj

**Opcja B: Przez MySQL CLI (jeśli masz dostęp SSH)**

```bash
mysql -h localhost -u krzyszton_port1 -p krzyszton_port1 < lib/schema.sql
# Wpisz hasło: Alicja2025##
```

### 4. Prześlij pliki na serwer

1. **Zaloguj się do panelu Zenbox** (FTP lub File Manager)
2. **Przejdź do katalogu `public_html`** (to jest katalog root Twojej domeny)
3. **Prześlij zawartość folderu `out`**:
   - Wszystkie pliki i foldery z folderu `out/` bezpośrednio do `public_html/`
   - Upewnij się, że `index.html` jest w katalogu `public_html/`
   - Struktura powinna być: `public_html/index.html`, `public_html/_next/`, itd.
4. **Prześlij plik `.htaccess`** do katalogu `public_html/` (główny katalog)
5. **Prześlij plik `install.php`** do katalogu `public_html/` (do instalacji bazy danych)

### 5. Sprawdź czy działa

Otwórz w przeglądarce:
- `https://twoja-domena.pl` - strona główna
- `https://twoja-domena.pl/regulamin` - regulamin
- `https://twoja-domena.pl/polityka-prywatnosci` - polityka prywatności

## 🔧 Rozwiązywanie problemów

### Błąd połączenia z bazą danych

Jeśli aplikacja będzie potrzebować połączenia z bazą (w przyszłości):
- Sprawdź czy dane w `.env.local` są poprawne
- Upewnij się, że baza danych `krzyszton_port1` istnieje
- Sprawdź czy użytkownik `krzyszton_port1` ma uprawnienia

### Pliki nie ładują się poprawnie

- Sprawdź czy `.htaccess` jest w głównym katalogu
- Upewnij się, że wszystkie pliki z `out/` są przesłane
- Sprawdź uprawnienia plików (powinny być 644 dla plików, 755 dla katalogów)

### Routing nie działa

- Sprawdź czy `.htaccess` jest poprawnie przesłany
- Upewnij się, że mod_rewrite jest włączony na serwerze Zenbox

## 📝 Uwagi

- **Eksport statyczny** - aplikacja działa jako statyczna strona, bez SSR i API Routes
- **Mock data** - obecnie aplikacja używa danych testowych z `lib/mock-data.ts`
- **Baza danych** - będzie potrzebna gdy dodasz funkcjonalności wymagające połączenia z bazą

## 🔐 Bezpieczeństwo

⚠️ **WAŻNE:** Plik `.env.local` zawiera hasła i nie powinien być commitowany do Git (jest już w `.gitignore`).

## 📞 Wsparcie

Jeśli masz problemy:
1. Sprawdź logi w panelu Zenbox
2. Skontaktuj się z supportem Zenbox
3. Sprawdź dokumentację: [ZENBOX_DEPLOY.md](./ZENBOX_DEPLOY.md)

