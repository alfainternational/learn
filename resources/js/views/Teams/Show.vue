<template>
  <div dir="rtl" class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8 px-4">
    <div class="max-w-6xl mx-auto">
      <!-- Back Button -->
      <button @click="$router.push({ name: 'teams.index' })" class="flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
        <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        العودة للفرق
      </button>

      <!-- Loading -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>

      <template v-else-if="team">
        <!-- Team Header -->
        <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
              <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-3xl font-bold">
                {{ team.name.charAt(0) }}
              </div>
              <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ team.name }}</h1>
                <p class="text-gray-500">{{ team.description || 'لا يوجد وصف' }}</p>
              </div>
            </div>
            <span :class="[
              'px-4 py-2 rounded-full text-sm font-medium',
              team.role === 'owner' ? 'bg-yellow-100 text-yellow-700' :
              team.role === 'admin' ? 'bg-purple-100 text-purple-700' :
              'bg-gray-100 text-gray-600'
            ]">
              {{ getRoleLabel(team.role) }}
            </span>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Members List -->
          <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl p-6 shadow-sm">
              <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800">الأعضاء ({{ members.length }})</h2>
              </div>

              <div class="space-y-3">
                <div
                  v-for="member in members"
                  :key="member.id"
                  class="flex items-center justify-between p-4 bg-gray-50 rounded-xl"
                >
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-500 flex items-center justify-center text-white font-medium">
                      {{ member.user?.name?.charAt(0) || '?' }}
                    </div>
                    <div>
                      <div class="font-medium text-gray-800">{{ member.user?.name || 'مستخدم' }}</div>
                      <div class="text-sm text-gray-500">{{ member.user?.email }}</div>
                    </div>
                  </div>
                  <div class="flex items-center gap-3">
                    <span :class="[
                      'px-3 py-1 rounded-full text-xs font-medium',
                      member.role === 'owner' ? 'bg-yellow-100 text-yellow-700' :
                      member.role === 'admin' ? 'bg-purple-100 text-purple-700' :
                      'bg-gray-200 text-gray-600'
                    ]">
                      {{ getRoleLabel(member.role) }}
                    </span>
                    <button
                      v-if="canManageMembers && member.role !== 'owner'"
                      @click="removeMember(member)"
                      class="text-red-500 hover:text-red-700 p-2"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Invite Form -->
          <div>
            <div class="bg-white rounded-2xl p-6 shadow-sm">
              <h2 class="text-xl font-bold text-gray-800 mb-6">دعوة عضو جديد</h2>
              <form @submit.prevent="sendInvite">
                <div class="mb-4">
                  <label class="block text-gray-700 font-medium mb-2">البريد الإلكتروني</label>
                  <input
                    v-model="inviteEmail"
                    type="email"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="email@example.com"
                  />
                </div>
                <div class="mb-6">
                  <label class="block text-gray-700 font-medium mb-2">الدور</label>
                  <select
                    v-model="inviteRole"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="member">عضو</option>
                    <option value="admin">مدير</option>
                  </select>
                </div>
                <button
                  type="submit"
                  :disabled="inviting"
                  class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-medium transition-all disabled:opacity-50"
                >
                  {{ inviting ? 'جاري الإرسال...' : 'إرسال الدعوة' }}
                </button>
              </form>

              <div v-if="inviteSuccess" class="mt-4 p-3 bg-green-100 text-green-700 rounded-xl text-center text-sm">
                تم إرسال الدعوة بنجاح!
              </div>
              <div v-if="inviteError" class="mt-4 p-3 bg-red-100 text-red-700 rounded-xl text-center text-sm">
                {{ inviteError }}
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl p-6 shadow-sm mt-6">
              <h3 class="font-bold text-gray-800 mb-4">إجراءات سريعة</h3>
              <div class="space-y-3">
                <button
                  @click="$router.push({ name: 'teams.invitations', params: { id: team.id } })"
                  class="w-full flex items-center gap-3 p-3 bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors text-right"
                >
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                  <span class="text-gray-700">إدارة الدعوات</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const loading = ref(true)
const team = ref(null)
const members = ref([])
const inviteEmail = ref('')
const inviteRole = ref('member')
const inviting = ref(false)
const inviteSuccess = ref(false)
const inviteError = ref('')

const canManageMembers = computed(() => {
  return team.value?.role === 'owner' || team.value?.role === 'admin'
})

const fetchTeam = async () => {
  try {
    loading.value = true
    const [teamRes, membersRes] = await Promise.all([
      axios.get(`/api/v1/teams/${route.params.id}`),
      axios.get(`/api/v1/teams/${route.params.id}/members`)
    ])
    team.value = teamRes.data.data || teamRes.data
    members.value = membersRes.data.data || membersRes.data
  } catch (error) {
    console.error('Error fetching team:', error)
  } finally {
    loading.value = false
  }
}

const sendInvite = async () => {
  try {
    inviting.value = true
    inviteSuccess.value = false
    inviteError.value = ''
    await axios.post(`/api/v1/teams/${route.params.id}/invite`, {
      email: inviteEmail.value,
      role: inviteRole.value
    })
    inviteSuccess.value = true
    inviteEmail.value = ''
  } catch (error) {
    inviteError.value = error.response?.data?.message || 'حدث خطأ أثناء إرسال الدعوة'
  } finally {
    inviting.value = false
  }
}

const removeMember = async (member) => {
  if (!confirm('هل أنت متأكد من إزالة هذا العضو؟')) return
  try {
    await axios.delete(`/api/v1/teams/${route.params.id}/members/${member.id}`)
    members.value = members.value.filter(m => m.id !== member.id)
  } catch (error) {
    console.error('Error removing member:', error)
  }
}

const getRoleLabel = (role) => {
  const labels = { owner: 'مالك', admin: 'مدير', member: 'عضو' }
  return labels[role] || role
}

onMounted(fetchTeam)
</script>
