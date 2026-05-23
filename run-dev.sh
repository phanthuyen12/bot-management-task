#!/bin/bash

# Script để chạy cùng lúc Laravel server và Telegram bot
# Sử dụng: chmod +x run-dev.sh && ./run-dev.sh

set -e

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_ROOT"

# Màu cho terminal output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║     Laravel Server + Telegram Bot Runner                  ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Kiểm tra .env file
if [ ! -f .env ]; then
    echo -e "${RED}❌ Error: .env file not found${NC}"
    echo "Vui lòng tạo .env file trước"
    exit 1
fi

# Kiểm tra TELEGRAM_TOKEN
if ! grep -q "TELEGRAM_TOKEN" .env || [ -z "$(grep TELEGRAM_TOKEN .env | cut -d '=' -f2 | xargs)" ]; then
    echo -e "${YELLOW}⚠️  Warning: TELEGRAM_TOKEN not set in .env${NC}"
    echo "Bot sẽ không hoạt động đầy đủ"
fi

LOCAL_DB_HOST="${LOCAL_DB_HOST:-127.0.0.1}"
LOCAL_DB_PORT="${LOCAL_DB_PORT:-3306}"
LOCAL_REDIS_HOST="${LOCAL_REDIS_HOST:-127.0.0.1}"
LOCAL_REDIS_PORT="${LOCAL_REDIS_PORT:-6379}"

echo -e "${BLUE}ℹ Running outside Docker with local service overrides:${NC}"
echo -e "  DB_HOST=${YELLOW}${LOCAL_DB_HOST}${NC}  DB_PORT=${YELLOW}${LOCAL_DB_PORT}${NC}"
echo -e "  REDIS_HOST=${YELLOW}${LOCAL_REDIS_HOST}${NC}  REDIS_PORT=${YELLOW}${LOCAL_REDIS_PORT}${NC}"
echo ""

if command -v nc >/dev/null 2>&1; then
    if ! nc -z "$LOCAL_REDIS_HOST" "$LOCAL_REDIS_PORT" >/dev/null 2>&1; then
        echo -e "${YELLOW}⚠️  Redis chưa phản hồi tại ${LOCAL_REDIS_HOST}:${LOCAL_REDIS_PORT}${NC}"
        echo "Nếu bot dùng conversation cache/queue qua Redis, hãy khởi động Redis trước."
    fi

    if ! nc -z "$LOCAL_DB_HOST" "$LOCAL_DB_PORT" >/dev/null 2>&1; then
        echo -e "${YELLOW}⚠️  MySQL chưa phản hồi tại ${LOCAL_DB_HOST}:${LOCAL_DB_PORT}${NC}"
        echo "Nếu app dùng MySQL local, hãy khởi động MySQL trước."
    fi

    echo ""
fi

echo -e "${GREEN}✓ Starting Laravel development server...${NC}"
env \
DB_HOST="$LOCAL_DB_HOST" \
DB_PORT="$LOCAL_DB_PORT" \
REDIS_HOST="$LOCAL_REDIS_HOST" \
REDIS_PORT="$LOCAL_REDIS_PORT" \
php artisan serve --host=127.0.0.1 --port=8000 &
SERVER_PID=$!

sleep 2

echo -e "${GREEN}✓ Starting Telegram bot (polling mode)...${NC}"
env \
DB_HOST="$LOCAL_DB_HOST" \
DB_PORT="$LOCAL_DB_PORT" \
REDIS_HOST="$LOCAL_REDIS_HOST" \
REDIS_PORT="$LOCAL_REDIS_PORT" \
php artisan nutgram:listen &
BOT_PID=$!

echo ""
echo -e "${BLUE}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}✓ Cả 2 service đã khởi động thành công!${NC}"
echo -e "${BLUE}╠════════════════════════════════════════════════════════════╣${NC}"
echo -e "  Website:  ${YELLOW}http://127.0.0.1:8000${NC}"
echo -e "  Bot:      ${YELLOW}Polling mode (listening)${NC}"
echo ""
echo -e "  Process IDs:"
echo -e "    - Server: ${YELLOW}$SERVER_PID${NC}"
echo -e "    - Bot:    ${YELLOW}$BOT_PID${NC}"
echo -e "${BLUE}╠════════════════════════════════════════════════════════════╣${NC}"
echo -e "  Để dừng, nhấn Ctrl+C"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Cleanup on exit
trap "kill $SERVER_PID $BOT_PID 2>/dev/null; exit" EXIT INT TERM

# Wait for processes
wait
