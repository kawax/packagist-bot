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

reload:
	$(DC) run --rm app php artisan packagist:reload

index:
	$(DC) run --rm app php artisan packagist:index

info:
	$(DC) run --rm app php artisan packagist:info

serve:
	@echo "Caution!! Can't ^C"
	$(DC) run --rm app php artisan discord:serve
