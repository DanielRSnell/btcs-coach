#!/bin/bash
set -e

echo "🚀 BTCS Coach Deployment Script"
echo "================================"

# Configuration
APP_URL="${APP_URL:-http://localhost}"
COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.prod.yml}"

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

# Check if docker-compose file exists
if [ ! -f "$COMPOSE_FILE" ]; then
    echo "❌ Docker compose file '$COMPOSE_FILE' not found."
    echo "💡 Downloading from repository..."
    curl -O https://raw.githubusercontent.com/DanielRSnell/btcs-coach/main/docker-compose.prod.yml
fi

echo "📦 Pulling latest image..."
docker compose -f "$COMPOSE_FILE" pull

echo "🛑 Stopping existing containers..."
docker compose -f "$COMPOSE_FILE" down

echo "🗑️ Cleaning up old containers and images..."
docker system prune -f

echo "🚀 Starting BTCS Coach..."
APP_URL="$APP_URL" docker compose -f "$COMPOSE_FILE" up -d

echo "⏳ Waiting for services to be ready..."
sleep 10

echo "🔍 Checking service status..."
docker compose -f "$COMPOSE_FILE" ps

echo "📊 Checking logs..."
docker compose -f "$COMPOSE_FILE" logs --tail=20

echo "✅ BTCS Coach deployment complete!"
echo "🌐 Application should be available at: $APP_URL"
echo ""
echo "Useful commands:"
echo "  View logs: docker compose -f $COMPOSE_FILE logs -f"
echo "  Stop app:  docker compose -f $COMPOSE_FILE down"
echo "  Restart:   docker compose -f $COMPOSE_FILE restart"