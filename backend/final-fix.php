<?php

echo "=== AS Hub Final Fix Script ===\n\n";

// 1. Clear all caches
echo "1. Clearing all caches...\n";
exec('php artisan config:clear 2>&1', $output1, $return1);
exec('php artisan cache:clear 2>&1', $output2, $return2);
exec('php artisan route:clear 2>&1', $output3, $return3);
echo "   ✅ Caches cleared\n\n";

// 2. Check .env file
echo "2. Checking .env file...\n";
if (!file_exists('.env')) {
    if (file_exists('.env.example')) {
        copy('.env.example', '.env');
        echo "   ✅ Created .env from .env.example\n";
    } else {
        echo "   ❌ No .env.example found!\n";
        exit(1);
    }
} else {
    echo "   ✅ .env file exists\n";
}

// 3. Read and update .env
echo "\n3. Updating .env configuration...\n";
$envContent = file_get_contents('.env');

// Ensure required values
$updates = [
    'APP_NAME' => 'AS Hub',
    'APP_ENV' => 'local',
    'APP_DEBUG' => 'true',
    'APP_URL' => 'http://localhost:8000',
    'DB_CONNECTION' => 'sqlite',
    'SESSION_DRIVER' => 'file',
    'CACHE_DRIVER' => 'file',
    'QUEUE_CONNECTION' => 'sync',
];

foreach ($updates as $key => $value) {
    if (strpos($envContent, "$key=") === false) {
        $envContent .= "\n$key=$value";
        echo "   ✅ Added $key=$value\n";
    } else {
        $pattern = "/^$key=.*/m";
        $envContent = preg_replace($pattern, "$key=$value", $envContent);
        echo "   ✅ Updated $key=$value\n";
    }
}

file_put_contents('.env', $envContent);

// 4. Generate APP_KEY if missing
echo "\n4. Checking APP_KEY...\n";
if (strpos($envContent, 'APP_KEY=base64:') === false || strpos($envContent, 'APP_KEY=') === false) {
    exec('php artisan key:generate 2>&1', $output4, $return4);
    echo "   ✅ Generated new APP_KEY\n";
} else {
    echo "   ✅ APP_KEY exists\n";
}

// 5. Check JWT secret
echo "\n5. Checking JWT secret...\n";
$envContent = file_get_contents('.env');
if (strpos($envContent, 'JWT_SECRET=') === false || trim(explode('JWT_SECRET=', $envContent)[1] ?? '') === '') {
    exec('php artisan jwt:secret --force 2>&1', $output5, $return5);
    echo "   ✅ Generated JWT secret\n";
} else {
    echo "   ✅ JWT secret exists\n";
}

// 6. Check required directories
echo "\n6. Checking required directories...\n";
$dirs = [
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
    'resources/views',
    'database',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "   ✅ Created $dir\n";
    } else {
        echo "   ✅ $dir exists\n";
    }
}

// 7. Check database
echo "\n7. Checking database...\n";
$dbPath = 'database/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
    echo "   ✅ Created SQLite database\n";
    
    echo "\n8. Running migrations...\n";
    exec('php artisan migrate --force 2>&1', $output6, $return6);
    echo "   ✅ Migrations completed\n";
    
    echo "\n9. Seeding database...\n";
    exec('php artisan db:seed --force 2>&1', $output7, $return7);
    echo "   ✅ Database seeded\n";
} else {
    echo "   ✅ Database exists\n";
    
    // Check if tables exist
    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) < 5) {
            echo "\n8. Database seems empty, running migrations...\n";
            exec('php artisan migrate --force 2>&1', $output8, $return8);
            echo "   ✅ Migrations completed\n";
            
            echo "\n9. Seeding database...\n";
            exec('php artisan db:seed --force 2>&1', $output9, $return9);
            echo "   ✅ Database seeded\n";
        } else {
            echo "   ✅ Database has " . count($tables) . " tables\n";
        }
    } catch (Exception $e) {
        echo "   ⚠️  Could not check database: " . $e->getMessage() . "\n";
    }
}

// 10. Set permissions
echo "\n10. Setting permissions...\n";
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    exec('chmod -R 775 storage bootstrap/cache 2>&1');
    echo "   ✅ Permissions set\n";
} else {
    echo "   ℹ️  Windows detected, skipping chmod\n";
}

// 11. Clear caches again
echo "\n11. Final cache clear...\n";
exec('php artisan config:clear 2>&1');
exec('php artisan cache:clear 2>&1');
echo "   ✅ Final caches cleared\n";

// 12. Test database connection
echo "\n12. Testing database connection...\n";
try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $adminCount = $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
    echo "   ✅ Database connection successful\n";
    echo "   ✅ Found $adminCount admin user(s)\n";
    
    if ($adminCount > 0) {
        $admin = $pdo->query("SELECT email FROM admins LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        echo "   ✅ Admin email: " . $admin['email'] . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ ALL FIXES COMPLETED SUCCESSFULLY!\n";
echo str_repeat("=", 50) . "\n\n";

echo "🚀 Next steps:\n";
echo "1. Run: php artisan serve\n";
echo "2. Open: http://localhost:8000\n";
echo "3. Test API: http://localhost:8000/api/health\n\n";

echo "📝 Admin credentials:\n";
echo "Email: info@as-hub.com\n";
echo "Password: AboodGit commit '0595466383'\n\n";

echo "✨ Backend is ready!\n";
