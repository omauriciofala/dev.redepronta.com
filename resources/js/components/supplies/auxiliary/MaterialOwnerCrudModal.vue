<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="$emit('close')"
    title="Gerenciar Proprietários"
    description="Parametrização de proprietários de materiais e ativos vinculados a pessoas com papel de solicitante"
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
            placeholder="Buscar proprietário por nome, código ou pessoa vinculada..."
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
          <span>Novo Proprietário</span>
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
              <span>{{ editingId ? 'Editar Proprietário' : 'Novo Proprietário' }}</span>
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

          <div class="space-y-3 text-xs">
            <!-- Linha 1: Pessoa com Papel de Solicitante -->
            <div>
              <PersonSearchSelect
                v-model="form.person_id"
                :initial-person-name="initialPersonName"
                role="requester"
                label="Pessoa Vinculada (Papel de Solicitante) *"
                placeholder="Busque a pessoa cadastrada (solicitante)..."
                required
                @select="onPersonSelected"
              />
              <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 block">
                O proprietário é associado obrigatoriamente a uma pessoa com perfil de solicitante no sistema.
              </span>
            </div>

            <!-- Linha 2: Código e Nome de Exibição -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                  Código / Sigla *
                </label>
                <input
                  v-model="form.code"
                  type="text"
                  placeholder="Ex: PROP-VIVO, CLARO"
                  class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#FC6714] outline-none uppercase"
                />
              </div>

              <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                  Nome do Proprietário *
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  placeholder="Ex: Telefônica Brasil / Vivo ou Nome da Operadora"
                  class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] outline-none"
                />
              </div>
            </div>

            <!-- Linha 3: Descrição e Detalhes Contratuais -->
            <div>
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Descrição / Observações de Comodato e Custódia
              </label>
              <input
                v-model="form.description"
                type="text"
                placeholder="Ex: Contrato de custódia e fornecimento de ONUs para ativação de clientes..."
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
              <span class="font-medium">Proprietário ativo para operações e movimentações</span>
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
                @click="saveOwner"
                class="px-4 py-1.5 rounded-lg text-xs font-semibold bg-[#FC6714] hover:bg-[#E0530A] text-white shadow-xs transition cursor-pointer flex items-center gap-1.5"
              >
                <span v-if="saving" class="inline-block animate-spin mr-1">⟳</span>
                <span>{{ saving ? 'Salvando...' : (editingId ? 'Atualizar Proprietário' : 'Criar Proprietário') }}</span>
              </button>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Tabela Confortável de Proprietários -->
      <div class="border border-slate-200 dark:border-[#14147A] rounded-xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto max-h-[380px]">
          <table class="w-full text-left border-collapse text-xs">
            <thead class="sticky top-0 bg-slate-100/90 dark:bg-[#03032E]/90 backdrop-blur-xs border-b border-slate-200 dark:border-[#14147A] text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold z-10">
              <tr>
                <th class="py-3 px-4 w-36">Código</th>
                <th class="py-3 px-4">Proprietário</th>
                <th class="py-3 px-4">Pessoa Vinculada (Solicitante)</th>
                <th class="py-3 px-4">Descrição</th>
                <th class="py-3 px-4 w-24 text-center">Status</th>
                <th class="py-3 px-4 text-right w-28">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <tr v-if="loading">
                <td colspan="6" class="py-8 text-center text-slate-400">
                  <span class="inline-block animate-spin mr-1.5 text-[#FC6714]">⟳</span> Carregando proprietários...
                </td>
              </tr>
              <tr v-else-if="filteredOwners.length === 0">
                <td colspan="6" class="py-8 text-center text-slate-400">
                  Nenhum proprietário encontrado.
                </td>
              </tr>
              <tr
                v-for="owner in filteredOwners"
                :key="owner.id"
                class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition group"
              >
                <td class="py-3 px-4 font-mono font-bold text-slate-900 dark:text-slate-100">
                  <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                    {{ owner.code }}
                  </span>
                </td>
                <td class="py-3 px-4 font-semibold text-slate-800 dark:text-slate-200">
                  {{ owner.name }}
                </td>
                <td class="py-3 px-4">
                  <div v-if="owner.person" class="space-y-0.5">
                    <div class="flex items-center gap-1.5">
                      <span class="font-medium text-slate-900 dark:text-slate-100">
                        {{ owner.person.name }}
                      </span>
                      <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                        Solicitante
                      </span>
                    </div>
                    <div v-if="owner.person.document_number" class="text-[11px] font-mono text-slate-400">
                      {{ owner.person.document_number }}
                    </div>
                  </div>
                  <span v-else class="text-slate-400 italic">Não vinculado</span>
                </td>
                <td class="py-3 px-4 text-slate-600 dark:text-slate-300 max-w-[200px] truncate">
                  {{ owner.description || '—' }}
                </td>
                <td class="py-3 px-4 text-center">
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold"
                    :class="owner.is_active
                      ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800'
                      : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700'"
                  >
                    {{ owner.is_active ? 'Ativo' : 'Inativo' }}
                  </span>
                </td>
                <td class="py-3 px-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <!-- Botão Gerenciar Catálogo de Materiais -->
                    <button
                      type="button"
                      @click="openCatalogModal(owner)"
                      class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-orange-50 dark:bg-[#FC6714]/15 hover:bg-[#FC6714] hover:text-white text-[#FC6714] dark:text-orange-400 font-semibold transition cursor-pointer text-xs"
                      title="Gerenciar Catálogo de Materiais e Códigos deste Proprietário"
                    >
                      <BookOpen class="w-3.5 h-3.5" />
                      <span>Catálogo</span>
                      <span
                        v-if="owner.owner_materials_count"
                        class="ml-0.5 px-1.5 py-0.2 rounded-full text-[10px] bg-[#FC6714]/20 text-[#FC6714]"
                      >
                        {{ owner.owner_materials_count }}
                      </span>
                    </button>

                    <button
                      type="button"
                      @click="openEditForm(owner)"
                      class="p-1.5 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 hover:text-[#FC6714] transition cursor-pointer"
                      title="Editar proprietário"
                    >
                      <Edit2 class="w-3.5 h-3.5" />
                    </button>
                    <button
                      type="button"
                      @click="deleteOwner(owner)"
                      :disabled="deletingId === owner.id"
                      class="p-1.5 rounded-md hover:bg-rose-50 dark:hover:bg-rose-950/50 text-slate-400 hover:text-rose-600 transition cursor-pointer disabled:opacity-50"
                      title="Excluir proprietário"
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
          {{ owners.length }} {{ owners.length === 1 ? 'proprietário cadastrado' : 'proprietários cadastrados' }}
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

  <!-- Modal do Catálogo de Materiais do Proprietário -->
  <OwnerMaterialCatalogModal
    :is-open="isCatalogModalOpen"
    :owner="selectedOwnerForCatalog"
    @close="isCatalogModalOpen = false"
    @updated="onCatalogUpdated"
  />
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
  BookOpen,
} from 'lucide-vue-next';
import BaseModal from '@/components/common/BaseModal.vue';
import PersonSearchSelect from '@/components/common/PersonSearchSelect.vue';
import OwnerMaterialCatalogModal from '@/components/supplies/auxiliary/OwnerMaterialCatalogModal.vue';

export interface OwnerItem {
  id: number;
  person_id: number;
  code: string;
  name: string;
  description?: string;
  is_active: boolean;
  owner_materials_count?: number;
  person?: {
    id: number;
    name: string;
    document_number?: string;
    is_requester?: boolean;
    role?: string;
  };
}

const props = defineProps<{
  isOpen: boolean;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'updated'): void;
}>();

const owners = ref<OwnerItem[]>([]);
const loading = ref(false);
const saving = ref(false);
const deletingId = ref<number | null>(null);
const searchTerm = ref('');
const isFormOpen = ref(false);
const editingId = ref<number | null>(null);
const formError = ref('');
const deleteError = ref('');
const initialPersonName = ref('');

// Gerenciamento de Catálogo do Proprietário
const isCatalogModalOpen = ref(false);
const selectedOwnerForCatalog = ref<OwnerItem | null>(null);

function openCatalogModal(owner: OwnerItem) {
  selectedOwnerForCatalog.value = owner;
  isCatalogModalOpen.value = true;
}

function onCatalogUpdated() {
  loadOwners();
  emit('updated');
}

const form = ref({
  person_id: null as number | null,
  code: '',
  name: '',
  description: '',
  is_active: true,
});

const filteredOwners = computed(() => {
  if (!searchTerm.value.trim()) return owners.value;
  const term = searchTerm.value.toLowerCase().trim();
  return owners.value.filter(
    (o) =>
      o.name.toLowerCase().includes(term) ||
      o.code.toLowerCase().includes(term) ||
      (o.description && o.description.toLowerCase().includes(term)) ||
      (o.person && o.person.name.toLowerCase().includes(term)) ||
      (o.person && o.person.document_number && o.person.document_number.includes(term))
  );
});

async function loadOwners() {
  loading.value = true;
  deleteError.value = '';
  try {
    const res = await axios.get('/api/v1/material-owners');
    owners.value = res.data.data || [];
  } catch (err: any) {
    console.error('Erro ao carregar proprietários:', err);
  } finally {
    loading.value = false;
  }
}

function onPersonSelected(person: any) {
  if (person) {
    if (!form.value.name.trim()) {
      form.value.name = person.name;
    }
    if (!form.value.code.trim()) {
      // Sugere um código inicial com base no nome
      const clean = person.name
        .toUpperCase()
        .replace(/[^A-Z0-9]/g, '')
        .substring(0, 8);
      form.value.code = clean ? `PROP-${clean}` : 'PROP-01';
    }
  }
}

function openCreateForm() {
  editingId.value = null;
  initialPersonName.value = '';
  form.value = {
    person_id: null,
    code: '',
    name: '',
    description: '',
    is_active: true,
  };
  formError.value = '';
  isFormOpen.value = true;
}

function openEditForm(o: OwnerItem) {
  editingId.value = o.id;
  initialPersonName.value = o.person ? o.person.name : '';
  form.value = {
    person_id: o.person_id,
    code: o.code,
    name: o.name,
    description: o.description || '',
    is_active: Boolean(o.is_active),
  };
  formError.value = '';
  isFormOpen.value = true;
}

function closeForm() {
  isFormOpen.value = false;
  editingId.value = null;
  formError.value = '';
  initialPersonName.value = '';
}

async function saveOwner() {
  if (!form.value.person_id) {
    formError.value = 'Selecione uma pessoa com papel de solicitante.';
    return;
  }
  if (!form.value.code.trim()) {
    formError.value = 'O código identificador do proprietário é obrigatório.';
    return;
  }
  if (!form.value.name.trim()) {
    formError.value = 'O nome ou razão social do proprietário é obrigatório.';
    return;
  }

  saving.value = true;
  formError.value = '';

  try {
    const payload = {
      person_id: form.value.person_id,
      code: form.value.code.trim().toUpperCase(),
      name: form.value.name.trim(),
      description: form.value.description ? form.value.description.trim() : null,
      is_active: form.value.is_active,
    };

    if (editingId.value) {
      await axios.put(`/api/v1/material-owners/${editingId.value}`, payload);
    } else {
      await axios.post('/api/v1/material-owners', payload);
    }

    closeForm();
    await loadOwners();
    emit('updated');
  } catch (err: any) {
    formError.value =
      err.response?.data?.message ||
      'Erro ao salvar proprietário. Verifique se o código já existe.';
  } finally {
    saving.value = false;
  }
}

async function deleteOwner(o: OwnerItem) {
  deleteError.value = '';

  if (!confirm(`Deseja realmente excluir o proprietário "${o.code} — ${o.name}"?`)) {
    return;
  }

  deletingId.value = o.id;
  try {
    await axios.delete(`/api/v1/material-owners/${o.id}`);
    await loadOwners();
    emit('updated');
  } catch (err: any) {
    deleteError.value =
      err.response?.data?.message || 'Erro ao excluir proprietário.';
  } finally {
    deletingId.value = null;
  }
}

watch(
  () => props.isOpen,
  (open) => {
    if (open) {
      closeForm();
      loadOwners();
    }
  }
);
</script>
