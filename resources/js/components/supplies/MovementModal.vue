<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="$emit('close')"
    size="fullscreen"
    :title="modalTitle"
    :description="modalDescription"
  >
    <!-- Header Badge -->
    <template #header-badge>
      <span
        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border"
        :class="badgeColorClass"
      >
        <component :is="typeIcon" class="w-3.5 h-3.5" />
        <span>{{ typeLabel }}</span>
      </span>
    </template>

    <div class="space-y-6 text-xs">
      <!-- Mensagem de Erro Geral -->
      <div
        v-if="errorMessage"
        class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 font-medium flex items-center justify-between gap-3 shadow-xs"
      >
        <div class="flex items-center gap-2.5">
          <AlertCircle class="w-5 h-5 shrink-0 text-rose-600 dark:text-rose-400" />
          <span class="text-sm font-semibold">{{ errorMessage }}</span>
        </div>
        <button
          type="button"
          @click="errorMessage = ''"
          class="text-rose-500 hover:text-rose-800 dark:hover:text-rose-200 cursor-pointer"
        >
          <X class="w-4 h-4" />
        </button>
      </div>

      <!-- ========================================================================= -->
      <!-- SEÇÃO 1: CABEÇALHO DA MOVIMENTAÇÃO (TIPO, DEPÓSITOS E DOCUMENTAÇÃO) -->
      <!-- ========================================================================= -->
      <div class="p-5 bg-slate-50/80 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-4">
        <!-- 4 Tipos Obrigatórios de Movimentação -->
        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
            Tipo de Movimentação de Estoque *
          </label>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <!-- Entrada -->
            <button
              type="button"
              @click="setMovementType('ENTRY')"
              class="flex items-center justify-center gap-2.5 py-3 px-4 rounded-xl border text-xs font-bold transition cursor-pointer"
              :class="form.movement_type === 'ENTRY'
                ? 'bg-emerald-600 text-white border-emerald-600 shadow-md ring-2 ring-emerald-500/30'
                : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60'"
            >
              <ArrowDownToLine class="w-4 h-4" />
              <span>Entrada</span>
            </button>

            <!-- Saída -->
            <button
              type="button"
              @click="setMovementType('EXIT')"
              class="flex items-center justify-center gap-2.5 py-3 px-4 rounded-xl border text-xs font-bold transition cursor-pointer"
              :class="form.movement_type === 'EXIT'
                ? 'bg-rose-600 text-white border-rose-600 shadow-md ring-2 ring-rose-500/30'
                : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60'"
            >
              <ArrowUpFromLine class="w-4 h-4" />
              <span>Saída</span>
            </button>

            <!-- Devolução -->
            <button
              type="button"
              @click="setMovementType('RETURN')"
              class="flex items-center justify-center gap-2.5 py-3 px-4 rounded-xl border text-xs font-bold transition cursor-pointer"
              :class="form.movement_type === 'RETURN'
                ? 'bg-indigo-600 text-white border-indigo-600 shadow-md ring-2 ring-indigo-500/30'
                : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60'"
            >
              <RotateCcw class="w-4 h-4" />
              <span>Devolução</span>
            </button>

            <!-- Transferência -->
            <button
              type="button"
              @click="setMovementType('TRANSFER')"
              class="flex items-center justify-center gap-2.5 py-3 px-4 rounded-xl border text-xs font-bold transition cursor-pointer"
              :class="form.movement_type === 'TRANSFER'
                ? 'bg-[#FC6714] text-white border-[#FC6714] shadow-md ring-2 ring-[#FC6714]/30'
                : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60'"
            >
              <ArrowRightLeft class="w-4 h-4" />
              <span>Transferência</span>
            </button>
          </div>
          <p class="mt-2 text-xs font-medium" :class="typeDescriptionColor">
            {{ typeDescriptionText }}
          </p>
        </div>

        <!-- Parâmetros de Origem, Destino e Documento -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2 border-t border-slate-200/80 dark:border-slate-700/60">
          <!-- ORIGEM -->
          <div>
            <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              Depósito de Origem {{ isSourceRequired ? '*' : '(Opcional / Externo)' }}
            </label>

            <template v-if="form.movement_type === 'ENTRY'">
              <div class="h-10 px-3.5 rounded-lg border border-dashed border-slate-300 dark:border-slate-700 bg-white/60 dark:bg-slate-800/40 text-slate-400 dark:text-slate-500 text-xs flex items-center select-none">
                Fornecedor / Compra / Inventário Inicial
              </div>
            </template>

            <template v-else-if="form.movement_type === 'RETURN'">
              <select
                v-model="form.source_depot_id"
                class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none cursor-pointer"
              >
                <option value="">Origem Externa (Cliente / Técnico em Campo)</option>
                <option v-for="d in depots" :key="d.id" :value="String(d.id)">
                  {{ d.name }} ({{ formatDepotType(d.type) }})
                </option>
              </select>
            </template>

            <template v-else>
              <select
                v-model="form.source_depot_id"
                :required="isSourceRequired"
                @change="onHeaderSourceChange"
                class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none cursor-pointer"
              >
                <option value="" disabled>Selecione o local de saída...</option>
                <option v-for="d in depots" :key="d.id" :value="String(d.id)">
                  {{ d.name }} ({{ formatDepotType(d.type) }})
                </option>
              </select>
            </template>
          </div>

          <!-- DESTINO -->
          <div>
            <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              Depósito de Destino {{ isDestRequired ? '*' : '(Opcional / Externo)' }}
            </label>

            <template v-if="form.movement_type === 'EXIT'">
              <div class="h-10 px-3.5 rounded-lg border border-dashed border-slate-300 dark:border-slate-700 bg-white/60 dark:bg-slate-800/40 text-slate-400 dark:text-slate-500 text-xs flex items-center select-none">
                Aplicação Operacional / Técnico / Cliente
              </div>
            </template>

            <template v-else>
              <select
                v-model="form.destination_depot_id"
                :required="isDestRequired"
                class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none cursor-pointer"
              >
                <option value="" disabled>Selecione o local de entrada...</option>
                <option v-for="d in destinationOptions" :key="d.id" :value="String(d.id)">
                  {{ d.name }} ({{ formatDepotType(d.type) }})
                </option>
              </select>
            </template>
          </div>

          <!-- DOCUMENTO DE REFERÊNCIA -->
          <div>
            <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              Documento / OS / NF-e
            </label>
            <input
              type="text"
              v-model="form.document_ref"
              :placeholder="docRefPlaceholder"
              class="w-full h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none"
            />
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- SEÇÃO 2: LINHA DE INSERÇÃO DE MATERIAL NA GRID (CONFORME SOLICITADO) -->
      <!-- ========================================================================= -->
      <div class="p-5 bg-white dark:bg-[#06064D]/50 rounded-2xl border border-slate-200 dark:border-[#14147A] shadow-xs space-y-4">
        <div class="flex items-center justify-between gap-4">
          <div class="flex items-center gap-2">
            <Layers class="w-4 h-4 text-[#FC6714]" />
            <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
              Adicionar Material à Movimentação
            </h4>
          </div>
          <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
            Insira os materiais um a um para compor a grid
          </span>
        </div>

        <!-- Linha: Campo Novo Material + Campo Quantidade + Botão Adicionar -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
          <!-- Campo 1: Material (8 colunas) -->
          <div class="md:col-span-8">
            <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              Novo Material *
            </label>
            <select
              v-model="newItem.material_id"
              @change="onNewItemMaterialChange"
              class="w-full h-11 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-xs font-medium focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none cursor-pointer"
            >
              <option value="" disabled>Selecione um material do catálogo para adicionar...</option>
              <option v-for="m in materials" :key="m.id" :value="m.id">
                {{ m.code }} — {{ m.name }} [{{ m.unit?.code || 'UND' }}] {{ m.has_serial ? '• [Serial Obrigatório]' : '• [Convencional]' }}
              </option>
            </select>
          </div>

          <!-- Campo 2: Quantidade (2 colunas) -->
          <div class="md:col-span-2">
            <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              Quantidade *
            </label>
            <input
              type="number"
              :step="selectedNewItemMaterial?.has_serial ? '1' : '0.01'"
              min="1"
              v-model.number="newItem.quantity"
              placeholder="Qtd"
              class="w-full h-11 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 font-mono text-xs font-bold focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none"
            />
          </div>

          <!-- Botão 3: Adicionar Material (2 colunas) -->
          <div class="md:col-span-2">
            <button
              type="button"
              @click="addMaterialToGrid"
              :disabled="!newItem.material_id || newItem.quantity <= 0"
              class="w-full h-11 px-4 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] disabled:opacity-50 text-white text-xs font-bold shadow-xs transition active:scale-98 cursor-pointer flex items-center justify-center gap-2"
            >
              <Plus class="w-4 h-4" />
              <span>Adicionar à Grid</span>
            </button>
          </div>
        </div>

        <!-- Alerta Contextual quando material tem Rastreamento Serial Obrigatório -->
        <div
          v-if="selectedNewItemMaterial?.has_serial"
          class="p-3.5 rounded-xl border border-purple-200 dark:border-purple-800/60 bg-purple-50/60 dark:bg-purple-950/25 flex items-center justify-between gap-3 text-xs"
        >
          <div class="flex items-center gap-2 text-purple-900 dark:text-purple-300 font-medium">
            <QrCode class="w-4 h-4 text-purple-600 shrink-0" />
            <span>
              <strong>Rastreamento Serial Obrigatório:</strong> A quantidade informada define que devem ser informados e bipados exatamente <strong>{{ newItem.quantity || 1 }}</strong> seriais deste item.
            </span>
          </div>
          <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-200/70 dark:bg-purple-900 text-purple-800 dark:text-purple-200 shrink-0">
            Conferência Obrigatória
          </span>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- SEÇÃO 3: GRID DE MATERIAIS DA MOVIMENTAÇÃO -->
      <!-- ========================================================================= -->
      <div class="rounded-2xl border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#06064D]/50 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-[#14147A] bg-slate-50/60 dark:bg-[#03032E]/70 flex items-center justify-between gap-4">
          <div class="flex items-center gap-3">
            <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
              Materiais da Movimentação (Grid de Conferência)
            </h4>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
              {{ gridItems.length }} item(ns)
            </span>
          </div>

          <div v-if="hasPendingSerials" class="flex items-center gap-1.5 text-xs text-amber-600 dark:text-amber-400 font-bold animate-pulse">
            <AlertCircle class="w-4 h-4" />
            <span>Existem seriais pendentes de bipagem</span>
          </div>
          <div v-else-if="gridItems.length > 0" class="flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-bold">
            <CheckCircle2 class="w-4 h-4" />
            <span>Todos os itens validados para movimentação</span>
          </div>
        </div>

        <!-- Se Grid Vazia -->
        <div v-if="gridItems.length === 0" class="py-12 text-center text-slate-400 dark:text-slate-500 space-y-2">
          <Layers class="w-10 h-10 mx-auto opacity-40 text-[#FC6714]" />
          <p class="text-sm font-semibold">Nenhum material adicionado à movimentação ainda.</p>
          <p class="text-xs">Selecione o material e a quantidade na linha acima e clique em "Adicionar à Grid".</p>
        </div>

        <!-- Tabela de Itens da Grid -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="border-b border-slate-200 dark:border-[#14147A] bg-slate-100/50 dark:bg-[#03032E]/40 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider select-none">
                <th class="py-3 px-4 w-12 text-center">#</th>
                <th class="py-3 px-4">Material / SKU</th>
                <th class="py-3 px-4 w-36">Tipo de Rastreio</th>
                <th class="py-3 px-4 w-28 text-right">Qtd</th>
                <th class="py-3 px-4">Conferência Serial / Bipagem</th>
                <th class="py-3 px-4 w-16 text-center">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
              <template v-for="(item, idx) in gridItems" :key="item.uid">
                <tr
                  class="transition-colors duration-150"
                  :class="item.material.has_serial && item.serials.length < item.quantity
                    ? 'bg-amber-50/40 dark:bg-amber-950/15'
                    : 'hover:bg-slate-50 dark:hover:bg-white/5'"
                >
                  <!-- Índice -->
                  <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                    {{ idx + 1 }}
                  </td>

                  <!-- Material / SKU -->
                  <td class="py-3.5 px-4">
                    <div class="font-bold text-slate-900 dark:text-slate-100">
                      {{ item.material.name }}
                    </div>
                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                      SKU: {{ item.material.code }} • {{ item.material.unit?.code || 'UND' }}
                    </div>
                  </td>

                  <!-- Tipo de Rastreio -->
                  <td class="py-3.5 px-4">
                    <span
                      v-if="item.material.has_serial"
                      class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-purple-100 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800"
                    >
                      <QrCode class="w-3 h-3" />
                      Serial Obrigatório
                    </span>
                    <span
                      v-else
                      class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400"
                    >
                      Convencional
                    </span>
                  </td>

                  <!-- Quantidade -->
                  <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 dark:text-slate-100 text-sm">
                    {{ item.quantity }}
                  </td>

                  <!-- Conferência Serial / Bipagem -->
                  <td class="py-3.5 px-4">
                    <!-- Caso Convencional -->
                    <span v-if="!item.material.has_serial" class="text-slate-400 italic">
                      Não aplicável (sem controle serial)
                    </span>

                    <!-- Caso Serializado -->
                    <div v-else class="space-y-2">
                      <div class="flex items-center justify-between gap-2">
                        <!-- Badge de Status -->
                        <span
                          class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold"
                          :class="item.serials.length === item.quantity
                            ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800'
                            : 'bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-300 dark:border-amber-800'"
                        >
                          <component
                            :is="item.serials.length === item.quantity ? CheckCircle2 : AlertCircle"
                            class="w-3.5 h-3.5"
                          />
                          <span>
                            {{ item.serials.length }} de {{ item.quantity }} serial(is) bipado(s)
                          </span>
                        </span>

                        <button
                          type="button"
                          @click="toggleItemScanner(item)"
                          class="text-xs font-semibold text-[#FC6714] hover:underline cursor-pointer inline-flex items-center gap-1"
                        >
                          <ScanBarcode class="w-3.5 h-3.5" />
                          <span>{{ item.showScanner ? 'Recolher leitor' : 'Bipar seriais' }}</span>
                        </button>
                      </div>

                      <!-- Área de Bipagem Rápida / Leitor Óptico Integrado -->
                      <div
                        v-if="item.showScanner || item.serials.length < item.quantity"
                        class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-purple-200 dark:border-purple-800 space-y-2"
                      >
                        <!-- Input de Bipagem -->
                        <div class="flex items-center gap-2">
                          <div class="relative flex-1">
                            <ScanBarcode class="w-4 h-4 absolute left-3 top-2.5 text-purple-500 pointer-events-none" />
                            <input
                              type="text"
                              v-model="item.scannerInput"
                              @keydown.enter.prevent="onScanBarcodeEnter(item)"
                              :placeholder="item.serials.length < item.quantity ? `Bipe o serial ${item.serials.length + 1} de ${item.quantity} e tecle Enter...` : 'Todos os seriais já foram bipados'"
                              :disabled="item.serials.length >= item.quantity"
                              class="w-full h-9 pl-9 pr-3 rounded-lg border border-purple-300 dark:border-purple-700 bg-purple-50/40 dark:bg-purple-950/30 text-xs font-mono focus:ring-2 focus:ring-purple-400 outline-none"
                            />
                          </div>
                          <button
                            type="button"
                            @click="onScanBarcodeEnter(item)"
                            :disabled="!item.scannerInput?.trim() || item.serials.length >= item.quantity"
                            class="h-9 px-3 rounded-lg bg-purple-600 hover:bg-purple-700 disabled:opacity-40 text-white text-xs font-bold transition cursor-pointer"
                          >
                            Bipar
                          </button>
                        </div>

                        <!-- Erro específico de bipagem na linha -->
                        <div v-if="item.scanError" class="text-[11px] font-bold text-rose-600 flex items-center gap-1">
                          <AlertCircle class="w-3.5 h-3.5" />
                          <span>{{ item.scanError }}</span>
                        </div>

                        <!-- Chips com seriais bipados -->
                        <div v-if="item.serials.length > 0" class="flex flex-wrap gap-1.5 pt-1">
                          <span
                            v-for="(s, sIdx) in item.serials"
                            :key="sIdx"
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-purple-100 dark:bg-purple-900/60 text-purple-900 dark:text-purple-200 font-mono text-[11px] border border-purple-200 dark:border-purple-800"
                          >
                            <span>{{ s.serial_number || s }}</span>
                            <button
                              type="button"
                              @click="removeSerialFromItem(item, sIdx)"
                              class="text-purple-400 hover:text-rose-600 transition cursor-pointer"
                              title="Remover este serial"
                            >
                              ✕
                            </button>
                          </span>
                        </div>
                      </div>
                    </div>
                  </td>

                  <!-- Ações -->
                  <td class="py-3.5 px-4 text-center">
                    <button
                      type="button"
                      @click="removeGridItem(idx)"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer"
                      title="Remover material da movimentação"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>

        <!-- Rodapé Interno da Grid com Totais -->
        <div v-if="gridItems.length > 0" class="p-4 bg-slate-50/70 dark:bg-[#03032E]/60 border-t border-slate-200 dark:border-[#14147A] flex flex-wrap items-center justify-between gap-4 text-xs font-semibold">
          <div class="flex items-center gap-4 text-slate-600 dark:text-slate-400">
            <span>Total de Materiais: <strong>{{ gridItems.length }}</strong></span>
            <span>Total de Peças: <strong>{{ totalPiecesCount }}</strong></span>
            <span>Itens Serializados: <strong>{{ totalSerializedCount }}</strong></span>
          </div>

          <div v-if="hasPendingSerials" class="text-rose-600 dark:text-rose-400 font-bold flex items-center gap-1.5">
            <AlertCircle class="w-4 h-4" />
            <span>Faltam seriais a serem bipados antes de confirmar</span>
          </div>
          <div v-else class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1.5">
            <CheckCircle2 class="w-4 h-4" />
            <span>Grid pronta para submissão</span>
          </div>
        </div>
      </div>

      <!-- Observações Operacionais da Movimentação -->
      <div>
        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
          Observações Operacionais Gerais
        </label>
        <textarea
          v-model="form.notes"
          rows="2"
          :placeholder="notesPlaceholder"
          class="w-full p-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none"
        ></textarea>
      </div>
    </div>

    <!-- RODAPÉ DO MODAL FULLSCREEN -->
    <template #footer>
      <div class="w-full flex items-center justify-between gap-4">
        <div class="text-xs text-slate-500 dark:text-slate-400 font-medium flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>Transação atômica única no banco de dados para todos os materiais da grid</span>
        </div>

        <div class="flex items-center gap-3">
          <button
            type="button"
            @click="$emit('close')"
            class="h-11 px-5 text-xs font-semibold rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition cursor-pointer"
          >
            Cancelar
          </button>

          <button
            type="button"
            @click="submitAllMovements"
            :disabled="submitting || gridItems.length === 0 || hasPendingSerials"
            class="h-11 px-6 text-white text-xs font-bold rounded-xl shadow-md transition active:scale-98 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer flex items-center gap-2"
            :class="submitButtonColorClass"
          >
            <span v-if="submitting" class="inline-block animate-spin mr-1">⟳</span>
            <span>{{ submitting ? 'Processando Movimentação...' : submitButtonLabel }}</span>
          </button>
        </div>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import {
  AlertCircle,
  QrCode,
  ArrowDownToLine,
  ArrowUpFromLine,
  RotateCcw,
  ArrowRightLeft,
  Plus,
  Trash2,
  CheckCircle2,
  ScanBarcode,
  Layers,
  X,
} from 'lucide-vue-next';
import BaseModal from '@/components/common/BaseModal.vue';

type MovementType = 'ENTRY' | 'EXIT' | 'RETURN' | 'TRANSFER';

interface GridItem {
  uid: string;
  material_id: number;
  material: any;
  quantity: number;
  serials: Array<{ serial_number: string; id?: number; mac_address?: string }>;
  scannerInput: string;
  scanError: string;
  showScanner: boolean;
}

const props = defineProps<{
  isOpen: boolean;
  depots: any[];
  materials: any[];
  initialMaterial?: any;
  initialDepot?: any;
  initialType?: MovementType;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'saved', movements?: any): void;
  (e: 'transferred'): void;
}>();

const submitting = ref(false);
const errorMessage = ref('');

// Dados de cabeçalho
const form = ref({
  movement_type: 'TRANSFER' as MovementType,
  source_depot_id: '',
  destination_depot_id: '',
  document_ref: '',
  notes: '',
});

// Grid de materiais da movimentação
const gridItems = ref<GridItem[]>([]);

// Linha de novo material para adicionar na grid
const newItem = ref({
  material_id: '' as number | '',
  quantity: 1,
});

const selectedNewItemMaterial = computed(() => {
  if (!newItem.value.material_id) return null;
  return props.materials.find(m => m.id === Number(newItem.value.material_id)) || null;
});

// Opções dinâmicas de destino (não permite mesmo depósito se for transferência)
const destinationOptions = computed(() => {
  if (form.value.movement_type === 'TRANSFER' && form.value.source_depot_id) {
    return props.depots.filter(d => String(d.id) !== form.value.source_depot_id);
  }
  return props.depots;
});

const isSourceRequired = computed(() => {
  return form.value.movement_type === 'TRANSFER' || form.value.movement_type === 'EXIT';
});

const isDestRequired = computed(() => {
  return form.value.movement_type === 'TRANSFER' || form.value.movement_type === 'ENTRY' || form.value.movement_type === 'RETURN';
});

const typeLabel = computed(() => {
  const map: Record<MovementType, string> = {
    ENTRY: 'Entrada de Estoque',
    EXIT: 'Saída de Estoque',
    RETURN: 'Devolução de Estoque',
    TRANSFER: 'Transferência de Estoque',
  };
  return map[form.value.movement_type];
});

const typeIcon = computed(() => {
  const map: Record<MovementType, any> = {
    ENTRY: ArrowDownToLine,
    EXIT: ArrowUpFromLine,
    RETURN: RotateCcw,
    TRANSFER: ArrowRightLeft,
  };
  return map[form.value.movement_type];
});

const badgeColorClass = computed(() => {
  switch (form.value.movement_type) {
    case 'ENTRY':
      return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800';
    case 'EXIT':
      return 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800';
    case 'RETURN':
      return 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800';
    case 'TRANSFER':
    default:
      return 'bg-orange-50 text-[#FC6714] border-orange-200 dark:bg-orange-950/60 dark:text-orange-300 dark:border-orange-800';
  }
});

const submitButtonColorClass = computed(() => {
  switch (form.value.movement_type) {
    case 'ENTRY':
      return 'bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800';
    case 'EXIT':
      return 'bg-rose-600 hover:bg-rose-700 active:bg-rose-800';
    case 'RETURN':
      return 'bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800';
    case 'TRANSFER':
    default:
      return 'bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605]';
  }
});

const submitButtonLabel = computed(() => {
  const total = gridItems.value.length;
  const map: Record<MovementType, string> = {
    ENTRY: total > 1 ? `Confirmar Entrada (${total} itens)` : 'Confirmar Entrada',
    EXIT: total > 1 ? `Confirmar Saída (${total} itens)` : 'Confirmar Saída',
    RETURN: total > 1 ? `Confirmar Devolução (${total} itens)` : 'Confirmar Devolução',
    TRANSFER: total > 1 ? `Confirmar Transferência (${total} itens)` : 'Confirmar Transferência',
  };
  return map[form.value.movement_type];
});

const modalTitle = computed(() => {
  return `Movimentação de Estoque em Tela Cheia: ${typeLabel.value}`;
});

const modalDescription = computed(() => {
  return 'Gestão e conferência de materiais em lote com rastreamento serial obrigatório e leitor óptico.';
});

const typeDescriptionText = computed(() => {
  switch (form.value.movement_type) {
    case 'ENTRY':
      return 'Entrada física de materiais no depósito (compras, notas fiscais ou inventário inicial).';
    case 'EXIT':
      return 'Baixa de itens do depósito (ordens de serviço, requisições de campo ou aplicação externa).';
    case 'RETURN':
      return 'Devolução de materiais para o depósito (retorno de técnicos, clientes ou garantias).';
    case 'TRANSFER':
    default:
      return 'Transferência física atômica entre dois depósitos da organização.';
  }
});

const typeDescriptionColor = computed(() => {
  switch (form.value.movement_type) {
    case 'ENTRY': return 'text-emerald-600 dark:text-emerald-400';
    case 'EXIT': return 'text-rose-600 dark:text-rose-400';
    case 'RETURN': return 'text-indigo-600 dark:text-indigo-400';
    case 'TRANSFER': default: return 'text-[#FC6714] dark:text-orange-400';
  }
});

const docRefPlaceholder = computed(() => {
  switch (form.value.movement_type) {
    case 'ENTRY': return 'Ex: NF-e 10492 ou Pedido de Compra';
    case 'EXIT': return 'Ex: OS-9812 ou Requisição de Campo';
    case 'RETURN': return 'Ex: Protocolo de Devolução ou OS-Retirada';
    case 'TRANSFER': default: return 'Ex: REQ-2026-042 ou Guia de Transferência';
  }
});

const notesPlaceholder = computed(() => {
  switch (form.value.movement_type) {
    case 'ENTRY': return 'Descreva detalhes da carga recebida, fornecedor ou lote...';
    case 'EXIT': return 'Descreva o técnico solicitante, cliente ou veículo de destino...';
    case 'RETURN': return 'Descreva as condições físicas dos materiais devolvidos...';
    case 'TRANSFER': default: return 'Descreva o motivo da transferência física ou logística interna...';
  }
});

// Verifica se há itens serializados na grid com bipagem pendente
const hasPendingSerials = computed(() => {
  return gridItems.value.some(item => {
    return item.material.has_serial && item.serials.length < item.quantity;
  });
});

const totalPiecesCount = computed(() => {
  return gridItems.value.reduce((acc, item) => acc + Number(item.quantity || 0), 0);
});

const totalSerializedCount = computed(() => {
  return gridItems.value.filter(item => item.material.has_serial).length;
});

function setMovementType(type: MovementType) {
  form.value.movement_type = type;
  errorMessage.value = '';
  if (type === 'ENTRY') {
    form.value.source_depot_id = '';
    if (!form.value.destination_depot_id && props.depots.length > 0) {
      form.value.destination_depot_id = String(props.depots[0].id);
    }
  } else if (type === 'EXIT') {
    form.value.destination_depot_id = '';
    if (!form.value.source_depot_id && props.depots.length > 0) {
      form.value.source_depot_id = String(props.depots[0].id);
    }
  }
}

function onHeaderSourceChange() {
  // Limpa seriais bipados na grid se a origem mudou em transferências/saídas
  if (form.value.movement_type === 'TRANSFER' || form.value.movement_type === 'EXIT') {
    gridItems.value.forEach(item => {
      if (item.material.has_serial) {
        item.serials = [];
        item.scanError = 'Origem alterada. Bipar os seriais novamente para validação no novo depósito.';
      }
    });
  }
}

function formatDepotType(type: string): string {
  const map: Record<string, string> = {
    CENTRAL: 'Almoxarifado Central',
    REGIONAL_BASE: 'Base Regional',
    LAB_REPAIR: 'Laboratório de Reparo',
  };
  return map[type] || 'Depósito';
}

function onNewItemMaterialChange() {
  if (selectedNewItemMaterial.value?.has_serial && newItem.value.quantity < 1) {
    newItem.value.quantity = 1;
  }
}

// Adiciona o material selecionado na linha para dentro da grid
function addMaterialToGrid() {
  errorMessage.value = '';
  if (!newItem.value.material_id) {
    errorMessage.value = 'Selecione um material para adicionar à grid.';
    return;
  }

  const mat = props.materials.find(m => m.id === Number(newItem.value.material_id));
  if (!mat) return;

  const qty = Number(newItem.value.quantity);
  if (qty <= 0) {
    errorMessage.value = 'A quantidade do material deve ser maior que zero.';
    return;
  }

  const uid = `${mat.id}-${Date.now()}-${Math.random().toString(36).substr(2, 5)}`;

  gridItems.value.push({
    uid,
    material_id: mat.id,
    material: mat,
    quantity: qty,
    serials: [],
    scannerInput: '',
    scanError: '',
    showScanner: mat.has_serial, // abre o scanner imediatamente se for serializado
  });

  // Limpa o seletor da linha de inserção
  newItem.value.material_id = '';
  newItem.value.quantity = 1;
}

function removeGridItem(index: number) {
  gridItems.value.splice(index, 1);
}

function toggleItemScanner(item: GridItem) {
  item.showScanner = !item.showScanner;
}

// Ação de bipagem com leitor de código de barras ou enter
async function onScanBarcodeEnter(item: GridItem) {
  item.scanError = '';
  const raw = item.scannerInput?.trim();
  if (!raw) return;

  if (item.serials.length >= item.quantity) {
    item.scanError = `Este item já atingiu a quantidade máxima de ${item.quantity} serial(is).`;
    return;
  }

  // Verifica se o serial já foi bipado neste item ou em outro da grid
  const alreadyInItem = item.serials.some(s => s.serial_number.toUpperCase() === raw.toUpperCase());
  if (alreadyInItem) {
    item.scanError = `O serial '${raw}' já foi bipado neste material.`;
    return;
  }

  // Se a operação for TRANSFER ou EXIT: o serial DEVE existir no depósito de origem com status IN_STOCK
  if (form.value.movement_type === 'TRANSFER' || form.value.movement_type === 'EXIT') {
    const depotId = form.value.source_depot_id;
    if (!depotId) {
      item.scanError = 'Selecione o Depósito de Origem no topo antes de bipar seriais.';
      return;
    }

    try {
      const res = await fetch(`/api/v1/stock/serials?depot_id=${depotId}&material_id=${item.material_id}&search=${encodeURIComponent(raw)}`);
      const json = await res.json();
      const list = json.data || [];
      const matched = list.find((s: any) =>
        s.serial_number.toUpperCase() === raw.toUpperCase() &&
        s.status === 'IN_STOCK' &&
        String(s.current_depot_id) === String(depotId)
      );

      if (!matched) {
        item.scanError = `Serial '${raw}' não encontrado com status 'Em Estoque' no depósito de origem.`;
        return;
      }

      item.serials.push({
        id: matched.id,
        serial_number: matched.serial_number,
        mac_address: matched.mac_address,
      });
      item.scannerInput = '';
    } catch (err) {
      item.scanError = 'Erro ao validar serial no depósito de origem.';
    }
    return;
  }

  // Para ENTRY ou RETURN: aceita o serial bipado
  item.serials.push({
    serial_number: raw,
  });
  item.scannerInput = '';
}

function removeSerialFromItem(item: GridItem, serialIndex: number) {
  item.serials.splice(serialIndex, 1);
  item.scanError = '';
}

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      errorMessage.value = '';
      const initialType = props.initialType || 'TRANSFER';

      const defaultSource = props.initialDepot?.id
        ? String(props.initialDepot.id)
        : (props.depots[0]?.id ? String(props.depots[0].id) : '');

      const otherDepot = props.depots.find(d => String(d.id) !== defaultSource);
      const defaultDest = otherDepot?.id ? String(otherDepot.id) : (props.depots[1]?.id ? String(props.depots[1].id) : '');

      form.value = {
        movement_type: initialType,
        source_depot_id: initialType === 'ENTRY' ? '' : defaultSource,
        destination_depot_id: initialType === 'EXIT' ? '' : defaultDest,
        document_ref: '',
        notes: '',
      };

      gridItems.value = [];

      // Se passou um material inicial (ex: clicou em "Movimentar" na tabela)
      if (props.initialMaterial) {
        const mat = props.initialMaterial.material || props.initialMaterial;
        if (mat?.id) {
          const found = props.materials.find(m => m.id === mat.id) || mat;
          gridItems.value.push({
            uid: `${found.id}-${Date.now()}`,
            material_id: found.id,
            material: found,
            quantity: 1,
            serials: [],
            scannerInput: '',
            scanError: '',
            showScanner: Boolean(found.has_serial),
          });
        }
      }

      newItem.value = {
        material_id: '',
        quantity: 1,
      };
    }
  }
);

async function submitAllMovements() {
  errorMessage.value = '';

  // Validação dos depósitos de cabeçalho
  if (form.value.movement_type === 'TRANSFER') {
    if (!form.value.source_depot_id) {
      errorMessage.value = 'Selecione o depósito de origem para a transferência.';
      return;
    }
    if (!form.value.destination_depot_id) {
      errorMessage.value = 'Selecione o depósito de destino para a transferência.';
      return;
    }
    if (form.value.source_depot_id === form.value.destination_depot_id) {
      errorMessage.value = 'O depósito de destino deve ser diferente do depósito de origem.';
      return;
    }
  } else if (form.value.movement_type === 'EXIT') {
    if (!form.value.source_depot_id) {
      errorMessage.value = 'Selecione o depósito de onde os materiais sairão.';
      return;
    }
  } else if (form.value.movement_type === 'ENTRY' || form.value.movement_type === 'RETURN') {
    if (!form.value.destination_depot_id) {
      errorMessage.value = 'Selecione o depósito de destino para entrada dos materiais.';
      return;
    }
  }

  // Validação da grid
  if (gridItems.value.length === 0) {
    errorMessage.value = 'Adicione ao menos um material na grid antes de confirmar a movimentação.';
    return;
  }

  // Validação estrita de seriais para cada item da grid
  for (let i = 0; i < gridItems.value.length; i++) {
    const item = gridItems.value[i];
    if (item.material.has_serial) {
      if (item.serials.length !== item.quantity) {
        errorMessage.value = `O material '${item.material.name}' possui rastreamento serial obrigatório. Quantidade: ${item.quantity}, mas apenas ${item.serials.length} serial(is) foram bipado(s).`;
        item.showScanner = true;
        return;
      }
    }
  }

  submitting.value = true;
  try {
    // Monta o payload em lote (items)
    const payload: any = {
      movement_type: form.value.movement_type,
      source_depot_id: form.value.source_depot_id ? Number(form.value.source_depot_id) : null,
      destination_depot_id: form.value.destination_depot_id ? Number(form.value.destination_depot_id) : null,
      document_ref: form.value.document_ref || null,
      notes: form.value.notes || null,
      items: gridItems.value.map(item => {
        const itemObj: any = {
          material_id: item.material_id,
          quantity: item.quantity,
        };

        if (item.material.has_serial) {
          if (form.value.movement_type === 'TRANSFER' || form.value.movement_type === 'EXIT') {
            itemObj.serial_ids = item.serials.map(s => s.id).filter(Boolean);
          } else {
            itemObj.serials = item.serials.map(s => s.serial_number);
          }
        }

        return itemObj;
      }),
    };

    const res = await fetch('/api/v1/stock/movement', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload),
    });

    const json = await res.json();
    if (!res.ok) {
      errorMessage.value = json.message || 'Erro ao processar movimentação de estoque.';
      return;
    }

    emit('saved', json.data);
    emit('transferred');
    emit('close');
  } catch (err: any) {
    errorMessage.value = err.message || 'Erro de conexão ao processar movimentação de estoque.';
  } finally {
    submitting.value = false;
  }
}
</script>
