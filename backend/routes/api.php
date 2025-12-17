<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\PricingController;
use App\Http\Controllers\Admin\FeatureController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\AdminLeadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public Routes
Route::prefix('v1')->group(function () {
    // Get all landing page content
    Route::get('/content', [ContentController::class, 'index']);
    
    // Submit contact form
    Route::post('/leads', [LeadController::class, 'store']);
});

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:api');
});

// Admin Routes (Protected by JWT)
Route::prefix('admin')->middleware('auth:api')->group(function () {
    
    // Profile Management
    Route::get('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'show']);
    Route::put('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update']);
    Route::put('password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword']);
    
    // Services Management
    Route::apiResource('services', ServiceController::class);
    Route::post('services/{id}/toggle', [ServiceController::class, 'toggle']);
    
    // Pricing Plans Management
    Route::apiResource('pricing', PricingController::class);
    Route::post('pricing/{id}/toggle', [PricingController::class, 'toggle']);
    
    // Features Management
    Route::apiResource('features', FeatureController::class);
    Route::post('features/{id}/toggle', [FeatureController::class, 'toggle']);
    
    // Testimonials Management
    Route::apiResource('testimonials', TestimonialController::class);
    Route::post('testimonials/{id}/toggle', [TestimonialController::class, 'toggle']);
    
    // FAQ Management
    Route::apiResource('faq', FaqController::class);
    Route::post('faq/{id}/toggle', [FaqController::class, 'toggle']);
    
    // Settings Management
    Route::get('settings', [SettingController::class, 'index']);
    Route::put('settings', [SettingController::class, 'update']);
    Route::get('settings/{key}', [SettingController::class, 'show']);
    
    // Media Management
    Route::post('media-upload', [MediaController::class, 'upload']);
    Route::get('media', [MediaController::class, 'index']);
    Route::delete('media/{id}', [MediaController::class, 'destroy']);
    
    // Leads Management
    Route::get('leads', [AdminLeadController::class, 'index']);
    Route::get('leads/export', [AdminLeadController::class, 'export']);
    Route::get('leads/{id}', [AdminLeadController::class, 'show']);
    Route::put('leads/{id}', [AdminLeadController::class, 'update']);
    Route::delete('leads/{id}', [AdminLeadController::class, 'destroy']);
    Route::post('leads/{id}/process', [AdminLeadController::class, 'markAsProcessed']);
    
    // Dashboard Stats
    Route::get('dashboard/stats', function () {
        return response()->json([
            'total_leads' => \App\Models\Lead::count(),
            'new_leads' => \App\Models\Lead::where('status', 'new')->count(),
            'processed_leads' => \App\Models\Lead::where('is_processed', true)->count(),
            'total_services' => \App\Models\Service::where('is_active', true)->count(),
            'total_testimonials' => \App\Models\Testimonial::where('is_active', true)->count(),
        ]);
    });
});

// Health Check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'service' => 'AS Hub API',
        'version' => '1.0.0'
    ]);
});
