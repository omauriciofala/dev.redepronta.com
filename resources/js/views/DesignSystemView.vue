<template>
  <div class="p-8 max-w-7xl mx-auto space-y-12">
    <!-- Header Principal do Design System -->
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-800/80">
      <div>
        <div class="flex items-center gap-3">
          <div class="p-2 rounded-xl bg-blue-600/10 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
            <Palette class="w-6 h-6" />
          </div>
          <div>
            <div class="flex items-center gap-2.5">
              <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">Design System</h1>
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 dark:bg-blue-950/70 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                v1.0 Canônico
              </span>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
              Tokens visuais, tipografia ergonômica, superfícies adaptáveis e componentes do ERP Rede Pronta.
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

        <button
          type="button"
          @click="setView('people')"
          class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-500 transition shadow-xs cursor-pointer"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Voltar para Pessoas</span>
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
        <Type class="w-5 h-5 text-blue-600 dark:text-blue-400" />
        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">1. Escala Tipográfica Ergonômica</h2>
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
        <Palette class="w-5 h-5 text-blue-600 dark:text-blue-400" />
        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">2. Paleta de Cores & Tokens Semânticos</h2>
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
          <!-- Primária -->
          <div class="p-4 rounded-xl border border-blue-200 dark:border-blue-900/60 bg-blue-50 dark:bg-blue-950/40 text-blue-900 dark:text-blue-300">
            <div class="flex items-center justify-between">
              <span class="text-sm font-semibold">Primária / Ação</span>
              <span class="w-3 h-3 rounded-full bg-blue-600"></span>
            </div>
            <p class="text-xs font-mono mt-2">blue-600 (#2563eb)</p>
            <p class="text-xs text-blue-700/80 dark:text-blue-400/80 mt-1">Botões de ação, links ativos, foco</p>
          </div>

          <!-- Sucesso / PF -->
          <div class="p-4 rounded-xl border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-300">
            <div class="flex items-center justify-between">
              <span class="text-sm font-semibold">Sucesso / Ativo / PF</span>
              <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
            </div>
            <p class="text-xs font-mono mt-2">emerald-600 (#059669)</p>
            <p class="text-xs text-emerald-700/80 dark:text-emerald-400/80 mt-1">Status Ativo, Pessoa Física</p>
          </div>

          <!-- Corporativo / PJ -->
          <div class="p-4 rounded-xl border border-cyan-200 dark:border-cyan-900/60 bg-cyan-50 dark:bg-cyan-950/40 text-cyan-900 dark:text-cyan-300">
            <div class="flex items-center justify-between">
              <span class="text-sm font-semibold">Corporativo / PJ</span>
              <span class="w-3 h-3 rounded-full bg-cyan-600"></span>
            </div>
            <p class="text-xs font-mono mt-2">cyan-600 (#0891b2)</p>
            <p class="text-xs text-cyan-700/80 dark:text-cyan-400/80 mt-1">Pessoa Jurídica, Fornecedor</p>
          </div>

          <!-- Alerta / Perigo -->
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
    </section>

    <!-- SEÇÃO 3: BOTÕES & CONTROLES -->
    <section id="botoes" class="space-y-4">
      <div class="flex items-center gap-2.5">
        <MousePointerClick class="w-5 h-5 text-blue-600 dark:text-blue-400" />
        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">3. Botões & Ações Interativas</h2>
      </div>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        Altura ergonômica mínima de 40px (`h-10`) para cliques seguros e operação rápida em telas sensíveis ou de alta resolução.
      </p>

      <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 space-y-6 shadow-xs">
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Variantes de Estilo</span>
          <div class="flex flex-wrap items-center gap-3 mt-3">
            <!-- Primário -->
            <button class="h-10 px-4 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-500 transition shadow-xs flex items-center gap-2 cursor-pointer">
              <Plus class="w-4 h-4" />
              <span>Botão Primário</span>
            </button>

            <!-- Secundário / Outline -->
            <button class="h-10 px-4 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition shadow-2xs flex items-center gap-2 cursor-pointer">
              <RefreshCw class="w-4 h-4" />
              <span>Secundário / Outline</span>
            </button>

            <!-- Sucesso -->
            <button class="h-10 px-4 text-sm font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-500 transition shadow-xs flex items-center gap-2 cursor-pointer">
              <Check class="w-4 h-4" />
              <span>Ação Sucesso</span>
            </button>

            <!-- Perigo -->
            <button class="h-10 px-4 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-500 transition shadow-xs flex items-center gap-2 cursor-pointer">
              <Trash2 class="w-4 h-4" />
              <span>Perigo / Excluir</span>
            </button>

            <!-- Ghost -->
            <button class="h-10 px-4 text-sm font-medium rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center gap-2 cursor-pointer">
              <ExternalLink class="w-4 h-4" />
              <span>Ghost / Neutro</span>
            </button>
          </div>
        </div>

        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Estados (Desabilitado & Carregando)</span>
          <div class="flex flex-wrap items-center gap-3 mt-3">
            <!-- Loading -->
            <button disabled class="h-10 px-4 text-sm font-medium rounded-lg bg-blue-600/70 text-white flex items-center gap-2 cursor-not-allowed opacity-80">
              <Loader2 class="w-4 h-4 animate-spin" />
              <span>Processando...</span>
            </button>

            <!-- Disabled -->
            <button disabled class="h-10 px-4 text-sm font-medium rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800/40 text-slate-400 dark:text-slate-600 cursor-not-allowed">
              Ação Desabilitada
            </button>

            <!-- Ícone Quadrado Confortável -->
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
        <Sliders class="w-5 h-5 text-blue-600 dark:text-blue-400" />
        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">4. Formulários & Inputs Ergonômicos</h2>
      </div>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        Labels destacadas, inputs de altura confortável `h-10`, texto em `text-sm` e contraste nítido em ambos os temas.
      </p>

      <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 space-y-6 shadow-xs">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
          <!-- Campo Normal -->
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
              Nome Completo / Razão Social
            </label>
            <input
              type="text"
              placeholder="Ex: Maria Silva Sauro"
              value="Maria Silva Sauro"
              class="w-full h-10 px-3.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden transition"
            />
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Nome cadastral canônico</p>
          </div>

          <!-- Campo com Ícone (Busca) -->
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
              Busca com Ícone
            </label>
            <div class="relative">
              <Search class="w-4 h-4 absolute left-3.5 top-3 text-slate-400" />
              <input
                type="text"
                placeholder="Pesquisar por CPF, CNPJ ou Nome..."
                class="w-full h-10 pl-10 pr-3.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden transition"
              />
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Filtro em tempo real</p>
          </div>

          <!-- Select -->
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
              Seleção de Cidade / UF
            </label>
            <select class="w-full h-10 px-3.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden transition">
              <option>Belo Horizonte / MG (IBGE: 3106200)</option>
              <option>São Paulo / SP (IBGE: 3550308)</option>
              <option>Rio de Janeiro / RJ (IBGE: 3304557)</option>
            </select>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Integridade relacional com código IBGE</p>
          </div>
        </div>

        <!-- Controles de Toggle e Checkbox -->
        <div class="pt-4 border-t border-slate-100 dark:border-slate-800/80 flex flex-wrap items-center gap-8">
          <!-- Switch Interativo -->
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

          <!-- Checkbox Amplo -->
          <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" checked class="w-4 h-4 rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500" />
            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Aceite de notificações automáticas</span>
          </label>
        </div>
      </div>
    </section>

    <!-- SEÇÃO 5: BADGES & PERSONAS DO ERP -->
    <section id="badges" class="space-y-4">
      <div class="flex items-center gap-2.5">
        <Tag class="w-5 h-5 text-blue-600 dark:text-blue-400" />
        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">5. Badges & Personas do ERP</h2>
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
        <Bell class="w-5 h-5 text-blue-600 dark:text-blue-400" />
        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">6. Banners de Alerta & Notificação</h2>
      </div>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        Comunicação clara de erros, avisos e confirmações operacionais sem sobrecarregar o usuário.
      </p>

      <div class="space-y-3">
        <!-- Sucesso -->
        <div class="p-4 rounded-xl border border-emerald-200 dark:border-emerald-900/80 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-300 flex items-start gap-3">
          <CheckCircle2 class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
          <div class="text-sm">
            <p class="font-semibold">Cadastro concluído com sucesso</p>
            <p class="text-emerald-700 dark:text-emerald-400 mt-0.5">A pessoa foi salva com integridade relacional de cidade e conta.</p>
          </div>
        </div>

        <!-- Informativo -->
        <div class="p-4 rounded-xl border border-blue-200 dark:border-blue-900/80 bg-blue-50 dark:bg-blue-950/40 text-blue-900 dark:text-blue-300 flex items-start gap-3">
          <Info class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" />
          <div class="text-sm">
            <p class="font-semibold">Sincronização em segundo plano</p>
            <p class="text-blue-700 dark:text-blue-400 mt-0.5">A busca relacional é realizada sob demanda utilizando códigos de cidade IBGE.</p>
          </div>
        </div>

        <!-- Atenção -->
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
        <Table class="w-5 h-5 text-blue-600 dark:text-blue-400" />
        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">7. Padrão de Tabelas Confortáveis</h2>
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
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import {
  Palette, Type, MousePointerClick, Sliders, Tag, Bell, Table,
  Sun, Moon, ArrowLeft, Plus, RefreshCw, Check, Trash2, ExternalLink,
  Loader2, Settings, Search, CheckCircle2, Info, AlertTriangle
} from 'lucide-vue-next';
import { useTheme } from '../composables/useTheme';
import { useNavigation } from '../composables/useNavigation';

const { theme, toggleTheme } = useTheme();
const { setView } = useNavigation();

const demoSwitch = ref(true);

const sections = [
  { id: 'tipografia', label: '1. Tipografia' },
  { id: 'cores', label: '2. Cores & Tokens' },
  { id: 'botoes', label: '3. Botões & Ações' },
  { id: 'formularios', label: '4. Formulários' },
  { id: 'badges', label: '5. Badges & Personas' },
  { id: 'alertas', label: '6. Alertas' },
  { id: 'tabelas', label: '7. Tabelas' },
];
</script>
