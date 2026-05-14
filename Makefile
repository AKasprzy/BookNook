export COMPOSE_DOCKER_CLI_BUILD=1
export DOCKER_BUILDKIT=1

DOCKER_COMPOSE_FILE := docker-compose.yml
APP_CONTAINER := app
DB_CONTAINER := db
NODE_CONTAINER := node
CURRENT_USER_ID := $(shell id -u)
CURRENT_GROUP_ID := $(shell id -g)

check-env:
	@if [ ! -f ".env" ]; then \
		exit 1; \
	fi

build: check-env
	@docker compose -f $(DOCKER_COMPOSE_FILE) build --pull

run:
	@docker compose -f $(DOCKER_COMPOSE_FILE) up -d

stop:
	@docker compose -f $(DOCKER_COMPOSE_FILE) down

restart:
	@make stop && make run

shell:
	@docker compose -f $(DOCKER_COMPOSE_FILE) exec -u "$(CURRENT_USER_ID):$(CURRENT_GROUP_ID)" $(APP_CONTAINER) bash

root-bash:
	@docker compose -f $(DOCKER_COMPOSE_FILE) exec $(APP_CONTAINER) bash

migrate:
	@docker compose -f $(DOCKER_COMPOSE_FILE) exec $(APP_CONTAINER) php artisan migrate

seed:
	@docker compose -f $(DOCKER_COMPOSE_FILE) exec $(APP_CONTAINER) php artisan migrate:fresh --seed

queue:
	@docker compose -f $(DOCKER_COMPOSE_FILE) exec $(APP_CONTAINER) php artisan queue:work

test:
	@docker compose -f $(DOCKER_COMPOSE_FILE) exec $(APP_CONTAINER) php artisan test

dev:
	@make run
	@docker compose exec $(NODE_CONTAINER) sh -c "npm ci && npm run dev"

pgshell:
	@docker compose -f $(DOCKER_COMPOSE_FILE) exec $(DB_CONTAINER) psql -U laravel -d laravel

logs:
	@docker compose -f $(DOCKER_COMPOSE_FILE) logs -f

status:
	@docker compose -f $(DOCKER_COMPOSE_FILE) ps

fresh:
	@make down && make build && make run && make migrate

fix:
	@docker compose exec app ./vendor/bin/pint


.PHONY: check-env build up down restart bash root-bash migrate seed queue test dev pgshell logs status fresh
