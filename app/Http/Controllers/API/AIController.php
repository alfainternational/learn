<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AI\GeminiService;
use App\Models\AIConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AIController extends Controller
{
    protected GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * General chat endpoint
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'plan_id' => 'nullable|exists:marketing_plans,id',
            'session_id' => 'nullable|string'
        ]);

        $user = $request->user();

        // Check credits
        if (!$user->hasAICredits()) {
            return response()->json([
                'success' => false,
                'message' => 'لقد استنفدت رصيدك من استخدامات الذكاء الاصطناعي. يرجى الترقية للاشتراك المدفوع.',
                'code' => 'NO_CREDITS'
            ], 403);
        }

        $sessionId = $request->session_id ?? Str::uuid();

        // Get conversation history
        $history = AIConversation::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse()
            ->map(fn($conv) => [
                'role' => $conv->message_type === 'user' ? 'user' : 'assistant',
                'content' => $conv->message_text
            ])
            ->toArray();

        // Get user context
        $context = [
            'user_name' => $user->name,
            'subscription_tier' => $user->subscription_tier
        ];

        // Call AI
        $response = $this->gemini->chat($request->message, $history, $context);

        // Save conversation
        $this->saveConversation($user->id, $sessionId, 'user', $request->message, $request->plan_id);
        
        if ($response['success']) {
            $assistantMessage = is_array($response['content']) 
                ? json_encode($response['content'], JSON_UNESCAPED_UNICODE)
                : $response['content'];

            $this->saveConversation(
                $user->id, 
                $sessionId, 
                'assistant', 
                $assistantMessage,
                $request->plan_id,
                $response['tokens_used'] ?? 0,
                $response['processing_time_ms'] ?? 0
            );

            // Deduct credit
            $user->deductAICredit();
        }

        return response()->json([
            'success' => $response['success'],
            'data' => [
                'message' => $response['content'],
                'session_id' => $sessionId,
                'credits_remaining' => $user->fresh()->ai_credits_remaining,
                'demo_mode' => $response['demo_mode'] ?? false
            ],
            'error' => $response['error'] ?? null
        ]);
    }

    /**
     * Get audience suggestions
     */
    public function suggestAudience(Request $request)
    {
        $request->validate([
            'industry' => 'required|string',
            'product' => 'nullable|string',
            'business_name' => 'nullable|string'
        ]);

        $user = $request->user();

        if (!$user->hasAICredits()) {
            return response()->json([
                'success' => false,
                'message' => 'لقد استنفدت رصيدك من استخدامات الذكاء الاصطناعي.',
                'code' => 'NO_CREDITS'
            ], 403);
        }

        $response = $this->gemini->generateTargetAudienceSuggestions($request->all());

        if ($response['success']) {
            $user->deductAICredit();
        }

        return response()->json([
            'success' => $response['success'],
            'data' => $response['content'],
            'credits_remaining' => $user->fresh()->ai_credits_remaining,
            'demo_mode' => $response['demo_mode'] ?? false
        ]);
    }

    /**
     * Improve marketing message
     */
    public function improveMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'industry' => 'nullable|string',
            'target_audience' => 'nullable|string'
        ]);

        $user = $request->user();

        if (!$user->hasAICredits()) {
            return response()->json([
                'success' => false,
                'message' => 'لقد استنفدت رصيدك من استخدامات الذكاء الاصطناعي.',
                'code' => 'NO_CREDITS'
            ], 403);
        }

        $context = [
            'industry' => $request->industry ?? 'عام',
            'target_audience' => $request->target_audience ?? 'الجمهور العام'
        ];

        $response = $this->gemini->improveMarketingMessage($request->message, $context);

        if ($response['success']) {
            $user->deductAICredit();
        }

        return response()->json([
            'success' => $response['success'],
            'data' => $response['content'],
            'credits_remaining' => $user->fresh()->ai_credits_remaining,
            'demo_mode' => $response['demo_mode'] ?? false
        ]);
    }

    /**
     * Analyze competitors
     */
    public function analyzeCompetitors(Request $request)
    {
        $request->validate([
            'industry' => 'required|string',
            'location' => 'required|string'
        ]);

        $user = $request->user();

        if (!$user->hasAICredits()) {
            return response()->json([
                'success' => false,
                'message' => 'لقد استنفدت رصيدك من استخدامات الذكاء الاصطناعي.',
                'code' => 'NO_CREDITS'
            ], 403);
        }

        $response = $this->gemini->analyzeCompetitors($request->industry, $request->location);

        if ($response['success']) {
            $user->deductAICredit();
        }

        return response()->json([
            'success' => $response['success'],
            'data' => $response['content'],
            'credits_remaining' => $user->fresh()->ai_credits_remaining,
            'demo_mode' => $response['demo_mode'] ?? false
        ]);
    }

    /**
     * Generate content plan
     */
    public function generateContentPlan(Request $request)
    {
        $request->validate([
            'industry' => 'required|string',
            'target_audience' => 'nullable|string',
            'goals' => 'nullable|string'
        ]);

        $user = $request->user();

        if (!$user->hasAICredits()) {
            return response()->json([
                'success' => false,
                'message' => 'لقد استنفدت رصيدك من استخدامات الذكاء الاصطناعي.',
                'code' => 'NO_CREDITS'
            ], 403);
        }

        $response = $this->gemini->generateContentPlan($request->all());

        if ($response['success']) {
            $user->deductAICredit();
        }

        return response()->json([
            'success' => $response['success'],
            'data' => $response['content'],
            'credits_remaining' => $user->fresh()->ai_credits_remaining,
            'demo_mode' => $response['demo_mode'] ?? false
        ]);
    }

    /**
     * Get contextual guidance for a plan section
     */
    public function guidance(Request $request)
    {
        $request->validate([
            'section_type' => 'required|string',
            'business_name' => 'nullable|string',
            'industry' => 'nullable|string'
        ]);

        // Return structured guidance without consuming credits
        $guidance = $this->getSectionGuidance($request->section_type, $request->industry);

        return response()->json([
            'success' => true,
            'data' => $guidance
        ]);
    }

    /**
     * Get AI suggestions based on user context
     */
    public function suggestions(Request $request)
    {
        $request->validate([
            'section_type' => 'required|string',
            'plan_data' => 'required|array',
            'current_input' => 'nullable|array'
        ]);

        $user = $request->user();

        if (!$user->hasAICredits()) {
            return response()->json([
                'success' => false,
                'message' => 'لقد استنفدت رصيدك من استخدامات الذكاء الاصطناعي.',
                'code' => 'NO_CREDITS'
            ], 403);
        }

        $prompt = $this->buildSuggestionsPrompt(
            $request->section_type,
            $request->plan_data,
            $request->current_input
        );

        $response = $this->gemini->chat($prompt, [], []);

        if ($response['success']) {
            $user->deductAICredit();
            
            // Parse suggestions from response
            $suggestions = $this->parseSuggestions($response['content']);
            
            return response()->json([
                'success' => true,
                'data' => ['suggestions' => $suggestions],
                'credits_remaining' => $user->fresh()->ai_credits_remaining
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في توليد الاقتراحات'
        ], 500);
    }

    /**
     * Analyze user input and provide feedback
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'section_type' => 'required|string',
            'user_input' => 'required|array',
            'plan_data' => 'required|array'
        ]);

        $user = $request->user();

        if (!$user->hasAICredits()) {
            return response()->json([
                'success' => false,
                'message' => 'لقد استنفدت رصيدك من استخدامات الذكاء الاصطناعي.',
                'code' => 'NO_CREDITS'
            ], 403);
        }

        $prompt = $this->buildAnalysisPrompt(
            $request->section_type,
            $request->user_input,
            $request->plan_data
        );

        $response = $this->gemini->chat($prompt, [], []);

        if ($response['success']) {
            $user->deductAICredit();
            
            $analysis = $this->parseAnalysis($response['content']);
            
            return response()->json([
                'success' => true,
                'data' => $analysis,
                'credits_remaining' => $user->fresh()->ai_credits_remaining
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحليل المحتوى'
        ], 500);
    }

    /**
     * Get AI credits info
     */
    public function getCredits(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'credits_remaining' => $user->ai_credits_remaining,
                'subscription_tier' => $user->subscription_tier,
                'has_unlimited' => $user->subscription_tier !== 'free',
                'reset_at' => $user->ai_credits_reset_at?->toIso8601String()
            ]
        ]);
    }

    /**
     * Save conversation to database
     */
    private function saveConversation(
        int $userId, 
        string $sessionId, 
        string $type, 
        string $message,
        ?int $planId = null,
        int $tokensUsed = 0,
        int $processingTime = 0
    ): void {
        AIConversation::create([
            'user_id' => $userId,
            'plan_id' => $planId,
            'session_id' => $sessionId,
            'message_type' => $type,
            'message_text' => $message,
            'tokens_used' => $tokensUsed,
            'processing_time_ms' => $processingTime,
            'ai_model' => 'gemini-1.5-flash'
        ]);
    }

    /**
     * Get static guidance for a section
     */
    private function getSectionGuidance(string $sectionType, ?string $industry): array
    {
        $guidanceMap = [
            'personal_card' => [
                'what' => 'بطاقة المشروع تعرّف بمشروعك وتحدد هويته التجارية والقيم التي يقوم عليها',
                'how' => [
                    'اكتب رؤية طموحة وملهمة لما تريد أن يصبح عليه مشروعك في المستقبل',
                    'حدد رسالتك بوضوح: ماذا تقدم، لمن تقدمه، ولماذا أنت مختلف',
                    'اذكر 3-5 قيم جوهرية تميز طريقة عملك وتعاملك مع العملاء'
                ],
                'example' => "رؤية: أن نكون المتجر الأول للهدايا المخصصة في المنطقة\nرسالة: نقدم هدايا فريدة تعبر عن مشاعر حقيقية بجودة عالية وتوصيل سريع\nالقيم: الإبداع، الجودة، الالتزام بالمواعيد"
            ],
            'diagnosis' => [
                'what' => 'تحليل SWOT يساعدك على فهم وضعك التنافسي من خلال تقييم العوامل الداخلية والخارجية',
                'how' => [
                    'نقاط القوة (Strengths): ما يميزك عن المنافسين - عوامل داخلية إيجابية',
                    'نقاط الضعف (Weaknesses): ما يحتاج تحسين في عملك - عوامل داخلية سلبية',
                    'الفرص (Opportunities): اتجاهات السوق المواتية - عوامل خارجية إيجابية',
                    'التهديدات (Threats): المخاطر والتحديات - عوامل خارجية سلبية'
                ],
                'example' => "قوة: فريق خدمة عملاء متميز متاح 24/7\nضعف: محدودية رأس المال التشغيلي\nفرصة: نمو التسوق الإلكتروني بنسبة 40% سنوياً\nتهديد: دخول منافسين كبار للسوق"
            ],
            'target_audience' => [
                'what' => 'تحديد الجمهور المستهدف بدقة يساعدك على توجيه رسائلك التسويقية بفعالية أكبر',
                'how' => [
                    'حدد الفئة العمرية، الجنس، الموقع الجغرافي، والمستوى الاقتصادي',
                    'اذكر رغباتهم وأحلامهم - ما الذي يسعون لتحقيقه؟',
                    'حدد آلامهم ومشاكلهم - ما الذي يزعجهم ويريدون حله؟',
                    'توقع اعتراضاتهم - ما الذي قد يمنعهم من الشراء؟'
                ],
                'example' => "الفئة: نساء 25-40 سنة، عاملات، دخل متوسط إلى عالي\nالرغبات: توفير الوقت، الظهور بمظهر أنيق\nالآلام: ضيق الوقت، صعوبة إيجاد هدايا مميزة\nالاعتراضات: السعر، الثقة في التوصيل"
            ],
            // Add more sections...
        ];

        return $guidanceMap[$sectionType] ?? [
            'what' => 'هذا القسم يساعدك على بناء جزء مهم من خطتك التسويقية',
            'how' => ['اكتب بوضوح ودقة', 'كن محدداً قدر الإمكان', 'استخدم أمثلة واقعية من مجالك'],
            'example' => ''
        ];
    }

    /**
     * Build prompt for suggestions
     */
    private function buildSuggestionsPrompt(string $sectionType, array $planData, ?array $currentInput): string
    {
        $businessName = $planData['business_name'] ?? 'المشروع';
        $industry = $planData['industry'] ?? 'غير محدد';

        $prompts = [
            'diagnosis' => "أنت مستشار تسويقي. مشروع '{$businessName}' في مجال '{$industry}'. اقترح 5 نقاط محتملة لتحليل SWOT (نقطتين قوة، نقطة ضعف، فرصة، تهديد). قدم الاقتراحات كنقاط منفصلة يمكن للمستخدم الاختيار منها.",
            'target_audience' => "أنت مستشار تسويقي. بناءً على مشروع '{$businessName}' في مجال '{$industry}'، اقترح 5 خصائص محتملة للجمهور المستهدف (عمر، اهتمامات، آلام). قدمها كنقاط منفصلة.",
            'channels' => "أنت مستشار تسويقي. اقترح 5 قنوات تسويقية مناسبة لمشروع '{$businessName}' في مجال '{$industry}'. لكل قناة، اذكر سبب مناسبتها بجملة واحدة."
        ];

        return $prompts[$sectionType] ?? "اقترح 5 أفكار مفيدة لقسم {$sectionType} في خطة تسويقية لمشروع {$businessName}.";
    }

    /**
     * Build prompt for analysis
     */
    private function buildAnalysisPrompt(string $sectionType, array $userInput, array $planData): string
    {
        $inputText = json_encode($userInput, JSON_UNESCAPED_UNICODE);
        
        return "أنت مستشار تسويقي محترف. راجع ما كتبه المستخدم في قسم '{$sectionType}' من خطته التسويقية:\n\n{$inputText}\n\nقدم تحليلاً بناءً يتضمن:\n1. نقاط القوة (2-3 نقاط)\n2. اقتراحات للتحسين (2-3 نقاط محددة)\n\nكن إيجابياً ومشجعاً، وقدم نصائح قابلة للتطبيق.";
    }

    /**
     * Parse suggestions from AI response
     */
    private function parseSuggestions(string $response): array
    {
        // Try to extract bullet points or numbered list
        $lines = explode("\n", $response);
        $suggestions = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            // Match lines starting with -, *, •, or numbers
            if (preg_match('/^[\-\*•\d\.]+\s+(.+)$/', $line, $matches)) {
                $suggestions[] = trim($matches[1]);
            } elseif (!empty($line) && count($suggestions) < 5) {
                $suggestions[] = $line;
            }
        }
        
        return array_slice($suggestions, 0, 5);
    }

    /**
     * Parse analysis from AI response
     */
    private function parseAnalysis(string $response): array
    {
        $strengths = [];
        $improvements = [];
        
        // Simple parsing - look for sections
        if (preg_match('/نقاط القوة[:\s]+(.*?)(?=اقتراحات|$)/s', $response, $matches)) {
            $strengthsText = $matches[1];
            preg_match_all('/[\-\*•]\s*(.+)/', $strengthsText, $strengthMatches);
            $strengths = array_map('trim', $strengthMatches[1]);
        }
        
        if (preg_match('/اقتراحات[:\s]+(.*?)$/s', $response, $matches)) {
            $improvementsText = $matches[1];
            preg_match_all('/[\-\*•]\s*(.+)/', $improvementsText, $improvementMatches);
            $improvements = array_map('trim', $improvementMatches[1]);
        }
        
        return [
            'strengths' => $strengths,
            'improvements' => $improvements
        ];
    }
}
