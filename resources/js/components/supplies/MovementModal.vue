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
      <!-- BARRA DE NAVEGAÇÃO POR ABAS DO MODAL FULLSCREEN -->
      <!-- ========================================================================= -->
      <div class="flex border-b border-slate-200 dark:border-slate-800 gap-2 sm:gap-6 overflow-x-auto select-none bg-slate-50/70 dark:bg-slate-900/40 px-3 pt-2 rounded-t-xl">
        <!-- Aba 1: Dados -->
        <button
          type="button"
          @click="currentTab = 'data'"
          :class="currentTab === 'data'
            ? 'border-[#FC6714] text-[#FC6714] font-bold'
            : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
          class="pb-3 border-b-2 text-xs sm:text-sm flex items-center gap-2 transition cursor-pointer shrink-0"
        >
          <SlidersHorizontal class="w-4 h-4" />
          <span>Dados</span>
        </button>

        <!-- Aba 2: Materiais (Grid) -->
        <button
          type="button"
          @click="currentTab = 'materials'"
          :class="currentTab === 'materials'
            ? 'border-[#FC6714] text-[#FC6714] font-bold'
            : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
          class="pb-3 border-b-2 text-xs sm:text-sm flex items-center gap-2 transition cursor-pointer shrink-0"
        >
          <Layers class="w-4 h-4" />
          <span>Materiais</span>
          <span
            v-if="gridItems.length > 0"
            class="px-2 py-0.5 rounded-full text-[11px] font-bold"
            :class="currentTab === 'materials'
              ? 'bg-[#FC6714]/15 text-[#FC6714]'
              : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
          >
            {{ gridItems.length }}
          </span>
        </button>

        <!-- Aba 3: Serializados -->
        <button
          type="button"
          @click="currentTab = 'serials'"
          :class="currentTab === 'serials'
            ? 'border-[#FC6714] text-[#FC6714] font-bold'
            : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
          class="pb-3 border-b-2 text-xs sm:text-sm flex items-center gap-2 transition cursor-pointer shrink-0"
        >
          <QrCode class="w-4 h-4" />
          <span>Serializado</span>
          <span
            v-if="serializedGridItems.length > 0"
            class="px-2 py-0.5 rounded-full text-[11px] font-bold flex items-center gap-1"
            :class="hasPendingSerials
              ? 'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-800 animate-pulse'
              : 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800'"
          >
            <component :is="hasPendingSerials ? AlertCircle : CheckCircle2" class="w-3 h-3" />
            <span>{{ totalBipedSerialsCount }}/{{ totalRequiredSerialsCount }}</span>
          </span>
        </button>

        <!-- Aba 4: Anexos -->
        <button
          type="button"
          @click="currentTab = 'attachments'"
          :class="currentTab === 'attachments'
            ? 'border-[#FC6714] text-[#FC6714] font-bold'
            : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
          class="pb-3 border-b-2 text-xs sm:text-sm flex items-center gap-2 transition cursor-pointer shrink-0"
        >
          <Paperclip class="w-4 h-4" />
          <span>Anexos</span>
          <span
            v-if="form.attachments.length > 0"
            class="px-2 py-0.5 rounded-full text-[11px] font-bold"
            :class="currentTab === 'attachments'
              ? 'bg-[#FC6714]/15 text-[#FC6714]'
              : 'bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
          >
            {{ form.attachments.length }}
          </span>
        </button>
      </div>

      <!-- ========================================================================= -->
      <!-- ABA 1: DADOS DA MOVIMENTAÇÃO -->
      <!-- ========================================================================= -->
      <div v-show="currentTab === 'data'" class="space-y-6">
        <div class="p-5 bg-slate-50/80 dark:bg-slate-800/40 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-5">
          <!-- Tipo de Movimentação de Estoque (Combobox) -->
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              Tipo de Movimentação de Estoque *
            </label>
            <div class="relative">
              <select
                :value="form.movement_type"
                @change="setMovementType(($event.target as HTMLSelectElement).value as MovementType)"
                class="w-full h-11 pl-3.5 pr-10 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm font-semibold focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-hidden cursor-pointer transition appearance-none"
              >
                <option value="ENTRY">Entrada de Estoque</option>
                <option value="EXIT">Saída de Estoque</option>
                <option value="RETURN">Devolução de Estoque</option>
                <option value="TRANSFER">Transferência de Estoque</option>
              </select>
              <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 dark:text-slate-500">
                <ChevronDown class="w-4 h-4" />
              </div>
            </div>
            <p class="mt-2 text-xs font-medium" :class="typeDescriptionColor">
              {{ typeDescriptionText }}
            </p>
          </div>

          <!-- Depósitos de Origem e Destino -->
          <div
            class="grid gap-4 pt-3 border-t border-slate-200/80 dark:border-slate-700/60"
            :class="(form.movement_type === 'ENTRY' || form.movement_type === 'EXIT' || form.movement_type === 'RETURN') ? 'grid-cols-1' : 'grid-cols-1 md:grid-cols-2'"
          >
            <!-- ORIGEM (Oculto em Entrada de Estoque) -->
            <div v-if="form.movement_type !== 'ENTRY'">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Depósito de Origem *
              </label>

              <select
                v-model="form.source_depot_id"
                :required="isSourceRequired"
                @change="onHeaderSourceChange"
                class="w-full h-11 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs sm:text-sm focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-hidden cursor-pointer"
              >
                <option value="" disabled>
                  {{ form.movement_type === 'RETURN' ? 'Selecione o depósito interno de onde sairá a devolução...' : 'Selecione o local de saída...' }}
                </option>
                <option v-for="d in depots" :key="d.id" :value="String(d.id)">
                  {{ d.name }} ({{ formatDepotType(d.type) }})
                </option>
              </select>
            </div>

            <!-- DESTINO (Oculto em Saída e Devolução ao Proprietário) -->
            <div v-if="form.movement_type !== 'EXIT' && form.movement_type !== 'RETURN'">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Depósito de Destino *
              </label>

              <select
                v-model="form.destination_depot_id"
                :required="isDestRequired"
                class="w-full h-11 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs sm:text-sm focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-hidden cursor-pointer"
              >
                <option value="" disabled>Selecione o local de entrada...</option>
                <option v-for="d in destinationOptions" :key="d.id" :value="String(d.id)">
                  {{ d.name }} ({{ formatDepotType(d.type) }})
                </option>
              </select>
            </div>
          </div>

          <!-- Documento e Datas (3 Colunas) -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-3 border-t border-slate-200/80 dark:border-slate-700/60">
            <!-- Número do Documento -->
            <div>
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Número do Documento
              </label>
              <input
                type="text"
                v-model="form.document_number"
                :placeholder="docRefPlaceholder"
                class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none"
              />
            </div>

            <!-- Data do Documento -->
            <div>
              <DateInput
                v-model="form.document_date"
                label="Data do Documento"
              />
            </div>

            <!-- Data da Movimentação -->
            <div>
              <DateInput
                v-model="form.movement_date"
                label="Data da Movimentação"
                required
              />
            </div>
          </div>

          <!-- Pessoas: Recebedor e Motorista (2 Colunas com Campo de Pesquisa) -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3 border-t border-slate-200/80 dark:border-slate-700/60">
            <!-- Recebedor -->
            <div>
              <PersonSearchSelect
                v-model="form.receiver_person_id"
                label="Recebedor (Pessoa / Colaborador)"
                placeholder="Digite ao menos 2 letras para buscar o recebedor..."
              />
            </div>

            <!-- Motorista -->
            <div>
              <PersonSearchSelect
                v-model="form.driver_person_id"
                label="Motorista / Transportador"
                placeholder="Digite ao menos 2 letras para buscar o motorista..."
              />
            </div>
          </div>

          <!-- Anotações da Movimentação -->
          <div class="pt-3 border-t border-slate-200/80 dark:border-slate-700/60">
            <div class="flex items-center gap-2 mb-1.5">
              <FileText class="w-4 h-4 text-[#FC6714]" />
              <label class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-xs">
                Anotações Operacionais da Movimentação
              </label>
            </div>
            <textarea
              v-model="form.notes"
              rows="3"
              :placeholder="notesPlaceholder"
              class="w-full p-3.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-none resize-none leading-relaxed"
            ></textarea>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- ABA 2: MATERIAIS (GRID DE MOVIMENTAÇÃO) -->
      <!-- ========================================================================= -->
      <div v-show="currentTab === 'materials'" class="space-y-6">
        <!-- Linha de Inserção de Material -->
        <div class="p-5 bg-white dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
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

          <!-- Banner de Operação Vinculada a Proprietário -->
          <div
            v-if="activeOperatingOwner"
            class="p-3 rounded-xl border border-blue-200 dark:border-blue-800/60 bg-blue-50/60 dark:bg-blue-950/25 flex items-center justify-between gap-3 text-xs"
          >
            <div class="flex items-center gap-2 text-blue-900 dark:text-blue-300 font-medium">
              <UserCheck class="w-4 h-4 text-blue-600 shrink-0" />
              <span>
                <strong>Operação com Proprietário:</strong> Depósito vinculado a <strong>{{ activeOperatingOwner.code }} — {{ activeOperatingOwner.name }}</strong>. Códigos e nomes do catálogo deste proprietário ativos.
              </span>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-200/70 dark:bg-blue-900 text-blue-800 dark:text-blue-200 shrink-0">
              Códigos do Proprietário Ativos
            </span>
          </div>

          <!-- Linha: Campo Novo Material (Componente) + Campo Quantidade + Botão Adicionar -->
          <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <!-- Campo 1: Material (8 colunas) com busca estilo cidade/pessoa -->
            <div class="md:col-span-8">
              <MaterialSearchSelect
                v-model="newItem.material_id"
                :materials-list="materials"
                :owner-id="activeOperatingOwner?.id"
                :depot-id="activeOperatingDepotId"
                label="Novo Material"
                required
                :placeholder="activeOperatingOwner ? `Buscar por códigos ou descrição da ${activeOperatingOwner.name}...` : 'Digite nome, código SKU ou categoria do material...'"
                @select="onMaterialSelected"
              />
            </div>

            <!-- Campo 2: Quantidade (2 colunas) -->
            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                Quantidade *
              </label>
              <input
                type="number"
                :step="selectedNewItemMaterial?.has_serial ? '1' : '0.01'"
                min="1"
                v-model.number="newItem.quantity"
                placeholder="Qtd"
                class="w-full h-11 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-mono text-xs font-bold focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] outline-hidden"
              />
            </div>

            <!-- Botão 3: Adicionar Material (2 colunas) -->
            <div class="md:col-span-2">
              <button
                type="button"
                @click="addMaterialToGrid"
                :disabled="!newItem.material_id || newItem.quantity <= 0"
                class="w-full h-11 px-4 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] disabled:opacity-50 text-white text-xs font-bold shadow-xs transition active:scale-98 cursor-pointer flex items-center justify-center gap-1.5"
              >
                <Plus class="w-4 h-4" />
                <span>Adicionar</span>
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
                <strong>Rastreamento Serial Obrigatório:</strong> A quantidade informada define que devem ser informados e bipados exatamente <strong>{{ newItem.quantity || 1 }}</strong> seriais deste item na aba <strong>Serializado</strong>.
              </span>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-200/70 dark:bg-purple-900 text-purple-800 dark:text-purple-200 shrink-0">
              Conferência Obrigatória
            </span>
          </div>
        </div>

        <!-- Grid de Conferência de Materiais -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/50 shadow-xs overflow-hidden">
          <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-900/70 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
              <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                Materiais da Movimentação (Grid de Conferência)
              </h4>
              <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                {{ gridItems.length }} item(ns)
              </span>
            </div>

            <div v-if="hasPendingSerials" class="flex items-center gap-1.5 text-xs text-amber-600 dark:text-amber-400 font-bold">
              <AlertCircle class="w-4 h-4 animate-pulse" />
              <span>{{ pendingSerialsCount }} serial(is) pendente(s) na aba Serializado</span>
            </div>
            <div v-else-if="gridItems.length > 0" class="flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-bold">
              <CheckCircle2 class="w-4 h-4" />
              <span>Todos os materiais validados</span>
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
                <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-100/50 dark:bg-slate-900/40 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider select-none">
                  <th class="py-3 px-4 w-12 text-center">#</th>
                  <th class="py-3 px-4">Material / SKU</th>
                  <th class="py-3 px-4 w-36">Tipo de Rastreio</th>
                  <th class="py-3 px-4 w-28 text-right">Qtd</th>
                  <th class="py-3 px-4 w-60">Conferência Serial</th>
                  <th class="py-3 px-4 w-16 text-center">Ações</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                <tr
                  v-for="(item, idx) in gridItems"
                  :key="item.uid"
                  class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors duration-150"
                >
                  <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                    {{ idx + 1 }}
                  </td>
                  <td class="py-3.5 px-4">
                    <div class="font-bold text-slate-900 dark:text-slate-100">
                      {{ item.material.name }}
                    </div>
                    <div class="text-[11px] text-slate-400 font-mono mt-0.5 flex items-center gap-1.5 flex-wrap">
                      <span :class="item.material.has_owner_alias ? 'text-[#FC6714] font-bold' : ''">
                        {{ item.material.has_owner_alias ? `Cód. Proprietário: ${item.material.code}` : `SKU: ${item.material.code}` }}
                      </span>
                      <span v-if="item.material.has_owner_alias && item.material.system_code" class="text-slate-400">
                        (SKU: {{ item.material.system_code }})
                      </span>
                      <span>• {{ item.material.unit?.code || 'UND' }}</span>
                    </div>
                  </td>
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
                  <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 dark:text-slate-100 text-sm">
                    {{ item.quantity }}
                  </td>
                  <td class="py-3.5 px-4">
                    <span v-if="!item.material.has_serial" class="text-slate-400 italic text-[11px]">
                      Não aplicável (sem serial)
                    </span>
                    <div v-else class="flex items-center gap-2">
                      <button
                        type="button"
                        @click="currentTab = 'serials'"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold transition cursor-pointer"
                        :class="item.serials.length === item.quantity
                          ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800'
                          : 'bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-300 dark:border-amber-800 hover:bg-amber-200'"
                        title="Ir para a aba Serializado para bipar este material"
                      >
                        <component :is="item.serials.length === item.quantity ? CheckCircle2 : AlertCircle" class="w-3.5 h-3.5" />
                        <span>{{ item.serials.length }} de {{ item.quantity }} bipado(s)</span>
                        <ArrowRight class="w-3 h-3 opacity-70" />
                      </button>
                    </div>
                  </td>
                  <td class="py-3.5 px-4 text-center">
                    <button
                      type="button"
                      @click="removeGridItem(idx)"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer"
                      title="Remover material da grid"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Rodapé Interno da Grid com Totais -->
          <div v-if="gridItems.length > 0" class="p-4 bg-slate-50/70 dark:bg-slate-900/60 border-t border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4 text-xs font-semibold">
            <div class="flex items-center gap-4 text-slate-600 dark:text-slate-400">
              <span>Total de Materiais: <strong>{{ gridItems.length }}</strong></span>
              <span>Total de Peças: <strong>{{ totalPiecesCount }}</strong></span>
              <span>Itens Serializados: <strong>{{ totalSerializedCount }}</strong></span>
            </div>

            <div v-if="hasPendingSerials" class="text-rose-600 dark:text-rose-400 font-bold flex items-center gap-1.5">
              <AlertCircle class="w-4 h-4" />
              <span>Existem seriais a serem bipados na aba Serializado</span>
            </div>
            <div v-else class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1.5">
              <CheckCircle2 class="w-4 h-4" />
              <span>Grid pronta para submissão</span>
            </div>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- ABA 3: SERIALIZADOS (CONFERÊNCIA E BIPAGEM DE SERIAIS) -->
      <!-- ========================================================================= -->
      <div v-show="currentTab === 'serials'" class="space-y-6">
        <!-- Se não houver itens serializados na grid -->
        <div
          v-if="serializedGridItems.length === 0"
          class="p-12 bg-white dark:bg-slate-900/50 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 text-center space-y-4"
        >
          <div class="w-16 h-16 rounded-2xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 flex items-center justify-center mx-auto">
            <QrCode class="w-8 h-8" />
          </div>
          <div class="space-y-1">
            <h4 class="text-base font-bold text-slate-900 dark:text-white">
              Nenhum material com rastreamento serial na grid
            </h4>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
              Os materiais adicionados até o momento não exigem controle serial individual. Caso precise movimentar ONUs, roteadores ou outros itens com serial obrigatório, adicione-os na aba Materiais.
            </p>
          </div>
          <div>
            <button
              type="button"
              @click="currentTab = 'materials'"
              class="h-10 px-4 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-xs transition cursor-pointer inline-flex items-center gap-2"
            >
              <Plus class="w-4 h-4" />
              <span>Ir para a Aba de Materiais</span>
            </button>
          </div>
        </div>

        <!-- Se HOUVER itens serializados na grid -->
        <div v-else class="space-y-5">
          <!-- Cabeçalho de Status Geral de Bipagem -->
          <div class="p-4 bg-gradient-to-r from-purple-900/10 via-purple-800/5 to-transparent rounded-2xl border border-purple-200 dark:border-purple-800/80 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center shadow-xs">
                <ScanBarcode class="w-5 h-5" />
              </div>
              <div>
                <h4 class="text-sm font-bold text-purple-950 dark:text-purple-200 uppercase tracking-wider">
                  Conferência e Bipagem de Seriais
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                  {{ hasPendingSerials ? `Faltam ${pendingSerialsCount} número(s) de série a serem bipados para liberar a movimentação.` : 'Todos os seriais obrigatórios foram bipados e validados com sucesso!' }}
                </p>
              </div>
            </div>

            <!-- Progresso Geral -->
            <div class="flex items-center gap-3">
              <div class="text-right">
                <span class="block text-xs font-mono font-bold" :class="hasPendingSerials ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'">
                  {{ totalBipedSerialsCount }} / {{ totalRequiredSerialsCount }} seriais
                </span>
                <span class="text-[11px] text-slate-400">
                  {{ serialsProgressPercent }}% conferido
                </span>
              </div>
              <div class="w-24 h-2.5 rounded-full bg-slate-200 dark:bg-slate-800 overflow-hidden">
                <div
                  class="h-full transition-all duration-300"
                  :class="hasPendingSerials ? 'bg-amber-500' : 'bg-emerald-500'"
                  :style="{ width: `${serialsProgressPercent}%` }"
                ></div>
              </div>
            </div>
          </div>

          <!-- Cards Individuais de Cada Item Serializado -->
          <div class="space-y-4">
            <div
              v-for="item in serializedGridItems"
              :key="item.uid"
              class="p-5 bg-white dark:bg-slate-900/50 rounded-2xl border transition-all duration-150 shadow-xs space-y-4"
              :class="item.serials.length === item.quantity
                ? 'border-emerald-300 dark:border-emerald-800/80 bg-emerald-50/20 dark:bg-emerald-950/10'
                : 'border-purple-200 dark:border-purple-800/70'"
            >
              <!-- Cabeçalho do Card do Item -->
              <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 flex items-center justify-center font-bold text-xs">
                    <QrCode class="w-4 h-4" />
                  </div>
                  <div>
                    <h5 class="text-sm font-bold text-slate-900 dark:text-white">
                      {{ item.material.name }}
                    </h5>
                    <div class="text-[11px] text-slate-400 font-mono">
                      SKU: {{ item.material.code }} • Exigidos: <strong>{{ item.quantity }} {{ item.material.unit?.code || 'UND' }}</strong>
                    </div>
                  </div>
                </div>

                <!-- Badge de Status do Item -->
                <div class="flex items-center gap-2">
                  <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold"
                    :class="item.serials.length === item.quantity
                      ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800'
                      : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-300 dark:border-amber-800'"
                  >
                    <component :is="item.serials.length === item.quantity ? CheckCircle2 : AlertCircle" class="w-3.5 h-3.5" />
                    <span>{{ item.serials.length }} de {{ item.quantity }} bipado(s)</span>
                  </span>

                  <button
                    v-if="item.serials.length > 0"
                    type="button"
                    @click="clearItemSerials(item)"
                    class="p-1.5 text-xs text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-lg transition cursor-pointer"
                    title="Limpar todos os seriais bipados deste item"
                  >
                    Limpar
                  </button>
                </div>
              </div>

              <!-- Linha de Entrada de Bipagem Rápida / Leitor Óptico -->
              <div class="space-y-2">
                <div class="flex items-center gap-2">
                  <div class="relative flex-1">
                    <ScanBarcode class="w-4 h-4 absolute left-3.5 top-3 text-purple-500 pointer-events-none" />
                    <input
                      type="text"
                      v-model="item.scannerInput"
                      @keydown.enter.prevent="onScanBarcodeEnter(item)"
                      :placeholder="item.serials.length < item.quantity ? `Bipe o serial ${item.serials.length + 1} de ${item.quantity} ou digite e tecle Enter...` : 'Todos os seriais deste material já foram conferidos'"
                      :disabled="item.serials.length >= item.quantity"
                      class="w-full h-10 pl-10 pr-3 rounded-xl border border-purple-300 dark:border-purple-700 bg-purple-50/40 dark:bg-slate-900 text-xs font-mono font-medium focus:ring-2 focus:ring-purple-400 outline-none"
                    />
                  </div>

                  <button
                    type="button"
                    @click="onScanBarcodeEnter(item)"
                    :disabled="!item.scannerInput?.trim() || item.serials.length >= item.quantity"
                    class="h-10 px-5 rounded-xl bg-purple-600 hover:bg-purple-700 active:bg-purple-800 disabled:opacity-40 text-white text-xs font-bold transition cursor-pointer flex items-center gap-1.5 shrink-0"
                  >
                    <Plus class="w-4 h-4" />
                    <span>Bipar</span>
                  </button>
                </div>

                <!-- Mensagem de Erro de Bipagem -->
                <div v-if="item.scanError" class="p-2.5 rounded-lg bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900 text-[11px] font-bold text-rose-600 dark:text-rose-300 flex items-center gap-2">
                  <AlertCircle class="w-4 h-4 shrink-0 text-rose-500" />
                  <span>{{ item.scanError }}</span>
                </div>
              </div>

              <!-- Lista de Seriais Já Bipados -->
              <div>
                <div class="flex items-center justify-between mb-2">
                  <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    Seriais Registrados ({{ item.serials.length }} de {{ item.quantity }}):
                  </span>
                  <span v-if="item.serials.length < item.quantity" class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold">
                    Restam {{ item.quantity - item.serials.length }} serial(is)
                  </span>
                  <span v-else class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                    <CheckCircle2 class="w-3.5 h-3.5" />
                    <span>Conferência 100% completa</span>
                  </span>
                </div>

                <div v-if="item.serials.length === 0" class="p-4 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 text-center text-slate-400 text-xs">
                  Nenhum serial bipado ainda para este material. Utilize o campo acima com seu leitor óptico.
                </div>

                <div v-else class="flex flex-wrap gap-2">
                  <div
                    v-for="(s, sIdx) in item.serials"
                    :key="sIdx"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-purple-50 dark:bg-purple-950/70 border border-purple-200 dark:border-purple-800 text-purple-900 dark:text-purple-200 text-xs font-mono font-medium shadow-2xs"
                  >
                    <span class="w-4 h-4 rounded-full bg-purple-200 dark:bg-purple-900 text-purple-800 dark:text-purple-200 flex items-center justify-center text-[10px] font-bold">
                      {{ sIdx + 1 }}
                    </span>
                    <span class="font-bold">{{ s.serial_number || s }}</span>
                    <span v-if="s.mac_address" class="text-[10px] text-purple-500 dark:text-purple-400">
                      [{{ s.mac_address }}]
                    </span>
                    <button
                      type="button"
                      @click="removeSerialFromItem(item, sIdx)"
                      class="text-purple-400 hover:text-rose-600 dark:hover:text-rose-400 p-0.5 rounded transition cursor-pointer"
                      title="Remover este serial"
                    >
                      <X class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ========================================================================= -->
      <!-- ABA 4: ANEXOS (DOCUMENTOS E FOTOS) -->
      <!-- ========================================================================= -->
      <div v-show="currentTab === 'attachments'" class="space-y-6">
        <div class="p-5 bg-white dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-4">
          <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2">
              <Paperclip class="w-4 h-4 text-[#FC6714]" />
              <h4 class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-xs">
                Anexos da Movimentação (Documentos e Fotos)
              </h4>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
              {{ form.attachments.length }} anexo(s)
            </span>
          </div>

          <!-- Dropzone e Seletor de Arquivos -->
          <div
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="onDropFiles"
            @click="triggerFileInput"
            class="p-8 rounded-2xl border-2 border-dashed transition cursor-pointer flex flex-col items-center justify-center text-center gap-2"
            :class="isDragging
              ? 'border-[#FC6714] bg-[#FC6714]/10 dark:bg-[#FC6714]/15'
              : 'border-slate-300 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-900/70 hover:border-[#FC6714]/60 hover:bg-slate-100/50 dark:hover:bg-slate-800/40'"
          >
            <input
              ref="fileInputRef"
              type="file"
              multiple
              accept="image/*,.pdf,.xml,.doc,.docx,.xls,.xlsx"
              @change="onFileChange"
              class="hidden"
            />
            <div class="w-12 h-12 rounded-full bg-orange-100 dark:bg-[#FC6714]/20 text-[#FC6714] flex items-center justify-center">
              <UploadCloud class="w-6 h-6" />
            </div>
            <div class="space-y-1">
              <p class="text-sm font-bold text-slate-800 dark:text-slate-200">
                Clique para selecionar ou arraste arquivos aqui
              </p>
              <p class="text-xs text-slate-400 dark:text-slate-500">
                Notas Fiscais (PDF / XML), Fotos da carga (JPG, PNG, WEBP), Guias, Planilhas (Máx. 20MB cada)
              </p>
            </div>
          </div>

          <!-- Spinner durante upload de arquivos -->
          <div v-if="isUploadingAttachment" class="flex items-center gap-2 p-3 rounded-xl bg-orange-50 dark:bg-[#FC6714]/10 text-[#FC6714] text-xs font-semibold">
            <Loader2 class="w-4 h-4 animate-spin" />
            <span>Fazendo upload de anexo(s) para o servidor...</span>
          </div>

          <!-- Lista de Arquivos Anexados -->
          <div v-if="form.attachments.length > 0" class="space-y-2 pt-2">
            <h5 class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
              Arquivos Anexados ({{ form.attachments.length }})
            </h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div
                v-for="(att, idx) in form.attachments"
                :key="att.tempId || att.id || idx"
                class="flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900 hover:border-slate-300 transition gap-3"
              >
                <div class="flex items-center gap-3 min-w-0">
                  <!-- Preview se for imagem -->
                  <div v-if="isImageAttachment(att)" class="w-12 h-12 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 bg-slate-100 dark:bg-slate-800">
                    <img :src="att.url" :alt="att.file_name" class="w-full h-full object-cover" />
                  </div>
                  <!-- Ícone se for documento -->
                  <div v-else class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0 text-slate-500 dark:text-slate-400">
                    <component :is="getFileIcon(att.file_name)" class="w-6 h-6 text-[#FC6714]" />
                  </div>

                  <div class="min-w-0">
                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate" :title="att.file_name">
                      {{ att.file_name }}
                    </p>
                    <div class="flex items-center gap-2 text-[11px] text-slate-400 dark:text-slate-500">
                      <span v-if="att.file_size">{{ formatFileSize(att.file_size) }}</span>
                      <span v-if="att.is_uploading" class="text-amber-500 font-medium">Enviando...</span>
                      <span v-else class="text-emerald-600 dark:text-emerald-400 font-medium">Pronto</span>
                    </div>
                  </div>
                </div>

                <!-- Ações do Anexo -->
                <div class="flex items-center gap-1 shrink-0">
                  <a
                    v-if="att.url && !att.is_uploading"
                    :href="att.url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-200/60 dark:hover:bg-slate-800 transition cursor-pointer"
                    title="Visualizar ou baixar anexo"
                  >
                    <ExternalLink class="w-4 h-4" />
                  </a>
                  <button
                    type="button"
                    @click="removeAttachment(idx)"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer"
                    title="Remover anexo"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- RODAPÉ DO MODAL FULLSCREEN -->
    <template #footer>
      <div class="w-full flex flex-wrap items-center justify-between gap-4">
        <div class="text-xs text-slate-500 dark:text-slate-400 font-medium flex flex-wrap items-center gap-4">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Transação atômica única</span>
          </div>

          <div class="flex items-center gap-2.5 text-[11px] font-semibold">
            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
              {{ gridItems.length }} material(is)
            </span>
            <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
              {{ totalPiecesCount }} peça(s)
            </span>
            <span
              v-if="serializedGridItems.length > 0"
              class="px-2 py-0.5 rounded-md font-bold flex items-center gap-1"
              :class="hasPendingSerials
                ? 'bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-800'
                : 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800'"
            >
              <component :is="hasPendingSerials ? AlertCircle : CheckCircle2" class="w-3 h-3" />
              <span>Seriais: {{ totalBipedSerialsCount }}/{{ totalRequiredSerialsCount }}</span>
            </span>
            <span v-if="form.attachments.length > 0" class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
              {{ form.attachments.length }} anexo(s)
            </span>
          </div>
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
            :disabled="submitting || gridItems.length === 0 || hasPendingSerials || isUploadingAttachment"
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
import axios from 'axios';
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
  FileText,
  Paperclip,
  UploadCloud,
  ExternalLink,
  FileCode,
  FileSpreadsheet,
  File as FileIcon,
  Loader2,
  SlidersHorizontal,
  ArrowRight,
  ChevronDown,
  UserCheck,
} from 'lucide-vue-next';
import BaseModal from '@/components/common/BaseModal.vue';
import PersonSearchSelect from '@/components/common/PersonSearchSelect.vue';
import MaterialSearchSelect from '@/components/common/MaterialSearchSelect.vue';
import DateInput from '@/components/common/DateInput.vue';

type MovementType = 'ENTRY' | 'EXIT' | 'RETURN' | 'TRANSFER';
type ModalTab = 'data' | 'materials' | 'serials' | 'attachments';

const currentTab = ref<ModalTab>('data');

interface AttachmentItem {
  tempId?: string;
  id?: number;
  file_name: string;
  file_path: string;
  file_type?: string;
  file_size?: number;
  url?: string;
  description?: string;
  is_uploading?: boolean;
}

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
  document_number: '',
  document_date: '',
  movement_date: '',
  receiver_person_id: null as number | null,
  driver_person_id: null as number | null,
  notes: '',
  attachments: [] as AttachmentItem[],
});

// Anexos e upload
const fileInputRef = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);
const isUploadingAttachment = ref(false);

// Grid de materiais da movimentação
const gridItems = ref<GridItem[]>([]);

// Linha de novo material para adicionar na grid
const newItem = ref({
  material_id: '' as number | '',
  quantity: 1,
});

const selectedMaterialObject = ref<any>(null);

const selectedNewItemMaterial = computed(() => {
  if (!newItem.value.material_id) return null;
  if (selectedMaterialObject.value && selectedMaterialObject.value.id === Number(newItem.value.material_id)) {
    return selectedMaterialObject.value;
  }
  return props.materials.find(m => m.id === Number(newItem.value.material_id)) || null;
});

// Opções dinâmicas de destino (não permite mesmo depósito se for transferência)
const destinationOptions = computed(() => {
  if (form.value.movement_type === 'TRANSFER' && form.value.source_depot_id) {
    return props.depots.filter(d => String(d.id) !== form.value.source_depot_id);
  }
  return props.depots;
});

// Depósito operacional e Proprietário ativos para resolução contextual de códigos e nomes
const activeOperatingDepotId = computed(() => {
  if (form.value.movement_type === 'ENTRY') {
    return form.value.destination_depot_id ? Number(form.value.destination_depot_id) : null;
  }
  return form.value.source_depot_id ? Number(form.value.source_depot_id) : null;
});

const activeOperatingOwner = computed(() => {
  if (!activeOperatingDepotId.value || !props.depots) return null;
  const depot = props.depots.find((d: any) => d.id === activeOperatingDepotId.value);
  if (depot?.owner) return depot.owner;
  if (depot?.cluster?.owner) return depot.cluster.owner;
  if (depot?.effective_owner) return depot.effective_owner;
  return null;
});

const isSourceRequired = computed(() => {
  return form.value.movement_type === 'TRANSFER' || form.value.movement_type === 'EXIT' || form.value.movement_type === 'RETURN';
});

const isDestRequired = computed(() => {
  return form.value.movement_type === 'TRANSFER' || form.value.movement_type === 'ENTRY';
});

const typeLabel = computed(() => {
  const map: Record<MovementType, string> = {
    ENTRY: 'Entrada de Estoque',
    EXIT: 'Saída de Estoque',
    RETURN: 'Devolução ao Proprietário',
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
      return 'Baixa de itens do depósito (ordens de serviço, requisições de campo ou aplicação operacional).';
    case 'RETURN':
      return 'Devolução de materiais ao proprietário (saída do depósito interno com baixa documental).';
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
    case 'RETURN': return 'Ex: NF-e de Devolução ou Guia de Remessa ao Proprietário';
    case 'TRANSFER': default: return 'Ex: REQ-2026-042 ou Guia de Transferência';
  }
});

const notesPlaceholder = computed(() => {
  switch (form.value.movement_type) {
    case 'ENTRY': return 'Descreva detalhes da carga recebida, fornecedor ou lote...';
    case 'EXIT': return 'Descreva o técnico solicitante, cliente ou veículo de destino...';
    case 'RETURN': return 'Descreva o motivo da devolução ao proprietário, dados da remessa ou transportador...';
    case 'TRANSFER': default: return 'Descreva o motivo da transferência física ou logística interna...';
  }
});

// Computeds para Itens Serializados e Bipagem
const serializedGridItems = computed(() => {
  return gridItems.value.filter(item => item.material?.has_serial);
});

const totalRequiredSerialsCount = computed(() => {
  return serializedGridItems.value.reduce((acc, item) => acc + Number(item.quantity || 0), 0);
});

const totalBipedSerialsCount = computed(() => {
  return serializedGridItems.value.reduce((acc, item) => acc + (item.serials?.length || 0), 0);
});

const pendingSerialsCount = computed(() => {
  return Math.max(0, totalRequiredSerialsCount.value - totalBipedSerialsCount.value);
});

const serialsProgressPercent = computed(() => {
  if (totalRequiredSerialsCount.value === 0) return 100;
  return Math.round((totalBipedSerialsCount.value / totalRequiredSerialsCount.value) * 100);
});

const hasPendingSerials = computed(() => {
  return pendingSerialsCount.value > 0;
});

const totalPiecesCount = computed(() => {
  return gridItems.value.reduce((acc, item) => acc + Number(item.quantity || 0), 0);
});

const totalSerializedCount = computed(() => {
  return serializedGridItems.value.length;
});

function clearItemSerials(item: GridItem) {
  item.serials = [];
  item.scanError = '';
}

function setMovementType(type: MovementType) {
  form.value.movement_type = type;
  errorMessage.value = '';
  if (type === 'ENTRY') {
    form.value.source_depot_id = '';
    if (!form.value.destination_depot_id && props.depots.length > 0) {
      form.value.destination_depot_id = String(props.depots[0].id);
    }
  } else if (type === 'EXIT' || type === 'RETURN') {
    form.value.destination_depot_id = '';
    if (!form.value.source_depot_id && props.depots.length > 0) {
      form.value.source_depot_id = String(props.depots[0].id);
    }
  }
}

function onHeaderSourceChange() {
  // Limpa seriais bipados na grid se a origem mudou em transferências/saídas/devoluções
  if (form.value.movement_type === 'TRANSFER' || form.value.movement_type === 'EXIT' || form.value.movement_type === 'RETURN') {
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

function onMaterialSelected(mat: any) {
  selectedMaterialObject.value = mat;
  if (mat?.has_serial && (!newItem.value.quantity || newItem.value.quantity < 1)) {
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

  const mat = selectedNewItemMaterial.value || props.materials.find(m => m.id === Number(newItem.value.material_id));
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
    showScanner: Boolean(mat.has_serial), // abre o scanner imediatamente se for serializado
  });

  // Limpa o seletor da linha de inserção
  newItem.value.material_id = '';
  newItem.value.quantity = 1;
  selectedMaterialObject.value = null;
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

  // Se a operação for TRANSFER, EXIT ou RETURN: o serial DEVE existir no depósito interno de origem com status IN_STOCK
  if (form.value.movement_type === 'TRANSFER' || form.value.movement_type === 'EXIT' || form.value.movement_type === 'RETURN') {
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

  // Para ENTRY: aceita o serial bipado
  item.serials.push({
    serial_number: raw,
  });
  item.scannerInput = '';
}

function removeSerialFromItem(item: GridItem, serialIndex: number) {
  item.serials.splice(serialIndex, 1);
  item.scanError = '';
}

// Manipulação e upload de anexos (documentos e fotos)
function triggerFileInput() {
  fileInputRef.value?.click();
}

function onFileChange(e: Event) {
  const target = e.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    uploadFiles(target.files);
    target.value = '';
  }
}

function onDropFiles(e: DragEvent) {
  isDragging.value = false;
  if (e.dataTransfer?.files && e.dataTransfer.files.length > 0) {
    uploadFiles(e.dataTransfer.files);
  }
}

async function uploadFiles(fileList: FileList | File[]) {
  const files = Array.from(fileList);
  if (files.length === 0) return;

  isUploadingAttachment.value = true;
  errorMessage.value = '';

  for (const file of files) {
    const tempId = `temp-${Date.now()}-${Math.random().toString(36).slice(2, 7)}`;
    const isImg = file.type.startsWith('image/');
    const localUrl = isImg ? URL.createObjectURL(file) : '';

    const newItem: AttachmentItem = {
      tempId,
      file_name: file.name,
      file_path: '',
      file_type: file.type,
      file_size: file.size,
      url: localUrl,
      is_uploading: true,
    };

    form.value.attachments.push(newItem);

    try {
      const formData = new FormData();
      formData.append('file', file);

      const response = await axios.post('/api/v1/stock/attachments', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });

      if (response.data?.data) {
        newItem.file_path = response.data.data.file_path;
        newItem.url = response.data.data.url;
        newItem.file_name = response.data.data.file_name;
        newItem.file_size = response.data.data.file_size;
        newItem.file_type = response.data.data.file_type;
        newItem.is_uploading = false;
      }
    } catch (err: any) {
      console.error('Erro ao enviar anexo:', err);
      form.value.attachments = form.value.attachments.filter(a => a.tempId !== tempId);
      errorMessage.value = `Falha ao anexar '${file.name}': ` + (err.response?.data?.message || err.message);
    }
  }

  isUploadingAttachment.value = false;
}

function removeAttachment(index: number) {
  form.value.attachments.splice(index, 1);
}

function formatFileSize(bytes?: number): string {
  if (!bytes) return '';
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

function isImageAttachment(att: AttachmentItem): boolean {
  if (att.file_type && att.file_type.startsWith('image/')) return true;
  const name = (att.file_name || '').toLowerCase();
  return (
    name.endsWith('.jpg') ||
    name.endsWith('.jpeg') ||
    name.endsWith('.png') ||
    name.endsWith('.webp') ||
    name.endsWith('.gif')
  );
}

function getFileIcon(fileName: string) {
  const name = (fileName || '').toLowerCase();
  if (name.endsWith('.pdf')) return FileText;
  if (name.endsWith('.xml') || name.endsWith('.json')) return FileCode;
  if (name.endsWith('.xls') || name.endsWith('.xlsx') || name.endsWith('.csv')) return FileSpreadsheet;
  return FileIcon;
}

function parseDateToIso(dateStr: string | null | undefined): string | null {
  if (!dateStr) return null;
  const clean = dateStr.trim();
  if (/^\d{4}-\d{2}-\d{2}$/.test(clean)) return clean;
  const parts = clean.split('/');
  if (parts.length === 3 && parts[2].length === 4) {
    return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
  }
  return null;
}

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      errorMessage.value = '';
      currentTab.value = 'data';
      const initialType = props.initialType || 'TRANSFER';

      const defaultSource = props.initialDepot?.id
        ? String(props.initialDepot.id)
        : (props.depots[0]?.id ? String(props.depots[0].id) : '');

      const otherDepot = props.depots.find(d => String(d.id) !== defaultSource);
      const defaultDest = otherDepot?.id ? String(otherDepot.id) : (props.depots[1]?.id ? String(props.depots[1].id) : '');

      const today = new Date();
      const day = String(today.getDate()).padStart(2, '0');
      const month = String(today.getMonth() + 1).padStart(2, '0');
      const year = today.getFullYear();
      const todayFormatted = `${day}/${month}/${year}`;

      form.value = {
        movement_type: initialType,
        source_depot_id: initialType === 'ENTRY' ? '' : defaultSource,
        destination_depot_id: (initialType === 'EXIT' || initialType === 'RETURN') ? '' : defaultDest,
        document_ref: '',
        document_number: '',
        document_date: '',
        movement_date: todayFormatted,
        receiver_person_id: null,
        driver_person_id: null,
        notes: '',
        attachments: [],
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
      selectedMaterialObject.value = null;
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
  } else if (form.value.movement_type === 'EXIT' || form.value.movement_type === 'RETURN') {
    if (!form.value.source_depot_id) {
      errorMessage.value = form.value.movement_type === 'RETURN'
        ? 'Selecione o depósito interno de onde os materiais serão devolvidos ao proprietário.'
        : 'Selecione o depósito de onde os materiais sairão.';
      return;
    }
  } else if (form.value.movement_type === 'ENTRY') {
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

  if (form.value.attachments.some(a => a.is_uploading)) {
    errorMessage.value = 'Aguarde a conclusão do upload de todos os anexos antes de salvar.';
    return;
  }

  submitting.value = true;
  try {
    const docNumber = form.value.document_number || form.value.document_ref || null;

    // Monta o payload em lote (items)
    const payload: any = {
      movement_type: form.value.movement_type,
      source_depot_id: form.value.source_depot_id ? Number(form.value.source_depot_id) : null,
      destination_depot_id: form.value.destination_depot_id ? Number(form.value.destination_depot_id) : null,
      document_ref: docNumber,
      document_number: docNumber,
      document_date: parseDateToIso(form.value.document_date),
      movement_date: parseDateToIso(form.value.movement_date),
      receiver_person_id: form.value.receiver_person_id || null,
      driver_person_id: form.value.driver_person_id || null,
      notes: form.value.notes || null,
      attachments: form.value.attachments
        .filter(a => !a.is_uploading && a.file_path)
        .map(a => ({
          file_name: a.file_name,
          file_path: a.file_path,
          file_type: a.file_type,
          file_size: a.file_size,
          description: a.description || null,
        })),
      items: gridItems.value.map(item => {
        const itemObj: any = {
          material_id: item.material_id,
          quantity: item.quantity,
        };

        if (item.material.has_serial) {
          if (form.value.movement_type === 'TRANSFER' || form.value.movement_type === 'EXIT' || form.value.movement_type === 'RETURN') {
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
