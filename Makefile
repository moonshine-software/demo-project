THIS_FILE := $(lastword $(MAKEFILE_LIST))
.PHONY: build rebuild rebuild-app up migrate migrate-rollback migrate-fresh migration route-list test composer-install composer-update composer-du npm-install npm-update npm-build npm-host demo-install update git-upstream publish
app := php-moonshine-demo
path := /var/www/moonshine-demo
app-npm := npm-moonshine-demo

#docker
build:
	docker-compose -f docker-compose.yml up --build -d $(c)
rebuild:
	docker-compose up -d --force-recreate --no-deps --build $(r)
rebuild-app:
	docker-compose up -d --force-recreate --no-deps --build $(app)
up:
	docker-compose -f docker-compose.yml up -d $(c)
it:
	docker exec -it $(app) /bin/bash

#laravel
migrate:
	docker exec $(app) php $(path)/artisan migrate
migrate-rollback:
	docker exec $(app) php $(path)/artisan migrate:rollback
migrate-fresh:
	docker exec $(app) php $(path)/artisan migrate:fresh --seed
migration:
	docker exec $(app) php $(path)/artisan make:migration $(m)
route-list:
	docker exec $(app) php $(path)/artisan route:list
test:
	docker exec $(app) php $(path)/artisan test

#composer
composer-install:
	docker exec $(app) composer install
composer-update:
	docker exec $(app) composer update
composer-du:
	docker exec $(app) composer du

#npm
npm-install:
	docker-compose run --rm --service-ports $(app-npm) install $(c)
npm-update:
	docker-compose run --rm --service-ports $(app-npm) update $(c)
npm-build:
	docker-compose run --rm --service-ports $(app-npm) run dev $(c)
npm-host:
	docker-compose run --rm --service-ports $(app-npm) run dev --host $(c)

#moonshine
demo-install:
	cp .env.example .env
	make build
	make npm-install
	make npm-build
	make composer-install
	docker exec $(app) php $(path)/artisan key:generate
	docker exec $(app) php $(path)/artisan storage:link
	make migrate-fresh

#for contributors
update: git-upstream publish

git-upstream:
	git fetch upstream && git merge upstream/2.0
publish:
	docker exec $(app) php $(path)/artisan vendor:publish --tag=laravel-assets --force $(c)

