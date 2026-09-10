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

      <!-- Linha 3: Categoria e Unidade -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
      </div>

      <!-- Parametrização de Rastreabilidade (3 Tipos Canônicos) -->
      <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 space-y-3">
        <div class="flex items-center justify-between">
          <span class="font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider text-[11px] block">
            Rastreabilidade WMS
          </span>
          <span class="text-[11px] text-slate-500 dark:text-slate-400">
            Selecione o modelo de controle físico no estoque
          </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <!-- 1. Serializado -->
          <div
            @click="setTrackingType('SERIAL')"
            class="relative flex flex-col justify-between p-3.5 rounded-xl border cursor-pointer transition-all select-none"
            :class="form.tracking_type === 'SERIAL'
              ? 'border-[#FC6714] bg-orange-50/70 dark:bg-orange-950/30 ring-1 ring-[#FC6714]'
              : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 hover:border-slate-300 dark:hover:border-slate-700'"
          >
            <div>
              <div class="flex items-center justify-between mb-2">
                <div
                  class="p-2 rounded-lg"
                  :class="form.tracking_type === 'SERIAL'
                    ? 'bg-[#FC6714] text-white'
                    : 'bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300'"
                >
                  <QrCode class="w-4 h-4" />
                </div>
                <span
                  v-if="form.tracking_type === 'SERIAL'"
                  class="w-4 h-4 rounded-full bg-[#FC6714] text-white flex items-center justify-center text-[10px]"
                >
                  <Check class="w-3 h-3 stroke-[3]" />
                </span>
              </div>
              <h4 class="font-bold text-xs text-slate-900 dark:text-slate-100 mb-1">
                Serializado
              </h4>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-snug">
                Exige número de série individual para cada unidade (ONUs, Roteadores).
              </p>
            </div>
          </div>

          <!-- 2. Lote / Metragem -->
          <div
            @click="setTrackingType('BATCH')"
            class="relative flex flex-col justify-between p-3.5 rounded-xl border cursor-pointer transition-all select-none"
            :class="form.tracking_type === 'BATCH'
              ? 'border-[#FC6714] bg-orange-50/70 dark:bg-orange-950/30 ring-1 ring-[#FC6714]'
              : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 hover:border-slate-300 dark:hover:border-slate-700'"
          >
            <div>
              <div class="flex items-center justify-between mb-2">
                <div
                  class="p-2 rounded-lg"
                  :class="form.tracking_type === 'BATCH'
                    ? 'bg-[#FC6714] text-white'
                    : 'bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300'"
                >
                  <Layers class="w-4 h-4" />
                </div>
                <span
                  v-if="form.tracking_type === 'BATCH'"
                  class="w-4 h-4 rounded-full bg-[#FC6714] text-white flex items-center justify-center text-[10px]"
                >
                  <Check class="w-3 h-3 stroke-[3]" />
                </span>
              </div>
              <h4 class="font-bold text-xs text-slate-900 dark:text-slate-100 mb-1">
                Lote / Metragem
              </h4>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-snug">
                Rastreio de metragem e lote de fabricação/bobina (Cabos Drop, Fibras).
              </p>
            </div>
          </div>

          <!-- 3. A Granel -->
          <div
            @click="setTrackingType('BULK')"
            class="relative flex flex-col justify-between p-3.5 rounded-xl border cursor-pointer transition-all select-none"
            :class="form.tracking_type === 'BULK'
              ? 'border-[#FC6714] bg-orange-50/70 dark:bg-orange-950/30 ring-1 ring-[#FC6714]'
              : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 hover:border-slate-300 dark:hover:border-slate-700'"
          >
            <div>
              <div class="flex items-center justify-between mb-2">
                <div
                  class="p-2 rounded-lg"
                  :class="form.tracking_type === 'BULK'
                    ? 'bg-[#FC6714] text-white'
                    : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'"
                >
                  <Box class="w-4 h-4" />
                </div>
                <span
                  v-if="form.tracking_type === 'BULK'"
                  class="w-4 h-4 rounded-full bg-[#FC6714] text-white flex items-center justify-center text-[10px]"
                >
                  <Check class="w-3 h-3 stroke-[3]" />
                </span>
              </div>
              <h4 class="font-bold text-xs text-slate-900 dark:text-slate-100 mb-1">
                A Granel
              </h4>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-snug">
                Controle apenas por quantidade/saldo sem série ou lote (Conectores, Fitas).
              </p>
            </div>
          </div>
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
import { AlertCircle, QrCode, Layers, Box, Check } from 'lucide-vue-next';
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
  (e: 'updated'): void;
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
  min_stock: 0,
  tracking_type: 'BULK' as 'SERIAL' | 'BATCH' | 'BULK',
  has_serial: false,
  track_batch: false,
});

function setTrackingType(type: 'SERIAL' | 'BATCH' | 'BULK') {
  form.value.tracking_type = type;
  form.value.has_serial = (type === 'SERIAL');
  form.value.track_batch = (type === 'BATCH');
}

watch(
  () => props.materialData,
  (val) => {
    if (val && val.id) {
      let tracking: 'SERIAL' | 'BATCH' | 'BULK' = 'BULK';
      const rawType = String(val.tracking_type || '').toUpperCase();
      if (rawType === 'SERIAL' || val.has_serial) {
        tracking = 'SERIAL';
      } else if (rawType === 'BATCH' || val.track_batch) {
        tracking = 'BATCH';
      } else {
        tracking = 'BULK';
      }

      form.value = {
        code: val.code || '',
        name: val.name || '',
        description: val.description || '',
        category: val.category || 'Geral',
        unit_id: val.unit_id ? String(val.unit_id) : (val.unit?.id ? String(val.unit.id) : ''),
        unit_cost: Number(val.unit_cost) || 0,
        min_stock: Number(val.min_stock) || 0,
        tracking_type: tracking,
        has_serial: tracking === 'SERIAL',
        track_batch: tracking === 'BATCH',
      };
    } else {
      form.value = {
        code: '',
        name: '',
        description: '',
        category: 'Geral',
        unit_id: props.unitsList[0]?.id ? String(props.unitsList[0].id) : '',
        unit_cost: 0,
        min_stock: 0,
        tracking_type: 'BULK',
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
