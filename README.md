# Phoenix LMS

Учебная платформа на Laravel 11 + Vue 3 (Pinia, Vite) с ролевой моделью (teacher/student/admin), API на Sanctum и готовыми Docker‑контейнерами для разработки.

## Стек
- Backend: Laravel 11, Sanctum, Spatie Permissions, PHP 8.4 (php-fpm)
- Frontend: Vue 3, Pinia, Vue Router, Vite, TailwindCSS
- Инфраструктура: Docker Compose (nginx + php-fpm + MySQL 8 + Redis)

## Быстрый старт (Docker)
Требуется установленный Docker Desktop или совместимый runtime.

```bash
# поднять контейнеры
docker compose up -d

# применить миграции и загрузить тестовые данные
docker compose exec app php artisan migrate --seed

# собрать фронтенд-ассеты (обязательно после первого запуска/изменений)
docker compose exec app npm run build
```

После сборки открой `http://localhost:8080`.

### Тестовые аккаунты
- Admin: `admin@example.com` / `password`
- Teacher: `teacher@example.com` / `password`
- Student: `student@example.com` / `password`
- Дополнительно: `superadmin@example.com`, `content@example.com`, `usermanager@example.com`, `rolemanager@example.com`, `user@example.com`, `test@example.com` (все с паролем `password`)

## Разработка с hot-reload
Если нужен Vite HMR вместо сборки:

```bash
docker compose exec app npm run dev -- --host 0.0.0.0 --port 5173
```

И открой фронт по адресу, который выведет Vite (обычно `http://localhost:5173`). nginx в этом случае продолжит отдавать backend на `http://localhost:8080`.

## Полезные команды
- Очистить кеши после изменений конфигов/провайдеров:
  ```bash
  docker compose exec app php artisan optimize:clear
  ```
- Прогнать тесты:
  ```bash
  docker compose exec app php artisan test
  ```
- Запустить очередь (если понадобится фоновые задачи):
  ```bash
  docker compose exec app php artisan queue:listen
  ```

## Структура
- `docker-compose.yml` — окружение nginx/php-fpm/mysql/redis
- `docker/php/Dockerfile` — сборка php-fpm c Node и Composer
- `backend/` — Laravel + Vue (ресурсы в `backend/resources/js`, стили в `backend/resources/css`)
- `backend/routes/api.php` — API для фронтенда
- `backend/app/Providers/AppServiceProvider.php` — настройка rate limiter `api`
- `backend/database/seeders/DatabaseSeeder.php` — создание ролей, пользователей, курсов

## Типичный цикл разработки
1. Внести изменения в backend или frontend.
2. Для backend достаточно `docker compose exec app php artisan optimize:clear` (если менялись провайдеры/роутинги).
3. Для frontend — `docker compose exec app npm run build` или держать `npm run dev`.
4. Обновить страницу в браузере (hard refresh для ассетов: `Ctrl+F5`).

## Точки входа
- Веб‑клиент: `http://localhost:8080`
- API: `http://localhost:8080/api/*` (авторизация по Bearer‑токену Sanctum)

## Troubleshooting
- 500 при логине / сообщение «Неверный email или пароль» после изменений: убедись, что выполнены миграции (таблица `personal_access_tokens`) и очищены кеши `optimize:clear`.
- Если Vite не отдает ассеты, собери `npm run build` и перезапусти контейнеры `docker compose restart web app`.
