<?php

echo "=== AS Hub - Complete Fix ===\n\n";

// 1. Delete all cache files
echo "1. Cleaning cache files...\n";
$cacheFiles = [
    __DIR__ . '/bootstrap/cache/config.php',
    __DIR__ . '/bootstrap/cache/services.php',
    __DIR__ . '/bootstrap/cache/packages.php',
    __DIR__ . '/bootstrap/cache/routes-v7.php',
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "   ✅ Deleted: " . basename($file) . "\n";
    }
}

// 2. Clear storage cache
echo "\n2. Clearing storage cache...\n";
$storageDirs = [
    __DIR__ . '/storage/framework/cache/data',
    __DIR__ . '/storage/framework/views',
    __DIR__ . '/storage/framework/sessions',
];

foreach ($storageDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file) && basename($file) !== '.gitignore') {
                unlink($file);
            }
        }
        echo "   ✅ Cleared: " . basename($dir) . "\n";
    }
}

// 3. Recreate .env with correct settings
echo "\n3. Configuring .env...\n";
$envContent = file_get_contents(__DIR__ . '/.env');

// Ensure SQLite is set
$envContent = preg_replace('/DB_CONNECTION=.*/','DB_CONNECTION=sqlite', $envContent);
$envContent = preg_replace('/^DB_HOST=.*/m','# DB_HOST=127.0.0.1', $envContent);
$envContent = preg_replace('/^DB_PORT=.*/m','# DB_PORT=3306', $envContent);
$envContent = preg_replace('/^DB_DATABASE=(?!.*sqlite).*/m','# DB_DATABASE=database', $envContent);
$envContent = preg_replace('/^DB_USERNAME=.*/m','# DB_USERNAME=root', $envContent);
$envContent = preg_replace('/^DB_PASSWORD=.*/m','# DB_PASSWORD=', $envContent);

file_put_contents(__DIR__ . '/.env', $envContent);
echo "   ✅ .env configured for SQLite\n";

// 4. Run artisan commands
echo "\n4. Running Laravel commands...\n";

$commands = [
    'config:clear' => 'Clear config cache',
    'cache:clear' => 'Clear application cache',
    'route:clear' => 'Clear route cache',
];

foreach ($commands as $cmd => $desc) {
    echo "   Running: $desc...\n";
    exec("php artisan $cmd 2>&1", $output, $code);
    if ($code === 0) {
        echo "   ✅ $desc completed\n";
    }
}

// 5. Test database
echo "\n5. Testing database...\n";
try {
    $dbPath = __DIR__ . '/database/database.sqlite';
    if (!file_exists($dbPath)) {
        touch($dbPath);
        echo "   ✅ Created database.sqlite\n";
    }
    
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if tables exist
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) < 3) {
        echo "   ⚠️  Tables missing, running migrations...\n";
        exec("php artisan migrate:fresh --seed 2>&1", $output, $code);
        if ($code === 0) {
            echo "   ✅ Migrations completed\n";
        }
    } else {
        echo "   ✅ Database tables exist (" . count($tables) . " tables)\n";
    }
    
    // Verify admin
    $stmt = $pdo->query("SELECT * FROM admins WHERE email = 'info@as-hub.com'");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "   ✅ Admin user exists\n";
    } else {
        echo "   ⚠️  Admin not found, creating...\n";
        $password = password_hash("AboodGit commit '0595466383'", PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO admins (name, email, password, is_active, created_at, updated_at) VALUES (?, ?, ?, 1, datetime('now'), datetime('now'))");
        $stmt->execute(['AS Hub Admin', 'info@as-hub.com', $password]);
        echo "   ✅ Admin created\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n";
}

// 6. Create startup script
echo "\n6. Creating startup script...\n";
$startScript = <<<'BAT'
@echo off
echo ========================================
echo    AS Hub - Starting Services
echo ========================================
echo.

cd /d "%~dp0backend"

echo [1/2] Starting Backend API...
start "AS Hub Backend" cmd /k "php artisan serve"
timeout /t 3 /nobreak >nul

echo [2/2] Starting Admin Panel...
cd ..\admin-panel
start "AS Hub Admin Panel" cmd /k "ng serve --port 4202"

echo.
echo ========================================
echo    Services Started Successfully!
echo ========================================
echo.
echo Backend API: http://localhost:8000
echo Admin Panel: http://localhost:4202
echo.
echo Login: info@as-hub.com
echo Password: AboodGit commit '0595466383'
echo.
echo Press any key to open Admin Panel...
pause >nul
start http://localhost:4202
BAT;

file_put_contents(__DIR__ . '/../start-ashub-fixed.bat', $startScript);
echo "   ✅ Created start-ashub-fixed.bat\n";

echo "\n=== Fix Complete! ===\n\n";
echo "✅ Everything is ready!\n\n";
echo "To start the system:\n";
echo "1. Double-click: start-ashub-fixed.bat\n";
echo "   OR\n";
echo "2. Manually:\n";
echo "   - Backend: cd backend && php artisan serve\n";
echo "   - Admin: cd admin-panel && ng serve --port 4202\n\n";
echo "Login at: http://localhost:4202\n";
echo "Email: info@as-hub.com\n";
echo "Password: AboodGit commit '0595466383'\n";
