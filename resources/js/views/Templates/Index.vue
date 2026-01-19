<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">مكتبة القوالب</h2>
        <p class="text-gray-500 mt-1">ابدأ بخطط جاهزة ومجربة لمختلف المجالات</p>
      </div>
      <!-- Filter placeholder -->
      <select class="input w-40">
          <option>الكل</option>
          <option>تجارة إلكترونية</option>
          <option>خدمات</option>
      </select>
    </div>

    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div v-for="n in 3" :key="n" class="h-64 bg-gray-100 rounded-xl animate-pulse"></div>
    </div>

    <div v-else-if="templates.length === 0" class="text-center py-12 bg-white rounded-xl border border-dashed">
        <p class="text-gray-500">لا توجد قوالب متاحة حالياً</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="template in templates" :key="template.id" class="card bg-white border border-gray-100 overflow-hidden hover:shadow-lg transition-all group">
            <div class="h-32 bg-gradient-to-br from-primary-500 to-secondary-500 flex items-center justify-center text-white relative">
                 <div class="bg-white/20 absolute inset-0 backdrop-blur-[2px]"></div>
                 <h3 class="relative z-10 font-bold text-xl">{{ template.name }}</h3>
            </div>
            <div class="p-6">
                <span class="text-xs font-bold text-primary-600 bg-primary-50 px-2 py-1 rounded-full mb-3 inline-block">
                    {{ template.category }}
                </span>
                <p class="text-gray-500 text-sm mb-6 h-12 overflow-hidden">{{ template.description }}</p>
                
                <button @click="useTemplate(template.id)" class="btn btn-outline-primary w-full group-hover:bg-primary-600 group-hover:text-white transition-colors">
                    استخدم هذا القالب
                </button>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const templates = ref([]);
const loading = ref(true);
const router = useRouter();

const fetchTemplates = async () => {
    try {
        const res = await axios.get('/templates');
        templates.value = res.data.data.data || res.data.data; // Handle pagination or list
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchTemplates();
});

const useTemplate = async (id) => {
    if (!confirm('هل تريد إنشاء خطة جديدة باستخدام هذا القالب؟')) return;
    try {
        const res = await axios.post(`/templates/${id}/use`);
        router.push(`/plans/${res.data.data.id}`);
    } catch (e) {
        alert(e.response?.data?.message || 'فشل استخدام القالب. ربما تجاوزت الحد المسموح.');
    }
};
</script>
