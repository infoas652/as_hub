<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

echo "Resetting admin password...\n";

$admin = Admin::where('email', 'admin@ashub.com')->first();

if ($admin) {
    $admin->password = Hash::make('Admin@123');
    $admin->save();
    
    echo "✅ Password reset successfully!\n";
    echo "Email: admin@ashub.com\n";
    echo "Password: Admin@123\n";
    
    // Test the password
    if (Hash::check('Admin@123', $admin->password)) {
        echo "✅ Password verification: SUCCESS\n";
    } else {
        echo "❌ Password verification: FAILED\n";
    }
} else {
    echo "❌ Admin user not found!\n";
}
