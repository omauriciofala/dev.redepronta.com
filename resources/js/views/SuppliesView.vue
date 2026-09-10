<template>
  <div class="py-8 w-full space-y-6">
    <!-- Cabeçalho Principal (Page Header) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-border pb-5">
      <div class="flex items-center gap-3.5">
        <div class="w-11 h-11 rounded-xl bg-[#FC6714]/10 text-[#FC6714] border border-[#FC6714]/25 flex items-center justify-center shadow-xs">
          <Package class="w-6 h-6" />
        </div>
        <div>
          <h1 class="text-2xl font-bold font-heading text-text-primary tracking-tight">Suprimentos & WMS</h1>
          <p class="text-sm text-text-muted">Gestão de depósitos físicos, saldo virtual aglutinado e rastreabilidade serial</p>
        </div>
      </div>

      <div class="flex items-center gap-2.5">
        <button
          type="button"
          @click="openTransferModal()"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#FC6714] hover:bg-[#e0560a] text-white text-sm font-semibold rounded-lg shadow-xs transition-colors cursor-pointer focus:ring-2 focus:ring-[#FC6714]/40 focus:outline-none"
        >
          <ArrowRightLeft class="w-4 h-4" />
          <span>Nova Transferência</span>
        </button>

        <button
          type="button"
          @click="openMaterialModal()"
          class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-surface hover:bg-surface-hover text-text-primary border border-border text-sm font-medium rounded-lg shadow-xs transition-colors cursor-pointer"
        >
          <Plus class="w-4 h-4 text-text-muted" />
          <span>Novo Material</span>
        </button>
      </div>
    </div>

    <!-- Navegação por Abas do Módulo -->
    <div class="flex border-b border-border gap-6">
      <button
        type="button"
        @click="activeTab = 'regional'"
        :class="activeTab === 'regional'
          ? 'border-[#FC6714] text-[#FC6714] font-bold'
          : 'border-transparent text-text-muted hover:text-text-primary font-medium'"
        class="pb-3 border-b-2 text-sm flex items-center gap-2 transition cursor-pointer"
      >
        <Layers class="w-4 h-4" />
        <span>Posição Regional (Saldo Virtual Aglutinado)</span>
      </button>

      <button
        type="button"
        @click="activeTab = 'materials'"
        :class="activeTab === 'materials'
          ? 'border-[#FC6714] text-[#FC6714] font-bold'
          : 'border-transparent text-text-muted hover:text-text-primary font-medium'"
        class="pb-3 border-b-2 text-sm flex items-center gap-2 transition cursor-pointer"
      >
        <Boxes class="w-4 h-4" />
        <span>Catálogo de Materiais</span>
      </button>

      <button
        type="button"
        @click="activeTab = 'depots'"
        :class="activeTab === 'depots'
          ? 'border-[#FC6714] text-[#FC6714] font-bold'
          : 'border-transparent text-text-muted hover:text-text-primary font-medium'"
        class="pb-3 border-b-2 text-sm flex items-center gap-2 transition cursor-pointer"
      >
        <Warehouse class="w-4 h-4" />
        <span>Depósitos & Veículos</span>
      </button>

      <button
        type="button"
        @click="activeTab = 'serials'"
        :class="activeTab === 'serials'
          ? 'border-[#FC6714] text-[#FC6714] font-bold'
          : 'border-transparent text-text-muted hover:text-text-primary font-medium'"
        class="pb-3 border-b-2 text-sm flex items-center gap-2 transition cursor-pointer"
      >
        <QrCode class="w-4 h-4" />
        <span>Seriais (ONUs / Roteadores)</span>
      </button>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════════════════ -->
    <!-- ABA 1: POSIÇÃO REGIONAL (SALDO VIRTUAL AGLUTINADO) -->
    <!-- ═════════════════════════════════════════════════════════════════════════════════ -->
    <div v-if="activeTab === 'regional'" class="space-y-6">
      <!-- Seletor de Cluster Regional -->
      <div class="flex flex-wrap items-center justify-between gap-4 p-4 rounded-xl bg-surface border border-border">
        <div class="flex items-center gap-3">
          <span class="text-xs font-bold text-text-muted uppercase tracking-wider">Cluster Ativo:</span>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="c in clusters"
              :key="c.id"
              type="button"
              @click="selectCluster(c.id)"
              :class="selectedClusterId === c.id
                ? 'bg-[#FC6714] text-white font-bold shadow-xs'
                : 'bg-surface-secondary text-text-secondary hover:bg-surface-hover border border-border'"
              class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition cursor-pointer flex items-center gap-2"
            >
              <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: c.color || '#FC6714' }"></span>
              <span>{{ c.name }}</span>
            </button>
          </div>
        </div>

        <button
          type="button"
          @click="loadRegionalStock()"
          class="inline-flex items-center gap-1.5 text-xs text-text-muted hover:text-text-primary cursor-pointer px-2.5 py-1.5 rounded-md hover:bg-surface-secondary transition"
          :disabled="loading"
        >
          <RefreshCw class="w-3.5 h-3.5" :class="{ 'animate-spin': loading }" />
          <span>Atualizar Saldo</span>
        </button>
      </div>

      <!-- Cards de Métricas Consolidadas do Cluster -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-xl bg-surface border border-border flex items-center gap-3.5 shadow-xs">
          <div class="w-10 h-10 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center">
            <Boxes class="w-5 h-5" />
          </div>
          <div>
            <p class="text-xs font-medium text-text-muted">Tipos de Materiais</p>
            <h3 class="text-xl font-bold text-text-primary">{{ currentClusterSummary.total_materials || 0 }}</h3>
          </div>
        </div>

        <div class="p-4 rounded-xl bg-surface border border-border flex items-center gap-3.5 shadow-xs">
          <div class="w-10 h-10 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
            <Layers class="w-5 h-5" />
          </div>
          <div>
            <p class="text-xs font-medium text-text-muted">Itens em Estoque Total</p>
            <h3 class="text-xl font-bold text-text-primary">{{ formatNumber(currentClusterSummary.total_items_count || 0) }}</h3>
          </div>
        </div>

        <div class="p-4 rounded-xl bg-surface border border-border flex items-center gap-3.5 shadow-xs">
          <div class="w-10 h-10 rounded-lg bg-[#FC6714]/10 text-[#FC6714] flex items-center justify-center">
            <QrCode class="w-5 h-5" />
          </div>
          <div>
            <p class="text-xs font-medium text-text-muted">ONUs / Seriais Prontos</p>
            <h3 class="text-xl font-bold text-text-primary">{{ currentClusterSummary.total_serials_in_stock || 0 }}</h3>
          </div>
        </div>

        <div class="p-4 rounded-xl bg-surface border border-border flex items-center gap-3.5 shadow-xs">
          <div class="w-10 h-10 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center">
            <Truck class="w-5 h-5" />
          </div>
          <div>
            <p class="text-xs font-medium text-text-muted">Veículos no Cluster</p>
            <h3 class="text-xl font-bold text-text-primary">{{ currentClusterSummary.vehicles_count || 0 }} técnicos</h3>
          </div>
        </div>
      </div>

      <!-- Tabela do Saldo Virtual Aglutinado -->
      <div class="rounded-xl bg-surface border border-border overflow-hidden shadow-xs">
        <div class="p-4 border-b border-border flex items-center justify-between">
          <h2 class="text-base font-bold font-heading text-text-primary">
            Posição Regional Aglutinada (Base + Veículos de Campo)
          </h2>
          <span class="text-xs text-text-muted">
            Soma em tempo real da base física e veículos alocados a este cluster
          </span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="border-b border-border bg-surface-secondary/50 text-xs font-semibold text-text-muted uppercase tracking-wider">
                <th class="py-3 px-4">Material</th>
                <th class="py-3 px-4">Unidade</th>
                <th class="py-3 px-4 text-right">Saldo Base Física</th>
                <th class="py-3 px-4 text-right">Saldo Veículos</th>
                <th class="py-3 px-4 text-right">Saldo Aglutinado</th>
                <th class="py-3 px-4 text-center">Rastreabilidade</th>
                <th class="py-3 px-4 text-center">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border">
              <template v-for="mat in regionalMaterials" :key="mat.material_id">
                <tr class="hover:bg-surface-hover/50 transition-colors">
                  <td class="py-3.5 px-4 font-medium text-text-primary">
                    <div class="flex items-center gap-2">
                      <span class="font-bold text-[#FC6714] font-mono text-xs">{{ mat.code }}</span>
                      <span>{{ mat.name }}</span>
                    </div>
                  </td>
                  <td class="py-3.5 px-4 text-text-secondary">
                    <span class="px-2 py-0.5 rounded-md bg-surface-secondary text-xs font-semibold">{{ mat.unit }}</span>
                  </td>
                  <td class="py-3.5 px-4 text-right font-medium text-text-primary">
                    {{ formatNumber(mat.base_quantity) }}
                  </td>
                  <td class="py-3.5 px-4 text-right font-medium text-blue-600 dark:text-blue-400">
                    <button
                      type="button"
                      @click="toggleExpand(mat.material_id)"
                      class="inline-flex items-center gap-1 hover:underline cursor-pointer"
                      title="Clique para ver detalhe por veículo"
                    >
                      <span>{{ formatNumber(mat.vehicles_quantity) }}</span>
                      <ChevronDown class="w-3.5 h-3.5" :class="{ 'rotate-180': expandedRows.includes(mat.material_id) }" />
                    </button>
                  </td>
                  <td class="py-3.5 px-4 text-right font-bold text-text-primary">
                    <span
                      :class="mat.is_low_stock ? 'text-amber-500' : 'text-emerald-600 dark:text-emerald-400'"
                    >
                      {{ formatNumber(mat.total_virtual_quantity) }}
                    </span>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <span
                      v-if="mat.has_serial"
                      class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-500/10 text-purple-600 dark:text-purple-400"
                    >
                      <QrCode class="w-3 h-3" />
                      <span>{{ mat.serials_in_stock }} ONUs</span>
                    </span>
                    <span v-else class="text-xs text-text-muted">Convencional</span>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <button
                      type="button"
                      @click="openTransferModal(mat)"
                      class="inline-flex items-center gap-1 text-xs font-semibold text-[#FC6714] hover:text-[#e0560a] hover:underline cursor-pointer"
                    >
                      <ArrowRightLeft class="w-3.5 h-3.5" />
                      <span>Transferir</span>
                    </button>
                  </td>
                </tr>

                <!-- Linha Expansível: Detalhamento por Veículo / Técnico -->
                <tr v-if="expandedRows.includes(mat.material_id)" class="bg-surface-secondary/40">
                  <td colspan="7" class="p-4">
                    <div class="pl-6 border-l-2 border-[#FC6714] space-y-2">
                      <p class="text-xs font-bold text-text-primary uppercase tracking-wider">
                        Distribuição Física nos Depósitos do Cluster:
                      </p>
                      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 text-xs">
                        <div
                          v-for="d in mat.breakdown"
                          :key="d.depot_id"
                          class="p-2.5 rounded-lg bg-surface border border-border flex items-center justify-between"
                        >
                          <div>
                            <div class="flex items-center gap-1.5 font-semibold text-text-primary">
                              <Truck v-if="d.depot_type === 'VEHICLE'" class="w-3.5 h-3.5 text-blue-500" />
                              <Warehouse v-else class="w-3.5 h-3.5 text-emerald-500" />
                              <span>{{ d.depot_name }}</span>
                            </div>
                            <p v-if="d.responsible_name" class="text-[11px] text-text-muted">
                              Resp: {{ d.responsible_name }}
                            </p>
                          </div>
                          <span class="font-bold text-text-primary font-mono text-sm">
                            {{ formatNumber(d.quantity) }} {{ mat.unit }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </template>

              <tr v-if="regionalMaterials.length === 0 && !loading">
                <td colspan="7" class="py-12 text-center text-text-muted">
                  Nenhum material com saldo registrado neste cluster regional.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════════════════ -->
    <!-- ABA 2: CATÁLOGO DE MATERIAIS -->
    <!-- ═════════════════════════════════════════════════════════════════════════════════ -->
    <div v-if="activeTab === 'materials'" class="space-y-4">
      <div class="flex items-center justify-between gap-4">
        <input
          type="text"
          v-model="materialSearch"
          placeholder="Buscar material por código, nome ou categoria..."
          class="max-w-md w-full px-3.5 py-2 rounded-lg bg-surface border border-border text-sm text-text-primary placeholder:text-text-muted focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
        />
        <span class="text-xs text-text-muted">{{ filteredMaterials.length }} materiais cadastrados</span>
      </div>

      <div class="rounded-xl bg-surface border border-border overflow-hidden shadow-xs">
        <table class="w-full text-left border-collapse text-sm">
          <thead>
            <tr class="border-b border-border bg-surface-secondary/50 text-xs font-semibold text-text-muted uppercase tracking-wider">
              <th class="py-3 px-4">Código / SKU</th>
              <th class="py-3 px-4">Nome</th>
              <th class="py-3 px-4">Categoria</th>
              <th class="py-3 px-4">Unidade</th>
              <th class="py-3 px-4 text-center">Tipo Rastreio</th>
              <th class="py-3 px-4 text-right">Custo Médio</th>
              <th class="py-3 px-4 text-right">Estoque Mín.</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border">
            <tr v-for="m in filteredMaterials" :key="m.id" class="hover:bg-surface-hover/50">
              <td class="py-3 px-4 font-mono font-bold text-[#FC6714] text-xs">{{ m.code }}</td>
              <td class="py-3 px-4 font-medium text-text-primary">{{ m.name }}</td>
              <td class="py-3 px-4 text-text-secondary">{{ m.category || '-' }}</td>
              <td class="py-3 px-4">{{ m.unit?.code || 'UND' }}</td>
              <td class="py-3 px-4 text-center">
                <span
                  v-if="m.has_serial"
                  class="px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-500/10 text-purple-600 dark:text-purple-400"
                >
                  Serializado (ONU)
                </span>
                <span v-else class="text-xs text-text-muted">Convencional</span>
              </td>
              <td class="py-3 px-4 text-right text-text-secondary">R$ {{ formatNumber(m.unit_cost) }}</td>
              <td class="py-3 px-4 text-right text-text-secondary">{{ formatNumber(m.min_stock) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════════════════ -->
    <!-- ABA 3: DEPÓSITOS & VEÍCULOS -->
    <!-- ═════════════════════════════════════════════════════════════════════════════════ -->
    <div v-if="activeTab === 'depots'" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div v-for="d in depots" :key="d.id" class="p-4 rounded-xl bg-surface border border-border shadow-xs space-y-3">
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-2.5">
              <div
                :class="d.type === 'VEHICLE' ? 'bg-blue-500/10 text-blue-600' : 'bg-emerald-500/10 text-emerald-600'"
                class="w-9 h-9 rounded-lg flex items-center justify-center"
              >
                <Truck v-if="d.type === 'VEHICLE'" class="w-5 h-5" />
                <Warehouse v-else class="w-5 h-5" />
              </div>
              <div>
                <h3 class="text-sm font-bold text-text-primary">{{ d.name }}</h3>
                <span class="text-xs font-mono text-[#FC6714]">{{ d.code }}</span>
              </div>
            </div>
            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-surface-secondary text-text-muted">
              {{ d.type }}
            </span>
          </div>

          <div class="text-xs text-text-secondary space-y-1">
            <p v-if="d.cluster"><strong class="text-text-primary">Cluster:</strong> {{ d.cluster.name }}</p>
            <p v-if="d.vehicle_plate"><strong class="text-text-primary">Placa:</strong> {{ d.vehicle_plate }}</p>
            <p v-if="d.responsible_person"><strong class="text-text-primary">Responsável:</strong> {{ d.responsible_person.name }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════════════════ -->
    <!-- ABA 4: RASTREABILIDADE DE SERIAIS (ONUs) -->
    <!-- ═════════════════════════════════════════════════════════════════════════════════ -->
    <div v-if="activeTab === 'serials'" class="space-y-4">
      <div class="flex items-center justify-between gap-4">
        <input
          type="text"
          v-model="serialSearch"
          @input="loadSerials"
          placeholder="Buscar por número de série (GPON SN) ou MAC address..."
          class="max-w-md w-full px-3.5 py-2 rounded-lg bg-surface border border-border text-sm text-text-primary placeholder:text-text-muted focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
        />
        <span class="text-xs text-text-muted">{{ serialsList.length }} seriais encontrados</span>
      </div>

      <div class="rounded-xl bg-surface border border-border overflow-hidden shadow-xs">
        <table class="w-full text-left border-collapse text-sm">
          <thead>
            <tr class="border-b border-border bg-surface-secondary/50 text-xs font-semibold text-text-muted uppercase tracking-wider">
              <th class="py-3 px-4">Número de Série (SN)</th>
              <th class="py-3 px-4">MAC Address</th>
              <th class="py-3 px-4">Modelo do Equipamento</th>
              <th class="py-3 px-4">Depósito Atual</th>
              <th class="py-3 px-4">Responsável</th>
              <th class="py-3 px-4 text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border">
            <tr v-for="s in serialsList" :key="s.id" class="hover:bg-surface-hover/50">
              <td class="py-3 px-4 font-mono font-bold text-[#FC6714]">{{ s.serial_number }}</td>
              <td class="py-3 px-4 font-mono text-xs text-text-secondary">{{ s.mac_address || '-' }}</td>
              <td class="py-3 px-4 font-medium text-text-primary">{{ s.material?.name }}</td>
              <td class="py-3 px-4 text-text-secondary">{{ s.current_depot?.name }}</td>
              <td class="py-3 px-4 text-text-secondary">{{ s.current_depot?.responsible_person?.name || '-' }}</td>
              <td class="py-3 px-4 text-center">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                  {{ s.status }}
                </span>
              </td>
            </tr>
            <tr v-if="serialsList.length === 0">
              <td colspan="6" class="py-8 text-center text-text-muted">Nenhum serial cadastrado no momento.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════════════════ -->
    <!-- MODAL DE TRANSFERÊNCIA DE ESTOQUE (ATÔMICA) -->
    <!-- ═════════════════════════════════════════════════════════════════════════════════ -->
    <div
      v-if="showTransferModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
    >
      <div class="w-full max-w-lg rounded-2xl bg-surface border border-border shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-surface-secondary/40">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#FC6714]/10 text-[#FC6714] flex items-center justify-center">
              <ArrowRightLeft class="w-4 h-4" />
            </div>
            <h3 class="text-base font-bold font-heading text-text-primary">Transferência de Materiais</h3>
          </div>
          <button type="button" @click="showTransferModal = false" class="text-text-muted hover:text-text-primary p-1.5 rounded-lg hover:bg-surface-secondary">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitTransfer" class="p-6 space-y-4">
          <div v-if="transferError" class="p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-600 text-xs">
            {{ transferError }}
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
              <label class="block text-xs font-bold text-text-secondary uppercase mb-1">Depósito de Origem *</label>
              <select
                v-model="transferForm.source_depot_id"
                required
                @change="onSourceDepotChange"
                class="w-full px-3 py-2 rounded-lg bg-surface border border-border text-sm text-text-primary focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
              >
                <option value="" disabled>Selecione a origem</option>
                <option v-for="d in depots" :key="d.id" :value="d.id">{{ d.name }} ({{ d.type }})</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-text-secondary uppercase mb-1">Depósito de Destino *</label>
              <select
                v-model="transferForm.destination_depot_id"
                required
                class="w-full px-3 py-2 rounded-lg bg-surface border border-border text-sm text-text-primary focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
              >
                <option value="" disabled>Selecione o destino</option>
                <option v-for="d in destinationDepotsList" :key="d.id" :value="d.id">{{ d.name }} ({{ d.type }})</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-text-secondary uppercase mb-1">Material *</label>
            <select
              v-model="transferForm.material_id"
              required
              @change="onMaterialChange"
              class="w-full px-3 py-2 rounded-lg bg-surface border border-border text-sm text-text-primary focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
            >
              <option value="" disabled>Selecione o material</option>
              <option v-for="m in materials" :key="m.id" :value="m.id">
                {{ m.code }} - {{ m.name }} ({{ m.has_serial ? 'Serializado' : 'Convencional' }})
              </option>
            </select>
          </div>

          <div v-if="selectedMaterialForTransfer?.has_serial" class="space-y-2 p-3 rounded-lg bg-purple-500/5 border border-purple-500/20">
            <label class="block text-xs font-bold text-purple-700 dark:text-purple-400 uppercase">
              Selecione os Seriais Disponíveis na Origem ({{ availableSerialsForTransfer.length }} disponíveis)
            </label>
            <div class="max-h-36 overflow-y-auto space-y-1 pr-1">
              <label
                v-for="s in availableSerialsForTransfer"
                :key="s.id"
                class="flex items-center gap-2 p-1.5 rounded hover:bg-surface text-xs cursor-pointer"
              >
                <input
                  type="checkbox"
                  :value="s.id"
                  v-model="transferForm.serial_ids"
                  @change="transferForm.quantity = transferForm.serial_ids.length"
                  class="rounded text-[#FC6714] focus:ring-[#FC6714]"
                />
                <span class="font-mono font-bold text-text-primary">{{ s.serial_number }}</span>
                <span v-if="s.mac_address" class="text-text-muted text-[11px] font-mono">({{ s.mac_address }})</span>
              </label>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div>
              <label class="block text-xs font-bold text-text-secondary uppercase mb-1">Quantidade *</label>
              <input
                type="number"
                step="0.01"
                min="0.01"
                v-model="transferForm.quantity"
                :readonly="selectedMaterialForTransfer?.has_serial"
                required
                class="w-full px-3 py-2 rounded-lg bg-surface border border-border text-sm text-text-primary focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-text-secondary uppercase mb-1">Doc. Ref / OFS</label>
              <input
                type="text"
                v-model="transferForm.document_ref"
                placeholder="Ex: REQ-042"
                class="w-full px-3 py-2 rounded-lg bg-surface border border-border text-sm text-text-primary focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-text-secondary uppercase mb-1">Observações</label>
            <textarea
              v-model="transferForm.notes"
              rows="2"
              placeholder="Motivo da transferência..."
              class="w-full px-3 py-2 rounded-lg bg-surface border border-border text-sm text-text-primary focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
            ></textarea>
          </div>

          <div class="pt-3 border-t border-border flex items-center justify-end gap-3">
            <button
              type="button"
              @click="showTransferModal = false"
              class="px-4 py-2 text-sm font-medium text-text-secondary hover:text-text-primary rounded-lg"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="submittingTransfer"
              class="px-4 py-2 bg-[#FC6714] hover:bg-[#e0560a] text-white text-sm font-semibold rounded-lg transition disabled:opacity-50"
            >
              <span v-if="submittingTransfer">Processando...</span>
              <span v-else>Confirmar Transferência</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import {
  Package, ArrowRightLeft, Plus, Layers, Boxes, Warehouse, QrCode, RefreshCw,
  Truck, ChevronDown, X
} from 'lucide-vue-next';

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
  category?: string;
  unit_cost: number;
  min_stock: number;
  has_serial: boolean;
  unit?: { code: string; name: string };
}

const activeTab = ref<'regional' | 'materials' | 'depots' | 'serials'>('regional');
const loading = ref(false);

const clusters = ref<Cluster[]>([]);
const selectedClusterId = ref<number | null>(null);
const currentClusterSummary = ref<any>({});
const regionalMaterials = ref<any[]>([]);
const expandedRows = ref<number[]>([]);

const materials = ref<Material[]>([]);
const materialSearch = ref('');

const depots = ref<Depot[]>([]);
const serialsList = ref<any[]>([]);
const serialSearch = ref('');

// Modal de transferência
const showTransferModal = ref(false);
const submittingTransfer = ref(false);
const transferError = ref('');
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

const filteredMaterials = computed(() => {
  if (!materialSearch.value.trim()) return materials.value;
  const q = materialSearch.value.toLowerCase();
  return materials.value.filter(m =>
    m.code.toLowerCase().includes(q) ||
    m.name.toLowerCase().includes(q) ||
    (m.category && m.category.toLowerCase().includes(q))
  );
});

function formatNumber(val: any): string {
  const num = Number(val) || 0;
  return new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(num);
}

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
  loadRegionalStock();
}

async function loadMaterials() {
  try {
    const res = await fetch('/api/v1/materials');
    const json = await res.json();
    materials.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar materiais:', err);
  }
}

async function loadDepots() {
  try {
    const res = await fetch('/api/v1/depots');
    const json = await res.json();
    depots.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar depósitos:', err);
  }
}

async function loadSerials() {
  try {
    const url = serialSearch.value
      ? `/api/v1/stock/serials?search=${encodeURIComponent(serialSearch.value)}`
      : '/api/v1/stock/serials';
    const res = await fetch(url);
    const json = await res.json();
    serialsList.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar seriais:', err);
  }
}

function openTransferModal(material?: any) {
  transferError.value = '';
  transferForm.value = {
    source_depot_id: depots.value[0]?.id ? String(depots.value[0].id) : '',
    destination_depot_id: depots.value[1]?.id ? String(depots.value[1].id) : '',
    material_id: material?.material_id ? String(material.material_id) : (materials.value[0]?.id ? String(materials.value[0].id) : ''),
    quantity: 1,
    serial_ids: [],
    document_ref: '',
    notes: '',
  };
  onMaterialChange();
  showTransferModal.value = true;
}

function openMaterialModal() {
  activeTab.value = 'materials';
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

  try {
    const res = await fetch(`/api/v1/stock/serials?depot_id=${depotId}&material_id=${matId}&status=IN_STOCK`);
    const json = await res.json();
    availableSerialsForTransfer.value = json.data || [];
  } catch (err) {
    console.error('Erro ao carregar seriais para transferência:', err);
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
    loadRegionalStock();
    loadSerials();
  } catch (err: any) {
    transferError.value = err.message || 'Erro de conexão com o servidor.';
  } finally {
    submittingTransfer.value = false;
  }
}

onMounted(async () => {
  await Promise.all([
    loadClusters(),
    loadMaterials(),
    loadDepots(),
    loadSerials(),
  ]);
  loadRegionalStock();
});
</script>
