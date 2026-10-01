<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="val => !val && $emit('close')"
    size="xl"
  >
    <template #header>
      <div class="flex items-center justify-between w-full pr-6">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-950/60 border border-orange-200 dark:border-orange-800 text-[#FC6714] flex items-center justify-center shrink-0">
            <ArrowLeftRight class="w-5 h-5" />
          </div>
          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h3 class="text-lg font-heading font-semibold text-[#172554] dark:text-slate-100">
                Detalhes da Movimentação
              </h3>
              <span
                class="px-2.5 py-0.5 rounded-full text-xs font-bold border"
                :class="getTypeBadgeClasses(movement?.movement_type)"
              >
                {{ formatTypeLabel(movement?.movement_type) }}
              </span>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
              <span>Protocolo:</span>
              <strong class="font-mono text-slate-800 dark:text-slate-200">{{ movement?.protocol || '-' }}</strong>
              <button
                v-if="movement?.protocol"
                type="button"
                @click="copyToClipboard(movement.protocol, 'protocol')"
                class="hover:text-[#FC6714] transition cursor-pointer p-0.5"
                title="Copiar Protocolo"
              >
                <Check v-if="copiedKey === 'protocol'" class="w-3.5 h-3.5 text-emerald-500" />
                <Copy v-else class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>
        </div>

        <!-- Atalho para Reconf Externo -->
        <a
          v-if="movement?.protocol"
          :href="`/u/reconf/${movement.protocol}`"
          target="_blank"
          class="hidden sm:inline-flex items-center gap-1.5 h-9 px-3 rounded-lg border border-orange-200 dark:border-orange-800/80 bg-orange-50/50 dark:bg-orange-950/40 text-[#FC6714] hover:bg-orange-100/70 text-xs font-semibold transition"
          title="Abrir página pública de conferência (Reconf)"
        >
          <FileText class="w-3.5 h-3.5" />
          <span>Ver Reconf</span>
          <ExternalLink class="w-3.5 h-3.5" />
        </a>
      </div>
    </template>

    <div v-if="movement" class="space-y-6">
      <!-- 1. BLOCO DE RESUMO / CABEÇALHO DA OPERAÇÃO -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800">
        <div>
          <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Documento / Ref</div>
          <div class="font-semibold text-slate-800 dark:text-slate-200 text-sm mt-0.5 font-mono truncate">
            {{ movement.document_number || movement.document_ref || '-' }}
          </div>
        </div>

        <div>
          <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Data Documento</div>
          <div class="font-semibold text-slate-800 dark:text-slate-200 text-sm mt-0.5">
            {{ formatDate(movement.document_date) }}
          </div>
        </div>

        <div>
          <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Data Movimentação</div>
          <div class="font-semibold text-slate-800 dark:text-slate-200 text-sm mt-0.5">
            {{ formatDate(movement.movement_date || movement.created_at) }}
          </div>
        </div>

        <div>
          <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Registro Sistema</div>
          <div class="font-semibold text-slate-800 dark:text-slate-200 text-sm mt-0.5">
            {{ formatDateTime(movement.created_at) }}
          </div>
        </div>
      </div>

      <!-- 2. FLUXO: ORIGEM -> DESTINO -->
      <div class="p-4 rounded-xl bg-white dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800 shadow-2xs">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
          <Warehouse class="w-4 h-4 text-[#FC6714]" />
          <span>Origem & Destino do Material</span>
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Origem -->
          <div class="p-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40">
            <span class="text-[10px] font-bold uppercase text-slate-400">Depósito de Origem</span>
            <div class="font-bold text-slate-800 dark:text-slate-200 text-sm mt-0.5">
              {{ movement.source_depot?.name || (movement.movement_type === 'ENTRY' ? 'Entrada Externa / Fornecedor' : '-') }}
            </div>
            <div v-if="movement.source_depot?.cluster" class="text-xs text-slate-500 mt-0.5">
              Regional: {{ movement.source_depot.cluster.name }}
            </div>
          </div>

          <!-- Destino -->
          <div class="p-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40">
            <span class="text-[10px] font-bold uppercase text-slate-400">Depósito de Destino</span>
            <div class="font-bold text-slate-800 dark:text-slate-200 text-sm mt-0.5">
              {{ movement.destination_depot?.name || (movement.movement_type === 'EXIT' ? 'Saída Externa / Consumo' : '-') }}
            </div>
            <div v-if="movement.destination_depot?.cluster" class="text-xs text-slate-500 mt-0.5">
              Regional: {{ movement.destination_depot.cluster.name }}
            </div>
          </div>
        </div>
      </div>

      <!-- 3. MATERIAL & QUANTIDADE -->
      <div class="p-4 rounded-xl bg-white dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800 shadow-2xs">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
          <Boxes class="w-4 h-4 text-[#FC6714]" />
          <span>Material Movimentado</span>
        </h4>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-3.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800">
          <div>
            <div class="font-mono text-xs font-bold text-[#FC6714] dark:text-orange-400">
              SKU: {{ movement.material?.code || '-' }}
            </div>
            <div class="font-bold text-slate-900 dark:text-white text-base mt-0.5">
              {{ movement.material?.name || 'Material' }}
            </div>
            <div class="text-xs text-slate-500 mt-0.5">
              Categoria: {{ movement.material?.category || 'Geral' }}
            </div>
          </div>

          <div class="text-right sm:border-l sm:pl-6 border-slate-200 dark:border-slate-800">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Quantidade</span>
            <div class="text-2xl font-bold font-heading text-slate-900 dark:text-white">
              {{ formatQuantity(movement.quantity) }}
              <span class="text-xs text-slate-500 font-sans font-normal ml-1">{{ movement.material?.unit?.code || 'UND' }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 4. RASTREABILIDADE SERIAL (SE HOUVER) -->
      <div v-if="movement.serials && movement.serials.length > 0" class="p-4 rounded-xl bg-white dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800 shadow-2xs">
        <div class="flex items-center justify-between mb-3">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
            <QrCode class="w-4 h-4 text-[#FC6714]" />
            <span>Equipamentos Serializados ({{ movement.serials.length }})</span>
          </h4>
          <button
            type="button"
            @click="copyAllSerials"
            class="text-xs font-medium text-[#FC6714] hover:underline flex items-center gap-1 cursor-pointer"
          >
            <Copy class="w-3.5 h-3.5" />
            <span>Copiar todos</span>
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 max-h-48 overflow-y-auto p-1">
          <div
            v-for="(s, sIdx) in movement.serials"
            :key="s.id || sIdx"
            class="p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/60 flex items-center justify-between text-xs"
          >
            <div class="truncate">
              <div class="font-mono font-bold text-slate-800 dark:text-slate-200 truncate">
                SN: {{ s.serial_number }}
              </div>
              <div v-if="s.mac_address" class="font-mono text-[10px] text-slate-400 truncate">
                MAC: {{ s.mac_address }}
              </div>
            </div>
            <button
              type="button"
              @click="copyToClipboard(s.serial_number, `sn-${sIdx}`)"
              class="text-slate-400 hover:text-[#FC6714] p-1 rounded transition cursor-pointer"
              title="Copiar serial"
            >
              <Check v-if="copiedKey === `sn-${sIdx}`" class="w-3.5 h-3.5 text-emerald-500" />
              <Copy v-else class="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>

      <!-- 5. TRANSPORTE & AUDITORIA -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-xs">
        <div>
          <span class="font-bold text-slate-400 uppercase tracking-wider block text-[10px]">Motorista</span>
          <span class="font-semibold text-slate-800 dark:text-slate-200 mt-1 block">
            {{ movement.driver?.name || '-' }}
          </span>
        </div>
        <div>
          <span class="font-bold text-slate-400 uppercase tracking-wider block text-[10px]">Recebedor</span>
          <span class="font-semibold text-slate-800 dark:text-slate-200 mt-1 block">
            {{ movement.receiver?.name || '-' }}
          </span>
        </div>
        <div>
          <span class="font-bold text-slate-400 uppercase tracking-wider block text-[10px]">Operador do Sistema</span>
          <span class="font-semibold text-slate-800 dark:text-slate-200 mt-1 block">
            {{ movement.user?.name || '-' }}
          </span>
        </div>
      </div>

      <!-- 6. OBSERVAÇÕES (SE HOUVER) -->
      <div v-if="movement.notes" class="p-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/40 text-xs">
        <span class="font-bold text-slate-400 uppercase tracking-wider block text-[10px] mb-1">Observações</span>
        <p class="text-slate-700 dark:text-slate-300 font-mono whitespace-pre-wrap">{{ movement.notes }}</p>
      </div>

      <!-- 7. ANEXOS / COMPROVANTES (SE HOUVER) -->
      <div v-if="movement.attachments && movement.attachments.length > 0" class="p-4 rounded-xl bg-white dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800 shadow-2xs">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
          <FileText class="w-4 h-4 text-[#FC6714]" />
          <span>Comprovantes & Anexos ({{ movement.attachments.length }})</span>
        </h4>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <a
            v-for="(att, aIdx) in movement.attachments"
            :key="att.id || aIdx"
            :href="att.file_path ? `/storage/${att.file_path}` : '#'"
            target="_blank"
            class="p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 hover:border-orange-300 dark:hover:border-orange-600 transition flex flex-col items-center text-center group"
          >
            <div class="w-10 h-10 rounded-md bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 group-hover:text-[#FC6714] mb-1.5">
              <FileText class="w-5 h-5" />
            </div>
            <span class="text-xs font-medium text-slate-800 dark:text-slate-200 truncate w-full" :title="att.file_name">
              {{ att.file_name || 'Arquivo' }}
            </span>
            <span class="text-[10px] text-slate-400 mt-0.5">Abrir anexo ↗</span>
          </a>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex items-center justify-between w-full">
        <a
          v-if="movement?.protocol"
          :href="`/u/reconf/${movement.protocol}`"
          target="_blank"
          class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#FC6714] hover:underline"
        >
          <ExternalLink class="w-3.5 h-3.5" />
          <span>Abrir página pública de conferência (Reconf)</span>
        </a>
        <div v-else></div>

        <button
          type="button"
          @click="$emit('close')"
          class="h-9 px-4 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition cursor-pointer"
        >
          Fechar
        </button>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import {
  ArrowLeftRight, Boxes, Warehouse, QrCode, FileText,
  Copy, Check, ExternalLink
} from 'lucide-vue-next';
import BaseModal from '@/components/common/BaseModal.vue';

const props = defineProps<{
  isOpen: boolean;
  movement: any;
}>();

defineEmits<{
  (e: 'close'): void;
}>();

const copiedKey = ref<string | null>(null);

function copyToClipboard(text: string, key: string) {
  if (!text) return;
  navigator.clipboard.writeText(text);
  copiedKey.value = key;
  setTimeout(() => {
    if (copiedKey.value === key) copiedKey.value = null;
  }, 2000);
}

function copyAllSerials() {
  if (!props.movement?.serials || props.movement.serials.length === 0) return;
  const list = props.movement.serials.map((s: any) => s.serial_number).join('\n');
  copyToClipboard(list, 'all-serials');
}

function formatTypeLabel(type?: string): string {
  switch (type) {
    case 'ENTRY': return 'Entrada';
    case 'EXIT': return 'Saída';
    case 'TRANSFER': return 'Transferência';
    case 'RETURN': return 'Devolução';
    case 'ADJUSTMENT': return 'Ajuste';
    default: return type || 'Movimentação';
  }
}

function getTypeBadgeClasses(type?: string): string {
  switch (type) {
    case 'ENTRY':
      return 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800';
    case 'EXIT':
      return 'bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800';
    case 'TRANSFER':
      return 'bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800';
    case 'RETURN':
      return 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800';
    case 'ADJUSTMENT':
      return 'bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800';
    default:
      return 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700';
  }
}

function formatQuantity(val?: number | string): string {
  if (val === null || val === undefined) return '0';
  const num = typeof val === 'string' ? parseFloat(val) : val;
  return num.toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

function formatDate(dateStr?: string): string {
  if (!dateStr) return '-';
  const clean = dateStr.split('T')[0];
  const parts = clean.split('-');
  if (parts.length === 3) return `${parts[2]}/${parts[1]}/${parts[0]}`;
  return dateStr;
}

function formatDateTime(dateStr?: string): string {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  if (isNaN(d.getTime())) return dateStr;
  return d.toLocaleString('pt-BR', {
    day: '2-digit', month: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  });
}
</script>
