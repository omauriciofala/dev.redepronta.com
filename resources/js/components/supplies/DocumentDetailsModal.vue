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
            <FileText class="w-5 h-5" />
          </div>
          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h3 class="text-lg font-heading font-semibold text-[#172554] dark:text-slate-100">
                Documento de Movimentação
              </h3>
              <span
                class="px-2.5 py-0.5 rounded-md text-xs font-bold border"
                :class="getTypeBadgeClasses(documentData?.movement_type)"
              >
                {{ formatTypeLabel(documentData?.movement_type) }}
              </span>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mt-0.5">
              <span>Protocolo:</span>
              <strong class="font-mono text-slate-800 dark:text-slate-200">{{ documentData?.protocol || '-' }}</strong>
              <button
                v-if="documentData?.protocol"
                type="button"
                @click="copyToClipboard(documentData.protocol, 'protocol')"
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
          v-if="documentData?.protocol"
          :href="`/u/reconf/${documentData.protocol}`"
          target="_blank"
          class="hidden sm:inline-flex items-center gap-1.5 h-9 px-3 rounded-lg border border-orange-200 dark:border-orange-800/80 bg-orange-50/50 dark:bg-orange-950/40 text-[#FC6714] hover:bg-orange-100/70 text-xs font-semibold transition cursor-pointer"
          title="Abrir página pública de conferência (Reconf)"
        >
          <FileText class="w-3.5 h-3.5" />
          <span>Ver Reconf</span>
          <ExternalLink class="w-3 h-3" />
        </a>
      </div>
    </template>

    <div v-if="documentData" class="space-y-6">
      <!-- 1. BLOCO DE RESUMO / CABEÇALHO DO DOCUMENTO -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800">
        <div>
          <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Documento / Ref</div>
          <div class="font-semibold text-slate-800 dark:text-slate-200 text-sm mt-0.5 font-mono truncate" :title="documentData.document_number || documentData.document_ref">
            {{ documentData.document_number || documentData.document_ref || '-' }}
          </div>
        </div>

        <div>
          <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Data Documento</div>
          <div class="font-semibold text-slate-800 dark:text-slate-200 text-sm mt-0.5">
            {{ formatDate(documentData.document_date) }}
          </div>
        </div>

        <div>
          <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Data Operação</div>
          <div class="font-semibold text-slate-800 dark:text-slate-200 text-sm mt-0.5">
            {{ formatDate(documentData.movement_date || documentData.created_at) }}
          </div>
        </div>

        <div>
          <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Registro no Sistema</div>
          <div class="font-semibold text-slate-800 dark:text-slate-200 text-sm mt-0.5">
            {{ formatDateTime(documentData.created_at) }}
          </div>
        </div>
      </div>

      <!-- 2. FLUXO: ORIGEM -> DESTINO -->
      <div class="p-4 rounded-xl bg-white dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800 shadow-2xs">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
          <Warehouse class="w-4 h-4 text-[#FC6714]" />
          <span>Origem & Destino da Operação</span>
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Origem -->
          <div class="p-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40">
            <span class="text-[10px] font-bold uppercase text-slate-400">Depósito de Origem</span>
            <div class="font-bold text-slate-800 dark:text-slate-200 text-sm mt-0.5">
              {{ documentData.source_depot?.name || (documentData.movement_type === 'ENTRY' ? 'Entrada Externa / Fornecedor' : '-') }}
            </div>
            <div v-if="documentData.source_depot?.cluster" class="text-xs text-slate-500 mt-1 flex items-center gap-1">
              <MapPin class="w-3 h-3 text-[#FC6714]" />
              <span>Regional: {{ documentData.source_depot.cluster.name }}</span>
            </div>
          </div>

          <!-- Destino -->
          <div class="p-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40">
            <span class="text-[10px] font-bold uppercase text-slate-400">Depósito de Destino</span>
            <div class="font-bold text-slate-800 dark:text-slate-200 text-sm mt-0.5">
              {{ documentData.destination_depot?.name || (documentData.movement_type === 'EXIT' ? 'Saída Externa / Consumo' : '-') }}
            </div>
            <div v-if="documentData.destination_depot?.cluster" class="text-xs text-slate-500 mt-1 flex items-center gap-1">
              <MapPin class="w-3 h-3 text-[#FC6714]" />
              <span>Regional: {{ documentData.destination_depot.cluster.name }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 3. GRADE DE ITENS / MATERIAIS DO DOCUMENTO -->
      <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/40 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <Boxes class="w-4 h-4 text-[#FC6714]" />
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200">
              Itens do Documento ({{ documentData.items?.length || documentData.items_count || 0 }})
            </h4>
          </div>
          <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
            Volume Total: <strong class="text-slate-900 dark:text-white font-mono">{{ formatQuantity(documentData.total_quantity) }}</strong>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-bold uppercase">
              <tr>
                <th class="py-2.5 px-4">#</th>
                <th class="py-2.5 px-4">SKU / Código</th>
                <th class="py-2.5 px-4">Material</th>
                <th class="py-2.5 px-4 text-right">Qtd</th>
                <th class="py-2.5 px-4 text-center">Unidade</th>
                <th class="py-2.5 px-4 text-center">Seriais</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
              <tr
                v-for="(item, idx) in documentData.items"
                :key="item.id || idx"
                class="hover:bg-slate-50/70 dark:hover:bg-white/[0.02]"
              >
                <td class="py-3 px-4 font-mono text-slate-400">{{ idx + 1 }}</td>
                <td class="py-3 px-4 font-mono font-semibold text-slate-700 dark:text-slate-300">
                  {{ item.material?.code || '-' }}
                </td>
                <td class="py-3 px-4">
                  <div class="font-medium text-slate-900 dark:text-slate-100 text-sm">
                    {{ item.material?.name || 'Material' }}
                  </div>
                  <div class="text-[11px] text-slate-400">
                    {{ item.material?.category || 'Geral' }}
                  </div>
                </td>
                <td class="py-3 px-4 text-right font-bold text-slate-900 dark:text-white text-sm font-mono">
                  {{ formatQuantity(item.quantity) }}
                </td>
                <td class="py-3 px-4 text-center font-mono text-slate-500">
                  {{ item.material?.unit?.code || 'UND' }}
                </td>
                <td class="py-3 px-4 text-center whitespace-nowrap">
                  <span
                    v-if="item.serials && item.serials.length > 0"
                    class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800"
                  >
                    <QrCode class="w-3 h-3 inline mr-1" />
                    {{ item.serials.length }}
                  </span>
                  <span v-else class="text-slate-400">-</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- 4. RASTREABILIDADE SERIAL TOTAL DO DOCUMENTO -->
      <div v-if="allDocumentSerials.length > 0" class="p-4 rounded-xl bg-white dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800 shadow-2xs">
        <div class="flex items-center justify-between mb-3">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
            <QrCode class="w-4 h-4 text-[#FC6714]" />
            <span>Equipamentos Serializados Vinculados ({{ allDocumentSerials.length }})</span>
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
            v-for="(s, sIdx) in allDocumentSerials"
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

      <!-- 5. TRANSPORTE & RESPONSÁVEIS -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 text-xs">
        <div>
          <span class="font-bold text-slate-400 uppercase tracking-wider block text-[10px]">Motorista / Transportador</span>
          <span class="font-semibold text-slate-800 dark:text-slate-200 mt-1 block">
            {{ documentData.driver?.name || '-' }}
          </span>
        </div>
        <div>
          <span class="font-bold text-slate-400 uppercase tracking-wider block text-[10px]">Recebedor / Destinatário</span>
          <span class="font-semibold text-slate-800 dark:text-slate-200 mt-1 block">
            {{ documentData.receiver?.name || '-' }}
          </span>
        </div>
        <div>
          <span class="font-bold text-slate-400 uppercase tracking-wider block text-[10px]">Operador do Sistema</span>
          <span class="font-semibold text-slate-800 dark:text-slate-200 mt-1 block">
            {{ documentData.user?.name || '-' }}
          </span>
        </div>
      </div>

      <!-- 6. ANEXOS / COMPROVANTES DO DOCUMENTO -->
      <div v-if="allDocumentAttachments.length > 0" class="p-4 rounded-xl bg-white dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800 shadow-2xs">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
          <FileText class="w-4 h-4 text-[#FC6714]" />
          <span>Comprovantes & Documentos Anexados ({{ allDocumentAttachments.length }})</span>
        </h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <a
            v-for="(att, aIdx) in allDocumentAttachments"
            :key="att.id || aIdx"
            :href="`/storage/${att.file_path}`"
            target="_blank"
            class="p-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/60 hover:border-[#FC6714] transition flex items-center justify-between text-xs group"
          >
            <div class="flex items-center gap-2.5 truncate">
              <FileText class="w-4 h-4 text-slate-400 group-hover:text-[#FC6714] shrink-0" />
              <div class="truncate">
                <div class="font-semibold text-slate-800 dark:text-slate-200 truncate group-hover:text-[#FC6714]">
                  {{ att.file_name || 'Comprovante' }}
                </div>
                <div class="text-[10px] text-slate-400">
                  {{ formatFileSize(att.file_size) }}
                </div>
              </div>
            </div>
            <ExternalLink class="w-3.5 h-3.5 text-slate-400 group-hover:text-[#FC6714] shrink-0" />
          </a>
        </div>
      </div>

      <!-- 7. OBSERVAÇÕES (SE HOUVER) -->
      <div v-if="documentData.notes" class="p-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/40 text-xs">
        <span class="font-bold text-slate-400 uppercase tracking-wider block text-[10px] mb-1">Observações da Operação</span>
        <p class="text-slate-700 dark:text-slate-300 font-mono whitespace-pre-wrap">{{ documentData.notes }}</p>
      </div>
    </div>

    <template #footer>
      <div class="flex items-center justify-between w-full">
        <div class="text-xs text-slate-400">
          ID da Operação: <strong class="font-mono text-slate-700 dark:text-slate-300">{{ documentData?.protocol || documentData?.key || '-' }}</strong>
        </div>

        <div class="flex items-center gap-2">
          <button
            type="button"
            @click="$emit('close')"
            class="h-9 px-4 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition cursor-pointer"
          >
            Fechar
          </button>
        </div>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import {
  FileText, ArrowLeftRight, Boxes, QrCode, Warehouse, MapPin,
  ExternalLink, Copy, Check
} from 'lucide-vue-next';
import BaseModal from '@/components/common/BaseModal.vue';

const props = defineProps<{
  isOpen: boolean;
  documentData: any | null;
}>();

defineEmits<{
  (e: 'close'): void;
}>();

const copiedKey = ref<string | null>(null);

function copyToClipboard(text: string, key: string) {
  if (navigator?.clipboard?.writeText) {
    navigator.clipboard.writeText(text);
    copiedKey.value = key;
    setTimeout(() => {
      if (copiedKey.value === key) copiedKey.value = null;
    }, 2000);
  }
}

// Consolidação de todos os seriais de todos os itens do documento
const allDocumentSerials = computed(() => {
  if (!props.documentData?.items) return [];
  const list: any[] = [];
  const seenIds = new Set<any>();

  props.documentData.items.forEach((item: any) => {
    if (item.serials && Array.isArray(item.serials)) {
      item.serials.forEach((s: any) => {
        const id = s.id || s.serial_number;
        if (!seenIds.has(id)) {
          seenIds.add(id);
          list.push(s);
        }
      });
    }
  });

  return list;
});

// Consolidação de todos os anexos de todos os itens do documento
const allDocumentAttachments = computed(() => {
  if (!props.documentData?.items) return [];
  const list: any[] = [];
  const seenIds = new Set<any>();

  props.documentData.items.forEach((item: any) => {
    if (item.attachments && Array.isArray(item.attachments)) {
      item.attachments.forEach((a: any) => {
        const id = a.id || a.file_path;
        if (!seenIds.has(id)) {
          seenIds.add(id);
          list.push(a);
        }
      });
    }
  });

  return list;
});

function copyAllSerials() {
  const serials = allDocumentSerials.value.map(s => s.serial_number).join('\n');
  copyToClipboard(serials, 'all-serials');
}

function formatQuantity(val: any): string {
  if (val === null || val === undefined) return '0';
  const num = Number(val);
  return num.toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
}

function formatDate(val: any): string {
  if (!val) return '-';
  const str = String(val);
  if (str.includes('T')) {
    const parts = str.split('T')[0].split('-');
    if (parts.length === 3) return `${parts[2]}/${parts[1]}/${parts[0]}`;
  }
  const parts = str.split('-');
  if (parts.length === 3) return `${parts[2]}/${parts[1]}/${parts[0]}`;
  return str;
}

function formatDateTime(val: any): string {
  if (!val) return '-';
  try {
    const d = new Date(val);
    return d.toLocaleString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  } catch {
    return String(val);
  }
}

function formatFileSize(bytes: any): string {
  if (!bytes) return '';
  const num = Number(bytes);
  if (num < 1024) return `${num} B`;
  if (num < 1024 * 1024) return `${(num / 1024).toFixed(1)} KB`;
  return `${(num / (1024 * 1024)).toFixed(1)} MB`;
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
      return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800';
    case 'EXIT':
      return 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800';
    case 'TRANSFER':
      return 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800';
    case 'RETURN':
      return 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-800';
    case 'ADJUSTMENT':
      return 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800';
    default:
      return 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700';
  }
}
</script>
