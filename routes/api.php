<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\LessonController;
use App\Http\Controllers\API\QuizController;
use App\Http\Controllers\API\CertificateController;
use App\Http\Controllers\API\ToolController;
use App\Http\Controllers\API\CampaignController;
use App\Http\Controllers\API\TeamController;
use App\Http\Controllers\API\AgencyController;

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
        Route::get('/sections/{sectionType}/suggestions', [App\Http\Controllers\API\PlanController::class, 'getSuggestedLessons']);
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

    // Courses & Lessons
    Route::prefix('courses')->group(function () {
        Route::get('/', [CourseController::class, 'index']);
        Route::get('/{id}', [CourseController::class, 'show']);
        Route::get('/{id}/progress', [CourseController::class, 'progress']);
    });

    Route::prefix('lessons')->group(function () {
        Route::get('/{id}', [LessonController::class, 'show']);
        Route::post('/{id}/complete', [LessonController::class, 'complete']);
        Route::put('/{id}/progress', [LessonController::class, 'updateProgress']);
    });

    Route::prefix('quizzes')->group(function () {
        Route::get('/{id}', [QuizController::class, 'show']);
        Route::post('/{id}/attempt', [QuizController::class, 'attempt']);
        Route::get('/attempts/{attemptId}', [QuizController::class, 'results']);
    });

    Route::prefix('certificates')->group(function () {
        Route::get('/{number}', [CertificateController::class, 'show']);
        Route::post('/generate/{courseId}', [CertificateController::class, 'generate']);
        Route::get('/{number}/download', [CertificateController::class, 'download']);
    });

    Route::prefix('tools')->group(function () {
        Route::get('/{id}', [ToolController::class, 'show']);
        Route::post('/{id}/use', [ToolController::class, 'use']);
        Route::post('/{id}/save', [ToolController::class, 'saveResult']);
    });

    // Teams & Members
    Route::prefix('teams')->group(function () {
        Route::get('/', [TeamController::class, 'index']);
        Route::post('/', [TeamController::class, 'store']);
        Route::get('/{team}', [TeamController::class, 'show']);
        Route::put('/{team}', [TeamController::class, 'update']);
        Route::delete('/{team}', [TeamController::class, 'destroy']);
        Route::get('/{team}/members', [TeamController::class, 'members']);
        Route::post('/{team}/invite', [TeamController::class, 'invite']);
        Route::put('/{team}/members/{member}', [TeamController::class, 'updateMemberRole']);
        Route::delete('/{team}/members/{member}', [TeamController::class, 'removeMember']);
        Route::post('/{team}/switch', [TeamController::class, 'switchTeam']);
    });
    Route::post('/invitations/accept', [TeamController::class, 'acceptInvitation']);

    // Agency (for agency accounts)
    Route::prefix('agency')->group(function () {
        Route::get('/clients', [AgencyController::class, 'clients']);
        Route::post('/clients', [AgencyController::class, 'storeClient']);
        Route::get('/clients/{client}', [AgencyController::class, 'showClient']);
        Route::put('/clients/{client}', [AgencyController::class, 'updateClient']);
        Route::delete('/clients/{client}', [AgencyController::class, 'destroyClient']);
        Route::get('/clients/{client}/campaigns', [AgencyController::class, 'clientCampaigns']);
        Route::get('/clients/{client}/analytics', [AgencyController::class, 'clientAnalytics']);
    });

    // Campaigns & Metrics
    Route::prefix('campaigns')->group(function () {
        Route::get('/dashboard', [CampaignController::class, 'dashboard']);
        Route::get('/', [CampaignController::class, 'index']);
        Route::post('/', [CampaignController::class, 'store']);
        Route::get('/{campaign}', [CampaignController::class, 'show']);
        Route::put('/{campaign}', [CampaignController::class, 'update']);
        Route::post('/{campaign}/metrics', [CampaignController::class, 'addMetrics']);
        Route::get('/{campaign}/analyze', [CampaignController::class, 'analyze']);
        Route::get('/{campaign}/compare-plan', [CampaignController::class, 'compareWithPlan']);
        Route::get('/{campaign}/recommendations', [CampaignController::class, 'getRecommendations']);
    });

    // Tasks Management
    Route::prefix('tasks')->group(function () {
        Route::get('/', [App\Http\Controllers\API\TaskController::class, 'index']);
        Route::post('/', [App\Http\Controllers\API\TaskController::class, 'store']);
        Route::get('/stats', [App\Http\Controllers\API\TaskController::class, 'boardStats']);
        Route::post('/reorder', [App\Http\Controllers\API\TaskController::class, 'reorder']);
        Route::get('/{task}', [App\Http\Controllers\API\TaskController::class, 'show']);
        Route::put('/{task}', [App\Http\Controllers\API\TaskController::class, 'update']);
        Route::delete('/{task}', [App\Http\Controllers\API\TaskController::class, 'destroy']);
        Route::post('/{task}/comments', [App\Http\Controllers\API\TaskController::class, 'addComment']);
        Route::put('/{task}/checklist', [App\Http\Controllers\API\TaskController::class, 'updateChecklist']);
    });

    // Calendar Events
    Route::prefix('calendar')->group(function () {
        Route::get('/', [App\Http\Controllers\API\CalendarController::class, 'index']);
        Route::post('/', [App\Http\Controllers\API\CalendarController::class, 'store']);
        Route::get('/occasions', [App\Http\Controllers\API\CalendarController::class, 'marketingOccasions']);
        Route::put('/{calendarEvent}', [App\Http\Controllers\API\CalendarController::class, 'update']);
        Route::delete('/{calendarEvent}', [App\Http\Controllers\API\CalendarController::class, 'destroy']);
    });

    // Reminders
    Route::prefix('reminders')->group(function () {
        Route::get('/', [App\Http\Controllers\API\ReminderController::class, 'index']);
        Route::post('/', [App\Http\Controllers\API\ReminderController::class, 'store']);
        Route::get('/upcoming', [App\Http\Controllers\API\ReminderController::class, 'upcoming']);
        Route::put('/{reminder}', [App\Http\Controllers\API\ReminderController::class, 'update']);
        Route::delete('/{reminder}', [App\Http\Controllers\API\ReminderController::class, 'destroy']);
        Route::post('/{reminder}/sent', [App\Http\Controllers\API\ReminderController::class, 'markSent']);
    });
});
