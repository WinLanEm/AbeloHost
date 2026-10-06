.DEFAULT_GOAL := help
.NOTPARALLEL:

COMPOSE := docker compose

.PHONY: help init install build up stop down restart status logs shell \
	composer-install assets-install migrate seed db test-db test quality stan \
	cs-check cs-fix audit css css-watch

help:
	@echo "make init       Create .env, install dependencies, start and seed the app"
	@echo "make up         Start all services"
	@echo "make stop       Stop services without removing them"
	@echo "make down       Stop and remove containers"
	@echo "make restart    Restart all services"
	@echo "make logs       Follow service logs"
	@echo "make shell      Open a shell in the PHP container"
	@echo "make migrate    Apply database migrations"
	@echo "make seed       Replace application data with demo data"
	@echo "make test       Run the test suite against the test database"
	@echo "make quality    Run all quality checks"
	@echo "make cs-fix     Fix PHP code style"
	@echo "make css        Compile SCSS once"
	@echo "make css-watch  Watch and compile SCSS"

.env:
	cp .env.example .env

init: install up db

install: composer-install assets-install

build: .env
	$(COMPOSE) build

composer-install: build
	$(COMPOSE) run --rm --no-deps php composer install

assets-install: .env
	$(COMPOSE) run --rm --no-deps assets npm ci

up: .env
	$(COMPOSE) up --detach

stop:
	$(COMPOSE) stop

down:
	$(COMPOSE) down

restart: down up

status:
	$(COMPOSE) ps

logs:
	$(COMPOSE) logs --follow

shell: up
	$(COMPOSE) exec php sh

migrate: up
	$(COMPOSE) exec php composer db:migrate

seed: up
	$(COMPOSE) exec php composer db:seed

db: migrate seed

test-db: up
	$(COMPOSE) cp docker/mysql/prepare-test-database.sh mysql:/tmp/prepare-test-database.sh
	$(COMPOSE) exec mysql sh /tmp/prepare-test-database.sh

test quality: test-db
	$(COMPOSE) exec php composer $@

stan: up
	$(COMPOSE) exec php composer stan

cs-check: up
	$(COMPOSE) exec php composer cs-check

cs-fix: up
	$(COMPOSE) exec php composer cs-fix

audit: up
	$(COMPOSE) exec php composer security-audit

css: .env
	$(COMPOSE) run --rm --no-deps assets npm run build:css

css-watch: .env
	$(COMPOSE) run --rm --no-deps assets npm run watch:css
