# Лабораторна робота №7

## Завдання
Переписати CRUD операції, які були реалізовані в Controller, на **ApiResource** (API Platform).

## Результат
CRUD-операції відображаються в Swagger UI:
- `http://localhost:8000/api`

## Перевірка
```bash
composer install
php bin/console doctrine:migrations:migrate
php -S localhost:8000 -t public
```
