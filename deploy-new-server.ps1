# Pełne wdrożenie na nowy serwer root@77.83.101.68
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

$session = New-SSHSession -ComputerName $Server -Credential $credential -AcceptKey
if (-not $session) {
    Write-Host "BŁĄD: Nie udało się połączyć!" -ForegroundColor Red
    exit 1
}

Write-Host "Połączono!" -ForegroundColor Green

function Invoke-Cmd {
    param([string]$Cmd, [string]$Desc = "")
    if ($Desc) { Write-Host "`n$Desc" -ForegroundColor Yellow }
    $result = Invoke-SSHCommand -SessionId $session.SessionId -Command $Cmd
    if ($result.Output) { Write-Host $result.Output -ForegroundColor Gray }
    if ($result.Error -and $result.ExitStatus -ne 0) { Write-Host "BŁĄD: $($result.Error)" -ForegroundColor Red }
    return $result.ExitStatus -eq 0
}

# Krok 1: Aktualizacja systemu
Write-Host "`n=== KROK 1: Aktualizacja systemu ===" -ForegroundColor Green
Invoke-Cmd "apt update -qq" "Aktualizacja listy pakietów..."

# Krok 2: Instalacja PHP
Write-Host "`n=== KROK 2: Instalacja PHP ===" -ForegroundColor Green
Invoke-Cmd "apt install -y php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd" "Instalacja PHP i rozszerzeń..."

# Krok 3: Instalacja Composer
Write-Host "`n=== KROK 3: Instalacja Composer ===" -ForegroundColor Green
Invoke-Cmd "curl -sS https://getcomposer.org/installer | php" "Pobieranie Composer..."
Invoke-Cmd "mv composer.phar /usr/local/bin/composer" "Instalacja Composer..."
Invoke-Cmd "chmod +x /usr/local/bin/composer" "Uprawnienia Composer..."
Invoke-Cmd "composer --version" "Weryfikacja Composer..."

# Krok 4: Instalacja Node.js
Write-Host "`n=== KROK 4: Instalacja Node.js ===" -ForegroundColor Green
Invoke-Cmd "curl -fsSL https://deb.nodesource.com/setup_20.x | bash -" "Dodawanie repozytorium Node.js..."
Invoke-Cmd "apt install -y nodejs" "Instalacja Node.js..."
Invoke-Cmd "node -v" "Weryfikacja Node.js..."

# Krok 5: Instalacja MySQL
Write-Host "`n=== KROK 5: Instalacja MySQL ===" -ForegroundColor Green
Invoke-Cmd "apt install -y mysql-server" "Instalacja MySQL..."

# Krok 6: Instalacja Nginx
Write-Host "`n=== KROK 6: Instalacja Nginx ===" -ForegroundColor Green
Invoke-Cmd "apt install -y nginx" "Instalacja Nginx..."

# Krok 7: Konfiguracja bazy danych
Write-Host "`n=== KROK 7: Konfiguracja bazy danych ===" -ForegroundColor Green
$dbScript = "CREATE DATABASE IF NOT EXISTS krzyszton_port1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER IF NOT EXISTS 'krzyszton_port1'@'localhost' IDENTIFIED BY 'Alicja2025##'; GRANT ALL PRIVILEGES ON krzyszton_port1.* TO 'krzyszton_port1'@'localhost'; FLUSH PRIVILEGES;"
Invoke-Cmd "mysql -e `"$dbScript`"" "Konfiguracja bazy danych..."

# Krok 8: Utworzenie katalogu aplikacji
Write-Host "`n=== KROK 8: Przygotowanie katalogu ===" -ForegroundColor Green
Invoke-Cmd "mkdir -p $AppDir" "Tworzenie katalogu aplikacji..."
Invoke-Cmd "chown -R www-data:www-data $AppDir" "Ustawianie uprawnień..."

# Krok 9: Przesłanie plików przez SFTP
Write-Host "`n=== KROK 9: Przesyłanie plików ===" -ForegroundColor Green
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
                Write-Host "  Przesłano: $item" -ForegroundColor Gray
            } catch {
                Write-Host "  Błąd: $item" -ForegroundColor Red
            }
        }
    }
    
    Remove-SFTPSession -SessionId $sftpSession.SessionId
    Write-Host "Przesłano $uploaded plików/katalogów" -ForegroundColor Green
} else {
    Write-Host "BŁĄD: Nie udało się utworzyć sesji SFTP!" -ForegroundColor Red
    Write-Host "Prześlij pliki ręcznie przez WinSCP do $AppDir" -ForegroundColor Yellow
}

# Krok 10: Konfiguracja aplikacji
Write-Host "`n=== KROK 10: Konfiguracja aplikacji ===" -ForegroundColor Green

$setupCommands = @(
    "cd $AppDir",
    "cp .env.example .env",
    "sed -i 's/APP_ENV=local/APP_ENV=production/' .env",
    "sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env",
    "sed -i 's/DB_DATABASE=.*/DB_DATABASE=krzyszton_port1/' .env",
    "sed -i 's/DB_USERNAME=.*/DB_USERNAME=krzyszton_port1/' .env",
    "sed -i 's|DB_PASSWORD=.*|DB_PASSWORD=Alicja2025##|' .env",
    "php artisan key:generate --force",
    "composer install --no-dev --optimize-autoloader --no-interaction",
    "npm install",
    "npm run build",
    "php artisan storage:link",
    "php artisan migrate --force",
    "php artisan config:cache",
    "php artisan route:cache",
    "php artisan view:cache"
)

foreach ($cmd in $setupCommands) {
    Invoke-Cmd $cmd
}

# Krok 11: Uprawnienia
Write-Host "`n=== KROK 11: Uprawnienia ===" -ForegroundColor Green
Invoke-Cmd "chown -R www-data:www-data $AppDir" "Ustawianie właściciela..."
Invoke-Cmd "chmod -R 755 $AppDir" "Uprawnienia katalogów..."
Invoke-Cmd "chmod -R 775 $AppDir/storage" "Uprawnienia storage..."
Invoke-Cmd "chmod -R 775 $AppDir/bootstrap/cache" "Uprawnienia cache..."

# Krok 12: Konfiguracja Nginx
Write-Host "`n=== KROK 12: Konfiguracja Nginx ===" -ForegroundColor Green

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

Invoke-Cmd "echo '$nginxConfig' > /etc/nginx/sites-available/portal-modelingowy" "Tworzenie konfiguracji Nginx..."
Invoke-Cmd "ln -sf /etc/nginx/sites-available/portal-modelingowy /etc/nginx/sites-enabled/" "Aktywacja konfiguracji..."
Invoke-Cmd "nginx -t" "Test konfiguracji Nginx..."
Invoke-Cmd "systemctl reload nginx" "Przeładowanie Nginx..."

# Krok 13: Firewall
Write-Host "`n=== KROK 13: Firewall ===" -ForegroundColor Green
Invoke-Cmd "ufw allow 22/tcp" "Port SSH..."
Invoke-Cmd "ufw allow 80/tcp" "Port HTTP..."
Invoke-Cmd "ufw allow 443/tcp" "Port HTTPS..."
Invoke-Cmd "ufw --force enable" "Włączanie firewall..."

Write-Host "`n=== Wdrożenie zakończone! ===" -ForegroundColor Green
Write-Host "Aplikacja dostępna pod adresem: http://$Server" -ForegroundColor Cyan

Remove-SSHSession -SessionId $session.SessionId | Out-Null

