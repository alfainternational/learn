<template>
  <BaseSection title="قمع المبيعات (Sales Funnel)" :saving="saving" :last-saved="lastSaved" v-model:isCompleted="localCompleted" @save="save">
    <div class="space-y-8">
        <p class="text-sm text-gray-600">كيف ستحول الغريب إلى عميل؟ صمم رحلة العميل خطوة بخطوة.</p>

        <!-- Funnel Stages -->
        <div class="relative border-l-4 border-indigo-200 ml-4 space-y-8 pl-8">
            <!-- Stage 1: Awareness -->
            <div class="relative">
                <div class="absolute -left-[42px] bg-indigo-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">1</div>
                <h4 class="font-bold text-gray-900">الوعي (Awareness)</h4>
                <p class="text-xs text-gray-500 mb-2">كيف سيعرفون بوجودك؟ (Traffic Source)</p>
                <textarea v-model="form.awareness" rows="2" class="input" placeholder="مثال: إعلانات ممولة، مقالات مدونة، ظهور في محركات البحث..."></textarea>
            </div>

            <!-- Stage 2: Interest -->
            <div class="relative">
                <div class="absolute -left-[42px] bg-indigo-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">2</div>
                <h4 class="font-bold text-gray-900">الاهتمام (Interest/Lead Magnet)</h4>
                <p class="text-xs text-gray-500 mb-2">كيف ستجذب اهتمامهم وتحصل على بياناتهم؟</p>
                <textarea v-model="form.interest" rows="2" class="input" placeholder="مثال: تقديم كتاب مجاني، خصم 20%، استشارة مجانية مقابل البريد الإلكتروني..."></textarea>
            </div>

            <!-- Stage 3: Desire/Decision -->
            <div class="relative">
                <div class="absolute -left-[42px] bg-indigo-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">3</div>
                <h4 class="font-bold text-gray-900">الرغبة والقرار (Desire & Action)</h4>
                <p class="text-xs text-gray-500 mb-2">ما هو العرض الذي سيحولهم لعملاء دافعين؟</p>
                <textarea v-model="form.decision" rows="2" class="input" placeholder="مثال: العرض الرئيسي، صفحة هبوط مقنعة مع شهادات عملاء..."></textarea>
            </div>
        </div>
        
        <div class="bg-gray-50 p-4 rounded-xl">
             <label class="block text-sm font-medium text-gray-700 mb-2">نوع القمع (Funnel Type)</label>
             <select v-model="form.funnel_type" class="input">
                 <option value="lead_magnet">Lead Magnet Funnel (جذب عملاء محتملين)</option>
                 <option value="webinar">Webinar Funnel (ندوة عبر الإنترنت)</option>
                 <option value="ecommerce">E-commerce Sales (متجر إلكتروني مباشر)</option>
                 <option value="consultation">Consultation Funnel (حجز استشارة)</option>
                 <option value="custom">Custom (مخصص)</option>
             </select>
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
    awareness: '',
    interest: '',
    decision: '',
    funnel_type: 'lead_magnet'
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
