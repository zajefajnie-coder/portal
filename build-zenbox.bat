@echo off
REM Skrypt do budowania aplikacji dla Zenbox (Windows)

echo 🔨 Budowanie aplikacji dla Zenbox...
echo.

REM Ustaw zmienną środowiskową dla eksportu statycznego
set NEXT_PUBLIC_STATIC_EXPORT=true

REM Zbuduj aplikację
echo 📦 Instalowanie zależności...
call npm install

echo 🏗️  Budowanie aplikacji (eksport statyczny)...
call npm run build:static

echo.
echo ✅ Budowanie zakończone!
echo.
echo 📁 Pliki gotowe do przesłania znajdują się w folderze: .\out
echo.
echo 📤 Następne kroki:
echo    1. Zaloguj się do panelu Zenbox (FTP lub File Manager)
echo    2. Przejdź do katalogu public_html (katalog root)
echo    3. Prześlij całą zawartość folderu .\out bezpośrednio do public_html
echo    4. Prześlij plik .htaccess do public_html
echo    5. Prześlij plik install.php do public_html
echo    6. Otwórz install.php w przeglądarce i wykonaj instalację bazy danych
echo.
echo 📖 Więcej informacji:
echo    - DEPLOY_INSTRUKCJA.md - szybka instrukcja wdrożenia
echo    - ZENBOX_SETUP.md - szczegółowa konfiguracja
echo    - ZENBOX_DEPLOY.md - pełna dokumentacja
pause

