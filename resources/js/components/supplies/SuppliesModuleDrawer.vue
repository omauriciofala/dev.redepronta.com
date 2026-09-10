<template>
  <BaseModal
    v-model="isOpenModel"
    size="drawer"
    title="Menu do Módulo de Suprimentos & WMS"
    description="Atalhos rápidos, posições de estoque, alertas de reposição e ferramentas de gestão"
    @close="close"
  >
    <!-- Header Badge -->
    <template #header-badge>
      <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 dark:bg-[#FC6714]/20 text-[#FC6714] border border-orange-200 dark:border-[#FC6714]/30">
        {{ totalMaterials }} materiais cadastrados
      </span>
    </template>

    <div class="space-y-6 text-xs">
      <!-- 1. RESUMO OPERACIONAL EM DESTAQUE -->
      <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 space-y-3">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
            Resumo da Posição Regional
          </span>
          <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            WMS Integrado ao Vivo
          </span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center">
          <div class="p-2.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
            <div class="text-base font-bold font-heading text-slate-900 dark:text-slate-100">{{ totalMaterials }}</div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Materiais</div>
          </div>
          <div class="p-2.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
            <div class="text-base font-bold font-heading text-emerald-600 dark:text-emerald-400">{{ totalBaseItems }}</div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Saldo Físico</div>
          </div>
          <div class="p-2.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
            <div class="text-base font-bold font-heading text-purple-600 dark:text-purple-400">{{ totalSerialsInStock }}</div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Serializados</div>
          </div>
          <div class="p-2.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
            <div class="text-base font-bold font-heading text-blue-600 dark:text-blue-400">{{ depotsCount }}</div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Depósitos</div>
          </div>
        </div>
      </div>

      <!-- 2. NAVEGAÇÃO RÁPIDA POR ABAS -->
      <div class="space-y-2.5">
        <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
          <Layers class="w-3.5 h-3.5 text-[#FC6714]" />
          <span>Visões do Módulo</span>
        </h4>
        <div class="grid grid-cols-2 gap-2">
          <button
            type="button"
            @click="navigateToTab('regional')"
            class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-white dark:bg-slate-900 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center gap-2.5 text-left cursor-pointer group shadow-2xs"
          >
            <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-950/80 text-[#FC6714] flex items-center justify-center shrink-0 group-hover:scale-105 transition">
              <Layers class="w-4 h-4" />
            </div>
            <div class="truncate">
              <div class="font-bold text-slate-800 dark:text-slate-200 truncate">Posição Regional</div>
              <div class="text-[10px] text-slate-400">Saldo por Região</div>
            </div>
          </button>

          <button
            type="button"
            @click="navigateToTab('materials')"
            class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-white dark:bg-slate-900 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center gap-2.5 text-left cursor-pointer group shadow-2xs"
          >
            <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-950/80 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
              <Boxes class="w-4 h-4" />
            </div>
            <div class="truncate">
              <div class="font-bold text-slate-800 dark:text-slate-200 truncate">Catálogo Materiais</div>
              <div class="text-[10px] text-slate-400">SKUs e Especificações</div>
            </div>
          </button>

          <button
            type="button"
            @click="navigateToTab('depots')"
            class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-white dark:bg-slate-900 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center gap-2.5 text-left cursor-pointer group shadow-2xs"
          >
            <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
              <Warehouse class="w-4 h-4" />
            </div>
            <div class="truncate">
              <div class="font-bold text-slate-800 dark:text-slate-200 truncate">Depósitos</div>
              <div class="text-[10px] text-slate-400">Locais Físicos e Bases</div>
            </div>
          </button>

          <button
            type="button"
            @click="navigateToTab('serials')"
            class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-white dark:bg-slate-900 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center gap-2.5 text-left cursor-pointer group shadow-2xs"
          >
            <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-950/80 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
              <QrCode class="w-4 h-4" />
            </div>
            <div class="truncate">
              <div class="font-bold text-slate-800 dark:text-slate-200 truncate">Serializado</div>
              <div class="text-[10px] text-slate-400">Rastreabilidade e Seriais</div>
            </div>
          </button>
        </div>
      </div>

      <!-- 3. AÇÕES RÁPIDAS OPERACIONAIS -->
      <div class="space-y-2.5">
        <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
          <Zap class="w-3.5 h-3.5 text-[#FC6714]" />
          <span>Ações Operacionais Imediatas</span>
        </h4>
        <div class="space-y-2">
          <button
            type="button"
            @click="triggerAction('transfer')"
            class="w-full p-3 rounded-xl border border-orange-200 dark:border-[#FC6714]/30 bg-orange-50/40 dark:bg-[#FC6714]/10 hover:bg-orange-100/60 dark:hover:bg-[#FC6714]/20 transition flex items-center justify-between text-left cursor-pointer group"
          >
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-[#FC6714] text-white flex items-center justify-center shrink-0 shadow-xs">
                <ArrowUpDown class="w-4 h-4" />
              </div>
              <div>
                <div class="font-bold text-slate-900 dark:text-slate-100">Nova Movimentação de Estoque</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400">Entrada, saída, devolução e transferência</div>
              </div>
            </div>
            <ArrowUpRight class="w-4 h-4 text-[#FC6714] opacity-70 group-hover:opacity-100 transition" />
          </button>

          <button
            type="button"
            @click="triggerAction('material')"
            class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition flex items-center justify-between text-left cursor-pointer group shadow-2xs"
          >
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <Plus class="w-4 h-4" />
              </div>
              <div>
                <div class="font-bold text-slate-800 dark:text-slate-200">Novo Material no Catálogo</div>
                <div class="text-[11px] text-slate-400">Cadastrar SKU, unidade e parametrização serial</div>
              </div>
            </div>
            <ArrowUpRight class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition" />
          </button>

          <button
            type="button"
            @click="triggerAction('import-materials')"
            class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition flex items-center justify-between text-left cursor-pointer group shadow-2xs"
          >
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-950/60 text-[#FC6714] flex items-center justify-center shrink-0">
                <FileSpreadsheet class="w-4 h-4" />
              </div>
              <div>
                <div class="font-bold text-slate-800 dark:text-slate-200">Importar Materiais (Planilha)</div>
                <div class="text-[11px] text-slate-400">Download de modelo e upload em lote (.csv)</div>
              </div>
            </div>
            <ArrowUpRight class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition" />
          </button>

          <button
            type="button"
            @click="triggerAction('depot')"
            class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition flex items-center justify-between text-left cursor-pointer group shadow-2xs"
          >
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <Warehouse class="w-4 h-4" />
              </div>
              <div>
                <div class="font-bold text-slate-800 dark:text-slate-200">Novo Depósito Físico</div>
                <div class="text-[11px] text-slate-400">Cadastrar almoxarifado ou base operacional</div>
              </div>
            </div>
            <ArrowUpRight class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition" />
          </button>

          <button
            type="button"
            @click="triggerAction('cluster')"
            class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition flex items-center justify-between text-left cursor-pointer group shadow-2xs"
          >
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                <MapPin class="w-4 h-4" />
              </div>
              <div>
                <div class="font-bold text-slate-800 dark:text-slate-200">Gerenciar Posições Regionais</div>
                <div class="text-[11px] text-slate-400">Agrupamentos geográficos de depósitos</div>
              </div>
            </div>
            <ArrowUpRight class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition" />
          </button>
        </div>
      </div>

      <!-- 4. ALERTAS DE INTEGRIDADE -->
      <div class="p-4 rounded-xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/60 space-y-2.5">
        <div class="flex items-center justify-between text-amber-800 dark:text-amber-300 font-bold">
          <span class="flex items-center gap-1.5">
            <AlertTriangle class="w-4 h-4 text-amber-600" />
            Integridade & Rastreabilidade WMS
          </span>
          <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-800 font-mono">
            ON DELETE RESTRICT
          </span>
        </div>
        <p class="text-[11px] text-amber-700/90 dark:text-amber-400/90 leading-relaxed">
          Materiais, depósitos e seriais vinculados a transferências ou movimentações estão protegidos com integridade estrita no banco de dados contra exclusão acidental.
        </p>
      </div>
    </div>

    <!-- Footer do Drawer -->
    <template #footer>
      <div class="w-full flex items-center justify-between">
        <span class="text-xs text-slate-400 font-medium">ERP Rede Pronta • Suprimentos & WMS</span>
        <button
          type="button"
          @click="close"
          class="h-9 px-4 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition cursor-pointer"
        >
          Fechar Menu
        </button>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import {
  Layers, Boxes, Warehouse, QrCode, ArrowRightLeft, Plus,
  MapPin, Zap, ArrowUpRight, AlertTriangle, FileSpreadsheet
} from 'lucide-vue-next';
import BaseModal from '@/components/common/BaseModal.vue';

const props = defineProps<{
  modelValue: boolean;
  totalMaterials: number;
  totalBaseItems: number;
  totalSerialsInStock: number;
  depotsCount: number;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
  (e: 'navigate', tab: 'regional' | 'materials' | 'depots' | 'serials'): void;
  (e: 'action', action: 'transfer' | 'material' | 'depot' | 'cluster' | 'import-materials'): void;
}>();

const isOpenModel = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v),
});

function close() {
  emit('update:modelValue', false);
}

function navigateToTab(tab: 'regional' | 'materials' | 'depots' | 'serials') {
  emit('navigate', tab);
  close();
}

function triggerAction(action: 'transfer' | 'material' | 'depot' | 'cluster' | 'import-materials') {
  emit('action', action);
  close();
}
</script>
