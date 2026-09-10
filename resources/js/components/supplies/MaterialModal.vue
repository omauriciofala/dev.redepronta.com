<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="$emit('close')"
    :title="isEditing ? 'Editar Material de Estoque' : 'Novo Material de Estoque'"
    description="Cadastro oficial de itens, insumos, cabos e equipamentos no catálogo do SaaS"
    size="lg"
  >
    <!-- Header Badge -->
    <template #header-badge>
      <span
        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border"
        :class="isEditing
          ? 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800'
          : 'bg-orange-50 text-[#FC6714] border-orange-200 dark:bg-[#FC6714]/15 dark:text-orange-300 dark:border-[#FC6714]/30'"
      >
        {{ isEditing ? `Editando SKU: ${form.code}` : 'Novo Cadastro' }}
      </span>
    </template>

    <form id="materialForm" @submit.prevent="submitForm" class="space-y-4 text-xs">
      <!-- Alerta de Erro -->
      <div
        v-if="errorMessage"
        class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 font-medium flex items-center gap-2"
      >
        <AlertCircle class="w-4 h-4 shrink-0 text-rose-600 dark:text-rose-400" />
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Linha 1: Código SKU, Nome e Categoria -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Código / SKU *
          </label>
          <input
            type="text"
            v-model="form.code"
            required
            placeholder="Ex: MAT-001"
            class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 font-mono text-xs uppercase focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none"
          />
        </div>

        <div class="sm:col-span-2">
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Nome do Material *
          </label>
          <input
            type="text"
            v-model="form.name"
            required
            placeholder="Ex: Roteador Wi-Fi 6 Gigabit"
            class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none"
          />
        </div>
      </div>

      <!-- Linha 2: Descrição -->
      <div>
        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
          Descrição Detalhada / Especificação Técnica
        </label>
        <input
          type="text"
          v-model="form.description"
          placeholder="Ex: Equipamento de terminação de fibra com portas GE e voz..."
          class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none"
        />
      </div>

      <!-- Linha 3: Categoria, Unidade, Custo e Estoque Mínimo -->
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Categoria
          </label>
          <select
            v-model="form.category"
            class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none cursor-pointer"
          >
            <option value="">Selecione...</option>
            <option v-for="cat in availableCategories" :key="cat.id" :value="cat.name">
              {{ cat.name }}
            </option>
            <!-- Fallback para preservar categoria existente se não estiver na lista -->
            <option
              v-if="form.category && !availableCategories.some(c => c.name === form.category)"
              :value="form.category"
            >
              {{ form.category }} (Atual)
            </option>
          </select>
        </div>

        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Unidade de Medida
          </label>
          <select
            v-model="form.unit_id"
            class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none cursor-pointer"
          >
            <option value="">Selecione...</option>
            <option v-for="u in unitsList" :key="u.id" :value="u.id">
              {{ u.code }} — {{ u.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Custo Médio (R$)
          </label>
          <input
            type="number"
            step="0.01"
            min="0"
            v-model="form.unit_cost"
            class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 font-mono text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none"
          />
        </div>

        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            Estoque Mínimo
          </label>
          <input
            type="number"
            step="1"
            min="0"
            v-model="form.min_stock"
            class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 font-mono text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none"
          />
        </div>
      </div>

      <!-- Parametrização de Rastreabilidade (Design System Box) -->
      <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 space-y-3">
        <span class="font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider text-[11px] block">
          Parâmetros de Controle WMS
        </span>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 cursor-pointer hover:border-orange-300 transition">
            <input
              type="checkbox"
              v-model="form.has_serial"
              class="w-4 h-4 rounded text-[#FC6714] focus:ring-[#FC6714]"
            />
            <div>
              <span class="font-bold text-slate-900 dark:text-slate-100 block">Rastreamento Serial Obrigatório</span>
              <span class="text-[11px] text-slate-500 dark:text-slate-400">Exige número de série individual (Serializado)</span>
            </div>
          </label>

          <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 cursor-pointer hover:border-orange-300 transition">
            <input
              type="checkbox"
              v-model="form.track_batch"
              class="w-4 h-4 rounded text-[#FC6714] focus:ring-[#FC6714]"
            />
            <div>
              <span class="font-bold text-slate-900 dark:text-slate-100 block">Controle de Lote / Bobina</span>
              <span class="text-[11px] text-slate-500 dark:text-slate-400">Rastreio de metragem e número de lote de fabricação</span>
            </div>
          </label>
        </div>
      </div>
    </form>

    <!-- Rodapé Canônico do Modal -->
    <template #footer>
      <div class="w-full flex items-center justify-between gap-3">
        <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
          Integridade estrita ON DELETE RESTRICT
        </span>
        <div class="flex items-center gap-2.5">
          <button
            type="button"
            @click="$emit('close')"
            class="h-10 px-4 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition cursor-pointer"
          >
            Cancelar
          </button>
          <button
            type="submit"
            form="materialForm"
            :disabled="submitting"
            class="h-10 px-5 bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-xs font-semibold rounded-lg shadow-sm transition active:scale-98 disabled:opacity-50 cursor-pointer flex items-center gap-2"
          >
            <span v-if="submitting" class="inline-block animate-spin mr-1">⟳</span>
            <span>{{ submitting ? 'Salvando...' : (isEditing ? 'Atualizar Material' : 'Cadastrar Material') }}</span>
          </button>
        </div>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import { AlertCircle } from 'lucide-vue-next';
import BaseModal from '@/components/common/BaseModal.vue';

const props = defineProps<{
  isOpen: boolean;
  materialData?: any;
  unitsList: Array<{ id: number; code: string; name: string }>;
  categoriesList?: Array<{ id: number; name: string; color?: string; code?: string }>;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'saved'): void;
}>();

const submitting = ref(false);
const errorMessage = ref('');
const internalCategories = ref<Array<{ id: number; name: string; color?: string; code?: string }>>([]);

const availableCategories = computed(() => {
  if (props.categoriesList && props.categoriesList.length > 0) {
    return props.categoriesList;
  }
  return internalCategories.value;
});

async function fetchCategories() {
  try {
    const res = await axios.get('/api/v1/material-categories?active_only=1');
    internalCategories.value = res.data.data || [];
  } catch (err) {
    console.error('Erro ao carregar categorias para o modal de material:', err);
  }
}

const isEditing = computed(() => !!props.materialData?.id);

const form = ref({
  code: '',
  name: '',
  description: '',
  category: 'Geral',
  unit_id: '',
  unit_cost: 0,
  min_stock: 5,
  has_serial: false,
  track_batch: false,
});

watch(
  () => props.materialData,
  (val) => {
    if (val && val.id) {
      form.value = {
        code: val.code || '',
        name: val.name || '',
        description: val.description || '',
        category: val.category || 'Geral',
        unit_id: val.unit_id ? String(val.unit_id) : (val.unit?.id ? String(val.unit.id) : ''),
        unit_cost: Number(val.unit_cost) || 0,
        min_stock: Number(val.min_stock) || 5,
        has_serial: Boolean(val.has_serial),
        track_batch: Boolean(val.track_batch),
      };
    } else {
      form.value = {
        code: '',
        name: '',
        description: '',
        category: 'Geral',
        unit_id: props.unitsList[0]?.id ? String(props.unitsList[0].id) : '',
        unit_cost: 0,
        min_stock: 5,
        has_serial: false,
        track_batch: false,
      };
    }
    errorMessage.value = '';
  },
  { immediate: true }
);

async function submitForm() {
  if (!form.value.code.trim() || !form.value.name.trim()) {
    errorMessage.value = 'Código SKU e Nome do material são obrigatórios.';
    return;
  }

  submitting.value = true;
  errorMessage.value = '';

  try {
    const url = isEditing.value ? `/api/v1/materials/${props.materialData.id}` : '/api/v1/materials';
    const method = isEditing.value ? 'PUT' : 'POST';

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
      errorMessage.value = json.message || 'Erro ao salvar material.';
      return;
    }

    emit('saved');
    emit('close');
  } catch (err: any) {
    errorMessage.value = err.message || 'Erro de comunicação ao salvar material.';
  } finally {
    submitting.value = false;
  }
}

watch(
  () => props.isOpen,
  (open) => {
    if (open) {
      fetchCategories();
    }
  },
  { immediate: true }
);
</script>
