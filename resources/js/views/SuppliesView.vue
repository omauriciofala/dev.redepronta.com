<template>
  <div class="py-8 w-full space-y-6">
    <!-- ============================================================================= -->
    <!-- CABEÇALHO PADRÃO DO MÓDULO (DESIGN SYSTEM CANÔNICO) -->
    <!-- ============================================================================= -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="p-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 text-blue-600 dark:text-blue-400 shadow-2xs shrink-0">
          <Package class="w-6 h-6" />
        </div>
        <div>
          <h1 class="text-2xl font-semibold font-heading text-[#06064D] dark:text-white tracking-tight">
            Suprimentos & WMS
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
            Gestão de depósitos físicos, posições regionais consolidadas e rastreabilidade serial
          </p>
        </div>
      </div>

      <!-- Ações do Cabeçalho -->
      <div class="flex items-center gap-2 shrink-0">
        <!-- Botão Primário Laranja (#FC6714) -->
        <button
          type="button"
          @click="openMovementModal()"
          class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-sm font-semibold shadow-sm transition active:scale-98 cursor-pointer focus:ring-2 focus:ring-[#FC6714] focus:ring-offset-2 focus:outline-none"
        >
          <ArrowUpDown class="w-4 h-4" />
          <span>Nova Movimentação</span>
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
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="currentColor">
              <circle cx="12" cy="5" r="2"></circle>
              <circle cx="12" cy="12" r="2"></circle>
              <circle cx="12" cy="19" r="2"></circle>
            </svg>
          </button>

          <!-- Dropdown Flutuante de Cadastros de Apoio -->
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
                  @click="openClusterCrudModal"
                  class="w-full text-left px-3.5 py-2.5 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] dark:hover:text-orange-300 transition cursor-pointer"
                >
                  <MapPin class="w-4 h-4 text-slate-400" />
                  <span>Posições Regionais</span>
                </button>

                <button
                  type="button"
                  @click="openDepotTypeCrudModal"
                  class="w-full text-left px-3.5 py-2.5 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] dark:hover:text-orange-300 transition cursor-pointer"
                >
                  <Tags class="w-4 h-4 text-slate-400" />
                  <span>Tipos de Depósito</span>
                </button>

                <button
                  type="button"
                  @click="openCreateDepotModal"
                  class="w-full text-left px-3.5 py-2.5 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] dark:hover:text-orange-300 transition cursor-pointer"
                >
                  <Warehouse class="w-4 h-4 text-slate-400" />
                  <span>Depósitos</span>
                </button>

                <button
                  type="button"
                  @click="openMaterialCategoryCrudModal"
                  class="w-full text-left px-3.5 py-2.5 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] dark:hover:text-orange-300 transition cursor-pointer"
                >
                  <FolderTree class="w-4 h-4 text-slate-400" />
                  <span>Categorias de Material</span>
                </button>

                <button
                  type="button"
                  @click="openUnitCrudModal"
                  class="w-full text-left px-3.5 py-2.5 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] dark:hover:text-orange-300 transition cursor-pointer"
                >
                  <Scale class="w-4 h-4 text-slate-400" />
                  <span>Unidades de Medida</span>
                </button>

                <button
                  type="button"
                  @click="openMaterialOwnerCrudModal"
                  class="w-full text-left px-3.5 py-2.5 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] dark:hover:text-orange-300 transition cursor-pointer"
                >
                  <UserCheck class="w-4 h-4 text-slate-400" />
                  <span>Proprietários</span>
                </button>

                <button
                  type="button"
                  @click="openCreateMaterialModal"
                  class="w-full text-left px-3.5 py-2.5 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] dark:hover:text-orange-300 transition cursor-pointer"
                >
                  <Boxes class="w-4 h-4 text-slate-400" />
                  <span>Materiais</span>
                </button>

                <button
                  type="button"
                  @click="openMaterialImportModal"
                  class="w-full text-left px-3.5 py-2.5 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] dark:hover:text-orange-300 transition cursor-pointer"
                >
                  <FileSpreadsheet class="w-4 h-4 text-slate-400" />
                  <span>Importar Materiais</span>
                </button>
              </div>
            </div>
          </Transition>
        </div>

        <!-- Menu Hambúrguer (☰) — Abre Drawer Lateral do Módulo -->
        <button
          type="button"
          @click="isModuleDrawerOpen = true"
          class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#03032E] text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-white/5 transition shadow-2xs cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
          :class="{ 'bg-slate-100 dark:bg-white/10 text-slate-900 dark:text-white ring-2 ring-[#FC6714]/30': isModuleDrawerOpen }"
          title="Suprimentos & WMS (☰)"
          aria-label="Suprimentos & WMS"
        >
          <Menu class="w-5 h-5" />
        </button>

        <!-- Botão de Recarregar Dados -->
        <button
          type="button"
          @click="refreshCurrentTab()"
          :disabled="loading"
          class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#03032E] text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-white/5 transition shadow-2xs cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
          title="Recarregar dados da tela"
        >
          <RefreshCw class="w-4 h-4 text-slate-500 dark:text-slate-400" :class="{ 'animate-spin text-[#FC6714]': loading }" />
        </button>
      </div>
    </div>

    <!-- ============================================================================= -->
    <!-- BARRA DE ABAS DO MÓDULO (DESIGN SYSTEM) -->
    <!-- ============================================================================= -->
    <div class="flex border-b border-slate-200 dark:border-[#14147A] gap-6 overflow-x-auto select-none">
      <button
        type="button"
        @click="activeTab = 'regional'"
        :class="activeTab === 'regional'
          ? 'border-[#FC6714] text-[#FC6714] font-bold'
          : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
        class="pb-3 border-b-2 text-sm flex items-center gap-2 transition cursor-pointer shrink-0"
      >
        <Layers class="w-4 h-4" />
        <span>Posição Regional</span>
        <span
          v-if="regionalMaterials.length > 0"
          class="px-2 py-0.5 rounded-full text-[11px] font-bold"
          :class="activeTab === 'regional' ? 'bg-[#FC6714]/15 text-[#FC6714]' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'"
        >
          {{ regionalMaterials.length }}
        </span>
      </button>

      <button
        type="button"
        @click="activeTab = 'materials'"
        :class="activeTab === 'materials'
          ? 'border-[#FC6714] text-[#FC6714] font-bold'
          : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
        class="pb-3 border-b-2 text-sm flex items-center gap-2 transition cursor-pointer shrink-0"
      >
        <Boxes class="w-4 h-4" />
        <span>Materiais</span>
        <span
          v-if="materials.length > 0"
          class="px-2 py-0.5 rounded-full text-[11px] font-bold"
          :class="activeTab === 'materials' ? 'bg-[#FC6714]/15 text-[#FC6714]' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'"
        >
          {{ materials.length }}
        </span>
      </button>

      <button
        type="button"
        @click="activeTab = 'depots'"
        :class="activeTab === 'depots'
          ? 'border-[#FC6714] text-[#FC6714] font-bold'
          : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
        class="pb-3 border-b-2 text-sm flex items-center gap-2 transition cursor-pointer shrink-0"
      >
        <Warehouse class="w-4 h-4" />
        <span>Depósitos</span>
        <span
          v-if="depots.length > 0"
          class="px-2 py-0.5 rounded-full text-[11px] font-bold"
          :class="activeTab === 'depots' ? 'bg-[#FC6714]/15 text-[#FC6714]' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'"
        >
          {{ depots.length }}
        </span>
      </button>

      <button
        type="button"
        @click="activeTab = 'serials'"
        :class="activeTab === 'serials'
          ? 'border-[#FC6714] text-[#FC6714] font-bold'
          : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
        class="pb-3 border-b-2 text-sm flex items-center gap-2 transition cursor-pointer shrink-0"
      >
        <QrCode class="w-4 h-4" />
        <span>Serializado</span>
        <span
          v-if="serialsList.length > 0"
          class="px-2 py-0.5 rounded-full text-[11px] font-bold"
          :class="activeTab === 'serials' ? 'bg-[#FC6714]/15 text-[#FC6714]' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'"
        >
          {{ serialsList.length }}
        </span>
      </button>
    </div>

    <!-- ============================================================================= -->
    <!-- ABA 1: POSIÇÃO REGIONAL -->
    <!-- ============================================================================= -->
    <div v-if="activeTab === 'regional'" class="space-y-6">
      <!-- Seletor Confortável de Posição Regional -->
      <div class="p-4 bg-white dark:bg-[#06064D]/50 rounded-xl border border-slate-200 dark:border-[#14147A] flex flex-wrap items-center justify-between gap-4 text-sm shadow-2xs">
        <div class="flex items-center gap-3">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
            Posição Regional:
          </span>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="c in clusters"
              :key="c.id"
              type="button"
              @click="selectCluster(c.id)"
              :class="selectedClusterId === c.id
                ? 'bg-[#FC6714] text-white font-bold shadow-xs'
                : 'bg-slate-50 dark:bg-[#03032E] text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 border border-slate-200 dark:border-[#14147A]'"
              class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition cursor-pointer flex items-center gap-2"
            >
              <span class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: c.color || '#FC6714' }"></span>
              <span>{{ c.name }}</span>
              <span
                v-if="c.owner"
                class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold tracking-wide"
                :class="selectedClusterId === c.id ? 'bg-white/25 text-white' : 'bg-slate-200 dark:bg-white/10 text-slate-600 dark:text-slate-300'"
                :title="`Proprietário: ${c.owner.code} - ${c.owner.name}`"
              >
                {{ c.owner.code }}
              </span>
            </button>
          </div>
        </div>

        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
          <span class="inline-flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Consolidação dos saldos dos depósitos da região em tempo real
          </span>
        </div>
      </div>

      <!-- 4 Cards Métricos de Estoque da Posição Regional -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-xl border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#06064D]/50 shadow-xs flex items-center gap-3.5">
          <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 shrink-0">
            <Boxes class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Tipos de Materiais</p>
            <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-0.5">
              {{ currentClusterSummary.total_materials || 0 }}
            </h3>
          </div>
        </div>

        <div class="p-5 rounded-xl border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#06064D]/50 shadow-xs flex items-center gap-3.5">
          <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 shrink-0">
            <Warehouse class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Saldo Físico Consolidado</p>
            <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-0.5">
              {{ formatNumber(currentClusterSummary.total_items_count || 0) }}
            </h3>
          </div>
        </div>

        <div class="p-5 rounded-xl border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#06064D]/50 shadow-xs flex items-center gap-3.5">
          <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 shrink-0">
            <QrCode class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Itens Serializados</p>
            <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-0.5">
              {{ currentClusterSummary.total_serials_in_stock || 0 }}
            </h3>
          </div>
        </div>

        <div class="p-5 rounded-xl border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#06064D]/50 shadow-xs flex items-center gap-3.5">
          <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 shrink-0">
            <Warehouse class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Depósitos na Região</p>
            <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-0.5">
              {{ currentClusterSummary.depots_count || 0 }} depósitos
            </h3>
          </div>
        </div>
      </div>

      <!-- Barra de Filtro e Busca Rápida na Posição Regional -->
      <div class="p-4 bg-white dark:bg-[#06064D]/50 rounded-xl border border-slate-200 dark:border-[#14147A] flex flex-wrap items-center justify-between gap-4 text-sm shadow-2xs">
        <div class="relative flex-1 min-w-[280px]">
          <Search class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400 pointer-events-none" />
          <input
            type="text"
            v-model="regionalSearch"
            placeholder="Filtrar por nome do material ou código SKU..."
            class="w-full h-11 pl-10 pr-10 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 text-sm transition"
          />
          <button
            v-if="regionalSearch"
            @click="regionalSearch = ''"
            class="absolute right-3 top-3 p-1 rounded text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition cursor-pointer"
            title="Limpar filtro"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>

        <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
          Exibindo {{ paginatedRegionalMaterials.length }} de {{ filteredRegionalMaterials.length }} materiais
        </div>
      </div>

      <!-- TABELA CONFORTÁVEL 1: POSIÇÃO REGIONAL (SEÇÃO 7 DESIGN SYSTEM) -->
      <div class="rounded-xl border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#06064D]/50 shadow-xs overflow-hidden transition-colors duration-200">
        <div class="overflow-x-auto min-h-[280px]">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="border-b border-slate-200 dark:border-[#14147A] bg-slate-50/80 dark:bg-[#03032E]/70 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider select-none">
                <!-- Material / Código -->
                <th
                  @click="toggleSortRegional('name')"
                  class="py-3.5 px-5 cursor-pointer hover:text-[#FC6714] dark:hover:text-[#FC6714] transition"
                  title="Clique para ordenar por Material"
                >
                  <div class="inline-flex items-center gap-1.5">
                    <span>Material / Código</span>
                    <ArrowUp v-if="regionalSortBy === 'name' && regionalSortDir === 'asc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowDown v-else-if="regionalSortBy === 'name' && regionalSortDir === 'desc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowUpDown v-else class="w-3.5 h-3.5 text-slate-400 opacity-60" />
                  </div>
                </th>

                <!-- Unidade -->
                <th class="py-3.5 px-5">Unidade</th>

                <!-- Custo Médio Unitário -->
                <th
                  @click="toggleSortRegional('unit_cost')"
                  class="py-3.5 px-5 text-right cursor-pointer hover:text-[#FC6714] dark:hover:text-[#FC6714] transition"
                  title="Ordenar por Custo Médio"
                >
                  <div class="inline-flex items-center justify-end gap-1.5 w-full">
                    <span>Custo Médio</span>
                    <ArrowUp v-if="regionalSortBy === 'unit_cost' && regionalSortDir === 'asc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowDown v-else-if="regionalSortBy === 'unit_cost' && regionalSortDir === 'desc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowUpDown v-else class="w-3.5 h-3.5 text-slate-400 opacity-60" />
                  </div>
                </th>

                <!-- Saldo na Região -->
                <th
                  @click="toggleSortRegional('total_virtual_quantity')"
                  class="py-3.5 px-5 text-right cursor-pointer hover:text-[#FC6714] dark:hover:text-[#FC6714] transition"
                  title="Ordenar por Saldo na Região"
                >
                  <div class="inline-flex items-center justify-end gap-1.5 w-full">
                    <span>Saldo na Região</span>
                    <ArrowUp v-if="regionalSortBy === 'total_virtual_quantity' && regionalSortDir === 'asc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowDown v-else-if="regionalSortBy === 'total_virtual_quantity' && regionalSortDir === 'desc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowUpDown v-else class="w-3.5 h-3.5 text-slate-400 opacity-60" />
                  </div>
                </th>

                <!-- Ações da Linha -->
                <th class="py-3.5 px-5 text-right">Ação</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <tr v-if="filteredRegionalMaterials.length === 0">
                <td colspan="5" class="py-12 text-center text-slate-400 text-sm">
                  <div class="max-w-sm mx-auto space-y-2">
                    <Boxes class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto" />
                    <p class="font-medium text-slate-700 dark:text-slate-300">Nenhum material encontrado nesta posição regional</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                      Faça uma transferência de estoque para os depósitos desta região ou selecione outra posição regional.
                    </p>
                  </div>
                </td>
              </tr>

              <template v-else v-for="m in paginatedRegionalMaterials" :key="m.material_id">
                <!-- Linha Principal do Material -->
                <tr class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition-colors">
                  <!-- Identificação do Material -->
                  <td class="py-4 px-5">
                    <div class="flex items-center gap-3">
                      <!-- Botão expansível se houver detalhamento de depósitos -->
                      <button
                        v-if="m.breakdown && m.breakdown.length > 0"
                        type="button"
                        @click="toggleExpand(m.material_id)"
                        class="p-1 rounded-md text-slate-400 hover:text-[#FC6714] hover:bg-orange-50 dark:hover:bg-[#FC6714]/10 transition cursor-pointer"
                        :title="expandedRows.includes(m.material_id) ? 'Recolher detalhes por depósito' : 'Ver distribuição por depósito'"
                      >
                        <ChevronDown
                          class="w-4 h-4 transition-transform duration-200"
                          :class="{ 'rotate-180 text-[#FC6714]': expandedRows.includes(m.material_id) }"
                        />
                      </button>
                      <div v-else class="w-6 shrink-0"></div>

                      <div>
                        <div class="font-semibold text-slate-900 dark:text-slate-100 text-sm">
                          {{ m.name }}
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                          <span class="font-mono text-xs text-slate-500 dark:text-slate-400">
                            SKU: {{ m.code }}
                          </span>
                          <button
                            @click="copyToClipboard(m.code, 'sku-reg-' + m.material_id)"
                            class="p-0.5 rounded text-slate-400 hover:text-[#FC6714] transition cursor-pointer"
                            title="Copiar código SKU"
                          >
                            <Check v-if="copiedKey === 'sku-reg-' + m.material_id" class="w-3 h-3 text-emerald-500" />
                            <Copy v-else class="w-3 h-3" />
                          </button>
                          <span
                            v-if="m.has_serial"
                            class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-purple-50 text-purple-700 border border-purple-200/70 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/60"
                          >
                            Serializado
                          </span>
                          <span
                            v-else-if="m.track_batch"
                            class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/70 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/60"
                          >
                            Lote / Metragem
                          </span>
                        </div>
                      </div>
                    </div>
                  </td>

                  <!-- Unidade de Medida -->
                  <td class="py-4 px-5 text-slate-600 dark:text-slate-300 font-mono text-sm">
                    {{ m.unit || 'UND' }}
                  </td>

                  <!-- Custo Médio -->
                  <td class="py-4 px-5 text-right font-mono text-sm text-slate-700 dark:text-slate-300">
                    R$ {{ formatNumber(m.unit_cost) }}
                  </td>

                  <!-- Saldo na Região (Destacado) -->
                  <td class="py-4 px-5 text-right">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-[#FC6714]/10 text-[#FC6714] border border-[#FC6714]/30">
                      {{ formatNumber(m.total_virtual_quantity) }} {{ m.unit || 'UND' }}
                    </span>
                  </td>

                  <!-- Ação de Movimentar -->
                  <td class="py-4 px-5 text-right">
                    <button
                      type="button"
                      @click="openMovementModal(m)"
                      class="h-8 px-3 text-xs font-medium rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-orange-50 hover:text-[#FC6714] hover:border-[#FC6714] dark:hover:bg-[#FC6714]/10 dark:hover:text-[#FC6714] transition cursor-pointer inline-flex items-center gap-1.5"
                    >
                      <ArrowUpDown class="w-3.5 h-3.5" />
                      <span>Movimentar</span>
                    </button>
                  </td>
                </tr>

                <!-- Linha Sub-Tabela Confortável: Distribuição por Depósitos da Região -->
                <tr v-if="expandedRows.includes(m.material_id)" class="bg-slate-50/60 dark:bg-[#03032E]/40">
                  <td colspan="6" class="p-4 pl-14">
                    <div class="rounded-xl border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#06064D]/80 shadow-xs overflow-hidden">
                      <div class="px-4 py-2.5 bg-slate-100/70 dark:bg-[#03032E] border-b border-slate-200 dark:border-[#14147A] flex items-center justify-between text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                        <span class="flex items-center gap-1.5">
                          <Warehouse class="w-3.5 h-3.5 text-blue-500" />
                          Distribuição por Depósito na Posição Regional
                        </span>
                        <span class="text-slate-400 font-medium lowercase">
                          {{ m.breakdown.length }} depósito(s) com este item
                        </span>
                      </div>
                      <table class="w-full text-left text-xs border-collapse">
                        <thead>
                          <tr class="border-b border-slate-200 dark:border-[#14147A] bg-slate-50/50 dark:bg-[#03032E]/30 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">
                            <th class="py-2.5 px-4">Depósito</th>
                            <th class="py-2.5 px-4">Tipo</th>
                            <th class="py-2.5 px-4">Responsável</th>
                            <th class="py-2.5 px-4 text-right">Saldo no Depósito</th>
                            <th class="py-2.5 px-4 text-right">Ação</th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
                          <tr
                            v-for="(bk, bIdx) in m.breakdown"
                            :key="bIdx"
                            class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition"
                          >
                            <td class="py-3 px-4 font-semibold text-slate-800 dark:text-slate-200">
                              {{ bk.depot_name }}
                            </td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-300">
                              {{ formatDepotType(bk.depot_type) }}
                            </td>
                            <td class="py-3 px-4 text-slate-700 dark:text-slate-300">
                              {{ bk.responsible_name || '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-blue-600 dark:text-blue-400">
                              {{ formatNumber(bk.quantity) }} {{ m.unit || 'UND' }}
                            </td>
                            <td class="py-3 px-4 text-right">
                              <button
                                type="button"
                                @click="openTransferFromBreakdown(bk, m)"
                                class="h-7 px-2.5 text-[11px] font-medium rounded-md border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#03032E] text-slate-700 dark:text-slate-200 hover:bg-orange-50 hover:text-[#FC6714] transition cursor-pointer"
                              >
                                Movimentar
                              </button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>

        <!-- Rodapé de Paginação Confortável -->
        <TablePagination
          v-model:currentPage="regionalCurrentPage"
          v-model:perPage="regionalPerPage"
          :lastPage="regionalLastPage"
          :totalRecords="filteredRegionalMaterials.length"
          :fromRecord="regionalFromRecord"
          :toRecord="regionalToRecord"
          :loading="loading"
          @changePage="(p) => regionalCurrentPage = p"
          @changePerPage="(pp) => { regionalPerPage = pp; regionalCurrentPage = 1; }"
        />
      </div>
    </div>

    <!-- ============================================================================= -->
    <!-- ABA 2: CATÁLOGO DE MATERIAIS -->
    <!-- ============================================================================= -->
    <div v-if="activeTab === 'materials'" class="space-y-4">
      <!-- Barra de Filtros e Busca de Materiais -->
      <div class="p-4 bg-white dark:bg-[#06064D]/50 rounded-xl border border-slate-200 dark:border-[#14147A] flex flex-wrap items-center justify-between gap-4 text-sm shadow-2xs">
        <div class="relative flex-1 min-w-[280px]">
          <Search class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400 pointer-events-none" />
          <input
            type="text"
            v-model="materialSearch"
            placeholder="Buscar por nome do item, código SKU ou categoria..."
            class="w-full h-11 pl-10 pr-10 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 text-sm transition"
          />
          <button
            v-if="materialSearch"
            @click="materialSearch = ''"
            class="absolute right-3 top-3 p-1 rounded text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition cursor-pointer"
            title="Limpar busca"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
          <!-- Filtro por Proprietário / Solicitante (Catálogo De/Para) -->
          <select
            v-model="materialFilterOwner"
            class="h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-xs font-semibold focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer"
            title="Filtrar por Proprietário para visualizar códigos e nomes do catálogo dele"
          >
            <option value="">Todos os Proprietários (SKU Canônico)</option>
            <option v-for="o in materialOwnersList" :key="o.id" :value="o.id">
              {{ o.code }} — {{ o.name }}
            </option>
          </select>

          <!-- Filtro por Categoria -->
          <select
            v-model="materialFilterCategory"
            class="h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-xs font-semibold focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer"
          >
            <option value="all">Todas as Categorias</option>
            <option v-for="c in materialCategoriesList" :key="c.id" :value="c.name">
              {{ c.name }}
            </option>
          </select>

          <!-- Filtro de Tipo de Rastreio -->
          <select
            v-model="materialFilterType"
            class="h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-xs font-semibold focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer"
          >
            <option value="all">Todos os Tipos de Rastreio</option>
            <option value="serialized">Serializado</option>
            <option value="batch">Lote / Metragem</option>
            <option value="bulk">A Granel</option>
          </select>

          <span class="text-xs text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">
            {{ filteredMaterials.length }} materiais
          </span>
        </div>
      </div>

      <!-- Banner Informativo quando o filtro de proprietário estiver ativo -->
      <div
        v-if="selectedMaterialOwnerInfo"
        class="p-4 rounded-xl border border-blue-200 dark:border-blue-800/60 bg-blue-50/60 dark:bg-blue-950/30 flex items-center justify-between gap-4 text-xs"
      >
        <div class="flex items-center gap-2.5">
          <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300 shrink-0">
            <UserCheck class="w-4 h-4" />
          </div>
          <div>
            <span class="font-bold text-slate-900 dark:text-slate-100 block text-xs">
              Catálogo do Proprietário Ativo: {{ selectedMaterialOwnerInfo.code }} — {{ selectedMaterialOwnerInfo.name }}
            </span>
            <span class="text-slate-600 dark:text-slate-300 text-[11px]">
              Os códigos e nomes exibidos abaixo correspondem à nomenclatura utilizada por este proprietário. Materiais mapeados trazem o SKU do sistema como referência cruzada.
            </span>
          </div>
        </div>
        <button
          type="button"
          @click="materialFilterOwner = ''"
          class="px-2.5 py-1 rounded-md border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition cursor-pointer shrink-0 font-medium text-[11px]"
        >
          Voltar para SKU Canônico
        </button>
      </div>

      <!-- TABELA CONFORTÁVEL 2: CATÁLOGO DE MATERIAIS (SEÇÃO 7 DESIGN SYSTEM) -->
      <div class="rounded-xl border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#06064D]/50 shadow-xs overflow-hidden transition-colors duration-200">
        <div class="overflow-x-auto min-h-[280px]">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="border-b border-slate-200 dark:border-[#14147A] bg-slate-50/80 dark:bg-[#03032E]/70 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider select-none">
                <!-- Código SKU -->
                <th
                  @click="toggleSortMaterial('code')"
                  class="py-3.5 px-5 cursor-pointer hover:text-[#FC6714] dark:hover:text-[#FC6714] transition"
                  title="Ordenar por Código"
                >
                  <div class="inline-flex items-center gap-1.5">
                    <span>Código</span>
                    <ArrowUp v-if="materialSortBy === 'code' && materialSortDir === 'asc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowDown v-else-if="materialSortBy === 'code' && materialSortDir === 'desc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowUpDown v-else class="w-3.5 h-3.5 text-slate-400 opacity-60" />
                  </div>
                </th>

                <!-- Nome / Descrição -->
                <th
                  @click="toggleSortMaterial('name')"
                  class="py-3.5 px-5 cursor-pointer hover:text-[#FC6714] dark:hover:text-[#FC6714] transition"
                  title="Ordenar por Nome"
                >
                  <div class="inline-flex items-center gap-1.5">
                    <span>{{ selectedMaterialOwnerInfo ? 'Nome no Proprietário' : 'Nome do Material' }}</span>
                    <ArrowUp v-if="materialSortBy === 'name' && materialSortDir === 'asc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowDown v-else-if="materialSortBy === 'name' && materialSortDir === 'desc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowUpDown v-else class="w-3.5 h-3.5 text-slate-400 opacity-60" />
                  </div>
                </th>

                <!-- Categoria -->
                <th class="py-3.5 px-5">Categoria</th>

                <!-- Unidade -->
                <th class="py-3.5 px-5">Unidade</th>

                <!-- Rastreabilidade -->
                <th class="py-3.5 px-5 text-center">Rastreabilidade</th>

                <!-- Ações -->
                <th class="py-3.5 px-5 text-right">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <tr v-if="filteredMaterials.length === 0">
                <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                  <div class="max-w-sm mx-auto space-y-2">
                    <Boxes class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto" />
                    <p class="font-medium text-slate-700 dark:text-slate-300">Nenhum material encontrado</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                      Tente outro termo de busca ou cadastre um novo material no botão acima.
                    </p>
                  </div>
                </td>
              </tr>

              <tr
                v-else
                v-for="(m, index) in paginatedMaterials"
                :key="m.id"
                class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition-colors"
              >
                <!-- Código SKU / Proprietário -->
                <td class="py-4 px-5">
                  <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="font-mono font-bold text-xs text-[#FC6714]">{{ m.code }}</span>
                    <button
                      @click="copyToClipboard(m.code, 'sku-cat-' + m.id)"
                      class="p-0.5 rounded text-slate-400 hover:text-[#FC6714] transition cursor-pointer"
                      title="Copiar código"
                    >
                      <Check v-if="copiedKey === 'sku-cat-' + m.id" class="w-3 h-3 text-emerald-500" />
                      <Copy v-else class="w-3 h-3" />
                    </button>
                  </div>
                  <div v-if="m.has_owner_alias && m.system_code" class="text-[10px] font-mono text-slate-400 mt-0.5">
                    SKU: {{ m.system_code }}
                  </div>
                </td>

                <!-- Nome / Descrição -->
                <td class="py-4 px-5">
                  <div class="font-semibold text-slate-900 dark:text-slate-100 text-sm">
                    {{ m.name }}
                  </div>
                  <div v-if="m.has_owner_alias && m.system_name && m.system_name !== m.name" class="text-[11px] text-slate-400 mt-0.5">
                    Nome no sistema: {{ m.system_name }}
                  </div>
                  <div v-else-if="m.description" class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-xs mt-0.5">
                    {{ m.description }}
                  </div>
                </td>

                <!-- Categoria -->
                <td class="py-4 px-5 text-slate-600 dark:text-slate-300">
                  <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
                    {{ m.category || 'Geral' }}
                  </span>
                </td>

                <!-- Unidade -->
                <td class="py-4 px-5 text-slate-700 dark:text-slate-300 font-mono text-sm">
                  {{ m.unit?.code || 'UND' }}
                </td>

                <!-- Rastreabilidade (3 Tipos Canônicos) -->
                <td class="py-4 px-5 text-center">
                  <!-- 1. Serializado -->
                  <span
                    v-if="m.tracking_type === 'SERIAL' || m.has_serial"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/60"
                  >
                    <QrCode class="w-3.5 h-3.5" />
                    <span>Serializado</span>
                  </span>

                  <!-- 2. Lote / Metragem -->
                  <span
                    v-else-if="m.tracking_type === 'BATCH' || m.track_batch"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/60"
                  >
                    <Layers class="w-3.5 h-3.5" />
                    <span>Lote / Metragem</span>
                  </span>

                  <!-- 3. A Granel -->
                  <span
                    v-else
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700"
                  >
                    <Box class="w-3.5 h-3.5" />
                    <span>A Granel</span>
                  </span>
                </td>

                <!-- Ações (Menu de Reticências Verticais) -->
                <td class="py-4 px-5 text-right relative">
                  <div class="inline-block text-left relative">
                    <button
                      type="button"
                      @click.stop="toggleMaterialActionsDropdown(m.id)"
                      class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:text-[#FC6714] hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 transition cursor-pointer focus:outline-hidden focus:ring-2 focus:ring-[#FC6714]"
                      :class="{ 'bg-orange-50 dark:bg-[#FC6714]/15 text-[#FC6714] ring-2 ring-[#FC6714]/40': activeDropdownMaterialId === m.id }"
                      title="Mais opções do material"
                      aria-label="Mais opções do material"
                      aria-haspopup="true"
                      :aria-expanded="activeDropdownMaterialId === m.id"
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
                        v-if="activeDropdownMaterialId === m.id"
                        @click.stop
                        class="absolute right-0 w-48 rounded-xl bg-white dark:bg-[#06064D] border border-slate-200 dark:border-[#14147A] shadow-2xl z-50 py-1 text-xs font-medium divide-y divide-slate-100 dark:divide-[#14147A]/60 focus:outline-hidden"
                        :class="index >= paginatedMaterials.length - 2 && paginatedMaterials.length > 2 ? 'bottom-full mb-1.5' : 'top-full mt-1.5'"
                      >
                        <div class="py-1">
                          <button
                            type="button"
                            @click="handleEditMaterial(m)"
                            class="w-full text-left px-3.5 py-2.5 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] dark:hover:text-orange-300 transition cursor-pointer"
                          >
                            <Edit2 class="w-4 h-4 text-slate-400" />
                            <span>Editar Material</span>
                          </button>

                          <button
                            type="button"
                            @click="copySkuAndClose(m.code, 'sku-cat-' + m.id)"
                            class="w-full text-left px-3.5 py-2.5 flex items-center gap-2.5 text-slate-700 dark:text-slate-200 hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 hover:text-[#FC6714] dark:hover:text-orange-300 transition cursor-pointer"
                          >
                            <Copy class="w-4 h-4 text-slate-400" />
                            <span>Copiar Código SKU</span>
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

        <!-- Rodapé de Paginação Confortável -->
        <TablePagination
          v-model:currentPage="materialCurrentPage"
          v-model:perPage="materialPerPage"
          :lastPage="materialLastPage"
          :totalRecords="filteredMaterials.length"
          :fromRecord="materialFromRecord"
          :toRecord="materialToRecord"
          :loading="loading"
          @changePage="(p) => materialCurrentPage = p"
          @changePerPage="(pp) => { materialPerPage = pp; materialCurrentPage = 1; }"
        />
      </div>
    </div>

    <!-- ============================================================================= -->
    <!-- ABA 3: DEPÓSITOS -->
    <!-- ============================================================================= -->
    <div v-if="activeTab === 'depots'" class="space-y-4">
      <!-- Barra de Filtros e Busca de Depósitos -->
      <div class="p-4 bg-white dark:bg-[#06064D]/50 rounded-xl border border-slate-200 dark:border-[#14147A] flex flex-wrap items-center justify-between gap-4 text-sm shadow-2xs">
        <div class="relative flex-1 min-w-[280px]">
          <Search class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400 pointer-events-none" />
          <input
            type="text"
            v-model="depotSearch"
            placeholder="Buscar por nome ou código do depósito, ou responsável..."
            class="w-full h-11 pl-10 pr-10 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 text-sm transition"
          />
          <button
            v-if="depotSearch"
            @click="depotSearch = ''"
            class="absolute right-3 top-3 p-1 rounded text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition cursor-pointer"
            title="Limpar busca"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>

        <div class="flex items-center gap-3">
          <!-- Filtro de Tipo de Depósito Dinâmico -->
          <select
            v-model="depotFilterType"
            class="h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-xs font-semibold focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer"
          >
            <option value="all">Todos os Tipos de Depósito</option>
            <option v-for="t in depotTypesList" :key="t.code" :value="t.code">
              {{ t.name }}
            </option>
          </select>

          <span class="text-xs text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">
            {{ filteredDepots.length }} depósitos
          </span>

          <!-- Botão Novo Depósito -->
          <button
            type="button"
            @click="openCreateDepotModal"
            class="h-11 px-4 text-xs font-semibold rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white shadow-sm hover:shadow transition cursor-pointer flex items-center gap-2 whitespace-nowrap shrink-0"
          >
            <Plus class="w-4 h-4" />
            <span>Novo Depósito</span>
          </button>
        </div>
      </div>

      <!-- TABELA CONFORTÁVEL 3: DEPÓSITOS (SEÇÃO 7 DESIGN SYSTEM) -->
      <div class="rounded-xl border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#06064D]/50 shadow-xs overflow-hidden transition-colors duration-200">
        <div class="overflow-x-auto min-h-[280px]">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="border-b border-slate-200 dark:border-[#14147A] bg-slate-50/80 dark:bg-[#03032E]/70 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider select-none">
                <!-- Nome / Identificação -->
                <th
                  @click="toggleSortDepot('name')"
                  class="py-3.5 px-5 cursor-pointer hover:text-[#FC6714] dark:hover:text-[#FC6714] transition"
                  title="Ordenar por Nome"
                >
                  <div class="inline-flex items-center gap-1.5">
                    <span>Identificação do Depósito</span>
                    <ArrowUp v-if="depotSortBy === 'name' && depotSortDir === 'asc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowDown v-else-if="depotSortBy === 'name' && depotSortDir === 'desc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowUpDown v-else class="w-3.5 h-3.5 text-slate-400 opacity-60" />
                  </div>
                </th>

                <!-- Tipo -->
                <th class="py-3.5 px-5">Tipo de Depósito</th>

                <!-- Posição Regional -->
                <th class="py-3.5 px-5">Posição Regional</th>

                <!-- Proprietário -->
                <th class="py-3.5 px-5">Proprietário</th>

                <!-- Responsável -->
                <th class="py-3.5 px-5">Responsável</th>

                <!-- Ações -->
                <th class="py-3.5 px-5 text-right">Ação</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <tr v-if="filteredDepots.length === 0">
                <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                  <div class="max-w-sm mx-auto space-y-2">
                    <Warehouse class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto" />
                    <p class="font-medium text-slate-700 dark:text-slate-300">Nenhum depósito encontrado</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Cadastre depósitos ou verifique os filtros selecionados.</p>
                    <button
                      type="button"
                      @click="openCreateDepotModal"
                      class="mt-3 inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-[#FC6714] text-white hover:bg-[#E0530A] transition cursor-pointer"
                    >
                      <Plus class="w-3.5 h-3.5" />
                      <span>Cadastrar Depósito</span>
                    </button>
                  </div>
                </td>
              </tr>

              <tr
                v-else
                v-for="d in paginatedDepots"
                :key="d.id"
                class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition-colors"
              >
                <!-- Identificação / Nome -->
                <td class="py-4 px-5">
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600">
                      <Warehouse class="w-5 h-5" />
                    </div>
                    <div>
                      <div class="font-semibold text-slate-900 dark:text-slate-100 text-sm">
                        {{ d.name }}
                      </div>
                      <div class="flex items-center gap-2 mt-0.5">
                        <span class="font-mono text-xs text-[#FC6714] font-medium">{{ d.code }}</span>
                        <span v-if="d.city" class="text-[11px] text-slate-400 dark:text-slate-500">
                          • {{ d.city.name }} - {{ d.city.state?.code || '' }}
                        </span>
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Tipo com Badge Confortável -->
                <td class="py-4 px-5">
                  <span
                    class="px-2.5 py-1 rounded-md text-xs font-semibold border"
                    :class="{
                      'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/60': d.type === 'CENTRAL',
                      'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60': d.type === 'REGIONAL_BASE',
                      'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60': d.type === 'LAB_REPAIR',
                      'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/60': d.type !== 'CENTRAL' && d.type !== 'REGIONAL_BASE' && d.type !== 'LAB_REPAIR'
                    }"
                  >
                    {{ formatDepotType(d.type) }}
                  </span>
                </td>

                <!-- Posição Regional Vinculada -->
                <td class="py-4 px-5 text-slate-700 dark:text-slate-300 font-medium text-sm">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 text-xs">
                    {{ d.cluster?.name || '-' }}
                  </span>
                </td>

                <!-- Proprietário Vinculado / Efetivo -->
                <td class="py-4 px-5 text-sm">
                  <div
                    v-if="d.owner || d.cluster?.owner"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/60"
                  >
                    <UserCheck class="w-3.5 h-3.5 shrink-0" />
                    <span>{{ (d.owner || d.cluster?.owner)?.code }}</span>
                    <span v-if="!d.owner && d.cluster?.owner" class="text-[10px] text-blue-500 dark:text-blue-400 font-normal">(Região)</span>
                  </div>
                  <span v-else class="text-xs text-slate-400 dark:text-slate-500 italic">Sem proprietário</span>
                </td>

                <!-- Responsável -->
                <td class="py-4 px-5 text-slate-700 dark:text-slate-300 font-medium text-sm">
                  {{ d.responsible_person?.name || '-' }}
                </td>

                <!-- Ações -->
                <td class="py-4 px-5 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      type="button"
                      @click="openEditDepotModal(d)"
                      class="h-8 px-2.5 text-xs font-medium rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-400 dark:hover:bg-blue-950/40 dark:hover:text-blue-300 transition cursor-pointer inline-flex items-center gap-1.5"
                      title="Editar Depósito"
                    >
                      <Edit2 class="w-3.5 h-3.5" />
                      <span>Editar</span>
                    </button>

                    <button
                      type="button"
                      @click="openTransferFromDepot(d)"
                      class="h-8 px-3 text-xs font-medium rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-orange-50 hover:text-[#FC6714] hover:border-[#FC6714] dark:hover:bg-[#FC6714]/10 dark:hover:text-[#FC6714] transition cursor-pointer inline-flex items-center gap-1.5"
                    >
                      <ArrowRightLeft class="w-3.5 h-3.5" />
                      <span>Transferir</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Rodapé de Paginação Confortável -->
        <TablePagination
          v-model:currentPage="depotCurrentPage"
          v-model:perPage="depotPerPage"
          :lastPage="depotLastPage"
          :totalRecords="filteredDepots.length"
          :fromRecord="depotFromRecord"
          :toRecord="depotToRecord"
          :loading="loading"
          @changePage="(p) => depotCurrentPage = p"
          @changePerPage="(pp) => { depotPerPage = pp; depotCurrentPage = 1; }"
        />
      </div>
    </div>

    <!-- ============================================================================= -->
    <!-- ABA 4: SERIALIZADO -->
    <!-- ============================================================================= -->
    <div v-if="activeTab === 'serials'" class="space-y-4">
      <!-- Barra de Filtros e Busca de Seriais -->
      <div class="p-4 bg-white dark:bg-[#06064D]/50 rounded-xl border border-slate-200 dark:border-[#14147A] flex flex-wrap items-center justify-between gap-4 text-sm shadow-2xs">
        <div class="relative flex-1 min-w-[280px]">
          <Search class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400 pointer-events-none" />
          <input
            type="text"
            v-model="serialSearch"
            @input="debounceLoadSerials"
            placeholder="Buscar por número de série ou endereço MAC..."
            class="w-full h-11 pl-10 pr-10 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 text-sm transition"
          />
          <button
            v-if="serialSearch"
            @click="clearSerialSearch"
            class="absolute right-3 top-3 p-1 rounded text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition cursor-pointer"
            title="Limpar busca"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>

        <div class="flex items-center gap-3">
          <!-- Filtro de Status do Serial -->
          <select
            v-model="serialStatusFilter"
            @change="loadSerials"
            class="h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-xs font-semibold focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer"
          >
            <option value="">Todos os Status</option>
            <option value="IN_STOCK">Em Estoque (Disponível)</option>
            <option value="IN_TRANSIT">Em Trânsito</option>
            <option value="INSTALLED_CLIENT">Instalado no Cliente</option>
            <option value="DEFECTIVE">Com Defeito / Reparo</option>
            <option value="RESERVED">Reservado para OS</option>
          </select>

          <span class="text-xs text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">
            {{ serialsList.length }} seriais
          </span>
        </div>
      </div>

      <!-- TABELA CONFORTÁVEL 4: SERIALIZADO (SEÇÃO 7 DESIGN SYSTEM) -->
      <div class="rounded-xl border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#06064D]/50 shadow-xs overflow-hidden transition-colors duration-200">
        <div class="overflow-x-auto min-h-[280px]">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="border-b border-slate-200 dark:border-[#14147A] bg-slate-50/80 dark:bg-[#03032E]/70 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider select-none">
                <!-- Número de Série -->
                <th
                  @click="toggleSortSerial('serial_number')"
                  class="py-3.5 px-5 cursor-pointer hover:text-[#FC6714] dark:hover:text-[#FC6714] transition"
                  title="Ordenar por Serial"
                >
                  <div class="inline-flex items-center gap-1.5">
                    <span>Número de Série</span>
                    <ArrowUp v-if="serialSortBy === 'serial_number' && serialSortDir === 'asc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowDown v-else-if="serialSortBy === 'serial_number' && serialSortDir === 'desc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowUpDown v-else class="w-3.5 h-3.5 text-slate-400 opacity-60" />
                  </div>
                </th>

                <!-- MAC Address -->
                <th class="py-3.5 px-5">MAC Address</th>

                <!-- Modelo / Equipamento -->
                <th
                  @click="toggleSortSerial('material_name')"
                  class="py-3.5 px-5 cursor-pointer hover:text-[#FC6714] dark:hover:text-[#FC6714] transition"
                  title="Ordenar por Equipamento"
                >
                  <div class="inline-flex items-center gap-1.5">
                    <span>Modelo do Equipamento</span>
                    <ArrowUp v-if="serialSortBy === 'material_name' && serialSortDir === 'asc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowDown v-else-if="serialSortBy === 'material_name' && serialSortDir === 'desc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowUpDown v-else class="w-3.5 h-3.5 text-slate-400 opacity-60" />
                  </div>
                </th>

                <!-- Depósito Atual -->
                <th class="py-3.5 px-5">Depósito Atual</th>

                <!-- Responsável -->
                <th class="py-3.5 px-5">Responsável</th>

                <!-- Status -->
                <th class="py-3.5 px-5 text-center">Status Operacional</th>

                <!-- Ações -->
                <th class="py-3.5 px-5 text-right">Ação</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <tr v-if="filteredSerials.length === 0">
                <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                  <div class="max-w-sm mx-auto space-y-2">
                    <QrCode class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto" />
                    <p class="font-medium text-slate-700 dark:text-slate-300">Nenhum equipamento serializado encontrado</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                      Não há seriais correspondentes aos critérios de busca.
                    </p>
                  </div>
                </td>
              </tr>

              <tr
                v-else
                v-for="s in paginatedSerials"
                :key="s.id"
                class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition-colors"
              >
                <!-- Serial Number -->
                <td class="py-4 px-5">
                  <div class="flex items-center gap-2">
                    <span class="font-mono font-bold text-sm text-[#FC6714] select-all">
                      {{ s.serial_number }}
                    </span>
                    <button
                      @click="copyToClipboard(s.serial_number, 'sn-' + s.id)"
                      class="p-0.5 rounded text-slate-400 hover:text-[#FC6714] transition cursor-pointer"
                      title="Copiar Serial"
                    >
                      <Check v-if="copiedKey === 'sn-' + s.id" class="w-3.5 h-3.5 text-emerald-500" />
                      <Copy v-else class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </td>

                <!-- MAC Address -->
                <td class="py-4 px-5 font-mono text-xs text-slate-600 dark:text-slate-300">
                  <div v-if="s.mac_address" class="flex items-center gap-1.5">
                    <span>{{ s.mac_address }}</span>
                    <button
                      @click="copyToClipboard(s.mac_address, 'mac-' + s.id)"
                      class="p-0.5 rounded text-slate-400 hover:text-[#FC6714] transition cursor-pointer"
                      title="Copiar MAC Address"
                    >
                      <Check v-if="copiedKey === 'mac-' + s.id" class="w-3 h-3 text-emerald-500" />
                      <Copy v-else class="w-3 h-3" />
                    </button>
                  </div>
                  <span v-else class="text-slate-400 italic">-</span>
                </td>

                <!-- Equipamento -->
                <td class="py-4 px-5">
                  <div class="font-semibold text-slate-900 dark:text-slate-100 text-sm">
                    {{ s.material?.name || '-' }}
                  </div>
                  <div class="font-mono text-xs text-slate-400 mt-0.5">
                    SKU: {{ s.material?.code }}
                  </div>
                </td>

                <!-- Depósito Atual -->
                <td class="py-4 px-5">
                  <div class="flex items-center gap-2">
                    <Warehouse class="w-4 h-4 text-emerald-500 shrink-0" />
                    <span class="font-medium text-slate-800 dark:text-slate-200 text-sm">
                      {{ s.current_depot?.name || '-' }}
                    </span>
                  </div>
                </td>

                <!-- Responsável -->
                <td class="py-4 px-5 text-slate-700 dark:text-slate-300 font-medium text-sm">
                  {{ s.current_depot?.responsible_person?.name || '-' }}
                </td>

                <!-- Status com Badge Confortável -->
                <td class="py-4 px-5 text-center">
                  <span
                    class="inline-block px-2.5 py-1 rounded-md text-xs font-semibold border"
                    :class="formatSerialStatusBadge(s.status)"
                  >
                    {{ formatSerialStatusLabel(s.status) }}
                  </span>
                </td>

                <!-- Ações -->
                <td class="py-4 px-5 text-right">
                  <button
                    type="button"
                    @click="openTransferFromSerial(s)"
                    class="h-8 px-2.5 text-xs font-medium rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-orange-50 hover:text-[#FC6714] hover:border-[#FC6714] transition cursor-pointer inline-flex items-center gap-1"
                    title="Transferir este equipamento"
                  >
                    <ArrowRightLeft class="w-3.5 h-3.5" />
                    <span>Movimentar</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Rodapé de Paginação Confortável -->
        <TablePagination
          v-model:currentPage="serialCurrentPage"
          v-model:perPage="serialPerPage"
          :lastPage="serialLastPage"
          :totalRecords="filteredSerials.length"
          :fromRecord="serialFromRecord"
          :toRecord="serialToRecord"
          :loading="loading"
          @changePage="(p) => serialCurrentPage = p"
          @changePerPage="(pp) => { serialPerPage = pp; serialCurrentPage = 1; }"
        />
      </div>
    </div>

    <!-- ============================================================================= -->
    <!-- COMPONENTES MODAIS INTEGRADOS (BASE MODAL CANÔNICO) -->
    <!-- ============================================================================= -->

    <!-- Modal 1: Movimentação de Estoque (Entrada, Saída, Devolução, Transferência) -->
    <MovementModal
      :is-open="isMovementModalOpen"
      :depots="depots"
      :materials="materials"
      :initial-material="selectedMaterialForMovement"
      :initial-depot="selectedDepotForMovement"
      :initial-type="selectedMovementType"
      @close="isMovementModalOpen = false"
      @saved="onMovementSuccess"
      @transferred="onMovementSuccess"
    />

    <!-- Modal 2: Cadastro / Edição de Material de Estoque -->
    <MaterialModal
      :is-open="isMaterialModalOpen"
      :material-data="selectedMaterialForEdit"
      :units-list="unitsList"
      :categories-list="materialCategoriesList"
      @close="isMaterialModalOpen = false"
      @saved="onMaterialSaved"
    />

    <!-- Modal 3: Gestão Completa de Posições Regionais -->
    <ClusterCrudModal
      :is-open="isClusterModalOpen"
      @close="isClusterModalOpen = false"
      @updated="onClustersUpdated"
    />

    <!-- Modal 3.1: Gestão Completa de Tipos de Depósito -->
    <DepotTypeCrudModal
      :is-open="isDepotTypeModalOpen"
      @close="isDepotTypeModalOpen = false"
      @updated="onDepotTypesUpdated"
    />

    <!-- Modal 3.2: Gestão Completa de Categorias de Material -->
    <MaterialCategoryCrudModal
      :is-open="isMaterialCategoryModalOpen"
      @close="isMaterialCategoryModalOpen = false"
      @updated="onMaterialCategoriesUpdated"
    />

    <!-- Modal 3.3: Gestão Completa de Unidades de Medida -->
    <UnitCrudModal
      :is-open="isUnitModalOpen"
      @close="isUnitModalOpen = false"
      @updated="onUnitsUpdated"
    />

    <!-- Modal 3.4: Gestão Completa de Proprietários -->
    <MaterialOwnerCrudModal
      :is-open="isMaterialOwnerModalOpen"
      @close="isMaterialOwnerModalOpen = false"
      @updated="onMaterialOwnersUpdated"
    />

    <!-- Modal 4: Cadastro / Edição de Depósito -->
    <DepotModal
      :is-open="isDepotModalOpen"
      :depot-data="selectedDepotForEdit"
      :clusters-list="clusters"
      @close="isDepotModalOpen = false"
      @saved="onDepotSaved"
    />

    <!-- Modal 5: Importação de Materiais via Planilha -->
    <MaterialImportModal
      :is-open="isMaterialImportModalOpen"
      @close="isMaterialImportModalOpen = false"
      @imported="onMaterialsImported"
    />

    <!-- Drawer Lateral do Módulo (☰) -->
    <SuppliesModuleDrawer
      v-model="isModuleDrawerOpen"
      :total-materials="materials.length"
      :total-base-items="Number(currentClusterSummary.total_items_count) || 0"
      :total-serials-in-stock="Number(currentClusterSummary.total_serials_in_stock) || 0"
      :depots-count="Number(currentClusterSummary.depots_count) || 0"
      @navigate="(tab) => activeTab = tab"
      @action="handleDrawerAction"
    />

    <!-- ============================================================================= -->
    <!-- TOAST DE FEEDBACK TEMPORÁRIO (DESIGN SYSTEM) -->
    <!-- ============================================================================= -->
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
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl border text-sm font-medium"
        :class="toastType === 'success'
          ? 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-950 dark:border-emerald-800 dark:text-emerald-200'
          : 'bg-rose-50 border-rose-200 text-rose-800 dark:bg-rose-950 dark:border-rose-800 dark:text-rose-200'"
      >
        <CheckCircle2 v-if="toastType === 'success'" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" />
        <AlertCircle v-else class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" />
        <span>{{ toastMessage }}</span>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import {
  Package, ArrowRightLeft, Plus, Layers, Boxes, Box, Warehouse, QrCode, RefreshCw,
  ChevronDown, X, Search, ArrowUp, ArrowDown, ArrowUpDown, Copy, Check,
  AlertCircle, CheckCircle2, Menu, MapPin, Edit2, Tags, FolderTree, Scale, UserCheck,
  Upload, FileSpreadsheet
} from 'lucide-vue-next';
import TablePagination from '@/components/common/TablePagination.vue';
import MovementModal from '@/components/supplies/MovementModal.vue';
import MaterialModal from '@/components/supplies/MaterialModal.vue';
import MaterialImportModal from '@/components/supplies/MaterialImportModal.vue';
import ClusterCrudModal from '@/components/supplies/auxiliary/ClusterCrudModal.vue';
import DepotTypeCrudModal from '@/components/supplies/auxiliary/DepotTypeCrudModal.vue';
import MaterialCategoryCrudModal from '@/components/supplies/auxiliary/MaterialCategoryCrudModal.vue';
import UnitCrudModal from '@/components/supplies/auxiliary/UnitCrudModal.vue';
import MaterialOwnerCrudModal from '@/components/supplies/auxiliary/MaterialOwnerCrudModal.vue';
import DepotModal from '@/components/supplies/DepotModal.vue';
import SuppliesModuleDrawer from '@/components/supplies/SuppliesModuleDrawer.vue';

// ─── INTERFACES DE DOMÍNIO ───────────────────────────────────────────────────
interface Cluster {
  id: number;
  name: string;
  code: string;
  color?: string;
  description?: string;
  owner_id?: number;
  owner?: {
    id: number;
    code: string;
    name: string;
    person?: {
      id: number;
      name: string;
    };
  };
}

interface Depot {
  id: number;
  name: string;
  code: string;
  type: string;
  cluster_id?: number;
  cluster?: { id: number; name: string };
  responsible_person_id?: number;
  responsible_person?: { id: number; name: string };
  city_id?: number;
  city?: { id: number; name: string; ibge_code?: string; state?: { id: number; code: string; name: string } };
  description?: string;
  is_active?: boolean;
}

interface Material {
  id: number;
  code: string;
  name: string;
  description?: string;
  category?: string;
  unit_cost: number;
  min_stock: number;
  tracking_type?: string;
  has_serial: boolean;
  track_batch?: boolean;
  unit?: { id: number; code: string; name: string };
}

interface Unit {
  id: number;
  code: string;
  name: string;
}

// ─── ESTADOS GERAIS DO MÓDULO ────────────────────────────────────────────────
const activeTab = ref<'regional' | 'materials' | 'depots' | 'serials'>('regional');
const loading = ref(false);
const toastMessage = ref('');
const toastType = ref<'success' | 'error'>('success');
const copiedKey = ref<string | null>(null);

// Menus e Modais
const isHeaderMenuOpen = ref(false);
const headerMenuContainerRef = ref<HTMLElement | null>(null);
const isModuleDrawerOpen = ref(false);
const activeDropdownMaterialId = ref<number | null>(null);
const isClusterModalOpen = ref(false);
const isDepotTypeModalOpen = ref(false);
const depotTypesList = ref<any[]>([]);
const isMaterialCategoryModalOpen = ref(false);
const materialCategoriesList = ref<any[]>([]);
const isUnitModalOpen = ref(false);
const isMaterialOwnerModalOpen = ref(false);
const materialOwnersList = ref<any[]>([]);
const materialFilterOwner = ref('');
const selectedMaterialOwnerInfo = computed(() => {
  if (!materialFilterOwner.value) return null;
  return materialOwnersList.value.find(o => String(o.id) === String(materialFilterOwner.value)) || null;
});
const isMaterialModalOpen = ref(false);
const isMaterialImportModalOpen = ref(false);
const selectedMaterialForEdit = ref<any>(null);
const materialFilterCategory = ref('all');
const isDepotModalOpen = ref(false);
const selectedDepotForEdit = ref<any>(null);
const isMovementModalOpen = ref(false);
const selectedMaterialForMovement = ref<any>(null);
const selectedDepotForMovement = ref<any>(null);
const selectedMovementType = ref<'ENTRY' | 'EXIT' | 'RETURN' | 'TRANSFER'>('TRANSFER');

function showToast(msg: string, type: 'success' | 'error' = 'success') {
  toastMessage.value = msg;
  toastType.value = type;
  setTimeout(() => {
    toastMessage.value = '';
  }, 3500);
}

function copyToClipboard(text: string, key: string) {
  if (!text) return;
  navigator.clipboard.writeText(text).then(() => {
    copiedKey.value = key;
    setTimeout(() => {
      if (copiedKey.value === key) copiedKey.value = null;
    }, 2000);
  });
}

function formatNumber(val: any): string {
  const num = Number(val) || 0;
  return new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(num);
}

function formatDepotType(type: string): string {
  const found = depotTypesList.value.find(t => t.code === type);
  if (found) return found.name;
  const map: Record<string, string> = {
    CENTRAL: 'Almoxarifado Central',
    REGIONAL_BASE: 'Base Regional',
    LAB_REPAIR: 'Laboratório de Reparo',
  };
  return map[type] || type || 'Depósito';
}

function formatSerialStatusLabel(status: string): string {
  const map: Record<string, string> = {
    IN_STOCK: 'Em Estoque',
    IN_TRANSIT: 'Em Trânsito',
    INSTALLED_CLIENT: 'Instalado',
    DEFECTIVE: 'Defeituoso',
    RESERVED: 'Reservado',
  };
  return map[status] || status;
}

function formatSerialStatusBadge(status: string): string {
  switch (status) {
    case 'IN_STOCK':
      return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60';
    case 'IN_TRANSIT':
      return 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/60';
    case 'INSTALLED_CLIENT':
      return 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700';
    case 'DEFECTIVE':
      return 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800/60';
    default:
      return 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60';
  }
}

// Fechar menu de apoio e dropdowns ao clicar fora
function handleDocumentClick(event: MouseEvent) {
  if (headerMenuContainerRef.value && !headerMenuContainerRef.value.contains(event.target as Node)) {
    isHeaderMenuOpen.value = false;
  }
  activeDropdownMaterialId.value = null;
}

function toggleMaterialActionsDropdown(id: number) {
  if (activeDropdownMaterialId.value === id) {
    activeDropdownMaterialId.value = null;
  } else {
    activeDropdownMaterialId.value = id;
  }
}

function handleEditMaterial(material: Material) {
  activeDropdownMaterialId.value = null;
  openEditMaterialModal(material);
}

function copySkuAndClose(code: string, key: string) {
  activeDropdownMaterialId.value = null;
  copyToClipboard(code, key);
}

function openClusterCrudModal() {
  isHeaderMenuOpen.value = false;
  isClusterModalOpen.value = true;
}

function openDepotTypeCrudModal() {
  isHeaderMenuOpen.value = false;
  isDepotTypeModalOpen.value = true;
}

function navigateToTab(tab: 'regional' | 'materials' | 'depots' | 'serials') {
  isHeaderMenuOpen.value = false;
  activeTab.value = tab;
}

function handleDrawerAction(action: 'transfer' | 'material' | 'cluster' | 'depot' | 'import-materials') {
  if (action === 'transfer') openTransferModal();
  else if (action === 'material') openCreateMaterialModal();
  else if (action === 'cluster') isClusterModalOpen.value = true;
  else if (action === 'depot') openCreateDepotModal();
  else if (action === 'import-materials') openMaterialImportModal();
}

// ─── ABA 1: POSIÇÃO REGIONAL ──────────────────────────────────────────────────
const clusters = ref<Cluster[]>([]);
const selectedClusterId = ref<number | null>(null);
const currentClusterSummary = ref<any>({});
const regionalMaterials = ref<any[]>([]);
const regionalSearch = ref('');
const expandedRows = ref<number[]>([]);

const regionalSortBy = ref('name');
const regionalSortDir = ref<'asc' | 'desc'>('asc');
const regionalCurrentPage = ref(1);
const regionalPerPage = ref(10);

function toggleSortRegional(col: string) {
  if (regionalSortBy.value === col) {
    regionalSortDir.value = regionalSortDir.value === 'asc' ? 'desc' : 'asc';
  } else {
    regionalSortBy.value = col;
    regionalSortDir.value = 'asc';
  }
}

const filteredRegionalMaterials = computed(() => {
  let list = [...regionalMaterials.value];
  if (regionalSearch.value.trim()) {
    const q = regionalSearch.value.toLowerCase();
    list = list.filter(m =>
      m.name.toLowerCase().includes(q) ||
      m.code.toLowerCase().includes(q)
    );
  }

  list.sort((a, b) => {
    let va = a[regionalSortBy.value];
    let vb = b[regionalSortBy.value];
    if (typeof va === 'string') va = va.toLowerCase();
    if (typeof vb === 'string') vb = vb.toLowerCase();
    if (va < vb) return regionalSortDir.value === 'asc' ? -1 : 1;
    if (va > vb) return regionalSortDir.value === 'asc' ? 1 : -1;
    return 0;
  });

  return list;
});

const regionalLastPage = computed(() => {
  return Math.ceil(filteredRegionalMaterials.value.length / regionalPerPage.value) || 1;
});

const paginatedRegionalMaterials = computed(() => {
  const start = (regionalCurrentPage.value - 1) * regionalPerPage.value;
  return filteredRegionalMaterials.value.slice(start, start + regionalPerPage.value);
});

const regionalFromRecord = computed(() => {
  if (filteredRegionalMaterials.value.length === 0) return 0;
  return (regionalCurrentPage.value - 1) * regionalPerPage.value + 1;
});

const regionalToRecord = computed(() => {
  return Math.min(regionalCurrentPage.value * regionalPerPage.value, filteredRegionalMaterials.value.length);
});

function toggleExpand(matId: number) {
  const idx = expandedRows.value.indexOf(matId);
  if (idx >= 0) {
    expandedRows.value.splice(idx, 1);
  } else {
    expandedRows.value.push(matId);
  }
}

async function loadClusters() {
  try {
    const res = await fetch('/api/v1/clusters');
    const json = await res.json();
    clusters.value = json.data || [];
    if (clusters.value.length > 0 && !selectedClusterId.value) {
      selectedClusterId.value = clusters.value[0].id;
    }
  } catch (err) {
    console.error('Erro ao carregar posições regionais:', err);
  }
}

async function loadRegionalStock() {
  if (!selectedClusterId.value) return;
  loading.value = true;
  try {
    const res = await fetch(`/api/v1/stock/regional-position?cluster_id=${selectedClusterId.value}`);
    const json = await res.json();
    if (json.data) {
      currentClusterSummary.value = json.data.summary || {};
      regionalMaterials.value = json.data.materials || [];
    }
  } catch (err) {
    console.error('Erro ao carregar posição regional:', err);
  } finally {
    loading.value = false;
  }
}

function selectCluster(id: number) {
  selectedClusterId.value = id;
  regionalCurrentPage.value = 1;
  loadRegionalStock();
}

// ─── ABA 2: CATÁLOGO DE MATERIAIS ─────────────────────────────────────────────
const materials = ref<Material[]>([]);
const unitsList = ref<Unit[]>([]);
const materialSearch = ref('');
const materialFilterType = ref('all');
const materialSortBy = ref('name');
const materialSortDir = ref<'asc' | 'desc'>('asc');
const materialCurrentPage = ref(1);
const materialPerPage = ref(10);

function toggleSortMaterial(col: string) {
  if (materialSortBy.value === col) {
    materialSortDir.value = materialSortDir.value === 'asc' ? 'desc' : 'asc';
  } else {
    materialSortBy.value = col;
    materialSortDir.value = 'asc';
  }
}

const filteredMaterials = computed(() => {
  let list = [...materials.value];

  if (materialFilterType.value === 'serialized') {
    list = list.filter(m => m.tracking_type === 'SERIAL' || m.has_serial);
  } else if (materialFilterType.value === 'batch') {
    list = list.filter(m => m.tracking_type === 'BATCH' || m.track_batch);
  } else if (materialFilterType.value === 'bulk' || materialFilterType.value === 'standard') {
    list = list.filter(m => m.tracking_type === 'BULK' || (!m.has_serial && !m.track_batch));
  }

  if (materialFilterCategory.value !== 'all') {
    list = list.filter(m => m.category === materialFilterCategory.value);
  }

  if (materialSearch.value.trim()) {
    const q = materialSearch.value.toLowerCase();
    list = list.filter(m =>
      m.code.toLowerCase().includes(q) ||
      m.name.toLowerCase().includes(q) ||
      (m.system_code && m.system_code.toLowerCase().includes(q)) ||
      (m.system_name && m.system_name.toLowerCase().includes(q)) ||
      (m.category && m.category.toLowerCase().includes(q))
    );
  }

  list.sort((a, b) => {
    let va = (a as any)[materialSortBy.value];
    let vb = (b as any)[materialSortBy.value];
    if (typeof va === 'string') va = va.toLowerCase();
    if (typeof vb === 'string') vb = vb.toLowerCase();
    if (va < vb) return materialSortDir.value === 'asc' ? -1 : 1;
    if (va > vb) return materialSortDir.value === 'asc' ? 1 : -1;
    return 0;
  });

  return list;
});

const materialLastPage = computed(() => {
  return Math.ceil(filteredMaterials.value.length / materialPerPage.value) || 1;
});

const paginatedMaterials = computed(() => {
  const start = (materialCurrentPage.value - 1) * materialPerPage.value;
  return filteredMaterials.value.slice(start, start + materialPerPage.value);
});

const materialFromRecord = computed(() => {
  if (filteredMaterials.value.length === 0) return 0;
  return (materialCurrentPage.value - 1) * materialPerPage.value + 1;
});

const materialToRecord = computed(() => {
  return Math.min(materialCurrentPage.value * materialPerPage.value, filteredMaterials.value.length);
});

async function loadMaterials() {
  try {
    const params = new URLSearchParams();
    if (materialFilterOwner.value) {
      params.append('owner_id', String(materialFilterOwner.value));
    }
    const queryString = params.toString() ? `?${params.toString()}` : '';
    const res = await fetch(`/api/v1/materials${queryString}`);
    const json = await res.json();
    materials.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar materiais:', err);
  }
}

async function loadMaterialOwners() {
  try {
    const res = await fetch('/api/v1/material-owners?active_only=true');
    const json = await res.json();
    materialOwnersList.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar proprietários de material:', err);
  }
}

watch(materialFilterOwner, () => {
  materialCurrentPage.value = 1;
  loadMaterials();
});

async function loadUnits() {
  try {
    const res = await fetch('/api/v1/units?active_only=true');
    const json = await res.json();
    unitsList.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar unidades:', err);
  }
}

// ─── ABA 3: DEPÓSITOS ─────────────────────────────────────────────────────────
const depots = ref<Depot[]>([]);
const depotSearch = ref('');
const depotFilterType = ref('all');
const depotSortBy = ref('name');
const depotSortDir = ref<'asc' | 'desc'>('asc');
const depotCurrentPage = ref(1);
const depotPerPage = ref(10);

function toggleSortDepot(col: string) {
  if (depotSortBy.value === col) {
    depotSortDir.value = depotSortDir.value === 'asc' ? 'desc' : 'asc';
  } else {
    depotSortBy.value = col;
    depotSortDir.value = 'asc';
  }
}

const filteredDepots = computed(() => {
  let list = [...depots.value];

  if (depotFilterType.value !== 'all') {
    list = list.filter(d => d.type === depotFilterType.value);
  }

  if (depotSearch.value.trim()) {
    const q = depotSearch.value.toLowerCase();
    list = list.filter(d =>
      d.name.toLowerCase().includes(q) ||
      d.code.toLowerCase().includes(q) ||
      (d.responsible_person?.name && d.responsible_person.name.toLowerCase().includes(q))
    );
  }

  list.sort((a, b) => {
    let va = (a as any)[depotSortBy.value];
    let vb = (b as any)[depotSortBy.value];
    if (typeof va === 'string') va = va.toLowerCase();
    if (typeof vb === 'string') vb = vb.toLowerCase();
    if (va < vb) return depotSortDir.value === 'asc' ? -1 : 1;
    if (va > vb) return depotSortDir.value === 'asc' ? 1 : -1;
    return 0;
  });

  return list;
});

const depotLastPage = computed(() => {
  return Math.ceil(filteredDepots.value.length / depotPerPage.value) || 1;
});

const paginatedDepots = computed(() => {
  const start = (depotCurrentPage.value - 1) * depotPerPage.value;
  return filteredDepots.value.slice(start, start + depotPerPage.value);
});

const depotFromRecord = computed(() => {
  if (filteredDepots.value.length === 0) return 0;
  return (depotCurrentPage.value - 1) * depotPerPage.value + 1;
});

const depotToRecord = computed(() => {
  return Math.min(depotCurrentPage.value * depotPerPage.value, filteredDepots.value.length);
});

async function loadDepots() {
  try {
    const res = await fetch('/api/v1/depots');
    const json = await res.json();
    depots.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar depósitos:', err);
  }
}

// ─── ABA 4: SERIALIZADO ───────────────────────────────────────────────────────
const serialsList = ref<any[]>([]);
const serialSearch = ref('');
const serialStatusFilter = ref('');
const serialSortBy = ref('serial_number');
const serialSortDir = ref<'asc' | 'desc'>('asc');
const serialCurrentPage = ref(1);
const serialPerPage = ref(10);
let serialDebounceTimer: any = null;

function debounceLoadSerials() {
  clearTimeout(serialDebounceTimer);
  serialDebounceTimer = setTimeout(() => {
    serialCurrentPage.value = 1;
    loadSerials();
  }, 300);
}

function clearSerialSearch() {
  serialSearch.value = '';
  serialCurrentPage.value = 1;
  loadSerials();
}

function toggleSortSerial(col: string) {
  if (serialSortBy.value === col) {
    serialSortDir.value = serialSortDir.value === 'asc' ? 'desc' : 'asc';
  } else {
    serialSortBy.value = col;
    serialSortDir.value = 'asc';
  }
}

const filteredSerials = computed(() => {
  let list = [...serialsList.value];

  list.sort((a, b) => {
    let va = a[serialSortBy.value] || a.material?.name || '';
    let vb = b[serialSortBy.value] || b.material?.name || '';
    if (typeof va === 'string') va = va.toLowerCase();
    if (typeof vb === 'string') vb = vb.toLowerCase();
    if (va < vb) return serialSortDir.value === 'asc' ? -1 : 1;
    if (va > vb) return serialSortDir.value === 'asc' ? 1 : -1;
    return 0;
  });

  return list;
});

const serialLastPage = computed(() => {
  return Math.ceil(filteredSerials.value.length / serialPerPage.value) || 1;
});

const paginatedSerials = computed(() => {
  const start = (serialCurrentPage.value - 1) * serialPerPage.value;
  return filteredSerials.value.slice(start, start + serialPerPage.value);
});

const serialFromRecord = computed(() => {
  if (filteredSerials.value.length === 0) return 0;
  return (serialCurrentPage.value - 1) * serialPerPage.value + 1;
});

const serialToRecord = computed(() => {
  return Math.min(serialCurrentPage.value * serialPerPage.value, filteredSerials.value.length);
});

async function loadSerials() {
  try {
    const params = new URLSearchParams();
    if (serialSearch.value.trim()) params.append('search', serialSearch.value.trim());
    if (serialStatusFilter.value) params.append('status', serialStatusFilter.value);

    const url = `/api/v1/stock/serials${params.toString() ? '?' + params.toString() : ''}`;
    const res = await fetch(url);
    const json = await res.json();
    serialsList.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar seriais:', err);
  }
}

// ─── AÇÕES DE MODAL: MOVIMENTAÇÃO DE ESTOQUE ────────────────────────────────
function openMovementModal(
  material?: any,
  depot?: any,
  type: 'ENTRY' | 'EXIT' | 'RETURN' | 'TRANSFER' = 'TRANSFER'
) {
  selectedMaterialForMovement.value = material || null;
  selectedDepotForMovement.value = depot || null;
  selectedMovementType.value = type;
  isMovementModalOpen.value = true;
}

function openTransferModal(material?: any) {
  openMovementModal(material, null, 'TRANSFER');
}

function openTransferFromDepot(depot: Depot) {
  openMovementModal(null, depot, 'TRANSFER');
}

function openTransferFromBreakdown(breakdownItem: any, material: any) {
  const originDepot = depots.value.find(d => d.id === breakdownItem.depot_id || d.name === breakdownItem.depot_name) || null;
  openMovementModal(material, originDepot, 'TRANSFER');
}

function openTransferFromSerial(serial: any) {
  openMovementModal(serial.material || null, serial.current_depot || null, 'TRANSFER');
}

async function onMovementSuccess(movement?: any) {
  const typeLabels: Record<string, string> = {
    ENTRY: 'Entrada',
    EXIT: 'Saída',
    RETURN: 'Devolução',
    TRANSFER: 'Transferência',
  };
  const label = movement?.movement_type ? (typeLabels[movement.movement_type] || 'Movimentação') : 'Movimentação';
  showToast(`${label} de estoque realizada com sucesso!`);
  await Promise.all([
    loadRegionalStock(),
    loadMaterials(),
    loadDepots(),
    loadSerials(),
  ]);
}

// ─── AÇÕES DE MODAL: MATERIAIS ───────────────────────────────────────────────
function openCreateMaterialModal() {
  isHeaderMenuOpen.value = false;
  selectedMaterialForEdit.value = null;
  isMaterialModalOpen.value = true;
}

function openEditMaterialModal(material: Material) {
  selectedMaterialForEdit.value = { ...material };
  isMaterialModalOpen.value = true;
}

async function onMaterialSaved() {
  showToast('Catálogo de materiais atualizado com sucesso!');
  await Promise.all([
    loadMaterials(),
    loadRegionalStock(),
  ]);
}

function openMaterialImportModal() {
  isHeaderMenuOpen.value = false;
  isMaterialImportModalOpen.value = true;
}

async function onMaterialsImported(result: any) {
  const count = (result?.imported_count || 0) + (result?.updated_count || 0);
  showToast(`${count} material(is) processado(s) com sucesso via planilha!`);
  await Promise.all([
    loadMaterials(),
    loadRegionalStock(),
  ]);
}

// ─── AÇÕES DE MODAL: POSIÇÕES REGIONAIS ──────────────────────────────────────
async function onClustersUpdated() {
  showToast('Posições regionais sincronizadas!');
  await loadClusters();
  await loadRegionalStock();
}

// ─── AÇÕES DE MODAL: TIPOS DE DEPÓSITO ───────────────────────────────────────
async function loadDepotTypes() {
  try {
    const res = await fetch('/api/v1/depot-types?active_only=true');
    const json = await res.json();
    depotTypesList.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar tipos de depósito:', err);
  }
}

async function onDepotTypesUpdated() {
  showToast('Tipos de depósito sincronizados com sucesso!');
  await Promise.all([
    loadDepotTypes(),
    loadDepots(),
  ]);
}

// ─── AÇÕES DE MODAL: CATEGORIAS DE MATERIAL ──────────────────────────────────
function openMaterialCategoryCrudModal() {
  isHeaderMenuOpen.value = false;
  isMaterialCategoryModalOpen.value = true;
}

async function loadMaterialCategories() {
  try {
    const res = await fetch('/api/v1/material-categories?active_only=true');
    const json = await res.json();
    materialCategoriesList.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar categorias de material:', err);
  }
}

async function onMaterialCategoriesUpdated() {
  showToast('Categorias de material sincronizadas com sucesso!');
  await Promise.all([
    loadMaterialCategories(),
    loadMaterials(),
  ]);
}

// ─── AÇÕES DE MODAL: UNIDADES DE MEDIDA ──────────────────────────────────────
function openUnitCrudModal() {
  isHeaderMenuOpen.value = false;
  isUnitModalOpen.value = true;
}

async function onUnitsUpdated() {
  showToast('Unidades de medida sincronizadas com sucesso!');
  await Promise.all([
    loadUnits(),
    loadMaterials(),
  ]);
}

// ─── AÇÕES DE MODAL: PROPRIETÁRIOS ───────────────────────────────────────────
function openMaterialOwnerCrudModal() {
  isHeaderMenuOpen.value = false;
  isMaterialOwnerModalOpen.value = true;
}

async function onMaterialOwnersUpdated() {
  showToast('Proprietários sincronizados com sucesso!');
  await Promise.all([
    loadMaterialOwners(),
    loadMaterials(),
    loadDepots(),
  ]);
}

// ─── AÇÕES DE MODAL: DEPÓSITOS ───────────────────────────────────────────────
function openCreateDepotModal() {
  isHeaderMenuOpen.value = false;
  selectedDepotForEdit.value = null;
  isDepotModalOpen.value = true;
}

function openEditDepotModal(depot: Depot) {
  selectedDepotForEdit.value = { ...depot };
  isDepotModalOpen.value = true;
}

async function onDepotSaved() {
  showToast(selectedDepotForEdit.value ? 'Depósito atualizado com sucesso!' : 'Depósito salvo com sucesso!');
  await Promise.all([
    loadDepots(),
    loadClusters(),
    loadDepotTypes(),
  ]);
  await loadRegionalStock();
}

// ─── RECARREGAR ABA ATUAL ─────────────────────────────────────────────────────
async function refreshCurrentTab() {
  loading.value = true;
  try {
    await Promise.all([
      loadClusters(),
      loadDepotTypes(),
      loadMaterialCategories(),
      loadMaterialOwners(),
      loadMaterials(),
      loadUnits(),
      loadDepots(),
      loadSerials(),
    ]);
    await loadRegionalStock();
    showToast('Dados de suprimentos atualizados com sucesso!');
  } finally {
    loading.value = false;
  }
}

// ─── CICLO DE VIDA ────────────────────────────────────────────────────────────
onMounted(async () => {
  document.addEventListener('click', handleDocumentClick);
  await Promise.all([
    loadClusters(),
    loadDepotTypes(),
    loadMaterialCategories(),
    loadMaterialOwners(),
    loadMaterials(),
    loadUnits(),
    loadDepots(),
    loadSerials(),
  ]);
  loadRegionalStock();
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleDocumentClick);
});
</script>
