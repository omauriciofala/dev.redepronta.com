<template>
  <BaseModal
    :model-value="modelValue"
    title="Promover Demanda para Chamado Formal (Ticket)"
    size="lg"
    @close="$emit('update:modelValue', false)"
  >
    <form id="promoteTicketForm" @submit.prevent="handleSubmit" class="space-y-4">
      <!-- Card Informativo da Tarefa de Origem -->
      <div v-if="task" class="p-3.5 rounded-xl bg-orange-50/70 dark:bg-orange-950/20 border border-orange-200 dark:border-orange-900/50 flex items-start gap-3 text-xs">
        <Sparkles class="w-4 h-4 text-[#FC6714] shrink-0 mt-0.5" />
        <div>
          <div class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
            <span>Tarefa de Origem:</span>
            <span class="font-mono text-[#FC6714]">{{ task.task_number }}</span>
          </div>
          <p class="text-slate-600 dark:text-slate-400 mt-0.5">
            {{ task.title }}
          </p>
        </div>
      </div>

      <!-- Título do Chamado -->
      <div>
        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
          Título Oficial do Chamado <span class="text-red-500">*</span>
        </label>
        <input
          v-model="form.title"
          type="text"
          required
          class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
        />
      </div>

      <!-- Cliente e Cidade -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
            Cliente do Atendimento <span class="text-red-500">*</span>
          </label>
          <PersonSearchSelect
            v-model="form.customer_person_id"
            placeholder="Selecione o cliente..."
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
            Município do Atendimento <span class="text-red-500">*</span>
          </label>
          <CitySearchSelect
            v-model="form.city_id"
            placeholder="Selecione a cidade..."
          />
        </div>
      </div>

      <!-- Estrutura Hierárquica: Departamento -> Categoria -> Motivo -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <!-- Departamento -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
            Departamento <span class="text-red-500">*</span>
          </label>
          <select
            v-model="form.department_id"
            @change="onDepartmentChange"
            required
            class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
          >
            <option :value="null" disabled>Selecione...</option>
            <option v-for="dept in departments" :key="dept.id" :value="dept.id">
              {{ dept.name }}
            </option>
          </select>
        </div>

        <!-- Categoria -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
            Categoria <span class="text-red-500">*</span>
          </label>
          <select
            v-model="form.category_id"
            @change="onCategoryChange"
            :disabled="!availableCategories.length"
            required
            class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] focus:outline-none disabled:opacity-50"
          >
            <option :value="null" disabled>Selecione...</option>
            <option v-for="cat in availableCategories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
        </div>

        <!-- Motivo (Reason) -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
            Motivo do Chamado <span class="text-red-500">*</span>
          </label>
          <select
            v-model="form.reason_id"
            @change="onReasonChange"
            :disabled="!availableReasons.length"
            required
            class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] focus:outline-none disabled:opacity-50"
          >
            <option :value="null" disabled>Selecione...</option>
            <option v-for="rsn in availableReasons" :key="rsn.id" :value="rsn.id">
              {{ rsn.name }} ({{ rsn.default_sla_hours }}h SLA)
            </option>
          </select>
        </div>
      </div>

      <!-- Banner de SLA Calculado -->
      <div v-if="selectedReason" class="p-3 rounded-lg bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-900/60 flex items-center justify-between text-xs text-indigo-900 dark:text-indigo-200">
        <div class="flex items-center gap-2">
          <Clock class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
          <span>Prazo Máximo de Resolução (SLA do Motivo):</span>
        </div>
        <span class="font-bold px-2 py-0.5 rounded-md bg-indigo-100 dark:bg-indigo-900/80 text-indigo-800 dark:text-indigo-200">
          {{ selectedReason.default_sla_hours }} horas
        </span>
      </div>

      <!-- Endereço do Local -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="sm:col-span-2">
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
            Logradouro / Rua
          </label>
          <input
            v-model="form.address_street"
            type="text"
            placeholder="Ex: Av. Paulista"
            class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
            Número
          </label>
          <input
            v-model="form.address_number"
            type="text"
            placeholder="Ex: 1000"
            class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
          />
        </div>
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
          form="promoteTicketForm"
          :disabled="isSubmitting"
          class="px-4 py-2 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-xs font-bold transition shadow-xs flex items-center gap-2 cursor-pointer disabled:opacity-50"
        >
          <Loader2 v-if="isSubmitting" class="w-3.5 h-3.5 animate-spin" />
          <span>{{ isSubmitting ? 'Promovendo...' : 'Emitir Chamado com SLA' }}</span>
        </button>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { reactive, ref, computed, watch } from 'vue';
import axios from 'axios';
import { AlertCircle, Loader2, Clock, Sparkles } from 'lucide-vue-next';
import BaseModal from '../common/BaseModal.vue';
import PersonSearchSelect from '../common/PersonSearchSelect.vue';
import CitySearchSelect from '../common/CitySearchSelect.vue';

const props = defineProps<{
  modelValue: boolean;
  task: any;
  departments: any[];
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', val: boolean): void;
  (e: 'promoted', ticket: any): void;
}>();

const isSubmitting = ref(false);
const errorMessage = ref('');

const form = reactive({
  title: '',
  description: '',
  customer_person_id: null as number | null,
  city_id: null as number | null,
  department_id: null as number | null,
  category_id: null as number | null,
  reason_id: null as number | null,
  priority: 'MEDIUM',
  address_street: '',
  address_number: '',
});

const availableCategories = computed(() => {
  if (!form.department_id || !props.departments) return [];
  const dept = props.departments.find((d) => d.id === form.department_id);
  return dept?.ticket_categories || [];
});

const availableReasons = computed(() => {
  if (!form.category_id || !availableCategories.value.length) return [];
  const cat = availableCategories.value.find((c: any) => c.id === form.category_id);
  return cat?.reasons || [];
});

const selectedReason = computed(() => {
  if (!form.reason_id || !availableReasons.value.length) return null;
  return availableReasons.value.find((r: any) => r.id === form.reason_id);
});

function onDepartmentChange() {
  form.category_id = null;
  form.reason_id = null;
}

function onCategoryChange() {
  form.reason_id = null;
}

function onReasonChange() {
  if (selectedReason.value) {
    form.priority = selectedReason.value.default_priority || 'MEDIUM';
  }
}

watch(
  () => props.task,
  (t) => {
    if (t) {
      form.title = t.title || '';
      form.description = t.description || '';
      form.customer_person_id = t.customer_person_id || null;
      form.priority = t.priority || 'MEDIUM';
    }
  },
  { immediate: true }
);

const handleSubmit = async () => {
  if (!form.customer_person_id) {
    errorMessage.value = 'Por favor, selecione o cliente para o chamado.';
    return;
  }
  if (!form.city_id) {
    errorMessage.value = 'Por favor, selecione o município do chamado.';
    return;
  }
  if (!form.department_id || !form.category_id || !form.reason_id) {
    errorMessage.value = 'Por favor, classifique o departamento, categoria e motivo.';
    return;
  }

  isSubmitting.value = true;
  errorMessage.value = '';

  try {
    const res = await axios.post(`/api/v1/operations/tasks/${props.task.id}/promote`, form);
    emit('promoted', res.data?.data);
    emit('update:modelValue', false);
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Erro ao promover tarefa.';
  } finally {
    isSubmitting.value = false;
  }
};
</script>
