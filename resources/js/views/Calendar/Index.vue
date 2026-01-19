<template>
  <div class="space-y-6" dir="rtl">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">التقويم</h1>
      <button @click="openEventModal()" class="btn btn-primary flex items-center gap-2">
        <PlusIcon class="w-5 h-5" />
        إضافة حدث
      </button>
    </div>

    <div class="flex gap-6">
      <!-- Main Calendar -->
      <div class="flex-1 bg-white rounded-xl shadow-sm p-6">
        <!-- Calendar Navigation -->
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center gap-4">
            <button @click="prevMonth" class="p-2 hover:bg-gray-100 rounded-lg">
              <ChevronRightIcon class="w-5 h-5" />
            </button>
            <h2 class="text-xl font-bold text-gray-900">{{ currentMonthName }} {{ currentYear }}</h2>
            <button @click="nextMonth" class="p-2 hover:bg-gray-100 rounded-lg">
              <ChevronLeftIcon class="w-5 h-5" />
            </button>
          </div>
          <div class="flex items-center gap-2">
            <button @click="goToToday" class="btn btn-outline text-sm">اليوم</button>
            <select v-model="filterType" class="input-field text-sm w-32">
              <option value="all">الكل</option>
              <option value="event">أحداث</option>
              <option value="campaign">حملات</option>
              <option value="task">مهام</option>
            </select>
          </div>
        </div>

        <!-- Day Names -->
        <div class="grid grid-cols-7 gap-1 mb-2">
          <div v-for="day in dayNames" :key="day" class="text-center text-sm font-medium text-gray-500 py-2">
            {{ day }}
          </div>
        </div>

        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 gap-1">
          <div
            v-for="(day, index) in calendarDays"
            :key="index"
            @click="selectDate(day)"
            class="min-h-24 p-2 border border-gray-100 rounded-lg cursor-pointer transition-colors"
            :class="{
              'bg-gray-50': !day.isCurrentMonth,
              'bg-white hover:bg-gray-50': day.isCurrentMonth,
              'ring-2 ring-primary-500': day.isToday,
              'bg-primary-50': day.isSelected
            }"
          >
            <div class="text-sm font-medium" :class="day.isCurrentMonth ? 'text-gray-900' : 'text-gray-400'">
              {{ day.date }}
            </div>
            <div class="space-y-1 mt-1">
              <div
                v-for="item in getItemsForDay(day).slice(0, 3)"
                :key="item.id"
                @click.stop="openEventModal(item)"
                class="text-xs p-1 rounded truncate cursor-pointer"
                :class="getItemClass(item.type)"
              >
                {{ item.title }}
              </div>
              <div v-if="getItemsForDay(day).length > 3" class="text-xs text-gray-500">
                +{{ getItemsForDay(day).length - 3 }} المزيد
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Reminders Sidebar -->
      <RemindersList :reminders="reminders" @refresh="fetchReminders" />
    </div>

    <!-- Event Modal -->
    <EventModal
      v-if="showEventModal"
      :event="selectedEvent"
      @close="showEventModal = false"
      @save="handleSaveEvent"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { PlusIcon, ChevronRightIcon, ChevronLeftIcon } from '@heroicons/vue/24/outline'
import axios from 'axios'
import EventModal from './components/EventModal.vue'
import RemindersList from './components/RemindersList.vue'

const currentDate = ref(new Date())
const selectedDate = ref(null)
const filterType = ref('all')
const events = ref([])
const campaigns = ref([])
const tasks = ref([])
const reminders = ref([])
const showEventModal = ref(false)
const selectedEvent = ref(null)
const loading = ref(false)

const dayNames = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت']

const arabicMonths = [
  'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
  'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
]

const currentMonthName = computed(() => arabicMonths[currentDate.value.getMonth()])
const currentYear = computed(() => currentDate.value.getFullYear())

const calendarDays = computed(() => {
  const year = currentDate.value.getFullYear()
  const month = currentDate.value.getMonth()
  const firstDay = new Date(year, month, 1)
  const lastDay = new Date(year, month + 1, 0)
  const startDay = firstDay.getDay()
  const days = []
  const today = new Date()

  // Previous month days
  const prevMonthLastDay = new Date(year, month, 0).getDate()
  for (let i = startDay - 1; i >= 0; i--) {
    days.push({
      date: prevMonthLastDay - i,
      isCurrentMonth: false,
      fullDate: new Date(year, month - 1, prevMonthLastDay - i)
    })
  }

  // Current month days
  for (let i = 1; i <= lastDay.getDate(); i++) {
    const fullDate = new Date(year, month, i)
    days.push({
      date: i,
      isCurrentMonth: true,
      isToday: fullDate.toDateString() === today.toDateString(),
      isSelected: selectedDate.value?.toDateString() === fullDate.toDateString(),
      fullDate
    })
  }

  // Next month days
  const remaining = 42 - days.length
  for (let i = 1; i <= remaining; i++) {
    days.push({
      date: i,
      isCurrentMonth: false,
      fullDate: new Date(year, month + 1, i)
    })
  }

  return days
})

const allItems = computed(() => {
  let items = []
  if (filterType.value === 'all' || filterType.value === 'event') {
    items = items.concat(events.value.map(e => ({ ...e, type: 'event' })))
  }
  if (filterType.value === 'all' || filterType.value === 'campaign') {
    items = items.concat(campaigns.value.map(c => ({ ...c, type: 'campaign' })))
  }
  if (filterType.value === 'all' || filterType.value === 'task') {
    items = items.concat(tasks.value.map(t => ({ ...t, type: 'task' })))
  }
  return items
})

function getItemsForDay(day) {
  return allItems.value.filter(item => {
    const itemDate = new Date(item.start_date || item.due_date)
    return itemDate.toDateString() === day.fullDate.toDateString()
  })
}

function getItemClass(type) {
  const classes = {
    event: 'bg-primary-100 text-primary-700',
    campaign: 'bg-green-100 text-green-700',
    task: 'bg-yellow-100 text-yellow-700'
  }
  return classes[type] || 'bg-gray-100 text-gray-700'
}

function prevMonth() {
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1)
  fetchCalendarData()
}

function nextMonth() {
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1)
  fetchCalendarData()
}

function goToToday() {
  currentDate.value = new Date()
  fetchCalendarData()
}

function selectDate(day) {
  selectedDate.value = day.fullDate
}

function openEventModal(event = null) {
  selectedEvent.value = event
  showEventModal.value = true
}

async function handleSaveEvent(eventData) {
  try {
    if (eventData.id) {
      await axios.put(`/api/v1/calendar/events/${eventData.id}`, eventData)
    } else {
      await axios.post('/api/v1/calendar/events', eventData)
    }
    showEventModal.value = false
    await fetchCalendarData()
  } catch (error) {
    console.error('Error saving event:', error)
  }
}

async function fetchCalendarData() {
  loading.value = true
  try {
    const year = currentDate.value.getFullYear()
    const month = currentDate.value.getMonth() + 1
    const response = await axios.get('/api/v1/calendar', { params: { year, month } })
    events.value = response.data.events || []
    campaigns.value = response.data.campaigns || []
    tasks.value = response.data.tasks || []
  } catch (error) {
    console.error('Error fetching calendar:', error)
  } finally {
    loading.value = false
  }
}

async function fetchReminders() {
  try {
    const response = await axios.get('/api/v1/reminders/upcoming')
    reminders.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching reminders:', error)
  }
}

onMounted(() => {
  fetchCalendarData()
  fetchReminders()
})
</script>
