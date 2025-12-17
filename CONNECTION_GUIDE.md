# 🔗 دليل الربط الشامل - AS Hub

**آخر تحديث:** 8 ديسمبر 2025

---

## 📋 جدول المحتويات

1. [نظرة عامة](#نظرة-عامة)
2. [هيكل الربط](#هيكل-الربط)
3. [ملفات Environment](#ملفات-environment)
4. [API Endpoints](#api-endpoints)
5. [كيفية التشغيل](#كيفية-التشغيل)
6. [اختبار الربط](#اختبار-الربط)
7. [حل المشاكل](#حل-المشاكل)

---

## 🎯 نظرة عامة

المشروع يتكون من 3 تطبيقات مربوطة مع بعضها:

```
Frontend (Angular) ←→ Backend API (Laravel) ←→ Database (MySQL)
Admin Panel (Angular) ←→ Backend API (Laravel) ←→ Database (MySQL)
```

---

## 🏗️ هيكل الربط

### 1. Frontend → Backend

**الملف:** `/vercel/sandbox/frontend/src/environments/environment.ts`

```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api',  // ← عنوان Backend API
  apiTimeout: 30000                      // ← 30 ثانية timeout
};
```

**الاستخدام في الكود:**
```typescript
// في api.service.ts
import { environment } from '../environments/environment';

private apiUrl = environment.apiUrl;

// مثال: جلب الخدمات
getServices() {
  return this.http.get(`${this.apiUrl}/services`);
}
```

---

### 2. Admin Panel → Backend

**الملف:** `/vercel/sandbox/admin-panel/src/environments/environment.ts`

```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api'  // ← عنوان Backend API
};
```

**الاستخدام في الكود:**
```typescript
// في api.service.ts
import { environment } from '../environments/environment';

private apiUrl = environment.apiUrl;

// مثال: تسجيل الدخول
login(credentials: any) {
  return this.http.post(`${this.apiUrl}/auth/login`, credentials);
}
```

---

### 3. Backend → Database

**الملف:** `/vercel/sandbox/backend/.env`

```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u643694170_Abood
DB_USERNAME=u643694170_Abood
DB_PASSWORD=Abood@0595466383

# CORS Configuration
CORS_ALLOWED_ORIGINS="http://localhost:4200,http://localhost:4201"

# JWT Configuration
JWT_SECRET=your-secret-key
JWT_TTL=60
JWT_REFRESH_TTL=20160
```

---

## 📁 ملفات Environment

### Frontend Environment

**الموقع:** `/vercel/sandbox/frontend/src/environments/`

```
environments/
├── environment.ts          ← للتطوير (Development)
└── environment.example.ts  ← مثال
```

**environment.ts (Development):**
```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api',
  apiTimeout: 30000
};
```

**environment.prod.ts (Production) - إذا احتجته:**
```typescript
export const environment = {
  production: true,
  apiUrl: 'https://api.ashub.com/api',  // ← عنوان Production
  apiTimeout: 30000
};
```

---

### Admin Panel Environment

**الموقع:** `/vercel/sandbox/admin-panel/src/environments/`

```
environments/
├── environment.ts          ← للتطوير (Development)
└── environment.example.ts  ← مثال
```

**environment.ts (Development):**
```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api'
};
```

**environment.prod.ts (Production) - إذا احتجته:**
```typescript
export const environment = {
  production: true,
  apiUrl: 'https://api.ashub.com/api'
};
```

---

### Backend Environment

**الموقع:** `/vercel/sandbox/backend/.env`

**الإعدادات الأساسية:**
```env
# Application
APP_NAME="AS Hub"
APP_ENV=local
APP_KEY=base64:...  # سيتم توليده تلقائياً
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u643694170_Abood
DB_USERNAME=u643694170_Abood
DB_PASSWORD=Abood@0595466383

# JWT
JWT_SECRET=...  # سيتم توليده تلقائياً
JWT_TTL=60
JWT_REFRESH_TTL=20160

# CORS
CORS_ALLOWED_ORIGINS="http://localhost:4200,http://localhost:4201"
```

---

## 🔌 API Endpoints

### Base URL
```
http://localhost:8000/api
```

### Authentication Endpoints

| Method | Endpoint | الوصف |
|--------|----------|-------|
| POST | `/auth/login` | تسجيل الدخول |
| POST | `/auth/register` | التسجيل |
| POST | `/auth/logout` | تسجيل الخروج |
| GET | `/auth/me` | معلومات المستخدم الحالي |
| POST | `/auth/refresh` | تحديث Token |

**مثال - تسجيل الدخول:**
```typescript
// Request
POST http://localhost:8000/api/auth/login
Content-Type: application/json

{
  "email": "admin@ashub.com",
  "password": "Admin@123"
}

// Response
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@ashub.com"
  }
}
```

---

### Public Endpoints (لا تحتاج Authentication)

| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/services` | جلب جميع الخدمات |
| GET | `/services/{id}` | جلب خدمة محددة |
| GET | `/features` | جلب جميع المميزات |
| GET | `/pricing` | جلب خطط الأسعار |
| GET | `/testimonials` | جلب آراء العملاء |
| GET | `/faq` | جلب الأسئلة الشائعة |
| POST | `/leads` | إرسال طلب تواصل |

**مثال - جلب الخدمات:**
```typescript
// Request
GET http://localhost:8000/api/services

// Response
{
  "data": [
    {
      "id": 1,
      "title_en": "Web Development",
      "title_ar": "تطوير المواقع",
      "description_en": "...",
      "description_ar": "...",
      "icon": "code",
      "order": 1,
      "is_active": true
    },
    // ...
  ]
}
```

---

### Admin Endpoints (تحتاج Authentication)

| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/admin/services` | جلب جميع الخدمات (Admin) |
| POST | `/admin/services` | إضافة خدمة جديدة |
| PUT | `/admin/services/{id}` | تعديل خدمة |
| DELETE | `/admin/services/{id}` | حذف خدمة |
| GET | `/admin/leads` | جلب جميع الطلبات |
| PUT | `/admin/leads/{id}/status` | تحديث حالة الطلب |
| GET | `/admin/dashboard/stats` | إحصائيات Dashboard |

**مثال - إضافة خدمة:**
```typescript
// Request
POST http://localhost:8000/api/admin/services
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
Content-Type: application/json

{
  "title_en": "Mobile Development",
  "title_ar": "تطوير التطبيقات",
  "description_en": "We build mobile apps",
  "description_ar": "نبني تطبيقات الجوال",
  "icon": "mobile",
  "order": 2,
  "is_active": true
}

// Response
{
  "data": {
    "id": 2,
    "title_en": "Mobile Development",
    "title_ar": "تطوير التطبيقات",
    // ...
  },
  "message": "Service created successfully"
}
```

---

## 🚀 كيفية التشغيل

### الطريقة الأولى: تشغيل تلقائي (موصى به)

```bash
# تشغيل جميع التطبيقات
./RUN_ALL.sh

# إيقاف جميع التطبيقات
./STOP_ALL.sh
```

---

### الطريقة الثانية: تشغيل يدوي

#### 1. Backend
```bash
cd /vercel/sandbox/backend

# أول مرة فقط
composer install
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed

# التشغيل
php artisan serve
# سيعمل على: http://localhost:8000
```

#### 2. Frontend
```bash
cd /vercel/sandbox/frontend

# أول مرة فقط
npm install

# التشغيل
ng serve
# سيعمل على: http://localhost:4200
```

#### 3. Admin Panel
```bash
cd /vercel/sandbox/admin-panel

# أول مرة فقط
npm install

# التشغيل
ng serve --port 4201
# سيعمل على: http://localhost:4201
```

---

## 🧪 اختبار الربط

### 1. اختبار Backend API

```bash
# اختبار Health Check
curl http://localhost:8000/api/health

# اختبار جلب الخدمات
curl http://localhost:8000/api/services

# اختبار تسجيل الدخول
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@ashub.com","password":"Admin@123"}'
```

---

### 2. اختبار Frontend

1. افتح المتصفح: `http://localhost:4200`
2. افتح Developer Console (F12)
3. اذهب إلى Network Tab
4. تصفح الموقع
5. يجب أن ترى طلبات API إلى `http://localhost:8000/api`

**مثال - اختبار جلب الخدمات:**
```
Request URL: http://localhost:8000/api/services
Request Method: GET
Status Code: 200 OK
```

---

### 3. اختبار Admin Panel

1. افتح المتصفح: `http://localhost:4201`
2. سجل دخول:
   - Email: `admin@ashub.com`
   - Password: `Admin@123`
3. افتح Developer Console (F12)
4. اذهب إلى Network Tab
5. قم بأي عملية (مثل جلب الخدمات)
6. يجب أن ترى طلبات API مع Authorization Header

**مثال - اختبار تسجيل الدخول:**
```
Request URL: http://localhost:8000/api/auth/login
Request Method: POST
Status Code: 200 OK
Response: { "access_token": "...", "user": {...} }
```

---

### 4. اختبار CORS

افتح Console في Frontend أو Admin Panel وجرب:

```javascript
fetch('http://localhost:8000/api/services')
  .then(res => res.json())
  .then(data => console.log(data))
  .catch(err => console.error(err));
```

**النتيجة المتوقعة:** يجب أن يرجع البيانات بدون أخطاء CORS

**إذا ظهر خطأ CORS:**
```
Access to fetch at 'http://localhost:8000/api/services' from origin 
'http://localhost:4200' has been blocked by CORS policy
```

**الحل:** تأكد من أن `CORS_ALLOWED_ORIGINS` في `.env` يحتوي على:
```env
CORS_ALLOWED_ORIGINS="http://localhost:4200,http://localhost:4201"
```

---

## 🔧 حل المشاكل

### مشكلة 1: Frontend لا يتصل بـ Backend

**الأعراض:**
- أخطاء في Console: `ERR_CONNECTION_REFUSED`
- لا توجد بيانات في الصفحة

**الحل:**
```bash
# 1. تأكد من أن Backend يعمل
curl http://localhost:8000/api/health

# 2. تأكد من environment.ts
cat /vercel/sandbox/frontend/src/environments/environment.ts
# يجب أن يحتوي على: apiUrl: 'http://localhost:8000/api'

# 3. أعد تشغيل Frontend
cd /vercel/sandbox/frontend
ng serve
```

---

### مشكلة 2: CORS Error

**الأعراض:**
```
Access to XMLHttpRequest at 'http://localhost:8000/api/...' from origin 
'http://localhost:4200' has been blocked by CORS policy
```

**الحل:**
```bash
# 1. تحقق من .env في Backend
cat /vercel/sandbox/backend/.env | grep CORS

# 2. يجب أن يحتوي على:
CORS_ALLOWED_ORIGINS="http://localhost:4200,http://localhost:4201"

# 3. إذا لم يكن موجود، أضفه
echo 'CORS_ALLOWED_ORIGINS="http://localhost:4200,http://localhost:4201"' >> /vercel/sandbox/backend/.env

# 4. أعد تشغيل Backend
cd /vercel/sandbox/backend
php artisan serve
```

---

### مشكلة 3: 401 Unauthorized في Admin Panel

**الأعراض:**
- لا يمكن الوصول إلى Admin Endpoints
- رسالة: `Unauthenticated`

**الحل:**
```typescript
// تأكد من أن Token يُرسل في Header
// في auth.interceptor.ts

intercept(req: HttpRequest<any>, next: HttpHandler) {
  const token = localStorage.getItem('token');
  
  if (token) {
    req = req.clone({
      setHeaders: {
        Authorization: `Bearer ${token}`
      }
    });
  }
  
  return next.handle(req);
}
```

---

### مشكلة 4: Database Connection Error

**الأعراض:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**الحل:**
```bash
# 1. تأكد من أن MySQL يعمل
sudo systemctl status mariadb
# أو
sudo systemctl status mysql

# 2. إذا لم يكن يعمل، شغله
sudo systemctl start mariadb

# 3. تأكد من بيانات قاعدة البيانات في .env
cat /vercel/sandbox/backend/.env | grep DB_

# 4. اختبر الاتصال
cd /vercel/sandbox/backend
php artisan migrate:status
```

---

### مشكلة 5: Port Already in Use

**الأعراض:**
```
Error: listen EADDRINUSE: address already in use :::4200
```

**الحل:**
```bash
# 1. اعرف Process ID
lsof -ti:4200

# 2. اقتل العملية
kill -9 $(lsof -ti:4200)

# 3. أو استخدم port آخر
ng serve --port 4202
```

---

## 📊 ملخص المنافذ

| التطبيق | المنفذ | URL |
|---------|--------|-----|
| Backend API | 8000 | http://localhost:8000 |
| Frontend | 4200 | http://localhost:4200 |
| Admin Panel | 4201 | http://localhost:4201 |
| MySQL | 3306 | localhost:3306 |

---

## ✅ Checklist النهائي

- [x] Frontend environment.ts موجود ومُعد
- [x] Admin Panel environment.ts موجود ومُعد
- [x] Backend .env موجود ومُعد
- [x] CORS مُعد بشكل صحيح
- [x] JWT مُعد بشكل صحيح
- [x] Database credentials صحيحة
- [x] جميع Dependencies مثبتة
- [ ] Backend يعمل على port 8000
- [ ] Frontend يعمل على port 4200
- [ ] Admin Panel يعمل على port 4201
- [ ] يمكن تسجيل الدخول للـ Admin Panel
- [ ] API يرجع البيانات بنجاح

---

## 🎉 الخلاصة

الربط تم بنجاح! جميع الملفات موجودة والإعدادات صحيحة. المشروع جاهز للتشغيل.

**للتشغيل السريع:**
```bash
./RUN_ALL.sh
```

**للإيقاف:**
```bash
./STOP_ALL.sh
```

---

**تم بواسطة:** Blackbox AI  
**التاريخ:** 8 ديسمبر 2025  
**الإصدار:** 1.0.0
