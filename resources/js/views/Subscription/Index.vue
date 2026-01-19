<template>
  <div class="max-w-5xl mx-auto space-y-8">
    <div class="text-center">
        <h2 class="text-3xl font-display font-bold text-gray-900">إدارة الاشتراك</h2>
        <p class="text-gray-500 mt-2">تحكم في باقتك واستمتع بمميزات غير محدودة</p>
    </div>

    <!-- Current Plan Status -->
    <div class="card bg-white p-8 border border-gray-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4">
             <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-emerald-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold">
                 <span v-if="currentTier === 'free'">Free</span>
                 <span v-else-if="currentTier === 'basic'">Basic</span>
                 <span v-else-if="currentTier === 'pro'">Pro</span>
                 <span v-else>Ent</span>
             </div>
             <div>
                 <h3 class="font-bold text-lg text-gray-900">باقتك الحالية: {{ currentTier.toUpperCase() }}</h3>
                 <p class="text-sm text-gray-500">لديك <span class="font-bold text-primary-600">{{ aiCredits }}</span> نقطة ذكاء اصطناعي متبقية.</p>
             </div>
        </div>
        <button class="text-red-500 text-sm hover:underline">إلغاء الاشتراك</button>
    </div>

    <!-- Plans Grid (Reused style) -->
    <div class="grid md:grid-cols-3 gap-6">
        <!-- Basic -->
        <div class="card bg-white border p-6 flex flex-col" :class="{'border-primary-500 ring-2 ring-primary-200': currentTier === 'basic'}">
            <h3 class="font-bold text-xl mb-2 text-primary-500">أساسي</h3>
            <div class="text-3xl font-bold mb-6">99 <span class="text-base text-gray-400 font-normal">ريال</span></div>
             <ul class="space-y-3 mb-8 flex-1 text-sm text-gray-600">
                <li class="flex gap-2">✔ 3 خطط تسويقية</li>
                <li class="flex gap-2">✔ ذكاء اصطناعي غير محدود</li>
                <li class="flex gap-2">✔ تصدير PDF</li>
            </ul>
            <button @click="subscribe('basic')" :disabled="currentTier === 'basic'" 
                class="btn w-full" :class="currentTier === 'basic' ? 'bg-gray-100 text-gray-400 cursor-default' : 'btn-primary'">
                {{ currentTier === 'basic' ? 'باقتك الحالية' : 'ترقية الآن' }}
            </button>
        </div>

        <!-- Pro -->
        <div class="card bg-white border p-6 flex flex-col relative overflow-hidden" :class="{'border-secondary-500 ring-2 ring-secondary-200': currentTier === 'pro'}">
            <div class="absolute top-0 left-0 right-0 bg-secondary-500 text-white text-xs text-center py-1">موصى به</div>
            <h3 class="font-bold text-xl mb-2 text-secondary-500 mt-4">احترافي</h3>
            <div class="text-3xl font-bold mb-6">299 <span class="text-base text-gray-400 font-normal">ريال</span></div>
             <ul class="space-y-3 mb-8 flex-1 text-sm text-gray-600">
                <li class="flex gap-2">✔ 10 خطط تسويقية</li>
                <li class="flex gap-2">✔ دعم فني سريع</li>
                <li class="flex gap-2">✔ تصدير جميع الصيغ</li>
            </ul>
            <button @click="subscribe('pro')" :disabled="currentTier === 'pro'"
                class="btn w-full" :class="currentTier === 'pro' ? 'bg-gray-100 text-gray-400 cursor-default' : 'btn-secondary'">
                {{ currentTier === 'pro' ? 'باقتك الحالية' : 'ترقية الآن' }}
            </button>
        </div>

          <!-- Enterprise -->
        <div class="card bg-white border p-6 flex flex-col">
            <h3 class="font-bold text-xl mb-2 text-gray-800">شركات</h3>
            <div class="text-3xl font-bold mb-6">999 <span class="text-base text-gray-400 font-normal">ريال</span></div>
             <ul class="space-y-3 mb-8 flex-1 text-sm text-gray-600">
                <li class="flex gap-2">✔ خطط غير محدودة</li>
                <li class="flex gap-2">✔ API Access</li>
                <li class="flex gap-2">✔ مدير حساب خاص</li>
            </ul>
             <button class="btn btn-outline text-gray-600 w-full hover:bg-gray-50">تواصل معنا</button>
        </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';

const authStore = useAuthStore();
const currentTier = computed(() => authStore.user?.subscription_tier || 'free');
const aiCredits = computed(() => authStore.user?.ai_credits_remaining || 0);

const subscribe = async (tier) => {
    if (!confirm(`هل أنت متأكد من الترقية لباقة ${tier}؟`)) return;
    try {
        await axios.post('/subscriptions/subscribe', {
            tier: tier,
            payment_method_id: 'pm_mock_123' // Mock ID
        });
        alert('تم الاشتراك بنجاح! استمتع بمميزاتك الجديدة.');
        await authStore.fetchUser(); // Refresh user data
    } catch (e) {
        alert('حدث خطأ أثناء الاشتراك');
    }
};
</script>
