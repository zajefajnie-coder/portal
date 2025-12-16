# Przesłanie plików i wdrożenie
Import-Module Posh-SSH

$Server = "83.230.44.103"
$User = "michal"
$Password = "Alicja2025##"
$AppDir = "/home/michal/portal-modelingowy"

$securePassword = ConvertTo-SecureString $Password -AsPlainText -Force
$credential = New-Object System.Management.Automation.PSCredential($User, $securePassword)

Write-Host "=== Przesyłanie plików przez SCP ===" -ForegroundColor Green
Write-Host "UWAGA: Użyj WinSCP lub scp do przesłania plików" -ForegroundColor Yellow
Write-Host "Komenda scp (z lokalnego terminala Linux/Mac):" -ForegroundColor Cyan
Write-Host "scp -r app bootstrap config database public resources routes artisan composer.json package.json vite.config.js tailwind.config.js postcss.config.js .env.example michal@$Server`:$AppDir/" -ForegroundColor White
Write-Host ""

# Połączenie SSH do konfiguracji
$session = New-SSHSession -ComputerName $Server -Credential $credential -AcceptKey
if (-not $session) {
    Write-Host "BŁĄD: Nie udało się połączyć!" -ForegroundColor Red
    exit 1
}

Write-Host "Połączono z serwerem!" -ForegroundColor Green

# Sprawdzenie czy pliki są już na serwerze
$check = Invoke-SSHCommand -SessionId $session.SessionId -Command "test -f $AppDir/composer.json && echo 'OK' || echo 'BRAK'"
if ($check.Output -match "BRAK") {
    Write-Host "`nBŁĄD: Pliki nie są jeszcze na serwerze!" -ForegroundColor Red
    Write-Host "Najpierw prześlij pliki przez WinSCP lub scp" -ForegroundColor Yellow
    Remove-SSHSession -SessionId $session.SessionId | Out-Null
    exit 1
}

Write-Host "Pliki znalezione na serwerze. Rozpoczynam konfigurację..." -ForegroundColor Green

# Konfiguracja .env
Write-Host "`n=== Konfiguracja .env ===" -ForegroundColor Yellow
Invoke-SSHCommand -SessionId $session.SessionId -Command "cd $AppDir; cp .env.example .env" | Out-Null
Invoke-SSHCommand -SessionId $session.SessionId -Command "cd $AppDir; sed -i 's/APP_ENV=local/APP_ENV=production/' .env" | Out-Null
Invoke-SSHCommand -SessionId $session.SessionId -Command "cd $AppDir; sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env" | Out-Null
Invoke-SSHCommand -SessionId $session.SessionId -Command "cd $AppDir; sed -i 's/DB_DATABASE=.*/DB_DATABASE=krzyszton_port1/' .env" | Out-Null
Invoke-SSHCommand -SessionId $session.SessionId -Command "cd $AppDir; sed -i 's/DB_USERNAME=.*/DB_USERNAME=krzyszton_port1/' .env" | Out-Null
Invoke-SSHCommand -SessionId $session.SessionId -Command "cd $AppDir; sed -i 's|DB_PASSWORD=.*|DB_PASSWORD=Alicja2025##|' .env" | Out-Null

# Generowanie klucza
Write-Host "`n=== Generowanie klucza aplikacji ===" -ForegroundColor Yellow
Invoke-SSHCommand -SessionId $session.SessionId -Command "cd $AppDir; php artisan key:generate --force"

# Instalacja Composer
Write-Host "`n=== Instalacja zależności Composer ===" -ForegroundColor Yellow
$result = Invoke-SSHCommand -SessionId $session.SessionId -Command "cd $AppDir; php composer.phar install --no-dev --optimize-autoloader --no-interaction"
Write-Host $result.Output

# Node.js i npm
Write-Host "`n=== Sprawdzanie Node.js ===" -ForegroundColor Yellow
$nodeCheck = Invoke-SSHCommand -SessionId $session.SessionId -Command "command -v node"
if ($nodeCheck.ExitStatus -eq 0) {
    Write-Host "Node.js zainstalowany. Budowa assets..." -ForegroundColor Green
    Invoke-SSHCommand -SessionId $session.SessionId -Command "cd $AppDir; npm install"
    Invoke-SSHCommand -SessionId $session.SessionId -Command "cd $AppDir; npm run build"
} else {
    Write-Host "Node.js nie jest zainstalowany. Wymaga uprawnień root." -ForegroundColor Yellow
}

# Storage link
Write-Host "`n=== Link symboliczny storage ===" -ForegroundColor Yellow
Invoke-SSHCommand -SessionId $session.SessionId -Command "cd $AppDir; php artisan storage:link"

# Migracje
Write-Host "`n=== Migracje bazy danych ===" -ForegroundColor Yellow
Invoke-SSHCommand -SessionId $session.SessionId -Command "cd $AppDir; php artisan migrate --force"

# Cache
Write-Host "`n=== Optymalizacja cache ===" -ForegroundColor Yellow
Invoke-SSHCommand -SessionId $session.SessionId -Command "cd $AppDir; php artisan config:cache"
Invoke-SSHCommand -SessionId $session.SessionId -Command "cd $AppDir; php artisan route:cache"
Invoke-SSHCommand -SessionId $session.SessionId -Command "cd $AppDir; php artisan view:cache"

Write-Host "`n=== Konfiguracja zakończona! ===" -ForegroundColor Green
Write-Host "`nUWAGA: Aplikacja jest w $AppDir" -ForegroundColor Yellow
Write-Host "Do pełnego wdrożenia potrzebne są uprawnienia root dla:" -ForegroundColor Yellow
Write-Host "1. Instalacji Node.js" -ForegroundColor White
Write-Host "2. Instalacji Nginx" -ForegroundColor White
Write-Host "3. Konfiguracji Nginx" -ForegroundColor White
Write-Host "4. Przeniesienia do /var/www" -ForegroundColor White

Remove-SSHSession -SessionId $session.SessionId | Out-Null
