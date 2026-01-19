<template>
  <BaseSection title="الجمهور المستهدف" :saving="saving" :last-saved="lastSaved" v-model:isCompleted="localCompleted" @save="save">
    <div class="space-y-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="font-bold text-blue-800 mb-2">من هو عميلك المثالي؟ (Avatar)</h4>
            <p class="text-sm text-blue-700">تخيل شخصاً واحداً يمثل أفضل عملائك وأجب عن الأسئلة التالية.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الافتراضي</label>
                <input v-model="form.avatar_name" type="text" class="input" placeholder="مثال: محمد الموظف، سارة الطالبة">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الفئة العمرية والجنس</label>
                <input v-model="form.demographics" type="text" class="input" placeholder="مثال: نساء 25-35 سنة، يقيمون في الرياض">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">الرغبات والأهداف (Desires)</label>
                <textarea v-model="form.desires" rows="3" class="input" placeholder="ماذا يريدون بشدة؟ (مثال: زيادة الدخل، توفير الوقت، الشعور بالأمان)"></textarea>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">الآلام والمشاكل (Pains)</label>
                <textarea v-model="form.pains" rows="3" class="input" placeholder="ما الذي يؤرقهم ليلاً؟ (مثال: الخوف من الفشل، تكاليف المعيشة المرتفعة)"></textarea>
            </div>
             
             <!-- Objections -->
            <div class="md:col-span-2 bg-gray-50 p-4 rounded-xl">
                <label class="block text-sm font-medium text-gray-700 mb-2">الاعتراضات المحتملة (Objections)</label>
                <p class="text-xs text-gray-500 mb-2">لماذا قد يترددون في الشراء منك؟</p>
                <textarea v-model="form.objections" rows="3" class="input" placeholder="مثال: السعر مرتفع، لا أثق في الجودة، ليس لدي وقت..."></textarea>
            </div>
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
    avatar_name: '',
    demographics: '',
    desires: '',
    pains: '',
    objections: ''
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
