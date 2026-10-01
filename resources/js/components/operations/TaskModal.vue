<template>
  <BaseModal
    :model-value="modelValue"
    :title="mode === 'create' ? 'Nova Demanda no Inbox' : 'Editar Demanda'"
    size="md"
    @close="$emit('update:modelValue', false)"
  >
    <form id="taskForm" @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Título da Tarefa -->
      <div>
        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
          Título da Demanda / Assunto <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.title"
          type="text"
          required
          placeholder="Ex: Luz vermelha (LOS) piscando no modem, lentidão severa..."
          class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
        />
      </div>

      <!-- Origem e Prioridade -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
            Canal de Origem
          </label>
          <select
            v-model="form.source"
            class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
          >
            <option value="MANUAL">Manual (Interno / Balcão)</option>
            <option value="WHATSAPP">WhatsApp</option>
            <option value="EMAIL">E-mail</option>
            <option value="AI">Triagem Automática por IA</option>
            <option value="API">Integração / Webhook API</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
            Prioridade Inicial
          </label>
          <select
            v-model="form.priority"
            class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
          >
            <option value="LOW">Baixa</option>
            <option value="MEDIUM">Média</option>
            <option value="HIGH">Alta</option>
            <option value="CRITICAL">Crítica / Emergencial</option>
          </select>
        </div>
      </div>

      <!-- Cliente Relacionado (Opcional na Tarefa) -->
      <div>
        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
          Cliente Solicitante (Opcional para tarefas de Inbox)
        </label>
        <PersonSearchSelect
          v-model="form.customer_person_id"
          placeholder="Buscar cliente por nome ou CPF/CNPJ..."
        />
      </div>

      <!-- Descrição Detalhada -->
      <div>
        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
          Descrição Detalhada do Relato
        </label>
        <textarea
          v-model="form.description"
          rows="3"
          placeholder="Descreva o contexto, informações passadas pelo solicitante ou logs..."
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
          form="taskForm"
          :disabled="isSubmitting"
          class="px-4 py-2 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-xs font-bold transition shadow-xs flex items-center gap-2 cursor-pointer disabled:opacity-50"
        >
          <Loader2 v-if="isSubmitting" class="w-3.5 h-3.5 animate-spin" />
          <span>{{ isSubmitting ? 'Salvando...' : (mode === 'create' ? 'Criar Tarefa no Inbox' : 'Salvar Alterações') }}</span>
        </button>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { reactive, ref, watch } from 'vue';
import axios from 'axios';
import { AlertCircle, Loader2 } from 'lucide-vue-next';
import BaseModal from '../common/BaseModal.vue';
import PersonSearchSelect from '../common/PersonSearchSelect.vue';

const props = defineProps<{
  modelValue: boolean;
  mode: 'create' | 'edit';
  taskData?: any;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', val: boolean): void;
  (e: 'saved'): void;
}>();

const isSubmitting = ref(false);
const errorMessage = ref('');

const form = reactive({
  title: '',
  description: '',
  source: 'MANUAL',
  priority: 'MEDIUM',
  customer_person_id: null as number | null,
});

watch(
  () => props.taskData,
  (val) => {
    if (val && props.mode === 'edit') {
      form.title = val.title || '';
      form.description = val.description || '';
      form.source = val.source || 'MANUAL';
      form.priority = val.priority || 'MEDIUM';
      form.customer_person_id = val.customer_person_id || null;
    } else {
      form.title = '';
      form.description = '';
      form.source = 'MANUAL';
      form.priority = 'MEDIUM';
      form.customer_person_id = null;
    }
  },
  { immediate: true }
);

const handleSubmit = async () => {
  isSubmitting.value = true;
  errorMessage.value = '';

  try {
    if (props.mode === 'create') {
      await axios.post('/api/v1/operations/tasks', form);
    } else {
      await axios.put(`/api/v1/operations/tasks/${props.taskData.id}`, form);
    }
    emit('saved');
    emit('update:modelValue', false);
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Erro ao salvar demanda.';
  } finally {
    isSubmitting.value = false;
  }
};
</script>
