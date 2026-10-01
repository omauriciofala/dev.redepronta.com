<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="$emit('close')"
    title="Gerenciar Unidades de Medida"
    description="Parametrização de unidades de estoque, fracionamento e movimentação de materiais"
    size="lg"
  >
    <div class="space-y-4">
      <!-- Barra Superior: Busca e Botão Novo -->
      <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="relative flex-1 min-w-[220px]">
          <Search class="w-4 h-4 absolute left-3 top-3 text-slate-400 pointer-events-none" />
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Buscar por sigla ou nome da unidade..."
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
          <span>Nova Unidade</span>
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
              <span>{{ editingId ? 'Editar Unidade de Medida' : 'Nova Unidade de Medida' }}</span>
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
                Sigla / Código *
              </label>
              <input
                v-model="form.code"
                type="text"
                maxlength="10"
                placeholder="Ex: UND, MT, CX"
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#FC6714] outline-none uppercase"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Nome por Extenso *
              </label>
              <input
                v-model="form.name"
                type="text"
                placeholder="Ex: Unidade, Metro, Caixa"
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] outline-none"
              />
            </div>
          </div>

          <div class="flex items-center justify-between pt-2">
            <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300 cursor-pointer select-none">
              <input
                type="checkbox"
                v-model="form.is_active"
                class="rounded border-slate-300 dark:border-slate-700 text-[#FC6714] focus:ring-[#FC6714]"
              />
              <span class="font-medium">Unidade ativa para novos cadastros</span>
            </label>

            <div class="flex items-center gap-2">
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
                @click="saveUnit"
                class="px-4 py-1.5 rounded-lg text-xs font-semibold bg-[#FC6714] hover:bg-[#E0530A] text-white shadow-xs transition cursor-pointer flex items-center gap-1.5"
              >
                <span v-if="saving" class="inline-block animate-spin mr-1">⟳</span>
                <span>{{ saving ? 'Salvando...' : (editingId ? 'Atualizar Unidade' : 'Criar Unidade') }}</span>
              </button>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Tabela Confortável de Unidades -->
      <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto max-h-[380px]">
          <table class="w-full text-left border-collapse text-xs">
            <thead class="sticky top-0 bg-slate-100/90 dark:bg-slate-900/90 backdrop-blur-xs border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold z-10">
              <tr>
                <th class="py-3 px-4 w-28">Sigla</th>
                <th class="py-3 px-4">Nome por Extenso</th>
                <th class="py-3 px-4 w-32 text-center">Materiais</th>
                <th class="py-3 px-4 w-24 text-center">Status</th>
                <th class="py-3 px-4 text-right w-28">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr v-if="loading">
                <td colspan="5" class="py-8 text-center text-slate-400">
                  <span class="inline-block animate-spin mr-1.5 text-[#FC6714]">⟳</span> Carregando unidades de medida...
                </td>
              </tr>
              <tr v-else-if="filteredUnits.length === 0">
                <td colspan="5" class="py-8 text-center text-slate-400">
                  Nenhuma unidade de medida encontrada.
                </td>
              </tr>
              <tr
                v-for="u in filteredUnits"
                :key="u.id"
                class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition group"
              >
                <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-slate-100">
                  <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                    {{ u.code }}
                  </span>
                </td>
                <td class="py-3 px-4 font-medium text-slate-800 dark:text-slate-200">
                  {{ u.name }}
                </td>
                <td class="py-3 px-4 text-center">
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium"
                    :class="(u.materials_count ?? 0) > 0 ? 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'"
                  >
                    {{ u.materials_count ?? 0 }} itens
                  </span>
                </td>
                <td class="py-3 px-4 text-center">
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold"
                    :class="u.is_active
                      ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800'
                      : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700'"
                  >
                    {{ u.is_active ? 'Ativo' : 'Inativo' }}
                  </span>
                </td>
                <td class="py-3 px-4 text-right">
                  <div class="flex items-center justify-end gap-1">
                    <button
                      type="button"
                      @click="openEditForm(u)"
                      class="p-1.5 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-[#FC6714] transition cursor-pointer"
                      title="Editar unidade"
                    >
                      <Edit2 class="w-3.5 h-3.5" />
                    </button>
                    <button
                      type="button"
                      @click="deleteUnit(u)"
                      :disabled="deletingId === u.id"
                      class="p-1.5 rounded-md hover:bg-rose-50 dark:hover:bg-rose-950/50 text-slate-400 hover:text-rose-600 transition cursor-pointer disabled:opacity-50"
                      title="Excluir unidade"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Feedback de erro de exclusão -->
      <div v-if="deleteError" class="p-3 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 text-xs flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
          <AlertTriangle class="w-4 h-4 shrink-0 text-amber-600" />
          <span>{{ deleteError }}</span>
        </div>
        <button type="button" @click="deleteError = ''" class="text-amber-600 hover:text-amber-800 text-xs font-bold">
          ✕
        </button>
      </div>
    </div>

    <template #footer>
      <div class="flex items-center justify-between w-full">
        <span class="text-xs text-slate-500 dark:text-slate-400">
          {{ units.length }} {{ units.length === 1 ? 'unidade cadastrada' : 'unidades cadastradas' }}
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
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import {
  Search,
  Plus,
  Edit2,
  Trash2,
  X,
  AlertCircle,
  AlertTriangle,
} from 'lucide-vue-next';
import BaseModal from '@/components/common/BaseModal.vue';

export interface UnitItem {
  id: number;
  code: string;
  name: string;
  is_active: boolean;
  materials_count?: number;
}

const props = defineProps<{
  isOpen: boolean;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'updated'): void;
}>();

const units = ref<UnitItem[]>([]);
const loading = ref(false);
const saving = ref(false);
const deletingId = ref<number | null>(null);
const searchTerm = ref('');
const isFormOpen = ref(false);
const editingId = ref<number | null>(null);
const formError = ref('');
const deleteError = ref('');

const form = ref({
  code: '',
  name: '',
  is_active: true,
});

const filteredUnits = computed(() => {
  if (!searchTerm.value.trim()) return units.value;
  const term = searchTerm.value.toLowerCase().trim();
  return units.value.filter(
    (u) =>
      u.name.toLowerCase().includes(term) ||
      u.code.toLowerCase().includes(term)
  );
});

async function loadUnits() {
  loading.value = true;
  deleteError.value = '';
  try {
    const res = await axios.get('/api/v1/units');
    units.value = res.data.data || [];
  } catch (err: any) {
    console.error('Erro ao carregar unidades de medida:', err);
  } finally {
    loading.value = false;
  }
}

function openCreateForm() {
  editingId.value = null;
  form.value = {
    code: '',
    name: '',
    is_active: true,
  };
  formError.value = '';
  isFormOpen.value = true;
}

function openEditForm(u: UnitItem) {
  editingId.value = u.id;
  form.value = {
    code: u.code,
    name: u.name,
    is_active: Boolean(u.is_active),
  };
  formError.value = '';
  isFormOpen.value = true;
}

function closeForm() {
  isFormOpen.value = false;
  editingId.value = null;
  formError.value = '';
}

async function saveUnit() {
  if (!form.value.code.trim() || !form.value.name.trim()) {
    formError.value = 'Preencha a sigla e o nome por extenso da unidade.';
    return;
  }

  saving.value = true;
  formError.value = '';

  try {
    const payload = {
      code: form.value.code.trim().toUpperCase(),
      name: form.value.name.trim(),
      is_active: form.value.is_active,
    };

    if (editingId.value) {
      await axios.put(`/api/v1/units/${editingId.value}`, payload);
    } else {
      await axios.post('/api/v1/units', payload);
    }

    closeForm();
    await loadUnits();
    emit('updated');
  } catch (err: any) {
    formError.value =
      err.response?.data?.message ||
      'Erro ao salvar unidade de medida. Verifique se o código já está em uso.';
  } finally {
    saving.value = false;
  }
}

async function deleteUnit(u: UnitItem) {
  deleteError.value = '';

  if ((u.materials_count ?? 0) > 0) {
    deleteError.value = `Não é possível excluir a unidade "${u.code}" pois existem ${u.materials_count} materiais associados no catálogo. Inative a unidade se desejar descontinuar seu uso.`;
    return;
  }

  if (!confirm(`Deseja realmente excluir a unidade "${u.code} — ${u.name}"?`)) {
    return;
  }

  deletingId.value = u.id;
  try {
    await axios.delete(`/api/v1/units/${u.id}`);
    await loadUnits();
    emit('updated');
  } catch (err: any) {
    deleteError.value =
      err.response?.data?.message || 'Erro ao excluir unidade de medida.';
  } finally {
    deletingId.value = null;
  }
}

watch(
  () => props.isOpen,
  (open) => {
    if (open) {
      closeForm();
      loadUnits();
    }
  }
);
</script>
