<template>
  <BaseSection title="خطة المحتوى (Content Plan)" :saving="saving" :last-saved="lastSaved" v-model:isCompleted="localCompleted" @save="save">
    <div class="space-y-6">
        <div>
            <h4 class="font-bold text-gray-800 mb-2">محاور المحتوى (Content Pillars)</h4>
            <p class="text-xs text-gray-500 mb-3">اختر 3-5 مواضيع رئيسية يدور حولها محتواك.</p>
            <div class="space-y-2">
                 <input v-model="form.pillar_1" type="text" class="input" placeholder="المحور الأول (مثال: نصائح تعليمية)">
                 <input v-model="form.pillar_2" type="text" class="input" placeholder="المحور الثاني (مثال: خلف الكواليس)">
                 <input v-model="form.pillar_3" type="text" class="input" placeholder="المحور الثالث (مثال: قصص نجاح عملاء)">
                 <input v-model="form.pillar_4" type="text" class="input" placeholder="المحور الرابع (اختياري)">
            </div>
        </div>

        <hr class="border-gray-100">

        <!-- Weekly Schedule Grid -->
        <div>
            <h4 class="font-bold text-gray-800 mb-4">الجدول الأسبوعي المقترح</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600">
                            <th class="p-2 border border-gray-200">اليوم</th>
                            <th class="p-2 border border-gray-200 w-1/3">المحور / الموضوع</th>
                            <th class="p-2 border border-gray-200 w-1/3">نوع المحتوى (فيديو، بوست)</th>
                            <th class="p-2 border border-gray-200">المنصة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(day, index) in weekDays" :key="index">
                            <td class="p-2 border border-gray-200 font-medium">{{ day }}</td>
                            <td class="p-1 border border-gray-200">
                                <input v-model="form.schedule[index].topic" type="text" class="w-full border-0 bg-transparent focus:ring-0 text-sm" placeholder="اكتب الموضوع...">
                            </td>
                            <td class="p-1 border border-gray-200">
                                <select v-model="form.schedule[index].type" class="w-full border-0 bg-transparent focus:ring-0 text-xs text-gray-500">
                                    <option value="">اختر</option>
                                    <option value="video">فيديو / ريلز</option>
                                    <option value="image">صورة / كاروسيل</option>
                                    <option value="text">نص / تغريدة</option>
                                    <option value="story">ستوري</option>
                                </select>
                            </td>
                            <td class="p-1 border border-gray-200">
                                <input v-model="form.schedule[index].platform" type="text" class="w-full border-0 bg-transparent focus:ring-0 text-sm" placeholder="انستقرام...">
                            </td>
                        </tr>
                    </tbody>
                </table>
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

const weekDays = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];

const form = ref({
    pillar_1: '', pillar_2: '', pillar_3: '', pillar_4: '',
    schedule: Array(7).fill().map(() => ({ topic: '', type: '', platform: '' }))
});

const localCompleted = ref(false);
const lastSaved = ref(null);

onMounted(() => {
    if (props.modelValue) {
        if (props.modelValue.pillar_1) {
             form.value.pillar_1 = props.modelValue.pillar_1;
             form.value.pillar_2 = props.modelValue.pillar_2;
             form.value.pillar_3 = props.modelValue.pillar_3;
             form.value.pillar_4 = props.modelValue.pillar_4;
        }
        if (props.modelValue.schedule && Array.isArray(props.modelValue.schedule)) {
             form.value.schedule = props.modelValue.schedule;
        }
    }
    localCompleted.value = props.isCompleted;
});

watch(() => props.isCompleted, (newVal) => localCompleted.value = newVal);

const save = () => {
    lastSaved.value = new Date().toLocaleTimeString('ar-SA');
    emit('update:modelValue', form.value);
    emit('update:isCompleted', localCompleted.value);
    emit('save');
};
</script>
