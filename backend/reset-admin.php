<?php

echo "=== Reset Admin Account ===\n\n";

$dbPath = __DIR__ . '/database/database.sqlite';

if (!file_exists($dbPath)) {
    echo "❌ Database file not found!\n";
    exit(1);
}

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to database\n\n";
    
    // 1. Delete all existing admins
    echo "1. Deleting old admin accounts...\n";
    $deleted = $pdo->exec("DELETE FROM admins");
    echo "   ✅ Deleted $deleted admin account(s)\n\n";
    
    // 2. Create new admin
    echo "2. Creating new admin account...\n";
    
    $name = "AS Hub Admin";
    $email = "admin@ashub.com";
    $password = "Admin@123456";
    
    // Hash password using bcrypt
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    $stmt = $pdo->prepare("
        INSERT INTO admins (name, email, password, is_active, created_at, updated_at)
        VALUES (?, ?, ?, 1, datetime('now'), datetime('now'))
    ");
    
    $stmt->execute([$name, $email, $hashedPassword]);
    
    echo "   ✅ New admin account created!\n\n";
    
    // 3. Verify
    echo "3. Verifying new admin...\n";
    $admin = $pdo->query("SELECT * FROM admins WHERE email = '$email'")->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "   ✅ Admin verified!\n";
        echo "   ID: {$admin['id']}\n";
        echo "   Name: {$admin['name']}\n";
        echo "   Email: {$admin['email']}\n";
        echo "   Active: " . ($admin['is_active'] ? 'Yes' : 'No') . "\n";
    } else {
        echo "   ❌ Admin not found!\n";
        exit(1);
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "✅ ADMIN RESET COMPLETED!\n";
    echo str_repeat("=", 50) . "\n\n";
    
    echo "🔐 New Admin Credentials:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Email:    $email\n";
    echo "Password: $password\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "📝 Save these credentials!\n";
    echo "🚀 You can now login to the admin panel\n\n";
    
    // Test password verification
    echo "4. Testing password verification...\n";
    if (password_verify($password, $hashedPassword)) {
        echo "   ✅ Password verification works!\n";
    } else {
        echo "   ❌ Password verification failed!\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    exit(1);
}
