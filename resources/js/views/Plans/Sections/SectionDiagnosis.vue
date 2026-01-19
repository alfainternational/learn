<template>
  <BaseSection title="التشخيص الحالي (SWOT & PESTEL)" :saving="saving" :last-saved="lastSaved" v-model:isCompleted="localCompleted" @save="save">
    <div class="space-y-8">
        <!-- SWOT Grid -->
        <div>
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="bg-primary-100 text-primary-700 px-2 py-1 rounded text-sm">SWOT</span>
                تحليل نقاط القوة والضعف
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Strengths -->
                <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                    <label class="block font-bold text-green-800 mb-2">نقاط القوة (Strengths)</label>
                    <textarea v-model="form.strengths" rows="4" class="input border-green-200 focus:border-green-500 focus:ring-green-200" placeholder="ما الذي يميزك عن غيرك؟ (مثال: جودة عالية، موقع مميز)"></textarea>
                </div>
                
                <!-- Weaknesses -->
                <div class="bg-red-50 p-4 rounded-xl border border-red-100">
                    <label class="block font-bold text-red-800 mb-2">نقاط الضعف (Weaknesses)</label>
                    <textarea v-model="form.weaknesses" rows="4" class="input border-red-200 focus:border-red-500 focus:ring-red-200" placeholder="ما هي التحديات الداخلية؟ (مثال: ميزانية محدودة، فريق صغير)"></textarea>
                </div>

                <!-- Opportunities -->
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                    <label class="block font-bold text-blue-800 mb-2">الفرص (Opportunities)</label>
                    <textarea v-model="form.opportunities" rows="4" class="input border-blue-200 focus:border-blue-500 focus:ring-blue-200" placeholder="ما هي الفرص الخارجية؟ (مثال: نمو السوق، انسحاب منافس)"></textarea>
                </div>

                <!-- Threats -->
                <div class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                    <label class="block font-bold text-orange-800 mb-2">التهديدات (Threats)</label>
                    <textarea v-model="form.threats" rows="4" class="input border-orange-200 focus:border-orange-500 focus:ring-orange-200" placeholder="ما هي المخاطر الخارجية؟ (مثال: قوانين جديدة، دخول منافس قوي)"></textarea>
                </div>
            </div>
        </div>

        <hr class="border-gray-100">

        <!-- Unique Selling Proposition -->
        <div>
            <h3 class="font-bold text-gray-900 mb-4">ما هي ميزتك التنافسية الوحيدة؟ (USP)</h3>
            <p class="text-sm text-gray-500 mb-2">لخص نقاط قوتك في جملة واحدة تقنع العميل بالشراء منك.</p>
            <input v-model="form.usp" type="text" class="input" placeholder="مثال: القهوة الوحيدة المحمصة طازجاً في المدينة خلال 24 ساعة.">
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
    strengths: '',
    weaknesses: '',
    opportunities: '',
    threats: '',
    usp: ''
});

const localCompleted = ref(false);
const lastSaved = ref(null);

onMounted(() => {
    if (props.modelValue) form.value = { ...form.value, ...props.modelValue };
    localCompleted.value = props.isCompleted;
});

watch(() => props.modelValue, (newVal) => {
    if (newVal) form.value = { ...form.value, ...newVal };
}, { deep: true });

watch(() => props.isCompleted, (newVal) => localCompleted.value = newVal);

const save = () => {
    lastSaved.value = new Date().toLocaleTimeString('ar-SA');
    emit('update:modelValue', form.value);
    emit('update:isCompleted', localCompleted.value);
    emit('save');
};
</script>
