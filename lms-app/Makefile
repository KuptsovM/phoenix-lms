.PHONY: up down restart logs shell migrate seed test fresh install help

help: ## Show this help message
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

up: ## Start all containers
	docker compose up -d --build

down: ## Stop all containers
	docker compose down

restart: ## Restart all containers
	docker compose restart

logs: ## View logs (follow mode)
	docker compose logs -f

logs-app: ## View app logs only
	docker compose logs -f app

logs-nginx: ## View nginx logs only
	docker compose logs -f nginx

logs-db: ## View database logs only
	docker compose logs -f db

shell: ## Access app container shell
	docker compose exec app bash

artisan: ## Run artisan command (usage: make artisan cmd="migrate")
	docker compose exec app php artisan $(cmd)

composer: ## Run composer command (usage: make composer cmd="install")
	docker compose exec app composer $(cmd)

install: ## Install Laravel dependencies and setup project
	docker compose exec app composer install
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan migrate --seed

migrate: ## Run database migrations
	docker compose exec app php artisan migrate

migrate-fresh: ## Fresh migrate database (WARNING: deletes all data)
	docker compose exec app php artisan migrate:fresh --seed

seed: ## Seed database
	docker compose exec app php artisan db:seed

test: ## Run tests
	docker compose exec app php artisan test

cache-clear: ## Clear all caches
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:clear

queue: ## Start queue worker
	docker compose exec app php artisan queue:work

scheduler: ## Start scheduler
	docker compose exec app php artisan schedule:work

npm-install: ## Install npm dependencies
	docker compose exec app npm install

npm-build: ## Build frontend assets
	docker compose exec app npm run build

npm-dev: ## Run npm development server
	docker compose exec app npm run dev

db-dump: ## Export database to SQL file
	docker compose exec db mysqldump -u lms_user -plms_password lms_database > backup.sql

db-restore: ## Restore database from SQL file (usage: make db-restore file=backup.sql)
	cat $(file) | docker compose exec -T db mysql -u lms_user -plms_password lms_database
