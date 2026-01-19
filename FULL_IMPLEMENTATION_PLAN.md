# 📘 الخطة الكاملة والتفصيلية لبناء منصة خطّط التسويقية

**المشروع**: Khattit Marketing Platform
**Framework**: Laravel 11 + Vue.js 3
**Database**: MySQL
**AI**: Gemini API

---

## 📋 جدول المحتويات

1. [Phase 1: الإعداد الأولي](#phase-1-الإعداد-الأولي)
2. [Phase 2: قاعدة البيانات](#phase-2-قاعدة-البيانات)
3. [Phase 3: Models & Relationships](#phase-3-models--relationships)
4. [Phase 4: Controllers & APIs](#phase-4-controllers--apis)
5. [Phase 5: Frontend Structure](#phase-5-frontend-structure)
6. [Phase 6: Gemini AI Integration](#phase-6-gemini-ai-integration)
7. [Phase 7: Payment System](#phase-7-payment-system)
8. [Phase 8: Ads System](#phase-8-ads-system)
9. [Phase 9: Dashboards](#phase-9-dashboards)
10. [Phase 10: Security](#phase-10-security)
11. [Phase 11: Testing](#phase-11-testing)
12. [Phase 12: Performance](#phase-12-performance)
13. [Phase 13: Deployment](#phase-13-deployment)

---

## ✅ Phase 1: الإعداد الأولي
**المدة**: 2-3 أيام
**الحالة**: ✅ مكتمل

### 1.1 إنشاء المشروع

```bash
composer create-project laravel/laravel khattit-marketing-platform
cd khattit-marketing-platform

# تثبيت الحزم الأساسية
composer require laravel/sanctum laravel/cashier barryvdh/laravel-dompdf
composer require maatwebsite/excel spatie/laravel-permission predis/predis

# حزم التطوير
composer require --dev laravel/pint pestphp/pest pestphp/pest-plugin-laravel
```

### 1.2 ملف .env

```ini
APP_NAME="Khattit Marketing Platform"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/learn

DB_CONNECTION=mysql
DB_DATABASE=khattit
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

GEMINI_API_KEY=your_gemini_key
STRIPE_KEY=pk_test_...
MOYASAR_API_KEY=your_moyasar_key
```

### 1.3 Frontend Setup

```bash
npm install vue@next @vitejs/plugin-vue
npm install vue-router@4 pinia axios
npm install @headlessui/vue @heroicons/vue chart.js
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```

### 1.4 هيكل المجلدات

```
app/
├── Models/
├── Http/
│   ├── Controllers/API/
│   │   ├── AuthController.php
│   │   ├── PlanController.php
│   │   ├── AIController.php
│   │   └── Admin/
│   ├── Middleware/
│   ├── Requests/
│   └── Resources/
├── Services/
│   ├── AI/GeminiService.php
│   ├── Payment/StripeService.php
│   └── Export/PDFExportService.php
└── Jobs/
```

---

## ✅ Phase 2: قاعدة البيانات
**المدة**: 3-4 أيام
**الحالة**: ✅ مكتمل

### 2.1 جدول Users (Enhanced)

```sql
-- إضافة حقول الاشتراكات للـ users table
ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER email;
ALTER TABLE users ADD COLUMN avatar_url TEXT;
ALTER TABLE users ADD COLUMN role ENUM('admin', 'user', 'advertiser') DEFAULT 'user';
ALTER TABLE users ADD COLUMN subscription_tier ENUM('free', 'basic', 'pro', 'enterprise') DEFAULT 'free';
ALTER TABLE users ADD COLUMN subscription_expires_at TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN ai_credits_remaining INT DEFAULT 3;
ALTER TABLE users ADD COLUMN ai_credits_reset_at TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN onboarding_completed BOOLEAN DEFAULT FALSE;
ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN deleted_at TIMESTAMP NULL;
```

### 2.2 جدول marketing_plans

```sql
CREATE TABLE marketing_plans (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    title VARCHAR(255) NOT NULL,
    status ENUM('draft', 'in_progress', 'completed', 'archived') DEFAULT 'draft',
    year INT NOT NULL,

    -- Business Data
    business_name VARCHAR(255),
    industry VARCHAR(100),
    target_audience JSON,
    marketing_goal TEXT,
    budget_monthly DECIMAL(10,2),

    -- Progress Tracking
    completion_percentage TINYINT DEFAULT 0,
    ai_score TINYINT DEFAULT 0,
    last_ai_review_at TIMESTAMP NULL,
    sections_completed JSON,

    -- Sharing
    is_public BOOLEAN DEFAULT FALSE,
    share_token VARCHAR(64) UNIQUE,
    view_count INT DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_plans (user_id, status),
    INDEX idx_share (share_token),
    INDEX idx_industry (industry)
);
```

### 2.3 جدول plan_sections

```sql
CREATE TABLE plan_sections (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    plan_id BIGINT NOT NULL,
    section_type ENUM(
        'personal_card', 'diagnosis', 'target_audience',
        'core_message', 'offer_stack', 'channels',
        'funnel', 'followup', 'metrics',
        'competitor_analysis', 'content_plan', 'budget_breakdown'
    ) NOT NULL,
    section_data JSON NOT NULL,
    ai_suggestions JSON,
    is_completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (plan_id) REFERENCES marketing_plans(id) ON DELETE CASCADE,
    INDEX idx_plan_sections (plan_id, section_type),
    UNIQUE KEY unique_plan_section (plan_id, section_type)
);
```

### 2.4 جدول ai_conversations

```sql
CREATE TABLE ai_conversations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    plan_id BIGINT NULL,
    session_id VARCHAR(64) NOT NULL,
    message_type ENUM('user', 'assistant') NOT NULL,
    message_text TEXT NOT NULL,
    context JSON,
    ai_model VARCHAR(50) DEFAULT 'gemini-pro',
    tokens_used INT,
    processing_time_ms INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES marketing_plans(id) ON DELETE SET NULL,
    INDEX idx_session (session_id),
    INDEX idx_user_conversations (user_id, created_at DESC)
);
```

### 2.5 جدول subscriptions

```sql
CREATE TABLE subscriptions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    tier ENUM('free', 'basic', 'pro', 'enterprise') NOT NULL,
    status ENUM('active', 'cancelled', 'expired', 'paused') DEFAULT 'active',
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'SAR',
    billing_cycle ENUM('monthly', 'yearly') DEFAULT 'monthly',
    starts_at TIMESTAMP NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    auto_renew BOOLEAN DEFAULT TRUE,
    stripe_subscription_id VARCHAR(255),
    moyasar_subscription_id VARCHAR(255),
    cancelled_at TIMESTAMP NULL,
    cancellation_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_subscription (user_id, status),
    INDEX idx_expiry (expires_at)
);
```

### 2.6 جدول transactions

```sql
CREATE TABLE transactions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    subscription_id BIGINT NULL,
    type ENUM('subscription', 'template_purchase', 'credit_purchase', 'refund') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'SAR',
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_method ENUM('stripe', 'moyasar', 'tap', 'free') NOT NULL,
    payment_gateway_id VARCHAR(255),
    payment_details JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE SET NULL,
    INDEX idx_user_transactions (user_id, created_at DESC),
    INDEX idx_status (status)
);
```

### 2.7 جدول plan_templates

```sql
CREATE TABLE plan_templates (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    industry VARCHAR(100),
    template_data JSON NOT NULL,
    thumbnail_url TEXT,
    is_premium BOOLEAN DEFAULT FALSE,
    price DECIMAL(8,2) DEFAULT 0,
    usage_count INT DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 0,
    created_by BIGINT NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_industry (industry),
    INDEX idx_premium (is_premium, is_featured)
);
```

### 2.8 جدول ad_campaigns

```sql
CREATE TABLE ad_campaigns (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    advertiser_id BIGINT NOT NULL,
    campaign_name VARCHAR(255) NOT NULL,
    status ENUM('draft', 'pending_review', 'active', 'paused', 'completed', 'rejected') DEFAULT 'draft',
    ad_type ENUM('banner', 'native', 'sponsored_template', 'sponsored_suggestion') NOT NULL,

    -- Ad Content
    title VARCHAR(255),
    description TEXT,
    image_url TEXT,
    cta_text VARCHAR(50),
    cta_url TEXT NOT NULL,

    -- Targeting
    target_subscription_tiers JSON,
    target_industries JSON,
    target_locations JSON,
    ad_placements JSON,

    -- Budget
    budget_total DECIMAL(10,2) NOT NULL,
    budget_spent DECIMAL(10,2) DEFAULT 0,
    cost_per_click DECIMAL(6,2),
    cost_per_impression DECIMAL(6,2),
    starts_at TIMESTAMP NOT NULL,
    ends_at TIMESTAMP NOT NULL,

    -- Statistics
    impressions INT DEFAULT 0,
    clicks INT DEFAULT 0,
    conversions INT DEFAULT 0,

    -- Review
    reviewed_by BIGINT NULL,
    reviewed_at TIMESTAMP NULL,
    rejection_reason TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (advertiser_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status, starts_at, ends_at),
    INDEX idx_advertiser (advertiser_id)
);
```

### 2.9 جدول ad_impressions

```sql
CREATE TABLE ad_impressions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    campaign_id BIGINT NOT NULL,
    user_id BIGINT NULL,
    placement VARCHAR(100),
    action ENUM('impression', 'click', 'conversion') NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    referrer TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (campaign_id) REFERENCES ad_campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_campaign_stats (campaign_id, action, created_at)
);
```

### 2.10 جدول notifications

```sql
CREATE TABLE notifications (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    action_url TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_notifications (user_id, is_read, created_at DESC)
);
```

### 2.11 جدول system_settings

```sql
CREATE TABLE system_settings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- قيم افتراضية
INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES
('ai_credits_free_tier', '3', 'number', 'عدد استخدامات AI المجانية شهرياً'),
('max_plans_free', '1', 'number', 'عدد الخطط للمستخدم المجاني'),
('max_plans_basic', '3', 'number', 'عدد الخطط للاشتراك الأساسي'),
('subscription_basic_price_monthly', '99', 'number', 'سعر الاشتراك الأساسي (ريال)'),
('subscription_pro_price_monthly', '299', 'number', 'سعر الاشتراك الاحترافي (ريال)'),
('subscription_enterprise_price_monthly', '999', 'number', 'سعر الاشتراك المؤسسي (ريال)'),
('maintenance_mode', 'false', 'boolean', 'وضع الصيانة'),
('registration_enabled', 'true', 'boolean', 'تفعيل التسجيل');
```

---

## ✅ Phase 3: Models & Relationships
**المدة**: 3-4 أيام
**الحالة**: ✅ مكتمل

### 3.1 User Model

انظر: `app/Models/User.php` - تم إنشاؤه ✅

**الميزات الرئيسية**:
- Subscription management methods
- AI credits tracking
- Role-based permissions
- Relationships مع جميع النماذج الأخرى

### 3.2 MarketingPlan Model

انظر: `app/Models/MarketingPlan.php` - تم إنشاؤه ✅

**الميزات الرئيسية**:
- Share token generation
- Progress tracking
- Section management
- AI evaluation integration

### 3.3 Models الأخرى

تم إنشاء جميع النماذج التالية بنجاح ✅:
- `PlanSection.php` - أقسام الخطة
- `AIConversation.php` - محادثات AI
- `Subscription.php` - الاشتراكات
- `Transaction.php` - المعاملات المالية
- `PlanTemplate.php` - قوالب الخطط
- `AdCampaign.php` - الحملات الإعلانية
- `AdImpression.php` - تتبع الإعلانات
- `Notification.php` - الإشعارات

---

## 🔄 Phase 4: Controllers & API Endpoints
**المدة**: 5-7 أيام
**الحالة**: 🔄 قيد التنفيذ (AuthController مكتمل)

### 4.1 AuthController ✅

**الموقع**: `app/Http/Controllers/API/AuthController.php`

**Endpoints**:
- `POST /api/v1/register` - تسجيل مستخدم جديد
- `POST /api/v1/login` - تسجيل الدخول
- `GET /api/v1/me` - بيانات المستخدم الحالي
- `PUT /api/v1/me` - تحديث الملف الشخصي
- `POST /api/v1/me/avatar` - رفع صورة
- `POST /api/v1/logout` - تسجيل الخروج
- `POST /api/v1/forgot-password` - نسيت كلمة المرور

### 4.2 PlanController (المطلوب)

**الموقع**: `app/Http/Controllers/API/PlanController.php`

```php
<?php
// سيتم إنشاؤه في الرد التالي

class PlanController extends Controller
{
    public function index(Request $request);        // GET /api/v1/plans
    public function store(Request $request);        // POST /api/v1/plans
    public function show(MarketingPlan $plan);      // GET /api/v1/plans/{plan}
    public function update(Request $request, MarketingPlan $plan);  // PUT /api/v1/plans/{plan}
    public function destroy(MarketingPlan $plan);   // DELETE /api/v1/plans/{plan}

    // Operations
    public function getSections(MarketingPlan $plan);
    public function updateSection(Request $request, MarketingPlan $plan, string $sectionType);
    public function duplicate(Request $request, MarketingPlan $plan);
    public function archive(Request $request, MarketingPlan $plan);
    public function generateShareLink(Request $request, MarketingPlan $plan);
    public function revokeShareLink(Request $request, MarketingPlan $plan);
    public function showShared(string $token);

    // Export
    public function exportPdf(MarketingPlan $plan);
    public function exportDocx(MarketingPlan $plan);
    public function exportExcel(MarketingPlan $plan);

    // AI Evaluation
    public function aiEvaluate(Request $request, MarketingPlan $plan);
}
```

### 4.3 AIController (المطلوب)

**الموقع**: `app/Http/Controllers/API/AIController.php`

```php
<?php
// سيتم إنشاؤه في الرد التالي

class AIController extends Controller
{
    public function chat(Request $request);                  // POST /api/v1/ai/chat
    public function suggestAudience(Request $request);       // POST /api/v1/ai/suggestions/audience
    public function improveMessage(Request $request);        // POST /api/v1/ai/suggestions/message
    public function analyzeCompetitors(Request $request);    // POST /api/v1/ai/analysis/competitors
    public function generateContentPlan(Request $request);   // POST /api/v1/ai/generate/content-plan
    public function getCredits(Request $request);            // GET /api/v1/ai/credits
}
```

### 4.4 Controllers الأخرى (المطلوبة)

- `TemplateController` - إدارة القوالب
- `SubscriptionController` - إدارة الاشتراكات
- `AnalyticsController` - التحليلات
- `NotificationController` - الإشعارات
- `Admin/DashboardController` - لوحة الأدمن
- `Admin/UserController` - إدارة المستخدمين
- `Admin/AdCampaignController` - إدارة الإعلانات
- `Advertiser/CampaignController` - حملات المعلنين

---

## ⏳ Phase 5: Frontend Structure
**المدة**: 4-5 أيام
**الحالة**: ⏳ معلق

### 5.1 هيكل Frontend

```
resources/
├── js/
│   ├── app.js
│   ├── router/
│   │   └── index.js
│   ├── stores/
│   │   ├── auth.js
│   │   ├── plan.js
│   │   ├── ai.js
│   │   └── notification.js
│   ├── components/
│   │   ├── Layout/
│   │   │   ├── AppLayout.vue
│   │   │   ├── Header.vue
│   │   │   ├── Sidebar.vue
│   │   │   └── Footer.vue
│   │   ├── Auth/
│   │   │   ├── LoginForm.vue
│   │   │   ├── RegisterForm.vue
│   │   │   └── ForgotPassword.vue
│   │   ├── Plan/
│   │   │   ├── PlanBuilder.vue
│   │   │   ├── PlanSection.vue
│   │   │   ├── PlanProgress.vue
│   │   │   ├── SectionPersonalCard.vue
│   │   │   ├── SectionTargetAudience.vue
│   │   │   ├── SectionCoreMessage.vue
│   │   │   └── ...
│   │   ├── AI/
│   │   │   ├── AIChatBox.vue
│   │   │   ├── AISuggestions.vue
│   │   │   └── AICreditsIndicator.vue
│   │   ├── Template/
│   │   │   ├── TemplateLibrary.vue
│   │   │   ├── TemplateCard.vue
│   │   │   └── TemplatePreview.vue
│   │   └── Admin/
│   │       ├── AdminDashboard.vue
│   │       ├── UserManagement.vue
│   │       ├── AdReviewQueue.vue
│   │       └── SystemSettings.vue
│   └── views/
│       ├── Auth/
│       │   ├── Login.vue
│       │   └── Register.vue
│       ├── Dashboard/
│       │   └── Index.vue
│       ├── Plans/
│       │   ├── Index.vue
│       │   ├── Create.vue
│       │   ├── Edit.vue
│       │   └── Show.vue
│       └── Admin/
│           ├── Dashboard.vue
│           ├── Users.vue
│           └── Ads.vue
└── css/
    └── app.css
```

### 5.2 Vue Router

```javascript
// resources/js/router/index.js
import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  // Public Routes
  { path: '/', name: 'home', component: () => import('../views/Home.vue') },
  { path: '/login', name: 'login', component: () => import('../views/Auth/Login.vue') },
  { path: '/register', name: 'register', component: () => import('../views/Auth/Register.vue') },

  // Protected Routes
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('../views/Dashboard/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/plans',
    name: 'plans',
    component: () => import('../views/Plans/Index.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/plans/create',
    name: 'plans.create',
    component: () => import('../views/Plans/Create.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/plans/:id',
    name: 'plans.show',
    component: () => import('../views/Plans/Show.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/plans/:id/edit',
    name: 'plans.edit',
    component: () => import('../views/Plans/Edit.vue'),
    meta: { requiresAuth: true }
  },

  // Admin Routes
  {
    path: '/admin',
    name: 'admin',
    component: () => import('../views/Admin/Dashboard.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation Guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login' })
  } else if (to.meta.requiresAdmin && !authStore.user?.isAdmin) {
    next({ name: 'dashboard' })
  } else {
    next()
  }
})

export default router
```

### 5.3 Pinia Stores

```javascript
// resources/js/stores/auth.js
import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    isAuthenticated: false,
  }),

  actions: {
    async login(credentials) {
      const response = await axios.post('/api/v1/login', credentials)
      this.token = response.data.data.token
      this.user = response.data.data.user
      this.isAuthenticated = true
      localStorage.setItem('token', this.token)
      axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
    },

    async register(data) {
      const response = await axios.post('/api/v1/register', data)
      this.token = response.data.data.token
      this.user = response.data.data.user
      this.isAuthenticated = true
      localStorage.setItem('token', this.token)
      axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
    },

    async logout() {
      await axios.post('/api/v1/logout')
      this.user = null
      this.token = null
      this.isAuthenticated = false
      localStorage.removeItem('token')
      delete axios.defaults.headers.common['Authorization']
    },

    async fetchUser() {
      if (this.token) {
        const response = await axios.get('/api/v1/me')
        this.user = response.data.data.user
        this.isAuthenticated = true
      }
    }
  }
})
```

---

## ⏳ Phase 6: Gemini AI Integration
**المدة**: 4-5 أيام
**الحالة**: ⏳ معلق

### 6.1 GeminiService

```php
<?php
// app/Services/AI/GeminiService.php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $model = 'gemini-1.5-pro';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    /**
     * توليد اقتراحات للشريحة المستهدفة
     */
    public function generateTargetAudienceSuggestions(array $businessData): array
    {
        $prompt = $this->buildTargetAudiencePrompt($businessData);
        return $this->callGemini($prompt);
    }

    /**
     * تحسين الرسالة التسويقية
     */
    public function improveMarketingMessage(string $message, array $context): array
    {
        $prompt = <<<PROMPT
أنت خبير تسويق استراتيجي. قم بتحليل وتحسين الرسالة التسويقية التالية:

الرسالة الحالية: "{$message}"

السياق:
- الصناعة: {$context['industry']}
- الجمهور المستهدف: {$context['target_audience']}

قدم:
1. تقييم الرسالة (من 10)
2. نقاط القوة
3. نقاط الضعف
4. 3 نسخ محسّنة
5. توصيات

الرد بصيغة JSON.
PROMPT;

        return $this->callGemini($prompt);
    }

    /**
     * تحليل المنافسين
     */
    public function analyzeCompetitors(string $industry, string $location): array
    {
        $prompt = <<<PROMPT
أنت محلل سوق متخصص في {$industry} في {$location}.

قدم تحليلاً شاملاً:
1. أبرز المنافسين (5-7)
2. نقاط القوة والضعف
3. استراتيجيات التسعير
4. اتجاهات السوق
5. فرص التميز

الرد بصيغة JSON منظمة.
PROMPT;

        return $this->callGemini($prompt);
    }

    /**
     * توليد خطة محتوى شهرية
     */
    public function generateContentPlan(array $planData): array
    {
        $prompt = $this->buildContentPlanPrompt($planData);
        return $this->callGemini($prompt, ['temperature' => 0.8]);
    }

    /**
     * تقييم جودة الخطة
     */
    public function evaluatePlanQuality(array $planSections): array
    {
        $prompt = <<<PROMPT
قم بتقييم الخطة التسويقية:

{$this->formatPlanForEvaluation($planSections)}

قدم:
1. درجة إجمالية (من 100)
2. تقييم كل قسم (من 10)
3. نقاط القوة
4. المجالات التي تحتاج تحسين
5. توصيات عملية
6. أولويات التنفيذ

الرد بصيغة JSON.
PROMPT;

        return $this->callGemini($prompt);
    }

    /**
     * Chat Contextual
     */
    public function chat(string $userMessage, array $conversationHistory, array $userContext): array
    {
        $prompt = $this->buildChatPrompt($userMessage, $conversationHistory, $userContext);
        return $this->callGemini($prompt);
    }

    /**
     * Core Gemini API Call
     */
    private function callGemini(string $prompt, array $options = []): array
    {
        $cacheKey = 'gemini_' . md5($prompt);

        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        try {
            $startTime = microtime(true);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => $options['temperature'] ?? 0.7,
                    'maxOutputTokens' => $options['max_tokens'] ?? 2000,
                    'topP' => 0.95,
                    'topK' => 40
                ],
            ]);

            $processingTime = (microtime(true) - $startTime) * 1000;

            if ($response->successful()) {
                $result = $response->json();
                $aiResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

                $parsedResponse = $this->tryParseJson($aiResponse);

                Cache::put($cacheKey, $parsedResponse, 3600);

                $this->logAIUsage($prompt, $parsedResponse, $processingTime);

                return $parsedResponse;
            }

            throw new \Exception('Gemini API error: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Gemini AI Error', [
                'message' => $e->getMessage(),
                'prompt' => substr($prompt, 0, 200)
            ]);

            return $this->getFallbackResponse();
        }
    }

    private function tryParseJson(string $response): array
    {
        $cleaned = preg_replace('/```json\n?|\n?```/', '', $response);
        $cleaned = trim($cleaned);

        $decoded = json_decode($cleaned, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return ['text' => $response];
    }

    private function logAIUsage(string $prompt, array $response, float $processingTime): void
    {
        $tokensUsed = (strlen($prompt) + strlen(json_encode($response))) / 4;

        AIConversation::create([
            'user_id' => auth()->id(),
            'message_type' => 'assistant',
            'message_text' => json_encode($response),
            'ai_model' => $this->model,
            'tokens_used' => $tokensUsed,
            'processing_time_ms' => $processingTime
        ]);

        if (auth()->user()->subscription_tier === 'free') {
            auth()->user()->deductAICredit();
        }
    }

    private function getFallbackResponse(): array
    {
        return [
            'error' => true,
            'message' => 'عذراً، حدث خطأ مؤقت. يرجى المحاولة مرة أخرى.',
        ];
    }
}
```

---

## ⏳ Phase 7-13: باقي المراحل

**سيتم توثيقها بالتفصيل في الردود التالية**

---

## 📊 ملخص التقدم الحالي

| Phase | الاسم | الحالة | التقدم |
|-------|-------|--------|--------|
| 1 | الإعداد الأولي | ✅ مكتمل | 100% |
| 2 | قاعدة البيانات | ✅ مكتمل | 100% |
| 3 | Models | ✅ مكتمل | 100% |
| 4 | Controllers & APIs | 🔄 قيد التنفيذ | 15% |
| 5 | Frontend | ⏳ معلق | 0% |
| 6 | AI Integration | ⏳ معلق | 0% |
| 7 | Payment System | ⏳ معلق | 0% |
| 8 | Ads System | ⏳ معلق | 0% |
| 9 | Dashboards | ⏳ معلق | 0% |
| 10 | Security | ⏳ معلق | 0% |
| 11 | Testing | ⏳ معلق | 0% |
| 12 | Performance | ⏳ معلق | 0% |
| 13 | Deployment | ⏳ معلق | 0% |

**التقدم الإجمالي**: 23.8%

---

## 🔗 الروابط المفيدة

- [Laravel Documentation](https://laravel.com/docs/11.x)
- [Vue.js 3 Guide](https://vuejs.org/guide/introduction.html)
- [Gemini API Docs](https://ai.google.dev/docs)
- [Stripe API Reference](https://stripe.com/docs/api)
- [Tailwind CSS](https://tailwindcss.com/docs)

---

**آخر تحديث**: 2024-01-18
**الإصدار**: 1.0.0
**المطور**: فريق خطّط 🇸🇦
