<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="$emit('close')"
    :title="modalTitle"
    :description="modalDescription"
    size="lg"
  >
    <!-- Header Badge -->
    <template #header-badge>
      <span
        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border"
        :class="badgeColorClass"
      >
        <component :is="typeIcon" class="w-3.5 h-3.5" />
        <span>{{ typeLabel }}</span>
      </span>
    </template>

    <form id="movementForm" @submit.prevent="submitMovement" class="space-y-4 text-xs">
      <!-- Mensagem de Erro -->
      <div
        v-if="errorMessage"
        class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 font-medium flex items-center gap-2"
      >
        <AlertCircle class="w-4 h-4 shrink-0 text-rose-600 dark:text-rose-400" />
        <span>{{ errorMessage }}</span>
      </div>

      <!-- SELETOR DE TIPO DE MOVIMENTAÇÃO (4 TIPOS OBRIGATÓRIOS) -->
      <div>
        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
          Tipo de Movimentação *
        </label>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
          <!-- Entrada -->
          <button
            type="button"
            @click="setMovementType('ENTRY')"
            class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg border text-xs font-semibold transition cursor-pointer"
            :class="form.movement_type === 'ENTRY'
              ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs'
              : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60'"
          >
            <ArrowDownToLine class="w-4 h-4" />
            <span>Entrada</span>
          </button>

          <!-- Saída -->
          <button
            type="button"
            @click="setMovementType('EXIT')"
            class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg border text-xs font-semibold transition cursor-pointer"
            :class="form.movement_type === 'EXIT'
              ? 'bg-rose-600 text-white border-rose-600 shadow-xs'
              : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60'"
          >
            <ArrowUpFromLine class="w-4 h-4" />
            <span>Saída</span>
          </button>

          <!-- Devolução -->
          <button
            type="button"
            @click="setMovementType('RETURN')"
            class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg border text-xs font-semibold transition cursor-pointer"
            :class="form.movement_type === 'RETURN'
              ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs'
              : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60'"
          >
            <RotateCcw class="w-4 h-4" />
            <span>Devolução</span>
          </button>

          <!-- Transferência -->
          <button
            type="button"
            @click="setMovementType('TRANSFER')"
            class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg border text-xs font-semibold transition cursor-pointer"
            :class="form.movement_type === 'TRANSFER'
              ? 'bg-[#FC6714] text-white border-[#FC6714] shadow-xs'
              : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60'"
          >
            <ArrowRightLeft class="w-4 h-4" />
            <span>Transferência</span>
          </button>
        </div>

        <!-- Banner explicativo dinâmico -->
        <p class="mt-2 text-[11px] font-medium" :class="typeDescriptionColor">
          {{ typeDescriptionText }}
        </p>
      </div>

      <!-- LINHA: ORIGEM E DESTINO ADAPTATIVOS -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- ORIGEM -->
        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Depósito de Origem {{ isSourceRequired ? '*' : '(Opcional / Externo)' }}
          </label>

          <template v-if="form.movement_type === 'ENTRY'">
            <div class="h-10 px-3 rounded-lg border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 text-slate-400 dark:text-slate-500 text-xs flex items-center select-none">
              Fornecedor / Compra / Inventário Inicial
            </div>
          </template>

          <template v-else-if="form.movement_type === 'RETURN'">
            <select
              v-model="form.source_depot_id"
              class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none cursor-pointer"
            >
              <option value="">Origem Externa (Cliente / Técnico de Campo)</option>
              <option v-for="d in depots" :key="d.id" :value="String(d.id)">
                {{ d.name }} ({{ formatDepotType(d.type) }})
              </option>
            </select>
          </template>

          <template v-else>
            <select
              v-model="form.source_depot_id"
              :required="isSourceRequired"
              @change="onSourceChange"
              class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none cursor-pointer"
            >
              <option value="" disabled>Selecione o local de saída...</option>
              <option v-for="d in depots" :key="d.id" :value="String(d.id)">
                {{ d.name }} ({{ formatDepotType(d.type) }})
              </option>
            </select>
          </template>
        </div>

        <!-- DESTINO -->
        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Depósito de Destino {{ isDestRequired ? '*' : '(Opcional / Externo)' }}
          </label>

          <template v-if="form.movement_type === 'EXIT'">
            <div class="h-10 px-3 rounded-lg border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 text-slate-400 dark:text-slate-500 text-xs flex items-center select-none">
              Aplicação em Campo / Técnico / Cliente
            </div>
          </template>

          <template v-else>
            <select
              v-model="form.destination_depot_id"
              :required="isDestRequired"
              class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none cursor-pointer"
            >
              <option value="" disabled>Selecione o local de entrada...</option>
              <option v-for="d in destinationOptions" :key="d.id" :value="String(d.id)">
                {{ d.name }} ({{ formatDepotType(d.type) }})
              </option>
            </select>
          </template>
        </div>
      </div>

      <!-- LINHA: MATERIAL -->
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
          <option v-for="m in materials" :key="m.id" :value="String(m.id)">
            {{ m.code }} — {{ m.name }} ({{ m.has_serial ? 'Serializado' : 'Convencional' }})
          </option>
        </select>
      </div>

      <!-- SEÇÃO ESPECIAL DE SERIAIS (SE MATERIAL SERIALIZADO) -->
      <div
        v-if="selectedMaterial?.has_serial"
        class="p-4 rounded-xl border border-purple-200 dark:border-purple-800/60 bg-purple-50/50 dark:bg-purple-950/20 space-y-3"
      >
        <!-- Para TRANSFER ou EXIT: Selecionar seriais disponíveis no depósito de origem -->
        <template v-if="form.movement_type === 'TRANSFER' || form.movement_type === 'EXIT'">
          <div class="flex items-center justify-between">
            <label class="font-bold text-purple-900 dark:text-purple-300 uppercase tracking-wider flex items-center gap-1.5">
              <QrCode class="w-4 h-4 text-purple-600" />
              <span>Selecione os Seriais em Estoque na Origem:</span>
            </label>
            <span class="font-semibold text-purple-700 dark:text-purple-300">
              {{ form.serial_ids.length }} de {{ availableSerials.length }} selecionado(s)
            </span>
          </div>

          <div v-if="loadingSerials" class="py-4 text-center text-purple-600 dark:text-purple-400">
            <span class="inline-block animate-spin mr-1.5">⟳</span> Localizando seriais no depósito...
          </div>
          <div v-else-if="availableSerials.length === 0" class="py-4 text-center text-slate-500 dark:text-slate-400 italic">
            Nenhum serial com status 'Em Estoque' no depósito selecionado.
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
                @change="onSerialCheckboxChange"
                class="w-4 h-4 rounded text-[#FC6714] focus:ring-[#FC6714]"
              />
              <div class="flex items-center gap-2">
                <span class="font-mono font-bold text-slate-900 dark:text-slate-100">{{ s.serial_number }}</span>
                <span v-if="s.mac_address" class="font-mono text-slate-400 text-[11px]">({{ s.mac_address }})</span>
              </div>
            </label>
          </div>
        </template>

        <!-- Para ENTRY: Digitar ou colar novos seriais -->
        <template v-else-if="form.movement_type === 'ENTRY'">
          <div class="flex items-center justify-between">
            <label class="font-bold text-purple-900 dark:text-purple-300 uppercase tracking-wider flex items-center gap-1.5">
              <QrCode class="w-4 h-4 text-purple-600" />
              <span>Inserir Números de Série para Entrada:</span>
            </label>
            <span class="font-semibold text-purple-700 dark:text-purple-300">
              {{ parsedSerialsCount }} serial(is) identificado(s)
            </span>
          </div>
          <p class="text-[11px] text-purple-700 dark:text-purple-300">
            Digite ou use o leitor de código de barras. Insira um número de série por linha ou separados por vírgula.
          </p>
          <textarea
            v-model="serialsInputText"
            @input="onSerialsTextInput"
            rows="3"
            placeholder="Exemplo:&#10;ALCLB0928172&#10;ALCLB0928173&#10;ALCLB0928174"
            class="w-full p-2.5 rounded-lg border border-purple-300 dark:border-purple-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 font-mono text-xs focus:ring-2 focus:ring-purple-400 outline-none"
          ></textarea>
        </template>

        <!-- Para RETURN: Selecionar seriais fora de estoque ou digitar seriais devolvidos -->
        <template v-else-if="form.movement_type === 'RETURN'">
          <div class="flex items-center justify-between">
            <label class="font-bold text-purple-900 dark:text-purple-300 uppercase tracking-wider flex items-center gap-1.5">
              <QrCode class="w-4 h-4 text-purple-600" />
              <span>Seriais Devolvidos para Retorno ao Estoque:</span>
            </label>
            <span class="font-semibold text-purple-700 dark:text-purple-300">
              {{ returnSerialsCount }} serial(is)
            </span>
          </div>
          <div v-if="loadingReturnSerials" class="py-2 text-center text-purple-600 text-xs">
            <span class="inline-block animate-spin mr-1">⟳</span> Carregando seriais de clientes/campo...
          </div>
          <div v-else-if="returnableSerials.length > 0" class="max-h-36 overflow-y-auto space-y-1.5 pr-1 divide-y divide-purple-100 dark:divide-purple-900/40">
            <p class="text-[10px] font-bold text-purple-800 dark:text-purple-300 uppercase">Seriais em campo/cliente:</p>
            <label
              v-for="s in returnableSerials"
              :key="s.id"
              class="flex items-center gap-3 p-1.5 rounded hover:bg-purple-100/60 dark:hover:bg-purple-900/30 cursor-pointer select-none"
            >
              <input
                type="checkbox"
                :value="s.id"
                v-model="form.serial_ids"
                @change="onSerialCheckboxChange"
                class="w-4 h-4 rounded text-[#FC6714]"
              />
              <span class="font-mono font-bold text-slate-900 dark:text-slate-100">{{ s.serial_number }}</span>
              <span class="text-[10px] text-slate-400">({{ s.status }})</span>
            </label>
          </div>
          <div>
            <label class="block text-[11px] font-semibold text-purple-900 dark:text-purple-300 mb-1">
              Ou digite/leia os seriais devolvidos (um por linha):
            </label>
            <textarea
              v-model="serialsInputText"
              @input="onSerialsTextInput"
              rows="2"
              placeholder="Ex: SER-DEV-001..."
              class="w-full p-2 rounded-lg border border-purple-300 dark:border-purple-700 bg-white dark:bg-slate-900 font-mono text-xs outline-none"
            ></textarea>
          </div>
        </template>
      </div>

      <!-- LINHA: QUANTIDADE E DOCUMENTO DE REFERÊNCIA -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Quantidade a Movimentar *
          </label>
          <input
            type="number"
            step="0.01"
            min="0.01"
            v-model.number="form.quantity"
            :readonly="selectedMaterial?.has_serial"
            required
            class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 font-mono text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none"
            :class="{ 'opacity-75 bg-slate-100 dark:bg-slate-900 cursor-not-allowed': selectedMaterial?.has_serial }"
          />
          <p v-if="selectedMaterial?.has_serial" class="mt-1 text-[10px] text-slate-500 dark:text-slate-400">
            Sincronizado automaticamente com os números de série selecionados.
          </p>
        </div>

        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Documento de Referência / OS / NF
          </label>
          <input
            type="text"
            v-model="form.document_ref"
            :placeholder="docRefPlaceholder"
            class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none"
          />
        </div>
      </div>

      <!-- LINHA: OBSERVAÇÕES OPERACIONAIS -->
      <div>
        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
          Observações Operacionais
        </label>
        <textarea
          v-model="form.notes"
          rows="2"
          :placeholder="notesPlaceholder"
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
            form="movementForm"
            :disabled="submitting"
            class="h-10 px-5 text-white text-xs font-semibold rounded-lg shadow-sm transition active:scale-98 disabled:opacity-50 cursor-pointer flex items-center gap-2"
            :class="submitButtonColorClass"
          >
            <span v-if="submitting" class="inline-block animate-spin mr-1">⟳</span>
            <span>{{ submitting ? 'Processando...' : submitButtonLabel }}</span>
          </button>
        </div>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import {
  AlertCircle,
  QrCode,
  ArrowDownToLine,
  ArrowUpFromLine,
  RotateCcw,
  ArrowRightLeft,
} from 'lucide-vue-next';
import BaseModal from '@/components/common/BaseModal.vue';

type MovementType = 'ENTRY' | 'EXIT' | 'RETURN' | 'TRANSFER';

const props = defineProps<{
  isOpen: boolean;
  depots: any[];
  materials: any[];
  initialMaterial?: any;
  initialDepot?: any;
  initialType?: MovementType;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'saved', movement?: any): void;
  (e: 'transferred'): void;
}>();

const submitting = ref(false);
const errorMessage = ref('');
const loadingSerials = ref(false);
const loadingReturnSerials = ref(false);
const availableSerials = ref<any[]>([]);
const returnableSerials = ref<any[]>([]);
const selectedMaterial = ref<any>(null);
const serialsInputText = ref('');

const form = ref({
  movement_type: 'TRANSFER' as MovementType,
  source_depot_id: '',
  destination_depot_id: '',
  material_id: '',
  quantity: 1,
  serial_ids: [] as number[],
  document_ref: '',
  notes: '',
});

// Opções dinâmicas de destino (não permite mesmo depósito se for transferência)
const destinationOptions = computed(() => {
  if (form.value.movement_type === 'TRANSFER' && form.value.source_depot_id) {
    return props.depots.filter(d => String(d.id) !== form.value.source_depot_id);
  }
  return props.depots;
});

const isSourceRequired = computed(() => {
  return form.value.movement_type === 'TRANSFER' || form.value.movement_type === 'EXIT';
});

const isDestRequired = computed(() => {
  return form.value.movement_type === 'TRANSFER' || form.value.movement_type === 'ENTRY' || form.value.movement_type === 'RETURN';
});

const typeLabel = computed(() => {
  const map: Record<MovementType, string> = {
    ENTRY: 'Entrada de Estoque',
    EXIT: 'Saída de Estoque',
    RETURN: 'Devolução de Estoque',
    TRANSFER: 'Transferência de Estoque',
  };
  return map[form.value.movement_type];
});

const typeIcon = computed(() => {
  const map: Record<MovementType, any> = {
    ENTRY: ArrowDownToLine,
    EXIT: ArrowUpFromLine,
    RETURN: RotateCcw,
    TRANSFER: ArrowRightLeft,
  };
  return map[form.value.movement_type];
});

const badgeColorClass = computed(() => {
  switch (form.value.movement_type) {
    case 'ENTRY':
      return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800';
    case 'EXIT':
      return 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800';
    case 'RETURN':
      return 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800';
    case 'TRANSFER':
    default:
      return 'bg-orange-50 text-[#FC6714] border-orange-200 dark:bg-orange-950/60 dark:text-orange-300 dark:border-orange-800';
  }
});

const submitButtonColorClass = computed(() => {
  switch (form.value.movement_type) {
    case 'ENTRY':
      return 'bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800';
    case 'EXIT':
      return 'bg-rose-600 hover:bg-rose-700 active:bg-rose-800';
    case 'RETURN':
      return 'bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800';
    case 'TRANSFER':
    default:
      return 'bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605]';
  }
});

const submitButtonLabel = computed(() => {
  const map: Record<MovementType, string> = {
    ENTRY: 'Confirmar Entrada',
    EXIT: 'Confirmar Saída',
    RETURN: 'Confirmar Devolução',
    TRANSFER: 'Confirmar Transferência',
  };
  return map[form.value.movement_type];
});

const modalTitle = computed(() => {
  return `Movimentação de Estoque: ${typeLabel.value}`;
});

const modalDescription = computed(() => {
  return 'Registro atômico e rastreável de entradas, saídas, devoluções e transferências no WMS.';
});

const typeDescriptionText = computed(() => {
  switch (form.value.movement_type) {
    case 'ENTRY':
      return 'Recebimento e entrada física de mercadorias no depósito (compras, fornecedores ou inventário inicial).';
    case 'EXIT':
      return 'Baixa ou retirada de itens do depósito (atendimento a ordens de serviço, entrega a técnicos ou aplicação externa).';
    case 'RETURN':
      return 'Retorno de materiais para o depósito (devolução de clientes, sobras de campo ou recolhimento técnico).';
    case 'TRANSFER':
    default:
      return 'Transferência atômica entre dois depósitos da organização com garantia de rastreabilidade.';
  }
});

const typeDescriptionColor = computed(() => {
  switch (form.value.movement_type) {
    case 'ENTRY': return 'text-emerald-600 dark:text-emerald-400';
    case 'EXIT': return 'text-rose-600 dark:text-rose-400';
    case 'RETURN': return 'text-indigo-600 dark:text-indigo-400';
    case 'TRANSFER': default: return 'text-[#FC6714] dark:text-orange-400';
  }
});

const docRefPlaceholder = computed(() => {
  switch (form.value.movement_type) {
    case 'ENTRY': return 'Ex: NF-e 10492 ou Pedido de Compra';
    case 'EXIT': return 'Ex: OS-9812 ou Requisição de Campo';
    case 'RETURN': return 'Ex: Protocolo de Devolução ou OS-Retirada';
    case 'TRANSFER': default: return 'Ex: REQ-2026-042 ou Guia de Transferência';
  }
});

const notesPlaceholder = computed(() => {
  switch (form.value.movement_type) {
    case 'ENTRY': return 'Descreva dados do fornecedor, lote recebido ou motivo da entrada...';
    case 'EXIT': return 'Descreva o técnico responsável, veículo ou cliente destino...';
    case 'RETURN': return 'Descreva as condições do material devolvido e motivo do retorno...';
    case 'TRANSFER': default: return 'Descreva o motivo da transferência física ou logística interna...';
  }
});

const parsedSerials = computed(() => {
  if (!serialsInputText.value.trim()) return [];
  return serialsInputText.value
    .split(/[\n,;]+/)
    .map(s => s.trim())
    .filter(s => s.length > 0);
});

const parsedSerialsCount = computed(() => parsedSerials.value.length);

const returnSerialsCount = computed(() => {
  return form.value.serial_ids.length + parsedSerials.value.length;
});

function setMovementType(type: MovementType) {
  form.value.movement_type = type;
  errorMessage.value = '';
  if (type === 'ENTRY') {
    form.value.source_depot_id = '';
    if (!form.value.destination_depot_id && props.depots.length > 0) {
      form.value.destination_depot_id = String(props.depots[0].id);
    }
  } else if (type === 'EXIT') {
    form.value.destination_depot_id = '';
    if (!form.value.source_depot_id && props.depots.length > 0) {
      form.value.source_depot_id = String(props.depots[0].id);
    }
  }
  handleSerialsOnTypeChange();
}

function handleSerialsOnTypeChange() {
  if (form.value.movement_type === 'TRANSFER' || form.value.movement_type === 'EXIT') {
    loadSerials();
  } else if (form.value.movement_type === 'RETURN') {
    loadReturnableSerials();
  }
}

function formatDepotType(type: string): string {
  const map: Record<string, string> = {
    CENTRAL: 'Almoxarifado Central',
    REGIONAL_BASE: 'Base Regional',
    LAB_REPAIR: 'Laboratório de Reparo',
  };
  return map[type] || 'Depósito';
}

function onSerialCheckboxChange() {
  if (selectedMaterial.value?.has_serial) {
    if (form.value.movement_type === 'RETURN') {
      form.value.quantity = Math.max(1, form.value.serial_ids.length + parsedSerials.value.length);
    } else {
      form.value.quantity = Math.max(1, form.value.serial_ids.length);
    }
  }
}

function onSerialsTextInput() {
  if (selectedMaterial.value?.has_serial) {
    if (form.value.movement_type === 'ENTRY') {
      form.value.quantity = Math.max(1, parsedSerials.value.length);
    } else if (form.value.movement_type === 'RETURN') {
      form.value.quantity = Math.max(1, form.value.serial_ids.length + parsedSerials.value.length);
    }
  }
}

function onSourceChange() {
  if (form.value.movement_type === 'TRANSFER' || form.value.movement_type === 'EXIT') {
    loadSerials();
  }
}

function onMaterialChange() {
  const matId = Number(form.value.material_id);
  selectedMaterial.value = props.materials.find(m => m.id === matId) || null;
  form.value.serial_ids = [];
  serialsInputText.value = '';
  if (selectedMaterial.value?.has_serial) {
    form.value.quantity = 1;
  }
  handleSerialsOnTypeChange();
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
    const res = await fetch(`/api/v1/stock/serials?depot_id=${depotId}&material_id=${matId}&status=IN_STOCK&per_page=500`);
    const json = await res.json();
    availableSerials.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar seriais:', err);
  } finally {
    loadingSerials.value = false;
  }
}

async function loadReturnableSerials() {
  if (!selectedMaterial.value?.has_serial) {
    returnableSerials.value = [];
    return;
  }
  const matId = form.value.material_id;
  if (!matId) return;

  loadingReturnSerials.value = true;
  try {
    const res = await fetch(`/api/v1/stock/serials?material_id=${matId}&per_page=100`);
    const json = await res.json();
    const all = json.data || [];
    // Seriais que não estão no status IN_STOCK ou que podem ser devolvidos
    returnableSerials.value = all.filter((s: any) => s.status !== 'IN_STOCK');
  } catch (err) {
    console.error('Erro ao carregar seriais para devolução:', err);
  } finally {
    loadingReturnSerials.value = false;
  }
}

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      errorMessage.value = '';
      const initialType = props.initialType || 'TRANSFER';

      const defaultSource = props.initialDepot?.id
        ? String(props.initialDepot.id)
        : (props.depots[0]?.id ? String(props.depots[0].id) : '');

      const otherDepot = props.depots.find(d => String(d.id) !== defaultSource);
      const defaultDest = otherDepot?.id ? String(otherDepot.id) : (props.depots[1]?.id ? String(props.depots[1].id) : '');

      const defaultMat = props.initialMaterial?.material_id
        ? String(props.initialMaterial.material_id)
        : (props.initialMaterial?.id ? String(props.initialMaterial.id) : (props.materials[0]?.id ? String(props.materials[0].id) : ''));

      form.value = {
        movement_type: initialType,
        source_depot_id: initialType === 'ENTRY' ? '' : defaultSource,
        destination_depot_id: initialType === 'EXIT' ? '' : defaultDest,
        material_id: defaultMat,
        quantity: 1,
        serial_ids: [],
        document_ref: '',
        notes: '',
      };

      serialsInputText.value = '';
      onMaterialChange();
    }
  }
);

async function submitMovement() {
  errorMessage.value = '';

  // Validações locais amigáveis
  if (form.value.movement_type === 'TRANSFER') {
    if (!form.value.source_depot_id) {
      errorMessage.value = 'Selecione o depósito de origem.';
      return;
    }
    if (!form.value.destination_depot_id) {
      errorMessage.value = 'Selecione o depósito de destino.';
      return;
    }
    if (form.value.source_depot_id === form.value.destination_depot_id) {
      errorMessage.value = 'O depósito de destino deve ser diferente do depósito de origem.';
      return;
    }
  } else if (form.value.movement_type === 'EXIT') {
    if (!form.value.source_depot_id) {
      errorMessage.value = 'Selecione o depósito de onde o material sairá.';
      return;
    }
  } else if (form.value.movement_type === 'ENTRY' || form.value.movement_type === 'RETURN') {
    if (!form.value.destination_depot_id) {
      errorMessage.value = 'Selecione o depósito de destino para entrada do material.';
      return;
    }
  }

  if (!form.value.material_id) {
    errorMessage.value = 'Selecione o material a movimentar.';
    return;
  }

  // Validações específicas para serializados
  if (selectedMaterial.value?.has_serial) {
    if (form.value.movement_type === 'TRANSFER' || form.value.movement_type === 'EXIT') {
      if (form.value.serial_ids.length === 0) {
        errorMessage.value = 'Selecione ao menos um número de série em estoque para movimentar.';
        return;
      }
      form.value.quantity = form.value.serial_ids.length;
    } else if (form.value.movement_type === 'ENTRY') {
      if (parsedSerials.value.length === 0) {
        errorMessage.value = 'Informe ao menos um número de série para a entrada do item serializado.';
        return;
      }
      form.value.quantity = parsedSerials.value.length;
    } else if (form.value.movement_type === 'RETURN') {
      if (form.value.serial_ids.length === 0 && parsedSerials.value.length === 0) {
        errorMessage.value = 'Selecione ou informe ao menos um número de série devolvido.';
        return;
      }
      form.value.quantity = form.value.serial_ids.length + parsedSerials.value.length;
    }
  }

  if (Number(form.value.quantity) <= 0) {
    errorMessage.value = 'A quantidade a movimentar deve ser maior que zero.';
    return;
  }

  submitting.value = true;
  try {
    const payload: any = {
      movement_type: form.value.movement_type,
      source_depot_id: form.value.source_depot_id ? Number(form.value.source_depot_id) : null,
      destination_depot_id: form.value.destination_depot_id ? Number(form.value.destination_depot_id) : null,
      material_id: Number(form.value.material_id),
      quantity: Number(form.value.quantity),
      document_ref: form.value.document_ref || null,
      notes: form.value.notes || null,
    };

    if (form.value.serial_ids.length > 0) {
      payload.serial_ids = form.value.serial_ids;
    }

    if (parsedSerials.value.length > 0) {
      payload.serials = parsedSerials.value;
    }

    const res = await fetch('/api/v1/stock/movement', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload),
    });

    const json = await res.json();
    if (!res.ok) {
      errorMessage.value = json.message || 'Erro ao processar movimentação de estoque.';
      return;
    }

    emit('saved', json.data);
    emit('transferred'); // retrocompatibilidade com componentes ouvindo @transferred
    emit('close');
  } catch (err: any) {
    errorMessage.value = err.message || 'Erro de conexão ao processar movimentação.';
  } finally {
    submitting.value = false;
  }
}
</script>
