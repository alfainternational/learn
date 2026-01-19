<template>
  <div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-bold text-gray-900">إعدادات الخطة</h2>
        <button @click="$router.push(`/plans/${id}`)" class="text-gray-500 hover:text-gray-700">عودة للخطة</button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" v-if="!loading">
        <form @submit.prevent="updatePlan" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">عنوان الخطة</label>
                <input v-model="form.title" type="text" class="input">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">اسم المشروع</label>
                <input v-model="form.business_name" type="text" class="input">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">المجال</label>
                <input v-model="form.industry" type="text" class="input" disabled>
                <p class="text-xs text-gray-500 mt-1">لا يمكن تغيير المجال بعد إنشاء الخطة</p>
            </div>

             <div class="pt-4 border-t flex justify-between items-center">
                 <button type="button" @click="archivePlan" class="text-red-500 hover:text-red-700 text-sm font-medium">أرشفة الخطة</button>
                 <button type="submit" class="btn btn-primary" :disabled="saving">
                     {{ saving ? 'جاري الحفظ...' : 'حفظ التغييرات' }}
                 </button>
             </div>
        </form>
    </div>
    <div v-else class="py-12 flex justify-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const id = route.params.id;
const loading = ref(true);
const saving = ref(false);

const form = reactive({
    title: '',
    business_name: '',
    industry: ''
});

onMounted(async () => {
    try {
        const response = await axios.get(`/plans/${id}`);
        const plan = response.data.data;
        form.title = plan.title;
        form.business_name = plan.business_name;
        form.industry = plan.industry;
    } catch (e) {
        console.error(e);
        router.push('/plans');
    } finally {
        loading.value = false;
    }
});

const updatePlan = async () => {
    saving.value = true;
    try {
        await axios.put(`/plans/${id}`, form);
        alert('تم التحديث بنجاح');
        router.push(`/plans/${id}`);
    } catch (e) {
        alert('حدث خطأ أثناء التحديث');
    } finally {
        saving.value = false;
    }
};

const archivePlan = async () => {
    if (!confirm('هل أنت متأكد من أرشفة هذه الخطة؟')) return;
    try {
        await axios.post(`/plans/${id}/archive`);
        router.push('/plans');
    } catch (e) {
        alert('حدث خطأ');
    }
};
</script>
