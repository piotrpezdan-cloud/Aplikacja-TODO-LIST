# API

Wszystkie endpointy użytkownika poza rejestracją i logowaniem wymagają nagłówka:

```http
Authorization: Bearer TOKEN
```

## Auth

- `POST /api/v1/register`: rejestracja użytkownika.
- `POST /api/v1/login`: logowanie i wydanie tokenu Sanctum.
- `POST /api/v1/logout`: usunięcie bieżącego tokenu.
- `GET /api/v1/me`: dane zalogowanego użytkownika.

## Profile

- `PUT /api/v1/profile`: aktualizacja imienia, nazwiska, emaila i opcjonalnie języka.
- `PUT /api/v1/profile/password`: zmiana hasła.

## Categories

- `GET /api/v1/categories`: aktywne kategorie dostępne dla użytkownika.

## Projects

- `GET /api/v1/projects`: lista własnych projektów.
- `POST /api/v1/projects`: utworzenie projektu.
- `GET /api/v1/projects/{project}`: szczegóły własnego projektu.
- `PUT /api/v1/projects/{project}`: aktualizacja własnego projektu.
- `DELETE /api/v1/projects/{project}`: usunięcie własnego projektu.

Pola: `name`, `slug`, `description`, `status` (`active`, `archived`), `due_date`.

## Tasks

- `GET /api/v1/tasks`: lista własnych zadań.
- `POST /api/v1/tasks`: utworzenie zadania.
- `GET /api/v1/tasks/{task}`: szczegóły własnego zadania.
- `PUT /api/v1/tasks/{task}`: aktualizacja zadania.
- `DELETE /api/v1/tasks/{task}`: usunięcie zadania.
- `PATCH /api/v1/tasks/{task}/status`: zmiana statusu.
- `PATCH /api/v1/tasks/{task}/complete`: ustawienie statusu `done`.
- `PATCH /api/v1/tasks/{task}/archive`: ustawienie statusu `archived`.

Filtry: `status`, `priority`, `category_id`, `project_id`, `search`.

Statusy: `new`, `in_progress`, `done`, `archived`.

Priorytety: `low`, `medium`, `high`.

## Comments

- `GET /api/v1/tasks/{task}/comments`: komentarze własnego zadania.
- `POST /api/v1/tasks/{task}/comments`: dodanie komentarza.
- `PUT /api/v1/comments/{comment}`: edycja własnego komentarza.
- `DELETE /api/v1/comments/{comment}`: usunięcie własnego komentarza.

## Stats

- `GET /api/v1/stats`: liczby zadań i projektów zalogowanego użytkownika.
