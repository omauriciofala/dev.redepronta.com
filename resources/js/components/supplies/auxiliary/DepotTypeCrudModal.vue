<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="$emit('close')"
    title="Gerenciar Tipos de Depósito"
    description="Parametrização de categorias e tipos de depósitos físicos, almoxarifados e bases operacionais"
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
            placeholder="Buscar tipo de depósito por nome ou código..."
            class="w-full h-10 pl-9 pr-4 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714] focus:outline-none transition"
          />
        </div>

        <button
          v-if="!isFormOpen"
          type="button"
          @click="openCreateForm"
          class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] text-white text-xs font-semibold shadow-xs transition cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Novo Tipo de Depósito</span>
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
              <span>{{ editingId ? 'Editar Tipo de Depósito' : 'Novo Tipo de Depósito' }}</span>
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
            <div>
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Código / Sigla *
              </label>
              <input
                v-model="form.code"
                type="text"
                placeholder="Ex: POSTO_AVANCADO"
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#FC6714] outline-none uppercase"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Nome do Tipo de Depósito *
              </label>
              <input
                v-model="form.name"
                type="text"
                placeholder="Ex: Posto Avançado / Base Satélite"
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] outline-none"
              />
            </div>

            <div class="sm:col-span-3">
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Descrição e Finalidade Operacional
              </label>
              <input
                v-model="form.description"
                type="text"
                placeholder="Ex: Depósito intermediário de campo para reabastecimento de frotas e técnicos"
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
              @click="saveType"
              class="px-4 py-1.5 rounded-lg text-xs font-semibold bg-[#FC6714] hover:bg-[#E0530A] text-white shadow-xs transition cursor-pointer flex items-center gap-1.5"
            >
              <span v-if="saving" class="inline-block animate-spin mr-1">⟳</span>
              <span>{{ saving ? 'Salvando...' : (editingId ? 'Atualizar Tipo' : 'Criar Tipo') }}</span>
            </button>
          </div>
        </div>
      </Transition>

      <!-- Tabela Confortável de Tipos de Depósito -->
      <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto max-h-[380px]">
          <table class="w-full text-left border-collapse text-xs">
            <thead class="sticky top-0 bg-slate-100/90 dark:bg-slate-900/90 backdrop-blur-xs border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold z-10">
              <tr>
                <th class="py-3 px-4 w-40">Código</th>
                <th class="py-3 px-4">Tipo de Depósito</th>
                <th class="py-3 px-4">Descrição</th>
                <th class="py-3 px-4 w-28 text-center">Depósitos</th>
                <th class="py-3 px-4 text-right w-32">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr v-if="loading">
                <td colspan="5" class="py-8 text-center text-slate-400">
                  <span class="inline-block animate-spin mr-1.5 text-[#FC6714]">⟳</span> Carregando tipos de depósito...
                </td>
              </tr>
              <tr v-else-if="filteredTypes.length === 0">
                <td colspan="5" class="py-8 text-center text-slate-400">
                  Nenhum tipo de depósito encontrado.
                </td>
              </tr>
              <tr
                v-for="t in filteredTypes"
                :key="t.id"
                class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition"
              >
                <!-- Código -->
                <td class="py-3 px-4 font-mono font-bold text-slate-800 dark:text-slate-200">
                  <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[#FC6714] border border-slate-200 dark:border-slate-700">
                    {{ t.code }}
                  </span>
                </td>

                <!-- Nome -->
                <td class="py-3 px-4 font-semibold text-slate-900 dark:text-slate-100">
                  {{ t.name }}
                </td>

                <!-- Descrição -->
                <td class="py-3 px-4 text-slate-600 dark:text-slate-400 truncate max-w-xs">
                  {{ t.description || '-' }}
                </td>

                <!-- Quantidade de Depósitos -->
                <td class="py-3 px-4 text-center font-mono font-semibold text-slate-700 dark:text-slate-300">
                  {{ t.depots_count ?? 0 }}
                </td>

                <!-- Ações -->
                <td class="py-3 px-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <button
                      type="button"
                      @click="editType(t)"
                      class="h-7 px-2.5 rounded text-xs font-medium border border-slate-300 dark:border-slate-700 hover:bg-orange-50 hover:text-[#FC6714] hover:border-[#FC6714] transition cursor-pointer inline-flex items-center gap-1"
                      title="Editar tipo"
                    >
                      <Edit2 class="w-3 h-3" />
                      <span>Editar</span>
                    </button>

                    <button
                      type="button"
                      @click="deleteType(t)"
                      class="h-7 px-2 rounded text-xs font-medium border border-rose-200 dark:border-rose-900/60 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer inline-flex items-center"
                      title="Excluir tipo"
                    >
                      <Trash2 class="w-3 h-3" />
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
          {{ typesList.length }} tipos cadastrados
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
import { Search, Plus, Edit2, Trash2, X, AlertCircle } from 'lucide-vue-next';
import BaseModal from '@/components/common/BaseModal.vue';

const props = defineProps<{
  isOpen: boolean;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'updated'): void;
}>();

const typesList = ref<any[]>([]);
const loading = ref(false);
const searchTerm = ref('');
const isFormOpen = ref(false);
const editingId = ref<number | null>(null);
const saving = ref(false);
const formError = ref('');

const form = ref({
  code: '',
  name: '',
  description: '',
  is_active: true,
});

async function loadTypes() {
  loading.value = true;
  try {
    const res = await fetch('/api/v1/depot-types');
    const json = await res.json();
    typesList.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar tipos de depósito:', err);
  } finally {
    loading.value = false;
  }
}

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      loadTypes();
      closeForm();
    }
  },
  { immediate: true }
);

const filteredTypes = computed(() => {
  if (!searchTerm.value.trim()) return typesList.value;
  const q = searchTerm.value.toLowerCase();
  return typesList.value.filter((t) =>
    t.name.toLowerCase().includes(q) ||
    t.code.toLowerCase().includes(q) ||
    (t.description && t.description.toLowerCase().includes(q))
  );
});

function openCreateForm() {
  editingId.value = null;
  form.value = {
    code: '',
    name: '',
    description: '',
    is_active: true,
  };
  formError.value = '';
  isFormOpen.value = true;
}

function editType(type: any) {
  editingId.value = type.id;
  form.value = {
    code: type.code,
    name: type.name,
    description: type.description || '',
    is_active: Boolean(type.is_active),
  };
  formError.value = '';
  isFormOpen.value = true;
}

function closeForm() {
  isFormOpen.value = false;
  editingId.value = null;
  formError.value = '';
}

async function saveType() {
  if (!form.value.name.trim() || !form.value.code.trim()) {
    formError.value = 'Código e Nome do tipo de depósito são obrigatórios.';
    return;
  }

  saving.value = true;
  formError.value = '';

  const payload = {
    code: form.value.code.trim().toUpperCase(),
    name: form.value.name.trim(),
    description: form.value.description ? form.value.description.trim() : null,
    is_active: Boolean(form.value.is_active),
  };

  try {
    const url = editingId.value ? `/api/v1/depot-types/${editingId.value}` : '/api/v1/depot-types';
    const method = editingId.value ? 'PUT' : 'POST';

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
      formError.value = json.message || 'Erro ao salvar tipo de depósito.';
      return;
    }

    closeForm();
    await loadTypes();
    emit('updated');
  } catch (err: any) {
    formError.value = err.message || 'Erro de comunicação ao salvar tipo de depósito.';
  } finally {
    saving.value = false;
  }
}

async function deleteType(type: any) {
  if (!confirm(`Deseja realmente excluir o tipo de depósito "${type.name}"?`)) return;

  try {
    const res = await fetch(`/api/v1/depot-types/${type.id}`, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
      },
    });

    const json = await res.json();
    if (!res.ok) {
      alert(json.message || 'Erro ao excluir tipo de depósito.');
      return;
    }

    await loadTypes();
    emit('updated');
  } catch (err: any) {
    alert(err.message || 'Erro ao comunicar com o servidor.');
  }
}
</script>
