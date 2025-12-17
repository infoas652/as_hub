# AS Hub - Advanced Pricing System Documentation

## 🎯 Overview

تم تطوير نظام تسعير متقدم يدعم 3 فئات رئيسية من الخدمات، كل فئة تحتوي على 3 مستويات (Basic, Professional, Enterprise) مع خيارات الدفع الشهري والسنوي.

---

## 📊 System Architecture

### Service Types (فئات الخدمات)

1. **Website Development (🌐 تطوير المواقع)**
   - Basic: $299/month - $2,990/year
   - Professional: $599/month - $5,990/year
   - Enterprise: $1,299/month - $12,990/year

2. **Mobile App Development (📱 تطوير التطبيقات)**
   - Basic: $499/month - $4,990/year
   - Professional: $999/month - $9,990/year
   - Enterprise: $2,499/month - $24,990/year

3. **Website + App Package (🚀 الباقة المتكاملة)**
   - Basic: $699/month - $6,990/year
   - Professional: $1,399/month - $13,990/year
   - Enterprise: $2,999/month - $29,990/year

---

## 🗄️ Database Schema

### Updated `pricing_plans` Table

```sql
CREATE TABLE pricing_plans (
  id BIGINT PRIMARY KEY,
  language ENUM('en', 'ar'),
  service_type ENUM('website', 'app', 'both'),  -- NEW
  tier ENUM('basic', 'professional', 'enterprise'),  -- NEW
  name VARCHAR(255),
  slug VARCHAR(255),
  description TEXT,
  price_monthly DECIMAL(10,2),
  price_yearly DECIMAL(10,2),
  features JSON,
  is_popular BOOLEAN,
  order INT,
  is_active BOOLEAN,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### Indexes
- `service_type` - للبحث السريع حسب نوع الخدمة
- `tier` - للبحث حسب المستوى
- `(service_type, tier, language)` - للبحث المركب

---

## 🔧 Backend Updates

### 1. Migration File
**Location:** `backend/database/migrations/2024_01_15_000001_add_service_type_and_tier_to_pricing_plans.php`

```bash
php artisan migrate
```

### 2. Model Updates
**Location:** `backend/app/Models/PricingPlan.php`

**New Features:**
- `service_type` and `tier` fields
- Auto-calculated `savings` attribute
- Auto-calculated `savings_percentage` attribute
- Scopes: `serviceType()`, `tier()`
- Auto-slug generation

### 3. Controller Updates
**Location:** `backend/app/Http/Controllers/Admin/PricingController.php`

**New Features:**
- Filter by `service_type`
- Filter by `tier`
- Validation for unique combination (language + service_type + tier)
- Enhanced response with savings calculations

### 4. Content API Updates
**Location:** `backend/app/Http/Controllers/ContentController.php`

**New Response Format:**
```json
{
  "success": true,
  "language": "en",
  "data": {
    "pricing": [...],  // Flat array (backward compatible)
    "pricing_by_service": {  // NEW: Grouped format
      "website": [...],
      "app": [...],
      "both": [...]
    }
  }
}
```

### 5. Seeder
**Location:** `backend/database/seeders/PricingPlansSeeder.php`

**Contains:**
- 9 English plans (3 per service type)
- 9 Arabic plans (3 per service type)
- Total: 18 pricing plans

**Run Seeder:**
```bash
php artisan db:seed --class=PricingPlansSeeder
```

---

## 🎨 Frontend Updates

### 1. Pricing Component
**Location:** `frontend/src/app/components/pricing/pricing.component.ts`

**New Features:**
- Loads pricing from API
- Groups plans by service_type
- Supports both API formats (flat & grouped)
- Auto-calculates savings if not provided by API
- Language-aware display

### 2. API Service
**Location:** `frontend/src/app/services/api.service.ts`

**Updated Interface:**
```typescript
interface ContentResponse {
  data?: {
    pricing: any[];
    pricing_by_service?: {
      website: any[];
      app: any[];
      both: any[];
    };
  };
}
```

---

## 🎛️ Admin Panel Updates

### 1. Pricing Management Component
**Location:** `admin-panel/src/app/pages/pricing/pricing.component.ts`

**New Features:**
- Service Type selector (Website, App, Both)
- Tier selector (Basic, Professional, Enterprise)
- Enhanced filtering
- Statistics cards by category
- Duplicate plan functionality
- Savings calculation display

### 2. UI Enhancements
**Location:** `admin-panel/src/app/pages/pricing/pricing.component.html`

**New Elements:**
- Service type badges with icons
- Tier badges with colors
- Savings display
- Enhanced modal form
- Better validation

### 3. Styling
**Location:** `admin-panel/src/app/pages/pricing/pricing.component.scss`

**Features:**
- Color-coded service types
- Tier-based styling
- Responsive grid layout
- Smooth animations
- Professional card design

---

## 🚀 Installation & Setup

### Step 1: Backend Setup

```bash
cd backend

# Run migration
php artisan migrate

# Run seeder
php artisan db:seed --class=PricingPlansSeeder

# Clear cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

### Step 2: Frontend Setup

```bash
cd frontend

# Install dependencies (if needed)
npm install

# Start development server
ng serve
```

### Step 3: Admin Panel Setup

```bash
cd admin-panel

# Install dependencies (if needed)
npm install

# Start development server
ng serve --port 4201
```

---

## 📡 API Endpoints

### Public Endpoints

#### Get Content (with new pricing format)
```http
GET /api/v1/content?language=en

Response:
{
  "success": true,
  "language": "en",
  "data": {
    "pricing": [...],
    "pricing_by_service": {
      "website": [...],
      "app": [...],
      "both": [...]
    }
  }
}
```

### Admin Endpoints (Requires JWT)

#### List Pricing Plans
```http
GET /api/admin/pricing?language=en&service_type=website&tier=professional

Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "language": "en",
      "service_type": "website",
      "tier": "professional",
      "name": "Professional Website",
      "price_monthly": 599,
      "price_yearly": 5990,
      "savings": 1098,
      "savings_percentage": 15,
      ...
    }
  ]
}
```

#### Create Pricing Plan
```http
POST /api/admin/pricing
Content-Type: application/json
Authorization: Bearer {token}

{
  "language": "en",
  "service_type": "website",
  "tier": "basic",
  "name": "Basic Website",
  "description": "Perfect for small businesses",
  "price_monthly": 299,
  "price_yearly": 2990,
  "features": [
    "Up to 5 pages",
    "Responsive design",
    "Basic SEO"
  ],
  "is_popular": false,
  "is_active": true
}
```

#### Update Pricing Plan
```http
PUT /api/admin/pricing/{id}
Content-Type: application/json
Authorization: Bearer {token}

{
  "price_monthly": 349,
  "price_yearly": 3490
}
```

#### Delete Pricing Plan
```http
DELETE /api/admin/pricing/{id}
Authorization: Bearer {token}
```

---

## ✅ Testing Checklist

### Backend Testing

- [ ] Run migration successfully
- [ ] Run seeder successfully
- [ ] Verify 18 plans created (9 EN + 9 AR)
- [ ] Test GET /api/v1/content?language=en
- [ ] Test GET /api/v1/content?language=ar
- [ ] Verify pricing_by_service in response
- [ ] Test admin endpoints with JWT
- [ ] Test filtering by service_type
- [ ] Test filtering by tier
- [ ] Test create/update/delete operations

### Frontend Testing

- [ ] Pricing component loads data from API
- [ ] Service type selector works
- [ ] Billing toggle (monthly/yearly) works
- [ ] Savings calculation displays correctly
- [ ] Language switching works (EN/AR)
- [ ] Responsive design on mobile
- [ ] All animations work smoothly

### Admin Panel Testing

- [ ] Login with admin credentials
- [ ] View all pricing plans
- [ ] Filter by language
- [ ] Filter by service type
- [ ] Filter by tier
- [ ] Create new pricing plan
- [ ] Edit existing plan
- [ ] Duplicate plan
- [ ] Toggle active status
- [ ] Delete plan
- [ ] Verify statistics cards
- [ ] Test form validation

---

## 🎨 Design Features

### Color Scheme

```scss
// Service Type Colors
$website-color: #3b82f6;  // Blue
$app-color: #8b5cf6;      // Purple
$both-color: #ec4899;     // Pink

// Tier Colors
$basic-color: #64748b;         // Gray
$professional-color: #3b82f6;  // Blue
$enterprise-color: #ec4899;    // Pink
```

### Icons

- 🌐 Website Development
- 📱 Mobile App Development
- 🚀 Website + App Package

---

## 📝 Usage Examples

### Admin: Create a New Plan

1. Navigate to Pricing Management
2. Click "Add New Plan"
3. Select Language (EN/AR)
4. Select Service Type (Website/App/Both)
5. Select Tier (Basic/Professional/Enterprise)
6. Enter plan details
7. Add features (minimum 1)
8. Set prices (monthly & yearly)
9. Mark as popular (optional)
10. Click "Create Plan"

### Frontend: Display Pricing

The pricing component automatically:
1. Loads data from API based on current language
2. Groups plans by service_type
3. Displays in organized tabs
4. Shows savings for yearly billing
5. Highlights popular plans

---

## 🔒 Security Notes

- All admin endpoints require JWT authentication
- Input validation on all fields
- Unique constraint on (language + service_type + tier)
- SQL injection protection via Eloquent ORM
- XSS protection enabled

---

## 🐛 Troubleshooting

### Issue: Migration fails
**Solution:** Check if pricing_plans table exists, drop it if needed:
```sql
DROP TABLE IF EXISTS pricing_plans;
```

### Issue: Seeder fails
**Solution:** Truncate table first:
```bash
php artisan db:seed --class=PricingPlansSeeder --force
```

### Issue: Frontend not showing data
**Solution:** 
1. Check API URL in environment.ts
2. Verify CORS settings in backend
3. Check browser console for errors

### Issue: Admin panel can't create plans
**Solution:**
1. Verify JWT token is valid
2. Check all required fields are filled
3. Ensure no duplicate (language + service_type + tier)

---

## 📞 Support

For issues or questions:
- Email: support@ashub.com
- Documentation: This file
- API Docs: See API Endpoints section above

---

## 🎉 Summary

✅ **Database:** Updated with service_type and tier fields
✅ **Backend:** Full CRUD API with filtering and validation
✅ **Frontend:** Dynamic pricing display from API
✅ **Admin Panel:** Complete management interface
✅ **Seeder:** 18 sample plans (9 EN + 9 AR)
✅ **Documentation:** Complete setup and usage guide

**Total Plans:** 18 (3 service types × 3 tiers × 2 languages)

---

**Last Updated:** 2024-01-15
**Version:** 2.0.0
**Author:** AS Hub Development Team
