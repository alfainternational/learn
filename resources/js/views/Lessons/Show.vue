<template>
  <div dir="rtl" class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Top Navigation -->
    <div class="bg-white shadow-sm sticky top-0 z-10">
      <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
        <button @click="goBack" class="flex items-center gap-2 text-gray-600 hover:text-blue-600">
          <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          <span>العودة للدروس</span>
        </button>
        <span class="text-sm text-gray-500">درس {{ lesson.order }} من 20</span>
      </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-8">
      <!-- Lesson Header -->
      <div class="bg-white rounded-2xl p-8 shadow-sm mb-6">
        <div class="flex items-start gap-6">
          <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-4xl">
            {{ lesson.icon }}
          </div>
          <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ lesson.title }}</h1>
            <p class="text-gray-600 mb-4">{{ lesson.description }}</p>
            <div class="flex items-center gap-4 text-sm text-gray-500">
              <span class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ lesson.duration }} دقيقة
              </span>
              <span :class="[
                'px-3 py-1 rounded-full text-xs',
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

      <!-- Learning Objectives -->
      <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
          <span class="text-2xl">🎯</span>
          أهداف التعلم
        </h2>
        <ul class="space-y-3">
          <li v-for="(objective, index) in lesson.objectives" :key="index" class="flex items-start gap-3">
            <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm flex-shrink-0">
              {{ index + 1 }}
            </span>
            <span class="text-gray-700">{{ objective }}</span>
          </li>
        </ul>
      </div>

      <!-- Key Concepts -->
      <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
          <span class="text-2xl">💡</span>
          المفاهيم الأساسية
        </h2>
        <div class="grid gap-4 md:grid-cols-2">
          <div
            v-for="(concept, index) in lesson.concepts"
            :key="index"
            class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl p-4 border border-gray-100"
          >
            <h3 class="font-semibold text-gray-800 mb-2">{{ concept.title }}</h3>
            <p class="text-sm text-gray-600">{{ concept.description }}</p>
          </div>
        </div>
      </div>

      <!-- Lesson Content -->
      <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
          <span class="text-2xl">📖</span>
          محتوى الدرس
        </h2>
        <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed" v-html="lesson.content"></div>
      </div>

      <!-- Action Buttons -->
      <div class="grid gap-4 md:grid-cols-2 mb-8">
        <button
          @click="openTool"
          class="bg-gradient-to-l from-purple-500 to-purple-600 text-white rounded-2xl p-6 flex items-center gap-4 hover:shadow-lg transition-all"
        >
          <span class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center text-3xl">🛠️</span>
          <div class="text-right">
            <span class="block font-bold text-lg">الأداة التفاعلية</span>
            <span class="text-purple-100 text-sm">طبق ما تعلمته عملياً</span>
          </div>
        </button>

        <button
          @click="startQuiz"
          class="bg-gradient-to-l from-green-500 to-green-600 text-white rounded-2xl p-6 flex items-center gap-4 hover:shadow-lg transition-all"
        >
          <span class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center text-3xl">📝</span>
          <div class="text-right">
            <span class="block font-bold text-lg">اختبر معلوماتك</span>
            <span class="text-green-100 text-sm">{{ lesson.quizQuestions }} أسئلة</span>
          </div>
        </button>
      </div>

      <!-- Navigation -->
      <div class="flex items-center justify-between">
        <button
          v-if="lesson.order > 1"
          @click="prevLesson"
          class="flex items-center gap-2 px-6 py-3 bg-white rounded-xl text-gray-600 hover:bg-gray-50 transition-all"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
          <span>الدرس السابق</span>
        </button>
        <div v-else></div>

        <button
          v-if="lesson.order < 20"
          @click="nextLesson"
          class="flex items-center gap-2 px-6 py-3 bg-blue-600 rounded-xl text-white hover:bg-blue-700 transition-all"
        >
          <span>الدرس التالي</span>
          <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
        <button
          v-else
          @click="completeCourse"
          class="flex items-center gap-2 px-6 py-3 bg-green-600 rounded-xl text-white hover:bg-green-700 transition-all"
        >
          <span>إنهاء الدورة</span>
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const lesson = ref({
  id: 1,
  order: 3,
  title: 'نموذج العمل التجاري',
  description: 'تعلم كيفية بناء Business Model Canvas لمشروعك وفهم العناصر التسعة الأساسية',
  icon: '🎯',
  duration: 25,
  status: 'in_progress',
  quizQuestions: 10,
  objectives: [
    'فهم مفهوم نموذج العمل التجاري وأهميته',
    'التعرف على العناصر التسعة لـ Business Model Canvas',
    'القدرة على ملء نموذج العمل لمشروعك',
    'تحليل نماذج أعمال شركات ناجحة'
  ],
  concepts: [
    { title: 'شرائح العملاء', description: 'تحديد من هم عملاؤك المستهدفون وما هي احتياجاتهم' },
    { title: 'القيمة المقترحة', description: 'ما الذي يميز منتجك أو خدمتك عن المنافسين' },
    { title: 'القنوات', description: 'كيف تصل إلى عملائك وتقدم لهم القيمة' },
    { title: 'علاقات العملاء', description: 'نوع العلاقة التي تبنيها مع كل شريحة' }
  ],
  content: `
    <p class="mb-4">نموذج العمل التجاري (Business Model Canvas) هو أداة استراتيجية تساعدك على تصور وتحليل وتطوير نموذج عملك بطريقة منظمة وشاملة.</p>
    
    <h3 class="text-xl font-bold mb-3 mt-6">العناصر التسعة للنموذج</h3>
    <p class="mb-4">يتكون النموذج من تسعة عناصر أساسية تغطي جميع جوانب العمل التجاري، من العملاء إلى الإيرادات والتكاليف.</p>
    
    <h3 class="text-xl font-bold mb-3 mt-6">كيفية استخدام النموذج</h3>
    <p class="mb-4">ابدأ بتحديد شرائح العملاء، ثم انتقل إلى القيمة المقترحة، واستمر في ملء باقي العناصر بشكل متسلسل.</p>
  `
})

const getStatusLabel = (status) => {
  const labels = { completed: 'مكتمل', in_progress: 'جاري', not_started: 'لم يبدأ' }
  return labels[status]
}

const goBack = () => router.push({ name: 'courses.index' })
const openTool = () => router.push({ name: 'tool.show', params: { id: lesson.value.id } })
const startQuiz = () => router.push({ name: 'quiz.take', params: { id: lesson.value.id } })
const prevLesson = () => router.push({ name: 'lesson.show', params: { id: lesson.value.id - 1 } })
const nextLesson = () => router.push({ name: 'lesson.show', params: { id: lesson.value.id + 1 } })
const completeCourse = () => router.push({ name: 'certificate.show' })
</script>
