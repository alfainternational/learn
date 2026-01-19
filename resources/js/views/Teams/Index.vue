<template>
  <div dir="rtl" class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8 px-4">
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-800 mb-2">فرق العمل</h1>
          <p class="text-gray-600">إدارة فرقك والتعاون مع زملائك</p>
        </div>
        <button
          @click="showCreateModal = true"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium flex items-center gap-2 transition-all shadow-lg"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          إنشاء فريق جديد
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="teams.length === 0" class="bg-white rounded-2xl p-12 text-center shadow-sm">
        <div class="text-6xl mb-4">👥</div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">لا توجد فرق حتى الآن</h3>
        <p class="text-gray-500 mb-6">قم بإنشاء فريقك الأول للبدء في التعاون</p>
        <button
          @click="showCreateModal = true"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-all"
        >
          إنشاء فريق
        </button>
      </div>

      <!-- Teams Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="team in teams"
          :key="team.id"
          @click="goToTeam(team)"
          class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all cursor-pointer group"
        >
          <!-- Team Header -->
          <div class="flex items-start justify-between mb-4">
            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-2xl font-bold">
              {{ team.name.charAt(0) }}
            </div>
            <span :class="[
              'px-3 py-1 rounded-full text-xs font-medium',
              team.role === 'owner' ? 'bg-yellow-100 text-yellow-700' :
              team.role === 'admin' ? 'bg-purple-100 text-purple-700' :
              'bg-gray-100 text-gray-600'
            ]">
              {{ getRoleLabel(team.role) }}
            </span>
          </div>

          <!-- Team Info -->
          <h3 class="font-bold text-gray-800 text-lg mb-2 group-hover:text-blue-600 transition-colors">
            {{ team.name }}
          </h3>
          <p class="text-gray-500 text-sm mb-4 line-clamp-2">{{ team.description || 'لا يوجد وصف' }}</p>

          <!-- Members Count -->
          <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="flex items-center gap-2 text-gray-500 text-sm">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              <span>{{ team.members_count }} {{ team.members_count === 1 ? 'عضو' : 'أعضاء' }}</span>
            </div>
            <div class="flex -space-x-2 space-x-reverse">
              <div
                v-for="(member, idx) in (team.members || []).slice(0, 3)"
                :key="idx"
                class="w-8 h-8 rounded-full bg-gray-300 border-2 border-white flex items-center justify-center text-xs font-medium text-gray-600"
              >
                {{ member.name?.charAt(0) || '?' }}
              </div>
              <div
                v-if="team.members_count > 3"
                class="w-8 h-8 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-xs font-medium text-gray-500"
              >
                +{{ team.members_count - 3 }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Create Team Modal -->
      <div v-if="showCreateModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6" @click.stop>
          <h2 class="text-xl font-bold text-gray-800 mb-6">إنشاء فريق جديد</h2>
          <form @submit.prevent="createTeam">
            <div class="mb-4">
              <label class="block text-gray-700 font-medium mb-2">اسم الفريق</label>
              <input
                v-model="newTeam.name"
                type="text"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="مثال: فريق التسويق"
              />
            </div>
            <div class="mb-6">
              <label class="block text-gray-700 font-medium mb-2">الوصف (اختياري)</label>
              <textarea
                v-model="newTeam.description"
                rows="3"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                placeholder="وصف موجز للفريق..."
              ></textarea>
            </div>
            <div class="flex gap-3">
              <button
                type="button"
                @click="showCreateModal = false"
                class="flex-1 px-4 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-all"
              >
                إلغاء
              </button>
              <button
                type="submit"
                :disabled="creating"
                class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-all disabled:opacity-50"
              >
                {{ creating ? 'جاري الإنشاء...' : 'إنشاء' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const loading = ref(true)
const creating = ref(false)
const teams = ref([])
const showCreateModal = ref(false)
const newTeam = ref({ name: '', description: '' })

const fetchTeams = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/v1/teams')
    teams.value = response.data.data || response.data
  } catch (error) {
    console.error('Error fetching teams:', error)
  } finally {
    loading.value = false
  }
}

const createTeam = async () => {
  try {
    creating.value = true
    const response = await axios.post('/api/v1/teams', newTeam.value)
    teams.value.push(response.data.data || response.data)
    showCreateModal.value = false
    newTeam.value = { name: '', description: '' }
  } catch (error) {
    console.error('Error creating team:', error)
  } finally {
    creating.value = false
  }
}

const getRoleLabel = (role) => {
  const labels = { owner: 'مالك', admin: 'مدير', member: 'عضو' }
  return labels[role] || role
}

const goToTeam = (team) => {
  router.push({ name: 'teams.show', params: { id: team.id } })
}

onMounted(fetchTeams)
</script>
