#!/bin/bash

# AS Hub - Quick Fix Script
# هذا السكريبت يقوم بإصلاح جميع المشاكل الأساسية

echo "🚀 AS Hub - Quick Fix Script"
echo "================================"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
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
    echo -e "${YELLOW}ℹ️  $1${NC}"
}

# Check if running in correct directory
if [ ! -d "backend" ] || [ ! -d "frontend" ] || [ ! -d "admin-panel" ]; then
    print_error "يجب تشغيل هذا السكريبت من المجلد الرئيسي للمشروع"
    exit 1
fi

echo "المرحلة 1: فحص البيئة"
echo "----------------------"

# Check PHP
if ! command -v php &> /dev/null; then
    print_warning "PHP غير مثبت"
    print_info "تثبيت PHP..."
    sudo dnf install -y php php-cli php-fpm php-mysqlnd php-zip php-xml php-mbstring php-json php-curl
    if [ $? -eq 0 ]; then
        print_success "تم تثبيت PHP"
    else
        print_error "فشل تثبيت PHP"
        exit 1
    fi
else
    print_success "PHP مثبت: $(php --version | head -n 1)"
fi

# Check Composer
if ! command -v composer &> /dev/null; then
    print_warning "Composer غير مثبت"
    print_info "تثبيت Composer..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    if [ $? -eq 0 ]; then
        print_success "تم تثبيت Composer"
    else
        print_error "فشل تثبيت Composer"
        exit 1
    fi
else
    print_success "Composer مثبت: $(composer --version | head -n 1)"
fi

# Check Node.js
if ! command -v node &> /dev/null; then
    print_error "Node.js غير مثبت"
    exit 1
else
    print_success "Node.js مثبت: $(node --version)"
fi

# Check npm
if ! command -v npm &> /dev/null; then
    print_error "npm غير مثبت"
    exit 1
else
    print_success "npm مثبت: $(npm --version)"
fi

echo ""
echo "المرحلة 2: إعداد Backend"
echo "------------------------"

cd backend

# Install Composer dependencies
if [ ! -d "vendor" ]; then
    print_info "تثبيت Backend dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
    if [ $? -eq 0 ]; then
        print_success "تم تثبيت Backend dependencies"
    else
        print_error "فشل تثبيت Backend dependencies"
        exit 1
    fi
else
    print_success "Backend dependencies مثبتة مسبقاً"
fi

# Setup .env file
if [ ! -f ".env" ]; then
    print_info "إنشاء ملف .env..."
    cp .env.example .env
    print_success "تم إنشاء ملف .env"
    
    # Generate APP_KEY
    print_info "توليد APP_KEY..."
    php artisan key:generate --force
    print_success "تم توليد APP_KEY"
    
    # Generate JWT_SECRET
    print_info "توليد JWT_SECRET..."
    php artisan jwt:secret --force
    print_success "تم توليد JWT_SECRET"
else
    print_success "ملف .env موجود مسبقاً"
fi

cd ..

echo ""
echo "المرحلة 3: إعداد Frontend"
echo "-------------------------"

cd frontend

# Install npm dependencies
if [ ! -d "node_modules" ]; then
    print_info "تثبيت Frontend dependencies..."
    npm install
    if [ $? -eq 0 ]; then
        print_success "تم تثبيت Frontend dependencies"
    else
        print_error "فشل تثبيت Frontend dependencies"
        exit 1
    fi
else
    print_success "Frontend dependencies مثبتة مسبقاً"
fi

# Setup environment file
if [ ! -f "src/environments/environment.ts" ]; then
    print_info "إنشاء ملف environment.ts..."
    cp src/environments/environment.example.ts src/environments/environment.ts
    print_success "تم إنشاء ملف environment.ts"
else
    print_success "ملف environment.ts موجود مسبقاً"
fi

cd ..

echo ""
echo "المرحلة 4: إعداد Admin Panel"
echo "-----------------------------"

cd admin-panel

# Install npm dependencies
if [ ! -d "node_modules" ]; then
    print_info "تثبيت Admin Panel dependencies..."
    npm install
    if [ $? -eq 0 ]; then
        print_success "تم تثبيت Admin Panel dependencies"
    else
        print_error "فشل تثبيت Admin Panel dependencies"
        exit 1
    fi
else
    print_success "Admin Panel dependencies مثبتة مسبقاً"
fi

# Setup environment file
if [ ! -f "src/environments/environment.ts" ]; then
    print_info "إنشاء ملف environment.ts..."
    cp src/environments/environment.example.ts src/environments/environment.ts
    print_success "تم إنشاء ملف environment.ts"
else
    print_success "ملف environment.ts موجود مسبقاً"
fi

cd ..

echo ""
echo "================================"
echo "✅ تم الإصلاح بنجاح!"
echo "================================"
echo ""
echo "الخطوات التالية:"
echo ""
echo "1️⃣  إعداد قاعدة البيانات:"
echo "   cd backend"
echo "   # تحديث بيانات قاعدة البيانات في .env"
echo "   php artisan migrate --seed"
echo ""
echo "2️⃣  تشغيل Backend:"
echo "   cd backend"
echo "   php artisan serve"
echo ""
echo "3️⃣  تشغيل Frontend:"
echo "   cd frontend"
echo "   ng serve"
echo ""
echo "4️⃣  تشغيل Admin Panel:"
echo "   cd admin-panel"
echo "   ng serve --port 4201"
echo ""
echo "🔐 بيانات الدخول الافتراضية:"
echo "   Email: admin@ashub.com"
echo "   Password: Admin@123"
echo ""
echo "📖 للمزيد من المعلومات، راجع:"
echo "   - QUICK_START.md"
echo "   - ISSUES_REPORT.md"
echo ""
