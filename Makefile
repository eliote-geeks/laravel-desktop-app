.PHONY: help install start stop restart build test clean logs shell db-migrate db-seed desktop-dev desktop-build

help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

install: ## Installation complète du projet
	@echo "🚀 Installation du projet Laravel Desktop App..."
	docker-compose build
	docker-compose up -d
	docker-compose exec app composer install
	docker-compose exec app php artisan key:generate
	docker-compose exec app php artisan migrate
	docker-compose exec app php artisan db:seed
	cd desktop && npm install
	@echo "✅ Installation terminée!"

start: ## Démarre tous les services
	@echo "🚀 Démarrage des services..."
	docker-compose up -d
	@echo "✅ Services démarrés!"
	@echo "📱 Laravel: http://localhost:8080"
	@echo "📧 Mailpit: http://localhost:8025"
	@echo "📊 Horizon: http://localhost:8080/horizon"

stop: ## Arrête tous les services
	@echo "🛑 Arrêt des services..."
	docker-compose down
	@echo "✅ Services arrêtés!"

restart: ## Redémarre tous les services
	@echo "🔄 Redémarrage des services..."
	docker-compose restart
	@echo "✅ Services redémarrés!"

build: ## Reconstruit les images Docker
	@echo "🏗️ Reconstruction des images..."
	docker-compose build --no-cache
	@echo "✅ Images reconstruites!"

test: ## Lance les tests Laravel
	@echo "🧪 Exécution des tests..."
	docker-compose exec app php artisan test
	@echo "✅ Tests terminés!"

clean: ## Nettoie les containers et volumes
	@echo "🧹 Nettoyage..."
	docker-compose down -v
	docker system prune -f
	@echo "✅ Nettoyage terminé!"

logs: ## Affiche les logs des services
	docker-compose logs -f

shell: ## Accède au shell du container Laravel
	docker-compose exec app bash

db-migrate: ## Lance les migrations
	docker-compose exec app php artisan migrate

db-seed: ## Lance les seeders
	docker-compose exec app php artisan db:seed

desktop-dev: ## Lance l'app desktop en mode développement
	@echo "🖥️ Lancement de l'application desktop..."
	cd desktop && npm run electron-dev

desktop-build: ## Build l'application desktop
	@echo "📦 Build de l'application desktop..."
	cd desktop && npm run build

horizon: ## Lance Laravel Horizon
	docker-compose exec app php artisan horizon

queue-work: ## Lance le worker de queue
	docker-compose exec app php artisan queue:work

cache-clear: ## Vide le cache
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear

permissions: ## Fixe les permissions
	docker-compose exec app chown -R www-data:www-data /var/www/storage
	docker-compose exec app chown -R www-data:www-data /var/www/bootstrap/cache