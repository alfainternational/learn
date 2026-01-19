<template>
  <div class="w-full">
    <div v-if="showLabel" class="flex justify-between items-center mb-2">
      <span class="text-sm font-medium text-gray-700">{{ label }}</span>
      <span class="text-sm font-bold" :class="percentageColorClass">{{ percentage }}%</span>
    </div>
    <div class="w-full rounded-full overflow-hidden" :class="[heightClass, bgClass]">
      <div 
        class="h-full rounded-full transition-all duration-500 ease-out"
        :class="barColorClass"
        :style="{ width: `${Math.min(percentage, 100)}%` }"
      ></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  percentage: { type: Number, default: 0 },
  label: { type: String, default: '' },
  showLabel: { type: Boolean, default: true },
  size: { type: String, default: 'md' }, // sm, md, lg
  color: { type: String, default: 'primary' }, // primary, success, warning, danger
  animated: { type: Boolean, default: false }
});

const heightClass = computed(() => ({
  'sm': 'h-1.5',
  'md': 'h-2.5',
  'lg': 'h-4'
}[props.size]));

const bgClass = computed(() => 'bg-gray-200');

const barColorClass = computed(() => {
  const colors = {
    'primary': 'bg-primary-500',
    'success': 'bg-green-500',
    'warning': 'bg-yellow-500',
    'danger': 'bg-red-500',
    'gradient': 'bg-gradient-to-r from-primary-500 to-secondary-500'
  };
  return props.animated ? `${colors[props.color]} animate-pulse` : colors[props.color];
});

const percentageColorClass = computed(() => {
  if (props.percentage >= 80) return 'text-green-600';
  if (props.percentage >= 50) return 'text-yellow-600';
  return 'text-gray-600';
});
</script>
