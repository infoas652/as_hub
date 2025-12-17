<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

echo "=== Resetting to Default Admin Account ===\n\n";

// Delete ALL old admins
$deletedCount = Admin::count();
Admin::truncate();
echo "✓ Deleted $deletedCount old admin account(s)\n\n";

// Create default admin
$newAdmin = Admin::create([
    'name' => 'AS Hub Admin',
    'email' => 'admin@ashub.com',
    'password' => Hash::make('Admin@123456'),
    'is_active' => true,
]);

echo "✓ Default admin account created successfully!\n\n";
echo "=================================\n";
echo "Default Admin Credentials:\n";
echo "=================================\n";
echo "Email: admin@ashub.com\n";
echo "Password: Admin@123456\n";
echo "=================================\n\n";
echo "✓ You can now login with the default credentials!\n";
