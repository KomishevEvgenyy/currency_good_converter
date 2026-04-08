SHELL := /bin/sh

COMPOSE := docker compose
APP_SERVICE := laravel.test
WORKER_SERVICE := queue-worker

.PHONY: help up down build restart logs ps shell composer artisan npm test phpstan setup clean install

help:
	@echo "Available targets:"
	@echo "  make install    - start containers and run composer install"
	@echo "  make setup      - start containers and run full project setup"
	@echo "  make up         - start containers in background"
	@echo "  make down       - stop containers"
	@echo "  make build      - build images"
	@echo "  make restart    - restart containers"
	@echo "  make logs       - follow container logs"
	@echo "  make ps         - show container status"
	@echo "  make shell      - open shell in app container"
	@echo "  make composer   - run composer command in app container"
	@echo "  make artisan    - run artisan command in app container"
	@echo "  make npm        - run npm command in app container"
	@echo "  make test       - run tests in app container"
	@echo "  make phpstan    - run static analysis in app container"
	@echo "  make clean      - stop containers and remove volumes"

up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

build:
	$(COMPOSE) build

reload:
	$(COMPOSE) down
	$(COMPOSE) up -d

rebuild: down build_up worker

build_up: build up

restart: reload worker

logs:
	$(COMPOSE) logs -f

ps:
	$(COMPOSE) ps

shell:
	$(COMPOSE) exec $(APP_SERVICE) sh

composer:
	$(COMPOSE) exec $(APP_SERVICE) composer $(filter-out $@,$(MAKECMDGOALS))

artisan:
	$(COMPOSE) exec $(APP_SERVICE) php artisan $(filter-out $@,$(MAKECMDGOALS))

npm:
	$(COMPOSE) exec $(APP_SERVICE) npm $(filter-out $@,$(MAKECMDGOALS))

test:
	$(COMPOSE) exec $(APP_SERVICE) php artisan test

phpstan:
	$(COMPOSE) exec $(APP_SERVICE) ./vendor/bin/phpstan analyse --configuration=phpstan.neon

install:
	$(COMPOSE) exec $(APP_SERVICE) composer install

setup: install

	$(COMPOSE) exec $(APP_SERVICE) php artisan key:generate
	$(COMPOSE) exec $(APP_SERVICE) php artisan migrate --force
	$(COMPOSE) exec $(APP_SERVICE) npm install
	$(COMPOSE) exec $(APP_SERVICE) npm run build

worker:
	$(COMPOSE) up -d $(WORKER_SERVICE)

run: setup worker
boot: build up setup worker

clean:
	$(COMPOSE) down -v --remove-orphans

%:
	@:
