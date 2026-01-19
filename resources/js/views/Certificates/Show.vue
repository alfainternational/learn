<template>
  <div dir="rtl" class="min-h-screen bg-gradient-to-br from-slate-100 to-blue-100 py-8 px-4">
    <div class="max-w-4xl mx-auto">
      <!-- Actions Bar -->
      <div class="flex items-center justify-between mb-6">
        <button @click="goBack" class="flex items-center gap-2 text-gray-600 hover:text-blue-600">
          <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
          العودة
        </button>
        <div class="flex gap-3">
          <button
            @click="downloadPDF"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            تحميل PDF
          </button>
          <button
            @click="shareCertificate"
            class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
            </svg>
            مشاركة
          </button>
        </div>
      </div>

      <!-- Certificate -->
      <div ref="certificateRef" class="bg-white rounded-3xl shadow-2xl overflow-hidden">
        <!-- Decorative Header -->
        <div class="h-4 bg-gradient-to-l from-blue-600 via-purple-600 to-blue-600"></div>
        
        <div class="p-12 text-center relative">
          <!-- Corner Decorations -->
          <div class="absolute top-8 right-8 w-24 h-24 border-t-4 border-r-4 border-blue-200 rounded-tr-3xl"></div>
          <div class="absolute top-8 left-8 w-24 h-24 border-t-4 border-l-4 border-blue-200 rounded-tl-3xl"></div>
          <div class="absolute bottom-8 right-8 w-24 h-24 border-b-4 border-r-4 border-blue-200 rounded-br-3xl"></div>
          <div class="absolute bottom-8 left-8 w-24 h-24 border-b-4 border-l-4 border-blue-200 rounded-bl-3xl"></div>

          <!-- Logo -->
          <div class="mb-8">
            <span class="text-5xl">📋</span>
            <h2 class="text-2xl font-bold text-blue-600 mt-2">خطط</h2>
          </div>

          <!-- Title -->
          <h1 class="text-4xl font-bold text-gray-800 mb-2">شهادة إتمام</h1>
          <p class="text-gray-500 mb-8">Certificate of Completion</p>

          <!-- Recipient -->
          <p class="text-gray-600 mb-2">تشهد منصة خطط بأن</p>
          <h2 class="text-3xl font-bold text-gray-800 mb-2 py-2 border-b-2 border-blue-200 inline-block px-8">
            {{ certificate.recipientName }}
          </h2>

          <!-- Course Info -->
          <p class="text-gray-600 mt-8 mb-2">قد أتم بنجاح دورة</p>
          <h3 class="text-2xl font-bold text-blue-600 mb-4">{{ certificate.courseName }}</h3>
          
          <p class="text-gray-500 mb-8">
            بإجمالي {{ certificate.totalLessons }} درساً و {{ certificate.totalHours }} ساعة تعليمية
          </p>

          <!-- Achievement Badge -->
          <div class="inline-flex items-center gap-2 bg-gradient-to-l from-yellow-100 to-orange-100 px-6 py-3 rounded-full mb-8">
            <span class="text-2xl">🏆</span>
            <span class="font-bold text-orange-700">متميز</span>
          </div>

          <!-- Date & ID -->
          <div class="flex justify-center gap-12 text-sm text-gray-500 mb-8">
            <div>
              <span class="block text-gray-400">تاريخ الإصدار</span>
              <span class="font-medium text-gray-700">{{ certificate.issueDate }}</span>
            </div>
            <div>
              <span class="block text-gray-400">رقم الشهادة</span>
              <span class="font-medium text-gray-700">{{ certificate.certificateId }}</span>
            </div>
          </div>

          <!-- Signature -->
          <div class="flex justify-center">
            <div class="text-center">
              <div class="w-32 h-16 border-b-2 border-gray-300 mb-2 mx-auto flex items-end justify-center">
                <span class="text-2xl font-script text-gray-600">خطط</span>
              </div>
              <span class="text-sm text-gray-500">المدير التنفيذي</span>
            </div>
          </div>

          <!-- Verification QR placeholder -->
          <div class="mt-8 pt-8 border-t border-gray-100">
            <p class="text-xs text-gray-400">
              للتحقق من صحة الشهادة: khattit.com/verify/{{ certificate.certificateId }}
            </p>
          </div>
        </div>

        <!-- Decorative Footer -->
        <div class="h-4 bg-gradient-to-l from-blue-600 via-purple-600 to-blue-600"></div>
      </div>

      <!-- Share Modal -->
      <div v-if="showShareModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full">
          <h3 class="text-lg font-bold text-gray-800 mb-4">مشاركة الشهادة</h3>
          
          <div class="grid grid-cols-4 gap-4 mb-6">
            <button class="flex flex-col items-center gap-2 p-4 rounded-xl hover:bg-gray-50">
              <span class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white text-xl">f</span>
              <span class="text-xs text-gray-600">Facebook</span>
            </button>
            <button class="flex flex-col items-center gap-2 p-4 rounded-xl hover:bg-gray-50">
              <span class="w-12 h-12 bg-sky-500 rounded-full flex items-center justify-center text-white text-xl">𝕏</span>
              <span class="text-xs text-gray-600">Twitter</span>
            </button>
            <button class="flex flex-col items-center gap-2 p-4 rounded-xl hover:bg-gray-50">
              <span class="w-12 h-12 bg-blue-700 rounded-full flex items-center justify-center text-white text-xl">in</span>
              <span class="text-xs text-gray-600">LinkedIn</span>
            </button>
            <button class="flex flex-col items-center gap-2 p-4 rounded-xl hover:bg-gray-50">
              <span class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white text-xl">📱</span>
              <span class="text-xs text-gray-600">WhatsApp</span>
            </button>
          </div>

          <div class="flex items-center gap-2 p-3 bg-gray-100 rounded-lg">
            <input
              type="text"
              :value="shareLink"
              readonly
              class="flex-1 bg-transparent text-sm text-gray-600 outline-none"
            />
            <button @click="copyLink" class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg">
              نسخ
            </button>
          </div>

          <button @click="showShareModal = false" class="w-full mt-4 py-3 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200">
            إغلاق
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const certificateRef = ref(null)
const showShareModal = ref(false)

const certificate = ref({
  recipientName: 'أحمد محمد الفهد',
  courseName: 'دورة خطط - تخطيط الأعمال الاحترافي',
  totalLessons: 20,
  totalHours: 8,
  issueDate: '15 يناير 2025',
  certificateId: 'KHT-2025-001234'
})

const shareLink = ref('https://khattit.com/certificates/KHT-2025-001234')

const goBack = () => router.push({ name: 'progress.dashboard' })

const downloadPDF = () => {
  // In real app, generate PDF
  alert('جاري تحميل الشهادة...')
}

const shareCertificate = () => {
  showShareModal.value = true
}

const copyLink = () => {
  navigator.clipboard.writeText(shareLink.value)
  alert('تم نسخ الرابط!')
}
</script>
