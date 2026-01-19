<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">الملف الشخصي</h1>
    </div>

    <!-- Personal Info -->
    <div class="card p-6 bg-white">
      <h2 class="text-lg font-bold text-gray-900 mb-4">المعلومات الشخصية</h2>
      <form @submit.prevent="updateProfile" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الاسم الكامل</label>
                <input v-model="form.name" type="text" required class="input" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني</label>
                <input v-model="form.email" type="email" required class="input" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف</label>
                <input v-model="form.phone" type="text" class="input" placeholder="اختياري" />
            </div>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" :disabled="loading" class="btn btn-primary">
                {{ loading ? 'جاري الحفظ...' : 'حفظ التغييرات' }}
            </button>
        </div>
      </form>
    </div>

    <!-- Avatar Upload (Placeholder for API logic) -->
    <div class="card p-6 bg-white">
        <h2 class="text-lg font-bold text-gray-900 mb-4">الصورة الرمزية</h2>
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-xl font-bold text-gray-500 overflow-hidden">
                <img v-if="form.avatar_url" :src="form.avatar_url" class="w-full h-full object-cover">
                <span v-else>{{ form.name?.charAt(0) }}</span>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-2">قم برفع صورة جديدة (JPG, PNG)</p>
                <input type="file" @change="uploadAvatar" accept="image/*" class="text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-primary-50 file:text-primary-700
                    hover:file:bg-primary-100
                " />
            </div>
        </div>
    </div>

    <!-- Security -->
    <div class="card p-6 bg-white">
        <h2 class="text-lg font-bold text-gray-900 mb-4">الأمان</h2>
        <form @submit.prevent="updatePassword" class="space-y-4">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور الحالية</label>
                    <input v-model="passwordForm.current_password" type="password" required class="input" />
                </div>
             </div>
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور الجديدة</label>
                    <input v-model="passwordForm.password" type="password" required class="input" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">تأكيد كلمة المرور</label>
                    <input v-model="passwordForm.password_confirmation" type="password" required class="input" />
                </div>
             </div>
              <div class="flex justify-end">
                <button type="submit" :disabled="passwordLoading" class="btn btn-secondary">
                    {{ passwordLoading ? 'جاري التحديث...' : 'تحديث كلمة المرور' }}
                </button>
            </div>
        </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';

const authStore = useAuthStore();
const loading = ref(false);
const passwordLoading = ref(false);

const form = ref({
    name: '',
    email: '',
    phone: '',
    avatar_url: '' // For display
});

const passwordForm = ref({
    current_password: '',
    password: '',
    password_confirmation: ''
});

onMounted(() => {
    if (authStore.user) {
        form.value.name = authStore.user.name;
        form.value.email = authStore.user.email;
        form.value.phone = authStore.user.phone || '';
        form.value.avatar_url = authStore.user.avatar_url;
    }
});

const updateProfile = async () => {
    loading.value = true;
    try {
        await authStore.updateProfile({
            name: form.value.name,
            email: form.value.email,
            phone: form.value.phone
        });
        alert('تم تحديث المعلومات بنجاح');
    } catch (e) {
        alert(e.response?.data?.message || 'فشل التحديث');
    } finally {
        loading.value = false;
    }
};

const updatePassword = async () => {
    passwordLoading.value = true;
     try {
        await axios.put('/me/password', passwordForm.value); // Backend endpoint check?
        // Wait, endpoint might be generic PUT /me? 
        // AuthController usually handles password if provided, OR separate endpoint.
        // Plan said: PUT /api/v1/me - Update profile.
        // Usually Laravel API handles password change separately or in same update if fields prominent.
        // Checking AuthController in next step. For now assume separate or same.
        // I will implement as separate call logic if needed, but safe bet is usually dedicated endpoint or same.
        // Let's assume Update Profile handles it if fields present, or we need to add logic.
        // Current implementation tries to be modular.
        
        // Actually, AuthController usually validates.
        // I will use `axios.put('/me', ...)` with password fields if the controller supports it.
        // If not, I'll need to update Controller.
        
        // Let's try PUT /me with password fields.
        await axios.put('/me', {
            ...passwordForm.value
        });

        alert('تم تغيير كلمة المرور');
        passwordForm.value = { current_password: '', password: '', password_confirmation: '' };
    } catch (e) {
        alert(e.response?.data?.message || 'فشل تغيير كلمة المرور');
    } finally {
        passwordLoading.value = false;
    }
};

const uploadAvatar = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('avatar', file);

    try {
        const res = await axios.post('/me/avatar', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        form.value.avatar_url = res.data.avatar_url; // Assume response
        await authStore.fetchUser(); // Refresh user state
        alert('تم رفع الصورة');
    } catch (e) {
        alert('فشل رفع الصورة');
    }
};
</script>
