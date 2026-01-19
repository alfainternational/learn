<template>
  <div dir="rtl" class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 py-8 px-4">
    <div class="max-w-4xl mx-auto">
      <!-- Back Button -->
      <button @click="$router.push({ name: 'teams.show', params: { id: $route.params.id } })" class="flex items-center gap-2 text-gray-600 hover:text-blue-600 mb-6 transition-colors">
        <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        العودة للفريق
      </button>

      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">الدعوات المعلقة</h1>
        <p class="text-gray-600">إدارة دعوات الانضمام للفريق</p>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>

      <!-- Empty State -->
      <div v-else-if="invitations.length === 0" class="bg-white rounded-2xl p-12 text-center shadow-sm">
        <div class="text-6xl mb-4">📧</div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">لا توجد دعوات معلقة</h3>
        <p class="text-gray-500">جميع الدعوات تم قبولها أو إلغاؤها</p>
      </div>

      <!-- Invitations List -->
      <div v-else class="space-y-4">
        <div
          v-for="invitation in invitations"
          :key="invitation.id"
          class="bg-white rounded-2xl p-6 shadow-sm"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 rounded-full bg-gradient-to-br from-orange-400 to-orange-500 flex items-center justify-center text-white text-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </div>
              <div>
                <div class="font-medium text-gray-800">{{ invitation.email }}</div>
                <div class="text-sm text-gray-500">
                  دور: {{ getRoleLabel(invitation.role) }} •
                  أُرسلت {{ formatDate(invitation.created_at) }}
                </div>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <span :class="[
                'px-3 py-1 rounded-full text-xs font-medium',
                invitation.status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                invitation.status === 'accepted' ? 'bg-green-100 text-green-700' :
                'bg-red-100 text-red-700'
              ]">
                {{ getStatusLabel(invitation.status) }}
              </span>
              <button
                v-if="invitation.status === 'pending'"
                @click="resendInvitation(invitation)"
                :disabled="invitation.resending"
                class="text-blue-600 hover:text-blue-700 p-2 disabled:opacity-50"
                title="إعادة إرسال"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
              </button>
              <button
                v-if="invitation.status === 'pending'"
                @click="cancelInvitation(invitation)"
                :disabled="invitation.cancelling"
                class="text-red-500 hover:text-red-700 p-2 disabled:opacity-50"
                title="إلغاء"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Expiry Warning -->
          <div v-if="invitation.status === 'pending' && isExpiringSoon(invitation)" class="mt-4 p-3 bg-yellow-50 text-yellow-700 rounded-xl text-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            الدعوة ستنتهي قريباً
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const loading = ref(true)
const invitations = ref([])

const fetchInvitations = async () => {
  try {
    loading.value = true
    const response = await axios.get(`/api/v1/teams/${route.params.id}/invitations`)
    invitations.value = (response.data.data || response.data).map(inv => ({
      ...inv,
      resending: false,
      cancelling: false
    }))
  } catch (error) {
    console.error('Error fetching invitations:', error)
  } finally {
    loading.value = false
  }
}

const resendInvitation = async (invitation) => {
  try {
    invitation.resending = true
    await axios.post(`/api/v1/teams/${route.params.id}/invitations/${invitation.id}/resend`)
    alert('تم إعادة إرسال الدعوة بنجاح')
  } catch (error) {
    console.error('Error resending invitation:', error)
  } finally {
    invitation.resending = false
  }
}

const cancelInvitation = async (invitation) => {
  if (!confirm('هل أنت متأكد من إلغاء هذه الدعوة؟')) return
  try {
    invitation.cancelling = true
    await axios.delete(`/api/v1/teams/${route.params.id}/invitations/${invitation.id}`)
    invitations.value = invitations.value.filter(i => i.id !== invitation.id)
  } catch (error) {
    console.error('Error cancelling invitation:', error)
  } finally {
    invitation.cancelling = false
  }
}

const getRoleLabel = (role) => {
  const labels = { owner: 'مالك', admin: 'مدير', member: 'عضو' }
  return labels[role] || role
}

const getStatusLabel = (status) => {
  const labels = { pending: 'معلقة', accepted: 'مقبولة', cancelled: 'ملغاة', expired: 'منتهية' }
  return labels[status] || status
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('ar-SA')
}

const isExpiringSoon = (invitation) => {
  if (!invitation.expires_at) return false
  const expiresAt = new Date(invitation.expires_at)
  const now = new Date()
  const daysUntilExpiry = (expiresAt - now) / (1000 * 60 * 60 * 24)
  return daysUntilExpiry <= 2 && daysUntilExpiry > 0
}

onMounted(fetchInvitations)
</script>
