# Instrukcje wdrożenia - Portal Modelingowy

## Status:
- ✅ Połączenie SSH działa (michal@83.230.44.103)
- ✅ Katalog aplikacji utworzony: /home/michal/portal-modelingowy
- ✅ Composer pobrany lokalnie
- ⚠️ Użytkownik michal NIE MA uprawnień sudo
- ⚠️ Node.js nie jest zainstalowany (wymaga root)
- ⚠️ Nginx nie jest zainstalowany (wymaga root)

## KROK 1: Przesłanie plików na serwer

### Opcja A: WinSCP (Windows) - ZALECANE
1. Pobierz WinSCP: https://winscp.net/
2. Połącz się:
   - Host: 83.230.44.103
   - User: michal
   - Password: Alicja2025##
3. Przeciągnij następujące katalogi/pliki do `/home/michal/portal-modelingowy/`:
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

### Opcja B: SCP (Linux/Mac)
```bash
scp -r app bootstrap config database public resources routes artisan composer.json package.json vite.config.js tailwind.config.js postcss.config.js .env.example michal@83.230.44.103:/home/michal/portal-modelingowy/
```

## KROK 2: Konfiguracja aplikacji (automatyczna)

Po przesłaniu plików, uruchom:
```powershell
.\upload-and-deploy.ps1
```

Lub ręcznie przez SSH:
```bash
ssh michal@83.230.44.103
cd /home/michal/portal-modelingowy
cp .env.example .env
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=krzyszton_port1/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=krzyszton_port1/' .env
sed -i 's|DB_PASSWORD=.*|DB_PASSWORD=Alicja2025##|' .env
php artisan key:generate --force
php composer.phar install --no-dev --optimize-autoloader --no-interaction
php artisan storage:link
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## KROK 3: Instalacja Node.js (wymaga root)

Połącz się jako root (jeśli masz dostęp) lub poproś administratora:
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo bash -
sudo apt install -y nodejs
```

Następnie na serwerze jako michal:
```bash
cd /home/michal/portal-modelingowy
npm install
npm run build
```

## KROK 4: Konfiguracja Nginx (wymaga root)

Poproś administratora o wykonanie:

```bash
# Instalacja Nginx
sudo apt install -y nginx php8.4-fpm

# Utworzenie konfiguracji
sudo nano /etc/nginx/sites-available/portal-modelingowy
```

Wklej:
```nginx
server {
    listen 80;
    server_name 83.230.44.103;
    root /home/michal/portal-modelingowy/public;
    index index.php;
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
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
sudo ln -s /etc/nginx/sites-available/portal-modelingowy /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## KROK 5: Uprawnienia (wymaga root)

```bash
sudo chown -R www-data:www-data /home/michal/portal-modelingowy
sudo chmod -R 755 /home/michal/portal-modelingowy
sudo chmod -R 775 /home/michal/portal-modelingowy/storage
sudo chmod -R 775 /home/michal/portal-modelingowy/bootstrap/cache
```

## Alternatywa: Przeniesienie do /var/www (wymaga root)

Jeśli chcesz przenieść aplikację do standardowej lokalizacji:
```bash
sudo mv /home/michal/portal-modelingowy /var/www/portal-modelingowy
sudo chown -R www-data:www-data /var/www/portal-modelingowy
```

I zaktualizuj konfigurację Nginx (root w /etc/nginx/sites-available/portal-modelingowy).

## Sprawdzenie działania

```bash
curl http://83.230.44.103
```

Lub otwórz w przeglądarce: http://83.230.44.103

