<div dir="rtl">

# توثيق API

## نظرة عامة

جميع نقاط النهاية (Endpoints) تستخدم البادئة `/api/v1/`

### المصادقة
النقاط المحمية تتطلب توكن Bearer في الـ Header:
```
Authorization: Bearer {token}
```

---

## 🔓 المسارات العامة (Public)

### التحقق من حالة الخادم
```http
GET /api/v1/health
```
**Response:**
```json
{
  "status": "ok",
  "app": "خطط",
  "version": "1.0.0",
  "timestamp": "2025-01-19T12:00:00+00:00"
}
```

---

### تسجيل مستخدم جديد
```http
POST /api/v1/register
```
**Body:**
```json
{
  "name": "أحمد محمد",
  "email": "ahmed@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```
**Response (201):**
```json
{
  "user": {
    "id": 1,
    "name": "أحمد محمد",
    "email": "ahmed@example.com"
  },
  "token": "1|abc123..."
}
```

---

### تسجيل الدخول
```http
POST /api/v1/login
```
**Body:**
```json
{
  "email": "ahmed@example.com",
  "password": "password123"
}
```
**Response (200):**
```json
{
  "user": {...},
  "token": "2|xyz789..."
}
```

---

### عرض خطة مشتركة
```http
GET /api/v1/plans/shared/{token}
```

---

## 🔒 المسارات المحمية (Protected)

## المستخدم

### جلب بيانات المستخدم الحالي
```http
GET /api/v1/me
```
**Response:**
```json
{
  "id": 1,
  "name": "أحمد محمد",
  "email": "ahmed@example.com",
  "role": "user",
  "ai_credits": 100
}
```

### تحديث البيانات الشخصية
```http
PUT /api/v1/me
```
**Body:**
```json
{
  "name": "أحمد محمد",
  "email": "new@example.com"
}
```

### رفع صورة شخصية
```http
POST /api/v1/me/avatar
Content-Type: multipart/form-data
```
**Body:** `avatar: (file)`

### تسجيل الخروج
```http
POST /api/v1/logout
```

### لوحة المعلومات
```http
GET /api/v1/dashboard
```

---

## 📋 الخطط التسويقية

### جلب جميع الخطط
```http
GET /api/v1/plans
```
**Query Parameters:**
- `page`: رقم الصفحة
- `per_page`: عدد العناصر

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "خطة التسويق 2025",
      "status": "draft",
      "created_at": "2025-01-19T12:00:00Z"
    }
  ],
  "meta": {...}
}
```

### إنشاء خطة جديدة
```http
POST /api/v1/plans
```
**Body:**
```json
{
  "title": "خطة التسويق الجديدة",
  "business_name": "شركتي",
  "industry": "تقنية",
  "target_audience": "الشباب 18-35"
}
```

### عرض خطة محددة
```http
GET /api/v1/plans/{id}
```

### تحديث خطة
```http
PUT /api/v1/plans/{id}
```

### حذف خطة
```http
DELETE /api/v1/plans/{id}
```

### جلب أقسام الخطة
```http
GET /api/v1/plans/{id}/sections
```

### تحديث قسم محدد
```http
PUT /api/v1/plans/{id}/sections/{sectionType}
```

### نسخ خطة
```http
POST /api/v1/plans/{id}/duplicate
```

### أرشفة خطة
```http
POST /api/v1/plans/{id}/archive
```

### إنشاء رابط مشاركة
```http
POST /api/v1/plans/{id}/share
```

### إلغاء رابط المشاركة
```http
DELETE /api/v1/plans/{id}/share
```

### تصدير PDF
```http
GET /api/v1/plans/{id}/export/pdf
```

### تصدير Excel
```http
GET /api/v1/plans/{id}/export/excel
```

### تصدير DOCX
```http
GET /api/v1/plans/{id}/export/docx
```

---

## 🤖 الذكاء الاصطناعي

### محادثة مع المستشار
```http
POST /api/v1/ai/chat
```
**Body:**
```json
{
  "message": "كيف أزيد مبيعاتي؟",
  "plan_id": 1
}
```
**Response:**
```json
{
  "response": "لزيادة مبيعاتك، أنصحك بـ...",
  "credits_remaining": 95
}
```

### إرشادات مجانية
```http
POST /api/v1/ai/guidance
```

### اقتراحات ذكية
```http
POST /api/v1/ai/suggestions
```

### تحليل المحتوى
```http
POST /api/v1/ai/analyze
```

### اقتراحات الجمهور
```http
POST /api/v1/ai/suggestions/audience
```
**Body:**
```json
{
  "business_type": "مطعم",
  "location": "الرياض"
}
```

### تحسين الرسالة التسويقية
```http
POST /api/v1/ai/suggestions/message
```

### تحليل المنافسين
```http
POST /api/v1/ai/analysis/competitors
```

### توليد خطة محتوى
```http
POST /api/v1/ai/generate/content-plan
```

### رصيد الاستخدام
```http
GET /api/v1/ai/credits
```
**Response:**
```json
{
  "credits": 95,
  "max_credits": 100
}
```

---

## 📝 القوالب

### جلب جميع القوالب
```http
GET /api/v1/templates
```

### عرض قالب محدد
```http
GET /api/v1/templates/{id}
```

### استخدام قالب
```http
POST /api/v1/templates/{id}/use
```

---

## 💳 الاشتراكات

### جلب خطط الاشتراك
```http
GET /api/v1/subscriptions
```

### الاشتراك الحالي
```http
GET /api/v1/subscriptions/current
```

### الاشتراك في خطة
```http
POST /api/v1/subscriptions/subscribe
```
**Body:**
```json
{
  "plan": "premium",
  "payment_method": "card"
}
```

### إلغاء الاشتراك
```http
POST /api/v1/subscriptions/cancel
```

### استئناف الاشتراك
```http
POST /api/v1/subscriptions/resume
```

### سجل المعاملات
```http
GET /api/v1/subscriptions/transactions
```

---

## 🔔 الإشعارات

### جلب جميع الإشعارات
```http
GET /api/v1/notifications
```

### الإشعارات غير المقروءة
```http
GET /api/v1/notifications/unread
```

### تحديد كمقروء
```http
POST /api/v1/notifications/{id}/read
```

### تحديد الكل كمقروء
```http
POST /api/v1/notifications/read-all
```

---

## 📚 الدورات والدروس

### جلب جميع الدورات
```http
GET /api/v1/courses
```

### عرض دورة محددة
```http
GET /api/v1/courses/{id}
```

### تقدم الدورة
```http
GET /api/v1/courses/{id}/progress
```

### عرض درس
```http
GET /api/v1/lessons/{id}
```

### إكمال درس
```http
POST /api/v1/lessons/{id}/complete
```

### تحديث التقدم
```http
PUT /api/v1/lessons/{id}/progress
```

---

## 📝 الاختبارات

### عرض اختبار
```http
GET /api/v1/quizzes/{id}
```

### تقديم محاولة
```http
POST /api/v1/quizzes/{id}/attempt
```
**Body:**
```json
{
  "answers": {
    "1": "a",
    "2": "c",
    "3": "b"
  }
}
```

### نتائج المحاولة
```http
GET /api/v1/quizzes/attempts/{attemptId}
```

---

## 🏆 الشهادات

### عرض شهادة
```http
GET /api/v1/certificates/{number}
```

### إنشاء شهادة
```http
POST /api/v1/certificates/generate/{courseId}
```

### تحميل شهادة
```http
GET /api/v1/certificates/{number}/download
```

---

## 🔧 الأدوات

### عرض أداة
```http
GET /api/v1/tools/{id}
```

### استخدام أداة
```http
POST /api/v1/tools/{id}/use
```

### حفظ نتيجة
```http
POST /api/v1/tools/{id}/save
```

---

## 👨‍💼 لوحة الإدارة (Admin)

> تتطلب دور `admin`

### لوحة المعلومات
```http
GET /api/v1/admin/dashboard
```

### إدارة المستخدمين
```http
GET /api/v1/admin/users
POST /api/v1/admin/users
GET /api/v1/admin/users/{id}
PUT /api/v1/admin/users/{id}
DELETE /api/v1/admin/users/{id}
POST /api/v1/admin/users/{id}/suspend
POST /api/v1/admin/users/{id}/activate
```

### إدارة الحملات
```http
GET /api/v1/admin/campaigns
GET /api/v1/admin/campaigns/pending
POST /api/v1/admin/campaigns/{id}/approve
POST /api/v1/admin/campaigns/{id}/reject
```

### الإعدادات
```http
GET /api/v1/admin/settings
PUT /api/v1/admin/settings
POST /api/v1/admin/settings/test-gemini
```

---

## 📢 المعلنين (Advertiser)

> تتطلب دور `advertiser`

### إدارة الحملات
```http
GET /api/v1/advertiser/campaigns
POST /api/v1/advertiser/campaigns
GET /api/v1/advertiser/campaigns/{id}
PUT /api/v1/advertiser/campaigns/{id}
DELETE /api/v1/advertiser/campaigns/{id}
GET /api/v1/advertiser/campaigns/{id}/analytics
POST /api/v1/advertiser/campaigns/{id}/pause
POST /api/v1/advertiser/campaigns/{id}/resume
```

---

## ❌ رموز الأخطاء

| الرمز | المعنى |
|-------|--------|
| 400 | طلب غير صالح |
| 401 | غير مصرح |
| 403 | محظور |
| 404 | غير موجود |
| 422 | خطأ في التحقق |
| 429 | تجاوز الحد المسموح |
| 500 | خطأ في الخادم |

</div>
