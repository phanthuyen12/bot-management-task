#!/bin/bash
# Make these scripts executable
chmod +x docker/deploy.sh
chmod +x docker/healthcheck.sh
chmod +x docker/monitor.sh
chmod +x docker/backup.sh
chmod +x docker/restore.sh
chmod +x docker/troubleshoot.sh
chmod +x docker/setup-ssl.sh

echo "✅ All scripts are now executable!"
echo ""
echo "Available scripts:"
echo "  docker/deploy.sh          - Deploy application"
echo "  docker/healthcheck.sh     - Check service health"
echo "  docker/monitor.sh         - Monitor resources"
echo "  docker/backup.sh          - Backup database"
echo "  docker/restore.sh         - Restore from backup"
echo "  docker/troubleshoot.sh    - Interactive troubleshooting"
echo "  docker/setup-ssl.sh       - Setup Let's Encrypt SSL"
echo ""
echo "Ready to deploy! Run: make up"
