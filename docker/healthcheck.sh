#!/bin/bash

# Health check for Docker containers
# Run: ./docker/healthcheck.sh

DOCKER_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$DOCKER_DIR"

echo "🏥 Checking Docker Services Health..."
echo "======================================"

# Check if docker-compose is running
if ! docker-compose ps | grep -q "Up"; then
    echo "❌ No containers running. Start with: docker-compose up -d"
    exit 1
fi

# Check MySQL
echo -n "MySQL: "
if docker-compose exec -T db mysqladmin ping -h localhost -u admin -p$(grep DB_PASSWORD ../.env | cut -d '=' -f2) &>/dev/null; then
    echo "✅ OK"
else
    echo "❌ FAILED"
fi

# Check Redis
echo -n "Redis: "
if docker-compose exec -T redis redis-cli ping | grep -q "PONG"; then
    echo "✅ OK"
else
    echo "❌ FAILED"
fi

# Check PHP-FPM
echo -n "PHP-FPM: "
if docker-compose exec -T app php -r "echo 'OK';" | grep -q "OK"; then
    echo "✅ OK"
else
    echo "❌ FAILED"
fi

# Check Nginx
echo -n "Nginx: "
if curl -s http://localhost > /dev/null 2>&1; then
    echo "✅ OK"
else
    echo "❌ FAILED"
fi

# Check Database Tables
echo -n "Database Tables: "
TABLES=$(docker-compose exec -T db mysql -u admin -p$(grep DB_PASSWORD ../.env | cut -d '=' -f2) $(grep DB_DATABASE ../.env | cut -d '=' -f2) -e "SHOW TABLES;" 2>/dev/null | wc -l)
if [ $TABLES -gt 1 ]; then
    echo "✅ OK ($((TABLES-1)) tables)"
else
    echo "⚠️  No tables found"
fi

echo "======================================"
echo "✅ Health check completed!"
