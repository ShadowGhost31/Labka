# Лабораторна робота №5 — Security + JWT 

## Вимоги
1) Додати модуль security. Усі endpoints мають бути захищені.
2) Використати JWT-auth.
3) Публічні endpoints: **тільки** `/api/login_check` та `/api/auth/register`.

---

##  Налаштування Security (security.yaml)
Файл: `config/packages/security.yaml`

- Firewall `login` для `json_login` (отримання токена за email/password)
- Firewall `api` для всіх `/api/**` з `jwt: ~`
- `access_control`:
  - PUBLIC: `/api/login_check`, `/api/auth/register`
  - PROTECTED: все інше `^/api`

---

##  Реєстрація користувача (Public)
Endpoint: `POST /api/auth/register`

**PowerShell:**
```powershell
Invoke-RestMethod -Method POST -Uri "http://localhost:8000/api/auth/register" `
  -ContentType "application/json" `
  -Body '{"email":"jwt@test.com","name":"JWT User","password":"123456"}'
```

---

##  Отримання JWT токена (Public)
Endpoint: `POST /api/login_check`

```powershell
$token = (Invoke-RestMethod -Method POST -Uri "http://localhost:8000/api/login_check" `
  -ContentType "application/json" `
  -Body '{"email":"jwt@test.com","password":"123456"}').token
$token
```

---

##  Виклик захищених endpoints (Protected)
Будь-який endpoint `/api/**` (крім public) потребує заголовка:

`Authorization: Bearer <token>`

Приклад:
```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/users" -Headers @{ Authorization = "Bearer $token" }
```

Без токена очікується `401 Unauthorized`.

---

##  Міграція
Додано поле `password` у таблицю `users` (для збереження hash).

Запуск:
```bash
php bin/console doctrine:migrations:migrate
```
