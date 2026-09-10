<template>
  <div class="relative w-full" ref="containerRef">
    <!-- Rótulo opcional -->
    <label v-if="label" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>

    <div class="relative flex items-center">
      <!-- Campo de Entrada de Texto -->
      <input
        ref="inputRef"
        type="text"
        :value="displayText"
        @input="onInput"
        @focus="onFocus"
        @keydown.down.prevent="navigateResults(1)"
        @keydown.up.prevent="navigateResults(-1)"
        @keydown.enter.prevent="selectHighlighted"
        @keydown.esc="isOpen = false"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required && !modelValue"
        class="w-full h-11 pl-3.5 pr-10 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-hidden text-xs sm:text-sm transition disabled:opacity-60 disabled:cursor-not-allowed"
      />

      <!-- Extremidade Direita: Lupa Canônica e Ações -->
      <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1 text-slate-400 dark:text-slate-500 pointer-events-none">
        <!-- Spinner de Carregamento -->
        <Loader2 v-if="isLoading" class="w-4 h-4 animate-spin text-[#FC6714]" />

        <!-- Botão Limpar (quando tem seleção ou texto) -->
        <button
          v-else-if="modelValue || query"
          type="button"
          @click.stop="clearSelection"
          class="pointer-events-auto p-1 hover:text-slate-600 dark:hover:text-slate-300 transition cursor-pointer"
          title="Limpar seleção"
        >
          <X class="w-3.5 h-3.5" />
        </button>

        <!-- Ícone Canônico da LUPA na Extremidade Direita -->
        <Search v-else class="w-4 h-4 text-slate-400 dark:text-slate-500" />
      </div>
    </div>

    <!-- Dropdown de Resultados da Busca Sob Demanda -->
    <div
      v-if="isOpen && (query.length >= 1 || results.length > 0 || hasSearched)"
      class="absolute left-0 right-0 top-full mt-1.5 z-50 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl overflow-hidden max-h-72 overflow-y-auto"
    >
      <!-- Aviso de menos de 1 caractere quando não pesquisou -->
      <div v-if="query.length < 1 && results.length === 0" class="p-3.5 text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2">
        <Search class="w-4 h-4 text-slate-400 shrink-0" />
        <span>Digite o nome, código (SKU) ou categoria do material...</span>
      </div>

      <!-- Carregando -->
      <div v-else-if="isLoading" class="p-4 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-center gap-2">
        <Loader2 class="w-4 h-4 animate-spin text-[#FC6714]" />
        <span>Buscando materiais no catálogo...</span>
      </div>

      <!-- Nenhum resultado encontrado -->
      <div v-else-if="results.length === 0 && hasSearched" class="p-4 text-xs text-slate-500 dark:text-slate-400 text-center">
        Nenhum material encontrado para "<strong>{{ query }}</strong>".
      </div>

      <!-- Lista de Materiais -->
      <ul v-else class="divide-y divide-slate-100 dark:divide-slate-800/60">
        <li
          v-for="(material, index) in results"
          :key="material.id"
          @mousedown.prevent="selectMaterial(material)"
          :class="[
            highlightedIndex === index
              ? 'bg-orange-50 dark:bg-[#FC6714]/10 text-[#FC6714] dark:text-orange-400 font-semibold'
              : 'hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-800 dark:text-slate-200',
            Number(modelValue) === material.id ? 'font-semibold' : ''
          ]"
          class="px-3.5 py-2.5 text-xs sm:text-sm cursor-pointer flex items-center justify-between gap-3 transition"
        >
          <div class="flex items-center gap-3 min-w-0">
            <!-- Ícone / Indicador Visual -->
            <div
              class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
              :class="material.has_serial
                ? 'bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400'
                : (material.track_batch
                  ? 'bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400'
                  : 'bg-orange-100 dark:bg-[#FC6714]/20 text-[#FC6714]')"
            >
              <QrCode v-if="material.has_serial" class="w-4 h-4" />
              <Layers v-else-if="material.track_batch" class="w-4 h-4" />
              <Package v-else class="w-4 h-4" />
            </div>

            <!-- Dados do Material -->
            <div class="min-w-0">
              <span class="truncate block text-xs font-semibold text-slate-900 dark:text-slate-100">
                {{ material.name }}
              </span>
              <div class="flex items-center gap-2 text-[11px] text-slate-400 dark:text-slate-500 truncate mt-0.5">
                <span class="font-mono font-bold" :class="material.has_owner_alias ? 'text-[#FC6714]' : 'text-slate-500 dark:text-slate-400'">
                  {{ material.has_owner_alias ? `Cód. Proprietário: ${material.code}` : `SKU: ${material.code}` }}
                </span>
                <span v-if="material.has_owner_alias && material.system_code" class="font-mono text-[10px] text-slate-400">
                  (SKU: {{ material.system_code }})
                </span>
                <span v-if="material.category">• {{ material.category }}</span>
                <span>• {{ material.unit?.code || material.unit || 'UND' }}</span>
              </div>
            </div>
          </div>

          <!-- Badges à direita -->
          <div class="flex items-center gap-1.5 shrink-0">
            <span
              v-if="material.has_serial"
              class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300 border border-purple-200 dark:border-purple-800"
            >
              <QrCode class="w-3 h-3" />
              <span>Serial</span>
            </span>
            <span
              v-else-if="material.track_batch"
              class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 border border-blue-200 dark:border-blue-800"
            >
              <Layers class="w-3 h-3" />
              <span>Lote</span>
            </span>
            <span
              v-else
              class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400"
            >
              {{ material.unit?.code || material.unit || 'UND' }}
            </span>
          </div>
        </li>
      </ul>

      <!-- Rodapé do dropdown -->
      <div class="p-2 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800/80 text-[11px] text-slate-500 dark:text-slate-400 flex items-center justify-between">
        <span>Catálogo de Materiais e Suprimentos</span>
        <span class="font-mono">WMS Rede Pronta</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Search, Loader2, X, Package, QrCode, Layers } from 'lucide-vue-next';
import axios from 'axios';

interface MaterialItem {
  id: number;
  code: string;
  name: string;
  category?: string;
  unit?: any;
  has_serial?: boolean;
  track_batch?: boolean;
  tracking_type?: string;
  min_stock?: number;
  is_active?: boolean;
  system_code?: string;
  system_name?: string;
  owner_code?: string;
  owner_name?: string;
  has_owner_alias?: boolean;
}

const props = withDefaults(defineProps<{
  modelValue: number | string | null;
  initialMaterialName?: string;
  label?: string;
  placeholder?: string;
  disabled?: boolean;
  required?: boolean;
  materialsList?: MaterialItem[];
  hasSerialOnly?: boolean;
  ownerId?: number | string | null;
  depotId?: number | string | null;
}>(), {
  modelValue: null,
  initialMaterialName: '',
  label: '',
  placeholder: 'Digite nome, código SKU ou categoria do material...',
  disabled: false,
  required: false,
  materialsList: () => [],
  hasSerialOnly: false,
  ownerId: null,
  depotId: null,
});

const emit = defineEmits<{
  (e: 'update:modelValue', value: number | null): void;
  (e: 'select', material: MaterialItem | null): void;
}>();

const containerRef = ref<HTMLElement | null>(null);
const inputRef = ref<HTMLInputElement | null>(null);

const isOpen = ref(false);
const isLoading = ref(false);
const query = ref('');
const results = ref<MaterialItem[]>([]);
const hasSearched = ref(false);
const highlightedIndex = ref(-1);

const selectedMaterial = ref<MaterialItem | null>(null);
let debounceTimeout: any = null;

const displayText = computed(() => {
  if (isOpen.value) {
    return query.value;
  }
  if (selectedMaterial.value) {
    const unitStr = selectedMaterial.value.unit?.code ? ` [${selectedMaterial.value.unit.code}]` : '';
    const serialStr = selectedMaterial.value.has_serial ? ' (Serial)' : '';
    const aliasStr = selectedMaterial.value.has_owner_alias && selectedMaterial.value.system_code
      ? ` (SKU: ${selectedMaterial.value.system_code})`
      : '';
    return `${selectedMaterial.value.code} - ${selectedMaterial.value.name}${aliasStr}${unitStr}${serialStr}`;
  }
  if (props.initialMaterialName) {
    return props.initialMaterialName;
  }
  return query.value;
});

const filterLocalList = (searchTerm: string): MaterialItem[] => {
  if (!props.materialsList || props.materialsList.length === 0) return [];
  const term = searchTerm.toLowerCase().trim();
  let list = props.materialsList;
  if (props.hasSerialOnly) {
    list = list.filter(m => m.has_serial);
  }
  if (!term) {
    return list.slice(0, 20);
  }
  return list.filter(m => {
    const nameMatch = m.name?.toLowerCase().includes(term);
    const codeMatch = m.code?.toLowerCase().includes(term);
    const catMatch = m.category?.toLowerCase().includes(term);
    const ownerCodeMatch = m.owner_code?.toLowerCase().includes(term);
    const ownerNameMatch = m.owner_name?.toLowerCase().includes(term);
    const sysCodeMatch = m.system_code?.toLowerCase().includes(term);
    return nameMatch || codeMatch || catMatch || ownerCodeMatch || ownerNameMatch || sysCodeMatch;
  }).slice(0, 20);
};

const searchMaterials = async (searchTerm: string) => {
  hasSearched.value = true;

  // Primeiro tenta filtrar na lista local se disponível (apenas se não houver ownerId/depotId específico)
  if (!props.ownerId && !props.depotId && props.materialsList && props.materialsList.length > 0) {
    const localMatches = filterLocalList(searchTerm);
    if (localMatches.length > 0 || searchTerm.length < 2) {
      results.value = localMatches;
      highlightedIndex.value = results.value.length > 0 ? 0 : -1;
      isLoading.value = false;
      return;
    }
  }

  if (searchTerm.length < 2 && !props.ownerId && !props.depotId) {
    results.value = filterLocalList('');
    isLoading.value = false;
    return;
  }

  isLoading.value = true;
  try {
    const params: any = {
      search: searchTerm,
      active_only: true,
    };
    if (props.hasSerialOnly) {
      params.has_serial = true;
    }
    if (props.ownerId) {
      params.owner_id = props.ownerId;
    }
    if (props.depotId) {
      params.depot_id = props.depotId;
    }

    const response = await axios.get('/api/v1/materials', { params });
    const apiData = response.data?.data || [];
    results.value = apiData;
    highlightedIndex.value = results.value.length > 0 ? 0 : -1;
  } catch (error) {
    console.error('Erro ao buscar materiais:', error);
    // Fallback para busca local
    results.value = filterLocalList(searchTerm);
  } finally {
    isLoading.value = false;
  }
};

const onInput = (event: Event) => {
  const target = event.target as HTMLInputElement;
  query.value = target.value;
  isOpen.value = true;

  clearTimeout(debounceTimeout);
  debounceTimeout = setTimeout(() => {
    searchMaterials(query.value.trim());
  }, 250);
};

const onFocus = () => {
  if (props.disabled) return;
  isOpen.value = true;
  if (!query.value && selectedMaterial.value) {
    query.value = selectedMaterial.value.name;
    searchMaterials(query.value);
  } else if (!query.value && props.initialMaterialName) {
    query.value = props.initialMaterialName;
    searchMaterials(query.value);
  } else {
    searchMaterials(query.value);
  }
};

const selectMaterial = (material: MaterialItem) => {
  selectedMaterial.value = material;
  query.value = '';
  isOpen.value = false;
  emit('update:modelValue', material.id);
  emit('select', material);
};

const selectHighlighted = () => {
  if (highlightedIndex.value >= 0 && highlightedIndex.value < results.value.length) {
    selectMaterial(results.value[highlightedIndex.value]);
  }
};

const navigateResults = (direction: number) => {
  if (!isOpen.value) {
    isOpen.value = true;
    return;
  }
  if (results.value.length === 0) return;

  highlightedIndex.value += direction;
  if (highlightedIndex.value < 0) {
    highlightedIndex.value = results.value.length - 1;
  } else if (highlightedIndex.value >= results.value.length) {
    highlightedIndex.value = 0;
  }
};

const clearSelection = () => {
  selectedMaterial.value = null;
  query.value = '';
  results.value = [];
  hasSearched.value = false;
  isOpen.value = false;
  emit('update:modelValue', null);
  emit('select', null);
  inputRef.value?.focus();
};

const loadInitialMaterial = async (materialId: number) => {
  if (props.materialsList && props.materialsList.length > 0) {
    const found = props.materialsList.find(m => m.id === materialId);
    if (found) {
      selectedMaterial.value = found;
      return;
    }
  }

  try {
    const response = await axios.get(`/api/v1/materials/${materialId}`);
    if (response.data?.data) {
      selectedMaterial.value = response.data.data;
    }
  } catch (err) {
    console.error('Erro ao carregar material selecionado', err);
  }
};

watch(() => props.modelValue, (newVal) => {
  if (!newVal) {
    selectedMaterial.value = null;
    query.value = '';
  } else {
    const numId = Number(newVal);
    if (!selectedMaterial.value || selectedMaterial.value.id !== numId) {
      loadInitialMaterial(numId);
    }
  }
}, { immediate: true });

const handleClickOutside = (event: MouseEvent) => {
  if (containerRef.value && !containerRef.value.contains(event.target as Node)) {
    isOpen.value = false;
    query.value = '';
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  clearTimeout(debounceTimeout);
});
</script>
