#!/bin/bash
# Skrypt do budowania aplikacji dla Zenbox (eksport statyczny)

echo "🔨 Budowanie aplikacji dla Zenbox..."
echo ""

# Ustaw zmienną środowiskową dla eksportu statycznego
export NEXT_PUBLIC_STATIC_EXPORT=true

# Zbuduj aplikację
echo "📦 Instalowanie zależności..."
npm install

echo "🏗️  Budowanie aplikacji (eksport statyczny)..."
npm run build:static

echo ""
echo "✅ Budowanie zakończone!"
echo ""
echo "📁 Pliki gotowe do przesłania znajdują się w folderze: ./out"
echo ""
echo "📤 Następne kroki:"
echo "   1. Zaloguj się do panelu Zenbox (FTP lub File Manager)"
echo "   2. Przejdź do katalogu public_html"
echo "   3. Prześlij całą zawartość folderu ./out"
echo "   4. Prześlij plik .htaccess do głównego katalogu"
echo "   5. Utwórz bazę danych MySQL w panelu Zenbox"
echo "   6. Uruchom skrypt lib/schema.sql w bazie danych"
echo ""
echo "📖 Więcej informacji w pliku ZENBOX_DEPLOY.md"

