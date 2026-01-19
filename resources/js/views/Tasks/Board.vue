<template>
  <div class="min-h-screen bg-gray-50 p-6" dir="rtl">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">لوحة المهام</h1>
      <div class="flex gap-3">
        <router-link to="/tasks" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
          عرض القائمة
        </router-link>
        <button @click="showNewTask = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          + مهمة جديدة
        </button>
      </div>
    </div>

    <!-- Kanban Board -->
    <div class="flex gap-4 overflow-x-auto pb-4">
      <div
        v-for="column in columns"
        :key="column.id"
        class="flex-shrink-0 w-80 bg-gray-100 rounded-xl p-4"
        @dragover.prevent
        @drop="onDrop($event, column.id)"
      >
        <!-- Column Header -->
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-2">
            <span :class="column.color" class="w-3 h-3 rounded-full"></span>
            <h3 class="font-semibold text-gray-700">{{ column.title }}</h3>
            <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full">
              {{ getTasksByStatus(column.id).length }}
            </span>
          </div>
        </div>

        <!-- Tasks -->
        <div class="space-y-3 min-h-[200px]">
          <div
            v-for="task in getTasksByStatus(column.id)"
            :key="task.id"
            draggable="true"
            @dragstart="onDragStart($event, task)"
            @click="openTask(task)"
            class="bg-white rounded-lg p-4 shadow-sm cursor-pointer hover:shadow-md transition-shadow border-r-4"
            :class="getPriorityBorder(task.priority)"
          >
            <h4 class="font-medium text-gray-900 mb-2">{{ task.title }}</h4>
            <p v-if="task.description" class="text-sm text-gray-500 mb-3 line-clamp-2">
              {{ task.description }}
            </p>
            
            <!-- Task Meta -->
            <div class="flex items-center justify-between text-xs">
              <div class="flex items-center gap-2">
                <span v-if="task.due_date" :class="getDueDateClass(task.due_date)" class="px-2 py-1 rounded">
                  {{ formatDate(task.due_date) }}
                </span>
                <span v-if="task.checklist_count" class="text-gray-500">
                  ✓ {{ task.checklist_completed }}/{{ task.checklist_count }}
                </span>
              </div>
              <div v-if="task.assignee" class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs">
                {{ task.assignee.name?.charAt(0) }}
              </div>
            </div>
          </div>
        </div>
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
              <option value="low">أولوية منخفضة</option>
              <option value="medium">أولوية متوسطة</option>
              <option value="high">أولوية عالية</option>
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
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const tasks = ref([])
const showNewTask = ref(false)
const draggedTask = ref(null)
const newTask = ref({ title: '', description: '', priority: 'medium', due_date: '', status: 'todo' })

const columns = [
  { id: 'todo', title: 'قيد الانتظار', color: 'bg-gray-400' },
  { id: 'in_progress', title: 'قيد التنفيذ', color: 'bg-blue-500' },
  { id: 'review', title: 'للمراجعة', color: 'bg-yellow-500' },
  { id: 'completed', title: 'مكتمل', color: 'bg-green-500' }
]

const getTasksByStatus = (status) => tasks.value.filter(t => t.status === status)

const getPriorityBorder = (priority) => ({
  'border-red-500': priority === 'high',
  'border-yellow-500': priority === 'medium',
  'border-gray-300': priority === 'low'
})

const getDueDateClass = (date) => {
  const diff = new Date(date) - new Date()
  if (diff < 0) return 'bg-red-100 text-red-700'
  if (diff < 86400000) return 'bg-yellow-100 text-yellow-700'
  return 'bg-gray-100 text-gray-600'
}

const formatDate = (date) => new Date(date).toLocaleDateString('ar-SA', { month: 'short', day: 'numeric' })

const onDragStart = (e, task) => { draggedTask.value = task }

const onDrop = async (e, status) => {
  if (draggedTask.value && draggedTask.value.status !== status) {
    await axios.patch(`/api/v1/tasks/${draggedTask.value.id}`, { status })
    draggedTask.value.status = status
  }
  draggedTask.value = null
}

const openTask = (task) => router.push(`/tasks/${task.id}`)

const createTask = async () => {
  const { data } = await axios.post('/api/v1/tasks', newTask.value)
  tasks.value.push(data.data)
  showNewTask.value = false
  newTask.value = { title: '', description: '', priority: 'medium', due_date: '', status: 'todo' }
}

const fetchTasks = async () => {
  const { data } = await axios.get('/api/v1/tasks')
  tasks.value = data.data
}

onMounted(fetchTasks)
</script>
