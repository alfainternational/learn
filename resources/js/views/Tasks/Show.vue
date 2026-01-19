<template>
  <div class="min-h-screen bg-gray-50 p-6" dir="rtl">
    <div v-if="task" class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="flex items-center gap-4 mb-6">
        <router-link to="/tasks" class="text-gray-500 hover:text-gray-700">→</router-link>
        <h1 class="text-2xl font-bold text-gray-900 flex-1">{{ task.title }}</h1>
        <button @click="deleteTask" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg">حذف</button>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Description -->
          <div class="bg-white rounded-xl p-6">
            <h3 class="font-semibold mb-3">الوصف</h3>
            <textarea v-model="task.description" @blur="updateTask" rows="4" placeholder="أضف وصفاً..."
              class="w-full px-4 py-2 border rounded-lg resize-none"></textarea>
          </div>

          <!-- Checklist -->
          <div class="bg-white rounded-xl p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="font-semibold">قائمة المهام الفرعية</h3>
              <span class="text-sm text-gray-500">{{ completedCount }}/{{ checklist.length }}</span>
            </div>
            <div class="space-y-2 mb-4">
              <div v-for="item in checklist" :key="item.id" class="flex items-center gap-3 group">
                <input type="checkbox" v-model="item.completed" @change="updateChecklistItem(item)"
                  class="w-5 h-5 rounded text-blue-600" />
                <span :class="{ 'line-through text-gray-400': item.completed }" class="flex-1">{{ item.title }}</span>
                <button @click="deleteChecklistItem(item.id)" class="text-red-500 opacity-0 group-hover:opacity-100">×</button>
              </div>
            </div>
            <form @submit.prevent="addChecklistItem" class="flex gap-2">
              <input v-model="newChecklistItem" placeholder="إضافة عنصر جديد..." class="flex-1 px-4 py-2 border rounded-lg" />
              <button type="submit" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">إضافة</button>
            </form>
          </div>

          <!-- Comments -->
          <div class="bg-white rounded-xl p-6">
            <h3 class="font-semibold mb-4">التعليقات</h3>
            <div class="space-y-4 mb-4">
              <div v-for="comment in comments" :key="comment.id" class="flex gap-3">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm">
                  {{ comment.user?.name?.charAt(0) || 'م' }}
                </div>
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-1">
                    <span class="font-medium text-sm">{{ comment.user?.name || 'مستخدم' }}</span>
                    <span class="text-xs text-gray-400">{{ formatDate(comment.created_at) }}</span>
                  </div>
                  <p class="text-gray-700">{{ comment.content }}</p>
                </div>
              </div>
            </div>
            <form @submit.prevent="addComment" class="flex gap-2">
              <input v-model="newComment" placeholder="أضف تعليقاً..." class="flex-1 px-4 py-2 border rounded-lg" />
              <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">إرسال</button>
            </form>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-4">
          <!-- Status -->
          <div class="bg-white rounded-xl p-4">
            <label class="text-sm text-gray-500 block mb-2">الحالة</label>
            <select v-model="task.status" @change="updateTask" class="w-full px-4 py-2 border rounded-lg">
              <option value="todo">قيد الانتظار</option>
              <option value="in_progress">قيد التنفيذ</option>
              <option value="review">للمراجعة</option>
              <option value="completed">مكتمل</option>
            </select>
          </div>

          <!-- Priority -->
          <div class="bg-white rounded-xl p-4">
            <label class="text-sm text-gray-500 block mb-2">الأولوية</label>
            <select v-model="task.priority" @change="updateTask" class="w-full px-4 py-2 border rounded-lg">
              <option value="low">منخفضة</option>
              <option value="medium">متوسطة</option>
              <option value="high">عالية</option>
            </select>
          </div>

          <!-- Due Date -->
          <div class="bg-white rounded-xl p-4">
            <label class="text-sm text-gray-500 block mb-2">تاريخ الاستحقاق</label>
            <input v-model="task.due_date" @change="updateTask" type="date" class="w-full px-4 py-2 border rounded-lg" />
            <p v-if="task.due_date" :class="getDueDateClass()" class="text-sm mt-2">
              {{ getDueDateText() }}
            </p>
          </div>

          <!-- Assignee -->
          <div class="bg-white rounded-xl p-4">
            <label class="text-sm text-gray-500 block mb-2">المسؤول</label>
            <div v-if="task.assignee" class="flex items-center gap-2">
              <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white">
                {{ task.assignee.name?.charAt(0) }}
              </div>
              <span>{{ task.assignee.name }}</span>
            </div>
            <span v-else class="text-gray-400">غير محدد</span>
          </div>

          <!-- Dates -->
          <div class="bg-white rounded-xl p-4 text-sm text-gray-500 space-y-2">
            <p>تاريخ الإنشاء: {{ formatDate(task.created_at) }}</p>
            <p>آخر تحديث: {{ formatDate(task.updated_at) }}</p>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="flex items-center justify-center h-64">
      <div class="animate-spin w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const task = ref(null)
const comments = ref([])
const checklist = ref([])
const newComment = ref('')
const newChecklistItem = ref('')

const completedCount = computed(() => checklist.value.filter(i => i.completed).length)

const formatDate = (d) => new Date(d).toLocaleDateString('ar-SA', { year: 'numeric', month: 'short', day: 'numeric' })

const getDueDateClass = () => {
  const diff = new Date(task.value.due_date) - new Date()
  if (diff < 0) return 'text-red-600'
  if (diff < 86400000) return 'text-yellow-600'
  return 'text-green-600'
}

const getDueDateText = () => {
  const diff = Math.ceil((new Date(task.value.due_date) - new Date()) / 86400000)
  if (diff < 0) return `متأخر بـ ${Math.abs(diff)} يوم`
  if (diff === 0) return 'اليوم'
  if (diff === 1) return 'غداً'
  return `باقي ${diff} يوم`
}

const updateTask = async () => {
  await axios.patch(`/api/v1/tasks/${task.value.id}`, {
    title: task.value.title,
    description: task.value.description,
    status: task.value.status,
    priority: task.value.priority,
    due_date: task.value.due_date
  })
}

const deleteTask = async () => {
  if (confirm('هل أنت متأكد من حذف هذه المهمة؟')) {
    await axios.delete(`/api/v1/tasks/${task.value.id}`)
    router.push('/tasks')
  }
}

const addComment = async () => {
  if (!newComment.value.trim()) return
  const { data } = await axios.post(`/api/v1/tasks/${task.value.id}/comments`, { content: newComment.value })
  comments.value.push(data.data)
  newComment.value = ''
}

const addChecklistItem = async () => {
  if (!newChecklistItem.value.trim()) return
  const { data } = await axios.post(`/api/v1/tasks/${task.value.id}/checklist`, { title: newChecklistItem.value })
  checklist.value.push(data.data)
  newChecklistItem.value = ''
}

const updateChecklistItem = async (item) => {
  await axios.patch(`/api/v1/tasks/${task.value.id}/checklist/${item.id}`, { completed: item.completed })
}

const deleteChecklistItem = async (id) => {
  await axios.delete(`/api/v1/tasks/${task.value.id}/checklist/${id}`)
  checklist.value = checklist.value.filter(i => i.id !== id)
}

onMounted(async () => {
  const { data } = await axios.get(`/api/v1/tasks/${route.params.id}`)
  task.value = data.data
  comments.value = data.data.comments || []
  checklist.value = data.data.checklist || []
})
</script>
