DC = docker-compose
cmd = list

.PHONY: artisan

up:
	$(DC) up --build

upd:
	$(DC) up -d --build

down:
	$(DC) down

build:
	$(DC) build

sh:
	$(DC) exec app /bin/bash

artisan:
	$(DC) run --rm app php artisan $(cmd)

provider:
	$(DC) run --rm app php artisan packagist:provider

sync:
	$(DC) run --rm app php artisan packagist:sync

setup:
	$(DC) run --rm app php artisan discord:setup

test:
	$(DC) run --rm app vendor/bin/phpunit

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
