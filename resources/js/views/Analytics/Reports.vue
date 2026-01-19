<template>
  <div class="min-h-screen bg-gray-50 p-6" dir="rtl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">التقارير</h1>
        <p class="text-gray-600 mt-2">إنشاء وإدارة تقارير الأداء المخصصة</p>
      </div>
      <button @click="showCreateModal = true" 
              class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition flex items-center gap-2">
        <i class="fas fa-plus"></i>
        إنشاء تقرير جديد
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 flex flex-wrap gap-4">
      <select v-model="filters.type" class="border rounded-lg px-4 py-2">
        <option value="">جميع الأنواع</option>
        <option value="performance">تقرير أداء</option>
        <option value="comparison">تقرير مقارنة</option>
        <option value="roi">تقرير العائد</option>
        <option value="audience">تقرير الجمهور</option>
      </select>
      <select v-model="filters.period" class="border rounded-lg px-4 py-2">
        <option value="">جميع الفترات</option>
        <option value="weekly">أسبوعي</option>
        <option value="monthly">شهري</option>
        <option value="quarterly">ربع سنوي</option>
      </select>
    </div>

    <!-- Reports List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="report in filteredReports" :key="report.id"
           class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
        <div class="p-6">
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <span class="px-3 py-1 text-xs rounded-full"
                    :class="getTypeClass(report.type)">
                {{ getTypeLabel(report.type) }}
              </span>
              <h3 class="text-lg font-semibold mt-3">{{ report.name }}</h3>
              <p class="text-gray-500 text-sm mt-1">{{ report.description }}</p>
            </div>
          </div>
          
          <div class="mt-4 pt-4 border-t flex items-center justify-between text-sm text-gray-500">
            <span>{{ formatDate(report.created_at) }}</span>
            <span>{{ report.period }}</span>
          </div>
        </div>
        
        <div class="bg-gray-50 px-6 py-3 flex items-center justify-between">
          <button @click="viewReport(report)" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-eye ml-1"></i> عرض
          </button>
          <button @click="downloadReport(report)" class="text-green-600 hover:text-green-800">
            <i class="fas fa-download ml-1"></i> تحميل
          </button>
          <button @click="deleteReport(report.id)" class="text-red-600 hover:text-red-800">
            <i class="fas fa-trash ml-1"></i> حذف
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!reports.length && !loading" class="bg-white rounded-xl shadow-sm p-12 text-center">
      <div class="text-6xl mb-4">📋</div>
      <h3 class="text-xl font-semibold text-gray-700">لا توجد تقارير</h3>
      <p class="text-gray-500 mt-2">ابدأ بإنشاء تقريرك الأول</p>
    </div>

    <!-- Create Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl w-full max-w-lg">
        <div class="p-6 border-b">
          <h2 class="text-xl font-bold">إنشاء تقرير جديد</h2>
        </div>
        
        <form @submit.prevent="createReport" class="p-6 space-y-4">
          <div>
            <label class="block text-gray-700 font-medium mb-2">اسم التقرير</label>
            <input v-model="newReport.name" type="text" required
                   class="w-full border rounded-lg px-4 py-3" placeholder="تقرير أداء الحملات">
          </div>
          
          <div>
            <label class="block text-gray-700 font-medium mb-2">نوع التقرير</label>
            <select v-model="newReport.type" required class="w-full border rounded-lg px-4 py-3">
              <option value="performance">تقرير أداء</option>
              <option value="comparison">تقرير مقارنة</option>
              <option value="roi">تقرير العائد</option>
              <option value="audience">تقرير الجمهور</option>
            </select>
          </div>
          
          <div>
            <label class="block text-gray-700 font-medium mb-2">الفترة الزمنية</label>
            <select v-model="newReport.period" required class="w-full border rounded-lg px-4 py-3">
              <option value="weekly">أسبوعي</option>
              <option value="monthly">شهري</option>
              <option value="quarterly">ربع سنوي</option>
              <option value="custom">مخصص</option>
            </select>
          </div>

          <div v-if="newReport.period === 'custom'" class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-700 font-medium mb-2">من</label>
              <input v-model="newReport.start_date" type="date" class="w-full border rounded-lg px-4 py-3">
            </div>
            <div>
              <label class="block text-gray-700 font-medium mb-2">إلى</label>
              <input v-model="newReport.end_date" type="date" class="w-full border rounded-lg px-4 py-3">
            </div>
          </div>

          <div>
            <label class="block text-gray-700 font-medium mb-2">الحملات</label>
            <div class="flex flex-wrap gap-2">
              <label v-for="campaign in campaigns" :key="campaign.id" 
                     class="flex items-center gap-2 px-3 py-2 bg-gray-100 rounded-lg cursor-pointer">
                <input type="checkbox" v-model="newReport.campaign_ids" :value="campaign.id">
                {{ campaign.name }}
              </label>
            </div>
          </div>

          <div>
            <label class="block text-gray-700 font-medium mb-2">الوصف (اختياري)</label>
            <textarea v-model="newReport.description" rows="2"
                      class="w-full border rounded-lg px-4 py-3" placeholder="وصف موجز للتقرير"></textarea>
          </div>
        </form>

        <div class="p-6 border-t flex justify-end gap-4">
          <button @click="showCreateModal = false" class="px-6 py-2 border rounded-lg">
            إلغاء
          </button>
          <button @click="createReport" :disabled="creating"
                  class="px-6 py-2 bg-blue-600 text-white rounded-lg disabled:opacity-50">
            {{ creating ? 'جاري الإنشاء...' : 'إنشاء التقرير' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-8 text-center">
        <div class="animate-spin w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full mx-auto"></div>
        <p class="mt-4 text-gray-600">جاري التحميل...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const loading = ref(false);
const creating = ref(false);
const showCreateModal = ref(false);
const reports = ref([]);
const campaigns = ref([]);

const filters = ref({
  type: '',
  period: ''
});

const newReport = ref({
  name: '',
  type: 'performance',
  period: 'monthly',
  campaign_ids: [],
  description: '',
  start_date: '',
  end_date: ''
});

const filteredReports = computed(() => {
  return reports.value.filter(r => {
    if (filters.value.type && r.type !== filters.value.type) return false;
    if (filters.value.period && r.period !== filters.value.period) return false;
    return true;
  });
});

const fetchReports = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/v1/analytics/reports');
    reports.value = data.data || [];
  } catch (error) {
    console.error('Error fetching reports:', error);
  } finally {
    loading.value = false;
  }
};

const fetchCampaigns = async () => {
  try {
    const { data } = await axios.get('/api/v1/campaigns');
    campaigns.value = data.data || [];
  } catch (error) {
    console.error('Error fetching campaigns:', error);
  }
};

const createReport = async () => {
  creating.value = true;
  try {
    await axios.post('/api/v1/analytics/reports', newReport.value);
    showCreateModal.value = false;
    newReport.value = { name: '', type: 'performance', period: 'monthly', campaign_ids: [], description: '', start_date: '', end_date: '' };
    await fetchReports();
  } catch (error) {
    console.error('Error creating report:', error);
  } finally {
    creating.value = false;
  }
};

const viewReport = (report) => {
  window.open(`/reports/${report.id}`, '_blank');
};

const downloadReport = async (report) => {
  try {
    const response = await axios.get(`/api/v1/analytics/reports/${report.id}/download`, { responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `${report.name}.pdf`);
    document.body.appendChild(link);
    link.click();
    link.remove();
  } catch (error) {
    console.error('Error downloading report:', error);
  }
};

const deleteReport = async (id) => {
  if (!confirm('هل أنت متأكد من حذف هذا التقرير؟')) return;
  try {
    await axios.delete(`/api/v1/analytics/reports/${id}`);
    reports.value = reports.value.filter(r => r.id !== id);
  } catch (error) {
    console.error('Error deleting report:', error);
  }
};

const getTypeClass = (type) => {
  const classes = {
    performance: 'bg-blue-100 text-blue-700',
    comparison: 'bg-purple-100 text-purple-700',
    roi: 'bg-green-100 text-green-700',
    audience: 'bg-orange-100 text-orange-700'
  };
  return classes[type] || 'bg-gray-100 text-gray-700';
};

const getTypeLabel = (type) => {
  const labels = { performance: 'أداء', comparison: 'مقارنة', roi: 'عائد', audience: 'جمهور' };
  return labels[type] || type;
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('ar-SA');
};

onMounted(() => {
  fetchReports();
  fetchCampaigns();
});
</script>
