# 📋 Cheat Sheet - Docker & Laravel Commands

## Docker Compose

```bash
# Start
docker-compose up -d

# Stop
docker-compose down

# Stop and remove volumes
docker-compose down -v

# View status
docker-compose ps

# View logs
docker-compose logs -f
docker-compose logs -f app
docker-compose logs --tail=100 nginx

# Restart service
docker-compose restart app

# Rebuild images
docker-compose build

# Scale service
docker-compose up -d --scale queue=2
```

## Laravel Artisan

```bash
# Run any artisan command (from project root)
make artisan ARGS="command"

# Or from docker/
docker-compose exec app php artisan <command>

# Migrations
php artisan migrate                          # Run migrations
php artisan migrate:rollback                 # Rollback
php artisan migrate:refresh                  # Rollback & remigrate
php artisan migrate:refresh --seed           # Refresh & seed
php artisan migrate:status                   # Check status

# Database
php artisan db:seed                          # Run seeders
php artisan db:seed --class=AdminUserSeeder  # Run specific seeder
php artisan tinker                           # PHP REPL

# Cache
php artisan cache:clear                      # Clear app cache
php artisan config:cache                     # Cache config
php artisan route:cache                      # Cache routes
php artisan view:cache                       # Cache views

# Queues
php artisan queue:work                       # Start queue worker
php artisan queue:work --sleep=3 --tries=3   # With options
php artisan queue:failed                     # Show failed jobs
php artisan queue:retry all                  # Retry all failed

# Make
php artisan make:migration create_table      # Create migration
php artisan make:model Post                  # Create model
php artisan make:controller PostController   # Create controller
php artisan make:request CreatePostRequest   # Create form request

# Testing
php artisan test                             # Run tests
php artisan test --filter=testName           # Run specific test
php artisan test --stop-on-failure           # Stop on first failure
```

## Database

```bash
# MySQL shell
docker-compose exec db mysql -u admin -p
# Password: (in .env DB_PASSWORD)

# Common MySQL commands
SHOW DATABASES;
USE taskmanagentbot;
SHOW TABLES;
DESCRIBE users;
SELECT * FROM users;

# Backup
docker-compose exec -T db mysqldump -u admin -p123456 taskmanagentbot > backup.sql

# Restore
docker-compose exec -T db mysql -u admin -p123456 taskmanagentbot < backup.sql
```

## Redis

```bash
# Connect to Redis
docker-compose exec redis redis-cli

# Common Redis commands
PING                    # Test connection
INFO                    # Server info
KEYS *                  # List all keys
GET key_name            # Get value
DEL key_name            # Delete key
FLUSHALL                # Delete all keys (⚠️ careful!)
DBSIZE                  # Number of keys
```

## Application Commands

```bash
# View logs (from project root)
make logs               # Laravel app logs
make logs-nginx         # Nginx logs
make logs-db            # Database logs

# Monitoring
make ps                 # Show container status
bash docker/monitor.sh  # Real-time monitoring

# Backup & Restore
make backup             # Backup database
bash docker/restore.sh backup.sql.gz

# Health check
bash docker/healthcheck.sh

# Troubleshooting
bash docker/troubleshoot.sh

# Setup SSL
sudo bash docker/setup-ssl.sh yourdomain.com
```

## File Management

```bash
# Copy from container to host
docker-compose cp app:/var/www/html/storage/logs ./logs_backup

# Copy from host to container
docker-compose cp ./file.txt app:/var/www/html/storage/

# Execute arbitrary command
docker-compose exec app find storage -type f -name "*.log"
```

## Environment & Debugging

```bash
# View env variables
docker-compose exec app env | grep DB_

# Rebuild without cache
docker-compose build --no-cache

# Run command without services starting
docker-compose run --rm app php artisan migrate

# Interactive shell
docker-compose exec app /bin/bash
docker-compose exec db mysql -u admin -p
```

## Performance & Monitoring

```bash
# Check container resource usage
docker stats

# View detailed container info
docker inspect laravel-app

# Check logs for errors
docker-compose logs app | grep -i error

# Monitor MySQL
docker-compose exec db mysqladmin -u admin -p status

# Monitor Redis
docker-compose exec redis redis-cli --stat
```

## Cleanup & Maintenance

```bash
# Remove unused images
docker image prune

# Remove unused volumes
docker volume prune

# Remove all unused resources
docker system prune -a

# View disk usage
docker system df

# Remove specific image
docker rmi image_name

# Remove specific volume
docker volume rm volume_name
```

## Network & Port

```bash
# Verify port mapping
docker-compose port nginx 80

# Check port usage
lsof -i :80                    # macOS/Linux
netstat -ano | findstr :80     # Windows

# Access other containers
docker-compose exec app curl http://nginx/
docker-compose exec app redis-cli -h redis
docker-compose exec app mysql -h db -u admin -p
```

## Quick Workflows

### Deploy new version
```bash
git pull origin main
docker-compose build
docker-compose up -d
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear
```

### Reset database
```bash
docker-compose exec app php artisan migrate:refresh --seed
```

### Full restart
```bash
docker-compose down -v
docker-compose up -d
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force
```

### Backup and cleanup
```bash
make backup
docker system prune -a
```

---

**💡 Tip**: Most commands can be run with `make` from project root (see Makefile)

**🆘 Help**: Run `bash docker/troubleshoot.sh` for interactive troubleshooting
