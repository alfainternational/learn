<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AIConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AIController extends Controller
{
    /**
     * محادثة مع AI (Chat)
     */
    public function chat(Request $request)
    {
        $user = $request->user();

        if (!$user->hasAICredits()) {
            return response()->json([
                'success' => false,
                'message' => 'لقد استنفدت رصيد AI الخاص بك. قم بالترقية للاشتراك المدفوع للحصول على رصيد غير محدود.',
                'code' => 'NO_AI_CREDITS',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:2000',
            'session_id' => 'nullable|string|max:64',
            'plan_id' => 'nullable|exists:marketing_plans,id',
            'context' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $sessionId = $request->session_id ?? Str::random(64);

        // حفظ رسالة المستخدم
        AIConversation::create([
            'user_id' => $user->id,
            'plan_id' => $request->plan_id,
            'session_id' => $sessionId,
            'message_type' => 'user',
            'message_text' => $request->message,
            'context' => $request->context,
        ]);

        // محاكاة رد AI (سيتم استبدالها بـ Gemini API في Phase 6)
        $aiResponse = $this->generateMockAIResponse($request->message);

        // حفظ رد AI
        AIConversation::create([
            'user_id' => $user->id,
            'plan_id' => $request->plan_id,
            'session_id' => $sessionId,
            'message_type' => 'assistant',
            'message_text' => $aiResponse,
            'ai_model' => 'gemini-pro',
            'tokens_used' => strlen($request->message . $aiResponse) / 4,
            'processing_time_ms' => rand(500, 2000),
        ]);

        // خصم رصيد AI
        $user->deductAICredit();

        return response()->json([
            'success' => true,
            'data' => [
                'response' => $aiResponse,
                'session_id' => $sessionId,
                'credits_remaining' => $user->ai_credits_remaining,
            ],
        ]);
    }

    /**
     * اقتراحات الشريحة المستهدفة
     */
    public function suggestAudience(Request $request)
    {
        $user = $request->user();

        if (!$user->hasAICredits()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد رصيد AI متاح',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:255',
            'industry' => 'required|string|max:100',
            'product_description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // محاكاة اقتراحات AI
        $suggestions = [
            'primary_audience' => [
                'age_range' => '25-40 سنة',
                'gender' => 'الجميع',
                'location' => 'المدن الكبرى (الرياض، جدة، الدمام)',
                'income_level' => 'متوسط إلى مرتفع (10,000 - 25,000 ريال)',
                'occupation' => 'موظفون في القطاع الخاص والحكومي',
            ],
            'pain_points' => [
                'نقص الوقت بسبب الالتزامات المهنية',
                'صعوبة إيجاد حلول عملية وسريعة',
                'الحاجة إلى خدمات موثوقة وذات جودة',
            ],
            'buying_motivations' => [
                'توفير الوقت والجهد',
                'الحصول على جودة عالية',
                'السعر المناسب مقابل القيمة',
            ],
            'digital_behavior' => [
                'platforms' => ['Instagram', 'Twitter', 'LinkedIn'],
                'active_times' => 'المساء (7-11 مساءً)',
                'content_preference' => 'فيديوهات قصيرة وصور جذابة',
            ],
        ];

        $user->deductAICredit();

        return response()->json([
            'success' => true,
            'data' => [
                'suggestions' => $suggestions,
                'credits_remaining' => $user->ai_credits_remaining,
            ],
        ]);
    }

    /**
     * تحسين الرسالة التسويقية
     */
    public function improveMessage(Request $request)
    {
        $user = $request->user();

        if (!$user->hasAICredits()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد رصيد AI متاح',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:500',
            'industry' => 'nullable|string|max:100',
            'target_audience' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // محاكاة تحسين الرسالة
        $improvements = [
            'rating' => 7,
            'strengths' => [
                'الرسالة واضحة ومباشرة',
                'تحتوي على دعوة واضحة للإجراء',
            ],
            'weaknesses' => [
                'تفتقر إلى الأرقام والإحصائيات',
                'لا تبرز الميزة التنافسية',
            ],
            'improved_versions' => [
                'نساعدك في تحقيق أهدافك التسويقية بزيادة مضمونة 40% خلال 60 يوماً فقط',
                'احصل على خطة تسويقية احترافية خلال دقائق، بدلاً من أيام - مع ضمان استرجاع المال',
                'انضم إلى +500 صاحب عمل حققوا نتائج مذهلة باستخدام منصتنا الذكية',
            ],
            'recommendations' => [
                'أضف أرقام محددة لتعزيز المصداقية',
                'اذكر ميزة تنافسية فريدة',
                'استخدم لغة عاطفية للتواصل مع الجمهور',
            ],
        ];

        $user->deductAICredit();

        return response()->json([
            'success' => true,
            'data' => [
                'improvements' => $improvements,
                'credits_remaining' => $user->ai_credits_remaining,
            ],
        ]);
    }

    /**
     * تحليل المنافسين
     */
    public function analyzeCompetitors(Request $request)
    {
        $user = $request->user();

        if (!$user->hasAICredits()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد رصيد AI متاح',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'industry' => 'required|string|max:100',
            'location' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // محاكاة تحليل المنافسين
        $analysis = [
            'competitors' => [
                [
                    'name' => 'المنافس الأول',
                    'strengths' => ['علامة تجارية قوية', 'خدمة عملاء ممتازة'],
                    'weaknesses' => ['أسعار مرتفعة', 'بطء في التسليم'],
                    'pricing_strategy' => 'تسعير مرتفع (Premium)',
                    'market_share' => '30%',
                ],
                [
                    'name' => 'المنافس الثاني',
                    'strengths' => ['أسعار تنافسية', 'تواجد رقمي قوي'],
                    'weaknesses' => ['جودة متوسطة', 'خيارات محدودة'],
                    'pricing_strategy' => 'تسعير متوسط',
                    'market_share' => '25%',
                ],
            ],
            'market_trends' => [
                'زيادة الطلب على الحلول الرقمية',
                'تفضيل العملاء للخدمات السريعة',
                'أهمية التواجد على وسائل التواصل',
            ],
            'opportunities' => [
                'استهداف شريحة الشباب (18-30)',
                'تقديم خدمات مخصصة',
                'الاستفادة من التسويق بالمحتوى',
            ],
            'differentiation_strategies' => [
                'التميز بالسرعة والكفاءة',
                'تقديم ضمانات قوية',
                'استخدام التقنية الحديثة (AI)',
            ],
        ];

        $user->deductAICredit();

        return response()->json([
            'success' => true,
            'data' => [
                'analysis' => $analysis,
                'credits_remaining' => $user->ai_credits_remaining,
            ],
        ]);
    }

    /**
     * توليد خطة محتوى شهرية
     */
    public function generateContentPlan(Request $request)
    {
        $user = $request->user();

        if (!$user->hasAICredits()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد رصيد AI متاح',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'industry' => 'required|string|max:100',
            'target_audience' => 'required|string|max:255',
            'primary_channel' => 'required|string|in:instagram,twitter,linkedin,tiktok,youtube',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // محاكاة خطة المحتوى
        $contentPlan = [
            'weekly_themes' => [
                'الأسبوع 1: التعريف بالعلامة التجارية',
                'الأسبوع 2: تقديم القيمة والنصائح',
                'الأسبوع 3: قصص نجاح العملاء',
                'الأسبوع 4: العروض والتفاعل',
            ],
            'content_types' => [
                'تعليمي' => '40%',
                'ترفيهي' => '30%',
                'تفاعلي' => '20%',
                'ترويجي' => '10%',
            ],
            'sample_posts' => [
                [
                    'day' => 'الأحد',
                    'type' => 'تعليمي',
                    'idea' => '5 نصائح ذهبية لتحسين استراتيجيتك التسويقية',
                    'format' => 'Carousel Post',
                ],
                [
                    'day' => 'الثلاثاء',
                    'type' => 'تفاعلي',
                    'idea' => 'استطلاع رأي: ما أكبر تحدي تواجهه في التسويق؟',
                    'format' => 'Poll/Story',
                ],
                [
                    'day' => 'الخميس',
                    'type' => 'قصة نجاح',
                    'idea' => 'كيف ضاعف أحمد مبيعاته 3 مرات في شهرين',
                    'format' => 'Video Testimonial',
                ],
            ],
            'best_posting_times' => [
                'الأحد-الخميس: 8-9 صباحاً، 7-9 مساءً',
                'الجمعة-السبت: 12-2 ظهراً، 8-10 مساءً',
            ],
        ];

        $user->deductAICredit();

        return response()->json([
            'success' => true,
            'data' => [
                'content_plan' => $contentPlan,
                'credits_remaining' => $user->ai_credits_remaining,
            ],
        ]);
    }

    /**
     * الحصول على رصيد AI المتبقي
     */
    public function getCredits(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'credits_remaining' => $user->ai_credits_remaining,
                'credits_unlimited' => $user->subscription_tier !== 'free',
                'credits_reset_at' => $user->ai_credits_reset_at,
                'subscription_tier' => $user->subscription_tier,
            ],
        ]);
    }

    /**
     * توليد رد AI مؤقت (Mock)
     * سيتم استبداله بـ Gemini API في Phase 6
     */
    private function generateMockAIResponse(string $message): string
    {
        $responses = [
            'شكراً لسؤالك! بناءً على المعلومات التي قدمتها، أنصحك بالتركيز على تضييق الشريحة المستهدفة أولاً.',
            'هذا سؤال ممتاز! من المهم أن تحدد عرض القيمة الفريد الخاص بك قبل البدء في التسويق.',
            'دعني أساعدك في ذلك. يمكنك البدء بتحليل منافسيك لفهم الفجوات في السوق.',
            'رائع! أرى أن لديك فكرة واضحة. الخطوة التالية هي بناء مسار تحويل فعال.',
        ];

        return $responses[array_rand($responses)];
    }
}
