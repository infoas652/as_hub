# 🔧 حل مشاكل تسجيل الدخول - AS Hub Admin Panel

## ✅ التحقق من البيانات

### 1. بيانات الدخول الصحيحة
```
📧 Email: admin@ashub.com
🔑 Password: Admin@123456
```

⚠️ **مهم:** تأكد من كتابة البيانات بالضبط كما هي (حساسة لحالة الأحرف)

---

## 🔍 خطوات استكشاف الأخطاء

### الخطوة 1: تحقق من Backend

```bash
# تأكد أن Backend يعمل
cd backend
php artisan serve --port=8000
```

**يجب أن تشاهد:**
```
Starting Laravel development server: http://127.0.0.1:8000
```

**اختبار API:**
```bash
php test-api-login.php
```

**النتيجة المتوقعة:** ✓ Login Successful!

---

### الخطوة 2: تحقق من Admin Panel

```bash
# أوقف Admin Panel (Ctrl+C)
# ثم أعد تشغيله
cd admin-panel
ng serve --port 4202
```

**يجب أن تشاهد:**
```
✔ Browser application bundle generation complete.
** Angular Live Development Server is listening on localhost:4202 **
```

---

### الخطوة 3: امسح Cache المتصفح

1. افتح Developer Tools (F12)
2. اذهب إلى **Application** (أو **Storage**)
3. اضغط على **Clear storage**
4. اضغط **Clear site data**
5. أعد تحميل الصفحة (Ctrl+Shift+R)

---

### الخطوة 4: تحقق من Console

1. افتح Developer Tools (F12)
2. اذهب إلى **Console**
3. ابحث عن أخطاء (باللون الأحمر)

#### الأخطاء الشائعة:

**أ) CORS Error**
```
Access to XMLHttpRequest at 'http://localhost:8000/api/auth/login' 
from origin 'http://localhost:4202' has been blocked by CORS policy
```

**الحل:**
```bash
cd backend
# تحقق من config/cors.php
# تأكد أن localhost:4202 موجود في allowed_origins
```

**ب) Network Error**
```
Http failure response for http://localhost:8000/api/auth/login: 0 Unknown Error
```

**الحل:**
- تأكد أن Backend يعمل على port 8000
- جرب: `curl http://localhost:8000/api/health`

**ج) 401 Unauthorized**
```
{
  "message": "Invalid credentials"
}
```

**الحل:**
```bash
# أعد تعيين كلمة المرور
php backend/reset-to-default-admin.php
```

---

### الخطوة 5: تحقق من Network

1. افتح Developer Tools (F12)
2. اذهب إلى **Network**
3. حاول تسجيل الدخول
4. ابحث عن طلب `login`
5. اضغط عليه وشاهد:
   - **Request URL:** يجب أن يكون `http://localhost:8000/api/auth/login`
   - **Request Method:** POST
   - **Status Code:** 200 (إذا نجح) أو 401 (إذا فشل)
   - **Response:** شاهد الرد من الـ API

---

## 🛠️ حلول سريعة

### الحل 1: إعادة تشغيل كل شيء

```bash
# أوقف كل شيء (Ctrl+C في كل Terminal)

# Terminal 1 - Backend
cd backend
php artisan serve --port=8000

# Terminal 2 - Admin Panel (terminal جديد)
cd admin-panel
ng serve --port 4202
```

### الحل 2: إعادة تعيين Admin

```bash
php backend/reset-to-default-admin.php
```

### الحل 3: مسح كل شيء وإعادة البناء

```bash
# Admin Panel
cd admin-panel
rm -rf .angular
ng build
ng serve --port 4202
```

### الحل 4: تحقق من environment.ts

```bash
# تأكد أن الملف موجود
cat admin-panel/src/environments/environment.ts
```

**يجب أن يحتوي على:**
```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api'
};
```

---

## 🧪 اختبار يدوي

### اختبار 1: API مباشرة

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@ashub.com","password":"Admin@123456"}'
```

**النتيجة المتوقعة:**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJh...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

### اختبار 2: Database

```bash
php test-login.php
```

**النتيجة المتوقعة:**
```
✓ Login credentials are correct!
```

---

## 📋 Checklist

قبل المحاولة مرة أخرى، تأكد من:

- [ ] Backend يعمل على port 8000
- [ ] Admin Panel يعمل على port 4202
- [ ] تم مسح cache المتصفح
- [ ] تم إعادة تشغيل Admin Panel
- [ ] لا توجد أخطاء في Console
- [ ] البيانات صحيحة: admin@ashub.com / Admin@123456
- [ ] environment.ts موجود وصحيح

---

## 🆘 إذا لم ينجح شيء

### جرب هذا:

```bash
# 1. أوقف كل شيء
# اضغط Ctrl+C في كل Terminal

# 2. أعد تعيين Admin
php backend/reset-to-default-admin.php

# 3. أعد تشغيل Backend
cd backend
php artisan cache:clear
php artisan config:clear
php artisan serve --port=8000

# 4. في terminal جديد، أعد تشغيل Admin Panel
cd admin-panel
rm -rf .angular
ng serve --port 4202

# 5. افتح متصفح جديد (Incognito/Private)
# اذهب إلى: http://localhost:4202
# سجل دخول: admin@ashub.com / Admin@123456
```

---

## 📞 معلومات إضافية

### Ports المستخدمة
- Backend API: `http://localhost:8000`
- Admin Panel: `http://localhost:4202`
- Frontend (إذا كان يعمل): `http://localhost:4200`

### الملفات المهمة
- `admin-panel/src/environments/environment.ts` - إعدادات API
- `admin-panel/src/app/services/auth.service.ts` - خدمة المصادقة
- `backend/config/cors.php` - إعدادات CORS
- `backend/.env` - إعدادات Backend

---

**تم إنشاء هذا الدليل بواسطة BLACKBOX AI** ✨
