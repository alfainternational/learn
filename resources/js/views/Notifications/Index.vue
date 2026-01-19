<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">الإشعارات</h1>
      <button 
        v-if="hasUnread" 
        @click="markAllRead" 
        class="text-sm text-primary-600 hover:text-primary-700"
      >
        تحديد الكل كمقروء
      </button>
    </div>

    <div v-if="loading" class="space-y-4">
        <div v-for="n in 3" :key="n" class="h-20 bg-gray-100 rounded-lg animate-pulse"></div>
    </div>

    <div v-else-if="notifications.length > 0" class="space-y-4">
        <div 
            v-for="notification in notifications" 
            :key="notification.id"
            class="card p-4 bg-white hover:bg-gray-50 transition-colors cursor-pointer"
            :class="{ 'border-l-4 border-l-primary-500': !notification.read_at }"
            @click="markRead(notification)"
        >
            <div class="flex items-start gap-4">
                <div class="p-2 rounded-full" 
                    :class="notification.type === 'success' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600'">
                    <BellIcon class="w-6 h-6" />
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-gray-900" :class="{ 'text-primary-700': !notification.read_at }">
                        {{ notification.data?.title || 'إشعار جديد' }}
                    </h3>
                    <p class="text-gray-600 text-sm mt-1">
                        {{ notification.data?.message || '...' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-2">
                        {{ new Date(notification.created_at).toLocaleString('ar-SA') }}
                    </p>
                </div>
                <div v-if="!notification.read_at" class="w-3 h-3 bg-red-500 rounded-full"></div>
            </div>
        </div>
    </div>

    <div v-else class="text-center py-12">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <BellIcon class="w-8 h-8 text-gray-400" />
        </div>
        <h3 class="text-lg font-medium text-gray-900">لا توجد إشعارات</h3>
        <p class="text-gray-500">سوف نبلغك عند وجود أي تحديثات جديدة.</p>
    </div>

    <!-- Pagination -->
     <div v-if="links.length > 3" class="flex justify-center mt-6">
        <nav class="flex gap-2">
            <button 
                v-for="(link, i) in links" 
                :key="i"
                :disabled="!link.url || link.active"
                @click="fetchNotifications(link.url)"
                class="px-3 py-1 rounded border"
                :class="link.active ? 'bg-primary-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                v-html="link.label"
            >
            </button>
        </nav>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { BellIcon } from '@heroicons/vue/24/outline';

const notifications = ref([]);
const loading = ref(true);
const links = ref([]);

const hasUnread = computed(() => {
    return notifications.value.some(n => !n.read_at);
});

const fetchNotifications = async (url = '/notifications') => {
    loading.value = true;
    try {
        const res = await axios.get(url);
        // Laravel default notification resource structure usually data wrapping
        // Assuming Standard Pagination Resource
        notifications.value = res.data.data || res.data; 
        links.value = res.data.links || []; 
        // Note: Laravel DatabaseNotification model has 'data' column JSON.
        // API response might be direct array or paginated.
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const markRead = async (notification) => {
    if (notification.read_at) return;
    
    try {
        await axios.post(`/notifications/${notification.id}/read`);
        notification.read_at = new Date().toISOString();
        // Decrease badge count in auth store if we tracked it
    } catch (e) {
        console.error(e);
    }
};

const markAllRead = async () => {
    try {
        await axios.post('/notifications/read-all');
        notifications.value.forEach(n => n.read_at = new Date().toISOString());
    } catch (e) {
        console.error(e);
    }
};

onMounted(() => {
    fetchNotifications();
});
</script>
