# Лабораторна робота №2 — Symfony + MySQL + Doctrine (CRUD, сервіси, валідація)

**Тема:** №12 — *Система управління проектами* (projects, tasks, members, statuses тощо)  
**База даних:** MySQL  
**ORM:** Doctrine ORM 

---

##  Встановити MySQL

- Встановив MySQL Server та MySQL Workbench.
- Перевірив, що сервер запускається та приймає підключення.

**Скріншоти:**
- `screens/mysql.png` — MySQL встановлено

---

##  Створити користувача в MySQL

Створив окремого користувача та базу для проєкту :

```sql
CREATE DATABASE labka CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'labka_user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON labka.* TO 'labka_user'@'localhost';
FLUSH PRIVILEGES;
```

##  Підключитися до бази даних в проєкті 


### Налаштування `.env`
У файлі `.env` або `.env.local` вказав рядок підключення:

```dotenv
DATABASE_URL="mysql://labka_user:password@127.0.0.1:3306/labka?serverVersion=8.0.0&charset=utf8mb4"


### Створення бази Doctrine

```bash
php bin/console doctrine:database:create
```

> Якщо Xdebug заважав у CLI — вимикав:
```powershell
$env:XDEBUG_MODE="off"
```


---

##  Проєктування БД 

**Обрана тема №12: Система управління проектами.**

Спроєктовано **12 таблиць** (Doctrine Entities):

1. `users` — користувачі
2. `roles` — ролі
3. `user_roles` — зв’язок користувач ↔ роль
4. `projects` — проєкти
5. `project_members` — учасники проєкту (user + role)
6. `task_statuses` — статуси задач
7. `tasks` — задачі
8. `comments` — коментарі до задач
9. `labels` — мітки
10. `task_labels` — зв’язок задача ↔ мітка
11. `attachments` — вкладення задач
12. `time_entries` — облік часу по задачах

Перевірка, що всі Entity підхоплені Doctrine:

```bash
php bin/console doctrine:mapping:info
```

Створення міграції та застосування:

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```


---

##  CRUD для всіх таблиць 

Зроблено **CRUD (GET/POST/PUT/DELETE)** для кожної таблиці у різних контролерах.

### Список API-роутів
Перевірка роутів:

```bash
php bin/console debug:router | findstr api
```

Приклади доступних endpoint-ів:
- `/api/users`
- `/api/roles`
- `/api/user-roles`
- `/api/projects`
- `/api/project-members`
- `/api/task-statuses`
- `/api/tasks`
- `/api/comments`
- `/api/labels`
- `/api/task-labels`
- `/api/attachments`
- `/api/time-entries`


### Приклади запитів 

> У PowerShell зручно тестувати через `Invoke-RestMethod`.

**Створити роль:**
```powershell
Invoke-RestMethod -Method POST -Uri "http://localhost:8000/api/roles" -ContentType "application/json" -Body '{"name":"MANAGER"}'
```

**Створити користувача:**
```powershell
Invoke-RestMethod -Method POST -Uri "http://localhost:8000/api/users" -ContentType "application/json" -Body '{"email":"u1@test.com","name":"User 1"}'
```

**Прив’язати роль до користувача (зв’язок):**
```powershell
Invoke-RestMethod -Method POST -Uri "http://localhost:8000/api/user-roles" -ContentType "application/json" -Body '{"userId":1,"roleId":1}'
```

**Створити проєкт (зв’язок ownerId):**
```powershell
Invoke-RestMethod -Method POST -Uri "http://localhost:8000/api/projects" -ContentType "application/json" -Body '{"title":"Lab2 Project","description":"demo","ownerId":1}'
```

**Додати учасника в проєкт (зв’язки projectId/userId/roleId):**
```powershell
Invoke-RestMethod -Method POST -Uri "http://localhost:8000/api/project-members" -ContentType "application/json" -Body '{"projectId":1,"userId":1,"roleId":1}'
```


---

##  Логіка створення об’єктів винесена в сервіс

Логіку створення сутностей при `POST` винесено у сервіс (Factory), щоб контролери були “тонкі”.

**Файли:**
- `src/Service/EntityFactory.php` — створення сутностей (User, Project, Task, ...)

Контролери тепер викликають Factory замість `new Entity()`.


---

##  Валідатор полів як окремий сервіс

Реалізовано сервіс валідації, який перевіряє поля при створенні сутностей (`POST`):
- required fields
- типи даних (int/string)
- email-формат
- дати (workDate / dueAt)
- інші обмеження

**Файли:**
- `src/Service/RequestValidator.php` — валідація вхідних даних

