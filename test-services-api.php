<?php

echo "=== Testing Services API ===\n\n";

// Test 1: Check if backend is running
echo "1. Testing if backend is running...\n";
$ch = curl_init('http://localhost:8000/api/health');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo "✅ Backend is running!\n";
    echo "Response: $response\n\n";
} else {
    echo "❌ Backend is NOT running! (HTTP $httpCode)\n";
    echo "Please start backend: cd backend && php artisan serve\n\n";
    exit(1);
}

// Test 2: Get services without auth (should work or return 401)
echo "2. Testing GET /api/admin/services (without auth)...\n";
$ch = curl_init('http://localhost:8000/api/admin/services');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: " . substr($response, 0, 200) . "...\n\n";

if ($httpCode == 200) {
    $data = json_decode($response, true);
    if (isset($data['data'])) {
        echo "✅ Services loaded successfully!\n";
        echo "Total services: " . count($data['data']) . "\n\n";
        
        echo "Services list:\n";
        foreach ($data['data'] as $service) {
            echo "  - [{$service['language']}] {$service['title']}\n";
        }
        echo "\n";
    }
} elseif ($httpCode == 401) {
    echo "⚠️ Authentication required (this is normal)\n\n";
} else {
    echo "❌ Error loading services\n\n";
}

echo "✅ API test completed!\n";
echo "\nNext steps:\n";
echo "1. Make sure backend is running: cd backend && php artisan serve\n";
echo "2. Make sure admin panel is running: cd admin-panel && ng serve --port 4201\n";
echo "3. Login to admin panel: http://localhost:4201\n";
echo "4. Navigate to Services page\n";
