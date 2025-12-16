# Instrukcje wdrożenia na nowy serwer root@77.83.101.68

## Status wdrożenia
- ✅ Połączenie SSH działa (root@77.83.101.68)
- ✅ Repozytoria naprawione
- ✅ PHP 8.2 zainstalowany
- ✅ Composer zainstalowany
- ✅ Node.js v18.20.4 zainstalowany
- ✅ MariaDB zainstalowany
- ✅ Nginx zainstalowany
- ✅ Apache zatrzymany
- ✅ Baza danych skonfigurowana
- ✅ Katalog aplikacji utworzony
- ⏳ Oczekiwanie na przesłanie plików

## KROK 1: Naprawa repozytoriów

Połącz się przez SSH i wykonaj:

```bash
ssh root@77.83.101.68
# Hasło: Alicja2025##

# Sprawdź konfigurację repozytoriów
cat /etc/apt/sources.list

# Aktualizacja z naprawą
apt update --fix-missing

# Jeśli nadal są problemy, użyj alternatywnych mirrorów
sed -i 's|deb.debian.org|mirror.debian.org|g' /etc/apt/sources.list
apt update
```

## KROK 2: Instalacja wymaganych pakietów

```bash
# PHP i rozszerzenia
apt install -y php php-fpm php-mysql php-xml php-mbstring php-curl php-zip php-gd

# Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# MySQL
apt install -y mysql-server

# Nginx
apt install -y nginx
```

## KROK 3: Konfiguracja bazy danych

```bash
mysql -e "CREATE DATABASE IF NOT EXISTS krzyszton_port1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS 'krzyszton_port1'@'localhost' IDENTIFIED BY 'Alicja2025##';"
mysql -e "GRANT ALL PRIVILEGES ON krzyszton_port1.* TO 'krzyszton_port1'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"
```

## KROK 4: Przygotowanie katalogu aplikacji

```bash
mkdir -p /var/www/portal-modelingowy
chown -R www-data:www-data /var/www/portal-modelingowy
```

## KROK 5: Przesłanie plików

### Opcja A: WinSCP (Windows)
1. Połącz się: root@77.83.101.68, hasło: Alicja2025##
2. Przeciągnij do `/var/www/portal-modelingowy/`:
   - app/
   - bootstrap/
   - config/
   - database/
   - public/
   - resources/
   - routes/
   - artisan
   - composer.json
   - package.json
   - vite.config.js
   - tailwind.config.js
   - postcss.config.js
   - .env.example

### Opcja B: SCP
```bash
scp -r app bootstrap config database public resources routes artisan composer.json package.json vite.config.js tailwind.config.js postcss.config.js .env.example root@77.83.101.68:/var/www/portal-modelingowy/
```

## KROK 6: Konfiguracja aplikacji

```bash
cd /var/www/portal-modelingowy

# Konfiguracja .env
cp .env.example .env
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=krzyszton_port1/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=krzyszton_port1/' .env
sed -i 's|DB_PASSWORD=.*|DB_PASSWORD=Alicja2025##|' .env

# Generowanie klucza
php artisan key:generate --force

# Instalacja zależności
composer install --no-dev --optimize-autoloader --no-interaction
npm install
npm run build

# Link symboliczny storage
php artisan storage:link

# Migracje
php artisan migrate --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## KROK 7: Uprawnienia

```bash
chown -R www-data:www-data /var/www/portal-modelingowy
chmod -R 755 /var/www/portal-modelingowy
chmod -R 775 /var/www/portal-modelingowy/storage
chmod -R 775 /var/www/portal-modelingowy/bootstrap/cache
```

## KROK 8: Konfiguracja Nginx

```bash
nano /etc/nginx/sites-available/portal-modelingowy
```

Wklej:

```nginx
server {
    listen 80;
    server_name 77.83.101.68;
    root /var/www/portal-modelingowy/public;
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

Aktywuj:

```bash
ln -s /etc/nginx/sites-available/portal-modelingowy /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

## KROK 9: Firewall

```bash
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable
```

## Sprawdzenie

```bash
curl http://77.83.101.68
```

Lub otwórz w przeglądarce: http://77.83.101.68

