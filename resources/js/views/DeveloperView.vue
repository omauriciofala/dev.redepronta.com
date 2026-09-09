<template>
  <div class="space-y-6">
    <!-- Cabeçalho da Página -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-200 dark:border-slate-800">
      <div>
        <div class="flex items-center gap-3">
          <div class="p-2.5 rounded-xl bg-orange-50 dark:bg-[#FC6714]/15 border border-orange-200 dark:border-[#FC6714]/30 text-[#FC6714] shadow-2xs">
            <Terminal class="w-6 h-6" />
          </div>
          <div>
            <div class="flex items-center gap-2.5 flex-wrap">
              <h1 class="text-2xl font-bold font-heading tracking-tight text-slate-900 dark:text-slate-100">
                Painel do Desenvolvedor
              </h1>
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-950/70 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Ambiente de Desenvolvimento
              </span>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
              Utilitários operacionais para reset do banco de dados, geração em massa de cadastros fictícios e inspeção de integridade.
            </p>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button
          type="button"
          @click="fetchStats"
          :disabled="isLoadingStats"
          class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer disabled:opacity-50 shadow-2xs"
        >
          <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': isLoadingStats }" />
          <span>{{ isLoadingStats ? 'Atualizando...' : 'Atualizar Métricas' }}</span>
        </button>
      </div>
    </div>

    <!-- Cards de Métricas em Tempo Real -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Pessoas Cadastradas -->
      <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total de Pessoas</span>
          <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-[#FC6714]/15 text-[#FC6714] flex items-center justify-center">
            <Users class="w-4 h-4" />
          </div>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="text-2xl font-bold font-heading text-slate-900 dark:text-slate-100">
            {{ stats.total_people.toLocaleString('pt-BR') }}
          </span>
          <span
            class="text-xs font-semibold px-2 py-0.5 rounded-md"
            :class="stats.total_people === 0 ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' : 'bg-orange-100 dark:bg-orange-950/60 text-orange-700 dark:text-orange-300'"
          >
            {{ stats.total_people === 0 ? 'Sistema Limpo' : 'Cadastros Ativos' }}
          </span>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
          {{ stats.individual_people }} PF • {{ stats.legal_people }} PJ
        </p>
      </div>

      <!-- Status dos Cadastros -->
      <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Situação Cadastral</span>
          <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
            <CheckCircle2 class="w-4 h-4" />
          </div>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="text-2xl font-bold font-heading text-emerald-600 dark:text-emerald-400">
            {{ stats.active_people.toLocaleString('pt-BR') }}
          </span>
          <span class="text-xs text-slate-500 dark:text-slate-400">
            ativos
          </span>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
          {{ stats.inactive_people }} inativos no sistema
        </p>
      </div>

      <!-- Grupos de Pessoas -->
      <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Grupos Canônicos</span>
          <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
            <FolderGit2 class="w-4 h-4" />
          </div>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="text-2xl font-bold font-heading text-slate-900 dark:text-slate-100">
            {{ stats.total_groups }}
          </span>
          <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">
            Preservados no Reset
          </span>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
          VIP, Prestadores, Fornecedores, Geral
        </p>
      </div>

      <!-- Geografia e Referência -->
      <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Base IBGE</span>
          <div class="w-8 h-8 rounded-lg bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center">
            <Database class="w-4 h-4" />
          </div>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="text-2xl font-bold font-heading text-slate-900 dark:text-slate-100">
            {{ stats.total_cities.toLocaleString('pt-BR') }}
          </span>
          <span class="text-xs text-slate-500 dark:text-slate-400">
            em {{ stats.total_states }} UFs
          </span>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
          {{ stats.total_cnaes }} CNAEs cadastrados
        </p>
      </div>
    </div>

    <!-- Bloco de Ações Principais do Desenvolvedor -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- CARD 1: RESETAR O SISTEMA -->
      <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-red-200/80 dark:border-red-900/50 shadow-xs space-y-5 flex flex-col justify-between">
        <div class="space-y-4">
          <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-xl bg-red-100 dark:bg-red-950/80 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800">
              <RotateCcw class="w-6 h-6" />
            </div>
            <div>
              <h2 class="text-lg font-bold font-heading text-slate-900 dark:text-slate-100">
                Resetar o Sistema
              </h2>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                Restaura o ambiente para o estado inicial sem cadastros de pessoas
              </p>
            </div>
          </div>

          <div class="p-4 rounded-xl bg-red-50/70 dark:bg-red-950/30 border border-red-200 dark:border-red-900/60 space-y-2 text-xs leading-relaxed text-red-900 dark:text-red-200">
            <div class="flex items-center gap-2 font-bold text-red-700 dark:text-red-300 text-sm">
              <AlertTriangle class="w-4 h-4 shrink-0" />
              <span>O que acontece ao resetar o sistema:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-slate-700 dark:text-slate-300">
              <li>Exclui <strong>todos os {{ stats.total_people }} cadastros de pessoas</strong> da base.</li>
              <li>Reseta a sequência de identificadores (IDs) da tabela de pessoas.</li>
              <li><strong>Preserva integralmente:</strong> Conta Matriz, Gêneros, Cidades, Estados (IBGE), CNAEs e Grupos de Pessoas canônicos.</li>
              <li>Deixa o sistema pronto para novas rodadas de testes limpos.</li>
            </ul>
          </div>
        </div>

        <div>
          <button
            type="button"
            @click="openResetConfirmModal"
            :disabled="isResetting || isPopulating"
            class="w-full h-11 px-4 rounded-xl bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-semibold text-sm shadow-xs transition flex items-center justify-center gap-2.5 cursor-pointer disabled:opacity-50"
          >
            <RotateCcw class="w-4 h-4" />
            <span>Resetar Sistema para o Padrão (Zerar Pessoas)</span>
          </button>
        </div>
      </div>

      <!-- CARD 2: POPULAR PESSOAS FICTÍCIAS -->
      <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs space-y-5 flex flex-col justify-between">
        <div class="space-y-4">
          <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-xl bg-orange-100 dark:bg-[#FC6714]/20 text-[#FC6714] border border-orange-200 dark:border-[#FC6714]/40">
              <UserPlus class="w-6 h-6" />
            </div>
            <div>
              <h2 class="text-lg font-bold font-heading text-slate-900 dark:text-slate-100">
                Popular Pessoas Fictícias
              </h2>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                Geração em massa de dados realistas para testes de carga e filtros
              </p>
            </div>
          </div>

          <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 space-y-2 text-xs leading-relaxed text-slate-700 dark:text-slate-300">
            <div class="flex items-center gap-2 font-bold text-slate-900 dark:text-white text-sm">
              <CheckCircle2 class="w-4 h-4 text-emerald-500 shrink-0" />
              <span>Características dos dados gerados:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-slate-600 dark:text-slate-400">
              <li><strong>50% PF / 50% PJ:</strong> Mix balanceado para validação de todas as abas e formulários.</li>
              <li><strong>Documentos Válidos:</strong> CPFs (11 dígitos) e CNPJs (14 dígitos) com algoritmos matemáticos reais.</li>
              <li><strong>Papéis Operacionais:</strong> Clientes, Fornecedores, Colaboradores, Terceirizados, Vendedores, Motoristas e Transportadoras.</li>
              <li><strong>Geolocalização Nacional:</strong> Endereços reais em cidades e estados distribuídos por todo o Brasil.</li>
            </ul>
          </div>

          <!-- Seletor de Quantidade -->
          <div class="space-y-2">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
              Quantidade de pessoas a popular:
            </label>
            <div class="grid grid-cols-4 gap-2">
              <button
                type="button"
                v-for="amt in [100, 500, 1000, 2000]"
                :key="amt"
                @click="populateCount = amt"
                :class="populateCount === amt
                  ? 'bg-[#FC6714] text-white font-bold shadow-xs'
                  : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                class="py-2 px-2 text-xs font-semibold rounded-lg transition cursor-pointer text-center"
              >
                {{ amt.toLocaleString('pt-BR') }}
              </button>
            </div>
          </div>
        </div>

        <div>
          <button
            type="button"
            @click="executePopulatePeople"
            :disabled="isPopulating || isResetting"
            class="w-full h-11 px-4 rounded-xl bg-[#FC6714] hover:bg-[#e0580e] active:bg-[#c94d0b] text-white font-semibold text-sm shadow-xs transition flex items-center justify-center gap-2.5 cursor-pointer disabled:opacity-50"
          >
            <Loader2 v-if="isPopulating" class="w-4 h-4 animate-spin" />
            <UserPlus v-else class="w-4 h-4" />
            <span>{{ isPopulating ? `Gerando ${populateCount} cadastros...` : `Popular ${populateCount.toLocaleString('pt-BR')} Pessoas Agora` }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Seção de Atalhos Rápidos para Validação -->
    <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs space-y-3">
      <div class="flex items-center justify-between">
        <h3 class="text-sm font-bold font-heading text-slate-900 dark:text-slate-100 flex items-center gap-2">
          <Compass class="w-4 h-4 text-[#FC6714]" />
          <span>Atalhos Rápidos de Inspeção do Sistema</span>
        </h3>
        <span class="text-xs text-slate-400">
          Servidor: PHP {{ stats.php_version }} • Laravel {{ stats.laravel_version }}
        </span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-1">
        <button
          type="button"
          @click="setView('people')"
          class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-slate-50/50 dark:bg-slate-800/40 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center gap-3 text-left cursor-pointer group"
        >
          <div class="p-2 rounded-lg bg-orange-100 dark:bg-[#FC6714]/20 text-[#FC6714] group-hover:scale-105 transition">
            <Users class="w-4 h-4" />
          </div>
          <div>
            <div class="text-xs font-bold text-slate-900 dark:text-white">Gestão de Pessoas</div>
            <div class="text-[11px] text-slate-500 dark:text-slate-400">Listagem, filtros e CRUD</div>
          </div>
        </button>

        <button
          type="button"
          @click="setView('design-system')"
          class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-slate-50/50 dark:bg-slate-800/40 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center gap-3 text-left cursor-pointer group"
        >
          <div class="p-2 rounded-lg bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 group-hover:scale-105 transition">
            <Palette class="w-4 h-4" />
          </div>
          <div>
            <div class="text-xs font-bold text-slate-900 dark:text-white">Design System</div>
            <div class="text-[11px] text-slate-500 dark:text-slate-400">Componentes e inputs</div>
          </div>
        </button>

        <button
          type="button"
          @click="setView('changelog')"
          class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-slate-50/50 dark:bg-slate-800/40 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center gap-3 text-left cursor-pointer group"
        >
          <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 group-hover:scale-105 transition">
            <History class="w-4 h-4" />
          </div>
          <div>
            <div class="text-xs font-bold text-slate-900 dark:text-white">Change-log Vivo</div>
            <div class="text-[11px] text-slate-500 dark:text-slate-400">Histórico de commits</div>
          </div>
        </button>

        <button
          type="button"
          @click="setView('integrations')"
          class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-slate-50/50 dark:bg-slate-800/40 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center gap-3 text-left cursor-pointer group"
        >
          <div class="p-2 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 group-hover:scale-105 transition">
            <Network class="w-4 h-4" />
          </div>
          <div>
            <div class="text-xs font-bold text-slate-900 dark:text-white">APIs & Integrações</div>
            <div class="text-[11px] text-slate-500 dark:text-slate-400">ViaCEP, CNPJá e IBGE</div>
          </div>
        </button>
      </div>
    </div>

    <!-- Toast de Notificação -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div
        v-if="toastMessage"
        class="fixed top-5 right-5 z-50 max-w-md p-4 rounded-xl shadow-2xl border flex items-center gap-3 text-sm font-semibold"
        :class="toastType === 'success' ? 'bg-emerald-50 dark:bg-emerald-950/90 text-emerald-800 dark:text-emerald-200 border-emerald-300 dark:border-emerald-800' : 'bg-red-50 dark:bg-red-950/90 text-red-800 dark:text-red-200 border-red-300 dark:border-red-800'"
      >
        <CheckCircle2 v-if="toastType === 'success'" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" />
        <AlertCircle v-else class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0" />
        <span>{{ toastMessage }}</span>
      </div>
    </Transition>

    <!-- Modal de Confirmação de Reset do Sistema -->
    <BaseModal
      v-model="isResetModalOpen"
      title="Confirmar Reset do Sistema"
      size="sm"
      @close="isResetModalOpen = false"
    >
      <div class="space-y-4">
        <div class="p-4 rounded-xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/60 text-red-800 dark:text-red-200 text-xs leading-relaxed flex items-start gap-3">
          <AlertTriangle class="w-6 h-6 shrink-0 text-red-600 dark:text-red-400 mt-0.5" />
          <div class="space-y-2">
            <p class="font-bold text-sm text-red-900 dark:text-red-100">
              Tem certeza absoluta que deseja resetar o sistema?
            </p>
            <p>
              Esta ação removerá todos os <strong>{{ stats.total_people }} cadastros de pessoas</strong> do banco de dados e restaurará o estado inicial para <strong>0 cadastros</strong>.
            </p>
            <p class="text-slate-600 dark:text-slate-400">
              Os dados canônicos essenciais (Conta Matriz, Cidades, Estados e Grupos de Pessoas) serão preservados.
            </p>
          </div>
        </div>
      </div>

      <template #footer>
        <div class="flex items-center justify-end gap-2.5 w-full">
          <button
            type="button"
            @click="isResetModalOpen = false"
            :disabled="isResetting"
            class="h-9 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition text-xs font-semibold cursor-pointer disabled:opacity-50"
          >
            Cancelar
          </button>
          <button
            type="button"
            @click="executeResetSystem"
            :disabled="isResetting"
            class="h-9 px-4 rounded-lg bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-semibold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer disabled:opacity-50"
          >
            <Loader2 v-if="isResetting" class="w-3.5 h-3.5 animate-spin" />
            <RotateCcw v-else class="w-3.5 h-3.5" />
            <span>{{ isResetting ? 'Resetando Sistema...' : 'Sim, Resetar Agora' }}</span>
          </button>
        </div>
      </template>
    </BaseModal>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import {
  Terminal,
  RotateCcw,
  UserPlus,
  Users,
  CheckCircle2,
  AlertTriangle,
  AlertCircle,
  RefreshCw,
  FolderGit2,
  Database,
  Compass,
  Palette,
  History,
  Network,
  Loader2,
} from 'lucide-vue-next';
import BaseModal from '../components/common/BaseModal.vue';
import { useNavigation } from '../composables/useNavigation';

const { setView } = useNavigation();

const stats = reactive({
  total_people: 0,
  individual_people: 0,
  legal_people: 0,
  active_people: 0,
  inactive_people: 0,
  total_groups: 0,
  total_cities: 0,
  total_states: 0,
  total_cnaes: 0,
  total_neighborhoods: 0,
  server_time: '',
  php_version: '8.4',
  laravel_version: '13.x',
});

const isLoadingStats = ref(false);
const isResetModalOpen = ref(false);
const isResetting = ref(false);
const isPopulating = ref(false);
const populateCount = ref(1000);

const toastMessage = ref('');
const toastType = ref<'success' | 'error'>('success');
let toastTimer: any = null;

const showToast = (msg: string, type: 'success' | 'error' = 'success') => {
  toastMessage.value = msg;
  toastType.value = type;
  if (toastTimer) clearTimeout(toastTimer);
  toastTimer = setTimeout(() => {
    toastMessage.value = '';
  }, 4000);
};

const fetchStats = async () => {
  isLoadingStats.value = true;
  try {
    const res = await axios.get('/api/v1/dev/stats');
    if (res.data?.data) {
      Object.assign(stats, res.data.data);
    }
  } catch (err: any) {
    showToast('Erro ao carregar métricas do sistema.', 'error');
  } finally {
    isLoadingStats.value = false;
  }
};

const openResetConfirmModal = () => {
  isResetModalOpen.value = true;
};

const executeResetSystem = async () => {
  isResetting.value = true;
  try {
    const res = await axios.post('/api/v1/dev/reset');
    isResetModalOpen.value = false;
    showToast(res.data?.message || 'Sistema resetado para o padrão com sucesso!', 'success');
    await fetchStats();
  } catch (err: any) {
    showToast(err.response?.data?.message || 'Erro ao resetar o sistema.', 'error');
  } finally {
    isResetting.value = false;
  }
};

const executePopulatePeople = async () => {
  isPopulating.value = true;
  try {
    const res = await axios.post('/api/v1/dev/populate-people', {
      count: populateCount.value,
    });
    showToast(res.data?.message || 'População de pessoas concluída com sucesso!', 'success');
    await fetchStats();
  } catch (err: any) {
    showToast(err.response?.data?.message || 'Erro ao popular pessoas.', 'error');
  } finally {
    isPopulating.value = false;
  }
};

onMounted(() => {
  fetchStats();
});
</script>
