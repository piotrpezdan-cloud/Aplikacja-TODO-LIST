# Dokumentacja Techniczna

## Cel I Zakres

Projekt jest systemem bazodanowym ToDo List z API, panelem administracyjnym oraz responsywnym klientem użytkownika. Zakres obejmuje rejestrację, logowanie, profil, CRUD zadań, CRUD projektów, kategorie, komentarze, panel admina, audit logi, mail panel, migracje, seedery, testy i Docker.

## Architektura

Aplikacja działa w Laravel 11. API `/api/v1` korzysta z kontrolerów, Form Requestów, Resource classes i Eloquent. Panel admina jest zbudowany w Blade. Klient `/user` jest aplikacją Vue 3 budowaną przez Vite i komunikuje się z API przez token Sanctum.

## Wybór MySQL

Wybrano MySQL 8.0, ponieważ jest stabilnym SZBD relacyjnym, dobrze wspieranym przez Laravel, prostym do uruchomienia w Dockerze i wystarczającym dla relacji, indeksów, kluczy obcych oraz ograniczeń unikalności wymaganych w projekcie.

## Model Danych

Główne encje:

- `users`: konta użytkowników i administratorów,
- `projects`: grupowanie zadań użytkownika,
- `tasks`: główna encja biznesowa,
- `categories`: słownik kategorii,
- `task_comments`: komentarze do zadań,
- `audit_logs`: historia wybranych operacji administracyjnych.

Relacje:

- `User` ma wiele `Project`, `Task`, `TaskComment`, `AuditLog`,
- `Project` ma wiele `Task`,
- `Category` ma wiele `Task`,
- `Task` należy do `User`, opcjonalnie do `Project` i opcjonalnie do `Category`,
- `Task` ma wiele `TaskComment`,
- `TaskComment` należy do `Task` i `User`.

## Integralność I Usuwanie

Usunięcie użytkownika usuwa jego projekty, zadania i komentarze przez `cascadeOnDelete`, ponieważ dane są prywatną przestrzenią użytkownika. Usunięcie zadania usuwa komentarze, bo komentarz bez zadania nie ma sensu biznesowego. Usunięcie kategorii albo projektu ustawia FK w zadaniu na `null`, żeby nie tracić historii zadania tylko dlatego, że słownik lub projekt został usunięty.

## Migracje

Projekt używa iteracyjnych migracji:

- początkowe tabele użytkowników, kategorii, zadań, audit logów i tabel technicznych,
- migracja `projects`,
- migracja rozszerzająca `tasks` o `project_id` i indeksy,
- migracja `task_comments`.

Każda migracja posiada `up()` i `down()`.

## Indeksy I Optymalizacja

Dodano indeksy:

- `tasks(user_id, status)` dla list i filtrów użytkownika,
- `tasks(user_id, priority)` dla filtrowania po priorytecie,
- `tasks(project_id, status)` dla widoków projektowych,
- `tasks(category_id)` dla filtrowania po kategorii,
- `tasks(due_date)` dla terminów,
- `projects(user_id)` i unique `projects(user_id, slug)`,
- `task_comments(task_id)` i `task_comments(user_id)`.

## Seedery

Seedery tworzą konto admina, konto testowe, kategorie, projekty, kilkanaście zadań, komentarze i przykładowy audit log. Dane stałe są tworzone przez `updateOrCreate` albo `firstOrCreate`, więc ponowne seedowanie nie powinno dublować kluczowych rekordów.

## Docker

`docker-compose.yml` uruchamia `app`, `nginx`, `mysql`, `redis`, `mailpit`, `queue` i `scheduler`. Nginx serwuje aplikację na porcie `8000`, MySQL działa wewnątrz sieci Docker na hoście `mysql`, a Mailpit jest dostępny pod `http://localhost:8025`.

## Testowanie

Testy feature obejmują auth, kategorie, CRUD zadań, filtrowanie, ochronę cudzych zasobów, CRUD projektów, komentarze, wymóg autoryzacji i podstawowy dostęp do panelu admina. W środowisku testowym używany jest SQLite in-memory.

## Wersjonowanie I Rozwój

Kod należy rozwijać małymi migracjami i testami feature dla każdego nowego zachowania API. Endpointy API powinny pozostać pod `/api/v1`, a zmiany łamiące kontrakt należy dokumentować w `docs/API.md`.
