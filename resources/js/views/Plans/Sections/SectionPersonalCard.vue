<template>
  <BaseSection title="بطاقة تعريف المشروع" :saving="saving" :last-saved="lastSaved" v-model:isCompleted="localCompleted" @save="save">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">اسم المشروع</label>
            <input v-model="form.business_name" type="text" class="input" placeholder="مثال: متجر الهدايا">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">الشعار (Slogan)</label>
            <input v-model="form.slogan" type="text" class="input" placeholder="مثال: نصنع ذكريات لا تنسى">
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">الرؤية (Vision)</label>
            <textarea v-model="form.vision" rows="2" class="input" placeholder="ما هو طموحك المستقبلي للمشروع؟"></textarea>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">الرسالة (Mission)</label>
            <textarea v-model="form.mission" rows="2" class="input" placeholder="ماذا تقدم ولمن وكيف؟"></textarea>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">القيم الجوهرية (Core Values)</label>
            <input v-model="form.values" type="text" class="input" placeholder="مثال: الجودة، الابتكار، سرعة التوصيل (افصل بفاصلة)">
            <p class="text-xs text-gray-500 mt-1">تساعد القيم في توجيه نبرة الصوت في التسويق.</p>
        </div>
    </div>
  </BaseSection>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import BaseSection from './BaseSection.vue';

const props = defineProps({
    modelValue: { type: Object, default: () => ({}) }, // Expect JSON object
    isCompleted: Boolean,
    saving: Boolean
});

const emit = defineEmits(['update:modelValue', 'update:isCompleted', 'save']);

// Local state mapping
const form = ref({
    business_name: '',
    slogan: '',
    vision: '',
    mission: '',
    values: ''
});

const localCompleted = ref(false);
const lastSaved = ref(null);

// Initialize from props
onMounted(() => {
    if (props.modelValue) {
        form.value = { ...form.value, ...props.modelValue };
    }
    localCompleted.value = props.isCompleted;
});

// Watch for external updates (if reloaded)
watch(() => props.modelValue, (newVal) => {
    if (newVal) form.value = { ...form.value, ...newVal };
}, { deep: true });

watch(() => props.isCompleted, (newVal) => {
    localCompleted.value = newVal;
});

// Emit changes on save
const save = () => {
    lastSaved.value = new Date().toLocaleTimeString('ar-SA');
    emit('update:modelValue', form.value);
    emit('update:isCompleted', localCompleted.value); // Ensure sync
    emit('save');
};
</script>
