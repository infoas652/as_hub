<?php

echo "=== Fixing .env file ===\n\n";

$envPath = '.env';
$envExamplePath = '.env.example';

// Read current .env or create from example
if (file_exists($envPath)) {
    $content = file_get_contents($envPath);
    echo "✅ Found existing .env file\n";
} elseif (file_exists($envExamplePath)) {
    $content = file_get_contents($envExamplePath);
    echo "✅ Created .env from .env.example\n";
} else {
    echo "❌ No .env or .env.example found!\n";
    exit(1);
}

// Fix APP_NAME - must be quoted if it contains spaces
$content = preg_replace('/^APP_NAME=AS Hub$/m', 'APP_NAME="AS Hub"', $content);
$content = preg_replace('/^APP_NAME=.*/m', 'APP_NAME="AS Hub"', $content);

// Ensure all required values are set correctly
$requiredValues = [
    'APP_NAME' => '"AS Hub"',
    'APP_ENV' => 'local',
    'APP_DEBUG' => 'true',
    'APP_URL' => 'http://localhost:8000',
    'DB_CONNECTION' => 'sqlite',
    'SESSION_DRIVER' => 'file',
    'SESSION_LIFETIME' => '120',
    'CACHE_DRIVER' => 'file',
    'QUEUE_CONNECTION' => 'sync',
    'BROADCAST_DRIVER' => 'log',
    'FILESYSTEM_DISK' => 'local',
    'LOG_CHANNEL' => 'stack',
    'LOG_LEVEL' => 'debug',
];

// Update or add each value
foreach ($requiredValues as $key => $value) {
    if (preg_match("/^$key=/m", $content)) {
        // Update existing
        $content = preg_replace("/^$key=.*/m", "$key=$value", $content);
        echo "✅ Updated $key=$value\n";
    } else {
        // Add new
        $content .= "\n$key=$value";
        echo "✅ Added $key=$value\n";
    }
}

// Remove any DB_ variables that shouldn't be there for SQLite
$content = preg_replace('/^DB_HOST=.*/m', '# DB_HOST=127.0.0.1', $content);
$content = preg_replace('/^DB_PORT=.*/m', '# DB_PORT=3306', $content);
$content = preg_replace('/^DB_DATABASE=(?!sqlite).*/m', '# DB_DATABASE=', $content);
$content = preg_replace('/^DB_USERNAME=.*/m', '# DB_USERNAME=', $content);
$content = preg_replace('/^DB_PASSWORD=.*/m', '# DB_PASSWORD=', $content);

// Write the fixed content
file_put_contents($envPath, $content);

echo "\n✅ .env file has been fixed!\n\n";

// Verify the file
echo "Verifying .env file...\n";
$lines = explode("\n", $content);
$errors = 0;

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line) || $line[0] === '#') {
        continue;
    }
    
    // Check for unquoted values with spaces
    if (preg_match('/^([A-Z_]+)=(.+)$/', $line, $matches)) {
        $key = $matches[1];
        $value = $matches[2];
        
        // If value contains spaces and is not quoted, it's an error
        if (strpos($value, ' ') !== false && $value[0] !== '"' && $value[0] !== "'") {
            echo "❌ Error: $key has unquoted value with spaces\n";
            $errors++;
        }
    }
}

if ($errors === 0) {
    echo "✅ No errors found in .env file\n";
} else {
    echo "❌ Found $errors error(s) in .env file\n";
}

echo "\n=== Fix completed ===\n";
