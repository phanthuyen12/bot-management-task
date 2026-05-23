#!/bin/bash

# Interactive troubleshooting guide
# Run: ./docker/troubleshoot.sh

DOCKER_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$DOCKER_DIR"

clear
echo "🔧 Docker Troubleshooting Guide"
echo "================================"
echo ""
echo "What issue are you experiencing?"
echo ""
echo "1) Containers won't start"
echo "2) Database connection error"
echo "3) Application shows 500 error"
echo "4) Cannot access website (port issue)"
echo "5) Queue not processing"
echo "6) Redis connection error"
echo "7) Disk space issue"
echo "8) Run health check"
echo "9) Show service logs"
echo "0) Exit"
echo ""
read -p "Select (0-9): " choice

case $choice in
    1)
        echo ""
        echo "🔍 Checking container status..."
        docker-compose ps
        echo ""
        echo "📋 Last 30 logs from app container:"
        docker-compose logs --tail=30 app
        echo ""
        echo "💡 Try: docker-compose down -v && docker-compose up -d"
        ;;
    2)
        echo ""
        echo "🔍 Testing MySQL connection..."
        DB_PASSWORD=$(grep DB_PASSWORD ../.env | cut -d '=' -f2)
        docker-compose exec -T db mysqladmin ping -h localhost -u admin -p$DB_PASSWORD
        echo ""
        echo "📋 MySQL container logs:"
        docker-compose logs --tail=20 db
        echo ""
        echo "💡 If failed, try: docker-compose restart db && sleep 10 && docker-compose exec app php artisan migrate --force"
        ;;
    3)
        echo ""
        echo "📋 Application logs (last 50 lines):"
        docker-compose logs --tail=50 app
        echo ""
        echo "💡 Common causes:"
        echo "  - Database not ready: Wait 10-15 seconds"
        echo "  - Migration not run: docker-compose exec app php artisan migrate --force"
        echo "  - Cache issues: docker-compose exec app php artisan cache:clear"
        ;;
    4)
        echo ""
        echo "🌐 Checking Nginx..."
        docker-compose exec -T nginx nginx -t
        echo ""
        echo "🔍 Testing port 80..."
        if curl -s http://localhost > /dev/null 2>&1; then
            echo "✅ Port 80 is accessible"
        else
            echo "❌ Port 80 not accessible"
            echo ""
            echo "💡 Check if port is in use:"
            echo "  lsof -i :80  # Linux/Mac"
            echo "  netstat -ano | findstr :80  # Windows"
            echo ""
            echo "💡 Or change port in docker-compose.yml:"
            echo "  ports:"
            echo "    - \"8080:80\"  # Change 80 to 8080"
        fi
        ;;
    5)
        echo ""
        echo "📋 Queue worker logs:"
        docker-compose logs --tail=30 queue
        echo ""
        echo "🔍 Check Redis:"
        docker-compose exec -T redis redis-cli info server
        echo ""
        echo "💡 Restart queue worker:"
        docker-compose restart queue
        ;;
    6)
        echo ""
        echo "🔍 Testing Redis..."
        docker-compose exec -T redis redis-cli ping
        echo ""
        docker-compose exec -T redis redis-cli info memory
        echo ""
        echo "💡 If failed, try: docker-compose restart redis"
        ;;
    7)
        echo ""
        echo "💾 Disk usage:"
        docker system df
        echo ""
        echo "🗑️  Old images & volumes:"
        echo "  docker system prune -a  # ⚠️  WARNING: Deletes unused images"
        echo ""
        echo "🗑️  Cleanup logs:"
        echo "  docker exec <container> sh -c 'truncate -s 0 /var/log/*.log'"
        ;;
    8)
        bash healthcheck.sh
        ;;
    9)
        echo ""
        echo "Select service:"
        echo "1) app (PHP-FPM)"
        echo "2) nginx"
        echo "3) db (MySQL)"
        echo "4) redis"
        echo "5) queue"
        read -p "Select: " service_choice
        case $service_choice in
            1) docker-compose logs -f app ;;
            2) docker-compose logs -f nginx ;;
            3) docker-compose logs -f db ;;
            4) docker-compose logs -f redis ;;
            5) docker-compose logs -f queue ;;
        esac
        ;;
    0)
        echo "Goodbye!"
        exit 0
        ;;
    *)
        echo "Invalid choice"
        ;;
esac

echo ""
read -p "Press Enter to continue..."
exec bash $0
