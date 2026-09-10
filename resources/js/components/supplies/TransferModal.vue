<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="$emit('close')"
    title="Transferência Atômica de Materiais"
    description="Movimentação rastreável entre almoxarifados centrais, bases e depósitos operacionais"
    size="lg"
  >
    <!-- Header Badge -->
    <template #header-badge>
      <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800">
        Rastreabilidade WMS
      </span>
    </template>

    <form id="transferForm" @submit.prevent="submitTransfer" class="space-y-4 text-xs">
      <!-- Mensagem de Erro -->
      <div
        v-if="errorMessage"
        class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 font-medium flex items-center gap-2"
      >
        <AlertCircle class="w-4 h-4 shrink-0 text-rose-600 dark:text-rose-400" />
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Linha 1: Origem e Destino -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Depósito de Origem *
          </label>
          <select
            v-model="form.source_depot_id"
            required
            @change="onSourceChange"
            class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none cursor-pointer"
          >
            <option value="" disabled>Selecione o local de saída...</option>
            <option v-for="d in depots" :key="d.id" :value="d.id">
              {{ d.name }} ({{ formatDepotType(d.type) }})
            </option>
          </select>
        </div>

        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Depósito de Destino *
          </label>
          <select
            v-model="form.destination_depot_id"
            required
            class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none cursor-pointer"
          >
            <option value="" disabled>Selecione o local de entrada...</option>
            <option v-for="d in destinationOptions" :key="d.id" :value="d.id">
              {{ d.name }} ({{ formatDepotType(d.type) }})
            </option>
          </select>
        </div>
      </div>

      <!-- Linha 2: Material -->
      <div>
        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
          Material a Movimentar *
        </label>
        <select
          v-model="form.material_id"
          required
          @change="onMaterialChange"
          class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none cursor-pointer"
        >
          <option value="" disabled>Selecione o item...</option>
          <option v-for="m in materials" :key="m.id" :value="m.id">
            {{ m.code }} — {{ m.name }} ({{ m.has_serial ? 'Serializado' : 'Convencional' }})
          </option>
        </select>
      </div>

      <!-- Caixa Especial de Seleção de Seriais (se serializado) -->
      <div
        v-if="selectedMaterial?.has_serial"
        class="p-4 rounded-xl border border-purple-200 dark:border-purple-800/60 bg-purple-50/50 dark:bg-purple-950/20 space-y-3"
      >
        <div class="flex items-center justify-between">
          <label class="font-bold text-purple-900 dark:text-purple-300 uppercase tracking-wider flex items-center gap-1.5">
            <QrCode class="w-4 h-4 text-purple-600" />
            <span>Selecione os Seriais em Estoque no Depósito de Origem:</span>
          </label>
          <span class="font-semibold text-purple-700 dark:text-purple-300">
            {{ form.serial_ids.length }} de {{ availableSerials.length }} selecionado(s)
          </span>
        </div>

        <div v-if="loadingSerials" class="py-4 text-center text-purple-600 dark:text-purple-400">
          <span class="inline-block animate-spin mr-1.5">⟳</span> Localizando seriais disponíveis no depósito...
        </div>
        <div v-else-if="availableSerials.length === 0" class="py-4 text-center text-slate-500 dark:text-slate-400 italic">
          Nenhum serial disponível com status 'Em Estoque' no depósito selecionado.
        </div>
        <div v-else class="max-h-44 overflow-y-auto space-y-1.5 pr-1 divide-y divide-purple-100 dark:divide-purple-900/40">
          <label
            v-for="s in availableSerials"
            :key="s.id"
            class="flex items-center gap-3 p-2 rounded-lg hover:bg-purple-100/60 dark:hover:bg-purple-900/30 cursor-pointer select-none"
          >
            <input
              type="checkbox"
              :value="s.id"
              v-model="form.serial_ids"
              @change="form.quantity = form.serial_ids.length"
              class="w-4 h-4 rounded text-[#FC6714] focus:ring-[#FC6714]"
            />
            <div class="flex items-center gap-2">
              <span class="font-mono font-bold text-slate-900 dark:text-slate-100">{{ s.serial_number }}</span>
              <span v-if="s.mac_address" class="font-mono text-slate-400 text-[11px]">({{ s.mac_address }})</span>
            </div>
          </label>
        </div>
      </div>

      <!-- Linha 3: Quantidade e Documento de Referência -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Quantidade a Transferir *
          </label>
          <input
            type="number"
            step="0.01"
            min="0.01"
            v-model="form.quantity"
            :readonly="selectedMaterial?.has_serial"
            required
            class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 font-mono text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none"
            :class="{ 'opacity-70 bg-slate-100 dark:bg-slate-900 cursor-not-allowed': selectedMaterial?.has_serial }"
          />
        </div>

        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Documento de Referência / OS / Requisição
          </label>
          <input
            type="text"
            v-model="form.document_ref"
            placeholder="Ex: REQ-2026-042 ou OS-892"
            class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none"
          />
        </div>
      </div>

      <!-- Linha 4: Observações -->
      <div>
        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
          Observações Operacionais
        </label>
        <textarea
          v-model="form.notes"
          rows="2"
          placeholder="Descreva o motivo da transferência física ou destino do material..."
          class="w-full p-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none"
        ></textarea>
      </div>
    </form>

    <template #footer>
      <div class="w-full flex items-center justify-between gap-3">
        <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
          Operação transacional imediata com partidas atômicas
        </span>
        <div class="flex items-center gap-2.5">
          <button
            type="button"
            @click="$emit('close')"
            class="h-10 px-4 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition cursor-pointer"
          >
            Cancelar
          </button>
          <button
            type="submit"
            form="transferForm"
            :disabled="submitting"
            class="h-10 px-5 bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-xs font-semibold rounded-lg shadow-sm transition active:scale-98 disabled:opacity-50 cursor-pointer flex items-center gap-2"
          >
            <span v-if="submitting" class="inline-block animate-spin mr-1">⟳</span>
            <span>{{ submitting ? 'Transferindo...' : 'Confirmar Transferência' }}</span>
          </button>
        </div>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { AlertCircle, QrCode } from 'lucide-vue-next';
import BaseModal from '@/components/common/BaseModal.vue';

const props = defineProps<{
  isOpen: boolean;
  depots: any[];
  materials: any[];
  initialMaterial?: any;
  initialDepot?: any;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'transferred'): void;
}>();

const submitting = ref(false);
const errorMessage = ref('');
const loadingSerials = ref(false);
const availableSerials = ref<any[]>([]);
const selectedMaterial = ref<any>(null);

const form = ref({
  source_depot_id: '',
  destination_depot_id: '',
  material_id: '',
  quantity: 1,
  serial_ids: [] as number[],
  document_ref: '',
  notes: '',
});

const destinationOptions = computed(() => {
  return props.depots.filter(d => d.id !== Number(form.value.source_depot_id));
});

function formatDepotType(type: string): string {
  const map: Record<string, string> = {
    CENTRAL: 'Almoxarifado Central',
    REGIONAL_BASE: 'Base Regional',
    LAB_REPAIR: 'Laboratório de Reparo',
  };
  return map[type] || 'Depósito';
}

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      errorMessage.value = '';
      const defaultSource = props.initialDepot?.id
        ? String(props.initialDepot.id)
        : (props.depots[0]?.id ? String(props.depots[0].id) : '');
      const otherDepot = props.depots.find(d => String(d.id) !== defaultSource);

      const defaultMat = props.initialMaterial?.material_id
        ? String(props.initialMaterial.material_id)
        : (props.initialMaterial?.id ? String(props.initialMaterial.id) : (props.materials[0]?.id ? String(props.materials[0].id) : ''));

      form.value = {
        source_depot_id: defaultSource,
        destination_depot_id: otherDepot?.id ? String(otherDepot.id) : '',
        material_id: defaultMat,
        quantity: 1,
        serial_ids: [],
        document_ref: '',
        notes: '',
      };
      onMaterialChange();
    }
  }
);

function onSourceChange() {
  loadSerials();
}

function onMaterialChange() {
  const matId = Number(form.value.material_id);
  selectedMaterial.value = props.materials.find(m => m.id === matId) || null;
  loadSerials();
}

async function loadSerials() {
  form.value.serial_ids = [];
  if (!selectedMaterial.value?.has_serial) {
    availableSerials.value = [];
    return;
  }
  const depotId = form.value.source_depot_id;
  const matId = form.value.material_id;
  if (!depotId || !matId) return;

  loadingSerials.value = true;
  try {
    const res = await fetch(`/api/v1/stock/serials?depot_id=${depotId}&material_id=${matId}&status=IN_STOCK`);
    const json = await res.json();
    availableSerials.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar seriais:', err);
  } finally {
    loadingSerials.value = false;
  }
}

async function submitTransfer() {
  errorMessage.value = '';

  if (selectedMaterial.value?.has_serial && form.value.serial_ids.length === 0) {
    errorMessage.value = 'Selecione ao menos um número de série (ONU) para transferir.';
    return;
  }

  submitting.value = true;
  try {
    const res = await fetch('/api/v1/stock/transfer', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(form.value),
    });

    const json = await res.json();
    if (!res.ok) {
      errorMessage.value = json.message || 'Erro ao realizar transferência.';
      return;
    }

    emit('transferred');
    emit('close');
  } catch (err: any) {
    errorMessage.value = err.message || 'Erro de conexão ao processar transferência.';
  } finally {
    submitting.value = false;
  }
}
</script>
