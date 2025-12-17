#!/bin/bash

# AS Hub - Stop All Applications
# هذا السكريبت يقوم بإيقاف جميع التطبيقات

echo "🛑 AS Hub - Stopping All Applications"
echo "======================================"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

# Stop Backend
if [ -f /tmp/backend.pid ]; then
    BACKEND_PID=$(cat /tmp/backend.pid)
    if kill -0 $BACKEND_PID 2>/dev/null; then
        kill $BACKEND_PID
        print_success "Backend stopped (PID: $BACKEND_PID)"
    else
        print_warning "Backend was not running"
    fi
    rm /tmp/backend.pid
else
    print_warning "Backend PID file not found"
fi

# Stop Frontend
if [ -f /tmp/frontend.pid ]; then
    FRONTEND_PID=$(cat /tmp/frontend.pid)
    if kill -0 $FRONTEND_PID 2>/dev/null; then
        kill $FRONTEND_PID
        print_success "Frontend stopped (PID: $FRONTEND_PID)"
    else
        print_warning "Frontend was not running"
    fi
    rm /tmp/frontend.pid
else
    print_warning "Frontend PID file not found"
fi

# Stop Admin Panel
if [ -f /tmp/admin.pid ]; then
    ADMIN_PID=$(cat /tmp/admin.pid)
    if kill -0 $ADMIN_PID 2>/dev/null; then
        kill $ADMIN_PID
        print_success "Admin Panel stopped (PID: $ADMIN_PID)"
    else
        print_warning "Admin Panel was not running"
    fi
    rm /tmp/admin.pid
else
    print_warning "Admin Panel PID file not found"
fi

# Also kill any remaining processes on these ports
print_warning "Checking for remaining processes..."

# Kill processes on port 8000 (Backend)
if lsof -ti:8000 >/dev/null 2>&1; then
    lsof -ti:8000 | xargs kill -9 2>/dev/null
    print_success "Killed remaining processes on port 8000"
fi

# Kill processes on port 4200 (Frontend)
if lsof -ti:4200 >/dev/null 2>&1; then
    lsof -ti:4200 | xargs kill -9 2>/dev/null
    print_success "Killed remaining processes on port 4200"
fi

# Kill processes on port 4201 (Admin Panel)
if lsof -ti:4201 >/dev/null 2>&1; then
    lsof -ti:4201 | xargs kill -9 2>/dev/null
    print_success "Killed remaining processes on port 4201"
fi

echo ""
print_success "All applications stopped! 🛑"
echo ""
