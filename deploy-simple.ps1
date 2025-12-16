# Proste wdrożenie - krok po kroku
Import-Module Posh-SSH

$Server = "77.83.101.68"
$User = "root"
$Password = "Alicja2025##"
$AppDir = "/var/www/portal-modelingowy"

$securePassword = ConvertTo-SecureString $Password -AsPlainText -Force
$credential = New-Object System.Management.Automation.PSCredential($User, $securePassword)

Write-Host "=== Wdrożenie Portal Modelingowy ===" -ForegroundColor Green

# Połączenie SSH
$session = New-SSHSession -ComputerName $Server -Credential $credential -AcceptKey
if (-not $session) {
    Write-Host "BŁĄD: Nie udało się połączyć!" -ForegroundColor Red
    exit 1
}

Write-Host "Połączono!" -ForegroundColor Green

# Przesłanie plików przez SFTP
Write-Host "`n=== Przesyłanie plików ===" -ForegroundColor Yellow
$sftpSession = New-SFTPSession -ComputerName $Server -Credential $credential -AcceptKey

if ($sftpSession) {
    $localPath = Get-Location
    $items = @("app", "bootstrap", "config", "database", "public", "resources", "routes", "artisan", "composer.json", "package.json", "vite.config.js", "tailwind.config.js", "postcss.config.js", ".env.example")
    
    foreach ($item in $items) {
        $localItem = Join-Path $localPath $item
        if (Test-Path $localItem) {
            Write-Host "  Przesyłanie: $item" -ForegroundColor Gray
            try {
                $sftp = Get-SFTPSession -SessionId $sftpSession.SessionId
                if (Test-Path $localItem -PathType Container) {
                    Get-ChildItem -Path $localItem -Recurse -File | ForEach-Object {
                        $relPath = $_.FullName.Replace("$localPath\", "").Replace("\", "/")
                        $remotePath = "$AppDir/$relPath"
                        $remoteDir = Split-Path $remotePath -Parent
                        try { $sftp.Session.CreateDirectory($remoteDir) | Out-Null } catch {}
                        $sftp.Session.PutFile($_.FullName, $remotePath) | Out-Null
                    }
                } else {
                    $remotePath = "$AppDir/$item"
                    $sftp.Session.PutFile($localItem, $remotePath) | Out-Null
                }
            } catch {
                Write-Host "    Błąd: $($_.Exception.Message)" -ForegroundColor Red
            }
        }
    }
    Remove-SFTPSession -SessionId $sftpSession.SessionId
    Write-Host "Pliki przesłane!" -ForegroundColor Green
} else {
    Write-Host "BŁĄD: Nie udało się utworzyć sesji SFTP!" -ForegroundColor Red
    Remove-SSHSession -SessionId $session.SessionId | Out-Null
    exit 1
}

# Konfiguracja aplikacji przez SSH
Write-Host "`n=== Konfiguracja aplikacji ===" -ForegroundColor Yellow

$setupScript = @"
cd $AppDir
cp .env.example .env
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=krzyszton_port1/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=krzyszton_port1/' .env
sed -i 's|DB_PASSWORD=.*|DB_PASSWORD=Alicja2025##|' .env
php artisan key:generate --force
composer install --no-dev --optimize-autoloader --no-interaction
npm install
npm run build
php artisan storage:link
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R www-data:www-data $AppDir
chmod -R 755 $AppDir
chmod -R 775 $AppDir/storage
chmod -R 775 $AppDir/bootstrap/cache
"@

Invoke-SSHCommand -SessionId $session.SessionId -Command $setupScript | Out-Null

# Konfiguracja Nginx
Write-Host "`n=== Konfiguracja Nginx ===" -ForegroundColor Yellow

$nginxConfig = "server {`n    listen 80;`n    server_name 77.83.101.68;`n    root $AppDir/public;`n    index index.php;`n    charset utf-8;`n    `n    location / {`n        try_files `$uri `$uri/ /index.php?`$query_string;`n    }`n    `n    location = /favicon.ico { access_log off; log_not_found off; }`n    location = /robots.txt  { access_log off; log_not_found off; }`n    `n    error_page 404 /index.php;`n    `n    location ~ \.php`$ {`n        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;`n        fastcgi_param SCRIPT_FILENAME `$realpath_root`$fastcgi_script_name;`n        include fastcgi_params;`n    }`n    `n    location ~ /\.(?!well-known).* {`n        deny all;`n    }`n}"

Invoke-SSHCommand -SessionId $session.SessionId -Command "echo '$nginxConfig' > /etc/nginx/sites-available/portal-modelingowy" | Out-Null
Invoke-SSHCommand -SessionId $session.SessionId -Command "ln -sf /etc/nginx/sites-available/portal-modelingowy /etc/nginx/sites-enabled/" | Out-Null
Invoke-SSHCommand -SessionId $session.SessionId -Command "nginx -t" | Out-Null
Invoke-SSHCommand -SessionId $session.SessionId -Command "systemctl enable php8.2-fpm && systemctl start php8.2-fpm" | Out-Null
Invoke-SSHCommand -SessionId $session.SessionId -Command "systemctl reload nginx" | Out-Null

Write-Host "`n=== Wdrożenie zakończone! ===" -ForegroundColor Green
Write-Host "Aplikacja dostępna: http://$Server" -ForegroundColor Cyan

Remove-SSHSession -SessionId $session.SessionId | Out-Null
