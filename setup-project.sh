#!/bin/bash

# AS Hub Project Setup Script
# This script sets up the complete AS Hub project including backend and frontend

set -e  # Exit on error

echo "=========================================="
echo "AS Hub Project Setup"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "${YELLOW}ℹ $1${NC}"
}

# Check if required tools are installed
echo "Checking prerequisites..."
echo ""

# Check Node.js
if command -v node &> /dev/null; then
    NODE_VERSION=$(node -v)
    print_success "Node.js is installed: $NODE_VERSION"
else
    print_error "Node.js is not installed. Please install Node.js 18+ from https://nodejs.org/"
    exit 1
fi

# Check npm
if command -v npm &> /dev/null; then
    NPM_VERSION=$(npm -v)
    print_success "npm is installed: $NPM_VERSION"
else
    print_error "npm is not installed"
    exit 1
fi

# Check PHP
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n 1)
    print_success "PHP is installed: $PHP_VERSION"
else
    print_error "PHP is not installed. Please install PHP 8.1+ from https://www.php.net/"
    exit 1
fi

# Check Composer
if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer -V)
    print_success "Composer is installed: $COMPOSER_VERSION"
else
    print_error "Composer is not installed. Please install from https://getcomposer.org/"
    exit 1
fi

# Check MySQL
if command -v mysql &> /dev/null; then
    print_success "MySQL is installed"
else
    print_error "MySQL is not installed. Please install MySQL 8+"
    exit 1
fi

echo ""
echo "=========================================="
echo "Setting up Backend (Laravel)"
echo "=========================================="
echo ""

cd backend

# Install Composer dependencies
print_info "Installing Composer dependencies..."
composer install
print_success "Composer dependencies installed"

# Copy .env file
if [ ! -f .env ]; then
    print_info "Creating .env file..."
    cp .env.example .env
    print_success ".env file created"
else
    print_info ".env file already exists"
fi

# Generate application key
print_info "Generating application key..."
php artisan key:generate
print_success "Application key generated"

# Generate JWT secret
print_info "Generating JWT secret..."
php artisan jwt:secret --force
print_success "JWT secret generated"

# Prompt for database credentials
echo ""
print_info "Database Configuration"
read -p "Enter database host [127.0.0.1]: " DB_HOST
DB_HOST=${DB_HOST:-127.0.0.1}

read -p "Enter database name [u643694170_Abood]: " DB_NAME
DB_NAME=${DB_NAME:-u643694170_Abood}

read -p "Enter database username [u643694170_Abood]: " DB_USER
DB_USER=${DB_USER:-u643694170_Abood}

read -sp "Enter database password: " DB_PASS
echo ""

# Update .env file
print_info "Updating database configuration..."
sed -i "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env
print_success "Database configuration updated"

# Run migrations
print_info "Running database migrations..."
php artisan migrate --force
print_success "Database migrations completed"

# Run seeders
print_info "Seeding database..."
php artisan db:seed --force
print_success "Database seeded"

# Create storage link
print_info "Creating storage link..."
php artisan storage:link
print_success "Storage link created"

# Set permissions
print_info "Setting permissions..."
chmod -R 775 storage bootstrap/cache
print_success "Permissions set"

cd ..

echo ""
echo "=========================================="
echo "Setting up Frontend (Angular)"
echo "=========================================="
echo ""

cd frontend

# Install npm dependencies
print_info "Installing npm dependencies (this may take a few minutes)..."
npm install
print_success "npm dependencies installed"

# Create environment file
if [ ! -f src/environments/environment.ts ]; then
    print_info "Creating environment file..."
    cp src/environments/environment.example.ts src/environments/environment.ts 2>/dev/null || cat > src/environments/environment.ts << EOF
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api',
  apiTimeout: 30000
};
EOF
    print_success "Environment file created"
else
    print_info "Environment file already exists"
fi

cd ..

echo ""
echo "=========================================="
echo "Setup Complete!"
echo "=========================================="
echo ""
print_success "Backend setup completed successfully"
print_success "Frontend setup completed successfully"
echo ""
echo "Default Admin Credentials:"
echo "  Email: admin@ashub.com"
echo "  Password: Admin@123"
echo ""
echo "⚠️  IMPORTANT: Change the default admin password immediately!"
echo ""
echo "To start the application:"
echo ""
echo "1. Start Backend API:"
echo "   cd backend"
echo "   php artisan serve"
echo "   (API will run on http://localhost:8000)"
echo ""
echo "2. Start Frontend (in a new terminal):"
echo "   cd frontend"
echo "   ng serve"
echo "   (Frontend will run on http://localhost:4200)"
echo ""
echo "3. Access the application:"
echo "   Frontend: http://localhost:4200"
echo "   API: http://localhost:8000/api"
echo ""
print_success "Setup completed successfully!"
echo ""
