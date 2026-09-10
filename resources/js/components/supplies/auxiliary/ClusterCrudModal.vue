<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="$emit('close')"
    title="Gerenciar Posições Regionais"
    description="Definição de posições geográficas e regiões para agrupamento e consolidação de depósitos"
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
            placeholder="Buscar posição regional por nome ou código..."
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
          <span>Nova Posição Regional</span>
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
              <span>{{ editingId ? 'Editar Posição Regional' : 'Nova Posição Regional' }}</span>
            </h4>
            <button
              type="button"
              @click="closeForm"
              class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 cursor-pointer"
            >
              <X class="w-4 h-4" />
            </button>
          </div>

          <div v-if="formError" class="p-3 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-2">
            <AlertCircle class="w-4 h-4 shrink-0 text-rose-600" />
            <span>{{ formError }}</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div class="sm:col-span-3">
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Proprietário Vinculado *
              </label>
              <select
                v-model="form.owner_id"
                required
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714] outline-none cursor-pointer"
              >
                <option value="">Selecione o proprietário obrigatório...</option>
                <option v-for="o in availableOwners" :key="o.id" :value="o.id">
                  {{ o.code }} — {{ o.name }} (Solicitante: {{ o.person ? o.person.name : 'N/A' }})
                </option>
              </select>
              <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 block">
                A posição regional é vinculada obrigatoriamente a um proprietário com perfil de solicitante.
              </span>
            </div>

            <div>
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Código / Sigla *
              </label>
              <input
                v-model="form.code"
                type="text"
                placeholder="Ex: REG-CAMPINAS"
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#FC6714] outline-none uppercase"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Nome da Posição Regional *
              </label>
              <input
                v-model="form.name"
                type="text"
                placeholder="Ex: Região de Campinas e RMC"
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
                placeholder="Ex: Cobre Campinas, Valinhos, Vinhedo, Sumaré e Paulínia"
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] outline-none"
              />
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 pt-2">
            <button
              type="button"
              @click="closeForm"
              class="px-3.5 py-1.5 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
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
              <span>{{ saving ? 'Salvando...' : (editingId ? 'Atualizar Região' : 'Criar Região') }}</span>
            </button>
          </div>
        </div>
      </Transition>

      <!-- Tabela Confortável de Posições Regionais (Item 7 Design System) -->
      <div class="border border-slate-200 dark:border-[#14147A] rounded-xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto max-h-[380px]">
          <table class="w-full text-left border-collapse text-xs">
            <thead class="sticky top-0 bg-slate-100/90 dark:bg-[#03032E]/90 backdrop-blur-xs border-b border-slate-200 dark:border-[#14147A] text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold z-10">
              <tr>
                <th class="py-3 px-4 w-32">Código</th>
                <th class="py-3 px-4">Posição Regional</th>
                <th class="py-3 px-4 w-44">Proprietário</th>
                <th class="py-3 px-4">Abrangência</th>
                <th class="py-3 px-4 w-24 text-center">Depósitos</th>
                <th class="py-3 px-4 text-right w-24">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <tr v-if="loading">
                <td colspan="6" class="py-8 text-center text-slate-400">
                  <span class="inline-block animate-spin mr-1.5 text-[#FC6714]">⟳</span> Carregando posições regionais...
                </td>
              </tr>
              <tr v-else-if="filteredClusters.length === 0">
                <td colspan="6" class="py-8 text-center text-slate-400">
                  Nenhuma posição regional encontrada.
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

                <!-- Proprietário -->
                <td class="py-3 px-4">
                  <div v-if="c.owner" class="space-y-0.5">
                    <div class="flex items-center gap-1.5">
                      <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-mono text-[10px] font-bold">
                        {{ c.owner.code }}
                      </span>
                      <span class="font-medium text-slate-800 dark:text-slate-200 truncate max-w-[130px]" :title="c.owner.name">
                        {{ c.owner.name }}
                      </span>
                    </div>
                    <span v-if="c.owner.person" class="text-[10px] text-slate-400 block truncate max-w-[150px]">
                      Solicitante: {{ c.owner.person.name }}
                    </span>
                  </div>
                  <span v-else class="text-slate-400 italic">Não vinculado</span>
                </td>

                <!-- Descrição -->
                <td class="py-3 px-4 text-slate-600 dark:text-slate-400 truncate max-w-xs">
                  {{ c.description || '-' }}
                </td>

                <!-- Quantidade de Depósitos -->
                <td class="py-3 px-4 text-center font-mono font-semibold text-slate-700 dark:text-slate-300">
                  {{ c.depots_count ?? (c.depots ? c.depots.length : 0) }}
                </td>

                <!-- Ações -->
                <td class="py-3 px-4 text-right">
                  <button
                    type="button"
                    @click="editCluster(c)"
                    class="h-7 px-2.5 rounded text-xs font-medium border border-slate-300 dark:border-slate-700 hover:bg-orange-50 hover:text-[#FC6714] hover:border-[#FC6714] transition cursor-pointer inline-flex items-center gap-1"
                  >
                    <Edit2 class="w-3 h-3" />
                    <span>Editar</span>
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
          {{ clustersList.length }} posições regionais cadastradas
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
const availableOwners = ref<any[]>([]);
const loading = ref(false);
const searchTerm = ref('');
const isFormOpen = ref(false);
const saving = ref(false);
const formError = ref('');
const editingId = ref<number | null>(null);

const form = ref({
  owner_id: '' as number | string,
  code: '',
  name: '',
  color: '#FC6714',
  description: '',
});

const filteredClusters = computed(() => {
  if (!searchTerm.value.trim()) return clustersList.value;
  const q = searchTerm.value.toLowerCase();
  return clustersList.value.filter(c =>
    c.name.toLowerCase().includes(q) ||
    c.code.toLowerCase().includes(q) ||
    (c.description && c.description.toLowerCase().includes(q)) ||
    (c.owner && (c.owner.name.toLowerCase().includes(q) || c.owner.code.toLowerCase().includes(q)))
  );
});

async function loadOwners() {
  try {
    const res = await fetch('/api/v1/material-owners?active_only=true');
    const json = await res.json();
    availableOwners.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar proprietários:', err);
  }
}

async function loadClusters() {
  loading.value = true;
  try {
    const res = await fetch('/api/v1/clusters');
    const json = await res.json();
    clustersList.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar posições regionais:', err);
  } finally {
    loading.value = false;
  }
}

function openCreateForm() {
  editingId.value = null;
  formError.value = '';
  form.value = {
    owner_id: availableOwners.value.length > 0 ? availableOwners.value[0].id : '',
    code: '',
    name: '',
    color: '#FC6714',
    description: '',
  };
  isFormOpen.value = true;
}

function editCluster(cluster: any) {
  editingId.value = cluster.id;
  formError.value = '';
  form.value = {
    owner_id: cluster.owner_id ? cluster.owner_id : (cluster.owner?.id ? cluster.owner.id : ''),
    code: cluster.code,
    name: cluster.name,
    color: cluster.color || '#FC6714',
    description: cluster.description || '',
  };
  isFormOpen.value = true;
}

function closeForm() {
  isFormOpen.value = false;
  editingId.value = null;
  formError.value = '';
}

async function saveCluster() {
  formError.value = '';
  if (!form.value.owner_id) {
    formError.value = 'O vínculo com um Proprietário é obrigatório para a Posição Regional.';
    return;
  }
  if (!form.value.name.trim()) {
    formError.value = 'O nome da posição regional é obrigatório.';
    return;
  }
  if (!form.value.code.trim()) {
    formError.value = 'O código da posição regional é obrigatório.';
    return;
  }

  saving.value = true;
  try {
    const url = editingId.value ? `/api/v1/clusters/${editingId.value}` : '/api/v1/clusters';
    const method = editingId.value ? 'PUT' : 'POST';

    const res = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        ...form.value,
        owner_id: Number(form.value.owner_id),
      }),
    });

    const json = await res.json();
    if (!res.ok) {
      formError.value = json.message || 'Erro ao salvar posição regional.';
      return;
    }

    closeForm();
    await loadClusters();
    emit('updated');
  } catch (err: any) {
    formError.value = err.message || 'Erro de conexão com o servidor.';
  } finally {
    saving.value = false;
  }
}

watch(
  () => props.isOpen,
  (open) => {
    if (open) {
      closeForm();
      loadOwners();
      loadClusters();
    }
  }
);

onMounted(() => {
  loadOwners();
  loadClusters();
});
</script>
