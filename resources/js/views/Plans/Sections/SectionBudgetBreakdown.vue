<template>
  <BaseSection title="توزيع الميزانية (Budget Breakdown)" :saving="saving" :last-saved="lastSaved" v-model:isCompleted="localCompleted" @save="save">
    <div class="space-y-6">
        <div class="bg-gray-800 text-white p-6 rounded-2xl flex justify-between items-center">
            <div>
                <h3 class="text-gray-400 text-sm mb-1">الميزانية الإجمالية الشهرية</h3>
                <div class="text-3xl font-bold flex items-baseline gap-1">
                    <input type="number" v-model="form.total_budget" class="bg-transparent border-0 border-b border-gray-600 w-32 text-center focus:ring-0 focus:border-white font-mono" placeholder="0">
                    <span class="text-sm font-normal">ر.س</span>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-400">المتبقي للتوزيع</div>
                <div :class="['text-xl font-bold', remainingBudget < 0 ? 'text-red-400' : 'text-green-400']">
                    {{ remainingBudget }} ر.س
                </div>
            </div>
        </div>

        <div>
             <h4 class="font-bold text-gray-800 mb-4">بنود الصرف</h4>
             <div class="space-y-3">
                 <div v-for="(item, index) in form.items" :key="index" class="flex items-center gap-3">
                      <input v-model="item.category" type="text" class="input flex-1" placeholder="البند (مثال: إعلانات انستقرام)">
                      <input v-model="item.amount" type="number" class="input w-32 text-center" placeholder="المبلغ">
                      <span class="text-gray-500 text-sm">%{{ getPercentage(item.amount) }}</span>
                      <button @click="removeItem(index)" class="text-gray-400 hover:text-red-500"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                 </div>
                 <button @click="addItem" class="text-sm text-primary-600 font-bold flex items-center gap-1 hover:text-primary-700">
                     + إضافة بند جديد
                 </button>
             </div>
        </div>

        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
            <h4 class="font-bold text-blue-900 mb-2 text-sm">توقعات العائد (ROI Calculation)</h4>
            <div class="flex gap-4 text-sm text-blue-800">
                <div class="flex-1">
                    إذا كان متوسط تكلفة العميل (CAC) هو <span class="font-bold">50 ر.س</span>،
                    فإن هذه الميزانية ستجلب لك تقريباً <span class="font-bold border-b border-blue-400">{{ estimatedCustomers }}</span> عميل محتمل.
                </div>
            </div>
        </div>
    </div>
  </BaseSection>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import BaseSection from './BaseSection.vue';

const props = defineProps({
    modelValue: { type: Object, default: () => ({}) },
    isCompleted: Boolean,
    saving: Boolean
});

const emit = defineEmits(['update:modelValue', 'update:isCompleted', 'save']);

const form = ref({
    total_budget: 0,
    items: [
        { category: 'إعلانات ممولة', amount: 0 },
        { category: 'إنتاج محتوى', amount: 0 },
        { category: 'أدوات واشتراكات', amount: 0 }
    ]
});

const localCompleted = ref(false);
const lastSaved = ref(null);

onMounted(() => {
    if (props.modelValue) {
        if (props.modelValue.total_budget !== undefined) form.value.total_budget = props.modelValue.total_budget;
        if (props.modelValue.items && Array.isArray(props.modelValue.items)) form.value.items = props.modelValue.items;
    }
    localCompleted.value = props.isCompleted;
});

const remainingBudget = computed(() => {
    const allocated = form.value.items.reduce((sum, item) => sum + Number(item.amount || 0), 0);
    return Number(form.value.total_budget || 0) - allocated;
});

const getPercentage = (amount) => {
    if (!form.value.total_budget) return 0;
    return Math.round((Number(amount || 0) / form.value.total_budget) * 100);
};

const estimatedCustomers = computed(() => {
    const budget = Number(form.value.total_budget || 0);
    return budget > 0 ? Math.floor(budget / 50) : 0; // Assuming CAC 50 for demo
});

const addItem = () => form.value.items.push({ category: '', amount: 0 });
const removeItem = (index) => form.value.items.splice(index, 1);

watch(() => props.isCompleted, (newVal) => localCompleted.value = newVal);

const save = () => {
    lastSaved.value = new Date().toLocaleTimeString('ar-SA');
    emit('update:modelValue', form.value);
    emit('update:isCompleted', localCompleted.value);
    emit('save');
};
</script>
