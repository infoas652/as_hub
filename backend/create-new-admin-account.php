<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

echo "=== Creating New Admin Account ===\n\n";

// Delete old admin
$oldAdmin = Admin::where('email', 'admin@ashub.com')->first();
if ($oldAdmin) {
    $oldAdmin->delete();
    echo "✓ Old admin account deleted\n\n";
}

// Create new admin
$newAdmin = Admin::create([
    'name' => 'AS Hub Admin',
    'email' => 'admin@ashub.com',
    'password' => Hash::make('Admin@123456'),
    'is_active' => true,
]);

echo "✓ New admin account created successfully!\n\n";
echo "=================================\n";
echo "Admin Credentials:\n";
echo "=================================\n";
echo "Email: admin@ashub.com\n";
echo "Password: Admin@123456\n";
echo "=================================\n\n";
echo "✓ You can now login with these credentials!\n";
