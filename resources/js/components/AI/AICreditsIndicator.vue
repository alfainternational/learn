<template>
    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-full">
        <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
        </svg>
        
        <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-gray-700">رصيد AI:</span>
            <span v-if="hasUnlimited" class="text-sm font-bold text-purple-600">غير محدود ∞</span>
            <template v-else>
                <span class="text-sm font-bold" :class="creditsClass">{{ credits }}</span>
                <div class="w-16 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                    <div 
                        class="h-full transition-all duration-300"
                        :class="progressClass"
                        :style="{ width: progressWidth + '%' }"
                    ></div>
                </div>
            </template>
        </div>

        <button 
            v-if="!hasUnlimited && credits === 0"
            @click="$emit('upgrade')"
            class="text-xs px-2 py-0.5 bg-purple-600 text-white rounded-full hover:bg-purple-700 transition-colors">
            ترقية
        </button>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    credits: {
        type: Number,
        default: 0
    },
    maxCredits: {
        type: Number,
        default: 3
    },
    hasUnlimited: {
        type: Boolean,
        default: false
    }
});

defineEmits(['upgrade']);

const progressWidth = computed(() => {
    if (props.hasUnlimited) return 100;
    return (props.credits / props.maxCredits) * 100;
});

const creditsClass = computed(() => {
    if (props.credits === 0) return 'text-red-600';
    if (props.credits === 1) return 'text-orange-600';
    return 'text-purple-600';
});

const progressClass = computed(() => {
    if (props.credits === 0) return 'bg-red-500';
    if (props.credits === 1) return 'bg-orange-500';
    return 'bg-purple-500';
});
</script>
