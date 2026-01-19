<template>
  <div class="flex h-screen bg-gray-100 font-sans" dir="rtl">
    <!-- Admin Sidebar -->
    <aside :class="['bg-slate-900 text-white w-64 flex-shrink-0 transition-all duration-300 ease-in-out z-30 flex flex-col', isOpen ? 'translate-x-0' : 'translate-x-full fixed inset-y-0 right-0 md:relative md:translate-x-0']">
      
      <!-- Brand -->
      <div class="h-16 flex items-center justify-center border-b border-gray-800 bg-slate-950">
        <router-link to="/admin/dashboard" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-gradient-to-br from-red-600 to-orange-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-xl">م</span>
            </div>
            <span class="text-xl font-display font-bold">لوحة الإدارة</span>
        </router-link>
      </div>

      <!-- Admin Navigation -->
      <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">الرئيسية</p>
        
        <router-link to="/admin/dashboard" 
          active-class="bg-indigo-600 text-white"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors group">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
          <span class="font-medium">لوحة المعلومات</span>
        </router-link>

        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">إدارة المنصة</p>

        <router-link to="/admin/users" 
          active-class="bg-indigo-600 text-white"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors group">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
          <span class="font-medium">المستخدمين</span>
        </router-link>

        <router-link to="/admin/campaigns" 
          active-class="bg-indigo-600 text-white"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors group">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
          <span class="font-medium">الحملات الإعلانية</span>
          <span v-if="pendingCampaignsCount > 0" class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full mr-auto">{{ pendingCampaignsCount }}</span>
        </router-link>

        <router-link to="/admin/subscriptions" 
          active-class="bg-indigo-600 text-white"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors group">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
          <span class="font-medium">الاشتراكات</span>
        </router-link>

         <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">النظام</p>

        <router-link to="/admin/settings" 
          active-class="bg-indigo-600 text-white"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors group">
          <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
          <span class="font-medium">الإعدادات</span>
        </router-link>
      </nav>

      <!-- User Footer -->
      <div class="border-t border-gray-800 p-4 bg-slate-950">
         <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full bg-indigo-700 flex items-center justify-center font-bold">
                {{ userInitials }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-medium truncate">{{ user?.name }}</p>
                <p class="text-gray-400 text-xs truncate">المسؤول</p>
            </div>
         </div>
         <div class="flex gap-2">
            <router-link to="/dashboard" class="flex-1 text-center py-2 text-xs bg-gray-800 text-gray-300 hover:text-white rounded hover:bg-gray-700 transition-colors">
                العودة للمنصة
            </router-link>
            <button @click="logout" class="px-3 py-2 bg-red-900/50 text-red-200 hover:bg-red-900 hover:text-white rounded transition-colors" title="تسجيل الخروج">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            </button>
         </div>
      </div>
    </aside>

    <!-- Overlay for mobile sidebar -->
    <div v-if="isOpen" @click="isOpen = false" class="fixed inset-0 bg-black/50 z-20 md:hidden"></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-50">
      <!-- Top Header -->
      <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 z-10 shadow-sm">
        <div class="flex items-center gap-4">
            <button @click="isOpen = !isOpen" class="md:hidden text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
            </button>
            <h1 class="text-lg font-bold text-gray-800">{{ currentRouteName }}</h1>
        </div>
        
        <div class="flex items-center gap-4">
             <div class="text-sm text-gray-500">
                أهلاً بك في لوحة التحكم الإدارية
             </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 scroll-smooth">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter, useRoute } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();
const isOpen = ref(false);

const user = computed(() => authStore.user);
const userInitials = computed(() => {
  if (!user.value?.name) return 'A';
  return user.value.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
});

const currentRouteName = computed(() => route.meta.title || '');

// TODO: Link with real pending count store
const pendingCampaignsCount = ref(0); 

const logout = async () => {
    await authStore.logout();
    router.push('/login');
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
