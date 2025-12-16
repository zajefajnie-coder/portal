# Instrukcja połączenia SSH z serwerem

## Status połączenia

- ✅ Serwer odpowiada na ping (83.230.44.103)
- ❌ Połączenie SSH na porcie 22 nie działa (timeout)

## Możliwe przyczyny:

1. **Port SSH jest inny niż 22** - sprawdź w panelu hostingowym
2. **Firewall blokuje port 22** - może być otwarty inny port
3. **SSH nie jest jeszcze skonfigurowany** - może trzeba go najpierw włączyć
4. **Inny użytkownik** - może nie jest to `root`, ale np. `admin`, `debian`, `ubuntu`

## Sprawdzenie portu SSH:

```bash
# Windows PowerShell
Test-NetConnection -ComputerName 83.230.44.103 -Port 22
Test-NetConnection -ComputerName 83.230.44.103 -Port 2222

# Linux/Mac
nc -zv 83.230.44.103 22
nc -zv 83.230.44.103 2222
```

## Alternatywne metody połączenia:

### 1. Panel hostingowy
Jeśli masz dostęp do panelu hostingowego (cPanel, Plesk, DirectAdmin), możesz:
- Sprawdzić port SSH
- Włączyć dostęp SSH
- Pobrać klucze SSH

### 2. Inny port SSH
```bash
ssh -p [PORT] root@83.230.44.103
```

### 3. Inny użytkownik
```bash
ssh admin@83.230.44.103
ssh debian@83.230.44.103
ssh ubuntu@83.230.44.103
```

## Po uzyskaniu dostępu SSH:

Wykonaj wdrożenie zgodnie z instrukcjami w `QUICK-DEPLOY.md` lub użyj skryptu `deploy.sh`.

## Informacje potrzebne do wdrożenia:

1. **Port SSH** (domyślnie 22)
2. **Użytkownik SSH** (domyślnie root)
3. **Metoda uwierzytelniania** (hasło czy klucz SSH)
4. **Czy serwer ma już zainstalowane:**
   - PHP 8.2+
   - Composer
   - Node.js/npm
   - MySQL
   - Nginx/Apache

