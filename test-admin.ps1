Write-Host "AS Hub Admin Panel - Comprehensive Test" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Gray
Write-Host ""

$passed = 0
$failed = 0
$total = 0

function Test-Endpoint {
    param([string]$Name, [string]$Url)
    $global:total++
    try {
        $response = Invoke-WebRequest -Uri $Url -UseBasicParsing -TimeoutSec 5
        Write-Host "[PASS] $Name" -ForegroundColor Green
        $global:passed++
        return $true
    } catch {
        Write-Host "[FAIL] $Name - $($_.Exception.Message)" -ForegroundColor Red
        $global:failed++
        return $false
    }
}

function Test-File {
    param([string]$Name, [string]$Path)
    $global:total++
    if (Test-Path $Path) {
        Write-Host "[PASS] $Name" -ForegroundColor Green
        $global:passed++
        return $true
    } else {
        Write-Host "[FAIL] $Name - File not found" -ForegroundColor Red
        $global:failed++
        return $false
    }
}

# Backend API Tests
Write-Host "Backend API Tests:" -ForegroundColor Yellow
Test-Endpoint "Health Check" "http://localhost:8000/api/health"
Test-Endpoint "Content API" "http://localhost:8000/api/v1/content"
Write-Host ""

# Admin Panel Test
Write-Host "Admin Panel Tests:" -ForegroundColor Yellow
Test-Endpoint "Admin Panel Running" "http://localhost:4202"
Write-Host ""

# File Structure Tests
Write-Host "File Structure Tests:" -ForegroundColor Yellow
Test-File "Login Component" "admin-panel/src/app/pages/login/login.component.ts"
Test-File "Dashboard Component" "admin-panel/src/app/pages/dashboard/dashboard.component.ts"
Test-File "Services Component" "admin-panel/src/app/pages/services/services.component.ts"
Test-File "Pricing Component" "admin-panel/src/app/pages/pricing/pricing.component.ts"
Test-File "Layout Component" "admin-panel/src/app/layout/layout.component.ts"
Test-File "API Service" "admin-panel/src/app/services/api.service.ts"
Test-File "Auth Service" "admin-panel/src/app/services/auth.service.ts"
Test-File "English Translations" "admin-panel/src/assets/i18n/en.json"
Test-File "Arabic Translations" "admin-panel/src/assets/i18n/ar.json"
Test-File "Database" "backend/database/database.sqlite"
Write-Host ""

# Summary
Write-Host "========================================" -ForegroundColor Gray
Write-Host "Test Summary:" -ForegroundColor Cyan
Write-Host "Total Tests: $total" -ForegroundColor White
Write-Host "Passed: $passed" -ForegroundColor Green
Write-Host "Failed: $failed" -ForegroundColor Red
$rate = [math]::Round(($passed / $total) * 100, 2)
Write-Host "Success Rate: $rate%" -ForegroundColor $(if ($rate -ge 80) { "Green" } else { "Yellow" })
Write-Host ""

if ($rate -ge 80) {
    Write-Host "Status: READY FOR USE!" -ForegroundColor Green
} else {
    Write-Host "Status: NEEDS FIXES" -ForegroundColor Yellow
}
