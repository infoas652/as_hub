<?php

// AS Hub - MySQL Configuration Setup
// This script will configure your backend to use MySQL on Hostinger

$envFile = __DIR__ . '/.env';
$envExampleFile = __DIR__ . '/.env.example';

echo "=== AS Hub MySQL Configuration ===\n\n";

// Database credentials
$dbHost = '127.0.0.1';
$dbPort = '3306';
$dbDatabase = 'u643694170_Abood';
$dbUsername = 'u643694170_Abood';
$dbPassword = "Abood@0595466383";

echo "Configuring MySQL connection...\n";
echo "Database: {$dbDatabase}\n";
echo "Username: {$dbUsername}\n\n";

// Read .env.example or create new .env
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    echo "✅ Found existing .env file\n";
} elseif (file_exists($envExampleFile)) {
    $envContent = file_get_contents($envExampleFile);
    echo "✅ Copied from .env.example\n";
} else {
    echo "❌ No .env file found!\n";
    exit(1);
}

// Update database configuration
$envContent = preg_replace('/DB_CONNECTION=.*/','DB_CONNECTION=mysql', $envContent);
$envContent = preg_replace('/DB_HOST=.*/','DB_HOST=' . $dbHost, $envContent);
$envContent = preg_replace('/DB_PORT=.*/','DB_PORT=' . $dbPort, $envContent);
$envContent = preg_replace('/DB_DATABASE=.*/','DB_DATABASE=' . $dbDatabase, $envContent);
$envContent = preg_replace('/DB_USERNAME=.*/','DB_USERNAME=' . $dbUsername, $envContent);
$envContent = preg_replace('/DB_PASSWORD=.*/','DB_PASSWORD="' . $dbPassword . '"', $envContent);

// Write updated .env
file_put_contents($envFile, $envContent);
echo "✅ Updated .env file with MySQL configuration\n\n";

// Test database connection
echo "Testing database connection...\n";
try {
    $pdo = new PDO(
        "mysql:host={$dbHost};port={$dbPort};dbname={$dbDatabase}",
        $dbUsername,
        $dbPassword,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✅ Database connection successful!\n\n";
    
    // Check if admin exists
    $stmt = $pdo->query("SELECT * FROM admins WHERE email = 'info@as-hub.com'");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "✅ Admin user found in database!\n";
        echo "   ID: {$admin['id']}\n";
        echo "   Name: {$admin['name']}\n";
        echo "   Email: {$admin['email']}\n\n";
    } else {
        echo "⚠️  Admin user not found in database\n";
        echo "   Creating admin user...\n\n";
        
        // Create admin
        $password = password_hash("AboodGit commit '0595466383'", PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("
            INSERT INTO admins (name, email, password, is_active, created_at, updated_at) 
            VALUES (?, ?, ?, 1, NOW(), NOW())
        ");
        $stmt->execute(['AS Hub Admin', 'info@as-hub.com', $password]);
        
        echo "✅ Admin user created successfully!\n\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

echo "=== Configuration Complete ===\n\n";
echo "Next steps:\n";
echo "1. Clear Laravel cache: php artisan config:clear\n";
echo "2. Start backend server: php artisan serve\n";
echo "3. Start admin panel: cd ../admin-panel && ng serve --port 4202\n\n";
echo "Login credentials:\n";
echo "Email: info@as-hub.com\n";
echo "Password: AboodGit commit '0595466383'\n";
