# Wdrożenie bez uprawnień sudo
Import-Module Posh-SSH

$Server = "83.230.44.103"
$User = "michal"
$Password = "Alicja2025##"
$AppDir = "/home/michal/portal-modelingowy"  # Używam katalogu domowego zamiast /var/www

$securePassword = ConvertTo-SecureString $Password -AsPlainText -Force
$credential = New-Object System.Management.Automation.PSCredential($User, $securePassword)

Write-Host "=== Wdrożenie Portal Modelingowy (bez sudo) ===" -ForegroundColor Green
Write-Host "UWAGA: Użytkownik $User nie ma uprawnień sudo" -ForegroundColor Yellow
Write-Host "Aplikacja zostanie zainstalowana w: $AppDir" -ForegroundColor Cyan
Write-Host ""

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

# Krok 1: Utworzenie katalogu
Write-Host "=== KROK 1: Przygotowanie katalogu ===" -ForegroundColor Green
Invoke-Cmd "mkdir -p $AppDir" "Tworzenie katalogu aplikacji..."

# Krok 2: Composer lokalnie
Write-Host "`n=== KROK 2: Composer ===" -ForegroundColor Green
Invoke-Cmd "cd $AppDir && if [ ! -f composer.phar ]; then curl -sS https://getcomposer.org/installer | php; fi" "Pobieranie Composer..."
Invoke-Cmd "cd $AppDir && php composer.phar --version" "Weryfikacja Composer..."

# Krok 3: Node.js (sprawdź czy jest zainstalowany)
Write-Host "`n=== KROK 3: Node.js ===" -ForegroundColor Green
$nodeCheck = Invoke-SSHCommand -SessionId $session.SessionId -Command "command -v node || echo 'BRAK'"
if ($nodeCheck.Output -match "BRAK") {
    Write-Host "Node.js nie jest zainstalowany. Wymaga uprawnień root do instalacji." -ForegroundColor Yellow
    Write-Host "Poproś administratora o: curl -fsSL https://deb.nodesource.com/setup_20.x | sudo bash - && sudo apt install -y nodejs" -ForegroundColor Cyan
} else {
    Invoke-Cmd "node -v" "Weryfikacja Node.js..."
}

# Krok 4: Przesłanie plików przez SFTP
Write-Host "`n=== KROK 4: Przesyłanie plików ===" -ForegroundColor Green
$sftpSession = New-SFTPSession -ComputerName $Server -Credential $credential -AcceptKey

if ($sftpSession) {
    Write-Host "Przesyłanie plików projektu..." -ForegroundColor Yellow
    
    $localPath = Get-Location
    $items = @("app", "bootstrap", "config", "database", "public", "resources", "routes", "artisan", "composer.json", "package.json", "vite.config.js", "tailwind.config.js", "postcss.config.js", ".env.example")
    
    foreach ($item in $items) {
        $localItem = Join-Path $localPath $item
        if (Test-Path $localItem) {
            try {
                if (Test-Path $localItem -PathType Container) {
                    Get-ChildItem -Path $localItem -Recurse -File | ForEach-Object {
                        $relPath = $_.FullName.Replace("$localPath\", "").Replace("\", "/")
                        $remotePath = "$AppDir/$relPath"
                        $remoteDir = Split-Path $remotePath -Parent
                        try { $null = Invoke-SFTPCommand -SessionId $sftpSession.SessionId -Command "mkdir -p $remoteDir" } catch {}
                        Set-SFTPFile -SessionId $sftpSession.SessionId -LocalFile $_.FullName -RemotePath $remotePath -ErrorAction SilentlyContinue
                    }
                } else {
                    $remotePath = "$AppDir/$item"
                    Set-SFTPFile -SessionId $sftpSession.SessionId -LocalFile $localItem -RemotePath $remotePath -ErrorAction SilentlyContinue
                }
                Write-Host "  ✓ $item" -ForegroundColor Gray
            } catch {
                Write-Host "  ✗ $item" -ForegroundColor Red
            }
        }
    }
    
    Remove-SFTPSession -SessionId $sftpSession.SessionId
    Write-Host "Pliki przesłane!" -ForegroundColor Green
} else {
    Write-Host "BŁĄD: Nie udało się utworzyć sesji SFTP!" -ForegroundColor Red
}

# Krok 5: Konfiguracja aplikacji
Write-Host "`n=== KROK 5: Konfiguracja aplikacji ===" -ForegroundColor Green

$setupScript = @"
cd $AppDir
cp .env.example .env
sed -i 's|APP_ENV=local|APP_ENV=production|' .env
sed -i 's|APP_DEBUG=true|APP_DEBUG=false|' .env
sed -i 's|DB_DATABASE=.*|DB_DATABASE=krzyszton_port1|' .env
sed -i 's|DB_USERNAME=.*|DB_USERNAME=krzyszton_port1|' .env
sed -i 's|DB_PASSWORD=.*|DB_PASSWORD=Alicja2025##|' .env
php artisan key:generate --force
php composer.phar install --no-dev --optimize-autoloader --no-interaction
"@

Invoke-Cmd $setupScript "Konfiguracja Laravel..."

# Sprawdzenie Node.js przed budową
$nodeCheck2 = Invoke-SSHCommand -SessionId $session.SessionId -Command "command -v node"
if ($nodeCheck2.ExitStatus -eq 0) {
    Invoke-Cmd "cd $AppDir && npm install && npm run build" "Budowa assets frontend..."
} else {
    Write-Host "Pominięto budowę assets - Node.js nie jest zainstalowany" -ForegroundColor Yellow
}

Invoke-Cmd "cd $AppDir && php artisan storage:link" "Link symboliczny storage..."
Invoke-Cmd "cd $AppDir && php artisan migrate --force" "Migracje bazy danych..."

Write-Host "`n=== Wdrożenie częściowe zakończone ===" -ForegroundColor Green
Write-Host "`nKROKI WYMAGAJĄCE UPRAWNIEŃ ROOT:" -ForegroundColor Yellow
Write-Host "1. Instalacja Node.js: curl -fsSL https://deb.nodesource.com/setup_20.x | sudo bash - && sudo apt install -y nodejs" -ForegroundColor White
Write-Host "2. Instalacja Nginx: sudo apt install -y nginx" -ForegroundColor White
Write-Host "3. Konfiguracja Nginx (wymaga sudo)" -ForegroundColor White
Write-Host "4. Przeniesienie aplikacji do /var/www (opcjonalnie): sudo mv $AppDir /var/www/portal-modelingowy" -ForegroundColor White
Write-Host "5. Ustawienie uprawnień: sudo chown -R www-data:www-data /var/www/portal-modelingowy" -ForegroundColor White
Write-Host "`nAplikacja tymczasowo dostępna w: $AppDir" -ForegroundColor Cyan

Remove-SSHSession -SessionId $session.SessionId | Out-Null

