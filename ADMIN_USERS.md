# 👥 Admin Users - معلومات المستخدمين

## 🔐 حسابات الأدمن

### 1. Admin الافتراضي
```
Email: admin@ashub.com
Password: Admin@123456
Name: Admin
```

### 2. AS Hub Admin (Abood)
```
Email: info@as-hub.com
Password: Abood!0595466383
Name: Abood
```

---

## 🚀 كيفية إنشاء المستخدمين

### الطريقة 1: استخدام Seeder (موصى بها)

```bash
cd backend
php artisan db:seed --class=AdminSeeder
```

### الطريقة 2: إعادة تشغيل كل الـ Migrations + Seeders

```bash
cd backend
php artisan migrate:fresh --seed
```

⚠️ **تحذير**: هذا سيحذف كل البيانات الموجودة!

### الطريقة 3: يدوياً من MySQL

```sql
USE u643694170_Abood;

INSERT INTO admins (name, email, password, is_active, created_at, updated_at) VALUES
('Admin', 'admin@ashub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW()),
('Abood', 'info@as-hub.com', '$2y$10$[HASHED_PASSWORD]', 1, NOW(), NOW());
```

---

## 🔑 تغيير كلمة المرور

### من لوحة التحكم:
1. سجل دخول
2. اذهب إلى Profile
3. غيّر كلمة المرور

### من Terminal:
```bash
cd backend
php artisan tinker

# ثم اكتب:
$admin = App\Models\Admin::where('email', 'info@as-hub.com')->first();
$admin->password = Hash::make('كلمة_المرور_الجديدة');
$admin->save();
```

---

## 📝 ملاحظات

1. **الأمان**: 
   - غيّر كلمات المرور الافتراضية فوراً
   - استخدم كلمات مرور قوية
   - لا تشارك كلمات المرور

2. **الصلاحيات**:
   - كل الأدمن لهم نفس الصلاحيات حالياً
   - يمكن إضافة نظام Roles لاحقاً

3. **النسخ الاحتياطي**:
   - احتفظ بنسخة احتياطية من قاعدة البيانات
   - سجل معلومات الدخول في مكان آمن

---

## ✅ التحقق من المستخدمين

```bash
cd backend
php artisan tinker

# عرض كل الأدمن:
App\Models\Admin::all();

# عرض أدمن معين:
App\Models\Admin::where('email', 'info@as-hub.com')->first();
```

---

**تم الإنشاء بواسطة BLACKBOX AI** ✨
**AS Hub © 2024**
