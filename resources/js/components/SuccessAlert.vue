<template>
  <div v-if="show" class="rounded-lg p-4 border bg-green-50 border-green-200">
    <div class="flex items-start gap-3">
      <CheckCircleIcon class="w-5 h-5 flex-shrink-0 mt-0.5 text-green-500" />
      <div class="flex-1 min-w-0">
        <h4 v-if="title" class="font-bold text-sm mb-1 text-green-800">{{ title }}</h4>
        <p class="text-sm text-green-700">{{ message }}</p>
      </div>
      <button v-if="dismissible" @click="$emit('dismiss')" class="flex-shrink-0 text-green-500">
        <XMarkIcon class="w-5 h-5" />
      </button>
    </div>
  </div>
</template>

<script setup>
import { CheckCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline';

defineProps({
  show: { type: Boolean, default: true },
  title: { type: String, default: '' },
  message: { type: String, required: true },
  dismissible: { type: Boolean, default: false }
});

defineEmits(['dismiss']);
</script>
