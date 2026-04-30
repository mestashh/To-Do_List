# To-Do List REST API

REST API для управления списком задач (To-Do List), реализованный на PHP 8.3 / Laravel 13.

Каждая задача принадлежит конкретному пользователю — для работы с `/tasks` требуется аутентификация по Bearer-токену (Laravel Sanctum).

---

## Стек

- PHP 8.3
- Laravel 13
- Laravel Sanctum 4 (токены API)
- PHPUnit 12

---
## Установка
```bash
./setup.sh
php artisan migrate
php artisan serve
```

API будет доступен по адресу `http://127.0.0.1:8000/api/v1`.

---

## Аутентификация

API использует **Bearer-токен** через Laravel Sanctum.

1. Зарегистрировать пользователя (`POST /api/v1/registration`).
2. Получить токен (`POST /api/v1/authorization`).
3. Передавать токен в заголовке каждого запроса к `/tasks`:

```
Authorization: Bearer <token>
Accept: application/json
Content-type: application/json
```

---

## Эндпоинты

Базовый путь: `/api/v1`

### Auth

| Метод | URL              | Описание                             | Auth |
|-------|------------------|--------------------------------------|------|
| POST  | `/registration`  | Регистрация нового пользователя      | —    |
| POST  | `/authorization` | Получение Bearer-токена              | —    |

### Tasks

Пользователь видит и изменяет только свои задачи. Чужие задачи возвращают `403 Forbidden`.

---

## Примеры запросов

### Регистрация

```http
POST /api/v1/registration
Content-Type: application/json
Accept: Application/json

{
  "name": "Name",
  "email": "email@example.com",
  "password": "password"
}
```

Ответ `201`:

```json
{
  "data": {
    "id": 1,
    "name": "Name",
    "email": "email@example.com"
  }
}
```

### Получение токена

```http
POST /api/v1/authorization
Content-Type: application/json
Accept: Application/json

{
  "email": "email@example.com",
  "password": "password"
}
```

Ответ `200`:

```json
{
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "token_type": "Bearer"
}
```

### Создание задачи

```http
POST /api/v1/tasks
Authorization: Bearer <token>
Content-Type: application/json
Accept: application/json

{
  "title": "Сделать задание",
  "description": "До января",
  "status": "pending"
}
```

Ответ `201`:

```json
{
  "data": {
    "user_id": 1,
    "id": 1,
    "title": "Сделать задание",
    "description": "До января",
    "status": "pending"
  }
}
```

### Список задач

```http
GET /api/v1/tasks
Authorization: Bearer <token>
Content-Type: application/json
Accept: application/json
```

Ответ `200` — стандартный paginated-ответ Laravel Resource Collection (`data`, `links`, `meta`).

### Получение одной задачи

```http
GET /api/v1/tasks/1
Authorization: Bearer <token>
Accept: application/json
Content-Type: application/json
```

### Обновление

```http
PUT /api/v1/tasks/1
Authorization: Bearer <token>
Content-Type: application/json
Content-Type: application/json

{
  "title": "Сделать задачу до января",
  "status": "done"
}
```

`UpdateTaskRequest` использует правило `sometimes`, поэтому передавать можно только изменяемые поля.

### Удаление

```http
DELETE /api/v1/tasks/1
Authorization: Bearer <token>
Content-Type: application/json
Content-Type: application/json
```

Ответ `204 No Content`.

---

## Валидация

### `POST /tasks`

| Поле          | Правила                                              |
|---------------|------------------------------------------------------|
| `title`       | required, string, max:255                            |
| `description` | required, string                                     |
| `status`      | enum: `done` \| `missed` \| `pending`                |

### `PUT /tasks/{id}`

Все поля опциональны (`sometimes`), но если переданы — валидируются по тем же правилам.

### `POST /registration`

| Поле       | Правила                          |
|------------|----------------------------------|
| `name`     | required, string, max:255        |
| `email`    | required, unique:users,email     |
| `password` | required, string, max:50         |

## Тесты

Покрытие — feature-тесты на CRUD задач и аутентификацию (`tests/Feature/TaskTest.php`).

```bash
php artisan test
```

Тесты используют `RefreshDatabase`, поэтому требуется доступ к тестовой БД (для MySQL — отдельная БД, либо переключиться на SQLite в `phpunit.xml`).

Покрываемые сценарии:
- регистрация и получение токена;
- гость не может выполнять CRUD над задачами (`401`);
- авторизованный пользователь может создавать / обновлять / удалять свои задачи;
- пользователь не может изменять или удалять чужие задачи (`403`).

---

## Структура БД

### `users`

Стандартная таблица Laravel + Sanctum (`name`, `email`, `password`, ...).

### `tasks`

| Поле          | Тип               | Примечание                          |
|---------------|-------------------|-------------------------------------|
| `id`          | bigint, PK        |                                     |
| `user_id`     | foreignId → users | restrictOnDelete                    |
| `title`       | string(255)       |                                     |
| `description` | text              |                                     |
| `status`      | enum              | `done`, `missed`, `pending` (default: pending) |
| timestamps    | created/updated   |                                     |
| `deleted_at`  | soft delete       |                                     |

### `personal_access_tokens`

Стандартная таблица Sanctum.
