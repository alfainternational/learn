<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\AIController;
use App\Http\Controllers\API\TemplateController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ═══════════════════════════════════════
// Public Routes
// ═══════════════════════════════════════

Route::prefix('v1')->group(function () {

    // Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Public Templates
    Route::get('/templates', [TemplateController::class, 'index']);
    Route::get('/templates/{template}', [TemplateController::class, 'show']);

    // Public Shared Plans
    Route::get('/plans/shared/{token}', [PlanController::class, 'showShared']);

    // Pricing Plans (Public)
    Route::get('/pricing', [SubscriptionController::class, 'pricingPlans']);

});

// ═══════════════════════════════════════
// Authenticated Routes
// ═══════════════════════════════════════

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // ─────────────────────────────────
    // User Profile
    // ─────────────────────────────────
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'updateProfile']);
    Route::post('/me/avatar', [AuthController::class, 'uploadAvatar']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // ─────────────────────────────────
    // Marketing Plans
    // ─────────────────────────────────
    Route::apiResource('plans', PlanController::class);

    Route::prefix('plans')->group(function () {
        // Operations
        Route::post('/{plan}/duplicate', [PlanController::class, 'duplicate']);
        Route::post('/{plan}/archive', [PlanController::class, 'archive']);
        Route::post('/{plan}/share', [PlanController::class, 'generateShareLink']);
        Route::delete('/{plan}/share', [PlanController::class, 'revokeShareLink']);

        // Export
        Route::get('/{plan}/export/pdf', [PlanController::class, 'exportPdf']);
        Route::get('/{plan}/export/docx', [PlanController::class, 'exportDocx']);
        Route::get('/{plan}/export/excel', [PlanController::class, 'exportExcel']);

        // Sections
        Route::get('/{plan}/sections', [PlanController::class, 'getSections']);
        Route::put('/{plan}/sections/{sectionType}', [PlanController::class, 'updateSection']);
    });

    // ─────────────────────────────────
    // AI Assistant
    // ─────────────────────────────────
    Route::prefix('ai')->group(function () {
        Route::post('/chat', [AIController::class, 'chat']);
        Route::post('/suggestions/audience', [AIController::class, 'suggestAudience']);
        Route::post('/suggestions/message', [AIController::class, 'improveMessage']);
        Route::post('/analysis/competitors', [AIController::class, 'analyzeCompetitors']);
        Route::post('/generate/content-plan', [AIController::class, 'generateContentPlan']);
        Route::get('/credits', [AIController::class, 'getCredits']);
    });

    // ─────────────────────────────────
    // Templates
    // ─────────────────────────────────
    Route::post('/templates/{template}/use', [TemplateController::class, 'createFromTemplate']);
    Route::post('/templates', [TemplateController::class, 'saveAsTemplate']);
    Route::get('/templates/my', [TemplateController::class, 'myTemplates']);
    Route::put('/templates/{template}', [TemplateController::class, 'update']);
    Route::delete('/templates/{template}', [TemplateController::class, 'destroy']);

    // ─────────────────────────────────
    // Subscriptions
    // ─────────────────────────────────
    Route::prefix('subscription')->group(function () {
        Route::get('/', [SubscriptionController::class, 'current']);
        Route::post('/upgrade', [SubscriptionController::class, 'upgrade']);
        Route::post('/cancel', [SubscriptionController::class, 'cancel']);
        Route::get('/invoices', [SubscriptionController::class, 'invoices']);
        Route::post('/payment-method', [SubscriptionController::class, 'updatePaymentMethod']);
    });

    // ─────────────────────────────────
    // Notifications
    // ─────────────────────────────────
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
    });

});

// ═══════════════════════════════════════
// Admin Routes (Phase 9)
// ═══════════════════════════════════════
// سيتم إضافتها في الرد التالي

// ═══════════════════════════════════════
// Advertiser Routes (Phase 8)
// ═══════════════════════════════════════
// سيتم إضافتها في الرد التالي
