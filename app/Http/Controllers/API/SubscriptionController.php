<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    /**
     * الاشتراك الحالي
     */
    public function current(Request $request)
    {
        $user = $request->user();
        $subscription = $user->currentSubscription;

        $data = [
            'current_tier' => $user->subscription_tier,
            'expires_at' => $user->subscription_expires_at,
            'has_active_subscription' => $user->hasActiveSubscription(),
            'subscription' => $subscription,
        ];

        // إضافة معلومات الخطة
        if ($subscription) {
            $data['days_remaining'] = $subscription->daysUntilExpiry();
            $data['auto_renew'] = $subscription->auto_renew;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * الترقية للاشتراك المدفوع
     */
    public function upgrade(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'tier' => 'required|in:basic,pro,enterprise',
            'billing_cycle' => 'required|in:monthly,yearly',
            'payment_method' => 'required|in:stripe,moyasar',
            'payment_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // الأسعار
        $prices = [
            'basic' => ['monthly' => 99, 'yearly' => 990],
            'pro' => ['monthly' => 299, 'yearly' => 2990],
            'enterprise' => ['monthly' => 999, 'yearly' => 9990],
        ];

        $tier = $request->tier;
        $billingCycle = $request->billing_cycle;
        $amount = $prices[$tier][$billingCycle];

        // معالجة الدفع (سيتم تنفيذها في Phase 7)
        // $paymentResult = $this->processPayment($request->payment_method, $request->payment_token, $amount);

        // محاكاة نجاح الدفع
        $startsAt = now();
        $expiresAt = $billingCycle === 'monthly' ? now()->addMonth() : now()->addYear();

        // إلغاء الاشتراك الحالي إن وجد
        if ($currentSubscription = $user->currentSubscription) {
            $currentSubscription->update(['status' => 'cancelled']);
        }

        // إنشاء اشتراك جديد
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'tier' => $tier,
            'status' => 'active',
            'amount' => $amount,
            'currency' => 'SAR',
            'billing_cycle' => $billingCycle,
            'starts_at' => $startsAt,
            'expires_at' => $expiresAt,
            'auto_renew' => true,
        ]);

        // تحديث بيانات المستخدم
        $user->update([
            'subscription_tier' => $tier,
            'subscription_expires_at' => $expiresAt,
            'ai_credits_remaining' => null, // رصيد غير محدود
        ]);

        // تسجيل المعاملة
        Transaction::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'type' => 'subscription',
            'amount' => $amount,
            'currency' => 'SAR',
            'status' => 'completed',
            'payment_method' => $request->payment_method,
            'payment_gateway_id' => 'mock_' . uniqid(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم الاشتراك بنجاح!',
            'data' => [
                'subscription' => $subscription,
                'user' => $user->fresh(),
            ],
        ], 201);
    }

    /**
     * إلغاء الاشتراك
     */
    public function cancel(Request $request)
    {
        $user = $request->user();
        $subscription = $user->currentSubscription;

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد اشتراك نشط',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // إلغاء الاشتراك
        $subscription->cancel($request->reason);

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الاشتراك. سيستمر حتى تاريخ انتهاء الصلاحية الحالي.',
        ]);
    }

    /**
     * قائمة الفواتير
     */
    public function invoices(Request $request)
    {
        $transactions = $request->user()
            ->transactions()
            ->with('subscription')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    /**
     * تحديث طريقة الدفع
     */
    public function updatePaymentMethod(Request $request)
    {
        $user = $request->user();
        $subscription = $user->currentSubscription;

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد اشتراك نشط',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:stripe,moyasar',
            'payment_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // تحديث طريقة الدفع (سيتم تنفيذها في Phase 7)
        // $this->updatePaymentMethodInGateway($subscription, $request->payment_method, $request->payment_token);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث طريقة الدفع بنجاح',
        ]);
    }

    /**
     * الحصول على خطط الأسعار المتاحة
     */
    public function pricingPlans()
    {
        $plans = [
            [
                'tier' => 'free',
                'name' => 'مجاني',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'features' => [
                    'خطة تسويقية واحدة',
                    '3 استخدامات AI شهرياً',
                    'القوالب المجانية',
                    'تصدير PDF',
                ],
                'limitations' => [
                    'بدون دعم فني مباشر',
                    'بدون ميزات متقدمة',
                ],
            ],
            [
                'tier' => 'basic',
                'name' => 'أساسي',
                'price_monthly' => 99,
                'price_yearly' => 990,
                'features' => [
                    '3 خطط تسويقية',
                    'استخدام AI غير محدود',
                    'جميع القوالب',
                    'تصدير PDF, Word, Excel',
                    'دعم فني عبر البريد',
                ],
                'popular' => false,
            ],
            [
                'tier' => 'pro',
                'name' => 'احترافي',
                'price_monthly' => 299,
                'price_yearly' => 2990,
                'features' => [
                    'خطط غير محدودة',
                    'استخدام AI غير محدود',
                    'جميع القوالب Premium',
                    'تصدير بجميع الصيغ',
                    'تحليلات متقدمة',
                    'دعم فني أولوية',
                    'إزالة العلامة المائية',
                ],
                'popular' => true,
            ],
            [
                'tier' => 'enterprise',
                'name' => 'مؤسسات',
                'price_monthly' => 999,
                'price_yearly' => 9990,
                'features' => [
                    'كل ميزات الخطة الاحترافية',
                    'فرق متعددة (Multi-user)',
                    'White-label (علامتك التجارية)',
                    'API للمطورين',
                    'دعم مخصص 24/7',
                    'تدريب وورش عمل',
                ],
                'contact_sales' => true,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => ['plans' => $plans],
        ]);
    }
}
