<template>
  <div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="card p-6 border-l-4 border-primary-500 hover:shadow-lg transition-shadow bg-white">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-gray-500 text-sm font-medium">الخطط النشطة</h3>
          <div class="bg-primary-50 p-2 rounded-lg text-primary-600">
            <DocumentTextIcon class="w-6 h-6" />
          </div>
        </div>
        <div class="text-3xl font-bold text-gray-900">{{ stats.plans_count || 0 }}</div>
        <p class="text-xs text-green-500 mt-2 flex items-center">
          <span class="ml-1">⬆</span> تم إنشاؤها
        </p>
      </div>

      <div class="card p-6 border-l-4 border-secondary-500 hover:shadow-lg transition-shadow bg-white">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-gray-500 text-sm font-medium">رصيد AI</h3>
          <div class="bg-secondary-50 p-2 rounded-lg text-secondary-600">
            <SparklesIcon class="w-6 h-6" />
          </div>
        </div>
        <div class="text-3xl font-bold text-gray-900">{{ aiCredits }}</div>
        <p class="text-xs text-gray-400 mt-2">
           نقطة متبقية هذا الشهر
        </p>
      </div>

      <div class="card p-6 border-l-4 border-green-500 hover:shadow-lg transition-shadow bg-white">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-gray-500 text-sm font-medium">نسبة الإنجاز</h3>
          <div class="bg-green-50 p-2 rounded-lg text-green-600">
            <ChartPieIcon class="w-6 h-6" />
          </div>
        </div>
        <div class="text-3xl font-bold text-gray-900">%{{ stats.completion_avg || 0 }}</div>
        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-3">
            <div class="bg-green-500 h-1.5 rounded-full" :style="{ width: (stats.completion_avg || 0) + '%' }"></div>
        </div>
      </div>

      <div class="card p-6 border-l-4 border-purple-500 hover:shadow-lg transition-shadow bg-white">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-gray-500 text-sm font-medium">الباقة الحالية</h3>
          <div class="bg-purple-50 p-2 rounded-lg text-purple-600">
            <StarIcon class="w-6 h-6" />
          </div>
        </div>
        <div class="text-xl font-bold text-gray-900 uppercase">{{ subscriptionTier }}</div>
        <router-link to="/subscription" class="text-xs text-primary-600 mt-2 block hover:underline">
           ترقية الباقة ->
        </router-link>
      </div>
    </div>

    <!-- Recent Plans & Quick Start -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Recent Plans List (2/3 width) -->
      <div class="lg:col-span-2 card bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-bold text-gray-900">آخر الخطط</h2>
          <router-link to="/plans" class="text-sm text-primary-600 hover:underline">عرض الكل</router-link>
        </div>

        <div v-if="loading" class="space-y-4">
            <div v-for="n in 3" :key="n" class="h-16 bg-gray-100 rounded-lg animate-pulse"></div>
        </div>

        <div v-else-if="recentPlans.length > 0" class="space-y-4">
          <div v-for="plan in recentPlans" :key="plan.id" class="flex items-center justify-between p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition-colors group cursor-pointer" @click="$router.push(`/plans/${plan.id}`)">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center text-primary-700 font-bold text-xl">
                {{ plan.business_name.charAt(0) }}
              </div>
              <div>
                <h4 class="font-bold text-gray-900 group-hover:text-primary-600 transition-colors">{{ plan.business_name }}</h4>
                <p class="text-xs text-gray-500">{{ formatDate(plan.created_at) }}</p>
              </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden sm:block text-right">
                    <span class="block text-xs text-gray-400 mb-1">الإنجاز</span>
                    <span class="inline-block px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-bold">%{{ plan.completion_percentage }}</span>
                </div>
                <svg class="w-5 h-5 text-gray-300 group-hover:text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <DocumentTextIcon class="w-8 h-8 text-gray-400" />
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد خطط بعد</h3>
            <p class="text-gray-500 mb-6">ابدأ بإنشاء خطتك التسويقية الأولى الآن</p>
            <router-link to="/plans/create" class="btn btn-primary">إنشاء خطة جديدة</router-link>
        </div>
      </div>

      <!-- Quick Actions (1/3 width) -->
      <div class="space-y-6">
        <!-- Create New CTA -->
        <div class="card bg-gradient-to-br from-primary-600 to-secondary-600 text-white p-6 relative overflow-hidden group hover:scale-[1.02] transition-transform cursor-pointer" @click="$router.push('/plans/create')">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-colors"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4 backdrop-blur-sm">
                    <PlusIcon class="w-6 h-6" />
                </div>
                <h3 class="text-xl font-bold mb-2">مشروع جديد؟</h3>
                <p class="text-primary-100 text-sm mb-4 leading-relaxed">استخدم معالج التخطيط الذكي لبناء استراتيجية كاملة في دقائق.</p>
                <div class="flex items-center text-sm font-bold">
                    ابــدأ الآن <ArrowLeftIcon class="w-4 h-4 mr-2" />
                </div>
            </div>
        </div>

        <!-- AI Assistant Promo -->
        <div class="card bg-white p-6 border border-gray-100 shadow-sm">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <SparklesIcon class="w-5 h-5 text-secondary-500" />
                مساعد AI
            </h3>
            <div class="space-y-3">
                <button class="w-full text-right p-3 rounded-lg bg-gray-50 hover:bg-secondary-50 hover:text-secondary-700 transition-colors text-sm flex items-center justify-between group">
                    <span>💡 اقترح أفكار محتوى</span>
                    <ArrowLeftIcon class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" />
                </button>
                 <button class="w-full text-right p-3 rounded-lg bg-gray-50 hover:bg-secondary-50 hover:text-secondary-700 transition-colors text-sm flex items-center justify-between group">
                    <span>🎯 تحليل المنافسين</span>
                     <ArrowLeftIcon class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" />
                </button>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios'; // Direct use or inject
import { 
    DocumentTextIcon, 
    SparklesIcon, 
    ChartPieIcon, 
    StarIcon, 
    PlusIcon, 
    ArrowLeftIcon 
} from '@heroicons/vue/24/outline';

const authStore = useAuthStore();
const loading = ref(true);
const recentPlans = ref([]);
const stats = ref({});

const aiCredits = computed(() => authStore.user?.ai_credits_remaining || 0);
const subscriptionTier = computed(() => authStore.user?.subscription_tier || 'Free');

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('ar-SA', { year: 'numeric', month: 'long', day: 'numeric' });
};

onMounted(async () => {
    try {
        // Fetch dashboard data
        const response = await axios.get('/dashboard'); // Calls /api/v1/dashboard
        
        if (response.data.success) {
            const data = response.data.data;
            stats.value = data.stats;
            recentPlans.value = data.recent_plans;
            // Update auth store slightly if needed to sync credits
            if (authStore.user) {
                authStore.user.ai_credits_remaining = data.stats.ai_credits;
            }
        }
    } catch (e) {
        console.error('Error fetching dashboard:', e);
    } finally {
        loading.value = false;
    }
});
</script>
