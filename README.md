# PHP Blog — тестовое задание

## Стек

- PHP 8.5, Composer;
- Smarty 5;
- MySQL 8.4, PDO;
- nginx, PHP-FPM;
- SCSS, Dart Sass;
- Docker и Docker Compose.

## Запуск

Требуются Docker и Docker Compose.

```bash
cp .env.example .env
docker compose build
docker compose run --rm --no-deps php composer install
docker compose run --rm --no-deps assets npm ci
docker compose up -d
docker compose exec php composer db:migrate
docker compose exec php composer db:seed
```

Приложение будет доступно по адресу <http://localhost:8080>.

Остановка:

```bash
docker compose down
```

## Тестовые данные

Повторный запуск сидинга полностью заменяет категории, статьи и связи между ними:

```bash
docker compose exec php composer db:seed
```

## Проверка качества

Все проверки запускаются в PHP-контейнере:

```bash
make quality
```

При запуске тестов команда автоматически создаёт отдельную базу из
`TEST_DB_NAME`, выдаёт к ней доступ пользователю приложения и применяет
миграции. Данные основной базы не изменяются.

Запуск тестов:

```bash
make test
```

Отдельные проверки:

```bash
docker compose run --rm --no-deps php composer cs-check
docker compose run --rm --no-deps php composer stan
```

Автоматическое исправление стиля:

```bash
docker compose run --rm --no-deps php composer cs-fix
```

## Стили

Исходные стили находятся в `assets/scss/main.scss`, скомпилированный CSS — в `public/assets/css/main.css`.

Установка frontend-зависимостей:

```bash
docker compose run --rm --no-deps assets npm ci
```

Разовая сборка CSS:

```bash
docker compose run --rm --no-deps assets npm run build:css
```

При обычном `docker compose up -d` сервис `assets` запускает Sass в watch-режиме
и пересобирает CSS после изменений `main.scss`.
