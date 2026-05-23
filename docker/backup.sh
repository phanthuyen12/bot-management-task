#!/bin/bash

# Backup script for database and files
# Run: ./docker/backup.sh

BACKUP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/backups"
mkdir -p "$BACKUP_DIR"

TIMESTAMP=$(date +%Y%m%d_%H%M%S)
DB_PASSWORD=$(grep DB_PASSWORD $(pwd)/../.env | cut -d '=' -f2)
DB_NAME=$(grep DB_DATABASE $(pwd)/../.env | cut -d '=' -f2)

echo "🔄 Starting backup process..."
echo "📁 Backup directory: $BACKUP_DIR"

# Backup database
echo "📊 Backing up database..."
docker-compose exec -T db mysqldump -u admin -p$DB_PASSWORD $DB_NAME --single-transaction --quick --lock-tables=false > "$BACKUP_DIR/database_$TIMESTAMP.sql"

if [ $? -eq 0 ]; then
    echo "✅ Database backup completed: database_$TIMESTAMP.sql"
    # Compress
    gzip "$BACKUP_DIR/database_$TIMESTAMP.sql"
    echo "✅ Database backup compressed"
else
    echo "❌ Database backup failed"
    exit 1
fi

# Backup application files
echo "📁 Backing up application files..."
tar -czf "$BACKUP_DIR/app_files_$TIMESTAMP.tar.gz" \
    --exclude=node_modules \
    --exclude=vendor \
    --exclude=storage/logs \
    --exclude=.git \
    -C $(dirname "$(pwd)") \
    bot-management-task

if [ $? -eq 0 ]; then
    echo "✅ Application backup completed: app_files_$TIMESTAMP.tar.gz"
else
    echo "❌ Application backup failed"
    exit 1
fi

# Backup storage
echo "💾 Backing up storage..."
tar -czf "$BACKUP_DIR/storage_$TIMESTAMP.tar.gz" \
    -C $(dirname "$(pwd)")/bot-management-task \
    storage

if [ $? -eq 0 ]; then
    echo "✅ Storage backup completed: storage_$TIMESTAMP.tar.gz"
else
    echo "❌ Storage backup failed"
    exit 1
fi

# Cleanup old backups (keep only 7 days)
echo "🧹 Cleaning up old backups..."
find "$BACKUP_DIR" -type f -mtime +7 -delete
echo "✅ Old backups removed"

echo ""
echo "✅ Backup process completed!"
echo "📍 Location: $BACKUP_DIR"
ls -lh "$BACKUP_DIR"
