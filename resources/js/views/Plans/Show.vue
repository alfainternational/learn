<template>
  <div v-if="loading" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
  </div>
  
  <div v-else-if="plan" class="min-h-screen pb-20">
    <!-- Header -->
    <div class="bg-white border-b sticky top-0 z-20 shadow-sm">
        <div class="container-custom py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button @click="$router.push('/plans')" class="text-gray-400 hover:text-gray-600">
                     <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <div>
                   <h1 class="text-xl font-bold text-gray-900">{{ plan.title }}</h1>
                   <div class="text-xs text-gray-500">{{ plan.business_name }} • {{ plan.industry }}</div>
                </div>
            </div>
            
             <div class="flex items-center gap-3">
                 <!-- Export Button -->
                 <button @click="exportPdf" class="btn btn-ghost text-sm border border-gray-200">
                     <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                     تصدير PDF
                 </button>
                 <button class="btn btn-secondary text-sm px-4">مشاركة</button>
             </div>
        </div>
        
         <!-- Tabs Scrollable -->
         <div class="container-custom overflow-x-auto no-scrollbar">
             <div class="flex border-b border-gray-200 min-w-max">
                 <button v-for="section in sections" :key="section.key" 
                    @click="activeTab = section.key" 
                    class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap"
                    :class="activeTab === section.key ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                    {{ section.label }}
                 </button>
             </div>
         </div>
    </div>

    <!-- Content Area -->
    <div class="container-custom py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Editor -->
            <div class="lg:col-span-2 space-y-6">
                 <!-- Dynamic Component for Section -->
                 <component 
                    :is="currentSectionComponent" 
                    v-model="localModel"
                    v-model:isCompleted="currentSectionCompleted"
                    :saving="saving"
                    @save="save"
                 />
            </div>

            <!-- AI Sidebar -->
            <div class="space-y-6">
                <!-- AI Advisor Component -->
                <AIAdvisor 
                    :section-type="activeTab"
                    :plan-data="plan"
                    :user-input="localModel"
                />

                <!-- Credits Display -->
                <div class="card bg-gradient-to-br from-purple-50 to-pink-50 p-4 border border-purple-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            <span class="text-sm font-medium text-purple-900">رصيد AI</span>
                        </div>
                        <span class="text-lg font-bold text-purple-700">{{ aiCredits }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch, defineAsyncComponent } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';
import AIAdvisor from '@/components/AIAdvisor.vue';

const route = useRoute();
const authStore = useAuthStore();
const plan = ref(null);
const loading = ref(true);
const activeTab = ref('personal_card'); // Default to first section
const saving = ref(false);

// Dynamic Components Import
const SectionPersonalCard = defineAsyncComponent(() => import('./Sections/SectionPersonalCard.vue'));
const SectionDiagnosis = defineAsyncComponent(() => import('./Sections/SectionDiagnosis.vue'));
const SectionTargetAudience = defineAsyncComponent(() => import('./Sections/SectionTargetAudience.vue'));
const SectionCoreMessage = defineAsyncComponent(() => import('./Sections/SectionCoreMessage.vue'));
const SectionOfferStack = defineAsyncComponent(() => import('./Sections/SectionOfferStack.vue'));
const SectionChannels = defineAsyncComponent(() => import('./Sections/SectionChannels.vue'));
const SectionFunnel = defineAsyncComponent(() => import('./Sections/SectionFunnel.vue'));
const SectionFollowup = defineAsyncComponent(() => import('./Sections/SectionFollowup.vue'));
const SectionMetrics = defineAsyncComponent(() => import('./Sections/SectionMetrics.vue'));
const SectionCompetitorAnalysis = defineAsyncComponent(() => import('./Sections/SectionCompetitorAnalysis.vue'));
const SectionContentPlan = defineAsyncComponent(() => import('./Sections/SectionContentPlan.vue'));
const SectionBudgetBreakdown = defineAsyncComponent(() => import('./Sections/SectionBudgetBreakdown.vue'));

const sections = [
    { key: 'personal_card', label: 'بطاقة المشروع', component: SectionPersonalCard },
    { key: 'diagnosis', label: 'التشخيص (SWOT)', component: SectionDiagnosis },
    { key: 'target_audience', label: 'الجمهور المستهدف', component: SectionTargetAudience },
    { key: 'core_message', label: 'الرسالة الأساسية', component: SectionCoreMessage },
    { key: 'offer_stack', label: 'عرض القيمة', component: SectionOfferStack },
    { key: 'channels', label: 'قنوات التسويق', component: SectionChannels },
    { key: 'funnel', label: 'قمع المبيعات', component: SectionFunnel },
    { key: 'followup', label: 'نظام المتابعة', component: SectionFollowup },
    { key: 'metrics', label: 'الأرقام والمقاييس', component: SectionMetrics },
    { key: 'competitor_analysis', label: 'تحليل المنافسين', component: SectionCompetitorAnalysis },
    { key: 'content_plan', label: 'خطة المحتوى', component: SectionContentPlan },
    { key: 'budget_breakdown', label: 'توزيع الميزانية', component: SectionBudgetBreakdown },
];

const currentSectionConfig = computed(() => sections.find(s => s.key === activeTab.value));
const currentSectionLabel = computed(() => currentSectionConfig.value?.label);
const currentSectionComponent = computed(() => currentSectionConfig.value?.component);
const aiCredits = computed(() => authStore.user?.ai_credits_remaining || 0);

// Data Management
const currentSectionData = computed(() => {
    if (!plan.value) return {};
    const section = plan.value.sections.find(s => s.section_type === activeTab.value);
    
    // Parse JSON if string, or return object
    if (!section || !section.data) return {};
    
    try {
        return typeof section.data === 'string' ? JSON.parse(section.data) : section.data;
    } catch (e) {
        return {};
    }
});

const currentSectionCompleted = ref(false);

// We need a local editable model to avoid mutating computed directly
const localModel = ref({});

watch(activeTab, () => {
    // Reset local model when tab changes
    localModel.value = { ...currentSectionData.value };
    
    const section = plan.value?.sections.find(s => s.section_type === activeTab.value);
    currentSectionCompleted.value = section ? Boolean(section.is_completed) : false;
});

onMounted(async () => {
    try {
        const response = await axios.get(`/plans/${route.params.id}`);
        plan.value = response.data.data;
        localModel.value = { ...currentSectionData.value };
        
        const section = plan.value.sections.find(s => s.section_type === activeTab.value);
        currentSectionCompleted.value = section ? Boolean(section.is_completed) : false;
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
});

const save = async () => {
    saving.value = true;
    try {
        await axios.put(`/plans/${plan.value.id}/sections/${activeTab.value}`, {
            section_data: localModel.value, // Send as JSON object, backend handles casting if needed
            is_completed: currentSectionCompleted.value
        });
        
        // Update local state to reflect saved
        const sectionIndex = plan.value.sections.findIndex(s => s.section_type === activeTab.value);
        if (sectionIndex !== -1) {
            plan.value.sections[sectionIndex].data = localModel.value;
            plan.value.sections[sectionIndex].is_completed = currentSectionCompleted.value;
            plan.value.sections[sectionIndex].updated_at = new Date().toISOString();
        } else {
             // If section didn't exist in array (rare case if seeded correctly), push it
             plan.value.sections.push({
                 section_type: activeTab.value,
                 data: localModel.value,
                 is_completed: currentSectionCompleted.value,
                 updated_at: new Date().toISOString()
             });
        }
    } catch (e) {
        console.error(e);
        alert('حدث خطأ أثناء الحفظ. يرجى المحاولة مرة أخرى.');
    } finally {
        saving.value = false;
    }
};

const exportPdf = () => {
    const apiBaseUrl = document.querySelector('meta[name="api-base-url"]')?.getAttribute('content') || '/api/v1';
    window.open(`${apiBaseUrl}/plans/${plan.value.id}/export/pdf`, '_blank');
};
</script>
