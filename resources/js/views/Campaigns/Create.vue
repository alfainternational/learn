<template>
  <div dir="rtl" class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 py-8 px-4">
    <div class="max-w-3xl mx-auto">
      <!-- Header -->
      <div class="flex items-center gap-4 mb-8">
        <button @click="$router.back()" class="p-2 hover:bg-white rounded-lg transition-colors">
          <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
        <div>
          <h1 class="text-2xl font-bold text-gray-800">إنشاء حملة جديدة</h1>
          <p class="text-gray-500">أدخل بيانات الحملة التسويقية</p>
        </div>
      </div>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Basic Info -->
        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <h3 class="font-bold text-gray-800 mb-4">المعلومات الأساسية</h3>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">اسم الحملة *</label>
              <input
                v-model="form.name"
                type="text"
                required
                class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                placeholder="مثال: حملة رمضان 2024"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
              <textarea
                v-model="form.description"
                rows="3"
                class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                placeholder="وصف مختصر للحملة..."
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">الهدف *</label>
              <select
                v-model="form.objective"
                required
                class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
                <option value="">اختر الهدف</option>
                <option value="awareness">زيادة الوعي</option>
                <option value="engagement">زيادة التفاعل</option>
                <option value="traffic">زيادة الزيارات</option>
                <option value="leads">جمع العملاء المحتملين</option>
                <option value="sales">زيادة المبيعات</option>
                <option value="app_installs">تحميل التطبيق</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Platforms -->
        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <h3 class="font-bold text-gray-800 mb-4">المنصات *</h3>
          <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <label
              v-for="platform in platforms"
              :key="platform.value"
              :class="[
                'flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all',
                form.platforms.includes(platform.value)
                  ? 'border-indigo-500 bg-indigo-50'
                  : 'border-gray-200 hover:border-gray-300'
              ]"
            >
              <input
                type="checkbox"
                :value="platform.value"
                v-model="form.platforms"
                class="hidden"
              />
              <span class="text-2xl">{{ platform.icon }}</span>
              <span class="font-medium text-gray-700">{{ platform.label }}</span>
            </label>
          </div>
        </div>

        <!-- Budget & Duration -->
        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <h3 class="font-bold text-gray-800 mb-4">الميزانية والمدة</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">الميزانية (ر.س)</label>
              <input
                v-model.number="form.budget"
                type="number"
                min="0"
                class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                placeholder="0"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">نوع الميزانية</label>
              <select
                v-model="form.budget_type"
                class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
                <option value="total">إجمالية</option>
                <option value="daily">يومية</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ البدء *</label>
              <input
                v-model="form.start_date"
                type="date"
                required
                class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ الانتهاء</label>
              <input
                v-model="form.end_date"
                type="date"
                class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              />
            </div>
          </div>
        </div>

        <!-- Target Audience -->
        <div class="bg-white rounded-2xl p-6 shadow-sm">
          <h3 class="font-bold text-gray-800 mb-4">الجمهور المستهدف</h3>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">الفئة العمرية</label>
              <div class="flex gap-4">
                <input
                  v-model.number="form.target_age_min"
                  type="number"
                  min="13"
                  max="65"
                  class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500"
                  placeholder="من"
                />
                <input
                  v-model.number="form.target_age_max"
                  type="number"
                  min="13"
                  max="65"
                  class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500"
                  placeholder="إلى"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">الجنس</label>
              <div class="flex gap-3">
                <label
                  v-for="gender in genders"
                  :key="gender.value"
                  :class="[
                    'flex-1 p-3 text-center border-2 rounded-xl cursor-pointer transition-all',
                    form.target_gender === gender.value
                      ? 'border-indigo-500 bg-indigo-50'
                      : 'border-gray-200'
                  ]"
                >
                  <input type="radio" :value="gender.value" v-model="form.target_gender" class="hidden" />
                  {{ gender.label }}
                </label>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">المواقع الجغرافية</label>
              <input
                v-model="form.target_locations"
                type="text"
                class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500"
                placeholder="مثال: السعودية، الإمارات، مصر"
              />
            </div>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
          {{ error }}
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-4">
          <button
            type="submit"
            :disabled="submitting"
            class="flex-1 bg-indigo-600 text-white py-3 rounded-xl font-medium hover:bg-indigo-700 transition-colors disabled:opacity-50"
          >
            {{ submitting ? 'جاري الحفظ...' : 'إنشاء الحملة' }}
          </button>
          <button
            type="button"
            @click="saveDraft"
            :disabled="submitting"
            class="px-6 py-3 border border-gray-300 rounded-xl font-medium hover:bg-gray-50 transition-colors"
          >
            حفظ كمسودة
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const submitting = ref(false)
const error = ref('')

const form = reactive({
  name: '',
  description: '',
  objective: '',
  platforms: [],
  budget: null,
  budget_type: 'total',
  start_date: '',
  end_date: '',
  target_age_min: 18,
  target_age_max: 45,
  target_gender: 'all',
  target_locations: '',
  status: 'active'
})

const platforms = [
  { value: 'facebook', label: 'فيسبوك', icon: '📘' },
  { value: 'instagram', label: 'انستغرام', icon: '📸' },
  { value: 'twitter', label: 'تويتر', icon: '🐦' },
  { value: 'tiktok', label: 'تيك توك', icon: '🎵' },
  { value: 'snapchat', label: 'سناب شات', icon: '👻' },
  { value: 'linkedin', label: 'لينكدإن', icon: '💼' }
]

const genders = [
  { value: 'all', label: 'الكل' },
  { value: 'male', label: 'ذكور' },
  { value: 'female', label: 'إناث' }
]

const validateForm = () => {
  if (!form.name.trim()) {
    error.value = 'اسم الحملة مطلوب'
    return false
  }
  if (!form.objective) {
    error.value = 'يرجى اختيار هدف الحملة'
    return false
  }
  if (form.platforms.length === 0) {
    error.value = 'يرجى اختيار منصة واحدة على الأقل'
    return false
  }
  if (!form.start_date) {
    error.value = 'تاريخ البدء مطلوب'
    return false
  }
  error.value = ''
  return true
}

const submitForm = async () => {
  if (!validateForm()) return
  
  submitting.value = true
  try {
    const payload = {
      ...form,
      platform: form.platforms[0], // Primary platform
      target_locations: form.target_locations.split(',').map(s => s.trim()).filter(Boolean)
    }
    
    await axios.post('/api/v1/campaigns', payload)
    router.push({ name: 'campaigns.index' })
  } catch (err) {
    error.value = err.response?.data?.message || 'حدث خطأ أثناء إنشاء الحملة'
  } finally {
    submitting.value = false
  }
}

const saveDraft = async () => {
  form.status = 'draft'
  await submitForm()
}
</script>
