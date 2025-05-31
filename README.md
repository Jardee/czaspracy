## Aplikacja uruchomiona jest na zewnętrznym serwerze

[http://37.233.98.57:8082/login](http://37.233.98.57:8082/login)


## Konta do testowania

* Admin User admin@example.com
* Supervisor User supervisor@example.com
* Employee One employee1@example.com
* Employee Two employee2@example.com

* Do Każdego hasło to `password`


## Instrukcja instalacji i uruchomienia aplikacji

1.  **Pobierz paczkę z GitHuba:**
    [https://github.com/Jardee/czaspracy/archive/refs/heads/main.zip](https://github.com/Jardee/czaspracy/archive/refs/heads/main.zip)

2.  **Wejdź do folderu z aplikacją:**

    ```bash
    cd "ścieżka do folderu"
    ```
    (Zamiast `"ścieżka do folderu"` wstaw swoją ścieżkę do folderu).

3.  **Zainstaluj zależności Composera:**
    ```bash
    composer install
    ```

4.  **Skonfiguruj plik .env:**
    ```bash
    cp .env.example .env
    ```
    Edytuj plik `.env` i wprowadź odpowiednie ustawienia (nazwa aplikacji, baza danych, mail itp.).

    zawartość pliku `.env`:
    ```
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
    ```

5.  **Wygeneruj klucz aplikacji:**
    ```bash
    php artisan key:generate
    ```

6.  **Uruchom migracje bazy danych:**
    ```bash
    php artisan migrate
    ```

7.  **Uruchom seedera bazy danych:**
    ```bash
    php artisan db:seed
    ```

8.  **Zainstaluj zależności Vite:**
    ```bash
    npm install
    ```

9.  **Zbuduj aplikację:**
    ```bash
    npm run build
    ```

10. **Umieść aplikację w XAMPP (opcjonalnie):**
    Możesz umieścić aplikację w folderze `xampp/htdocs`. Aplikacja będzie dostępna w katalogu `/public`.



# Czas Pracy - Aplikacja Laravel

Aplikacja "Czas Pracy" jest systemem internetowym napisanym w frameworku Laravel, służącym do zarządzania czasem pracy pracowników. Umożliwia ona rejestrowanie wpisów czasu pracy, dodawanie do nich komentarzy oraz zarządzanie użytkownikami i ich rolami.

## Główne funkcjonalności

### 1. Zarządzanie Użytkownikami i Rolami
* System definiuje trzy role użytkowników: Pracownik (`employee`), Przełożony (`supervisor`) i Administrator (`admin`).
* Administratorzy mają możliwość zarządzania użytkownikami:
    * Przeglądanie listy użytkowników wraz z ich rolami.
    * Dodawanie nowych użytkowników, przypisując im imię, nazwisko, email, hasło oraz rolę.
    * Edycja danych istniejących użytkowników, w tym zmiana imienia, nazwiska, emaila, hasła oraz roli.
    * Usuwanie użytkowników (z zabezpieczeniem przed usunięciem samego siebie oraz ostatniego administratora).
* Logika biznesowa związana z uprawnieniami (np. kto może zarządzać użytkownikami) jest zdefiniowana w `app/Providers/AuthServiceProvider.php` oraz sprawdzana w kontrolerach.
```
Gate::define('manage-users', function(User $user) {
            return $user->role === User::ROLE_ADMIN;
        });
```

### 2. Rejestracja i Zarządzanie Czasem Pracy
* Pracownicy (lub uprawnieni użytkownicy jak Przełożony/Administrator) mogą dodawać wpisy czasu pracy.
* Każdy wpis zawiera informacje o pracowniku, dacie pracy oraz liczbie przepracowanych godzin.
* Istnieje walidacja zapewniająca, że dla danego pracownika można dodać tylko jeden wpis na dany dzień. Maksymalna liczba godzin w jednym wpisie to 13.
* Użytkownicy z odpowiednimi uprawnieniami (Administrator, Przełożony) mogą przeglądać wpisy czasu pracy wszystkich pracowników. Pracownicy widzą tylko swoje wpisy.
* Dostępna jest funkcjonalność filtrowania wpisów czasu pracy po zakresie dat oraz dla Admina/Przełożonego po konkretnym pracowniku.
* Administratorzy i Przełożeni mogą edytować i usuwać istniejące wpisy czasu pracy.

### 3. Komentarze do Wpisów Czasu Pracy
* Administratorzy i Przełożeni mogą dodawać komentarze do wszystkich wpisów. Pracownicy mogą komentować tylko własne wpisy.
* Komentarze są wyświetlane na stronie szczegółów wpisu czasu pracy.
* Użytkownicy mogą edytować własne komentarze. Administratorzy mogą edytować wszystkie komentarze.
* Użytkownicy mogą usuwać własne komentarze. Administratorzy i Przełożeni mogą usuwać wszystkie komentarze.
* Edycja komentarzy odbywa się dynamicznie (za pomocą JavaScript i Axios) bez przeładowania całej strony.

### 4. Uwierzytelnianie i Profil Użytkownika
* Aplikacja wykorzystuje standardowy system uwierzytelniania Laravel Breeze, obejmujący rejestrację, logowanie, resetowanie hasła.
* Zalogowani użytkownicy mają dostęp do swojego profilu, gdzie mogą zaktualizować swoje dane (imię, email) oraz zmienić hasło.
* Użytkownicy mogą również usunąć swoje konto.

## Struktura Techniczna (ogólny zarys)

* **Modele Eloquent (`app/Models`)**:
    * `User`: Reprezentuje użytkownika systemu, przechowuje informacje o roli.
    * `WorkEntry`: Reprezentuje pojedynczy wpis czasu pracy, powiązany z użytkownikiem (pracownikiem) oraz użytkownikiem, który go wprowadził.
    * `Comment`: Reprezentuje komentarz do wpisu czasu pracy, powiązany z wpisem oraz użytkownikiem, który go dodał.
* **Kontrolery (`app/Http/Controllers`)**:
    * `WorkEntryController`: Obsługuje logikę związaną z wpisami czasu pracy (CRUD, listowanie, filtrowanie).
    * `CommentController`: Zarządza komentarzami do wpisów (dodawanie, edycja, usuwanie).
    * `Admin/UserController`: Odpowiada za zarządzanie użytkownikami w panelu administracyjnym.
    * Kontrolery w `Auth` (`app/Http/Controllers/Auth`): Obsługują procesy uwierzytelniania.
    * `ProfileController`: Zarządza aktualizacją profilu użytkownika.
* **Routing (`routes/web.php`)**: Definiuje ścieżki URL aplikacji i mapuje je na odpowiednie akcje w kontrolerach, w tym grupy chronione middlewarem `auth` oraz ścieżki dla panelu administracyjnego.
* **Widoki Blade (`resources/views`)**: Tworzą interfejs użytkownika, wykorzystując komponenty Blade do budowy UI
* **Polityki Dostępu (`app/Providers/AuthServiceProvider.php`)**: Centralne miejsce definiowania uprawnień dla poszczególnych akcji w systemie (np. `create-work-entry`, `manage-users`).
* **Seedery (`database/seeders`)**: Umożliwiają wypełnienie bazy danych początkowymi danymi, w tym przykładowymi użytkownikami różnych ról, wpisami pracy i komentarzami.
* **Walidacja**: Aplikacja wykorzystuje reguły walidacji Laravel do sprawdzania poprawności danych wprowadzanych przez użytkowników, w tym niestandardową regułę `UniqueWorkEntryForUserAndDate`.
