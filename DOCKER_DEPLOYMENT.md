# 🐳 Docker Deployment Guide

## Prerequisites
- Docker >= 20.10
- Docker Compose >= 2.0
- 2GB RAM minimum
- 2GB free disk space

## Local Development Setup

### 1. Clone and Setup
```bash
git clone <repository>
cd bot-management-task
cp .env.example .env
```

### 2. Start Docker Containers
```bash
cd docker
docker-compose up -d
docker-compose logs -f app
```

### 3. Initialize Database
```bash
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force
```

### 4. Access Application
- **Web**: http://localhost
- **MySQL**: localhost:3306 (admin/123456)
- **Redis**: localhost:6379

## Production Deployment on Ubuntu VPS

### 1. Server Setup
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER
newgrp docker

# Verify installation
docker --version
docker-compose --version
```

### 2. Clone Project
```bash
git clone <repository> /opt/bot-management
cd /opt/bot-management
```

### 3. Configure Environment
```bash
cp .env.example .env

# Edit with production values
nano .env
```

**Required changes for production:**
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
TELEGRAM_TOKEN=your_bot_token
DB_PASSWORD=strong_secure_password
QUEUE_CONNECTION=redis
CACHE_STORE=redis
```

### 4. Create SSL Directory
```bash
mkdir -p docker/ssl
# Place your SSL certificates here:
# docker/ssl/cert.pem
# docker/ssl/key.pem
```

### 5. Deploy
```bash
chmod +x docker/deploy.sh
cd docker
docker-compose up -d

# Check logs
docker-compose logs -f app
```

### 6. Verify Deployment
```bash
# Test database connection
docker-compose exec app php artisan tinker
>>> DB::connection()->getPDO();
# Should return PDOConnection object

# Test Redis
docker-compose exec app php artisan tinker
>>> Cache::put('test', 'working', 60);
>>> Cache::get('test');
# Should return 'working'
```

## Backup & Recovery

### Backup Database
```bash
docker-compose exec db mysqldump -u admin -p123456 taskmanagentbot > backup.sql
```

### Restore Database
```bash
docker-compose exec -T db mysql -u admin -p123456 taskmanagentbot < backup.sql
```

### Backup Application Files
```bash
tar -czf app-backup.tar.gz /opt/bot-management/storage
```

## Maintenance Commands

### View Logs
```bash
cd /opt/bot-management/docker
docker-compose logs -f app          # Laravel logs
docker-compose logs -f nginx        # Nginx logs
docker-compose logs -f db           # MySQL logs
docker-compose logs -f queue        # Queue logs
```

### Restart Services
```bash
docker-compose restart app
docker-compose restart nginx
docker-compose restart db
```

### Run Artisan Commands
```bash
docker-compose exec app php artisan <command>
```

### Clear Cache
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
```

### Update Application
```bash
git pull origin main
docker-compose build
docker-compose up -d
docker-compose exec app php artisan migrate --force
```

## Monitoring

### Check Container Health
```bash
docker-compose ps
# STATUS column should show "Up (healthy)"
```

### Monitor Resource Usage
```bash
docker stats
```

### Check Disk Usage
```bash
docker system df
# Clean unused resources: docker system prune
```

## Troubleshooting

### Database Connection Error
```bash
# Check MySQL is running
docker-compose ps db

# Check logs
docker-compose logs db

# Restart database
docker-compose restart db
```

### Redis Connection Error
```bash
# Check Redis is running
docker-compose ps redis

# Test connection
docker-compose exec redis redis-cli ping
# Should return PONG
```

### Permission Issues
```bash
# Fix storage permissions
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Out of Disk Space
```bash
# Clean Docker system
docker system prune -a

# Check log size
du -sh docker/logs/
# Limit logs in docker-compose.yml if needed
```

## Performance Optimization

### Enable OPcache
Already configured in `docker/php.ini`

### Redis Optimization
Current settings in `docker-compose.yml`:
- Max memory: 256MB
- Eviction policy: allkeys-lru
- Persistence: appendonly yes

### MySQL Optimization
For production, add to `docker-compose.yml`:
```yaml
environment:
  MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
  MYSQL_INITIAL_ROOT_PASSWORD: ${DB_PASSWORD}
command: --max-connections=100 --query-cache-size=256M
```

## Security Checklist

- [ ] Change default DB_PASSWORD in .env
- [ ] Change APP_KEY (non-default value)
- [ ] Enable HTTPS with valid SSL certificates
- [ ] Set APP_DEBUG=false in production
- [ ] Update TELEGRAM_TOKEN
- [ ] Configure firewall rules
- [ ] Setup automated backups
- [ ] Monitor logs regularly
- [ ] Keep Docker images updated

## Automatic Backups

Create a cron job for daily backups:
```bash
# Edit crontab
crontab -e

# Add this line (daily at 2 AM)
0 2 * * * cd /opt/bot-management && docker-compose exec -T db mysqldump -u admin -p$DB_PASSWORD taskmanagentbot > backups/backup-$(date +\%Y\%m\%d).sql
```

## Support & Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com)
- [Telegram Bot API](https://core.telegram.org/bots/api)
- [NutGram Documentation](https://docs.nutgram.org)
