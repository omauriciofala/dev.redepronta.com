<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="$emit('close')"
    :title="`Catálogo de Materiais — ${owner?.code || ''}`"
    :description="`Mapeamento de códigos e descrições técnicas do proprietário ${owner?.name || ''}`"
    size="2xl"
  >
    <div class="space-y-4">
      <!-- Barra Superior: Busca, Ações em Lote e Botão Novo -->
      <div class="flex items-center justify-between gap-3 flex-wrap">
        <!-- Campo de Busca -->
        <div class="relative flex-1 min-w-[240px]">
          <Search class="w-4 h-4 absolute left-3 top-3 text-slate-400 pointer-events-none" />
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Buscar por código do proprietário, nome ou SKU do sistema..."
            class="w-full h-10 pl-9 pr-4 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714] focus:outline-none transition"
          />
        </div>

        <!-- Botões de Ação -->
        <div class="flex items-center gap-2">
          <!-- Download Modelo -->
          <button
            type="button"
            @click="downloadTemplate"
            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#03032E] hover:bg-slate-50 dark:hover:bg-white/5 text-slate-700 dark:text-slate-200 text-xs font-medium shadow-2xs transition cursor-pointer"
            title="Baixar planilha CSV modelo para importação em lote"
          >
            <Download class="w-3.5 h-3.5 text-slate-400" />
            <span>Modelo CSV</span>
          </button>

          <!-- Importar Planilha -->
          <label
            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#03032E] hover:bg-slate-50 dark:hover:bg-white/5 text-slate-700 dark:text-slate-200 text-xs font-medium shadow-2xs transition cursor-pointer"
            title="Importar planilha de De/Para do proprietário"
          >
            <Upload class="w-3.5 h-3.5 text-[#FC6714]" />
            <span>Importar</span>
            <input
              type="file"
              accept=".csv,.txt"
              class="hidden"
              @change="handleFileUpload"
            />
          </label>

          <!-- Botão Novo Vínculo -->
          <button
            v-if="!isFormOpen"
            type="button"
            @click="openCreateForm"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] text-white text-xs font-semibold shadow-xs transition cursor-pointer"
          >
            <Plus class="w-4 h-4" />
            <span>Vincular Material</span>
          </button>
        </div>
      </div>

      <!-- Feedback de Importação / Ações -->
      <div v-if="alertMessage" class="p-3 rounded-lg text-xs flex items-center justify-between gap-2" :class="alertSuccess ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800'">
        <div class="flex items-center gap-2">
          <component :is="alertSuccess ? CheckCircle2 : AlertCircle" class="w-4 h-4 shrink-0" />
          <span>{{ alertMessage }}</span>
        </div>
        <button type="button" @click="alertMessage = ''" class="p-0.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
          <X class="w-3.5 h-3.5" />
        </button>
      </div>

      <!-- Formulário de Cadastro / Edição -->
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
              <span>{{ editingId ? 'Editar Vínculo no Catálogo' : 'Novo Vínculo no Catálogo do Proprietário' }}</span>
            </h4>
            <button
              type="button"
              @click="closeForm"
              class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 cursor-pointer"
            >
              <X class="w-4 h-4" />
            </button>
          </div>

          <div v-if="formError" class="p-2.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-2">
            <AlertCircle class="w-4 h-4 shrink-0 text-rose-600" />
            <span>{{ formError }}</span>
          </div>

          <div class="space-y-3 text-xs">
            <!-- Linha 1: Seleção do Material Canônico do Sistema (Apenas se criando) -->
            <div v-if="!editingId">
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Material Canônico do Sistema (Rede Pronta) *
              </label>
              <MaterialSearchSelect
                v-model="form.material_id"
                label=""
                placeholder="Busque o material por código SKU interno ou nome..."
                required
                @select="onSystemMaterialSelected"
              />
              <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 block">
                Selecione o material que já existe no catálogo do sistema para vincular ao código deste proprietário.
              </span>
            </div>
            <div v-else class="p-2.5 rounded-lg bg-white/70 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
              <div>
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Material Canônico no Sistema:</span>
                <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                  {{ editingMaterialDisplay }}
                </span>
              </div>
              <span class="font-mono text-xs font-bold text-[#FC6714]">
                SKU: {{ editingMaterialSku }}
              </span>
            </div>

            <!-- Linha 2: Código e Nome no Proprietário -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                  Código do Proprietário *
                </label>
                <input
                  v-model="form.owner_code"
                  type="text"
                  placeholder="Ex: 40012903, MAT-VIV-99"
                  class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#FC6714] outline-none uppercase"
                />
              </div>

              <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                  Nome / Descrição no Proprietário *
                </label>
                <input
                  v-model="form.owner_name"
                  type="text"
                  placeholder="Ex: CABO FIBRA OPTICA DROP 1 FO BOBINA 1000M"
                  class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] outline-none"
                />
              </div>
            </div>

            <!-- Linha 3: Observações Técnicas -->
            <div>
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Observações / Especificação do Proprietário (Opcional)
              </label>
              <input
                v-model="form.notes"
                type="text"
                placeholder="Ex: Homologado para contratos FTTH regional Sudeste"
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] outline-none"
              />
            </div>

            <!-- Ações do Formulário -->
            <div class="flex items-center justify-end gap-2 pt-2">
              <button
                type="button"
                @click="closeForm"
                class="px-3.5 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer font-medium"
              >
                Cancelar
              </button>
              <button
                type="button"
                @click="saveItem"
                :disabled="isSaving"
                class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] disabled:opacity-60 text-white font-semibold shadow-xs transition cursor-pointer"
              >
                <Loader2 v-if="isSaving" class="w-3.5 h-3.5 animate-spin" />
                <Check v-else class="w-3.5 h-3.5" />
                <span>{{ editingId ? 'Atualizar Vínculo' : 'Salvar Vínculo' }}</span>
              </button>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Tabela do Catálogo De/Para -->
      <div class="rounded-xl border border-slate-200 dark:border-[#14147A] overflow-hidden bg-white dark:bg-[#03032E] shadow-2xs">
        <div v-if="isLoading" class="p-8 text-center text-slate-400">
          <Loader2 class="w-6 h-6 animate-spin mx-auto text-[#FC6714] mb-2" />
          <p class="text-xs">Carregando catálogo de materiais do proprietário...</p>
        </div>

        <div v-else-if="filteredItems.length === 0" class="p-8 text-center text-slate-400">
          <PackageSearch class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2" />
          <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Nenhum material no catálogo deste proprietário</p>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
            Cadastre os códigos que a {{ owner?.name || 'operadora' }} utiliza para identificar seus materiais ou importe a planilha de relacionamento.
          </p>
          <div class="mt-4 flex items-center justify-center gap-2">
            <button
              type="button"
              @click="openCreateForm"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#FC6714] text-white text-xs font-semibold hover:bg-[#E0530A] transition cursor-pointer"
            >
              <Plus class="w-3.5 h-3.5" />
              <span>Vincular Primeiro Material</span>
            </button>
            <button
              type="button"
              @click="downloadTemplate"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#03032E] text-slate-700 dark:text-slate-300 text-xs font-medium hover:bg-slate-50 transition cursor-pointer"
            >
              <Download class="w-3.5 h-3.5 text-slate-400" />
              <span>Baixar Modelo CSV</span>
            </button>
          </div>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="border-b border-slate-200 dark:border-[#14147A] bg-slate-50/75 dark:bg-[#06064D]/50 font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[11px]">
                <th class="py-3 px-4">Código (Proprietário)</th>
                <th class="py-3 px-4">Nome no Proprietário</th>
                <th class="py-3 px-4">Material no Sistema (SKU / Canônico)</th>
                <th class="py-3 px-4">Unid.</th>
                <th class="py-3 px-4 text-right">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <tr
                v-for="item in filteredItems"
                :key="item.id"
                class="hover:bg-slate-50/60 dark:hover:bg-white/5 transition"
              >
                <!-- Código do Proprietário -->
                <td class="py-3 px-4 font-mono font-bold text-[#FC6714]">
                  {{ item.owner_code }}
                </td>

                <!-- Nome no Proprietário -->
                <td class="py-3 px-4">
                  <div class="font-semibold text-slate-900 dark:text-slate-100">
                    {{ item.owner_name }}
                  </div>
                  <div v-if="item.notes" class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 truncate max-w-xs">
                    {{ item.notes }}
                  </div>
                </td>

                <!-- Material Canônico no Sistema -->
                <td class="py-3 px-4">
                  <div class="flex items-center gap-1.5">
                    <span class="font-mono text-slate-500 dark:text-slate-400 text-[11px] bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded font-bold">
                      SKU: {{ item.material?.code || '-' }}
                    </span>
                    <span class="text-slate-700 dark:text-slate-300 truncate max-w-xs">
                      {{ item.material?.name || '-' }}
                    </span>
                  </div>
                </td>

                <!-- Unidade -->
                <td class="py-3 px-4 font-mono text-slate-600 dark:text-slate-400">
                  {{ item.material?.unit?.code || 'UN' }}
                </td>

                <!-- Ações -->
                <td class="py-3 px-4 text-right">
                  <div class="inline-flex items-center gap-1">
                    <button
                      type="button"
                      @click="openEditForm(item)"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-[#FC6714] hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 transition cursor-pointer"
                      title="Editar vínculo"
                    >
                      <Edit2 class="w-3.5 h-3.5" />
                    </button>
                    <button
                      type="button"
                      @click="deleteItem(item)"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer"
                      title="Excluir vínculo"
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

      <!-- Rodapé Informativo -->
      <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 pt-1">
        <span>
          Exibindo <strong>{{ filteredItems.length }}</strong> de <strong>{{ items.length }}</strong> materiais vinculados.
        </span>
        <span class="italic">
          Em depósitos vinculados a este proprietário, estes códigos e nomes substituirão automaticamente a nomenclatura interna.
        </span>
      </div>
    </div>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import {
  Search, Plus, Edit2, Trash2, X, Check, Loader2, AlertCircle, CheckCircle2,
  Download, Upload, PackageSearch
} from 'lucide-vue-next';
import BaseModal from '@/components/common/BaseModal.vue';
import MaterialSearchSelect from '@/components/common/MaterialSearchSelect.vue';

interface OwnerData {
  id: number;
  code: string;
  name: string;
}

interface OwnerMaterialItem {
  id: number;
  account_id: number;
  material_owner_id: number;
  material_id: number;
  owner_code: string;
  owner_name: string;
  notes?: string;
  is_active: boolean;
  material?: {
    id: number;
    code: string;
    name: string;
    unit?: { code: string; name: string };
  };
}

const props = defineProps<{
  isOpen: boolean;
  owner: OwnerData | null;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'updated'): void;
}>();

const items = ref<OwnerMaterialItem[]>([]);
const isLoading = ref(false);
const isSaving = ref(false);
const searchTerm = ref('');
const isFormOpen = ref(false);
const editingId = ref<number | null>(null);
const editingMaterialDisplay = ref('');
const editingMaterialSku = ref('');
const formError = ref('');
const alertMessage = ref('');
const alertSuccess = ref(true);

const form = ref({
  material_id: '' as string | number,
  owner_code: '',
  owner_name: '',
  notes: '',
  is_active: true,
});

const filteredItems = computed(() => {
  if (!searchTerm.value.trim()) return items.value;
  const s = searchTerm.value.toLowerCase().trim();
  return items.value.filter((item) => {
    return (
      item.owner_code?.toLowerCase().includes(s) ||
      item.owner_name?.toLowerCase().includes(s) ||
      item.material?.code?.toLowerCase().includes(s) ||
      item.material?.name?.toLowerCase().includes(s)
    );
  });
});

watch(
  () => [props.isOpen, props.owner],
  ([open, currentOwner]) => {
    if (open && currentOwner) {
      loadItems();
      closeForm();
      alertMessage.value = '';
      searchTerm.value = '';
    }
  },
  { immediate: true }
);

async function loadItems() {
  if (!props.owner?.id) return;
  isLoading.value = true;
  try {
    const res = await axios.get(`/api/v1/material-owners/${props.owner.id}/materials`);
    items.value = res.data.data || [];
  } catch (err: any) {
    console.error('Erro ao carregar catálogo do proprietário:', err);
  } finally {
    isLoading.value = false;
  }
}

function openCreateForm() {
  editingId.value = null;
  editingMaterialDisplay.value = '';
  editingMaterialSku.value = '';
  formError.value = '';
  form.value = {
    material_id: '',
    owner_code: '',
    owner_name: '',
    notes: '',
    is_active: true,
  };
  isFormOpen.value = true;
}

function openEditForm(item: OwnerMaterialItem) {
  editingId.value = item.id;
  editingMaterialDisplay.value = item.material?.name || '';
  editingMaterialSku.value = item.material?.code || '';
  formError.value = '';
  form.value = {
    material_id: item.material_id,
    owner_code: item.owner_code,
    owner_name: item.owner_name,
    notes: item.notes || '',
    is_active: item.is_active,
  };
  isFormOpen.value = true;
}

function closeForm() {
  isFormOpen.value = false;
  editingId.value = null;
  formError.value = '';
}

function onSystemMaterialSelected(material: any) {
  if (material) {
    // Sugere o nome do sistema como inicial se o usuário ainda não digitou
    if (!form.value.owner_name.trim()) {
      form.value.owner_name = material.name || '';
    }
  }
}

async function saveItem() {
  if (!props.owner?.id) return;
  formError.value = '';

  if (!editingId.value && !form.value.material_id) {
    formError.value = 'Selecione o material canônico do sistema.';
    return;
  }
  if (!form.value.owner_code.trim()) {
    formError.value = 'Informe o código do material no proprietário.';
    return;
  }
  if (!form.value.owner_name.trim()) {
    formError.value = 'Informe o nome do material no proprietário.';
    return;
  }

  isSaving.value = true;
  try {
    if (editingId.value) {
      await axios.put(`/api/v1/material-owners/${props.owner.id}/materials/${editingId.value}`, {
        owner_code: form.value.owner_code.trim(),
        owner_name: form.value.owner_name.trim(),
        notes: form.value.notes.trim() || null,
        is_active: form.value.is_active,
      });
      alertMessage.value = 'Vínculo do catálogo atualizado com sucesso!';
    } else {
      await axios.post(`/api/v1/material-owners/${props.owner.id}/materials`, {
        material_id: Number(form.value.material_id),
        owner_code: form.value.owner_code.trim(),
        owner_name: form.value.owner_name.trim(),
        notes: form.value.notes.trim() || null,
        is_active: form.value.is_active,
      });
      alertMessage.value = 'Material vinculado com sucesso ao catálogo do proprietário!';
    }

    alertSuccess.value = true;
    closeForm();
    await loadItems();
    emit('updated');
  } catch (err: any) {
    formError.value = err.response?.data?.message || 'Erro ao salvar material no catálogo.';
  } finally {
    isSaving.value = false;
  }
}

async function deleteItem(item: OwnerMaterialItem) {
  if (!props.owner?.id) return;
  if (!confirm(`Remover o código "${item.owner_code}" do catálogo deste proprietário?`)) return;

  try {
    await axios.delete(`/api/v1/material-owners/${props.owner.id}/materials/${item.id}`);
    alertMessage.value = 'Item removido do catálogo com sucesso.';
    alertSuccess.value = true;
    await loadItems();
    emit('updated');
  } catch (err: any) {
    alertMessage.value = err.response?.data?.message || 'Erro ao remover item do catálogo.';
    alertSuccess.value = false;
  }
}

function downloadTemplate() {
  if (!props.owner?.id) return;
  window.open(`/api/v1/material-owners/${props.owner.id}/materials/template`, '_blank');
}

async function handleFileUpload(event: Event) {
  const target = event.target as HTMLInputElement;
  if (!target.files || target.files.length === 0 || !props.owner?.id) return;

  const file = target.files[0];
  const formData = new FormData();
  formData.append('file', file);

  isLoading.value = true;
  alertMessage.value = '';
  try {
    const res = await axios.post(`/api/v1/material-owners/${props.owner.id}/materials/import`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    alertMessage.value = res.data.message || 'Importação realizada com sucesso!';
    alertSuccess.value = true;
    await loadItems();
    emit('updated');
  } catch (err: any) {
    alertMessage.value = err.response?.data?.message || 'Erro ao importar planilha.';
    alertSuccess.value = false;
  } finally {
    isLoading.value = false;
    target.value = '';
  }
}
</script>
