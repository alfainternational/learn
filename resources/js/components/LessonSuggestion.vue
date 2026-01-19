<template>
  <div v-if="lessons.length || tools.length" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
    <h4 class="text-sm font-semibold text-blue-800 mb-3 flex items-center gap-2">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      موارد مفيدة لهذا القسم
    </h4>
    
    <!-- الدروس المقترحة -->
    <div v-if="lessons.length" class="mb-3">
      <p class="text-xs text-gray-600 mb-2">دروس مقترحة:</p>
      <div class="flex flex-wrap gap-2">
        <router-link
          v-for="lesson in lessons"
          :key="lesson.id"
          :to="{ name: 'lessons.show', params: { id: lesson.id } }"
          class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-blue-300 rounded-full text-xs text-blue-700 hover:bg-blue-100 transition-colors"
        >
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
          </svg>
          {{ lesson.title }}
        </router-link>
      </div>
    </div>
    
    <!-- الأدوات المرتبطة -->
    <div v-if="tools.length">
      <p class="text-xs text-gray-600 mb-2">أدوات مفيدة:</p>
      <div class="flex flex-wrap gap-2">
        <button
          v-for="tool in tools"
          :key="tool.slug"
          @click="openTool(tool.slug)"
          class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-500 text-white rounded-full text-xs hover:bg-emerald-600 transition-colors"
        >
          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          {{ tool.name }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const props = defineProps({
  planId: {
    type: [Number, String],
    required: true
  },
  sectionType: {
    type: String,
    required: true
  }
})

const emit = defineEmits(['tool-opened'])
const router = useRouter()

const lessons = ref([])
const tools = ref([])
const loading = ref(false)

const fetchSuggestions = async () => {
  loading.value = true
  try {
    const response = await axios.get(`/api/plans/${props.planId}/sections/${props.sectionType}/suggestions`)
    if (response.data.success) {
      lessons.value = response.data.data.lessons || []
      tools.value = response.data.data.tools || []
    }
  } catch (error) {
    console.error('Error fetching suggestions:', error)
  } finally {
    loading.value = false
  }
}

const openTool = (toolSlug) => {
  emit('tool-opened', toolSlug)
  router.push({ name: 'tools.show', params: { slug: toolSlug } })
}

onMounted(fetchSuggestions)

watch(() => props.sectionType, fetchSuggestions)
</script>
