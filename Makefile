# ============================================
# Makefile untuk Laravel Docker Project
# Simplify common Docker operations
# ============================================

.PHONY: help build up down restart logs shell test migrate cache-clear backup

# Default target
.DEFAULT_GOAL := help

# Colors for output
BLUE := \033[0;34m
GREEN := \033[0;32m
YELLOW := \033[0;33m
RED := \033[0;31m
NC := \033[0m # No Color

## help: Display this help message
help:
	@echo "$(BLUE)Available commands:$(NC)"
	@echo ""
	@grep -E '^## ' $(MAKEFILE_LIST) | sed 's/## //' | awk 'BEGIN {FS = ":"}; {printf "$(GREEN)%-20s$(NC) %s\n", $$1, $$2}'

## build: Build Docker containers
build:
	@echo "$(BLUE)Building Docker containers...$(NC)"
	docker compose build

## up: Start all containers
up:
	@echo "$(BLUE)Starting containers...$(NC)"
	docker compose up -d
	@echo "$(GREEN)Containers started successfully!$(NC)"
	@make ps

## down: Stop all containers
down:
	@echo "$(YELLOW)Stopping containers...$(NC)"
	docker compose down
	@echo "$(GREEN)Containers stopped!$(NC)"

## restart: Restart all containers
restart: down up

## ps: Show container status
ps:
	@echo "$(BLUE)Container status:$(NC)"
	@docker compose ps

## logs: View logs (all containers)
logs:
	docker compose logs -f

## logs-app: View application logs
logs-app:
	docker compose logs -f app

## logs-db: View database logs
logs-db:
	docker compose logs -f db

## logs-queue: View queue worker logs
logs-queue:
	docker compose logs -f queue

## shell: Access application container shell
shell:
	docker compose exec app sh

## shell-db: Access database container shell
shell-db:
	docker compose exec db mysql -u root -p

## shell-redis: Access Redis CLI
shell-redis:
	docker compose exec redis redis-cli

## install: Initial setup (first time deployment)
install: build up migrate seed cache-optimize
	@echo "$(GREEN)Installation completed!$(NC)"

## migrate: Run database migrations
migrate:
	@echo "$(BLUE)Running migrations...$(NC)"
	docker compose exec app php artisan migrate --force
	@echo "$(GREEN)Migrations completed!$(NC)"

## migrate-fresh: Fresh migration (WARNING: Deletes all data)
migrate-fresh:
	@echo "$(RED)WARNING: This will delete all data!$(NC)"
	@read -p "Are you sure? (yes/no): " confirm; \
	if [ "$$confirm" = "yes" ]; then \
		docker compose exec app php artisan migrate:fresh --force; \
		echo "$(GREEN)Fresh migration completed!$(NC)"; \
	else \
		echo "$(YELLOW)Aborted.$(NC)"; \
	fi

## seed: Run database seeders
seed:
	@echo "$(BLUE)Running seeders...$(NC)"
	docker compose exec app php artisan db:seed --force
	@echo "$(GREEN)Seeding completed!$(NC)"

## test: Run tests
test:
	@echo "$(BLUE)Running tests...$(NC)"
	docker compose exec app php artisan test

## cache-clear: Clear all caches
cache-clear:
	@echo "$(BLUE)Clearing caches...$(NC)"
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:clear
	@echo "$(GREEN)Caches cleared!$(NC)"

## cache-optimize: Optimize application caches
cache-optimize:
	@echo "$(BLUE)Optimizing caches...$(NC)"
	docker compose exec app php artisan config:cache
	docker compose exec app php artisan route:cache
	docker compose exec app php artisan view:cache
	@echo "$(GREEN)Optimization completed!$(NC)"

## queue-restart: Restart queue workers
queue-restart:
	@echo "$(BLUE)Restarting queue workers...$(NC)"
	docker compose restart queue
	@echo "$(GREEN)Queue workers restarted!$(NC)"

## backup-db: Backup database
backup-db:
	@echo "$(BLUE)Creating database backup...$(NC)"
	@mkdir -p ./backups
	@docker compose exec -T db mysqldump -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} > ./backups/backup_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "$(GREEN)Database backup created in ./backups/$(NC)"

## restore-db: Restore database from backup
restore-db:
	@echo "$(YELLOW)Available backups:$(NC)"
	@ls -1 ./backups/*.sql
	@read -p "Enter backup filename: " backup; \
	if [ -f "./backups/$$backup" ]; then \
		docker compose exec -T db mysql -u root -p${DB_ROOT_PASSWORD} ${DB_DATABASE} < ./backups/$$backup; \
		echo "$(GREEN)Database restored from $$backup$(NC)"; \
	else \
		echo "$(RED)Backup file not found!$(NC)"; \
	fi

## clean: Clean up Docker resources
clean:
	@echo "$(YELLOW)Cleaning up Docker resources...$(NC)"
	docker compose down -v
	docker system prune -f
	@echo "$(GREEN)Cleanup completed!$(NC)"

## deploy: Deploy updates (pull, build, migrate, cache)
deploy:
	@echo "$(BLUE)Deploying updates...$(NC)"
	git pull origin main
	docker compose pull
	docker compose up -d --build
	@make migrate
	@make cache-optimize
	@make queue-restart
	@echo "$(GREEN)Deployment completed!$(NC)"

## status: Show comprehensive status
status:
	@echo "$(BLUE)=== Docker Status ===$(NC)"
	@docker compose ps
	@echo ""
	@echo "$(BLUE)=== Disk Usage ===$(NC)"
	@docker system df
	@echo ""
	@echo "$(BLUE)=== Container Resources ===$(NC)"
	@docker stats --no-stream

## health: Check application health
health:
	@echo "$(BLUE)Checking application health...$(NC)"
	@curl -s http://localhost:8000/health && echo "$(GREEN)✓ App is healthy$(NC)" || echo "$(RED)✗ App is not responding$(NC)"

## composer: Run composer commands (usage: make composer CMD="install")
composer:
	docker compose exec app composer $(CMD)

## artisan: Run artisan commands (usage: make artisan CMD="route:list")
artisan:
	docker compose exec app php artisan $(CMD)

## npm: Run npm commands (usage: make npm CMD="install")
npm:
	docker compose exec app npm $(CMD)
