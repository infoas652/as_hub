#!/bin/bash

# AS Hub - Run All Applications
# هذا السكريبت يقوم بتشغيل جميع التطبيقات

echo "🚀 AS Hub - Starting All Applications"
echo "======================================"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored messages
print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    print_error "PHP is not installed!"
    print_info "Please install PHP first:"
    echo "  sudo dnf install -y php php-cli php-fpm php-mysqlnd php-zip php-xml php-mbstring php-json php-curl"
    exit 1
fi

print_success "PHP is installed: $(php --version | head -n 1)"

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    print_error "Composer is not installed!"
    print_info "Please install Composer first:"
    echo "  curl -sS https://getcomposer.org/installer | php"
    echo "  sudo mv composer.phar /usr/local/bin/composer"
    exit 1
fi

print_success "Composer is installed: $(composer --version | head -n 1)"

# Check if Angular CLI is installed
if ! command -v ng &> /dev/null; then
    print_warning "Angular CLI is not installed globally"
    print_info "Installing Angular CLI..."
    npm install -g @angular/cli
fi

print_success "Angular CLI is installed"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# 1. Start Backend
print_info "Starting Backend API..."
cd /vercel/sandbox/backend

# Check if .env exists
if [ ! -f .env ]; then
    print_warning ".env file not found, creating from .env.example"
    cp .env.example .env
fi

# Check if vendor directory exists
if [ ! -d vendor ]; then
    print_info "Installing Backend dependencies..."
    composer install
fi

# Generate keys if not set
if ! grep -q "APP_KEY=base64:" .env; then
    print_info "Generating APP_KEY..."
    php artisan key:generate
fi

if ! grep -q "JWT_SECRET=" .env || [ -z "$(grep JWT_SECRET= .env | cut -d'=' -f2)" ]; then
    print_info "Generating JWT_SECRET..."
    php artisan jwt:secret
fi

# Start Backend in background
print_success "Starting Backend on http://localhost:8000"
php artisan serve > /tmp/backend.log 2>&1 &
BACKEND_PID=$!
echo $BACKEND_PID > /tmp/backend.pid
print_success "Backend started (PID: $BACKEND_PID)"

# Wait for backend to start
sleep 3

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# 2. Start Frontend
print_info "Starting Frontend..."
cd /vercel/sandbox/frontend

# Check if node_modules exists
if [ ! -d node_modules ]; then
    print_info "Installing Frontend dependencies..."
    npm install
fi

# Check if environment.ts exists
if [ ! -f src/environments/environment.ts ]; then
    print_warning "environment.ts not found, creating from example"
    cp src/environments/environment.example.ts src/environments/environment.ts
fi

print_success "Starting Frontend on http://localhost:4200"
ng serve > /tmp/frontend.log 2>&1 &
FRONTEND_PID=$!
echo $FRONTEND_PID > /tmp/frontend.pid
print_success "Frontend started (PID: $FRONTEND_PID)"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# 3. Start Admin Panel
print_info "Starting Admin Panel..."
cd /vercel/sandbox/admin-panel

# Check if node_modules exists
if [ ! -d node_modules ]; then
    print_info "Installing Admin Panel dependencies..."
    npm install
fi

# Check if environment.ts exists
if [ ! -f src/environments/environment.ts ]; then
    print_warning "environment.ts not found, creating from example"
    cp src/environments/environment.example.ts src/environments/environment.ts
fi

print_success "Starting Admin Panel on http://localhost:4201"
ng serve --port 4201 > /tmp/admin.log 2>&1 &
ADMIN_PID=$!
echo $ADMIN_PID > /tmp/admin.pid
print_success "Admin Panel started (PID: $ADMIN_PID)"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo ""
print_success "All applications started successfully! 🎉"
echo ""
echo "📍 URLs:"
echo "   Backend API:   http://localhost:8000"
echo "   Frontend:      http://localhost:4200"
echo "   Admin Panel:   http://localhost:4201"
echo ""
echo "📝 Logs:"
echo "   Backend:       tail -f /tmp/backend.log"
echo "   Frontend:      tail -f /tmp/frontend.log"
echo "   Admin Panel:   tail -f /tmp/admin.log"
echo ""
echo "🔑 Admin Credentials:"
echo "   Email:    admin@ashub.com"
echo "   Password: Admin@123"
echo ""
echo "🛑 To stop all applications:"
echo "   ./STOP_ALL.sh"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
print_info "Press Ctrl+C to stop monitoring (applications will continue running)"
echo ""

# Monitor processes
while true; do
    sleep 5
    
    # Check if processes are still running
    if ! kill -0 $BACKEND_PID 2>/dev/null; then
        print_error "Backend stopped unexpectedly!"
        print_info "Check logs: tail -f /tmp/backend.log"
        break
    fi
    
    if ! kill -0 $FRONTEND_PID 2>/dev/null; then
        print_error "Frontend stopped unexpectedly!"
        print_info "Check logs: tail -f /tmp/frontend.log"
        break
    fi
    
    if ! kill -0 $ADMIN_PID 2>/dev/null; then
        print_error "Admin Panel stopped unexpectedly!"
        print_info "Check logs: tail -f /tmp/admin.log"
        break
    fi
done
