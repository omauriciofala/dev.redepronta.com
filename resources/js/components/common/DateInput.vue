<template>
  <div class="relative w-full">
    <!-- Rótulo opcional -->
    <label v-if="label" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>

    <div class="relative flex items-center">
      <!-- Ícone de Calendário na Esquerda -->
      <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 pointer-events-none">
        <Calendar class="w-4 h-4" />
      </div>

      <!-- Input Formatado Dia, Mês e Ano -->
      <input
        ref="inputRef"
        type="text"
        :value="formattedValue"
        @input="onInput"
        @blur="onBlur"
        placeholder="DD/MM/AAAA"
        maxlength="10"
        :disabled="disabled"
        :required="required"
        :class="[
          hasError
            ? 'border-red-400 focus:ring-red-400 dark:border-red-800'
            : 'border-slate-300 dark:border-slate-700 focus:ring-blue-500',
          disabled ? 'opacity-60 cursor-not-allowed' : ''
        ]"
        class="w-full h-10 pl-10 pr-12 rounded-lg border bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 focus:ring-2 focus:border-transparent outline-hidden text-sm font-mono transition"
      />

      <!-- Indicador Canônico de Formato Brasileiro à Direita -->
      <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
        <span class="px-1.5 py-0.5 rounded-sm text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
          pt-BR
        </span>
      </div>
    </div>

    <!-- Mensagem de Data Inválida se digitada incorretamente -->
    <p v-if="hasError && formattedValue.length === 10" class="text-[11px] text-red-500 mt-1">
      Data inválida. Use o formato Dia, Mês e Ano (ex: 25/12/1990).
    </p>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Calendar } from 'lucide-vue-next';

const props = withDefaults(defineProps<{
  modelValue?: string | null;
  label?: string;
  required?: boolean;
  disabled?: boolean;
}>(), {
  modelValue: '',
  label: '',
  required: false,
  disabled: false,
});

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void;
  (e: 'change', value: string): void;
}>();

const inputRef = ref<HTMLInputElement | null>(null);
const rawDigits = ref('');

// Converte entrada inicial (ISO YYYY-MM-DD ou DD/MM/AAAA) para dígitos puros
const parseToDigits = (val: string | null | undefined): string => {
  if (!val) return '';
  const trimmed = val.trim();

  // Se já for ISO: YYYY-MM-DD
  if (/^\d{4}-\d{2}-\d{2}/.test(trimmed)) {
    const parts = trimmed.split('T')[0].split('-');
    const [year, month, day] = parts;
    return `${day}${month}${year}`;
  }

  // Se for com barras ou apenas números
  return trimmed.replace(/\D/g, '').slice(0, 8);
};

// Formata para visualização com barras: DD/MM/AAAA
const formatDigits = (digits: string): string => {
  if (!digits) return '';
  const d = digits.slice(0, 2);
  const m = digits.slice(2, 4);
  const y = digits.slice(4, 8);

  if (digits.length <= 2) return d;
  if (digits.length <= 4) return `${d}/${m}`;
  return `${d}/${m}/${y}`;
};

// Inicializa estado
rawDigits.value = parseToDigits(props.modelValue);

watch(() => props.modelValue, (newVal) => {
  const newDigits = parseToDigits(newVal);
  if (newDigits !== rawDigits.value) {
    rawDigits.value = newDigits;
  }
});

const formattedValue = computed(() => formatDigits(rawDigits.value));

// Validação simples de dia <= 31 e mês <= 12
const hasError = computed(() => {
  if (rawDigits.value.length < 8) return false;
  const day = parseInt(rawDigits.value.slice(0, 2), 10);
  const month = parseInt(rawDigits.value.slice(2, 4), 10);
  const year = parseInt(rawDigits.value.slice(4, 8), 10);

  if (day < 1 || day > 31) return true;
  if (month < 1 || month > 12) return true;
  if (year < 1900 || year > 2100) return true;
  return false;
});

const onInput = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const val = target.value;
  // Extrai apenas dígitos
  const digits = val.replace(/\D/g, '').slice(0, 8);
  rawDigits.value = digits;

  // Emite no formato canônico DD/MM/AAAA se preenchido
  const formatted = formatDigits(digits);
  emit('update:modelValue', formatted);
};

const onBlur = () => {
  emit('change', formatDigits(rawDigits.value));
};
</script>
