<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\AIConversation;

class GeminiService
{
    private string $apiKey;
    private string $model = 'gemini-1.5-flash';
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        // Get API key from system settings or env
        $this->apiKey = $this->getApiKey();
    }

    private function getApiKey(): string
    {
        // Try to get from database first
        $setting = DB::table('system_settings')
            ->where('setting_key', 'gemini_api_key')
            ->first();

        if ($setting && $setting->setting_value) {
            return $setting->setting_value;
        }

        // Fallback to env or demo key
        return config('services.gemini.api_key', 'DEMO_KEY_FOR_TESTING');
    }

    /**
     * Generate target audience suggestions
     */
    public function generateTargetAudienceSuggestions(array $businessData): array
    {
        $prompt = $this->buildTargetAudiencePrompt($businessData);
        return $this->callGemini($prompt);
    }

    /**
     * Improve marketing message
     */
    public function improveMarketingMessage(string $message, array $context): array
    {
        $industry = $context['industry'] ?? 'عام';
        $audience = $context['target_audience'] ?? 'الجمهور العام';

        $prompt = <<<PROMPT
أنت خبير تسويق استراتيجي. قم بتحليل وتحسين الرسالة التسويقية التالية:

الرسالة الحالية: "{$message}"

السياق:
- الصناعة: {$industry}
- الجمهور المستهدف: {$audience}

قدم النتيجة بصيغة JSON التالية:
{
  "score": 8,
  "strengths": ["نقطة قوة 1", "نقطة قوة 2"],
  "weaknesses": ["نقطة ضعف 1", "نقطة ضعف 2"],
  "improved_versions": [
    "نسخة محسنة 1",
    "نسخة محسنة 2",
    "نسخة محسنة 3"
  ],
  "recommendations": ["توصية 1", "توصية 2"]
}
PROMPT;

        return $this->callGemini($prompt);
    }

    /**
     * Analyze competitors
     */
    public function analyzeCompetitors(string $industry, string $location): array
    {
        $prompt = <<<PROMPT
أنت محلل سوق متخصص في {$industry} في {$location}.

قدم تحليلاً شاملاً بصيغة JSON:
{
  "competitors": [
    {
      "name": "اسم المنافس",
      "strengths": ["قوة 1", "قوة 2"],
      "weaknesses": ["ضعف 1", "ضعف 2"],
      "pricing_strategy": "وصف الاستراتيجية"
    }
  ],
  "market_trends": ["اتجاه 1", "اتجاه 2"],
  "opportunities": ["فرصة 1", "فرصة 2"]
}
PROMPT;

        return $this->callGemini($prompt);
    }

    /**
     * Generate content plan
     */
    public function generateContentPlan(array $planData): array
    {
        $industry = $planData['industry'] ?? 'عام';
        $audience = $planData['target_audience'] ?? 'الجمهور العام';
        $goals = $planData['goals'] ?? 'زيادة الوعي بالعلامة التجارية';

        $prompt = <<<PROMPT
أنت خبير تسويق محتوى. قم بإنشاء خطة محتوى شهرية (30 يوم).

معلومات المشروع:
- الصناعة: {$industry}
- الجمهور المستهدف: {$audience}
- الأهداف: {$goals}

قدم خطة بصيغة JSON:
{
  "content_calendar": [
    {
      "day": 1,
      "content_type": "منشور مدونة",
      "topic": "عنوان المحتوى",
      "platform": "المنصة",
      "description": "وصف مختصر"
    }
  ],
  "content_themes": ["ثيم 1", "ثيم 2"],
  "posting_schedule": "جدول النشر الموصى به"
}
PROMPT;

        return $this->callGemini($prompt, ['temperature' => 0.8]);
    }

    /**
     * Evaluate plan quality
     */
    public function evaluatePlanQuality(array $planSections): array
    {
        $planText = $this->formatPlanForEvaluation($planSections);

        $prompt = <<<PROMPT
قم بتقييم الخطة التسويقية التالية:

{$planText}

قدم تقييماً شاملاً بصيغة JSON:
{
  "overall_score": 85,
  "section_scores": {
    "target_audience": 9,
    "core_message": 8,
    "channels": 7
  },
  "strengths": ["قوة 1", "قوة 2"],
  "improvements": ["تحسين 1", "تحسين 2"],
  "recommendations": ["توصية 1", "توصية 2"],
  "priorities": ["أولوية 1", "أولوية 2"]
}
PROMPT;

        return $this->callGemini($prompt);
    }

    /**
     * Contextual chat
     */
    public function chat(string $userMessage, array $conversationHistory = [], array $userContext = []): array
    {
        $prompt = $this->buildChatPrompt($userMessage, $conversationHistory, $userContext);
        return $this->callGemini($prompt);
    }

    /**
     * Core Gemini API call
     */
    private function callGemini(string $prompt, array $options = []): array
    {
        $temperature = $options['temperature'] ?? 0.7;
        $maxTokens = $options['max_tokens'] ?? 2048;

        try {
            $startTime = microtime(true);

            // Check if using demo key
            if ($this->apiKey === 'DEMO_KEY_FOR_TESTING') {
                return $this->getDemoResponse($prompt);
            }

            $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

            $response = Http::timeout(30)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => $temperature,
                    'maxOutputTokens' => $maxTokens,
                ]
            ]);

            $processingTime = (microtime(true) - $startTime) * 1000;

            if (!$response->successful()) {
                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'error' => 'فشل في الاتصال بخدمة الذكاء الاصطناعي',
                    'processing_time_ms' => $processingTime
                ];
            }

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            // Try to parse as JSON
            $parsedContent = $this->parseResponse($text);

            return [
                'success' => true,
                'content' => $parsedContent,
                'raw_text' => $text,
                'processing_time_ms' => $processingTime,
                'tokens_used' => $data['usageMetadata']['totalTokenCount'] ?? 0
            ];

        } catch (\Exception $e) {
            Log::error('Gemini Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'حدث خطأ أثناء معالجة الطلب: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Parse AI response (try JSON first, fallback to text)
     */
    private function parseResponse(string $text): mixed
    {
        // Try to extract JSON from markdown code blocks
        if (preg_match('/```json\s*(.*?)\s*```/s', $text, $matches)) {
            $jsonText = $matches[1];
        } elseif (preg_match('/```\s*(.*?)\s*```/s', $text, $matches)) {
            $jsonText = $matches[1];
        } else {
            $jsonText = $text;
        }

        $decoded = json_decode($jsonText, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $text;
    }

    /**
     * Demo response for testing without API key
     */
    private function getDemoResponse(string $prompt): array
    {
        sleep(1); // Simulate API delay

        // Detect request type and return appropriate demo data
        if (str_contains($prompt, 'تحسين الرسالة')) {
            return [
                'success' => true,
                'content' => [
                    'score' => 7,
                    'strengths' => ['رسالة واضحة', 'لغة بسيطة'],
                    'weaknesses' => ['تحتاج لمزيد من التحديد', 'غير موجهة بشكل كافٍ'],
                    'improved_versions' => [
                        'نسخة محسنة 1: رسالة أكثر تحديداً وتركيزاً',
                        'نسخة محسنة 2: رسالة عاطفية تلامس احتياجات العميل',
                        'نسخة محسنة 3: رسالة تركز على الفوائد المباشرة'
                    ],
                    'recommendations' => [
                        'حدد الجمهور المستهدف بدقة',
                        'أضف دعوة واضحة للإجراء'
                    ]
                ],
                'processing_time_ms' => 1000,
                'tokens_used' => 150,
                'demo_mode' => true
            ];
        }

        if (str_contains($prompt, 'الجمهور المستهدف') || str_contains($prompt, 'target audience')) {
            return [
                'success' => true,
                'content' => [
                    'segments' => [
                        [
                            'name' => 'الشباب المهتمون بالتكنولوجيا',
                            'age_range' => '18-35',
                            'characteristics' => ['نشطون على وسائل التواصل', 'يبحثون عن الابتكار'],
                            'pain_points' => ['نقص الوقت', 'البحث عن حلول سريعة']
                        ]
                    ],
                    'recommendations' => ['استخدم منصات التواصل الاجتماعي', 'ركز على الفيديو القصير']
                ],
                'processing_time_ms' => 1000,
                'demo_mode' => true
            ];
        }

        // Default chat response
        return [
            'success' => true,
            'content' => 'هذا رد تجريبي من نظام الذكاء الاصطناعي. للحصول على ردود حقيقية، يرجى إضافة مفتاح Gemini API في إعدادات النظام.',
            'processing_time_ms' => 1000,
            'demo_mode' => true
        ];
    }

    // Helper methods
    private function buildTargetAudiencePrompt(array $data): string
    {
        $industry = $data['industry'] ?? 'عام';
        $product = $data['product'] ?? 'منتج/خدمة';

        return <<<PROMPT
أنت خبير تسويق. قم بتحليل الجمهور المستهدف لـ:
- الصناعة: {$industry}
- المنتج/الخدمة: {$product}

قدم اقتراحات بصيغة JSON تحتوي على شرائح الجمهور المستهدف مع خصائصهم ونقاط الألم والتوصيات.
PROMPT;
    }

    private function buildChatPrompt(string $message, array $history, array $context): string
    {
        $contextStr = !empty($context) ? "\n\nالسياق:\n" . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $historyStr = '';

        if (!empty($history)) {
            $historyStr = "\n\nالمحادثة السابقة:\n";
            foreach (array_slice($history, -5) as $msg) {
                $historyStr .= "- {$msg['role']}: {$msg['content']}\n";
            }
        }

        return <<<PROMPT
أنت مساعد تسويق ذكي متخصص في مساعدة أصحاب الأعمال على تطوير استراتيجياتهم التسويقية.
{$contextStr}
{$historyStr}

المستخدم: {$message}

قدم رداً مفيداً ومفصلاً بالعربية.
PROMPT;
    }

    private function formatPlanForEvaluation(array $sections): string
    {
        $formatted = '';
        foreach ($sections as $key => $section) {
            $formatted .= "\n## {$key}\n";
            $formatted .= is_array($section) ? json_encode($section, JSON_UNESCAPED_UNICODE) : $section;
        }
        return $formatted;
    }
}
