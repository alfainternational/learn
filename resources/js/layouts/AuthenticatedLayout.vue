<template>
  <div class="flex h-screen bg-gray-100 font-sans" dir="rtl">
    <!-- Sidebar -->
    <aside :class="['bg-slate-900 text-white w-64 flex-shrink-0 transition-all duration-300 ease-in-out z-30 flex flex-col', isOpen ? 'translate-x-0' : 'translate-x-full fixed inset-y-0 right-0 md:relative md:translate-x-0']">
      <!-- Logo -->
      <div class="h-16 flex items-center justify-center border-b border-gray-800">
        <router-link to="/dashboard" class="flex items-center gap-2">
          <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-secondary-500 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-xl">خ</span>
          </div>
          <span class="text-xl font-display font-bold">خطّط</span>
        </router-link>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <!-- Main Navigation -->
        <div class="space-y-1">
          <router-link v-for="item in mainNavigation" :key="item.name" :to="item.to" 
            :class="[
              'flex items-center gap-3 px-4 py-3 rounded-xl transition-colors group',
              isActiveRoute(item.to) ? 'bg-primary-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white'
            ]">
            <component :is="item.icon" class="w-5 h-5 group-hover:scale-110 transition-transform" />
            <span class="font-medium">{{ item.name }}</span>
            <span v-if="item.badge" class="mr-auto bg-secondary-500 text-white text-xs px-2 py-0.5 rounded-full">{{ item.badge }}</span>
          </router-link>
        </div>

        <!-- Learn Section -->
        <div class="pt-4 mt-4 border-t border-gray-800">
          <div class="px-4 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2">
            <AcademicCapIcon class="w-4 h-4" />
            تعلّم
          </div>
          <div class="space-y-1 mt-2">
            <router-link v-for="item in learnNavigation" :key="item.name" :to="item.to" 
              :class="[
                'flex items-center gap-3 px-4 py-2.5 rounded-xl transition-colors group',
                isActiveRoute(item.to) ? 'bg-secondary-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white'
              ]">
              <component :is="item.icon" class="w-5 h-5 group-hover:scale-110 transition-transform" />
              <span class="font-medium text-sm">{{ item.name }}</span>
            </router-link>
          </div>
        </div>

        <!-- Admin Navigation -->
        <div v-if="isAdmin" class="pt-4 mt-4 border-t border-gray-800">
          <div class="px-4 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2">
            <CogIcon class="w-4 h-4" />
            الإدارة
          </div>
          <div class="space-y-1 mt-2">
            <router-link v-for="item in adminNavigation" :key="item.name" :to="item.to" 
              :class="[
                'flex items-center gap-3 px-4 py-2.5 rounded-xl transition-colors group',
                isActiveRoute(item.to) ? 'bg-red-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white'
              ]">
              <component :is="item.icon" class="w-5 h-5 group-hover:scale-110 transition-transform" />
              <span class="font-medium text-sm">{{ item.name }}</span>
            </router-link>
          </div>
        </div>
      </nav>

      <!-- Progress Indicator -->
      <div class="p-4 border-t border-gray-800">
        <div class="bg-gray-800 rounded-xl p-3">
          <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-gray-400">تقدم الدورة</span>
            <span class="text-xs font-bold text-secondary-400">{{ courseProgress }}%</span>
          </div>
          <div class="w-full bg-gray-700 rounded-full h-1.5">
            <div class="bg-gradient-to-r from-secondary-500 to-primary-500 h-1.5 rounded-full transition-all duration-500" :style="{ width: courseProgress + '%' }"></div>
          </div>
        </div>
      </div>

      <!-- User Info Footer -->
      <div class="p-4 border-t border-gray-800">
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
            <ArrowRightOnRectangleIcon class="w-5 h-5" />
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
          <Bars3Icon class="w-6 h-6" />
        </button>
        
        <h1 class="text-xl font-bold text-gray-800 hidden md:block">{{ currentRouteName }}</h1>

        <div class="flex items-center gap-4">
          <!-- Create Button -->
          <router-link to="/plans/create" class="btn btn-primary btn-sm flex items-center gap-2">
            <PlusIcon class="w-4 h-4" />
            <span class="hidden sm:inline">خطة جديدة</span>
          </router-link>
          
          <!-- Notifications -->
          <router-link to="/notifications" class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors">
            <span v-if="unreadCount > 0" class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
            <BellIcon class="w-6 h-6" />
          </router-link>
          
          <!-- User Dropdown -->
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
  HomeIcon, DocumentTextIcon, DocumentDuplicateIcon, CreditCardIcon,
  ChartBarIcon, UsersIcon, MegaphoneIcon, AcademicCapIcon, BookOpenIcon,
  WrenchScrewdriverIcon, TrophyIcon, PlusIcon, BellIcon, CogIcon,
  Bars3Icon, ArrowRightOnRectangleIcon, RocketLaunchIcon, ClipboardDocumentListIcon,
  CalendarDaysIcon, PresentationChartLineIcon, UserGroupIcon
} from '@heroicons/vue/24/outline';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();
const isOpen = ref(false);

const user = computed(() => authStore.user);
const isAdmin = computed(() => user.value?.role === 'admin');
const userInitials = computed(() => {
  if (!user.value?.name) return 'U';
  return user.value.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
});

const unreadCount = ref(0);
const courseProgress = ref(35);

const currentRouteName = computed(() => route.meta.title || '');

const isActiveRoute = (path) => {
  if (path === '/dashboard') return route.path === '/dashboard';
  return route.path.startsWith(path);
};

const mainNavigation = [
  { name: 'لوحة التحكم', to: '/dashboard', icon: HomeIcon },
  { name: 'خططي', to: '/plans', icon: DocumentTextIcon },
  { name: 'الحملات', to: '/campaigns', icon: RocketLaunchIcon },
  { name: 'المهام', to: '/tasks', icon: ClipboardDocumentListIcon },
  { name: 'التقويم', to: '/calendar', icon: CalendarDaysIcon },
  { name: 'التحليلات', to: '/analytics', icon: PresentationChartLineIcon },
  { name: 'الفرق', to: '/teams', icon: UserGroupIcon },
  { name: 'القوالب', to: '/templates', icon: DocumentDuplicateIcon },
  { name: 'الاشتراك', to: '/subscription', icon: CreditCardIcon },
];

const learnNavigation = [
  { name: 'الدورات', to: '/learn/courses', icon: BookOpenIcon },
  { name: 'الأدوات', to: '/learn/tools', icon: WrenchScrewdriverIcon },
  { name: 'الشهادات', to: '/learn/certificates', icon: TrophyIcon },
];

const adminNavigation = [
  { name: 'لوحة المشرف', to: '/admin/dashboard', icon: ChartBarIcon },
  { name: 'المستخدمين', to: '/admin/users', icon: UsersIcon },
  { name: 'الإعلانات', to: '/admin/campaigns', icon: MegaphoneIcon },
];

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
