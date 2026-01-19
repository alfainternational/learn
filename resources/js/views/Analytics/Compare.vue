<template>
  <div class="min-h-screen bg-gray-50 p-6" dir="rtl">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">مقارنة الحملات</h1>
      <p class="text-gray-600 mt-2">قارن بين حملاتك التسويقية جنبًا إلى جنب</p>
    </div>

    <!-- Campaign Selection -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
      <h3 class="text-lg font-semibold mb-4">اختر الحملات للمقارنة</h3>
      <div class="flex flex-wrap gap-4">
        <div v-for="campaign in availableCampaigns" :key="campaign.id"
             @click="toggleCampaign(campaign.id)"
             class="px-4 py-2 rounded-lg cursor-pointer transition-all border-2"
             :class="selectedCampaigns.includes(campaign.id) 
               ? 'bg-blue-500 text-white border-blue-500' 
               : 'bg-gray-100 text-gray-700 border-gray-200 hover:border-blue-300'">
          {{ campaign.name }}
        </div>
      </div>
      <button @click="compareNow" 
              :disabled="selectedCampaigns.length < 2"
              class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
        مقارنة الآن ({{ selectedCampaigns.length }}/4)
      </button>
    </div>

    <!-- Comparison Table -->
    <div v-if="comparisonData.length" class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-4 text-right text-gray-700 font-semibold">المؤشر</th>
              <th v-for="campaign in comparisonData" :key="campaign.id" 
                  class="px-6 py-4 text-center text-gray-700 font-semibold">
                {{ campaign.name }}
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="metric in metrics" :key="metric.key" class="hover:bg-gray-50">
              <td class="px-6 py-4 font-medium text-gray-700">{{ metric.label }}</td>
              <td v-for="campaign in comparisonData" :key="campaign.id + metric.key" 
                  class="px-6 py-4 text-center"
                  :class="getBestClass(metric.key, campaign[metric.key])">
                {{ formatValue(campaign[metric.key], metric.format) }}
                <span v-if="isBest(metric.key, campaign[metric.key])" class="mr-2 text-green-500">🏆</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Comparison Charts -->
    <div v-if="comparisonData.length" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Performance Comparison -->
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">مقارنة الأداء</h3>
        <canvas ref="comparisonChartRef" height="300"></canvas>
      </div>

      <!-- ROI Comparison -->
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">مقارنة العائد على الاستثمار</h3>
        <canvas ref="roiChartRef" height="300"></canvas>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!comparisonData.length && !loading" 
         class="bg-white rounded-xl shadow-sm p-12 text-center">
      <div class="text-6xl mb-4">📊</div>
      <h3 class="text-xl font-semibold text-gray-700">اختر حملتين على الأقل للمقارنة</h3>
      <p class="text-gray-500 mt-2">يمكنك مقارنة حتى 4 حملات في نفس الوقت</p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-8 text-center">
        <div class="animate-spin w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full mx-auto"></div>
        <p class="mt-4 text-gray-600">جاري تحميل المقارنة...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';
import Chart from 'chart.js/auto';

const loading = ref(false);
const availableCampaigns = ref([]);
const selectedCampaigns = ref([]);
const comparisonData = ref([]);

const comparisonChartRef = ref(null);
const roiChartRef = ref(null);
let comparisonChart = null;
let roiChart = null;

const metrics = [
  { key: 'impressions', label: 'المشاهدات', format: 'number' },
  { key: 'clicks', label: 'النقرات', format: 'number' },
  { key: 'ctr', label: 'معدل النقر', format: 'percent' },
  { key: 'conversions', label: 'التحويلات', format: 'number' },
  { key: 'conversion_rate', label: 'معدل التحويل', format: 'percent' },
  { key: 'spend', label: 'الإنفاق', format: 'currency' },
  { key: 'cpc', label: 'تكلفة النقرة', format: 'currency' },
  { key: 'cpa', label: 'تكلفة التحويل', format: 'currency' },
  { key: 'roi', label: 'العائد على الاستثمار', format: 'percent' }
];

const fetchCampaigns = async () => {
  try {
    const { data } = await axios.get('/api/v1/campaigns');
    availableCampaigns.value = data.data || [];
  } catch (error) {
    console.error('Error fetching campaigns:', error);
  }
};

const toggleCampaign = (id) => {
  const index = selectedCampaigns.value.indexOf(id);
  if (index > -1) {
    selectedCampaigns.value.splice(index, 1);
  } else if (selectedCampaigns.value.length < 4) {
    selectedCampaigns.value.push(id);
  }
};

const compareNow = async () => {
  if (selectedCampaigns.value.length < 2) return;
  
  loading.value = true;
  try {
    const { data } = await axios.post('/api/v1/analytics/compare-campaigns', {
      campaign_ids: selectedCampaigns.value
    });
    comparisonData.value = data.campaigns || [];
    await nextTick();
    renderCharts();
  } catch (error) {
    console.error('Error comparing campaigns:', error);
  } finally {
    loading.value = false;
  }
};

const renderCharts = () => {
  if (comparisonChart) comparisonChart.destroy();
  if (roiChart) roiChart.destroy();

  const labels = comparisonData.value.map(c => c.name);
  const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444'];

  if (comparisonChartRef.value) {
    comparisonChart = new Chart(comparisonChartRef.value, {
      type: 'bar',
      data: {
        labels: ['المشاهدات', 'النقرات', 'التحويلات'],
        datasets: comparisonData.value.map((c, i) => ({
          label: c.name,
          data: [c.impressions / 1000, c.clicks, c.conversions],
          backgroundColor: colors[i]
        }))
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'top', rtl: true } }
      }
    });
  }

  if (roiChartRef.value) {
    roiChart = new Chart(roiChartRef.value, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'ROI %',
          data: comparisonData.value.map(c => c.roi),
          backgroundColor: colors
        }]
      },
      options: {
        responsive: true,
        indexAxis: 'y',
        plugins: { legend: { display: false } }
      }
    });
  }
};

const formatValue = (value, format) => {
  if (value === undefined || value === null) return '-';
  switch (format) {
    case 'number': return value.toLocaleString('ar-SA');
    case 'percent': return value.toFixed(2) + '%';
    case 'currency': return value.toLocaleString('ar-SA') + ' ر.س';
    default: return value;
  }
};

const isBest = (key, value) => {
  const values = comparisonData.value.map(c => c[key]);
  const shouldBeLowest = ['cpc', 'cpa', 'spend'].includes(key);
  return shouldBeLowest ? value === Math.min(...values) : value === Math.max(...values);
};

const getBestClass = (key, value) => {
  return isBest(key, value) ? 'text-green-600 font-bold' : '';
};

onMounted(() => {
  fetchCampaigns();
});
</script>
