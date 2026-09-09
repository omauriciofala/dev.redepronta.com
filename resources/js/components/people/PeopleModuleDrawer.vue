<template>
  <BaseModal
    v-model="isOpenModel"
    size="drawer"
    title="Menu do Módulo de Pessoas"
    description="Atalhos rápidos, relatórios operacionais, exportação e configurações do módulo"
    @close="close"
  >
    <!-- Header Badge -->
    <template #header-badge>
      <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 dark:bg-[#FC6714]/20 text-[#FC6714] border border-orange-200 dark:border-[#FC6714]/30">
        {{ totalRecords.toLocaleString('pt-BR') }} registros
      </span>
    </template>

    <div class="space-y-6 text-xs">
      <!-- 1. RESUMO OPERACIONAL EM DESTAQUE -->
      <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 space-y-3">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
            Resumo Cadastral
          </span>
          <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            Base Canônica Ativa
          </span>
        </div>
        <div class="grid grid-cols-3 gap-2 text-center">
          <div class="p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
            <div class="text-base font-bold font-heading text-slate-900 dark:text-slate-100">{{ totalRecords }}</div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Total Pessoas</div>
          </div>
          <div class="p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
            <div class="text-base font-bold font-heading text-emerald-600 dark:text-emerald-400">{{ activeRecords }}</div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Ativas</div>
          </div>
          <div class="p-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
            <div class="text-base font-bold font-heading text-[#FC6714]">{{ inactiveRecords }}</div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Inativas</div>
          </div>
        </div>
      </div>

      <!-- 2. RELATÓRIOS & FILTROS POR PAPEL OPERACIONAL -->
      <div class="space-y-2.5">
        <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
          <Filter class="w-3.5 h-3.5 text-[#FC6714]" />
          <span>Filtros Rápidos por Papel Operacional</span>
        </h4>
        <div class="grid grid-cols-2 gap-2">
          <button
            type="button"
            @click="applyRoleFilter('client')"
            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-white dark:bg-slate-900 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center gap-2.5 text-left cursor-pointer group shadow-2xs"
          >
            <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
              <UserCheck class="w-3.5 h-3.5" />
            </div>
            <div class="truncate">
              <div class="font-bold text-slate-800 dark:text-slate-200 truncate">Clientes</div>
              <div class="text-[10px] text-slate-400">Filtrar na grid</div>
            </div>
          </button>

          <button
            type="button"
            @click="applyRoleFilter('supplier')"
            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-white dark:bg-slate-900 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center gap-2.5 text-left cursor-pointer group shadow-2xs"
          >
            <div class="w-7 h-7 rounded-lg bg-purple-100 dark:bg-purple-950/80 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
              <Building2 class="w-3.5 h-3.5" />
            </div>
            <div class="truncate">
              <div class="font-bold text-slate-800 dark:text-slate-200 truncate">Fornecedores</div>
              <div class="text-[10px] text-slate-400">Filtrar na grid</div>
            </div>
          </button>

          <button
            type="button"
            @click="applyRoleFilter('employee')"
            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-white dark:bg-slate-900 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center gap-2.5 text-left cursor-pointer group shadow-2xs"
          >
            <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-950/80 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
              <Users class="w-3.5 h-3.5" />
            </div>
            <div class="truncate">
              <div class="font-bold text-slate-800 dark:text-slate-200 truncate">Colaboradores</div>
              <div class="text-[10px] text-slate-400">Equipe interna</div>
            </div>
          </button>

          <button
            type="button"
            @click="applyRoleFilter('seller')"
            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-white dark:bg-slate-900 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center gap-2.5 text-left cursor-pointer group shadow-2xs"
          >
            <div class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-950/80 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
              <TrendingUp class="w-3.5 h-3.5" />
            </div>
            <div class="truncate">
              <div class="font-bold text-slate-800 dark:text-slate-200 truncate">Vendedores</div>
              <div class="text-[10px] text-slate-400">Comercial</div>
            </div>
          </button>

          <button
            type="button"
            @click="applyRoleFilter('carrier')"
            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-white dark:bg-slate-900 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center gap-2.5 text-left cursor-pointer group shadow-2xs"
          >
            <div class="w-7 h-7 rounded-lg bg-cyan-100 dark:bg-cyan-950/80 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
              <Truck class="w-3.5 h-3.5" />
            </div>
            <div class="truncate">
              <div class="font-bold text-slate-800 dark:text-slate-200 truncate">Transportadoras</div>
              <div class="text-[10px] text-slate-400">Logística</div>
            </div>
          </button>

          <button
            type="button"
            @click="applyRoleFilter('driver')"
            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-white dark:bg-slate-900 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center gap-2.5 text-left cursor-pointer group shadow-2xs"
          >
            <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
              <Car class="w-3.5 h-3.5" />
            </div>
            <div class="truncate">
              <div class="font-bold text-slate-800 dark:text-slate-200 truncate">Motoristas</div>
              <div class="text-[10px] text-slate-400">Condutores</div>
            </div>
          </button>
        </div>
      </div>

      <!-- 3. IMPORTAÇÃO & EXPORTAÇÃO DE DADOS -->
      <div class="space-y-2.5">
        <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
          <DownloadCloud class="w-3.5 h-3.5 text-[#FC6714]" />
          <span>Importação & Exportação</span>
        </h4>
        <div class="space-y-2">
          <!-- Exportar CSV -->
          <button
            type="button"
            @click="exportCsv"
            :disabled="isExporting"
            class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-white dark:bg-slate-900 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center justify-between text-left cursor-pointer shadow-2xs group"
          >
            <div class="flex items-center gap-3">
              <div class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 group-hover:scale-105 transition">
                <FileSpreadsheet class="w-4 h-4" />
              </div>
              <div>
                <div class="font-bold text-slate-900 dark:text-slate-100">Exportar para Planilha (CSV)</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400">Baixa os cadastros atuais com dados de contato e endereço</div>
              </div>
            </div>
            <Loader2 v-if="isExporting" class="w-4 h-4 animate-spin text-[#FC6714]" />
            <Download v-else class="w-4 h-4 text-slate-400 group-hover:text-[#FC6714] transition" />
          </button>

          <!-- Importar Lote -->
          <button
            type="button"
            @click="emit('openImport')"
            class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-white dark:bg-slate-900 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center justify-between text-left cursor-pointer shadow-2xs group"
          >
            <div class="flex items-center gap-3">
              <div class="p-2 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 group-hover:scale-105 transition">
                <UploadCloud class="w-4 h-4" />
              </div>
              <div>
                <div class="font-bold text-slate-900 dark:text-slate-100">Importação em Lote</div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400">Carregamento em massa via arquivo CSV / Excel</div>
              </div>
            </div>
            <ChevronRight class="w-4 h-4 text-slate-400 group-hover:text-[#FC6714] transition" />
          </button>
        </div>
      </div>

      <!-- 4. CADASTROS DE APOIO E TABELAS BASE (ATALHOS DIRETOS) -->
      <div class="space-y-2.5">
        <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
          <FolderTree class="w-3.5 h-3.5 text-[#FC6714]" />
          <span>Cadastros de Apoio & Tabelas Base</span>
        </h4>
        <div class="grid grid-cols-1 gap-1.5">
          <button
            type="button"
            @click="openAuxModal('states')"
            class="px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-between text-slate-700 dark:text-slate-200 transition cursor-pointer"
          >
            <div class="flex items-center gap-2">
              <MapPin class="w-3.5 h-3.5 text-slate-400" />
              <span>Gerenciar Estados (UF)</span>
            </div>
            <span class="text-[10px] text-slate-400 font-mono">27 UFs</span>
          </button>

          <button
            type="button"
            @click="openAuxModal('cities')"
            class="px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-between text-slate-700 dark:text-slate-200 transition cursor-pointer"
          >
            <div class="flex items-center gap-2">
              <Building2 class="w-3.5 h-3.5 text-slate-400" />
              <span>Gerenciar Cidades / Municípios</span>
            </div>
            <span class="text-[10px] text-slate-400 font-mono">Paginado</span>
          </button>

          <button
            type="button"
            @click="openAuxModal('neighborhoods')"
            class="px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-between text-slate-700 dark:text-slate-200 transition cursor-pointer"
          >
            <div class="flex items-center gap-2">
              <Home class="w-3.5 h-3.5 text-slate-400" />
              <span>Gerenciar Bairros Canônicos</span>
            </div>
            <span class="text-[10px] text-slate-400">CRUD</span>
          </button>

          <button
            type="button"
            @click="openAuxModal('groups')"
            class="px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-between text-slate-700 dark:text-slate-200 transition cursor-pointer"
          >
            <div class="flex items-center gap-2">
              <Users class="w-3.5 h-3.5 text-slate-400" />
              <span>Grupos de Pessoas & Categorias</span>
            </div>
            <span class="text-[10px] text-slate-400">VIP / Prestadores</span>
          </button>

          <button
            type="button"
            @click="openAuxModal('cnaes')"
            class="px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-between text-slate-700 dark:text-slate-200 transition cursor-pointer"
          >
            <div class="flex items-center gap-2">
              <FileText class="w-3.5 h-3.5 text-slate-400" />
              <span>Tabela Canônica de CNAEs</span>
            </div>
            <span class="text-[10px] text-slate-400">Receita</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Rodapé Fixo do Drawer -->
    <template #footer>
      <div class="flex items-center justify-between gap-3 w-full">
        <button
          type="button"
          @click="emit('resetFilters'); close();"
          class="inline-flex items-center gap-1.5 h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 font-medium text-xs transition cursor-pointer"
        >
          <RotateCcw class="w-3.5 h-3.5 text-slate-400" />
          <span>Limpar Filtros da Grid</span>
        </button>

        <button
          type="button"
          @click="close"
          class="h-9 px-4 rounded-lg bg-[#FC6714] hover:bg-[#e0580e] text-white font-semibold text-xs shadow-xs transition cursor-pointer"
        >
          Fechar Menu
        </button>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import {
  Users,
  UserCheck,
  Building2,
  Filter,
  TrendingUp,
  Truck,
  Car,
  DownloadCloud,
  FileSpreadsheet,
  Download,
  UploadCloud,
  ChevronRight,
  FolderTree,
  MapPin,
  Home,
  FileText,
  RotateCcw,
  Loader2,
} from 'lucide-vue-next';
import BaseModal from '../common/BaseModal.vue';
import axios from 'axios';

const props = defineProps<{
  modelValue?: boolean;
  isOpen?: boolean;
  totalRecords?: number;
  activeRecords?: number;
  inactiveRecords?: number;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', val: boolean): void;
  (e: 'update:isOpen', val: boolean): void;
  (e: 'close'): void;
  (e: 'filterRole', role: string): void;
  (e: 'openAuxModal', modalName: string): void;
  (e: 'openImport'): void;
  (e: 'resetFilters'): void;
  (e: 'toast', msg: string, type: 'success' | 'error'): void;
}>();

const isOpenModel = computed({
  get: () => props.modelValue ?? props.isOpen ?? false,
  set: (val: boolean) => {
    emit('update:modelValue', val);
    emit('update:isOpen', val);
    if (!val) emit('close');
  },
});

const close = () => {
  isOpenModel.value = false;
  emit('close');
};

const isExporting = ref(false);

const applyRoleFilter = (role: string) => {
  emit('filterRole', role);
  close();
};

const openAuxModal = (modalName: string) => {
  close();
  emit('openAuxModal', modalName);
};

const exportCsv = async () => {
  isExporting.value = true;
  try {
    const res = await axios.get('/api/v1/people?per_page=5000');
    const items = res.data?.data || [];

    if (items.length === 0) {
      emit('toast', 'Nenhum registro para exportar.', 'error');
      return;
    }

    const headers = ['ID', 'Tipo', 'Nome', 'Nome Fantasia', 'Documento', 'Status', 'Grupo', 'Email', 'Telefone', 'Cidade', 'UF'];
    const rows = items.map((p: any) => [
      p.id,
      p.person_type === 'individual' ? 'Física' : 'Jurídica',
      `"${(p.name || '').replace(/"/g, '""')}"`,
      `"${(p.trade_name || '').replace(/"/g, '""')}"`,
      `"${p.document_number || p.document || ''}"`,
      p.status === 'active' ? 'Ativo' : 'Inativo',
      `"${(p.group?.name || p.group_name || '').replace(/"/g, '""')}"`,
      `"${p.email || ''}"`,
      `"${p.phone || p.whatsapp || ''}"`,
      `"${(p.city?.name || p.address?.city_name || '').replace(/"/g, '""')}"`,
      `"${p.city?.state?.code || p.address?.state_code || ''}"`,
    ]);

    const csvContent = '\uFEFF' + [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', `pessoas_export_${new Date().toISOString().slice(0, 10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);

    emit('toast', `${items.length} pessoas exportadas com sucesso!`, 'success');
  } catch (err: any) {
    emit('toast', 'Erro ao exportar dados em CSV.', 'error');
  } finally {
    isExporting.value = false;
  }
};
</script>
