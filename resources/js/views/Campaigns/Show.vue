<template>
  <div dir="rtl" class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 py-8 px-4">
    <div class="max-w-6xl mx-auto">
      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      </div>

      <template v-else>
        <!-- Header -->
        <div class="flex items-center gap-4 mb-8">
          <button @click="$router.back()" class="p-2 hover:bg-white rounded-lg transition-colors">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
          <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-800">{{ campaign.name }}</h1>
            <div class="flex items-center gap-3 mt-1">
              <span class="text-2xl">{{ getPlatformIcon(campaign.platform) }}</span>
              <span :class="['px-3 py-1 rounded-full text-xs font-medium', getStatusClass(campaign.status)]">
                {{ getStatusLabel(campaign.status) }}
              </span>
            </div>
          </div>
          <router-link
            :to="{ name: 'campaigns.analysis', params: { id: campaign.id } }"
            class="bg-indigo-600 text-white px-5 py-2 rounded-xl hover:bg-indigo-700 transition-colors"
          >
            تحليل تفصيلي
          </router-link>
        </div>

        <!-- Analysis Score Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-lg font-bold text-gray-800 mb-1">درجة التحليل</h2>
              <p class="text-sm text-gray-500">تقييم أداء الحملة الإجمالي</p>
            </div>
            <div :class="['w-20 h-20 rounded-full flex items-center justify-center text-white font-bold text-2xl', getScoreColor(analysis.score)]">
              {{ analysis.score || 0 }}
            </div>
          </div>
          <div class="mt-4 grid grid-cols-4 gap-4">
            <div v-for="(value, key) in analysis.breakdown" :key="key" class="text-center">
              <div class="text-lg font-bold text-gray-700">{{ value }}</div>
              <div class="text-xs text-gray-500">{{ getBreakdownLabel(key) }}</div>
            </div>
          </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-xl p-5 shadow-sm">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </div>
              <div>
                <div class="text-xl font-bold text-gray-800">{{ formatNumber(metrics.impressions) }}</div>
                <div class="text-xs text-gray-500">مرات الظهور</div>
              </div>
            </div>
          </div>
          <div class="bg-white rounded-xl p-5 shadow-sm">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <div>
                <div class="text-xl font-bold text-gray-800">{{ formatNumber(metrics.reach) }}</div>
                <div class="text-xs text-gray-500">الوصول</div>
              </div>
            </div>
          </div>
          <div class="bg-white rounded-xl p-5 shadow-sm">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
              </div>
              <div>
                <div class="text-xl font-bold text-gray-800">{{ formatNumber(metrics.engagement) }}</div>
                <div class="text-xs text-gray-500">التفاعل</div>
              </div>
            </div>
          </div>
          <div class="bg-white rounded-xl p-5 shadow-sm">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
              </div>
              <div>
                <div class="text-xl font-bold text-gray-800">{{ metrics.conversion_rate }}%</div>
                <div class="text-xs text-gray-500">معدل التحويل</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
          <!-- Performance Chart -->
          <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4">أداء الحملة</h3>
            <div class="h-64 flex items-end justify-around gap-2">
              <div v-for="(day, index) in performanceData" :key="index" class="flex flex-col items-center">
                <div
                  class="w-8 bg-gradient-to-t from-indigo-500 to-indigo-400 rounded-t-lg transition-all"
                  :style="{ height: day.value + '%' }"
                ></div>
                <span class="text-xs text-gray-500 mt-2">{{ day.label }}</span>
              </div>
            </div>
          </div>

          <!-- Engagement Breakdown -->
          <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4">توزيع التفاعل</h3>
            <div class="space-y-4">
              <div v-for="item in engagementBreakdown" :key="item.type" class="flex items-center gap-4">
                <span class="text-2xl">{{ item.icon }}</span>
                <div class="flex-1">
                  <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-700">{{ item.label }}</span>
                    <span class="font-bold">{{ formatNumber(item.value) }}</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div :class="['h-2 rounded-full', item.color]" :style="{ width: item.percentage + '%' }"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Campaign Details -->
        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <h3 class="font-bold text-gray-800 mb-4">تفاصيل الحملة</h3>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
              <div class="text-xs text-gray-500 mb-1">الهدف</div>
              <div class="font-medium text-gray-800">{{ campaign.objective }}</div>
            </div>
            <div>
              <div class="text-xs text-gray-500 mb-1">الميزانية</div>
              <div class="font-medium text-gray-800">{{ campaign.budget }} ر.س</div>
            </div>
            <div>
              <div class="text-xs text-gray-500 mb-1">تاريخ البدء</div>
              <div class="font-medium text-gray-800">{{ formatDate(campaign.start_date) }}</div>
            </div>
            <div>
              <div class="text-xs text-gray-500 mb-1">تاريخ الانتهاء</div>
              <div class="font-medium text-gray-800">{{ formatDate(campaign.end_date) }}</div>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const loading = ref(true)
const campaign = ref({})
const metrics = ref({})
const analysis = ref({ score: 0, breakdown: {} })

const performanceData = ref([
  { label: 'سبت', value: 45 },
  { label: 'أحد', value: 65 },
  { label: 'إثن', value: 55 },
  { label: 'ثلا', value: 80 },
  { label: 'أرب', value: 70 },
  { label: 'خمي', value: 90 },
  { label: 'جمع', value: 60 }
])

const engagementBreakdown = ref([
  { type: 'likes', label: 'إعجابات', icon: '❤️', value: 0, percentage: 0, color: 'bg-red-500' },
  { type: 'comments', label: 'تعليقات', icon: '💬', value: 0, percentage: 0, color: 'bg-blue-500' },
  { type: 'shares', label: 'مشاركات', icon: '🔄', value: 0, percentage: 0, color: 'bg-green-500' },
  { type: 'saves', label: 'حفظ', icon: '🔖', value: 0, percentage: 0, color: 'bg-purple-500' }
])

const fetchCampaign = async () => {
  try {
    const response = await axios.get(`/api/v1/campaigns/${route.params.id}`)
    campaign.value = response.data.data
    metrics.value = campaign.value.metrics || {}
    
    // Fetch analysis
    const analysisRes = await axios.get(`/api/v1/campaigns/${route.params.id}/analysis`)
    analysis.value = analysisRes.data.data || { score: 0, breakdown: {} }
    
    updateEngagementBreakdown()
  } catch (error) {
    console.error('Error:', error)
  } finally {
    loading.value = false
  }
}

const updateEngagementBreakdown = () => {
  const total = (metrics.value.likes || 0) + (metrics.value.comments || 0) + 
                (metrics.value.shares || 0) + (metrics.value.saves || 0)
  if (total > 0) {
    engagementBreakdown.value[0].value = metrics.value.likes || 0
    engagementBreakdown.value[0].percentage = ((metrics.value.likes || 0) / total * 100).toFixed(0)
    engagementBreakdown.value[1].value = metrics.value.comments || 0
    engagementBreakdown.value[1].percentage = ((metrics.value.comments || 0) / total * 100).toFixed(0)
    engagementBreakdown.value[2].value = metrics.value.shares || 0
    engagementBreakdown.value[2].percentage = ((metrics.value.shares || 0) / total * 100).toFixed(0)
    engagementBreakdown.value[3].value = metrics.value.saves || 0
    engagementBreakdown.value[3].percentage = ((metrics.value.saves || 0) / total * 100).toFixed(0)
  }
}

const getPlatformIcon = (platform) => {
  const icons = { facebook: '📘', instagram: '📸', twitter: '🐦', tiktok: '🎵', snapchat: '👻' }
  return icons[platform] || '📱'
}

const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-700',
    paused: 'bg-yellow-100 text-yellow-700',
    completed: 'bg-blue-100 text-blue-700',
    draft: 'bg-gray-100 text-gray-700'
  }
  return classes[status] || 'bg-gray-100 text-gray-700'
}

const getStatusLabel = (status) => {
  const labels = { active: 'نشطة', paused: 'متوقفة', completed: 'مكتملة', draft: 'مسودة' }
  return labels[status] || status
}

const getScoreColor = (score) => {
  if (score >= 80) return 'bg-green-500'
  if (score >= 60) return 'bg-yellow-500'
  if (score >= 40) return 'bg-orange-500'
  return 'bg-red-500'
}

const getBreakdownLabel = (key) => {
  const labels = { reach: 'الوصول', engagement: 'التفاعل', conversion: 'التحويل', roi: 'العائد' }
  return labels[key] || key
}

const formatNumber = (num) => {
  if (!num) return '0'
  if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
  if (num >= 1000) return (num / 1000).toFixed(1) + 'K'
  return num
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('ar-SA')
}

onMounted(fetchCampaign)
</script>
