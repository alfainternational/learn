<template>
  <div dir="rtl" class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8 px-4">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">دورة خطط - تخطيط الأعمال</h1>
        <p class="text-gray-600">20 درساً تفاعلياً لإتقان فن تخطيط الأعمال</p>
      </div>

      <!-- Filter Tabs -->
      <div class="flex gap-2 mb-6 flex-wrap">
        <button
          v-for="filter in filters"
          :key="filter.value"
          @click="activeFilter = filter.value"
          :class="[
            'px-4 py-2 rounded-full text-sm font-medium transition-all',
            activeFilter === filter.value
              ? 'bg-blue-600 text-white shadow-lg'
              : 'bg-white text-gray-600 hover:bg-gray-100'
          ]"
        >
          {{ filter.label }} ({{ getFilterCount(filter.value) }})
        </button>
      </div>

      <!-- Progress Overview -->
      <div class="bg-white rounded-2xl p-6 shadow-sm mb-8">
        <div class="flex items-center justify-between mb-4">
          <span class="text-gray-700 font-medium">تقدمك في الدورة</span>
          <span class="text-blue-600 font-bold">{{ overallProgress }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3">
          <div
            class="bg-gradient-to-l from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500"
            :style="{ width: overallProgress + '%' }"
          ></div>
        </div>
      </div>

      <!-- Lessons Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
          v-for="lesson in filteredLessons"
          :key="lesson.id"
          @click="goToLesson(lesson)"
          class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 cursor-pointer group"
        >
          <!-- Lesson Image/Icon -->
          <div :class="[
            'h-32 flex items-center justify-center relative',
            lesson.status === 'completed' ? 'bg-gradient-to-br from-green-400 to-green-500' :
            lesson.status === 'in_progress' ? 'bg-gradient-to-br from-blue-400 to-blue-500' :
            'bg-gradient-to-br from-gray-300 to-gray-400'
          ]">
            <span class="text-5xl">{{ lesson.icon }}</span>
            <span class="absolute top-3 right-3 bg-white/20 backdrop-blur-sm text-white text-xs px-2 py-1 rounded-full">
              درس {{ lesson.order }}
            </span>
            <div v-if="lesson.status === 'completed'" class="absolute top-3 left-3">
              <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>

          <!-- Lesson Content -->
          <div class="p-5">
            <h3 class="font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors">
              {{ lesson.title }}
            </h3>
            <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ lesson.description }}</p>

            <!-- Progress Bar -->
            <div class="mb-3">
              <div class="flex justify-between text-xs mb-1">
                <span class="text-gray-500">التقدم</span>
                <span :class="lesson.progress === 100 ? 'text-green-600' : 'text-blue-600'">
                  {{ lesson.progress }}%
                </span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  :class="[
                    'h-2 rounded-full transition-all',
                    lesson.progress === 100 ? 'bg-green-500' : 'bg-blue-500'
                  ]"
                  :style="{ width: lesson.progress + '%' }"
                ></div>
              </div>
            </div>

            <!-- Meta Info -->
            <div class="flex items-center justify-between text-xs text-gray-400">
              <span>{{ lesson.duration }} دقيقة</span>
              <span :class="[
                'px-2 py-1 rounded-full',
                lesson.status === 'completed' ? 'bg-green-100 text-green-700' :
                lesson.status === 'in_progress' ? 'bg-blue-100 text-blue-700' :
                'bg-gray-100 text-gray-600'
              ]">
                {{ getStatusLabel(lesson.status) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const activeFilter = ref('all')

const filters = [
  { value: 'all', label: 'الكل' },
  { value: 'completed', label: 'مكتمل' },
  { value: 'in_progress', label: 'جاري' },
  { value: 'not_started', label: 'لم يبدأ' }
]

const lessons = ref([
  { id: 1, order: 1, title: 'مقدمة في تخطيط الأعمال', description: 'تعرف على أساسيات تخطيط الأعمال وأهميته', icon: '📚', duration: 15, progress: 100, status: 'completed' },
  { id: 2, order: 2, title: 'تحليل السوق', description: 'كيفية دراسة وتحليل السوق المستهدف', icon: '📊', duration: 20, progress: 100, status: 'completed' },
  { id: 3, order: 3, title: 'نموذج العمل التجاري', description: 'بناء Business Model Canvas', icon: '🎯', duration: 25, progress: 75, status: 'in_progress' },
  { id: 4, order: 4, title: 'دراسة المنافسين', description: 'تحليل نقاط القوة والضعف للمنافسين', icon: '🔍', duration: 20, progress: 30, status: 'in_progress' },
  { id: 5, order: 5, title: 'القيمة المقترحة', description: 'صياغة عرض القيمة الفريد', icon: '💎', duration: 18, progress: 0, status: 'not_started' },
  { id: 6, order: 6, title: 'شرائح العملاء', description: 'تحديد وفهم العملاء المستهدفين', icon: '👥', duration: 22, progress: 0, status: 'not_started' },
  { id: 7, order: 7, title: 'قنوات التوزيع', description: 'اختيار القنوات المناسبة للوصول للعملاء', icon: '📡', duration: 15, progress: 0, status: 'not_started' },
  { id: 8, order: 8, title: 'مصادر الإيرادات', description: 'تحديد طرق تحقيق الدخل', icon: '💰', duration: 20, progress: 0, status: 'not_started' },
  { id: 9, order: 9, title: 'هيكل التكاليف', description: 'تحليل وإدارة التكاليف', icon: '📉', duration: 25, progress: 0, status: 'not_started' },
  { id: 10, order: 10, title: 'الموارد الرئيسية', description: 'تحديد الموارد اللازمة للنجاح', icon: '🏗️', duration: 18, progress: 0, status: 'not_started' },
  { id: 11, order: 11, title: 'الأنشطة الرئيسية', description: 'العمليات الأساسية للمشروع', icon: '⚙️', duration: 20, progress: 0, status: 'not_started' },
  { id: 12, order: 12, title: 'الشركاء الرئيسيون', description: 'بناء شبكة الشراكات', icon: '🤝', duration: 15, progress: 0, status: 'not_started' },
  { id: 13, order: 13, title: 'التخطيط المالي', description: 'إعداد التوقعات المالية', icon: '📈', duration: 30, progress: 0, status: 'not_started' },
  { id: 14, order: 14, title: 'خطة التسويق', description: 'استراتيجيات التسويق الفعالة', icon: '📣', duration: 25, progress: 0, status: 'not_started' },
  { id: 15, order: 15, title: 'الفريق والهيكل التنظيمي', description: 'بناء فريق العمل', icon: '👔', duration: 20, progress: 0, status: 'not_started' },
  { id: 16, order: 16, title: 'تحليل المخاطر', description: 'تحديد وإدارة المخاطر', icon: '⚠️', duration: 22, progress: 0, status: 'not_started' },
  { id: 17, order: 17, title: 'خطة التنفيذ', description: 'الجدول الزمني والمراحل', icon: '📅', duration: 20, progress: 0, status: 'not_started' },
  { id: 18, order: 18, title: 'مؤشرات الأداء', description: 'قياس النجاح بـ KPIs', icon: '🎯', duration: 18, progress: 0, status: 'not_started' },
  { id: 19, order: 19, title: 'العرض التقديمي', description: 'إعداد Pitch Deck احترافي', icon: '🎤', duration: 25, progress: 0, status: 'not_started' },
  { id: 20, order: 20, title: 'المراجعة النهائية', description: 'تجميع ومراجعة خطة العمل', icon: '✅', duration: 30, progress: 0, status: 'not_started' }
])

const filteredLessons = computed(() => {
  if (activeFilter.value === 'all') return lessons.value
  return lessons.value.filter(l => l.status === activeFilter.value)
})

const overallProgress = computed(() => {
  const total = lessons.value.reduce((sum, l) => sum + l.progress, 0)
  return Math.round(total / lessons.value.length)
})

const getFilterCount = (filter) => {
  if (filter === 'all') return lessons.value.length
  return lessons.value.filter(l => l.status === filter).length
}

const getStatusLabel = (status) => {
  const labels = {
    completed: 'مكتمل',
    in_progress: 'جاري',
    not_started: 'لم يبدأ'
  }
  return labels[status]
}

const goToLesson = (lesson) => {
  router.push({ name: 'lesson.show', params: { id: lesson.id } })
}
</script>
