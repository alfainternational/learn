<template>
  <div dir="rtl" class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8 px-4">
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">لوحة التقدم</h1>
        <p class="text-gray-600">تتبع إنجازاتك في دورة خطط</p>
      </div>

      <!-- Main Stats -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <span class="text-3xl">📚</span>
            <span class="text-3xl font-bold text-blue-600">{{ stats.lessonsCompleted }}/20</span>
          </div>
          <span class="text-gray-600">الدروس المكتملة</span>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <span class="text-3xl">📝</span>
            <span class="text-3xl font-bold text-green-600">{{ stats.quizzesPassed }}</span>
          </div>
          <span class="text-gray-600">الاختبارات المجتازة</span>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <span class="text-3xl">🛠️</span>
            <span class="text-3xl font-bold text-purple-600">{{ stats.toolsUsed }}</span>
          </div>
          <span class="text-gray-600">الأدوات المستخدمة</span>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <span class="text-3xl">⏱️</span>
            <span class="text-3xl font-bold text-orange-600">{{ stats.timeSpent }}</span>
          </div>
          <span class="text-gray-600">ساعات التعلم</span>
        </div>
      </div>

      <!-- Course Progress -->
      <div class="bg-white rounded-2xl p-6 shadow-sm mb-8">
        <h2 class="text-lg font-bold text-gray-800 mb-6">تقدم الدورة</h2>
        
        <div class="flex items-center justify-center mb-6">
          <div class="relative w-48 h-48">
            <svg class="w-full h-full transform -rotate-90">
              <circle cx="96" cy="96" r="88" stroke="#e5e7eb" stroke-width="12" fill="none"/>
              <circle
                cx="96" cy="96" r="88"
                stroke="url(#gradient)"
                stroke-width="12"
                fill="none"
                stroke-linecap="round"
                :stroke-dasharray="553"
                :stroke-dashoffset="553 - (553 * stats.courseProgress / 100)"
              />
              <defs>
                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                  <stop offset="0%" stop-color="#3b82f6"/>
                  <stop offset="100%" stop-color="#8b5cf6"/>
                </linearGradient>
              </defs>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="text-center">
                <span class="text-4xl font-bold text-gray-800">{{ stats.courseProgress }}%</span>
                <span class="block text-sm text-gray-500">مكتمل</span>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-3 gap-4 text-center">
          <div>
            <span class="text-2xl font-bold text-green-600">{{ stats.lessonsCompleted }}</span>
            <span class="block text-sm text-gray-500">دروس مكتملة</span>
          </div>
          <div>
            <span class="text-2xl font-bold text-blue-600">{{ stats.lessonsInProgress }}</span>
            <span class="block text-sm text-gray-500">دروس جارية</span>
          </div>
          <div>
            <span class="text-2xl font-bold text-gray-400">{{ stats.lessonsRemaining }}</span>
            <span class="block text-sm text-gray-500">دروس متبقية</span>
          </div>
        </div>
      </div>

      <div class="grid lg:grid-cols-2 gap-6">
        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <h2 class="text-lg font-bold text-gray-800 mb-4">النشاط الأخير</h2>
          <div class="space-y-4">
            <div v-for="activity in recentActivities" :key="activity.id" class="flex items-center gap-4 p-3 rounded-xl bg-gray-50">
              <span class="text-2xl">{{ activity.icon }}</span>
              <div class="flex-1">
                <p class="font-medium text-gray-800">{{ activity.title }}</p>
                <p class="text-sm text-gray-500">{{ activity.time }}</p>
              </div>
              <span :class="[
                'px-2 py-1 rounded-full text-xs',
                activity.type === 'lesson' ? 'bg-blue-100 text-blue-700' :
                activity.type === 'quiz' ? 'bg-green-100 text-green-700' :
                'bg-purple-100 text-purple-700'
              ]">
                {{ activity.label }}
              </span>
            </div>
          </div>
        </div>

        <!-- Quiz Performance -->
        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <h2 class="text-lg font-bold text-gray-800 mb-4">أداء الاختبارات</h2>
          <div class="space-y-4">
            <div v-for="quiz in quizPerformance" :key="quiz.id">
              <div class="flex justify-between mb-1">
                <span class="text-sm text-gray-700">{{ quiz.title }}</span>
                <span :class="quiz.score >= 70 ? 'text-green-600' : 'text-orange-600'" class="text-sm font-medium">
                  {{ quiz.score }}%
                </span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  :class="quiz.score >= 70 ? 'bg-green-500' : 'bg-orange-500'"
                  class="h-2 rounded-full transition-all"
                  :style="{ width: quiz.score + '%' }"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Certificate CTA -->
      <div v-if="stats.courseProgress === 100" class="mt-8 bg-gradient-to-l from-yellow-500 to-orange-500 rounded-2xl p-8 text-center text-white">
        <span class="text-5xl mb-4 block">🏆</span>
        <h2 class="text-2xl font-bold mb-2">تهانينا! أكملت الدورة</h2>
        <p class="mb-6 text-white/80">احصل على شهادتك الآن</p>
        <button @click="viewCertificate" class="px-8 py-3 bg-white text-orange-600 rounded-xl font-bold hover:bg-gray-100 transition-all">
          عرض الشهادة
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const stats = ref({
  courseProgress: 35,
  lessonsCompleted: 7,
  lessonsInProgress: 2,
  lessonsRemaining: 11,
  quizzesPassed: 5,
  toolsUsed: 4,
  timeSpent: '8.5'
})

const recentActivities = ref([
  { id: 1, icon: '📚', title: 'أكملت درس نموذج العمل التجاري', time: 'منذ ساعة', type: 'lesson', label: 'درس' },
  { id: 2, icon: '📝', title: 'اجتزت اختبار تحليل السوق', time: 'منذ 3 ساعات', type: 'quiz', label: 'اختبار' },
  { id: 3, icon: '🛠️', title: 'استخدمت أداة Business Model Canvas', time: 'أمس', type: 'tool', label: 'أداة' },
  { id: 4, icon: '📚', title: 'بدأت درس القيمة المقترحة', time: 'أمس', type: 'lesson', label: 'درس' }
])

const quizPerformance = ref([
  { id: 1, title: 'مقدمة في تخطيط الأعمال', score: 90 },
  { id: 2, title: 'تحليل السوق', score: 85 },
  { id: 3, title: 'نموذج العمل التجاري', score: 75 },
  { id: 4, title: 'دراسة المنافسين', score: 60 }
])

const viewCertificate = () => router.push({ name: 'certificate.show' })
</script>
