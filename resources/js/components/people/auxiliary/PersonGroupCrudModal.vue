<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="$emit('close')"
    title="Gerenciar Grupos de Pessoas"
    description="Crie e organize categorias para classificar pessoas e parceiros no sistema"
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
            placeholder="Buscar grupo por nome ou descrição..."
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
          <span>Novo Grupo</span>
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
              <span>{{ editingId ? 'Editar Grupo' : 'Novo Grupo' }}</span>
            </h4>
            <button
              type="button"
              @click="closeForm"
              class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1"
            >
              <X class="w-4 h-4" />
            </button>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div class="sm:col-span-2">
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Nome do Grupo *
              </label>
              <input
                v-model="form.name"
                type="text"
                placeholder="Ex: Clientes VIP, Prestadores Nível 1"
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
                Descrição (Opcional)
              </label>
              <input
                v-model="form.description"
                type="text"
                placeholder="Breve descrição da finalidade deste grupo..."
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] outline-none"
              />
            </div>

            <div class="flex items-center gap-2 pt-6">
              <input
                id="group_is_active"
                v-model="form.is_active"
                type="checkbox"
                class="w-4 h-4 rounded text-[#FC6714] focus:ring-[#FC6714] border-slate-300 dark:border-slate-700"
              />
              <label for="group_is_active" class="font-semibold text-slate-700 dark:text-slate-300 select-none cursor-pointer">
                Grupo Ativo
              </label>
            </div>
          </div>

          <div v-if="formError" class="text-xs font-medium text-red-600 dark:text-red-400">
            {{ formError }}
          </div>

          <div class="flex items-center justify-end gap-2 pt-1">
            <button
              type="button"
              @click="closeForm"
              class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-medium text-slate-700 dark:text-slate-300 transition cursor-pointer"
            >
              Cancelar
            </button>
            <button
              type="button"
              :disabled="saving"
              @click="saveGroup"
              class="px-4 py-1.5 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] text-white text-xs font-semibold shadow-xs disabled:opacity-50 transition cursor-pointer flex items-center gap-1.5"
            >
              <span v-if="saving" class="animate-spin">⟳</span>
              <span>{{ editingId ? 'Atualizar' : 'Salvar' }}</span>
            </button>
          </div>
        </div>
      </Transition>

      <!-- Mensagem Geral de Feedback -->
      <div
        v-if="feedbackMessage"
        class="p-2.5 rounded-lg text-xs font-medium"
        :class="feedbackType === 'success' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200' : 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300 border border-red-200'"
      >
        {{ feedbackMessage }}
      </div>

      <!-- Tabela de Grupos -->
      <div class="border border-slate-200 dark:border-[#14147A] rounded-xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto max-h-[380px]">
          <table class="w-full text-left border-collapse text-xs">
            <thead class="sticky top-0 bg-slate-100/90 dark:bg-[#03032E]/90 backdrop-blur-xs border-b border-slate-200 dark:border-[#14147A] text-slate-600 dark:text-slate-300 uppercase tracking-wider font-bold z-10">
              <tr>
                <th class="py-2.5 px-4 w-40">Grupo</th>
                <th class="py-2.5 px-4">Descrição</th>
                <th class="py-2.5 px-4 w-28 text-center">Integrantes</th>
                <th class="py-2.5 px-4 w-24 text-center">Status</th>
                <th class="py-2.5 px-4 text-right w-28">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <tr v-if="loading">
                <td colspan="5" class="py-8 text-center text-slate-400">
                  <span class="inline-block animate-spin mr-1.5 text-[#FC6714]">⟳</span> Carregando grupos...
                </td>
              </tr>
              <tr v-else-if="filteredGroups.length === 0">
                <td colspan="5" class="py-8 text-center text-slate-400">
                  Nenhum grupo encontrado.
                </td>
              </tr>
              <tr
                v-for="grp in filteredGroups"
                :key="grp.id"
                class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition"
              >
                <td class="py-2.5 px-4">
                  <div class="flex items-center gap-2">
                    <span
                      class="w-3 h-3 rounded-full shrink-0 border border-black/10 shadow-2xs"
                      :style="{ backgroundColor: grp.color || '#FC6714' }"
                    />
                    <span class="font-bold text-slate-900 dark:text-slate-100">
                      {{ grp.name }}
                    </span>
                  </div>
                </td>
                <td class="py-2.5 px-4 text-slate-600 dark:text-slate-400">
                  {{ grp.description || '-' }}
                </td>
                <td class="py-2.5 px-4 text-center">
                  <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                    {{ grp.people_count ?? 0 }} pessoas
                  </span>
                </td>
                <td class="py-2.5 px-4 text-center">
                  <span
                    class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                    :class="grp.is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'"
                  >
                    {{ grp.is_active ? 'Ativo' : 'Inativo' }}
                  </span>
                </td>
                <td class="py-2.5 px-4 text-right">
                  <div class="flex items-center justify-end gap-1">
                    <button
                      type="button"
                      @click="openEditForm(grp)"
                      class="p-1.5 rounded-lg text-slate-500 hover:text-[#FC6714] hover:bg-orange-50 dark:hover:bg-white/10 transition cursor-pointer"
                      title="Editar grupo"
                    >
                      <Edit2 class="w-3.5 h-3.5" />
                    </button>
                    <button
                      type="button"
                      @click="confirmDelete(grp)"
                      class="p-1.5 rounded-lg text-slate-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                      title="Excluir grupo"
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

      <!-- Diálogo Modal de Confirmação de Exclusão -->
      <Transition
        enter-active-class="transition duration-100 ease-out"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition duration-75 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
      >
        <div
          v-if="groupToDelete"
          class="p-3.5 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/60 flex items-center justify-between gap-3 text-xs"
        >
          <div class="flex items-center gap-2 text-red-800 dark:text-red-200 font-medium">
            <AlertCircle class="w-4 h-4 shrink-0 text-red-600 dark:text-red-400" />
            <span>Confirmar exclusão do grupo <strong>{{ groupToDelete.name }}</strong>?</span>
          </div>
          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="groupToDelete = null"
              class="px-2.5 py-1 rounded bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 transition cursor-pointer"
            >
              Não
            </button>
            <button
              type="button"
              :disabled="deleting"
              @click="deleteGroup"
              class="px-2.5 py-1 rounded bg-red-600 hover:bg-red-700 text-white font-semibold shadow-xs disabled:opacity-50 transition cursor-pointer"
            >
              <span v-if="deleting">Excluindo...</span>
              <span v-else>Sim, excluir</span>
            </button>
          </div>
        </div>
      </Transition>
    </div>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue';
import axios from 'axios';
import { Search, Plus, Edit2, Trash2, X, AlertCircle } from 'lucide-vue-next';
import BaseModal from '../../common/BaseModal.vue';

const props = defineProps<{
  isOpen: boolean;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'updated'): void;
}>();

const groups = ref<any[]>([]);
const loading = ref(false);
const saving = ref(false);
const deleting = ref(false);
const searchTerm = ref('');
const isFormOpen = ref(false);
const editingId = ref<number | null>(null);
const formError = ref('');
const feedbackMessage = ref('');
const feedbackType = ref<'success' | 'error'>('success');
const groupToDelete = ref<any | null>(null);

const form = reactive({
  name: '',
  description: '',
  color: '#FC6714',
  is_active: true,
});

const filteredGroups = computed(() => {
  if (!searchTerm.value) return groups.value;
  const t = searchTerm.value.toLowerCase();
  return groups.value.filter(
    g => g.name?.toLowerCase().includes(t) || g.description?.toLowerCase().includes(t)
  );
});

const loadGroups = async () => {
  loading.value = true;
  try {
    const res = await axios.get('/api/v1/person-groups');
    groups.value = res.data.data || [];
  } catch (err: any) {
    console.error('Erro ao carregar grupos', err);
  } finally {
    loading.value = false;
  }
};

const openCreateForm = () => {
  editingId.value = null;
  form.name = '';
  form.description = '';
  form.color = '#FC6714';
  form.is_active = true;
  formError.value = '';
  isFormOpen.value = true;
};

const openEditForm = (grp: any) => {
  editingId.value = grp.id;
  form.name = grp.name;
  form.description = grp.description || '';
  form.color = grp.color || '#FC6714';
  form.is_active = Boolean(grp.is_active);
  formError.value = '';
  isFormOpen.value = true;
};

const closeForm = () => {
  isFormOpen.value = false;
  editingId.value = null;
  formError.value = '';
};

const saveGroup = async () => {
  if (!form.name.trim()) {
    formError.value = 'O nome do grupo é obrigatório.';
    return;
  }

  saving.value = true;
  formError.value = '';
  try {
    const payload = {
      name: form.name.trim(),
      description: form.description ? form.description.trim() : null,
      color: form.color || '#FC6714',
      is_active: Boolean(form.is_active),
    };

    if (editingId.value) {
      await axios.put(`/api/v1/person-groups/${editingId.value}`, payload);
      setFeedback('Grupo atualizado com sucesso!', 'success');
    } else {
      await axios.post('/api/v1/person-groups', payload);
      setFeedback('Grupo cadastrado com sucesso!', 'success');
    }

    closeForm();
    await loadGroups();
    emit('updated');
  } catch (err: any) {
    formError.value = err.response?.data?.message || 'Erro ao salvar grupo.';
  } finally {
    saving.value = false;
  }
};

const confirmDelete = (grp: any) => {
  groupToDelete.value = grp;
};

const deleteGroup = async () => {
  if (!groupToDelete.value) return;
  deleting.value = true;
  try {
    await axios.delete(`/api/v1/person-groups/${groupToDelete.value.id}`);
    setFeedback('Grupo excluído com sucesso!', 'success');
    groupToDelete.value = null;
    await loadGroups();
    emit('updated');
  } catch (err: any) {
    setFeedback(err.response?.data?.message || 'Erro ao excluir grupo.', 'error');
  } finally {
    deleting.value = false;
  }
};

const setFeedback = (msg: string, type: 'success' | 'error') => {
  feedbackMessage.value = msg;
  feedbackType.value = type;
  setTimeout(() => {
    if (feedbackMessage.value === msg) {
      feedbackMessage.value = '';
    }
  }, 4000);
};

watch(
  () => props.isOpen,
  (open) => {
    if (open) {
      closeForm();
      groupToDelete.value = null;
      feedbackMessage.value = '';
      loadGroups();
    }
  },
  { immediate: true }
);
</script>
