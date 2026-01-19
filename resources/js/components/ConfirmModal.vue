<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" dir="rtl">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50" @click="!persistent && $emit('cancel')"></div>
        
        <!-- Modal -->
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
          <!-- Icon -->
          <div class="w-14 h-14 mx-auto mb-4 rounded-full flex items-center justify-center" :class="iconBgClass">
            <component :is="variantIcon" class="w-7 h-7" :class="iconColorClass" />
          </div>
          
          <!-- Content -->
          <div class="text-center mb-6">
            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ title }}</h3>
            <p class="text-gray-500">{{ message }}</p>
          </div>
          
          <!-- Actions -->
          <div class="flex gap-3">
            <button 
              @click="$emit('cancel')" 
              class="flex-1 btn btn-secondary"
              :disabled="loading"
            >
              {{ cancelText }}
            </button>
            <button 
              @click="$emit('confirm')" 
              class="flex-1 btn"
              :class="confirmBtnClass"
              :disabled="loading"
            >
              <LoadingSpinner v-if="loading" size="sm" color="white" class="ml-2" />
              {{ confirmText }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue';
import { 
  ExclamationTriangleIcon, 
  TrashIcon, 
  QuestionMarkCircleIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline';
import LoadingSpinner from './LoadingSpinner.vue';

const props = defineProps({
  show: { type: Boolean, default: false },
  title: { type: String, default: 'تأكيد' },
  message: { type: String, default: 'هل أنت متأكد من هذا الإجراء؟' },
  variant: { type: String, default: 'warning' }, // warning, danger, info, success
  confirmText: { type: String, default: 'تأكيد' },
  cancelText: { type: String, default: 'إلغاء' },
  loading: { type: Boolean, default: false },
  persistent: { type: Boolean, default: false }
});

defineEmits(['confirm', 'cancel']);

const variantIcon = computed(() => ({
  'warning': ExclamationTriangleIcon,
  'danger': TrashIcon,
  'info': QuestionMarkCircleIcon,
  'success': CheckCircleIcon
}[props.variant]));

const iconBgClass = computed(() => ({
  'warning': 'bg-yellow-100',
  'danger': 'bg-red-100',
  'info': 'bg-blue-100',
  'success': 'bg-green-100'
}[props.variant]));

const iconColorClass = computed(() => ({
  'warning': 'text-yellow-600',
  'danger': 'text-red-600',
  'info': 'text-blue-600',
  'success': 'text-green-600'
}[props.variant]));

const confirmBtnClass = computed(() => ({
  'warning': 'btn-primary',
  'danger': 'bg-red-600 hover:bg-red-700 text-white',
  'info': 'btn-primary',
  'success': 'bg-green-600 hover:bg-green-700 text-white'
}[props.variant]));
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
.modal-enter-from > div:last-child,
.modal-leave-to > div:last-child {
  transform: scale(0.95);
}
</style>
