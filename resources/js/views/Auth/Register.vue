<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
      <div class="text-center mb-8">
        <h2 class="text-4xl font-display font-black text-gradient">خطّط</h2>
        <p class="mt-2 text-sm text-gray-600">ابدأ رحلتك التسويقية الآن</p>
      </div>
      
      <div class="card">
        <form @submit.prevent="handleRegister" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الكامل</label>
            <input v-model="form.name" type="text" required class="input" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
            <input v-model="form.email" type="email" required class="input" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور</label>
            <input v-model="form.password" type="password" required class="input" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">تأكيد كلمة المرور</label>
            <input v-model="form.password_confirmation" type="password" required class="input" />
          </div>

          <div v-if="error" class="text-red-600 text-sm">{{ error }}</div>

          <button type="submit" :disabled="loading" class="btn btn-primary w-full">
            <span v-if="loading">جاري إنشاء الحساب...</span>
            <span v-else>إنشاء حساب مجاني</span>
          </button>
        </form>

        <div class="mt-6 text-center">
          <p class="text-sm text-gray-600">
            لديك حساب بالفعل؟
            <router-link to="/login" class="font-medium text-primary-600 hover:text-primary-500">
              سجّل دخولك
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const loading = ref(false);
const error = ref('');

const handleRegister = async () => {
  loading.value = true;
  error.value = '';
  
  try {
    await authStore.register(form.value);
    router.push('/dashboard');
  } catch (err) {
    error.value = err.response?.data?.message || 'حدث خطأ أثناء إنشاء الحساب';
  } finally {
    loading.value = false;
  }
};
</script>
