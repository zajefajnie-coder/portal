# Skrypt wdrożenia PowerShell - Portal Modelingowy
# Użycie: .\deploy-manual.ps1

$Server = "michal@83.230.44.103"
$Password = "Alicja2025##"
$AppDir = "/var/www/portal-modelingowy"

Write-Host "=== Wdrożenie Portal Modelingowy na serwer Debian ===" -ForegroundColor Green
Write-Host "Serwer: $Server" -ForegroundColor Cyan
Write-Host "Katalog aplikacji: $AppDir" -ForegroundColor Cyan
Write-Host ""

# Funkcja do wykonywania komend SSH
function Invoke-SSHCommand {
    param([string]$Command)
    
    $securePassword = ConvertTo-SecureString $Password -AsPlainText -Force
    $credential = New-Object System.Management.Automation.PSCredential("michal", $securePassword)
    
    # Użyj plink (PuTTY) lub ssh z expect
    # Alternatywnie użyj modułu Posh-SSH
    Write-Host "Wykonywanie: $Command" -ForegroundColor Yellow
    
    # Jeśli masz zainstalowany Posh-SSH:
    # Import-Module Posh-SSH
    # $session = New-SSHSession -ComputerName "83.230.44.103" -Credential $credential
    # Invoke-SSHCommand -SessionId $session.SessionId -Command $Command
    
    # Lub użyj ssh bezpośrednio (wymaga ręcznego wprowadzenia hasła)
    ssh $Server $Command
}

Write-Host "UWAGA: Ten skrypt wymaga ręcznego wprowadzenia hasła przy każdym połączeniu SSH" -ForegroundColor Yellow
Write-Host "Lepszym rozwiązaniem jest użycie skryptu bash z sshpass lub kluczy SSH" -ForegroundColor Yellow
Write-Host ""

# Przesłanie plików przez rsync (wymaga sshpass na Linux/Mac lub WinSCP na Windows)
Write-Host "1. Przesyłanie plików na serwer..." -ForegroundColor Yellow
Write-Host "Użyj WinSCP lub rsync z sshpass do przesłania plików" -ForegroundColor Yellow

# Instrukcje dla użytkownika
Write-Host ""
Write-Host "=== Instrukcje ręcznego wdrożenia ===" -ForegroundColor Green
Write-Host ""
Write-Host "1. Połącz się z serwerem:" -ForegroundColor Cyan
Write-Host "   ssh $Server" -ForegroundColor White
Write-Host "   Hasło: $Password" -ForegroundColor White
Write-Host ""
Write-Host "2. Na serwerze wykonaj:" -ForegroundColor Cyan
Write-Host "   cd $AppDir" -ForegroundColor White
Write-Host "   composer install --no-dev --optimize-autoloader" -ForegroundColor White
Write-Host "   npm install" -ForegroundColor White
Write-Host "   npm run build" -ForegroundColor White
Write-Host "   php artisan key:generate --force" -ForegroundColor White
Write-Host "   php artisan storage:link" -ForegroundColor White
Write-Host "   php artisan migrate --force" -ForegroundColor White
Write-Host "   php artisan config:cache" -ForegroundColor White
Write-Host "   php artisan route:cache" -ForegroundColor White
Write-Host "   php artisan view:cache" -ForegroundColor White
Write-Host ""

