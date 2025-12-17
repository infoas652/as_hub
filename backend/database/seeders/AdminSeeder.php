<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin if not exists
        Admin::firstOrCreate(
            ['email' => 'admin@ashub.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@123456'),
                'is_active' => true,
            ]
        );

        // Create AS Hub admin
        Admin::firstOrCreate(
            ['email' => 'info@as-hub.com'],
            [
                'name' => 'Abood',
                'password' => Hash::make('Abood!0595466383'),
                'is_active' => true,
            ]
        );

        $this->command->info('Admins created successfully!');
    }
}
