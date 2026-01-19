<template>
  <div v-if="show" class="rounded-lg p-4 border" :class="alertClass">
    <div class="flex items-start gap-3">
      <ExclamationCircleIcon class="w-5 h-5 flex-shrink-0 mt-0.5" :class="iconClass" />
      <div class="flex-1 min-w-0">
        <h4 v-if="title" class="font-bold text-sm mb-1" :class="titleClass">{{ title }}</h4>
        <p class="text-sm" :class="textClass">{{ message }}</p>
        <button v-if="retryable" @click="$emit('retry')" class="mt-2 text-sm font-medium underline" :class="textClass">
          إعادة المحاولة
        </button>
      </div>
      <button v-if="dismissible" @click="$emit('dismiss')" class="flex-shrink-0" :class="iconClass">
        <XMarkIcon class="w-5 h-5" />
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { ExclamationCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  show: { type: Boolean, default: true },
  title: { type: String, default: '' },
  message: { type: String, required: true },
  variant: { type: String, default: 'error' }, // error, warning
  dismissible: { type: Boolean, default: false },
  retryable: { type: Boolean, default: false }
});

defineEmits(['dismiss', 'retry']);

const alertClass = computed(() => ({
  'error': 'bg-red-50 border-red-200',
  'warning': 'bg-yellow-50 border-yellow-200'
}[props.variant]));

const iconClass = computed(() => ({
  'error': 'text-red-500',
  'warning': 'text-yellow-500'
}[props.variant]));

const titleClass = computed(() => ({
  'error': 'text-red-800',
  'warning': 'text-yellow-800'
}[props.variant]));

const textClass = computed(() => ({
  'error': 'text-red-700',
  'warning': 'text-yellow-700'
}[props.variant]));
</script>
