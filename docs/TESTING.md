# Testowanie

## Uruchomienie

```bash
docker compose exec app php artisan test
```

Po pełnym resecie:

```bash
docker compose down -v
docker compose up -d --build
docker compose exec app php artisan test
```

## Zakres Testów

Testy feature sprawdzają:

- rejestrację i logowanie użytkownika,
- pobranie kategorii,
- CRUD tasków,
- filtrowanie tasków po statusie, priorytecie i projekcie,
- blokadę odczytu i edycji cudzego taska,
- CRUD projektów,
- blokadę odczytu i edycji cudzego projektu,
- dodanie komentarza do własnego taska,
- blokadę komentowania cudzego taska,
- edycję i usunięcie własnego komentarza,
- wymóg autoryzacji dla endpointów API,
- dostęp admina do dashboardu i blokadę zwykłego użytkownika.

## CRUD

Pokryte operacje CRUD:

- `tasks`: create, read/list, update, delete,
- `projects`: create, read/list/show, update, delete,
- `task_comments`: create, read/list, update, delete,
- `categories`: read dla użytkownika, pełny CRUD w panelu admina.
