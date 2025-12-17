<?php

echo "=== Testing Login API ===\n\n";

$apiUrl = 'http://localhost:8000/api/auth/login';
$credentials = [
    'email' => 'admin@ashub.com',
    'password' => 'Admin@123456'
];

echo "Testing API endpoint: $apiUrl\n";
echo "Credentials:\n";
echo "  Email: " . $credentials['email'] . "\n";
echo "  Password: " . $credentials['password'] . "\n\n";

// Initialize cURL
$ch = curl_init($apiUrl);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($credentials));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "=================================\n";
echo "API Response:\n";
echo "=================================\n";
echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "❌ cURL Error: $error\n";
    echo "\n⚠️ Make sure backend is running:\n";
    echo "   cd backend && php artisan serve --port=8000\n";
} else {
    echo "Response Body:\n";
    $responseData = json_decode($response, true);
    
    if ($httpCode == 200) {
        echo "✓ Login Successful!\n\n";
        echo "Access Token: " . substr($responseData['access_token'], 0, 50) . "...\n";
        echo "Token Type: " . $responseData['token_type'] . "\n";
        echo "Expires In: " . $responseData['expires_in'] . " seconds\n";
    } else {
        echo "❌ Login Failed!\n\n";
        echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
    }
}

echo "=================================\n";
