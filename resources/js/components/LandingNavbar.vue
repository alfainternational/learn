<template>
  <nav :class="['fixed w-full z-50 transition-all duration-300', scrolled ? 'bg-white/90 backdrop-blur-md shadow-sm py-4' : 'bg-transparent py-6']">
    <div class="container-custom flex items-center justify-between">
      <!-- Logo -->
      <router-link :to="{ name: 'home' }" class="flex items-center gap-2 group">
        <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg group-hover:scale-110 transition-transform">
          خ
        </div>
        <span :class="['font-display font-black text-2xl', scrolled ? 'text-gray-900' : 'text-white']">خطّط</span>
      </router-link>

      <!-- Desktop Menu -->
      <div class="hidden md:flex items-center gap-8">
        <router-link :to="{ name: 'home', hash: '#features' }" :class="['text-sm font-medium hover:text-primary-500 transition-colors', scrolled ? 'text-gray-600' : 'text-white/90']">الميزات</router-link>
        <router-link :to="{ name: 'home', hash: '#how-it-works' }" :class="['text-sm font-medium hover:text-primary-500 transition-colors', scrolled ? 'text-gray-600' : 'text-white/90']">كيف يعمل؟</router-link>
        <router-link :to="{ name: 'home', hash: '#pricing' }" :class="['text-sm font-medium hover:text-primary-500 transition-colors', scrolled ? 'text-gray-600' : 'text-white/90']">الأسعار</router-link>
        <router-link :to="{ name: 'home', hash: '#testimonials' }" :class="['text-sm font-medium hover:text-primary-500 transition-colors', scrolled ? 'text-gray-600' : 'text-white/90']">آراء العملاء</router-link>
      </div>

      <!-- Auth Buttons -->
      <div class="flex items-center gap-4">
        <router-link to="/login" :class="['text-sm font-medium hover:text-primary-500 transition-colors hidden md:block', scrolled ? 'text-gray-600' : 'text-white']">
          تسجيل الدخول
        </router-link>
        <router-link to="/register" :class="['btn px-6 py-2.5 text-sm', scrolled ? 'btn-primary' : 'bg-white text-primary-700 hover:bg-gray-100']">
          ابدأ مجاناً
        </router-link>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const scrolled = ref(false);

const handleScroll = () => {
  scrolled.value = window.scrollY > 20;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>
