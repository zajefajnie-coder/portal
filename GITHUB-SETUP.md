# Instrukcje wypchnięcia projektu na GitHub

## KROK 1: Utworzenie repozytorium na GitHub

1. Zaloguj się na https://github.com
2. Kliknij przycisk "+" w prawym górnym rogu
3. Wybierz "New repository"
4. Nazwa: `portal-modelingowy` (lub inna)
5. Opis: "Portal społecznościowy dla twórców mody i fotografii"
6. **NIE zaznaczaj** "Initialize this repository with a README"
7. Kliknij "Create repository"

## KROK 2: Dodanie remote i push

Po utworzeniu repozytorium GitHub pokaże Ci URL. Użyj jednego z poniższych:

### Opcja A: HTTPS (wymaga hasła/tokenu)
```powershell
cd C:\Users\micha\OneDrive\Desktop\PROJEKT
git remote add origin https://github.com/TWOJ-USER/portal-modelingowy.git
git branch -M main
git push -u origin main
```

### Opcja B: SSH (wymaga skonfigurowanego klucza SSH)
```powershell
cd C:\Users\micha\OneDrive\Desktop\PROJEKT
git remote add origin git@github.com:TWOJ-USER/portal-modelingowy.git
git branch -M main
git push -u origin main
```

**Zastąp `TWOJ-USER` swoją nazwą użytkownika GitHub!**

## KROK 3: Wdrożenie na serwer z GitHub

Po wypchnięciu kodu na GitHub, uruchom:

```powershell
.\deploy-from-github.ps1
```

Skrypt poprosi Cię o URL repozytorium GitHub i automatycznie:
- Sklonuje kod na serwer
- Skonfiguruje aplikację
- Uruchomi migracje
- Skonfiguruje Nginx
- Uruchomi serwisy

## Alternatywnie: Ręczne wdrożenie na serwerze

```bash
ssh root@77.83.101.68
cd /var/www
rm -rf portal-modelingowy
git clone https://github.com/TWOJ-USER/portal-modelingowy.git portal-modelingowy
cd portal-modelingowy
bash setup-app.sh
```
