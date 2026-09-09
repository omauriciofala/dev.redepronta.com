<template>
  <div class="relative w-full">
    <!-- Rótulo opcional -->
    <label
      v-if="label"
      :for="id"
      class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5"
    >
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>

    <div class="relative flex items-center w-full group">
      <!-- Ícone opcional à esquerda -->
      <div
        v-if="$slots.icon"
        class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 pointer-events-none flex items-center z-10"
      >
        <slot name="icon" />
      </div>

      <!-- Select Nativo Canônico -->
      <select
        :id="id"
        :name="name"
        :value="modelValue"
        :disabled="disabled"
        :required="required"
        @change="handleChange"
        @blur="$emit('blur', $event)"
        @focus="$emit('focus', $event)"
        class="w-full appearance-none no-custom-arrow !bg-none rounded-lg border bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] focus:outline-hidden transition cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed"
        :class="[
          sizeClass,
          $slots.icon ? 'pl-10' : (size === 'sm' ? 'pl-3' : 'pl-3.5'),
          size === 'sm' ? 'pr-9' : 'pr-11',
          hasError
            ? 'border-red-400 dark:border-red-800 focus:ring-red-400'
            : 'border-slate-300 dark:border-slate-700',
          selectClass
        ]"
      >
        <!-- Placeholder / Opção Padrão -->
        <option
          v-if="placeholder"
          :value="placeholderValue"
          :disabled="placeholderDisabled"
        >
          {{ placeholder }}
        </option>

        <!-- Slot Default para options customizadas ou options fornecidas via props -->
        <slot>
          <option
            v-for="opt in formattedOptions"
            :key="String(opt.value)"
            :value="opt.value"
            :disabled="opt.disabled"
          >
            {{ opt.label }}
          </option>
        </slot>
      </select>

      <!-- Ícone de Seta com Recuo Elegante e Confortável da Borda (mais pra dentro do campo) -->
      <div
        class="absolute top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300 transition-colors flex items-center justify-center z-10"
        :class="[
          size === 'sm' ? 'right-3.5' : 'right-4',
          disabled ? 'opacity-50' : ''
        ]"
      >
        <ChevronDown :class="size === 'sm' ? 'w-3.5 h-3.5' : 'w-4 h-4'" />
      </div>
    </div>

    <!-- Mensagem de Erro ou Dica -->
    <p v-if="hasError && errorMessage" class="text-[11px] text-red-500 mt-1">
      {{ errorMessage }}
    </p>
    <p v-else-if="hint" class="text-xs text-slate-500 dark:text-slate-400 mt-1">
      {{ hint }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { ChevronDown } from 'lucide-vue-next';

export interface SelectOptionItem {
  value: any;
  label: string;
  disabled?: boolean;
}

const props = withDefaults(defineProps<{
  modelValue?: any;
  label?: string;
  placeholder?: string;
  placeholderValue?: any;
  placeholderDisabled?: boolean;
  options?: Array<SelectOptionItem | string | number>;
  required?: boolean;
  disabled?: boolean;
  id?: string;
  name?: string;
  hasError?: boolean;
  errorMessage?: string;
  hint?: string;
  selectClass?: string;
  size?: 'sm' | 'md' | 'lg';
}>(), {
  modelValue: '',
  label: '',
  placeholder: '',
  placeholderValue: '',
  placeholderDisabled: false,
  options: () => [],
  required: false,
  disabled: false,
  id: undefined,
  name: undefined,
  hasError: false,
  errorMessage: '',
  hint: '',
  selectClass: '',
  size: 'md',
});

const emit = defineEmits<{
  (e: 'update:modelValue', value: any): void;
  (e: 'change', event: Event): void;
  (e: 'blur', event: FocusEvent): void;
  (e: 'focus', event: FocusEvent): void;
}>();

const sizeClass = computed(() => {
  switch (props.size) {
    case 'sm':
      return 'h-9 text-xs';
    case 'lg':
      return 'h-11 text-base';
    case 'md':
    default:
      return 'h-10 text-sm';
  }
});

const formattedOptions = computed<SelectOptionItem[]>(() => {
  if (!props.options || !Array.isArray(props.options)) return [];
  return props.options.map((opt) => {
    if (typeof opt === 'object' && opt !== null && 'value' in opt) {
      return opt as SelectOptionItem;
    }
    return {
      value: opt,
      label: String(opt),
    };
  });
});

const handleChange = (e: Event) => {
  const target = e.target as HTMLSelectElement;
  emit('update:modelValue', target.value);
  emit('change', e);
};
</script>
