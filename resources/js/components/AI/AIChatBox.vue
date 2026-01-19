<template>
    <div class="flex flex-col h-full bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">مساعد التسويق الذكي</h3>
                    <p class="text-xs text-gray-500">مدعوم بـ Gemini AI</p>
                </div>
            </div>
            <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Messages -->
        <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
            <div v-for="(msg, index) in messages" :key="index" :class="['flex', msg.role === 'user' ? 'justify-end' : 'justify-start']">
                <div :class="[
                    'max-w-[80%] rounded-lg p-3',
                    msg.role === 'user' 
                        ? 'bg-primary-600 text-white' 
                        : 'bg-gray-100 text-gray-900'
                ]">
                    <div v-if="typeof msg.content === 'string'" class="text-sm whitespace-pre-wrap">{{ msg.content }}</div>
                    <div v-else class="text-sm">
                        <pre class="text-xs bg-white/10 p-2 rounded overflow-x-auto">{{ JSON.stringify(msg.content, null, 2) }}</pre>
                    </div>
                    <div class="text-xs opacity-70 mt-1">{{ msg.timestamp }}</div>
                </div>
            </div>

            <div v-if="isTyping" class="flex justify-start">
                <div class="bg-gray-100 rounded-lg p-3">
                    <div class="flex gap-1">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                    </div>
                </div>
            </div>

            <div v-if="demoMode" class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-800">
                ⚠️ الوضع التجريبي: للحصول على ردود حقيقية، يرجى إضافة مفتاح Gemini API في إعدادات النظام.
            </div>
        </div>

        <!-- Input -->
        <div class="p-4 border-t border-gray-200">
            <div class="flex gap-2">
                <input 
                    v-model="userInput"
                    @keypress.enter="sendMessage"
                    :disabled="isTyping || !hasCredits"
                    type="text"
                    placeholder="اكتب رسالتك هنا..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 disabled:opacity-50"
                >
                <button 
                    @click="sendMessage"
                    :disabled="!userInput.trim() || isTyping || !hasCredits"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                </button>
            </div>
            
            <div class="flex items-center justify-between mt-2 text-xs text-gray-500">
                <span>الرصيد المتبقي: {{ creditsRemaining }} {{ creditsRemaining === -1 ? '(غير محدود)' : '' }}</span>
                <button @click="clearChat" class="text-red-500 hover:text-red-700">مسح المحادثة</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted } from 'vue';
import axios from 'axios';

const emit = defineEmits(['close']);

const messages = ref([]);
const userInput = ref('');
const isTyping = ref(false);
const creditsRemaining = ref(3);
const sessionId = ref(null);
const messagesContainer = ref(null);
const demoMode = ref(false);

const hasCredits = computed(() => creditsRemaining.value > 0 || creditsRemaining.value === -1);

const sendMessage = async () => {
    if (!userInput.value.trim() || isTyping.value) return;

    const message = userInput.value.trim();
    userInput.value = '';

    // Add user message
    messages.value.push({
        role: 'user',
        content: message,
        timestamp: new Date().toLocaleTimeString('ar-EG')
    });

    scrollToBottom();
    isTyping.value = true;

    try {
        const response = await axios.post('/api/v1/ai/chat', {
            message,
            session_id: sessionId.value
        });

        if (response.data.success) {
            messages.value.push({
                role: 'assistant',
                content: response.data.data.message,
                timestamp: new Date().toLocaleTimeString('ar-EG')
            });

            sessionId.value = response.data.data.session_id;
            creditsRemaining.value = response.data.data.credits_remaining;
            demoMode.value = response.data.data.demo_mode || false;
        } else {
            messages.value.push({
                role: 'assistant',
                content: 'عذراً، حدث خطأ: ' + (response.data.error || 'خطأ غير معروف'),
                timestamp: new Date().toLocaleTimeString('ar-EG')
            });
        }
    } catch (error) {
        messages.value.push({
            role: 'assistant',
            content: 'عذراً، فشل الاتصال بالخادم. يرجى المحاولة مرة أخرى.',
            timestamp: new Date().toLocaleTimeString('ar-EG')
        });
    } finally {
        isTyping.value = false;
        scrollToBottom();
    }
};

const clearChat = () => {
    if (confirm('هل أنت متأكد من مسح المحادثة؟')) {
        messages.value = [];
        sessionId.value = null;
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

const fetchCredits = async () => {
    try {
        const response = await axios.get('/api/v1/ai/credits');
        if (response.data.success) {
            creditsRemaining.value = response.data.data.has_unlimited ? -1 : response.data.data.credits_remaining;
        }
    } catch (error) {
        console.error('Error fetching credits:', error);
    }
};

onMounted(() => {
    fetchCredits();
    messages.value.push({
        role: 'assistant',
        content: 'مرحباً! أنا مساعدك التسويقي الذكي. كيف يمكنني مساعدتك اليوم؟',
        timestamp: new Date().toLocaleTimeString('ar-EG')
    });
});
</script>
