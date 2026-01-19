<template>
  <BaseSection title="قنوات التسويق (Channels)" :saving="saving" :last-saved="lastSaved" v-model:isCompleted="localCompleted" @save="save">
    <div class="space-y-6">
        <p class="text-sm text-gray-600">حدد القنوات التي ستستخدمها للوصول لعملائك. ركز على الجودة وليس الكمية.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
             <!-- Paid Ads -->
             <div class="border rounded-xl p-4 bg-white hover:shadow-md transition-shadow">
                 <label class="flex items-center gap-2 font-bold mb-3 text-gray-800">
                     <input type="checkbox" v-model="form.channels.paid_ads" class="rounded text-primary-600">
                     إعلانات مدفوعة
                 </label>
                 <div v-if="form.channels.paid_ads" class="scale-up-anim ml-6 space-y-2">
                     <label class="block text-xs text-gray-500">اختر المنصات:</label>
                     <div class="flex flex-wrap gap-2">
                         <label class="badge-check"><input type="checkbox" v-model="form.platforms.facebook"> Facebook</label>
                         <label class="badge-check"><input type="checkbox" v-model="form.platforms.instagram"> Instagram</label>
                         <label class="badge-check"><input type="checkbox" v-model="form.platforms.google"> Google</label>
                         <label class="badge-check"><input type="checkbox" v-model="form.platforms.tiktok"> TikTok</label>
                         <label class="badge-check"><input type="checkbox" v-model="form.platforms.snapchat"> Snapchat</label>
                     </div>
                 </div>
             </div>

             <!-- Content Marketing -->
             <div class="border rounded-xl p-4 bg-white hover:shadow-md transition-shadow">
                 <label class="flex items-center gap-2 font-bold mb-3 text-gray-800">
                     <input type="checkbox" v-model="form.channels.content" class="rounded text-primary-600">
                     صناعة المحتوى (Organic)
                 </label>
                 <div v-if="form.channels.content" class="ml-6 space-y-2">
                     <textarea v-model="form.content_strategy" rows="3" class="input text-xs" placeholder="ما نوع المحتوى؟ (فيديو، مقالات، ريلز...)"></textarea>
                 </div>
             </div>

             <!-- Email Marketing -->
             <div class="border rounded-xl p-4 bg-white hover:shadow-md transition-shadow">
                 <label class="flex items-center gap-2 font-bold mb-3 text-gray-800">
                     <input type="checkbox" v-model="form.channels.email" class="rounded text-primary-600">
                     البريد الإلكتروني
                 </label>
                 <div v-if="form.channels.email" class="ml-6 space-y-2">
                     <select v-model="form.email_frequency" class="input text-xs">
                         <option value="daily">يومي</option>
                         <option value="weekly">أسبوعي</option>
                         <option value="monthly">شهري</option>
                     </select>
                 </div>
             </div>

             <!-- Influencers -->
             <div class="border rounded-xl p-4 bg-white hover:shadow-md transition-shadow">
                 <label class="flex items-center gap-2 font-bold mb-3 text-gray-800">
                     <input type="checkbox" v-model="form.channels.influencers" class="rounded text-primary-600">
                     المؤثرين (Influencers)
                 </label>
             </div>

             <!-- SEO -->
             <div class="border rounded-xl p-4 bg-white hover:shadow-md transition-shadow">
                 <label class="flex items-center gap-2 font-bold mb-3 text-gray-800">
                     <input type="checkbox" v-model="form.channels.seo" class="rounded text-primary-600">
                     تحسين محركات البحث (SEO)
                 </label>
             </div>

             <!-- Offline -->
             <div class="border rounded-xl p-4 bg-white hover:shadow-md transition-shadow">
                 <label class="flex items-center gap-2 font-bold mb-3 text-gray-800">
                     <input type="checkbox" v-model="form.channels.offline" class="rounded text-primary-600">
                     تسويق ميداني / مطبوعات
                 </label>
             </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">استراتيجية توزيع الميزانية (مبدئياً)</label>
            <textarea v-model="form.budget_allocation_notes" rows="2" class="input" placeholder="مثال: سنركز 70% من الميزانية على إعلانات انستقرام لأن جمهورنا هناك..."></textarea>
        </div>
    </div>
  </BaseSection>
</template>

<style scoped>
.badge-check {
    @apply flex items-center gap-1 text-xs bg-gray-100 px-2 py-1 rounded cursor-pointer hover:bg-gray-200 transition-colors;
}
</style>

<script setup>
import { ref, watch, onMounted } from 'vue';
import BaseSection from './BaseSection.vue';

const props = defineProps({
    modelValue: { type: Object, default: () => ({}) },
    isCompleted: Boolean,
    saving: Boolean
});

const emit = defineEmits(['update:modelValue', 'update:isCompleted', 'save']);

const form = ref({
    channels: {
        paid_ads: false,
        content: false,
        email: false,
        influencers: false,
        seo: false,
        offline: false
    },
    platforms: {
        facebook: false,
        instagram: false,
        google: false,
        tiktok: false,
        snapchat: false
    },
    content_strategy: '',
    email_frequency: 'weekly',
    budget_allocation_notes: ''
});

const localCompleted = ref(false);
const lastSaved = ref(null);

onMounted(() => {
    if (props.modelValue) {
        // Merge carefully to preserve nested objects
        if (props.modelValue.channels) form.value.channels = { ...form.value.channels, ...props.modelValue.channels };
        if (props.modelValue.platforms) form.value.platforms = { ...form.value.platforms, ...props.modelValue.platforms };
        form.value.content_strategy = props.modelValue.content_strategy || '';
        form.value.email_frequency = props.modelValue.email_frequency || 'weekly';
        form.value.budget_allocation_notes = props.modelValue.budget_allocation_notes || '';
    }
    localCompleted.value = props.isCompleted;
});

watch(() => props.modelValue, (newVal) => {
    // Basic sync if needed
}, { deep: true });

watch(() => props.isCompleted, (newVal) => localCompleted.value = newVal);

const save = () => {
    lastSaved.value = new Date().toLocaleTimeString('ar-SA');
    emit('update:modelValue', form.value);
    emit('update:isCompleted', localCompleted.value);
    emit('save');
};
</script>
