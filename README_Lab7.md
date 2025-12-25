# Лабораторна робота №7 — API Platform (переписати CRUD з Controller на ApiResource)

## Мета
Переписати всі CRUD-операції, які були реалізовані через **Controller**, на **ApiResource** (API Platform), щоб CRUD був автоматично описаний та доступний у **Swagger UI**.

---

## Виконання


---

###  Перенесення CRUD з Controller → ApiResource
CRUD-контролери з папки `src/Controller/Api/*Controller.php` (для сутностей) були прибрані, а CRUD тепер описаний прямо на Entity через атрибут `#[ApiResource(...)]`.

Кожна сутність має операції:
- `GET` collection
- `POST`
- `GET` item
- `PATCH`
- `DELETE`

Також додані **Serializer Groups** (`<entity>:read`, `<entity>:write`), щоб Swagger коректно показував поля, а серіалізація не зациклювалась на звʼязках.

---

###  Перевірка у Swagger
Запуск:
```bash
php -S localhost:8000 -t public
```
Swagger UI:
- http://localhost:8000/api
---

## Результат
Всі CRUD-операції тепер працюють через **API Platform** як **ApiResource** та відображаються у Swagger UI.

---

