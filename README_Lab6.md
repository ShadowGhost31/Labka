# Лабораторна робота №6 — API Platform CRUD

## Мета
Підключити **API Platform** і реалізувати **CRUD** для однієї сутності (**Test**), щоб усі операції відображались у **Swagger UI**.



##  Сутність Test (CRUD)

У проєкті додано сутність:

- `src/Entity/Test.php`

В ній описано `ApiResource` з операціями:

- `GET /api/tests` (collection)
- `POST /api/tests` (create)
- `GET /api/tests/{id}` (item)
- `PATCH /api/tests/{id}` (update частково)
- `DELETE /api/tests/{id}` (remove)

Також додані **Serializer groups**, щоб контролювати поля в різних операціях:

- `get:collection:test`
- `get:item:test`
- `post:collection:test`
- `patch:item:test`

##  Налаштування API Platform

Додано:

- `config/packages/api_platform.yaml`
- `config/routes/api_platform.yaml` (prefix `/api`)

##  Міграція

Додано міграцію:

- `migrations/Version20251225233000.php`

Запуск:

```bash
php bin/console doctrine:migrations:migrate
```

##  Запуск проєкту

```bash
symfony server:start
# або
php -S localhost:8000 -t public
```

##  Перевірка Swagger


- Swagger UI: `http://localhost:8000/api`
- OpenAPI JSON: `http://localhost:8000/api/docs.json`


##  Приклади запитів (curl)

### Створити (POST)
```bash
curl -X POST "http://localhost:8000/api/tests" \
  -H "Content-Type: application/ld+json" \
  -d '{"name":"Test item","price":"99.99"}'
```

### Отримати список (GET)
```bash
curl "http://localhost:8000/api/tests"
```

### Оновити (PATCH)
```bash
curl -X PATCH "http://localhost:8000/api/tests/<UUID>" \
  -H "Content-Type: application/merge-patch+json" \
  -d '{"price":"120.00"}'
```

### Видалити (DELETE)
```bash
curl -X DELETE "http://localhost:8000/api/tests/<UUID>"
```

---
