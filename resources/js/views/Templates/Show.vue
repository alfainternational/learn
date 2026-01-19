<template>
  <div v-if="loading" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-600"></div>
  </div>

  <div v-else-if="template" class="space-y-6">
    <!-- Header / Nav -->
    <div class="flex items-center justify-between">
      <router-link to="/templates" class="flex items-center text-sm text-gray-500 hover:text-gray-900">
        <ArrowRightIcon class="w-4 h-4 ml-1" />
        العودة للقوالب
      </router-link>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-0 overflow-hidden bg-white">
                <div class="bg-gray-100 h-48 md:h-64 flex items-center justify-center relative">
                     <span class="text-6xl">{{ template.name.charAt(0) }}</span>
                     <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                     <h1 class="absolute bottom-6 right-6 text-3xl font-bold text-white shadow-sm">{{ template.name }}</h1>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-2 py-1 bg-primary-50 text-primary-700 text-xs rounded-full">{{ template.industry || 'عام' }}</span>
                        <span v-if="template.is_premium" class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Pro</span>
                    </div>
                    
                    <h2 class="text-lg font-bold text-gray-900 mb-2">عن هذا القالب</h2>
                    <p class="text-gray-600 leading-relaxed mb-6">{{ template.description }}</p>

                    <h2 class="text-lg font-bold text-gray-900 mb-2">الأقسام المتضمنة</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="section in sections" :key="section" class="flex items-center text-sm text-gray-600">
                            <CheckCircleIcon class="w-5 h-5 text-green-500 ml-2" />
                            {{ getSectionName(section) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / CTA -->
        <div class="space-y-6">
             <div class="card p-6 bg-white sticky top-6">
                <h3 class="font-bold text-gray-900 mb-4">استخدام هذا القالب</h3>
                <div v-if="template.is_premium" class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
                    هذا القالب متاح فقط لمشتركي الباقة الاحترافية.
                </div>
                
                <button 
                    @click="useTemplate" 
                    :disabled="creating || (template.is_premium && !canUsePremium)"
                    class="btn btn-primary w-full flex justify-center items-center gap-2 mb-3"
                >
                    <span v-if="creating">جاري الإنشاء...</span>
                    <span v-else>إنشاء خطة من القالب</span>
                    <PlusIcon v-if="!creating" class="w-5 h-5" />
                </button>
                
                <p class="text-xs text-center text-gray-500">
                    سيتم إنشاء خطة جديدة مملوءة بالهيكل والمحتوى الافتراضي لهذا القالب.
                </p>
             </div>
        </div>
    </div>
  </div>
  
  <div v-else class="text-center py-20">
      <h3 class="text-xl font-bold text-gray-700">القالب غير موجود</h3>
      <router-link to="/templates" class="text-primary-600 mt-2 block">العودة للمكتبة</router-link>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router'; // Correct import
import axios from 'axios'; // Ensure axios is imported or available globally? In views we import.
import { ArrowRightIcon, PlusIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';
import { useAuthStore } from '@/stores/auth';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const template = ref(null);
const loading = ref(true);
const creating = ref(false);

// Mock sections based on PlanTemplate model mostly storing JSON 'template_data'
// Assuming template_data has 'sections' or we infer
const sections = ref(['diagnosis', 'target_audience', 'core_message', 'marketing_channels', 'content_plan']);

const getSectionName = (key) => {
    const names = {
        'diagnosis': 'التشخيص الأولي',
        'target_audience': 'الجمهور المستهدف',
        'core_message': 'الرسالة الأساسية',
        'marketing_channels': 'قنوات التسويق',
        'content_plan': 'خطة المحتوى',
        'offer_stack': 'العروض'
    };
    return names[key] || key;
};

const canUsePremium = computed(() => {
    return ['pro', 'enterprise'].includes(authStore.user?.subscription_tier);
});

onMounted(async () => {
    try {
        const res = await axios.get(`/templates/${route.params.id}`);
        template.value = res.data.data;
        // If template_data exists, parse it
        if (template.value.template_data && template.value.template_data.sections) {
            sections.value = template.value.template_data.sections;
        }
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
});

const useTemplate = async () => {
    creating.value = true;
    try {
        const res = await axios.post(`/templates/${template.value.id}/use`);
        router.push(`/plans/${res.data.data.id}`);
    } catch (e) {
        alert(e.response?.data?.message || 'فشل استخدام القالب');
    } finally {
        creating.value = false;
    }
};
</script>
