<template>
  <div class="flex items-center justify-center" :class="containerClass">
    <div 
      class="animate-spin rounded-full border-t-transparent"
      :class="[sizeClass, colorClass]"
    ></div>
    <span v-if="text" class="mr-3 text-gray-600" :class="textSizeClass">{{ text }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  size: { type: String, default: 'md' }, // sm, md, lg, xl
  color: { type: String, default: 'primary' },
  text: { type: String, default: '' },
  fullPage: { type: Boolean, default: false }
});

const sizeClass = computed(() => ({
  'sm': 'w-4 h-4 border-2',
  'md': 'w-8 h-8 border-3',
  'lg': 'w-12 h-12 border-4',
  'xl': 'w-16 h-16 border-4'
}[props.size]));

const textSizeClass = computed(() => ({
  'sm': 'text-xs',
  'md': 'text-sm',
  'lg': 'text-base',
  'xl': 'text-lg'
}[props.size]));

const colorClass = computed(() => ({
  'primary': 'border-primary-500',
  'secondary': 'border-secondary-500',
  'white': 'border-white',
  'gray': 'border-gray-500'
}[props.color]));

const containerClass = computed(() => props.fullPage ? 'min-h-[400px]' : '');
</script>
