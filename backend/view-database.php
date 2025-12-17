<?php

echo "=== AS Hub Database Viewer ===\n\n";

$dbPath = __DIR__ . '/database/database.sqlite';

if (!file_exists($dbPath)) {
    echo "❌ Database file not found!\n";
    echo "Path: $dbPath\n";
    exit(1);
}

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to database\n\n";
    
    // Get all tables
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📊 Database Tables (" . count($tables) . "):\n";
    echo str_repeat("=", 50) . "\n\n";
    
    foreach ($tables as $table) {
        // Get row count
        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "📋 $table ($count rows)\n";
        
        // Show sample data for important tables
        if (in_array($table, ['admins', 'services', 'pricing_plans', 'leads', 'settings'])) {
            $stmt = $pdo->query("SELECT * FROM $table LIMIT 3");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($rows)) {
                echo "   Sample data:\n";
                foreach ($rows as $i => $row) {
                    echo "   " . ($i + 1) . ". ";
                    
                    // Show relevant fields based on table
                    switch ($table) {
                        case 'admins':
                            echo "ID: {$row['id']}, Email: {$row['email']}, Name: {$row['name']}\n";
                            break;
                        case 'services':
                            echo "ID: {$row['id']}, Title: {$row['title']} ({$row['language']})\n";
                            break;
                        case 'pricing_plans':
                            echo "ID: {$row['id']}, Name: {$row['name']}, Monthly: \${$row['price_monthly']}\n";
                            break;
                        case 'leads':
                            echo "ID: {$row['id']}, Name: {$row['name']}, Email: {$row['email']}, Status: {$row['status']}\n";
                            break;
                        case 'settings':
                            echo "Key: {$row['key']}, Value: " . substr($row['value'], 0, 30) . "...\n";
                            break;
                    }
                }
            }
        }
        echo "\n";
    }
    
    // Statistics
    echo str_repeat("=", 50) . "\n";
    echo "📈 Statistics:\n";
    echo str_repeat("=", 50) . "\n\n";
    
    $stats = [
        'Total Admins' => $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn(),
        'Active Services' => $pdo->query("SELECT COUNT(*) FROM services WHERE is_active = 1")->fetchColumn(),
        'Pricing Plans' => $pdo->query("SELECT COUNT(*) FROM pricing_plans")->fetchColumn(),
        'Features' => $pdo->query("SELECT COUNT(*) FROM features")->fetchColumn(),
        'Testimonials' => $pdo->query("SELECT COUNT(*) FROM testimonials")->fetchColumn(),
        'FAQ Items' => $pdo->query("SELECT COUNT(*) FROM faq")->fetchColumn(),
        'Total Leads' => $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn(),
        'New Leads' => $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'new'")->fetchColumn(),
        'Settings' => $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn(),
    ];
    
    foreach ($stats as $label => $value) {
        echo sprintf("%-20s: %d\n", $label, $value);
    }
    
    // Admin details
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "👤 Admin Users:\n";
    echo str_repeat("=", 50) . "\n\n";
    
    $admins = $pdo->query("SELECT * FROM admins")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($admins as $admin) {
        echo "ID: {$admin['id']}\n";
        echo "Name: {$admin['name']}\n";
        echo "Email: {$admin['email']}\n";
        echo "Active: " . ($admin['is_active'] ? 'Yes' : 'No') . "\n";
        echo "Created: {$admin['created_at']}\n";
        if ($admin['last_login_at']) {
            echo "Last Login: {$admin['last_login_at']}\n";
        }
        echo "\n";
    }
    
    // Recent leads
    echo str_repeat("=", 50) . "\n";
    echo "📬 Recent Leads (Last 5):\n";
    echo str_repeat("=", 50) . "\n\n";
    
    $leads = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    if (empty($leads)) {
        echo "No leads yet.\n\n";
    } else {
        foreach ($leads as $i => $lead) {
            echo ($i + 1) . ". {$lead['name']} ({$lead['email']})\n";
            echo "   Company: {$lead['company']}\n";
            echo "   Status: {$lead['status']}\n";
            echo "   Date: {$lead['created_at']}\n\n";
        }
    }
    
    echo str_repeat("=", 50) . "\n";
    echo "✅ Database viewer complete!\n\n";
    
    echo "💡 Tips:\n";
    echo "- Use DB Browser for SQLite for GUI access\n";
    echo "- Use 'php artisan tinker' for interactive queries\n";
    echo "- Database location: backend/database/database.sqlite\n";
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    exit(1);
}
