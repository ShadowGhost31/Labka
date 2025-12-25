# Лабораторна робота №8

## Завдання
1) **Написати 2 Action** для будь-яких Entity.
2) **Написати 2 Event (Doctrine postUpdate)**.
3) **Написати 2 Extension** для колекцій (API Platform Doctrine ORM extensions).

---

##  Actions (2 шт.)

### Action #1: Project stats
- **Endpoint:** `GET /api/projects/{id}/stats`
- **Файл:** `src/Controller/Api/ProjectStatsAction.php`
- **Опис:** повертає статистику проєкту (кількість задач і учасників).

### Action #2: Assign current user to task
- **Endpoint:** `POST /api/tasks/{id}/assign-me`
- **Файл:** `src/Controller/Api/AssignMeToTaskAction.php`
- **Опис:** встановлює поточного користувача як `assignee` для вибраної задачі.

---

##  Doctrine Events (postUpdate) (2 шт.)

### Event #1: Task postUpdate
- **Файл:** `src/EventListener/TaskPostUpdateListener.php`
- **Подія:** `postUpdate`
- **Дія:** логування оновлення задачі.

### Event #2: Project postUpdate
- **Файл:** `src/EventListener/ProjectPostUpdateListener.php`
- **Подія:** `postUpdate`
- **Дія:** логування оновлення проєкту.

---

##  API Platform Extensions для колекцій (2 шт.)

> Extension-и застосовуються до **collection** і **item** запитів.
> Для **не адмінів** (`ROLE_ADMIN`) фільтрують дані по поточному користувачу.

### Extension #1: ProjectOwnerExtension
- **Файл:** `src/Extension/ProjectOwnerExtension.php`
- **Ресурс:** `Project`
- **Фільтр:** `project.owner = current_user`

### Extension #2: TaskCreatorExtension
- **Файл:** `src/Extension/TaskCreatorExtension.php`
- **Ресурс:** `Task`
- **Фільтр:** `task.creator = current_user`

Базовий клас:
- `src/Extension/AbstractCurrentUserExtension.php`

Теги сервісів:
- `config/services.yaml`

---

## Перевірка

Запуск:
```bash
composer install
php bin/console doctrine:migrations:migrate
php -S localhost:8000 -t public
```

Swagger:
- `http://localhost:8000/api`
