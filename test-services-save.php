<?php

echo "=== Testing Services Save Functionality ===\n\n";

// Step 1: Login to get token
echo "1. Logging in...\n";
$loginData = json_encode([
    'email' => 'admin@ashub.com',
    'password' => 'Admin@123456'
]);

$ch = curl_init('http://localhost:8000/api/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode != 200) {
    echo "❌ Login failed! HTTP Code: $httpCode\n";
    echo "Response: $response\n";
    exit(1);
}

$loginResponse = json_decode($response, true);
$token = $loginResponse['access_token'] ?? null;

if (!$token) {
    echo "❌ No token received!\n";
    echo "Response: $response\n";
    exit(1);
}

echo "✅ Login successful! Token received.\n\n";

// Step 2: Test GET services
echo "2. Getting existing services...\n";
$ch = curl_init('http://localhost:8000/api/admin/services');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($httpCode == 200) {
    $data = json_decode($response, true);
    $count = count($data['data'] ?? []);
    echo "✅ Services loaded: $count services\n\n";
} else {
    echo "❌ Failed to load services\n";
    echo "Response: $response\n\n";
}

// Step 3: Test CREATE service
echo "3. Testing CREATE service...\n";

// Create multipart form data
$boundary = '----WebKitFormBoundary' . uniqid();
$postData = '';

// Add form fields
$fields = [
    'language' => 'en',
    'title' => 'Test Service ' . time(),
    'icon' => '🧪',
    'description' => 'This is a test service created by automated script',
    'features' => json_encode(['Feature 1', 'Feature 2', 'Feature 3']),
    'order' => '99',
    'is_active' => '1'
];

foreach ($fields as $name => $value) {
    $postData .= "--$boundary\r\n";
    $postData .= "Content-Disposition: form-data; name=\"$name\"\r\n\r\n";
    $postData .= "$value\r\n";
}

$postData .= "--$boundary--\r\n";

$ch = curl_init('http://localhost:8000/api/admin/services');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: multipart/form-data; boundary=' . $boundary,
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: " . substr($response, 0, 500) . "\n";

if ($httpCode == 201 || $httpCode == 200) {
    $data = json_decode($response, true);
    $serviceId = $data['data']['id'] ?? null;
    echo "✅ Service created successfully! ID: $serviceId\n\n";
    
    // Step 4: Test UPDATE service
    if ($serviceId) {
        echo "4. Testing UPDATE service...\n";
        
        $updateData = '';
        $updateFields = [
            '_method' => 'PUT',
            'language' => 'en',
            'title' => 'Updated Test Service ' . time(),
            'icon' => '✅',
            'description' => 'This service has been updated',
            'features' => json_encode(['Updated Feature 1', 'Updated Feature 2']),
            'order' => '98',
            'is_active' => '1'
        ];
        
        foreach ($updateFields as $name => $value) {
            $updateData .= "--$boundary\r\n";
            $updateData .= "Content-Disposition: form-data; name=\"$name\"\r\n\r\n";
            $updateData .= "$value\r\n";
        }
        
        $updateData .= "--$boundary--\r\n";
        
        $ch = curl_init("http://localhost:8000/api/admin/services/$serviceId");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $updateData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: multipart/form-data; boundary=' . $boundary,
            'Accept: application/json'
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "HTTP Code: $httpCode\n";
        echo "Response: " . substr($response, 0, 500) . "\n";
        
        if ($httpCode == 200) {
            echo "✅ Service updated successfully!\n\n";
        } else {
            echo "❌ Failed to update service\n\n";
        }
        
        // Step 5: Test TOGGLE status
        echo "5. Testing TOGGLE status...\n";
        $ch = curl_init("http://localhost:8000/api/admin/services/$serviceId/toggle");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "HTTP Code: $httpCode\n";
        if ($httpCode == 200) {
            echo "✅ Status toggled successfully!\n\n";
        } else {
            echo "❌ Failed to toggle status\n";
            echo "Response: $response\n\n";
        }
        
        // Step 6: Test DELETE service
        echo "6. Testing DELETE service...\n";
        $ch = curl_init("http://localhost:8000/api/admin/services/$serviceId");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Accept: application/json'
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "HTTP Code: $httpCode\n";
        if ($httpCode == 200) {
            echo "✅ Service deleted successfully!\n\n";
        } else {
            echo "❌ Failed to delete service\n";
            echo "Response: $response\n\n";
        }
    }
} else {
    echo "❌ Failed to create service\n";
    echo "Full Response: $response\n\n";
}

echo "=== Test Complete ===\n";
echo "\nSummary:\n";
echo "- Login: ✅\n";
echo "- GET Services: Check above\n";
echo "- CREATE Service: Check above\n";
echo "- UPDATE Service: Check above\n";
echo "- TOGGLE Status: Check above\n";
echo "- DELETE Service: Check above\n";
