<template>
  <BaseSection title="عرض القيمة (The Offer Stack)" :saving="saving" :last-saved="lastSaved" v-model:isCompleted="localCompleted" @save="save">
    <div class="space-y-6">
        <!-- Main Product -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">المنتج أو الخدمة الرئيسية</label>
            <input v-model="form.main_product" type="text" class="input" placeholder="اسم المنتج الأساسي">
        </div>
        
        <div class="flex gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">السعر الأساسي</label>
                <input v-model="form.price" type="number" class="input" placeholder="0.00">
            </div>
             <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">القيمة الحقيقية (للمقارنة)</label>
                <input v-model="form.value_price" type="number" class="input" placeholder="0.00">
            </div>
        </div>

        <!-- Bonuses -->
        <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-100">
            <h4 class="font-bold text-yellow-800 mb-4">المكافآت والهدايا (Bonuses)</h4>
            <div class="space-y-3">
                 <div v-for="(bonus, index) in form.bonuses" :key="index" class="flex gap-2">
                     <input v-model="bonus.name" type="text" class="input" placeholder="اسم المكافأة (مثال: شحن مجاني، كتاب إلكتروني)">
                     <input v-model="bonus.value" type="text" class="input w-24" placeholder="قيمتها">
                     <button @click="removeBonus(index)" class="text-red-500 hover:bg-red-50 p-2 rounded">
                         <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                     </button>
                 </div>
                 <button @click="addBonus" class="text-sm font-bold text-yellow-700 hover:text-yellow-800 flex items-center gap-1">
                     <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                     إضافة مكافأة
                 </button>
            </div>
        </div>

        <!-- Risk Reversal -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">إزالة المخاطرة (الضمان)</label>
            <textarea v-model="form.guarantee" rows="2" class="input" placeholder="مثال: ضمان استرجاع الأموال لمدة 30 يوماً بلا أسئلة."></textarea>
        </div>
        
         <!-- Scarcity -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الندرة أو العجلة (Scarcity & Urgency)</label>
            <input v-model="form.scarcity" type="text" class="input" placeholder="مثال: العرض متاح لأول 50 عميل فقط، أو ينتهي يوم الجمعة.">
        </div>
    </div>
  </BaseSection>
</template>

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
    main_product: '',
    price: '',
    value_price: '',
    bonuses: [ { name: '', value: '' } ], // Array of objects
    guarantee: '',
    scarcity: ''
});

const localCompleted = ref(false);
const lastSaved = ref(null);

onMounted(() => {
    // When mounting, we must ensure arrays are initialized even if json saved it as empty or undefined
    if (props.modelValue) {
        form.value = { ...form.value, ...props.modelValue };
        if (!form.value.bonuses || !Array.isArray(form.value.bonuses)) {
            form.value.bonuses = [{ name: '', value: '' }];
        }
    }
    localCompleted.value = props.isCompleted;
});

watch(() => props.modelValue, (newVal) => {
    // Careful not to overwrite if typing
    if (newVal) {
         // Deep merge logic simplified
         // Just ensure structure
    }
}, { deep: true });

watch(() => props.isCompleted, (newVal) => localCompleted.value = newVal);

const addBonus = () => {
    form.value.bonuses.push({ name: '', value: '' });
};

const removeBonus = (index) => {
    form.value.bonuses.splice(index, 1);
};

const save = () => {
    lastSaved.value = new Date().toLocaleTimeString('ar-SA');
    emit('update:modelValue', form.value);
    emit('update:isCompleted', localCompleted.value);
    emit('save');
};
</script>
