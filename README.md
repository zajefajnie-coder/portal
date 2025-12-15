# portal-modelingowy.pl

Platforma społecznościowa dla branży fotomodelingu w Polsce. Portal umożliwia fotografom, modelom i członkom zespołu kreatywnego prezentację portfolio, nawiązywanie kontaktów i współpracę.

## 🚀 Technologie

- **Next.js 14** (App Router, Server Components)
- **TypeScript**
- **Tailwind CSS** + **shadcn/ui**
- **MySQL** (baza danych - Zenbox)
- **Zenbox** (deployment-ready - eksport statyczny)

## 📋 Wymagania

- Node.js 18+ 
- npm lub yarn
- MySQL 8.0+ (lokalnie lub zdalnie)

## 🛠️ Instalacja

1. Sklonuj repozytorium:
```bash
git clone [url-repozytorium]
cd NEW_PORTAL_MODELING
```

2. Zainstaluj zależności:
```bash
npm install
```

3. Skonfiguruj bazę danych MySQL:
```bash
# Uruchom skrypt SQL aby utworzyć schemat
mysql -u root -p < lib/schema.sql
```

4. Skonfiguruj zmienne środowiskowe:
```bash
cp .env.example .env.local
```

Edytuj `.env.local` i dodaj swoje dane MySQL:
```
DB_HOST=localhost
DB_PORT=3306
DB_USER=root
DB_PASSWORD=twoje-haslo
DB_NAME=portal_modelingowy
NEXTAUTH_SECRET=wygeneruj-secret-key
```

4. Uruchom serwer deweloperski:
```bash
npm run dev
```

Otwórz [http://localhost:3000](http://localhost:3000) w przeglądarce.

## 📁 Struktura projektu

```
├── app/                    # Next.js App Router
│   ├── layout.tsx         # Główny layout
│   ├── page.tsx           # Strona główna
│   ├── regulamin/         # Strona regulaminu
│   ├── polityka-prywatnosci/  # Polityka prywatności
│   ├── rodo/              # Informacja RODO
│   ├── rejestracja/       # Formularz rejestracji
│   ├── kontakt/           # Strona kontaktowa
│   ├── look/[id]/         # Szczegóły sesji
│   └── profil/[id]/       # Profil użytkownika
├── components/            # Komponenty React
│   ├── ui/                # Komponenty UI (shadcn/ui)
│   ├── hero.tsx           # Sekcja hero
│   ├── latest-looks.tsx   # Najnowsze sesje
│   ├── look-card.tsx      # Karta sesji
│   ├── role-card.tsx      # Karta roli (fotograf/model/zespół)
│   ├── footer.tsx          # Stopka
│   └── cookie-consent.tsx # Banner zgody na cookies
├── lib/                   # Narzędzia i pomocniki
│   ├── db.ts              # Połączenie z MySQL
│   ├── db-helpers.ts      # Funkcje pomocnicze do zapytań
│   ├── schema.sql         # Schemat bazy danych
│   ├── mock-data.ts       # Dane testowe
│   └── utils.ts           # Funkcje pomocnicze
└── public/                # Pliki statyczne
```

## 🎨 Funkcjonalności

### ✅ Zaimplementowane

- Strona główna z sekcją hero, najnowszymi sesjami i kartami ról
- Strony prawne (Regulamin, Polityka Prywatności, RODO) - pełne treści po polsku
- Formularz rejestracji z wymaganą zgodą na regulamin
- Banner zgody na pliki cookies
- Stopka z linkami do dokumentów prawnych
- Responsywny design (mobile-first)
- Dostępność (WCAG 2.1 AA)
- Mock dane dla 3 przykładowych sesji

### 🚧 Do zaimplementowania

- Integracja z NextAuth (logowanie/rejestracja)
- Połączenie z bazą danych MySQL (zamiast mock data)
- Przesyłanie i przechowywanie zdjęć (Cloudinary/AWS S3)
- System tagowania i wyszukiwania
- Profil użytkownika z edycją
- Publikowanie nowych sesji
- System powiadomień
- Panel administracyjny

## 🔒 Zgodność z RODO

Platforma jest zaprojektowana zgodnie z wymogami RODO:

- ✅ Pełna Polityka Prywatności
- ✅ Informacja RODO
- ✅ Zgoda na przetwarzanie danych przy rejestracji
- ✅ Banner zgody na pliki cookies
- ✅ Prawa użytkownika (dostęp, usunięcie, przenoszalność)
- ✅ Przechowywanie danych w UE (MySQL w chmurze)

## 📝 Uwagi prawne

**WAŻNE:** Przed uruchomieniem produkcyjnym należy:

1. Uzupełnić dane administratora w dokumentach prawnych:
   - `/app/regulamin/page.tsx` - [Nazwa firmy], [Adres], [NIP], [REGON], [e-mail]
   - `/app/polityka-prywatnosci/page.tsx` - te same dane
   - `/app/rodo/page.tsx` - te same dane
   - `/app/kontakt/page.tsx` - te same dane

2. Skonfigurować Inspektora Ochrony Danych (jeśli wymagane):
   - Ustaw `NEXT_PUBLIC_HAS_IOD=true` w `.env.local`
   - Uzupełnij dane IOD w `/app/rodo/page.tsx`

3. Przejrzeć i dostosować treści prawne do specyfiki działalności

## 🚀 Deployment

### Opcja 1: Zenbox (Hosting PHP/MySQL) ⭐

Platforma jest przygotowana do hostingu na **Zenbox** przez eksport statyczny:

1. **Zbuduj aplikację:**
   ```bash
   # Windows
   build-zenbox.bat
   
   # Linux/Mac
   chmod +x build-zenbox.sh
   ./build-zenbox.sh
   ```

2. **Prześlij pliki:**
   - Prześlij **całą zawartość** folderu `out` bezpośrednio do `public_html` na Zenbox (FTP/File Manager)
   - Struktura: `public_html/index.html`, `public_html/_next/`, `public_html/regulamin/`, itd.
   - Prześlij plik `.htaccess` do `public_html/`
   - Prześlij plik `install.php` do `public_html/` (do instalacji bazy danych)

3. **Skonfiguruj bazę danych MySQL** w panelu Zenbox i uruchom `lib/schema.sql`

📖 **Szczegółowa instrukcja:** [ZENBOX_DEPLOY.md](./ZENBOX_DEPLOY.md) | [ZENBOX_SETUP.md](./ZENBOX_SETUP.md)

## 📄 Licencja

[Określ licencję]

## 👥 Autorzy

[Twoje dane]

