# Wdrożenie na serwer - Instrukcja krok po kroku

## Dane dostępowe:
- **Serwer:** michal@83.230.44.103
- **Hasło:** Alicja2025##

## Metoda 1: Ręczne wdrożenie przez SSH (ZALECANE)

### Krok 1: Połącz się z serwerem

```bash
ssh michal@83.230.44.103
# Wprowadź hasło: Alicja2025##
```

### Krok 2: Przygotowanie katalogu aplikacji

```bash
# Utwórz katalog jeśli nie istnieje
sudo mkdir -p /var/www/portal-modelingowy
sudo chown -R michal:michal /var/www/portal-modelingowy
cd /var/www/portal-modelingowy
```

### Krok 3: Przesłanie plików (z lokalnego komputera)

**Opcja A: Użyj WinSCP (Windows)**
1. Pobierz WinSCP: https://winscp.net/
2. Połącz się z serwerem (michal@83.230.44.103)
3. Przeciągnij pliki z lokalnego katalogu do `/var/www/portal-modelingowy`

**Opcja B: Użyj rsync z lokalnego komputera Linux/Mac**
```bash
# Z katalogu projektu lokalnie
rsync -avz --exclude 'node_modules' --exclude 'vendor' --exclude '.git' \
    --exclude 'storage/logs' --exclude '.env' \
    ./ michal@83.230.44.103:/var/www/portal-modelingowy/
```

**Opcja C: Git clone (jeśli masz repozytorium)**
```bash
# Na serwerze
cd /var/www
git clone [URL_REPOZYTORIUM] portal-modelingowy
cd portal-modelingowy
```

### Krok 4: Instalacja wymaganych pakietów (jeśli nie są zainstalowane)

```bash
# Aktualizacja systemu
sudo apt update && sudo apt upgrade -y

# Instalacja PHP i rozszerzeń
sudo apt install -y php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml \
    php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd

# Instalacja Composer
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
fi

# Instalacja Node.js i npm
if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_20.x | sudo bash -
    sudo apt install -y nodejs
fi

# Instalacja MySQL (jeśli nie jest zainstalowany)
sudo apt install -y mysql-server

# Instalacja Nginx (jeśli nie jest zainstalowany)
sudo apt install -y nginx

# Instalacja Git (jeśli nie jest zainstalowany)
sudo apt install -y git
```

### Krok 5: Konfiguracja bazy danych

```bash
sudo mysql -u root -p
```

W konsoli MySQL:
```sql
CREATE DATABASE IF NOT EXISTS krzyszton_port1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'krzyszton_port1'@'localhost' IDENTIFIED BY 'Alicja2025##';
GRANT ALL PRIVILEGES ON krzyszton_port1.* TO 'krzyszton_port1'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Krok 6: Konfiguracja aplikacji

```bash
cd /var/www/portal-modelingowy

# Skopiuj .env.example do .env
cp .env.example .env

# Edytuj .env (nano lub vim)
nano .env

# Ustaw w .env:
# APP_ENV=production
# APP_DEBUG=false
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=krzyszton_port1
# DB_USERNAME=krzyszton_port1
# DB_PASSWORD=Alicja2025##

# Generowanie klucza aplikacji
php artisan key:generate --force

# Instalacja zależności
composer install --no-dev --optimize-autoloader --no-interaction
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

### Krok 7: Ustawienie uprawnień

```bash
sudo chown -R www-data:www-data /var/www/portal-modelingowy
sudo chmod -R 755 /var/www/portal-modelingowy
sudo chmod -R 775 /var/www/portal-modelingowy/storage
sudo chmod -R 775 /var/www/portal-modelingowy/bootstrap/cache
```

### Krok 8: Konfiguracja Nginx

```bash
sudo nano /etc/nginx/sites-available/portal-modelingowy
```

Wklej konfigurację:
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
sudo ln -s /etc/nginx/sites-available/portal-modelingowy /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Krok 9: Firewall

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

## Metoda 2: Automatyczne wdrożenie (Linux/Mac z sshpass)

```bash
# Zainstaluj sshpass
sudo apt install sshpass  # Debian/Ubuntu
# lub
brew install sshpass  # Mac

# Uruchom skrypt
chmod +x deploy-with-password.sh
./deploy-with-password.sh
```

## Sprawdzenie działania

Po wdrożeniu sprawdź:
```bash
# Status serwisów
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql

# Logi aplikacji
tail -f /var/www/portal-modelingowy/storage/logs/laravel.log

# Test strony
curl http://83.230.44.103
```

## Rozwiązywanie problemów

### Błąd uprawnień
```bash
sudo chown -R www-data:www-data /var/www/portal-modelingowy
sudo chmod -R 775 /var/www/portal-modelingowy/storage
```

### Błąd połączenia z bazą danych
```bash
# Sprawdź czy MySQL działa
sudo systemctl status mysql

# Sprawdź użytkownika bazy danych
sudo mysql -u root -p
SHOW GRANTS FOR 'krzyszton_port1'@'localhost';
```

### Assets nie ładują się
```bash
cd /var/www/portal-modelingowy
npm run build
sudo chown -R www-data:www-data public/build
```
