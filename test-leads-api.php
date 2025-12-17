<?php

require __DIR__.'/backend/vendor/autoload.php';

$app = require_once __DIR__.'/backend/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Lead;

echo "=== Testing Leads API ===\n\n";

// Check if leads exist
$totalLeads = Lead::count();
echo "Total leads in database: $totalLeads\n\n";

if ($totalLeads > 0) {
    echo "Leads found:\n";
    echo "-------------------\n";
    
    $leads = Lead::orderBy('created_at', 'desc')->get();
    
    foreach ($leads as $lead) {
        echo "ID: {$lead->id}\n";
        echo "Name: {$lead->name}\n";
        echo "Email: {$lead->email}\n";
        echo "Status: {$lead->status}\n";
        echo "Processed: " . ($lead->is_processed ? 'Yes' : 'No') . "\n";
        echo "Created: {$lead->created_at}\n";
        echo "-------------------\n";
    }
    
    echo "\n✓ Leads are in the database!\n";
    echo "\nNow testing API response format...\n\n";
    
    // Simulate API response
    $response = [
        'success' => true,
        'data' => [
            'data' => $leads->toArray(),
            'total' => $totalLeads
        ]
    ];
    
    echo "API Response Structure:\n";
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} else {
    echo "✗ No leads found in database!\n";
    echo "Run: php backend/add-sample-leads.php\n";
}
