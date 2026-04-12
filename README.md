# Phoenix LMS

Phoenix LMS — учебная платформа с чистой архитектурой: **Laravel API** (backend) + **Vue 3 SPA** (frontend), запускаемые через Docker Compose.

## Архитектура

```
phoenix-lms/
├── backend/      # Laravel 11 — чистый REST API (PHP-FPM)
├── frontend/     # Vue 3 SPA — Vite, Pinia, Vue Router, TailwindCSS
└── docker/       # Docker конфигурация
    ├── php/      # PHP-FPM сервис
    └── nginx/    # Nginx: собирает frontend + проксирует /api/* на PHP
```

## Стек

- **Backend**: Laravel 11, Sanctum, Spatie Permissions, PHP 8.4
- **Frontend**: Vue 3, Pinia, Vue Router 4, Vite 6, TailwindCSS 3
- **Infra**: Docker Compose (`nginx` + `app` + `mysql:8` + `redis`)

## Запуск через Docker

```bash
docker compose build
docker compose up
```

Приложение будет доступно на [http://localhost:8080](http://localhost:8080).


> PHP-FPM обрабатывает только `/api/*` и `/sanctum/*`.

## Локальная разработка фронтенда

```bash
cd frontend
npm install
npm run dev
```

При локальном запуске (`npm run dev`) запросы к `/api/` проксируются на `http://localhost:8080` (Docker).

## Структура ролей

| Роль           | Возможности                                  |
|----------------|----------------------------------------------|
| `student`      | Запись на курсы, просмотр лекций, тесты      |
| `teacher`      | Управление курсами, лекциями, тестами        |
| `admin`        | Полный доступ                                |

## Сидированные пользователи (dev)

| Email                    | Пароль   | Роль    |
|--------------------------|----------|---------|
| admin@phoenix.com        | password | admin   |
| teacher@phoenix.com      | password | teacher |
| student@phoenix.com      | password | student |
