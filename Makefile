.PHONY: help build up down logs migrate seed cache-clear test

help:
	@echo "Bot Management - Docker Commands"
	@echo "================================="
	@echo "make build          - Build Docker images"
	@echo "make up             - Start all containers"
	@echo "make down           - Stop all containers"
	@echo "make logs           - View application logs"
	@echo "make migrate        - Run database migrations"
	@echo "make seed           - Run database seeders"
	@echo "make cache-clear    - Clear application cache"
	@echo "make tinker         - Open Laravel Tinker shell"
	@echo "make test           - Run tests"
	@echo "make install        - Install dependencies (composer + npm)"
	@echo "make backup         - Backup database"

build:
	cd docker && docker-compose build

up:
	cd docker && docker-compose up -d
	@echo "Containers started. Waiting for services..."
	sleep 5
	@echo "Access application at http://localhost"

down:
	cd docker && docker-compose down

logs:
	cd docker && docker-compose logs -f app

logs-nginx:
	cd docker && docker-compose logs -f nginx

logs-db:
	cd docker && docker-compose logs -f db

migrate:
	cd docker && docker-compose exec app php artisan migrate

seed:
	cd docker && docker-compose exec app php artisan db:seed

cache-clear:
	cd docker && docker-compose exec app php artisan cache:clear
	cd docker && docker-compose exec app php artisan config:cache
	cd docker && docker-compose exec app php artisan route:cache

tinker:
	cd docker && docker-compose exec app php artisan tinker

test:
	cd docker && docker-compose exec app php artisan test

install:
	cd docker && docker-compose exec app composer install
	cd docker && docker-compose exec app npm install
	cd docker && docker-compose exec app npm run build

backup:
	cd docker && docker-compose exec -T db mysqldump -u admin -p$$(grep DB_PASSWORD ../.env | cut -d '=' -f2) $$(grep DB_DATABASE ../.env | cut -d '=' -f2) > backups/backup-$$(date +%Y%m%d-%H%M%S).sql
	@echo "Backup created in docker/backups/"

ps:
	cd docker && docker-compose ps

restart:
	cd docker && docker-compose restart

shell:
	cd docker && docker-compose exec app /bin/bash

artisan:
	cd docker && docker-compose exec app php artisan $(ARGS)
