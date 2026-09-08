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
            <button class="h-10 px-4 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-500 transition shadow-xs flex items-center gap-2 cursor-pointer">
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
        <Sliders class="w-5 h-5 text-blue-600 dark:text-blue-400" />
        <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">4. Formulários & Inputs Ergonômicos</h2>
      </div>
      <p class="text-sm text-slate-500 dark:text-slate-400">
        Labels destacadas, inputs de altura confortável `h-10`, texto em `text-sm` e contraste nítido em ambos os temas.
      </p>

      <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 space-y-6 shadow-xs">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
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
        <div class="p-4 rounded-xl border border-emerald-200 dark:border-emerald-900/80 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-300 flex items-start gap-3">
          <CheckCircle2 class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
          <div class="text-sm">
            <p class="font-semibold">Cadastro concluído com sucesso</p>
            <p class="text-emerald-700 dark:text-emerald-400 mt-0.5">A pessoa foi salva com integridade relacional de cidade e conta.</p>
          </div>
        </div>

        <div class="p-4 rounded-xl border border-blue-200 dark:border-blue-900/80 bg-blue-50 dark:bg-blue-950/40 text-blue-900 dark:text-blue-300 flex items-start gap-3">
          <Info class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" />
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

    <!-- SEÇÃO 8: MODAIS, DIÁLOGOS & FORMULÁRIOS EM TELA CHEIA (NOVO) -->
    <section id="modais" class="space-y-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2.5">
          <Maximize2 class="w-5 h-5 text-blue-600 dark:text-blue-400" />
          <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100">8. Modais, Diálogos & Formulários em Tela Cheia</h2>
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
              Sobrepõe quase todo o viewport (deixa margem arejada de 24px a 32px nas bordas), com backdrop blur escurecido, cantos arredondados finos, cabeçalho fixo com abas de contexto, corpo com scroll próprio e rodapé com ações permanentes. <strong>Substitui a necessidade de abrir novas páginas para formulários densos e complexos.</strong>
            </p>
          </div>
          <button
            type="button"
            @click="openModalFullscreen = true"
            class="h-11 px-5 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-500 transition shadow-sm cursor-pointer flex items-center justify-center gap-2"
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
          class="h-9 px-4 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-500 transition cursor-pointer"
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
              ? 'border-blue-600 text-blue-600 dark:text-blue-400 font-bold border-b-2'
              : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-medium'"
            class="px-4 py-3 border-b-2 transition whitespace-nowrap cursor-pointer flex items-center gap-2"
          >
            <component :is="tab.icon" class="w-4 h-4" />
            <span>{{ idx + 1 }}. {{ tab.label }}</span>
          </button>
        </div>
      </template>

      <!-- Conteúdo da Aba 1: Dados Contratuais -->
      <div v-if="activeFsTab === 'dados'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
          <div class="md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
              Cliente Titular / Razão Social
            </label>
            <input
              type="text"
              value="Telecom Prime Infraestrutura de Redes Ltda"
              class="w-full h-10 px-3.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100"
            />
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
              CNPJ do Contrato
            </label>
            <input
              type="text"
              value="12.345.678/0001-90"
              class="w-full h-10 px-3.5 text-sm font-mono rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Tipo de Circuito</label>
            <select class="w-full h-10 px-3 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
              <option>Fibra Óptica Dedicada (Link IP)</option>
              <option>MPLS / Ponto a Ponto L2</option>
              <option>Banda Larga Corporativa</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Largura de Banda</label>
            <input type="text" value="1 Gbps Full Duplex" class="w-full h-10 px-3 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">Vigência Inicial</label>
            <input type="text" value="36 Meses" class="w-full h-10 px-3 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">SLA de Atendimento</label>
            <input type="text" value="4 Horas (24x7)" class="w-full h-10 px-3 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
          </div>
        </div>

        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 space-y-3">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Parâmetros de Rede & Entrega</h4>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs text-slate-500 mb-1">Bloco IPv4 Alocado</label>
              <input type="text" value="187.23.10.0/29 (6 IPs úteis)" class="w-full h-9 px-3 text-xs font-mono rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
            </div>
            <div>
              <label class="block text-xs text-slate-500 mb-1">VLAN de Transporte</label>
              <input type="text" value="VLAN 3420" class="w-full h-9 px-3 text-xs font-mono rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
            </div>
            <div>
              <label class="block text-xs text-slate-500 mb-1">Equipamento CPE</label>
              <input type="text" value="Huawei NetEngine AR651" class="w-full h-9 px-3 text-xs rounded-md border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100" />
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
            class="h-10 px-5 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-500 transition shadow-xs cursor-pointer flex items-center gap-2"
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
          class="w-full h-10 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-500 transition cursor-pointer"
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
  Sun, Moon, ArrowLeft, Plus, RefreshCw, Check, Trash2, ExternalLink,
  Loader2, Settings, Search, CheckCircle2, Info, AlertTriangle,
  Maximize2, AlertCircle, Layers, Sidebar as SidebarIcon, FileText,
  DollarSign, Wrench
} from 'lucide-vue-next';
import { useTheme } from '../composables/useTheme';
import { useNavigation } from '../composables/useNavigation';
import BaseModal from '../components/common/BaseModal.vue';

const { theme, toggleTheme } = useTheme();
const { setView } = useNavigation();

const demoSwitch = ref(true);

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
];
</script>
