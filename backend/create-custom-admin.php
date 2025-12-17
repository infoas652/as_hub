<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

echo "=== Creating Custom Admin Account ===\n\n";

// Delete ALL old admins
$deletedCount = Admin::count();
Admin::truncate();
echo "✓ Deleted $deletedCount old admin account(s)\n\n";

// Create new admin with custom password
$newAdmin = Admin::create([
    'name' => 'AS Hub Admin',
    'email' => 'admin@ashub.com',
    'password' => Hash::make("AdminGit commit '1234567'"),
    'is_active' => true,
]);

echo "✓ New admin account created successfully!\n\n";
echo "=================================\n";
echo "Admin Credentials:\n";
echo "=================================\n";
echo "Email: admin@ashub.com\n";
echo "Password: AdminGit commit '1234567'\n";
echo "=================================\n\n";
echo "✓ You can now login with these credentials!\n";
echo "\nNote: Copy the password exactly as shown above.\n";
