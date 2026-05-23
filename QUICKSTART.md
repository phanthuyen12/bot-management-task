# 🚀 Quick Start Guide

## 30 Seconds Deploy

```bash
# 1. Setup
cd /opt/bot-management
cp .env.example .env
nano .env  # Edit DB_PASSWORD, TELEGRAM_TOKEN, APP_URL

# 2. Deploy
cd docker
docker-compose up -d

# 3. Wait & Test
sleep 10
docker-compose logs app
```

## Common Tasks

### View Logs
```bash
cd docker
docker-compose logs -f app
```

### Run Artisan Commands
```bash
cd docker
docker-compose exec app php artisan <command>

# Examples:
docker-compose exec app php artisan migrate
docker-compose exec app php artisan tinker
docker-compose exec app php artisan cache:clear
```

### Access Database
```bash
cd docker
docker-compose exec db mysql -u admin -p
# Password is in .env DB_PASSWORD
```

### Stop & Remove All
```bash
cd docker
docker-compose down -v  # Remove volumes too
```

## Using Makefile (Simpler!)

```bash
# From project root
make up              # Start containers
make logs            # View logs
make migrate         # Run migrations
make seed            # Seed database
make cache-clear     # Clear cache
make backup          # Backup database
make down            # Stop containers
```

## Troubleshooting

### Container won't start?
```bash
cd docker
docker-compose logs app
# Check error message
```

### Database connection error?
```bash
# Restart database
cd docker
docker-compose restart db

# Wait 10 seconds
sleep 10

# Run migrations again
docker-compose exec app php artisan migrate --force
```

### Port already in use?
```bash
# Change port in docker-compose.yml
# Find: ports: - "80:80"
# Change to: ports: - "8080:80"

# Then restart
docker-compose restart nginx
```

### Permission denied on storage?
```bash
cd docker
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

## Production Deployment

### 1. Initial Setup
```bash
# On your VPS
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker ubuntu

# Clone project
git clone <repo> /opt/bot-management
cd /opt/bot-management

# Copy and edit env
cp .env.example .env
nano .env
```

### 2. Deploy
```bash
chmod +x docker/deploy.sh
cd docker
./deploy.sh
```

### 3. Setup SSL (optional but recommended)
```bash
sudo chmod +x docker/setup-ssl.sh
sudo docker/setup-ssl.sh yourdomain.com
```

### 4. Monitor
```bash
chmod +x docker/monitor.sh
./monitor.sh

# In another terminal, setup auto-backup
chmod +x docker/backup.sh
crontab -e
# Add: 0 2 * * * /opt/bot-management/docker/backup.sh
```

## Docker Compose Reference

```bash
# Start in background
docker-compose up -d

# View logs
docker-compose logs -f

# Stop containers
docker-compose stop

# Restart specific service
docker-compose restart app

# Run command in container
docker-compose exec app php artisan <command>

# Remove everything
docker-compose down -v
```

## Resources

- 📖 [Laravel Docs](https://laravel.com/docs)
- 🐳 [Docker Docs](https://docs.docker.com)
- 🤖 [Telegram Bot API](https://core.telegram.org/bots/api)
- 🥜 [NutGram Docs](https://docs.nutgram.org)

## Need Help?

1. Check logs: `docker-compose logs -f app`
2. Run healthcheck: `bash docker/healthcheck.sh`
3. Read full guide: [DOCKER_DEPLOYMENT.md](../DOCKER_DEPLOYMENT.md)
