<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Service;
use Illuminate\Support\Facades\DB;

echo "=== Testing Services ===\n\n";

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    DB::connection()->getPdo();
    echo "✅ Database connected successfully!\n\n";

    // Check if services table exists
    echo "2. Checking if services table exists...\n";
    $driver = DB::connection()->getDriverName();
    echo "Database driver: $driver\n";
    
    if ($driver === 'sqlite') {
        $tableExists = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name='services'");
    } else {
        $tableExists = DB::select("SHOW TABLES LIKE 'services'");
    }
    
    if (empty($tableExists)) {
        echo "❌ Services table does NOT exist!\n";
        echo "Creating services table...\n";
        
        if ($driver === 'sqlite') {
            DB::statement("
                CREATE TABLE IF NOT EXISTS services (
                  id INTEGER PRIMARY KEY AUTOINCREMENT,
                  language TEXT NOT NULL DEFAULT 'en',
                  title TEXT NOT NULL,
                  slug TEXT NOT NULL,
                  icon TEXT NULL,
                  description TEXT NULL,
                  features TEXT NULL,
                  `order` INTEGER DEFAULT 0,
                  is_active INTEGER DEFAULT 1,
                  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            DB::statement("CREATE INDEX IF NOT EXISTS idx_language ON services(language)");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_slug ON services(slug)");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_is_active ON services(is_active)");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_order ON services(`order`)");
        } else {
            DB::statement("
                CREATE TABLE IF NOT EXISTS `services` (
                  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                  `language` ENUM('en', 'ar') NOT NULL DEFAULT 'en',
                  `title` VARCHAR(255) NOT NULL,
                  `slug` VARCHAR(255) NOT NULL,
                  `icon` VARCHAR(255) NULL,
                  `description` TEXT NULL,
                  `features` JSON NULL,
                  `order` INT DEFAULT 0,
                  `is_active` TINYINT(1) DEFAULT 1,
                  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  INDEX `idx_language` (`language`),
                  INDEX `idx_slug` (`slug`),
                  INDEX `idx_is_active` (`is_active`),
                  INDEX `idx_order` (`order`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        }
        
        echo "✅ Services table created!\n\n";
    } else {
        echo "✅ Services table exists!\n\n";
    }

    // Count services
    echo "3. Counting services...\n";
    $count = Service::count();
    echo "Total services: $count\n\n";

    // If no services, add sample data
    if ($count == 0) {
        echo "4. Adding sample services...\n";
        
        $sampleServices = [
            [
                'language' => 'en',
                'title' => 'Website Development',
                'slug' => 'website-development',
                'icon' => '🌐',
                'description' => 'Professional website development services',
                'features' => json_encode(['Responsive Design', 'SEO Optimized', 'Fast Loading']),
                'order' => 1,
                'is_active' => true
            ],
            [
                'language' => 'en',
                'title' => 'Mobile App Development',
                'slug' => 'mobile-app-development',
                'icon' => '📱',
                'description' => 'Native and cross-platform mobile apps',
                'features' => json_encode(['iOS & Android', 'User Friendly', 'Secure']),
                'order' => 2,
                'is_active' => true
            ],
            [
                'language' => 'ar',
                'title' => 'تطوير المواقع',
                'slug' => 'website-development-ar',
                'icon' => '🌐',
                'description' => 'خدمات تطوير مواقع احترافية',
                'features' => json_encode(['تصميم متجاوب', 'محسّن لمحركات البحث', 'سريع التحميل']),
                'order' => 1,
                'is_active' => true
            ]
        ];

        foreach ($sampleServices as $service) {
            Service::create($service);
        }
        
        echo "✅ Added " . count($sampleServices) . " sample services!\n\n";
    }

    // List all services
    echo "5. Listing all services:\n";
    $services = Service::orderBy('order')->get();
    foreach ($services as $service) {
        echo "  - [{$service->language}] {$service->title} (Order: {$service->order})\n";
    }
    echo "\n";

    echo "✅ All tests passed!\n";
    echo "\nYou can now access services at: http://localhost:8000/api/admin/services\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
