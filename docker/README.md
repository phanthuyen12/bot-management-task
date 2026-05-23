# Docker Configuration Directory

This directory contains all Docker configuration and deployment scripts for the Bot Management application.

## 📁 Files Overview

### Docker Configuration
- **`Dockerfile`** - PHP 8.3 FPM application image
  - Built on Alpine Linux for minimal size
  - Includes all required PHP extensions (gd, zip, pdo, redis, etc.)
  - Auto-installs composer dependencies

- **`docker-compose.yml`** - Development compose file
  - 5 services: PHP-FPM, Nginx, MySQL, Redis, Queue Worker
  - Health checks for all services
  - Volume mounts for live development

- **`docker-compose.prod.yml`** - Production compose file
  - Optimized configuration for production
  - Enhanced security and performance settings
  - Use with: `docker-compose -f docker-compose.prod.yml up -d`

### Application Configuration
- **`php.ini`** - PHP configuration
  - Memory: 256MB
  - Upload: 100MB
  - OPcache enabled and optimized

- **`php-fpm.conf`** - PHP-FPM pool configuration
  - Dynamic workers (2-10)
  - Max children: 20
  - Slow query logging

- **`nginx.conf`** - Nginx web server configuration
  - HTTP/1.1 support
  - Gzip compression
  - 30-day asset caching
  - Client body size: 100MB

- **`nginx-https.conf`** - HTTPS version with SSL/TLS
  - Redirect HTTP → HTTPS
  - Security headers (HSTS, X-Frame-Options, etc.)
  - Perfect for Let's Encrypt

- **`mysql-init.sql`** - MySQL initialization script
  - Default character set: utf8mb4
  - Performance indexes
  - Runs on first startup

### Deployment Scripts
- **`deploy.sh`** - One-command deployment
  - Checks Docker installation
  - Creates .env from .env.example
  - Builds images
  - Starts services
  - Runs migrations and seeders
  - Optimizes application cache
  
  **Usage**: `chmod +x deploy.sh && ./deploy.sh`

- **`entrypoint.sh`** - Container startup script
  - Waits for database to be ready
  - Generates APP_KEY if needed
  - Runs migrations
  - Runs seeders
  - Caches configuration

### Utility Scripts
- **`healthcheck.sh`** - Service health verification
  - Tests MySQL connection
  - Tests Redis connection
  - Tests PHP-FPM
  - Tests Nginx
  - Checks database tables
  
  **Usage**: `bash healthcheck.sh`

- **`monitor.sh`** - Real-time resource monitoring
  - Container status
  - CPU/Memory usage
  - Database size
  - Redis memory usage
  - Refreshes every 10 seconds
  
  **Usage**: `bash monitor.sh`

- **`backup.sh`** - Automated backup system
  - Database dump (compressed)
  - Application files backup
  - Storage backup
  - Auto-cleanup old backups (7+ days)
  
  **Usage**: `bash backup.sh`

- **`restore.sh`** - Restore from backup
  - Restore database from compressed backup
  - Confirmation prompt for safety
  
  **Usage**: `bash restore.sh backup_file.sql.gz`

- **`troubleshoot.sh`** - Interactive troubleshooting
  - Diagnose common issues
  - View logs quickly
  - Test service connectivity
  - Provide solutions
  
  **Usage**: `bash troubleshoot.sh`

- **`setup-ssl.sh`** - Let's Encrypt SSL setup
  - Installs certbot
  - Generates SSL certificate
  - Creates auto-renewal script
  - Root access required
  
  **Usage**: `sudo bash setup-ssl.sh yourdomain.com`

- **`init.sh`** - Initialize all scripts
  - Makes all scripts executable
  
  **Usage**: `bash init.sh`

### Support Files
- **`.gitignore`** - Ignore Docker artifacts
  - Backups/
  - SSL certificates
  - Logs
  - Local env files

## 🚀 Quick Start

### Development (Local)
```bash
# From project root
docker-compose -f docker/docker-compose.yml up -d
make migrate
make seed

# Access: http://localhost
```

### Production (VPS)
```bash
# 1. Copy to VPS
scp -r docker/ user@vps:/opt/bot-management/

# 2. Configure
cd /opt/bot-management
cp .env.example .env
nano .env  # Edit production values

# 3. Deploy
cd docker
chmod +x deploy.sh
./deploy.sh

# 4. Monitor
bash monitor.sh
```

## 📋 Service Specifications

### PHP-FPM (`app`)
- **Image**: Custom (Dockerfile)
- **Port**: 9000 (internal)
- **Workers**: 5-20 dynamic
- **Memory**: 256MB per worker
- **Extensions**: gd, zip, pdo, pdo_mysql, pdo_pgsql, sockets, pcntl, redis

### Nginx (`nginx`)
- **Image**: nginx:alpine
- **Port**: 80 (80), 443 (optional)
- **Workers**: 1 (adjustable)
- **Compression**: Gzip enabled
- **Cache**: 30 days for static assets

### MySQL (`db`)
- **Image**: mysql:8.0
- **Port**: 3306
- **Password**: From .env (DB_PASSWORD)
- **Database**: taskmanagentbot (default)
- **Volume**: Persistent (mysql_data)
- **Max Connections**: 100

### Redis (`redis`)
- **Image**: redis:7-alpine
- **Port**: 6379
- **Memory**: 256MB (LRU eviction)
- **Persistence**: AOF enabled
- **Volume**: Persistent (redis_data)

### Queue Worker (`queue`)
- **Image**: Custom (Dockerfile)
- **Command**: php artisan queue:work
- **Retry**: 3 times
- **Timeout**: 120 seconds
- **Sleep**: 3 seconds between jobs

## 🔐 Security Notes

✅ **Encryption**: No secrets in Dockerfile (use .env)
✅ **User**: All services run as www-data (non-root)
✅ **Volumes**: Read-only mounts where possible
✅ **Headers**: Security headers configured in nginx
✅ **SSL/TLS**: Let's Encrypt support via setup-ssl.sh

## 📊 Disk Usage

Typical sizes:
- **Dockerfile**: ~2GB installed (final image ~800MB)
- **MySQL data**: Depends on data (~100MB-1GB)
- **Redis data**: Configurable (~256MB default)
- **Application**: ~200MB
- **Total**: ~1.5-3GB

## 🆘 Troubleshooting

### Quick Checks
```bash
# Check all services
docker-compose ps

# View logs
docker-compose logs -f app

# Run health check
bash healthcheck.sh

# Interactive help
bash troubleshoot.sh
```

### Common Issues

**Database won't connect**
```bash
docker-compose restart db
sleep 10
docker-compose exec app php artisan migrate
```

**Port already in use**
Edit `docker-compose.yml` and change ports, then restart:
```bash
docker-compose up -d
```

**Out of memory**
```bash
# Check resource usage
docker stats

# Reduce Redis memory in docker-compose.yml
# Change: --maxmemory 256mb → --maxmemory 128mb
```

## 📞 Support

- **Quick Start**: See [../QUICKSTART.md](../QUICKSTART.md)
- **Full Guide**: See [../DOCKER_DEPLOYMENT.md](../DOCKER_DEPLOYMENT.md)
- **Cheat Sheet**: See [../CHEATSHEET.md](../CHEATSHEET.md)
- **Makefile**: Run `make help` from project root

## 📝 Version Info

- **PHP**: 8.3 (Alpine)
- **Nginx**: Latest Alpine
- **MySQL**: 8.0
- **Redis**: 7 (Alpine)
- **Docker Compose**: 3.8 format

---

**Last Updated**: May 2026
**Status**: ✅ Production Ready
