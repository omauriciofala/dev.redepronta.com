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
        class="w-full h-10 pl-3.5 pr-10 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-hidden text-sm transition disabled:opacity-60 disabled:cursor-not-allowed"
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
      v-if="isOpen && (query.length >= 2 || results.length > 0 || hasSearched)"
      class="absolute left-0 right-0 top-full mt-1.5 z-50 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl overflow-hidden max-h-64 overflow-y-auto"
    >
      <!-- Aviso de menos de 2 caracteres -->
      <div v-if="query.length < 2 && !selectedPerson" class="p-3.5 text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2">
        <Search class="w-4 h-4 text-slate-400 shrink-0" />
        <span>Digite no mínimo <strong>2 caracteres</strong> para buscar por nome ou documento...</span>
      </div>

      <!-- Carregando -->
      <div v-else-if="isLoading" class="p-4 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-center gap-2">
        <Loader2 class="w-4 h-4 animate-spin text-[#FC6714]" />
        <span>Buscando pessoas cadastradas...</span>
      </div>

      <!-- Nenhum resultado encontrado -->
      <div v-else-if="results.length === 0 && hasSearched" class="p-4 text-xs text-slate-500 dark:text-slate-400 text-center">
        Nenhuma pessoa encontrada para "<strong>{{ query }}</strong>".
      </div>

      <!-- Lista de Pessoas -->
      <ul v-else class="divide-y divide-slate-100 dark:divide-slate-800/60">
        <li
          v-for="(person, index) in results"
          :key="person.id"
          @mousedown.prevent="selectPerson(person)"
          :class="[
            highlightedIndex === index
              ? 'bg-orange-50 dark:bg-[#FC6714]/10 text-[#FC6714] dark:text-orange-400 font-semibold'
              : 'hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-800 dark:text-slate-200',
            modelValue === person.id ? 'font-semibold' : ''
          ]"
          class="px-3.5 py-2.5 text-sm cursor-pointer flex items-center justify-between transition"
        >
          <div class="flex items-center gap-2.5 truncate">
            <div class="w-7 h-7 rounded-full bg-orange-100 dark:bg-[#FC6714]/20 text-[#FC6714] flex items-center justify-center shrink-0 text-xs font-bold">
              {{ person.name ? person.name.charAt(0).toUpperCase() : 'P' }}
            </div>
            <div class="truncate">
              <span class="truncate block text-xs font-medium text-slate-900 dark:text-slate-100">{{ person.name }}</span>
              <span v-if="person.role" class="text-[11px] text-slate-400 dark:text-slate-500 block">
                {{ person.role }}
              </span>
            </div>
          </div>
          <span v-if="person.document_number" class="text-[11px] font-mono text-slate-400 dark:text-slate-500 ml-2 shrink-0">
            {{ person.document_number }}
          </span>
        </li>
      </ul>

      <!-- Rodapé do dropdown -->
      <div class="p-2 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800/80 text-[11px] text-slate-500 dark:text-slate-400 flex items-center justify-between">
        <span>Base de Pessoas e Colaboradores</span>
        <span class="font-mono">ERP Rede Pronta</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Search, Loader2, X, User } from 'lucide-vue-next';
import axios from 'axios';

interface Person {
  id: number;
  name: string;
  document_number?: string;
  role?: string;
  status?: string;
}

const props = withDefaults(defineProps<{
  modelValue: number | null;
  initialPersonName?: string;
  label?: string;
  placeholder?: string;
  disabled?: boolean;
  required?: boolean;
  role?: string;
}>(), {
  modelValue: null,
  initialPersonName: '',
  label: '',
  placeholder: 'Digite ao menos 2 letras do nome ou documento...',
  disabled: false,
  required: false,
  role: '',
});

const emit = defineEmits<{
  (e: 'update:modelValue', value: number | null): void;
  (e: 'select', person: Person | null): void;
}>();

const containerRef = ref<HTMLElement | null>(null);
const inputRef = ref<HTMLInputElement | null>(null);

const isOpen = ref(false);
const isLoading = ref(false);
const query = ref('');
const results = ref<Person[]>([]);
const hasSearched = ref(false);
const highlightedIndex = ref(-1);

const selectedPerson = ref<Person | null>(null);
let debounceTimeout: any = null;

const displayText = computed(() => {
  if (isOpen.value) {
    return query.value;
  }
  if (selectedPerson.value) {
    const role = selectedPerson.value.role ? ` (${selectedPerson.value.role})` : '';
    return `${selectedPerson.value.name}${role}`;
  }
  if (props.initialPersonName) {
    return props.initialPersonName;
  }
  return query.value;
});

const searchPeople = async (searchTerm: string) => {
  if (searchTerm.length < 2) {
    results.value = [];
    isLoading.value = false;
    hasSearched.value = false;
    return;
  }

  isLoading.value = true;
  hasSearched.value = true;

  try {
    const params: any = {
      search: searchTerm,
      status: 'active',
      per_page: 15,
    };
    if (props.role) {
      params.role = props.role;
    }

    const response = await axios.get('/api/v1/people', { params });
    results.value = response.data.data || [];
    highlightedIndex.value = results.value.length > 0 ? 0 : -1;
  } catch (error) {
    console.error('Erro ao buscar pessoas:', error);
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
  if (query.value.trim().length >= 2) {
    debounceTimeout = setTimeout(() => {
      searchPeople(query.value.trim());
    }, 300);
  } else {
    results.value = [];
    hasSearched.value = false;
  }
};

const onFocus = () => {
  if (props.disabled) return;
  isOpen.value = true;
  if (!query.value && selectedPerson.value) {
    query.value = selectedPerson.value.name;
    searchPeople(query.value);
  } else if (!query.value && props.initialPersonName) {
    query.value = props.initialPersonName;
    searchPeople(query.value);
  } else if (query.value.length >= 2) {
    searchPeople(query.value);
  }
};

const selectPerson = (person: Person) => {
  selectedPerson.value = person;
  query.value = '';
  isOpen.value = false;
  emit('update:modelValue', person.id);
  emit('select', person);
};

const selectHighlighted = () => {
  if (highlightedIndex.value >= 0 && highlightedIndex.value < results.value.length) {
    selectPerson(results.value[highlightedIndex.value]);
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
  selectedPerson.value = null;
  query.value = '';
  results.value = [];
  hasSearched.value = false;
  isOpen.value = false;
  emit('update:modelValue', null);
  emit('select', null);
  inputRef.value?.focus();
};

const loadInitialPerson = async (personId: number) => {
  try {
    const response = await axios.get(`/api/v1/people/${personId}`);
    if (response.data.data) {
      selectedPerson.value = response.data.data;
    }
  } catch (err) {
    console.error('Erro ao carregar pessoa selecionada', err);
  }
};

watch(() => props.modelValue, (newVal) => {
  if (!newVal) {
    selectedPerson.value = null;
    query.value = '';
  } else if (!selectedPerson.value || selectedPerson.value.id !== newVal) {
    loadInitialPerson(newVal);
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
