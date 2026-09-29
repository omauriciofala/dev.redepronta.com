<template>
  <div class="py-8 w-full space-y-12">
    <!-- Linha 1 Canônica Obrigatória: BaseBreadcrumb -->
    <BaseBreadcrumb :items="[
      { label: 'Início', href: '#people' },
      { label: 'Desenvolvedor', href: '#developer' },
      { label: 'Design System & BrandBook' }
    ]" />

    <!-- Header Principal do Design System -->
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-800/80">
      <div>
        <div class="flex items-center gap-3">
          <div class="p-2 rounded-xl bg-blue-600/10 dark:bg-blue-500/10 text-[#FC6714] dark:text-orange-400">
            <Palette class="w-6 h-6" />
          </div>
          <div>
            <div class="flex items-center gap-2.5">
              <h1 class="text-2xl font-heading font-semibold tracking-tight text-[#06064D] dark:text-white">Design System & BrandBook</h1>
              <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-100 dark:bg-blue-950/70 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                v1.1 Modais & Fullscreen
              </span>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
              Tokens visuais, tipografia ergonômica, superfícies adaptáveis e biblioteca de modais do ERP Rede Pronta.
            </p>
          </div>
        </div>
      </div>

      <!-- Ações do Header -->
      <div class="flex items-center gap-3">
        <button
          type="button"
          @click="toggleTheme"
          class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition shadow-2xs cursor-pointer"
        >
          <Sun v-if="theme === 'dark'" class="w-4 h-4 text-amber-400" />
          <Moon v-else class="w-4 h-4 text-slate-600" />
          <span>Alternar: {{ theme === 'dark' ? 'Tema Escuro Ativo' : 'Tema Claro Ativo' }}</span>
        </button>
      </div>
    </header>

    <!-- Navegação Interna por Âncoras -->
    <nav class="flex flex-wrap items-center gap-2 p-1.5 rounded-xl bg-slate-100 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800/80 text-sm">
      <a
        v-for="section in sections"
        :key="section.id"
        :href="'#' + section.id"
        class="px-3.5 py-1.5 rounded-lg font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-white dark:hover:bg-slate-800 transition shadow-2xs"
      >
        {{ section.label }}
      </a>
    </nav>

    <!-- SEÇÃO 1: TIPOGRAFIA ERGONÔMICA -->
    <section id="tipografia" class="space-y-4">
      <div class="flex items-center gap-2.5">
        <Type class="w-5 h-5 text-[#FC6714] dark:text-orange-400" />
        <h2 class="text-lg font-heading font-semibold text-[#06064D] dark:text-white">1. Escala Tipográfica Ergonômica</h2>
      </div>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        Desenvolvida para operadores de rotina contínua. Fontes microscópicas foram banidas; a leitura padrão do ERP opera entre 14px e 16px.
      </p>

      <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 divide-y divide-slate-100 dark:divide-slate-800/80 shadow-xs overflow-hidden">
        <div class="p-5 flex flex-col md:flex-row md:items-baseline justify-between gap-4">
          <div class="w-64 shrink-0">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Título de Página (H1)</span>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">text-2xl font-bold (24px)</p>
          </div>
          <div class="flex-1">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100">Cadastro Geral de Pessoas</h1>
          </div>
        </div>

        <div class="p-5 flex flex-col md:flex-row md:items-baseline justify-between gap-4">
          <div class="w-64 shrink-0">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Título de Seção / Modal (H2)</span>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">text-lg font-semibold (18px)</p>
          </div>
          <div class="flex-1">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Dados Principais & Identificação Fiscal</h2>
          </div>
        </div>

        <div class="p-5 flex flex-col md:flex-row md:items-baseline justify-between gap-4">
          <div class="w-64 shrink-0">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Subtítulos / Cards (H3)</span>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">text-base font-semibold (16px)</p>
          </div>
          <div class="flex-1">
            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Endereço de Faturamento & Operação</h3>
          </div>
        </div>

        <div class="p-5 flex flex-col md:flex-row md:items-baseline justify-between gap-4">
          <div class="w-64 shrink-0">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Texto Padrão (Inputs & Tabelas)</span>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">text-sm font-normal / medium (14px)</p>
          </div>
          <div class="flex-1 space-y-1">
            <p class="text-sm font-medium text-slate-800 dark:text-slate-200">Rede Pronta Telecomunicações Ltda. — CNPJ: 12.345.678/0001-90</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Av. Paulista, 1000, Bela Vista — São Paulo / SP</p>
          </div>
        </div>

        <div class="p-5 flex flex-col md:flex-row md:items-baseline justify-between gap-4">
          <div class="w-64 shrink-0">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Metadados & Badges (Mínimo)</span>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">text-xs font-semibold (12px)</p>
          </div>
          <div class="flex-1">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Última sincronização: Há 2 minutos via API</span>
          </div>
        </div>
      </div>
    </section>

    <!-- SEÇÃO 2: PALETA DE CORES & TOKENS SEMÂNTICOS -->
    <section id="cores" class="space-y-4">
      <div class="flex items-center gap-2.5">
        <Palette class="w-5 h-5 text-[#FC6714] dark:text-orange-400" />
        <h2 class="text-lg font-heading font-semibold text-[#06064D] dark:text-white">2. Paleta de Cores & Tokens Semânticos</h2>
      </div>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        Cores calibradas para garantir conformidade de contraste WCAG AA tanto no modo escuro quanto no modo claro.
      </p>

      <!-- Superfícies -->
      <div class="space-y-2">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Superfícies & Contraste de Fundo</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
          <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-950 text-white">
            <p class="text-xs font-mono font-semibold">dark:bg-slate-950</p>
            <p class="text-xs opacity-70 mt-1">#020617 (Canvas Escuro)</p>
          </div>
          <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-900 text-white">
            <p class="text-xs font-mono font-semibold">dark:bg-slate-900</p>
            <p class="text-xs opacity-70 mt-1">#0f172a (Cards & Sidebar)</p>
          </div>
          <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 text-slate-900">
            <p class="text-xs font-mono font-semibold">bg-slate-50</p>
            <p class="text-xs text-slate-500 mt-1">#f8fafc (Canvas Claro)</p>
          </div>
          <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white text-slate-900">
            <p class="text-xs font-mono font-semibold">bg-white</p>
            <p class="text-xs text-slate-500 mt-1">#ffffff (Cards Claros)</p>
          </div>
        </div>
      </div>

      <!-- Cores de Estado / Semântica -->
      <div class="space-y-2 pt-2">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Estados do ERP & Personas</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          <div class="p-4 rounded-xl border border-blue-200 dark:border-blue-900/60 bg-blue-50 dark:bg-blue-950/40 text-blue-900 dark:text-blue-300">
            <div class="flex items-center justify-between">
              <span class="text-sm font-semibold">Primária / Ação</span>
              <span class="w-3 h-3 rounded-full bg-blue-600"></span>
            </div>
            <p class="text-xs font-mono mt-2">blue-600 (#2563eb)</p>
            <p class="text-xs text-blue-700/80 dark:text-blue-400/80 mt-1">Botões de ação, links ativos, foco</p>
          </div>

          <div class="p-4 rounded-xl border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-300">
            <div class="flex items-center justify-between">
              <span class="text-sm font-semibold">Sucesso / Ativo / PF</span>
              <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
            </div>
            <p class="text-xs font-mono mt-2">emerald-600 (#059669)</p>
            <p class="text-xs text-emerald-700/80 dark:text-emerald-400/80 mt-1">Status Ativo, Pessoa Física</p>
          </div>

          <div class="p-4 rounded-xl border border-cyan-200 dark:border-cyan-900/60 bg-cyan-50 dark:bg-cyan-950/40 text-cyan-900 dark:text-cyan-300">
            <div class="flex items-center justify-between">
              <span class="text-sm font-semibold">Corporativo / PJ</span>
              <span class="w-3 h-3 rounded-full bg-cyan-600"></span>
            </div>
            <p class="text-xs font-mono mt-2">cyan-600 (#0891b2)</p>
            <p class="text-xs text-cyan-700/80 dark:text-cyan-400/80 mt-1">Pessoa Jurídica, Fornecedor</p>
          </div>

          <div class="p-4 rounded-xl border border-red-200 dark:border-red-900/60 bg-red-50 dark:bg-red-950/40 text-red-900 dark:text-red-300">
            <div class="flex items-center justify-between">
              <span class="text-sm font-semibold">Crítico / Inativo</span>
              <span class="w-3 h-3 rounded-full bg-red-600"></span>
            </div>
            <p class="text-xs font-mono mt-2">red-600 (#dc2626)</p>
            <p class="text-xs text-red-700/80 dark:text-red-400/80 mt-1">Inativo, Bloqueio, Exclusão</p>
          </div>
        </div>
      </div>

      <!-- Escala Canônica de Cantos & Raios Reduzidos (Enterprise Radius) -->
      <div class="space-y-2 pt-2">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Escala de Cantos & Raios de Borda (Layout Sóbrio)</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400">
          Cantos menos arredondados calibrados globalmente via Tailwind v4 para uma identidade corporativa limpa, técnica e moderna.
        </p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
          <div class="p-3.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs">
            <span class="text-xs font-bold text-slate-900 dark:text-slate-100">rounded-lg (5px)</span>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Botões, inputs, selects e dropdowns</p>
            <div class="mt-2.5 h-6 w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg flex items-center justify-center text-[10px] font-mono text-slate-500">5px</div>
          </div>

          <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs">
            <span class="text-xs font-bold text-slate-900 dark:text-slate-100">rounded-xl (6px)</span>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Cards, containers e box breadcrumb</p>
            <div class="mt-2.5 h-6 w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center justify-center text-[10px] font-mono text-slate-500">6px</div>
          </div>

          <div class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs">
            <span class="text-xs font-bold text-slate-900 dark:text-slate-100">rounded-2xl (8px)</span>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Modais centrais e drawer lateral</p>
            <div class="mt-2.5 h-6 w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl flex items-center justify-center text-[10px] font-mono text-slate-500">8px</div>
          </div>

          <div class="p-3.5 rounded-md border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs">
            <span class="text-xs font-bold text-slate-900 dark:text-slate-100">rounded-md (4px)</span>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Badges, tags, pílulas e contadores</p>
            <div class="mt-2.5 h-6 w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-md flex items-center justify-center text-[10px] font-mono text-slate-500">4px</div>
          </div>
        </div>
      </div>
    </section>

    <!-- SEÇÃO 3: BOTÕES & CONTROLES -->
    <section id="botoes" class="space-y-4">
      <div class="flex items-center gap-2.5">
        <MousePointerClick class="w-5 h-5 text-[#FC6714] dark:text-orange-400" />
        <h2 class="text-lg font-heading font-semibold text-[#06064D] dark:text-white">3. Botões & Ações Interativas</h2>
      </div>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        Altura ergonômica mínima de 40px (`h-10`) para cliques seguros e operação rápida em telas sensíveis ou de alta resolução.
      </p>

      <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 space-y-6 shadow-xs">
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Variantes de Estilo</span>
          <div class="flex flex-wrap items-center gap-3 mt-3">
            <button class="h-10 px-4 text-sm font-medium rounded-lg bg-[#FC6714] text-white hover:bg-[#E0530A] active:bg-[#C94605] transition shadow-xs flex items-center gap-2 cursor-pointer">
              <Plus class="w-4 h-4" />
              <span>Botão Primário</span>
            </button>

            <button class="h-10 px-4 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition shadow-2xs flex items-center gap-2 cursor-pointer">
              <RefreshCw class="w-4 h-4" />
              <span>Secundário / Outline</span>
            </button>

            <button class="h-10 px-4 text-sm font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-500 transition shadow-xs flex items-center gap-2 cursor-pointer">
              <Check class="w-4 h-4" />
              <span>Ação Sucesso</span>
            </button>

            <button class="h-10 px-4 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-500 transition shadow-xs flex items-center gap-2 cursor-pointer">
              <Trash2 class="w-4 h-4" />
              <span>Perigo / Excluir</span>
            </button>

            <button class="h-10 px-4 text-sm font-medium rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center gap-2 cursor-pointer">
              <ExternalLink class="w-4 h-4" />
              <span>Ghost / Neutro</span>
            </button>
          </div>
        </div>

        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Estados (Desabilitado & Carregando)</span>
          <div class="flex flex-wrap items-center gap-3 mt-3">
            <button disabled class="h-10 px-4 text-sm font-medium rounded-lg bg-blue-600/70 text-white flex items-center gap-2 cursor-not-allowed opacity-80">
              <Loader2 class="w-4 h-4 animate-spin" />
              <span>Processando...</span>
            </button>

            <button disabled class="h-10 px-4 text-sm font-medium rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800/40 text-slate-400 dark:text-slate-600 cursor-not-allowed">
              Ação Desabilitada
            </button>

            <button class="w-10 h-10 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer" title="Configurações">
              <Settings class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- SEÇÃO 4: FORMULÁRIOS & INPUTS ERGONÔMICOS -->
    <section id="formularios" class="space-y-4">
      <div class="flex items-center gap-2.5">
        <Sliders class="w-5 h-5 text-[#FC6714] dark:text-orange-400" />
        <h2 class="text-lg font-heading font-semibold text-[#06064D] dark:text-white">4. Formulários & Inputs Ergonômicos</h2>
      </div>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        Labels destacadas, inputs de altura confortável `h-10`, texto em `text-sm` e contraste nítido em ambos os temas.
      </p>

      <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 space-y-8 shadow-xs">
        <!-- Inputs Básicos -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
              Nome Completo / Razão Social
            </label>
            <input
              type="text"
              placeholder="Ex: Maria Silva Sauro"
              value="Maria Silva Sauro"
              class="w-full h-10 px-3.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] focus:border-transparent outline-hidden transition"
            />
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Nome cadastral canônico</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
              Busca com Ícone à Esquerda
            </label>
            <div class="relative">
              <Search class="w-4 h-4 absolute left-3.5 top-3 text-slate-400" />
              <input
                type="text"
                placeholder="Pesquisar por CPF, CNPJ ou Nome..."
                class="w-full h-10 pl-10 pr-3.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714] focus:border-transparent outline-hidden transition"
              />
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Filtro em tempo real de tabelas</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
              Select Padrão com Seta
            </label>
            <BaseSelect
              v-model="demoSelect"
              hint="Seta com recuo ergonômico da borda direita (16px / right-4)"
            >
              <option value="3106200">Belo Horizonte / MG (IBGE: 3106200)</option>
              <option value="3550308">São Paulo / SP (IBGE: 3550308)</option>
              <option value="3304557">Rio de Janeiro / RJ (IBGE: 3304557)</option>
            </BaseSelect>
          </div>
        </div>

        <!-- COMPONENTES ESPECIALIZADOS DO DESIGN SYSTEM -->
        <div class="pt-6 border-t border-slate-100 dark:border-slate-800/80 space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                Componentes Canônicos Especializados
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Padrões universais do ERP para seleção de municípios, entrada estrita de datas e selects ergonômicos.
              </p>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 text-[#FC6714] dark:text-orange-400 border border-blue-200 dark:border-blue-900">
              Padrão ERP v2
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-5 rounded-2xl bg-slate-50/70 dark:bg-slate-950/50 border border-slate-200 dark:border-slate-800">
            <!-- 1. CitySearchSelect -->
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-[#FC6714] dark:text-orange-400 font-mono">
                  &lt;CitySearchSelect /&gt;
                </span>
                <span class="text-[11px] px-2 py-0.5 rounded-sm bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold">
                  5.570 Cidades IBGE
                </span>
              </div>
              <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                Busca ativada ao digitar <strong>3 caracteres</strong> com <strong>ícone de Lupa (🔍) na extremidade DIREITA</strong> do campo (substituindo a seta convencional).
              </p>
              <CitySearchSelect
                v-model="demoCityId"
                label="Busca de Cidades do Brasil"
                placeholder="Digite ao menos 3 letras (ex: Bel, Cur, São)..."
              />
              <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">
                ID Selecionado: <strong class="text-[#FC6714] dark:text-orange-400">{{ demoCityId || 'Nenhum' }}</strong>
              </p>
            </div>

            <!-- 2. DateInput -->
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 font-mono">
                  &lt;DateInput /&gt;
                </span>
                <span class="text-[11px] px-2 py-0.5 rounded-sm bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-semibold border border-emerald-200 dark:border-emerald-800">
                  DD/MM/AAAA
                </span>
              </div>
              <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                Regra canônica do ERP: <strong>datas são sempre Dia, Mês e Ano</strong> com máscara inteligente na digitação e badge pt-BR.
              </p>
              <DateInput
                v-model="demoDate"
                label="Data Canônica (Nascimento / Fundação)"
              />
              <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">
                Valor Formatado: <strong class="text-emerald-600 dark:text-emerald-400">{{ demoDate || 'Vazio' }}</strong>
              </p>
            </div>

            <!-- 3. BaseSelect -->
            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 font-mono">
                  &lt;BaseSelect /&gt;
                </span>
                <span class="text-[11px] px-2 py-0.5 rounded-sm bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 font-semibold border border-indigo-200 dark:border-indigo-800">
                  Seta Recuada 16px
                </span>
              </div>
              <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                Select com <strong>seta estética recuada a 16px da borda</strong> (right-4), acabando com a seta colada na linha da borda e evitando encavalamento de textos.
              </p>
              <BaseSelect
                v-model="demoSelect"
                label="Select Padrão com Seta Ergonômica"
              >
                <option value="3106200">Belo Horizonte / MG (IBGE: 3106200)</option>
                <option value="3550308">São Paulo / SP (IBGE: 3550308)</option>
                <option value="3304557">Rio de Janeiro / RJ (IBGE: 3304557)</option>
              </BaseSelect>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">
                Código IBGE Selecionado: <strong class="text-indigo-600 dark:text-indigo-400">{{ demoSelect || 'Nenhum' }}</strong>
              </p>
            </div>
          </div>
        </div>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800/80 flex flex-wrap items-center gap-8">
          <div class="flex items-center gap-3">
            <button
              type="button"
              @click="demoSwitch = !demoSwitch"
              :class="demoSwitch ? 'bg-blue-600' : 'bg-slate-300 dark:bg-slate-700'"
              class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-hidden"
            >
              <span
                :class="demoSwitch ? 'translate-x-5' : 'translate-x-1'"
                class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out mt-1"
              />
            </button>
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
              Controle Toggle: {{ demoSwitch ? 'Ativo' : 'Inativo' }}
            </span>
          </div>

          <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" checked class="w-4 h-4 rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-2 focus:ring-[#FC6714]/40 focus:border-[#FC6714]" />
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Aceite de notificações automáticas</span>
          </label>
        </div>
      </div>
    </section>

    <!-- SEÇÃO 5: BADGES & PERSONAS DO ERP -->
    <section id="badges" class="space-y-4">
      <div class="flex items-center gap-2.5">
        <Tag class="w-5 h-5 text-[#FC6714] dark:text-orange-400" />
        <h2 class="text-lg font-heading font-semibold text-[#06064D] dark:text-white">5. Badges & Personas do ERP</h2>
      </div>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        Tamanho ergonômico `text-xs font-semibold` com espaçamento interno generoso (`px-2.5 py-1`). Evita textos colados e ilegíveis.
      </p>

      <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 space-y-5 shadow-xs">
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Personas Relacionais</span>
          <div class="flex flex-wrap items-center gap-2.5 mt-3">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-950/70 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
              <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
              Cliente
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-purple-50 text-purple-700 dark:bg-purple-950/70 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
              <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
              Fornecedor
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/70 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
              <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
              Colaborador
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/70 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
              <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
              Parceiro
            </span>
          </div>
        </div>

        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Natureza Jurídica & Status</span>
          <div class="flex flex-wrap items-center gap-2.5 mt-3">
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
              PF (Física)
            </span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-cyan-50 text-cyan-700 dark:bg-cyan-950/70 dark:text-cyan-400 border border-cyan-200 dark:border-cyan-800">
              PJ (Jurídica)
            </span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
              ● Ativo
            </span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-red-50 text-red-700 dark:bg-red-950/70 dark:text-red-400 border border-red-200 dark:border-red-800">
              ○ Inativo
            </span>
          </div>
        </div>
      </div>
    </section>

    <!-- SEÇÃO 6: ALERTAS & FEEDBACK VISUAL -->
    <section id="alertas" class="space-y-4">
      <div class="flex items-center gap-2.5">
        <Bell class="w-5 h-5 text-[#FC6714] dark:text-orange-400" />
        <h2 class="text-lg font-heading font-semibold text-[#06064D] dark:text-white">6. Banners de Alerta & Notificação</h2>
      </div>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        Comunicação clara de erros, avisos e confirmações operacionais sem sobrecarregar o usuário.
      </p>

      <div class="space-y-3">
        <div class="p-4 rounded-xl border border-emerald-200 dark:border-emerald-900/80 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-300 flex items-start gap-3">
          <CheckCircle2 class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
          <div class="text-sm">
            <p class="font-semibold">Cadastro concluído com sucesso</p>
            <p class="text-emerald-700 dark:text-emerald-400 mt-0.5">A pessoa foi salva com integridade relacional de cidade e conta.</p>
          </div>
        </div>

        <div class="p-4 rounded-xl border border-blue-200 dark:border-blue-900/80 bg-blue-50 dark:bg-blue-950/40 text-blue-900 dark:text-blue-300 flex items-start gap-3">
          <Info class="w-5 h-5 text-[#FC6714] dark:text-orange-400 shrink-0 mt-0.5" />
          <div class="text-sm">
            <p class="font-semibold">Sincronização em segundo plano</p>
            <p class="text-blue-700 dark:text-blue-400 mt-0.5">A busca relacional é realizada sob demanda utilizando códigos de cidade IBGE.</p>
          </div>
        </div>

        <div class="p-4 rounded-xl border border-amber-200 dark:border-amber-900/80 bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-300 flex items-start gap-3">
          <AlertTriangle class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" />
          <div class="text-sm">
            <p class="font-semibold">Restrição de integridade ativa</p>
            <p class="text-amber-700 dark:text-amber-400 mt-0.5">Esta entidade possui vínculos ativos e não pode ser excluída fisicamente (ON DELETE RESTRICT).</p>
          </div>
        </div>
      </div>
    </section>

    <!-- SEÇÃO 7: TABELAS DE DADOS AREJADAS -->
    <section id="tabelas" class="space-y-4">
      <div class="flex items-center gap-2.5">
        <Table class="w-5 h-5 text-[#FC6714] dark:text-orange-400" />
        <h2 class="text-lg font-heading font-semibold text-[#06064D] dark:text-white">7. Padrão de Tabelas Confortáveis</h2>
      </div>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        Linhas com padding generoso (`py-4 px-5`), fontes `text-sm` e contraste nítido entre linhas.
      </p>

      <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
        <table class="w-full text-left text-sm border-collapse">
          <thead>
            <tr class="border-b border-slate-200 dark:border-slate-800/80 bg-slate-50/80 dark:bg-slate-900/80">
              <th class="py-3 px-5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Identificação</th>
              <th class="py-3 px-5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Documento</th>
              <th class="py-3 px-5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Localidade</th>
              <th class="py-3 px-5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Personas</th>
              <th class="py-3 px-5 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition">
              <td class="py-4 px-5">
                <p class="font-semibold text-slate-900 dark:text-slate-100">Telecom Prime Soluções Ltda</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">contato@telecomprime.com.br</p>
              </td>
              <td class="py-4 px-5 font-mono text-sm text-slate-700 dark:text-slate-300">
                12.345.678/0001-90
              </td>
              <td class="py-4 px-5 text-sm text-slate-600 dark:text-slate-300">
                Belo Horizonte / MG
              </td>
              <td class="py-4 px-5">
                <div class="flex items-center gap-1.5">
                  <span class="px-2 py-0.5 rounded-sm text-xs font-semibold bg-cyan-50 text-cyan-700 dark:bg-cyan-950/70 dark:text-cyan-400">PJ</span>
                  <span class="px-2 py-0.5 rounded-sm text-xs font-semibold bg-purple-50 text-purple-700 dark:bg-purple-950/70 dark:text-purple-400">Fornecedor</span>
                </div>
              </td>
              <td class="py-4 px-5 text-right">
                <button class="h-8 px-3 text-xs font-medium rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition cursor-pointer">
                  Editar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- SEÇÃO 8: MODAIS, DIÁLOGOS & FORMULÁRIOS EM TELA CHEIA (NOVO) -->
    <section id="modais" class="space-y-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2.5">
          <Maximize2 class="w-5 h-5 text-[#FC6714] dark:text-orange-400" />
          <h2 class="text-lg font-heading font-semibold text-[#06064D] dark:text-white">8. Modais, Diálogos & Formulários em Tela Cheia</h2>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-100 dark:bg-blue-950/70 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
          Biblioteca Canônica
        </span>
      </div>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        Família completa de diálogos do ERP Rede Pronta. Destaque especial para o <strong>Modal de Tela Cheia</strong>, projetado para acomodar formulários densos, workflows multi-etapas e configurações complexas sem navegar para outra página.
      </p>

      <!-- Cartões de Demonstração Interativa dos 5 Tamanhos -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- 1. Modal Pequeno (Confirmação) -->
        <div class="p-5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs flex flex-col justify-between space-y-4">
          <div>
            <div class="flex items-center justify-between">
              <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">1. Confirmação (sm)</h3>
              <span class="text-xs font-mono font-semibold text-slate-400">max-w-md</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">
              Diálogos curtos, confirmação de exclusão ou ações irreversíveis.
            </p>
          </div>
          <button
            type="button"
            @click="openModalSm = true"
            class="w-full h-10 px-4 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/80 transition cursor-pointer flex items-center justify-center gap-2"
          >
            <AlertCircle class="w-4 h-4 text-red-500" />
            <span>Testar Modal de Confirmação</span>
          </button>
        </div>

        <!-- 2. Modal Padrão (Cadastro Simples) -->
        <div class="p-5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs flex flex-col justify-between space-y-4">
          <div>
            <div class="flex items-center justify-between">
              <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">2. Padrão (md / lg)</h3>
              <span class="text-xs font-mono font-semibold text-slate-400">max-w-3xl</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">
              O modal clássico de cadastros de 2 colunas (ex: cadastro de pessoas do Sprint 1).
            </p>
          </div>
          <button
            type="button"
            @click="openModalLg = true"
            class="w-full h-10 px-4 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/80 transition cursor-pointer flex items-center justify-center gap-2"
          >
            <Layers class="w-4 h-4 text-blue-500" />
            <span>Testar Modal de Cadastro (lg)</span>
          </button>
        </div>

        <!-- 3. Modal Amplo (Tabelas / Seleção) -->
        <div class="p-5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs flex flex-col justify-between space-y-4">
          <div>
            <div class="flex items-center justify-between">
              <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">3. Amplo (xl)</h3>
              <span class="text-xs font-mono font-semibold text-slate-400">max-w-5xl</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">
              Visualização de relatórios rápidos, conciliações ou seleção avançada de itens.
            </p>
          </div>
          <button
            type="button"
            @click="openModalXl = true"
            class="w-full h-10 px-4 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/80 transition cursor-pointer flex items-center justify-center gap-2"
          >
            <Table class="w-4 h-4 text-emerald-500" />
            <span>Testar Modal Amplo (xl)</span>
          </button>
        </div>

        <!-- 4. ⭐ MODAL DE TELA CHEIA (FULLSCREEN OVERLAY) -->
        <div class="p-5 rounded-xl border-2 border-blue-500/80 dark:border-blue-500/80 bg-blue-50/40 dark:bg-blue-950/30 shadow-md flex flex-col justify-between space-y-4 md:col-span-2">
          <div>
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="p-1 rounded-md bg-blue-600 text-white">
                  <Maximize2 class="w-4 h-4" />
                </span>
                <h3 class="text-base font-bold text-blue-900 dark:text-blue-100">
                  4. Modal de Tela Cheia (Fullscreen Overlay)
                </h3>
              </div>
              <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-blue-600 text-white">
                Destaque de Arquitetura
              </span>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-300 mt-2">
              <strong>Largura horizontal maximizada</strong> com espaçamento externo simétrico e uniforme em todas as 4 bordas (<code>inset-3 sm:inset-5 md:inset-6 lg:inset-8</code>). Centraliza de modo responsivo em qualquer monitor e substitui páginas inteiras para formulários de alta densidade.
            </p>
          </div>
          <button
            type="button"
            @click="openModalFullscreen = true"
            class="h-11 px-5 text-sm font-semibold rounded-lg bg-[#FC6714] text-white hover:bg-[#E0530A] active:bg-[#C94605] transition shadow-sm cursor-pointer flex items-center justify-center gap-2"
          >
            <Maximize2 class="w-4 h-4" />
            <span>Abrir Modal de Tela Cheia (Formulário Complexo)</span>
          </button>
        </div>

        <!-- 5. Drawer Lateral (Slide-Over) -->
        <div class="p-5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs flex flex-col justify-between space-y-4">
          <div>
            <div class="flex items-center justify-between">
              <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">5. Drawer Lateral (Slide-over)</h3>
              <span class="text-xs font-mono font-semibold text-slate-400">max-w-xl lateral</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">
              Painel deslizante à direita para inspeção ou edição rápida sem perder a listagem de fundo.
            </p>
          </div>
          <button
            type="button"
            @click="openModalDrawer = true"
            class="w-full h-10 px-4 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/80 transition cursor-pointer flex items-center justify-center gap-2"
          >
            <SidebarIcon class="w-4 h-4 text-purple-500" />
            <span>Testar Drawer Lateral</span>
          </button>
        </div>
      </div>
    </section>

    <!-- SEÇÃO 9: ESTRUTURA DE PÁGINAS & MARGENS DE TELA (PAGE SHELL & SPACING) -->
    <section id="layout-paginas" class="space-y-4">
      <div class="flex items-center gap-2.5">
        <LayoutTemplate class="w-5 h-5 text-[#FC6714] dark:text-orange-400" />
        <h2 class="text-lg font-heading font-semibold text-[#06064D] dark:text-white">9. Estrutura de Páginas & Margens (Page Shell & Spacing) ⭐</h2>
      </div>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        Padrão obrigatório de margens, respiros perimetrais e estrutura de cabeçalho para todas as telas do ERP.
        <strong>É expressamente obrigatório que a Linha 1 de toda tela seja o BaseBreadcrumb</strong>, seguido do cabeçalho da página, sem colar no topo da viewport.
      </p>

      <!-- Grid com Diretrizes e Demonstração do Page Shell -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Margem Superior & Inferior -->
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 space-y-2.5 shadow-xs">
          <div class="flex items-center gap-2 text-slate-900 dark:text-slate-100 font-bold text-sm">
            <span class="w-2.5 h-2.5 rounded-full bg-[#FC6714]"></span>
            <span>1. Margem Superior (py-8)</span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
            O container raiz de toda view Vue DEVE conter <code class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[#FC6714] font-mono text-[11px]">py-8 w-full</code> (32px de respiro no topo e na base).
          </p>
          <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 font-mono text-[11px] text-slate-600 dark:text-slate-300">
            &lt;div class="py-8 w-full space-y-6"&gt;
          </div>
        </div>

        <!-- Card 2: Linha 1 Obrigatória (Breadcrumb em Box 100%) -->
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 space-y-2.5 shadow-xs">
          <div class="flex items-center gap-2 text-slate-900 dark:text-slate-100 font-bold text-sm">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
            <span>2. Linha 1: Box de Breadcrumb (100%)</span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
            O primeiro elemento é o <code class="font-mono text-[11px]">&lt;BaseBreadcrumb&gt;</code>, encapsulado em um <strong>box de 100% de largura</strong> com borda e superfície elegante.
          </p>
          <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 font-mono text-[11px] text-amber-700 dark:text-amber-400">
            &lt;BaseBreadcrumb :items="..." /&gt;
          </div>
        </div>

        <!-- Card 3: Simetria Horizontal de 60px -->
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 space-y-2.5 shadow-xs">
          <div class="flex items-center gap-2 text-slate-900 dark:text-slate-100 font-bold text-sm">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
            <span>3. Margem Lateral (px-[60px])</span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
            A área de conteúdo (<code class="font-mono text-[11px]">&lt;main&gt;</code>) no <code class="font-mono text-[11px]">AppLayout.vue</code> garante <strong>60px à esquerda e à direita</strong>.
          </p>
          <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 font-mono text-[11px] text-slate-600 dark:text-slate-300">
            &lt;main class="... px-[60px] pb-12"&gt;
          </div>
        </div>

        <!-- Card 4: Ritmo Vertical Entre Blocos -->
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 space-y-2.5 shadow-xs">
          <div class="flex items-center gap-2 text-slate-900 dark:text-slate-100 font-bold text-sm">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            <span>4. Ritmo (space-y-6 / 8)</span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
            Utilize <code class="font-mono text-[11px]">space-y-6</code> (24px) para fluxos operacionais e <code class="font-mono text-[11px]">space-y-8</code> (32px) para telas analíticas e cadastros.
          </p>
          <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 font-mono text-[11px] text-slate-600 dark:text-slate-300">
            Breadcrumb ➔ Topo ➔ Filtros ➔ Grid
          </div>
        </div>
      </div>

      <!-- Diagrama Visual Interativo do Page Shell com Breadcrumb -->
      <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 space-y-4 shadow-xs">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
          <span>Esquema Anatômico do Page Shell com Breadcrumb</span>
          <span class="text-xs font-normal text-slate-400">(Visão perimétrica oficial do ERP Rede Pronta)</span>
        </h3>

        <!-- Mockup Visual de Tela -->
        <div class="p-4 rounded-xl bg-slate-100 dark:bg-slate-950 border border-dashed border-slate-300 dark:border-slate-700 relative overflow-hidden">
          <!-- Régua Superior (py-8 = 32px) -->
          <div class="mb-3 py-1.5 px-3 rounded bg-orange-100 dark:bg-[#FC6714]/20 border border-orange-300 dark:border-[#FC6714]/40 text-center text-xs font-mono font-bold text-[#FC6714] flex items-center justify-center gap-2">
            <span>▲ Margem Superior Obrigatória: py-8 (32px de respiro perimetral no topo) ▲</span>
          </div>

          <!-- Conteúdo Simulado da View -->
          <div class="bg-white dark:bg-slate-900 rounded-lg p-5 border border-slate-200 dark:border-slate-800 space-y-4 shadow-sm">
            <!-- Linha 1 Simulada: Box do Breadcrumb 100% -->
            <div class="w-full py-2 px-3.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200/90 dark:border-slate-700 shadow-2xs flex items-center justify-between">
              <div class="flex items-center gap-1.5 text-[11px] font-mono">
                <span class="text-[#FC6714] font-bold flex items-center gap-1">
                  <Home class="w-3 h-3" /> Início
                </span>
                <span class="text-slate-400">›</span>
                <span class="text-slate-500">Módulo</span>
                <span class="text-slate-400">›</span>
                <span class="text-slate-900 dark:text-slate-100 font-bold">Página Atual</span>
              </div>
              <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 font-bold border border-amber-200 dark:border-amber-800">
                Box 100% Horizontal (Linha 1)
              </span>
            </div>

            <!-- Linha 2 Simulada: Cabeçalho Canônico -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950/80 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xs">
                  Ícone
                </div>
                <div class="space-y-1">
                  <div class="h-5 w-48 bg-slate-200 dark:bg-slate-700 rounded"></div>
                  <div class="h-3 w-72 bg-slate-100 dark:bg-slate-800 rounded"></div>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <div class="h-9 px-3.5 bg-[#FC6714] text-white text-xs font-semibold rounded-lg flex items-center">
                  Ação Primária
                </div>
                <div class="h-9 w-9 bg-slate-100 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700"></div>
              </div>
            </div>

            <!-- Linha 3 Simulada: Corpo / Filtros / Grid -->
            <div class="h-16 bg-slate-50 dark:bg-slate-800/40 rounded-lg border border-dashed border-slate-200 dark:border-slate-700 flex items-center justify-center text-xs text-slate-400 font-mono">
              Conteúdo da View (Filtros, Cards de Métricas, Grid de Dados, Paginação) • space-y-6
            </div>
          </div>

          <!-- Régua Inferior (pb-12 = 48px) -->
          <div class="mt-3 py-1.5 px-3 rounded bg-blue-100 dark:bg-blue-950/40 border border-blue-300 dark:border-blue-800 text-center text-xs font-mono font-bold text-blue-600 dark:text-blue-400 flex items-center justify-center gap-2">
            <span>▼ Margem Inferior: pb-12 (48px no layout principal) + py-8 (32px na view) ▼</span>
          </div>
        </div>
      </div>
    </section>

    <!-- SEÇÃO 10: TOPO DE PÁGINA PADRÃO (PAGE HEADER & BREADCRUMB) -->
    <section id="topo-pagina-padrao" class="space-y-6">
      <div class="flex items-center gap-2.5">
        <Compass class="w-5 h-5 text-[#FC6714] dark:text-orange-400" />
        <h2 class="text-lg font-heading font-semibold text-[#06064D] dark:text-white">10. Topo de Página Padrão (Page Header & Breadcrumb) ⭐</h2>
      </div>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        Padrão canônico de composição do topo de tela no ERP Rede Pronta. Toda página do sistema deve apresentar harmoniosamente
        o <strong>Breadcrumb em Box 100% horizontal na Linha 1</strong> e o <strong>Cabeçalho Principal na Linha 2</strong>, garantindo orientação espacial, consistência semântica e ergonomia operacional.
      </p>

      <!-- Exemplos Vivos / Múltiplas Variações -->
      <div class="space-y-6">
        <!-- Variação 1: Topo Completo com Barra de Abas do Módulo (Padrão Suprimentos & WMS) -->
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 space-y-5 shadow-xs">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800/80">
            <span class="text-xs font-bold uppercase tracking-wider text-[#FC6714]">Variação 1: Topo Completo com Barra de Abas do Módulo</span>
            <span class="text-xs font-mono px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">Módulos Multi-Aba (ex: Suprimentos)</span>
          </div>

          <!-- Demonstração Viva -->
          <div class="p-6 rounded-xl bg-slate-50 dark:bg-[#03032E] border border-slate-200/80 dark:border-[#14147A]/70 space-y-5">
            <!-- Linha 1: BaseBreadcrumb -->
            <BaseBreadcrumb :items="[
              { label: 'Início', href: '#people' },
              { label: 'Operações', href: '#supplies' },
              { label: 'Suprimentos & WMS' }
            ]" />

            <!-- Linha 2: Cabeçalho Canônico -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 text-blue-600 dark:text-blue-400 shadow-2xs shrink-0">
                  <Boxes class="w-6 h-6" />
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
                <button
                  type="button"
                  class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-sm font-semibold shadow-sm transition active:scale-98 cursor-pointer"
                >
                  <Plus class="w-4 h-4" />
                  <span>Nova Movimentação</span>
                </button>

                <button
                  type="button"
                  class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-200 dark:border-[#14147A] bg-white dark:bg-[#03032E] text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition shadow-2xs cursor-pointer"
                  title="Cadastros de Apoio"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="currentColor">
                    <circle cx="12" cy="5" r="2"></circle>
                    <circle cx="12" cy="12" r="2"></circle>
                    <circle cx="12" cy="19" r="2"></circle>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Linha 3: Barra de Abas do Módulo -->
            <div class="flex border-b border-slate-200 dark:border-[#14147A] gap-6 overflow-x-auto select-none pt-2">
              <button
                type="button"
                @click="demoActiveModuleTab = 'materiais'"
                :class="demoActiveModuleTab === 'materiais'
                  ? 'border-[#FC6714] text-[#FC6714] font-bold'
                  : 'border-transparent text-slate-500 hover:text-slate-900 dark:hover:text-white font-medium'"
                class="pb-3 border-b-2 text-sm flex items-center gap-2 transition cursor-pointer shrink-0"
              >
                <Boxes class="w-4 h-4" />
                <span>Materiais</span>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-[#FC6714]/15 text-[#FC6714]">248</span>
              </button>

              <button
                type="button"
                @click="demoActiveModuleTab = 'seriais'"
                :class="demoActiveModuleTab === 'seriais'
                  ? 'border-[#FC6714] text-[#FC6714] font-bold'
                  : 'border-transparent text-slate-500 hover:text-slate-900 dark:hover:text-white font-medium'"
                class="pb-3 border-b-2 text-sm flex items-center gap-2 transition cursor-pointer shrink-0"
              >
                <Tag class="w-4 h-4" />
                <span>Serializado</span>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300">120</span>
              </button>

              <button
                type="button"
                @click="demoActiveModuleTab = 'movimento'"
                :class="demoActiveModuleTab === 'movimento'
                  ? 'border-[#FC6714] text-[#FC6714] font-bold'
                  : 'border-transparent text-slate-500 hover:text-slate-900 dark:hover:text-white font-medium'"
                class="pb-3 border-b-2 text-sm flex items-center gap-2 transition cursor-pointer shrink-0"
              >
                <LayoutTemplate class="w-4 h-4" />
                <span>Movimento</span>
                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300">15</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Variação 2: Topo Operacional Simples com Ações Primárias e Secundárias (Padrão Pessoas) -->
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 space-y-5 shadow-xs">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800/80">
            <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Variação 2: Topo Operacional com Ações Primárias & Secundárias</span>
            <span class="text-xs font-mono px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">Telas Cadastrais (ex: Pessoas)</span>
          </div>

          <!-- Demonstração Viva -->
          <div class="p-6 rounded-xl bg-slate-50 dark:bg-[#03032E] border border-slate-200/80 dark:border-[#14147A]/70 space-y-5">
            <!-- Linha 1: BaseBreadcrumb -->
            <BaseBreadcrumb :items="[
              { label: 'Início', href: '#people' },
              { label: 'Cadastros', href: '#people' },
              { label: 'Pessoas & Entidades' }
            ]" />

            <!-- Linha 2: Cabeçalho Canônico -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 shadow-2xs shrink-0">
                  <Users class="w-6 h-6" />
                </div>
                <div>
                  <h1 class="text-2xl font-semibold font-heading text-[#06064D] dark:text-white tracking-tight">
                    Pessoas & Entidades
                  </h1>
                  <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
                    Base unificada de clientes, fornecedores, parceiros e colaboradores
                  </p>
                </div>
              </div>

              <!-- Ações do Cabeçalho -->
              <div class="flex items-center gap-2 shrink-0">
                <button
                  type="button"
                  class="inline-flex items-center gap-2 h-10 px-3.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 text-sm font-medium transition cursor-pointer shadow-2xs"
                >
                  <FileSpreadsheet class="w-4 h-4 text-emerald-600" />
                  <span>Exportar</span>
                </button>

                <button
                  type="button"
                  class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-sm font-semibold shadow-sm transition active:scale-98 cursor-pointer"
                >
                  <Plus class="w-4 h-4" />
                  <span>Nova Pessoa</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Variação 3: Topo Editorial com Badges de Status & Metadados (Padrão Contratos / FSM) -->
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 space-y-5 shadow-xs">
          <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800/80">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Variação 3: Topo de Detalhe com Badge de Status & Metadados</span>
            <span class="text-xs font-mono px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">Telas de Detalhe / FSM</span>
          </div>

          <!-- Demonstração Viva -->
          <div class="p-6 rounded-xl bg-slate-50 dark:bg-[#03032E] border border-slate-200/80 dark:border-[#14147A]/70 space-y-5">
            <!-- Linha 1: BaseBreadcrumb -->
            <BaseBreadcrumb :items="[
              { label: 'Início', href: '#people' },
              { label: 'Faturamento & Contratos', href: '#integrations' },
              { label: 'Termo de Adesão #2609-001' }
            ]" />

            <!-- Linha 2: Cabeçalho Canônico -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 shadow-2xs shrink-0">
                  <FileCheck class="w-6 h-6" />
                </div>
                <div>
                  <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-2xl font-semibold font-heading text-[#06064D] dark:text-white tracking-tight">
                      Contrato de Custódia PoliFibra
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                      ATIVO / EM VIGOR
                    </span>
                  </div>
                  <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
                    Protocolo: <strong>260929-POLI01</strong> • Solicitante: <strong>ALLOHA / GIGA+</strong> • Posição: <strong>Belém - PA</strong>
                  </p>
                </div>
              </div>

              <!-- Ações do Cabeçalho -->
              <div class="flex items-center gap-2 shrink-0">
                <button
                  type="button"
                  class="inline-flex items-center gap-2 h-10 px-3.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 text-sm font-medium transition cursor-pointer shadow-2xs"
                >
                  <FileText class="w-4 h-4 text-slate-400" />
                  <span>Imprimir Termo</span>
                </button>

                <button
                  type="button"
                  class="inline-flex items-center gap-2 h-10 px-4 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-sm font-semibold shadow-sm transition active:scale-98 cursor-pointer"
                >
                  <span>Editar Contrato</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Bloco de Código Vue Canônico (Copiar e Colar) -->
        <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-900 text-slate-100 p-5 space-y-3 shadow-lg">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-red-500/80"></span>
              <span class="w-3 h-3 rounded-full bg-amber-500/80"></span>
              <span class="w-3 h-3 rounded-full bg-emerald-500/80"></span>
              <span class="text-xs font-mono text-slate-400 ml-2">Template Canônico Obrigatório (PageTop.vue)</span>
            </div>
            <button
              type="button"
              @click="copyCanonicalCode"
              class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-xs font-mono text-[#FC6714] hover:text-white transition flex items-center gap-1.5 cursor-pointer"
            >
              <Check v-if="copiedSnippet" class="w-3.5 h-3.5 text-emerald-400" />
              <Copy v-else class="w-3.5 h-3.5" />
              <span>{{ copiedSnippet ? 'Copiado!' : 'Copiar Código' }}</span>
            </button>
          </div>

          <pre class="font-mono text-xs text-slate-300 overflow-x-auto leading-relaxed p-2"><code>&lt;template&gt;
  &lt;!-- Container Raiz Obrigatório: py-8 w-full space-y-6 --&gt;
  &lt;div class="py-8 w-full space-y-6"&gt;

    &lt;!-- Linha 1 Obrigatória: BaseBreadcrumb Canônico (em Box 100% horizontal) --&gt;
    &lt;BaseBreadcrumb :items="[
      { label: 'Início', href: '#people' },
      { label: 'Domínio / Módulo', href: '#modulo' },
      { label: 'Nome da Tela Atual' }
    ]" /&gt;

    &lt;!-- Linha 2 Obrigatória: Cabeçalho Canônico com Ícone e Ações --&gt;
    &lt;div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4"&gt;
      &lt;div class="flex items-center gap-3"&gt;
        &lt;div class="p-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 text-blue-600 dark:text-blue-400 shadow-2xs shrink-0"&gt;
          &lt;ComponenteIcone class="w-6 h-6" /&gt;
        &lt;/div&gt;
        &lt;div&gt;
          &lt;h1 class="text-2xl font-semibold font-heading text-[#06064D] dark:text-white tracking-tight"&gt;
            Título da Tela
          &lt;/h1&gt;
          &lt;p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5 font-medium"&gt;
            Descrição técnica orientada ao operador e propósito da tela
          &lt;/p&gt;
        &lt;/div&gt;
      &lt;/div&gt;

      &lt;!-- Botão Primário Laranja (#FC6714) e Ações --&gt;
      &lt;div class="flex items-center gap-2 shrink-0"&gt;
        &lt;button type="button" class="h-10 px-4 rounded-lg bg-[#FC6714] text-white text-sm font-semibold shadow-sm hover:bg-[#E0530A] transition"&gt;
          &lt;Plus class="w-4 h-4 inline mr-1.5" /&gt;
          Ação Primária
        &lt;/button&gt;
      &lt;/div&gt;
    &lt;/div&gt;

    &lt;!-- Linha 3: Conteúdo da View (Filtros, Grid, Cards, Paginação) --&gt;
    ...
  &lt;/div&gt;
&lt;/template&gt;</code></pre>
        </div>
      </div>
    </section>

    <!-- ========================================================= -->
    <!-- MODAIS REAIS INSTANCIADOS PARA DEMONSTRAÇÃO E TESTE VIVO -->
    <!-- ========================================================= -->

    <!-- 1. MODAL PEQUENO: CONFIRMAÇÃO -->
    <BaseModal
      v-model="openModalSm"
      size="sm"
      title="Confirmar Inativação"
      description="Esta ação desativará temporariamente os acessos da entidade."
    >
      <div class="flex items-start gap-3 py-2">
        <div class="p-2.5 rounded-full bg-red-100 dark:bg-red-950/60 text-red-600 shrink-0">
          <AlertTriangle class="w-5 h-5" />
        </div>
        <div>
          <p class="text-sm font-medium text-slate-800 dark:text-slate-200">
            Deseja realmente inativar o cadastro de <strong>Telecom Prime Ltda</strong>?
          </p>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Nenhum dado será excluído. O registro apenas não aparecerá nas rotinas operacionais cotidianas.
          </p>
        </div>
      </div>

      <template #footer>
        <button
          type="button"
          @click="openModalSm = false"
          class="h-9 px-3.5 text-xs font-medium rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition cursor-pointer"
        >
          Cancelar
        </button>
        <button
          type="button"
          @click="openModalSm = false"
          class="h-9 px-4 text-xs font-semibold rounded-lg bg-red-600 text-white hover:bg-red-500 transition cursor-pointer"
        >
          Confirmar Inativação
        </button>
      </template>
    </BaseModal>

    <!-- 2. MODAL PADRÃO (LG): CADASTRO -->
    <BaseModal
      v-model="openModalLg"
      size="lg"
      title="Novo Cadastro de Pessoa"
      description="Preencha os campos essenciais de identificação e localização."
    >
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Nome / Razão Social</label>
          <input type="text" placeholder="Nome completo" class="w-full h-10 px-3 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
        </div>
        <div>
          <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">CPF ou CNPJ</label>
          <input type="text" placeholder="000.000.000-00" class="w-full h-10 px-3 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
        </div>
        <div>
          <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">E-mail</label>
          <input type="email" placeholder="contato@empresa.com" class="w-full h-10 px-3 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
        </div>
        <div>
          <label class="block text-xs font-semibold uppercase text-slate-600 dark:text-slate-400 mb-1">Telefone Principal</label>
          <input type="text" placeholder="(31) 99999-0000" class="w-full h-10 px-3 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
        </div>
      </div>

      <template #footer>
        <button
          type="button"
          @click="openModalLg = false"
          class="h-9 px-4 text-xs font-medium rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition cursor-pointer"
        >
          Cancelar
        </button>
        <button
          type="button"
          @click="openModalLg = false"
          class="h-9 px-4 text-xs font-semibold rounded-lg bg-[#FC6714] text-white hover:bg-[#E0530A] active:bg-[#C94605] transition cursor-pointer"
        >
          Salvar Pessoa
        </button>
      </template>
    </BaseModal>

    <!-- 3. MODAL AMPLO (XL): LISTAGEM / RELATÓRIO -->
    <BaseModal
      v-model="openModalXl"
      size="xl"
      title="Histórico de Auditoria & Alterações"
      description="Visualização abrangente de logs operacionais para a conta selecionada."
    >
      <div class="rounded-lg border border-slate-200 dark:border-slate-800 overflow-hidden">
        <table class="w-full text-left text-xs border-collapse">
          <thead class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800">
            <tr>
              <th class="py-2.5 px-4 font-bold text-slate-500 uppercase">Data/Hora</th>
              <th class="py-2.5 px-4 font-bold text-slate-500 uppercase">Usuário</th>
              <th class="py-2.5 px-4 font-bold text-slate-500 uppercase">Ação</th>
              <th class="py-2.5 px-4 font-bold text-slate-500 uppercase">Entidade</th>
              <th class="py-2.5 px-4 font-bold text-slate-500 uppercase">IP Origem</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
            <tr v-for="i in 4" :key="i" class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
              <td class="py-3 px-4 text-slate-500 font-mono">08/09/2026 20:{{ 20 + i }}:15</td>
              <td class="py-3 px-4 font-medium text-slate-800 dark:text-slate-200">admin@redepronta.com</td>
              <td class="py-3 px-4"><span class="px-2 py-0.5 rounded-sm font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-400">UPDATE</span></td>
              <td class="py-3 px-4 text-slate-600 dark:text-slate-400">Pessoas #{{ 100 + i }}</td>
              <td class="py-3 px-4 font-mono text-slate-500">187.23.10.4{{ i }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <template #footer>
        <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Exibindo 4 de 128 registros auditados</span>
        <button
          type="button"
          @click="openModalXl = false"
          class="h-9 px-4 text-xs font-semibold rounded-lg bg-slate-800 dark:bg-slate-200 text-white dark:text-slate-900 transition cursor-pointer"
        >
          Fechar Auditoria
        </button>
      </template>
    </BaseModal>

    <!-- 4. ⭐ MODAL DE TELA CHEIA (FULLSCREEN OVERLAY PARA FORMULÁRIOS COMPLEXOS) -->
    <BaseModal
      v-model="openModalFullscreen"
      size="fullscreen"
      title="Contrato Corporativo & Ordem de Instalação FSM"
      description="Formulário denso e complexo operado em tela cheia sem sair da página atual."
    >
      <template #header-badge>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
          Modo Edição Avançada
        </span>
      </template>

      <!-- Abas de Navegação Interna do Modal Complexo -->
      <template #tabs>
        <div class="flex items-center gap-2 overflow-x-auto text-sm">
          <button
            v-for="(tab, idx) in fsTabs"
            :key="tab.id"
            type="button"
            @click="activeFsTab = tab.id"
            :class="activeFsTab === tab.id
              ? 'border-blue-600 text-[#FC6714] dark:text-orange-400 font-bold border-b-2'
              : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-medium'"
            class="px-4 py-3 border-b-2 transition whitespace-nowrap cursor-pointer flex items-center gap-2"
          >
            <component :is="tab.icon" class="w-4 h-4" />
            <span>{{ idx + 1 }}. {{ tab.label }}</span>
          </button>
        </div>
      </template>

      <!-- Conteúdo da Aba 1: Dados Contratuais (Grid amplo aproveitando a horizontalidade) -->
      <div v-if="activeFsTab === 'dados'" class="space-y-6">
        <!-- Linha 1: 4 Colunas Amplas -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
          <div class="xl:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
              Cliente Titular / Razão Social
            </label>
            <input
              type="text"
              value="Telecom Prime Infraestrutura de Redes e Conectividade Ltda"
              class="w-full h-10 px-3.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100"
            />
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
              CNPJ da Contratante
            </label>
            <input
              type="text"
              value="12.345.678/0001-90"
              class="w-full h-10 px-3.5 text-sm font-mono rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100"
            />
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
              Inscrição Estadual (IE)
            </label>
            <input
              type="text"
              value="062.198.344.0091"
              class="w-full h-10 px-3.5 text-sm font-mono rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100"
            />
          </div>
        </div>

        <!-- Linha 2: 5 Colunas Especializadas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Tipo de Circuito</label>
            <select class="w-full h-10 px-3 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
              <option>Fibra Óptica Dedicada (Link IP)</option>
              <option>MPLS / Ponto a Ponto L2</option>
              <option>Banda Larga Corporativa</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Capacidade / Banda</label>
            <input type="text" value="1 Gbps Full Duplex" class="w-full h-10 px-3 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Vigência do Contrato</label>
            <input type="text" value="36 Meses (Fidelidade)" class="w-full h-10 px-3 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">SLA de Atendimento</label>
            <input type="text" value="4 Horas (24x7 Crítico)" class="w-full h-10 px-3 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Índice Reajuste</label>
            <input type="text" value="IPCA Anual" class="w-full h-10 px-3 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
          </div>
        </div>

        <!-- Linha 3: Card Expandido de Parâmetros de Conectividade e Engenharia -->
        <div class="p-5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 space-y-4">
          <div class="flex items-center justify-between">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-blue-600"></span>
              Parâmetros de Rede, Entrega & Equipamentos de Campo (NOC / FSM)
            </h4>
            <span class="text-xs font-mono text-slate-500">ID Circuito: RP-BHZ-2026-0981</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
              <label class="block text-xs text-slate-500 mb-1">Bloco IPv4 Alocado</label>
              <input type="text" value="187.23.10.0/29 (6 IPs úteis)" class="w-full h-9 px-3 text-xs font-mono rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
            </div>
            <div>
              <label class="block text-xs text-slate-500 mb-1">Prefixo IPv6 Delegado</label>
              <input type="text" value="2804:49c0:1002::/48" class="w-full h-9 px-3 text-xs font-mono rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
            </div>
            <div>
              <label class="block text-xs text-slate-500 mb-1">VLAN de Transporte (QinQ)</label>
              <input type="text" value="C-VLAN 100 / S-VLAN 3420" class="w-full h-9 px-3 text-xs font-mono rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
            </div>
            <div>
              <label class="block text-xs text-slate-500 mb-1">Roteador CPE Alocado</label>
              <input type="text" value="Huawei NetEngine AR651 (10G SFP+)" class="w-full h-9 px-3 text-xs rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
            </div>
          </div>
        </div>
      </div>

      <!-- Conteúdo da Aba 2: Faturamento -->
      <div v-if="activeFsTab === 'financeiro'" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Valor Mensal Recorrente (MRR)</label>
            <input type="text" value="R$ 3.850,00" class="w-full h-10 px-3 text-sm font-semibold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400" />
          </div>
          <div>
            <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Taxa de Instalação (Setup)</label>
            <input type="text" value="R$ 1.500,00" class="w-full h-10 px-3 text-sm font-semibold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
          </div>
          <div>
            <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Dia de Vencimento</label>
            <input type="text" value="Dia 10 de cada mês" class="w-full h-10 px-3 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
          </div>
        </div>
      </div>

      <!-- Conteúdo da Aba 3: Ordem de Instalação FSM -->
      <div v-if="activeFsTab === 'fsm'" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Equipe Técnica Responsável</label>
            <select class="w-full h-10 px-3 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
              <option>Equipe Alpha Fibra - Belo Horizonte / Central</option>
              <option>Equipe Beta Infra - Betim / Contagem</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Janela de Instalação Agendada</label>
            <input type="text" value="12/09/2026 das 08:00 às 12:00" class="w-full h-10 px-3 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
          </div>
        </div>
      </div>

      <!-- Rodapé Fixo do Modal de Tela Cheia -->
      <template #footer>
        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>Rascunho salvo automaticamente às 20:25</span>
        </div>

        <div class="flex items-center gap-3">
          <button
            type="button"
            @click="openModalFullscreen = false"
            class="h-10 px-4 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition cursor-pointer"
          >
            Fechar sem Salvar
          </button>
          <button
            type="button"
            @click="openModalFullscreen = false"
            class="h-10 px-4 text-sm font-medium rounded-lg border border-blue-300 dark:border-blue-700 bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 hover:bg-blue-100 transition cursor-pointer"
          >
            Salvar Rascunho
          </button>
          <button
            type="button"
            @click="openModalFullscreen = false"
            class="h-10 px-5 text-sm font-semibold rounded-lg bg-[#FC6714] text-white hover:bg-[#E0530A] active:bg-[#C94605] transition shadow-xs cursor-pointer flex items-center gap-2"
          >
            <Check class="w-4 h-4" />
            <span>Emitir Contrato & Ordem de Instalação</span>
          </button>
        </div>
      </template>
    </BaseModal>

    <!-- 5. MODAL LATERAL: DRAWER (SLIDE-OVER) -->
    <BaseModal
      v-model="openModalDrawer"
      size="drawer"
      title="Painel de Detalhes da Entidade"
      description="Inspeção rápida de relacionamentos e atividades recentes."
    >
      <div class="space-y-5">
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-2">
          <p class="text-base font-bold text-slate-900 dark:text-slate-100">Rede Pronta Telecomunicações Ltda</p>
          <p class="text-xs font-mono text-slate-500 dark:text-slate-400">CNPJ: 12.345.678/0001-90</p>
          <div class="flex items-center gap-1.5 pt-1">
            <span class="px-2 py-0.5 rounded-sm text-xs font-semibold bg-cyan-50 text-cyan-700 dark:bg-cyan-950/70 dark:text-cyan-400">PJ</span>
            <span class="px-2 py-0.5 rounded-sm text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-400">Ativo</span>
            <span class="px-2 py-0.5 rounded-sm text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-950/70 dark:text-blue-400">Cliente</span>
          </div>
        </div>

        <div class="space-y-3">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Últimos Atendimentos</h4>
          <div v-for="n in 3" :key="n" class="p-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 space-y-1 text-xs">
            <div class="flex items-center justify-between font-semibold text-slate-800 dark:text-slate-200">
              <span>Instalação Ponto #{{ n }}</span>
              <span class="text-emerald-600">Concluído</span>
            </div>
            <p class="text-slate-500">Atendido por Equipe Alpha • 0{{ n }}/09/2026</p>
          </div>
        </div>
      </div>

      <template #footer>
        <button
          type="button"
          @click="openModalDrawer = false"
          class="w-full h-10 text-xs font-semibold rounded-lg bg-[#FC6714] text-white hover:bg-[#E0530A] active:bg-[#C94605] transition cursor-pointer"
        >
          Concluir Visualização
        </button>
      </template>
    </BaseModal>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import {
  Palette, Type, MousePointerClick, Sliders, Tag, Bell, Table,
  Sun, Moon, Plus, RefreshCw, Check, Trash2, ExternalLink,
  Loader2, Settings, Search, CheckCircle2, Info, AlertTriangle,
  Maximize2, AlertCircle, Layers, Sidebar as SidebarIcon, FileText,
  DollarSign, Wrench, LayoutTemplate, Compass, Users, FileSpreadsheet,
  FileCheck, Boxes, Copy, Home
} from 'lucide-vue-next';
import { useTheme } from '../composables/useTheme';
import BaseBreadcrumb from '../components/common/BaseBreadcrumb.vue';
import BaseModal from '../components/common/BaseModal.vue';
import CitySearchSelect from '../components/common/CitySearchSelect.vue';
import DateInput from '../components/common/DateInput.vue';
import BaseSelect from '../components/common/BaseSelect.vue';

const { theme, toggleTheme } = useTheme();

const demoSwitch = ref(true);
const demoCityId = ref<number | null>(null);
const demoDate = ref('25/12/1990');
const demoSelect = ref('3106200');

// Estados para a Seção 10: Topo de Página Padrão
const demoActiveModuleTab = ref('materiais');
const copiedSnippet = ref(false);

function copyCanonicalCode() {
  const code = `<template>
  <!-- Container Raiz Obrigatório: py-8 w-full space-y-6 -->
  <div class="py-8 w-full space-y-6">

    <!-- Linha 1 Obrigatória: BaseBreadcrumb Canônico (em Box 100% horizontal) -->
    <BaseBreadcrumb :items="[
      { label: 'Início', href: '#people' },
      { label: 'Domínio / Módulo', href: '#modulo' },
      { label: 'Nome da Tela Atual' }
    ]" />

    <!-- Linha 2 Obrigatória: Cabeçalho Canônico com Ícone e Ações -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="p-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 text-blue-600 dark:text-blue-400 shadow-2xs shrink-0">
          <ComponenteIcone class="w-6 h-6" />
        </div>
        <div>
          <h1 class="text-2xl font-semibold font-heading text-[#06064D] dark:text-white tracking-tight">
            Título da Tela
          </h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
            Descrição técnica orientada ao operador e propósito da tela
          </p>
        </div>
      </div>

      <!-- Botão Primário Laranja (#FC6714) e Ações -->
      <div class="flex items-center gap-2 shrink-0">
        <button type="button" class="h-10 px-4 rounded-lg bg-[#FC6714] text-white text-sm font-semibold shadow-sm hover:bg-[#E0530A] transition">
          <Plus class="w-4 h-4 inline mr-1.5" />
          Ação Primária
        </button>
      </div>
    </div>

    <!-- Linha 3: Conteúdo da View (Filtros, Grid, Cards, Paginação) -->
    ...
  </div>
</template>`;

  if (navigator?.clipboard?.writeText) {
    navigator.clipboard.writeText(code).then(() => {
      copiedSnippet.value = true;
      setTimeout(() => { copiedSnippet.value = false; }, 2500);
    });
  }
}

// Estados de abertura de modais
const openModalSm = ref(false);
const openModalLg = ref(false);
const openModalXl = ref(false);
const openModalFullscreen = ref(false);
const openModalDrawer = ref(false);

// Abas do Modal Fullscreen
const activeFsTab = ref('dados');
const fsTabs = [
  { id: 'dados', label: 'Dados Contratuais & Circuito', icon: FileText },
  { id: 'financeiro', label: 'Faturamento & Recorrência', icon: DollarSign },
  { id: 'fsm', label: 'Ordem de Instalação FSM', icon: Wrench },
];

const sections = [
  { id: 'tipografia', label: '1. Tipografia' },
  { id: 'cores', label: '2. Cores & Tokens' },
  { id: 'botoes', label: '3. Botões & Ações' },
  { id: 'formularios', label: '4. Formulários' },
  { id: 'badges', label: '5. Badges & Personas' },
  { id: 'alertas', label: '6. Alertas' },
  { id: 'tabelas', label: '7. Tabelas' },
  { id: 'modais', label: '8. Modais & Fullscreen ⭐' },
  { id: 'layout-paginas', label: '9. Layout & Margens ⭐' },
  { id: 'topo-pagina-padrao', label: '10. Topo de Página Padrão ⭐' },
];
</script>
