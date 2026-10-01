<template>
  <!-- Container Raiz Canônico Obrigatório (py-8 w-full space-y-6) -->
  <div class="py-8 w-full space-y-6 font-sans">
    <!-- Componente Canônico: 10. Topo de Página Padrão (Page Header & Breadcrumb) -->
    <BasePageHeader
      :breadcrumb-items="[
        { label: 'Início', href: '#people' },
        { label: 'Operações', href: '#tasks' },
        { label: 'Gestão de Tarefas' },
      ]"
      title="Gestão de Tarefas"
      description="Caixa de entrada universal, triagem operacional, atribuição de operadores e acompanhamento ágil de demandas."
      :icon="CheckSquare"
      icon-color-class="bg-orange-50 dark:bg-[#FC6714]/15 border border-orange-200 dark:border-[#FC6714]/30 text-[#FC6714]"
      :badge-text="`${tasksCounts.inbox || 0} no Inbox`"
      badge-variant="neutral"
    >
      <template #breadcrumb-right>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-orange-50 dark:bg-orange-950/60 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-900">
          Inbox Universal & Triagem
        </span>
      </template>

      <template #actions>
        <!-- Alternador de Visualização: Lista vs Kanban -->
        <div class="inline-flex items-center p-1 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs">
          <button
            type="button"
            @click="setViewMode('list')"
            :class="viewMode === 'list'
              ? 'bg-[#FC6714] text-white font-bold shadow-2xs'
              : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'"
            class="h-8 px-3 rounded-md text-xs transition flex items-center gap-1.5 cursor-pointer"
            title="Visualização em Lista"
          >
            <List class="w-3.5 h-3.5" />
            <span>Lista</span>
          </button>

          <button
            type="button"
            @click="setViewMode('kanban')"
            :class="viewMode === 'kanban'
              ? 'bg-[#FC6714] text-white font-bold shadow-2xs'
              : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'"
            class="h-8 px-3 rounded-md text-xs transition flex items-center gap-1.5 cursor-pointer"
            title="Visualização em Quadro Kanban"
          >
            <Columns3 class="w-3.5 h-3.5" />
            <span>Quadro Kanban</span>
          </button>
        </div>

        <!-- Botão Primário Laranja (#FC6714) -->
        <button
          type="button"
          @click="openNewTaskModal"
          class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-sm font-semibold shadow-sm transition active:scale-98 cursor-pointer focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
        >
          <Plus class="w-4 h-4" />
          <span>Nova Tarefa</span>
        </button>

        <!-- Botão Recarregar Dados -->
        <button
          type="button"
          @click="fetchTasks"
          :disabled="isLoading"
          class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 transition shadow-2xs cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
          title="Recarregar tarefas"
        >
          <RefreshCw class="w-4 h-4" :class="{ 'animate-spin text-[#FC6714]': isLoading }" />
        </button>
      </template>
    </BasePageHeader>

    <!-- Cards de Métricas Rápidas (KPIs de Triagem) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
      <div class="p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
        <div class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold uppercase">1. Inbox / Novas</div>
        <div class="text-xl font-bold font-heading text-blue-700 dark:text-blue-300 mt-1">{{ tasksCounts.inbox || 0 }}</div>
      </div>
      <div class="p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
        <div class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold uppercase">2. Em Triagem</div>
        <div class="text-xl font-bold font-heading text-amber-700 dark:text-amber-300 mt-1">{{ tasksCounts.triaged || 0 }}</div>
      </div>
      <div class="p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
        <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold uppercase">3. Resolvidas Interno</div>
        <div class="text-xl font-bold font-heading text-emerald-700 dark:text-emerald-300 mt-1">{{ tasksCounts.resolved || 0 }}</div>
      </div>
      <div class="p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
        <div class="text-[11px] text-purple-600 dark:text-purple-400 font-semibold uppercase">4. Em Chamado (SLA)</div>
        <div class="text-xl font-bold font-heading text-purple-700 dark:text-purple-300 mt-1">{{ tasksCounts.promoted || 0 }}</div>
      </div>
    </div>

    <!-- Barra de Filtros e Busca Rápida -->
    <div class="p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
      <!-- Campo de Busca -->
      <div class="flex-1 relative">
        <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
        <input
          v-model="searchTerm"
          @input="debounceFetchTasks"
          type="text"
          placeholder="Buscar por identificador TSK, assunto ou relato..."
          class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
        />
      </div>

      <!-- Filtros Rápidos -->
      <div class="flex flex-wrap items-center gap-2 shrink-0">
        <!-- Status (apenas visível no modo Lista, pois o Kanban já divide por status) -->
        <select
          v-if="viewMode === 'list'"
          v-model="statusFilter"
          @change="fetchTasks"
          class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
        >
          <option value="ALL">Todos os Status</option>
          <option value="INBOX">Inbox (Novas)</option>
          <option value="TRIAGED">Em Triagem</option>
          <option value="RESOLVED_INTERNAL">Resolvidas</option>
          <option value="PROMOTED_TICKET">Em Chamado</option>
          <option value="CANCELED">Canceladas</option>
        </select>

        <!-- Canal de Origem -->
        <select
          v-model="sourceFilter"
          @change="fetchTasks"
          class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
        >
          <option value="ALL">Todos os Canais</option>
          <option value="WHATSAPP">WhatsApp</option>
          <option value="EMAIL">E-mail</option>
          <option value="AI">IA / Triagem</option>
          <option value="API">API Webhook</option>
          <option value="MANUAL">Manual</option>
        </select>

        <!-- Prioridade -->
        <select
          v-model="priorityFilter"
          @change="fetchTasks"
          class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
        >
          <option value="ALL">Todas as Prioridades</option>
          <option value="CRITICAL">Crítica</option>
          <option value="HIGH">Alta</option>
          <option value="MEDIUM">Média</option>
          <option value="LOW">Baixa</option>
        </select>

        <!-- Operador Atribuído -->
        <select
          v-model="assignedUserFilter"
          @change="fetchTasks"
          class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
        >
          <option value="ALL">Todos os Operadores</option>
          <option value="UNASSIGNED">Não Atribuídos (Fila)</option>
          <option v-for="op in catalogs.operators" :key="op.id" :value="op.id">
            {{ op.name }}
          </option>
        </select>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- VISÃO 1: LISTA CANÔNICA DE TAREFAS                             -->
    <!-- ============================================================== -->
    <div v-if="viewMode === 'list'" class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs overflow-hidden">
      <table class="w-full text-left text-xs divide-y divide-slate-100 dark:divide-slate-800">
        <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[10px]">
          <tr>
            <th class="py-3 px-4">Identificador</th>
            <th class="py-3 px-4">Assunto & Relato</th>
            <th class="py-3 px-4">Canal</th>
            <th class="py-3 px-4">Prioridade</th>
            <th class="py-3 px-4">Operador Responsável</th>
            <th class="py-3 px-4">Status</th>
            <th class="py-3 px-4">Data</th>
            <th class="py-3 px-4 text-right">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium text-slate-700 dark:text-slate-300">
          <tr v-if="isLoading" class="text-center py-8">
            <td colspan="8" class="py-8 text-center text-slate-400">
              <Loader2 class="w-5 h-5 animate-spin mx-auto text-[#FC6714]" />
              <span class="mt-2 block text-xs">Carregando tarefas...</span>
            </td>
          </tr>

          <tr v-else-if="!tasksList.length" class="text-center py-8">
            <td colspan="8" class="py-8 text-center text-slate-400">
              <CheckSquare class="w-8 h-8 mx-auto text-slate-300 dark:text-slate-600 mb-1" />
              <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">Nenhuma tarefa encontrada.</p>
              <p class="text-[11px] text-slate-400 mt-0.5">Clique em "+ Nova Tarefa" para registrar uma demanda.</p>
            </td>
          </tr>

          <tr
            v-for="task in tasksList"
            :key="task.id"
            @click="openTaskDetail(task)"
            class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition cursor-pointer"
          >
            <td class="py-3 px-4 font-mono font-bold text-[#FC6714]">
              {{ task.task_number }}
            </td>
            <td class="py-3 px-4 max-w-sm">
              <div class="font-bold text-slate-900 dark:text-slate-100 truncate">{{ task.title }}</div>
              <div class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">
                {{ task.customer_person ? `Cliente: ${task.customer_person.name} • ` : '' }}{{ task.description || 'Sem descrição detalhada' }}
              </div>
            </td>
            <td class="py-3 px-4">
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                {{ task.source }}
              </span>
            </td>
            <td class="py-3 px-4">
              <span
                class="px-2 py-0.5 rounded-md text-[10px] font-bold"
                :class="getPriorityBadgeClass(task.priority)"
              >
                {{ formatPriority(task.priority) }}
              </span>
            </td>
            <td class="py-3 px-4">
              <div v-if="task.assigned_user" class="flex items-center gap-1.5 font-medium text-slate-800 dark:text-slate-200">
                <div class="w-5 h-5 rounded-full bg-[#FC6714]/15 text-[#FC6714] flex items-center justify-center text-[10px] font-bold">
                  {{ task.assigned_user.name.charAt(0).toUpperCase() }}
                </div>
                <span>{{ task.assigned_user.name }}</span>
              </div>
              <span v-else class="text-slate-400 italic text-[11px]">Não atribuído</span>
            </td>
            <td class="py-3 px-4">
              <span
                class="px-2 py-0.5 rounded-md text-[10px] font-bold"
                :class="getTaskStatusBadgeClass(task.status)"
              >
                {{ formatTaskStatus(task.status) }}
              </span>
            </td>
            <td class="py-3 px-4 text-slate-400 text-[11px]">
              {{ formatDate(task.created_at) }}
            </td>
            <td class="py-3 px-4 text-right" @click.stop>
              <div class="inline-flex items-center gap-1.5">
                <!-- Botão Comentários / Detalhes -->
                <button
                  type="button"
                  @click="openTaskDetail(task)"
                  class="h-7 px-2 rounded-md border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 text-[11px] font-medium transition flex items-center gap-1 cursor-pointer"
                  title="Ver Detalhes e Comentários"
                >
                  <MessageSquare class="w-3 h-3" />
                  <span v-if="task.comments_count" class="font-bold">{{ task.comments_count }}</span>
                </button>

                <!-- Botão Promover para Chamado -->
                <button
                  v-if="task.status !== 'PROMOTED_TICKET'"
                  type="button"
                  @click="openPromoteModal(task)"
                  class="h-7 px-2.5 rounded-md bg-[#FC6714]/10 hover:bg-[#FC6714] text-[#FC6714] hover:text-white font-semibold text-[11px] transition flex items-center gap-1 cursor-pointer"
                  title="Promover para Chamado com SLA"
                >
                  <Sparkles class="w-3.5 h-3.5" />
                  <span>Promover</span>
                </button>

                <!-- Botão Editar -->
                <button
                  v-if="task.status !== 'PROMOTED_TICKET'"
                  type="button"
                  @click="openEditTaskModal(task)"
                  class="p-1 rounded-md text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                  title="Editar Tarefa"
                >
                  <Pencil class="w-3.5 h-3.5" />
                </button>

                <!-- Botão Excluir -->
                <button
                  v-if="task.status !== 'PROMOTED_TICKET'"
                  type="button"
                  @click="deleteTask(task)"
                  class="p-1 rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                  title="Excluir Tarefa"
                >
                  <Trash2 class="w-3.5 h-3.5" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Paginação Canônica -->
      <TablePagination
        v-if="pagination.total > pagination.per_page"
        :current-page="pagination.current_page"
        :total-items="pagination.total"
        :per-page="pagination.per_page"
        @page-change="onPageChange"
      />
    </div>

    <!-- ============================================================== -->
    <!-- VISÃO 2: QUADRO KANBAN DE TAREFAS                              -->
    <!-- ============================================================== -->
    <div v-else-if="viewMode === 'kanban'">
      <div v-if="isLoading" class="p-12 text-center text-slate-400">
        <Loader2 class="w-6 h-6 animate-spin mx-auto text-[#FC6714] mb-2" />
        <span>Organizando cartões no Quadro Kanban...</span>
      </div>

      <TaskKanbanBoard
        v-else
        :tasks="kanbanTasks"
        @select-task="openTaskDetail"
        @promote-ticket="openPromoteModal"
        @move-status="moveTaskStatus"
      />
    </div>

    <!-- Modais e Gaveta Lateral -->
    <TaskModal
      v-model="isTaskModalOpen"
      :mode="taskModalMode"
      :task-data="selectedTask"
      @saved="fetchTasks"
    />

    <PromoteToTicketModal
      v-model="isPromoteModalOpen"
      :task="taskToPromote"
      :departments="catalogs.departments"
      @promoted="onTaskPromoted"
    />

    <TaskDetailDrawer
      v-model="isDetailDrawerOpen"
      :task="taskInDetail"
      :operators="catalogs.operators"
      @task-updated="onTaskUpdatedInDrawer"
      @promote="openPromoteModal"
    />

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
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import {
  CheckSquare,
  Plus,
  RefreshCw,
  Search,
  List,
  Columns3,
  Loader2,
  Sparkles,
  Pencil,
  Trash2,
  MessageSquare,
  CheckCircle2,
  AlertCircle,
} from 'lucide-vue-next';
import BasePageHeader from '../components/common/BasePageHeader.vue';
import TablePagination from '../components/common/TablePagination.vue';
import TaskModal from '../components/operations/TaskModal.vue';
import PromoteToTicketModal from '../components/operations/PromoteToTicketModal.vue';
import TaskDetailDrawer from '../components/operations/TaskDetailDrawer.vue';
import TaskKanbanBoard from '../components/operations/TaskKanbanBoard.vue';

type ViewMode = 'list' | 'kanban';

function getInitialViewMode(): ViewMode {
  try {
    const hash = window.location.hash || '';
    const params = new URLSearchParams(hash.split('?')[1] || '');
    const mode = params.get('view') as ViewMode;
    if (mode && ['list', 'kanban'].includes(mode)) return mode;
    const saved = localStorage.getItem('rp_tasks_view_mode') as ViewMode;
    if (saved && ['list', 'kanban'].includes(saved)) return saved;
  } catch (e) {}
  return 'list';
}

const viewMode = ref<ViewMode>(getInitialViewMode());

function setViewMode(mode: ViewMode) {
  viewMode.value = mode;
  try {
    localStorage.setItem('rp_tasks_view_mode', mode);
    const baseHash = window.location.hash.split('?')[0] || '#tasks';
    history.replaceState(null, '', `${baseHash}?view=${mode}`);
  } catch (e) {}
  fetchTasks();
}

const tasksList = ref<any[]>([]);
const kanbanTasks = ref<any[]>([]);
const isLoading = ref(false);

const searchTerm = ref('');
const statusFilter = ref('ALL');
const sourceFilter = ref('ALL');
const priorityFilter = ref('ALL');
const assignedUserFilter = ref('ALL');

const tasksCounts = reactive<any>({
  total: 0,
  inbox: 0,
  triaged: 0,
  promoted: 0,
  resolved: 0,
});

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
});

const catalogs = reactive<{
  departments: any[];
  operators: any[];
}>({
  departments: [],
  operators: [],
});

// Toast
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

let debounceTimer: any = null;
function debounceFetchTasks() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    pagination.current_page = 1;
    fetchTasks();
  }, 350);
}

const fetchCatalogs = async () => {
  try {
    const res = await axios.get('/api/v1/operations/catalogs');
    if (res.data?.data) {
      catalogs.departments = res.data.data.departments || [];
      catalogs.operators = res.data.data.operators || [];
    }
  } catch (e) {}
};

const fetchTasks = async () => {
  isLoading.value = true;
  try {
    const isKanban = viewMode.value === 'kanban';
    const res = await axios.get('/api/v1/operations/tasks', {
      params: {
        page: isKanban ? 1 : pagination.current_page,
        per_page: isKanban ? -1 : pagination.per_page,
        search: searchTerm.value || undefined,
        status: !isKanban && statusFilter.value !== 'ALL' ? statusFilter.value : undefined,
        source: sourceFilter.value !== 'ALL' ? sourceFilter.value : undefined,
        priority: priorityFilter.value !== 'ALL' ? priorityFilter.value : undefined,
        assigned_user_id: assignedUserFilter.value !== 'ALL' ? assignedUserFilter.value : undefined,
      },
    });

    if (isKanban) {
      kanbanTasks.value = res.data?.data || [];
    } else {
      tasksList.value = res.data?.data || [];
      if (res.data?.meta) {
        pagination.current_page = res.data.meta.current_page;
        pagination.last_page = res.data.meta.last_page;
        pagination.total = res.data.meta.total;
      }
    }

    if (res.data?.meta?.counts) {
      Object.assign(tasksCounts, res.data.meta.counts);
    }
  } catch (err: any) {
    showToast('Erro ao carregar tarefas.', 'error');
  } finally {
    isLoading.value = false;
  }
};

function onPageChange(page: number) {
  pagination.current_page = page;
  fetchTasks();
}

// Modais & Drawer
const isTaskModalOpen = ref(false);
const taskModalMode = ref<'create' | 'edit'>('create');
const selectedTask = ref<any>(null);

const isPromoteModalOpen = ref(false);
const taskToPromote = ref<any>(null);

const isDetailDrawerOpen = ref(false);
const taskInDetail = ref<any>(null);

function openNewTaskModal() {
  taskModalMode.value = 'create';
  selectedTask.value = null;
  isTaskModalOpen.value = true;
}

function openEditTaskModal(task: any) {
  taskModalMode.value = 'edit';
  selectedTask.value = task;
  isTaskModalOpen.value = true;
}

function openPromoteModal(task: any) {
  taskToPromote.value = task;
  isPromoteModalOpen.value = true;
}

function openTaskDetail(task: any) {
  taskInDetail.value = task;
  isDetailDrawerOpen.value = true;
}

function onTaskUpdatedInDrawer(updated: any) {
  showToast('Tarefa atualizada com sucesso!', 'success');
  taskInDetail.value = updated;
  fetchTasks();
}

function onTaskPromoted(ticket: any) {
  showToast(`Tarefa promovida para Chamado com sucesso! Protocolo: ${ticket.protocol}`, 'success');
  fetchTasks();
}

const moveTaskStatus = async (task: any, newStatus: string) => {
  try {
    const res = await axios.patch(`/api/v1/operations/tasks/${task.id}/status`, {
      status: newStatus,
    });
    showToast(`Status atualizado para ${formatTaskStatus(newStatus)}!`, 'success');
    fetchTasks();
  } catch (err: any) {
    showToast(err.response?.data?.message || 'Erro ao alterar status.', 'error');
  }
};

const deleteTask = async (task: any) => {
  if (!confirm(`Deseja realmente excluir a tarefa ${task.task_number}?`)) return;
  try {
    await axios.delete(`/api/v1/operations/tasks/${task.id}`);
    showToast('Tarefa removida com sucesso!', 'success');
    fetchTasks();
  } catch (err: any) {
    showToast(err.response?.data?.message || 'Erro ao excluir tarefa.', 'error');
  }
};

// Formatadores
function formatPriority(p: string): string {
  switch (p) {
    case 'CRITICAL': return 'Crítica';
    case 'HIGH': return 'Alta';
    case 'MEDIUM': return 'Média';
    case 'LOW': return 'Baixa';
    default: return p || 'Média';
  }
}

function getPriorityBadgeClass(p: string): string {
  switch (p) {
    case 'CRITICAL': return 'bg-red-100 dark:bg-red-950/80 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800';
    case 'HIGH': return 'bg-orange-100 dark:bg-orange-950/80 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800';
    case 'MEDIUM': return 'bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800';
    case 'LOW': return 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700';
    default: return 'bg-slate-100 text-slate-700';
  }
}

function formatTaskStatus(s: string): string {
  switch (s) {
    case 'INBOX': return 'Inbox';
    case 'TRIAGED': return 'Em Triagem';
    case 'PROMOTED_TICKET': return 'Em Chamado';
    case 'RESOLVED_INTERNAL': return 'Resolvida';
    case 'CANCELED': return 'Cancelada';
    default: return s;
  }
}

function getTaskStatusBadgeClass(s: string): string {
  switch (s) {
    case 'INBOX': return 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-900';
    case 'TRIAGED': return 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-900';
    case 'PROMOTED_TICKET': return 'bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-900';
    case 'RESOLVED_INTERNAL': return 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-900';
    case 'CANCELED': return 'bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-900';
    default: return 'bg-slate-100 text-slate-600';
  }
}

function formatDate(dateStr: string | null): string {
  if (!dateStr) return '--';
  return new Date(dateStr).toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
  });
}

onMounted(() => {
  fetchCatalogs();
  fetchTasks();
});
</script>
