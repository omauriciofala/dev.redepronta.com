<template>
  <Teleport to="body">
    <div v-if="modelValue" class="relative z-50">
      <!-- Backdrop -->
      <Transition
        enter-active-class="transition-opacity duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="modelValue"
          @click="close"
          class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs"
        />
      </Transition>

      <!-- Painel Drawer Lateral Direito -->
      <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <Transition
          enter-active-class="transform transition ease-in-out duration-300"
          enter-from-class="translate-x-full"
          enter-to-class="translate-x-0"
          leave-active-class="transform transition ease-in-out duration-200"
          leave-from-class="translate-x-0"
          leave-to-class="translate-x-full"
        >
          <div
            v-if="modelValue"
            class="w-screen max-w-lg bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 shadow-2xl flex flex-col justify-between"
          >
            <!-- Topo do Drawer -->
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-start justify-between gap-3 bg-slate-50/50 dark:bg-slate-800/40">
              <div>
                <div class="flex items-center gap-2">
                  <span class="font-mono text-sm font-bold text-[#FC6714]">
                    {{ task?.task_number }}
                  </span>
                  <span
                    class="px-2 py-0.5 rounded-md text-[10px] font-bold"
                    :class="getPriorityBadgeClass(task?.priority)"
                  >
                    {{ formatPriority(task?.priority) }}
                  </span>
                  <span
                    class="px-2 py-0.5 rounded-md text-[10px] font-bold"
                    :class="getTaskStatusBadgeClass(task?.status)"
                  >
                    {{ formatTaskStatus(task?.status) }}
                  </span>
                </div>
                <h3 class="text-base font-bold font-heading text-slate-900 dark:text-slate-100 mt-1.5 leading-snug">
                  {{ task?.title }}
                </h3>
              </div>

              <button
                type="button"
                @click="close"
                class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer shrink-0"
              >
                <X class="w-5 h-5" />
              </button>
            </div>

            <!-- Conteúdo com Scroll -->
            <div class="flex-1 overflow-y-auto p-5 space-y-5 text-xs text-slate-700 dark:text-slate-300">
              <!-- Grid de Propriedades Rápidas -->
              <div class="grid grid-cols-2 gap-3 p-3.5 rounded-xl bg-slate-50/70 dark:bg-slate-800/40 border border-slate-200/80 dark:border-slate-800">
                <!-- Canal de Entrada -->
                <div>
                  <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Canal de Entrada</span>
                  <span class="font-semibold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                    <Radio class="w-3.5 h-3.5 text-[#FC6714]" />
                    <span>{{ task?.source }}</span>
                  </span>
                </div>

                <!-- Data de Criação -->
                <div>
                  <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Criada em</span>
                  <span class="font-medium text-slate-600 dark:text-slate-300">
                    {{ formatDate(task?.created_at) }}
                  </span>
                </div>

                <!-- Cliente Solicitante -->
                <div class="col-span-2 pt-2 border-t border-slate-200/50 dark:border-slate-700/50">
                  <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Cliente Relacionado</span>
                  <div v-if="task?.customer_person" class="flex items-center gap-2">
                    <UserIcon class="w-3.5 h-3.5 text-slate-400" />
                    <span class="font-bold text-slate-900 dark:text-slate-100">{{ task.customer_person.name }}</span>
                    <span class="text-[11px] text-slate-400">({{ task.customer_person.document_number || 'Sem documento' }})</span>
                  </div>
                  <span v-else class="text-slate-400 italic">Nenhum cliente vinculado</span>
                </div>
              </div>

              <!-- Atribuição de Operador Responsável -->
              <div class="space-y-1.5 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                <div class="flex items-center justify-between">
                  <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                    <UserCheck class="w-3.5 h-3.5 text-[#FC6714]" />
                    <span>Operador Responsável (Atribuição)</span>
                  </label>
                  <span v-if="isAssigning" class="text-[10px] text-slate-400 animate-pulse">Atualizando...</span>
                </div>
                <select
                  :value="task?.assigned_user_id || ''"
                  @change="handleAssignChange(($event.target as HTMLSelectElement).value)"
                  class="w-full px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
                >
                  <option value="">Não Atribuído (Disponível na Fila)</option>
                  <option v-for="op in operators" :key="op.id" :value="op.id">
                    {{ op.name }} ({{ op.email }})
                  </option>
                </select>
              </div>

              <!-- Status da Tarefa -->
              <div class="space-y-1.5 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                <div class="flex items-center justify-between">
                  <label class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    Estágio / Status Atual
                  </label>
                  <span v-if="isUpdatingStatus" class="text-[10px] text-slate-400 animate-pulse">Atualizando...</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5">
                  <button
                    v-for="st in availableStatuses"
                    :key="st.key"
                    type="button"
                    @click="handleStatusChange(st.key)"
                    :disabled="task?.status === 'PROMOTED_TICKET' && st.key !== 'PROMOTED_TICKET'"
                    :class="task?.status === st.key
                      ? 'bg-[#FC6714] text-white font-bold shadow-2xs'
                      : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                    class="py-1.5 px-2 text-[11px] rounded-md transition cursor-pointer text-center disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    {{ st.label }}
                  </button>
                </div>
              </div>

              <!-- Descrição Detalhada -->
              <div class="space-y-1.5">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                  Descrição / Relato da Demanda
                </span>
                <div class="p-3.5 rounded-xl bg-slate-50/60 dark:bg-slate-800/30 border border-slate-200/80 dark:border-slate-800 leading-relaxed text-slate-800 dark:text-slate-200 whitespace-pre-wrap">
                  {{ task?.description || 'Nenhum detalhe adicional informado na abertura.' }}
                </div>
              </div>

              <!-- Seção de Comentários Internos e Histórico -->
              <div class="space-y-3 pt-2 border-t border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between">
                  <h4 class="text-xs font-bold font-heading text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                    <MessageSquare class="w-3.5 h-3.5 text-[#FC6714]" />
                    <span>Anotações & Comentários da Operação ({{ commentsList.length }})</span>
                  </h4>
                  <button
                    type="button"
                    @click="fetchComments"
                    class="text-[11px] text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer"
                  >
                    <RefreshCw class="w-3 h-3" :class="{ 'animate-spin': isLoadingComments }" />
                  </button>
                </div>

                <!-- Feed de Comentários -->
                <div class="space-y-2.5 max-h-60 overflow-y-auto pr-1">
                  <div v-if="isLoadingComments && !commentsList.length" class="text-center py-4 text-slate-400 text-xs">
                    <Loader2 class="w-4 h-4 animate-spin mx-auto text-[#FC6714] mb-1" />
                    <span>Carregando anotações...</span>
                  </div>

                  <div v-else-if="!commentsList.length" class="text-center py-4 text-slate-400 text-xs italic">
                    Nenhum comentário registrado nesta tarefa ainda.
                  </div>

                  <div
                    v-for="comm in commentsList"
                    :key="comm.id"
                    class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 space-y-1"
                  >
                    <div class="flex items-center justify-between text-[11px]">
                      <span class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                        <div class="w-4 h-4 rounded-full bg-[#FC6714]/20 text-[#FC6714] flex items-center justify-center text-[9px] font-bold">
                          {{ (comm.user?.name || 'U').charAt(0).toUpperCase() }}
                        </div>
                        {{ comm.user?.name || 'Operador' }}
                      </span>
                      <span class="text-slate-400 text-[10px]">{{ formatDate(comm.created_at) }}</span>
                    </div>
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap text-[11px]">
                      {{ comm.comment }}
                    </p>
                  </div>
                </div>

                <!-- Formulário para Novo Comentário -->
                <form @submit.prevent="submitComment" class="space-y-2 pt-1">
                  <textarea
                    v-model="newComment"
                    rows="2"
                    placeholder="Adicionar anotação técnica ou andamento interno..."
                    required
                    class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
                  ></textarea>

                  <div class="flex items-center justify-end">
                    <button
                      type="submit"
                      :disabled="isSubmittingComment || !newComment.trim()"
                      class="px-3.5 py-1.5 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-xs font-semibold shadow-2xs transition flex items-center gap-1.5 cursor-pointer disabled:opacity-50"
                    >
                      <Loader2 v-if="isSubmittingComment" class="w-3 h-3 animate-spin" />
                      <Send v-else class="w-3 h-3" />
                      <span>Comentar</span>
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Rodapé do Drawer -->
            <div class="p-4 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between gap-3">
              <button
                v-if="task?.status !== 'PROMOTED_TICKET'"
                type="button"
                @click="$emit('promote', task)"
                class="px-4 py-2 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer"
              >
                <Sparkles class="w-3.5 h-3.5" />
                <span>Promover para Chamado</span>
              </button>
              <div v-else class="text-xs text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1.5">
                <CheckCircle2 class="w-4 h-4" />
                <span>Promovida para Chamado (Protocolo Ativo)</span>
              </div>

              <button
                type="button"
                @click="close"
                class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 text-xs font-semibold cursor-pointer"
              >
                Fechar
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import axios from 'axios';
import {
  X,
  Radio,
  User as UserIcon,
  UserCheck,
  MessageSquare,
  Send,
  Loader2,
  RefreshCw,
  Sparkles,
  CheckCircle2,
} from 'lucide-vue-next';

const props = defineProps<{
  modelValue: boolean;
  task: any;
  operators: any[];
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', val: boolean): void;
  (e: 'task-updated', task: any): void;
  (e: 'promote', task: any): void;
}>();

const close = () => {
  emit('update:modelValue', false);
};

const commentsList = ref<any[]>([]);
const isLoadingComments = ref(false);
const newComment = ref('');
const isSubmittingComment = ref(false);

const isAssigning = ref(false);
const isUpdatingStatus = ref(false);

const availableStatuses = [
  { key: 'INBOX', label: 'Inbox' },
  { key: 'TRIAGED', label: 'Triada' },
  { key: 'RESOLVED_INTERNAL', label: 'Resolvida' },
  { key: 'CANCELED', label: 'Cancelada' },
];

const fetchComments = async () => {
  if (!props.task?.id) return;
  isLoadingComments.value = true;
  try {
    const res = await axios.get(`/api/v1/operations/tasks/${props.task.id}/comments`);
    commentsList.value = res.data?.data || [];
  } catch (e) {
    // Silencioso
  } finally {
    isLoadingComments.value = false;
  }
};

const submitComment = async () => {
  if (!newComment.value.trim() || !props.task?.id) return;
  isSubmittingComment.value = true;
  try {
    const res = await axios.post(`/api/v1/operations/tasks/${props.task.id}/comments`, {
      comment: newComment.value,
    });
    commentsList.value.push(res.data?.data);
    newComment.value = '';
  } catch (e) {
    // Silencioso
  } finally {
    isSubmittingComment.value = false;
  }
};

const handleAssignChange = async (val: string) => {
  if (!props.task?.id) return;
  isAssigning.value = true;
  try {
    const userId = val ? parseInt(val) : null;
    const res = await axios.patch(`/api/v1/operations/tasks/${props.task.id}/assign`, {
      assigned_user_id: userId,
    });
    emit('task-updated', res.data?.data);
  } catch (e) {
    // Silencioso
  } finally {
    isAssigning.value = false;
  }
};

const handleStatusChange = async (newStatus: string) => {
  if (!props.task?.id || props.task.status === newStatus) return;
  isUpdatingStatus.value = true;
  try {
    const res = await axios.patch(`/api/v1/operations/tasks/${props.task.id}/status`, {
      status: newStatus,
    });
    emit('task-updated', res.data?.data);
  } catch (e) {
    // Silencioso
  } finally {
    isUpdatingStatus.value = false;
  }
};

watch(
  () => props.task,
  (t) => {
    if (t?.id && props.modelValue) {
      fetchComments();
    }
  },
  { immediate: true }
);

watch(
  () => props.modelValue,
  (val) => {
    if (val && props.task?.id) {
      fetchComments();
    }
  }
);

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
    case 'TRIAGED': return 'Triada';
    case 'PROMOTED_TICKET': return 'Promovida p/ Chamado';
    case 'RESOLVED_INTERNAL': return 'Resolvida';
    case 'CANCELED': return 'Cancelada';
    default: return s;
  }
}

function getTaskStatusBadgeClass(s: string): string {
  switch (s) {
    case 'INBOX': return 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-900';
    case 'TRIAGED': return 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-900';
    case 'PROMOTED_TICKET': return 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-900';
    case 'RESOLVED_INTERNAL': return 'bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-800 dark:text-slate-300';
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
</script>
