# 🚀 AS Hub - Quick Start Guide

## نظام التسعير المتقدم - دليل البدء السريع

---

## ⚡ التشغيل السريع (5 دقائق)

### 1️⃣ Backend Setup

```bash
cd backend

# تثبيت المكتبات
composer install

# نسخ ملف البيئة
cp .env.example .env

# تحديث بيانات قاعدة البيانات في .env
DB_DATABASE=u643694170_Abood
DB_USERNAME=u643694170_Abood
DB_PASSWORD=your_password

# توليد مفاتيح التطبيق
php artisan key:generate
php artisan jwt:secret

# تشغيل Migration والبيانات التجريبية
php artisan migrate --seed

# تشغيل السيرفر
php artisan serve
```

✅ **Backend جاهز على:** http://localhost:8000

---

### 2️⃣ Frontend Setup

```bash
cd frontend

# تثبيت المكتبات
npm install

# تحديث API URL في environment.ts
# apiUrl: 'http://localhost:8000/api'

# تشغيل السيرفر
ng serve
```

✅ **Frontend جاهز على:** http://localhost:4200

---

### 3️⃣ Admin Panel Setup

```bash
cd admin-panel

# تثبيت المكتبات
npm install

# تحديث API URL في environment.ts
# apiUrl: 'http://localhost:8000/api'

# تشغيل السيرفر
ng serve --port 4201
```

✅ **Admin Panel جاهز على:** http://localhost:4201

---

## 🔐 بيانات الدخول الافتراضية

```
Email: admin@ashub.com
Password: Admin@123
```

⚠️ **مهم:** غيّر كلمة المرور فوراً في الإنتاج!

---

## 📊 البيانات التجريبية

بعد تشغيل `php artisan migrate --seed` سيتم إنشاء:

### ✅ 18 خطة تسعير:

**English Plans (9):**
- 🌐 Website: Basic, Professional, Enterprise
- 📱 App: Basic, Professional, Enterprise  
- 🚀 Package: Basic, Professional, Enterprise

**Arabic Plans (9):**
- 🌐 موقع: أساسي، احترافي، مؤسسي
- 📱 تطبيق: أساسي، احترافي، مؤسسي
- 🚀 باقة: أساسي، احترافي، مؤسسي

---

## 🧪 اختبار سريع

### 1. اختبار Backend API

```bash
# Health Check
curl http://localhost:8000/api/health

# Get Content (English)
curl http://localhost:8000/api/v1/content?language=en

# Get Content (Arabic)
curl http://localhost:8000/api/v1/content?language=ar
```

### 2. اختبار Frontend

1. افتح http://localhost:4200
2. انتقل لقسم Pricing
3. جرب تبديل Service Type (Website/App/Package)
4. جرب تبديل Billing (Monthly/Yearly)
5. جرب تبديل اللغة (EN/AR)

### 3. اختبار Admin Panel

1. افتح http://localhost:4201
2. سجل دخول بالبيانات الافتراضية
3. انتقل لـ Pricing Management
4. جرب الفلاتر (Language, Service Type, Tier)
5. جرب إضافة خطة جديدة
6. جرب تعديل خطة موجودة

---

## 🎯 الميزات الرئيسية

### Frontend (الموقع العام)
✅ عرض الأسعار حسب نوع الخدمة
✅ تبديل بين الشهري والسنوي
✅ عرض نسبة التوفير
✅ دعم اللغتين (EN/AR)
✅ تصميم متجاوب
✅ تأثيرات بصرية احترافية

### Admin Panel (لوحة التحكم)
✅ إدارة كاملة للأسعار (CRUD)
✅ فلترة حسب اللغة
✅ فلترة حسب نوع الخدمة
✅ فلترة حسب المستوى
✅ نسخ الخطط
✅ تفعيل/تعطيل الخطط
✅ إحصائيات مباشرة

### Backend API
✅ RESTful API
✅ JWT Authentication
✅ Input Validation
✅ CORS Support
✅ Grouped Pricing Format
✅ Auto-calculated Savings

---

## 📁 هيكل المشروع

```
as-hub-web/
├── frontend/                    # Angular Landing Page
│   ├── src/app/components/
│   │   └── pricing/            # Pricing Component
│   └── src/app/services/
│       └── api.service.ts      # API Service
│
├── admin-panel/                 # Angular Admin CMS
│   └── src/app/pages/
│       └── pricing/            # Pricing Management
│
├── backend/                     # Laravel API
│   ├── app/Models/
│   │   └── PricingPlan.php    # Pricing Model
│   ├── app/Http/Controllers/
│   │   ├── ContentController.php
│   │   └── Admin/
│   │       └── PricingController.php
│   └── database/
│       ├── migrations/
│       │   └── 2024_01_15_*_add_service_type_and_tier.php
│       └── seeders/
│           └── PricingPlansSeeder.php
│
└── database/
    └── schema.sql              # Database Schema
```

---

## 🔧 الأوامر المفيدة

### Backend Commands

```bash
# إعادة تشغيل Migration والبيانات
php artisan migrate:fresh --seed

# تشغيل Seeder فقط
php artisan db:seed --class=PricingPlansSeeder

# مسح الـ Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# إنشاء رابط للملفات
php artisan storage:link
```

### Frontend Commands

```bash
# تشغيل Development Server
ng serve

# Build للإنتاج
ng build --configuration production

# تشغيل Tests
ng test
```

---

## 🐛 حل المشاكل الشائعة

### ❌ Migration Error
```bash
# حذف الجدول وإعادة المحاولة
php artisan migrate:fresh --seed
```

### ❌ CORS Error
تأكد من إضافة Frontend URL في `backend/config/cors.php`:
```php
'allowed_origins' => [
    'http://localhost:4200',
    'http://localhost:4201',
],
```

### ❌ JWT Error
```bash
# إعادة توليد JWT Secret
php artisan jwt:secret --force
```

### ❌ Frontend لا يعرض البيانات
1. تحقق من API URL في `environment.ts`
2. تحقق من تشغيل Backend
3. افتح Console في المتصفح للأخطاء

---

## 📊 API Endpoints السريعة

### Public
```
GET  /api/v1/content?language=en    # Get all content
POST /api/v1/leads                  # Submit contact form
GET  /api/health                    # Health check
```

### Admin (Requires JWT)
```
POST /api/auth/login                # Login
GET  /api/admin/pricing             # List plans
POST /api/admin/pricing             # Create plan
PUT  /api/admin/pricing/{id}        # Update plan
DELETE /api/admin/pricing/{id}      # Delete plan
```

---

## 📞 الدعم

### للمساعدة:
- 📧 Email: support@ashub.com
- 📖 Documentation: `PRICING_SYSTEM_DOCUMENTATION.md`
- 🔧 Quick Start: هذا الملف

### الملفات المهمة:
- `README.md` - نظرة عامة على المشروع
- `PRICING_SYSTEM_DOCUMENTATION.md` - توثيق كامل للنظام
- `QUICK_START.md` - هذا الملف

---

## ✅ Checklist للتأكد من التشغيل

- [ ] Backend يعمل على http://localhost:8000
- [ ] Frontend يعمل على http://localhost:4200
- [ ] Admin Panel يعمل على http://localhost:4201
- [ ] Database تحتوي على 18 خطة تسعير
- [ ] API يرجع البيانات بنجاح
- [ ] يمكن تسجيل الدخول للـ Admin Panel
- [ ] يمكن إضافة/تعديل/حذف الخطط
- [ ] Frontend يعرض الأسعار من API
- [ ] تبديل اللغة يعمل بشكل صحيح

---

## 🎉 جاهز للانطلاق!

الآن النظام جاهز بالكامل! يمكنك:

1. ✅ عرض الأسعار في الموقع العام
2. ✅ إدارة الأسعار من لوحة التحكم
3. ✅ إضافة خطط جديدة
4. ✅ تعديل الأسعار الموجودة
5. ✅ دعم اللغتين العربية والإنجليزية

**استمتع بالعمل! 🚀**

---

**Last Updated:** 2024-01-15
**Version:** 2.0.0
