# Лабораторна робота №3 — Symfony Validation та Services


---

##  Constraints для Entity

Для всіх Entity у проєкті описано обмеження (Constraints) за допомогою Symfony Validator.

Використані основні Constraints:
- `NotNull`
- `NotBlank`
- `Length`
- `Positive`
- `Regex`
- інші відповідно до типів і логіки полів

Constraints описані безпосередньо над властивостями Entity через атрибути.

**Приклад:**
```php
#[ORM\Column(length: 50)]
#[NotBlank]
#[Length(min: 1, max: 50)]
private string $name;
```

---

##  RuntimeConstraintExceptionListener

Реалізовано `RuntimeConstraintExceptionListener`, який:
- перехоплює всі винятки в додатку
- приводить помилки валідації до єдиного JSON-формату
- визначає коректний HTTP-код відповіді

Listener зареєстрований у `services.yaml` як `kernel.event_listener`.

---

## RequestCheckerService

Створено сервіс `RequestCheckerService`, який:
- перевіряє наявність обовʼязкових полів у запиті
- виконує валідацію даних через Symfony Validator
- викидає HTTP-винятки у разі помилок валідації

Сервіс використовується у всіх контролерах при обробці запитів.

---

## Сервіси для кожної Entity

Для кожної Entity створено окремий Service, який:
- містить логіку створення та оновлення обʼєктів
- виконує валідацію через `RequestCheckerService`
- взаємодіє з `EntityManagerInterface`

Це дозволило винести бізнес-логіку з контролерів.

---

## Рефакторинг контролерів

Контролери з лабораторної роботи №2 були відрефакторені:
- логіка створення та оновлення перенесена у сервіси
- валідація виконується централізовано
- усі помилки повертаються у єдиному форматі

---
