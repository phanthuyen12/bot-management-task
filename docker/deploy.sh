#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}================================${NC}"
echo -e "${YELLOW}Docker Deploy Script${NC}"
echo -e "${YELLOW}================================${NC}"

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo -e "${RED}Docker is not installed. Please install Docker first.${NC}"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}Docker Compose is not installed. Please install Docker Compose first.${NC}"
    exit 1
fi

# Navigate to project directory
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR"

# Check .env file
if [ ! -f .env ]; then
    echo -e "${YELLOW}Creating .env file from .env.example${NC}"
    cp .env.example .env
    echo -e "${RED}WARNING: Please update .env with your settings!${NC}"
    echo -e "${RED}Edit .env and set:${NC}"
    echo -e "${RED}  - APP_KEY${NC}"
    echo -e "${RED}  - DB_PASSWORD${NC}"
    echo -e "${RED}  - TELEGRAM_TOKEN${NC}"
    echo -e "${RED}  - APP_URL${NC}"
    read -p "Press enter to continue..."
fi

# Build images
echo -e "${YELLOW}Building Docker images...${NC}"
cd docker
docker-compose build

# Start containers
echo -e "${YELLOW}Starting containers...${NC}"
docker-compose up -d

# Wait for DB to be ready
echo -e "${YELLOW}Waiting for database to be ready...${NC}"
sleep 10

# Run migrations and seeders
echo -e "${YELLOW}Running migrations...${NC}"
docker-compose exec -T app php artisan migrate --force

echo -e "${YELLOW}Running seeders...${NC}"
docker-compose exec -T app php artisan db:seed --force

# Cache config
echo -e "${YELLOW}Optimizing application...${NC}"
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

echo -e "${GREEN}✓ Deployment completed successfully!${NC}"
echo -e "${YELLOW}================================${NC}"
echo -e "${YELLOW}Access your application:${NC}"
echo -e "${YELLOW}  - URL: http://localhost${NC}"
echo -e "${YELLOW}  - phpMyAdmin: http://localhost:8080${NC}"
echo -e "${YELLOW}================================${NC}"
echo -e "${YELLOW}Useful commands:${NC}"
echo -e "${YELLOW}  - View logs: cd docker && docker-compose logs -f app${NC}"
echo -e "${YELLOW}  - Stop containers: cd docker && docker-compose down${NC}"
echo -e "${YELLOW}  - Restart containers: cd docker && docker-compose restart${NC}"
echo -e "${YELLOW}  - Run artisan: cd docker && docker-compose exec app php artisan${NC}"
echo -e "${YELLOW}================================${NC}"
