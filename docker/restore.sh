#!/bin/bash

# Restore script for database and files
# Usage: ./docker/restore.sh <backup_file>

BACKUP_FILE=$1

if [ -z "$BACKUP_FILE" ]; then
    echo "Usage: $0 <backup_file.sql.gz>"
    echo ""
    echo "Available backups:"
    ls -lh "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/backups" | grep database
    exit 1
fi

BACKUP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/backups"
FULL_PATH="$BACKUP_DIR/$BACKUP_FILE"

if [ ! -f "$FULL_PATH" ]; then
    echo "❌ Backup file not found: $FULL_PATH"
    exit 1
fi

echo "⚠️  WARNING: This will restore the database from backup!"
echo "📁 File: $BACKUP_FILE"
read -p "Continue? (yes/no): " -r CONFIRM

if [[ $CONFIRM != "yes" ]]; then
    echo "Restore cancelled"
    exit 0
fi

DB_PASSWORD=$(grep DB_PASSWORD $(pwd)/../.env | cut -d '=' -f2)
DB_NAME=$(grep DB_DATABASE $(pwd)/../.env | cut -d '=' -f2)

echo "🔄 Restoring database..."

# Decompress and restore
gunzip < "$FULL_PATH" | docker-compose exec -T db mysql -u admin -p$DB_PASSWORD $DB_NAME

if [ $? -eq 0 ]; then
    echo "✅ Database restored successfully"
else
    echo "❌ Database restore failed"
    exit 1
fi

echo "✅ Restore completed!"
