<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="$emit('close')">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6" dir="rtl">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">
          {{ isEditing ? 'تعديل الحدث' : 'إضافة حدث جديد' }}
        </h2>
        <button @click="$emit('close')" class="p-2 hover:bg-gray-100 rounded-lg">
          <XMarkIcon class="w-5 h-5 text-gray-500" />
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <!-- Title -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">عنوان الحدث</label>
          <input
            v-model="form.title"
            type="text"
            required
            class="input-field w-full"
            placeholder="أدخل عنوان الحدث"
          />
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
          <textarea
            v-model="form.description"
            rows="3"
            class="input-field w-full"
            placeholder="وصف الحدث (اختياري)"
          ></textarea>
        </div>

        <!-- Event Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">نوع الحدث</label>
          <select v-model="form.type" class="input-field w-full">
            <option value="meeting">اجتماع</option>
            <option value="deadline">موعد نهائي</option>
            <option value="campaign_launch">إطلاق حملة</option>
            <option value="reminder">تذكير</option>
            <option value="other">أخرى</option>
          </select>
        </div>

        <!-- Date & Time -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ البداية</label>
            <input
              v-model="form.start_date"
              type="date"
              required
              class="input-field w-full"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">وقت البداية</label>
            <input
              v-model="form.start_time"
              type="time"
              class="input-field w-full"
            />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ النهاية</label>
            <input
              v-model="form.end_date"
              type="date"
              class="input-field w-full"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">وقت النهاية</label>
            <input
              v-model="form.end_time"
              type="time"
              class="input-field w-full"
            />
          </div>
        </div>

        <!-- All Day Toggle -->
        <div class="flex items-center gap-2">
          <input
            v-model="form.all_day"
            type="checkbox"
            id="all_day"
            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
          />
          <label for="all_day" class="text-sm text-gray-700">طوال اليوم</label>
        </div>

        <!-- Color -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">اللون</label>
          <div class="flex gap-2">
            <button
              v-for="color in colors"
              :key="color"
              type="button"
              @click="form.color = color"
              class="w-8 h-8 rounded-full border-2 transition-transform"
              :class="[color, form.color === color ? 'ring-2 ring-offset-2 ring-gray-400 scale-110' : '']"
            ></button>
          </div>
        </div>

        <!-- Reminder -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">تذكير قبل</label>
          <select v-model="form.reminder_before" class="input-field w-full">
            <option value="">بدون تذكير</option>
            <option value="15">15 دقيقة</option>
            <option value="30">30 دقيقة</option>
            <option value="60">ساعة واحدة</option>
            <option value="1440">يوم واحد</option>
          </select>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-4 border-t">
          <button
            v-if="isEditing"
            type="button"
            @click="handleDelete"
            class="btn text-red-600 hover:bg-red-50"
          >
            <TrashIcon class="w-5 h-5" />
            حذف
          </button>
          <div class="flex gap-3 mr-auto">
            <button type="button" @click="$emit('close')" class="btn btn-outline">
              إلغاء
            </button>
            <button type="submit" class="btn btn-primary" :disabled="loading">
              {{ loading ? 'جاري الحفظ...' : 'حفظ' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { XMarkIcon, TrashIcon } from '@heroicons/vue/24/outline'
import axios from 'axios'

const props = defineProps({
  event: { type: Object, default: null }
})

const emit = defineEmits(['close', 'save'])

const loading = ref(false)

const colors = [
  'bg-primary-500',
  'bg-green-500',
  'bg-yellow-500',
  'bg-red-500',
  'bg-purple-500',
  'bg-pink-500'
]

const form = ref({
  title: '',
  description: '',
  type: 'meeting',
  start_date: '',
  start_time: '',
  end_date: '',
  end_time: '',
  all_day: false,
  color: 'bg-primary-500',
  reminder_before: ''
})

const isEditing = computed(() => !!props.event?.id)

onMounted(() => {
  if (props.event) {
    const startDate = props.event.start_date ? new Date(props.event.start_date) : new Date()
    const endDate = props.event.end_date ? new Date(props.event.end_date) : null
    
    form.value = {
      id: props.event.id,
      title: props.event.title || '',
      description: props.event.description || '',
      type: props.event.type || 'meeting',
      start_date: startDate.toISOString().split('T')[0],
      start_time: startDate.toTimeString().slice(0, 5),
      end_date: endDate ? endDate.toISOString().split('T')[0] : '',
      end_time: endDate ? endDate.toTimeString().slice(0, 5) : '',
      all_day: props.event.all_day || false,
      color: props.event.color || 'bg-primary-500',
      reminder_before: props.event.reminder_before || ''
    }
  } else {
    const today = new Date()
    form.value.start_date = today.toISOString().split('T')[0]
  }
})

async function handleSubmit() {
  loading.value = true
  try {
    const eventData = {
      ...form.value,
      start_date: form.value.all_day 
        ? form.value.start_date 
        : `${form.value.start_date}T${form.value.start_time || '00:00'}`,
      end_date: form.value.end_date 
        ? (form.value.all_day ? form.value.end_date : `${form.value.end_date}T${form.value.end_time || '23:59'}`)
        : null
    }
    emit('save', eventData)
  } finally {
    loading.value = false
  }
}

async function handleDelete() {
  if (!confirm('هل أنت متأكد من حذف هذا الحدث؟')) return
  
  try {
    await axios.delete(`/api/v1/calendar/events/${props.event.id}`)
    emit('close')
  } catch (error) {
    console.error('Error deleting event:', error)
  }
}
</script>
