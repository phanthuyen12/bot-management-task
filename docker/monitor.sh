#!/bin/bash

# Monitor Docker containers and application health
# Run in a terminal: ./docker/monitor.sh

DOCKER_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$DOCKER_DIR"

clear

while true; do
    clear
    echo "🔍 Docker Services Monitor - $(date)"
    echo "=================================================="
    
    # Container status
    echo ""
    echo "📦 Container Status:"
    docker-compose ps --format "table {{.Name}}\t{{.Status}}\t{{.Ports}}" 2>/dev/null
    
    # System resources
    echo ""
    echo "💾 System Resources:"
    docker stats --no-stream --format "table {{.Container}}\t{{.CPUPerc}}\t{{.MemUsage}}" 2>/dev/null
    
    # Database size
    echo ""
    echo "🗄️  Database Info:"
    DB_PASSWORD=$(grep DB_PASSWORD ../.env | cut -d '=' -f2)
    docker-compose exec -T db mysql -u admin -p$DB_PASSWORD -e "SELECT table_schema, ROUND(SUM(data_length+index_length)/1024/1024,2) AS size_mb FROM information_schema.tables GROUP BY table_schema;" 2>/dev/null | grep -E "taskmanagentbot"
    
    # Redis info
    echo ""
    echo "⚡ Redis Memory:"
    docker-compose exec -T redis redis-cli info memory 2>/dev/null | grep -E "used_memory_human|max_memory|maxmemory_policy"
    
    echo ""
    echo "Press Ctrl+C to exit. Refreshing every 10 seconds..."
    sleep 10
done
