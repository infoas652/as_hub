# AS Hub Database Schema

## Database Information

- **Database Name**: u643694170_Abood
- **Character Set**: utf8mb4
- **Collation**: utf8mb4_unicode_ci

## Tables Overview

### 1. admins
Stores admin user credentials and information.

### 2. services
Stores service offerings (Website Development, Mobile Apps, etc.)

### 3. pricing_plans
Stores pricing tiers with monthly/yearly options.

### 4. features
Stores platform features/benefits.

### 5. testimonials
Stores client testimonials and reviews.

### 6. faq
Stores frequently asked questions.

### 7. leads
Stores contact form submissions.

### 8. settings
Stores site-wide settings (contact info, social links, etc.)

### 9. media
Stores uploaded media files metadata.

## Schema Details

See `schema.sql` for complete table structures.

## Migrations

Laravel migrations are located in `backend/database/migrations/`

## Seeders

Sample data seeders are in `backend/database/seeders/`

## Setup Instructions

```bash
# Create database
mysql -u u643694170_Abood -p
CREATE DATABASE u643694170_Abood CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE u643694170_Abood;

# Import schema (if using raw SQL)
source schema.sql;

# OR use Laravel migrations
cd ../backend
php artisan migrate --seed
