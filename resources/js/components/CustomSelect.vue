<template>
  <div class="relative" ref="selectRef">
    <button 
      type="button"
      @click="toggleDropdown"
      :disabled="disabled"
      :class="[
        bgClass,
        'w-full rounded-xl px-4 py-3 text-sm text-left text-white focus:outline-none transition-colors flex justify-between items-center disabled:opacity-50 disabled:cursor-not-allowed border',
        isOpen ? 'border-red-600 shadow-[0_0_15px_rgba(220,38,38,0.2)]' : ''
      ]"
    >
      <span :class="{'text-zinc-500': !selectedOption}" class="truncate pr-2">
        {{ selectedOption ? selectedOption.name : placeholder }}
      </span>
      <component :is="ChevronDown" 
        class="w-4 h-4 text-zinc-400 transition-transform duration-200 flex-shrink-0" 
        :class="{'rotate-180': isOpen}"
      />
    </button>

    <Transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <div 
        v-if="isOpen"
        class="absolute z-50 w-full mt-2 bg-zinc-900 border border-zinc-800 rounded-xl shadow-2xl max-h-60 overflow-y-auto overflow-x-hidden custom-scrollbar"
      >
        <div class="p-1">
          <button
            v-for="option in options"
            :key="option.id"
            @click="selectOption(option)"
            type="button"
            class="w-full text-left px-3 py-2.5 text-sm rounded-lg hover:bg-zinc-800 transition-colors flex items-center justify-between"
            :class="{'text-red-500 bg-red-500/10 hover:bg-red-500/20 font-bold': modelValue === option.id, 'text-zinc-300': modelValue !== option.id}"
          >
            <span class="truncate pr-2">{{ option.name }}</span>
            <component :is="Check" v-if="modelValue === option.id" class="w-4 h-4 text-red-500 flex-shrink-0" />
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { ChevronDown, Check } from 'lucide-vue-next';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  options: {
    type: Array,
    default: () => []
  },
  placeholder: {
    type: String,
    default: 'Pilih...'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  bgClass: {
    type: String,
    default: 'bg-zinc-900 border-zinc-800'
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const isOpen = ref(false);
const selectRef = ref(null);

const selectedOption = computed(() => {
  return props.options.find(opt => String(opt.id) === String(props.modelValue));
});

const toggleDropdown = () => {
  if (!props.disabled) {
    isOpen.value = !isOpen.value;
  }
};

const selectOption = (option) => {
  emit('update:modelValue', option.id);
  emit('change', option.id);
  isOpen.value = false;
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (selectRef.value && !selectRef.value.contains(event.target)) {
    isOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
/* Custom Scrollbar for the dropdown */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #3f3f46; /* zinc-700 */
  border-radius: 10px;
}
</style>
