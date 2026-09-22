<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="$emit('close')"
    title="Gerenciar Depósitos"
    description="Cadastro e parametrização de depósitos físicos, almoxarifados e bases operacionais"
    size="2xl"
  >
    <div class="space-y-4">
      <!-- Mensagem de Erro / Alerta -->
      <div
        v-if="errorMessage"
        class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 font-medium flex items-center justify-between gap-2 text-xs"
      >
        <div class="flex items-center gap-2">
          <AlertCircle class="w-4 h-4 shrink-0 text-rose-600 dark:text-rose-400" />
          <span>{{ errorMessage }}</span>
        </div>
        <button
          type="button"
          @click="errorMessage = ''"
          class="text-rose-400 hover:text-rose-600 dark:hover:text-rose-200 p-0.5 cursor-pointer"
        >
          <X class="w-3.5 h-3.5" />
        </button>
      </div>

      <!-- Barra Superior: Busca e Botão Novo -->
      <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="relative flex-1 min-w-[240px]">
          <Search class="w-4 h-4 absolute left-3 top-3 text-slate-400 pointer-events-none" />
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Buscar depósito por nome, código ou responsável..."
            class="w-full h-10 pl-9 pr-4 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714] focus:outline-none transition"
          />
        </div>

        <button
          type="button"
          @click="$emit('create')"
          class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] text-white text-xs font-semibold shadow-xs transition cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Novo Depósito</span>
        </button>
      </div>

      <!-- Tabela Confortável de Depósitos (Design System) -->
      <div class="border border-slate-200 dark:border-[#14147A] rounded-xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto max-h-[380px]">
          <table class="w-full text-left border-collapse text-xs">
            <thead class="sticky top-0 bg-slate-100/90 dark:bg-[#03032E]/90 backdrop-blur-xs border-b border-slate-200 dark:border-[#14147A] text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold z-10">
              <tr>
                <th class="py-3 px-4">Identificação / Depósito</th>
                <th class="py-3 px-4">Tipo</th>
                <th class="py-3 px-4">Posição Regional</th>
                <th class="py-3 px-4">Proprietário</th>
                <th class="py-3 px-4">Responsável</th>
                <th class="py-3 px-4 text-right w-36">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <tr v-if="loading">
                <td colspan="6" class="py-8 text-center text-slate-400">
                  <span class="inline-block animate-spin mr-1.5 text-[#FC6714]">⟳</span> Carregando depósitos...
                </td>
              </tr>
              <tr v-else-if="filteredDepots.length === 0">
                <td colspan="6" class="py-8 text-center text-slate-400">
                  Nenhum depósito encontrado.
                </td>
              </tr>
              <tr
                v-else
                v-for="d in filteredDepots"
                :key="d.id"
                class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition"
              >
                <!-- Identificação / Nome -->
                <td class="py-3 px-4">
                  <div class="font-semibold text-slate-900 dark:text-slate-100">
                    {{ d.name }}
                  </div>
                  <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="font-mono text-[11px] text-[#FC6714] font-medium">{{ d.code }}</span>
                    <span v-if="d.city" class="text-[10px] text-slate-400 dark:text-slate-500">
                      • {{ d.city.name }} - {{ d.city.state?.code || '' }}
                    </span>
                  </div>
                </td>

                <!-- Tipo -->
                <td class="py-3 px-4">
                  <span
                    class="px-2 py-0.5 rounded text-[11px] font-semibold border"
                    :class="{
                      'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/60': d.type === 'CENTRAL',
                      'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60': d.type === 'REGIONAL_BASE',
                      'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60': d.type === 'LAB_REPAIR',
                      'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/60': d.type !== 'CENTRAL' && d.type !== 'REGIONAL_BASE' && d.type !== 'LAB_REPAIR'
                    }"
                  >
                    {{ formatDepotType(d.type) }}
                  </span>
                </td>

                <!-- Posição Regional -->
                <td class="py-3 px-4 text-slate-700 dark:text-slate-300">
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-[11px]">
                    {{ d.cluster?.name || '-' }}
                  </span>
                </td>

                <!-- Proprietário -->
                <td class="py-3 px-4">
                  <div
                    v-if="d.owner || d.cluster?.owner"
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/60"
                  >
                    <UserCheck class="w-3 h-3 shrink-0" />
                    <span>{{ (d.owner || d.cluster?.owner)?.code }}</span>
                    <span v-if="!d.owner && d.cluster?.owner" class="text-[9px] text-blue-500 font-normal">(Região)</span>
                  </div>
                  <span v-else class="text-[11px] text-slate-400 italic">Sem proprietário</span>
                </td>

                <!-- Responsável -->
                <td class="py-3 px-4 text-slate-700 dark:text-slate-300">
                  {{ d.responsible_person?.name || '-' }}
                </td>

                <!-- Ações -->
                <td class="py-3 px-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <button
                      type="button"
                      @click="$emit('edit', d)"
                      class="h-7 px-2 rounded text-xs font-medium border border-slate-300 dark:border-slate-700 hover:bg-orange-50 hover:text-[#FC6714] hover:border-[#FC6714] transition cursor-pointer inline-flex items-center gap-1"
                      title="Editar Depósito"
                    >
                      <Edit2 class="w-3 h-3" />
                      <span>Editar</span>
                    </button>

                    <button
                      type="button"
                      @click="deleteDepot(d)"
                      :disabled="deletingId === d.id"
                      class="h-7 px-2 rounded text-xs font-medium border border-rose-200 dark:border-rose-900/60 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer inline-flex items-center gap-1"
                      title="Excluir Depósito"
                    >
                      <span v-if="deletingId === d.id" class="animate-spin text-[10px]">⟳</span>
                      <Trash2 v-else class="w-3 h-3" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="w-full flex items-center justify-between">
        <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
          {{ depotsList.length }} depósitos cadastrados
        </span>
        <button
          type="button"
          @click="$emit('close')"
          class="h-9 px-4 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition cursor-pointer"
        >
          Fechar
        </button>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import { Search, Plus, Edit2, Trash2, X, AlertCircle, UserCheck } from 'lucide-vue-next';
import BaseModal from '@/components/common/BaseModal.vue';

const props = defineProps<{
  isOpen: boolean;
  depotTypesList?: any[];
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'create'): void;
  (e: 'edit', depot: any): void;
  (e: 'updated'): void;
}>();

const depotsList = ref<any[]>([]);
const loading = ref(false);
const searchTerm = ref('');
const errorMessage = ref('');
const deletingId = ref<number | null>(null);

const filteredDepots = computed(() => {
  if (!searchTerm.value.trim()) return depotsList.value;
  const q = searchTerm.value.toLowerCase();
  return depotsList.value.filter(d =>
    d.name.toLowerCase().includes(q) ||
    d.code.toLowerCase().includes(q) ||
    (d.responsible_person?.name && d.responsible_person.name.toLowerCase().includes(q)) ||
    (d.cluster?.name && d.cluster.name.toLowerCase().includes(q))
  );
});

function formatDepotType(type: string): string {
  if (props.depotTypesList) {
    const found = props.depotTypesList.find(t => t.code === type);
    if (found) return found.name;
  }
  const map: Record<string, string> = {
    CENTRAL: 'Almoxarifado Central',
    REGIONAL_BASE: 'Base Regional',
    LAB_REPAIR: 'Laboratório de Reparo',
  };
  return map[type] || type || 'Depósito';
}

async function loadDepots() {
  loading.value = true;
  errorMessage.value = '';
  try {
    const res = await fetch('/api/v1/depots');
    const json = await res.json();
    depotsList.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar depósitos:', err);
    errorMessage.value = 'Erro ao carregar lista de depósitos.';
  } finally {
    loading.value = false;
  }
}

async function deleteDepot(depot: any) {
  if (!confirm(`Deseja realmente excluir o depósito "${depot.name}" (${depot.code})?`)) {
    return;
  }

  deletingId.value = depot.id;
  errorMessage.value = '';
  try {
    const res = await fetch(`/api/v1/depots/${depot.id}`, {
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

    await loadDepots();
    emit('updated');
  } catch (err: any) {
    errorMessage.value = err.message || 'Erro de comunicação ao excluir depósito.';
  } finally {
    deletingId.value = null;
  }
}

watch(
  () => props.isOpen,
  (open) => {
    if (open) {
      loadDepots();
    }
  }
);

onMounted(() => {
  if (props.isOpen) {
    loadDepots();
  }
});

defineExpose({
  loadDepots,
});
</script>
