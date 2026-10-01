<template>
  <!-- Container Raiz Canônico Obrigatório (py-8 w-full space-y-6) -->
  <div class="py-8 w-full space-y-6 font-sans">
    <!-- Componente Canônico: 10. Topo de Página Padrão (Page Header & Breadcrumb) -->
    <BasePageHeader
      :breadcrumb-items="breadcrumbItems"
      title="Funil Operacional & FSM"
      description="Esteira operacional em 3 estágios: Ingestão de tarefas no Inbox, triagem com SLA formal e acionamentos de campo para técnicos."
      :icon="Activity"
      icon-color-class="bg-orange-50 dark:bg-[#FC6714]/15 border border-orange-200 dark:border-[#FC6714]/30 text-[#FC6714]"
      :badge-text="headerBadgeText"
      :badge-variant="ticketsCounts.sla_breached > 0 ? 'warning' : 'neutral'"
    >
      <template #breadcrumb-right>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-orange-50 dark:bg-orange-950/60 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-900">
          Field Service Management
        </span>
      </template>

      <template #actions>
        <!-- Botão Primário Laranja (#FC6714) Conforme a Aba Ativa -->
        <button
          v-if="currentTab === 'tasks'"
          type="button"
          @click="openNewTaskModal"
          class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-sm font-semibold shadow-sm transition active:scale-98 cursor-pointer focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
        >
          <Plus class="w-4 h-4" />
          <span>Nova Tarefa no Inbox</span>
        </button>

        <button
          v-else-if="currentTab === 'tickets'"
          type="button"
          @click="openNewTaskModal"
          class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-sm font-semibold shadow-sm transition active:scale-98 cursor-pointer focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
        >
          <Plus class="w-4 h-4" />
          <span>Abrir Novo Chamado</span>
        </button>

        <!-- Botão Recarregar Dados -->
        <button
          type="button"
          @click="refreshActiveTabData"
          :disabled="isLoading"
          class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 transition shadow-2xs cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
          title="Recarregar dados operacionais"
        >
          <RefreshCw class="w-4 h-4" :class="{ 'animate-spin text-[#FC6714]': isLoading }" />
        </button>
      </template>

      <!-- Linha 3: Barra de Abas do Módulo (Variação 1) -->
      <template #tabs>
        <div class="flex border-b border-slate-200 dark:border-slate-800 gap-6 overflow-x-auto select-none pt-2">
          <!-- Aba 1: Tarefas (Inbox Universal) -->
          <button
            type="button"
            @click="setTab('tasks')"
            :class="currentTab === 'tasks'
              ? 'border-[#FC6714] text-[#FC6714] font-bold border-b-2'
              : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium border-b-2'"
            class="pb-3 border-b-2 text-sm flex items-center gap-2 transition cursor-pointer shrink-0"
          >
            <Inbox class="w-4 h-4" />
            <span>1. Tarefas (Inbox Universal)</span>
            <span
              class="px-2 py-0.5 rounded-md text-[11px] font-bold transition"
              :class="currentTab === 'tasks' ? 'bg-[#FC6714]/15 text-[#FC6714]' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'"
            >
              {{ tasksCounts.inbox || 0 }}
            </span>
          </button>

          <!-- Aba 2: Chamados (SLA & Diagnóstico) -->
          <button
            type="button"
            @click="setTab('tickets')"
            :class="currentTab === 'tickets'
              ? 'border-[#FC6714] text-[#FC6714] font-bold border-b-2'
              : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium border-b-2'"
            class="pb-3 border-b-2 text-sm flex items-center gap-2 transition cursor-pointer shrink-0"
          >
            <TicketIcon class="w-4 h-4" />
            <span>2. Chamados com SLA</span>
            <span
              class="px-2 py-0.5 rounded-md text-[11px] font-bold transition"
              :class="ticketsCounts.sla_breached > 0
                ? 'bg-red-100 dark:bg-red-950/80 text-red-600 dark:text-red-400 font-extrabold animate-pulse'
                : (currentTab === 'tickets' ? 'bg-[#FC6714]/15 text-[#FC6714]' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400')"
            >
              {{ ticketsCounts.open || 0 }}
            </span>
          </button>

          <!-- Aba 3: Acionamentos de Campo (Despacho) -->
          <button
            type="button"
            @click="setTab('dispatches')"
            :class="currentTab === 'dispatches'
              ? 'border-[#FC6714] text-[#FC6714] font-bold border-b-2'
              : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium border-b-2'"
            class="pb-3 border-b-2 text-sm flex items-center gap-2 transition cursor-pointer shrink-0"
          >
            <Truck class="w-4 h-4" />
            <span>3. Acionamentos de Campo (Despacho)</span>
            <span
              class="px-2 py-0.5 rounded-md text-[11px] font-bold transition"
              :class="currentTab === 'dispatches' ? 'bg-[#FC6714]/15 text-[#FC6714]' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'"
            >
              {{ dispatchesCounts.total || 0 }}
            </span>
          </button>
        </div>
      </template>
    </BasePageHeader>

    <!-- ============================================================== -->
    <!-- ABA 1: TAREFAS (INBOX UNIVERSAL)                               -->
    <!-- ============================================================== -->
    <div v-show="currentTab === 'tasks'" class="space-y-4">
      <!-- Filtros Rápidos da Caixa de Entrada -->
      <div class="p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <!-- Campo de Busca -->
        <div class="flex-1 relative">
          <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
          <input
            v-model="tasksSearch"
            @input="debounceFetchTasks"
            type="text"
            placeholder="Buscar por assunto, identificador ou solicitante..."
            class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
          />
        </div>

        <!-- Seletor de Status -->
        <div class="flex items-center gap-2 shrink-0">
          <span class="text-xs text-slate-400 font-medium">Status:</span>
          <select
            v-model="tasksStatusFilter"
            @change="fetchTasks"
            class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
          >
            <option value="ALL">Todos os Status</option>
            <option value="INBOX">Inbox (Novas)</option>
            <option value="TRIAGED">Triadas</option>
            <option value="PROMOTED_TICKET">Promovidas para Chamado</option>
            <option value="RESOLVED_INTERNAL">Resolvidas Internamente</option>
          </select>

          <!-- Seletor de Origem -->
          <select
            v-model="tasksSourceFilter"
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
        </div>
      </div>

      <!-- Tabela de Tarefas -->
      <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs overflow-hidden">
        <table class="w-full text-left text-xs divide-y divide-slate-100 dark:divide-slate-800">
          <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[10px]">
            <tr>
              <th class="py-3 px-4">Identificador</th>
              <th class="py-3 px-4">Assunto & Contexto</th>
              <th class="py-3 px-4">Origem</th>
              <th class="py-3 px-4">Prioridade</th>
              <th class="py-3 px-4">Status</th>
              <th class="py-3 px-4">Data/Hora</th>
              <th class="py-3 px-4 text-right">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium text-slate-700 dark:text-slate-300">
            <tr v-if="isLoadingTasks" class="text-center py-8">
              <td colspan="7" class="py-8 text-center text-slate-400">
                <Loader2 class="w-5 h-5 animate-spin mx-auto text-[#FC6714]" />
                <span class="mt-2 block text-xs">Carregando tarefas do Inbox...</span>
              </td>
            </tr>

            <tr v-else-if="!tasksList.length" class="text-center py-8">
              <td colspan="7" class="py-8 text-center text-slate-400">
                <Inbox class="w-8 h-8 mx-auto text-slate-300 dark:text-slate-600 mb-1" />
                <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">Nenhuma tarefa encontrada na caixa de entrada.</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Clique em "+ Nova Tarefa no Inbox" para registrar uma demanda.</p>
              </td>
            </tr>

            <tr
              v-for="task in tasksList"
              :key="task.id"
              class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition"
            >
              <td class="py-3 px-4 font-mono font-bold text-[#FC6714]">
                {{ task.task_number }}
              </td>
              <td class="py-3 px-4 max-w-sm">
                <div class="font-bold text-slate-900 dark:text-slate-100 truncate">{{ task.title }}</div>
                <div class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">
                  {{ task.customer_person ? `Cliente: ${task.customer_person.name} • ` : '' }}{{ task.description || 'Sem descrição' }}
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
              <td class="py-3 px-4 text-right">
                <div class="inline-flex items-center gap-1.5">
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

                  <span v-else class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
                    <CheckCircle2 class="w-3.5 h-3.5" />
                    <span>Promovida</span>
                  </span>

                  <!-- Botão Editar -->
                  <button
                    v-if="task.status !== 'PROMOTED_TICKET'"
                    type="button"
                    @click="openEditTaskModal(task)"
                    class="p-1 rounded-md text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                    title="Editar Demanda"
                  >
                    <Pencil class="w-3.5 h-3.5" />
                  </button>

                  <!-- Botão Excluir -->
                  <button
                    v-if="task.status !== 'PROMOTED_TICKET'"
                    type="button"
                    @click="deleteTask(task)"
                    class="p-1 rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                    title="Excluir Demanda"
                  >
                    <Trash2 class="w-3.5 h-3.5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Paginação -->
        <TablePagination
          v-if="tasksPagination.total > tasksPagination.per_page"
          :current-page="tasksPagination.current_page"
          :total-items="tasksPagination.total"
          :per-page="tasksPagination.per_page"
          @page-change="onTasksPageChange"
        />
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- ABA 2: CHAMADOS (TICKETS COM SLA)                              -->
    <!-- ============================================================== -->
    <div v-show="currentTab === 'tickets'" class="space-y-4">
      <!-- Cards de Métricas e SLA em Tempo Real -->
      <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs">
          <div class="text-[11px] text-slate-400 font-semibold uppercase">Total de Chamados</div>
          <div class="text-xl font-bold font-heading text-slate-900 dark:text-slate-100 mt-1">{{ ticketsCounts.total || 0 }}</div>
        </div>
        <div class="p-3.5 rounded-xl bg-blue-50/50 dark:bg-blue-950/20 border border-blue-100/60 dark:border-blue-900/40">
          <div class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold uppercase">Abertos / Em Triagem</div>
          <div class="text-xl font-bold font-heading text-blue-700 dark:text-blue-300 mt-1">{{ ticketsCounts.open || 0 }}</div>
        </div>
        <div class="p-3.5 rounded-xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-100/60 dark:border-amber-900/40">
          <div class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold uppercase">Aguardando Despacho</div>
          <div class="text-xl font-bold font-heading text-amber-700 dark:text-amber-300 mt-1">{{ ticketsCounts.waiting_dispatch || 0 }}</div>
        </div>
        <div class="p-3.5 rounded-xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-100/60 dark:border-purple-900/40">
          <div class="text-[11px] text-purple-600 dark:text-purple-400 font-semibold uppercase">Em Campo (FSM)</div>
          <div class="text-xl font-bold font-heading text-purple-700 dark:text-purple-300 mt-1">{{ ticketsCounts.in_field || 0 }}</div>
        </div>
        <div
          class="p-3.5 rounded-xl border shadow-2xs"
          :class="ticketsCounts.sla_breached > 0
            ? 'bg-red-50 dark:bg-red-950/40 border-red-200 dark:border-red-800'
            : 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-100/60 dark:border-emerald-900/40'"
        >
          <div
            class="text-[11px] font-semibold uppercase"
            :class="ticketsCounts.sla_breached > 0 ? 'text-red-700 dark:text-red-300 font-bold' : 'text-emerald-600 dark:text-emerald-400'"
          >
            SLA Estourado
          </div>
          <div
            class="text-xl font-bold font-heading mt-1"
            :class="ticketsCounts.sla_breached > 0 ? 'text-red-700 dark:text-red-300' : 'text-emerald-700 dark:text-emerald-300'"
          >
            {{ ticketsCounts.sla_breached || 0 }}
          </div>
        </div>
      </div>

      <!-- Filtros da Listagem de Chamados -->
      <div class="p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <!-- Campo de Busca -->
        <div class="flex-1 relative">
          <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
          <input
            v-model="ticketsSearch"
            @input="debounceFetchTickets"
            type="text"
            placeholder="Buscar por protocolo (TK-...), título ou nome do cliente..."
            class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
          />
        </div>

        <!-- Seletor de Status -->
        <div class="flex items-center gap-2 shrink-0">
          <span class="text-xs text-slate-400 font-medium">Status:</span>
          <select
            v-model="ticketsStatusFilter"
            @change="fetchTickets"
            class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
          >
            <option value="ALL">Todos os Status</option>
            <option value="OPEN">Aberto</option>
            <option value="IN_TRIAGE">Em Triagem</option>
            <option value="WAITING_DISPATCH">Aguardando Despacho</option>
            <option value="IN_FIELD">Em Campo (Técnico Alocado)</option>
            <option value="CLOSED">Encerrado</option>
          </select>

          <!-- Seletor de Departamento -->
          <select
            v-model="ticketsDepartmentFilter"
            @change="fetchTickets"
            class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
          >
            <option value="ALL">Todos os Departamentos</option>
            <option v-for="d in catalogs.departments" :key="d.id" :value="d.id">
              {{ d.name }}
            </option>
          </select>
        </div>
      </div>

      <!-- Tabela de Chamados -->
      <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs overflow-hidden">
        <table class="w-full text-left text-xs divide-y divide-slate-100 dark:divide-slate-800">
          <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[10px]">
            <tr>
              <th class="py-3 px-4">Protocolo</th>
              <th class="py-3 px-4">Cliente & Local</th>
              <th class="py-3 px-4">Departamento / Motivo</th>
              <th class="py-3 px-4">Controle de SLA</th>
              <th class="py-3 px-4">Prioridade</th>
              <th class="py-3 px-4">Status</th>
              <th class="py-3 px-4 text-right">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium text-slate-700 dark:text-slate-300">
            <tr v-if="isLoadingTickets" class="text-center py-8">
              <td colspan="7" class="py-8 text-center text-slate-400">
                <Loader2 class="w-5 h-5 animate-spin mx-auto text-[#FC6714]" />
                <span class="mt-2 block text-xs">Carregando chamados com SLA...</span>
              </td>
            </tr>

            <tr v-else-if="!ticketsList.length" class="text-center py-8">
              <td colspan="7" class="py-8 text-center text-slate-400">
                <TicketIcon class="w-8 h-8 mx-auto text-slate-300 dark:text-slate-600 mb-1" />
                <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">Nenhum chamado encontrado com os filtros selecionados.</p>
              </td>
            </tr>

            <tr
              v-for="ticket in ticketsList"
              :key="ticket.id"
              class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition"
            >
              <td class="py-3 px-4 font-mono font-bold text-blue-600 dark:text-blue-400">
                {{ ticket.protocol }}
              </td>
              <td class="py-3 px-4 max-w-xs">
                <div class="font-bold text-slate-900 dark:text-slate-100 truncate">
                  {{ ticket.customer_person?.name || 'Cliente não identificado' }}
                </div>
                <div class="text-[11px] text-slate-400 truncate mt-0.5">
                  {{ ticket.city?.name }}/{{ ticket.city?.state?.code }} • {{ ticket.title }}
                </div>
              </td>
              <td class="py-3 px-4">
                <div class="font-semibold text-slate-800 dark:text-slate-200">
                  {{ ticket.reason?.name || 'Motivo Geral' }}
                </div>
                <div class="text-[11px] text-slate-400">
                  {{ ticket.department?.name }}
                </div>
              </td>
              <td class="py-3 px-4">
                <div class="flex items-center gap-1.5">
                  <Clock class="w-3.5 h-3.5" :class="isSlaBreached(ticket) ? 'text-red-500' : 'text-slate-400'" />
                  <span
                    class="font-mono text-[11px] font-bold"
                    :class="isSlaBreached(ticket) ? 'text-red-600 dark:text-red-400' : 'text-slate-700 dark:text-slate-300'"
                  >
                    {{ formatSlaRemaining(ticket.sla_due_at, ticket.status) }}
                  </span>
                </div>
              </td>
              <td class="py-3 px-4">
                <span
                  class="px-2 py-0.5 rounded-md text-[10px] font-bold"
                  :class="getPriorityBadgeClass(ticket.priority)"
                >
                  {{ formatPriority(ticket.priority) }}
                </span>
              </td>
              <td class="py-3 px-4">
                <span
                  class="px-2 py-0.5 rounded-md text-[10px] font-bold"
                  :class="getTicketStatusBadgeClass(ticket.status)"
                >
                  {{ formatTicketStatus(ticket.status) }}
                </span>
              </td>
              <td class="py-3 px-4 text-right">
                <div class="inline-flex items-center gap-1.5">
                  <!-- Botão Despachar Técnico (se não estiver finalizado) -->
                  <button
                    v-if="ticket.status !== 'CLOSED' && ticket.status !== 'CANCELED'"
                    type="button"
                    @click="openDispatchModal(ticket)"
                    class="h-7 px-2.5 rounded-md bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-600 text-blue-700 dark:text-blue-300 hover:text-white font-semibold text-[11px] transition flex items-center gap-1 cursor-pointer"
                    title="Despachar Técnico de Campo"
                  >
                    <Truck class="w-3.5 h-3.5" />
                    <span>Despachar</span>
                  </button>

                  <!-- Botão Excluir Chamado -->
                  <button
                    type="button"
                    @click="deleteTicket(ticket)"
                    class="p-1 rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                    title="Excluir Chamado"
                  >
                    <Trash2 class="w-3.5 h-3.5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Paginação -->
        <TablePagination
          v-if="ticketsPagination.total > ticketsPagination.per_page"
          :current-page="ticketsPagination.current_page"
          :total-items="ticketsPagination.total"
          :per-page="ticketsPagination.per_page"
          @page-change="onTicketsPageChange"
        />
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- ABA 3: ACIONAMENTOS DE CAMPO (DESPACHO & FSM)                  -->
    <!-- ============================================================== -->
    <div v-show="currentTab === 'dispatches'" class="space-y-4">
      <!-- Filtros de Despacho -->
      <div class="p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <!-- Campo de Busca -->
        <div class="flex-1 relative">
          <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
          <input
            v-model="dispatchesSearch"
            @input="debounceFetchDispatches"
            type="text"
            placeholder="Buscar por número DSP, chamado vinculado ou técnico..."
            class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
          />
        </div>

        <!-- Seletor de Status de Despacho -->
        <div class="flex items-center gap-2 shrink-0">
          <span class="text-xs text-slate-400 font-medium">Status FSM:</span>
          <select
            v-model="dispatchesStatusFilter"
            @change="fetchDispatches"
            class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
          >
            <option value="ALL">Todos os Estágios</option>
            <option value="DISPATCHED">Despachado</option>
            <option value="ON_ROUTE">Em Deslocamento (A Caminho)</option>
            <option value="ARRIVED_SITE">No Local do Atendimento</option>
            <option value="IN_SERVICE">Executando Serviço</option>
            <option value="COMPLETED">Concluído</option>
          </select>
        </div>
      </div>

      <!-- Tabela de Acionamentos -->
      <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs overflow-hidden">
        <table class="w-full text-left text-xs divide-y divide-slate-100 dark:divide-slate-800">
          <thead class="bg-slate-50/80 dark:bg-slate-800/40 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[10px]">
            <tr>
              <th class="py-3 px-4">Acionamento</th>
              <th class="py-3 px-4">Chamado / Cliente</th>
              <th class="py-3 px-4">Técnico Responsável</th>
              <th class="py-3 px-4">Base de Apoio</th>
              <th class="py-3 px-4">Status de Campo</th>
              <th class="py-3 px-4 text-right">Avanço de Estágio FSM</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium text-slate-700 dark:text-slate-300">
            <tr v-if="isLoadingDispatches" class="text-center py-8">
              <td colspan="6" class="py-8 text-center text-slate-400">
                <Loader2 class="w-5 h-5 animate-spin mx-auto text-[#FC6714]" />
                <span class="mt-2 block text-xs">Carregando ordens de despacho...</span>
              </td>
            </tr>

            <tr v-else-if="!dispatchesList.length" class="text-center py-8">
              <td colspan="6" class="py-8 text-center text-slate-400">
                <Truck class="w-8 h-8 mx-auto text-slate-300 dark:text-slate-600 mb-1" />
                <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">Nenhum acionamento de campo registrado.</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Despache chamados abertos na aba "2. Chamados com SLA".</p>
              </td>
            </tr>

            <tr
              v-for="disp in dispatchesList"
              :key="disp.id"
              class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition"
            >
              <td class="py-3 px-4 font-mono font-bold text-purple-600 dark:text-purple-400">
                {{ disp.dispatch_number }}
              </td>
              <td class="py-3 px-4 max-w-xs">
                <div class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                  <span class="font-mono text-blue-600 dark:text-blue-400">{{ disp.ticket?.protocol }}</span>
                </div>
                <div class="text-[11px] text-slate-400 truncate mt-0.5">
                  {{ disp.ticket?.customer_person?.name }} • {{ disp.ticket?.city?.name }}
                </div>
              </td>
              <td class="py-3 px-4">
                <div class="font-semibold text-slate-800 dark:text-slate-200">
                  {{ disp.worker_person?.name }}
                </div>
                <div class="text-[11px] text-slate-400">
                  {{ disp.worker_person?.phone || 'Sem telefone' }}
                </div>
              </td>
              <td class="py-3 px-4">
                <div class="font-medium text-slate-700 dark:text-slate-300">
                  {{ disp.depot?.name }}
                </div>
                <div class="text-[11px] text-slate-400">
                  {{ disp.depot?.cluster?.name }}
                </div>
              </td>
              <td class="py-3 px-4">
                <span
                  class="px-2 py-0.5 rounded-md text-[10px] font-bold"
                  :class="getDispatchStatusBadgeClass(disp.status)"
                >
                  {{ formatDispatchStatus(disp.status) }}
                </span>
              </td>
              <td class="py-3 px-4 text-right">
                <div class="inline-flex items-center gap-2">
                  <!-- Botão de Próximo Passo FSM com 1 Clique -->
                  <button
                    v-if="disp.status === 'DISPATCHED'"
                    type="button"
                    @click="advanceDispatch(disp, 'ON_ROUTE')"
                    class="h-7 px-2.5 rounded-md bg-purple-50 dark:bg-purple-950/60 hover:bg-purple-600 text-purple-700 dark:text-purple-300 hover:text-white font-semibold text-[11px] transition flex items-center gap-1 cursor-pointer"
                  >
                    <ArrowRight class="w-3 h-3" />
                    <span>A Caminho (Deslocamento)</span>
                  </button>

                  <button
                    v-else-if="disp.status === 'ON_ROUTE'"
                    type="button"
                    @click="advanceDispatch(disp, 'ARRIVED_SITE')"
                    class="h-7 px-2.5 rounded-md bg-amber-50 dark:bg-amber-950/60 hover:bg-amber-600 text-amber-700 dark:text-amber-300 hover:text-white font-semibold text-[11px] transition flex items-center gap-1 cursor-pointer"
                  >
                    <MapPin class="w-3 h-3" />
                    <span>Chegou no Local</span>
                  </button>

                  <button
                    v-else-if="disp.status === 'ARRIVED_SITE'"
                    type="button"
                    @click="advanceDispatch(disp, 'IN_SERVICE')"
                    class="h-7 px-2.5 rounded-md bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-600 text-blue-700 dark:text-blue-300 hover:text-white font-semibold text-[11px] transition flex items-center gap-1 cursor-pointer"
                  >
                    <Wrench class="w-3 h-3" />
                    <span>Iniciar Serviço</span>
                  </button>

                  <button
                    v-else-if="disp.status === 'IN_SERVICE'"
                    type="button"
                    @click="advanceDispatch(disp, 'COMPLETED')"
                    class="h-7 px-2.5 rounded-md bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-600 text-emerald-700 dark:text-emerald-300 hover:text-white font-semibold text-[11px] transition flex items-center gap-1 cursor-pointer"
                  >
                    <CheckCircle2 class="w-3 h-3" />
                    <span>Concluir Atendimento</span>
                  </button>

                  <span
                    v-else-if="disp.status === 'COMPLETED'"
                    class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1"
                  >
                    <CheckCircle2 class="w-3.5 h-3.5" />
                    <span>Finalizado</span>
                  </span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Paginação -->
        <TablePagination
          v-if="dispatchesPagination.total > dispatchesPagination.per_page"
          :current-page="dispatchesPagination.current_page"
          :total-items="dispatchesPagination.total"
          :per-page="dispatchesPagination.per_page"
          @page-change="onDispatchesPageChange"
        />
      </div>
    </div>

    <!-- Modais do Módulo -->
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

    <DispatchModal
      v-model="isDispatchModalOpen"
      :ticket="ticketToDispatch"
      :workers="catalogs.workers"
      :depots="catalogs.depots"
      @dispatched="onTicketDispatched"
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
import { ref, reactive, computed, onMounted, onUnmounted, watch } from 'vue';
import axios from 'axios';
import {
  Activity,
  Plus,
  RefreshCw,
  Inbox,
  Ticket as TicketIcon,
  Truck,
  Search,
  Loader2,
  Sparkles,
  Pencil,
  Trash2,
  CheckCircle2,
  AlertCircle,
  Clock,
  ArrowRight,
  MapPin,
  Wrench,
} from 'lucide-vue-next';
import BasePageHeader from '../components/common/BasePageHeader.vue';
import TablePagination from '../components/common/TablePagination.vue';
import TaskModal from '../components/operations/TaskModal.vue';
import PromoteToTicketModal from '../components/operations/PromoteToTicketModal.vue';
import DispatchModal from '../components/operations/DispatchModal.vue';

type TabKey = 'tasks' | 'tickets' | 'dispatches';

function getInitialTab(): TabKey {
  try {
    const hash = window.location.hash || '';
    const params = new URLSearchParams(hash.split('?')[1] || '');
    const tabParam = params.get('tab') as TabKey;
    if (tabParam && ['tasks', 'tickets', 'dispatches'].includes(tabParam)) {
      return tabParam;
    }
    const saved = localStorage.getItem('rp_operations_active_tab') as TabKey;
    if (saved && ['tasks', 'tickets', 'dispatches'].includes(saved)) {
      return saved;
    }
  } catch (e) {
    // Fallback silencioso
  }
  return 'tasks';
}

const currentTab = ref<TabKey>(getInitialTab());

function setTab(tab: TabKey) {
  currentTab.value = tab;
  updateTabInUrlAndStorage(tab);
  if (tab === 'tasks' && !tasksList.value.length) fetchTasks();
  if (tab === 'tickets' && !ticketsList.value.length) fetchTickets();
  if (tab === 'dispatches' && !dispatchesList.value.length) fetchDispatches();
}

function updateTabInUrlAndStorage(tab: TabKey) {
  try {
    localStorage.setItem('rp_operations_active_tab', tab);
    const currentHash = window.location.hash.replace(/^#\/?/, '').split('?')[0].split('/')[0] || 'operations';
    const newHash = `${currentHash}?tab=${tab}`;
    if (window.location.hash !== `#${newHash}`) {
      history.replaceState(null, '', `#${newHash}`);
    }
  } catch (e) {
    // Fallback silencioso
  }
}

watch(currentTab, (newTab) => {
  updateTabInUrlAndStorage(newTab);
});

function handleHashChangeForTabs() {
  const currentTabInHash = getInitialTab();
  if (currentTabInHash && currentTabInHash !== currentTab.value) {
    currentTab.value = currentTabInHash;
  }
}

const breadcrumbItems = computed(() => [
  { label: 'Início', href: '#people' },
  { label: 'Operações', href: '#operations' },
  { label: currentTab.value === 'tasks' ? 'Inbox de Tarefas' : (currentTab.value === 'tickets' ? 'Chamados com SLA' : 'Painel de Despacho') },
]);

const headerBadgeText = computed(() => {
  if (ticketsCounts.sla_breached > 0) {
    return `${ticketsCounts.sla_breached} SLA Estourado!`;
  }
  return `${ticketsCounts.open || 0} Chamados Ativos`;
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

// Catálogos Operacionais (Departamentos, Técnicos, Depósitos)
const catalogs = reactive<{
  departments: any[];
  workers: any[];
  depots: any[];
}>({
  departments: [],
  workers: [],
  depots: [],
});

const fetchCatalogs = async () => {
  try {
    const res = await axios.get('/api/v1/operations/catalogs');
    if (res.data?.data) {
      Object.assign(catalogs, res.data.data);
    }
  } catch (e) {
    // Fallback silencioso
  }
};

// -------------------------------------------------------------
// ABA 1: TAREFAS (TASKS)
// -------------------------------------------------------------
const tasksList = ref<any[]>([]);
const isLoadingTasks = ref(false);
const tasksSearch = ref('');
const tasksStatusFilter = ref('ALL');
const tasksSourceFilter = ref('ALL');
const tasksCounts = reactive<any>({});
const tasksPagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
});

let tasksDebounce: any = null;
function debounceFetchTasks() {
  clearTimeout(tasksDebounce);
  tasksDebounce = setTimeout(() => {
    tasksPagination.current_page = 1;
    fetchTasks();
  }, 350);
}

const fetchTasks = async () => {
  isLoadingTasks.value = true;
  try {
    const res = await axios.get('/api/v1/operations/tasks', {
      params: {
        page: tasksPagination.current_page,
        per_page: tasksPagination.per_page,
        search: tasksSearch.value || undefined,
        status: tasksStatusFilter.value !== 'ALL' ? tasksStatusFilter.value : undefined,
        source: tasksSourceFilter.value !== 'ALL' ? tasksSourceFilter.value : undefined,
      },
    });

    tasksList.value = res.data?.data || [];
    if (res.data?.meta) {
      tasksPagination.current_page = res.data.meta.current_page;
      tasksPagination.last_page = res.data.meta.last_page;
      tasksPagination.total = res.data.meta.total;
      if (res.data.meta.counts) {
        Object.assign(tasksCounts, res.data.meta.counts);
      }
    }
  } catch (err: any) {
    showToast('Erro ao carregar tarefas do Inbox.', 'error');
  } finally {
    isLoadingTasks.value = false;
  }
};

function onTasksPageChange(page: number) {
  tasksPagination.current_page = page;
  fetchTasks();
}

// -------------------------------------------------------------
// ABA 2: CHAMADOS (TICKETS)
// -------------------------------------------------------------
const ticketsList = ref<any[]>([]);
const isLoadingTickets = ref(false);
const ticketsSearch = ref('');
const ticketsStatusFilter = ref('ALL');
const ticketsDepartmentFilter = ref('ALL');
const ticketsCounts = reactive<any>({
  total: 0,
  open: 0,
  waiting_dispatch: 0,
  in_field: 0,
  resolved: 0,
  sla_breached: 0,
});
const ticketsPagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
});

let ticketsDebounce: any = null;
function debounceFetchTickets() {
  clearTimeout(ticketsDebounce);
  ticketsDebounce = setTimeout(() => {
    ticketsPagination.current_page = 1;
    fetchTickets();
  }, 350);
}

const fetchTickets = async () => {
  isLoadingTickets.value = true;
  try {
    const res = await axios.get('/api/v1/operations/tickets', {
      params: {
        page: ticketsPagination.current_page,
        per_page: ticketsPagination.per_page,
        search: ticketsSearch.value || undefined,
        status: ticketsStatusFilter.value !== 'ALL' ? ticketsStatusFilter.value : undefined,
        department_id: ticketsDepartmentFilter.value !== 'ALL' ? ticketsDepartmentFilter.value : undefined,
      },
    });

    ticketsList.value = res.data?.data || [];
    if (res.data?.meta) {
      ticketsPagination.current_page = res.data.meta.current_page;
      ticketsPagination.last_page = res.data.meta.last_page;
      ticketsPagination.total = res.data.meta.total;
      if (res.data.meta.counts) {
        Object.assign(ticketsCounts, res.data.meta.counts);
      }
    }
  } catch (err: any) {
    showToast('Erro ao carregar chamados.', 'error');
  } finally {
    isLoadingTickets.value = false;
  }
};

function onTicketsPageChange(page: number) {
  ticketsPagination.current_page = page;
  fetchTickets();
}

// -------------------------------------------------------------
// ABA 3: ACIONAMENTOS DE CAMPO (DISPATCHES)
// -------------------------------------------------------------
const dispatchesList = ref<any[]>([]);
const isLoadingDispatches = ref(false);
const dispatchesSearch = ref('');
const dispatchesStatusFilter = ref('ALL');
const dispatchesCounts = reactive<any>({});
const dispatchesPagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
});

let dispatchesDebounce: any = null;
function debounceFetchDispatches() {
  clearTimeout(dispatchesDebounce);
  dispatchesDebounce = setTimeout(() => {
    dispatchesPagination.current_page = 1;
    fetchDispatches();
  }, 350);
}

const fetchDispatches = async () => {
  isLoadingDispatches.value = true;
  try {
    const res = await axios.get('/api/v1/operations/dispatches', {
      params: {
        page: dispatchesPagination.current_page,
        per_page: dispatchesPagination.per_page,
        search: dispatchesSearch.value || undefined,
        status: dispatchesStatusFilter.value !== 'ALL' ? dispatchesStatusFilter.value : undefined,
      },
    });

    dispatchesList.value = res.data?.data || [];
    if (res.data?.meta) {
      dispatchesPagination.current_page = res.data.meta.current_page;
      dispatchesPagination.last_page = res.data.meta.last_page;
      dispatchesPagination.total = res.data.meta.total;
      if (res.data.meta.counts) {
        Object.assign(dispatchesCounts, res.data.meta.counts);
      }
    }
  } catch (err: any) {
    showToast('Erro ao carregar acionamentos de campo.', 'error');
  } finally {
    isLoadingDispatches.value = false;
  }
};

function onDispatchesPageChange(page: number) {
  dispatchesPagination.current_page = page;
  fetchDispatches();
}

const advanceDispatch = async (dispatch: any, nextStatus: string) => {
  try {
    const res = await axios.patch(`/api/v1/operations/dispatches/${dispatch.id}/advance-status`, {
      status: nextStatus,
    });
    showToast(res.data?.message || 'Estágio do acionamento atualizado com sucesso!', 'success');
    await fetchDispatches();
    await fetchTickets();
  } catch (err: any) {
    showToast(err.response?.data?.message || 'Erro ao avançar estágio.', 'error');
  }
};

// -------------------------------------------------------------
// MODAIS E AÇÕES
// -------------------------------------------------------------
const isTaskModalOpen = ref(false);
const taskModalMode = ref<'create' | 'edit'>('create');
const selectedTask = ref<any>(null);

const isPromoteModalOpen = ref(false);
const taskToPromote = ref<any>(null);

const isDispatchModalOpen = ref(false);
const ticketToDispatch = ref<any>(null);

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

function openDispatchModal(ticket: any) {
  ticketToDispatch.value = ticket;
  isDispatchModalOpen.value = true;
}

function onTaskPromoted(ticket: any) {
  showToast(`Demanda promovida para Chamado com sucesso! Protocolo: ${ticket.protocol}`, 'success');
  fetchTasks();
  fetchTickets();
  setTab('tickets');
}

function onTicketDispatched() {
  showToast('Acionamento de campo emitido com sucesso!', 'success');
  fetchTickets();
  fetchDispatches();
  setTab('dispatches');
}

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

const deleteTicket = async (ticket: any) => {
  if (!confirm(`Deseja realmente excluir o chamado ${ticket.protocol}?`)) return;
  try {
    await axios.delete(`/api/v1/operations/tickets/${ticket.id}`);
    showToast('Chamado removido com sucesso!', 'success');
    fetchTickets();
  } catch (err: any) {
    showToast(err.response?.data?.message || 'Erro ao excluir chamado.', 'error');
  }
};

const refreshActiveTabData = () => {
  fetchCatalogs();
  if (currentTab.value === 'tasks') fetchTasks();
  if (currentTab.value === 'tickets') fetchTickets();
  if (currentTab.value === 'dispatches') fetchDispatches();
};

const isLoading = computed(() => isLoadingTasks.value || isLoadingTickets.value || isLoadingDispatches.value);

// Formatadores e Helpers Visuais
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

function formatTicketStatus(s: string): string {
  switch (s) {
    case 'OPEN': return 'Aberto';
    case 'IN_TRIAGE': return 'Em Triagem';
    case 'WAITING_DISPATCH': return 'Aguardando Campo';
    case 'IN_FIELD': return 'Em Atendimento (Campo)';
    case 'CLOSED': return 'Encerrado';
    case 'CANCELED': return 'Cancelado';
    default: return s;
  }
}

function getTicketStatusBadgeClass(s: string): string {
  switch (s) {
    case 'OPEN': return 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/60 dark:text-blue-300';
    case 'IN_TRIAGE': return 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/60 dark:text-amber-300';
    case 'WAITING_DISPATCH': return 'bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-950/60 dark:text-purple-300';
    case 'IN_FIELD': return 'bg-orange-50 text-orange-700 border border-orange-200 dark:bg-orange-950/60 dark:text-orange-300 font-bold';
    case 'CLOSED': return 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300';
    default: return 'bg-slate-100 text-slate-600';
  }
}

function formatDispatchStatus(s: string): string {
  switch (s) {
    case 'DISPATCHED': return 'Despachado';
    case 'ON_ROUTE': return 'A Caminho (Deslocamento)';
    case 'ARRIVED_SITE': return 'No Local';
    case 'IN_SERVICE': return 'Em Serviço';
    case 'COMPLETED': return 'Concluído';
    case 'FAILED': return 'Insucesso';
    default: return s;
  }
}

function getDispatchStatusBadgeClass(s: string): string {
  switch (s) {
    case 'DISPATCHED': return 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300';
    case 'ON_ROUTE': return 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300';
    case 'ARRIVED_SITE': return 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300';
    case 'IN_SERVICE': return 'bg-orange-100 text-orange-800 dark:bg-orange-950 dark:text-orange-300 font-bold';
    case 'COMPLETED': return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
    default: return 'bg-slate-100 text-slate-700';
  }
}

function isSlaBreached(ticket: any): boolean {
  if (['CLOSED', 'CANCELED'].includes(ticket.status)) return false;
  if (!ticket.sla_due_at) return false;
  return new Date(ticket.sla_due_at).getTime() < Date.now();
}

function formatSlaRemaining(slaDueAt: string | null, status: string): string {
  if (['CLOSED', 'CANCELED'].includes(status)) return 'Cumprido';
  if (!slaDueAt) return '--';

  const diffMs = new Date(slaDueAt).getTime() - Date.now();
  if (diffMs < 0) {
    const hoursOver = Math.abs(Math.round(diffMs / 3600000));
    return `Vencido há ${hoursOver}h`;
  }
  const hoursLeft = Math.round(diffMs / 3600000);
  if (hoursLeft === 0) return 'Menos de 1h';
  return `${hoursLeft}h restantes`;
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
  if (currentTab.value === 'tasks') fetchTasks();
  if (currentTab.value === 'tickets') fetchTickets();
  if (currentTab.value === 'dispatches') fetchDispatches();
  window.addEventListener('hashchange', handleHashChangeForTabs);
});

onUnmounted(() => {
  window.removeEventListener('hashchange', handleHashChangeForTabs);
});
</script>
