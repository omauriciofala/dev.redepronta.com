<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="$emit('close')"
    :title="isEditing ? 'Editar Depósito' : 'Novo Depósito'"
    description="Cadastro e parametrização de depósitos físicos, almoxarifados e bases operacionais"
    size="lg"
  >
    <!-- Header Badge -->
    <template #header-badge>
      <span
        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border"
        :class="isEditing
          ? 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800'
          : 'bg-orange-50 text-[#FC6714] border-orange-200 dark:bg-[#FC6714]/15 dark:text-orange-300 dark:border-[#FC6714]/30'"
      >
        {{ isEditing ? `Editando: ${form.code}` : 'Novo Cadastro' }}
      </span>
    </template>

    <form id="depotForm" @submit.prevent="submitForm" class="space-y-4 text-xs">
      <!-- Alerta de Erro -->
      <div
        v-if="errorMessage"
        class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 font-medium flex items-center gap-2"
      >
        <AlertCircle class="w-4 h-4 shrink-0 text-rose-600 dark:text-rose-400" />
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Linha 1: Nome do Depósito e Código -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="sm:col-span-2">
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Nome do Depósito *
          </label>
          <input
            type="text"
            v-model="form.name"
            required
            placeholder="Ex: Almoxarifado Central Matriz"
            class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none"
          />
        </div>

        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Código / Sigla *
          </label>
          <input
            type="text"
            v-model="form.code"
            required
            placeholder="Ex: ALMOX-01"
            class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 font-mono text-xs uppercase focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none"
          />
        </div>
      </div>

      <!-- Linha 2: Posição Regional e Tipo de Depósito -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Posição Regional *
          </label>
          <select
            v-model="form.cluster_id"
            required
            class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none cursor-pointer"
          >
            <option value="" disabled>Selecione a posição regional...</option>
            <option v-for="c in clustersList" :key="c.id" :value="c.id">
              {{ c.code }} — {{ c.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Tipo de Depósito *
          </label>
          <select
            v-model="form.type"
            required
            class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none cursor-pointer"
          >
            <option v-for="t in availableDepotTypes" :key="t.code" :value="t.code">
              {{ t.name }}
            </option>
          </select>
        </div>
      </div>

      <!-- Linha 3: Proprietário / Solicitante (Vínculo de Operação) -->
      <div>
        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
          Proprietário / Solicitante do Depósito (Opcional)
        </label>
        <select
          v-model="form.owner_id"
          class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none cursor-pointer"
        >
          <option value="">Herdar da Região / Cluster selecionado ({{ currentClusterOwnerHint }})</option>
          <option v-for="o in availableOwners" :key="o.id" :value="o.id">
            {{ o.code }} — {{ o.name }}
          </option>
        </select>
        <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 block">
          Define para qual proprietário este depósito opera. Movimentações neste depósito utilizarão os códigos e catálogo deste proprietário.
        </span>
      </div>

      <!-- Linha 4: Responsável e Município -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <PersonSearchSelect
            v-model="form.responsible_person_id"
            :initial-person-name="selectedPersonInfo.name"
            label="Responsável pelo Depósito"
            placeholder="Digite o nome ou CPF da pessoa..."
          />
        </div>

        <div>
          <CitySearchSelect
            v-model="form.city_id"
            :initial-city-name="selectedCityInfo.name"
            :initial-state-code="selectedCityInfo.state_code"
            :initial-ibge-code="selectedCityInfo.ibge_code"
            label="Município"
            placeholder="Digite 3 letras do município..."
          />
        </div>
      </div>

      <!-- Linha 4: Descrição -->
      <div>
        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
          Descrição / Detalhes de Acesso
        </label>
        <textarea
          v-model="form.description"
          rows="2"
          placeholder="Ex: Galpão operacional principal com doca de recebimento e expedição..."
          class="w-full p-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none resize-none"
        ></textarea>
      </div>

      <!-- Linha 5: Ativo / Inativo -->
      <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 flex items-center justify-between">
        <div>
          <span class="font-bold text-slate-800 dark:text-slate-200 block text-xs">Depósito Ativo</span>
          <span class="text-[11px] text-slate-500 dark:text-slate-400">Depósitos ativos estão disponíveis para recebimento, estocagem e transferências.</span>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
          <input
            type="checkbox"
            v-model="form.is_active"
            class="sr-only peer"
          />
          <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-[#FC6714]"></div>
        </label>
      </div>
    </form>

    <!-- Footer Modal -->
    <template #footer>
      <div class="w-full flex items-center justify-between gap-2.5">
        <div>
          <button
            v-if="isEditing"
            type="button"
            :disabled="submitting || deleting"
            @click="deleteDepot"
            class="h-9 px-3 text-xs font-semibold rounded-lg border border-rose-200 dark:border-rose-900/60 bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/50 transition cursor-pointer flex items-center gap-1.5 disabled:opacity-50"
          >
            <span v-if="deleting" class="inline-block animate-spin mr-1">⟳</span>
            <Trash2 class="w-3.5 h-3.5" v-if="!deleting" />
            <span>{{ deleting ? 'Excluindo...' : 'Excluir Depósito' }}</span>
          </button>
        </div>

        <div class="flex items-center gap-2.5">
          <button
            type="button"
            @click="$emit('close')"
            class="h-9 px-4 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition cursor-pointer"
          >
            Cancelar
          </button>

          <button
            type="submit"
            form="depotForm"
            :disabled="submitting || deleting"
            class="h-9 px-4 text-xs font-semibold rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white shadow-sm transition cursor-pointer flex items-center gap-1.5 disabled:opacity-50"
          >
            <span v-if="submitting" class="inline-block animate-spin mr-1">⟳</span>
            <component :is="isEditing ? Edit2 : Plus" class="w-3.5 h-3.5" v-if="!submitting" />
            <span>{{ submitting ? 'Salvando...' : (isEditing ? 'Atualizar Depósito' : 'Salvar Depósito') }}</span>
          </button>
        </div>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import { Plus, Edit2, AlertCircle, Trash2 } from 'lucide-vue-next';
import BaseModal from '@/components/common/BaseModal.vue';
import CitySearchSelect from '@/components/common/CitySearchSelect.vue';
import PersonSearchSelect from '@/components/common/PersonSearchSelect.vue';

const props = defineProps<{
  isOpen: boolean;
  depotData?: any;
  clustersList: any[];
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'saved'): void;
}>();

const isEditing = computed(() => Boolean(props.depotData && props.depotData.id));
const submitting = ref(false);
const errorMessage = ref('');

const selectedPersonInfo = ref({
  name: '',
});

const selectedCityInfo = ref({
  name: '',
  state_code: '',
  ibge_code: '',
});

const availableDepotTypes = ref<any[]>([
  { code: 'REGIONAL_BASE', name: 'Base Regional' },
  { code: 'CENTRAL', name: 'Almoxarifado Central' },
  { code: 'LAB_REPAIR', name: 'Laboratório de Reparo' },
]);

const availableOwners = ref<any[]>([]);

const form = ref({
  name: '',
  code: '',
  cluster_id: '' as string | number,
  owner_id: '' as string | number,
  type: 'REGIONAL_BASE',
  responsible_person_id: null as number | null,
  city_id: null as number | null,
  description: '',
  is_active: true,
});

const currentClusterOwnerHint = computed(() => {
  const selectedCluster = props.clustersList.find(c => c.id === Number(form.value.cluster_id));
  if (selectedCluster?.owner) {
    return `${selectedCluster.owner.code} - ${selectedCluster.owner.name}`;
  }
  return 'Próprio / Sem proprietário específico';
});

async function loadDepotTypes() {
  try {
    const res = await fetch('/api/v1/depot-types?active_only=true');
    const json = await res.json();
    if (json.data && json.data.length > 0) {
      availableDepotTypes.value = json.data;
    }
  } catch (err) {
    console.error('Erro ao carregar tipos de depósito:', err);
  }
}

async function loadOwners() {
  try {
    const res = await fetch('/api/v1/material-owners?active_only=true');
    const json = await res.json();
    if (json.data) {
      availableOwners.value = json.data;
    }
  } catch (err) {
    console.error('Erro ao carregar proprietários:', err);
  }
}

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      loadDepotTypes();
      loadOwners();

      if (props.depotData) {
        selectedPersonInfo.value = {
          name: props.depotData.responsible_person?.name || '',
        };

        selectedCityInfo.value = {
          name: props.depotData.city?.name || '',
          state_code: props.depotData.city?.state?.code || props.depotData.city?.state_code || '',
          ibge_code: props.depotData.city?.ibge_code || '',
        };

        form.value = {
          name: props.depotData.name || '',
          code: props.depotData.code || '',
          cluster_id: props.depotData.cluster_id || (props.depotData.cluster?.id ?? (props.clustersList[0]?.id || '')),
          owner_id: props.depotData.owner_id ? String(props.depotData.owner_id) : '',
          type: props.depotData.type || (availableDepotTypes.value[0]?.code || 'REGIONAL_BASE'),
          responsible_person_id: props.depotData.responsible_person_id || (props.depotData.responsible_person?.id ?? null),
          city_id: props.depotData.city_id || (props.depotData.city?.id ?? null),
          description: props.depotData.description || '',
          is_active: props.depotData.is_active !== undefined ? Boolean(props.depotData.is_active) : true,
        };
      } else {
        selectedPersonInfo.value = {
          name: '',
        };

        selectedCityInfo.value = {
          name: '',
          state_code: '',
          ibge_code: '',
        };

        form.value = {
          name: '',
          code: '',
          cluster_id: props.clustersList[0]?.id || '',
          owner_id: '',
          type: availableDepotTypes.value[0]?.code || 'REGIONAL_BASE',
          responsible_person_id: null,
          city_id: null,
          description: '',
          is_active: true,
        };
      }
      errorMessage.value = '';
    }
  },
  { immediate: true }
);

async function submitForm() {
  if (!form.value.name.trim() || !form.value.code.trim()) {
    errorMessage.value = 'Nome e Código do depósito são obrigatórios.';
    return;
  }

  if (!form.value.cluster_id) {
    errorMessage.value = 'Selecione uma Posição Regional para o depósito.';
    return;
  }

  submitting.value = true;
  errorMessage.value = '';

  const payload: any = {
    name: form.value.name.trim(),
    code: form.value.code.trim().toUpperCase(),
    cluster_id: Number(form.value.cluster_id),
    owner_id: form.value.owner_id ? Number(form.value.owner_id) : null,
    type: form.value.type,
    responsible_person_id: form.value.responsible_person_id ? Number(form.value.responsible_person_id) : null,
    city_id: form.value.city_id ? Number(form.value.city_id) : null,
    description: form.value.description ? form.value.description.trim() : null,
    is_active: Boolean(form.value.is_active),
  };

  try {
    const url = isEditing.value ? `/api/v1/depots/${props.depotData.id}` : '/api/v1/depots';
    const method = isEditing.value ? 'PUT' : 'POST';

    const res = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload),
    });

    const json = await res.json();
    if (!res.ok) {
      errorMessage.value = json.message || 'Erro ao salvar depósito.';
      return;
    }

    emit('saved');
    emit('close');
  } catch (err: any) {
    errorMessage.value = err.message || 'Erro de comunicação ao salvar depósito.';
  } finally {
    submitting.value = false;
  }
}

const deleting = ref(false);

async function deleteDepot() {
  if (!props.depotData?.id) return;
  if (!confirm(`Deseja realmente excluir o depósito "${form.value.name}"?`)) return;

  deleting.value = true;
  errorMessage.value = '';

  try {
    const res = await fetch(`/api/v1/depots/${props.depotData.id}`, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
      },
    });

    const json = await res.json();
    if (!res.ok) {
      errorMessage.value = json.message || 'Erro ao excluir depósito.';
      return;
    }

    emit('saved');
    emit('close');
  } catch (err: any) {
    errorMessage.value = err.message || 'Erro ao comunicar com o servidor.';
  } finally {
    deleting.value = false;
  }
}
</script>
