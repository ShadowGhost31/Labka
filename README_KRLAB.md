

# Контрольне завдання

**Вимога:** написати **3 Entity**, пов’язані між собою, зробити **CRUD** на кожну з них, та **обмежити доступ до створення** по ролі **ADMIN**.

## Додані сутності

- `Category` (1) → `Product` (many)
- `Product` (1) → `ProductReview` (many)

## CRUD endpoints

> Усі `/api/*` (окрім `/api/login_check` та `/api/auth/register`) вже захищені JWT (див. `config/packages/security.yaml`).

### Categories
- `GET /api/categories`
- `GET /api/categories/{id}`
- `POST /api/categories` **(тільки ROLE_ADMIN)**
- `PUT|PATCH /api/categories/{id}`
- `DELETE /api/categories/{id}`

### Products
- `GET /api/products`
- `GET /api/products/{id}`
- `POST /api/products` **(тільки ROLE_ADMIN)**
- `PUT|PATCH /api/products/{id}`
- `DELETE /api/products/{id}`

### Product Reviews
- `GET /api/product-reviews`
- `GET /api/product-reviews/{id}`
- `POST /api/product-reviews` **(тільки ROLE_ADMIN)**
- `PUT|PATCH /api/product-reviews/{id}`
- `DELETE /api/product-reviews/{id}`

## Міграції

Додано міграцію: `migrations/Version20251226110000.php`.

Запуск:
```bash
php bin/console doctrine:migrations:migrate
```

## Як працює доступ ROLE_ADMIN

У create-методах використано:

```php
$this->denyAccessUnlessGranted('ROLE_ADMIN');
```

Роль береться з таблиць `roles` + `user_roles` і перетворюється в Symfony роль як `ROLE_<NAME>` (див. `User::getRoles()`).
---
