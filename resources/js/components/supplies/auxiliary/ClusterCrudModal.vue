<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="$emit('close')"
    title="Gerenciar Clusters Regionais"
    description="Agrupamentos de bases físicas, almoxarifados e veículos de técnicos por região operacional"
    size="xl"
  >
    <div class="space-y-4">
      <!-- Barra Superior: Busca e Botão Novo -->
      <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="relative flex-1 min-w-[240px]">
          <Search class="w-4 h-4 absolute left-3 top-3 text-slate-400 pointer-events-none" />
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Buscar cluster por nome ou código..."
            class="w-full h-10 pl-9 pr-4 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714] focus:outline-none transition"
          />
        </div>

        <button
          v-if="!isFormOpen"
          type="button"
          @click="openCreateForm"
          class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] text-white text-xs font-semibold shadow-xs transition cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Novo Cluster</span>
        </button>
      </div>

      <!-- Formulário de Criação / Edição -->
      <Transition
        enter-active-class="transition duration-150 ease-out"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-100 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div
          v-if="isFormOpen"
          class="p-4 rounded-xl bg-orange-50/50 dark:bg-orange-950/20 border border-orange-200 dark:border-[#FC6714]/30 space-y-3"
        >
          <div class="flex items-center justify-between pb-2 border-b border-orange-200/60 dark:border-[#FC6714]/20">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
              <component :is="editingId ? Edit2 : Plus" class="w-3.5 h-3.5 text-[#FC6714]" />
              <span>{{ editingId ? 'Editar Cluster Regional' : 'Novo Cluster Regional' }}</span>
            </h4>
            <button
              type="button"
              @click="closeForm"
              class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1"
            >
              <X class="w-4 h-4" />
            </button>
          </div>

          <div v-if="formError" class="p-3 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-2">
            <AlertCircle class="w-4 h-4 shrink-0 text-rose-600" />
            <span>{{ formError }}</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div>
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Código / Sigla *
              </label>
              <input
                v-model="form.code"
                type="text"
                placeholder="Ex: CLU-MG-BH"
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#FC6714] outline-none uppercase"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Nome do Cluster *
              </label>
              <input
                v-model="form.name"
                type="text"
                placeholder="Ex: Belo Horizonte e Região Metropolitana"
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] outline-none"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Cor Identificadora
              </label>
              <div class="flex items-center gap-2">
                <input
                  v-model="form.color"
                  type="color"
                  class="w-9 h-9 p-0.5 rounded-lg border border-slate-300 dark:border-slate-700 cursor-pointer bg-white dark:bg-slate-950"
                />
                <input
                  v-model="form.color"
                  type="text"
                  placeholder="#FC6714"
                  class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#FC6714] outline-none text-xs"
                />
              </div>
            </div>

            <div class="sm:col-span-2">
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Descrição da Região / Abrangência
              </label>
              <input
                v-model="form.description"
                type="text"
                placeholder="Ex: Cobre as cidades de BH, Contagem, Betim e Sabará"
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] outline-none"
              />
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-2">
            <button
              type="button"
              @click="closeForm"
              class="px-3.5 py-1.5 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
            >
              Cancelar
            </button>
            <button
              type="button"
              :disabled="saving"
              @click="saveCluster"
              class="px-4 py-1.5 rounded-lg text-xs font-semibold bg-[#FC6714] hover:bg-[#E0530A] text-white shadow-xs transition cursor-pointer flex items-center gap-1.5"
            >
              <span v-if="saving" class="inline-block animate-spin mr-1">⟳</span>
              <span>{{ saving ? 'Salvando...' : (editingId ? 'Atualizar Cluster' : 'Criar Cluster') }}</span>
            </button>
          </div>
        </div>
      </Transition>

      <!-- Tabela Confortável de Clusters (Item 7 Design System) -->
      <div class="border border-slate-200 dark:border-[#14147A] rounded-xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto max-h-[380px]">
          <table class="w-full text-left border-collapse text-xs">
            <thead class="sticky top-0 bg-slate-100/90 dark:bg-[#03032E]/90 backdrop-blur-xs border-b border-slate-200 dark:border-[#14147A] text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold z-10">
              <tr>
                <th class="py-3 px-4 w-36">Código</th>
                <th class="py-3 px-4">Cluster Regional</th>
                <th class="py-3 px-4">Abrangência</th>
                <th class="py-3 px-4 w-28 text-center">Depósitos</th>
                <th class="py-3 px-4 text-right w-28">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <tr v-if="loading">
                <td colspan="5" class="py-8 text-center text-slate-400">
                  <span class="inline-block animate-spin mr-1.5 text-[#FC6714]">⟳</span> Carregando clusters...
                </td>
              </tr>
              <tr v-else-if="filteredClusters.length === 0">
                <td colspan="5" class="py-8 text-center text-slate-400">
                  Nenhum cluster regional encontrado.
                </td>
              </tr>
              <tr
                v-for="c in filteredClusters"
                :key="c.id"
                class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition"
              >
                <!-- Código -->
                <td class="py-3 px-4 font-mono font-bold text-slate-800 dark:text-slate-200">
                  <div class="flex items-center gap-2">
                    <span
                      class="w-3 h-3 rounded-full shrink-0 border border-black/10 shadow-2xs"
                      :style="{ backgroundColor: c.color || '#FC6714' }"
                    />
                    <span>{{ c.code }}</span>
                  </div>
                </td>

                <!-- Nome -->
                <td class="py-3 px-4 font-semibold text-slate-900 dark:text-slate-100">
                  {{ c.name }}
                </td>

                <!-- Descrição -->
                <td class="py-3 px-4 text-slate-500 dark:text-slate-400">
                  {{ c.description || '-' }}
                </td>

                <!-- Depósitos -->
                <td class="py-3 px-4 text-center">
                  <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                    {{ c.depots_count ?? 0 }} depósitos
                  </span>
                </td>

                <!-- Ações -->
                <td class="py-3 px-4 text-right">
                  <button
                    type="button"
                    @click="startEdit(c)"
                    class="p-1.5 rounded-lg text-slate-500 hover:text-[#FC6714] hover:bg-orange-50 dark:hover:bg-[#FC6714]/10 transition cursor-pointer"
                    title="Editar cluster"
                  >
                    <Edit2 class="w-3.5 h-3.5" />
                  </button>
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
          {{ clustersList.length }} clusters cadastrados
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
import { ref, computed, onMounted } from 'vue';
import { Search, Plus, Edit2, X, AlertCircle } from 'lucide-vue-next';
import BaseModal from '@/components/common/BaseModal.vue';

const props = defineProps<{
  isOpen: boolean;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'updated'): void;
}>();

const clustersList = ref<any[]>([]);
const loading = ref(false);
const saving = ref(false);
const searchTerm = ref('');
const isFormOpen = ref(false);
const editingId = ref<number | null>(null);
const formError = ref('');

const form = ref({
  code: '',
  name: '',
  color: '#FC6714',
  description: '',
});

const filteredClusters = computed(() => {
  if (!searchTerm.value.trim()) return clustersList.value;
  const q = searchTerm.value.toLowerCase();
  return clustersList.value.filter(c =>
    c.name?.toLowerCase().includes(q) ||
    c.code?.toLowerCase().includes(q) ||
    c.description?.toLowerCase().includes(q)
  );
});

async function loadClusters() {
  loading.value = true;
  try {
    const res = await fetch('/api/v1/clusters');
    const json = await res.json();
    clustersList.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar clusters:', err);
  } finally {
    loading.value = false;
  }
}

function openCreateForm() {
  editingId.value = null;
  formError.value = '';
  form.value = {
    code: '',
    name: '',
    color: '#FC6714',
    description: '',
  };
  isFormOpen.value = true;
}

function startEdit(c: any) {
  editingId.value = c.id;
  formError.value = '';
  form.value = {
    code: c.code || '',
    name: c.name || '',
    color: c.color || '#FC6714',
    description: c.description || '',
  };
  isFormOpen.value = true;
}

function closeForm() {
  isFormOpen.value = false;
  editingId.value = null;
  formError.value = '';
}

async function saveCluster() {
  if (!form.value.name.trim() || !form.value.code.trim()) {
    formError.value = 'Código e Nome são obrigatórios.';
    return;
  }

  saving.value = true;
  formError.value = '';
  try {
    const url = editingId.value ? `/api/v1/clusters/${editingId.value}` : '/api/v1/clusters';
    const method = editingId.value ? 'PUT' : 'POST';

    const res = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(form.value),
    });

    const json = await res.json();
    if (!res.ok) {
      formError.value = json.message || 'Erro ao salvar cluster.';
      return;
    }

    closeForm();
    await loadClusters();
    emit('updated');
  } catch (err: any) {
    formError.value = err.message || 'Erro de conexão ao salvar cluster.';
  } finally {
    saving.value = false;
  }
}

onMounted(() => {
  loadClusters();
});
</script>
