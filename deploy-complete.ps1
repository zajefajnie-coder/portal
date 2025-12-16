# Pełne wdrożenie - przesłanie plików i konfiguracja
Import-Module Posh-SSH

$Server = "77.83.101.68"
$User = "root"
$Password = "Alicja2025##"
$AppDir = "/var/www/portal-modelingowy"

$securePassword = ConvertTo-SecureString $Password -AsPlainText -Force
$credential = New-Object System.Management.Automation.PSCredential($User, $securePassword)

Write-Host "=== Pełne wdrożenie Portal Modelingowy ===" -ForegroundColor Green
Write-Host "Serwer: $User@$Server" -ForegroundColor Cyan
Write-Host ""

# Połączenie SSH
$session = New-SSHSession -ComputerName $Server -Credential $credential -AcceptKey
if (-not $session) {
    Write-Host "BŁĄD: Nie udało się połączyć!" -ForegroundColor Red
    exit 1
}

Write-Host "Połączono z serwerem!" -ForegroundColor Green

function Invoke-Cmd {
    param([string]$Cmd, [string]$Desc = "")
    if ($Desc) { Write-Host "`n$Desc" -ForegroundColor Yellow }
    $result = Invoke-SSHCommand -SessionId $session.SessionId -Command $Cmd
    if ($result.Output) { Write-Host $result.Output -ForegroundColor Gray }
    if ($result.Error -and $result.ExitStatus -ne 0) { Write-Host "BŁĄD: $($result.Error)" -ForegroundColor Red }
    return $result.ExitStatus -eq 0
}

# KROK 1: Przesłanie plików przez SFTP
Write-Host "`n=== KROK 1: Przesyłanie plików ===" -ForegroundColor Green
$sftpSession = New-SFTPSession -ComputerName $Server -Credential $credential -AcceptKey

if ($sftpSession) {
    Write-Host "Przesyłanie plików projektu..." -ForegroundColor Yellow
    
    $localPath = Get-Location
    $items = @("app", "bootstrap", "config", "database", "public", "resources", "routes", "artisan", "composer.json", "package.json", "vite.config.js", "tailwind.config.js", "postcss.config.js", ".env.example")
    $uploaded = 0
    
    foreach ($item in $items) {
        $localItem = Join-Path $localPath $item
        if (Test-Path $localItem) {
            try {
                if (Test-Path $localItem -PathType Container) {
                    Get-ChildItem -Path $localItem -Recurse -File | ForEach-Object {
                        $relPath = $_.FullName.Replace("$localPath\", "").Replace("\", "/")
                        $remotePath = "$AppDir/$relPath"
                        $remoteDir = Split-Path $remotePath -Parent
                        try { 
                            $sftp = Get-SFTPSession -SessionId $sftpSession.SessionId
                            $sftp.Session.CreateDirectory($remoteDir) | Out-Null
                        } catch {}
                        $sftp.Session.PutFile($_.FullName, $remotePath) | Out-Null
                        $uploaded++
                    }
                } else {
                    $remotePath = "$AppDir/$item"
                    $sftp = Get-SFTPSession -SessionId $sftpSession.SessionId
                    $sftp.Session.PutFile($localItem, $remotePath) | Out-Null
                    $uploaded++
                }
                Write-Host "  ✓ $item" -ForegroundColor Gray
            } catch {
                Write-Host "  ✗ Błąd: $item - $($_.Exception.Message)" -ForegroundColor Red
            }
        }
    }
    
    Remove-SFTPSession -SessionId $sftpSession.SessionId
    Write-Host "`nPrzesłano $uploaded plików/katalogów" -ForegroundColor Green
} else {
    Write-Host "BŁĄD: Nie udało się utworzyć sesji SFTP!" -ForegroundColor Red
    Remove-SSHSession -SessionId $session.SessionId | Out-Null
    exit 1
}

# KROK 2: Konfiguracja .env
Write-Host "`n=== KROK 2: Konfiguracja .env ===" -ForegroundColor Green
Invoke-Cmd "cd $AppDir && cp .env.example .env" "Kopiowanie .env.example..."
Invoke-Cmd "cd $AppDir && sed -i 's/APP_ENV=local/APP_ENV=production/' .env" "Ustawianie APP_ENV..."
Invoke-Cmd "cd $AppDir && sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env" "Ustawianie APP_DEBUG..."
Invoke-Cmd "cd $AppDir && sed -i 's/DB_DATABASE=.*/DB_DATABASE=krzyszton_port1/' .env" "Ustawianie DB_DATABASE..."
Invoke-Cmd "cd $AppDir && sed -i 's/DB_USERNAME=.*/DB_USERNAME=krzyszton_port1/' .env" "Ustawianie DB_USERNAME..."
Invoke-Cmd "cd $AppDir && sed -i 's|DB_PASSWORD=.*|DB_PASSWORD=Alicja2025##|' .env" "Ustawianie DB_PASSWORD..."

# KROK 3: Generowanie klucza aplikacji
Write-Host "`n=== KROK 3: Generowanie klucza aplikacji ===" -ForegroundColor Green
Invoke-Cmd "cd $AppDir && php artisan key:generate --force" "Generowanie klucza..."

# KROK 4: Instalacja zależności Composer
Write-Host "`n=== KROK 4: Instalacja zależności Composer ===" -ForegroundColor Green
Invoke-Cmd "cd $AppDir && composer install --no-dev --optimize-autoloader --no-interaction" "Instalacja Composer..."

# KROK 5: Instalacja zależności npm i budowa
Write-Host "`n=== KROK 5: Instalacja npm i budowa assets ===" -ForegroundColor Green
Invoke-Cmd "cd $AppDir && npm install" "Instalacja npm..."
Invoke-Cmd "cd $AppDir && npm run build" "Budowa assets..."

# KROK 6: Link symboliczny storage
Write-Host "`n=== KROK 6: Link symboliczny storage ===" -ForegroundColor Green
Invoke-Cmd "cd $AppDir && php artisan storage:link" "Tworzenie linku storage..."

# KROK 7: Migracje bazy danych
Write-Host "`n=== KROK 7: Migracje bazy danych ===" -ForegroundColor Green
Invoke-Cmd "cd $AppDir && php artisan migrate --force" "Uruchamianie migracji..."

# KROK 8: Cache Laravel
Write-Host "`n=== KROK 8: Optymalizacja cache ===" -ForegroundColor Green
Invoke-Cmd "cd $AppDir && php artisan config:cache" "Cache konfiguracji..."
Invoke-Cmd "cd $AppDir && php artisan route:cache" "Cache routingu..."
Invoke-Cmd "cd $AppDir && php artisan view:cache" "Cache widoków..."

# KROK 9: Uprawnienia
Write-Host "`n=== KROK 9: Uprawnienia ===" -ForegroundColor Green
Invoke-Cmd "chown -R www-data:www-data $AppDir" "Ustawianie właściciela..."
Invoke-Cmd "chmod -R 755 $AppDir" "Uprawnienia katalogów..."
Invoke-Cmd "chmod -R 775 $AppDir/storage" "Uprawnienia storage..."
Invoke-Cmd "chmod -R 775 $AppDir/bootstrap/cache" "Uprawnienia cache..."

# KROK 10: Konfiguracja Nginx
Write-Host "`n=== KROK 10: Konfiguracja Nginx ===" -ForegroundColor Green

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

# Zapisanie konfiguracji Nginx
$nginxConfigEscaped = $nginxConfig -replace "`$", "\`$" -replace '"', '\"'
Invoke-Cmd "echo '$nginxConfigEscaped' > /etc/nginx/sites-available/portal-modelingowy" "Tworzenie konfiguracji Nginx..."
Invoke-Cmd "ln -sf /etc/nginx/sites-available/portal-modelingowy /etc/nginx/sites-enabled/" "Aktywacja konfiguracji..."
Invoke-Cmd "nginx -t" "Test konfiguracji Nginx..."

# KROK 11: Uruchomienie PHP-FPM i Nginx
Write-Host "`n=== KROK 11: Uruchomienie serwisów ===" -ForegroundColor Green
Invoke-Cmd "systemctl enable php8.2-fpm" "Włączanie PHP-FPM..."
Invoke-Cmd "systemctl start php8.2-fpm" "Uruchamianie PHP-FPM..."
Invoke-Cmd "systemctl enable nginx" "Włączanie Nginx..."
Invoke-Cmd "systemctl reload nginx" "Przeładowanie Nginx..."

# KROK 12: Firewall
Write-Host "`n=== KROK 12: Konfiguracja firewall ===" -ForegroundColor Green
Invoke-Cmd "ufw allow 22/tcp" "Port SSH..."
Invoke-Cmd "ufw allow 80/tcp" "Port HTTP..."
Invoke-Cmd "ufw allow 443/tcp" "Port HTTPS..."
Invoke-Cmd "ufw --force enable" "Włączanie firewall..."

Write-Host "`n=== Wdrożenie zakończone! ===" -ForegroundColor Green
Write-Host "Aplikacja dostępna pod adresem: http://$Server" -ForegroundColor Cyan
Write-Host "`nSprawdź status:" -ForegroundColor Yellow
Write-Host "  systemctl status nginx" -ForegroundColor White
Write-Host "  systemctl status php8.2-fpm" -ForegroundColor White

Remove-SSHSession -SessionId $session.SessionId | Out-Null
