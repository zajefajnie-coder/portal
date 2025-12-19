# Wdrożenie z GitHub na serwer
Import-Module Posh-SSH

$Server = "77.83.101.68"
$User = "root"
$Password = "Alicja2025##"
$AppDir = "/var/www/portal-modelingowy"
$GitHubRepo = Read-Host "Podaj URL repozytorium GitHub (np. https://github.com/user/repo.git)"

$securePassword = ConvertTo-SecureString $Password -AsPlainText -Force
$credential = New-Object System.Management.Automation.PSCredential($User, $securePassword)

Write-Host "=== Wdrożenie z GitHub ===" -ForegroundColor Green

$session = New-SSHSession -ComputerName $Server -Credential $credential -AcceptKey
if (-not $session) {
    Write-Host "BŁĄD: Nie udało się połączyć!" -ForegroundColor Red
    exit 1
}

function Invoke-Cmd {
    param([string]$Cmd, [string]$Desc = "")
    if ($Desc) { Write-Host "`n$Desc" -ForegroundColor Yellow }
    $result = Invoke-SSHCommand -SessionId $session.SessionId -Command $Cmd
    if ($result.Output) { Write-Host $result.Output -ForegroundColor Gray }
    if ($result.Error -and $result.ExitStatus -ne 0) { Write-Host "BŁĄD: $($result.Error)" -ForegroundColor Red }
    return $result.ExitStatus -eq 0
}

# Krok 1: Klonowanie lub aktualizacja repozytorium
Write-Host "`n=== KROK 1: Pobieranie kodu z GitHub ===" -ForegroundColor Green
if (Invoke-Cmd "test -d $AppDir/.git && echo 'EXISTS' || echo 'NEW'") {
    $exists = Invoke-SSHCommand -SessionId $session.SessionId -Command "test -d $AppDir/.git && echo 'EXISTS' || echo 'NEW'"
    if ($exists.Output -match "EXISTS") {
        Invoke-Cmd "cd $AppDir && git pull" "Aktualizacja kodu..."
    } else {
        Invoke-Cmd "rm -rf $AppDir/* $AppDir/.* 2>/dev/null || true" "Czyszczenie katalogu..."
        Invoke-Cmd "git clone $GitHubRepo $AppDir" "Klonowanie repozytorium..."
    }
} else {
    Invoke-Cmd "rm -rf $AppDir/* $AppDir/.* 2>/dev/null || true" "Czyszczenie katalogu..."
    Invoke-Cmd "git clone $GitHubRepo $AppDir" "Klonowanie repozytorium..."
}

# Krok 2: Konfiguracja .env
Write-Host "`n=== KROK 2: Konfiguracja .env ===" -ForegroundColor Green
Invoke-Cmd "cd $AppDir && cp .env.example .env" "Kopiowanie .env.example..."
Invoke-Cmd "cd $AppDir && sed -i 's/APP_ENV=local/APP_ENV=production/' .env" "Ustawianie APP_ENV..."
Invoke-Cmd "cd $AppDir && sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env" "Ustawianie APP_DEBUG..."
Invoke-Cmd "cd $AppDir && sed -i 's/DB_DATABASE=.*/DB_DATABASE=krzyszton_port1/' .env" "Ustawianie DB_DATABASE..."
Invoke-Cmd "cd $AppDir && sed -i 's/DB_USERNAME=.*/DB_USERNAME=krzyszton_port1/' .env" "Ustawianie DB_USERNAME..."
Invoke-Cmd "cd $AppDir && sed -i 's|DB_PASSWORD=.*|DB_PASSWORD=Alicja2025##|' .env" "Ustawianie DB_PASSWORD..."

# Krok 3: Generowanie klucza
Write-Host "`n=== KROK 3: Generowanie klucza ===" -ForegroundColor Green
Invoke-Cmd "cd $AppDir && php artisan key:generate --force" "Generowanie klucza..."

# Krok 4: Instalacja zależności
Write-Host "`n=== KROK 4: Instalacja zależności ===" -ForegroundColor Green
Invoke-Cmd "cd $AppDir && composer install --no-dev --optimize-autoloader --no-interaction" "Instalacja Composer..."
Invoke-Cmd "cd $AppDir && npm install" "Instalacja npm..."
Invoke-Cmd "cd $AppDir && npm run build" "Budowa assets..."

# Krok 5: Storage i migracje
Write-Host "`n=== KROK 5: Storage i migracje ===" -ForegroundColor Green
Invoke-Cmd "cd $AppDir && php artisan storage:link" "Link storage..."
Invoke-Cmd "cd $AppDir && php artisan migrate --force" "Migracje..."

# Krok 6: Cache
Write-Host "`n=== KROK 6: Cache ===" -ForegroundColor Green
Invoke-Cmd "cd $AppDir && php artisan config:cache" "Cache konfiguracji..."
Invoke-Cmd "cd $AppDir && php artisan route:cache" "Cache routingu..."
Invoke-Cmd "cd $AppDir && php artisan view:cache" "Cache widoków..."

# Krok 7: Uprawnienia
Write-Host "`n=== KROK 7: Uprawnienia ===" -ForegroundColor Green
Invoke-Cmd "chown -R www-data:www-data $AppDir" "Właściciel..."
Invoke-Cmd "chmod -R 755 $AppDir" "Uprawnienia..."
Invoke-Cmd "chmod -R 775 $AppDir/storage" "Uprawnienia storage..."
Invoke-Cmd "chmod -R 775 $AppDir/bootstrap/cache" "Uprawnienia cache..."

# Krok 8: Konfiguracja Nginx
Write-Host "`n=== KROK 8: Konfiguracja Nginx ===" -ForegroundColor Green
$nginxConfig = "server {`n    listen 80;`n    server_name 77.83.101.68;`n    root $AppDir/public;`n    index index.php;`n    charset utf-8;`n    `n    location / {`n        try_files `$uri `$uri/ /index.php?`$query_string;`n    }`n    `n    location = /favicon.ico { access_log off; log_not_found off; }`n    location = /robots.txt  { access_log off; log_not_found off; }`n    `n    error_page 404 /index.php;`n    `n    location ~ \.php`$ {`n        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;`n        fastcgi_param SCRIPT_FILENAME `$realpath_root`$fastcgi_script_name;`n        include fastcgi_params;`n    }`n    `n    location ~ /\.(?!well-known).* {`n        deny all;`n    }`n}"

Invoke-Cmd "cat > /tmp/nginx.conf << 'NGINXEOF'
$nginxConfig
NGINXEOF
cp /tmp/nginx.conf /etc/nginx/sites-available/portal-modelingowy" "Tworzenie konfiguracji Nginx..."

Invoke-Cmd "ln -sf /etc/nginx/sites-available/portal-modelingowy /etc/nginx/sites-enabled/" "Aktywacja..."
Invoke-Cmd "nginx -t" "Test konfiguracji..."
Invoke-Cmd "systemctl enable php8.2-fpm && systemctl start php8.2-fpm" "PHP-FPM..."
Invoke-Cmd "systemctl reload nginx" "Przeładowanie Nginx..."

Write-Host "`n=== Wdrożenie zakończone! ===" -ForegroundColor Green
Write-Host "Aplikacja: http://$Server" -ForegroundColor Cyan

Remove-SSHSession -SessionId $session.SessionId | Out-Null
