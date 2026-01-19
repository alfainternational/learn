<template>
  <div class="w-80 bg-white rounded-xl shadow-sm p-4" dir="rtl">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-bold text-gray-900 flex items-center gap-2">
        <BellIcon class="w-5 h-5 text-primary-500" />
        التذكيرات القادمة
      </h3>
      <button @click="$emit('refresh')" class="p-1 hover:bg-gray-100 rounded">
        <ArrowPathIcon class="w-4 h-4 text-gray-500" />
      </button>
    </div>

    <div v-if="reminders.length === 0" class="text-center py-8 text-gray-500">
      <BellSlashIcon class="w-12 h-12 mx-auto mb-2 text-gray-300" />
      <p class="text-sm">لا توجد تذكيرات قادمة</p>
    </div>

    <div v-else class="space-y-3">
      <div
        v-for="reminder in reminders"
        :key="reminder.id"
        class="p-3 rounded-lg border transition-colors"
        :class="isUrgent(reminder) ? 'border-red-200 bg-red-50' : 'border-gray-100 hover:bg-gray-50'"
      >
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <p class="font-medium text-gray-900 text-sm">{{ reminder.title }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ formatDate(reminder.remind_at) }}</p>
            <p v-if="reminder.description" class="text-xs text-gray-400 mt-1 line-clamp-2">
              {{ reminder.description }}
            </p>
          </div>
          <div class="flex items-center gap-1">
            <button
              @click="markAsDone(reminder)"
              class="p-1 hover:bg-green-100 rounded text-green-600"
              title="تم"
            >
              <CheckIcon class="w-4 h-4" />
            </button>
            <button
              @click="snoozeReminder(reminder)"
              class="p-1 hover:bg-gray-100 rounded text-gray-500"
              title="تأجيل"
            >
              <ClockIcon class="w-4 h-4" />
            </button>
          </div>
        </div>
        
        <div v-if="isUrgent(reminder)" class="mt-2">
          <span class="text-xs text-red-600 font-medium flex items-center gap-1">
            <ExclamationTriangleIcon class="w-3 h-3" />
            عاجل
          </span>
        </div>
      </div>
    </div>

    <!-- Quick Add Reminder -->
    <div class="mt-4 pt-4 border-t">
      <button
        @click="showQuickAdd = !showQuickAdd"
        class="w-full text-sm text-primary-600 hover:text-primary-700 flex items-center justify-center gap-1"
      >
        <PlusIcon class="w-4 h-4" />
        إضافة تذكير سريع
      </button>

      <div v-if="showQuickAdd" class="mt-3 space-y-2">
        <input
          v-model="quickReminder.title"
          type="text"
          class="input-field w-full text-sm"
          placeholder="عنوان التذكير"
        />
        <input
          v-model="quickReminder.remind_at"
          type="datetime-local"
          class="input-field w-full text-sm"
        />
        <button
          @click="addQuickReminder"
          class="btn btn-primary w-full text-sm"
          :disabled="!quickReminder.title"
        >
          إضافة
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import {
  BellIcon,
  BellSlashIcon,
  ArrowPathIcon,
  CheckIcon,
  ClockIcon,
  PlusIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'
import axios from 'axios'

defineProps({
  reminders: { type: Array, default: () => [] }
})

const emit = defineEmits(['refresh'])

const showQuickAdd = ref(false)
const quickReminder = ref({
  title: '',
  remind_at: ''
})

function formatDate(dateStr) {
  const date = new Date(dateStr)
  const now = new Date()
  const diff = date - now
  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  const hours = Math.floor(diff / (1000 * 60 * 60))
  const minutes = Math.floor(diff / (1000 * 60))

  if (minutes < 60) return `خلال ${minutes} دقيقة`
  if (hours < 24) return `خلال ${hours} ساعة`
  if (days === 1) return 'غداً'
  if (days < 7) return `خلال ${days} أيام`
  
  return date.toLocaleDateString('ar-SA', { 
    day: 'numeric', 
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function isUrgent(reminder) {
  const date = new Date(reminder.remind_at)
  const now = new Date()
  const diff = date - now
  return diff < 1000 * 60 * 60 // Less than 1 hour
}

async function markAsDone(reminder) {
  try {
    await axios.post(`/api/v1/reminders/${reminder.id}/mark-sent`)
    emit('refresh')
  } catch (error) {
    console.error('Error marking reminder:', error)
  }
}

async function snoozeReminder(reminder) {
  try {
    const newTime = new Date(Date.now() + 30 * 60 * 1000) // 30 minutes later
    await axios.put(`/api/v1/reminders/${reminder.id}`, {
      ...reminder,
      remind_at: newTime.toISOString()
    })
    emit('refresh')
  } catch (error) {
    console.error('Error snoozing reminder:', error)
  }
}

async function addQuickReminder() {
  if (!quickReminder.value.title) return
  
  try {
    await axios.post('/api/v1/reminders', {
      title: quickReminder.value.title,
      remind_at: quickReminder.value.remind_at || new Date(Date.now() + 60 * 60 * 1000).toISOString()
    })
    quickReminder.value = { title: '', remind_at: '' }
    showQuickAdd.value = false
    emit('refresh')
  } catch (error) {
    console.error('Error adding reminder:', error)
  }
}
</script>
