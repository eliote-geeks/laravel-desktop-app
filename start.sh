#!/bin/bash

echo "🚀 Démarrage de Laravel Desktop App..."

# Vérifier que Docker est installé
if ! command -v docker &> /dev/null; then
    echo "❌ Docker n'est pas installé. Veuillez l'installer d'abord."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose n'est pas installé. Veuillez l'installer d'abord."
    exit 1
fi

# Démarrer les services Docker
echo "📦 Démarrage des services Docker..."
docker-compose up -d

# Attendre que les services soient prêts
echo "⏳ Attente du démarrage des services..."
sleep 10

# Vérifier que les services sont actifs
if docker-compose ps | grep -q "Up"; then
    echo "✅ Services Docker démarrés avec succès!"
    echo ""
    echo "📱 Laravel Backend: http://localhost:8080"
    echo "📧 Mailpit: http://localhost:8025"
    echo "📊 Horizon: http://localhost:8080/horizon"
    echo "🗄️  MySQL: localhost:3307"
    echo "🔴 Redis: localhost:6380"
    echo ""
    echo "📖 Pour plus d'informations, voir le README.md"
    echo "🛠️  Commandes disponibles: make help"
else
    echo "❌ Erreur lors du démarrage des services."
    docker-compose logs
    exit 1
fi