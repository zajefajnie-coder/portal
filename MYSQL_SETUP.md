# Instrukcja konfiguracji MySQL

## 📋 Wymagania

- MySQL 8.0+ lub MariaDB 10.3+
- Node.js 18+

## 🚀 Szybki start

### 1. Utwórz bazę danych

Uruchom skrypt SQL:
```bash
mysql -u root -p < lib/schema.sql
```

Lub ręcznie w kliencie MySQL:
```sql
SOURCE lib/schema.sql;
```

### 2. Skonfiguruj zmienne środowiskowe

Skopiuj `.env.example` do `.env.local`:
```bash
cp .env.example .env.local
```

Edytuj `.env.local`:
```env
DB_HOST=localhost
DB_PORT=3306
DB_USER=root
DB_PASSWORD=twoje-haslo
DB_NAME=portal_modelingowy

NEXTAUTH_URL=http://localhost:3000
NEXTAUTH_SECRET=wygeneruj-secret-key-tutaj
```

### 3. Wygeneruj NEXTAUTH_SECRET

```bash
# Linux/Mac
openssl rand -base64 32

# Windows PowerShell
[Convert]::ToBase64String((1..32 | ForEach-Object { Get-Random -Maximum 256 }))
```

### 4. Zainstaluj zależności

```bash
npm install
```

### 5. Uruchom aplikację

```bash
npm run dev
```

## 📊 Struktura bazy danych

### Tabela `users`
Przechowuje dane użytkowników:
- `id` - UUID użytkownika
- `email` - unikalny adres e-mail
- `password_hash` - zahashowane hasło (bcrypt)
- `name` - imię i nazwisko
- `pronouns` - zaimki (opcjonalnie)
- `location` - lokalizacja (opcjonalnie)
- `experience_level` - poziom doświadczenia
- `bio` - biografia
- `specialties` - specjalizacje jako JSON array
- `avatar_url` - URL do zdjęcia profilowego

### Tabela `looks`
Przechowuje sesje fotograficzne:
- `id` - UUID sesji
- `author_id` - ID autora (fotografa)
- `title` - tytuł sesji
- `date` - data sesji
- `location` - lokalizacja (opcjonalnie)
- `image_url` - URL do głównego zdjęcia
- `image_alt` - tekst alternatywny dla zdjęcia
- `tags` - tagi jako JSON array
- `is_public` - czy sesja jest publiczna

### Tabela `collaborators`
Przechowuje współpracowników w sesjach:
- `id` - UUID współpracownika
- `look_id` - ID sesji
- `user_id` - ID użytkownika (opcjonalnie, jeśli jest zarejestrowany)
- `name` - imię i nazwisko
- `role` - rola (model, fotograf, wizażysta, etc.)

### Tabele NextAuth
- `sessions` - sesje użytkowników
- `accounts` - konta OAuth (Google, etc.)
- `verification_tokens` - tokeny weryfikacyjne

## 🔧 Rozwiązywanie problemów

### Błąd połączenia z bazą danych

**Problem:** `ER_ACCESS_DENIED_ERROR` lub `ECONNREFUSED`

**Rozwiązanie:**
1. Sprawdź czy MySQL jest uruchomiony:
   ```bash
   # Windows
   net start MySQL80
   
   # Linux/Mac
   sudo systemctl start mysql
   ```

2. Sprawdź dane logowania w `.env.local`

3. Sprawdź czy użytkownik ma uprawnienia:
   ```sql
   GRANT ALL PRIVILEGES ON portal_modelingowy.* TO 'twoj_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

### Błąd: "Table doesn't exist"

**Rozwiązanie:**
Upewnij się, że uruchomiłeś skrypt `lib/schema.sql`:
```bash
mysql -u root -p < lib/schema.sql
```

### Błąd: "Unknown column 'tags'"

**Rozwiązanie:**
MySQL 5.7 nie obsługuje natywnie JSON. Użyj MySQL 8.0+ lub zmień typ kolumny na TEXT i parsuj JSON w aplikacji.

## 🔒 Bezpieczeństwo

### Produkcja

1. **Utwórz dedykowanego użytkownika MySQL:**
   ```sql
   CREATE USER 'portal_user'@'localhost' IDENTIFIED BY 'silne-haslo';
   GRANT SELECT, INSERT, UPDATE, DELETE ON portal_modelingowy.* TO 'portal_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

2. **Użyj silnego hasła** dla użytkownika bazy danych

3. **Włącz SSL** dla połączeń z bazą danych (jeśli baza jest zdalna)

4. **Backup bazy danych:**
   ```bash
   mysqldump -u root -p portal_modelingowy > backup.sql
   ```

## 📝 Przykładowe zapytania

### Dodaj użytkownika testowego
```sql
INSERT INTO users (id, email, password_hash, name, location) 
VALUES (UUID(), 'test@example.com', '$2a$10$...', 'Jan Kowalski', 'Warszawa');
```

### Pobierz wszystkie publiczne sesje
```sql
SELECT l.*, u.name as author_name 
FROM looks l 
JOIN users u ON l.author_id = u.id 
WHERE l.is_public = TRUE 
ORDER BY l.created_at DESC;
```

### Pobierz sesje użytkownika z współpracownikami
```sql
SELECT l.*, 
       JSON_ARRAYAGG(JSON_OBJECT('name', c.name, 'role', c.role)) as collaborators
FROM looks l
LEFT JOIN collaborators c ON c.look_id = l.id
WHERE l.author_id = ?
GROUP BY l.id;
```

## 🔗 Przydatne linki

- [Dokumentacja MySQL](https://dev.mysql.com/doc/)
- [mysql2 npm package](https://github.com/sidorares/node-mysql2)
- [NextAuth.js](https://next-auth.js.org/)
- [MySQL Workbench](https://www.mysql.com/products/workbench/) - GUI dla MySQL

