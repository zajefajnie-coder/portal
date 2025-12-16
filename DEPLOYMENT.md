# Instrukcja wdrożenia na serwer Debian

## Serwer: 83.230.44.103

## Wymagania na serwerze:

1. **PHP 8.2+** z rozszerzeniami:
   - php8.2-cli
   - php8.2-fpm
   - php8.2-mysql
   - php8.2-xml
   - php8.2-mbstring
   - php8.2-curl
   - php8.2-zip
   - php8.2-gd

2. **Composer** - menedżer zależności PHP

3. **Node.js i npm** - do budowy assets frontend

4. **MySQL** - baza danych

5. **Nginx** lub **Apache** - serwer WWW

6. **Git** - do klonowania repozytorium

## Metoda 1: Automatyczne wdrożenie (rsync)

### Przygotowanie lokalne:

1. Upewnij się, że masz zainstalowany `rsync` i `ssh`
2. Skonfiguruj dostęp SSH do serwera (klucze SSH)

### Wykonanie wdrożenia:

```bash
chmod +x deploy.sh
./deploy.sh root@83.230.44.103
```

Lub z innym użytkownikiem:
```bash
./deploy.sh user@83.230.44.103
```

## Metoda 2: Ręczne wdrożenie

### Krok 1: Połączenie z serwerem

```bash
ssh root@83.230.44.103
```

### Krok 2: Instalacja wymaganych pakietów

```bash
# Aktualizacja systemu
apt update && apt upgrade -y

# Instalacja PHP i rozszerzeń
apt install -y php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml \
    php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd

# Instalacja Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Instalacja Node.js i npm
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Instalacja MySQL (jeśli nie jest zainstalowany)
apt install -y mysql-server

# Instalacja Nginx
apt install -y nginx

# Instalacja Git
apt install -y git
```

### Krok 3: Konfiguracja bazy danych

```bash
mysql -u root -p
```

W konsoli MySQL:
```sql
CREATE DATABASE krzyszton_port1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'krzyszton_port1'@'localhost' IDENTIFIED BY 'Alicja2025##';
GRANT ALL PRIVILEGES ON krzyszton_port1.* TO 'krzyszton_port1'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Krok 4: Klonowanie repozytorium

```bash
cd /var/www
git clone [URL_REPOZYTORIUM] portal-modelingowy
cd portal-modelingowy
```

### Krok 5: Konfiguracja aplikacji

```bash
# Skopiuj plik .env.example do .env
cp .env.example .env

# Edytuj plik .env i ustaw:
# - APP_ENV=production
# - APP_DEBUG=false
# - DB_CONNECTION=mysql
# - DB_HOST=127.0.0.1
# - DB_DATABASE=krzyszton_port1
# - DB_USERNAME=krzyszton_port1
# - DB_PASSWORD=Alicja2025##

nano .env

# Generowanie klucza aplikacji
php artisan key:generate

# Instalacja zależności
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Utworzenie linku symbolicznego
php artisan storage:link

# Uruchomienie migracji
php artisan migrate --force

# Optymalizacja
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Krok 6: Konfiguracja Nginx

Utwórz plik `/etc/nginx/sites-available/portal-modelingowy`:

```nginx
server {
    listen 80;
    server_name 83.230.44.103;
    root /var/www/portal-modelingowy/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktywuj konfigurację:
```bash
ln -s /etc/nginx/sites-available/portal-modelingowy /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

### Krok 7: Ustawienie uprawnień

```bash
chown -R www-data:www-data /var/www/portal-modelingowy
chmod -R 755 /var/www/portal-modelingowy
chmod -R 775 /var/www/portal-modelingowy/storage
chmod -R 775 /var/www/portal-modelingowy/bootstrap/cache
```

### Krok 8: Konfiguracja firewall

```bash
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw enable
```

## Aktualizacja aplikacji

Po wprowadzeniu zmian w kodzie:

```bash
cd /var/www/portal-modelingowy
git pull
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Monitoring i logi

- Logi aplikacji: `/var/www/portal-modelingowy/storage/logs/laravel.log`
- Logi Nginx: `/var/log/nginx/access.log` i `/var/log/nginx/error.log`
- Logi PHP-FPM: `/var/log/php8.2-fpm.log`

## Bezpieczeństwo

1. Ustaw `APP_DEBUG=false` w pliku `.env`
2. Ustaw silne hasła w bazie danych
3. Skonfiguruj SSL/HTTPS (Let's Encrypt)
4. Regularnie aktualizuj system i pakiety
5. Skonfiguruj backup bazy danych

## Troubleshooting

### Problem: 500 Internal Server Error
- Sprawdź uprawnienia do katalogów `storage` i `bootstrap/cache`
- Sprawdź logi: `tail -f storage/logs/laravel.log`

### Problem: Błąd połączenia z bazą danych
- Sprawdź konfigurację w `.env`
- Sprawdź czy MySQL działa: `systemctl status mysql`
- Sprawdź czy użytkownik ma uprawnienia

### Problem: Assets nie ładują się
- Uruchom `npm run build`
- Sprawdź uprawnienia do `public/build`
- Sprawdź konfigurację Nginx

