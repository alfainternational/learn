<template>
  <BaseSection title="الرسالة الأساسية (Core Message)" :saving="saving" :last-saved="lastSaved" v-model:isCompleted="localCompleted" @save="save">
    <div class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">وعد العلامة التجارية (Brand Promise)</label>
            <textarea v-model="form.brand_promise" rows="3" class="input" placeholder="نحن نساعد [العميل] على [تحقيق النتيجة] من خلال [المنتج/الخدمة]..."></textarea>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
             <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نبرة الصوت (Voice & Tone)</label>
                <select v-model="form.voice_tone" class="input">
                    <option value="formal">رسمي ومهني</option>
                    <option value="friendly">ودود وقريب</option>
                    <option value="exciting">حماسي وملهم</option>
                    <option value="authoritative">خبير وموثوق</option>
                    <option value="humorous">مرح وفكاهي</option>
                </select>
             </div>
             <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">كلمات مفتاحية يجب استخدامها</label>
                <input v-model="form.keywords" type="text" class="input" placeholder="جودة، سرعة، ضمان...">
             </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">القصة (Story Brand)</label>
            <textarea v-model="form.story" rows="4" class="input" placeholder="كان العميل يعاني من... ثم وجد منتجنا... وأصبحت حياته..."></textarea>
        </div>
        
        <div class="bg-purple-50 p-4 rounded-xl border border-purple-100">
             <label class="block text-sm font-bold text-purple-900 mb-2">الشعار اللفظي (Elevator Pitch)</label>
             <p class="text-xs text-gray-600 mb-2">كيف تشرح مشروعك في 30 ثانية؟</p>
             <textarea v-model="form.elevator_pitch" rows="3" class="input border-purple-200 focus:border-purple-500" placeholder=""></textarea>
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
    brand_promise: '',
    voice_tone: 'friendly',
    keywords: '',
    story: '',
    elevator_pitch: ''
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
