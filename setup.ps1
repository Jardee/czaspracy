<#
.SYNOPSIS
    Skrypt do automatycznej instalacji, konfiguracji i uruchomienia aplikacji Laravel.

.DESCRIPTION
    Ten skrypt wykonuje następujące kroki:
    1. Sprawdza, czy wymagane narzędzia (PHP, Composer, NPM) są zainstalowane.
    2. Instaluje zależności PHP za pomocą Composera.
    3. Tworzy i konfiguruje plik .env z podaną zawartością.
    4. Generuje klucz aplikacji Laravel (php artisan key:generate).
    5. Uruchamia migracje bazy danych.
    6. Uruchamia seeder'y bazy danych.
    7. Instaluje zależności JavaScript za pomocą NPM.
    8. Kompiluje zasoby front-endowe za pomocą Vite (npm run build).
    9. Uruchamia serwer deweloperski (php artisan serve).

    Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope Process

.NOTES
    Autor: AI
    Wersja: 1.1
    Data: 2025-06-07
#>

# =================================================================================
# USTAWIENIA I KONFIGURACJA
# =================================================================================

# Zatrzymaj wykonywanie skryptu w przypadku wystąpienia błędu
$ErrorActionPreference = "Stop"

# Zawartość pliku .env
# Używamy apostrofów w @''@, aby PowerShell nie próbował interpretować ${...} jako swoich zmiennych.
$envContent = @'
APP_NAME=czaspracy
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
'@

# =================================================================================
# GŁÓWNA LOGIKA SKRYPTU
# =================================================================================

try {
    # --- KROK 0: SPRAWDZENIE ZALEŻNOŚCI ---
    Write-Host "Krok 0: Sprawdzanie wymaganych narzędzi..." -ForegroundColor Yellow
    $requiredCommands = "composer", "php", "npm"
    foreach ($command in $requiredCommands) {
        if (-not (Get-Command $command -ErrorAction SilentlyContinue)) {
            throw "Błąd: Polecenie '$command' nie zostało znalezione. Upewnij się, że jest zainstalowane i dostępne w ścieżce systemowej (PATH)."
        }
    }
    Write-Host "Wszystkie wymagane narzędzia są dostępne." -ForegroundColor Green
    
    # --- KROK 1: INSTALACJA ZALEŻNOŚCI COMPOSERA ---
    Write-Host "`nKrok 1: Instalowanie zależności Composera (composer install)..." -ForegroundColor Yellow
    composer install
    Write-Host "Zależności Composera zainstalowane pomyślnie." -ForegroundColor Green

    # --- KROK 2: KONFIGURACJA PLIKU .ENV ---
    Write-Host "`nKrok 2: Konfiguracja pliku .env..." -ForegroundColor Yellow
    Set-Content -Path ".env" -Value $envContent -Encoding UTF8
    # Sprawdzenie, czy plik .env został utworzony w bieżącym katalogu
    if (Test-Path -Path ".env") {
        # Utworzenie pliku bazy danych SQLite, jeśli nie istnieje
        if ($envContent -match 'DB_CONNECTION=sqlite') {
            $dbFile = "database/database.sqlite"
            if (-not (Test-Path -Path $dbFile)) {
                Write-Host "Wykryto połączenie SQLite. Tworzenie pliku bazy danych: $dbFile" -ForegroundColor Cyan
                New-Item -Path "database" -ItemType Directory -Force | Out-Null
                New-Item -Path $dbFile -ItemType File -Force | Out-Null
            }
        }
        Write-Host "Plik .env został utworzony pomyślnie." -ForegroundColor Green
    } else {
        throw "Nie udało się utworzyć pliku .env!"
    }
    
    # --- KROK 3: GENEROWANIE KLUCZA APLIKACJI ---
    Write-Host "`nKrok 3: Generowanie klucza aplikacji (php artisan key:generate)..." -ForegroundColor Yellow
    php artisan key:generate
    Write-Host "Klucz aplikacji wygenerowany pomyślnie." -ForegroundColor Green

    # --- KROK 4: MIGRACJE BAZY DANYCH ---
    Write-Host "`nKrok 4: Uruchamianie migracji bazy danych (php artisan migrate)..." -ForegroundColor Yellow
    php artisan migrate
    Write-Host "Migracje zakończone pomyślnie." -ForegroundColor Green

    # --- KROK 5: SEEDER BAZY DANYCH ---
    Write-Host "`nKrok 5: Uruchamianie seeder'ów (php artisan db:seed)..." -ForegroundColor Yellow
    php artisan db:seed
    Write-Host "Seedowanie bazy danych zakończone pomyślnie." -ForegroundColor Green

    # --- KROK 6: INSTALACJA ZALEŻNOŚCI VITE/NPM ---
    Write-Host "`nKrok 6: Instalowanie zależności NPM (npm install)..." -ForegroundColor Yellow
    npm install
    Write-Host "Zależności NPM zainstalowane pomyślnie." -ForegroundColor Green

    # --- KROK 7: BUDOWANIE APLIKACJI ---
    Write-Host "`nKrok 7: Budowanie zasobów front-end (npm run build)..." -ForegroundColor Yellow
    npm run build
    Write-Host "Zasoby front-end zbudowane pomyślnie." -ForegroundColor Green

    Write-Host "`n================================================" -ForegroundColor Cyan
    Write-Host "Instalacja zakończona sukcesem! ✨" -ForegroundColor Cyan
    Write-Host "================================================" -ForegroundColor Cyan

    # --- KROK 8: URUCHOMIENIE SERWERA DEWELOPERSKIEGO ---
    Write-Host "`nKrok 8: Uruchamianie serwera deweloperskiego (php artisan serve)..." -ForegroundColor Yellow
    Write-Host "Serwer będzie działał pod adresem http://127.0.0.1:8000" -ForegroundColor Cyan
    Write-Host "Aby zatrzymać serwer, naciśnij CTRL+C w tym oknie." -ForegroundColor Cyan
    php artisan serve

}
catch {
    # W przypadku błędu wyświetl komunikat i zakończ skrypt
    Write-Host "`n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!" -ForegroundColor Red
    Write-Host "Wystąpił błąd podczas wykonywania skryptu:" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    Write-Host "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!" -ForegroundColor Red
    # Zatrzymaj okno, aby użytkownik mógł przeczytać błąd, zanim się zamknie
    Read-Host "Naciśnij Enter, aby zakończyć..."
    exit 1
}