<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="$emit('close')"
    title="Gerenciar CNAEs"
    description="Cadastre, edite ou remova códigos de Classificação Nacional de Atividades Econômicas"
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
            placeholder="Buscar CNAE por código ou descrição..."
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
          <span>Novo CNAE</span>
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
              <span>{{ editingId ? 'Editar CNAE' : 'Novo CNAE' }}</span>
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
            <div>
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Código CNAE *
              </label>
              <input
                v-model="form.code"
                type="text"
                placeholder="Ex: 61.10-8-03"
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-mono font-bold focus:ring-2 focus:ring-[#FC6714] outline-none"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Descrição da Atividade Econômica *
              </label>
              <input
                v-model="form.description"
                type="text"
                placeholder="Ex: Serviços de telecomunicações por fio não especificados"
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] outline-none"
              />
            </div>

            <div class="flex items-center gap-2 pt-2 sm:col-span-3">
              <input
                id="cnae_is_active"
                v-model="form.is_active"
                type="checkbox"
                class="w-4 h-4 rounded text-[#FC6714] focus:ring-[#FC6714] border-slate-300 dark:border-slate-700"
              />
              <label for="cnae_is_active" class="font-semibold text-slate-700 dark:text-slate-300 select-none cursor-pointer">
                CNAE Ativo
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
              @click="saveCnae"
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

      <!-- Tabela de CNAEs -->
      <div class="border border-slate-200 dark:border-[#14147A] rounded-xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto max-h-[380px]">
          <table class="w-full text-left border-collapse text-xs">
            <thead class="sticky top-0 bg-slate-100/90 dark:bg-[#03032E]/90 backdrop-blur-xs border-b border-slate-200 dark:border-[#14147A] text-slate-600 dark:text-slate-300 uppercase tracking-wider font-bold z-10">
              <tr>
                <th class="py-2.5 px-4 w-32">Código</th>
                <th class="py-2.5 px-4">Descrição da Atividade</th>
                <th class="py-2.5 px-4 w-24 text-center">Status</th>
                <th class="py-2.5 px-4 text-right w-28">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <tr v-if="loading">
                <td colspan="4" class="py-8 text-center text-slate-400">
                  <span class="inline-block animate-spin mr-1.5 text-[#FC6714]">⟳</span> Carregando CNAEs...
                </td>
              </tr>
              <tr v-else-if="filteredCnaes.length === 0">
                <td colspan="4" class="py-8 text-center text-slate-400">
                  Nenhum CNAE cadastrado.
                </td>
              </tr>
              <tr
                v-for="c in filteredCnaes"
                :key="c.id"
                class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition"
              >
                <td class="py-2.5 px-4 font-mono font-bold text-slate-900 dark:text-slate-100">
                  <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                    {{ c.code }}
                  </span>
                </td>
                <td class="py-2.5 px-4 font-medium text-slate-800 dark:text-slate-200">
                  {{ c.description }}
                </td>
                <td class="py-2.5 px-4 text-center">
                  <span
                    class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                    :class="c.is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'"
                  >
                    {{ c.is_active ? 'Ativo' : 'Inativo' }}
                  </span>
                </td>
                <td class="py-2.5 px-4 text-right">
                  <div class="flex items-center justify-end gap-1">
                    <button
                      type="button"
                      @click="openEditForm(c)"
                      class="p-1.5 rounded-lg text-slate-500 hover:text-[#FC6714] hover:bg-orange-50 dark:hover:bg-white/10 transition cursor-pointer"
                      title="Editar CNAE"
                    >
                      <Edit2 class="w-3.5 h-3.5" />
                    </button>
                    <button
                      type="button"
                      @click="confirmDelete(c)"
                      class="p-1.5 rounded-lg text-slate-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                      title="Excluir CNAE"
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
          v-if="cnaeToDelete"
          class="p-3.5 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/60 flex items-center justify-between gap-3 text-xs"
        >
          <div class="flex items-center gap-2 text-red-800 dark:text-red-200 font-medium">
            <AlertCircle class="w-4 h-4 shrink-0 text-red-600 dark:text-red-400" />
            <span>Confirmar exclusão do CNAE <strong>{{ cnaeToDelete.code }}</strong>?</span>
          </div>
          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="cnaeToDelete = null"
              class="px-2.5 py-1 rounded bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 transition cursor-pointer"
            >
              Não
            </button>
            <button
              type="button"
              :disabled="deleting"
              @click="deleteCnae"
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

const cnaes = ref<any[]>([]);
const loading = ref(false);
const saving = ref(false);
const deleting = ref(false);
const searchTerm = ref('');
const isFormOpen = ref(false);
const editingId = ref<number | null>(null);
const formError = ref('');
const feedbackMessage = ref('');
const feedbackType = ref<'success' | 'error'>('success');
const cnaeToDelete = ref<any | null>(null);

const form = reactive({
  code: '',
  description: '',
  is_active: true,
});

const filteredCnaes = computed(() => {
  if (!searchTerm.value) return cnaes.value;
  const t = searchTerm.value.toLowerCase();
  return cnaes.value.filter(
    c => c.code?.toLowerCase().includes(t) || c.description?.toLowerCase().includes(t)
  );
});

const loadCnaes = async () => {
  loading.value = true;
  try {
    const res = await axios.get('/api/v1/cnaes');
    cnaes.value = res.data.data || [];
  } catch (err: any) {
    console.error('Erro ao carregar CNAEs', err);
  } finally {
    loading.value = false;
  }
};

const openCreateForm = () => {
  editingId.value = null;
  form.code = '';
  form.description = '';
  form.is_active = true;
  formError.value = '';
  isFormOpen.value = true;
};

const openEditForm = (c: any) => {
  editingId.value = c.id;
  form.code = c.code;
  form.description = c.description;
  form.is_active = Boolean(c.is_active);
  formError.value = '';
  isFormOpen.value = true;
};

const closeForm = () => {
  isFormOpen.value = false;
  editingId.value = null;
  formError.value = '';
};

const saveCnae = async () => {
  if (!form.code.trim()) {
    formError.value = 'O código CNAE é obrigatório.';
    return;
  }
  if (!form.description.trim()) {
    formError.value = 'A descrição da atividade econômica é obrigatória.';
    return;
  }

  saving.value = true;
  formError.value = '';
  try {
    const payload = {
      code: form.code.trim(),
      description: form.description.trim(),
      is_active: Boolean(form.is_active),
    };

    if (editingId.value) {
      await axios.put(`/api/v1/cnaes/${editingId.value}`, payload);
      setFeedback('CNAE atualizado com sucesso!', 'success');
    } else {
      await axios.post('/api/v1/cnaes', payload);
      setFeedback('CNAE cadastrado com sucesso!', 'success');
    }

    closeForm();
    await loadCnaes();
    emit('updated');
  } catch (err: any) {
    formError.value = err.response?.data?.message || 'Erro ao salvar CNAE.';
  } finally {
    saving.value = false;
  }
};

const confirmDelete = (c: any) => {
  cnaeToDelete.value = c;
};

const deleteCnae = async () => {
  if (!cnaeToDelete.value) return;
  deleting.value = true;
  try {
    await axios.delete(`/api/v1/cnaes/${cnaeToDelete.value.id}`);
    setFeedback('CNAE excluído com sucesso!', 'success');
    cnaeToDelete.value = null;
    await loadCnaes();
    emit('updated');
  } catch (err: any) {
    setFeedback(err.response?.data?.message || 'Erro ao excluir CNAE.', 'error');
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
      cnaeToDelete.value = null;
      feedbackMessage.value = '';
      loadCnaes();
    }
  },
  { immediate: true }
);
</script>
