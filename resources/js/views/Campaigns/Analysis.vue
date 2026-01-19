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
          <div>
            <h1 class="text-2xl font-bold text-gray-800">تحليل تفصيلي</h1>
            <p class="text-gray-500">{{ campaign.name }}</p>
          </div>
        </div>

        <!-- Score Overview -->
        <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
          <div class="flex flex-col md:flex-row items-center gap-6">
            <div :class="['w-32 h-32 rounded-full flex items-center justify-center text-white font-bold text-4xl', getScoreColor(analysis.score)]">
              {{ analysis.score }}
            </div>
            <div class="flex-1 text-center md:text-right">
              <h2 class="text-xl font-bold text-gray-800 mb-2">{{ getScoreLabel(analysis.score) }}</h2>
              <p class="text-gray-500 mb-4">{{ analysis.summary }}</p>
              <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                <span v-for="tag in analysis.tags" :key="tag" class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm">
                  {{ tag }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Score Breakdown -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
          <div v-for="(item, key) in scoreBreakdown" :key="key" class="bg-white rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between mb-2">
              <span class="text-gray-500 text-sm">{{ item.label }}</span>
              <span :class="['font-bold', getItemScoreColor(item.score)]">{{ item.score }}/100</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div :class="['h-2 rounded-full', getProgressColor(item.score)]" :style="{ width: item.score + '%' }"></div>
            </div>
          </div>
        </div>

        <!-- Recommendations -->
        <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
          <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            توصيات التحسين
          </h3>
          <div class="space-y-4">
            <div
              v-for="(rec, index) in recommendations"
              :key="index"
              class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl"
            >
              <div :class="['w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0', getPriorityColor(rec.priority)]">
                <span class="text-lg">{{ getPriorityIcon(rec.priority) }}</span>
              </div>
              <div class="flex-1">
                <h4 class="font-medium text-gray-800 mb-1">{{ rec.title }}</h4>
                <p class="text-sm text-gray-500">{{ rec.description }}</p>
                <div class="flex items-center gap-4 mt-2 text-xs">
                  <span class="text-gray-400">التأثير المتوقع: {{ rec.impact }}</span>
                  <span :class="['px-2 py-1 rounded-full', getPriorityBadge(rec.priority)]">
                    {{ getPriorityLabel(rec.priority) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Comparison with Benchmarks -->
        <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
          <h3 class="font-bold text-gray-800 mb-4">مقارنة مع المعايير</h3>
          <div class="space-y-4">
            <div v-for="metric in benchmarkComparison" :key="metric.name" class="flex items-center gap-4">
              <div class="w-32 text-sm text-gray-600">{{ metric.label }}</div>
              <div class="flex-1 flex items-center gap-2">
                <div class="flex-1 bg-gray-200 rounded-full h-4 relative">
                  <div class="absolute inset-y-0 bg-gray-400 rounded-full" :style="{ width: metric.benchmark + '%' }"></div>
                  <div :class="['absolute inset-y-0 rounded-full', metric.value >= metric.benchmark ? 'bg-green-500' : 'bg-orange-500']" :style="{ width: metric.value + '%' }"></div>
                </div>
                <span class="text-sm font-medium w-16">{{ metric.value }}%</span>
              </div>
              <div :class="['text-sm font-medium', metric.value >= metric.benchmark ? 'text-green-600' : 'text-orange-600']">
                {{ metric.value >= metric.benchmark ? 'أعلى' : 'أقل' }} من المعيار
              </div>
            </div>
          </div>
        </div>

        <!-- Comparison with Other Campaigns -->
        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <h3 class="font-bold text-gray-800 mb-4">مقارنة مع حملاتك الأخرى</h3>
          <div v-if="similarCampaigns.length" class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="text-right text-sm text-gray-500 border-b">
                  <th class="pb-3 font-medium">الحملة</th>
                  <th class="pb-3 font-medium">المنصة</th>
                  <th class="pb-3 font-medium">الوصول</th>
                  <th class="pb-3 font-medium">التفاعل</th>
                  <th class="pb-3 font-medium">الدرجة</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="c in similarCampaigns"
                  :key="c.id"
                  :class="['border-b last:border-0', c.id === campaign.id ? 'bg-indigo-50' : '']"
                >
                  <td class="py-3 font-medium">{{ c.name }}</td>
                  <td class="py-3">{{ getPlatformIcon(c.platform) }}</td>
                  <td class="py-3">{{ formatNumber(c.reach) }}</td>
                  <td class="py-3">{{ c.engagement_rate }}%</td>
                  <td class="py-3">
                    <span :class="['px-2 py-1 rounded-full text-sm font-medium', getScoreBadge(c.score)]">
                      {{ c.score }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="text-center text-gray-500 py-8">
            لا توجد حملات مشابهة للمقارنة
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
const analysis = ref({ score: 0, summary: '', tags: [] })

const scoreBreakdown = ref({
  reach: { label: 'الوصول', score: 0 },
  engagement: { label: 'التفاعل', score: 0 },
  conversion: { label: 'التحويل', score: 0 },
  roi: { label: 'العائد', score: 0 }
})

const recommendations = ref([])
const benchmarkComparison = ref([])
const similarCampaigns = ref([])

const fetchAnalysis = async () => {
  try {
    const [campaignRes, analysisRes] = await Promise.all([
      axios.get(`/api/v1/campaigns/${route.params.id}`),
      axios.get(`/api/v1/campaigns/${route.params.id}/analysis`)
    ])
    
    campaign.value = campaignRes.data.data
    const data = analysisRes.data.data || {}
    
    analysis.value = {
      score: data.score || 0,
      summary: data.summary || 'تحليل أداء الحملة',
      tags: data.tags || []
    }
    
    if (data.breakdown) {
      Object.keys(data.breakdown).forEach(key => {
        if (scoreBreakdown.value[key]) {
          scoreBreakdown.value[key].score = data.breakdown[key]
        }
      })
    }
    
    recommendations.value = data.recommendations || [
      { title: 'تحسين وقت النشر', description: 'جرب النشر في أوقات الذروة بين 7-9 مساءً', priority: 'high', impact: '+15% تفاعل' },
      { title: 'زيادة المحتوى المرئي', description: 'أضف المزيد من الفيديوهات القصيرة', priority: 'medium', impact: '+10% وصول' },
      { title: 'تحسين الهاشتاقات', description: 'استخدم هاشتاقات أكثر ملاءمة للجمهور المستهدف', priority: 'low', impact: '+5% ظهور' }
    ]
    
    benchmarkComparison.value = data.benchmarks || [
      { name: 'engagement_rate', label: 'معدل التفاعل', value: 4.5, benchmark: 3.0 },
      { name: 'reach_rate', label: 'معدل الوصول', value: 25, benchmark: 30 },
      { name: 'conversion_rate', label: 'معدل التحويل', value: 2.8, benchmark: 2.5 }
    ]
    
    similarCampaigns.value = data.similar_campaigns || []
  } catch (error) {
    console.error('Error:', error)
  } finally {
    loading.value = false
  }
}

const getScoreColor = (score) => {
  if (score >= 80) return 'bg-green-500'
  if (score >= 60) return 'bg-yellow-500'
  if (score >= 40) return 'bg-orange-500'
  return 'bg-red-500'
}

const getScoreLabel = (score) => {
  if (score >= 80) return 'أداء ممتاز'
  if (score >= 60) return 'أداء جيد'
  if (score >= 40) return 'أداء متوسط'
  return 'يحتاج تحسين'
}

const getItemScoreColor = (score) => {
  if (score >= 80) return 'text-green-600'
  if (score >= 60) return 'text-yellow-600'
  return 'text-orange-600'
}

const getProgressColor = (score) => {
  if (score >= 80) return 'bg-green-500'
  if (score >= 60) return 'bg-yellow-500'
  return 'bg-orange-500'
}

const getPriorityColor = (priority) => {
  const colors = { high: 'bg-red-100', medium: 'bg-yellow-100', low: 'bg-blue-100' }
  return colors[priority] || 'bg-gray-100'
}

const getPriorityIcon = (priority) => {
  const icons = { high: '🔴', medium: '🟡', low: '🔵' }
  return icons[priority] || '⚪'
}

const getPriorityBadge = (priority) => {
  const badges = { high: 'bg-red-100 text-red-700', medium: 'bg-yellow-100 text-yellow-700', low: 'bg-blue-100 text-blue-700' }
  return badges[priority] || 'bg-gray-100 text-gray-700'
}

const getPriorityLabel = (priority) => {
  const labels = { high: 'أولوية عالية', medium: 'أولوية متوسطة', low: 'أولوية منخفضة' }
  return labels[priority] || priority
}

const getPlatformIcon = (platform) => {
  const icons = { facebook: '📘', instagram: '📸', twitter: '🐦', tiktok: '🎵', snapchat: '👻' }
  return icons[platform] || '📱'
}

const getScoreBadge = (score) => {
  if (score >= 80) return 'bg-green-100 text-green-700'
  if (score >= 60) return 'bg-yellow-100 text-yellow-700'
  return 'bg-orange-100 text-orange-700'
}

const formatNumber = (num) => {
  if (!num) return '0'
  if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
  if (num >= 1000) return (num / 1000).toFixed(1) + 'K'
  return num
}

onMounted(fetchAnalysis)
</script>
