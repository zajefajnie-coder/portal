# Wypchnięcie na GitHub i wdrożenie na serwer
param(
    [Parameter(Mandatory=$true)]
    [string]$GitHubRepo
)

Write-Host "=== Wypchnięcie na GitHub i wdrożenie ===" -ForegroundColor Green

# Krok 1: Dodanie remote i push
Write-Host "`n=== KROK 1: Wypychanie na GitHub ===" -ForegroundColor Yellow

# Sprawdź czy remote już istnieje
$remoteExists = git remote get-url origin 2>$null
if ($LASTEXITCODE -ne 0) {
    Write-Host "Dodawanie remote GitHub..." -ForegroundColor Gray
    git remote add origin $GitHubRepo
} else {
    Write-Host "Aktualizacja remote GitHub..." -ForegroundColor Gray
    git remote set-url origin $GitHubRepo
}

# Zmiana nazwy brancha na main (jeśli potrzeba)
$currentBranch = git branch --show-current
if ($currentBranch -ne "main") {
    Write-Host "Zmiana brancha na main..." -ForegroundColor Gray
    git branch -M main
}

# Push na GitHub
Write-Host "Wypychanie kodu na GitHub..." -ForegroundColor Gray
git push -u origin main

if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Kod wypchnięty na GitHub!" -ForegroundColor Green
} else {
    Write-Host "✗ Błąd podczas wypychania na GitHub!" -ForegroundColor Red
    exit 1
}

# Krok 2: Wdrożenie na serwer
Write-Host "`n=== KROK 2: Wdrożenie na serwer ===" -ForegroundColor Yellow

$Server = "77.83.101.68"
$User = "root"
$Password = "Alicja2025##"
$AppDir = "/var/www/portal-modelingowy"

$securePassword = ConvertTo-SecureString $Password -AsPlainText -Force
$credential = New-Object System.Management.Automation.PSCredential($User, $securePassword)

Import-Module Posh-SSH

$session = New-SSHSession -ComputerName $Server -Credential $credential -AcceptKey
if (-not $session) {
    Write-Host "BŁĄD: Nie udało się połączyć z serwerem!" -ForegroundColor Red
    exit 1
}

function Invoke-Cmd {
    param([string]$Cmd, [string]$Desc = "")
    if ($Desc) { Write-Host "  $Desc" -ForegroundColor Gray }
    $result = Invoke-SSHCommand -SessionId $session.SessionId -Command $Cmd
    if ($result.Output) { Write-Host $result.Output -ForegroundColor DarkGray }
    if ($result.Error -and $result.ExitStatus -ne 0) { Write-Host "  BŁĄD: $($result.Error)" -ForegroundColor Red }
    return $result.ExitStatus -eq 0
}

# Klonowanie lub aktualizacja
Write-Host "`nPobieranie kodu z GitHub..." -ForegroundColor Cyan
$checkRepo = Invoke-SSHCommand -SessionId $session.SessionId -Command "test -d $AppDir/.git && echo 'EXISTS' || echo 'NEW'"
if ($checkRepo.Output -match "EXISTS") {
    Invoke-Cmd "cd $AppDir && git pull" "Aktualizacja kodu..."
} else {
    Invoke-Cmd "rm -rf $AppDir 2>/dev/null || true" "Czyszczenie..."
    Invoke-Cmd "git clone $GitHubRepo $AppDir" "Klonowanie repozytorium..."
}

# Konfiguracja aplikacji
Write-Host "`nKonfiguracja aplikacji..." -ForegroundColor Cyan
Invoke-Cmd "cd $AppDir && cp .env.example .env" "Kopiowanie .env..."
Invoke-Cmd "cd $AppDir && sed -i 's/APP_ENV=local/APP_ENV=production/' .env" "APP_ENV..."
Invoke-Cmd "cd $AppDir && sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env" "APP_DEBUG..."
Invoke-Cmd "cd $AppDir && sed -i 's/DB_DATABASE=.*/DB_DATABASE=krzyszton_port1/' .env" "DB_DATABASE..."
Invoke-Cmd "cd $AppDir && sed -i 's/DB_USERNAME=.*/DB_USERNAME=krzyszton_port1/' .env" "DB_USERNAME..."
Invoke-Cmd "cd $AppDir && sed -i 's|DB_PASSWORD=.*|DB_PASSWORD=Alicja2025##|' .env" "DB_PASSWORD..."
Invoke-Cmd "cd $AppDir && php artisan key:generate --force" "Generowanie klucza..."
Invoke-Cmd "cd $AppDir && composer install --no-dev --optimize-autoloader --no-interaction" "Composer..."
Invoke-Cmd "cd $AppDir && npm install" "npm install..."
Invoke-Cmd "cd $AppDir && npm run build" "npm build..."
Invoke-Cmd "cd $AppDir && php artisan storage:link" "Storage link..."
Invoke-Cmd "cd $AppDir && php artisan migrate --force" "Migracje..."
Invoke-Cmd "cd $AppDir && php artisan config:cache" "Config cache..."
Invoke-Cmd "cd $AppDir && php artisan route:cache" "Route cache..."
Invoke-Cmd "cd $AppDir && php artisan view:cache" "View cache..."

# Uprawnienia
Write-Host "`nUprawnienia..." -ForegroundColor Cyan
Invoke-Cmd "chown -R www-data:www-data $AppDir" "Właściciel..."
Invoke-Cmd "chmod -R 755 $AppDir" "Uprawnienia..."
Invoke-Cmd "chmod -R 775 $AppDir/storage" "Storage..."
Invoke-Cmd "chmod -R 775 $AppDir/bootstrap/cache" "Cache..."

# Nginx
Write-Host "`nKonfiguracja Nginx..." -ForegroundColor Cyan
$nginxConfig = @"
server {
    listen 80;
    server_name 77.83.101.68;
    root $AppDir/public;
    index index.php;
    charset utf-8;
    
    location / {
        try_files `$uri `$uri/ /index.php?`$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME `$realpath_root`$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
"@

Invoke-Cmd "cat > /tmp/nginx.conf << 'NGINXEOF'
$nginxConfig
NGINXEOF
cp /tmp/nginx.conf /etc/nginx/sites-available/portal-modelingowy" "Konfiguracja Nginx..."

Invoke-Cmd "ln -sf /etc/nginx/sites-available/portal-modelingowy /etc/nginx/sites-enabled/" "Aktywacja..."
Invoke-Cmd "nginx -t" "Test Nginx..."
Invoke-Cmd "systemctl enable php8.2-fpm && systemctl start php8.2-fpm" "PHP-FPM..."
Invoke-Cmd "systemctl reload nginx" "Nginx reload..."

Write-Host "`n=== Gotowe! ===" -ForegroundColor Green
Write-Host "Aplikacja dostępna: http://$Server" -ForegroundColor Cyan

Remove-SSHSession -SessionId $session.SessionId | Out-Null
