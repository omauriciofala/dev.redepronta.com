<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="onModalClose"
    :title="isPreviewMode ? `Pré-visualização da Importação — ${owner?.code || ''}` : `Catálogo de Materiais — ${owner?.code || ''}`"
    :description="isPreviewMode ? 'Confira o mapeamento das 3 colunas e novos códigos gerados antes de gravar' : `Mapeamento de códigos e descrições técnicas do proprietário ${owner?.name || ''}`"
    size="fullscreen"
  >
    <!-- Header Badge -->
    <template #header-badge>
      <span
        v-if="isPreviewMode"
        class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-[#FC6714] border border-orange-200 dark:bg-orange-950/60 dark:text-orange-300 dark:border-orange-800"
      >
        <FileSpreadsheet class="w-3.5 h-3.5" />
        <span>Importação de Planilha</span>
      </span>
      <span
        v-else
        class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800"
      >
        <BookOpen class="w-3.5 h-3.5" />
        <span>{{ items.length }} materiais</span>
      </span>
    </template>

    <!-- ========================================================================= -->
    <!-- TELA 1: PRÉ-VISUALIZAÇÃO DA IMPORTAÇÃO (PREVIEW ANTES DE GRAVAR)          -->
    <!-- ========================================================================= -->
    <div v-if="isPreviewMode" class="space-y-4">
      <!-- Cabeçalho do Preview com Nome do Arquivo e Resumo Rápido -->
      <div class="p-3.5 rounded-xl bg-orange-50 dark:bg-orange-950/20 border border-orange-200 dark:border-[#FC6714]/30 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <div class="p-2.5 rounded-lg bg-white dark:bg-[#03032E] text-[#FC6714] shadow-2xs">
            <FileSpreadsheet class="w-5 h-5" />
          </div>
          <div>
            <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
              <span>Arquivo: <strong>{{ previewFileName }}</strong></span>
            </h4>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
              Colunas detectadas: <strong class="text-slate-700 dark:text-slate-200">COD.</strong>, <strong class="text-slate-700 dark:text-slate-200">NOME DO MATERIAL</strong> e <strong class="text-slate-700 dark:text-slate-200">COD. PROP.</strong>
            </p>
          </div>
        </div>

        <button
          type="button"
          @click="cancelPreview"
          class="px-3 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 transition cursor-pointer font-medium"
        >
          Descartar Prévia
        </button>
      </div>

      <!-- Cards de Métricas do Preview -->
      <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-xs">
        <div class="p-3 rounded-xl border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#03032E] shadow-2xs">
          <span class="text-[10px] uppercase font-bold text-slate-400 block">Total de Linhas</span>
          <span class="text-xl font-bold text-slate-800 dark:text-slate-100 mt-1 block">{{ previewSummary.total_rows }}</span>
        </div>

        <div class="p-3 rounded-xl border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300 shadow-2xs">
          <span class="text-[10px] uppercase font-bold text-emerald-600 dark:text-emerald-400 block flex items-center gap-1">
            <Sparkles class="w-3.5 h-3.5" />
            <span>Novos Materiais</span>
          </span>
          <span class="text-xl font-bold mt-1 block">{{ previewSummary.to_create_material }}</span>
          <span class="text-[10px] block opacity-80">(cód. 4 dígitos)</span>
        </div>

        <div class="p-3 rounded-xl border border-blue-200 dark:border-blue-900/60 bg-blue-50/50 dark:bg-blue-950/20 text-blue-800 dark:text-blue-300 shadow-2xs">
          <span class="text-[10px] uppercase font-bold text-blue-600 dark:text-blue-400 block">Vincular Existentes</span>
          <span class="text-xl font-bold mt-1 block">{{ previewSummary.to_link_existing }}</span>
        </div>

        <div class="p-3 rounded-xl border border-purple-200 dark:border-purple-900/60 bg-purple-50/50 dark:bg-purple-950/20 text-purple-800 dark:text-purple-300 shadow-2xs">
          <span class="text-[10px] uppercase font-bold text-purple-600 dark:text-purple-400 block">Atualizar Vínculos</span>
          <span class="text-xl font-bold mt-1 block">{{ previewSummary.to_update_link }}</span>
        </div>

        <div class="p-3 rounded-xl border border-rose-200 dark:border-rose-900/60 bg-rose-50/50 dark:bg-rose-950/20 text-rose-800 dark:text-rose-300 shadow-2xs">
          <span class="text-[10px] uppercase font-bold text-rose-600 dark:text-rose-400 block">Erros / Avisos</span>
          <span class="text-xl font-bold mt-1 block">{{ previewSummary.errors_count }}</span>
          <span v-if="previewSummary.errors_count > 0" class="text-[10px] block opacity-80">(serão ignoradas)</span>
        </div>
      </div>

      <!-- Tabela Panorâmica de Preview das Linhas em Tela Cheia -->
      <div class="rounded-xl border border-slate-200 dark:border-[#14147A] overflow-hidden bg-white dark:bg-[#03032E] shadow-2xs">
        <div class="max-h-[calc(100vh-340px)] min-h-[420px] overflow-y-auto overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead class="sticky top-0 bg-slate-50 dark:bg-[#06064D] border-b border-slate-200 dark:border-[#14147A] z-10">
              <tr class="font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[11px]">
                <th class="py-3 px-4 text-center w-14">Linha</th>
                <th class="py-3 px-4">Cód. Sistema (Canônico)</th>
                <th class="py-3 px-4">Nome do Material</th>
                <th class="py-3 px-4">Cód. Proprietário</th>
                <th class="py-3 px-4 text-right">Ação Prevista</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <tr
                v-for="row in previewRows"
                :key="row.line"
                class="hover:bg-slate-50/70 dark:hover:bg-white/5 transition"
                :class="{ 'bg-rose-50/40 dark:bg-rose-950/20': row.status === 'error' }"
              >
                <!-- Linha -->
                <td class="py-3 px-4 text-center font-mono text-slate-400 text-[11px]">
                  #{{ row.line }}
                </td>

                <!-- Cód. Sistema -->
                <td class="py-3 px-4">
                  <div v-if="row.is_generated_code" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded font-mono font-bold text-xs bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                    <Sparkles class="w-3.5 h-3.5 text-emerald-600" />
                    <span>{{ row.system_code }}</span>
                    <span class="text-[10px] font-normal opacity-75">(Gerado 4D)</span>
                  </div>
                  <div v-else-if="row.system_code" class="font-mono font-bold text-slate-700 dark:text-slate-200 text-xs">
                    {{ row.system_code }}
                  </div>
                  <div v-else class="text-slate-400 italic text-[11px]">
                    (Em branco)
                  </div>
                </td>

                <!-- Nome do Material -->
                <td class="py-3 px-4">
                  <div class="font-semibold text-slate-900 dark:text-slate-100">
                    {{ row.owner_name || '-' }}
                  </div>
                  <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">
                    <span v-if="row.name_source === 'inherited'" class="text-blue-600 dark:text-blue-400">
                      • Herdará nome do sistema
                    </span>
                    <span v-else-if="row.name_source === 'new_material'" class="text-emerald-600 dark:text-emerald-400">
                      • Cadastro de novo material
                    </span>
                    <span v-else class="text-slate-500">
                      • Nome customizado no proprietário
                    </span>
                  </div>
                </td>

                <!-- Cód. Proprietário -->
                <td class="py-3 px-4 font-mono font-bold text-[#FC6714]">
                  {{ row.owner_code || '-' }}
                </td>

                <!-- Ação / Status -->
                <td class="py-3 px-4 text-right">
                  <span
                    v-if="row.status === 'valid' && row.action === 'create_material_and_link'"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800"
                  >
                    <Plus class="w-3.5 h-3.5" />
                    <span>Criar Material e Vincular</span>
                  </span>

                  <span
                    v-else-if="row.status === 'valid' && row.action === 'link_existing'"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 border border-blue-300 dark:border-blue-800"
                  >
                    <Check class="w-3.5 h-3.5" />
                    <span>Vincular Existente</span>
                  </span>

                  <span
                    v-else-if="row.status === 'valid' && row.action === 'update_link'"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 border border-purple-300 dark:border-purple-800"
                  >
                    <Edit2 class="w-3.5 h-3.5" />
                    <span>Atualizar Vínculo</span>
                  </span>

                  <div v-else class="text-right">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border border-rose-300 dark:border-rose-800">
                      <AlertCircle class="w-3.5 h-3.5" />
                      <span>Erro</span>
                    </span>
                    <span class="block text-[11px] text-rose-600 dark:text-rose-400 mt-1 max-w-sm ml-auto text-right font-medium">
                      {{ row.message }}
                    </span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TELA 2: LISTAGEM E CADASTRO DO CATÁLOGO DO PROPRIETÁRIO                   -->
    <!-- ========================================================================= -->
    <div v-else class="space-y-4">
      <!-- Barra Superior: Busca, Ações em Lote e Botão Novo -->
      <div class="flex items-center justify-between gap-3 flex-wrap">
        <!-- Campo de Busca -->
        <div class="relative flex-1 min-w-[240px]">
          <Search class="w-4 h-4 absolute left-3 top-3 text-slate-400 pointer-events-none" />
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Buscar por código do proprietário, nome ou SKU do sistema..."
            class="w-full h-10 pl-9 pr-4 rounded-lg border border-slate-200 dark:border-[#14147A] bg-slate-50 dark:bg-[#03032E] text-slate-900 dark:text-slate-100 text-xs focus:ring-2 focus:ring-[#FC6714] focus:outline-none transition"
          />
        </div>

        <!-- Botões de Ação -->
        <div class="flex items-center gap-2">
          <!-- Download Modelo 3 Colunas: Excel e CSV -->
          <button
            type="button"
            @click="downloadTemplate('xlsx')"
            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#03032E] hover:bg-slate-50 dark:hover:bg-white/5 text-slate-700 dark:text-slate-200 text-xs font-medium shadow-2xs transition cursor-pointer"
            title="Baixar planilha Excel modelo (.xlsx) com 3 colunas: COD., NOME DO MATERIAL, COD. PROP."
          >
            <Download class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
            <span>Modelo Excel (.xlsx)</span>
          </button>

          <button
            type="button"
            @click="downloadTemplate('csv')"
            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#03032E] hover:bg-slate-50 dark:hover:bg-white/5 text-slate-700 dark:text-slate-200 text-xs font-medium shadow-2xs transition cursor-pointer"
            title="Baixar planilha CSV modelo (3 colunas: COD., NOME DO MATERIAL, COD. PROP.)"
          >
            <Download class="w-3.5 h-3.5 text-slate-400" />
            <span>Modelo CSV</span>
          </button>

          <!-- Importar Planilha com Preview -->
          <label
            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#03032E] hover:bg-slate-50 dark:hover:bg-white/5 text-slate-700 dark:text-slate-200 text-xs font-medium shadow-2xs transition cursor-pointer"
            title="Importar planilha Excel (.xlsx) ou CSV de materiais com pré-visualização"
          >
            <Loader2 v-if="isPreviewLoading" class="w-3.5 h-3.5 animate-spin text-[#FC6714]" />
            <Upload v-else class="w-3.5 h-3.5 text-[#FC6714]" />
            <span>{{ isPreviewLoading ? 'Analisando...' : 'Importar' }}</span>
            <input
              type="file"
              accept=".xlsx,.csv,.txt"
              class="hidden"
              :disabled="isPreviewLoading"
              @change="handleFileUploadForPreview"
            />
          </label>

          <!-- Botão Novo Vínculo -->
          <button
            v-if="!isFormOpen"
            type="button"
            @click="openCreateForm"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] text-white text-xs font-semibold shadow-xs transition cursor-pointer"
          >
            <Plus class="w-4 h-4" />
            <span>Vincular Material</span>
          </button>
        </div>
      </div>

      <!-- Feedback de Importação / Ações -->
      <div v-if="alertMessage" class="p-3 rounded-lg text-xs flex items-center justify-between gap-2" :class="alertSuccess ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800'">
        <div class="flex items-center gap-2">
          <component :is="alertSuccess ? CheckCircle2 : AlertCircle" class="w-4 h-4 shrink-0" />
          <span>{{ alertMessage }}</span>
        </div>
        <button type="button" @click="alertMessage = ''" class="p-0.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
          <X class="w-3.5 h-3.5" />
        </button>
      </div>

      <!-- Formulário de Cadastro / Edição -->
      <Transition
        enter-active-class="transition duration-150 ease-out"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-100 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
      >
        <div
          v-if="isFormOpen"
          class="p-4 rounded-xl bg-orange-50/50 dark:bg-orange-950/20 border border-orange-200 dark:border-[#FC6714]/30 space-y-3"
        >
          <div class="flex items-center justify-between pb-2 border-b border-orange-200/60 dark:border-[#FC6714]/20">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
              <component :is="editingId ? Edit2 : Plus" class="w-3.5 h-3.5 text-[#FC6714]" />
              <span>{{ editingId ? 'Editar Vínculo no Catálogo' : 'Novo Vínculo no Catálogo do Proprietário' }}</span>
            </h4>
            <button
              type="button"
              @click="closeForm"
              class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 cursor-pointer"
            >
              <X class="w-4 h-4" />
            </button>
          </div>

          <div v-if="formError" class="p-2.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 text-xs flex items-center gap-2">
            <AlertCircle class="w-4 h-4 shrink-0 text-rose-600" />
            <span>{{ formError }}</span>
          </div>

          <div class="space-y-3 text-xs">
            <!-- Linha 1: Seleção do Material Canônico do Sistema (Apenas se criando) -->
            <div v-if="!editingId">
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Material Canônico do Sistema (Rede Pronta) *
              </label>
              <MaterialSearchSelect
                v-model="form.material_id"
                label=""
                placeholder="Busque o material por código SKU interno ou nome..."
                required
                @select="onSystemMaterialSelected"
              />
              <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 block">
                Selecione o material que já existe no catálogo do sistema para vincular ao código deste proprietário.
              </span>
            </div>
            <div v-else class="p-2.5 rounded-lg bg-white/70 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
              <div>
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Material Canônico no Sistema:</span>
                <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                  {{ editingMaterialDisplay }}
                </span>
              </div>
              <span class="font-mono text-xs font-bold text-[#FC6714]">
                SKU: {{ editingMaterialSku }}
              </span>
            </div>

            <!-- Linha 2: Código e Nome no Proprietário -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                  Código do Proprietário *
                </label>
                <input
                  v-model="form.owner_code"
                  type="text"
                  placeholder="Ex: 40012903, MAT-VIV-99"
                  class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-mono focus:ring-2 focus:ring-[#FC6714] outline-none uppercase"
                />
              </div>

              <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                  Nome / Descrição no Proprietário *
                </label>
                <input
                  v-model="form.owner_name"
                  type="text"
                  placeholder="Ex: CABO FIBRA OPTICA DROP 1 FO BOBINA 1000M"
                  class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] outline-none"
                />
              </div>
            </div>

            <!-- Linha 3: Observações Técnicas -->
            <div>
              <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                Observações / Especificação do Proprietário (Opcional)
              </label>
              <input
                v-model="form.notes"
                type="text"
                placeholder="Ex: Homologado para contratos FTTH regional Sudeste"
                class="w-full h-9 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-[#FC6714] outline-none"
              />
            </div>

            <!-- Ações do Formulário -->
            <div class="flex items-center justify-end gap-2 pt-2">
              <button
                type="button"
                @click="closeForm"
                class="px-3.5 py-1.5 rounded-lg border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer font-medium"
              >
                Cancelar
              </button>
              <button
                type="button"
                @click="saveItem"
                :disabled="isSaving"
                class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] disabled:opacity-60 text-white font-semibold shadow-xs transition cursor-pointer"
              >
                <Loader2 v-if="isSaving" class="w-3.5 h-3.5 animate-spin" />
                <Check v-else class="w-3.5 h-3.5" />
                <span>{{ editingId ? 'Atualizar Vínculo' : 'Salvar Vínculo' }}</span>
              </button>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Tabela do Catálogo De/Para -->
      <div class="rounded-xl border border-slate-200 dark:border-[#14147A] overflow-hidden bg-white dark:bg-[#03032E] shadow-2xs">
        <div v-if="isLoading" class="p-8 text-center text-slate-400">
          <Loader2 class="w-6 h-6 animate-spin mx-auto text-[#FC6714] mb-2" />
          <p class="text-xs">Carregando catálogo de materiais do proprietário...</p>
        </div>

        <div v-else-if="filteredItems.length === 0" class="p-8 text-center text-slate-400">
          <PackageSearch class="w-10 h-10 mx-auto text-slate-300 dark:text-slate-600 mb-2" />
          <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Nenhum material no catálogo deste proprietário</p>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
            Cadastre os códigos que a {{ owner?.name || 'operadora' }} utiliza para identificar seus materiais ou importe a planilha de relacionamento.
          </p>
          <div class="mt-4 flex items-center justify-center gap-2">
            <button
              type="button"
              @click="openCreateForm"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#FC6714] text-white text-xs font-semibold hover:bg-[#E0530A] transition cursor-pointer"
            >
              <Plus class="w-3.5 h-3.5" />
              <span>Vincular Primeiro Material</span>
            </button>
            <button
              type="button"
              @click="downloadTemplate"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#03032E] text-slate-700 dark:text-slate-300 text-xs font-medium hover:bg-slate-50 transition cursor-pointer"
            >
              <Download class="w-3.5 h-3.5 text-slate-400" />
              <span>Baixar Modelo CSV (3 Colunas)</span>
            </button>
          </div>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="border-b border-slate-200 dark:border-[#14147A] bg-slate-50/75 dark:bg-[#06064D]/50 font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[11px]">
                <th class="py-3 px-4">Código (Proprietário)</th>
                <th class="py-3 px-4">Nome no Proprietário</th>
                <th class="py-3 px-4">Material no Sistema (SKU / Canônico)</th>
                <th class="py-3 px-4">Unid.</th>
                <th class="py-3 px-4 text-right">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-[#14147A]/60">
              <tr
                v-for="item in filteredItems"
                :key="item.id"
                class="hover:bg-slate-50/60 dark:hover:bg-white/5 transition"
              >
                <!-- Código do Proprietário -->
                <td class="py-3 px-4 font-mono font-bold text-[#FC6714]">
                  {{ item.owner_code }}
                </td>

                <!-- Nome no Proprietário -->
                <td class="py-3 px-4">
                  <div class="font-semibold text-slate-900 dark:text-slate-100">
                    {{ item.owner_name }}
                  </div>
                  <div v-if="item.notes" class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 truncate max-w-xs">
                    {{ item.notes }}
                  </div>
                </td>

                <!-- Material Canônico no Sistema -->
                <td class="py-3 px-4">
                  <div class="flex items-center gap-1.5">
                    <span class="font-mono text-slate-500 dark:text-slate-400 text-[11px] bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded font-bold">
                      SKU: {{ item.material?.code || '-' }}
                    </span>
                    <span class="text-slate-700 dark:text-slate-300 truncate max-w-xs">
                      {{ item.material?.name || '-' }}
                    </span>
                  </div>
                </td>

                <!-- Unidade -->
                <td class="py-3 px-4 font-mono text-slate-600 dark:text-slate-400">
                  {{ item.material?.unit?.code || 'UN' }}
                </td>

                <!-- Ações -->
                <td class="py-3 px-4 text-right">
                  <div class="inline-flex items-center gap-1">
                    <button
                      type="button"
                      @click="openEditForm(item)"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-[#FC6714] hover:bg-orange-50 dark:hover:bg-[#FC6714]/15 transition cursor-pointer"
                      title="Editar vínculo"
                    >
                      <Edit2 class="w-3.5 h-3.5" />
                    </button>
                    <button
                      type="button"
                      @click="deleteItem(item)"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition cursor-pointer"
                      title="Excluir vínculo"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Rodapé Informativo -->
      <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 pt-1">
        <span>
          Exibindo <strong>{{ filteredItems.length }}</strong> de <strong>{{ items.length }}</strong> materiais vinculados.
        </span>
        <span class="italic">
          Em depósitos vinculados a este proprietário, estes códigos e nomes substituirão automaticamente a nomenclatura interna.
        </span>
      </div>
    </div>

    <!-- Rodapé Fixo de Ações no Modal Fullscreen -->
    <template #footer>
      <div v-if="isPreviewMode" class="w-full flex flex-wrap items-center justify-between gap-4">
        <button
          type="button"
          @click="cancelPreview"
          class="h-10 px-4 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition cursor-pointer"
        >
          Descartar / Cancelar Prévia
        </button>

        <div class="flex items-center gap-4">
          <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
            Pronto para gravar: <strong class="text-emerald-600 dark:text-emerald-400 font-bold">{{ validRowsCount }} linha(s) válida(s)</strong>
          </span>
          <button
            type="button"
            @click="confirmImport"
            :disabled="!canImportPreview || isImporting"
            class="h-10 inline-flex items-center gap-2 px-6 text-xs font-bold rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] disabled:opacity-50 disabled:cursor-not-allowed text-white shadow-xs transition cursor-pointer"
          >
            <Loader2 v-if="isImporting" class="w-4 h-4 animate-spin" />
            <Check v-else class="w-4 h-4 stroke-[2.5]" />
            <span>Confirmar e Importar ({{ validRowsCount }} linhas válidas)</span>
          </button>
        </div>
      </div>

      <div v-else class="w-full flex items-center justify-between gap-3 text-[11px] text-slate-500 dark:text-slate-400">
        <span class="italic">
          Em depósitos vinculados a este proprietário, estes códigos e nomes substituirão automaticamente a nomenclatura interna.
        </span>
        <button
          type="button"
          @click="onModalClose"
          class="h-9 px-4 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition cursor-pointer shrink-0"
        >
          Fechar
        </button>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import {
  Search, Plus, Edit2, Trash2, X, Check, Loader2, AlertCircle, CheckCircle2,
  Download, Upload, PackageSearch, FileSpreadsheet, Sparkles, BookOpen
} from 'lucide-vue-next';
import BaseModal from '@/components/common/BaseModal.vue';
import MaterialSearchSelect from '@/components/common/MaterialSearchSelect.vue';

interface OwnerData {
  id: number;
  code: string;
  name: string;
}

interface OwnerMaterialItem {
  id: number;
  account_id: number;
  material_owner_id: number;
  material_id: number;
  owner_code: string;
  owner_name: string;
  notes?: string;
  is_active: boolean;
  material?: {
    id: number;
    code: string;
    name: string;
    unit?: { code: string; name: string };
  };
}

interface PreviewRow {
  line: number;
  system_code: string;
  is_generated_code: boolean;
  system_name: string;
  owner_name: string;
  name_source: 'inherited' | 'custom' | 'new_material';
  name_note: string;
  owner_code: string;
  action: 'create_material_and_link' | 'link_existing' | 'update_link' | 'error';
  action_label: string;
  status: 'valid' | 'error';
  message: string | null;
}

const props = defineProps<{
  isOpen: boolean;
  owner: OwnerData | null;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'updated'): void;
}>();

const items = ref<OwnerMaterialItem[]>([]);
const isLoading = ref(false);
const isSaving = ref(false);
const searchTerm = ref('');
const isFormOpen = ref(false);
const editingId = ref<number | null>(null);
const editingMaterialDisplay = ref('');
const editingMaterialSku = ref('');
const formError = ref('');
const alertMessage = ref('');
const alertSuccess = ref(true);

// Estado de Preview da Importação
const isPreviewMode = ref(false);
const isPreviewLoading = ref(false);
const isImporting = ref(false);
const previewFileName = ref('');
const previewRows = ref<PreviewRow[]>([]);
const previewSummary = ref({
  total_rows: 0,
  to_create_material: 0,
  to_link_existing: 0,
  to_update_link: 0,
  errors_count: 0,
});
const canImportPreview = ref(false);

const form = ref({
  material_id: '' as string | number,
  owner_code: '',
  owner_name: '',
  notes: '',
  is_active: true,
});

const filteredItems = computed(() => {
  if (!searchTerm.value.trim()) return items.value;
  const s = searchTerm.value.toLowerCase().trim();
  return items.value.filter((item) => {
    return (
      item.owner_code?.toLowerCase().includes(s) ||
      item.owner_name?.toLowerCase().includes(s) ||
      item.material?.code?.toLowerCase().includes(s) ||
      item.material?.name?.toLowerCase().includes(s)
    );
  });
});

const validRowsCount = computed(() => {
  return previewRows.value.filter(r => r.status === 'valid').length;
});

watch(
  () => [props.isOpen, props.owner],
  ([open, currentOwner]) => {
    if (open && currentOwner) {
      loadItems();
      closeForm();
      cancelPreview();
      alertMessage.value = '';
      searchTerm.value = '';
    }
  },
  { immediate: true }
);

function onModalClose() {
  cancelPreview();
  emit('close');
}

async function loadItems() {
  if (!props.owner?.id) return;
  isLoading.value = true;
  try {
    const res = await axios.get(`/api/v1/material-owners/${props.owner.id}/materials`);
    items.value = res.data.data || [];
  } catch (err: any) {
    console.error('Erro ao carregar catálogo do proprietário:', err);
  } finally {
    isLoading.value = false;
  }
}

function openCreateForm() {
  editingId.value = null;
  editingMaterialDisplay.value = '';
  editingMaterialSku.value = '';
  formError.value = '';
  form.value = {
    material_id: '',
    owner_code: '',
    owner_name: '',
    notes: '',
    is_active: true,
  };
  isFormOpen.value = true;
}

function openEditForm(item: OwnerMaterialItem) {
  editingId.value = item.id;
  editingMaterialDisplay.value = item.material?.name || '';
  editingMaterialSku.value = item.material?.code || '';
  formError.value = '';
  form.value = {
    material_id: item.material_id,
    owner_code: item.owner_code,
    owner_name: item.owner_name,
    notes: item.notes || '',
    is_active: item.is_active,
  };
  isFormOpen.value = true;
}

function closeForm() {
  isFormOpen.value = false;
  editingId.value = null;
  formError.value = '';
}

function onSystemMaterialSelected(material: any) {
  if (material) {
    if (!form.value.owner_name.trim()) {
      form.value.owner_name = material.name || '';
    }
  }
}

async function saveItem() {
  if (!props.owner?.id) return;
  formError.value = '';

  if (!editingId.value && !form.value.material_id) {
    formError.value = 'Selecione o material canônico do sistema.';
    return;
  }
  if (!form.value.owner_code.trim()) {
    formError.value = 'Informe o código do material no proprietário.';
    return;
  }
  if (!form.value.owner_name.trim()) {
    formError.value = 'Informe o nome do material no proprietário.';
    return;
  }

  isSaving.value = true;
  try {
    if (editingId.value) {
      await axios.put(`/api/v1/material-owners/${props.owner.id}/materials/${editingId.value}`, {
        owner_code: form.value.owner_code.trim(),
        owner_name: form.value.owner_name.trim(),
        notes: form.value.notes.trim() || null,
        is_active: form.value.is_active,
      });
      alertMessage.value = 'Vínculo do catálogo atualizado com sucesso!';
    } else {
      await axios.post(`/api/v1/material-owners/${props.owner.id}/materials`, {
        material_id: Number(form.value.material_id),
        owner_code: form.value.owner_code.trim(),
        owner_name: form.value.owner_name.trim(),
        notes: form.value.notes.trim() || null,
        is_active: form.value.is_active,
      });
      alertMessage.value = 'Material vinculado com sucesso ao catálogo do proprietário!';
    }

    alertSuccess.value = true;
    closeForm();
    await loadItems();
    emit('updated');
  } catch (err: any) {
    formError.value = err.response?.data?.message || 'Erro ao salvar material no catálogo.';
  } finally {
    isSaving.value = false;
  }
}

async function deleteItem(item: OwnerMaterialItem) {
  if (!props.owner?.id) return;
  if (!confirm(`Remover o código "${item.owner_code}" do catálogo deste proprietário?`)) return;

  try {
    await axios.delete(`/api/v1/material-owners/${props.owner.id}/materials/${item.id}`);
    alertMessage.value = 'Item removido do catálogo com sucesso.';
    alertSuccess.value = true;
    await loadItems();
    emit('updated');
  } catch (err: any) {
    alertMessage.value = err.response?.data?.message || 'Erro ao remover item do catálogo.';
    alertSuccess.value = false;
  }
}

function downloadTemplate(format: 'xlsx' | 'csv' = 'xlsx') {
  if (!props.owner?.id) return;
  window.open(`/api/v1/material-owners/${props.owner.id}/materials/template?format=${format}`, '_blank');
}

/**
 * Envia o arquivo para validação e pré-visualização (Preview)
 */
async function handleFileUploadForPreview(event: Event) {
  const target = event.target as HTMLInputElement;
  if (!target.files || target.files.length === 0 || !props.owner?.id) return;

  const file = target.files[0];
  previewFileName.value = file.name;

  const formData = new FormData();
  formData.append('file', file);

  isPreviewLoading.value = true;
  alertMessage.value = '';
  try {
    const res = await axios.post(`/api/v1/material-owners/${props.owner.id}/materials/preview`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    previewRows.value = res.data.preview_rows || [];
    previewSummary.value = res.data.summary || {
      total_rows: 0,
      to_create_material: 0,
      to_link_existing: 0,
      to_update_link: 0,
      errors_count: 0,
    };
    canImportPreview.value = Boolean(res.data.can_import);
    isPreviewMode.value = true;
  } catch (err: any) {
    alertMessage.value = err.response?.data?.message || 'Falha ao analisar arquivo para prévia.';
    alertSuccess.value = false;
  } finally {
    isPreviewLoading.value = false;
    target.value = '';
  }
}

function cancelPreview() {
  isPreviewMode.value = false;
  previewRows.value = [];
  previewFileName.value = '';
}

/**
 * Grava definitivamente as linhas validadas da prévia
 */
async function confirmImport() {
  if (!props.owner?.id || !canImportPreview.value) return;

  isImporting.value = true;
  alertMessage.value = '';
  try {
    const res = await axios.post(`/api/v1/material-owners/${props.owner.id}/materials/import`, {
      rows: previewRows.value,
    });

    alertMessage.value = res.data.message || 'Importação realizada com sucesso!';
    alertSuccess.value = true;
    cancelPreview();
    await loadItems();
    emit('updated');
  } catch (err: any) {
    alertMessage.value = err.response?.data?.message || 'Erro ao efetivar a importação dos materiais.';
    alertSuccess.value = false;
  } finally {
    isImporting.value = false;
  }
}
</script>
