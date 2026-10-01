<template>
  <BaseModal
    :model-value="modelValue"
    title="Despachar Chamado para Campo (Acionamento FSM)"
    size="md"
    @close="$emit('update:modelValue', false)"
  >
    <form id="dispatchForm" @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Identificação do Chamado -->
      <div v-if="ticket" class="p-3.5 rounded-xl bg-blue-50/70 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/50 flex items-start gap-3 text-xs">
        <Truck class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" />
        <div>
          <div class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
            <span>Chamado:</span>
            <span class="font-mono text-blue-600 dark:text-blue-400">{{ ticket.protocol }}</span>
          </div>
          <p class="text-slate-600 dark:text-slate-400 mt-0.5">
            {{ ticket.title }}
          </p>
          <p class="text-slate-500 dark:text-slate-400 mt-1 font-medium">
            Cliente: {{ ticket.customer_person?.name }} • {{ ticket.city?.name }}/{{ ticket.city?.state?.code }}
          </p>
        </div>
      </div>

      <!-- Seleção de Técnico Responsável -->
      <div>
        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
          Técnico de Campo Responsável <span class="text-red-500">*</span>
        </label>
        <select
          v-model="form.worker_person_id"
          required
          class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
        >
          <option :value="null" disabled>Selecione o técnico de campo...</option>
          <option v-for="w in workers" :key="w.id" :value="w.id">
            {{ w.name }} ({{ w.phone || 'Sem telefone' }})
          </option>
        </select>
      </div>

      <!-- Base de Apoio / Depósito de Suprimentos -->
      <div>
        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
          Base Operacional / Almoxarifado de Apoio <span class="text-red-500">*</span>
        </label>
        <select
          v-model="form.depot_id"
          required
          class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
        >
          <option :value="null" disabled>Selecione a base de saída...</option>
          <option v-for="d in depots" :key="d.id" :value="d.id">
            {{ d.name }} • {{ d.cluster?.name || 'Sem Regional' }}
          </option>
        </select>
      </div>

      <!-- Instruções / Observações de Despacho -->
      <div>
        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
          Instruções Operacionais para a Equipe
        </label>
        <textarea
          v-model="form.notes"
          rows="3"
          placeholder="Ex: Levar máquina de fusão, ONU reserva e escada de 7m..."
          class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
        ></textarea>
      </div>

      <!-- Feedback de Erro -->
      <div v-if="errorMessage" class="p-3 rounded-lg bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900 text-red-700 dark:text-red-300 text-xs flex items-center gap-2">
        <AlertCircle class="w-4 h-4 shrink-0" />
        <span>{{ errorMessage }}</span>
      </div>
    </form>

    <template #footer>
      <div class="flex items-center justify-end gap-2.5 w-full">
        <button
          type="button"
          @click="$emit('update:modelValue', false)"
          class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 text-xs font-semibold cursor-pointer"
        >
          Cancelar
        </button>
        <button
          type="submit"
          form="dispatchForm"
          :disabled="isSubmitting"
          class="px-4 py-2 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-xs font-bold transition shadow-xs flex items-center gap-2 cursor-pointer disabled:opacity-50"
        >
          <Loader2 v-if="isSubmitting" class="w-3.5 h-3.5 animate-spin" />
          <span>{{ isSubmitting ? 'Despachando...' : 'Confirmar Acionamento de Campo' }}</span>
        </button>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { reactive, ref, watch } from 'vue';
import axios from 'axios';
import { AlertCircle, Loader2, Truck } from 'lucide-vue-next';
import BaseModal from '../common/BaseModal.vue';

const props = defineProps<{
  modelValue: boolean;
  ticket: any;
  workers: any[];
  depots: any[];
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', val: boolean): void;
  (e: 'dispatched'): void;
}>();

const isSubmitting = ref(false);
const errorMessage = ref('');

const form = reactive({
  worker_person_id: null as number | null,
  depot_id: null as number | null,
  notes: '',
});

watch(
  () => props.ticket,
  () => {
    form.worker_person_id = props.workers?.[0]?.id || null;
    form.depot_id = props.depots?.[0]?.id || null;
    form.notes = '';
  },
  { immediate: true }
);

const handleSubmit = async () => {
  if (!form.worker_person_id) {
    errorMessage.value = 'Selecione o técnico responsável.';
    return;
  }
  if (!form.depot_id) {
    errorMessage.value = 'Selecione a base operacional.';
    return;
  }

  isSubmitting.value = true;
  errorMessage.value = '';

  try {
    await axios.post(`/api/v1/operations/tickets/${props.ticket.id}/dispatch`, form);
    emit('dispatched');
    emit('update:modelValue', false);
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Erro ao despachar chamado.';
  } finally {
    isSubmitting.value = false;
  }
};
</script>
