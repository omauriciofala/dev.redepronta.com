<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="$emit('close')"
    title="Gerenciar Categorias de Material"
    description="Parametrização de famílias, grupos e classificações de materiais e equipamentos no estoque"
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
            placeholder="Buscar categoria por nome, código ou descrição..."
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
          <span>Nova Categoria</span>
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
              <span>{{ editingId ? 'Editar Categoria de Material' : 'Nova Categoria de Material' }}</span>
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
            <div class="sm:col-span-2">
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Nome da Categoria *
              </label>
              <input
                v-model="form.name"
                type="text"
                placeholder="Ex: Cabos & Fibras Ópticas, Ferramentas, Ativos..."
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] outline-none"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Código / Sigla
              </label>
              <input
                v-model="form.code"
                type="text"
                placeholder="Ex: CABOS_FIBRAS"
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#FC6714] outline-none uppercase"
              />
            </div>

            <div class="sm:col-span-3">
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Descrição da Categoria
              </label>
              <input
                v-model="form.description"
                type="text"
                placeholder="Ex: Materiais destinados à infraestrutura óptica, cabos e tubos..."
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] outline-none"
              />
            </div>

            <div class="sm:col-span-3">
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Cor Visual de Destaque
              </label>
              <div class="flex items-center gap-2 flex-wrap">
                <button
                  v-for="color in colorPalette"
                  :key="color.hex"
                  type="button"
                  @click="form.color = color.hex"
                  class="w-7 h-7 rounded-lg border-2 transition cursor-pointer flex items-center justify-center"
                  :style="{ backgroundColor: color.hex }"
                  :class="form.color === color.hex ? 'border-slate-900 dark:border-white scale-110 shadow-sm' : 'border-transparent opacity-80 hover:opacity-100'"
                  :title="color.label"
                >
                  <span v-if="form.color === color.hex" class="text-white text-[10px] font-bold">✓</span>
                </button>
                <div class="flex items-center gap-2 ml-2">
                  <input
                    type="color"
                    v-model="form.color"
                    class="w-7 h-7 rounded cursor-pointer border-0 p-0"
                  />
                  <span class="font-mono text-slate-500 uppercase">{{ form.color }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between pt-2">
            <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300 cursor-pointer select-none">
              <input
                type="checkbox"
                v-model="form.is_active"
                class="rounded border-slate-300 dark:border-slate-700 text-[#FC6714] focus:ring-[#FC6714]"
              />
              <span class="font-medium">Categoria ativa para seleção nos materiais</span>
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
                @click="saveCategory"
                class="px-4 py-1.5 rounded-lg text-xs font-semibold bg-[#FC6714] hover:bg-[#E0530A] text-white shadow-xs transition cursor-pointer flex items-center gap-1.5"
              >
                <span v-if="saving" class="inline-block animate-spin mr-1">⟳</span>
                <span>{{ saving ? 'Salvando...' : (editingId ? 'Atualizar Categoria' : 'Criar Categoria') }}</span>
              </button>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Tabela Confortável de Categorias -->
      <div class="border border-slate-200 dark:border-[#14147A] rounded-xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto max-h-[380px]">
          <table class="w-full text-left border-collapse text-xs">
            <thead class="sticky top-0 bg-slate-100/90 dark:bg-[#03032E]/90 backdrop-blur-xs border-b border-slate-200 dark:border-[#14147A] text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold z-10">
              <tr>
                <th class="py-3 px-4 w-48">Categoria</th>
                <th class="py-3 px-4 w-36">Código</th>
                <th class="py-3 px-4">Descrição</th>
                <th class="py-3 px-4 w-28 text-center">Materiais</th>
                <th class="py-3 px-4 w-24 text-center">Status</th>
                <th class="py-3 px-4 text-right w-28">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <tr v-if="loading">
                <td colspan="6" class="py-8 text-center text-slate-400">
                  <span class="inline-block animate-spin mr-1.5 text-[#FC6714]">⟳</span> Carregando categorias de material...
                </td>
              </tr>
              <tr v-else-if="filteredCategories.length === 0">
                <td colspan="6" class="py-8 text-center text-slate-400">
                  Nenhuma categoria de material encontrada.
                </td>
              </tr>
              <tr
                v-for="cat in filteredCategories"
                :key="cat.id"
                class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition group"
              >
                <td class="py-3 px-4">
                  <div class="flex items-center gap-2">
                    <span
                      class="w-3 h-3 rounded-full shrink-0 shadow-xs"
                      :style="{ backgroundColor: cat.color || '#FC6714' }"
                    ></span>
                    <span class="font-bold text-slate-900 dark:text-slate-100">
                      {{ cat.name }}
                    </span>
                  </div>
                </td>
                <td class="py-3 px-4 font-mono text-slate-500 dark:text-slate-400">
                  {{ cat.code || '—' }}
                </td>
                <td class="py-3 px-4 text-slate-600 dark:text-slate-300 max-w-[240px] truncate">
                  {{ cat.description || '—' }}
                </td>
                <td class="py-3 px-4 text-center">
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium"
                    :class="(cat.materials_count ?? 0) > 0 ? 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'"
                  >
                    {{ cat.materials_count ?? 0 }} itens
                  </span>
                </td>
                <td class="py-3 px-4 text-center">
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold"
                    :class="cat.is_active
                      ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800'
                      : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700'"
                  >
                    {{ cat.is_active ? 'Ativo' : 'Inativo' }}
                  </span>
                </td>
                <td class="py-3 px-4 text-right">
                  <div class="flex items-center justify-end gap-1">
                    <button
                      type="button"
                      @click="openEditForm(cat)"
                      class="p-1.5 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-[#FC6714] transition cursor-pointer"
                      title="Editar categoria"
                    >
                      <Edit2 class="w-3.5 h-3.5" />
                    </button>
                    <button
                      type="button"
                      @click="deleteCategory(cat)"
                      :disabled="deletingId === cat.id"
                      class="p-1.5 rounded-md hover:bg-rose-50 dark:hover:bg-rose-950/50 text-slate-400 hover:text-rose-600 transition cursor-pointer disabled:opacity-50"
                      title="Excluir categoria"
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
          {{ categories.length }} {{ categories.length === 1 ? 'categoria cadastrada' : 'categorias cadastradas' }}
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

export interface CategoryItem {
  id: number;
  name: string;
  code?: string;
  description?: string;
  color?: string;
  is_active: boolean;
  materials_count?: number;
}

const colorPalette = [
  { hex: '#FC6714', label: 'Laranja Primário' },
  { hex: '#3B82F6', label: 'Azul' },
  { hex: '#10B981', label: 'Verde Esmeralda' },
  { hex: '#8B5CF6', label: 'Roxo' },
  { hex: '#F59E0B', label: 'Âmbar' },
  { hex: '#EC4899', label: 'Rosa' },
  { hex: '#6B7280', label: 'Cinza Neutro' },
];

const props = defineProps<{
  isOpen: boolean;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'updated'): void;
}>();

const categories = ref<CategoryItem[]>([]);
const loading = ref(false);
const saving = ref(false);
const deletingId = ref<number | null>(null);
const searchTerm = ref('');
const isFormOpen = ref(false);
const editingId = ref<number | null>(null);
const formError = ref('');
const deleteError = ref('');

const form = ref({
  name: '',
  code: '',
  description: '',
  color: '#FC6714',
  is_active: true,
});

const filteredCategories = computed(() => {
  if (!searchTerm.value.trim()) return categories.value;
  const term = searchTerm.value.toLowerCase().trim();
  return categories.value.filter(
    (c) =>
      c.name.toLowerCase().includes(term) ||
      (c.code && c.code.toLowerCase().includes(term)) ||
      (c.description && c.description.toLowerCase().includes(term))
  );
});

async function loadCategories() {
  loading.value = true;
  deleteError.value = '';
  try {
    const res = await axios.get('/api/v1/material-categories');
    categories.value = res.data.data || [];
  } catch (err: any) {
    console.error('Erro ao carregar categorias de material:', err);
  } finally {
    loading.value = false;
  }
}

function openCreateForm() {
  editingId.value = null;
  form.value = {
    name: '',
    code: '',
    description: '',
    color: '#FC6714',
    is_active: true,
  };
  formError.value = '';
  isFormOpen.value = true;
}

function openEditForm(c: CategoryItem) {
  editingId.value = c.id;
  form.value = {
    name: c.name,
    code: c.code || '',
    description: c.description || '',
    color: c.color || '#FC6714',
    is_active: Boolean(c.is_active),
  };
  formError.value = '';
  isFormOpen.value = true;
}

function closeForm() {
  isFormOpen.value = false;
  editingId.value = null;
  formError.value = '';
}

async function saveCategory() {
  if (!form.value.name.trim()) {
    formError.value = 'O nome da categoria de material é obrigatório.';
    return;
  }

  saving.value = true;
  formError.value = '';

  try {
    const payload = {
      name: form.value.name.trim(),
      code: form.value.code ? form.value.code.trim().toUpperCase() : null,
      description: form.value.description ? form.value.description.trim() : null,
      color: form.value.color || '#FC6714',
      is_active: form.value.is_active,
    };

    if (editingId.value) {
      await axios.put(`/api/v1/material-categories/${editingId.value}`, payload);
    } else {
      await axios.post('/api/v1/material-categories', payload);
    }

    closeForm();
    await loadCategories();
    emit('updated');
  } catch (err: any) {
    formError.value =
      err.response?.data?.message ||
      'Erro ao salvar categoria de material. Verifique se o nome já existe.';
  } finally {
    saving.value = false;
  }
}

async function deleteCategory(c: CategoryItem) {
  deleteError.value = '';

  if ((c.materials_count ?? 0) > 0) {
    deleteError.value = `Não é possível excluir a categoria "${c.name}" pois existem ${c.materials_count} materiais associados no catálogo. Inative a categoria ou reclassifique os materiais.`;
    return;
  }

  if (!confirm(`Deseja realmente excluir a categoria "${c.name}"?`)) {
    return;
  }

  deletingId.value = c.id;
  try {
    await axios.delete(`/api/v1/material-categories/${c.id}`);
    await loadCategories();
    emit('updated');
  } catch (err: any) {
    deleteError.value =
      err.response?.data?.message || 'Erro ao excluir categoria de material.';
  } finally {
    deletingId.value = null;
  }
}

watch(
  () => props.isOpen,
  (open) => {
    if (open) {
      closeForm();
      loadCategories();
    }
  }
);
</script>
