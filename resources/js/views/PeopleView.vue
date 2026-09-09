<template>
  <div class="py-8 w-full space-y-6">
    <!-- Cabeçalho Confortável Integrado (Sem Top Bar) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold font-heading text-[#06064D] dark:text-white tracking-tight">
          Gestão de Pessoas
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 font-medium">
          Base canônica centralizada de funcionários, solicitantes, clientes, fornecedores e parceiros
        </p>
      </div>

      <!-- Ações do Cabeçalho -->
      <div class="flex items-center gap-2 shrink-0">
        <!-- Botão de Ação Primária Institucional (Laranja #FC6714) -->
        <button
          @click="openCreateModal"
          class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-sm font-semibold shadow-sm transition active:scale-98 cursor-pointer focus:ring-2 focus:ring-[#FC6714] focus:ring-offset-2 focus:outline-none"
        >
          <Plus class="w-4 h-4" />
          <span>Nova Pessoa</span>
        </button>

        <!-- Menu de Cadastros Base / Tabelas de Apoio (Três Pontos Verticais ⋮) -->
        <div class="relative" ref="headerMenuContainerRef">
          <button
            type="button"
            @click.stop="isHeaderMenuOpen = !isHeaderMenuOpen"
            class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#03032E] text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-white/5 transition shadow-2xs cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
            :class="{ 'bg-slate-100 dark:bg-white/10 text-slate-900 dark:text-white ring-2 ring-[#FC6714]/30': isHeaderMenuOpen }"
            title="Cadastros de Apoio e Tabelas Base"
            aria-label="Cadastros de Apoio e Tabelas Base"
            aria-haspopup="true"
            :aria-expanded="isHeaderMenuOpen"
          >
            <!-- Três pontos verticais (Reticências) nativos e nítidos -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="currentColor">
              <circle cx="12" cy="5" r="2"></circle>
              <circle cx="12" cy="12" r="2"></circle>
              <circle cx="12" cy="19" r="2"></circle>
            </svg>
          </button>

          <!-- Dropdown Flutuante de Cadastros Base -->
          <Transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
          >
            <div
              v-if="isHeaderMenuOpen"
              @click.stop
              class="absolute right-0 mt-2 w-56 rounded-xl bg-white dark:bg-[#06064D] border border-slate-200 dark:border-[#14147A] shadow-2xl z-50 py-1 text-xs font-medium divide-y divide-slate-100 dark:divide-[#14147A]/60 focus:outline-hidden"
            >
              <div class="px-3.5 py-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-400">
                <span>Cadastros de Apoio</span>
              </div>
              <div class="py-1">
                <button
                  type="button"
                  @click="openAuxModal('states')"
                  class="w-full text-left px-3.5 py-2.5 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] dark:hover:text-orange-300 transition cursor-pointer"
                >
                  <MapPin class="w-4 h-4 text-slate-400" />
                  <span>UF</span>
                </button>

                <button
                  type="button"
                  @click="openAuxModal('cities')"
                  class="w-full text-left px-3.5 py-2.5 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] dark:hover:text-orange-300 transition cursor-pointer"
                >
                  <Building2 class="w-4 h-4 text-slate-400" />
                  <span>Cidade</span>
                </button>

                <button
                  type="button"
                  @click="openAuxModal('neighborhoods')"
                  class="w-full text-left px-3.5 py-2.5 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] dark:hover:text-orange-300 transition cursor-pointer"
                >
                  <Home class="w-4 h-4 text-slate-400" />
                  <span>Bairro</span>
                </button>

                <button
                  type="button"
                  @click="openAuxModal('groups')"
                  class="w-full text-left px-3.5 py-2.5 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] dark:hover:text-orange-300 transition cursor-pointer"
                >
                  <FolderTree class="w-4 h-4 text-slate-400" />
                  <span>Grupos de Pessoas</span>
                </button>

                <button
                  type="button"
                  @click="openAuxModal('cnaes')"
                  class="w-full text-left px-3.5 py-2.5 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] dark:hover:text-orange-300 transition cursor-pointer"
                >
                  <FileText class="w-4 h-4 text-slate-400" />
                  <span>CNAEs</span>
                </button>
              </div>
            </div>
          </Transition>
        </div>

        <!-- Menu Hambúrguer (☰) — Abre Sidebar / Drawer do Módulo de Pessoas -->
        <button
          type="button"
          @click="isModuleDrawerOpen = true"
          class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#03032E] text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-white/5 transition shadow-2xs cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
          :class="{ 'bg-slate-100 dark:bg-white/10 text-slate-900 dark:text-white ring-2 ring-[#FC6714]/30': isModuleDrawerOpen }"
          title="Menu do Módulo de Pessoas (☰)"
          aria-label="Menu do Módulo de Pessoas"
        >
          <!-- Ícone Hambúrguer (Menu) -->
          <Menu class="w-5 h-5" />
        </button>
      </div>
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

        <!-- Botão de Filtros com Menu Dropdown para Escolha de Campos -->
        <div class="relative" ref="filtersDropdownContainerRef">
          <button
            type="button"
            @click.stop="isFiltersMenuOpen = !isFiltersMenuOpen"
            :class="activeFilterKeys.length > 0
              ? 'bg-[#FC6714]/10 text-[#FC6714] border-[#FC6714] font-semibold ring-2 ring-[#FC6714]/20'
              : 'bg-white dark:bg-[#03032E] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-[#14147A] hover:bg-slate-50 dark:hover:bg-white/5'"
            class="inline-flex items-center gap-2 h-11 px-4 rounded-lg border text-sm font-medium transition shadow-2xs cursor-pointer focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
            title="Escolher campos para filtrar a listagem"
            aria-haspopup="true"
            :aria-expanded="isFiltersMenuOpen"
          >
            <SlidersHorizontal class="w-4 h-4" :class="activeFilterKeys.length > 0 ? 'text-[#FC6714]' : 'text-slate-500 dark:text-slate-400'" />
            <span>Filtros</span>
            <span
              v-if="activeFilterKeys.length > 0"
              class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-[#FC6714] text-white"
            >
              {{ activeFilterKeys.length }}
            </span>
            <ChevronDown
              class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400"
              :class="{ 'rotate-180': isFiltersMenuOpen }"
            />
          </button>

          <!-- Menu Dropdown Flutuante de Seleção de Campos -->
          <Transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
          >
            <div
              v-if="isFiltersMenuOpen"
              @click.stop
              class="absolute right-0 mt-2 w-64 rounded-xl bg-white dark:bg-[#06064D] border border-slate-200 dark:border-[#14147A] shadow-2xl z-50 py-1.5 text-xs font-medium divide-y divide-slate-100 dark:divide-[#14147A]/60 focus:outline-hidden"
            >
              <div class="px-3.5 py-2 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <span>Adicionar Campo de Filtro</span>
              </div>
              <div class="py-1">
                <button
                  v-for="field in AVAILABLE_FILTER_FIELDS"
                  :key="field.key"
                  type="button"
                  @click="toggleFilterField(field.key)"
                  class="w-full text-left px-3.5 py-2.5 flex items-center justify-between hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] transition cursor-pointer"
                  :class="isFilterActive(field.key) ? 'text-[#FC6714] font-semibold bg-orange-50/50 dark:bg-[#FC6714]/10' : 'text-slate-700 dark:text-slate-200'"
                >
                  <div class="flex items-center gap-2.5">
                    <component :is="field.icon" class="w-4 h-4" :class="isFilterActive(field.key) ? 'text-[#FC6714]' : 'text-slate-400'" />
                    <span>{{ field.label }}</span>
                  </div>
                  <div class="flex items-center gap-1.5">
                    <span v-if="isFilterActive(field.key)" class="text-[10px] text-[#FC6714] font-semibold">Incluído</span>
                    <Check v-if="isFilterActive(field.key)" class="w-4 h-4 text-[#FC6714]" />
                  </div>
                </button>
              </div>

              <div v-if="activeFilterKeys.length > 0" class="p-1.5 bg-slate-50/50 dark:bg-white/5">
                <button
                  type="button"
                  @click="clearAllFilters(); isFiltersMenuOpen = false;"
                  class="w-full text-center px-3 py-1.5 rounded-lg text-xs font-semibold text-[#FC6714] hover:bg-[#FC6714]/10 transition cursor-pointer flex items-center justify-center gap-1.5"
                >
                  <RotateCcw class="w-3.5 h-3.5" />
                  <span>Limpar todos os filtros</span>
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </div>

      <!-- Barra Dinâmica de Filtros Selecionados -->
      <Transition
        enter-active-class="transition duration-150 ease-out"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-100 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div
          v-if="activeFilterKeys.length > 0"
          class="p-4 bg-slate-50 dark:bg-[#06064D]/30 rounded-xl border border-slate-200 dark:border-[#14147A] shadow-2xs transition-all space-y-3"
        >
          <!-- Barra Superior: Indicador de Filtros Ativos e Ações -->
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <SlidersHorizontal class="w-4 h-4 text-[#FC6714]" />
              <span class="text-xs font-semibold font-heading uppercase tracking-wider text-slate-800 dark:text-slate-200">
                Filtros Selecionados
              </span>
              <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-[#FC6714]/15 text-[#FC6714]">
                {{ activeFilterKeys.length }} campo{{ activeFilterKeys.length > 1 ? 's' : '' }} ativo{{ activeFilterKeys.length > 1 ? 's' : '' }}
              </span>
            </div>

            <div class="flex items-center gap-3">
              <!-- Botão Adicionar Mais Campos -->
              <button
                type="button"
                @click.stop="isFiltersMenuOpen = !isFiltersMenuOpen"
                class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-[#FC6714] dark:hover:text-[#FC6714] flex items-center gap-1 transition cursor-pointer"
              >
                <Plus class="w-3.5 h-3.5 text-[#FC6714]" />
                <span>Adicionar campo</span>
              </button>

              <!-- Botão Limpar Filtros -->
              <button
                type="button"
                @click="clearAllFilters"
                class="text-xs font-semibold text-[#FC6714] hover:text-[#E0530A] flex items-center gap-1 transition cursor-pointer"
                title="Limpar e remover todos os filtros"
              >
                <RotateCcw class="w-3.5 h-3.5" />
                <span>Limpar Filtros</span>
              </button>
            </div>
          </div>

          <!-- Grade de Controles dos Filtros Selecionados -->
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3.5 text-xs">
            <!-- 0. Filtro: Papel -->
            <div
              v-if="isFilterActive('role')"
              class="p-3 bg-white dark:bg-[#03032E] rounded-lg border border-slate-200 dark:border-[#14147A] shadow-2xs space-y-1.5 transition-all relative group"
            >
              <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                <span class="font-bold uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                  <UserCheck class="w-3.5 h-3.5 text-[#FC6714]" />
                  Papel
                </span>
                <button
                  type="button"
                  @click="removeFilter('role')"
                  class="p-0.5 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                  title="Remover filtro de Papel"
                >
                  <X class="w-3.5 h-3.5" />
                </button>
              </div>
              <select
                v-model="filterRole"
                @change="applySecondaryFilters"
                class="w-full h-9 px-2.5 rounded-md border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#06064D]/50 text-slate-900 dark:text-slate-100 font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer text-xs truncate"
              >
                <option value="">Todos os Papéis</option>
                <option v-for="r in roleOptions" :key="r.value" :value="r.value">
                  {{ r.label }} {{ counts[r.value] !== undefined ? `(${counts[r.value]})` : '' }}
                </option>
              </select>
            </div>

            <!-- 1. Filtro: Status -->
            <div
              v-if="isFilterActive('status')"
              class="p-3 bg-white dark:bg-[#03032E] rounded-lg border border-slate-200 dark:border-[#14147A] shadow-2xs space-y-1.5 transition-all relative group"
            >
              <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                <span class="font-bold uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                  <CheckCircle2 class="w-3.5 h-3.5 text-[#FC6714]" />
                  Status
                </span>
                <button
                  type="button"
                  @click="removeFilter('status')"
                  class="p-0.5 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                  title="Remover filtro de Status"
                >
                  <X class="w-3.5 h-3.5" />
                </button>
              </div>
              <select
                v-model="filterStatus"
                @change="applySecondaryFilters"
                class="w-full h-9 px-2.5 rounded-md border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#06064D]/50 text-slate-900 dark:text-slate-100 font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer text-xs"
              >
                <option value="">Todos os Status</option>
                <option value="active">● Apenas Ativos</option>
                <option value="inactive">○ Apenas Inativos</option>
              </select>
            </div>

            <!-- 2. Filtro: Tipo de Pessoa -->
            <div
              v-if="isFilterActive('person_type')"
              class="p-3 bg-white dark:bg-[#03032E] rounded-lg border border-slate-200 dark:border-[#14147A] shadow-2xs space-y-1.5 transition-all relative group"
            >
              <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                <span class="font-bold uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                  <Users class="w-3.5 h-3.5 text-[#FC6714]" />
                  Tipo de Pessoa
                </span>
                <button
                  type="button"
                  @click="removeFilter('person_type')"
                  class="p-0.5 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                  title="Remover filtro de Tipo de Pessoa"
                >
                  <X class="w-3.5 h-3.5" />
                </button>
              </div>
              <select
                v-model="filterPersonType"
                @change="applySecondaryFilters"
                class="w-full h-9 px-2.5 rounded-md border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#06064D]/50 text-slate-900 dark:text-slate-100 font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer text-xs"
              >
                <option value="">Todos os Tipos</option>
                <option value="individual">Pessoa Física (PF)</option>
                <option value="legal">Pessoa Jurídica (PJ)</option>
              </select>
            </div>

            <!-- 3. Filtro: Estado (UF) -->
            <div
              v-if="isFilterActive('state')"
              class="p-3 bg-white dark:bg-[#03032E] rounded-lg border border-slate-200 dark:border-[#14147A] shadow-2xs space-y-1.5 transition-all relative group"
            >
              <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                <span class="font-bold uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                  <MapPin class="w-3.5 h-3.5 text-[#FC6714]" />
                  Estado (UF)
                </span>
                <button
                  type="button"
                  @click="removeFilter('state')"
                  class="p-0.5 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                  title="Remover filtro de Estado"
                >
                  <X class="w-3.5 h-3.5" />
                </button>
              </div>
              <select
                v-model="filterStateId"
                @change="handleStateChange"
                class="w-full h-9 px-2.5 rounded-md border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#06064D]/50 text-slate-900 dark:text-slate-100 font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer text-xs truncate"
              >
                <option value="">Todos os Estados</option>
                <option v-for="st in statesList" :key="st.id" :value="st.id">
                  {{ st.code }} - {{ st.name }}
                </option>
              </select>
            </div>

            <!-- 4. Filtro: Município -->
            <div
              v-if="isFilterActive('city')"
              class="p-3 bg-white dark:bg-[#03032E] rounded-lg border border-slate-200 dark:border-[#14147A] shadow-2xs space-y-1.5 transition-all relative group"
            >
              <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                <span class="font-bold uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                  <Building2 class="w-3.5 h-3.5 text-[#FC6714]" />
                  Município
                </span>
                <button
                  type="button"
                  @click="removeFilter('city')"
                  class="p-0.5 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                  title="Remover filtro de Município"
                >
                  <X class="w-3.5 h-3.5" />
                </button>
              </div>
              <select
                v-if="filterStateId && citiesList.length > 0"
                v-model="filterCityId"
                @change="applySecondaryFilters"
                class="w-full h-9 px-2.5 rounded-md border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#06064D]/50 text-slate-900 dark:text-slate-100 font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer text-xs truncate"
              >
                <option value="">Todas as Cidades de {{ selectedStateCode }}</option>
                <option v-for="ct in citiesList" :key="ct.id" :value="ct.id">
                  {{ ct.name }}
                </option>
              </select>
              <div v-else>
                <CitySearchSelect
                  v-model="filterCityId"
                  :state-id="filterStateId"
                  placeholder="Buscar município..."
                  @update:modelValue="applySecondaryFilters"
                />
              </div>
            </div>

            <!-- 5. Filtro: Grupo -->
            <div
              v-if="isFilterActive('group')"
              class="p-3 bg-white dark:bg-[#03032E] rounded-lg border border-slate-200 dark:border-[#14147A] shadow-2xs space-y-1.5 transition-all relative group"
            >
              <div class="flex items-center justify-between text-slate-700 dark:text-slate-300">
                <span class="font-bold uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                  <FolderTree class="w-3.5 h-3.5 text-[#FC6714]" />
                  Grupo
                </span>
                <button
                  type="button"
                  @click="removeFilter('group')"
                  class="p-0.5 rounded text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 transition cursor-pointer"
                  title="Remover filtro de Grupo"
                >
                  <X class="w-3.5 h-3.5" />
                </button>
              </div>
              <select
                v-model="filterGroup"
                @change="applySecondaryFilters"
                class="w-full h-9 px-2.5 rounded-md border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#06064D]/50 text-slate-900 dark:text-slate-100 font-medium focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer text-xs truncate"
              >
                <option value="">Todos os Grupos</option>
                <option v-for="g in groupsList" :key="g.id" :value="g.id">
                  {{ g.name }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </Transition>
    </div>

    <!-- Tabela Confortável com Linhas Finas e Ordenação Clicável nos Cabeçalhos -->
    <div class="bg-white dark:bg-[#06064D]/50 rounded-xl border border-slate-200 dark:border-[#14147A] overflow-hidden shadow-xs transition-colors duration-200">
      <div class="overflow-x-auto min-h-[280px]">
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



              <!-- Coluna 8: Ações -->
              <th class="py-3.5 px-5 text-right">Ação</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
            <!-- Loading -->
            <tr v-if="loading">
              <td colspan="5" class="py-12 text-center text-slate-400 text-sm">
                <span class="inline-block animate-spin mr-2 text-[#FC6714]">⟳</span> Carregando registros...
              </td>
            </tr>

            <!-- Vazio -->
            <tr v-else-if="people.length === 0">
              <td colspan="5" class="py-12 text-center text-slate-400 text-sm">
                Nenhuma pessoa encontrada com os filtros selecionados.
              </td>
            </tr>

            <!-- Linha da Tabela -->
            <tr
              v-else
              v-for="(person, index) in people"
              :key="person.id"
              class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition-colors"
            >
              <!-- Nome (Clicável para abrir edição) -->
              <td class="py-4 px-5">
                <button
                  type="button"
                  @click="openEditModal(person)"
                  class="text-left group cursor-pointer focus:outline-none block"
                  title="Clique para editar este cadastro"
                >
                  <div class="font-semibold text-slate-900 dark:text-slate-100 text-sm group-hover:text-[#FC6714] dark:group-hover:text-[#FC6714] transition-colors">
                    {{ person.name }}
                  </div>
                  <div v-if="person.trade_name" class="text-xs text-slate-500 dark:text-slate-400 font-medium group-hover:text-[#FC6714]/80 transition-colors">
                    {{ person.trade_name }}
                  </div>
                </button>
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



              <!-- Ação (Menu de Reticências) -->
              <td class="py-4 px-5 text-right relative">
                <div class="inline-block text-left relative">
                  <button
                    type="button"
                    @click.stop="toggleActionsDropdown(person.id)"
                    class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:text-[#FC6714] hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 transition cursor-pointer focus:outline-hidden focus:ring-2 focus:ring-[#FC6714]"
                    :class="{ 'bg-orange-50 dark:bg-[#FC6714]/15 text-[#FC6714] ring-2 ring-[#FC6714]/40': activeDropdownPersonId === person.id }"
                    title="Mais opções"
                    aria-label="Mais opções"
                    aria-haspopup="true"
                    :aria-expanded="activeDropdownPersonId === person.id"
                  >
                    <!-- Três pontos verticais (Reticências) nativos e nítidos -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                      <circle cx="12" cy="5" r="2"></circle>
                      <circle cx="12" cy="12" r="2"></circle>
                      <circle cx="12" cy="19" r="2"></circle>
                    </svg>
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
                      @click.stop
                      class="absolute right-0 w-48 rounded-xl bg-white dark:bg-[#06064D] border border-slate-200 dark:border-[#14147A] shadow-2xl z-50 py-1 text-xs font-medium divide-y divide-slate-100 dark:divide-[#14147A]/60 focus:outline-hidden"
                      :class="index >= people.length - 2 && people.length > 2 ? 'bottom-full mb-1.5' : 'top-full mt-1.5'"
                    >
                      <div class="py-1">
                        <button
                          type="button"
                          @click="handleActionEdit(person)"
                          class="w-full text-left px-3.5 py-2 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] dark:hover:text-orange-300 transition cursor-pointer"
                        >
                          <Edit2 class="w-3.5 h-3.5 text-[#FC6714]" />
                          <span>Editar cadastro</span>
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

                      <div class="py-1 border-t border-slate-100 dark:border-[#14147A]/60">
                        <button
                          type="button"
                          @click="handleActionDelete(person)"
                          class="w-full text-left px-3.5 py-2 flex items-center gap-2.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition cursor-pointer font-medium"
                        >
                          <Trash2 class="w-3.5 h-3.5 text-red-500" />
                          <span>Excluir pessoa</span>
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

      <!-- Rodapé de Paginação Avançado da Grid (3 Blocos Ergonômicos) -->
      <TablePagination
        v-model:currentPage="currentPage"
        v-model:perPage="perPage"
        :lastPage="lastPage"
        :totalRecords="totalRecords"
        :fromRecord="fromRecord"
        :toRecord="toRecord"
        :loading="loading"
        @changePage="changePage"
        @changePerPage="handlePerPageChange"
      />
    </div>

    <!-- Modal de Cadastro/Edição de Pessoa -->
    <PersonModal
      :isOpen="isModalOpen"
      :personToEdit="personEditing"
      @close="isModalOpen = false"
      @saved="fetchPeople"
    />

    <!-- Modais de Apoio / Cadastros Base -->
    <StateCrudModal
      :isOpen="isStateModalOpen"
      @close="isStateModalOpen = false"
      @updated="handleAuxUpdated('states')"
    />

    <CityCrudModal
      :isOpen="isCityModalOpen"
      @close="isCityModalOpen = false"
      @updated="handleAuxUpdated('cities')"
    />

    <NeighborhoodCrudModal
      :isOpen="isNeighborhoodModalOpen"
      @close="isNeighborhoodModalOpen = false"
      @updated="handleAuxUpdated('neighborhoods')"
    />

    <PersonGroupCrudModal
      :isOpen="isGroupModalOpen"
      @close="isGroupModalOpen = false"
      @updated="handleAuxUpdated('groups')"
    />

    <CnaeCrudModal
      :isOpen="isCnaeModalOpen"
      @close="isCnaeModalOpen = false"
      @updated="handleAuxUpdated('cnaes')"
    />

    <!-- Sidebar / Drawer do Módulo de Pessoas (☰) -->
    <PeopleModuleDrawer
      v-model="isModuleDrawerOpen"
      :totalRecords="totalRecords"
      :activeRecords="activePeopleCount"
      :inactiveRecords="inactivePeopleCount"
      @close="isModuleDrawerOpen = false"
      @filterRole="handleDrawerFilterRole"
      @openAuxModal="openAuxModal"
      @openImport="showToast('Funcionalidade de importação em lote em fase de homologação.', 'success')"
      @resetFilters="clearAllFilters"
      @toast="showToast"
    />

    <!-- Toast de Notificação Temporário -->
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

    <!-- Modal de Confirmação de Exclusão de Pessoa -->
    <BaseModal
      v-model="isDeleteModalOpen"
      title="Confirmar Exclusão de Pessoa"
      size="sm"
      @close="closeDeleteModal"
    >
      <div class="space-y-4">
        <div class="p-4 rounded-xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/60 text-red-800 dark:text-red-200 text-xs leading-relaxed flex items-start gap-3">
          <AlertTriangle class="w-5 h-5 shrink-0 text-red-600 dark:text-red-400 mt-0.5" />
          <div class="space-y-2 flex-1">
            <p class="font-bold text-sm text-red-900 dark:text-red-100">
              Tem certeza que deseja excluir esta pessoa?
            </p>
            <div class="p-2.5 rounded-lg bg-white dark:bg-slate-900 border border-red-100 dark:border-red-900/40 text-slate-800 dark:text-slate-200">
              <div class="font-semibold text-sm text-slate-900 dark:text-white">{{ personToDelete?.name }}</div>
              <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex flex-wrap items-center gap-2">
                <span>ID: #{{ personToDelete?.id }}</span>
                <span v-if="personToDelete?.document">• Doc: {{ personToDelete.document }}</span>
                <span v-if="personToDelete?.person_type">
                  • {{ personToDelete.person_type === 'individual' ? 'Pessoa Física' : 'Pessoa Jurídica' }}
                </span>
              </div>
            </div>
            <p class="text-slate-600 dark:text-slate-400 text-xs">
              Esta ação removerá o cadastro permanentemente. A exclusão só será permitida se <strong>não houver vínculos em nenhuma outra parte do sistema</strong> (contratos, ordens de serviço, financeiro ou usuários).
            </p>
          </div>
        </div>

        <div v-if="deleteErrorMessage" class="p-3 rounded-lg bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-200 text-xs font-semibold flex items-center gap-2">
          <AlertCircle class="w-4 h-4 shrink-0 text-red-600 dark:text-red-400" />
          <span>{{ deleteErrorMessage }}</span>
        </div>
      </div>

      <template #footer>
        <div class="flex items-center justify-end gap-2.5 w-full">
          <button
            type="button"
            @click="closeDeleteModal"
            :disabled="isDeleting"
            class="h-9 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition text-xs font-semibold cursor-pointer disabled:opacity-50"
          >
            Cancelar
          </button>
          <button
            type="button"
            @click="executeDeletePerson"
            :disabled="isDeleting"
            class="h-9 px-4 rounded-lg bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-semibold text-xs shadow-xs transition flex items-center gap-2 cursor-pointer disabled:opacity-50"
          >
            <Loader2 v-if="isDeleting" class="w-3.5 h-3.5 animate-spin" />
            <Trash2 v-else class="w-3.5 h-3.5" />
            <span>{{ isDeleting ? 'Excluindo...' : 'Sim, Excluir Pessoa' }}</span>
          </button>
        </div>
      </template>
    </BaseModal>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import {
  Plus, Search, Edit2, Power, Copy, Check, Phone, Mail, X, MoreVertical, MoreHorizontal,
  SlidersHorizontal, ChevronDown, RotateCcw, ArrowUpDown, ArrowUp, ArrowDown,
  CheckCircle2, Users, MapPin, Building2, FolderTree, UserCheck, Home, FileText,
  Trash2, AlertTriangle, Loader2, AlertCircle, Menu
} from 'lucide-vue-next';
import axios from 'axios';
import BaseModal from '../components/common/BaseModal.vue';
import PersonModal from '../components/people/PersonModal.vue';
import PeopleModuleDrawer from '../components/people/PeopleModuleDrawer.vue';
import TablePagination from '../components/common/TablePagination.vue';
import CitySearchSelect from '../components/common/CitySearchSelect.vue';
import StateCrudModal from '../components/people/auxiliary/StateCrudModal.vue';
import CityCrudModal from '../components/people/auxiliary/CityCrudModal.vue';
import NeighborhoodCrudModal from '../components/people/auxiliary/NeighborhoodCrudModal.vue';
import PersonGroupCrudModal from '../components/people/auxiliary/PersonGroupCrudModal.vue';
import CnaeCrudModal from '../components/people/auxiliary/CnaeCrudModal.vue';

const people = ref<any[]>([]);
const loading = ref(false);
const search = ref('');
const currentPage = ref(1);
const lastPage = ref(1);
const totalRecords = ref(0);
const perPage = ref(25);
const fromRecord = ref(0);
const toRecord = ref(0);

const isModalOpen = ref(false);
const personEditing = ref<any | null>(null);

// Controle do Menu Geral / Drawer do Módulo de Pessoas
const isModuleDrawerOpen = ref(false);
const activePeopleCount = computed(() => people.value.filter(p => p.status === 'active').length);
const inactivePeopleCount = computed(() => people.value.filter(p => p.status === 'inactive').length);

// Controle de Exclusão de Pessoa
const isDeleteModalOpen = ref(false);
const personToDelete = ref<any | null>(null);
const isDeleting = ref(false);
const deleteErrorMessage = ref('');

// Feedback Toast
const toastMessage = ref('');
const toastType = ref<'success' | 'error'>('success');

const showToast = (msg: string, type: 'success' | 'error' = 'success') => {
  toastMessage.value = msg;
  toastType.value = type;
  setTimeout(() => {
    if (toastMessage.value === msg) {
      toastMessage.value = '';
    }
  }, 4500);
};

// Controle do Menu de Cadastros Base do Cabeçalho
const isHeaderMenuOpen = ref(false);
const headerMenuContainerRef = ref<HTMLElement | null>(null);

// Modais dos Cadastros Auxiliares
const isStateModalOpen = ref(false);
const isCityModalOpen = ref(false);
const isNeighborhoodModalOpen = ref(false);
const isGroupModalOpen = ref(false);
const isCnaeModalOpen = ref(false);

const openAuxModal = (type: 'states' | 'cities' | 'neighborhoods' | 'groups' | 'cnaes') => {
  isHeaderMenuOpen.value = false;
  if (type === 'states') isStateModalOpen.value = true;
  if (type === 'cities') isCityModalOpen.value = true;
  if (type === 'neighborhoods') isNeighborhoodModalOpen.value = true;
  if (type === 'groups') isGroupModalOpen.value = true;
  if (type === 'cnaes') isCnaeModalOpen.value = true;
};

const handleAuxUpdated = (type: string) => {
  if (type === 'states') {
    loadStates();
  }
  if (type === 'groups') {
    loadGroups();
  }
  if (activeFilterKeys.value.includes('city') || activeFilterKeys.value.includes('group') || activeFilterKeys.value.includes('state')) {
    fetchPeople();
  }
};

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

// Filtros Dinâmicos por Campo
const isFiltersMenuOpen = ref(false);
const filtersDropdownContainerRef = ref<HTMLElement | null>(null);

const AVAILABLE_FILTER_FIELDS = [
  { key: 'role', label: 'Papel', icon: UserCheck },
  { key: 'status', label: 'Status', icon: CheckCircle2 },
  { key: 'person_type', label: 'Tipo de Pessoa', icon: Users },
  { key: 'state', label: 'Estado (UF)', icon: MapPin },
  { key: 'city', label: 'Município', icon: Building2 },
  { key: 'group', label: 'Grupo', icon: FolderTree },
];

const roleOptions = [
  { label: 'Funcionário', value: 'employee' },
  { label: 'Solicitante', value: 'requester' },
  { label: 'Cliente', value: 'client' },
  { label: 'Fornecedor', value: 'supplier' },
  { label: 'Vendedor', value: 'seller' },
  { label: 'Terceirizado', value: 'outsourced' },
  { label: 'Motorista', value: 'driver' },
  { label: 'Transportadora', value: 'carrier' },
];

const activeFilterKeys = ref<string[]>([]);
const filterRole = ref('');
const filterStatus = ref('');
const filterPersonType = ref('');
const filterStateId = ref('');
const filterCityId = ref<number | null | ''>('');
const filterGroup = ref('');

const statesList = ref<any[]>([]);
const citiesList = ref<any[]>([]);
const groupsList = ref<any[]>([]);

const selectedStateCode = computed(() => {
  const st = statesList.value.find(s => s.id === Number(filterStateId.value));
  return st ? st.code : '';
});

const isFilterActive = (key: string) => {
  return activeFilterKeys.value.includes(key);
};

const toggleFilterField = (key: string) => {
  if (activeFilterKeys.value.includes(key)) {
    removeFilter(key);
  } else {
    activeFilterKeys.value.push(key);
    if (key === 'state' || key === 'city') {
      if (statesList.value.length === 0) {
        loadStates();
      }
    }
    if (key === 'group') {
      if (groupsList.value.length === 0) {
        loadGroups();
      }
    }
    isFiltersMenuOpen.value = false;
  }
};

const removeFilter = (key: string) => {
  activeFilterKeys.value = activeFilterKeys.value.filter(k => k !== key);
  if (key === 'role') filterRole.value = '';
  if (key === 'status') filterStatus.value = '';
  if (key === 'person_type') filterPersonType.value = '';
  if (key === 'state') {
    filterStateId.value = '';
    citiesList.value = [];
  }
  if (key === 'city') filterCityId.value = '';
  if (key === 'group') filterGroup.value = '';
  currentPage.value = 1;
  fetchPeople();
};

const clearAllFilters = () => {
  activeFilterKeys.value = [];
  filterRole.value = '';
  filterStatus.value = '';
  filterPersonType.value = '';
  filterStateId.value = '';
  filterCityId.value = '';
  filterGroup.value = '';
  citiesList.value = [];
  currentPage.value = 1;
  fetchPeople();
};

const handleDrawerFilterRole = (role: string) => {
  if (!activeFilterKeys.value.includes('role')) {
    activeFilterKeys.value.push('role');
  }
  filterRole.value = role;
  currentPage.value = 1;
  fetchPeople();
};

const loadStates = async () => {
  try {
    const res = await axios.get('/api/v1/states');
    statesList.value = res.data.data || [];
  } catch (err) {
    console.error('Erro ao carregar estados', err);
  }
};

const loadGroups = async () => {
  try {
    const res = await axios.get('/api/v1/person-groups');
    groupsList.value = res.data.data || [];
  } catch (err) {
    console.error('Erro ao carregar grupos', err);
  }
};

const handleStateChange = async () => {
  filterCityId.value = '';
  citiesList.value = [];
  if (filterStateId.value) {
    try {
      const res = await axios.get('/api/v1/cities', {
        params: { state_id: filterStateId.value, all: 1 }
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
    label: 'Funcionário',
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
    badgeClass: 'bg-orange-50 text-[#FC6714] border-orange-200/80 dark:bg-[#FC6714]/10 dark:text-orange-300 dark:border-[#FC6714]/30',
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

let searchTimeout: any = null;
const debounceSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchPeople();
  }, 300);
};

const changePage = (page: number) => {
  currentPage.value = page;
  fetchPeople();
};

const handlePerPageChange = (newPerPage: number) => {
  perPage.value = newPerPage;
  currentPage.value = 1;
  fetchPeople();
};

const fetchPeople = async () => {
  loading.value = true;
  try {
    const params: any = {
      page: currentPage.value,
      per_page: perPage.value,
      sort_by: sortBy.value,
      sort_direction: sortDirection.value,
    };
    if (search.value) params.search = search.value;
    if (activeFilterKeys.value.includes('role') && filterRole.value) {
      params.role = filterRole.value;
      params.persona = filterRole.value;
    }
    if (activeFilterKeys.value.includes('status') && filterStatus.value) params.status = filterStatus.value;
    if (activeFilterKeys.value.includes('person_type') && filterPersonType.value) params.person_type = filterPersonType.value;
    if (activeFilterKeys.value.includes('state') && filterStateId.value) params.state_id = filterStateId.value;
    if (activeFilterKeys.value.includes('city') && filterCityId.value) params.city_id = filterCityId.value;
    if (activeFilterKeys.value.includes('group') && filterGroup.value) {
      params.group = filterGroup.value;
      params.group_id = filterGroup.value;
    }

    const res = await axios.get('/api/v1/people', { params });
    people.value = res.data.data;
    currentPage.value = res.data.meta.current_page;
    lastPage.value = res.data.meta.last_page;
    totalRecords.value = res.data.meta.total;
    fromRecord.value = res.data.meta.from ?? (totalRecords.value > 0 ? (currentPage.value - 1) * perPage.value + 1 : 0);
    toRecord.value = res.data.meta.to ?? Math.min(currentPage.value * perPage.value, totalRecords.value);

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

const closeFiltersMenu = (event?: MouseEvent) => {
  if (event && filtersDropdownContainerRef.value && filtersDropdownContainerRef.value.contains(event.target as Node)) {
    return;
  }
  isFiltersMenuOpen.value = false;
};

const closeHeaderMenu = (event?: MouseEvent) => {
  if (event && headerMenuContainerRef.value && headerMenuContainerRef.value.contains(event.target as Node)) {
    return;
  }
  isHeaderMenuOpen.value = false;
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

const handleActionDelete = (person: any) => {
  closeActionsDropdown();
  personToDelete.value = person;
  deleteErrorMessage.value = '';
  isDeleteModalOpen.value = true;
};

const closeDeleteModal = () => {
  if (isDeleting.value) return;
  isDeleteModalOpen.value = false;
  personToDelete.value = null;
  deleteErrorMessage.value = '';
};

const executeDeletePerson = async () => {
  if (!personToDelete.value) return;
  isDeleting.value = true;
  deleteErrorMessage.value = '';

  try {
    const res = await axios.delete(`/api/v1/people/${personToDelete.value.id}`);
    showToast(res.data?.message || 'Pessoa excluída com sucesso!', 'success');
    isDeleteModalOpen.value = false;
    personToDelete.value = null;
    await fetchPeople();
  } catch (err: any) {
    deleteErrorMessage.value = err.response?.data?.message || 'Não foi possível excluir a pessoa.';
  } finally {
    isDeleting.value = false;
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleGlobalKeyDown);
  window.addEventListener('click', closeActionsDropdown);
  window.addEventListener('click', closeFiltersMenu);
  window.addEventListener('click', closeHeaderMenu);
  loadStates();
  loadGroups();
  fetchPeople();
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleGlobalKeyDown);
  window.removeEventListener('click', closeActionsDropdown);
  window.removeEventListener('click', closeFiltersMenu);
  window.removeEventListener('click', closeHeaderMenu);
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
