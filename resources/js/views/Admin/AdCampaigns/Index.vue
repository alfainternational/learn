<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">إدارة الحملات الإعلانية</h1>
                <p class="text-sm text-gray-500 mt-1">مراجعة واعتماد إعلانات المعلنين</p>
            </div>
             <!-- Tabs -->
            <div class="flex bg-gray-100 p-1 rounded-lg">
                <button 
                    v-for="tab in tabs" 
                    :key="tab.id"
                    @click="currentTab = tab.id"
                    :class="[currentTab === tab.id ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900', 'px-4 py-2 text-sm font-medium rounded-md transition-all']"
                >
                    {{ tab.name }}
                </button>
            </div>
        </div>

        <!-- Campaigns Content -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div v-if="loading" class="p-12 flex justify-center">
                 <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <div v-else-if="filteredCampaigns.length === 0" class="p-12 text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                <p class="text-lg font-medium text-gray-900">لا توجد حملات إعلانية هنا</p>
                <p class="text-sm mt-1">لا توجد حملات تطابق حالة الفلتر الحالي</p>
            </div>

            <div v-else class="divide-y divide-gray-100">
                <div v-for="campaign in filteredCampaigns" :key="campaign.id" class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Ad Preview (Image) -->
                        <div class="w-full md:w-64 h-40 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 border border-gray-200">
                            <img v-if="campaign.image_url" :src="campaign.image_url" class="w-full h-full object-cover">
                            <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                <span class="text-xs">لا توجد صورة</span>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="flex-1">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="font-bold text-lg text-gray-900">{{ campaign.title || campaign.campaign_name }}</h3>
                                        <span :class="statusClasses(campaign.status)" class="px-2 py-0.5 text-xs font-medium rounded-full">
                                            {{ statusLabel(campaign.status) }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-sm line-clamp-2">{{ campaign.description }}</p>
                                    
                                    <div class="flex items-center gap-4 mt-4 text-sm text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                            {{ campaign.advertiser?.name || 'مجهول' }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            {{ campaign.budget_total }} ر.س
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            {{ new Date(campaign.starts_at).toLocaleDateString() }} - {{ new Date(campaign.ends_at).toLocaleDateString() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons for Pending Campaigns -->
                            <div v-if="campaign.status === 'pending_review'" class="mt-6 flex gap-3 justify-end border-t pt-4 border-gray-100">
                                <button 
                                    @click="rejectCampaign(campaign)"
                                    class="px-4 py-2 bg-white text-red-600 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                >
                                    رفض الحملة
                                </button>
                                <button 
                                    @click="approveCampaign(campaign)"
                                    class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-sm"
                                >
                                    قبول ونشر
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const campaigns = ref([]);
const loading = ref(true);
const currentTab = ref('pending');

const tabs = [
    { id: 'pending', name: 'بانتظار المراجعة' },
    { id: 'active', name: 'نشطة' },
    { id: 'all', name: 'الكل' }
];

const filteredCampaigns = computed(() => {
    if (currentTab.value === 'all') return campaigns.value;
    if (currentTab.value === 'pending') return campaigns.value.filter(c => c.status === 'pending_review');
    if (currentTab.value === 'active') return campaigns.value.filter(c => c.status === 'active');
    return campaigns.value;
});

const fetchCampaigns = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/v1/admin/campaigns');
        if (response.data.success) {
            campaigns.value = response.data.data;
        }
    } catch (error) {
        console.error('Error fetching campaigns:', error);
    } finally {
        loading.value = false;
    }
};

const approveCampaign = async (campaign) => {
    if (!confirm('هل أنت متأكد من قبول ونشر هذه الحملة؟')) return;
    try {
        await axios.post(`/api/v1/admin/campaigns/${campaign.id}/approve`);
        // Remove from local list or refresh
        fetchCampaigns();
    } catch (error) {
        console.error('Error approving campaign:', error);
    }
};

const rejectCampaign = async (campaign) => {
    const reason = prompt('الرجاء إدخال سبب الرفض:');
    if (reason === null) return;
    
    try {
        await axios.post(`/api/v1/admin/campaigns/${campaign.id}/reject`, { reason });
        fetchCampaigns();
    } catch (error) {
        console.error('Error rejecting campaign:', error);
    }
};

const statusClasses = (status) => {
    const map = {
        'pending_review': 'bg-yellow-100 text-yellow-800',
        'active': 'bg-green-100 text-green-800',
        'paused': 'bg-gray-100 text-gray-800',
        'rejected': 'bg-red-100 text-red-800',
        'completed': 'bg-blue-100 text-blue-800'
    };
    return map[status] || 'bg-gray-100 text-gray-800';
};

const statusLabel = (status) => {
    const map = {
        'pending_review': 'قيد المراجعة',
        'active': 'نشطة',
        'paused': 'متوقفة',
        'rejected': 'مرفوضة',
        'completed': 'مكتملة',
        'draft': 'مسودة'
    };
    return map[status] || status;
};

onMounted(() => {
    fetchCampaigns();
});
</script>
