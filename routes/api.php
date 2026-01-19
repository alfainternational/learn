<?php

use Illuminate\Support\Facades\Route;

// Public routes (no authentication required)
Route::prefix('v1')->group(function () {
    
    // Health check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'app' => config('app.name'),
            'version' => '1.0.0',
            'timestamp' => now()->toIso8601String(),
        ]);
    });

    // Shared plans (public access)
    Route::get('/plans/shared/{token}', [App\Http\Controllers\API\PlanController::class, 'showShared']);
    
    // Authentication (public)
    Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);
});

// Protected routes (require authentication)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // User & Profile
    Route::get('/me', [App\Http\Controllers\API\AuthController::class, 'me']);
    Route::get('/dashboard', [App\Http\Controllers\API\DashboardController::class, 'index']); // New Dashboard Route
    Route::put('/me', [App\Http\Controllers\API\AuthController::class, 'update']);

    Route::post('/me/avatar', [App\Http\Controllers\API\AuthController::class, 'uploadAvatar']);
    Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);

    // Marketing Plans
    Route::apiResource('plans', App\Http\Controllers\API\PlanController::class);
    Route::prefix('plans/{plan}')->group(function () {
        Route::get('/sections', [App\Http\Controllers\API\PlanController::class, 'getSections']);
        Route::put('/sections/{sectionType}', [App\Http\Controllers\API\PlanController::class, 'updateSection']);
        Route::post('/duplicate', [App\Http\Controllers\API\PlanController::class, 'duplicate']);
        Route::post('/archive', [App\Http\Controllers\API\PlanController::class, 'archive']);
        Route::post('/share', [App\Http\Controllers\API\PlanController::class, 'generateShareLink']);
        Route::delete('/share', [App\Http\Controllers\API\PlanController::class, 'revokeShareLink']);
        Route::get('/export/pdf', [App\Http\Controllers\API\PlanController::class, 'exportPdf']);
        Route::get('/export/excel', [App\Http\Controllers\API\PlanController::class, 'exportExcel']);
        Route::get('/export/docx', [App\Http\Controllers\API\PlanController::class, 'exportDocx']);
    });

    // AI Features
    Route::prefix('ai')->group(function () {
        Route::post('/chat', [App\Http\Controllers\API\AIController::class, 'chat']);
        Route::post('/guidance', [App\Http\Controllers\API\AIController::class, 'guidance']); // New: Free guidance
        Route::post('/suggestions', [App\Http\Controllers\API\AIController::class, 'suggestions']); // New: AI suggestions
        Route::post('/analyze', [App\Http\Controllers\API\AIController::class, 'analyze']); // New: Content analysis
        Route::post('/suggestions/audience', [App\Http\Controllers\API\AIController::class, 'suggestAudience']);
        Route::post('/suggestions/message', [App\Http\Controllers\API\AIController::class, 'improveMessage']);
        Route::post('/analysis/competitors', [App\Http\Controllers\API\AIController::class, 'analyzeCompetitors']);
        Route::post('/generate/content-plan', [App\Http\Controllers\API\AIController::class, 'generateContentPlan']);
        Route::get('/credits', [App\Http\Controllers\API\AIController::class, 'getCredits']);
    });

    // Templates
    Route::apiResource('templates', App\Http\Controllers\API\TemplateController::class)->only(['index', 'show']);
    Route::post('/templates/{template}/use', [App\Http\Controllers\API\TemplateController::class, 'useTemplate']);

    // Subscriptions
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [App\Http\Controllers\API\SubscriptionController::class, 'index']);
        Route::get('/current', [App\Http\Controllers\API\SubscriptionController::class, 'current']);
        Route::post('/subscribe', [App\Http\Controllers\API\SubscriptionController::class, 'subscribe']);
        Route::post('/cancel', [App\Http\Controllers\API\SubscriptionController::class, 'cancel']);
        Route::post('/resume', [App\Http\Controllers\API\SubscriptionController::class, 'resume']);
        Route::get('/transactions', [App\Http\Controllers\API\TransactionController::class, 'index']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [App\Http\Controllers\API\NotificationController::class, 'index']);
        Route::get('/unread', [App\Http\Controllers\API\NotificationController::class, 'unread']);
        Route::post('/{notification}/read', [App\Http\Controllers\API\NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [App\Http\Controllers\API\NotificationController::class, 'markAllAsRead']);
    });

    // Admin Routes
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\API\Admin\DashboardController::class, 'index']);
        
        // User Management
        Route::apiResource('users', App\Http\Controllers\API\Admin\UserController::class);
        Route::post('/users/{user}/suspend', [App\Http\Controllers\API\Admin\UserController::class, 'suspend']);
        Route::post('/users/{user}/activate', [App\Http\Controllers\API\Admin\UserController::class, 'activate']);
        
        // Ad Campaign Management
        Route::get('/campaigns', [App\Http\Controllers\API\Admin\AdCampaignController::class, 'index']);
        Route::get('/campaigns/pending', [App\Http\Controllers\API\Admin\AdCampaignController::class, 'pending']);
        Route::post('/campaigns/{campaign}/approve', [App\Http\Controllers\API\Admin\AdCampaignController::class, 'approve']);
        Route::post('/campaigns/{campaign}/reject', [App\Http\Controllers\API\Admin\AdCampaignController::class, 'reject']);
        
        // Settings Management
        Route::get('/settings', [App\Http\Controllers\API\Admin\SettingsController::class, 'index']);
        Route::put('/settings', [App\Http\Controllers\API\Admin\SettingsController::class, 'update']);
        Route::post('/settings/test-gemini', [App\Http\Controllers\API\Admin\SettingsController::class, 'testGeminiKey']);
    });

    // Advertiser Routes
    Route::prefix('advertiser')->middleware('advertiser')->group(function () {
        Route::apiResource('campaigns', App\Http\Controllers\API\Advertiser\CampaignController::class);
        Route::get('/campaigns/{campaign}/analytics', [App\Http\Controllers\API\Advertiser\CampaignController::class, 'analytics']);
        Route::post('/campaigns/{campaign}/pause', [App\Http\Controllers\API\Advertiser\CampaignController::class, 'pause']);
        Route::post('/campaigns/{campaign}/resume', [App\Http\Controllers\API\Advertiser\CampaignController::class, 'resume']);
    });
});
