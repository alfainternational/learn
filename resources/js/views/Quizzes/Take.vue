<template>
  <div dir="rtl" class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8 px-4">
    <div class="max-w-3xl mx-auto">
      <!-- Quiz Header -->
      <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
        <div class="flex items-center justify-between mb-4">
          <h1 class="text-xl font-bold text-gray-800">اختبار: {{ quiz.title }}</h1>
          <div v-if="quiz.hasTimer" class="flex items-center gap-2 text-orange-600 bg-orange-50 px-4 py-2 rounded-full">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-mono font-bold">{{ formatTime(timeLeft) }}</span>
          </div>
        </div>

        <!-- Progress -->
        <div class="flex items-center gap-4">
          <div class="flex-1 bg-gray-200 rounded-full h-2">
            <div
              class="bg-blue-600 h-2 rounded-full transition-all"
              :style="{ width: ((currentQuestionIndex + 1) / quiz.questions.length * 100) + '%' }"
            ></div>
          </div>
          <span class="text-sm text-gray-500">{{ currentQuestionIndex + 1 }} / {{ quiz.questions.length }}</span>
        </div>
      </div>

      <!-- Question Navigation -->
      <div class="bg-white rounded-2xl p-4 shadow-sm mb-6">
        <div class="flex flex-wrap gap-2">
          <button
            v-for="(q, index) in quiz.questions"
            :key="index"
            @click="goToQuestion(index)"
            :class="[
              'w-10 h-10 rounded-lg font-medium text-sm transition-all',
              currentQuestionIndex === index ? 'bg-blue-600 text-white' :
              answers[index] !== undefined ? 'bg-green-100 text-green-700 border-2 border-green-300' :
              'bg-gray-100 text-gray-600 hover:bg-gray-200'
            ]"
          >
            {{ index + 1 }}
          </button>
        </div>
      </div>

      <!-- Current Question -->
      <div class="bg-white rounded-2xl p-8 shadow-sm mb-6">
        <div class="mb-6">
          <span :class="[
            'inline-block px-3 py-1 rounded-full text-xs font-medium mb-4',
            currentQuestion.type === 'multiple_choice' ? 'bg-blue-100 text-blue-700' :
            currentQuestion.type === 'true_false' ? 'bg-purple-100 text-purple-700' :
            'bg-orange-100 text-orange-700'
          ]">
            {{ getQuestionTypeLabel(currentQuestion.type) }}
          </span>
          <h2 class="text-lg font-bold text-gray-800">{{ currentQuestion.text }}</h2>
        </div>

        <!-- Multiple Choice -->
        <div v-if="currentQuestion.type === 'multiple_choice'" class="space-y-3">
          <button
            v-for="(option, index) in currentQuestion.options"
            :key="index"
            @click="selectAnswer(index)"
            :class="[
              'w-full p-4 rounded-xl text-right transition-all border-2',
              answers[currentQuestionIndex] === index
                ? 'border-blue-500 bg-blue-50 text-blue-700'
                : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
            ]"
          >
            <div class="flex items-center gap-3">
              <span :class="[
                'w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium',
                answers[currentQuestionIndex] === index ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-600'
              ]">
                {{ ['أ', 'ب', 'ج', 'د'][index] }}
              </span>
              <span>{{ option }}</span>
            </div>
          </button>
        </div>

        <!-- True/False -->
        <div v-if="currentQuestion.type === 'true_false'" class="grid grid-cols-2 gap-4">
          <button
            @click="selectAnswer(true)"
            :class="[
              'p-6 rounded-xl text-center transition-all border-2',
              answers[currentQuestionIndex] === true
                ? 'border-green-500 bg-green-50 text-green-700'
                : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
            ]"
          >
            <span class="text-3xl mb-2 block">✓</span>
            <span class="font-medium">صحيح</span>
          </button>
          <button
            @click="selectAnswer(false)"
            :class="[
              'p-6 rounded-xl text-center transition-all border-2',
              answers[currentQuestionIndex] === false
                ? 'border-red-500 bg-red-50 text-red-700'
                : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
            ]"
          >
            <span class="text-3xl mb-2 block">✗</span>
            <span class="font-medium">خطأ</span>
          </button>
        </div>

        <!-- Multiple Select -->
        <div v-if="currentQuestion.type === 'multiple_select'" class="space-y-3">
          <p class="text-sm text-gray-500 mb-4">اختر جميع الإجابات الصحيحة</p>
          <button
            v-for="(option, index) in currentQuestion.options"
            :key="index"
            @click="toggleMultiSelect(index)"
            :class="[
              'w-full p-4 rounded-xl text-right transition-all border-2',
              isSelected(index)
                ? 'border-blue-500 bg-blue-50 text-blue-700'
                : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50'
            ]"
          >
            <div class="flex items-center gap-3">
              <span :class="[
                'w-6 h-6 rounded flex items-center justify-center text-sm',
                isSelected(index) ? 'bg-blue-500 text-white' : 'border-2 border-gray-300'
              ]">
                <svg v-if="isSelected(index)" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
              </span>
              <span>{{ option }}</span>
            </div>
          </button>
        </div>
      </div>

      <!-- Navigation Buttons -->
      <div class="flex items-center justify-between">
        <button
          v-if="currentQuestionIndex > 0"
          @click="prevQuestion"
          class="flex items-center gap-2 px-6 py-3 bg-white rounded-xl text-gray-600 hover:bg-gray-50"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
          السابق
        </button>
        <div v-else></div>

        <button
          v-if="currentQuestionIndex < quiz.questions.length - 1"
          @click="nextQuestion"
          class="flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700"
        >
          التالي
          <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
        <button
          v-else
          @click="submitQuiz"
          :disabled="!allAnswered"
          :class="[
            'flex items-center gap-2 px-8 py-3 rounded-xl font-medium transition-all',
            allAnswered
              ? 'bg-green-600 text-white hover:bg-green-700'
              : 'bg-gray-300 text-gray-500 cursor-not-allowed'
          ]"
        >
          إرسال الإجابات
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const quiz = ref({
  id: 1,
  title: 'نموذج العمل التجاري',
  hasTimer: true,
  timeLimit: 600, // 10 minutes in seconds
  questions: [
    { type: 'multiple_choice', text: 'كم عدد العناصر في Business Model Canvas؟', options: ['7 عناصر', '9 عناصر', '11 عنصر', '5 عناصر'], correct: 1 },
    { type: 'true_false', text: 'القيمة المقترحة هي العنصر المركزي في نموذج العمل', correct: true },
    { type: 'multiple_select', text: 'أي من التالي يعتبر من مصادر الإيرادات؟', options: ['رسوم الاشتراك', 'تكاليف التشغيل', 'بيع المنتجات', 'رواتب الموظفين'], correct: [0, 2] },
    { type: 'multiple_choice', text: 'ما هو الهدف من تحديد شرائح العملاء؟', options: ['زيادة التكاليف', 'فهم احتياجات العملاء المستهدفين', 'تقليل الإيرادات', 'إلغاء المنتجات'], correct: 1 },
    { type: 'true_false', text: 'الشركاء الرئيسيون لا يؤثرون على نجاح المشروع', correct: false }
  ]
})

const currentQuestionIndex = ref(0)
const answers = ref({})
const timeLeft = ref(quiz.value.timeLimit)
let timer = null

const currentQuestion = computed(() => quiz.value.questions[currentQuestionIndex.value])

const allAnswered = computed(() => {
  return quiz.value.questions.every((_, index) => answers.value[index] !== undefined)
})

const formatTime = (seconds) => {
  const mins = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
}

const getQuestionTypeLabel = (type) => {
  const labels = {
    multiple_choice: 'اختيار واحد',
    true_false: 'صح أو خطأ',
    multiple_select: 'اختيار متعدد'
  }
  return labels[type]
}

const selectAnswer = (answer) => {
  answers.value[currentQuestionIndex.value] = answer
}

const isSelected = (index) => {
  const current = answers.value[currentQuestionIndex.value]
  return Array.isArray(current) && current.includes(index)
}

const toggleMultiSelect = (index) => {
  if (!answers.value[currentQuestionIndex.value]) {
    answers.value[currentQuestionIndex.value] = []
  }
  const arr = answers.value[currentQuestionIndex.value]
  const i = arr.indexOf(index)
  if (i > -1) {
    arr.splice(i, 1)
  } else {
    arr.push(index)
  }
}

const goToQuestion = (index) => { currentQuestionIndex.value = index }
const prevQuestion = () => { if (currentQuestionIndex.value > 0) currentQuestionIndex.value-- }
const nextQuestion = () => { if (currentQuestionIndex.value < quiz.value.questions.length - 1) currentQuestionIndex.value++ }

const submitQuiz = () => {
  router.push({ name: 'quiz.results', params: { id: quiz.value.id }, query: { answers: JSON.stringify(answers.value) } })
}

onMounted(() => {
  if (quiz.value.hasTimer) {
    timer = setInterval(() => {
      if (timeLeft.value > 0) {
        timeLeft.value--
      } else {
        submitQuiz()
      }
    }, 1000)
  }
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>
