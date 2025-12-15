# 🚀 Instrukcja wdrożenia na Zenbox - Krok po kroku

## 📋 Co będziesz potrzebować

- ✅ Zbudowana aplikacja (folder `out`)
- ✅ Dostęp do panelu Zenbox (FTP lub File Manager)
- ✅ Dane dostępowe do bazy danych MySQL

## 🔨 Krok 1: Zbuduj aplikację lokalnie

### Windows:
```bash
build-zenbox.bat
```

### Linux/Mac:
```bash
chmod +x build-zenbox.sh
./build-zenbox.sh
```

**Lub ręcznie:**
```bash
npm install
npm run build:static
```

Po zakończeniu będziesz miał folder `out` z gotowymi plikami.

## 📤 Krok 2: Prześlij pliki na serwer

### Opcja A: Przez File Manager (Panel Zenbox)

1. **Zaloguj się do panelu Zenbox**
2. **Otwórz File Manager**
3. **Przejdź do katalogu `public_html`** (to jest katalog root Twojej domeny)
4. **Prześlij pliki:**
   - Prześlij **całą zawartość** folderu `out` do `public_html`
   - Prześlij plik `.htaccess` do `public_html`
   - Prześlij plik `install.php` do `public_html`

**Struktura powinna wyglądać tak:**
```
public_html/
├── index.html          ← Strona główna
├── .htaccess          ← Konfiguracja Apache
├── install.php        ← Instalator bazy danych
├── _next/             ← Pliki Next.js
│   └── static/
├── regulamin/
│   └── index.html
├── polityka-prywatnosci/
│   └── index.html
└── ...
```

### Opcja B: Przez FTP (np. FileZilla)

1. **Połącz się przez FTP:**
   - Host: `ftp.twoja-domena.pl` lub IP serwera
   - Użytkownik: Twój login Zenbox
   - Hasło: Twoje hasło Zenbox
   - Port: 21 (lub 22 dla SFTP)

2. **Przejdź do katalogu `public_html`**

3. **Prześlij pliki:**
   - Zaznacz wszystkie pliki z folderu `out`
   - Przeciągnij je do `public_html` na serwerze
   - Prześlij `.htaccess` i `install.php`

## 🗄️ Krok 3: Zainstaluj bazę danych

### Metoda 1: Przez install.php (Najłatwiejsza) ⭐

1. **Otwórz w przeglądarce:**
   ```
   https://twoja-domena.pl/install.php
   ```

2. **Kliknij "Rozpocznij instalację"**

3. **Poczekaj na komunikat sukcesu**

4. **⚠️ WAŻNE: Usuń plik `install.php`** po zakończeniu instalacji (ze względów bezpieczeństwa)

### Metoda 2: Przez phpMyAdmin

1. **Zaloguj się do panelu Zenbox**
2. **Otwórz phpMyAdmin**
3. **Wybierz bazę danych `krzyszton_port1`**
4. **Przejdź do zakładki "SQL"**
5. **Skopiuj zawartość pliku `lib/schema-zenbox.sql`**
6. **Wklej i kliknij "Wykonaj"**

## ✅ Krok 4: Sprawdź czy działa

Otwórz w przeglądarce:
- ✅ `https://twoja-domena.pl` - strona główna
- ✅ `https://twoja-domena.pl/regulamin` - regulamin
- ✅ `https://twoja-domena.pl/polityka-prywatnosci` - polityka prywatności
- ✅ `https://twoja-domena.pl/rodo` - RODO
- ✅ `https://twoja-domena.pl/kontakt` - kontakt

## 🔧 Rozwiązywanie problemów

### Strona nie ładuje się / Błąd 404

**Rozwiązanie:**
- Sprawdź czy `index.html` jest w `public_html/`
- Sprawdź czy `.htaccess` jest w `public_html/`
- Sprawdź uprawnienia plików (powinny być 644 dla plików, 755 dla katalogów)

### Routing nie działa (strony pokazują 404)

**Rozwiązanie:**
- Sprawdź czy `.htaccess` jest poprawnie przesłany
- Upewnij się, że mod_rewrite jest włączony na serwerze (skontaktuj się z supportem Zenbox)

### Błąd połączenia z bazą danych (w install.php)

**Rozwiązanie:**
- Sprawdź dane dostępowe w pliku `install.php` (linie 8-11)
- Upewnij się, że baza danych `krzyszton_port1` istnieje
- Sprawdź czy użytkownik ma uprawnienia

### Pliki CSS/JS nie ładują się

**Rozwiązanie:**
- Sprawdź czy folder `_next/` jest przesłany
- Sprawdź uprawnienia folderów (755)
- Wyczyść cache przeglądarki (Ctrl+F5)

## 📝 Checklist przed deploymentem

- [ ] Aplikacja zbudowana (`npm run build:static`)
- [ ] Folder `out` zawiera wszystkie pliki
- [ ] Plik `.htaccess` jest gotowy
- [ ] Plik `install.php` jest gotowy
- [ ] Masz dostęp do panelu Zenbox
- [ ] Baza danych MySQL jest utworzona w panelu Zenbox
- [ ] Masz dane dostępowe do bazy danych

## 📞 Wsparcie

Jeśli masz problemy:
1. Sprawdź logi w panelu Zenbox
2. Skontaktuj się z supportem Zenbox
3. Sprawdź dokumentację: [ZENBOX_SETUP.md](./ZENBOX_SETUP.md)

## 🔐 Bezpieczeństwo po deploymentzie

Po zakończeniu instalacji:
1. ✅ **Usuń `install.php`** ze serwera
2. ✅ Sprawdź uprawnienia plików (644 dla plików, 755 dla katalogów)
3. ✅ Upewnij się, że `.env.local` nie jest na serwerze (jest w `.gitignore`)

---

**Gotowe! 🎉 Twoja aplikacja powinna działać na `https://twoja-domena.pl`**

