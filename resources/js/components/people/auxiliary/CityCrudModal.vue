<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="$emit('close')"
    title="Gerenciar Cidades / Municípios"
    description="Cadastre, edite ou remova cidades canônicas vinculadas às UFs"
    size="xl"
  >
    <div class="space-y-4">
      <!-- Barra Superior: Filtro por Estado, Busca e Botão Novo -->
      <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2 flex-1 min-w-[280px]">
          <!-- Filtro por Estado -->
          <div class="w-36">
            <select
              v-model="filterStateId"
              @change="loadCities"
              class="w-full h-10 px-2.5 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714] outline-none"
            >
              <option value="">Todas as UFs</option>
              <option v-for="st in states" :key="st.id" :value="st.id">
                {{ st.code }} - {{ st.name }}
              </option>
            </select>
          </div>

          <!-- Busca Textual -->
          <div class="relative flex-1">
            <Search class="w-4 h-4 absolute left-3 top-3 text-slate-400 pointer-events-none" />
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Buscar cidade por nome ou código IBGE..."
              class="w-full h-10 pl-9 pr-4 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714] focus:outline-none transition"
            />
          </div>
        </div>

        <button
          v-if="!isFormOpen"
          type="button"
          @click="openCreateForm"
          class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] text-white text-xs font-semibold shadow-xs transition cursor-pointer"
        >
          <Plus class="w-4 h-4" />
          <span>Nova Cidade</span>
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
              <span>{{ editingId ? 'Editar Cidade' : 'Nova Cidade' }}</span>
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
                Estado (UF) *
              </label>
              <select
                v-model="form.state_id"
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] outline-none"
              >
                <option value="">Selecione o Estado</option>
                <option v-for="st in states" :key="st.id" :value="st.id">
                  {{ st.code }} - {{ st.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Nome do Município *
              </label>
              <input
                v-model="form.name"
                type="text"
                placeholder="Ex: Belo Horizonte"
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] outline-none"
              />
            </div>

            <div>
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Código IBGE *
              </label>
              <input
                v-model="form.ibge_code"
                type="text"
                maxlength="7"
                placeholder="Ex: 3106200 (7 dígitos)"
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#FC6714] outline-none"
              />
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
              @click="saveCity"
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

      <!-- Tabela de Cidades -->
      <div class="border border-slate-200 dark:border-[#14147A] rounded-xl overflow-hidden shadow-2xs">
        <div class="overflow-x-auto max-h-[380px]">
          <table class="w-full text-left border-collapse text-xs">
            <thead class="sticky top-0 bg-slate-100/90 dark:bg-[#03032E]/90 backdrop-blur-xs border-b border-slate-200 dark:border-[#14147A] text-slate-600 dark:text-slate-300 uppercase tracking-wider font-bold z-10">
              <tr>
                <th class="py-2.5 px-4 w-28">Código IBGE</th>
                <th class="py-2.5 px-4">Nome do Município</th>
                <th class="py-2.5 px-4 w-32">Estado (UF)</th>
                <th class="py-2.5 px-4 text-right w-28">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <tr v-if="loading">
                <td colspan="4" class="py-8 text-center text-slate-400">
                  <span class="inline-block animate-spin mr-1.5 text-[#FC6714]">⟳</span> Carregando cidades...
                </td>
              </tr>
              <tr v-else-if="filteredCities.length === 0">
                <td colspan="4" class="py-8 text-center text-slate-400">
                  Nenhuma cidade encontrada.
                </td>
              </tr>
              <tr
                v-for="ct in filteredCities"
                :key="ct.id"
                class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition"
              >
                <td class="py-2.5 px-4 font-mono font-medium text-slate-600 dark:text-slate-400">
                  {{ ct.ibge_code }}
                </td>
                <td class="py-2.5 px-4 font-semibold text-slate-900 dark:text-slate-100">
                  {{ ct.name }}
                </td>
                <td class="py-2.5 px-4 text-slate-700 dark:text-slate-300 font-medium">
                  <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-mono font-bold">
                    {{ ct.state?.code || getStateCode(ct.state_id) }}
                  </span>
                </td>
                <td class="py-2.5 px-4 text-right">
                  <div class="flex items-center justify-end gap-1">
                    <button
                      type="button"
                      @click="openEditForm(ct)"
                      class="p-1.5 rounded-lg text-slate-500 hover:text-[#FC6714] hover:bg-orange-50 dark:hover:bg-white/10 transition cursor-pointer"
                      title="Editar cidade"
                    >
                      <Edit2 class="w-3.5 h-3.5" />
                    </button>
                    <button
                      type="button"
                      @click="confirmDelete(ct)"
                      class="p-1.5 rounded-lg text-slate-500 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                      title="Excluir cidade"
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
          v-if="cityToDelete"
          class="p-3.5 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/60 flex items-center justify-between gap-3 text-xs"
        >
          <div class="flex items-center gap-2 text-red-800 dark:text-red-200 font-medium">
            <AlertCircle class="w-4 h-4 shrink-0 text-red-600 dark:text-red-400" />
            <span>Confirmar exclusão de <strong>{{ cityToDelete.name }}</strong>?</span>
          </div>
          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="cityToDelete = null"
              class="px-2.5 py-1 rounded bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 transition cursor-pointer"
            >
              Não
            </button>
            <button
              type="button"
              :disabled="deleting"
              @click="deleteCity"
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

const cities = ref<any[]>([]);
const states = ref<any[]>([]);
const loading = ref(false);
const saving = ref(false);
const deleting = ref(false);
const searchTerm = ref('');
const filterStateId = ref<number | ''>('');
const isFormOpen = ref(false);
const editingId = ref<number | null>(null);
const formError = ref('');
const feedbackMessage = ref('');
const feedbackType = ref<'success' | 'error'>('success');
const cityToDelete = ref<any | null>(null);

const form = reactive({
  state_id: '' as any,
  name: '',
  ibge_code: '',
});

const getStateCode = (stateId: number) => {
  const s = states.value.find(st => st.id === stateId);
  return s ? s.code : '-';
};

const filteredCities = computed(() => {
  let list = cities.value;
  if (searchTerm.value) {
    const t = searchTerm.value.toLowerCase();
    list = list.filter(
      c => c.name?.toLowerCase().includes(t) || c.ibge_code?.includes(t)
    );
  }
  return list;
});

const loadStates = async () => {
  try {
    const res = await axios.get('/api/v1/states');
    states.value = res.data.data || [];
  } catch (err: any) {
    console.error('Erro ao carregar estados', err);
  }
};

const loadCities = async () => {
  loading.value = true;
  try {
    const params: any = { all: 1 };
    if (filterStateId.value) {
      params.state_id = filterStateId.value;
    }
    const res = await axios.get('/api/v1/cities', { params });
    cities.value = res.data.data || [];
  } catch (err: any) {
    console.error('Erro ao carregar cidades', err);
  } finally {
    loading.value = false;
  }
};

const openCreateForm = () => {
  editingId.value = null;
  form.state_id = filterStateId.value || '';
  form.name = '';
  form.ibge_code = '';
  formError.value = '';
  isFormOpen.value = true;
};

const openEditForm = (ct: any) => {
  editingId.value = ct.id;
  form.state_id = ct.state_id;
  form.name = ct.name;
  form.ibge_code = ct.ibge_code;
  formError.value = '';
  isFormOpen.value = true;
};

const closeForm = () => {
  isFormOpen.value = false;
  editingId.value = null;
  formError.value = '';
};

const saveCity = async () => {
  if (!form.state_id) {
    formError.value = 'Selecione o Estado (UF).';
    return;
  }
  if (!form.name.trim()) {
    formError.value = 'O nome do município é obrigatório.';
    return;
  }
  if (!form.ibge_code.trim() || form.ibge_code.trim().length !== 7) {
    formError.value = 'O código IBGE deve conter exatamente 7 dígitos numéricos.';
    return;
  }

  saving.value = true;
  formError.value = '';
  try {
    const payload = {
      state_id: Number(form.state_id),
      name: form.name.trim(),
      ibge_code: form.ibge_code.trim(),
    };

    if (editingId.value) {
      await axios.put(`/api/v1/cities/${editingId.value}`, payload);
      setFeedback('Município atualizado com sucesso!', 'success');
    } else {
      await axios.post('/api/v1/cities', payload);
      setFeedback('Município cadastrado com sucesso!', 'success');
    }

    closeForm();
    await loadCities();
    emit('updated');
  } catch (err: any) {
    formError.value = err.response?.data?.message || 'Erro ao salvar município.';
  } finally {
    saving.value = false;
  }
};

const confirmDelete = (ct: any) => {
  cityToDelete.value = ct;
};

const deleteCity = async () => {
  if (!cityToDelete.value) return;
  deleting.value = true;
  try {
    await axios.delete(`/api/v1/cities/${cityToDelete.value.id}`);
    setFeedback('Município excluído com sucesso!', 'success');
    cityToDelete.value = null;
    await loadCities();
    emit('updated');
  } catch (err: any) {
    setFeedback(err.response?.data?.message || 'Erro ao excluir município.', 'error');
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
  async (open) => {
    if (open) {
      closeForm();
      cityToDelete.value = null;
      feedbackMessage.value = '';
      await loadStates();
      await loadCities();
    }
  },
  { immediate: true }
);
</script>
