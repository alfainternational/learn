<template>
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900">إعدادات النظام</h1>
            <p class="text-sm text-gray-500 mt-1">إدارة إعدادات المنصة ومفاتيح API</p>
        </div>

        <!-- AI Settings Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-purple-50 text-purple-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">إعدادات الذكاء الاصطناعي</h2>
                    <p class="text-sm text-gray-500">مفتاح Gemini API</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        مفتاح Gemini API
                    </label>
                    <div class="flex gap-2">
                        <input 
                            v-model="geminiKey" 
                            :type="showKey ? 'text' : 'password'"
                            placeholder="أدخل مفتاح Gemini API"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                        <button 
                            @click="showKey = !showKey"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <svg v-if="!showKey" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        احصل على مفتاح مجاني من 
                        <a href="https://makersuite.google.com/app/apikey" target="_blank" class="text-primary-600 hover:underline">Google AI Studio</a>
                    </p>
                </div>

                <div class="flex gap-3">
                    <button 
                        @click="testApiKey"
                        :disabled="!geminiKey || testing"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        {{ testing ? 'جاري الاختبار...' : 'اختبار المفتاح' }}
                    </button>
                    <button 
                        @click="saveApiKey"
                        :disabled="!geminiKey || saving"
                        class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        {{ saving ? 'جاري الحفظ...' : 'حفظ المفتاح' }}
                    </button>
                </div>

                <!-- Status Messages -->
                <div v-if="testResult" :class="[testResult.success ? 'bg-green-50 text-green-800 border-green-200' : 'bg-red-50 text-red-800 border-red-200', 'p-4 rounded-lg border']">
                    <p class="text-sm font-medium">{{ testResult.message }}</p>
                </div>
            </div>
        </div>

        <!-- Subscription Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">إعدادات الاشتراكات</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        استخدامات AI المجانية (شهرياً)
                    </label>
                    <input 
                        v-model.number="settings.ai_credits_free_tier" 
                        type="number"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        عدد الخطط للمستخدم المجاني
                    </label>
                    <input 
                        v-model.number="settings.max_plans_free" 
                        type="number"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        سعر الاشتراك الأساسي (ريال/شهر)
                    </label>
                    <input 
                        v-model.number="settings.subscription_basic_price_monthly" 
                        type="number"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        سعر الاشتراك الاحترافي (ريال/شهر)
                    </label>
                    <input 
                        v-model.number="settings.subscription_pro_price_monthly" 
                        type="number"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                </div>
            </div>

            <button 
                @click="saveSettings"
                :disabled="saving"
                class="mt-4 px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 transition-colors">
                {{ saving ? 'جاري الحفظ...' : 'حفظ الإعدادات' }}
            </button>
        </div>

        <!-- Current Status -->
        <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl p-6 border border-purple-100">
            <h3 class="font-bold text-gray-900 mb-3">حالة النظام</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">وضع AI:</span>
                    <span class="mr-2 font-medium" :class="geminiKey && geminiKey !== 'DEMO_KEY_FOR_TESTING' ? 'text-green-600' : 'text-yellow-600'">
                        {{ geminiKey && geminiKey !== 'DEMO_KEY_FOR_TESTING' ? 'مفعّل' : 'وضع تجريبي' }}
                    </span>
                </div>
                <div>
                    <span class="text-gray-600">إجمالي المستخدمين:</span>
                    <span class="mr-2 font-medium text-gray-900">{{ totalUsers }}</span>
                </div>
                <div>
                    <span class="text-gray-600">الاشتراكات النشطة:</span>
                    <span class="mr-2 font-medium text-gray-900">{{ activeSubscriptions }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const geminiKey = ref('');
const showKey = ref(false);
const testing = ref(false);
const saving = ref(false);
const testResult = ref(null);

const settings = ref({
    ai_credits_free_tier: 3,
    max_plans_free: 1,
    subscription_basic_price_monthly: 99,
    subscription_pro_price_monthly: 299
});

const totalUsers = ref(0);
const activeSubscriptions = ref(0);

const fetchSettings = async () => {
    try {
        const response = await axios.get('/admin/settings');
        if (response.data.success) {
            const data = response.data.data;
            
            geminiKey.value = data.gemini_api_key?.value || '';
            settings.value.ai_credits_free_tier = data.ai_credits_free_tier?.value || 3;
            settings.value.max_plans_free = data.max_plans_free?.value || 1;
            settings.value.subscription_basic_price_monthly = data.subscription_basic_price_monthly?.value || 99;
            settings.value.subscription_pro_price_monthly = data.subscription_pro_price_monthly?.value || 299;
        }
    } catch (error) {
        console.error('Error fetching settings:', error);
    }
};

const testApiKey = async () => {
    testing.value = true;
    testResult.value = null;

    try {
        const response = await axios.post('/admin/settings/test-gemini', {
            api_key: geminiKey.value
        });

        testResult.value = {
            success: true,
            message: response.data.message
        };
    } catch (error) {
        testResult.value = {
            success: false,
            message: error.response?.data?.message || 'فشل اختبار المفتاح'
        };
    } finally {
        testing.value = false;
    }
};

const saveApiKey = async () => {
    saving.value = true;

    try {
        await axios.put('/admin/settings', {
            key: 'gemini_api_key',
            value: geminiKey.value,
            description: 'Gemini API Key for AI features'
        });

        alert('تم حفظ مفتاح API بنجاح');
        testResult.value = null;
    } catch (error) {
        alert('فشل حفظ المفتاح');
    } finally {
        saving.value = false;
    }
};

const saveSettings = async () => {
    saving.value = true;

    try {
        const updates = [
            { key: 'ai_credits_free_tier', value: settings.value.ai_credits_free_tier },
            { key: 'max_plans_free', value: settings.value.max_plans_free },
            { key: 'subscription_basic_price_monthly', value: settings.value.subscription_basic_price_monthly },
            { key: 'subscription_pro_price_monthly', value: settings.value.subscription_pro_price_monthly }
        ];

        for (const update of updates) {
            await axios.put('/admin/settings', update);
        }

        alert('تم حفظ الإعدادات بنجاح');
    } catch (error) {
        alert('فشل حفظ الإعدادات');
    } finally {
        saving.value = false;
    }
};

onMounted(() => {
    fetchSettings();
    // Fetch stats
    axios.get('/admin/dashboard').then(res => {
        if (res.data.success) {
            totalUsers.value = res.data.data.stats.total_users;
            activeSubscriptions.value = res.data.data.stats.active_subscriptions;
        }
    });
});
</script>
