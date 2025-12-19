# Szybkie wdrożenie na serwer Debian (83.230.44.103)

## Opcja 1: Automatyczne wdrożenie (rsync)

### Wymagania lokalne:
- Zainstalowany `rsync` i `ssh`
- Dostęp SSH do serwera (klucze SSH)

### Wykonanie:

```bash
chmod +x deploy.sh
./deploy.sh root@83.230.44.103
```

## Opcja 2: Ręczne wdrożenie przez SSH

### Krok 1: Połącz się z serwerem

```bash
ssh root@83.230.44.103
```

### Krok 2: Zainstaluj wymagane pakiety

```bash
apt update && apt upgrade -y
apt install -y php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml \
    php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd \
    composer nodejs npm nginx mysql-server git
```

### Krok 3: Skonfiguruj bazę danych

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

### Krok 4: Skonfiguruj aplikację

```bash
cd /var/www
git clone [URL_TWOJEGO_REPO] portal-modelingowy
cd portal-modelingowy

# Skopiuj .env.example do .env i edytuj
cp .env.example .env
nano .env

# Ustaw w .env:
# APP_ENV=production
# APP_DEBUG=false
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=krzyszton_port1
# DB_USERNAME=krzyszton_port1
# DB_PASSWORD=Alicja2025##

# Instalacja i konfiguracja
php artisan key:generate
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan storage:link
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Uprawnienia
chown -R www-data:www-data /var/www/portal-modelingowy
chmod -R 755 /var/www/portal-modelingowy
chmod -R 775 storage bootstrap/cache
```

### Krok 5: Konfiguracja Nginx

Utwórz `/etc/nginx/sites-available/portal-modelingowy`:

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

Aktywuj:
```bash
ln -s /etc/nginx/sites-available/portal-modelingowy /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

### Krok 6: Firewall

```bash
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw enable
```

## Status repozytorium Git

✅ Repozytorium Git zostało zainicjalizowane
✅ Wszystkie pliki zostały dodane do Git
✅ Utworzono commit: "Initial commit - Portal Modelingowy"
✅ Utworzono commit: "Dodano skrypty wdrożenia na serwer Debian"

## Następne kroki

1. **Utwórz repozytorium zdalne** (GitHub, GitLab, Bitbucket)
2. **Dodaj remote**:
   ```bash
   git remote add origin [URL_REPOZYTORIUM]
   git push -u origin master
   ```
3. **Na serwerze** sklonuj repozytorium:
   ```bash
   cd /var/www
   git clone [URL_REPOZYTORIUM] portal-modelingowy
   ```

## Aktualizacja aplikacji

Po wprowadzeniu zmian:

```bash
# Lokalnie
git add .
git commit -m "Opis zmian"
git push

# Na serwerze
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

## Wsparcie

Szczegółowe instrukcje znajdują się w pliku `DEPLOYMENT.md`

