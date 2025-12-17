<?php

require __DIR__.'/backend/vendor/autoload.php';

$app = require_once __DIR__.'/backend/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

echo "=== Testing Admin Login ===\n\n";

// Check if admin exists
$admin = Admin::where('email', 'admin@ashub.com')->first();

if (!$admin) {
    echo "❌ Admin not found!\n";
    echo "Creating new admin...\n\n";
    
    $admin = Admin::create([
        'name' => 'AS Hub Admin',
        'email' => 'admin@ashub.com',
        'password' => Hash::make('Admin@123456'),
        'is_active' => true,
    ]);
    
    echo "✓ Admin created!\n\n";
}

echo "Admin Details:\n";
echo "=================================\n";
echo "ID: " . $admin->id . "\n";
echo "Name: " . $admin->name . "\n";
echo "Email: " . $admin->email . "\n";
echo "Is Active: " . ($admin->is_active ? 'Yes' : 'No') . "\n";
echo "=================================\n\n";

// Test password
$testPassword = 'Admin@123456';
$passwordMatch = Hash::check($testPassword, $admin->password);

echo "Password Test:\n";
echo "=================================\n";
echo "Testing password: $testPassword\n";
echo "Result: " . ($passwordMatch ? "✓ CORRECT" : "❌ WRONG") . "\n";
echo "=================================\n\n";

if ($passwordMatch) {
    echo "✓ Login credentials are correct!\n";
    echo "\nYou can login with:\n";
    echo "Email: admin@ashub.com\n";
    echo "Password: Admin@123456\n";
} else {
    echo "❌ Password doesn't match!\n";
    echo "Resetting password...\n\n";
    
    $admin->password = Hash::make('Admin@123456');
    $admin->save();
    
    echo "✓ Password reset successfully!\n";
    echo "\nTry login again with:\n";
    echo "Email: admin@ashub.com\n";
    echo "Password: Admin@123456\n";
}

// Test JWT configuration
echo "\n=================================\n";
echo "JWT Configuration:\n";
echo "=================================\n";
echo "JWT_SECRET exists: " . (config('jwt.secret') ? 'Yes' : 'No') . "\n";
echo "JWT_TTL: " . config('jwt.ttl') . " minutes\n";
echo "=================================\n";
