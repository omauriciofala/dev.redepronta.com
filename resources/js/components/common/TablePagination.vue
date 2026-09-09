<template>
  <div class="px-4 py-3 border-t border-slate-200 dark:border-[#14147A] flex flex-col md:flex-row items-center justify-between gap-3.5 text-xs text-slate-600 dark:text-slate-400 bg-slate-50/60 dark:bg-[#03032E]/70 transition-colors duration-200">
    
    <!-- BLOCO ESQUERDO: QUANTIDADE DE REGISTROS POR PÁGINA -->
    <div class="flex items-center gap-2 order-2 md:order-1">
      <div class="relative inline-flex items-center">
        <select
          :value="perPage"
          :disabled="loading"
          @change="onPerPageChange"
          aria-label="Registros por página"
          class="h-8 pl-2.5 pr-7 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] shadow-2xs transition cursor-pointer appearance-none disabled:opacity-50"
        >
          <option v-for="opt in perPageOptions" :key="opt" :value="opt">
            {{ opt }}
          </option>
        </select>
        <ChevronDown class="w-3.5 h-3.5 absolute right-2 text-slate-400 pointer-events-none" />
      </div>
      <span class="text-xs text-slate-600 dark:text-slate-400 whitespace-nowrap font-medium">
        Registros por página
      </span>
    </div>

    <!-- BLOCO CENTRAL: NAVEGAÇÃO SEQUENCIAL & NÚMEROS DE PÁGINA -->
    <div class="flex items-center gap-1.5 flex-wrap justify-center order-1 md:order-2">
      <!-- Botão ‹ Anterior -->
      <button
        type="button"
        :disabled="currentPage <= 1 || loading"
        @click="changePage(currentPage - 1)"
        class="inline-flex items-center justify-center h-8 px-3 rounded-lg border border-slate-300 dark:border-slate-700 hover:bg-white dark:hover:bg-slate-800 hover:border-[#FC6714] hover:text-[#FC6714] disabled:opacity-40 disabled:hover:border-slate-300 dark:disabled:hover:border-slate-700 disabled:hover:bg-transparent disabled:hover:text-inherit font-medium text-xs transition cursor-pointer disabled:cursor-not-allowed shadow-2xs select-none focus:outline-none focus:ring-2 focus:ring-[#FC6714]/30"
      >
        <span>‹ Anterior</span>
      </button>

      <!-- Lista de Badges / Botões Numéricos de Páginas -->
      <div class="flex items-center gap-1">
        <template v-for="(item, index) in visiblePages" :key="index">
          <!-- Reticências (...) -->
          <span
            v-if="item === '...'"
            class="w-6 h-8 flex items-center justify-center text-xs font-bold text-slate-400 select-none"
          >
            …
          </span>

          <!-- Página Atual (Destaque Ativo) -->
          <button
            v-else-if="item === currentPage"
            type="button"
            disabled
            class="min-w-8 h-8 px-2.5 flex items-center justify-center rounded-lg font-bold text-xs bg-[#FC6714] text-white border border-[#FC6714] shadow-2xs select-none cursor-default"
            :title="`Página ${item} (Atual)`"
          >
            {{ item }}
          </button>

          <!-- Outras Páginas Clicáveis -->
          <button
            v-else
            type="button"
            :disabled="loading"
            @click="changePage(Number(item))"
            class="min-w-8 h-8 px-2 flex items-center justify-center rounded-lg font-semibold text-xs border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 hover:border-[#FC6714] hover:text-[#FC6714] dark:hover:text-orange-400 disabled:opacity-50 transition cursor-pointer shadow-2xs select-none focus:outline-none focus:ring-2 focus:ring-[#FC6714]/30"
            :title="`Ir para página ${item}`"
          >
            {{ item }}
          </button>
        </template>
      </div>

      <!-- Botão Próximo › -->
      <button
        type="button"
        :disabled="currentPage >= lastPage || loading"
        @click="changePage(currentPage + 1)"
        class="inline-flex items-center justify-center h-8 px-3 rounded-lg border border-slate-300 dark:border-slate-700 hover:bg-white dark:hover:bg-slate-800 hover:border-[#FC6714] hover:text-[#FC6714] disabled:opacity-40 disabled:hover:border-slate-300 dark:disabled:hover:border-slate-700 disabled:hover:bg-transparent disabled:hover:text-inherit font-medium text-xs transition cursor-pointer disabled:cursor-not-allowed shadow-2xs select-none focus:outline-none focus:ring-2 focus:ring-[#FC6714]/30"
      >
        <span>Próximo ›</span>
      </button>
    </div>

    <!-- BLOCO DIREITO: IR PARA PÁGINA ESPECÍFICA -->
    <div class="flex items-center gap-2 order-3">
      <span class="text-xs text-slate-600 dark:text-slate-400 whitespace-nowrap font-medium">
        Ir para página
      </span>
      <input
        type="number"
        v-model.number="targetPageInput"
        :min="1"
        :max="lastPage"
        :disabled="loading || lastPage <= 1"
        @keydown.enter.prevent="submitDirectPage"
        aria-label="Número da página para navegação direta"
        class="w-14 h-8 px-1 text-center text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] shadow-2xs transition [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none disabled:opacity-50"
      />
      <button
        type="button"
        @click="submitDirectPage"
        :disabled="loading || !isTargetPageValid"
        class="h-8 px-3 rounded-lg bg-[#FC6714] hover:bg-[#e0580e] disabled:opacity-40 disabled:hover:bg-[#FC6714] text-white font-semibold text-xs shadow-2xs transition cursor-pointer disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-[#FC6714]/40"
      >
        Ok
      </button>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { ChevronDown } from 'lucide-vue-next';

interface Props {
  currentPage?: number;
  lastPage?: number;
  totalRecords?: number;
  perPage?: number;
  perPageOptions?: number[];
  fromRecord?: number;
  toRecord?: number;
  loading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  currentPage: 1,
  lastPage: 1,
  totalRecords: 0,
  perPage: 25,
  perPageOptions: () => [10, 25, 50, 100],
  fromRecord: undefined,
  toRecord: undefined,
  loading: false,
});

const emit = defineEmits<{
  (e: 'update:currentPage', page: number): void;
  (e: 'update:perPage', perPage: number): void;
  (e: 'changePage', page: number): void;
  (e: 'changePerPage', perPage: number): void;
}>();

const targetPageInput = ref<number>(props.currentPage);

watch(() => props.currentPage, (val) => {
  targetPageInput.value = val;
});

const isTargetPageValid = computed(() => {
  if (!targetPageInput.value) return false;
  const val = Number(targetPageInput.value);
  return Number.isInteger(val) && val >= 1 && val <= props.lastPage;
});

type PageItem = number | '...';

const visiblePages = computed<PageItem[]>(() => {
  const total = props.lastPage;
  const current = props.currentPage;

  if (total <= 1) {
    return [1];
  }

  if (total <= 7) {
    return Array.from({ length: total }, (_, i) => i + 1);
  }

  // Mais de 7 páginas: janela com elipses
  if (current <= 4) {
    return [1, 2, 3, 4, 5, '...', total];
  }

  if (current >= total - 3) {
    return [1, '...', total - 4, total - 3, total - 2, total - 1, total];
  }

  return [1, '...', current - 1, current, current + 1, '...', total];
});

const changePage = (page: number) => {
  if (props.loading) return;
  if (page < 1 || page > props.lastPage) return;
  emit('update:currentPage', page);
  emit('changePage', page);
};

const submitDirectPage = () => {
  if (!targetPageInput.value) {
    targetPageInput.value = props.currentPage;
    return;
  }
  let page = Math.floor(Number(targetPageInput.value));
  if (isNaN(page) || page < 1) {
    page = 1;
  } else if (page > props.lastPage) {
    page = props.lastPage;
  }
  targetPageInput.value = page;
  if (page !== props.currentPage) {
    changePage(page);
  }
};

const onPerPageChange = (event: Event) => {
  const select = event.target as HTMLSelectElement;
  const newPerPage = Number(select.value);
  emit('update:perPage', newPerPage);
  emit('changePerPage', newPerPage);
  // Reseta para a página 1 ao trocar a quantidade de registros
  emit('update:currentPage', 1);
  emit('changePage', 1);
};
</script>
