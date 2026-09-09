<template>
  <div class="py-8 w-full space-y-6">
    <!-- Cabeçalho Confortável Integrado (Sem Top Bar) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold font-heading text-[#06064D] dark:text-white tracking-tight">
          Gestão de Pessoas
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium">
          Base canônica centralizada de colaboradores, clientes, fornecedores e solicitantes
        </p>
      </div>

      <!-- Botão de Ação Primária Institucional (Laranja #FC6714) -->
      <button
        @click="openCreateModal"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-sm font-semibold shadow-sm transition active:scale-98 cursor-pointer focus:ring-2 focus:ring-[#FC6714] focus:ring-offset-2 focus:outline-none"
      >
        <Plus class="w-4 h-4" />
        <span>Nova Pessoa</span>
      </button>
    </div>

    <!-- Barra de Filtros e Busca Principal -->
    <div class="space-y-3">
      <div class="p-4 bg-white dark:bg-[#06064D]/50 rounded-xl border border-slate-200 dark:border-[#14147A] flex flex-wrap items-center justify-between gap-4 text-sm shadow-2xs transition-colors duration-200">
        <!-- Busca Textual com Suporte a Atalho (Ctrl+K ou /) e Botão Limpar (X) -->
        <div class="relative flex-1 min-w-[280px]">
          <Search class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400 pointer-events-none" />
          <input
            ref="searchInputRef"
            type="text"
            v-model="search"
            @input="debounceSearch"
            placeholder="Buscar por nome, CPF/CNPJ, e-mail ou telefone..."
            class="w-full h-11 pl-10 pr-20 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 text-sm transition"
          />

          <!-- Canto Direito da Busca: Botão Limpar (X) e Indicador de Atalho (Ctrl K) -->
          <div class="absolute right-3 top-3 flex items-center gap-1.5">
            <button
              v-if="search"
              @click="clearSearch"
              class="p-1 rounded text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800 transition cursor-pointer focus:ring-2 focus:ring-[#FC6714]"
              title="Limpar pesquisa (Esc)"
            >
              <X class="w-3.5 h-3.5" />
            </button>
            <kbd
              v-else
              class="hidden sm:inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-[10px] font-mono text-slate-400 select-none shadow-2xs"
              title="Atalho de teclado: Ctrl+K ou /"
            >
              Ctrl K
            </kbd>
          </div>
        </div>

        <!-- Botão de Filtros Secundários Avançados (Gaveta Retrátil) -->
        <button
          type="button"
          @click="isFiltersOpen = !isFiltersOpen"
          :class="activeSecondaryFiltersCount > 0
            ? 'bg-[#FC6714]/10 text-[#FC6714] border-[#FC6714] font-semibold'
            : 'bg-white dark:bg-[#03032E] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-[#14147A] hover:bg-slate-50 dark:hover:bg-white/5'"
          class="inline-flex items-center gap-2 h-11 px-4 rounded-lg border text-sm font-medium transition shadow-2xs cursor-pointer focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
          title="Abrir/fechar filtros secundários avançados"
        >
          <SlidersHorizontal class="w-4 h-4" :class="activeSecondaryFiltersCount > 0 ? 'text-[#FC6714]' : 'text-slate-500 dark:text-slate-400'" />
          <span>Filtros</span>
          <span
            v-if="activeSecondaryFiltersCount > 0"
            class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-[#FC6714] text-white"
          >
            {{ activeSecondaryFiltersCount }}
          </span>
          <ChevronDown
            class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400"
            :class="{ 'rotate-180': isFiltersOpen }"
          />
        </button>

        <!-- Filtro por Persona (Tabs com Contadores Numéricos e Destaque Laranja Institucional) -->
        <div class="flex items-center gap-1 bg-slate-100 dark:bg-[#03032E] p-1 rounded-lg border border-slate-200 dark:border-[#14147A] overflow-x-auto max-w-full">
          <button
            v-for="p in personaFilters"
            :key="p.value"
            @click="selectPersona(p.value)"
            :class="selectedPersona === p.value
              ? 'bg-[#FC6714] text-white font-bold shadow-xs'
              : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'"
            class="px-3 py-1.5 rounded-md text-xs transition whitespace-nowrap cursor-pointer flex items-center gap-1.5 focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
          >
            <span>{{ p.label }}</span>
            <span
              :class="selectedPersona === p.value
                ? 'bg-white/25 text-white'
                : 'bg-slate-200/80 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
              class="px-1.5 py-0.2 rounded-full text-[10px] font-bold tracking-tight"
            >
              {{ counts[p.value || 'total'] ?? 0 }}
            </span>
          </button>
        </div>
      </div>

      <!-- Painel Retrátil de Filtros Secundários Avançados -->
      <div
        v-if="isFiltersOpen"
        class="p-4 bg-slate-50 dark:bg-[#06064D]/30 rounded-xl border border-slate-200 dark:border-[#14147A] animate-fadeIn transition-all shadow-xs"
      >
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200 dark:border-[#14147A]">
          <div class="flex items-center gap-2">
            <SlidersHorizontal class="w-4 h-4 text-[#FC6714]" />
            <span class="text-xs font-semibold font-heading uppercase tracking-wider text-slate-800 dark:text-slate-200">
              Filtros Secundários Avançados
            </span>
          </div>

          <button
            v-if="activeSecondaryFiltersCount > 0"
            @click="clearSecondaryFilters"
            class="text-xs font-semibold text-[#FC6714] hover:text-[#E0530A] transition cursor-pointer flex items-center gap-1 focus:ring-2 focus:ring-[#FC6714]"
          >
            <RotateCcw class="w-3.5 h-3.5" />
            <span>Limpar Filtros</span>
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
          <!-- Filtro por Status -->
          <div>
            <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              Status do Registro
            </label>
            <select
              v-model="filterStatus"
              @change="applySecondaryFilters"
              class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-[#14147A] bg-white dark:bg-[#03032E] text-slate-900 dark:text-slate-100 font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer"
            >
              <option value="">Todos os Status</option>
              <option value="active">● Apenas Ativos</option>
              <option value="inactive">○ Apenas Inativos</option>
            </select>
          </div>

          <!-- Filtro por Tipo de Pessoa -->
          <div>
            <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              Tipo de Pessoa
            </label>
            <select
              v-model="filterPersonType"
              @change="applySecondaryFilters"
              class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-[#14147A] bg-white dark:bg-[#03032E] text-slate-900 dark:text-slate-100 font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer"
            >
              <option value="">Todos os Tipos</option>
              <option value="individual">Pessoa Física (PF)</option>
              <option value="legal">Pessoa Jurídica (PJ)</option>
            </select>
          </div>

          <!-- Filtro por Estado (UF) -->
          <div>
            <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              Estado (UF)
            </label>
            <select
              v-model="filterStateId"
              @change="handleStateChange"
              class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-[#14147A] bg-white dark:bg-[#03032E] text-slate-900 dark:text-slate-100 font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer"
            >
              <option value="">Todos os Estados</option>
              <option v-for="st in statesList" :key="st.id" :value="st.id">
                {{ st.code }} - {{ st.name }}
              </option>
            </select>
          </div>

          <!-- Filtro por Cidade -->
          <div>
            <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              Cidade
            </label>
            <select
              v-model="filterCityId"
              @change="applySecondaryFilters"
              class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-[#14147A] bg-white dark:bg-[#03032E] text-slate-900 dark:text-slate-100 font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer"
            >
              <option value="">Todas as Cidades</option>
              <option v-for="ct in citiesList" :key="ct.id" :value="ct.id">
                {{ ct.name }}
              </option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabela Confortável com Linhas Finas e Ordenação Clicável nos Cabeçalhos -->
    <div class="bg-white dark:bg-[#06064D]/50 rounded-xl border border-slate-200 dark:border-[#14147A] overflow-hidden shadow-xs transition-colors duration-200">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
          <thead>
            <tr class="border-b border-slate-200 dark:border-[#14147A] bg-slate-50/80 dark:bg-[#03032E]/70 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider select-none">
              <!-- Coluna 1: Nome (Ordenável) -->
              <th
                @click="toggleSort('name')"
                class="py-3.5 px-5 cursor-pointer hover:text-[#FC6714] dark:hover:text-[#FC6714] transition"
                title="Clique para ordenar por Nome"
              >
                <div class="inline-flex items-center gap-1.5">
                  <span>Nome</span>
                  <ArrowUp v-if="sortBy === 'name' && sortDirection === 'asc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                  <ArrowDown v-else-if="sortBy === 'name' && sortDirection === 'desc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                  <ArrowUpDown v-else class="w-3.5 h-3.5 text-slate-400 opacity-60" />
                </div>
              </th>

              <!-- Coluna 2: Tipo & Documento -->
              <th class="py-3.5 px-5">Tipo & Documento</th>


              <!-- Coluna 4: Ações Rápidas de Contato -->
              <th class="py-3.5 px-5">Ações Rápidas de Contato</th>

              <!-- Coluna 4: Município (Ordenável) -->
              <th
                @click="toggleSort('city')"
                class="py-3.5 px-5 cursor-pointer hover:text-[#FC6714] dark:hover:text-[#FC6714] transition"
                title="Clique para ordenar por Município"
              >
                <div class="inline-flex items-center gap-1.5">
                  <span>Município</span>
                  <ArrowUp v-if="sortBy === 'city' && sortDirection === 'asc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                  <ArrowDown v-else-if="sortBy === 'city' && sortDirection === 'desc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                  <ArrowUpDown v-else class="w-3.5 h-3.5 text-slate-400 opacity-60" />
                </div>
              </th>

              <!-- Coluna 6: Data de Cadastro (Ordenável) -->
              <th
                @click="toggleSort('date')"
                class="py-3.5 px-5 cursor-pointer hover:text-[#FC6714] dark:hover:text-[#FC6714] transition"
                title="Clique para ordenar por Data de Cadastro"
              >
                <div class="inline-flex items-center gap-1.5">
                  <span>Cadastro</span>
                  <ArrowUp v-if="sortBy === 'date' && sortDirection === 'asc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                  <ArrowDown v-else-if="sortBy === 'date' && sortDirection === 'desc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                  <ArrowUpDown v-else class="w-3.5 h-3.5 text-slate-400 opacity-60" />
                </div>
              </th>

              <!-- Coluna 7: Status -->
              <th class="py-3.5 px-5">Status</th>

              <!-- Coluna 8: Ações -->
              <th class="py-3.5 px-5 text-right">Ação</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
            <!-- Loading -->
            <tr v-if="loading">
              <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                <span class="inline-block animate-spin mr-2 text-[#FC6714]">⟳</span> Carregando registros...
              </td>
            </tr>

            <!-- Vazio -->
            <tr v-else-if="people.length === 0">
              <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                Nenhuma pessoa encontrada com os filtros selecionados.
              </td>
            </tr>

            <!-- Linha da Tabela -->
            <tr
              v-else
              v-for="person in people"
              :key="person.id"
              class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition-colors"
            >
              <!-- Nome -->
              <td class="py-4 px-5">
                <div class="font-semibold text-slate-900 dark:text-slate-100 text-sm">
                  {{ person.name }}
                </div>
                <div v-if="person.trade_name" class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                  {{ person.trade_name }}
                </div>
                <div v-if="person.group_name && person.group_name !== 'Geral'" class="text-[11px] font-semibold text-[#FC6714] mt-0.5">
                  Grupo: {{ person.group_name }}
                </div>
              </td>

              <!-- Tipo & Documento com Máscara Dinâmica e Cópia em 1 Clique -->
              <td class="py-4 px-5">
                <div class="flex items-center gap-2">
                  <span
                    class="inline-block px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider border"
                    :class="person.person_type === 'individual'
                      ? 'bg-amber-50 text-amber-700 border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60'
                      : 'bg-indigo-50 text-indigo-700 border-indigo-200/80 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800/60'"
                  >
                    {{ person.person_type === 'individual' ? 'PF' : 'PJ' }}
                  </span>

                  <span class="font-mono text-xs font-medium text-slate-700 dark:text-slate-200 select-all">
                    {{ formatDocument(person.document_number, person.person_type) }}
                  </span>

                  <!-- Botão discreto de cópia rápida com 1 clique -->
                  <button
                    v-if="person.document_number"
                    @click="copyToClipboard(person.document_number, 'doc-' + person.id)"
                    class="p-1 rounded text-slate-400 hover:text-[#FC6714] hover:bg-[#FC6714]/10 transition relative group cursor-pointer focus:ring-2 focus:ring-[#FC6714]"
                    :title="copiedKey === 'doc-' + person.id ? 'Copiado!' : 'Copiar documento'"
                  >
                    <Check v-if="copiedKey === 'doc-' + person.id" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                    <Copy v-else class="w-3.5 h-3.5" />

                    <!-- Tooltip temporário -->
                    <span
                      v-if="copiedKey === 'doc-' + person.id"
                      class="absolute -top-7 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded bg-[#06064D] text-white text-[10px] font-medium shadow-md whitespace-nowrap z-20 pointer-events-none"
                    >
                      Copiado!
                    </span>
                  </button>
                </div>
              </td>

              <!-- Ações Rápidas de Contato (Links Operacionais Diretos) -->
              <td class="py-4 px-5 text-sm">
                <div class="space-y-1">
                  <!-- Telefone & WhatsApp Operacionais -->
                  <div v-if="person.contact?.phone || person.contact?.whatsapp" class="flex items-center gap-2">
                    <!-- Link de Discagem Direta (tel:) -->
                    <a
                      :href="'tel:+55' + sanitizePhone(person.contact?.phone || person.contact?.whatsapp)"
                      class="inline-flex items-center gap-1 text-slate-700 dark:text-slate-200 hover:text-[#FC6714] font-medium text-xs transition group focus:ring-1 focus:ring-[#FC6714]"
                      title="Ligar pelo discador"
                    >
                      <Phone class="w-3.5 h-3.5 text-slate-400 group-hover:text-[#FC6714]" />
                      <span>{{ formatPhone(person.contact?.phone || person.contact?.whatsapp) }}</span>
                    </a>

                    <!-- Link direto para WhatsApp -->
                    <a
                      :href="'https://wa.me/55' + sanitizePhone(person.contact?.whatsapp || person.contact?.phone)"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="p-1 rounded text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 transition cursor-pointer focus:ring-1 focus:ring-[#FC6714]"
                      title="Abrir conversa no WhatsApp"
                    >
                      <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-10.416c-4.412 0-8 3.588-8 8 0 1.411.367 2.736 1.009 3.886l-1.072 3.921 4.024-1.055c1.112.607 2.384.948 3.739.948 4.412 0 8-3.588 8-8s-3.588-8-8-8z"/>
                      </svg>
                    </a>

                    <!-- Botão de Cópia de Telefone -->
                    <button
                      @click="copyToClipboard(person.contact?.phone || person.contact?.whatsapp, 'phone-' + person.id)"
                      class="p-0.5 rounded text-slate-400 hover:text-[#FC6714] transition cursor-pointer focus:ring-1 focus:ring-[#FC6714]"
                      title="Copiar telefone"
                    >
                      <Check v-if="copiedKey === 'phone-' + person.id" class="w-3 h-3 text-emerald-600 dark:text-emerald-400" />
                      <Copy v-else class="w-3 h-3" />
                    </button>
                  </div>
                  <div v-else class="text-xs text-slate-400 italic">Sem telefone</div>

                  <!-- E-mail Operacional (mailto:) -->
                  <div v-if="person.contact?.email" class="flex items-center gap-1.5">
                    <a
                      :href="'mailto:' + person.contact?.email"
                      class="inline-flex items-center gap-1 text-slate-500 dark:text-slate-400 hover:text-[#FC6714] text-xs transition truncate max-w-[190px] group focus:ring-1 focus:ring-[#FC6714]"
                      :title="'Enviar e-mail para ' + person.contact?.email"
                    >
                      <Mail class="w-3.5 h-3.5 text-slate-400 group-hover:text-[#FC6714] flex-shrink-0" />
                      <span class="truncate">{{ person.contact?.email }}</span>
                    </a>

                    <!-- Botão de Cópia de E-mail -->
                    <button
                      @click="copyToClipboard(person.contact?.email, 'email-' + person.id)"
                      class="p-0.5 rounded text-slate-400 hover:text-[#FC6714] transition flex-shrink-0 cursor-pointer focus:ring-1 focus:ring-[#FC6714]"
                      title="Copiar e-mail"
                    >
                      <Check v-if="copiedKey === 'email-' + person.id" class="w-3 h-3 text-emerald-600 dark:text-emerald-400" />
                      <Copy v-else class="w-3 h-3" />
                    </button>
                  </div>
                </div>
              </td>

              <!-- Município -->
              <td class="py-4 px-5 text-sm font-medium text-slate-700 dark:text-slate-300">
                <div>{{ person.residential_address?.city_name || person.address?.city_name || person.address?.city?.name || '-' }}</div>
                <div v-if="person.residential_address?.state_code || person.address?.state_code" class="text-xs text-slate-400">
                  {{ person.residential_address?.state_code || person.address?.state_code }}
                </div>
              </td>

              <!-- Data de Cadastro -->
              <td class="py-4 px-5 text-xs text-slate-600 dark:text-slate-400 whitespace-nowrap">
                <div class="font-medium text-slate-800 dark:text-slate-200">
                  {{ person.registration_date_formatted || person.created_at?.split(' ')[0] || '-' }}
                </div>
                <div class="text-[11px] text-slate-400">
                  {{ person.created_at?.split(' ')[1] || '' }}
                </div>
              </td>

              <!-- Status -->
              <td class="py-4 px-5">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                  :class="person.status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-400'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="person.status === 'active' ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                  {{ person.status === 'active' ? 'Ativo' : 'Inativo' }}
                </span>
              </td>

              <!-- Ação (Menu de Reticências) -->
              <td class="py-4 px-5 text-right relative" @click.stop>
                <div class="inline-block text-left">
                  <button
                    type="button"
                    @click="toggleActionsDropdown(person.id)"
                    class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:text-[#FC6714] hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 transition cursor-pointer focus:outline-hidden focus:ring-2 focus:ring-[#FC6714]"
                    :class="{ 'bg-orange-50 dark:bg-[#FC6714]/15 text-[#FC6714] ring-2 ring-[#FC6714]/40': activeDropdownPersonId === person.id }"
                    title="Opções da pessoa"
                    aria-label="Opções"
                    aria-haspopup="true"
                    :aria-expanded="activeDropdownPersonId === person.id"
                  >
                    <MoreVertical class="w-4 h-4" />
                  </button>

                  <!-- Dropdown Menu Flutuante -->
                  <Transition
                    enter-active-class="transition duration-100 ease-out"
                    enter-from-class="transform scale-95 opacity-0"
                    enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-75 ease-in"
                    leave-from-class="transform scale-100 opacity-100"
                    leave-to-class="transform scale-95 opacity-0"
                  >
                    <div
                      v-if="activeDropdownPersonId === person.id"
                      class="absolute right-5 mt-1.5 w-48 rounded-xl bg-white dark:bg-[#06064D] border border-slate-200 dark:border-[#14147A] shadow-xl z-50 py-1 text-xs font-medium divide-y divide-slate-100 dark:divide-[#14147A]/60 focus:outline-hidden"
                    >
                      <div class="py-1">
                        <button
                          type="button"
                          @click="handleActionEdit(person)"
                          class="w-full text-left px-3.5 py-2 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] dark:hover:text-orange-300 transition cursor-pointer"
                        >
                          <Edit2 class="w-3.5 h-3.5 text-[#FC6714]" />
                          <span>Editar pessoa</span>
                        </button>

                        <button
                          type="button"
                          @click="handleActionToggleStatus(person)"
                          class="w-full text-left px-3.5 py-2 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#14147A]/50 transition cursor-pointer"
                        >
                          <Power class="w-3.5 h-3.5" :class="person.status === 'active' ? 'text-amber-500' : 'text-emerald-500'" />
                          <span>{{ person.status === 'active' ? 'Inativar pessoa' : 'Ativar pessoa' }}</span>
                        </button>
                      </div>

                      <div class="py-1">
                        <button
                          type="button"
                          @click="handleActionCopyId(person)"
                          class="w-full text-left px-3.5 py-2 flex items-center gap-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-[#14147A]/50 hover:text-slate-700 dark:text-slate-300 transition cursor-pointer"
                        >
                          <Copy class="w-3.5 h-3.5" />
                          <span>Copiar ID (#{{ person.id }})</span>
                        </button>
                      </div>
                    </div>
                  </Transition>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginação Confortável -->
      <div class="px-5 py-3.5 border-t border-slate-200 dark:border-[#14147A] flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 bg-slate-50/50 dark:bg-[#03032E]/50">
        <div>
          Exibindo <strong>{{ people.length }}</strong> de <strong>{{ totalRecords }}</strong> pessoas
        </div>
        <div class="flex items-center gap-1.5">
          <button
            :disabled="currentPage <= 1"
            @click="changePage(currentPage - 1)"
            class="px-3 py-1.5 rounded-md border border-slate-200 dark:border-[#14147A] hover:bg-white dark:hover:bg-[#06064D] hover:border-[#FC6714] hover:text-[#FC6714] disabled:opacity-40 font-medium transition cursor-pointer focus:ring-2 focus:ring-[#FC6714]"
          >
            Anterior
          </button>
          <span class="px-3 font-semibold text-slate-800 dark:text-slate-200">{{ currentPage }} / {{ lastPage }}</span>
          <button
            :disabled="currentPage >= lastPage"
            @click="changePage(currentPage + 1)"
            class="px-3 py-1.5 rounded-md border border-slate-200 dark:border-[#14147A] hover:bg-white dark:hover:bg-[#06064D] hover:border-[#FC6714] hover:text-[#FC6714] disabled:opacity-40 font-medium transition cursor-pointer focus:ring-2 focus:ring-[#FC6714]"
          >
            Próxima
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Cadastro/Edição -->
    <PersonModal
      :isOpen="isModalOpen"
      :personToEdit="personEditing"
      @close="isModalOpen = false"
      @saved="fetchPeople"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import {
  Plus, Search, Edit2, Power, Copy, Check, Phone, Mail, X,
  SlidersHorizontal, ChevronDown, RotateCcw, ArrowUpDown, ArrowUp, ArrowDown
} from 'lucide-vue-next';
import axios from 'axios';
import PersonModal from '../components/people/PersonModal.vue';

const people = ref<any[]>([]);
const loading = ref(false);
const search = ref('');
const selectedPersona = ref('');
const currentPage = ref(1);
const lastPage = ref(1);
const totalRecords = ref(0);

const isModalOpen = ref(false);
const personEditing = ref<any | null>(null);

// Input ref para atalho de teclado global
const searchInputRef = ref<HTMLInputElement | null>(null);

// Contadores por aba
const counts = ref<Record<string, number>>({
  total: 0,
  client: 0,
  supplier: 0,
  employee: 0,
  outsourced: 0,
  seller: 0,
  driver: 0,
  carrier: 0,
  requester: 0,
});

// Ordenação de Colunas
const sortBy = ref('name');
const sortDirection = ref<'asc' | 'desc'>('asc');

const toggleSort = (col: string) => {
  if (sortBy.value === col) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortBy.value = col;
    sortDirection.value = 'asc';
  }
  currentPage.value = 1;
  fetchPeople();
};

// Filtros Secundários Avançados
const isFiltersOpen = ref(false);
const filterStatus = ref('');
const filterPersonType = ref('');
const filterStateId = ref('');
const filterCityId = ref('');

const statesList = ref<any[]>([]);
const citiesList = ref<any[]>([]);

const activeSecondaryFiltersCount = computed(() => {
  let c = 0;
  if (filterStatus.value) c++;
  if (filterPersonType.value) c++;
  if (filterStateId.value) c++;
  if (filterCityId.value) c++;
  return c;
});

const loadStates = async () => {
  try {
    const res = await axios.get('/api/v1/states');
    statesList.value = res.data.data || [];
  } catch (err) {
    console.error('Erro ao carregar estados', err);
  }
};

const handleStateChange = async () => {
  filterCityId.value = '';
  citiesList.value = [];
  if (filterStateId.value) {
    try {
      const res = await axios.get('/api/v1/cities', {
        params: { state_id: filterStateId.value }
      });
      citiesList.value = res.data.data || [];
    } catch (err) {
      console.error('Erro ao carregar cidades do estado', err);
    }
  }
  applySecondaryFilters();
};

const applySecondaryFilters = () => {
  currentPage.value = 1;
  fetchPeople();
};

const clearSecondaryFilters = () => {
  filterStatus.value = '';
  filterPersonType.value = '';
  filterStateId.value = '';
  filterCityId.value = '';
  citiesList.value = [];
  currentPage.value = 1;
  fetchPeople();
};

// Limpar pesquisa
const clearSearch = () => {
  search.value = '';
  currentPage.value = 1;
  fetchPeople();
  searchInputRef.value?.focus();
};

// Atalho de teclado global: Ctrl+K / Cmd+K ou /
const handleGlobalKeyDown = (e: KeyboardEvent) => {
  const isInputActive = ['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName || '');

  // Ctrl+K ou Cmd+K
  if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
    e.preventDefault();
    searchInputRef.value?.focus();
    searchInputRef.value?.select();
  }

  // Tecla / (quando fora de outros campos)
  if (e.key === '/' && !isInputActive) {
    e.preventDefault();
    searchInputRef.value?.focus();
    searchInputRef.value?.select();
  }

  // Tecla Esc para limpar se o campo de busca estiver focado
  if (e.key === 'Escape' && document.activeElement === searchInputRef.value) {
    if (search.value) {
      clearSearch();
    } else {
      searchInputRef.value?.blur();
    }
  }
};

// Estado para controle de cópia rápida
const copiedKey = ref<string | null>(null);

// Controle de expansão de papéis para evitar sobrecarga visual
const expandedRolesMap = ref<Record<number, boolean>>({});

const isRolesExpanded = (personId: number) => {
  return !!expandedRolesMap.value[personId];
};

const toggleExpandRoles = (personId: number) => {
  expandedRolesMap.value[personId] = !expandedRolesMap.value[personId];
};

// Paleta semântica estrita de papéis (Personas)
const ROLE_CONFIGS = [
  {
    key: 'client',
    personaKey: 'is_client',
    label: 'Cliente',
    badgeClass: 'bg-emerald-50 text-emerald-700 border-emerald-200/80 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60',
  },
  {
    key: 'supplier',
    personaKey: 'is_supplier',
    label: 'Fornecedor',
    badgeClass: 'bg-purple-50 text-purple-700 border-purple-200/80 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/60',
  },
  {
    key: 'employee',
    personaKey: 'is_employee',
    label: 'Colaborador',
    badgeClass: 'bg-blue-50 text-blue-700 border-blue-200/80 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/60',
  },
  {
    key: 'outsourced',
    personaKey: 'is_outsourced',
    label: 'Terceirizado',
    badgeClass: 'bg-amber-50 text-amber-700 border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60',
  },
  {
    key: 'seller',
    personaKey: 'is_seller',
    label: 'Vendedor',
    badgeClass: 'bg-cyan-50 text-cyan-700 border-cyan-200/80 dark:bg-cyan-950/40 dark:text-cyan-300 dark:border-cyan-800/60',
  },
  {
    key: 'driver',
    personaKey: 'is_driver',
    label: 'Motorista',
    badgeClass: 'bg-indigo-50 text-indigo-700 border-indigo-200/80 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800/60',
  },
  {
    key: 'carrier',
    personaKey: 'is_carrier',
    label: 'Transportadora',
    badgeClass: 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200/80 dark:bg-fuchsia-950/40 dark:text-fuchsia-300 dark:border-fuchsia-800/60',
  },
  {
    key: 'requester',
    personaKey: 'is_requester',
    label: 'Solicitante',
    badgeClass: 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
  },
];

const getAllRoles = (person: any) => {
  return ROLE_CONFIGS.filter(cfg => {
    return !!(person.roles?.[cfg.key] || person.personas?.[cfg.personaKey]);
  });
};

const getVisibleRoles = (person: any) => {
  const all = getAllRoles(person);
  return all.slice(0, 2);
};

const getHiddenRoles = (person: any) => {
  const all = getAllRoles(person);
  return all.slice(2);
};

// Função de formatação dinâmica com máscara para CPF e CNPJ
const formatDocument = (doc: string | null | undefined, type: string) => {
  if (!doc) return '-';
  const clean = doc.replace(/\D/g, '');
  if (clean.length === 11) {
    return clean.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
  }
  if (clean.length === 14) {
    return clean.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
  }
  return doc;
};

// Formatação e sanitização de telefone
const sanitizePhone = (phone: string | null | undefined) => {
  return (phone || '').replace(/\D/g, '');
};

const formatPhone = (phone: string | null | undefined) => {
  if (!phone) return '-';
  const clean = sanitizePhone(phone);
  if (clean.length === 11) {
    return clean.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
  }
  if (clean.length === 10) {
    return clean.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
  }
  return phone;
};

// Cópia rápida com 1 clique e feedback visual
const copyToClipboard = async (text: string, key: string) => {
  if (!text) return;
  try {
    await navigator.clipboard.writeText(text);
    copiedKey.value = key;
    setTimeout(() => {
      if (copiedKey.value === key) {
        copiedKey.value = null;
      }
    }, 2000);
  } catch (err) {
    console.error('Erro ao copiar para a área de transferência', err);
  }
};

const personaFilters = [
  { label: 'Todos', value: '' },
  { label: 'Clientes', value: 'client' },
  { label: 'Fornecedores', value: 'supplier' },
  { label: 'Colaboradores', value: 'employee' },
  { label: 'Terceirizados', value: 'outsourced' },
  { label: 'Vendedores', value: 'seller' },
  { label: 'Motoristas', value: 'driver' },
  { label: 'Transportadoras', value: 'carrier' },
  { label: 'Solicitantes', value: 'requester' },
];

let searchTimeout: any = null;
const debounceSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchPeople();
  }, 300);
};

const selectPersona = (val: string) => {
  selectedPersona.value = val;
  currentPage.value = 1;
  fetchPeople();
};

const changePage = (page: number) => {
  currentPage.value = page;
  fetchPeople();
};

const fetchPeople = async () => {
  loading.value = true;
  try {
    const params: any = {
      page: currentPage.value,
      per_page: 15,
      sort_by: sortBy.value,
      sort_direction: sortDirection.value,
    };
    if (search.value) params.search = search.value;
    if (selectedPersona.value) params.persona = selectedPersona.value;
    if (filterStatus.value) params.status = filterStatus.value;
    if (filterPersonType.value) params.person_type = filterPersonType.value;
    if (filterStateId.value) params.state_id = filterStateId.value;
    if (filterCityId.value) params.city_id = filterCityId.value;

    const res = await axios.get('/api/v1/people', { params });
    people.value = res.data.data;
    currentPage.value = res.data.meta.current_page;
    lastPage.value = res.data.meta.last_page;
    totalRecords.value = res.data.meta.total;

    // Atualiza contadores numéricos de segmentação
    if (res.data.meta.counts) {
      counts.value = { ...counts.value, ...res.data.meta.counts };
    }
  } catch (err) {
    console.error('Erro ao buscar pessoas', err);
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  personEditing.value = null;
  isModalOpen.value = true;
};

const openEditModal = (person: any) => {
  personEditing.value = person;
  isModalOpen.value = true;
};

const toggleStatus = async (person: any) => {
  try {
    const res = await axios.patch(`/api/v1/people/${person.id}/toggle-status`);
    person.status = res.data.data.status;
  } catch (err) {
    console.error('Erro ao alterar status', err);
  }
};


// Controle do menu de reticências (Ação)
const activeDropdownPersonId = ref<number | null>(null);

const toggleActionsDropdown = (personId: number) => {
  activeDropdownPersonId.value = activeDropdownPersonId.value === personId ? null : personId;
};

const closeActionsDropdown = () => {
  activeDropdownPersonId.value = null;
};

const handleActionEdit = (person: any) => {
  closeActionsDropdown();
  openEditModal(person);
};

const handleActionToggleStatus = (person: any) => {
  closeActionsDropdown();
  toggleStatus(person);
};

const handleActionCopyId = (person: any) => {
  closeActionsDropdown();
  if (navigator.clipboard) {
    navigator.clipboard.writeText(String(person.id));
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleGlobalKeyDown);
  window.addEventListener('click', closeActionsDropdown);
  loadStates();
  fetchPeople();
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleGlobalKeyDown);
  window.removeEventListener('click', closeActionsDropdown);
});
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.2s ease-out forwards;
}
</style>
