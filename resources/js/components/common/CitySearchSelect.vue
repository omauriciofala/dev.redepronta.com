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
        class="w-full h-10 pl-3.5 pr-10 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 focus:ring-2 focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] focus:border-transparent outline-hidden text-sm transition disabled:opacity-60 disabled:cursor-not-allowed"
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
      v-if="isOpen && (query.length >= 3 || results.length > 0 || hasSearched)"
      class="absolute left-0 right-0 top-full mt-1.5 z-50 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl overflow-hidden max-h-64 overflow-y-auto"
    >
      <!-- Aviso de menos de 3 caracteres -->
      <div v-if="query.length < 3 && !selectedCity" class="p-3.5 text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2">
        <Search class="w-4 h-4 text-slate-400 shrink-0" />
        <span>Digite no mínimo <strong>3 caracteres</strong> para iniciar a busca de cidades...</span>
      </div>

      <!-- Carregando -->
      <div v-else-if="isLoading" class="p-4 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-center gap-2">
        <Loader2 class="w-4 h-4 animate-spin text-[#FC6714]" />
        <span>Buscando cidades do Brasil...</span>
      </div>

      <!-- Nenhum resultado encontrado -->
      <div v-else-if="results.length === 0 && hasSearched" class="p-4 text-xs text-slate-500 dark:text-slate-400 text-center">
        Nenhuma cidade encontrada para "<strong>{{ query }}</strong>".
      </div>

      <!-- Lista de Cidades -->
      <ul v-else class="divide-y divide-slate-100 dark:divide-slate-800/60">
        <li
          v-for="(city, index) in results"
          :key="city.id"
          @mousedown.prevent="selectCity(city)"
          :class="[
            highlightedIndex === index
              ? 'bg-orange-50 dark:bg-[#FC6714]/10 text-[#FC6714] dark:text-orange-400 font-semibold'
              : 'hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-800 dark:text-slate-200',
            modelValue === city.id ? 'font-semibold' : ''
          ]"
          class="px-3.5 py-2.5 text-sm cursor-pointer flex items-center justify-between transition"
        >
          <div class="flex items-center gap-2.5 truncate">
            <MapPin class="w-4 h-4 shrink-0 text-[#FC6714]" />
            <span class="truncate">{{ city.name }}</span>
            <span class="px-1.5 py-0.5 rounded-sm text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
              {{ city.state?.code || city.state_code || 'UF' }}
            </span>
          </div>
          <span class="text-[11px] font-mono text-slate-400 dark:text-slate-500 ml-2 shrink-0">
            IBGE: {{ city.ibge_code }}
          </span>
        </li>
      </ul>

      <!-- Rodapé do dropdown -->
      <div class="p-2 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800/80 text-[11px] text-slate-500 dark:text-slate-400 flex items-center justify-between">
        <span>Total de 5.570 municípios do Brasil</span>
        <span class="font-mono">IBGE / Base Oficial</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Search, Loader2, X, MapPin } from 'lucide-vue-next';
import axios from 'axios';

interface City {
  id: number;
  name: string;
  ibge_code?: string;
  state_id?: number;
  state?: {
    id: number;
    code: string;
    name: string;
  };
  state_code?: string;
}

const props = withDefaults(defineProps<{
  modelValue: number | null;
  initialCityName?: string;
  initialStateCode?: string;
  initialIbgeCode?: string;
  label?: string;
  placeholder?: string;
  disabled?: boolean;
  required?: boolean;
}>(), {
  modelValue: null,
  initialCityName: '',
  initialStateCode: '',
  initialIbgeCode: '',
  label: '',
  placeholder: 'Digite ao menos 3 letras da cidade (ex: Curitiba, Campinas)...',
  disabled: false,
  required: false,
});

const emit = defineEmits<{
  (e: 'update:modelValue', value: number | null): void;
  (e: 'select', city: City | null): void;
}>();

const containerRef = ref<HTMLElement | null>(null);
const inputRef = ref<HTMLInputElement | null>(null);

const isOpen = ref(false);
const isLoading = ref(false);
const query = ref('');
const results = ref<City[]>([]);
const hasSearched = ref(false);
const highlightedIndex = ref(-1);

const selectedCity = ref<City | null>(null);
let debounceTimeout: any = null;

// Formata exibição do campo de texto
const displayText = computed(() => {
  if (isOpen.value) {
    return query.value;
  }
  if (selectedCity.value) {
    const uf = selectedCity.value.state?.code || selectedCity.value.state_code || '';
    const ibge = selectedCity.value.ibge_code ? ` (IBGE: ${selectedCity.value.ibge_code})` : '';
    return `${selectedCity.value.name}${uf ? ' / ' + uf : ''}${ibge}`;
  }
  if (props.initialCityName) {
    const uf = props.initialStateCode ? ` / ${props.initialStateCode}` : '';
    const ibge = props.initialIbgeCode ? ` (IBGE: ${props.initialIbgeCode})` : '';
    return `${props.initialCityName}${uf}${ibge}`;
  }
  return query.value;
});

const searchCities = async (searchTerm: string) => {
  if (searchTerm.length < 3) {
    results.value = [];
    isLoading.value = false;
    hasSearched.value = false;
    return;
  }

  isLoading.value = true;
  hasSearched.value = true;

  try {
    const response = await axios.get('/api/v1/cities', {
      params: { q: searchTerm },
    });
    results.value = response.data.data || [];
    highlightedIndex.value = results.value.length > 0 ? 0 : -1;
  } catch (error) {
    console.error('Erro ao buscar cidades:', error);
    results.value = [];
  } finally {
    isLoading.value = false;
  }
};

const onInput = (event: Event) => {
  const target = event.target as HTMLInputElement;
  query.value = target.value;
  isOpen.value = true;

  clearTimeout(debounceTimeout);
  if (query.value.trim().length >= 3) {
    debounceTimeout = setTimeout(() => {
      searchCities(query.value.trim());
    }, 300);
  } else {
    results.value = [];
    hasSearched.value = false;
  }
};

const onFocus = () => {
  if (props.disabled) return;
  isOpen.value = true;
  if (!query.value && selectedCity.value) {
    query.value = selectedCity.value.name;
    searchCities(query.value);
  } else if (!query.value && props.initialCityName) {
    query.value = props.initialCityName;
    searchCities(query.value);
  } else if (query.value.length >= 3) {
    searchCities(query.value);
  }
};

const selectCity = (city: City) => {
  selectedCity.value = city;
  query.value = '';
  isOpen.value = false;
  emit('update:modelValue', city.id);
  emit('select', city);
};

const selectHighlighted = () => {
  if (highlightedIndex.value >= 0 && highlightedIndex.value < results.value.length) {
    selectCity(results.value[highlightedIndex.value]);
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
  selectedCity.value = null;
  query.value = '';
  results.value = [];
  hasSearched.value = false;
  isOpen.value = false;
  emit('update:modelValue', null);
  emit('select', null);
  inputRef.value?.focus();
};

// Carrega dados da cidade quando um modelValue existe inicialmente
const loadInitialCity = async (cityId: number) => {
  try {
    const response = await axios.get('/api/v1/cities', {
      params: { id: cityId },
    });
    if (response.data.data && response.data.data.length > 0) {
      selectedCity.value = response.data.data[0];
    }
  } catch (err) {
    console.error('Erro ao carregar cidade selecionada', err);
  }
};

watch(() => props.modelValue, (newVal) => {
  if (!newVal) {
    selectedCity.value = null;
    query.value = '';
  } else if (!selectedCity.value || selectedCity.value.id !== newVal) {
    loadInitialCity(newVal);
  }
}, { immediate: true });

// Fecha o dropdown ao clicar fora
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
