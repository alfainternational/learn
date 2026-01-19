<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <h2 class="font-bold text-lg text-gray-800">{{ title }}</h2>
        <div class="flex items-center gap-2">
             <span v-if="saving" class="text-xs text-primary-600 animate-pulse">جاري الحفظ...</span>
             <span v-else-if="lastSaved" class="text-xs text-gray-400">آخر حفظ: {{ lastSaved }}</span>
             <div :class="['w-2 h-2 rounded-full', isCompleted ? 'bg-green-500' : 'bg-gray-300']"></div>
        </div>
    </div>

    <!-- Content Slot -->
    <div class="p-6">
        <slot></slot>
    </div>

    <!-- Footer Actions -->
    <div class="bg-gray-50 p-4 flex justify-between items-center border-t">
        <button @click="$emit('save')" class="btn btn-primary min-w-[120px]" :disabled="saving">
            {{ saving ? 'جاري الحفظ...' : 'حفظ التغييرات' }}
        </button>
        
        <label class="flex items-center gap-2 cursor-pointer select-none">
             <input type="checkbox" :checked="isCompleted" @change="$emit('update:isCompleted', $event.target.checked)" 
                class="rounded text-primary-600 focus:ring-primary-500 h-4 w-4">
             <span class="text-sm text-gray-600">تمييز هذا القسم كمكتمل</span>
        </label>
    </div>
  </div>
</template>

<script setup>
defineProps({
    title: { type: String, required: true },
    saving: { type: Boolean, default: false },
    lastSaved: { type: String, default: null },
    isCompleted: { type: Boolean, default: false }
});

defineEmits(['save', 'update:isCompleted']);
</script>
