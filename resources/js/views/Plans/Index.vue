<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-bold text-gray-900">خططي التسويقية</h2>
      <router-link to="/plans/create" class="btn btn-primary flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        <span>إنشاء خطة جديدة</span>
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
       <div v-for="n in 6" :key="n" class="h-48 bg-gray-100 rounded-xl animate-pulse"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="plans.length === 0" class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد خطط حتى الآن</h3>
        <p class="text-gray-500 mb-8 max-w-md mx-auto">ابدأ بإنشاء خطتك التسويقية الأولى واستفد من قوة الذكاء الاصطناعي في تنمية أعمالك.</p>
        <router-link to="/plans/create" class="btn btn-primary">إنشاء خطة جديدة</router-link>
    </div>

    <!-- Plans Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="plan in plans" :key="plan.id" class="card bg-white border border-gray-100 p-6 hover:shadow-lg transition-all group flex flex-col h-full relative overflow-hidden">
        <!-- Status Badge -->
        <span class="absolute top-4 left-4 px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600 font-medium">
             {{ plan.status === 'draft' ? 'مسودة' : (plan.status === 'completed' ? 'مكتملة' : plan.status) }}
        </span>

        <div class="mb-6">
            <div class="w-14 h-14 bg-gradient-to-br from-primary-100 to-primary-200 rounded-2xl flex items-center justify-center text-primary-700 font-bold text-2xl mb-4 group-hover:scale-110 transition-transform">
                {{ plan.business_name.charAt(0) }}
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-1 line-clamp-1 group-hover:text-primary-600 transition-colors">{{ plan.business_name }}</h3>
            <p class="text-sm text-gray-500">{{ plan.industry }}</p>
        </div>

        <div class="mt-auto space-y-4">
             <!-- Progress Bar -->
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-gray-500">اكتمال الخطة</span>
                    <span class="font-bold text-gray-900">%{{ plan.completion_percentage }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5">
                    <div class="bg-gradient-to-r from-green-400 to-green-500 h-1.5 rounded-full" :style="{ width: plan.completion_percentage + '%' }"></div>
                </div>
            </div>

            <div class="flex gap-2 border-t pt-4">
                <router-link :to="`/plans/${plan.id}`" class="btn btn-primary flex-1 text-sm justify-center"> عرض التفاصيل </router-link>
                <button class="btn btn-ghost text-gray-400 hover:text-red-500 p-2" title="حذف">
                     <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            </div>
        </div>
      </div>
    </div>
    
    <!-- Pagination (Simple) -->
    <div v-if="plans.length > 0" class="flex justify-center mt-8">
        <!-- Add pagination component logic here usually -->
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const loading = ref(true);
const plans = ref([]);

onMounted(async () => {
  try {
    const response = await axios.get('/plans');
    plans.value = response.data.data;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
});
</script>
