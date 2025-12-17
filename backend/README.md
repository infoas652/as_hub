# AS Hub Backend API

Laravel-based REST API for AS Hub platform with JWT authentication.

## Features

- ✅ JWT Authentication
- ✅ RESTful API endpoints
- ✅ Input validation & sanitization
- ✅ File upload handling
- ✅ CORS configuration
- ✅ Database migrations & seeders
- ✅ API rate limiting

## Requirements

- PHP 8.1 or higher
- Composer
- MySQL 8.0+
- PHP Extensions: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath

## Installation

```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Update database credentials in .env
DB_DATABASE=u643694170_Abood
DB_USERNAME=u643694170_Abood
DB_PASSWORD=your_password

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret

# Run migrations and seeders
php artisan migrate --seed

# Create storage link
php artisan storage:link

# Start development server
php artisan serve
```

API will be available at: `http://localhost:8000`

## Default Admin Credentials

```
Email: admin@ashub.com
Password: Admin@123
```

**⚠️ Change immediately in production!**

## API Endpoints

### Public Endpoints

#### Get Landing Page Content
```http
GET /api/content
```

Response includes: services, pricing, features, testimonials, FAQ, settings

#### Submit Contact Form
```http
POST /api/leads
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "company": "Example Corp",
  "message": "Interested in your services",
  "plan": "professional"
}
```

### Authentication

#### Admin Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@ashub.com",
  "password": "Admin@123"
}
```

Response:
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

#### Refresh Token
```http
POST /api/auth/refresh
Authorization: Bearer {token}
```

#### Logout
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

### Admin Endpoints (Requires JWT)

All admin endpoints require `Authorization: Bearer {token}` header.

#### Services
```http
GET    /api/admin/services
GET    /api/admin/services/{id}
POST   /api/admin/services
PUT    /api/admin/services/{id}
DELETE /api/admin/services/{id}
```

#### Pricing Plans
```http
GET    /api/admin/pricing
GET    /api/admin/pricing/{id}
POST   /api/admin/pricing
PUT    /api/admin/pricing/{id}
DELETE /api/admin/pricing/{id}
```

#### Features
```http
GET    /api/admin/features
GET    /api/admin/features/{id}
POST   /api/admin/features
PUT    /api/admin/features/{id}
DELETE /api/admin/features/{id}
```

#### Testimonials
```http
GET    /api/admin/testimonials
GET    /api/admin/testimonials/{id}
POST   /api/admin/testimonials
PUT    /api/admin/testimonials/{id}
DELETE /api/admin/testimonials/{id}
```

#### FAQ
```http
GET    /api/admin/faq
GET    /api/admin/faq/{id}
POST   /api/admin/faq
PUT    /api/admin/faq/{id}
DELETE /api/admin/faq/{id}
```

#### Settings
```http
GET    /api/admin/settings
PUT    /api/admin/settings
```

#### Media Upload
```http
POST   /api/admin/media-upload
Content-Type: multipart/form-data

file: [binary]
alt_text: "Image description"
title: "Image title"
```

#### Leads Management
```http
GET    /api/admin/leads
GET    /api/admin/leads/{id}
PUT    /api/admin/leads/{id}
DELETE /api/admin/leads/{id}
GET    /api/admin/leads/export (CSV export)
```

## Project Structure

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── ContentController.php
│   │   │   ├── LeadController.php
│   │   │   └── Admin/
│   │   │       ├── ServiceController.php
│   │   │       ├── PricingController.php
│   │   │       ├── FeatureController.php
│   │   │       ├── TestimonialController.php
│   │   │       ├── FaqController.php
│   │   │       ├── SettingController.php
│   │   │       ├── MediaController.php
│   │   │       └── LeadController.php
│   │   ├── Middleware/
│   │   │   └── JwtMiddleware.php
│   │   └── Requests/
│   │       ├── LeadRequest.php
│   │       └── Admin/
│   ├── Models/
│   │   ├── Admin.php
│   │   ├── Service.php
│   │   ├── PricingPlan.php
│   │   ├── Feature.php
│   │   ├── Testimonial.php
│   │   ├── Faq.php
│   │   ├── Lead.php
│   │   ├── Setting.php
│   │   └── Media.php
│   └── Services/
│       └── FileUploadService.php
├── config/
│   ├── cors.php
│   └── jwt.php
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
└── storage/
    └── app/
        └── public/
            └── uploads/
```

## Configuration

### CORS

Edit `config/cors.php` to configure allowed origins:

```php
'allowed_origins' => [
    'http://localhost:4200',
    'http://localhost:4201',
    'https://ashub.com',
],
```

### JWT

JWT configuration in `config/jwt.php`:

```php
'ttl' => env('JWT_TTL', 60), // Token lifetime in minutes
'refresh_ttl' => env('JWT_REFRESH_TTL', 20160), // Refresh token lifetime
```

### File Uploads

Maximum file size: 10MB (configurable in `php.ini`)

Allowed types: jpg, jpeg, png, gif, svg, pdf, doc, docx

Upload path: `storage/app/public/uploads/`

## Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=AuthTest
```

## Deployment

### Production Optimization

```bash
# Install production dependencies
composer install --optimize-autoloader --no-dev

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chmod -R 755 storage bootstrap/cache
```

### Environment Variables

Update `.env` for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.ashub.com

DB_HOST=your_production_host
DB_DATABASE=u643694170_Abood
DB_USERNAME=u643694170_Abood
DB_PASSWORD=your_secure_password

JWT_SECRET=your_secure_jwt_secret
```

## Security

- All passwords are hashed using bcrypt
- JWT tokens expire after 60 minutes
- CORS is configured for specific origins
- Input validation on all endpoints
- SQL injection protection via Eloquent ORM
- XSS protection enabled
- CSRF protection for web routes

## Troubleshooting

### Storage Permission Issues
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### JWT Secret Not Set
```bash
php artisan jwt:secret
```

### Database Connection Issues
- Verify database credentials in `.env`
- Ensure MySQL service is running
- Check database exists: `CREATE DATABASE u643694170_Abood;`

## Support

For issues or questions, contact: support@ashub.com

## License

Proprietary - AS Hub © 2024
