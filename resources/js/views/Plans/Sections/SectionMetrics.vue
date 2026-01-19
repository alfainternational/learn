<template>
  <BaseSection title="الأرقام والمقاييس (KPIs & Metrics)" :saving="saving" :last-saved="lastSaved" v-model:isCompleted="localCompleted" @save="save">
    <div class="space-y-6">
        <p class="text-sm text-gray-600">ما لا يمكن قياسه، لا يمكن إدارته. حدد أهم الأرقام التي ستتابعها.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
             <div class="kpi-card">
                 <label>تكلفة الاستحواذ (CAC)</label>
                 <input v-model="form.cac_target" type="text" class="kpi-input" placeholder="الهدف: 50 ريال">
             </div>
             
             <div class="kpi-card">
                 <label>قيمة العميل (LTV)</label>
                 <input v-model="form.ltv_target" type="text" class="kpi-input" placeholder="الهدف: 500 ريال">
             </div>

             <div class="kpi-card">
                 <label>معدل التحويل (Conversion Rate)</label>
                 <input v-model="form.cr_target" type="text" class="kpi-input" placeholder="الهدف: 3%">
             </div>

             <div class="kpi-card">
                 <label>عدد الزوار شهرياً</label>
                 <input v-model="form.traffic_target" type="text" class="kpi-input" placeholder="الهدف: 10,000">
             </div>

             <div class="kpi-card">
                 <label>عدد المبيعات شهرياً</label>
                 <input v-model="form.sales_target" type="text" class="kpi-input" placeholder="الهدف: 300">
             </div>

             <div class="kpi-card">
                 <label>العائد على الإعلان (ROAS)</label>
                 <input v-model="form.roas_target" type="text" class="kpi-input" placeholder="الهدف: 4x">
             </div>
        </div>

        <div>
            <h4 class="font-bold text-gray-800 mb-2">أدوات القياس</h4>
            <div class="flex flex-wrap gap-4">
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" v-model="form.tools.ga4" class="rounded"> Google Analytics 4</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" v-model="form.tools.pixel" class="rounded"> Facebook Pixel</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" v-model="form.tools.snapchat" class="rounded"> Snapchat Pixel</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" v-model="form.tools.crm" class="rounded"> نظام CRM</label>
            </div>
        </div>
    </div>
  </BaseSection>
</template>

<style scoped>
.kpi-card {
    @apply bg-gray-50 border border-gray-200 rounded-lg p-3;
}
.kpi-card label {
    @apply block text-xs font-bold text-gray-500 mb-1 uppercase tracking-wider;
}
.kpi-input {
    @apply w-full bg-white border border-gray-300 rounded px-2 py-1 text-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 outline-none;
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
    cac_target: '',
    ltv_target: '',
    cr_target: '',
    traffic_target: '',
    sales_target: '',
    roas_target: '',
    tools: { ga4: false, pixel: false, snapchat: false, crm: false }
});

const localCompleted = ref(false);
const lastSaved = ref(null);

onMounted(() => {
    if (props.modelValue) {
        if (props.modelValue.tools) form.value.tools = { ...form.value.tools, ...props.modelValue.tools };
        // sync other fields
        ['cac_target', 'ltv_target', 'cr_target', 'traffic_target', 'sales_target', 'roas_target'].forEach(k => {
            if (props.modelValue[k]) form.value[k] = props.modelValue[k];
        });
    }
    localCompleted.value = props.isCompleted;
});

watch(() => props.modelValue, (newVal) => {
    // deep merge if needed
}, { deep: true });

watch(() => props.isCompleted, (newVal) => localCompleted.value = newVal);

const save = () => {
    lastSaved.value = new Date().toLocaleTimeString('ar-SA');
    emit('update:modelValue', form.value);
    emit('update:isCompleted', localCompleted.value);
    emit('save');
};
</script>
