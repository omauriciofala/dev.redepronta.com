<template>
  <div class="py-8 w-full space-y-6">
    <!-- ============================================================================= -->
    <!-- CABEÇALHO DA PÁGINA (DESIGN SYSTEM PADRÃO) -->
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
            Gestão de depósitos físicos, saldo virtual aglutinado de clusters e rastreabilidade serial
          </p>
        </div>
      </div>

      <!-- Ações do Cabeçalho -->
      <div class="flex items-center gap-2.5 shrink-0">
        <!-- Botão Primário Laranja (#FC6714) -->
        <button
          type="button"
          @click="openTransferModal()"
          class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-sm font-semibold shadow-sm transition active:scale-98 cursor-pointer focus:ring-2 focus:ring-[#FC6714] focus:ring-offset-2 focus:outline-none"
        >
          <ArrowRightLeft class="w-4 h-4" />
          <span>Nova Transferência</span>
        </button>

        <!-- Botão Secundário de Contorno -->
        <button
          type="button"
          @click="openMaterialCreateModal()"
          class="inline-flex items-center gap-2 h-10 px-3.5 rounded-lg border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#03032E] text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-white/5 text-sm font-medium shadow-2xs transition cursor-pointer"
        >
          <Plus class="w-4 h-4 text-[#FC6714]" />
          <span>Novo Material</span>
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
        <span>Posição Regional (Saldo Virtual Aglutinado)</span>
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
        <span>Catálogo de Materiais</span>
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
        <span>Depósitos & Veículos</span>
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
        <span>Seriais (ONUs & GPON)</span>
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
    <!-- ABA 1: POSIÇÃO REGIONAL (SALDO VIRTUAL AGLUTINADO) -->
    <!-- ============================================================================= -->
    <div v-if="activeTab === 'regional'" class="space-y-6">
      <!-- Seletor de Cluster Regional -->
      <div class="p-4 bg-white dark:bg-[#06064D]/50 rounded-xl border border-slate-200 dark:border-[#14147A] flex flex-wrap items-center justify-between gap-4 text-sm shadow-2xs">
        <div class="flex items-center gap-3">
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
            Cluster Regional:
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
            </button>
          </div>
        </div>

        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
          <span class="inline-flex items-center gap-1">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Aglutinação em tempo real (Base + Técnicos)
          </span>
        </div>
      </div>

      <!-- 4 Cards Métricos de Estoque do Cluster -->
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
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Saldo Físico (Base Regional)</p>
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
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">ONUs / Seriais Prontos</p>
            <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-0.5">
              {{ currentClusterSummary.total_serials_in_stock || 0 }}
            </h3>
          </div>
        </div>

        <div class="p-5 rounded-xl border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#06064D]/50 shadow-xs flex items-center gap-3.5">
          <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 shrink-0">
            <Truck class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Veículos no Cluster</p>
            <h3 class="text-xl font-bold text-slate-900 dark:text-slate-100 mt-0.5">
              {{ currentClusterSummary.vehicles_count || 0 }} técnicos
            </h3>
          </div>
        </div>
      </div>

      <!-- Barra de Filtro e Busca Rápida na Aba Regional -->
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

      <!-- TABELA CONFORTÁVEL 1: POSIÇÃO REGIONAL AGLUTINADA (SEÇÃO 7 DESIGN SYSTEM) -->
      <div class="bg-white dark:bg-[#06064D]/50 rounded-xl border border-slate-200 dark:border-[#14147A] overflow-hidden shadow-xs transition-colors duration-200">
        <div class="overflow-x-auto min-h-[280px]">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="border-b border-slate-200 dark:border-[#14147A] bg-slate-50/80 dark:bg-[#03032E]/70 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider select-none">
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

                <!-- Saldo Base Física -->
                <th
                  @click="toggleSortRegional('base_quantity')"
                  class="py-3.5 px-5 text-right cursor-pointer hover:text-[#FC6714] dark:hover:text-[#FC6714] transition"
                  title="Ordenar por Saldo na Base"
                >
                  <div class="inline-flex items-center justify-end gap-1.5 w-full">
                    <span>Saldo Base Física</span>
                    <ArrowUp v-if="regionalSortBy === 'base_quantity' && regionalSortDir === 'asc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowDown v-else-if="regionalSortBy === 'base_quantity' && regionalSortDir === 'desc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowUpDown v-else class="w-3.5 h-3.5 text-slate-400 opacity-60" />
                  </div>
                </th>

                <!-- Saldo em Veículos -->
                <th
                  @click="toggleSortRegional('vehicles_quantity')"
                  class="py-3.5 px-5 text-right cursor-pointer hover:text-[#FC6714] dark:hover:text-[#FC6714] transition"
                  title="Ordenar por Saldo em Veículos"
                >
                  <div class="inline-flex items-center justify-end gap-1.5 w-full">
                    <span>Saldo Veículos (Campo)</span>
                    <ArrowUp v-if="regionalSortBy === 'vehicles_quantity' && regionalSortDir === 'asc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowDown v-else-if="regionalSortBy === 'vehicles_quantity' && regionalSortDir === 'desc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowUpDown v-else class="w-3.5 h-3.5 text-slate-400 opacity-60" />
                  </div>
                </th>

                <!-- Saldo Virtual Aglutinado -->
                <th
                  @click="toggleSortRegional('total_virtual_quantity')"
                  class="py-3.5 px-5 text-right cursor-pointer hover:text-[#FC6714] dark:hover:text-[#FC6714] transition"
                  title="Ordenar por Saldo Virtual Aglutinado"
                >
                  <div class="inline-flex items-center justify-end gap-1.5 w-full">
                    <span>Saldo Aglutinado</span>
                    <ArrowUp v-if="regionalSortBy === 'total_virtual_quantity' && regionalSortDir === 'asc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowDown v-else-if="regionalSortBy === 'total_virtual_quantity' && regionalSortDir === 'desc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowUpDown v-else class="w-3.5 h-3.5 text-slate-400 opacity-60" />
                  </div>
                </th>

                <!-- Rastreabilidade -->
                <th class="py-3.5 px-5 text-center">Rastreabilidade</th>

                <!-- Ações -->
                <th class="py-3.5 px-5 text-right">Ação</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <!-- Loading -->
              <tr v-if="loading">
                <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                  <span class="inline-block animate-spin mr-2 text-[#FC6714]">⟳</span> Carregando posição regional aglutinada...
                </td>
              </tr>

              <!-- Vazio -->
              <tr v-else-if="filteredRegionalMaterials.length === 0">
                <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                  <div class="max-w-sm mx-auto space-y-2">
                    <Package class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto" />
                    <p class="font-medium text-slate-700 dark:text-slate-300">Nenhum saldo registrado</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                      Nenhum material com saldo registrado neste cluster regional. Realize uma transferência ou entrada.
                    </p>
                  </div>
                </td>
              </tr>

              <!-- Linhas da Tabela Confortável com py-4 px-5 -->
              <template v-else v-for="mat in paginatedRegionalMaterials" :key="mat.material_id">
                <tr class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition-colors">
                  <!-- Material & Código SKU -->
                  <td class="py-4 px-5">
                    <div class="flex items-start gap-2.5">
                      <div>
                        <div class="font-semibold text-slate-900 dark:text-slate-100 text-sm">
                          {{ mat.name }}
                        </div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                          <span class="font-mono text-xs font-bold text-[#FC6714]">{{ mat.code }}</span>
                          <button
                            @click="copyToClipboard(mat.code, 'sku-' + mat.material_id)"
                            class="p-0.5 rounded text-slate-400 hover:text-[#FC6714] transition cursor-pointer"
                            title="Copiar código SKU"
                          >
                            <Check v-if="copiedKey === 'sku-' + mat.material_id" class="w-3 h-3 text-emerald-500" />
                            <Copy v-else class="w-3 h-3" />
                          </button>
                        </div>
                      </div>
                    </div>
                  </td>

                  <!-- Unidade -->
                  <td class="py-4 px-5 text-slate-600 dark:text-slate-300">
                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                      {{ mat.unit }}
                    </span>
                  </td>

                  <!-- Saldo Base Física -->
                  <td class="py-4 px-5 text-right font-mono font-medium text-slate-800 dark:text-slate-200">
                    {{ formatNumber(mat.base_quantity) }}
                  </td>

                  <!-- Saldo Veículos (Campo) com Botão Expansível -->
                  <td class="py-4 px-5 text-right font-mono font-medium text-blue-600 dark:text-blue-400">
                    <button
                      type="button"
                      @click="toggleExpand(mat.material_id)"
                      class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md hover:bg-blue-50 dark:hover:bg-blue-950/40 transition cursor-pointer"
                      title="Clique para expandir o detalhamento por veículo"
                    >
                      <Truck class="w-3.5 h-3.5 text-blue-500" />
                      <span>{{ formatNumber(mat.vehicles_quantity) }}</span>
                      <ChevronDown class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180 text-[#FC6714]': expandedRows.includes(mat.material_id) }" />
                    </button>
                  </td>

                  <!-- Saldo Virtual Aglutinado -->
                  <td class="py-4 px-5 text-right">
                    <div class="font-mono text-sm font-bold" :class="mat.is_low_stock ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'">
                      {{ formatNumber(mat.total_virtual_quantity) }}
                    </div>
                    <div v-if="mat.is_low_stock" class="text-[10px] font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-tight mt-0.5">
                      Estoque Baixo
                    </div>
                  </td>

                  <!-- Rastreabilidade Serial -->
                  <td class="py-4 px-5 text-center">
                    <span
                      v-if="mat.has_serial"
                      class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200/80 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/60"
                    >
                      <QrCode class="w-3.5 h-3.5" />
                      <span>{{ mat.serials_in_stock }} ONUs</span>
                    </span>
                    <span
                      v-else
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400"
                    >
                      Convencional
                    </span>
                  </td>

                  <!-- Ações Rápidas Confortáveis -->
                  <td class="py-4 px-5 text-right">
                    <button
                      type="button"
                      @click="openTransferModal(mat)"
                      class="h-8 px-3 text-xs font-medium rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-orange-50 hover:text-[#FC6714] hover:border-[#FC6714] dark:hover:bg-[#FC6714]/10 dark:hover:text-[#FC6714] transition cursor-pointer inline-flex items-center gap-1.5"
                    >
                      <ArrowRightLeft class="w-3.5 h-3.5" />
                      <span>Transferir</span>
                    </button>
                  </td>
                </tr>

                <!-- Linha Expansível: Detalhamento por Veículo / Técnico -->
                <tr v-if="expandedRows.includes(mat.material_id)" class="bg-slate-50/60 dark:bg-[#03032E]/40">
                  <td colspan="7" class="py-4 px-6">
                    <div class="pl-4 border-l-3 border-[#FC6714] space-y-2.5">
                      <div class="flex items-center justify-between">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                          <Layers class="w-3.5 h-3.5 text-[#FC6714]" />
                          Distribuição Física nos Depósitos do Cluster:
                        </p>
                        <span class="text-[11px] text-slate-400">Total de locais: {{ mat.breakdown?.length || 0 }}</span>
                      </div>

                      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 text-xs">
                        <div
                          v-for="d in mat.breakdown"
                          :key="d.depot_id"
                          class="p-3.5 rounded-xl bg-white dark:bg-[#06064D] border border-slate-200 dark:border-[#14147A] shadow-2xs flex items-center justify-between"
                        >
                          <div>
                            <div class="flex items-center gap-1.5 font-semibold text-slate-900 dark:text-slate-100">
                              <Truck v-if="d.depot_type === 'VEHICLE'" class="w-3.5 h-3.5 text-blue-500" />
                              <Warehouse v-else class="w-3.5 h-3.5 text-emerald-500" />
                              <span>{{ d.depot_name }}</span>
                            </div>
                            <p v-if="d.responsible_name" class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                              Técnico: <strong class="text-slate-700 dark:text-slate-200">{{ d.responsible_name }}</strong>
                            </p>
                          </div>
                          <span class="font-bold text-slate-900 dark:text-slate-100 font-mono text-sm">
                            {{ formatNumber(d.quantity) }} {{ mat.unit }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>

        <!-- Rodapé de Paginação Avançado -->
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
            placeholder="Buscar por código SKU, nome do produto ou categoria..."
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

        <div class="flex items-center gap-3">
          <!-- Filtro de Tipo de Rastreio -->
          <select
            v-model="materialFilterType"
            class="h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-xs font-semibold focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer"
          >
            <option value="all">Todos os Tipos de Rastreio</option>
            <option value="serialized">Apenas Serializados (ONUs)</option>
            <option value="standard">Apenas Convencionais</option>
          </select>

          <span class="text-xs text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">
            {{ filteredMaterials.length }} materiais
          </span>
        </div>
      </div>

      <!-- TABELA CONFORTÁVEL 2: MATERIAIS (SEÇÃO 7 DESIGN SYSTEM) -->
      <div class="bg-white dark:bg-[#06064D]/50 rounded-xl border border-slate-200 dark:border-[#14147A] overflow-hidden shadow-xs transition-colors duration-200">
        <div class="overflow-x-auto min-h-[280px]">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="border-b border-slate-200 dark:border-[#14147A] bg-slate-50/80 dark:bg-[#03032E]/70 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider select-none">
                <!-- Código SKU -->
                <th
                  @click="toggleSortMaterial('code')"
                  class="py-3.5 px-5 cursor-pointer hover:text-[#FC6714] dark:hover:text-[#FC6714] transition"
                  title="Ordenar por Código SKU"
                >
                  <div class="inline-flex items-center gap-1.5">
                    <span>Código / SKU</span>
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
                    <span>Nome do Material</span>
                    <ArrowUp v-if="materialSortBy === 'name' && materialSortDir === 'asc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowDown v-else-if="materialSortBy === 'name' && materialSortDir === 'desc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowUpDown v-else class="w-3.5 h-3.5 text-slate-400 opacity-60" />
                  </div>
                </th>

                <!-- Categoria -->
                <th class="py-3.5 px-5">Categoria</th>

                <!-- Unidade -->
                <th class="py-3.5 px-5">Unidade</th>

                <!-- Tipo Rastreio -->
                <th class="py-3.5 px-5 text-center">Tipo Rastreio</th>

                <!-- Custo Médio Unitário -->
                <th
                  @click="toggleSortMaterial('unit_cost')"
                  class="py-3.5 px-5 text-right cursor-pointer hover:text-[#FC6714] dark:hover:text-[#FC6714] transition"
                  title="Ordenar por Custo Médio"
                >
                  <div class="inline-flex items-center justify-end gap-1.5 w-full">
                    <span>Custo Médio</span>
                    <ArrowUp v-if="materialSortBy === 'unit_cost' && materialSortDir === 'asc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowDown v-else-if="materialSortBy === 'unit_cost' && materialSortDir === 'desc'" class="w-3.5 h-3.5 text-[#FC6714]" />
                    <ArrowUpDown v-else class="w-3.5 h-3.5 text-slate-400 opacity-60" />
                  </div>
                </th>

                <!-- Estoque Mínimo -->
                <th class="py-3.5 px-5 text-right">Estoque Mín.</th>

                <!-- Ações -->
                <th class="py-3.5 px-5 text-right">Ação</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <tr v-if="filteredMaterials.length === 0">
                <td colspan="8" class="py-12 text-center text-slate-400 text-sm">
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
                v-for="m in paginatedMaterials"
                :key="m.id"
                class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition-colors"
              >
                <!-- Código SKU -->
                <td class="py-4 px-5">
                  <div class="flex items-center gap-1.5">
                    <span class="font-mono font-bold text-xs text-[#FC6714]">{{ m.code }}</span>
                    <button
                      @click="copyToClipboard(m.code, 'sku-cat-' + m.id)"
                      class="p-0.5 rounded text-slate-400 hover:text-[#FC6714] transition cursor-pointer"
                      title="Copiar código SKU"
                    >
                      <Check v-if="copiedKey === 'sku-cat-' + m.id" class="w-3 h-3 text-emerald-500" />
                      <Copy v-else class="w-3 h-3" />
                    </button>
                  </div>
                </td>

                <!-- Nome / Descrição -->
                <td class="py-4 px-5">
                  <div class="font-semibold text-slate-900 dark:text-slate-100 text-sm">
                    {{ m.name }}
                  </div>
                  <div v-if="m.description" class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-xs mt-0.5">
                    {{ m.description }}
                  </div>
                </td>

                <!-- Categoria -->
                <td class="py-4 px-5 text-slate-600 dark:text-slate-300">
                  <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                    {{ m.category || 'Geral' }}
                  </span>
                </td>

                <!-- Unidade -->
                <td class="py-4 px-5 text-slate-700 dark:text-slate-300 font-medium">
                  {{ m.unit?.code || 'UND' }}
                </td>

                <!-- Tipo Rastreio -->
                <td class="py-4 px-5 text-center">
                  <span
                    v-if="m.has_serial"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200/80 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/60"
                  >
                    <QrCode class="w-3 h-3" />
                    <span>Serializado (ONU)</span>
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400"
                  >
                    Convencional
                  </span>
                </td>

                <!-- Custo Médio -->
                <td class="py-4 px-5 text-right font-mono font-medium text-slate-700 dark:text-slate-300">
                  R$ {{ formatNumber(m.unit_cost) }}
                </td>

                <!-- Estoque Mínimo -->
                <td class="py-4 px-5 text-right font-mono font-medium text-slate-700 dark:text-slate-300">
                  {{ formatNumber(m.min_stock) }}
                </td>

                <!-- Ação -->
                <td class="py-4 px-5 text-right">
                  <button
                    type="button"
                    @click="openTransferModal(m)"
                    class="h-8 px-3 text-xs font-medium rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-orange-50 hover:text-[#FC6714] hover:border-[#FC6714] dark:hover:bg-[#FC6714]/10 dark:hover:text-[#FC6714] transition cursor-pointer inline-flex items-center gap-1.5"
                  >
                    <ArrowRightLeft class="w-3.5 h-3.5" />
                    <span>Movimentar</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Rodapé de Paginação Avançado -->
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
    <!-- ABA 3: DEPÓSITOS & VEÍCULOS -->
    <!-- ============================================================================= -->
    <div v-if="activeTab === 'depots'" class="space-y-4">
      <!-- Barra de Filtros e Busca de Depósitos -->
      <div class="p-4 bg-white dark:bg-[#06064D]/50 rounded-xl border border-slate-200 dark:border-[#14147A] flex flex-wrap items-center justify-between gap-4 text-sm shadow-2xs">
        <div class="relative flex-1 min-w-[280px]">
          <Search class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400 pointer-events-none" />
          <input
            type="text"
            v-model="depotSearch"
            placeholder="Buscar por nome do depósito, placa do veículo ou técnico responsável..."
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
          <!-- Filtro de Tipo de Depósito -->
          <select
            v-model="depotFilterType"
            class="h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-xs font-semibold focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer"
          >
            <option value="all">Todos os Tipos</option>
            <option value="CENTRAL">Almoxarifados Centrais</option>
            <option value="REGIONAL_BASE">Bases Regionais</option>
            <option value="VEHICLE">Veículos em Campo</option>
            <option value="LAB_REPAIR">Laboratórios de Reparo</option>
          </select>

          <span class="text-xs text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">
            {{ filteredDepots.length }} depósitos
          </span>
        </div>
      </div>

      <!-- TABELA CONFORTÁVEL 3: DEPÓSITOS & VEÍCULOS (SEÇÃO 7 DESIGN SYSTEM) -->
      <div class="bg-white dark:bg-[#06064D]/50 rounded-xl border border-slate-200 dark:border-[#14147A] overflow-hidden shadow-xs transition-colors duration-200">
        <div class="overflow-x-auto min-h-[280px]">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="border-b border-slate-200 dark:border-[#14147A] bg-slate-50/80 dark:bg-[#03032E]/70 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider select-none">
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

                <!-- Cluster Regional -->
                <th class="py-3.5 px-5">Cluster Vinculado</th>

                <!-- Veículo / Placa -->
                <th class="py-3.5 px-5">Veículo / Placa</th>

                <!-- Responsável / Técnico -->
                <th class="py-3.5 px-5">Técnico / Responsável</th>

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
                    <p class="text-xs text-slate-500 dark:text-slate-400">Verifique os filtros selecionados.</p>
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
                    <div
                      class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                      :class="d.type === 'VEHICLE' ? 'bg-blue-50 dark:bg-blue-950/60 text-blue-600' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600'"
                    >
                      <Truck v-if="d.type === 'VEHICLE'" class="w-5 h-5" />
                      <Warehouse v-else class="w-5 h-5" />
                    </div>
                    <div>
                      <div class="font-semibold text-slate-900 dark:text-slate-100 text-sm">
                        {{ d.name }}
                      </div>
                      <div class="font-mono text-xs text-[#FC6714] font-medium mt-0.5">
                        {{ d.code }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Tipo -->
                <td class="py-4 px-5">
                  <span
                    class="px-2.5 py-1 rounded-full text-xs font-semibold border"
                    :class="{
                      'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/60': d.type === 'CENTRAL',
                      'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60': d.type === 'REGIONAL_BASE',
                      'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/60': d.type === 'VEHICLE',
                      'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60': d.type === 'LAB_REPAIR',
                    }"
                  >
                    {{ formatDepotType(d.type) }}
                  </span>
                </td>

                <!-- Cluster Vinculado -->
                <td class="py-4 px-5 text-slate-700 dark:text-slate-300 font-medium">
                  {{ d.cluster?.name || '-' }}
                </td>

                <!-- Veículo / Placa -->
                <td class="py-4 px-5 font-mono text-sm">
                  <div v-if="d.vehicle_plate" class="flex items-center gap-1.5">
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ d.vehicle_plate }}</span>
                    <button
                      @click="copyToClipboard(d.vehicle_plate, 'plate-' + d.id)"
                      class="p-0.5 rounded text-slate-400 hover:text-[#FC6714] transition cursor-pointer"
                      title="Copiar placa"
                    >
                      <Check v-if="copiedKey === 'plate-' + d.id" class="w-3 h-3 text-emerald-500" />
                      <Copy v-else class="w-3 h-3" />
                    </button>
                  </div>
                  <span v-else class="text-xs text-slate-400 italic">-</span>
                </td>

                <!-- Responsável / Técnico -->
                <td class="py-4 px-5 text-slate-700 dark:text-slate-300 font-medium">
                  {{ d.responsible_person?.name || '-' }}
                </td>

                <!-- Ações -->
                <td class="py-4 px-5 text-right">
                  <button
                    type="button"
                    @click="openTransferFromDepot(d)"
                    class="h-8 px-3 text-xs font-medium rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-orange-50 hover:text-[#FC6714] hover:border-[#FC6714] dark:hover:bg-[#FC6714]/10 dark:hover:text-[#FC6714] transition cursor-pointer inline-flex items-center gap-1.5"
                  >
                    <ArrowRightLeft class="w-3.5 h-3.5" />
                    <span>Transferir</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Rodapé de Paginação Avançado -->
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
    <!-- ABA 4: RASTREABILIDADE DE SERIAIS (ONUs & GPON) -->
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
            placeholder="Buscar por número de série (GPON SN) ou MAC address..."
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
            <option value="IN_TRANSIT">Em Trânsito / Veículo</option>
            <option value="INSTALLED_CLIENT">Instalado no Cliente</option>
            <option value="DEFECTIVE">Com Defeito / Reparo</option>
            <option value="RESERVED">Reservado para OS</option>
          </select>

          <span class="text-xs text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">
            {{ serialsList.length }} seriais
          </span>
        </div>
      </div>

      <!-- TABELA CONFORTÁVEL 4: SERIAIS (ONUs) (SEÇÃO 7 DESIGN SYSTEM) -->
      <div class="bg-white dark:bg-[#06064D]/50 rounded-xl border border-slate-200 dark:border-[#14147A] overflow-hidden shadow-xs transition-colors duration-200">
        <div class="overflow-x-auto min-h-[280px]">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="border-b border-slate-200 dark:border-[#14147A] bg-slate-50/80 dark:bg-[#03032E]/70 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider select-none">
                <!-- Número de Série -->
                <th
                  @click="toggleSortSerial('serial_number')"
                  class="py-3.5 px-5 cursor-pointer hover:text-[#FC6714] dark:hover:text-[#FC6714] transition"
                  title="Ordenar por Serial"
                >
                  <div class="inline-flex items-center gap-1.5">
                    <span>Número de Série (GPON SN)</span>
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
                <th class="py-3.5 px-5">Localização Atual (Depósito)</th>

                <!-- Responsável -->
                <th class="py-3.5 px-5">Responsável</th>

                <!-- Status -->
                <th class="py-3.5 px-5 text-center">Status Operacional</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <tr v-if="filteredSerials.length === 0">
                <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                  <div class="max-w-sm mx-auto space-y-2">
                    <QrCode class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto" />
                    <p class="font-medium text-slate-700 dark:text-slate-300">Nenhum serial encontrado</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                      Não há seriais com os critérios de filtro pesquisados.
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
                      title="Copiar Serial GPON"
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
                  <div class="font-mono text-xs text-slate-400">
                    SKU: {{ s.material?.code }}
                  </div>
                </td>

                <!-- Depósito Atual -->
                <td class="py-4 px-5">
                  <div class="flex items-center gap-2">
                    <Truck v-if="s.current_depot?.type === 'VEHICLE'" class="w-4 h-4 text-blue-500 shrink-0" />
                    <Warehouse v-else class="w-4 h-4 text-emerald-500 shrink-0" />
                    <span class="font-medium text-slate-800 dark:text-slate-200 text-sm">
                      {{ s.current_depot?.name || '-' }}
                    </span>
                  </div>
                </td>

                <!-- Responsável -->
                <td class="py-4 px-5 text-slate-700 dark:text-slate-300 font-medium">
                  {{ s.current_depot?.responsible_person?.name || '-' }}
                </td>

                <!-- Status com Badge Semântico -->
                <td class="py-4 px-5 text-center">
                  <span
                    class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold border"
                    :class="formatSerialStatusBadge(s.status)"
                  >
                    {{ formatSerialStatusLabel(s.status) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Rodapé de Paginação Avançado -->
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
    <!-- MODAL 1: TRANSFERÊNCIA ATÔMICA DE ESTOQUE (DESIGN SYSTEM SEÇÃO 8) -->
    <!-- ============================================================================= -->
    <div
      v-if="showTransferModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 dark:bg-slate-950/80 backdrop-blur-xs animate-in fade-in duration-150"
    >
      <div class="w-full max-w-xl rounded-2xl border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#06064D] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <!-- Header do Modal -->
        <div class="px-6 py-4 border-b border-slate-200 dark:border-[#14147A] flex items-center justify-between bg-slate-50/80 dark:bg-[#03032E]/70">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-[#FC6714]/10 text-[#FC6714] flex items-center justify-center">
              <ArrowRightLeft class="w-4 h-4" />
            </div>
            <div>
              <h2 class="text-base font-bold font-heading text-[#06064D] dark:text-white">
                Transferência Atômica de Materiais
              </h2>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                Movimentação rastreável entre almoxarifados centrais, bases e veículos de técnicos
              </p>
            </div>
          </div>
          <button
            type="button"
            @click="showTransferModal = false"
            class="p-2 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/10 transition cursor-pointer"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Formulário do Modal -->
        <form @submit.prevent="submitTransfer" class="p-6 space-y-4 overflow-y-auto flex-1">
          <div v-if="transferError" class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 text-xs font-medium flex items-center gap-2">
            <AlertCircle class="w-4 h-4 shrink-0 text-rose-600" />
            <span>{{ transferError }}</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Depósito de Origem *
              </label>
              <select
                v-model="transferForm.source_depot_id"
                required
                @change="onSourceDepotChange"
                class="w-full h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer"
              >
                <option value="" disabled>Selecione a origem...</option>
                <option v-for="d in depots" :key="d.id" :value="d.id">
                  {{ d.name }} ({{ formatDepotType(d.type) }})
                </option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Depósito de Destino *
              </label>
              <select
                v-model="transferForm.destination_depot_id"
                required
                class="w-full h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer"
              >
                <option value="" disabled>Selecione o destino...</option>
                <option v-for="d in destinationDepotsList" :key="d.id" :value="d.id">
                  {{ d.name }} ({{ formatDepotType(d.type) }})
                </option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              Material a Transferir *
            </label>
            <select
              v-model="transferForm.material_id"
              required
              @change="onMaterialChange"
              class="w-full h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer"
            >
              <option value="" disabled>Selecione o item...</option>
              <option v-for="m in materials" :key="m.id" :value="m.id">
                {{ m.code }} — {{ m.name }} ({{ m.has_serial ? 'Serializado' : 'Convencional' }})
              </option>
            </select>
          </div>

          <!-- Seleção de Seriais se for material serializado (ONU) -->
          <div
            v-if="selectedMaterialForTransfer?.has_serial"
            class="space-y-2 p-3.5 rounded-xl bg-purple-50/60 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-800/60"
          >
            <div class="flex items-center justify-between">
              <label class="block text-xs font-bold text-purple-800 dark:text-purple-300 uppercase tracking-wider">
                Selecione os Seriais em Estoque na Origem:
              </label>
              <span class="text-xs font-semibold text-purple-700 dark:text-purple-300">
                {{ transferForm.serial_ids.length }} de {{ availableSerialsForTransfer.length }} selecionado(s)
              </span>
            </div>

            <div v-if="loadingSerialsTransfer" class="py-4 text-center text-xs text-purple-600 dark:text-purple-400">
              <span class="inline-block animate-spin mr-1">⟳</span> Carregando seriais disponíveis no depósito...
            </div>
            <div v-else-if="availableSerialsForTransfer.length === 0" class="py-4 text-center text-xs text-slate-500 italic">
              Nenhum serial disponível no depósito de origem para este material.
            </div>
            <div v-else class="max-h-40 overflow-y-auto space-y-1.5 pr-1 divide-y divide-purple-100 dark:divide-purple-900/40">
              <label
                v-for="s in availableSerialsForTransfer"
                :key="s.id"
                class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-purple-100/50 dark:hover:bg-purple-900/30 text-xs cursor-pointer select-none"
              >
                <input
                  type="checkbox"
                  :value="s.id"
                  v-model="transferForm.serial_ids"
                  @change="transferForm.quantity = transferForm.serial_ids.length"
                  class="rounded text-[#FC6714] focus:ring-[#FC6714] w-4 h-4"
                />
                <span class="font-mono font-bold text-slate-900 dark:text-slate-100">{{ s.serial_number }}</span>
                <span v-if="s.mac_address" class="text-slate-500 font-mono text-[11px]">({{ s.mac_address }})</span>
              </label>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Quantidade a Transferir *
              </label>
              <input
                type="number"
                step="0.01"
                min="0.01"
                v-model="transferForm.quantity"
                :readonly="selectedMaterialForTransfer?.has_serial"
                required
                class="w-full h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25"
                :class="{ 'opacity-70 cursor-not-allowed': selectedMaterialForTransfer?.has_serial }"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Documento de Ref. / OFS
              </label>
              <input
                type="text"
                v-model="transferForm.document_ref"
                placeholder="Ex: REQ-0042 ou OS-1892"
                class="w-full h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              Observações & Justificativa
            </label>
            <textarea
              v-model="transferForm.notes"
              rows="2"
              placeholder="Descreva o motivo da movimentação física..."
              class="w-full p-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25"
            ></textarea>
          </div>

          <!-- Footer do Modal -->
          <div class="pt-4 border-t border-slate-200 dark:border-[#14147A] flex items-center justify-end gap-3">
            <button
              type="button"
              @click="showTransferModal = false"
              class="h-10 px-4 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition cursor-pointer"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="submittingTransfer"
              class="h-10 px-5 bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-sm font-semibold rounded-lg shadow-sm transition active:scale-98 disabled:opacity-50 cursor-pointer flex items-center gap-2"
            >
              <span v-if="submittingTransfer" class="inline-block animate-spin mr-1">⟳</span>
              <span>{{ submittingTransfer ? 'Processando...' : 'Confirmar Transferência' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ============================================================================= -->
    <!-- MODAL 2: CADASTRO DE NOVO MATERIAL (DESIGN SYSTEM SEÇÃO 8) -->
    <!-- ============================================================================= -->
    <div
      v-if="showMaterialModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 dark:bg-slate-950/80 backdrop-blur-xs animate-in fade-in duration-150"
    >
      <div class="w-full max-w-lg rounded-2xl border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#06064D] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <!-- Header do Modal -->
        <div class="px-6 py-4 border-b border-slate-200 dark:border-[#14147A] flex items-center justify-between bg-slate-50/80 dark:bg-[#03032E]/70">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-[#FC6714]/10 text-[#FC6714] flex items-center justify-center">
              <Plus class="w-4 h-4" />
            </div>
            <div>
              <h2 class="text-base font-bold font-heading text-[#06064D] dark:text-white">
                Novo Material de Estoque
              </h2>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                Cadastro no catálogo oficial de suprimentos com parametrização serial
              </p>
            </div>
          </div>
          <button
            type="button"
            @click="showMaterialModal = false"
            class="p-2 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/10 transition cursor-pointer"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Formulário do Modal -->
        <form @submit.prevent="submitMaterial" class="p-6 space-y-4 overflow-y-auto flex-1">
          <div v-if="materialError" class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 text-xs font-medium flex items-center gap-2">
            <AlertCircle class="w-4 h-4 shrink-0 text-rose-600" />
            <span>{{ materialError }}</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-1">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Código / SKU *
              </label>
              <input
                type="text"
                v-model="materialForm.code"
                required
                placeholder="Ex: ONU-GPON-01"
                class="w-full h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 font-mono text-sm uppercase focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Nome do Material *
              </label>
              <input
                type="text"
                v-model="materialForm.name"
                required
                placeholder="Ex: ONU GPON Wi-Fi AC 1200"
                class="w-full h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Categoria *
              </label>
              <input
                type="text"
                v-model="materialForm.category"
                required
                placeholder="Ex: ONUs, Cabos, Conectores"
                class="w-full h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Unidade de Medida *
              </label>
              <select
                v-model="materialForm.unit_id"
                required
                class="w-full h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25 cursor-pointer"
              >
                <option value="" disabled>Selecione a unidade...</option>
                <option v-for="u in unitsList" :key="u.id" :value="u.id">
                  {{ u.code }} — {{ u.name }}
                </option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Custo Médio Unitário (R$)
              </label>
              <input
                type="number"
                step="0.01"
                min="0"
                v-model="materialForm.unit_cost"
                class="w-full h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Estoque Mínimo de Alerta
              </label>
              <input
                type="number"
                step="1"
                min="0"
                v-model="materialForm.min_stock"
                class="w-full h-11 px-3 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:border-[#FC6714] focus:ring-2 focus:ring-[#FC6714]/25"
              />
            </div>
          </div>

          <!-- Opções Booleanas de Rastreio -->
          <div class="p-3.5 rounded-xl border border-slate-200 dark:border-[#14147A] bg-slate-50/50 dark:bg-[#03032E]/30 space-y-2">
            <label class="flex items-center gap-2.5 cursor-pointer text-xs font-semibold text-slate-800 dark:text-slate-200">
              <input
                type="checkbox"
                v-model="materialForm.has_serial"
                class="rounded text-[#FC6714] focus:ring-[#FC6714] w-4 h-4"
              />
              <span>Rastreabilidade Serial Obrigatória (Ex: ONUs, Roteadores, OLTs)</span>
            </label>

            <label class="flex items-center gap-2.5 cursor-pointer text-xs font-semibold text-slate-800 dark:text-slate-200">
              <input
                type="checkbox"
                v-model="materialForm.track_batch"
                class="rounded text-[#FC6714] focus:ring-[#FC6714] w-4 h-4"
              />
              <span>Rastreabilidade por Número de Lote / Bobina (Cabos ópticos)</span>
            </label>
          </div>

          <!-- Footer do Modal -->
          <div class="pt-4 border-t border-slate-200 dark:border-[#14147A] flex items-center justify-end gap-3">
            <button
              type="button"
              @click="showMaterialModal = false"
              class="h-10 px-4 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition cursor-pointer"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="submittingMaterial"
              class="h-10 px-5 bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-sm font-semibold rounded-lg shadow-sm transition active:scale-98 disabled:opacity-50 cursor-pointer flex items-center gap-2"
            >
              <span v-if="submittingMaterial" class="inline-block animate-spin mr-1">⟳</span>
              <span>{{ submittingMaterial ? 'Salvando...' : 'Salvar Material' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

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
import { ref, computed, onMounted } from 'vue';
import {
  Package, ArrowRightLeft, Plus, Layers, Boxes, Warehouse, QrCode, RefreshCw,
  Truck, ChevronDown, X, Search, ArrowUp, ArrowDown, ArrowUpDown, Copy, Check,
  AlertCircle, CheckCircle2
} from 'lucide-vue-next';
import TablePagination from '@/components/common/TablePagination.vue';

// ─── INTERFACES DE DOMÍNIO ───────────────────────────────────────────────────
interface Cluster {
  id: number;
  name: string;
  code: string;
  color?: string;
  description?: string;
}

interface Depot {
  id: number;
  name: string;
  code: string;
  type: string;
  vehicle_plate?: string;
  cluster?: { id: number; name: string };
  responsible_person?: { id: number; name: string };
}

interface Material {
  id: number;
  code: string;
  name: string;
  description?: string;
  category?: string;
  unit_cost: number;
  min_stock: number;
  has_serial: boolean;
  unit?: { id: number; code: string; name: string };
}

interface Unit {
  id: number;
  code: string;
  name: string;
}

// ─── ESTADOS GERAIS ──────────────────────────────────────────────────────────
const activeTab = ref<'regional' | 'materials' | 'depots' | 'serials'>('regional');
const loading = ref(false);
const toastMessage = ref('');
const toastType = ref<'success' | 'error'>('success');
const copiedKey = ref<string | null>(null);

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
  const map: Record<string, string> = {
    CENTRAL: 'Almoxarifado Central',
    REGIONAL_BASE: 'Base Regional',
    VEHICLE: 'Veículo em Campo',
    LAB_REPAIR: 'Laboratório de Reparo',
  };
  return map[type] || type;
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

// ─── ABA 1: POSIÇÃO REGIONAL (SALDO VIRTUAL AGLUTINADO) ──────────────────────
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
    console.error('Erro ao carregar clusters:', err);
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
    list = list.filter(m => m.has_serial);
  } else if (materialFilterType.value === 'standard') {
    list = list.filter(m => !m.has_serial);
  }

  if (materialSearch.value.trim()) {
    const q = materialSearch.value.toLowerCase();
    list = list.filter(m =>
      m.code.toLowerCase().includes(q) ||
      m.name.toLowerCase().includes(q) ||
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
    const res = await fetch('/api/v1/materials');
    const json = await res.json();
    materials.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar materiais:', err);
  }
}

async function loadUnits() {
  try {
    const res = await fetch('/api/v1/materials/units');
    const json = await res.json();
    unitsList.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar unidades:', err);
  }
}

// ─── ABA 3: DEPÓSITOS & VEÍCULOS ─────────────────────────────────────────────
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
      (d.vehicle_plate && d.vehicle_plate.toLowerCase().includes(q)) ||
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

// ─── ABA 4: SERIAIS DE ONUs ───────────────────────────────────────────────────
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

// ─── MODAL DE TRANSFERÊNCIA DE ESTOQUE (ATÔMICA) ─────────────────────────────
const showTransferModal = ref(false);
const submittingTransfer = ref(false);
const transferError = ref('');
const loadingSerialsTransfer = ref(false);
const availableSerialsForTransfer = ref<any[]>([]);
const selectedMaterialForTransfer = ref<Material | null>(null);

const transferForm = ref({
  source_depot_id: '',
  destination_depot_id: '',
  material_id: '',
  quantity: 1,
  serial_ids: [] as number[],
  document_ref: '',
  notes: '',
});

const destinationDepotsList = computed(() => {
  return depots.value.filter(d => d.id !== Number(transferForm.value.source_depot_id));
});

function openTransferModal(material?: any) {
  transferError.value = '';
  transferForm.value = {
    source_depot_id: depots.value[0]?.id ? String(depots.value[0].id) : '',
    destination_depot_id: depots.value[1]?.id ? String(depots.value[1].id) : '',
    material_id: material?.material_id ? String(material.material_id) : (material?.id ? String(material.id) : (materials.value[0]?.id ? String(materials.value[0].id) : '')),
    quantity: 1,
    serial_ids: [],
    document_ref: '',
    notes: '',
  };
  onMaterialChange();
  showTransferModal.value = true;
}

function openTransferFromDepot(depot: Depot) {
  transferError.value = '';
  const otherDepot = depots.value.find(d => d.id !== depot.id);
  transferForm.value = {
    source_depot_id: String(depot.id),
    destination_depot_id: otherDepot ? String(otherDepot.id) : '',
    material_id: materials.value[0]?.id ? String(materials.value[0].id) : '',
    quantity: 1,
    serial_ids: [],
    document_ref: '',
    notes: '',
  };
  onMaterialChange();
  showTransferModal.value = true;
}

function onSourceDepotChange() {
  loadSerialsForTransfer();
}

function onMaterialChange() {
  const matId = Number(transferForm.value.material_id);
  selectedMaterialForTransfer.value = materials.value.find(m => m.id === matId) || null;
  loadSerialsForTransfer();
}

async function loadSerialsForTransfer() {
  transferForm.value.serial_ids = [];
  if (!selectedMaterialForTransfer.value?.has_serial) {
    availableSerialsForTransfer.value = [];
    return;
  }
  const depotId = transferForm.value.source_depot_id;
  const matId = transferForm.value.material_id;
  if (!depotId || !matId) return;

  loadingSerialsTransfer.value = true;
  try {
    const res = await fetch(`/api/v1/stock/serials?depot_id=${depotId}&material_id=${matId}&status=IN_STOCK`);
    const json = await res.json();
    availableSerialsForTransfer.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar seriais para transferência:', err);
  } finally {
    loadingSerialsTransfer.value = false;
  }
}

async function submitTransfer() {
  transferError.value = '';
  submittingTransfer.value = true;
  try {
    const res = await fetch('/api/v1/stock/transfer', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(transferForm.value),
    });

    const json = await res.json();
    if (!res.ok) {
      transferError.value = json.message || 'Erro ao realizar transferência.';
      return;
    }

    showTransferModal.value = false;
    showToast('Transferência atômica concluída com sucesso!');
    await Promise.all([
      loadRegionalStock(),
      loadMaterials(),
      loadSerials(),
    ]);
  } catch (err: any) {
    transferError.value = err.message || 'Erro de conexão com o servidor.';
  } finally {
    submittingTransfer.value = false;
  }
}

// ─── MODAL DE CADASTRO DE MATERIAL ────────────────────────────────────────────
const showMaterialModal = ref(false);
const submittingMaterial = ref(false);
const materialError = ref('');

const materialForm = ref({
  code: '',
  name: '',
  category: '',
  unit_id: '',
  unit_cost: 0,
  min_stock: 5,
  has_serial: false,
  track_batch: false,
});

function openMaterialCreateModal() {
  materialError.value = '';
  materialForm.value = {
    code: '',
    name: '',
    category: 'Geral',
    unit_id: unitsList.value[0]?.id ? String(unitsList.value[0].id) : '',
    unit_cost: 0,
    min_stock: 5,
    has_serial: false,
    track_batch: false,
  };
  showMaterialModal.value = true;
}

async function submitMaterial() {
  materialError.value = '';
  submittingMaterial.value = true;
  try {
    const res = await fetch('/api/v1/materials', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(materialForm.value),
    });

    const json = await res.json();
    if (!res.ok) {
      materialError.value = json.message || 'Erro ao cadastrar material.';
      return;
    }

    showMaterialModal.value = false;
    showToast('Material cadastrado com sucesso no catálogo!');
    await Promise.all([
      loadMaterials(),
      loadRegionalStock(),
    ]);
  } catch (err: any) {
    materialError.value = err.message || 'Erro de conexão ao salvar material.';
  } finally {
    submittingMaterial.value = false;
  }
}

// ─── RECARREGAR ABA ATUAL ─────────────────────────────────────────────────────
async function refreshCurrentTab() {
  loading.value = true;
  try {
    await Promise.all([
      loadClusters(),
      loadMaterials(),
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
  await Promise.all([
    loadClusters(),
    loadMaterials(),
    loadUnits(),
    loadDepots(),
    loadSerials(),
  ]);
  loadRegionalStock();
});
</script>
