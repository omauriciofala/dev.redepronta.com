<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-start select-none">
    <!-- Coluna 1: INBOX -->
    <div class="rounded-xl border border-blue-200/70 dark:border-blue-900/50 bg-slate-50/60 dark:bg-slate-900/40 p-3 flex flex-col min-h-[480px]">
      <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-blue-100 dark:border-blue-900/40">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
          <h4 class="font-bold text-xs text-slate-900 dark:text-slate-100 uppercase tracking-wider">
            1. Inbox / Novas
          </h4>
        </div>
        <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300">
          {{ getColumnTasks('INBOX').length }}
        </span>
      </div>

      <!-- Cards da Coluna -->
      <div class="space-y-2.5 flex-1">
        <div
          v-if="!getColumnTasks('INBOX').length"
          class="h-32 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-lg flex items-center justify-center text-slate-400 text-xs italic"
        >
          Nenhuma tarefa no Inbox
        </div>

        <div
          v-for="task in getColumnTasks('INBOX')"
          :key="task.id"
          @click="$emit('select-task', task)"
          class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:shadow-xs hover:border-blue-400 dark:hover:border-blue-600 transition cursor-pointer group space-y-2"
        >
          <div class="flex items-center justify-between">
            <span class="font-mono text-[11px] font-bold text-[#FC6714]">{{ task.task_number }}</span>
            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold" :class="getPriorityBadgeClass(task.priority)">
              {{ formatPriority(task.priority) }}
            </span>
          </div>

          <p class="text-xs font-bold text-slate-900 dark:text-slate-100 leading-snug line-clamp-2">
            {{ task.title }}
          </p>

          <div v-if="task.customer_person" class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
            Cliente: {{ task.customer_person.name }}
          </div>

          <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-400">
            <span class="inline-flex items-center gap-1 font-semibold text-slate-600 dark:text-slate-300">
              <span class="text-[9px] px-1 rounded bg-slate-100 dark:bg-slate-800 uppercase">{{ task.source }}</span>
            </span>

            <div class="flex items-center gap-2">
              <span v-if="task.comments_count" class="flex items-center gap-0.5 text-slate-400">
                <MessageSquare class="w-3 h-3" />
                <span class="text-[10px]">{{ task.comments_count }}</span>
              </span>
              <button
                type="button"
                @click.stop="$emit('move-status', task, 'TRIAGED')"
                class="px-2 py-0.5 rounded bg-amber-50 hover:bg-amber-100 text-amber-700 text-[10px] font-bold transition flex items-center gap-0.5"
                title="Mover para Em Triagem"
              >
                <span>Triar</span>
                <ChevronRight class="w-3 h-3" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Coluna 2: EM TRIAGEM -->
    <div class="rounded-xl border border-amber-200/70 dark:border-amber-900/50 bg-slate-50/60 dark:bg-slate-900/40 p-3 flex flex-col min-h-[480px]">
      <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-amber-100 dark:border-amber-900/40">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
          <h4 class="font-bold text-xs text-slate-900 dark:text-slate-100 uppercase tracking-wider">
            2. Em Triagem
          </h4>
        </div>
        <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300">
          {{ getColumnTasks('TRIAGED').length }}
        </span>
      </div>

      <div class="space-y-2.5 flex-1">
        <div
          v-if="!getColumnTasks('TRIAGED').length"
          class="h-32 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-lg flex items-center justify-center text-slate-400 text-xs italic"
        >
          Nenhuma tarefa em triagem
        </div>

        <div
          v-for="task in getColumnTasks('TRIAGED')"
          :key="task.id"
          @click="$emit('select-task', task)"
          class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs hover:shadow-xs hover:border-amber-400 dark:hover:border-amber-600 transition cursor-pointer group space-y-2"
        >
          <div class="flex items-center justify-between">
            <span class="font-mono text-[11px] font-bold text-[#FC6714]">{{ task.task_number }}</span>
            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold" :class="getPriorityBadgeClass(task.priority)">
              {{ formatPriority(task.priority) }}
            </span>
          </div>

          <p class="text-xs font-bold text-slate-900 dark:text-slate-100 leading-snug line-clamp-2">
            {{ task.title }}
          </p>

          <div v-if="task.assigned_user" class="text-[11px] text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
            <div class="w-4 h-4 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-[9px] font-bold">
              {{ task.assigned_user.name.charAt(0) }}
            </div>
            <span>{{ task.assigned_user.name }}</span>
          </div>

          <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-400">
            <button
              type="button"
              @click.stop="$emit('promote-ticket', task)"
              class="px-2 py-0.5 rounded bg-[#FC6714]/15 hover:bg-[#FC6714] text-[#FC6714] hover:text-white text-[10px] font-bold transition flex items-center gap-0.5"
              title="Promover para Chamado com SLA"
            >
              <Sparkles class="w-3 h-3" />
              <span>Promover</span>
            </button>

            <button
              type="button"
              @click.stop="$emit('move-status', task, 'RESOLVED_INTERNAL')"
              class="px-2 py-0.5 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[10px] font-bold transition flex items-center gap-0.5"
              title="Resolver Internamente"
            >
              <Check class="w-3 h-3" />
              <span>Resolver</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Coluna 3: RESOLVIDAS INTERNAMENTE -->
    <div class="rounded-xl border border-emerald-200/70 dark:border-emerald-900/50 bg-slate-50/60 dark:bg-slate-900/40 p-3 flex flex-col min-h-[480px]">
      <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-emerald-100 dark:border-emerald-900/40">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
          <h4 class="font-bold text-xs text-slate-900 dark:text-slate-100 uppercase tracking-wider">
            3. Resolvidas Interno
          </h4>
        </div>
        <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">
          {{ getColumnTasks('RESOLVED_INTERNAL').length }}
        </span>
      </div>

      <div class="space-y-2.5 flex-1">
        <div
          v-if="!getColumnTasks('RESOLVED_INTERNAL').length"
          class="h-32 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-lg flex items-center justify-center text-slate-400 text-xs italic"
        >
          Nenhuma tarefa resolvida internamente
        </div>

        <div
          v-for="task in getColumnTasks('RESOLVED_INTERNAL')"
          :key="task.id"
          @click="$emit('select-task', task)"
          class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 opacity-90 hover:opacity-100 transition cursor-pointer space-y-2"
        >
          <div class="flex items-center justify-between">
            <span class="font-mono text-[11px] font-bold text-slate-500">{{ task.task_number }}</span>
            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
              <CheckCircle2 class="w-3 h-3" />
              <span>Resolvida</span>
            </span>
          </div>

          <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 leading-snug line-clamp-2">
            {{ task.title }}
          </p>

          <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-400">
            <span>Resolvida internamente</span>
            <button
              type="button"
              @click.stop="$emit('move-status', task, 'TRIAGED')"
              class="text-[10px] text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 underline cursor-pointer"
            >
              Reabrir
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Coluna 4: PROMOVIDAS P/ CHAMADO -->
    <div class="rounded-xl border border-purple-200/70 dark:border-purple-900/50 bg-slate-50/60 dark:bg-slate-900/40 p-3 flex flex-col min-h-[480px]">
      <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-purple-100 dark:border-purple-900/40">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
          <h4 class="font-bold text-xs text-slate-900 dark:text-slate-100 uppercase tracking-wider">
            4. Em Chamado (SLA)
          </h4>
        </div>
        <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300">
          {{ getColumnTasks('PROMOTED_TICKET').length }}
        </span>
      </div>

      <div class="space-y-2.5 flex-1">
        <div
          v-if="!getColumnTasks('PROMOTED_TICKET').length"
          class="h-32 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-lg flex items-center justify-center text-slate-400 text-xs italic"
        >
          Nenhuma tarefa promovida
        </div>

        <div
          v-for="task in getColumnTasks('PROMOTED_TICKET')"
          :key="task.id"
          @click="$emit('select-task', task)"
          class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs transition cursor-pointer space-y-2 border-l-4 border-l-purple-500"
        >
          <div class="flex items-center justify-between">
            <span class="font-mono text-[11px] font-bold text-slate-500">{{ task.task_number }}</span>
            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300">
              Em Chamado
            </span>
          </div>

          <p class="text-xs font-bold text-slate-900 dark:text-slate-100 leading-snug line-clamp-2">
            {{ task.title }}
          </p>

          <div v-if="task.ticket" class="text-[11px] font-mono font-bold text-blue-600 dark:text-blue-400 flex items-center gap-1">
            <span>Protocolo:</span>
            <span>{{ task.ticket.protocol }}</span>
          </div>

          <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-400">
            <span>SLA Ativo em Campo</span>
            <Sparkles class="w-3.5 h-3.5 text-purple-500" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {
  MessageSquare,
  Sparkles,
  ChevronRight,
  Check,
  CheckCircle2,
} from 'lucide-vue-next';

const props = defineProps<{
  tasks: any[];
}>();

defineEmits<{
  (e: 'select-task', task: any): void;
  (e: 'promote-ticket', task: any): void;
  (e: 'move-status', task: any, newStatus: string): void;
}>();

function getColumnTasks(status: string) {
  return props.tasks.filter((t) => t.status === status);
}

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
</script>
