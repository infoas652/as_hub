<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

echo "=== Creating New Admin ===\n\n";

$email = 'info@as-hub.com';
$password = "AboodGit commit '0595466383'";
$name = 'AS Hub Admin';

// Delete old admin if exists
echo "1. Checking for existing admin...\n";
$oldAdmin = Admin::where('email', $email)->first();
if ($oldAdmin) {
    echo "   Found existing admin, deleting...\n";
    $oldAdmin->delete();
}

// Also delete the old admin@ashub.com
$oldAdmin2 = Admin::where('email', 'admin@ashub.com')->first();
if ($oldAdmin2) {
    echo "   Deleting old admin@ashub.com...\n";
    $oldAdmin2->delete();
}

echo "✅ Old admins deleted\n\n";

// Create new admin
echo "2. Creating new admin...\n";
$admin = Admin::create([
    'name' => $name,
    'email' => $email,
    'password' => Hash::make($password),
    'is_active' => true,
]);

echo "✅ Admin created successfully!\n\n";

// Verify
echo "3. Verifying admin...\n";
$admin = Admin::where('email', $email)->first();

if (!$admin) {
    echo "❌ Admin not found!\n";
    exit(1);
}

echo "✅ Admin found:\n";
echo "   ID: {$admin->id}\n";
echo "   Name: {$admin->name}\n";
echo "   Email: {$admin->email}\n";
echo "   Active: " . ($admin->is_active ? 'Yes' : 'No') . "\n\n";

// Test password
echo "4. Testing password...\n";
if (Hash::check($password, $admin->password)) {
    echo "✅ Password verification: SUCCESS\n\n";
} else {
    echo "❌ Password verification: FAILED\n\n";
    exit(1);
}

// Test authentication
echo "5. Testing authentication...\n";
$credentials = [
    'email' => $email,
    'password' => $password
];

try {
    if ($token = auth('api')->attempt($credentials)) {
        echo "✅ Authentication: SUCCESS\n";
        echo "   JWT Token: " . substr($token, 0, 50) . "...\n\n";
    } else {
        echo "❌ Authentication: FAILED\n\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Authentication Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

echo "=== SUCCESS ===\n";
echo "🎉 Admin created and tested successfully!\n\n";
echo "Login Credentials:\n";
echo "Email: {$email}\n";
echo "Password: {$password}\n";
