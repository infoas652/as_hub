<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Lead;

echo "Adding sample leads...\n";

$leads = [
    [
        'name' => 'John Smith',
        'email' => 'john.smith@example.com',
        'phone' => '+1 (555) 123-4567',
        'company' => 'Tech Solutions Inc',
        'message' => 'Interested in your web development services for our new project.',
        'plan' => 'professional',
        'status' => 'new',
        'source' => 'website',
        'is_processed' => false,
    ],
    [
        'name' => 'Sarah Johnson',
        'email' => 'sarah.j@company.com',
        'phone' => '+1 (555) 234-5678',
        'company' => 'Digital Marketing Co',
        'message' => 'Need a mobile app for our business. Can you help?',
        'plan' => 'enterprise',
        'status' => 'contacted',
        'source' => 'website',
        'is_processed' => false,
    ],
    [
        'name' => 'Michael Brown',
        'email' => 'mbrown@startup.io',
        'phone' => '+1 (555) 345-6789',
        'company' => 'StartupXYZ',
        'message' => 'Looking for e-commerce solution with payment integration.',
        'plan' => 'basic',
        'status' => 'new',
        'source' => 'website',
        'is_processed' => false,
    ],
    [
        'name' => 'Emily Davis',
        'email' => 'emily.davis@business.com',
        'phone' => '+1 (555) 456-7890',
        'company' => 'Business Solutions Ltd',
        'message' => 'Interested in management system for our operations.',
        'plan' => 'professional',
        'status' => 'qualified',
        'source' => 'website',
        'is_processed' => true,
        'processed_at' => now(),
    ],
    [
        'name' => 'Ahmed Ali',
        'email' => 'ahmed@company.sa',
        'phone' => '+966 50 123 4567',
        'company' => 'Saudi Tech',
        'message' => 'نحتاج موقع إلكتروني احترافي لشركتنا',
        'plan' => 'enterprise',
        'status' => 'new',
        'source' => 'website',
        'is_processed' => false,
    ],
];

foreach ($leads as $leadData) {
    try {
        Lead::create($leadData);
        echo "✓ Added lead: {$leadData['name']}\n";
    } catch (Exception $e) {
        echo "✗ Error adding {$leadData['name']}: {$e->getMessage()}\n";
    }
}

$total = Lead::count();
echo "\n✓ Total leads in database: $total\n";
echo "Done!\n";
