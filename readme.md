# ToDo List / Strona-main

System zarządzania zadaniami z backendem API, panelem administracyjnym i responsywnym klientem webowym dla użytkownika końcowego. Klient `/user` działa wygodnie na telefonie i pełni rolę mobilnego/PWA-like frontendu komunikującego się z API Laravel Sanctum.

## Grupa

Projekt zgłoszony jako praca 3-osobowa. Repozytorium: `piotrpezdan-cloud/Aplikacja-TODO-LIST`.
Tobiasz Jabłoński - Full Stack Developer
Piotr Pezdan - Full Stack Developer
Igor Harmala - Full Stack Developer

## Technologie

- Laravel 11, PHP 8.3
- MySQL 8.0 jako główna baza danych
- Laravel Sanctum dla API
- Blade + Bootstrap dla panelu admina
- Vue 3, Pinia, Vue Router i Vite dla klienta `/user`
- Docker Compose: `app`, `nginx`, `mysql`, `redis`, `mailpit`, `adminer`, `queue`, `scheduler`

## Uruchomienie

Wymagania: Docker Desktop z Docker Compose.

```bash
docker compose up -d --build
```

Kontener `app` automatycznie:

- tworzy `.env` z `.env.example`, jeśli go nie ma,
- instaluje zależności Composer,
- instaluje zależności npm,
- buduje frontend przez `npm run build`,
- generuje `APP_KEY`, jeśli go brakuje,
- wykonuje migracje z seederami,
- uruchamia `php-fpm`.

Adresy:

- aplikacja: `http://localhost:8000`
- panel admina: `http://localhost:8000/admin/login`
- klient użytkownika: `http://localhost:8000/user/login`
- Mailpit: `http://localhost:8025`
- Adminer / panel bazy danych: `http://localhost:8080`

Dane logowania do Adminera:

- System: `MySQL`
- Server: `mysql`
- Username: `todo`
- Password: `todo`
- Database: `todo_list`

## Konta Testowe

Admin:

- email: `admin@todo-list.local`
- hasło: `Admin123!`

Użytkownik:

- email: `jacek91@example.net`
- hasło: `password`

## Reset Bazy

```bash
docker compose down -v
docker compose up -d --build
```

Ręczne odświeżenie migracji:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

## Testy I Build

```bash
docker compose exec app php artisan test
docker compose exec app npm run build
```

## Baza Danych

Finalny model obejmuje encje: `users`, `projects`, `tasks`, `categories`, `task_comments`, `audit_logs` oraz tabele techniczne Laravel (`personal_access_tokens`, `jobs`, `cache`). Migracje mają metody `up()` i `down()`, seedery tworzą dane testowe idempotentnie przez `updateOrCreate` / `firstOrCreate`.

Najważniejsze reguły integralności:

- usunięcie użytkownika usuwa jego projekty, zadania i komentarze,
- usunięcie projektu ustawia `tasks.project_id` na `null`,
- usunięcie kategorii ustawia `tasks.category_id` na `null`,
- usunięcie zadania usuwa jego komentarze,
- `projects` ma unikalność `user_id + slug`,
- zadania mają indeksy pod filtrowanie po statusie, priorytecie, projekcie, kategorii i terminie.

## API

API jest wersjonowane prefiksem `/api/v1`. Obejmuje auth, profil, kategorie, projekty, zadania, komentarze i statystyki. Endpointy użytkownika wymagają `auth:sanctum` i ograniczają dostęp do własnych danych.

Szczegóły są w [docs/API.md](docs/API.md).

## Dokumentacja

- [ERD](docs/ERD.md)
- [Dokumentacja techniczna](docs/TECHNICAL_DOCUMENTATION.md)
- [API](docs/API.md)
- [Testowanie](docs/TESTING.md)
