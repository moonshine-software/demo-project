-include .env

THIS_FILE := $(lastword $(MAKEFILE_LIST))

app := $(COMPOSE_PROJECT_NAME)-php
nginx := $(COMPOSE_PROJECT_NAME)-nginx
mysql := $(COMPOSE_PROJECT_NAME)-mysql
app-npm := npm
path := /var/www/app

#docker
build:
	docker compose -f docker-compose.yml up --build -d $(c)
	@echo "$(APP_URL)/admin"
rebuild:
	docker-compose up -d --force-recreate --no-deps --build $(r)
	@echo "$(APP_URL)/admin"
rebuild-app:
	docker-compose up -d --force-recreate --no-deps --build $(app)
up:
	docker-compose -f docker-compose.yml up -d $(c)
	@echo "$(APP_URL)/admin"
stop:
	docker-compose -f docker-compose.yml stop $(c)
it:
	docker exec -it $(to) /bin/bash
it-app:
	docker exec -it $(app) /bin/bash
it-nginx:
	docker exec -it $(nginx) /bin/bash
it-mysql:
	docker exec -it $(mysql) /bin/bash

#laravel
migrate:
	docker exec $(app) php $(path)/artisan migrate
migrate-rollback:
	docker exec $(app) php $(path)/artisan migrate:rollback
migrate-fresh:
	docker exec $(app) php $(path)/artisan migrate:fresh --seed
migration:
	docker exec $(app) php $(path)/artisan make:migration $(m)

#composer
composer-install:
	docker exec $(app) composer install
composer-update:
	docker exec $(app) composer update
composer-du:
	docker exec $(app) composer du

#npm
npm-install:
	docker compose run --rm --service-ports $(app-npm) install $(c)
npm-update:
	docker compose run --rm --service-ports $(app-npm) update $(c)
npm-build:
	docker compose run --rm --service-ports $(app-npm) run build $(c)
npm-host:
	docker compose run --rm --service-ports $(app-npm) run dev --host $(c)

#moonshine
demo-install:
	make build
	make composer-install
	docker exec $(app) php $(path)/artisan key:generate
	docker exec $(app) php $(path)/artisan storage:link
	make npm-install
	make npm-build
	make migrate-fresh

#for contributors
update: git-upstream publish

git-upstream:
	git fetch upstream && git merge upstream/3.0
publish:
	docker exec $(app) php $(path)/artisan vendor:publish --tag=laravel-assets --force $(c)