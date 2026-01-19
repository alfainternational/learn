<template>
  <BaseSection title="نظام المتابعة (Follow-up)" :saving="saving" :last-saved="lastSaved" v-model:isCompleted="localCompleted" @save="save">
    <div class="space-y-6">
        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-xl text-yellow-800 text-sm">
            💡 معلومة: 80% من المبيعات تتم بعد التواصل رقم 5 إلى 12. معظم الناس يستسلمون بعد المرة الأولى!
        </div>

        <div class="space-y-4">
            <div>
                 <h4 class="font-bold text-gray-800">سلسلة البريد الإلكتروني (Email Sequence)</h4>
                 <div class="mt-2 space-y-2">
                     <div class="flex items-center gap-2">
                        <span class="w-20 text-xs text-gray-500">فوراً:</span>
                        <input v-model="form.email_1" type="text" class="input flex-1" placeholder="رسالة الترحيب وتسليم الهدية (Lead Magnet)">
                     </div>
                     <div class="flex items-center gap-2">
                        <span class="w-20 text-xs text-gray-500">بعد يوم:</span>
                        <input v-model="form.email_2" type="text" class="input flex-1" placeholder="بناء القيمة / سرد قصة المشكلة">
                     </div>
                     <div class="flex items-center gap-2">
                        <span class="w-20 text-xs text-gray-500">بعد يومين:</span>
                        <input v-model="form.email_3" type="text" class="input flex-1" placeholder="التعامل مع الاعتراضات / شهادات عملاء">
                     </div>
                     <div class="flex items-center gap-2">
                        <span class="w-20 text-xs text-gray-500">بعد 3 أيام:</span>
                        <input v-model="form.email_4" type="text" class="input flex-1" placeholder="العرض القوي (Hard Sell)">
                     </div>
                 </div>
            </div>
            
            <hr class="border-gray-100">

            <div>
                 <h4 class="font-bold text-gray-800">إعادة الاستهداف (Retargeting)</h4>
                 <p class="text-xs text-gray-500 mb-2">ماذا سنعرض لمن زار موقعنا ولم يشترِ؟</p>
                 <textarea v-model="form.retargeting_ads" rows="3" class="input" placeholder="مثال: إعلان يذكرهم بالخصم، إعلان شهادات عملاء..."></textarea>
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
    email_1: '',
    email_2: '',
    email_3: '',
    email_4: '',
    retargeting_ads: ''
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
