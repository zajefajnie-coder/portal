# Skrypt inicjalizacji wdrożenia
# Użycie: .\start-deployment.ps1

$Server = "michal@83.230.44.103"
$AppDir = "/var/www/portal-modelingowy"

Write-Host "=== Przygotowanie wdrożenia Portal Modelingowy ===" -ForegroundColor Green
Write-Host ""
Write-Host "Serwer: $Server" -ForegroundColor Cyan
Write-Host "Katalog aplikacji: $AppDir" -ForegroundColor Cyan
Write-Host ""

Write-Host "KROK 1: Połącz się z serwerem" -ForegroundColor Yellow
Write-Host "Komenda: ssh $Server" -ForegroundColor White
Write-Host "Hasło: Alicja2025##" -ForegroundColor White
Write-Host ""
Write-Host "Naciśnij Enter, aby otworzyć połączenie SSH..." -ForegroundColor Yellow
Read-Host

# Otwórz połączenie SSH
Start-Process ssh -ArgumentList "$Server" -NoNewWindow

Write-Host ""
Write-Host "KROK 2: Po połączeniu z serwerem wykonaj następujące komendy:" -ForegroundColor Yellow
Write-Host ""
Write-Host "# Utworzenie katalogu aplikacji" -ForegroundColor Cyan
Write-Host "sudo mkdir -p $AppDir" -ForegroundColor White
Write-Host "sudo chown -R michal:michal $AppDir" -ForegroundColor White
Write-Host ""
Write-Host "# Sprawdzenie zainstalowanych pakietów" -ForegroundColor Cyan
Write-Host "php -v" -ForegroundColor White
Write-Host "composer --version" -ForegroundColor White
Write-Host "node -v" -ForegroundColor White
Write-Host "mysql --version" -ForegroundColor White
Write-Host "nginx -v" -ForegroundColor White
Write-Host ""
Write-Host "Szczegółowe instrukcje znajdują się w pliku: DEPLOY-NOW.md" -ForegroundColor Green

