# AS Hub Admin Panel - Comprehensive Test Script
# ================================================

Write-Host "🧪 بدء الاختبار الشامل للوحة التحكم AS Hub" -ForegroundColor Cyan
Write-Host "=" * 60 -ForegroundColor Gray
Write-Host ""

$testResults = @{
    Passed = 0
    Failed = 0
    Total = 0
}

function Test-API {
    param(
        [string]$Name,
        [string]$Url,
        [string]$Method = "GET",
        [string]$Body = $null,
        [hashtable]$Headers = @{}
    )
    
    $testResults.Total++
    
    try {
        $params = @{
            Uri = $Url
            Method = $Method
            Headers = $Headers
            UseBasicParsing = $true
        }
        
        if ($Body) {
            $params.Body = $Body
            $params.ContentType = "application/json"
        }
        
        $response = Invoke-WebRequest @params
        
        if ($response.StatusCode -ge 200 -and $response.StatusCode -lt 300) {
            Write-Host "✅ $Name" -ForegroundColor Green
            $testResults.Passed++
            return $true
        } else {
            Write-Host "❌ $Name - Status: $($response.StatusCode)" -ForegroundColor Red
            $testResults.Failed++
            return $false
        }
    } catch {
        Write-Host "❌ $Name - Error: $($_.Exception.Message)" -ForegroundColor Red
        $testResults.Failed++
        return $false
    }
}

# ============================================
# 1. Backend API Tests
# ============================================
Write-Host "📡 اختبار Backend API" -ForegroundColor Yellow
Write-Host "-" * 60 -ForegroundColor Gray

# Test 1: Health Check
Test-API -Name "Health Check" -Url "http://localhost:8000/api/health"

# Test 2: Content API
Test-API -Name "Content API (Public)" -Url "http://localhost:8000/api/v1/content"

# Test 3: Login API
$loginBody = @{
    email = "admin@ashub.com"
    password = "Admin@123456"
} | ConvertTo-Json

$loginResult = Test-API -Name "Login API" -Url "http://localhost:8000/api/auth/login" -Method "POST" -Body $loginBody

Write-Host ""

# ============================================
# 2. Admin Panel Build Tests
# ============================================
Write-Host "🏗️  اختبار بناء Admin Panel" -ForegroundColor Yellow
Write-Host "-" * 60 -ForegroundColor Gray

# Check if admin panel is running
try {
    $adminResponse = Invoke-WebRequest -Uri "http://localhost:4202" -UseBasicParsing -TimeoutSec 5
    Write-Host "✅ Admin Panel يعمل على المنفذ 4202" -ForegroundColor Green
    $testResults.Passed++
} catch {
    Write-Host "❌ Admin Panel لا يعمل على المنفذ 4202" -ForegroundColor Red
    $testResults.Failed++
}
$testResults.Total++

Write-Host ""

# ============================================
# 3. File Structure Tests
# ============================================
Write-Host "📁 اختبار هيكل الملفات" -ForegroundColor Yellow
Write-Host "-" * 60 -ForegroundColor Gray

$requiredFiles = @(
    "admin-panel/src/app/pages/login/login.component.ts",
    "admin-panel/src/app/pages/dashboard/dashboard.component.ts",
    "admin-panel/src/app/pages/services/services.component.ts",
    "admin-panel/src/app/pages/pricing/pricing.component.ts",
    "admin-panel/src/app/layout/layout.component.ts",
    "admin-panel/src/app/services/api.service.ts",
    "admin-panel/src/app/services/auth.service.ts",
    "admin-panel/src/assets/i18n/en.json",
    "admin-panel/src/assets/i18n/ar.json",
    "backend/app/Models/Admin.php",
    "backend/app/Models/Service.php",
    "backend/app/Models/PricingPlan.php",
    "backend/routes/api.php",
    "backend/database/database.sqlite"
)

foreach ($file in $requiredFiles) {
    $testResults.Total++
    if (Test-Path $file) {
        Write-Host "✅ $file" -ForegroundColor Green
        $testResults.Passed++
    } else {
        Write-Host "❌ $file - غير موجود" -ForegroundColor Red
        $testResults.Failed++
    }
}

Write-Host ""

# ============================================
# 4. Component Tests
# ============================================
Write-Host "🎨 اختبار المكونات" -ForegroundColor Yellow
Write-Host "-" * 60 -ForegroundColor Gray

$components = @(
    @{Name="Login"; Path="admin-panel/src/app/pages/login"},
    @{Name="Dashboard"; Path="admin-panel/src/app/pages/dashboard"},
    @{Name="Services"; Path="admin-panel/src/app/pages/services"},
    @{Name="Pricing"; Path="admin-panel/src/app/pages/pricing"},
    @{Name="Layout"; Path="admin-panel/src/app/layout"}
)

foreach ($component in $components) {
    $testResults.Total++
    $tsFile = "$($component.Path)/$($component.Name.ToLower()).component.ts"
    $htmlFile = "$($component.Path)/$($component.Name.ToLower()).component.html"
    $scssFile = "$($component.Path)/$($component.Name.ToLower()).component.scss"
    
    if ((Test-Path $tsFile) -and (Test-Path $htmlFile) -and (Test-Path $scssFile)) {
        Write-Host "✅ $($component.Name) Component - كامل" -ForegroundColor Green
        $testResults.Passed++
    } else {
        Write-Host "❌ $($component.Name) Component - ناقص" -ForegroundColor Red
        $testResults.Failed++
    }
}

Write-Host ""

# ============================================
# 5. Translation Tests
# ============================================
Write-Host "🌐 اختبار الترجمات" -ForegroundColor Yellow
Write-Host "-" * 60 -ForegroundColor Gray

$testResults.Total++
if ((Test-Path "admin-panel/src/assets/i18n/en.json") -and (Test-Path "admin-panel/src/assets/i18n/ar.json")) {
    $enContent = Get-Content "admin-panel/src/assets/i18n/en.json" -Raw | ConvertFrom-Json
    $arContent = Get-Content "admin-panel/src/assets/i18n/ar.json" -Raw | ConvertFrom-Json
    
    if ($enContent -and $arContent) {
        Write-Host "✅ ملفات الترجمة موجودة وصالحة" -ForegroundColor Green
        $testResults.Passed++
    } else {
        Write-Host "❌ ملفات الترجمة تالفة" -ForegroundColor Red
        $testResults.Failed++
    }
} else {
    Write-Host "❌ ملفات الترجمة غير موجودة" -ForegroundColor Red
    $testResults.Failed++
}

Write-Host ""

# ============================================
# 6. Database Tests
# ============================================
Write-Host "💾 اختبار قاعدة البيانات" -ForegroundColor Yellow
Write-Host "-" * 60 -ForegroundColor Gray

$testResults.Total++
if (Test-Path "backend/database/database.sqlite") {
    $dbSize = (Get-Item "backend/database/database.sqlite").Length
    if ($dbSize -gt 0) {
        Write-Host "✅ قاعدة البيانات موجودة (الحجم: $([math]::Round($dbSize/1KB, 2)) KB)" -ForegroundColor Green
        $testResults.Passed++
    } else {
        Write-Host "❌ قاعدة البيانات فارغة" -ForegroundColor Red
        $testResults.Failed++
    }
} else {
    Write-Host "❌ قاعدة البيانات غير موجودة" -ForegroundColor Red
    $testResults.Failed++
}

Write-Host ""

# ============================================
# Summary
# ============================================
Write-Host "=" * 60 -ForegroundColor Gray
Write-Host "📊 ملخص نتائج الاختبار" -ForegroundColor Cyan
Write-Host "=" * 60 -ForegroundColor Gray
Write-Host ""
Write-Host "إجمالي الاختبارات: $($testResults.Total)" -ForegroundColor White
Write-Host "✅ ناجح: $($testResults.Passed)" -ForegroundColor Green
Write-Host "❌ فاشل: $($testResults.Failed)" -ForegroundColor Red

$successRate = [math]::Round(($testResults.Passed / $testResults.Total) * 100, 2)
Write-Host ""
Write-Host "معدل النجاح: $successRate%" -ForegroundColor $(if ($successRate -ge 80) { "Green" } elseif ($successRate -ge 60) { "Yellow" } else { "Red" })
Write-Host ""

if ($successRate -ge 80) {
    Write-Host "🎉 ممتاز! لوحة التحكم جاهزة للاستخدام" -ForegroundColor Green
} elseif ($successRate -ge 60) {
    Write-Host "⚠️  جيد، لكن يحتاج بعض التحسينات" -ForegroundColor Yellow
} else {
    Write-Host "❌ يحتاج إلى إصلاحات كثيرة" -ForegroundColor Red
}

Write-Host ""
Write-Host "=" * 60 -ForegroundColor Gray
