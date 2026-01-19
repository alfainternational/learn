<template>
  <div class="min-h-screen bg-gray-50 p-6" dir="rtl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">المهام</h1>
      <div class="flex gap-3">
        <router-link to="/tasks/board" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
          عرض اللوحة
        </router-link>
        <button @click="showNewTask = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          + مهمة جديدة
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl p-4 mb-6 flex flex-wrap gap-4">
      <input v-model="filters.search" placeholder="بحث..." class="flex-1 min-w-[200px] px-4 py-2 border rounded-lg" />
      <select v-model="filters.status" class="px-4 py-2 border rounded-lg">
        <option value="">كل الحالات</option>
        <option value="todo">قيد الانتظار</option>
        <option value="in_progress">قيد التنفيذ</option>
        <option value="review">للمراجعة</option>
        <option value="completed">مكتمل</option>
      </select>
      <select v-model="filters.priority" class="px-4 py-2 border rounded-lg">
        <option value="">كل الأولويات</option>
        <option value="high">عالية</option>
        <option value="medium">متوسطة</option>
        <option value="low">منخفضة</option>
      </select>
      <select v-model="filters.sort" class="px-4 py-2 border rounded-lg">
        <option value="created_at">الأحدث</option>
        <option value="due_date">تاريخ الاستحقاق</option>
        <option value="priority">الأولوية</option>
      </select>
    </div>

    <!-- Tasks Table -->
    <div class="bg-white rounded-xl overflow-hidden shadow-sm">
      <table class="w-full">
        <thead class="bg-gray-50 border-b">
          <tr>
            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500">المهمة</th>
            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500">الحالة</th>
            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500">الأولوية</th>
            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500">تاريخ الاستحقاق</th>
            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500">المسؤول</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <tr v-for="task in filteredTasks" :key="task.id" 
            @click="$router.push(`/tasks/${task.id}`)"
            class="hover:bg-gray-50 cursor-pointer">
            <td class="px-6 py-4">
              <div class="font-medium text-gray-900">{{ task.title }}</div>
              <div v-if="task.description" class="text-sm text-gray-500 truncate max-w-xs">{{ task.description }}</div>
            </td>
            <td class="px-6 py-4">
              <span :class="getStatusClass(task.status)" class="px-2 py-1 rounded-full text-xs">
                {{ getStatusLabel(task.status) }}
              </span>
            </td>
            <td class="px-6 py-4">
              <span :class="getPriorityClass(task.priority)" class="px-2 py-1 rounded-full text-xs">
                {{ getPriorityLabel(task.priority) }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm" :class="getDueDateClass(task.due_date)">
              {{ task.due_date ? formatDate(task.due_date) : '-' }}
            </td>
            <td class="px-6 py-4">
              <div v-if="task.assignee" class="flex items-center gap-2">
                <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs">
                  {{ task.assignee.name?.charAt(0) }}
                </div>
                <span class="text-sm">{{ task.assignee.name }}</span>
              </div>
              <span v-else class="text-gray-400">-</span>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-if="filteredTasks.length === 0" class="text-center py-12 text-gray-500">
        لا توجد مهام
      </div>
    </div>

    <!-- New Task Modal -->
    <div v-if="showNewTask" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 w-full max-w-lg mx-4">
        <h2 class="text-xl font-bold mb-4">مهمة جديدة</h2>
        <form @submit.prevent="createTask">
          <input v-model="newTask.title" placeholder="عنوان المهمة" required
            class="w-full px-4 py-2 border rounded-lg mb-3" />
          <textarea v-model="newTask.description" placeholder="الوصف" rows="3"
            class="w-full px-4 py-2 border rounded-lg mb-3"></textarea>
          <div class="grid grid-cols-2 gap-3 mb-4">
            <select v-model="newTask.priority" class="px-4 py-2 border rounded-lg">
              <option value="low">منخفضة</option>
              <option value="medium">متوسطة</option>
              <option value="high">عالية</option>
            </select>
            <input v-model="newTask.due_date" type="date" class="px-4 py-2 border rounded-lg" />
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" @click="showNewTask = false" class="px-4 py-2 text-gray-600">إلغاء</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">إنشاء</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const tasks = ref([])
const showNewTask = ref(false)
const filters = ref({ search: '', status: '', priority: '', sort: 'created_at' })
const newTask = ref({ title: '', description: '', priority: 'medium', due_date: '' })

const filteredTasks = computed(() => {
  let result = [...tasks.value]
  if (filters.value.search) {
    result = result.filter(t => t.title.includes(filters.value.search) || t.description?.includes(filters.value.search))
  }
  if (filters.value.status) result = result.filter(t => t.status === filters.value.status)
  if (filters.value.priority) result = result.filter(t => t.priority === filters.value.priority)
  result.sort((a, b) => {
    if (filters.value.sort === 'due_date') return new Date(a.due_date || '9999') - new Date(b.due_date || '9999')
    if (filters.value.sort === 'priority') {
      const order = { high: 0, medium: 1, low: 2 }
      return order[a.priority] - order[b.priority]
    }
    return new Date(b.created_at) - new Date(a.created_at)
  })
  return result
})

const getStatusClass = (s) => ({ 'bg-gray-100 text-gray-700': s === 'todo', 'bg-blue-100 text-blue-700': s === 'in_progress', 'bg-yellow-100 text-yellow-700': s === 'review', 'bg-green-100 text-green-700': s === 'completed' })
const getStatusLabel = (s) => ({ todo: 'قيد الانتظار', in_progress: 'قيد التنفيذ', review: 'للمراجعة', completed: 'مكتمل' }[s])
const getPriorityClass = (p) => ({ 'bg-red-100 text-red-700': p === 'high', 'bg-yellow-100 text-yellow-700': p === 'medium', 'bg-gray-100 text-gray-600': p === 'low' })
const getPriorityLabel = (p) => ({ high: 'عالية', medium: 'متوسطة', low: 'منخفضة' }[p])
const getDueDateClass = (d) => { if (!d) return ''; const diff = new Date(d) - new Date(); return diff < 0 ? 'text-red-600' : diff < 86400000 ? 'text-yellow-600' : '' }
const formatDate = (d) => new Date(d).toLocaleDateString('ar-SA')

const createTask = async () => {
  const { data } = await axios.post('/api/v1/tasks', newTask.value)
  tasks.value.unshift(data.data)
  showNewTask.value = false
  newTask.value = { title: '', description: '', priority: 'medium', due_date: '' }
}

onMounted(async () => { const { data } = await axios.get('/api/v1/tasks'); tasks.value = data.data })
</script>
