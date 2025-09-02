# Laravel Desktop App

Application desktop professionnelle basée sur Laravel et Electron avec Docker, Redis, et déploiement automatique.

## 🚀 Fonctionnalités

- **Backend Laravel** avec architecture professionnelle
- **Application Desktop** avec Electron
- **Docker** pour l'environnement de développement
- **Redis** pour le cache et les sessions
- **Authentification sécurisée** avec Sanctum
- **Système de permissions** avec Spatie
- **Activity logging** pour l'audit
- **Déploiement automatique** avec GitHub Actions
- **Horizon** pour la gestion des queues

## 📋 Prérequis

- Docker & Docker Compose
- Node.js 18+
- PHP 8.2+
- Composer

## 🛠️ Installation

```bash
# Cloner le projet
git clone <votre-repo>
cd laravel-desktop-app

# Installation complète
make install

# Ou installation manuelle
docker-compose build
docker-compose up -d
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
cd desktop && npm install
```

## 🎯 Commandes utiles

```bash
# Démarrer l'environnement
make start

# Arrêter les services
make stop

# Application desktop (développement)
make desktop-dev

# Tests
make test

# Voir les logs
make logs

# Accéder au shell Laravel
make shell

# Build desktop
make desktop-build
```

## 🔧 Configuration

### Variables d'environnement

Le fichier `.env` est configuré pour Docker avec :
- MySQL sur port 3307
- Redis sur port 6380  
- Mailpit sur port 8025
- Laravel sur port 8080

### Sécurité

- Headers de sécurité configurés
- CORS pour l'application desktop
- Rate limiting avec Redis
- Authentification multi-facteurs disponible
- Activity logging pour l'audit

## 🖥️ Application Desktop

L'application Electron se connecte au backend Laravel via l'API REST sécurisée.

```bash
# Développement
cd desktop
npm run electron-dev

# Build pour production
npm run build-win    # Windows
npm run build-mac    # macOS  
npm run build-linux  # Linux
```

## 📦 Architecture

```
laravel-desktop-app/
├── app/                    # Code Laravel
│   ├── Services/          # Services métier
│   ├── Repositories/      # Repositories
│   ├── Traits/           # Traits réutilisables
│   └── Http/             # Controllers & Middleware
├── desktop/              # Application Electron
│   ├── main.js          # Process principal
│   ├── preload.js       # Script preload
│   └── assets/          # Ressources
├── docker/              # Configuration Docker
└── .github/workflows/   # CI/CD
```

## 🚀 Déploiement

Le déploiement automatique est configuré avec GitHub Actions :
- Tests automatiques sur chaque PR
- Build multi-plateforme de l'app desktop
- Déploiement en production sur la branche `production`

## 📊 Monitoring

- **Horizon** : http://localhost:8080/horizon
- **Mailpit** : http://localhost:8025
- **Logs** : `make logs`

## 🔒 Sécurité

- Authentification JWT avec Sanctum
- Système de rôles et permissions
- Headers de sécurité configurés
- Protection CSRF
- Rate limiting
- Activity logging pour audit
- Validation stricte des données

## 📝 Scripts disponibles

- `make help` : Affiche toutes les commandes disponibles
- `make install` : Installation complète
- `make start/stop/restart` : Gestion des services
- `make test` : Lance les tests
- `make desktop-dev` : Application desktop en dev
- `make clean` : Nettoie l'environnement

## 🤝 Contribution

1. Fork le projet
2. Créer une branche feature
3. Commit les changements
4. Push vers la branche
5. Ouvrir une Pull Request