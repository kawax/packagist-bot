DC = docker-compose
cmd = list

.PHONY: artisan

up:
	$(DC) up

upd:
	$(DC) up -d

down:
	$(DC) down

build:
	$(DC) build

artisan:
	$(DC) run --rm app php artisan $(cmd)

ci:
	$(DC) run --rm app composer install

cu:
	$(DC) run --rm app composer update
