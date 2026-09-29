<template>
  <div class="py-8 w-full space-y-6">
    <!-- Linha 1 Canônica Obrigatória: BaseBreadcrumb -->
    <BaseBreadcrumb :items="[
      { label: 'Início', href: '#people' },
      { label: 'Operações', href: '#supplies' },
      { label: 'Suprimentos & WMS' }
    ]" />

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
            Catálogo de materiais, rastreabilidade serial e movimentação de estoque
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
                  @click="openDepotCrudModal"
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
          class="px-2 py-0.5 rounded-md text-[11px] font-bold"
          :class="activeTab === 'materials' ? 'bg-[#FC6714]/15 text-[#FC6714]' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'"
        >
          {{ materials.length }}
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
          class="px-2 py-0.5 rounded-md text-[11px] font-bold"
          :class="activeTab === 'serials' ? 'bg-[#FC6714]/15 text-[#FC6714]' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'"
        >
          {{ serialsList.length }}
        </span>
      </button>

      <button
        type="button"
        @click="activeTab = 'movements'"
        :class="activeTab === 'movements'
          ? 'border-[#FC6714] text-[#FC6714] font-bold'
          : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
        class="pb-3 border-b-2 text-sm flex items-center gap-2 transition cursor-pointer shrink-0"
      >
        <ArrowLeftRight class="w-4 h-4" />
        <span>Movimento</span>
        <span
          v-if="movementsTotal > 0"
          class="px-2 py-0.5 rounded-md text-[11px] font-bold"
          :class="activeTab === 'movements' ? 'bg-[#FC6714]/15 text-[#FC6714]' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'"
        >
          {{ movementsTotal }}
        </span>
      </button>
    </div>

    <!-- ============================================================================= -->
    <!-- ABA: MATERIAIS -->
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
            <option value="">Proprietários</option>
            <option v-for="o in materialOwnersList" :key="o.id" :value="o.id" :title="o.name">
              {{ o.code }}
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
    <!-- ABA: SERIALIZADO -->
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
    <!-- ABA: MOVIMENTO (GRID DE MOVIMENTAÇÕES DE TODOS OS TIPOS) -->
    <!-- ============================================================================= -->
    <div v-if="activeTab === 'movements'" class="space-y-4">
      <!-- Barra Superior da Aba: Alternador de Visualização (Itens Corridos vs. Por Documentos) -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-white dark:bg-[#06064D]/50 rounded-xl border border-slate-200 dark:border-[#14147A] shadow-2xs">
        <div class="inline-flex p-1 rounded-lg bg-slate-100 dark:bg-[#03032E] border border-slate-200 dark:border-[#14147A] shrink-0">
          <button
            type="button"
            @click="setMovementViewMode('items')"
            :class="movementViewMode === 'items'
              ? 'bg-white dark:bg-[#06064D] text-[#FC6714] shadow-xs font-bold'
              : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-md text-xs transition cursor-pointer"
          >
            <Boxes class="w-3.5 h-3.5" />
            <span>Itens Corridos</span>
            <span
              class="px-1.5 py-0.5 rounded text-[10px] font-bold"
              :class="movementViewMode === 'items' ? 'bg-[#FC6714]/15 text-[#FC6714]' : 'bg-slate-200 dark:bg-slate-800 text-slate-500 dark:text-slate-400'"
            >
              {{ movementsTotal }}
            </span>
          </button>

          <button
            type="button"
            @click="setMovementViewMode('documents')"
            :class="movementViewMode === 'documents'
              ? 'bg-white dark:bg-[#06064D] text-[#FC6714] shadow-xs font-bold'
              : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-md text-xs transition cursor-pointer"
          >
            <FileStack class="w-3.5 h-3.5" />
            <span>Por Documentos</span>
            <span
              class="px-1.5 py-0.5 rounded text-[10px] font-bold"
              :class="movementViewMode === 'documents' ? 'bg-[#FC6714]/15 text-[#FC6714]' : 'bg-slate-200 dark:bg-slate-800 text-slate-500 dark:text-slate-400'"
            >
              {{ documentsTotal }}
            </span>
          </button>
        </div>

        <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
          <span v-if="movementViewMode === 'items'">
            Visualização detalhada linha por linha de cada movimentação individual
          </span>
          <span v-else>
            Visualização consolidada por documento / protocolo com sanfona de itens
          </span>
        </div>
      </div>

      <!-- Barra de Filtros e Busca de Movimentações -->
      <div class="p-4 bg-white dark:bg-[#06064D]/50 rounded-xl border border-slate-200 dark:border-[#14147A] flex flex-wrap items-center justify-between gap-4 text-sm shadow-2xs">
        <div class="relative flex-1 min-w-[280px]">
          <Search class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400 pointer-events-none" />
          <input
            type="text"
            v-model="movementSearch"
            @input="debounceLoadMovements"
            placeholder="Buscar por protocolo, documento (NF/OS), SKU, material ou serial..."
            class="w-full h-11 pl-10 pr-10 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 text-sm transition"
          />
          <button
            v-if="movementSearch"
            @click="clearMovementSearch"
            class="absolute right-3 top-3 p-1 rounded text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition cursor-pointer"
            title="Limpar busca"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
          <!-- Filtro de Tipo de Movimentação -->
          <select
            v-model="movementTypeFilter"
            class="h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-xs font-semibold focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer"
          >
            <option value="">Todos os Tipos</option>
            <option value="ENTRY">Entrada</option>
            <option value="EXIT">Saída</option>
            <option value="TRANSFER">Transferência</option>
            <option value="RETURN">Devolução</option>
            <option value="ADJUSTMENT">Ajuste</option>
          </select>

          <!-- Filtro de Depósito -->
          <select
            v-model="movementDepotFilter"
            class="h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-xs font-semibold focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer max-w-[200px] truncate"
          >
            <option value="">Todos os Depósitos</option>
            <option v-for="d in depots" :key="d.id" :value="d.id">
              {{ d.name }}
            </option>
          </select>

          <!-- Período: Data Inicial -->
          <div class="flex items-center gap-1.5 bg-slate-50 dark:bg-[#03032E] px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-[#14147A]">
            <span class="text-[11px] font-bold text-slate-400 uppercase">De:</span>
            <input
              type="date"
              v-model="movementStartDate"
              class="bg-transparent text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none"
            />
          </div>

          <!-- Período: Data Final -->
          <div class="flex items-center gap-1.5 bg-slate-50 dark:bg-[#03032E] px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-[#14147A]">
            <span class="text-[11px] font-bold text-slate-400 uppercase">Até:</span>
            <input
              type="date"
              v-model="movementEndDate"
              class="bg-transparent text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none"
            />
          </div>

          <button
            v-if="movementSearch || movementTypeFilter || movementDepotFilter || movementStartDate || movementEndDate"
            type="button"
            @click="clearAllMovementFilters"
            class="h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#03032E] text-slate-600 dark:text-slate-300 hover:text-[#FC6714] text-xs font-semibold transition cursor-pointer flex items-center gap-1"
            title="Limpar todos os filtros"
          >
            <X class="w-3.5 h-3.5" />
            <span>Limpar</span>
          </button>

          <span class="text-xs text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">
            {{ movementViewMode === 'items' ? `${movementsTotal} movimentações` : `${documentsTotal} documentos` }}
          </span>
        </div>
      </div>

      <!-- TABELA CONFORTÁVEL: MOVIMENTAÇÕES ITENS CORRIDOS (DESIGN SYSTEM CANÔNICO) -->
      <div v-if="movementViewMode === 'items'" class="rounded-xl border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#06064D]/50 shadow-xs overflow-hidden transition-colors duration-200">
        <div class="overflow-x-auto min-h-[280px]">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="border-b border-slate-200 dark:border-[#14147A] bg-slate-50/80 dark:bg-[#03032E]/70 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider select-none">
                <th class="py-3.5 px-5">Data / Hora</th>
                <th class="py-3.5 px-5">Protocolo & Doc</th>
                <th class="py-3.5 px-5 text-center">Tipo</th>
                <th class="py-3.5 px-5">Material (SKU / Descrição)</th>
                <th class="py-3.5 px-5 text-right">Qtd</th>
                <th class="py-3.5 px-5">Origem ➔ Destino</th>
                <th class="py-3.5 px-5 text-center">Rastreio</th>
                <th class="py-3.5 px-5 text-right">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/50">
              <tr v-if="loadingMovements">
                <td colspan="8" class="py-12 text-center text-slate-400">
                  <div class="flex items-center justify-center gap-2">
                    <RefreshCw class="w-5 h-5 animate-spin text-[#FC6714]" />
                    <span>Carregando movimentações...</span>
                  </div>
                </td>
              </tr>
              <tr v-else-if="movementsList.length === 0">
                <td colspan="8" class="py-12 text-center text-slate-400 dark:text-slate-500">
                  <Boxes class="w-8 h-8 mx-auto mb-2 opacity-40 text-slate-400" />
                  <p class="font-medium text-sm">Nenhuma movimentação de estoque encontrada.</p>
                  <p class="text-xs text-slate-400 mt-1">Utilize o botão "Nova Movimentação" para registrar entradas, transferências e saídas.</p>
                </td>
              </tr>
              <tr
                v-else
                v-for="mv in movementsList"
                :key="mv.id"
                class="hover:bg-slate-50/80 dark:hover:bg-white/[0.03] transition-colors group cursor-pointer"
                @click="openMovementDetails(mv)"
              >
                <!-- Data / Hora -->
                <td class="py-4 px-5 whitespace-nowrap text-xs text-slate-600 dark:text-slate-300">
                  <div class="font-semibold text-slate-800 dark:text-slate-200">
                    {{ formatDate(mv.movement_date || mv.created_at) }}
                  </div>
                  <div class="text-[11px] text-slate-400 mt-0.5">
                    {{ formatTime(mv.created_at) }}
                  </div>
                </td>

                <!-- Protocolo & Documento -->
                <td class="py-4 px-5">
                  <div class="flex items-center gap-1.5">
                    <span class="font-mono font-bold text-xs text-slate-900 dark:text-white">
                      {{ mv.protocol || `#${mv.id}` }}
                    </span>
                    <button
                      v-if="mv.protocol"
                      type="button"
                      @click.stop="copyToClipboard(mv.protocol, `prot-${mv.id}`)"
                      class="opacity-0 group-hover:opacity-100 hover:text-[#FC6714] text-slate-400 transition cursor-pointer p-0.5"
                      title="Copiar Protocolo"
                    >
                      <Check v-if="copiedKey === `prot-${mv.id}`" class="w-3.5 h-3.5 text-emerald-500" />
                      <Copy v-else class="w-3.5 h-3.5" />
                    </button>
                  </div>
                  <div class="text-[11px] text-slate-400 truncate max-w-[160px]" :title="mv.document_number || mv.document_ref">
                    Doc: {{ mv.document_number || mv.document_ref || '-' }}
                  </div>
                </td>

                <!-- Tipo com Badge -->
                <td class="py-4 px-5 text-center whitespace-nowrap">
                  <span
                    class="inline-block px-2.5 py-1 rounded-md text-xs font-semibold border"
                    :class="formatMovementTypeBadge(mv.movement_type)"
                  >
                    {{ formatMovementTypeLabel(mv.movement_type) }}
                  </span>
                </td>

                <!-- Material (SKU e Nome) -->
                <td class="py-4 px-5 max-w-[300px]">
                  <div class="font-mono text-xs font-bold text-[#FC6714] dark:text-orange-400">
                    {{ mv.material?.code || '-' }}
                  </div>
                  <div class="font-medium text-slate-900 dark:text-slate-100 text-sm truncate" :title="mv.material?.name">
                    {{ mv.material?.name || 'Material' }}
                  </div>
                </td>

                <!-- Quantidade -->
                <td class="py-4 px-5 text-right whitespace-nowrap">
                  <div class="font-bold text-slate-900 dark:text-white text-sm">
                    {{ formatNumber(mv.quantity) }}
                  </div>
                  <div class="text-[11px] text-slate-400 font-mono">
                    {{ mv.material?.unit?.code || 'UND' }}
                  </div>
                </td>

                <!-- Origem ➔ Destino -->
                <td class="py-4 px-5 text-xs text-slate-700 dark:text-slate-300">
                  <div class="flex items-center gap-1.5 flex-wrap">
                    <span class="font-medium truncate max-w-[120px]" :title="mv.source_depot?.name || 'Fornecedor/Entrada'">
                      {{ mv.source_depot?.name || (mv.movement_type === 'ENTRY' ? 'Entrada Externa' : '-') }}
                    </span>
                    <span class="text-slate-400">➔</span>
                    <span class="font-medium text-slate-900 dark:text-white truncate max-w-[120px]" :title="mv.destination_depot?.name || 'Consumo/Saída'">
                      {{ mv.destination_depot?.name || (mv.movement_type === 'EXIT' ? 'Saída Externa' : '-') }}
                    </span>
                  </div>
                  <div v-if="mv.destination_depot?.cluster || mv.source_depot?.cluster" class="text-[10px] text-slate-400 mt-0.5">
                    Regional: {{ mv.destination_depot?.cluster?.name || mv.source_depot?.cluster?.name }}
                  </div>
                </td>

                <!-- Rastreio: Seriais e Anexos -->
                <td class="py-4 px-5 text-center whitespace-nowrap">
                  <div class="inline-flex items-center gap-1.5">
                    <span
                      v-if="mv.serials && mv.serials.length > 0"
                      class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800"
                      :title="`${mv.serials.length} seriais vinculados`"
                    >
                      <QrCode class="w-3 h-3 inline mr-1" />
                      {{ mv.serials.length }}
                    </span>
                    <span
                      v-if="mv.attachments && mv.attachments.length > 0"
                      class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800"
                      :title="`${mv.attachments.length} arquivos/comprovantes anexados`"
                    >
                      <FileText class="w-3 h-3 inline mr-1" />
                      {{ mv.attachments.length }}
                    </span>
                    <span v-if="(!mv.serials || mv.serials.length === 0) && (!mv.attachments || mv.attachments.length === 0)" class="text-slate-400 text-xs">
                      -
                    </span>
                  </div>
                </td>

                <!-- Ações -->
                <td class="py-4 px-5 text-right whitespace-nowrap" @click.stop>
                  <div class="inline-flex items-center gap-1.5">
                    <!-- Botão Reconf Externo -->
                    <a
                      v-if="mv.protocol"
                      :href="`/u/reconf/${mv.protocol}`"
                      target="_blank"
                      class="h-8 px-2.5 text-xs font-semibold rounded-md border border-orange-200 dark:border-orange-800/80 bg-orange-50/60 dark:bg-orange-950/40 text-[#FC6714] hover:bg-[#FC6714] hover:text-white transition cursor-pointer inline-flex items-center gap-1"
                      title="Abrir página pública de conferência (Reconf)"
                    >
                      <FileText class="w-3.5 h-3.5" />
                      <span>Reconf</span>
                      <ExternalLink class="w-3 h-3" />
                    </a>

                    <!-- Botão Ver Detalhes -->
                    <button
                      type="button"
                      @click="openMovementDetails(mv)"
                      class="h-8 px-2.5 text-xs font-medium rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer inline-flex items-center gap-1"
                      title="Ver detalhes completos da movimentação"
                    >
                      <Eye class="w-3.5 h-3.5" />
                      <span>Detalhes</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Rodapé de Paginação Confortável -->
        <TablePagination
          v-model:currentPage="movementCurrentPage"
          v-model:perPage="movementPerPage"
          :lastPage="movementsLastPage"
          :totalRecords="movementsTotal"
          :fromRecord="movementFromRecord"
          :toRecord="movementToRecord"
          :loading="loadingMovements"
          @changePage="(p) => { movementCurrentPage = p; loadMovements(); }"
          @changePerPage="(pp) => { movementPerPage = pp; movementCurrentPage = 1; loadMovements(); }"
        />
      </div>

      <!-- TABELA CONFORTÁVEL: DOCUMENTOS CONSOLIDADOS (DESIGN SYSTEM CANÔNICO) -->
      <div v-else-if="movementViewMode === 'documents'" class="rounded-xl border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#06064D]/50 shadow-xs overflow-hidden transition-colors duration-200">
        <div class="overflow-x-auto min-h-[280px]">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="border-b border-slate-200 dark:border-[#14147A] bg-slate-50/80 dark:bg-[#03032E]/70 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider select-none">
                <th class="py-3.5 px-3 w-10 text-center"></th>
                <th class="py-3.5 px-5">Data / Hora</th>
                <th class="py-3.5 px-5">Protocolo & Documento</th>
                <th class="py-3.5 px-5 text-center">Tipo</th>
                <th class="py-3.5 px-5">Origem ➔ Destino</th>
                <th class="py-3.5 px-5">Resumo de Itens</th>
                <th class="py-3.5 px-5 text-center">Rastreio</th>
                <th class="py-3.5 px-5 text-right">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/50">
              <tr v-if="loadingDocuments">
                <td colspan="8" class="py-12 text-center text-slate-400">
                  <div class="flex items-center justify-center gap-2">
                    <RefreshCw class="w-5 h-5 animate-spin text-[#FC6714]" />
                    <span>Carregando documentos de movimentação...</span>
                  </div>
                </td>
              </tr>
              <tr v-else-if="documentsList.length === 0">
                <td colspan="8" class="py-12 text-center text-slate-400 dark:text-slate-500">
                  <FileStack class="w-8 h-8 mx-auto mb-2 opacity-40 text-slate-400" />
                  <p class="font-medium text-sm">Nenhum documento de movimentação encontrado.</p>
                  <p class="text-xs text-slate-400 mt-1">Utilize o botão "Nova Movimentação" para gerar documentos de entrada, transferência e saída com múltiplos itens.</p>
                </td>
              </tr>
              <template v-else v-for="doc in documentsList" :key="doc.key">
                <!-- Linha Principal do Documento -->
                <tr
                  class="hover:bg-slate-50/80 dark:hover:bg-white/[0.03] transition-colors group cursor-pointer"
                  :class="{ 'bg-orange-50/30 dark:bg-orange-950/15': expandedDocumentKeys.has(doc.key) }"
                  @click="toggleDocumentExpanded(doc.key)"
                >
                  <!-- Botão Expansor / Sanfona -->
                  <td class="py-4 px-3 text-center" @click.stop="toggleDocumentExpanded(doc.key)">
                    <button
                      type="button"
                      class="inline-flex items-center justify-center w-7 h-7 rounded-md text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-white/10 transition cursor-pointer"
                      :title="expandedDocumentKeys.has(doc.key) ? 'Recolher itens deste documento' : 'Expandir e ver itens deste documento'"
                    >
                      <ChevronDown v-if="expandedDocumentKeys.has(doc.key)" class="w-4 h-4 text-[#FC6714]" />
                      <ChevronRight v-else class="w-4 h-4" />
                    </button>
                  </td>

                  <!-- Data / Hora -->
                  <td class="py-4 px-5 whitespace-nowrap text-xs text-slate-600 dark:text-slate-300">
                    <div class="font-semibold text-slate-800 dark:text-slate-200">
                      {{ formatDate(doc.movement_date || doc.created_at) }}
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5">
                      {{ formatTime(doc.created_at) }}
                    </div>
                  </td>

                  <!-- Protocolo & Documento -->
                  <td class="py-4 px-5">
                    <div class="flex items-center gap-1.5">
                      <span class="font-mono font-bold text-xs text-slate-900 dark:text-white">
                        {{ doc.protocol || doc.key }}
                      </span>
                      <button
                        v-if="doc.protocol"
                        type="button"
                        @click.stop="copyToClipboard(doc.protocol, `doc-prot-${doc.key}`)"
                        class="opacity-0 group-hover:opacity-100 hover:text-[#FC6714] text-slate-400 transition cursor-pointer p-0.5"
                        title="Copiar Protocolo"
                      >
                        <Check v-if="copiedKey === `doc-prot-${doc.key}`" class="w-3.5 h-3.5 text-emerald-500" />
                        <Copy v-else class="w-3.5 h-3.5" />
                      </button>
                    </div>
                    <div class="text-[11px] text-slate-400 truncate max-w-[180px]" :title="doc.document_number || doc.document_ref">
                      Doc: <strong class="text-slate-600 dark:text-slate-300 font-semibold">{{ doc.document_number || doc.document_ref || '-' }}</strong>
                    </div>
                  </td>

                  <!-- Tipo com Badge -->
                  <td class="py-4 px-5 text-center whitespace-nowrap">
                    <span
                      class="inline-block px-2.5 py-1 rounded-md text-xs font-semibold border"
                      :class="formatMovementTypeBadge(doc.movement_type)"
                    >
                      {{ formatMovementTypeLabel(doc.movement_type) }}
                    </span>
                  </td>

                  <!-- Origem ➔ Destino -->
                  <td class="py-4 px-5 text-xs text-slate-700 dark:text-slate-300">
                    <div class="flex items-center gap-1.5 flex-wrap">
                      <span class="font-medium truncate max-w-[130px]" :title="doc.source_depot?.name || (doc.movement_type === 'ENTRY' ? 'Entrada Externa / Fornecedor' : '-')">
                        {{ doc.source_depot?.name || (doc.movement_type === 'ENTRY' ? 'Entrada Externa' : '-') }}
                      </span>
                      <span class="text-slate-400">➔</span>
                      <span class="font-semibold text-slate-900 dark:text-white truncate max-w-[130px]" :title="doc.destination_depot?.name || (doc.movement_type === 'EXIT' ? 'Saída Externa / Consumo' : '-')">
                        {{ doc.destination_depot?.name || (doc.movement_type === 'EXIT' ? 'Saída Externa' : '-') }}
                      </span>
                    </div>
                    <div v-if="doc.destination_depot?.cluster || doc.source_depot?.cluster" class="text-[10px] text-slate-400 mt-0.5">
                      Regional: {{ doc.destination_depot?.cluster?.name || doc.source_depot?.cluster?.name }}
                    </div>
                  </td>

                  <!-- Resumo de Itens e Volume -->
                  <td class="py-4 px-5 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                        <Boxes class="w-3.5 h-3.5 text-slate-500" />
                        {{ doc.items_count }} {{ doc.items_count === 1 ? 'material' : 'materiais' }}
                      </span>
                    </div>
                    <div class="text-[11px] text-slate-400 font-medium mt-1">
                      Volume: <strong class="text-slate-700 dark:text-slate-200">{{ formatNumber(doc.total_quantity) }}</strong> un.
                    </div>
                  </td>

                  <!-- Rastreio (Seriais & Anexos) -->
                  <td class="py-4 px-5 text-center whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">
                      <span
                        v-if="doc.serials_count > 0"
                        class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800"
                        :title="`${doc.serials_count} seriais vinculados ao documento`"
                      >
                        <QrCode class="w-3 h-3 inline mr-1" />
                        {{ doc.serials_count }}
                      </span>
                      <span
                        v-if="doc.attachments_count > 0"
                        class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800"
                        :title="`${doc.attachments_count} arquivos / comprovantes anexados`"
                      >
                        <FileText class="w-3 h-3 inline mr-1" />
                        {{ doc.attachments_count }}
                      </span>
                      <span v-if="doc.serials_count === 0 && doc.attachments_count === 0" class="text-slate-400 text-xs">
                        -
                      </span>
                    </div>
                  </td>

                  <!-- Ações -->
                  <td class="py-4 px-5 text-right whitespace-nowrap" @click.stop>
                    <div class="inline-flex items-center gap-1.5">
                      <!-- Atalho Reconf Externo -->
                      <a
                        v-if="doc.protocol"
                        :href="`/u/reconf/${doc.protocol}`"
                        target="_blank"
                        class="h-8 px-2.5 text-xs font-semibold rounded-md border border-orange-200 dark:border-orange-800/80 bg-orange-50/60 dark:bg-orange-950/40 text-[#FC6714] hover:bg-[#FC6714] hover:text-white transition cursor-pointer inline-flex items-center gap-1"
                        title="Abrir conferência Reconf em nova guia"
                      >
                        <FileText class="w-3.5 h-3.5" />
                        <span>Reconf</span>
                        <ExternalLink class="w-3 h-3" />
                      </a>

                      <!-- Botão Detalhes Completos -->
                      <button
                        type="button"
                        @click="openDocumentDetails(doc)"
                        class="h-8 px-2.5 text-xs font-semibold rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer inline-flex items-center gap-1"
                        title="Ver detalhes consolidados do documento"
                      >
                        <Eye class="w-3.5 h-3.5" />
                        <span>Detalhes</span>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- Linha de Sanfona / Accordion (Itens do Documento) -->
                <tr
                  v-if="expandedDocumentKeys.has(doc.key)"
                  class="bg-slate-50/80 dark:bg-[#03032E]/90 border-t border-b border-orange-200/60 dark:border-orange-900/40"
                >
                  <td colspan="8" class="p-4 sm:p-5">
                    <div class="space-y-3 pl-8 pr-2">
                      <div class="flex items-center justify-between gap-4 flex-wrap">
                        <div class="flex items-center gap-2">
                          <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Materiais deste Documento
                          </span>
                          <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-[#FC6714]/15 text-[#FC6714]">
                            {{ doc.items?.length || 0 }} {{ (doc.items?.length || 0) === 1 ? 'item' : 'itens' }}
                          </span>
                        </div>
                        <div v-if="doc.receiver?.name || doc.driver?.name" class="text-xs text-slate-500 dark:text-slate-400">
                          <span v-if="doc.driver?.name" class="mr-3">Motorista: <strong class="text-slate-700 dark:text-slate-200">{{ doc.driver.name }}</strong></span>
                          <span v-if="doc.receiver?.name">Recebedor: <strong class="text-slate-700 dark:text-slate-200">{{ doc.receiver.name }}</strong></span>
                        </div>
                      </div>

                      <!-- Sub-tabela de Itens do Documento -->
                      <div class="rounded-lg border border-slate-200 dark:border-[#14147A] overflow-hidden bg-white dark:bg-[#06064D]/70 shadow-2xs">
                        <table class="w-full text-left text-xs border-collapse">
                          <thead>
                            <tr class="border-b border-slate-200 dark:border-[#14147A] bg-slate-100/70 dark:bg-[#03032E] text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                              <th class="py-2.5 px-4">SKU / Código</th>
                              <th class="py-2.5 px-4">Material</th>
                              <th class="py-2.5 px-4 text-right">Quantidade</th>
                              <th class="py-2.5 px-4">Rastreabilidade Serial</th>
                              <th class="py-2.5 px-4 text-right">Anexos</th>
                            </tr>
                          </thead>
                          <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/50">
                            <tr v-for="item in doc.items" :key="item.id" class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02]">
                              <!-- SKU -->
                              <td class="py-2.5 px-4 font-mono font-bold text-[#FC6714] dark:text-orange-400 whitespace-nowrap">
                                {{ item.material?.code || '-' }}
                              </td>

                              <!-- Material -->
                              <td class="py-2.5 px-4 font-medium text-slate-800 dark:text-slate-200">
                                <div>{{ item.material?.name || 'Material' }}</div>
                                <div v-if="item.material?.category" class="text-[10px] text-slate-400 mt-0.5">
                                  {{ item.material.category }}
                                </div>
                              </td>

                              <!-- Quantidade -->
                              <td class="py-2.5 px-4 text-right font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                {{ formatNumber(item.quantity) }}
                                <span class="font-normal text-[10px] text-slate-400 font-mono ml-0.5">
                                  {{ item.material?.unit?.code || 'UND' }}
                                </span>
                              </td>

                              <!-- Rastreabilidade Serial -->
                              <td class="py-2.5 px-4">
                                <div v-if="item.serials && item.serials.length > 0" class="flex flex-wrap items-center gap-1 max-w-md">
                                  <span
                                    v-for="(s, sIdx) in item.serials.slice(0, 4)"
                                    :key="s.id || sIdx"
                                    class="inline-block px-1.5 py-0.5 rounded text-[10px] font-mono font-semibold bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800"
                                  >
                                    {{ s.serial_number }}
                                  </span>
                                  <span
                                    v-if="item.serials.length > 4"
                                    class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700"
                                    :title="`${item.serials.length - 4} seriais adicionais. Clique em Detalhes para ver todos.`"
                                  >
                                    +{{ item.serials.length - 4 }}
                                  </span>
                                </div>
                                <span v-else class="text-slate-400 text-[11px] italic">Sem seriais</span>
                              </td>

                              <!-- Anexos do Item -->
                              <td class="py-2.5 px-4 text-right whitespace-nowrap">
                                <span
                                  v-if="item.attachments && item.attachments.length > 0"
                                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800"
                                >
                                  <FileText class="w-3 h-3" />
                                  {{ item.attachments.length }}
                                </span>
                                <span v-else class="text-slate-400 text-[11px]">-</span>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>

        <!-- Rodapé de Paginação de Documentos -->
        <TablePagination
          v-model:currentPage="documentCurrentPage"
          v-model:perPage="documentPerPage"
          :lastPage="documentsLastPage"
          :totalRecords="documentsTotal"
          :fromRecord="documentFromRecord"
          :toRecord="documentToRecord"
          :loading="loadingDocuments"
          @changePage="(p) => { documentCurrentPage = p; loadDocuments(); }"
          @changePerPage="(pp) => { documentPerPage = pp; documentCurrentPage = 1; loadDocuments(); }"
        />
      </div>
    </div>

    <!-- ============================================================================= -->
    <!-- COMPONENTES MODAIS INTEGRADOS (BASE MODAL CANÔNICO) -->
    <!-- ============================================================================= -->

    <!-- Modal de Detalhes da Movimentação Individual -->
    <MovementDetailsModal
      :is-open="isMovementDetailsModalOpen"
      :movement="selectedMovementForDetails"
      @close="isMovementDetailsModalOpen = false"
    />

    <!-- Modal de Detalhes do Documento / Protocolo Consolidado -->
    <DocumentDetailsModal
      :is-open="isDocumentDetailsModalOpen"
      :document-data="selectedDocumentForDetails"
      @close="isDocumentDetailsModalOpen = false"
    />

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

    <!-- Modal 4.1: Gestão Completa de Depósitos (Tabela de Apoio) -->
    <DepotCrudModal
      :is-open="isDepotCrudModalOpen"
      :depot-types-list="depotTypesList"
      @close="isDepotCrudModalOpen = false"
      @create="openCreateDepotModal"
      @edit="openEditDepotModal"
      @updated="onDepotCrudUpdated"
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
  Package, ArrowRightLeft, ArrowLeftRight, Plus, Layers, Boxes, Box, Warehouse, QrCode, RefreshCw,
  X, Search, ArrowUp, ArrowDown, ArrowUpDown, Copy, Check,
  AlertCircle, CheckCircle2, Menu, MapPin, Edit2, Tags, FolderTree, Scale, UserCheck,
  FileSpreadsheet, ExternalLink, Eye, FileText, ChevronDown, ChevronRight, FileStack
} from 'lucide-vue-next';
import BaseBreadcrumb from '@/components/common/BaseBreadcrumb.vue';
import TablePagination from '@/components/common/TablePagination.vue';
import MovementModal from '@/components/supplies/MovementModal.vue';
import MovementDetailsModal from '@/components/supplies/MovementDetailsModal.vue';
import DocumentDetailsModal from '@/components/supplies/DocumentDetailsModal.vue';
import MaterialModal from '@/components/supplies/MaterialModal.vue';
import MaterialImportModal from '@/components/supplies/MaterialImportModal.vue';
import ClusterCrudModal from '@/components/supplies/auxiliary/ClusterCrudModal.vue';
import DepotTypeCrudModal from '@/components/supplies/auxiliary/DepotTypeCrudModal.vue';
import MaterialCategoryCrudModal from '@/components/supplies/auxiliary/MaterialCategoryCrudModal.vue';
import UnitCrudModal from '@/components/supplies/auxiliary/UnitCrudModal.vue';
import MaterialOwnerCrudModal from '@/components/supplies/auxiliary/MaterialOwnerCrudModal.vue';
import DepotModal from '@/components/supplies/DepotModal.vue';
import DepotCrudModal from '@/components/supplies/auxiliary/DepotCrudModal.vue';
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
  has_serial?: boolean;
  track_batch?: boolean;
  unit?: { id: number; code: string; name: string };
}

interface Unit {
  id: number;
  code: string;
  name: string;
}

// ─── ESTADOS GERAIS DO MÓDULO ────────────────────────────────────────────────
type TabKey = 'materials' | 'serials' | 'movements';
const VALID_TABS: TabKey[] = ['materials', 'serials', 'movements'];

type MovementViewMode = 'items' | 'documents';

function getInitialMovementViewMode(): MovementViewMode {
  try {
    const hash = window.location.hash;
    if (hash && hash.includes('?')) {
      const queryPart = hash.split('?')[1];
      const params = new URLSearchParams(queryPart);
      const view = params.get('view') as MovementViewMode;
      if (view === 'documents' || view === 'items') {
        return view;
      }
    }
    const saved = localStorage.getItem('rp_supplies_movement_view_mode') as MovementViewMode;
    if (saved === 'documents' || saved === 'items') {
      return saved;
    }
  } catch (e) {
    // Fallback silencioso
  }
  return 'items';
}

function getInitialTab(): TabKey {
  try {
    const hash = window.location.hash;
    if (hash) {
      if (hash.includes('?')) {
        const queryPart = hash.split('?')[1];
        const params = new URLSearchParams(queryPart);
        const tab = params.get('tab') as TabKey;
        if (VALID_TABS.includes(tab)) {
          return tab;
        }
      }
      const slashParts = hash.replace(/^#\/?/, '').split('?')[0].split('/');
      if (slashParts.length > 1 && VALID_TABS.includes(slashParts[1] as TabKey)) {
        return slashParts[1] as TabKey;
      }
    }

    const saved = localStorage.getItem('rp_supplies_active_tab') as TabKey;
    if (saved && VALID_TABS.includes(saved)) {
      return saved;
    }
  } catch (e) {
    // Fallback silencioso
  }

  return 'materials';
}

const activeTab = ref<TabKey>(getInitialTab());
const movementViewMode = ref<MovementViewMode>(getInitialMovementViewMode());

function updateTabInUrlAndStorage(tab: TabKey) {
  try {
    localStorage.setItem('rp_supplies_active_tab', tab);

    const currentHash = window.location.hash.replace(/^#\/?/, '').split('?')[0].split('/')[0] || 'supplies';
    let newHash = `${currentHash}?tab=${tab}`;
    if (tab === 'movements' && movementViewMode.value === 'documents') {
      newHash += '&view=documents';
    }
    if (window.location.hash !== `#${newHash}`) {
      history.replaceState(null, '', `#${newHash}`);
    }
  } catch (e) {
    // Fallback silencioso
  }
}

watch(activeTab, (newTab) => {
  updateTabInUrlAndStorage(newTab);
});

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

function navigateToTab(tab: 'materials' | 'serials') {
  isHeaderMenuOpen.value = false;
  activeTab.value = tab;
}

function handleDrawerAction(action: 'transfer' | 'material' | 'cluster' | 'depot' | 'import-materials') {
  if (action === 'transfer') openMovementModal();
  else if (action === 'material') openCreateMaterialModal();
  else if (action === 'cluster') isClusterModalOpen.value = true;
  else if (action === 'depot') openDepotCrudModal();
  else if (action === 'import-materials') openMaterialImportModal();
}

// ─── CADASTROS DE APOIO: POSIÇÕES REGIONAIS & RESUMO ──────────────────────────
const clusters = ref<Cluster[]>([]);
const selectedClusterId = ref<number | null>(null);
const currentClusterSummary = ref<any>({});

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
  if (!selectedClusterId.value && clusters.value.length > 0) {
    selectedClusterId.value = clusters.value[0].id;
  }
  if (!selectedClusterId.value) return;
  try {
    const res = await fetch(`/api/v1/stock/regional-position?cluster_id=${selectedClusterId.value}`);
    const json = await res.json();
    if (json.data) {
      currentClusterSummary.value = json.data.summary || {};
    }
  } catch (err) {
    console.error('Erro ao carregar posição regional:', err);
  }
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

// ─── CADASTROS DE APOIO: DEPÓSITOS ───────────────────────────────────────────
const depots = ref<Depot[]>([]);

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

// ─── ABA MOVIMENTAÇÕES DE ESTOQUE ───────────────────────────────────────────
function setMovementViewMode(mode: MovementViewMode) {
  movementViewMode.value = mode;
  try {
    localStorage.setItem('rp_supplies_movement_view_mode', mode);
    updateTabInUrlAndStorage(activeTab.value);
  } catch (e) {
    // Silencioso
  }
  if (mode === 'documents' && documentsList.value.length === 0) {
    loadDocuments();
  }
}

// Estados de Itens Corridos
const loadingMovements = ref(false);
const movementsList = ref<any[]>([]);
const movementsTotal = ref(0);
const movementsLastPage = ref(1);
const movementCurrentPage = ref(1);
const movementPerPage = ref(20);

// Estados de Agrupamento por Documentos
const loadingDocuments = ref(false);
const documentsList = ref<any[]>([]);
const documentsTotal = ref(0);
const documentsLastPage = ref(1);
const documentCurrentPage = ref(1);
const documentPerPage = ref(15);
const expandedDocumentKeys = ref<Set<string>>(new Set());
const isDocumentDetailsModalOpen = ref(false);
const selectedDocumentForDetails = ref<any>(null);

// Filtros compartilhados
const movementSearch = ref('');
const movementTypeFilter = ref('');
const movementDepotFilter = ref('');
const movementStartDate = ref('');
const movementEndDate = ref('');
const isMovementDetailsModalOpen = ref(false);
const selectedMovementForDetails = ref<any>(null);

let movementDebounceTimeout: any = null;
function debounceLoadMovements() {
  clearTimeout(movementDebounceTimeout);
  movementDebounceTimeout = setTimeout(() => {
    movementCurrentPage.value = 1;
    documentCurrentPage.value = 1;
    loadMovements();
    loadDocuments();
  }, 300);
}

function clearMovementSearch() {
  movementSearch.value = '';
  movementCurrentPage.value = 1;
  documentCurrentPage.value = 1;
  loadMovements();
  loadDocuments();
}

function clearAllMovementFilters() {
  movementSearch.value = '';
  movementTypeFilter.value = '';
  movementDepotFilter.value = '';
  movementStartDate.value = '';
  movementEndDate.value = '';
  movementCurrentPage.value = 1;
  documentCurrentPage.value = 1;
  loadMovements();
  loadDocuments();
}

const movementFromRecord = computed(() => {
  if (movementsTotal.value === 0) return 0;
  return (movementCurrentPage.value - 1) * movementPerPage.value + 1;
});

const movementToRecord = computed(() => {
  return Math.min(movementCurrentPage.value * movementPerPage.value, movementsTotal.value);
});

const documentFromRecord = computed(() => {
  if (documentsTotal.value === 0) return 0;
  return (documentCurrentPage.value - 1) * documentPerPage.value + 1;
});

const documentToRecord = computed(() => {
  return Math.min(documentCurrentPage.value * documentPerPage.value, documentsTotal.value);
});

async function loadMovements() {
  loadingMovements.value = true;
  try {
    const params = new URLSearchParams();
    params.append('page', String(movementCurrentPage.value));
    params.append('per_page', String(movementPerPage.value));

    if (movementSearch.value.trim()) params.append('search', movementSearch.value.trim());
    if (movementTypeFilter.value) params.append('movement_type', movementTypeFilter.value);
    if (movementDepotFilter.value) params.append('depot_id', movementDepotFilter.value);
    if (movementStartDate.value) params.append('start_date', movementStartDate.value);
    if (movementEndDate.value) params.append('end_date', movementEndDate.value);

    const url = `/api/v1/stock/movements?${params.toString()}`;
    const res = await fetch(url);
    const json = await res.json();

    movementsList.value = json.data || [];
    movementsTotal.value = json.total || (json.data ? json.data.length : 0);
    movementsLastPage.value = json.last_page || 1;
  } catch (err) {
    console.error('Erro ao carregar movimentações:', err);
  } finally {
    loadingMovements.value = false;
  }
}

async function loadDocuments() {
  loadingDocuments.value = true;
  try {
    const params = new URLSearchParams();
    params.append('page', String(documentCurrentPage.value));
    params.append('per_page', String(documentPerPage.value));

    if (movementSearch.value.trim()) params.append('search', movementSearch.value.trim());
    if (movementTypeFilter.value) params.append('movement_type', movementTypeFilter.value);
    if (movementDepotFilter.value) params.append('depot_id', movementDepotFilter.value);
    if (movementStartDate.value) params.append('start_date', movementStartDate.value);
    if (movementEndDate.value) params.append('end_date', movementEndDate.value);

    const url = `/api/v1/stock/documents?${params.toString()}`;
    const res = await fetch(url);
    const json = await res.json();

    documentsList.value = json.data || [];
    documentsTotal.value = json.total || (json.data ? json.data.length : 0);
    documentsLastPage.value = json.last_page || 1;
  } catch (err) {
    console.error('Erro ao carregar documentos de movimentação:', err);
  } finally {
    loadingDocuments.value = false;
  }
}

function toggleDocumentExpanded(key: string) {
  const set = new Set(expandedDocumentKeys.value);
  if (set.has(key)) {
    set.delete(key);
  } else {
    set.add(key);
  }
  expandedDocumentKeys.value = set;
}

function openDocumentDetails(doc: any) {
  selectedDocumentForDetails.value = doc;
  isDocumentDetailsModalOpen.value = true;
}

watch([movementTypeFilter, movementDepotFilter, movementStartDate, movementEndDate], () => {
  movementCurrentPage.value = 1;
  documentCurrentPage.value = 1;
  loadMovements();
  loadDocuments();
});

watch(activeTab, (newTab) => {
  if (newTab === 'movements') {
    loadMovements();
    loadDocuments();
  }
});

function openMovementDetails(mv: any) {
  selectedMovementForDetails.value = mv;
  isMovementDetailsModalOpen.value = true;
}

function formatMovementTypeLabel(type?: string): string {
  switch (type) {
    case 'ENTRY': return 'Entrada';
    case 'EXIT': return 'Saída';
    case 'TRANSFER': return 'Transferência';
    case 'RETURN': return 'Devolução';
    case 'ADJUSTMENT': return 'Ajuste';
    default: return type || 'Movimentação';
  }
}

function formatMovementTypeBadge(type?: string): string {
  switch (type) {
    case 'ENTRY':
      return 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800';
    case 'EXIT':
      return 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800';
    case 'TRANSFER':
      return 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800';
    case 'RETURN':
      return 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800';
    case 'ADJUSTMENT':
      return 'bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800';
    default:
      return 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700';
  }
}

function formatTime(dateStr?: string): string {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  if (isNaN(d.getTime())) return '';
  return d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
}

function formatDate(dateStr?: string): string {
  if (!dateStr) return '-';
  const clean = dateStr.split('T')[0];
  const parts = clean.split('-');
  if (parts.length === 3) return `${parts[2]}/${parts[1]}/${parts[0]}`;
  return dateStr;
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
    loadMovements(),
    loadDocuments(),
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
const isDepotCrudModalOpen = ref(false);

function openDepotCrudModal() {
  isHeaderMenuOpen.value = false;
  isDepotCrudModalOpen.value = true;
}

function openCreateDepotModal() {
  isHeaderMenuOpen.value = false;
  selectedDepotForEdit.value = null;
  isDepotModalOpen.value = true;
}

function openEditDepotModal(depot: Depot) {
  selectedDepotForEdit.value = { ...depot };
  isDepotModalOpen.value = true;
}

async function onDepotCrudUpdated() {
  showToast('Depósitos sincronizados com sucesso!');
  await Promise.all([
    loadDepots(),
    loadClusters(),
    loadDepotTypes(),
  ]);
  await loadRegionalStock();
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
      loadMovements(),
      loadDocuments(),
    ]);
    await loadRegionalStock();
    showToast('Dados de suprimentos atualizados com sucesso!');
  } finally {
    loading.value = false;
  }
}

// ─── CICLO DE VIDA ────────────────────────────────────────────────────────────
function handleHashChangeForTabs() {
  const currentTabInHash = getInitialTab();
  if (currentTabInHash && currentTabInHash !== activeTab.value) {
    activeTab.value = currentTabInHash;
  }
  const currentViewInHash = getInitialMovementViewMode();
  if (currentViewInHash && currentViewInHash !== movementViewMode.value) {
    movementViewMode.value = currentViewInHash;
  }
}

onMounted(async () => {
  window.addEventListener('hashchange', handleHashChangeForTabs);
  updateTabInUrlAndStorage(activeTab.value);
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
    loadMovements(),
    loadDocuments(),
  ]);
  loadRegionalStock();
});

onBeforeUnmount(() => {
  window.removeEventListener('hashchange', handleHashChangeForTabs);
  document.removeEventListener('click', handleDocumentClick);
});
</script>
