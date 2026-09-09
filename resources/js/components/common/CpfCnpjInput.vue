<template>
  <div class="relative">
    <div class="relative flex items-center">
      <input
        ref="inputRef"
        type="text"
        :value="displayValue"
        @input="onInput"
        @blur="onBlur"
        :placeholder="placeholder"
        :disabled="disabled"
        :class="[
          'w-full h-10 pl-3.5 pr-28 rounded-lg border text-sm font-mono transition-all outline-hidden',
          disabled
            ? 'bg-slate-100 dark:bg-slate-800/50 text-slate-400 border-slate-200 dark:border-slate-800 cursor-not-allowed'
            : isInvalid
              ? 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-400 dark:border-rose-600 text-rose-900 dark:text-rose-100 focus:ring-2 focus:ring-rose-500'
              : isValid
                ? 'bg-emerald-50/30 dark:bg-emerald-950/20 border-emerald-400 dark:border-emerald-600 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500'
                : 'bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500'
        ]"
      />

      <!-- Ações e Badges no Canto Direito -->
      <div class="absolute right-2 flex items-center gap-1.5">
        <!-- Indicador de Validade -->
        <span
          v-if="isValid"
          class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[11px] font-bold rounded bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300"
          :title="docType === 'legal' ? 'CNPJ Válido' : 'CPF Válido'"
        >
          <Check class="w-3.5 h-3.5" />
          <span>{{ docType === 'legal' ? 'CNPJ' : 'CPF' }}</span>
        </span>

        <span
          v-else-if="isInvalid"
          class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[11px] font-bold rounded bg-rose-100 dark:bg-rose-900/60 text-rose-700 dark:text-rose-300"
          title="Documento Inválido"
        >
          <AlertCircle class="w-3.5 h-3.5" />
          <span>Inválido</span>
        </span>

        <!-- Botão Puxar Dados da CNPJá (quando for CNPJ válido) -->
        <button
          v-if="docType === 'legal' && isValid"
          type="button"
          @click="fetchCnpja"
          :disabled="loading"
          class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-md bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white shadow-xs transition-all cursor-pointer disabled:opacity-50"
          title="Puxar Razão Social, Fantasia e Endereço da API CNPJá"
        >
          <Loader2 v-if="loading" class="w-3.5 h-3.5 animate-spin" />
          <Building2 v-else class="w-3.5 h-3.5" />
          <span>{{ loading ? 'Buscando...' : 'Puxar CNPJá' }}</span>
        </button>
      </div>
    </div>

    <!-- Mensagem de Ajuda ou Erro -->
    <div class="flex items-center justify-between text-[11px] mt-1 text-slate-500 dark:text-slate-400">
      <span v-if="isInvalid" class="text-rose-500 dark:text-rose-400 font-medium">
        {{ docType === 'legal' ? 'CNPJ com dígitos verificadores inválidos.' : 'CPF com dígitos verificadores inválidos.' }}
      </span>
      <span v-else-if="isValid && docType === 'legal'" class="text-emerald-600 dark:text-emerald-400 font-medium">
        CNPJ autêntico. Clique em "Puxar CNPJá" para carregar os dados.
      </span>
      <span v-else-if="isValid && docType === 'individual'" class="text-emerald-600 dark:text-emerald-400 font-medium">
        CPF com formato e dígitos válidos.
      </span>
      <span v-else>
        Digite os 11 dígitos do CPF ou 14 dígitos do CNPJ
      </span>

      <span v-if="cleanDigits.length > 0" class="font-mono text-slate-400">
        {{ cleanDigits.length }}/{{ cleanDigits.length > 11 ? '14' : '11' }}
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Check, AlertCircle, Building2, Loader2 } from 'lucide-vue-next';

const props = withDefaults(
  defineProps<{
    modelValue?: string;
    placeholder?: string;
    disabled?: boolean;
    autoFetch?: boolean;
  }>(),
  {
    modelValue: '',
    placeholder: '000.000.000-00 ou 00.000.000/0000-00',
    disabled: false,
    autoFetch: false,
  }
);

const emit = defineEmits<{
  (e: 'update:modelValue', val: string): void;
  (e: 'change-doc-type', type: 'individual' | 'legal'): void;
  (e: 'cnpja-data', data: any): void;
  (e: 'validity-change', isValid: boolean): void;
}>();

const inputRef = ref<HTMLInputElement | null>(null);
const loading = ref(false);
const touched = ref(false);

// Dígitos limpos (apenas números)
const cleanDigits = computed(() => {
  return (props.modelValue || '').replace(/\D/g, '');
});

// Tipo de documento baseado no tamanho
const docType = computed<'individual' | 'legal'>(() => {
  return cleanDigits.value.length > 11 ? 'legal' : 'individual';
});

// Formatação com máscara de CPF ou CNPJ
function formatDocument(digits: string): string {
  if (digits.length <= 11) {
    // CPF: 000.000.000-00
    return digits
      .replace(/^(\d{3})(\d)/, '$1.$2')
      .replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3')
      .replace(/^(\d{3})\.(\d{3})\.(\d{3})(\d)/, '$1.$2.$3-$4')
      .slice(0, 14);
  } else {
    // CNPJ: 00.000.000/0000-00
    return digits
      .replace(/^(\d{2})(\d)/, '$1.$2')
      .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
      .replace(/^(\d{2})\.(\d{3})\.(\d{3})(\d)/, '$1.$2.$3/$4')
      .replace(/^(\d{2})\.(\d{3})\.(\d{3})\/(\d{4})(\d)/, '$1.$2.$3/$4-$5')
      .slice(0, 18);
  }
}

const displayValue = computed(() => {
  return formatDocument(cleanDigits.value);
});

// Algoritmo Oficial de Validação de CPF
function isValidCpf(cpf: string): boolean {
  if (cpf.length !== 11) return false;
  if (/^(\d)\1{10}$/.test(cpf)) return false;

  let sum = 0;
  for (let i = 0; i < 9; i++) {
    sum += parseInt(cpf.charAt(i)) * (10 - i);
  }
  let rest = (sum * 10) % 11;
  if (rest === 10 || rest === 11) rest = 0;
  if (rest !== parseInt(cpf.charAt(9))) return false;

  sum = 0;
  for (let i = 0; i < 10; i++) {
    sum += parseInt(cpf.charAt(i)) * (11 - i);
  }
  rest = (sum * 10) % 11;
  if (rest === 10 || rest === 11) rest = 0;
  return rest === parseInt(cpf.charAt(10));
}

// Algoritmo Oficial de Validação de CNPJ
function isValidCnpj(cnpj: string): boolean {
  if (cnpj.length !== 14) return false;
  if (/^(\d)\1{13}$/.test(cnpj)) return false;

  const weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
  const weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

  let sum = 0;
  for (let i = 0; i < 12; i++) {
    sum += parseInt(cnpj.charAt(i)) * weights1[i];
  }
  let rest = sum % 11;
  let d1 = rest < 2 ? 0 : 11 - rest;
  if (parseInt(cnpj.charAt(12)) !== d1) return false;

  sum = 0;
  for (let i = 0; i < 13; i++) {
    sum += parseInt(cnpj.charAt(i)) * weights2[i];
  }
  rest = sum % 11;
  let d2 = rest < 2 ? 0 : 11 - rest;
  return parseInt(cnpj.charAt(13)) === d2;
}

// Estados de validação
const isValid = computed(() => {
  const digits = cleanDigits.value;
  if (digits.length === 11) return isValidCpf(digits);
  if (digits.length === 14) return isValidCnpj(digits);
  return false;
});

const isInvalid = computed(() => {
  const digits = cleanDigits.value;
  if (!touched.value && digits.length < 11) return false;
  if (digits.length === 11) return !isValidCpf(digits);
  if (digits.length === 14) return !isValidCnpj(digits);
  if (digits.length > 11 && digits.length < 14 && touched.value) return true;
  if (digits.length > 14) return true;
  return false;
});

// Emissão de eventos
watch(
  () => isValid.value,
  (val) => {
    emit('validity-change', val);
  },
  { immediate: true }
);

watch(
  () => docType.value,
  (val) => {
    emit('change-doc-type', val);
  },
  { immediate: true }
);

function onInput(e: Event) {
  const target = e.target as HTMLInputElement;
  const digits = target.value.replace(/\D/g, '').slice(0, 14);
  const formatted = formatDocument(digits);
  emit('update:modelValue', formatted);

  if (digits.length >= 11) {
    touched.value = true;
  }
}

function onBlur() {
  touched.value = true;
}

// Consulta de Dados na CNPJá
async function fetchCnpja() {
  const cnpj = cleanDigits.value;
  if (cnpj.length !== 14 || !isValidCnpj(cnpj)) return;

  loading.value = true;
  try {
    const res = await fetch(`/api/v1/integrations/cnpj/${cnpj}`, {
      headers: {
        Accept: 'application/json',
      },
    });
    const json = await res.json();
    if (json.success && json.data) {
      emit('cnpja-data', json.data);
    } else {
      alert(json.message || 'CNPJ não encontrado na API CNPJá.');
    }
  } catch (err: any) {
    console.error('Erro ao consultar CNPJá:', err);
    alert('Falha na comunicação com a API CNPJá.');
  } finally {
    loading.value = false;
  }
}

defineExpose({
  fetchCnpja,
  isValid,
  docType,
  cleanDigits,
});
</script>
