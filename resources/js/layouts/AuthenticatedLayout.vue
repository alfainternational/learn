<template>
  <div class="flex h-screen bg-gray-100 font-sans" dir="rtl">
    <!-- Sidebar -->
    <aside :class="['bg-slate-900 text-white w-64 flex-shrink-0 transition-all duration-300 ease-in-out z-30', isOpen ? 'translate-x-0' : 'translate-x-full fixed inset-y-0 right-0 md:relative md:translate-x-0']">
      <div class="h-16 flex items-center justify-center border-b border-gray-800">
        <router-link to="/dashboard" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-secondary-500 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-xl">خ</span>
            </div>
            <span class="text-xl font-display font-bold">خطّط</span>
        </router-link>
      </div>

      <nav class="p-4 space-y-2">
        <router-link v-for="item in navigation" :key="item.name" :to="item.to" 
          :class="[
            'flex items-center gap-3 px-4 py-3 rounded-xl transition-colors group',
            isActiveRoute(item.to) ? 'bg-primary-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white'
          ]">
          <component :is="item.icon" class="w-5 h-5 group-hover:scale-110 transition-transform" />
          <span class="font-medium">{{ item.name }}</span>
        </router-link>
      </nav>

      <!-- User Info Footer -->
      <div class="absolute bottom-0 w-full p-4 border-t border-gray-800">
        <div class="flex items-center gap-3">
           <router-link to="/profile" class="flex items-center gap-3 flex-1 min-w-0 hover:opacity-80 transition-opacity">
              <div class="w-10 h-10 rounded-full bg-gray-700 overflow-hidden">
                <img v-if="user?.avatar_url" :src="user.avatar_url" class="w-full h-full object-cover">
                <div v-else class="w-full h-full flex items-center justify-center text-sm font-bold">
                   {{ userInitials }}
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ user?.name }}</p>
                <p class="text-xs text-gray-500 truncate">تعديل الملف الشخصي</p>
              </div>
           </router-link>
          
          <button @click="logout" class="text-gray-400 hover:text-red-400 p-2" title="تسجيل الخروج">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
          </button>
        </div>
      </div>
    </aside>

    <!-- Overlay for mobile sidebar -->
    <div v-if="isOpen" @click="isOpen = false" class="fixed inset-0 bg-black/50 z-20 md:hidden"></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Top Header -->
      <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 z-10">
        <button @click="isOpen = !isOpen" class="md:hidden text-gray-500 hover:text-gray-700">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
        </button>
        
        <h1 class="text-xl font-bold text-gray-800 hidden md:block">{{ currentRouteName }}</h1>

        <div class="flex items-center gap-4">
          <!-- Create Button -->
          <router-link to="/plans/create" class="btn btn-primary btn-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            <span class="hidden sm:inline">خطة جديدة</span>
          </router-link>
          
          <!-- Notifications -->
          <router-link to="/notifications" class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors">
             <span v-if="unreadCount > 0" class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
             <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
          </router-link>
          
          <!-- User Dropdown (Simplified) -->
          <div class="relative ml-3">
             <router-link to="/profile" class="flex items-center gap-2 text-sm text-gray-700 hover:text-primary-600">
               <span class="hidden sm:inline font-medium">{{ user?.name }}</span>
               <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden">
                  <img v-if="user?.avatar_url" :src="user.avatar_url" class="w-full h-full object-cover">
                  <div v-else class="w-full h-full flex items-center justify-center bg-primary-100 text-primary-700 font-bold">
                    {{ userInitials }}
                  </div>
               </div>
             </router-link>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 scroll-smooth">
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
import { 
  HomeIcon, 
  DocumentTextIcon, 
  DocumentDuplicateIcon, 
  CreditCardIcon, 
  ChartBarIcon,
  UsersIcon,
  MegaphoneIcon
} from '@heroicons/vue/24/outline';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();
const isOpen = ref(false);

const user = computed(() => authStore.user);
const userInitials = computed(() => {
  if (!user.value?.name) return 'U';
  return user.value.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
});

// Mock or Fetch unread count, usually from a Notification Store
const unreadCount = ref(0);

const currentRouteName = computed(() => route.meta.title || '');

// Helper to determine active route
const isActiveRoute = (path) => {
  // Exact match for dashboard
  if (path === '/dashboard') {
    return route.path === '/dashboard';
  }
  // For other routes, check if current path starts with the nav item path
  return route.path.startsWith(path);
};

const navigation = computed(() => {
  const nav = [
    { name: 'لوحة التحكم', to: '/dashboard', icon: HomeIcon },
    { name: 'خططي', to: '/plans', icon: DocumentTextIcon },
    { name: 'القوالب', to: '/templates', icon: DocumentDuplicateIcon },
    { name: 'الاشتراك', to: '/subscription', icon: CreditCardIcon },
  ];

  if (user.value?.role === 'admin') {
      // Add Admin Links
      nav.unshift({ name: 'لوحة المشرف', to: '/admin/dashboard', icon: ChartBarIcon });
      nav.push(
          { name: 'المستخدمين', to: '/admin/users', icon: UsersIcon },
          { name: 'الإعلانات', to: '/admin/campaigns', icon: MegaphoneIcon }
      );
  }

  return nav;
});

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
