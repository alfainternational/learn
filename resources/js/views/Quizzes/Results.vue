<template>
  <div dir="rtl" class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8 px-4">
    <div class="max-w-3xl mx-auto">
      <!-- Results Header -->
      <div :class="[
        'rounded-2xl p-8 shadow-sm mb-6 text-center',
        passed ? 'bg-gradient-to-br from-green-500 to-green-600' : 'bg-gradient-to-br from-orange-500 to-orange-600'
      ]">
        <div class="text-6xl mb-4">{{ passed ? '🎉' : '💪' }}</div>
        <h1 class="text-2xl font-bold text-white mb-2">
          {{ passed ? 'أحسنت! لقد اجتزت الاختبار' : 'حاول مرة أخرى' }}
        </h1>
        <p class="text-white/80">{{ quiz.title }}</p>

        <!-- Score Circle -->
        <div class="mt-6 inline-flex items-center justify-center">
          <div class="w-32 h-32 rounded-full bg-white/20 flex items-center justify-center">
            <div class="text-center">
              <span class="text-4xl font-bold text-white">{{ score }}%</span>
              <span class="block text-sm text-white/80">{{ correctCount }}/{{ totalQuestions }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Summary Stats -->
      <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 text-center shadow-sm">
          <span class="text-2xl font-bold text-green-600">{{ correctCount }}</span>
          <span class="block text-sm text-gray-500">إجابات صحيحة</span>
        </div>
        <div class="bg-white rounded-xl p-4 text-center shadow-sm">
          <span class="text-2xl font-bold text-red-600">{{ wrongCount }}</span>
          <span class="block text-sm text-gray-500">إجابات خاطئة</span>
        </div>
        <div class="bg-white rounded-xl p-4 text-center shadow-sm">
          <span class="text-2xl font-bold text-blue-600">{{ timeSpent }}</span>
          <span class="block text-sm text-gray-500">الوقت المستغرق</span>
        </div>
      </div>

      <!-- Answers Review -->
      <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-6">مراجعة الإجابات</h2>

        <div class="space-y-6">
          <div
            v-for="(question, index) in quiz.questions"
            :key="index"
            :class="[
              'p-5 rounded-xl border-2',
              results[index].correct ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'
            ]"
          >
            <!-- Question Header -->
            <div class="flex items-start gap-3 mb-4">
              <span :class="[
                'w-8 h-8 rounded-full flex items-center justify-center text-white text-sm flex-shrink-0',
                results[index].correct ? 'bg-green-500' : 'bg-red-500'
              ]">
                {{ results[index].correct ? '✓' : '✗' }}
              </span>
              <div>
                <span class="text-xs text-gray-500 mb-1 block">سؤال {{ index + 1 }}</span>
                <h3 class="font-medium text-gray-800">{{ question.text }}</h3>
              </div>
            </div>

            <!-- Your Answer -->
            <div class="mr-11 space-y-2">
              <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">إجابتك:</span>
                <span :class="results[index].correct ? 'text-green-700' : 'text-red-700'">
                  {{ formatAnswer(question, results[index].userAnswer) }}
                </span>
              </div>

              <!-- Correct Answer (if wrong) -->
              <div v-if="!results[index].correct" class="flex items-center gap-2">
                <span class="text-sm text-gray-500">الإجابة الصحيحة:</span>
                <span class="text-green-700 font-medium">
                  {{ formatAnswer(question, question.correct) }}
                </span>
              </div>

              <!-- Explanation -->
              <div class="mt-3 p-3 bg-white rounded-lg border border-gray-200">
                <span class="text-sm text-gray-500 block mb-1">💡 الشرح:</span>
                <p class="text-sm text-gray-700">{{ question.explanation }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row gap-4">
        <button
          @click="retryQuiz"
          class="flex-1 flex items-center justify-center gap-2 px-6 py-4 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          إعادة المحاولة
        </button>
        <button
          @click="goToLesson"
          class="flex-1 flex items-center justify-center gap-2 px-6 py-4 bg-white text-gray-700 rounded-xl hover:bg-gray-50 transition-all"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
          </svg>
          العودة للدرس
        </button>
        <button
          v-if="passed"
          @click="nextLesson"
          class="flex-1 flex items-center justify-center gap-2 px-6 py-4 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all"
        >
          الدرس التالي
          <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
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

const quiz = ref({
  id: 1,
  title: 'نموذج العمل التجاري',
  lessonId: 3,
  questions: [
    { type: 'multiple_choice', text: 'كم عدد العناصر في Business Model Canvas؟', options: ['7 عناصر', '9 عناصر', '11 عنصر', '5 عناصر'], correct: 1, explanation: 'يتكون Business Model Canvas من 9 عناصر أساسية تغطي جميع جوانب نموذج العمل التجاري.' },
    { type: 'true_false', text: 'القيمة المقترحة هي العنصر المركزي في نموذج العمل', correct: true, explanation: 'نعم، القيمة المقترحة هي جوهر نموذج العمل وتحدد ما يميز منتجك عن المنافسين.' },
    { type: 'multiple_select', text: 'أي من التالي يعتبر من مصادر الإيرادات؟', options: ['رسوم الاشتراك', 'تكاليف التشغيل', 'بيع المنتجات', 'رواتب الموظفين'], correct: [0, 2], explanation: 'رسوم الاشتراك وبيع المنتجات هي مصادر إيرادات، بينما التكاليف والرواتب هي مصروفات.' },
    { type: 'multiple_choice', text: 'ما هو الهدف من تحديد شرائح العملاء؟', options: ['زيادة التكاليف', 'فهم احتياجات العملاء المستهدفين', 'تقليل الإيرادات', 'إلغاء المنتجات'], correct: 1, explanation: 'تحديد شرائح العملاء يساعد في فهم احتياجاتهم وتقديم قيمة مناسبة لهم.' },
    { type: 'true_false', text: 'الشركاء الرئيسيون لا يؤثرون على نجاح المشروع', correct: false, explanation: 'الشركاء الرئيسيون يلعبون دوراً مهماً في نجاح المشروع من خلال توفير الموارد والخبرات.' }
  ]
})

// Simulated user answers (in real app, get from route query or store)
const userAnswers = ref({
  0: 1,
  1: true,
  2: [0, 2],
  3: 1,
  4: true
})

const results = computed(() => {
  return quiz.value.questions.map((q, i) => {
    const userAnswer = userAnswers.value[i]
    let correct = false
    if (q.type === 'multiple_select') {
      correct = JSON.stringify([...userAnswer].sort()) === JSON.stringify([...q.correct].sort())
    } else {
      correct = userAnswer === q.correct
    }
    return { userAnswer, correct }
  })
})

const correctCount = computed(() => results.value.filter(r => r.correct).length)
const wrongCount = computed(() => results.value.filter(r => !r.correct).length)
const totalQuestions = computed(() => quiz.value.questions.length)
const score = computed(() => Math.round((correctCount.value / totalQuestions.value) * 100))
const passed = computed(() => score.value >= 70)
const timeSpent = ref('5:23')

const formatAnswer = (question, answer) => {
  if (question.type === 'true_false') {
    return answer ? 'صحيح' : 'خطأ'
  }
  if (question.type === 'multiple_select' && Array.isArray(answer)) {
    return answer.map(i => question.options[i]).join('، ')
  }
  if (question.type === 'multiple_choice') {
    return question.options[answer]
  }
  return answer
}

const retryQuiz = () => router.push({ name: 'quiz.take', params: { id: quiz.value.id } })
const goToLesson = () => router.push({ name: 'lesson.show', params: { id: quiz.value.lessonId } })
const nextLesson = () => router.push({ name: 'lesson.show', params: { id: quiz.value.lessonId + 1 } })
</script>
