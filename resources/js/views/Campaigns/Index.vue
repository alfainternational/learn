<template>
  <div dir="rtl" class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 py-8 px-4">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-800 mb-2">تحليل الحملات</h1>
          <p class="text-gray-600">إدارة وتحليل حملاتك التسويقية</p>
        </div>
        <router-link
          :to="{ name: 'campaigns.create' }"
          class="mt-4 md:mt-0 inline-flex items-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-xl font-medium hover:bg-indigo-700 transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          حملة جديدة
        </router-link>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-2xl p-4 shadow-sm mb-6">
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="البحث في الحملات..."
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
          </div>
          <select v-model="platformFilter" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            <option value="">جميع المنصات</option>
            <option value="facebook">فيسبوك</option>
            <option value="instagram">انستغرام</option>
            <option value="twitter">تويتر</option>
            <option value="tiktok">تيك توك</option>
            <option value="snapchat">سناب شات</option>
          </select>
          <select v-model="statusFilter" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            <option value="">جميع الحالات</option>
            <option value="active">نشطة</option>
            <option value="paused">متوقفة</option>
            <option value="completed">مكتملة</option>
            <option value="draft">مسودة</option>
          </select>
        </div>
      </div>

      <!-- Stats Summary -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-4 shadow-sm">
          <div class="text-2xl font-bold text-indigo-600">{{ stats.totalCampaigns }}</div>
          <div class="text-sm text-gray-500">إجمالي الحملات</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm">
          <div class="text-2xl font-bold text-green-600">{{ stats.activeCampaigns }}</div>
          <div class="text-sm text-gray-500">حملات نشطة</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm">
          <div class="text-2xl font-bold text-blue-600">{{ formatNumber(stats.totalReach) }}</div>
          <div class="text-sm text-gray-500">إجمالي الوصول</div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm">
          <div class="text-2xl font-bold text-purple-600">{{ stats.avgEngagement }}%</div>
          <div class="text-sm text-gray-500">متوسط التفاعل</div>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      </div>

      <!-- Campaigns Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="campaign in filteredCampaigns"
          :key="campaign.id"
          @click="$router.push({ name: 'campaigns.show', params: { id: campaign.id } })"
          class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all cursor-pointer group"
        >
          <!-- Campaign Header -->
          <div :class="['h-24 p-4 flex items-center justify-between', getPlatformBg(campaign.platform)]">
            <div class="text-white">
              <span class="text-3xl">{{ getPlatformIcon(campaign.platform) }}</span>
            </div>
            <span :class="['px-3 py-1 rounded-full text-xs font-medium', getStatusClass(campaign.status)]">
              {{ getStatusLabel(campaign.status) }}
            </span>
          </div>

          <!-- Campaign Content -->
          <div class="p-5">
            <h3 class="font-bold text-gray-800 mb-2 group-hover:text-indigo-600 transition-colors">
              {{ campaign.name }}
            </h3>
            <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ campaign.objective }}</p>

            <!-- Metrics -->
            <div class="grid grid-cols-3 gap-2 text-center mb-4">
              <div class="bg-gray-50 rounded-lg p-2">
                <div class="text-sm font-bold text-gray-700">{{ formatNumber(campaign.metrics?.reach || 0) }}</div>
                <div class="text-xs text-gray-400">وصول</div>
              </div>
              <div class="bg-gray-50 rounded-lg p-2">
                <div class="text-sm font-bold text-gray-700">{{ formatNumber(campaign.metrics?.engagement || 0) }}</div>
                <div class="text-xs text-gray-400">تفاعل</div>
              </div>
              <div class="bg-gray-50 rounded-lg p-2">
                <div class="text-sm font-bold text-gray-700">{{ campaign.metrics?.conversion_rate || 0 }}%</div>
                <div class="text-xs text-gray-400">تحويل</div>
              </div>
            </div>

            <!-- Analysis Score -->
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div :class="['w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm', getScoreColor(campaign.analysis_score)]">
                  {{ campaign.analysis_score || '-' }}
                </div>
                <span class="text-xs text-gray-500">درجة التحليل</span>
              </div>
              <span class="text-xs text-gray-400">{{ formatDate(campaign.created_at) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && filteredCampaigns.length === 0" class="text-center py-12">
        <div class="text-6xl mb-4">📊</div>
        <h3 class="text-xl font-bold text-gray-700 mb-2">لا توجد حملات</h3>
        <p class="text-gray-500 mb-4">ابدأ بإنشاء حملتك الأولى</p>
        <router-link
          :to="{ name: 'campaigns.create' }"
          class="inline-flex items-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-xl"
        >
          إنشاء حملة جديدة
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const loading = ref(true)
const campaigns = ref([])
const searchQuery = ref('')
const platformFilter = ref('')
const statusFilter = ref('')

const stats = ref({
  totalCampaigns: 0,
  activeCampaigns: 0,
  totalReach: 0,
  avgEngagement: 0
})

const filteredCampaigns = computed(() => {
  return campaigns.value.filter(c => {
    const matchSearch = !searchQuery.value || c.name.includes(searchQuery.value)
    const matchPlatform = !platformFilter.value || c.platform === platformFilter.value
    const matchStatus = !statusFilter.value || c.status === statusFilter.value
    return matchSearch && matchPlatform && matchStatus
  })
})

const fetchCampaigns = async () => {
  try {
    const response = await axios.get('/api/v1/campaigns')
    campaigns.value = response.data.data || []
    calculateStats()
  } catch (error) {
    console.error('Error fetching campaigns:', error)
  } finally {
    loading.value = false
  }
}

const calculateStats = () => {
  stats.value.totalCampaigns = campaigns.value.length
  stats.value.activeCampaigns = campaigns.value.filter(c => c.status === 'active').length
  stats.value.totalReach = campaigns.value.reduce((sum, c) => sum + (c.metrics?.reach || 0), 0)
  const engagements = campaigns.value.filter(c => c.metrics?.engagement_rate)
  stats.value.avgEngagement = engagements.length
    ? (engagements.reduce((sum, c) => sum + c.metrics.engagement_rate, 0) / engagements.length).toFixed(1)
    : 0
}

const getPlatformIcon = (platform) => {
  const icons = { facebook: '📘', instagram: '📸', twitter: '🐦', tiktok: '🎵', snapchat: '👻' }
  return icons[platform] || '📱'
}

const getPlatformBg = (platform) => {
  const bgs = {
    facebook: 'bg-gradient-to-r from-blue-600 to-blue-700',
    instagram: 'bg-gradient-to-r from-pink-500 to-purple-600',
    twitter: 'bg-gradient-to-r from-sky-400 to-sky-500',
    tiktok: 'bg-gradient-to-r from-gray-800 to-gray-900',
    snapchat: 'bg-gradient-to-r from-yellow-400 to-yellow-500'
  }
  return bgs[platform] || 'bg-gradient-to-r from-gray-500 to-gray-600'
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

const formatNumber = (num) => {
  if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
  if (num >= 1000) return (num / 1000).toFixed(1) + 'K'
  return num
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('ar-SA')
}

onMounted(fetchCampaigns)
</script>
