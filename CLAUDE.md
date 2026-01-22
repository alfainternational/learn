# CLAUDE.md - AI Assistant Guide for خطِّط (Khattit) Marketing Platform

> Last Updated: 2026-01-22
>
> This document provides comprehensive guidance for AI assistants working with the Khattit marketing platform codebase.

## 📋 Table of Contents

1. [Project Overview](#project-overview)
2. [Tech Stack & Architecture](#tech-stack--architecture)
3. [Directory Structure](#directory-structure)
4. [Key Concepts & Patterns](#key-concepts--patterns)
5. [Development Workflows](#development-workflows)
6. [API Conventions](#api-conventions)
7. [Database & Models](#database--models)
8. [Frontend Architecture](#frontend-architecture)
9. [Common Tasks](#common-tasks)
10. [Important Warnings & Gotchas](#important-warnings--gotchas)
11. [Testing & Quality](#testing--quality)

---

## Project Overview

**Name:** خطِّط (Khattit) - AI-Powered Marketing Planning Platform

**Purpose:** A comprehensive SaaS application that helps marketers and business owners create, manage, and optimize marketing strategies using Google Gemini AI.

**Primary Features:**
- AI-powered marketing plan generation and consulting
- Interactive educational course system with quizzes and certificates
- Campaign management with real-time analytics
- Team collaboration and agency client management
- Task management with calendar integration
- Multi-tier subscription system with credit management

**Target Users:**
- Marketing professionals
- Business owners
- Marketing agencies
- Marketing students

**Language:** Application is primarily in Arabic (RTL), with English code comments

---

## Tech Stack & Architecture

### Backend Stack
```
Laravel 11.x (PHP 8.2+)
├── Authentication: Laravel Sanctum (Bearer token)
├── Authorization: Spatie Permissions (RBAC)
├── Database: MySQL/SQLite
├── Cache/Queue: Redis (Predis)
├── Payments: Laravel Cashier (Stripe) + Moyasar (Middle East)
├── Exports: DomPDF + Maatwebsite/Excel
└── AI: Google Gemini API
```

### Frontend Stack
```
Vue 3 + Vite
├── Router: Vue Router 4
├── State: Pinia
├── Styling: Tailwind CSS 3
├── Icons: Heroicons
├── Components: Headless UI
├── HTTP: Axios
└── Charts: Chart.js
```

### Architecture Pattern
- **Backend:** RESTful API with service layer pattern
- **Frontend:** SPA with component-based architecture
- **Data:** Eloquent ORM with JSON columns for flexibility
- **Auth:** Token-based authentication with role/permission system

---

## Directory Structure

```
learn/
├── app/
│   ├── Http/
│   │   ├── Controllers/API/          # 26 API controllers
│   │   │   ├── AIController.php      # Gemini AI integration
│   │   │   ├── PlanController.php    # Marketing plans CRUD
│   │   │   ├── CourseController.php  # Educational system
│   │   │   ├── CampaignController.php # Campaign management
│   │   │   ├── Admin/                # Admin-only controllers
│   │   │   └── Advertiser/           # Advertiser controllers
│   │   ├── Requests/                 # Form validation classes
│   │   └── Middleware/               # Custom middleware
│   ├── Models/                       # 32 Eloquent models
│   ├── Services/                     # Business logic services
│   │   ├── AI/GeminiService.php      # AI service wrapper
│   │   ├── Export/ExportService.php  # PDF/Excel export
│   │   └── Payment/PaymentService.php # Payment processing
│   ├── Policies/                     # Authorization policies
│   └── Providers/                    # Service providers
│
├── resources/
│   ├── js/
│   │   ├── app.js                    # Vue app entry
│   │   ├── App.vue                   # Root component
│   │   ├── bootstrap.js              # Axios config
│   │   ├── router/index.js           # Route definitions (30+ routes)
│   │   ├── stores/auth.js            # Pinia authentication store
│   │   ├── layouts/                  # Layout components
│   │   ├── views/                    # 54 page components
│   │   │   ├── Plans/                # Plan management
│   │   │   ├── Courses/              # Course system
│   │   │   ├── Dashboard/            # User dashboard
│   │   │   ├── Admin/                # Admin panel
│   │   │   └── [+10 more]
│   │   └── components/               # 50+ reusable components
│   └── views/                        # Blade templates (minimal - SPA)
│
├── routes/
│   └── api.php                       # 100+ API endpoints
│
├── database/
│   ├── migrations/                   # 21 database migrations
│   ├── seeders/                      # Database seeders
│   └── factories/                    # Model factories
│
├── config/                           # Laravel configuration
├── public/                           # Public assets (compiled via Vite)
├── storage/                          # File storage
├── tests/                            # PHPUnit tests
└── docs/                             # Additional documentation
```

---

## Key Concepts & Patterns

### 1. Authentication Flow

```
Registration/Login:
┌─────────┐         ┌─────────────┐         ┌──────────┐
│ Frontend│────────▶│ POST /login │────────▶│ Sanctum  │
│ (Vue)   │         │ /register   │         │ Token    │
└─────────┘         └─────────────┘         └──────────┘
     │                                             │
     │◀────────────────────────────────────────────┘
     │         Token stored in localStorage
     │
     ▼
All subsequent requests include:
Authorization: Bearer {token}
```

**Key Files:**
- Frontend: `resources/js/stores/auth.js` (Pinia store)
- Backend: `app/Http/Controllers/API/AuthController.php`
- Middleware: `auth:sanctum` on protected routes

### 2. Role-Based Access Control (RBAC)

**Roles:**
- `admin` - Full system access, admin panel
- `user` - Standard users, plan creation
- `advertiser` - Ad campaign management
- `agency` - Team/client management (implicit via account_type)

**Authorization Pattern:**
```php
// Route-level middleware
Route::middleware('admin')->group(function() {
    // Admin routes
});

// Policy-based authorization
$this->authorize('update', $plan);  // Only plan owner
$this->authorize('view', $campaign); // Only campaign owner
```

**Key Files:**
- Middleware: `app/Http/Middleware/AdminMiddleware.php`
- Policies: `app/Policies/*Policy.php`
- Model: `app/Models/User.php` (HasRoles trait)

### 3. Subscription & Credit System

**Subscription Tiers:**
```
free       → 1 plan, 3 AI credits/month
basic      → 3 plans, unlimited AI
pro        → Unlimited plans, advanced features
enterprise → Custom features, dedicated support
```

**Credit Management:**
- Free users: 3 credits/month (resets via `ai_credits_reset_at`)
- Paid users: Unlimited AI usage
- Each AI call deducts 1 credit for free users
- Check credits before AI calls: `$user->canUseAI()`

**Key Files:**
- Model: `app/Models/User.php` (credit methods)
- Model: `app/Models/Subscription.php`
- Controller: `app/Http/Controllers/API/SubscriptionController.php`

### 4. Marketing Plan Structure

**12 Plan Sections:**
1. `diagnosis` - Market diagnosis
2. `target_audience` - Target audience definition
3. `core_message` - Core marketing message
4. `offer_stack` - Offer stack/value proposition
5. `channels` - Marketing channels
6. `content_plan` - Content planning
7. `budget_breakdown` - Budget allocation
8. `competitor_analysis` - Competitor analysis
9. `metrics` - Success metrics/KPIs
10. `personal_card` - Personal business card
11. `followup` - Follow-up strategy
12. `funnel` - Marketing funnel

**Structure:**
```
MarketingPlan (main table)
  └── PlanSection (12 rows per plan)
        ├── section_type (enum)
        └── section_data (JSON - flexible schema)
```

**Completion Tracking:**
- `completion_percentage` auto-calculated
- `sections_completed` (JSON array)
- Call `$plan->updateCompletionPercentage()` after section updates

**Key Files:**
- Model: `app/Models/MarketingPlan.php`
- Model: `app/Models/PlanSection.php`
- Controller: `app/Http/Controllers/API/PlanController.php`
- Views: `resources/js/views/Plans/Sections/*.vue`

### 5. AI Integration (Google Gemini)

**Service:** `GeminiService` wraps Google Gemini API

**Features:**
- Chat with conversation history
- Target audience suggestions
- Message improvement recommendations
- Competitor analysis
- Content plan generation

**Usage Pattern:**
```php
$gemini = app(GeminiService::class);
$response = $gemini->chat($message, $history, $context);

// Response includes:
// - text: Generated response
// - tokens_used: Token count
// - processing_time: Time in ms
```

**Demo Mode:** Fallback when `GEMINI_API_KEY` not set

**Key Files:**
- Service: `app/Services/AI/GeminiService.php`
- Controller: `app/Http/Controllers/API/AIController.php`
- Model: `app/Models/AIConversation.php` (stores history)

### 6. Educational System Flow

```
Course → Lesson → Quiz → Certificate
  │        │        │         │
  │        │        │         └─ Issued when course completed
  │        │        └─ Pass required (70%+)
  │        └─ Lessons with video, objectives, tools
  └─ Multiple lessons, estimated hours
```

**Progress Tracking:**
- `user_lesson_progress` table tracks completion
- Automatic unlock of next lesson
- Certificates generated when all lessons + final quiz passed

**Key Files:**
- Models: `app/Models/Course.php`, `Lesson.php`, `Quiz.php`
- Controllers: `app/Http/Controllers/API/CourseController.php`
- Views: `resources/js/views/Courses/`, `Lessons/`, `Quizzes/`

### 7. Team & Agency Model

**Structure:**
```
Team (business or agency)
  ├── Owner (User)
  ├── Members (TeamMember pivot)
  └── Clients (AgencyClient) - agencies only
```

**Member Roles:**
- `owner` - Full control
- `admin` - Manage members, settings
- `editor` - Edit content
- `viewer` - Read-only access

**Agency Features:**
- Manage multiple client accounts
- Access client plans/campaigns
- Separate billing per client

**Key Files:**
- Models: `app/Models/Team.php`, `TeamMember.php`, `AgencyClient.php`
- Controllers: `app/Http/Controllers/API/TeamController.php`

---

## Development Workflows

### Setting Up Development Environment

```bash
# 1. Clone repository (already done)
cd /home/user/learn

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Database setup (SQLite for development)
touch database/database.sqlite
php artisan migrate --seed

# 5. Start development servers
# Option A: Run all services together
composer dev

# Option B: Run separately
# Terminal 1: Backend
php artisan serve

# Terminal 2: Frontend
npm run dev

# Terminal 3: Queue worker (if testing async jobs)
php artisan queue:listen
```

### Essential Environment Variables

**Required for full functionality:**
```env
# AI Features
GEMINI_API_KEY=your-api-key-here

# Payment Processing (for subscription testing)
STRIPE_KEY=your-stripe-key
STRIPE_SECRET=your-stripe-secret
MOYASAR_API_KEY=your-moyasar-key

# Storage (for file uploads)
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_BUCKET=your-bucket
```

### Git Workflow

**IMPORTANT:** All work must be done on branch: `claude/claude-md-mkpc7c5eduntnxzw-oxraL`

```bash
# Ensure you're on the correct branch
git status

# Create branch if it doesn't exist
git checkout -b claude/claude-md-mkpc7c5eduntnxzw-oxraL

# Stage and commit changes
git add .
git commit -m "feat: descriptive commit message"

# Push to remote (with retry logic for network failures)
git push -u origin claude/claude-md-mkpc7c5eduntnxzw-oxraL
```

**Commit Message Convention:**
- `feat:` - New feature
- `fix:` - Bug fix
- `refactor:` - Code refactoring
- `docs:` - Documentation updates
- `test:` - Test additions
- `chore:` - Maintenance tasks

---

## API Conventions

### Request/Response Format

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

**Success Response:**
```json
{
  "success": true,
  "data": { /* resource or collection */ },
  "message": "Operation successful",
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error description in Arabic",
  "error": "error_code",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

### API Versioning

All API routes are versioned: `/api/v1/*`

When adding new routes:
```php
// routes/api.php
Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('plans', PlanController::class);
    });
});
```

### Pagination

**Default:** 15 items per page

**Usage:**
```php
// Controller
$plans = MarketingPlan::where('user_id', auth()->id())
    ->paginate(15);

return response()->json([
    'data' => $plans->items(),
    'pagination' => [
        'current_page' => $plans->currentPage(),
        'per_page' => $plans->perPage(),
        'total' => $plans->total(),
        'last_page' => $plans->lastPage(),
    ]
]);
```

**Frontend (Axios):**
```javascript
const response = await axios.get('/api/v1/plans?page=2');
```

### Common API Endpoints

**Authentication (Public):**
- `POST /api/v1/register` - User registration
- `POST /api/v1/login` - User login
- `GET /api/v1/plans/shared/{token}` - View shared plan

**Marketing Plans (Protected):**
- `GET /api/v1/plans` - List user's plans
- `POST /api/v1/plans` - Create new plan
- `GET /api/v1/plans/{id}` - View plan
- `PUT /api/v1/plans/{id}` - Update plan
- `DELETE /api/v1/plans/{id}` - Delete plan (soft delete)
- `PUT /api/v1/plans/{id}/sections/{type}` - Update section
- `POST /api/v1/plans/{id}/duplicate` - Clone plan
- `GET /api/v1/plans/{id}/export/{format}` - Export (pdf|excel|docx)

**AI Features (Protected):**
- `POST /api/v1/ai/chat` - Chat with AI advisor
- `POST /api/v1/ai/suggestions/audience` - Get audience suggestions
- `POST /api/v1/ai/suggestions/message` - Improve message
- `POST /api/v1/ai/analysis/competitors` - Analyze competitors
- `GET /api/v1/ai/credits` - Check remaining credits

**Educational (Protected):**
- `GET /api/v1/courses` - List courses
- `GET /api/v1/courses/{id}` - Course details
- `GET /api/v1/lessons/{id}` - Lesson content
- `POST /api/v1/lessons/{id}/complete` - Mark complete
- `POST /api/v1/quizzes/{id}/attempt` - Submit quiz

**Admin (Protected + admin middleware):**
- `GET /api/v1/admin/dashboard` - Admin stats
- `GET /api/v1/admin/users` - List all users
- `POST /api/v1/admin/users/{user}/suspend` - Suspend user

---

## Database & Models

### Core Models & Relationships

**User Model:**
```php
User::class
  - hasMany(MarketingPlan::class)
  - hasMany(Campaign::class)
  - hasMany(Task::class)
  - hasMany(Subscription::class)
  - belongsToMany(Team::class, 'team_members')
  - hasMany(AIConversation::class)

  // Methods
  - canUseAI(): bool              // Check AI credits
  - deductAICredit(): void        // Deduct 1 credit
  - resetAICredits(): void        // Monthly reset
  - hasActiveSubscription(): bool
  - isAdmin(): bool
  - isAdvertiser(): bool
```

**MarketingPlan Model:**
```php
MarketingPlan::class
  - belongsTo(User::class)
  - hasMany(PlanSection::class)
  - hasMany(Campaign::class)
  - hasMany(Task::class)

  // Scopes
  - scopeActive($query)
  - scopeCompleted($query)
  - scopeShared($query)

  // Methods
  - updateCompletionPercentage(): void
  - generateShareToken(): string
  - revokeShareToken(): void
```

**Campaign Model:**
```php
Campaign::class
  - belongsTo(User::class)
  - belongsTo(MarketingPlan::class)
  - hasMany(CampaignMetric::class)
  - hasOne(CampaignAnalysis::class)

  // Accessors
  - getTotalMetricsAttribute(): array  // Aggregated metrics
  - getPerformanceScoreAttribute(): float
```

**Course Model:**
```php
Course::class
  - hasMany(Lesson::class)
  - hasMany(Quiz::class)
  - hasMany(CourseCertificate::class)

  // Relationships with pivot data
  - belongsToMany(User::class, 'user_lesson_progress')
```

### Migration Patterns

**Standard Table Structure:**
```php
Schema::create('table_name', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->text('description')->nullable();
    $table->json('data')->nullable();           // Flexible data
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->timestamps();
    $table->softDeletes();                      // Soft delete support

    $table->index(['user_id', 'status']);       // Performance indexes
});
```

**JSON Column Usage:**
When you need flexible/extensible data, use JSON columns:
```php
// Migration
$table->json('target_audience');
$table->json('sections_completed');

// Model casting
protected $casts = [
    'target_audience' => 'array',
    'sections_completed' => 'array',
];

// Usage
$plan->target_audience = ['age' => '25-35'];
$plan->save();  // Auto-converted to JSON
```

### Important Database Conventions

1. **Soft Deletes:** Use on user-facing data (plans, campaigns, tasks)
2. **Foreign Keys:** Always use `->constrained()->cascadeOnDelete()`
3. **Indexes:** Add on frequently queried columns
4. **JSON Columns:** For flexible/extensible data structures
5. **Timestamps:** Always include `timestamps()`

---

## Frontend Architecture

### Vue Router Setup

**Router Location:** `resources/js/router/index.js`

**Route Structure:**
```javascript
const routes = [
  // Public routes
  { path: '/', component: Home },
  { path: '/login', component: Login },

  // Protected routes (requiresAuth: true)
  {
    path: '/dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },

  // Admin routes (requiresAdmin: true)
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, requiresAdmin: true }
  }
];
```

**Navigation Guards:**
```javascript
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login');
  } else if (to.meta.requiresAdmin && !authStore.isAdmin) {
    next('/dashboard');
  } else {
    next();
  }
});
```

### Pinia State Management

**Auth Store:** `resources/js/stores/auth.js`

```javascript
export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    loading: false
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
    isAdvertiser: (state) => state.user?.role === 'advertiser',
    hasActiveSubscription: (state) => {
      // Check subscription status
    }
  },

  actions: {
    async login(credentials) { /* ... */ },
    async logout() {
      localStorage.removeItem('token');
      this.token = null;
      this.user = null;
      router.push('/login');
    },
    async fetchUser() { /* ... */ }
  }
});
```

**Usage in Components:**
```vue
<script setup>
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

// Access state
const user = computed(() => authStore.user);
const isAdmin = computed(() => authStore.isAdmin);

// Call actions
const handleLogout = async () => {
  await authStore.logout();
};
</script>
```

### Axios Configuration

**Setup:** `resources/js/bootstrap.js`

```javascript
// Base URL setup
window.axios = axios.create({
  baseURL: document.querySelector('meta[name="api-base-url"]')?.content
           || window.location.origin,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  }
});

// Token interceptor
window.axios.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Error interceptor
window.axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      // Logout on 401
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);
```

### Component Patterns

**Composition API Pattern (Preferred):**
```vue
<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const loading = ref(false);
const plans = ref([]);

const completedPlans = computed(() =>
  plans.value.filter(p => p.completion_percentage === 100)
);

const fetchPlans = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/v1/plans');
    plans.value = response.data.data;
  } catch (error) {
    console.error('Error fetching plans:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchPlans();
});
</script>

<template>
  <div>
    <LoadingSpinner v-if="loading" />
    <div v-else-if="plans.length > 0">
      <!-- Content -->
    </div>
    <EmptyState v-else />
  </div>
</template>
```

### Common Components

**Reusable Components Location:** `resources/js/components/`

**Key Components:**
- `AIAdvisor.vue` - AI chat interface
- `ConfirmModal.vue` - Confirmation dialogs
- `EmptyState.vue` - Empty state UI
- `LoadingSpinner.vue` - Loading indicator
- `ErrorAlert.vue` - Error display
- `SuccessAlert.vue` - Success messages

**Usage:**
```vue
<script setup>
import AIAdvisor from '@/components/AIAdvisor.vue';
import ConfirmModal from '@/components/ConfirmModal.vue';
</script>

<template>
  <AIAdvisor
    :context="planData"
    @message-sent="handleAIResponse"
  />

  <ConfirmModal
    v-if="showDeleteModal"
    title="حذف الخطة"
    message="هل أنت متأكد من حذف هذه الخطة؟"
    @confirm="deletePlan"
    @cancel="showDeleteModal = false"
  />
</template>
```

---

## Common Tasks

### Adding a New API Endpoint

**1. Create Controller Method:**
```php
// app/Http/Controllers/API/NewController.php
public function index(Request $request)
{
    $items = Model::where('user_id', auth()->id())
        ->paginate(15);

    return response()->json([
        'success' => true,
        'data' => $items->items(),
        'pagination' => [
            'current_page' => $items->currentPage(),
            'total' => $items->total(),
        ]
    ]);
}
```

**2. Add Route:**
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('items', NewController::class);
});
```

**3. Create Form Request (optional):**
```bash
php artisan make:request CreateItemRequest
```

**4. Add Policy (if needed):**
```bash
php artisan make:policy ItemPolicy --model=Item
```

### Adding a New Vue Page

**1. Create Component:**
```vue
<!-- resources/js/views/Items/Index.vue -->
<script setup>
import { ref, onMounted } from 'vue';

const items = ref([]);

const fetchItems = async () => {
  const response = await axios.get('/api/v1/items');
  items.value = response.data.data;
};

onMounted(() => fetchItems());
</script>

<template>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Items</h1>
    <div v-for="item in items" :key="item.id">
      {{ item.name }}
    </div>
  </div>
</template>
```

**2. Add Route:**
```javascript
// resources/js/router/index.js
import ItemsIndex from '@/views/Items/Index.vue';

const routes = [
  {
    path: '/items',
    component: ItemsIndex,
    meta: { requiresAuth: true }
  }
];
```

**3. Build Frontend:**
```bash
npm run dev  # Development
npm run build  # Production
```

### Creating a Database Migration

**1. Generate Migration:**
```bash
php artisan make:migration create_items_table
```

**2. Define Schema:**
```php
public function up()
{
    Schema::create('items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('name');
        $table->text('description')->nullable();
        $table->json('metadata')->nullable();
        $table->enum('status', ['active', 'inactive'])->default('active');
        $table->timestamps();
        $table->softDeletes();

        $table->index(['user_id', 'status']);
    });
}

public function down()
{
    Schema::dropIfExists('items');
}
```

**3. Run Migration:**
```bash
php artisan migrate
```

**4. Create Model:**
```bash
php artisan make:model Item
```

**5. Define Model:**
```php
class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'name', 'description', 'metadata', 'status'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### Adding AI Functionality

**Using GeminiService:**
```php
use App\Services\AI\GeminiService;

public function analyzeContent(Request $request, GeminiService $gemini)
{
    $user = auth()->user();

    // Check AI credits
    if (!$user->canUseAI()) {
        return response()->json([
            'success' => false,
            'message' => 'لا يوجد رصيد كافٍ من AI'
        ], 403);
    }

    // Call AI service
    $response = $gemini->chat(
        $request->input('message'),
        $request->input('history', []),
        ['context' => 'content_analysis']
    );

    // Deduct credit if free user
    if ($user->subscription_tier === 'free') {
        $user->deductAICredit();
    }

    // Store conversation
    AIConversation::create([
        'user_id' => $user->id,
        'message_type' => 'user',
        'message_text' => $request->input('message'),
        'tokens_used' => $response['tokens_used'] ?? 0,
    ]);

    return response()->json([
        'success' => true,
        'data' => $response
    ]);
}
```

### Exporting Data to PDF/Excel

**Using ExportService:**
```php
use App\Services\Export\ExportService;

public function export($planId, $format, ExportService $exportService)
{
    $plan = MarketingPlan::with('sections')->findOrFail($planId);

    $this->authorize('view', $plan);

    switch ($format) {
        case 'pdf':
            return $exportService->exportPlanToPDF($plan);
        case 'excel':
            return $exportService->exportPlanToExcel($plan);
        case 'docx':
            return $exportService->exportPlanToDocx($plan);
        default:
            abort(400, 'Invalid format');
    }
}
```

### Implementing Authorization

**1. Create Policy:**
```bash
php artisan make:policy PlanPolicy --model=MarketingPlan
```

**2. Define Policy Methods:**
```php
class PlanPolicy
{
    public function view(User $user, MarketingPlan $plan)
    {
        return $user->id === $plan->user_id;
    }

    public function update(User $user, MarketingPlan $plan)
    {
        return $user->id === $plan->user_id;
    }

    public function delete(User $user, MarketingPlan $plan)
    {
        return $user->id === $plan->user_id;
    }
}
```

**3. Register Policy (if not auto-discovered):**
```php
// app/Providers/AuthServiceProvider.php
protected $policies = [
    MarketingPlan::class => PlanPolicy::class,
];
```

**4. Use in Controller:**
```php
public function update(Request $request, $id)
{
    $plan = MarketingPlan::findOrFail($id);

    $this->authorize('update', $plan);

    $plan->update($request->validated());

    return response()->json([
        'success' => true,
        'data' => $plan
    ]);
}
```

---

## Important Warnings & Gotchas

### ⚠️ Critical Issues to Avoid

**1. AI Credit Management**
```php
// ❌ WRONG - Don't deduct credits without checking
$user->deductAICredit();

// ✅ CORRECT - Always check first
if ($user->canUseAI()) {
    // Make AI call
    if ($user->subscription_tier === 'free') {
        $user->deductAICredit();
    }
} else {
    return response()->json(['error' => 'Insufficient AI credits'], 403);
}
```

**2. Plan Completion Tracking**
```php
// ❌ WRONG - Don't forget to update completion
$section->update(['section_data' => $data]);

// ✅ CORRECT - Update completion after section changes
$section->update(['section_data' => $data, 'is_completed' => true]);
$plan->updateCompletionPercentage();
```

**3. Authorization Checks**
```php
// ❌ WRONG - No authorization check
public function update(Request $request, $id) {
    $plan = MarketingPlan::findOrFail($id);
    $plan->update($request->all());
}

// ✅ CORRECT - Always authorize
public function update(Request $request, $id) {
    $plan = MarketingPlan::findOrFail($id);
    $this->authorize('update', $plan);
    $plan->update($request->validated());
}
```

**4. Soft Deletes**
```php
// ❌ WRONG - Forgets soft deletes exist
$plan = MarketingPlan::find($id);  // Won't find soft-deleted

// ✅ CORRECT - Use findOrFail for proper 404
$plan = MarketingPlan::findOrFail($id);  // Respects soft deletes

// To include soft-deleted
$plan = MarketingPlan::withTrashed()->find($id);
```

**5. JSON Columns**
```php
// ❌ WRONG - Manual JSON encoding
$plan->sections_completed = json_encode(['diagnosis', 'target_audience']);

// ✅ CORRECT - Use arrays (auto-cast)
$plan->sections_completed = ['diagnosis', 'target_audience'];
```

**6. Frontend Token Management**
```javascript
// ❌ WRONG - Making API calls without error handling
const response = await axios.get('/api/v1/plans');

// ✅ CORRECT - Always handle 401 errors
try {
  const response = await axios.get('/api/v1/plans');
} catch (error) {
  if (error.response?.status === 401) {
    authStore.logout();  // Auto-handled by interceptor
  }
}
```

**7. RTL (Right-to-Left) Support**
```vue
<!-- ❌ WRONG - Hardcoded LTR -->
<div class="text-left">

<!-- ✅ CORRECT - Use RTL-aware classes -->
<div dir="rtl" class="text-right">
```

**8. Git Branch Naming**
```bash
# ❌ WRONG - Wrong branch pattern
git push origin feature/new-feature

# ✅ CORRECT - Must use claude/ prefix and session ID suffix
git push -u origin claude/claude-md-mkpc7c5eduntnxzw-oxraL
```

### 🔒 Security Best Practices

**1. Input Validation:**
- Always use FormRequest classes for validation
- Never trust user input
- Sanitize all inputs

**2. SQL Injection Prevention:**
- Always use Eloquent ORM or Query Builder
- Never concatenate user input into raw SQL
- Use parameter binding

**3. XSS Prevention:**
- Always escape output in Blade: `{{ $variable }}`
- In Vue: Use `v-text` or `{{ }}` instead of `v-html` unless necessary

**4. CSRF Protection:**
- Sanctum handles this for API routes
- Ensure all state-changing operations use POST/PUT/DELETE

**5. Rate Limiting:**
- Consider adding rate limiting to AI endpoints
- Protect authentication routes

### 📝 Code Style Guidelines

**PHP (Laravel):**
- Follow PSR-12 coding standards
- Use Laravel Pint for formatting: `./vendor/bin/pint`
- Type-hint parameters and return types
- Use strict comparison (`===`, `!==`)

**JavaScript/Vue:**
- Use ESLint configuration
- Prefer Composition API over Options API
- Use `<script setup>` syntax
- Destructure props and emits

**Database:**
- Use migrations for all schema changes
- Never modify old migrations (create new ones)
- Always add indexes for foreign keys
- Use descriptive column names

---

## Testing & Quality

### Running Tests

```bash
# PHP Unit Tests
php artisan test

# Run specific test file
php artisan test --filter=PlanTest

# With coverage
php artisan test --coverage
```

### Writing Tests

**Feature Test Example:**
```php
// tests/Feature/PlanTest.php
public function test_user_can_create_plan()
{
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/plans', [
            'business_name' => 'Test Business',
            'industry' => 'Technology',
            'marketing_goal' => 'Increase brand awareness',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'data' => ['id', 'business_name', 'user_id']
        ]);

    $this->assertDatabaseHas('marketing_plans', [
        'business_name' => 'Test Business',
        'user_id' => $user->id,
    ]);
}
```

### Code Quality Tools

**Laravel Pint (PHP formatter):**
```bash
./vendor/bin/pint
```

**PHPStan (Static analysis):**
```bash
./vendor/bin/phpstan analyse
```

### Pre-Commit Checklist

Before committing changes:
- [ ] Run tests: `php artisan test`
- [ ] Format code: `./vendor/bin/pint`
- [ ] Build frontend: `npm run build` (for production)
- [ ] Check for console errors
- [ ] Verify authorization checks on new endpoints
- [ ] Update this CLAUDE.md if adding new patterns

---

## Quick Reference

### Essential Commands

```bash
# Development
composer dev              # Run all services (server, queue, logs, vite)
php artisan serve        # Backend only (port 8000)
npm run dev             # Frontend only (Vite)

# Database
php artisan migrate           # Run migrations
php artisan migrate:fresh --seed  # Fresh database with seed data
php artisan db:seed          # Run seeders

# Cache
php artisan cache:clear      # Clear application cache
php artisan config:clear     # Clear config cache
php artisan route:clear      # Clear route cache
php artisan view:clear       # Clear view cache

# Code Generation
php artisan make:model ModelName -mcr  # Model + migration + controller + resource
php artisan make:controller API/ControllerName --api
php artisan make:request RequestName
php artisan make:policy PolicyName --model=ModelName
php artisan make:migration create_table_name

# Testing
php artisan test
./vendor/bin/pint           # Format code
```

### File Path Quick Reference

| Purpose | Path |
|---------|------|
| API Routes | `routes/api.php` |
| Controllers | `app/Http/Controllers/API/` |
| Models | `app/Models/` |
| Services | `app/Services/` |
| Policies | `app/Policies/` |
| Requests | `app/Http/Requests/` |
| Migrations | `database/migrations/` |
| Vue Router | `resources/js/router/index.js` |
| Pinia Store | `resources/js/stores/` |
| Vue Views | `resources/js/views/` |
| Vue Components | `resources/js/components/` |
| Tailwind Config | `tailwind.config.js` |
| Vite Config | `vite.config.js` |

### Model Statistics

- **Total Models:** 32
- **Total Migrations:** 21
- **Total Controllers:** 26
- **Total Vue Components:** 89
- **Total Vue Views:** 54
- **API Endpoints:** 100+

---

## Additional Resources

### Documentation Files

- `README.md` - Project overview and setup (Arabic)
- `DEVELOPMENT_COMPLETE.md` - Development completion report
- `FULL_IMPLEMENTATION_PLAN.md` - Original implementation plan
- `ANALYSIS_AND_IMPLEMENTATION_REPORT.md` - Detailed analysis
- `docs/` - Additional documentation

### External Documentation

- [Laravel 11 Documentation](https://laravel.com/docs/11.x)
- [Vue 3 Documentation](https://vuejs.org/guide/)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum)
- [Google Gemini API](https://ai.google.dev/docs)

---

## Changelog

- **2026-01-22:** Initial CLAUDE.md created with comprehensive codebase analysis

---

## Notes for AI Assistants

When working with this codebase:

1. **Always check authentication and authorization** before implementing features
2. **Respect the subscription tier limits** and AI credit system
3. **Use the service layer** for complex business logic
4. **Follow Laravel and Vue best practices**
5. **Test API endpoints** with proper Bearer token
6. **Update completion percentages** when modifying plan sections
7. **Use soft deletes** for user-facing data
8. **Handle Arabic (RTL) properly** in UI components
9. **Check AI credits** before making Gemini API calls
10. **Follow the git branch naming convention** (claude/* with session ID)

This platform is production-ready and actively used. Make changes carefully and always test thoroughly.
