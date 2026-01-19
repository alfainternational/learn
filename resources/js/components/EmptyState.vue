<template>
  <div class="text-center py-12 px-4">
    <div class="w-20 h-20 mx-auto mb-6 rounded-full flex items-center justify-center" :class="iconBgClass">
      <component :is="icon" class="w-10 h-10" :class="iconColorClass" />
    </div>
    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ title }}</h3>
    <p class="text-gray-500 mb-6 max-w-sm mx-auto">{{ description }}</p>
    <slot name="action">
      <button v-if="actionText" @click="$emit('action')" class="btn btn-primary">
        {{ actionText }}
      </button>
    </slot>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { DocumentTextIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  icon: { type: Object, default: () => DocumentTextIcon },
  title: { type: String, default: 'لا توجد بيانات' },
  description: { type: String, default: 'لم يتم العثور على أي عناصر' },
  actionText: { type: String, default: '' },
  variant: { type: String, default: 'default' }
});

defineEmits(['action']);

const iconBgClass = computed(() => ({
  'default': 'bg-gray-100',
  'primary': 'bg-primary-50',
  'warning': 'bg-yellow-50'
}[props.variant]));

const iconColorClass = computed(() => ({
  'default': 'text-gray-400',
  'primary': 'text-primary-500',
  'warning': 'text-yellow-500'
}[props.variant]));
</script>
