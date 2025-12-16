# Pełne automatyczne wdrożenie
Import-Module Posh-SSH

$Server = "83.230.44.103"
$User = "michal"
$Password = "Alicja2025##"
$AppDir = "/var/www/portal-modelingowy"

$securePassword = ConvertTo-SecureString $Password -AsPlainText -Force
$credential = New-Object System.Management.Automation.PSCredential($User, $securePassword)

Write-Host "=== Automatyczne wdrożenie Portal Modelingowy ===" -ForegroundColor Green
Write-Host "Serwer: $User@$Server" -ForegroundColor Cyan
Write-Host ""

# Połączenie
Write-Host "Łączenie z serwerem..." -ForegroundColor Yellow
$session = New-SSHSession -ComputerName $Server -Credential $credential -AcceptKey

if (-not $session) {
    Write-Host "BŁĄD: Nie udało się połączyć z serwerem!" -ForegroundColor Red
    exit 1
}

Write-Host "Połączono pomyślnie!" -ForegroundColor Green

function Invoke-RemoteCommand {
    param([string]$Command, [string]$Description = "")
    if ($Description) {
        Write-Host "`n$Description" -ForegroundColor Yellow
    }
    $result = Invoke-SSHCommand -SessionId $session.SessionId -Command $Command
    if ($result.ExitStatus -eq 0) {
        if ($result.Output) {
            Write-Host $result.Output -ForegroundColor Gray
        }
        return $true
    } else {
        Write-Host "BŁĄD: $($result.Error)" -ForegroundColor Red
        return $false
    }
}

# Krok 1: Instalacja wymaganych pakietów
Write-Host "`n=== KROK 1: Instalacja wymaganych pakietów ===" -ForegroundColor Green

Invoke-RemoteCommand "sudo apt update" "Aktualizacja listy pakietów..."

# Instalacja Composer
Invoke-RemoteCommand "if ! command -v composer &> /dev/null; then curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer && sudo chmod +x /usr/local/bin/composer; fi" "Instalacja Composer..."

# Instalacja Node.js
Invoke-RemoteCommand "if ! command -v node &> /dev/null; then curl -fsSL https://deb.nodesource.com/setup_20.x | sudo bash - && sudo apt install -y nodejs; fi" "Instalacja Node.js..."

# Instalacja Nginx
Invoke-RemoteCommand "if ! command -v nginx &> /dev/null; then sudo apt install -y nginx; fi" "Instalacja Nginx..."

# Instalacja PHP-FPM i rozszerzeń
Invoke-RemoteCommand "sudo apt install -y php8.4-fpm php8.4-mysql php8.4-xml php8.4-mbstring php8.4-curl php8.4-zip php8.4-gd" "Instalacja rozszerzeń PHP..."

# Krok 2: Konfiguracja bazy danych
Write-Host "`n=== KROK 2: Konfiguracja bazy danych ===" -ForegroundColor Green

$dbCommands = @"
CREATE DATABASE IF NOT EXISTS krzyszton_port1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'krzyszton_port1'@'localhost' IDENTIFIED BY 'Alicja2025##';
GRANT ALL PRIVILEGES ON krzyszton_port1.* TO 'krzyszton_port1'@'localhost';
FLUSH PRIVILEGES;
"@

Invoke-RemoteCommand "echo '$dbCommands' | sudo mysql" "Konfiguracja bazy danych..."

# Krok 3: Utworzenie katalogu aplikacji
Write-Host "`n=== KROK 3: Przygotowanie katalogu aplikacji ===" -ForegroundColor Green

Invoke-RemoteCommand "sudo mkdir -p $AppDir" "Tworzenie katalogu aplikacji..."
Invoke-RemoteCommand "sudo chown -R $User`:$User $AppDir" "Ustawianie uprawnień..."

# Krok 4: Przesłanie plików
Write-Host "`n=== KROK 4: Przesyłanie plików ===" -ForegroundColor Green
Write-Host "UWAGA: Przesyłanie plików przez SCP..." -ForegroundColor Yellow

# Użyj SCP do przesłania plików
$scpSession = New-SFTPSession -ComputerName $Server -Credential $credential -AcceptKey

if ($scpSession) {
    Write-Host "Przesyłanie plików..." -ForegroundColor Yellow
    
    # Wyklucz niepotrzebne katalogi
    $excludeDirs = @('node_modules', 'vendor', '.git', 'storage/logs', 'storage/framework/cache', 'storage/framework/sessions', 'storage/framework/views', '.env')
    
    # Prześlij pliki
    $localPath = Get-Location
    $files = Get-ChildItem -Path $localPath -Recurse -File | Where-Object {
        $relativePath = $_.FullName.Replace($localPath.Path + '\', '').Replace('\', '/')
        $shouldExclude = $false
        foreach ($exclude in $excludeDirs) {
            if ($relativePath.StartsWith($exclude)) {
                $shouldExclude = $true
                break
            }
        }
        -not $shouldExclude
    }
    
    foreach ($file in $files) {
        $remotePath = $file.FullName.Replace($localPath.Path + '\', '').Replace('\', '/')
        $remoteDir = "/$AppDir/" + (Split-Path $remotePath -Parent).Replace('\', '/')
        $remoteFile = "/$AppDir/$remotePath"
        
        try {
            # Utwórz katalog jeśli nie istnieje
            $null = Invoke-SFTPCommand -SessionId $scpSession.SessionId -Command "mkdir -p $remoteDir"
            
            # Prześlij plik
            Set-SFTPFile -SessionId $scpSession.SessionId -LocalFile $file.FullName -RemotePath $remoteFile
            Write-Host "  ✓ $remotePath" -ForegroundColor Gray
        } catch {
            Write-Host "  ✗ Błąd: $remotePath - $_" -ForegroundColor Red
        }
    }
    
    Remove-SFTPSession -SessionId $scpSession.SessionId
    Write-Host "Pliki przesłane!" -ForegroundColor Green
} else {
    Write-Host "BŁĄD: Nie udało się utworzyć sesji SFTP!" -ForegroundColor Red
    Write-Host "Prześlij pliki ręcznie przez WinSCP do $AppDir" -ForegroundColor Yellow
}

# Krok 5: Instalacja zależności i konfiguracja aplikacji
Write-Host "`n=== KROK 5: Konfiguracja aplikacji ===" -ForegroundColor Green

$appCommands = @"
cd $AppDir
cp .env.example .env
php artisan key:generate --force
composer install --no-dev --optimize-autoloader --no-interaction
npm install
npm run build
php artisan storage:link
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo chown -R www-data:www-data $AppDir
sudo chmod -R 755 $AppDir
sudo chmod -R 775 $AppDir/storage
sudo chmod -R 775 $AppDir/bootstrap/cache
"@

Invoke-RemoteCommand $appCommands "Konfiguracja aplikacji Laravel..."

# Krok 6: Konfiguracja Nginx
Write-Host "`n=== KROK 6: Konfiguracja Nginx ===" -ForegroundColor Green

$nginxConfig = @"
server {
    listen 80;
    server_name $Server;
    root $AppDir/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files `$uri `$uri/ /index.php?`$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME `$realpath_root`$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
"@

Invoke-RemoteCommand "echo '$nginxConfig' | sudo tee /etc/nginx/sites-available/portal-modelingowy > /dev/null" "Tworzenie konfiguracji Nginx..."
Invoke-RemoteCommand "sudo ln -sf /etc/nginx/sites-available/portal-modelingowy /etc/nginx/sites-enabled/" "Aktywacja konfiguracji..."
Invoke-RemoteCommand "sudo nginx -t" "Testowanie konfiguracji Nginx..."
Invoke-RemoteCommand "sudo systemctl reload nginx" "Przeładowanie Nginx..."

# Krok 7: Firewall
Write-Host "`n=== KROK 7: Konfiguracja firewall ===" -ForegroundColor Green

Invoke-RemoteCommand "sudo ufw allow 22/tcp && sudo ufw allow 80/tcp && sudo ufw allow 443/tcp && sudo ufw --force enable" "Konfiguracja firewall..."

# Zakończenie
Write-Host "`n=== Wdrożenie zakończone! ===" -ForegroundColor Green
Write-Host "Aplikacja dostępna pod adresem: http://$Server" -ForegroundColor Cyan

Remove-SSHSession -SessionId $session.SessionId | Out-Null
