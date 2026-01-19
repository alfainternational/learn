<template>
  <div class="min-h-screen bg-gray-50 p-6" dir="rtl">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900">لوحة التحليلات</h1>
      <p class="text-gray-600 mt-2">نظرة شاملة على أداء حملاتك التسويقية</p>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 flex items-center gap-4">
      <label class="text-gray-700 font-medium">الفترة الزمنية:</label>
      <select v-model="dateRange" @change="fetchDashboard" class="border rounded-lg px-4 py-2">
        <option value="7d">آخر 7 أيام</option>
        <option value="30d">آخر 30 يوم</option>
        <option value="90d">آخر 90 يوم</option>
        <option value="1y">آخر سنة</option>
      </select>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div v-for="stat in stats" :key="stat.label" 
           class="bg-white rounded-xl shadow-sm p-6 border-r-4"
           :class="stat.borderColor">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">{{ stat.label }}</p>
            <p class="text-2xl font-bold mt-1">{{ stat.value }}</p>
            <p class="text-sm mt-2" :class="stat.changePositive ? 'text-green-600' : 'text-red-600'">
              {{ stat.change }}
            </p>
          </div>
          <div class="text-4xl" :class="stat.iconColor">
            <i :class="stat.icon"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Line Chart - Performance Over Time -->
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">الأداء عبر الزمن</h3>
        <canvas ref="lineChartRef" height="300"></canvas>
      </div>

      <!-- Bar Chart - By Platform -->
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">الأداء حسب المنصة</h3>
        <canvas ref="barChartRef" height="300"></canvas>
      </div>
    </div>

    <!-- Second Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <!-- Pie Chart - Traffic Sources -->
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">مصادر الزيارات</h3>
        <canvas ref="pieChartRef" height="250"></canvas>
      </div>

      <!-- Top Campaigns -->
      <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2">
        <h3 class="text-lg font-semibold mb-4">أفضل الحملات</h3>
        <div class="space-y-4">
          <div v-for="campaign in topCampaigns" :key="campaign.id"
               class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div>
              <p class="font-medium">{{ campaign.name }}</p>
              <p class="text-sm text-gray-500">{{ campaign.platform }}</p>
            </div>
            <div class="text-left">
              <p class="font-bold text-green-600">{{ campaign.roi }}% ROI</p>
              <p class="text-sm text-gray-500">{{ campaign.conversions }} تحويل</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-8">
        <div class="animate-spin w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full mx-auto"></div>
        <p class="mt-4 text-gray-600">جاري تحميل البيانات...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';
import Chart from 'chart.js/auto';

const loading = ref(false);
const dateRange = ref('30d');
const stats = ref([]);
const topCampaigns = ref([]);

const lineChartRef = ref(null);
const barChartRef = ref(null);
const pieChartRef = ref(null);

let lineChart = null;
let barChart = null;
let pieChart = null;

const fetchDashboard = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/v1/analytics/dashboard', {
      params: { date_range: dateRange.value }
    });

    // Update stats
    stats.value = [
      { label: 'إجمالي الحملات', value: data.total_campaigns, change: '+12%', changePositive: true, icon: 'fas fa-bullhorn', iconColor: 'text-blue-500', borderColor: 'border-blue-500' },
      { label: 'إجمالي المشاهدات', value: formatNumber(data.total_impressions), change: '+8%', changePositive: true, icon: 'fas fa-eye', iconColor: 'text-purple-500', borderColor: 'border-purple-500' },
      { label: 'معدل التحويل', value: data.conversion_rate + '%', change: '+2.5%', changePositive: true, icon: 'fas fa-chart-line', iconColor: 'text-green-500', borderColor: 'border-green-500' },
      { label: 'التكلفة لكل تحويل', value: data.cost_per_conversion + ' ر.س', change: '-5%', changePositive: true, icon: 'fas fa-coins', iconColor: 'text-yellow-500', borderColor: 'border-yellow-500' }
    ];

    topCampaigns.value = data.top_campaigns || [];

    await nextTick();
    renderCharts(data);
  } catch (error) {
    console.error('Error fetching dashboard:', error);
  } finally {
    loading.value = false;
  }
};

const renderCharts = (data) => {
  // Destroy existing charts
  if (lineChart) lineChart.destroy();
  if (barChart) barChart.destroy();
  if (pieChart) pieChart.destroy();

  // Line Chart
  if (lineChartRef.value) {
    lineChart = new Chart(lineChartRef.value, {
      type: 'line',
      data: {
        labels: data.time_series?.labels || ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
        datasets: [{
          label: 'المشاهدات',
          data: data.time_series?.impressions || [1200, 1900, 3000, 5000, 4000, 6000],
          borderColor: '#3B82F6',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          fill: true,
          tension: 0.4
        }, {
          label: 'التحويلات',
          data: data.time_series?.conversions || [100, 200, 350, 500, 400, 650],
          borderColor: '#10B981',
          backgroundColor: 'rgba(16, 185, 129, 0.1)',
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'top', rtl: true } }
      }
    });
  }

  // Bar Chart
  if (barChartRef.value) {
    barChart = new Chart(barChartRef.value, {
      type: 'bar',
      data: {
        labels: data.by_platform?.labels || ['فيسبوك', 'انستغرام', 'تويتر', 'سناب شات', 'تيك توك'],
        datasets: [{
          label: 'الميزانية المنفقة',
          data: data.by_platform?.spend || [5000, 8000, 3000, 6000, 4500],
          backgroundColor: ['#1877F2', '#E4405F', '#1DA1F2', '#FFFC00', '#000000']
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } }
      }
    });
  }

  // Pie Chart
  if (pieChartRef.value) {
    pieChart = new Chart(pieChartRef.value, {
      type: 'doughnut',
      data: {
        labels: data.traffic_sources?.labels || ['البحث المباشر', 'الإعلانات', 'وسائل التواصل', 'الإحالات'],
        datasets: [{
          data: data.traffic_sources?.values || [35, 30, 25, 10],
          backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444']
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', rtl: true } }
      }
    });
  }
};

const formatNumber = (num) => {
  if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
  if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
  return num?.toString() || '0';
};

onMounted(() => {
  fetchDashboard();
});
</script>
