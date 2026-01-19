<template>
  <BaseSection title="تحليل المنافسين (Competitor Analysis)" :saving="saving" :last-saved="lastSaved" v-model:isCompleted="localCompleted" @save="save">
    <div class="space-y-6">
        <p class="text-sm text-gray-600">تعرف على منافسيك لتتفوق عليهم. حلل نقاط القوة والضعف لديهم.</p>

        <div class="space-y-4">
            <div v-for="(competitor, index) in form.competitors" :key="index" class="bg-gray-50 border border-gray-200 rounded-xl p-4 relative group">
                <button @click="removeCompetitor(index)" class="absolute top-2 left-2 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">المنافس</label>
                        <input v-model="competitor.name" type="text" class="input bg-white h-9 text-sm" placeholder="اسم المنافس">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">نقاط القوة</label>
                        <input v-model="competitor.strengths" type="text" class="input bg-white h-9 text-sm" placeholder="بماذا يتميز؟">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">نقاط الضعف</label>
                        <input v-model="competitor.weaknesses" type="text" class="input bg-white h-9 text-sm" placeholder="أين يقصر؟">
                    </div>
                    <div>
                         <label class="block text-xs font-bold text-gray-500 mb-1">كيف نتفوق عليه؟</label>
                         <input v-model="competitor.advantage" type="text" class="input bg-white h-9 text-sm border-indigo-200 focus:border-indigo-500" placeholder="ميزتنا التنافسية">
                    </div>
                </div>
            </div>

            <button @click="addCompetitor" class="w-full py-3 border-2 border-dashed border-gray-300 rounded-xl text-gray-500 font-bold hover:border-sidebar-500 hover:text-sidebar-600 hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                إضافة منافس جديد
            </button>
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
    competitors: [
        { name: '', strengths: '', weaknesses: '', advantage: '' }
    ]
});

const localCompleted = ref(false);
const lastSaved = ref(null);

onMounted(() => {
    if (props.modelValue) {
        if (props.modelValue.competitors && Array.isArray(props.modelValue.competitors)) {
            form.value.competitors = props.modelValue.competitors;
        } else if (Object.keys(props.modelValue).length > 0) {
             // Handle case where saved data might differ or be empty object
        }
    }
    localCompleted.value = props.isCompleted;
});

watch(() => props.modelValue, (newVal) => {
    // Sync logic
}, { deep: true });

watch(() => props.isCompleted, (newVal) => localCompleted.value = newVal);

const addCompetitor = () => {
    form.value.competitors.push({ name: '', strengths: '', weaknesses: '', advantage: '' });
};

const removeCompetitor = (index) => {
    form.value.competitors.splice(index, 1);
};

const save = () => {
    lastSaved.value = new Date().toLocaleTimeString('ar-SA');
    emit('update:modelValue', form.value);
    emit('update:isCompleted', localCompleted.value);
    emit('save');
};
</script>
