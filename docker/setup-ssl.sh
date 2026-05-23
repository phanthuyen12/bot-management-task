#!/bin/bash

# Setup SSL with Let's Encrypt on Ubuntu VPS
# Usage: sudo ./docker/setup-ssl.sh yourdomain.com

DOMAIN=$1

if [ -z "$DOMAIN" ]; then
    echo "Usage: sudo ./docker/setup-ssl.sh yourdomain.com"
    exit 1
fi

echo "🔐 Setting up SSL for $DOMAIN"

# Install certbot
sudo apt install -y certbot python3-certbot-nginx

# Create SSL directory
mkdir -p docker/ssl

# Generate SSL certificate
sudo certbot certonly --standalone \
    -d $DOMAIN \
    -d www.$DOMAIN \
    --non-interactive \
    --agree-tos \
    --email admin@$DOMAIN

# Copy certificates to docker/ssl
sudo cp /etc/letsencrypt/live/$DOMAIN/fullchain.pem docker/ssl/cert.pem
sudo cp /etc/letsencrypt/live/$DOMAIN/privkey.pem docker/ssl/key.pem
sudo chown $(whoami):$(whoami) docker/ssl/*.pem

echo "✅ SSL certificates installed at docker/ssl/"

# Create renewal script
cat > docker/renew-ssl.sh << 'EOF'
#!/bin/bash
DOMAIN=$1
sudo certbot renew --non-interactive
sudo cp /etc/letsencrypt/live/$DOMAIN/fullchain.pem docker/ssl/cert.pem
sudo cp /etc/letsencrypt/live/$DOMAIN/privkey.pem docker/ssl/key.pem
cd docker
docker-compose restart nginx
EOF

chmod +x docker/renew-ssl.sh

echo "ℹ️  To renew SSL certificate automatically, add to crontab:"
echo "0 3 * * * /opt/bot-management/docker/renew-ssl.sh $DOMAIN"
