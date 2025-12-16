# Wdrożenie krok po kroku z instrukcjami
Import-Module Posh-SSH

$Server = "83.230.44.103"
$User = "michal"
$Password = "Alicja2025##"
$AppDir = "/var/www/portal-modelingowy"

$securePassword = ConvertTo-SecureString $Password -AsPlainText -Force
$credential = New-Object System.Management.Automation.PSCredential($User, $securePassword)

Write-Host "=== Wdrożenie Portal Modelingowy ===" -ForegroundColor Green
Write-Host "Serwer: $User@$Server" -ForegroundColor Cyan
Write-Host ""

# Połączenie
$session = New-SSHSession -ComputerName $Server -Credential $credential -AcceptKey
if (-not $session) {
    Write-Host "BŁĄD: Nie udało się połączyć!" -ForegroundColor Red
    exit 1
}

Write-Host "Połączono!" -ForegroundColor Green

# Funkcja pomocnicza
function Invoke-Cmd {
    param([string]$Cmd, [string]$Desc = "")
    if ($Desc) { Write-Host "`n$Desc" -ForegroundColor Yellow }
    $result = Invoke-SSHCommand -SessionId $session.SessionId -Command $Cmd
    if ($result.Output) { Write-Host $result.Output -ForegroundColor Gray }
    if ($result.Error) { Write-Host "BŁĄD: $($result.Error)" -ForegroundColor Red }
    return $result.ExitStatus -eq 0
}

# Krok 1: Instalacja pakietów
Write-Host "`n=== KROK 1: Instalacja pakietów ===" -ForegroundColor Green
Invoke-Cmd "sudo apt update -qq" "Aktualizacja pakietów..."

# Composer
Invoke-Cmd "command -v composer || (curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer && sudo chmod +x /usr/local/bin/composer)" "Instalacja Composer..."

# Node.js
Invoke-Cmd "command -v node || (curl -fsSL https://deb.nodesource.com/setup_20.x | sudo bash - && sudo apt install -y nodejs)" "Instalacja Node.js..."

# Nginx
Invoke-Cmd "command -v nginx || sudo apt install -y nginx" "Instalacja Nginx..."

# PHP-FPM i rozszerzenia
Invoke-Cmd "sudo apt install -y php8.4-fpm php8.4-mysql php8.4-xml php8.4-mbstring php8.4-curl php8.4-zip php8.4-gd" "Instalacja rozszerzeń PHP..."

# Krok 2: Baza danych
Write-Host "`n=== KROK 2: Konfiguracja bazy danych ===" -ForegroundColor Green
$dbScript = @"
CREATE DATABASE IF NOT EXISTS krzyszton_port1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'krzyszton_port1'@'localhost' IDENTIFIED BY 'Alicja2025##';
GRANT ALL PRIVILEGES ON krzyszton_port1.* TO 'krzyszton_port1'@'localhost';
FLUSH PRIVILEGES;
"@
Invoke-Cmd "echo '$dbScript' | sudo mysql" "Konfiguracja bazy danych..."

# Krok 3: Katalog aplikacji
Write-Host "`n=== KROK 3: Przygotowanie katalogu ===" -ForegroundColor Green
Invoke-Cmd "sudo mkdir -p $AppDir && sudo chown -R $User`:$User $AppDir" "Tworzenie katalogu aplikacji..."

# Krok 4: Przesłanie plików przez SFTP
Write-Host "`n=== KROK 4: Przesyłanie plików ===" -ForegroundColor Green
Write-Host "Tworzenie sesji SFTP..." -ForegroundColor Yellow

$sftpSession = New-SFTPSession -ComputerName $Server -Credential $credential -AcceptKey
if ($sftpSession) {
    Write-Host "Przesyłanie plików projektu..." -ForegroundColor Yellow
    
    # Prześlij kluczowe pliki i katalogi
    $itemsToUpload = @(
        "app", "bootstrap", "config", "database", "public", "resources", "routes",
        "artisan", "composer.json", "package.json", "vite.config.js", "tailwind.config.js",
        "postcss.config.js", ".env.example", ".gitignore"
    )
    
    $localPath = Get-Location
    $uploaded = 0
    
    foreach ($item in $itemsToUpload) {
        $localItem = Join-Path $localPath $item
        if (Test-Path $localItem) {
            try {
                if (Test-Path $localItem -PathType Container) {
                    # Katalog - rekurencyjnie
                    Get-ChildItem -Path $localItem -Recurse -File | ForEach-Object {
                        $relPath = $_.FullName.Replace("$localPath\", "").Replace("\", "/")
                        $remotePath = "$AppDir/$relPath"
                        $remoteDir = Split-Path $remotePath -Parent
                        
                        # Utwórz katalog
                        try { $null = Invoke-SFTPCommand -SessionId $sftpSession.SessionId -Command "mkdir -p $remoteDir" } catch {}
                        
                        # Prześlij plik
                        Set-SFTPFile -SessionId $sftpSession.SessionId -LocalFile $_.FullName -RemotePath $remotePath -ErrorAction SilentlyContinue
                        $uploaded++
                    }
                } else {
                    # Plik
                    $remotePath = "$AppDir/$item"
                    Set-SFTPFile -SessionId $sftpSession.SessionId -LocalFile $localItem -RemotePath $remotePath -ErrorAction SilentlyContinue
                    $uploaded++
                }
                Write-Host "  ✓ $item" -ForegroundColor Gray
            } catch {
                Write-Host "  ✗ $item - $_" -ForegroundColor Red
            }
        }
    }
    
    Remove-SFTPSession -SessionId $sftpSession.SessionId
    Write-Host "Przesłano $uploaded plików/katalogów" -ForegroundColor Green
} else {
    Write-Host "BŁĄD: Nie udało się utworzyć sesji SFTP!" -ForegroundColor Red
    Write-Host "Prześlij pliki ręcznie przez WinSCP do $AppDir" -ForegroundColor Yellow
}

# Krok 5: Konfiguracja aplikacji
Write-Host "`n=== KROK 5: Konfiguracja aplikacji ===" -ForegroundColor Green

$appSetup = @"
cd $AppDir
cp .env.example .env
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=krzyszton_port1/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=krzyszton_port1/' .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=Alicja2025##/' .env
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

Invoke-Cmd $appSetup "Konfiguracja Laravel..."

# Krok 6: Nginx
Write-Host "`n=== KROK 6: Konfiguracja Nginx ===" -ForegroundColor Green

$nginxConf = 'server {
    listen 80;
    server_name 83.230.44.103;
    root /var/www/portal-modelingowy/public;
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
}'

Invoke-Cmd "echo '$nginxConf' | sudo tee /etc/nginx/sites-available/portal-modelingowy > /dev/null" "Tworzenie konfiguracji Nginx..."
Invoke-Cmd "sudo ln -sf /etc/nginx/sites-available/portal-modelingowy /etc/nginx/sites-enabled/" "Aktywacja..."
Invoke-Cmd "sudo nginx -t && sudo systemctl reload nginx" "Test i przeładowanie Nginx..."

# Krok 7: Firewall
Write-Host "`n=== KROK 7: Firewall ===" -ForegroundColor Green
Invoke-Cmd "sudo ufw allow 80/tcp && sudo ufw allow 443/tcp" "Otwieranie portów..."

Write-Host "`n=== Wdrożenie zakończone! ===" -ForegroundColor Green
Write-Host "Aplikacja: http://$Server" -ForegroundColor Cyan

Remove-SSHSession -SessionId $session.SessionId | Out-Null
