<template>
    <div class="card bg-white p-6 border border-gray-100 shadow-sm">
        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-secondary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
            مساعدك التسويقي
        </h3>

        <!-- Tabs -->
        <div class="flex gap-2 mb-4 border-b border-gray-200">
            <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
                :class="['px-3 py-2 text-sm font-medium border-b-2 transition-colors', activeTab === tab.id ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700']">
                {{ tab.label }}
            </button>
        </div>

        <!-- Tab Content -->
        <div class="space-y-4">
            <!-- Guidance Tab -->
            <div v-if="activeTab === 'guidance'" class="space-y-3">
                <div v-if="loading" class="animate-pulse space-y-2">
                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-200 rounded w-full"></div>
                    <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                </div>
                <div v-else class="prose prose-sm max-w-none">
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-3 mb-3">
                        <p class="text-sm font-medium text-blue-900">📋 {{ guidance.what }}</p>
                    </div>
                    <div class="bg-green-50 border-l-4 border-green-500 p-3 mb-3">
                        <p class="text-sm font-medium text-green-900 mb-2">✍️ كيف تكتبه:</p>
                        <ul class="text-sm text-green-800 space-y-1 mr-4">
                            <li v-for="(step, i) in guidance.how" :key="i">{{ step }}</li>
                        </ul>
                    </div>
                    <div v-if="guidance.example" class="bg-purple-50 border-l-4 border-purple-500 p-3">
                        <p class="text-sm font-medium text-purple-900 mb-2">💡 مثال:</p>
                        <p class="text-sm text-purple-800 whitespace-pre-line">{{ guidance.example }}</p>
                    </div>
                </div>
            </div>

            <!-- Suggestions Tab -->
            <div v-if="activeTab === 'suggestions'" class="space-y-3">
                <button @click="fetchSuggestions" :disabled="loadingSuggestions" 
                    class="btn btn-primary btn-sm w-full">
                    <span v-if="loadingSuggestions">جاري التحليل...</span>
                    <span v-else>احصل على اقتراحات 💭</span>
                </button>
                
                <div v-if="suggestions.length > 0" class="space-y-2">
                    <p class="text-sm text-gray-600 mb-2">💭 بناءً على مشروعك، قد تفكر في:</p>
                    <div v-for="(suggestion, i) in suggestions" :key="i" 
                        class="flex items-start gap-2 p-3 bg-amber-50 rounded-lg border border-amber-200 hover:bg-amber-100 transition-colors">
                        <span class="text-amber-600 font-bold">•</span>
                        <p class="text-sm text-gray-800 flex-1">{{ suggestion }}</p>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">💡 هذه مجرد أفكار - اختر ما يناسبك وعدّل حسب احتياجك</p>
                </div>
            </div>

            <!-- Analysis Tab -->
            <div v-if="activeTab === 'analysis'" class="space-y-3">
                <button @click="analyzeContent" :disabled="loadingAnalysis || !hasUserInput" 
                    class="btn btn-secondary btn-sm w-full">
                    <span v-if="loadingAnalysis">جاري التحليل...</span>
                    <span v-else>راجع ما كتبته 🔍</span>
                </button>
                
                <div v-if="!hasUserInput" class="text-sm text-gray-500 text-center py-4">
                    اكتب شيئاً أولاً لأتمكن من مراجعته
                </div>

                <div v-if="analysis" class="space-y-3">
                    <div v-if="analysis.strengths?.length" class="bg-green-50 border-l-4 border-green-500 p-3">
                        <p class="text-sm font-medium text-green-900 mb-2">✅ نقاط قوة:</p>
                        <ul class="text-sm text-green-800 space-y-1 mr-4">
                            <li v-for="(s, i) in analysis.strengths" :key="i">{{ s }}</li>
                        </ul>
                    </div>
                    
                    <div v-if="analysis.improvements?.length" class="bg-orange-50 border-l-4 border-orange-500 p-3">
                        <p class="text-sm font-medium text-orange-900 mb-2">💡 اقتراحات للتحسين:</p>
                        <ul class="text-sm text-orange-800 space-y-1 mr-4">
                            <li v-for="(imp, i) in analysis.improvements" :key="i">{{ imp }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    sectionType: { type: String, required: true },
    planData: { type: Object, required: true },
    userInput: { type: Object, default: () => ({}) }
});

const activeTab = ref('guidance');
const loading = ref(false);
const loadingSuggestions = ref(false);
const loadingAnalysis = ref(false);

const tabs = [
    { id: 'guidance', label: 'إرشادات' },
    { id: 'suggestions', label: 'اقتراحات' },
    { id: 'analysis', label: 'تحليل' }
];

const guidance = ref({
    what: '',
    how: [],
    example: ''
});

const suggestions = ref([]);
const analysis = ref(null);

const hasUserInput = computed(() => {
    return Object.values(props.userInput).some(val => val && val.toString().trim().length > 0);
});

// Load guidance on mount and when section changes
watch(() => props.sectionType, () => {
    loadGuidance();
}, { immediate: true });

const loadGuidance = async () => {
    loading.value = true;
    try {
        const response = await axios.post('/ai/guidance', {
            section_type: props.sectionType,
            business_name: props.planData.business_name,
            industry: props.planData.industry
        });
        
        if (response.data.success) {
            guidance.value = response.data.data;
        }
    } catch (error) {
        console.error('Error loading guidance:', error);
        // Fallback to static guidance
        guidance.value = getStaticGuidance(props.sectionType);
    } finally {
        loading.value = false;
    }
};

const fetchSuggestions = async () => {
    loadingSuggestions.value = true;
    try {
        const response = await axios.post('/ai/suggestions', {
            section_type: props.sectionType,
            plan_data: props.planData,
            current_input: props.userInput
        });
        
        if (response.data.success) {
            suggestions.value = response.data.data.suggestions || [];
        }
    } catch (error) {
        console.error('Error fetching suggestions:', error);
        suggestions.value = ['حدث خطأ في جلب الاقتراحات. حاول مرة أخرى.'];
    } finally {
        loadingSuggestions.value = false;
    }
};

const analyzeContent = async () => {
    loadingAnalysis.value = true;
    try {
        const response = await axios.post('/ai/analyze', {
            section_type: props.sectionType,
            user_input: props.userInput,
            plan_data: props.planData
        });
        
        if (response.data.success) {
            analysis.value = response.data.data;
        }
    } catch (error) {
        console.error('Error analyzing content:', error);
        analysis.value = { 
            strengths: [], 
            improvements: ['حدث خطأ في التحليل. حاول مرة أخرى.'] 
        };
    } finally {
        loadingAnalysis.value = false;
    }
};

// Static fallback guidance
const getStaticGuidance = (sectionType) => {
    const guidanceMap = {
        'personal_card': {
            what: 'بطاقة المشروع تعرّف بمشروعك وتحدد هويته التجارية',
            how: [
                'اكتب رؤية طموحة لما تريد أن يصبح عليه مشروعك',
                'حدد رسالتك: ماذا تقدم ولمن ولماذا',
                'اذكر 3-5 قيم جوهرية تميز عملك'
            ],
            example: 'رؤية: أن نكون المتجر الأول للهدايا المخصصة في المنطقة\nرسالة: نقدم هدايا فريدة تعبر عن مشاعر حقيقية'
        },
        'diagnosis': {
            what: 'تحليل SWOT يساعدك على فهم وضعك التنافسي الحالي',
            how: [
                'نقاط القوة: ما يميزك عن المنافسين (داخلي)',
                'نقاط الضعف: ما يحتاج تحسين (داخلي)',
                'الفرص: اتجاهات السوق المواتية (خارجي)',
                'التهديدات: المخاطر والتحديات (خارجي)'
            ],
            example: 'قوة: فريق خدمة عملاء متميز\nضعف: محدودية رأس المال\nفرصة: نمو التسوق الإلكتروني\nتهديد: دخول منافسين كبار'
        }
        // ... more sections
    };
    
    return guidanceMap[sectionType] || {
        what: 'هذا القسم يساعدك على بناء جزء مهم من خطتك التسويقية',
        how: ['اكتب بوضوح', 'كن محدداً', 'استخدم أمثلة واقعية'],
        example: ''
    };
};
</script>
