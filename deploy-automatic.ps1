# Automatyczne wdrożenie przez SSH z hasłem
# Wymaga: Install-Module -Name Posh-SSH

$Server = "83.230.44.103"
$User = "root"
$Password = "Alicja2025##"
$AppDir = "/var/www/portal-modelingowy"

Write-Host "=== Automatyczne wdrożenie Portal Modelingowy ===" -ForegroundColor Green
Write-Host "Serwer: $User@$Server" -ForegroundColor Cyan
Write-Host ""

# Sprawdzenie czy Posh-SSH jest zainstalowany
if (-not (Get-Module -ListAvailable -Name Posh-SSH)) {
    Write-Host "Instalowanie modułu Posh-SSH..." -ForegroundColor Yellow
    Install-Module -Name Posh-SSH -Force -Scope CurrentUser -AllowClobber
}

Import-Module Posh-SSH

# Utworzenie credential
$securePassword = ConvertTo-SecureString $Password -AsPlainText -Force
$credential = New-Object System.Management.Automation.PSCredential($User, $securePassword)

Write-Host "Łączenie z serwerem..." -ForegroundColor Yellow
try {
    $session = New-SSHSession -ComputerName $Server -Credential $credential -AcceptKey
    
    if ($session) {
        Write-Host "Połączono pomyślnie!" -ForegroundColor Green
        
        # Funkcja do wykonywania komend
        function Invoke-RemoteCommand {
            param([string]$Command)
            $result = Invoke-SSHCommand -SessionId $session.SessionId -Command $Command
            if ($result.ExitStatus -ne 0) {
                Write-Host "BŁĄD: $($result.Error)" -ForegroundColor Red
                return $false
            }
            Write-Host $result.Output -ForegroundColor Gray
            return $true
        }
        
        Write-Host "`n1. Tworzenie katalogu aplikacji..." -ForegroundColor Yellow
        Invoke-RemoteCommand "mkdir -p $AppDir" | Out-Null
        Invoke-RemoteCommand "chown -R www-data:www-data $AppDir" | Out-Null
        
        Write-Host "`n2. Sprawdzanie zainstalowanych pakietów..." -ForegroundColor Yellow
        Invoke-RemoteCommand "php -v" | Out-Null
        Invoke-RemoteCommand "composer --version" | Out-Null
        Invoke-RemoteCommand "node -v" | Out-Null
        
        Write-Host "`n3. Przesyłanie plików..." -ForegroundColor Yellow
        Write-Host "UWAGA: Musisz przesłać pliki ręcznie przez WinSCP lub rsync" -ForegroundColor Yellow
        Write-Host "Katalog źródłowy: $PWD" -ForegroundColor Cyan
        Write-Host "Katalog docelowy: $AppDir" -ForegroundColor Cyan
        
        Write-Host "`n4. Instalacja zależności (po przesłaniu plików)..." -ForegroundColor Yellow
        $installCommands = @(
            "cd $AppDir",
            "composer install --no-dev --optimize-autoloader --no-interaction",
            "npm install",
            "npm run build",
            "cp .env.example .env",
            "php artisan key:generate --force",
            "php artisan storage:link",
            "php artisan migrate --force",
            "php artisan config:cache",
            "php artisan route:cache",
            "php artisan view:cache",
            "chown -R www-data:www-data $AppDir",
            "chmod -R 755 $AppDir",
            "chmod -R 775 $AppDir/storage",
            "chmod -R 775 $AppDir/bootstrap/cache"
        )
        
        Write-Host "Komendy do wykonania na serwerze:" -ForegroundColor Cyan
        foreach ($cmd in $installCommands) {
            Write-Host "  $cmd" -ForegroundColor White
        }
        
        Write-Host "`n5. Zamykanie sesji SSH..." -ForegroundColor Yellow
        Remove-SSHSession -SessionId $session.SessionId | Out-Null
        
        Write-Host "`n=== Wdrożenie przygotowane! ===" -ForegroundColor Green
        Write-Host "Następne kroki:" -ForegroundColor Yellow
        Write-Host "1. Prześlij pliki przez WinSCP do $AppDir" -ForegroundColor White
        Write-Host "2. Połącz się przez SSH i wykonaj komendy instalacyjne" -ForegroundColor White
        
    } else {
        Write-Host "Nie udało się połączyć z serwerem!" -ForegroundColor Red
    }
} catch {
    Write-Host "BŁĄD: $_" -ForegroundColor Red
    Write-Host "`nSpróbuj ręcznego wdrożenia zgodnie z DEPLOY-NOW.md" -ForegroundColor Yellow
}
