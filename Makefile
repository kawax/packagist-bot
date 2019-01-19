DC = docker-compose
cmd = list

.PHONY: artisan

up:
	$(DC) up --build

upd:
	$(DC) up -d

down:
	$(DC) down

build:
	$(DC) build

sh:
	$(DC) exec app /bin/bash

artisan:
	$(DC) run --rm app php artisan $(cmd)

ci:
	$(DC) run --rm app composer install

cu:
	$(DC) run --rm app composer update
