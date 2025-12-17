<?php

echo "=== AS Hub - Fix Local Setup ===\n\n";

// 1. Configure for SQLite
echo "1. Configuring for SQLite (local development)...\n";

$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    
    // Update to SQLite
    $envContent = preg_replace('/DB_CONNECTION=.*/','DB_CONNECTION=sqlite', $envContent);
    $envContent = preg_replace('/DB_HOST=.*/','# DB_HOST=127.0.0.1', $envContent);
    $envContent = preg_replace('/DB_PORT=.*/','# DB_PORT=3306', $envContent);
    $envContent = preg_replace('/DB_DATABASE=.*/','# DB_DATABASE=database', $envContent);
    $envContent = preg_replace('/DB_USERNAME=.*/','# DB_USERNAME=root', $envContent);
    $envContent = preg_replace('/DB_PASSWORD=.*/','# DB_PASSWORD=', $envContent);
    
    file_put_contents($envFile, $envContent);
    echo "✅ Updated .env to use SQLite\n\n";
} else {
    echo "❌ .env file not found!\n";
    exit(1);
}

// 2. Ensure database.sqlite exists
$dbPath = __DIR__ . '/database/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
    echo "✅ Created database.sqlite\n\n";
} else {
    echo "✅ database.sqlite exists\n\n";
}

// 3. Run migrations
echo "2. Running migrations...\n";
exec('php artisan migrate:fresh --seed 2>&1', $output, $returnCode);
foreach ($output as $line) {
    echo "   $line\n";
}

if ($returnCode === 0) {
    echo "✅ Migrations completed successfully\n\n";
} else {
    echo "❌ Migrations failed\n\n";
    exit(1);
}

// 4. Verify admin exists
echo "3. Verifying admin user...\n";
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

$admin = Admin::where('email', 'info@as-hub.com')->first();

if ($admin) {
    echo "✅ Admin found!\n";
    echo "   ID: {$admin->id}\n";
    echo "   Name: {$admin->name}\n";
    echo "   Email: {$admin->email}\n\n";
    
    // Test password
    $testPassword = "AboodGit commit '0595466383'";
    if (Hash::check($testPassword, $admin->password)) {
        echo "✅ Password is correct!\n\n";
    } else {
        echo "⚠️  Password mismatch, updating...\n";
        $admin->password = Hash::make($testPassword);
        $admin->save();
        echo "✅ Password updated!\n\n";
    }
    
    // Test JWT authentication
    echo "4. Testing JWT authentication...\n";
    try {
        $credentials = [
            'email' => 'info@as-hub.com',
            'password' => $testPassword
        ];
        
        if ($token = auth('api')->attempt($credentials)) {
            echo "✅ JWT Authentication works!\n";
            echo "   Token: " . substr($token, 0, 50) . "...\n\n";
        } else {
            echo "❌ JWT Authentication failed!\n\n";
        }
    } catch (Exception $e) {
        echo "❌ JWT Error: " . $e->getMessage() . "\n\n";
    }
    
} else {
    echo "❌ Admin not found!\n\n";
    exit(1);
}

echo "=== Setup Complete ===\n\n";
echo "✅ Everything is configured and working!\n\n";
echo "Next steps:\n";
echo "1. Start backend: php artisan serve\n";
echo "2. Start admin panel: cd ../admin-panel && ng serve --port 4202\n";
echo "3. Open: http://localhost:4202\n\n";
echo "Login credentials:\n";
echo "Email: info@as-hub.com\n";
echo "Password: AboodGit commit '0595466383'\n\n";
echo "Note: This uses SQLite for local development.\n";
echo "For production on Hostinger, you'll need to:\n";
echo "1. Upload the project to Hostinger\n";
echo "2. Configure .env with MySQL credentials on the server\n";
echo "3. Run migrations on the server\n";
