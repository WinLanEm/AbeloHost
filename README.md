# PHP Blog — тестовое задание

## Стек

- PHP 8.5, Composer;
- Smarty 5;
- MySQL 8.4, PDO;
- nginx, PHP-FPM;
- Docker и Docker Compose.

## Запуск

Требуются Docker и Docker Compose.

```bash
cp .env.example .env
docker compose build
docker compose run --rm --no-deps php composer install
docker compose up -d
```

Приложение будет доступно по адресу <http://localhost:8080>.

Остановка:

```bash
docker compose down
```
