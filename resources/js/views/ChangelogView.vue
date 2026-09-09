<template>
  <div class="py-8 w-full space-y-8">
    <!-- Header do Change-log -->
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-800/80">
      <div>
        <div class="flex items-center gap-3">
          <div class="p-2.5 rounded-xl bg-purple-600/10 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400">
            <History class="w-6 h-6" />
          </div>
          <div>
            <div class="flex items-center gap-2.5">
              <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">Change-log do Sistema</h1>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                Histórico Vivo de Commits
              </span>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
              Registro cronológico e rastreabilidade total de cada modificação e evolução do ERP Rede Pronta.
            </p>
          </div>
        </div>
      </div>

      <!-- Ações do Header -->
      <div class="flex items-center gap-3">
        <button
          type="button"
          @click="fetchChangelog"
          :disabled="isLoading"
          class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition shadow-2xs cursor-pointer disabled:opacity-50"
          title="Recarregar histórico"
        >
          <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': isLoading }" />
          <span>Recarregar</span>
        </button>
      </div>
    </header>

    <!-- Cards de Métricas / Resumo dos Commits -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs">
        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total de Modificações</p>
        <p class="text-2xl font-bold text-slate-900 dark:text-slate-100 mt-1">{{ commits.length }}</p>
        <p class="text-xs text-slate-400 mt-0.5">Commits registrados</p>
      </div>

      <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs">
        <p class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Funcionalidades</p>
        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ countByType('feat') }}</p>
        <p class="text-xs text-slate-400 mt-0.5">Commits tipo 'feat'</p>
      </div>

      <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs">
        <p class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Correções & Refinos</p>
        <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ countByType('fix') }}</p>
        <p class="text-xs text-slate-400 mt-0.5">Commits tipo 'fix'</p>
      </div>

      <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs">
        <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Padrão do Idioma</p>
        <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-1.5">
          <Check class="w-5 h-5" />
          <span>pt-BR (100%)</span>
        </p>
        <p class="text-xs text-slate-400 mt-0.5">Português do Brasil</p>
      </div>
    </div>

    <!-- Filtros de Busca e Tipo -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
      <div class="relative w-full sm:w-80">
        <Search class="w-4 h-4 absolute left-3.5 top-3 text-slate-400" />
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Filtrar por mensagem, escopo ou hash..."
          class="w-full h-10 pl-10 pr-3.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-hidden transition"
        />
      </div>

      <!-- Filtros por tipo -->
      <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto">
        <button
          v-for="filter in typeFilters"
          :key="filter.value"
          type="button"
          @click="selectedType = filter.value"
          :class="selectedType === filter.value
            ? 'bg-purple-600 text-white font-semibold shadow-xs'
            : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium'"
          class="px-3 py-1.5 text-xs rounded-lg transition cursor-pointer whitespace-nowrap"
        >
          {{ filter.label }}
        </button>
      </div>
    </div>

    <!-- Timeline de Commits -->
    <div v-if="isLoading" class="p-12 text-center text-slate-500 flex flex-col items-center justify-center gap-3">
      <Loader2 class="w-8 h-8 animate-spin text-purple-600" />
      <span class="text-sm font-medium">Carregando histórico do Git em tempo real...</span>
    </div>

    <div v-else-if="filteredCommits.length === 0" class="p-12 text-center rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
      <GitCommit class="w-10 h-10 text-slate-400 mx-auto mb-3 opacity-50" />
      <p class="text-base font-semibold text-slate-800 dark:text-slate-200">Nenhum commit encontrado</p>
      <p class="text-xs text-slate-500 mt-1">Tente ajustar seus termos de pesquisa ou filtros.</p>
    </div>

    <!-- Lista em Linha do Tempo -->
    <div v-else class="relative pl-6 sm:pl-8 space-y-6 before:absolute before:left-3 sm:before:left-4 before:top-3 before:bottom-3 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-800">
      <div
        v-for="commit in filteredCommits"
        :key="commit.hash"
        class="relative group"
      >
        <!-- Ponto / Nó Conector da Linha do Tempo -->
        <div
          :class="getTypeBadge(commit.type).dotClass"
          class="absolute -left-6 sm:-left-8 top-3.5 w-3 h-3 rounded-full ring-4 ring-white dark:ring-slate-950 transition group-hover:scale-125"
        />

        <!-- Card do Commit -->
        <div class="p-5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs hover:border-slate-300 dark:hover:border-slate-700 transition space-y-2.5">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
              <!-- Badge do Tipo de Commit -->
              <span
                :class="getTypeBadge(commit.type).badgeClass"
                class="px-2.5 py-0.5 rounded-md text-xs font-bold uppercase tracking-wider"
              >
                {{ commit.type }}
              </span>

              <!-- Escopo se houver -->
              <span v-if="commit.scope" class="px-2 py-0.5 rounded-md text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono">
                ({{ commit.scope }})
              </span>
            </div>

            <!-- Hash e Data -->
            <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
              <span class="font-mono bg-slate-100 dark:bg-slate-800/80 px-2 py-0.5 rounded-md font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1">
                <GitCommit class="w-3.5 h-3.5" />
                {{ commit.short_hash }}
              </span>
              <span class="flex items-center gap-1" :title="commit.date_formatted">
                <Clock class="w-3.5 h-3.5" />
                {{ commit.relative_time }}
              </span>
            </div>
          </div>

          <!-- Mensagem em Português do Brasil -->
          <p class="text-sm font-semibold text-slate-900 dark:text-slate-100 leading-snug">
            {{ commit.message }}
          </p>

          <!-- Metadados do Autor e Horário Exato -->
          <div class="pt-2 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
            <div class="flex items-center gap-2">
              <div class="w-5 h-5 rounded-full bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-400 flex items-center justify-center font-bold text-[10px]">
                {{ commit.author.charAt(0) }}
              </div>
              <span class="font-medium text-slate-700 dark:text-slate-300">{{ commit.author }}</span>
            </div>
            <span>{{ commit.date_formatted }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Regra de Commits Documentada na Própria Página -->
    <div class="p-5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/60 space-y-2">
      <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
        <ShieldCheck class="w-4 h-4 text-emerald-500" />
        <span>Diretriz Inegociável de Governança de Código</span>
      </div>
      <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
        Todos os commits do repositório <strong>ERP Rede Pronta</strong> são obrigatoriamente escritos em <strong>Português do Brasil (pt-BR)</strong> no formato <code>tipo(escopo): mensagem clara e descritiva</code>. Commits em inglês ou genéricos são estritamente rejeitados pelas diretrizes do projeto.
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import {
  History, RefreshCw, GitCommit, Clock, Search,
  Check, Loader2, ShieldCheck
} from 'lucide-vue-next';
import axios from 'axios';

interface CommitItem {
  hash: string;
  short_hash: string;
  author: string;
  email: string;
  date_formatted: string;
  date_iso: string;
  relative_time: string;
  type: string;
  scope: string | null;
  subject: string;
  message: string;
}

const commits = ref<CommitItem[]>([]);
const isLoading = ref(true);
const searchQuery = ref('');
const selectedType = ref('all');

const typeFilters = [
  { label: 'Todos', value: 'all' },
  { label: 'Funcionalidades (feat)', value: 'feat' },
  { label: 'Correções (fix)', value: 'fix' },
  { label: 'Estrutura (refactor/chore)', value: 'structure' },
  { label: 'Docs', value: 'docs' },
];

const fetchChangelog = async () => {
  isLoading.value = true;
  try {
    const res = await axios.get('/api/v1/changelog');
    commits.value = res.data.data;
  } catch (err) {
    console.error('Erro ao carregar changelog do Git', err);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchChangelog();
});

const countByType = (type: string) => {
  return commits.value.filter(c => c.type === type).length;
};

const filteredCommits = computed(() => {
  let list = commits.value;

  if (selectedType.value !== 'all') {
    if (selectedType.value === 'structure') {
      list = list.filter(c => ['refactor', 'chore', 'config'].includes(c.type));
    } else {
      list = list.filter(c => c.type === selectedType.value);
    }
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase();
    list = list.filter(c =>
      c.message.toLowerCase().includes(q) ||
      (c.scope && c.scope.toLowerCase().includes(q)) ||
      c.short_hash.toLowerCase().includes(q) ||
      c.author.toLowerCase().includes(q)
    );
  }

  return list;
});

const getTypeBadge = (type: string) => {
  switch (type) {
    case 'feat':
      return {
        badgeClass: 'bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300 border border-blue-200 dark:border-blue-800',
        dotClass: 'bg-blue-600',
      };
    case 'fix':
      return {
        badgeClass: 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200 dark:border-amber-800',
        dotClass: 'bg-amber-500',
      };
    case 'refactor':
      return {
        badgeClass: 'bg-purple-100 text-purple-800 dark:bg-purple-950/80 dark:text-purple-300 border border-purple-200 dark:border-purple-800',
        dotClass: 'bg-purple-600',
      };
    case 'docs':
      return {
        badgeClass: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/80 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800',
        dotClass: 'bg-indigo-600',
      };
    case 'chore':
    case 'config':
      return {
        badgeClass: 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700',
        dotClass: 'bg-slate-500',
      };
    default:
      return {
        badgeClass: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700',
        dotClass: 'bg-slate-400',
      };
  }
};
</script>
