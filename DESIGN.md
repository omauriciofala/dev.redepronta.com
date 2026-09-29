# Guia Oficial do Design System & BrandBook - ERP Rede Pronta

> **Documento Normativo Canônico**: Este guia define todas as diretrizes de interface do usuário (UI), experiência do usuário (UX), tokens de design, tipografia, paleta cromática e arquitetura visual para o **ERP Rede Pronta**.
> Todas as telas, componentes e fluxos desenvolvidos no ecossistema devem seguir rigorosamente estes padrões.

---

## 📑 Sumário

1. [Identidade & Filosofia Visual](#1-identidade--filosofia-visual)
2. [Tipografia Institucional (BrandBook)](#2-tipografia-institucional-brandbook)
3. [Paleta Cromática & Tokens de Cores](#3-paleta-cromática--tokens-de-cores)
4. [Escala Canônica de Cantos (Enterprise Sóbrio)](#4-escala-canônica-de-cantos-enterprise-sóbrio)
5. [Estrutura Canônica de Páginas (Page Shell & Spacing)](#5-estrutura-canônica-de-páginas-page-shell--spacing)
6. [Topo de Página Padrão (Linha 1 e Linha 2)](#6-topo-de-página-padrão-linha-1-e-linha-2)
7. [Componentes de Formulário & Controles de Entrada](#7-componentes-de-formulário--controles-de-entrada)
8. [Botões & Ações Interativas](#8-botões--ações-interativas)
9. [Modais, Janelas de Diálogo & Drawers](#9-modais-janelas-de-diálogo--drawers)
10. [Tabelas de Dados & Paginação](#10-tabelas-de-dados--paginação)
11. [Badges, Tags & Indicadores de Status](#11-badges-tags--indicadores-de-status)
12. [Acessibilidade, Estados de Foco & Dark Mode](#12-acessibilidade-estados-de-foco--dark-mode)

---

## 1. Identidade & Filosofia Visual

O **ERP Rede Pronta** é um sistema de missão crítica para gestão integrada de Field Service Management (FSM), Estoque/WMS com rastreabilidade serial, Faturamento, Contratos e Governança de Usuários (RBAC).

### Princípios Norteadores de Design:
- **Sobriedade Enterprise**: Ausência de elementos infantis, cantos excessivamente arredondados ou decorações desnecessárias. A interface prioriza densidade informacional equilibrada e organização lógica.
- **Ergonomia Operacional**: Desenhado para operadores com turnos prolongados de digitação e consulta. Espaçamentos generosos no topo da página, textos legíveis (mínimo de 14px para leitura regular) e contraste em conformidade com normas WCAG AA.
- **Hierarquia Visual Imediata**: O usuário deve identificar em menos de 1 segundo em qual módulo se encontra, qual é a hierarquia de navegação e quais são os botões de ação primária.
- **Harmonia Multi-Tema**: Suporte nativo completo a Modo Claro (*Light*) e Modo Escuro (*Dark*), preservando as cores institucionais Laranja `#FC6714` e Azul Navy `#06064D`.

---

## 2. Tipografia Institucional (BrandBook)

A tipografia do sistema é rigorosamente dividida entre duas famílias de fontes do Google Fonts, devidamente importadas no cabeçalho da aplicação:

### A. Headings & Títulos (`h1`-`h6`, `.font-heading`, `brand-heading`)
- **Família:** `'Kanit'`, sans-serif.
- **Peso Mandatório:** **SemiBold 600** (`font-weight: 600 !important`).
- **Letter Spacing:** `-0.015em` (tracking levemente fechado para coesão ótica).
- **Uso Estrito:** Exclusivo para títulos principais de páginas (H1), títulos de seções/modais (H2), títulos de cartões analíticos (H3) e chamadas de alto impacto.

### B. Corpo de Texto, Formulários & Controles (`body`, `.font-body`)
- **Família:** `'Montserrat'`, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif.
- **Pesos Mandatórios:** **Medium 500** (para textos corridos, labels, inputs e tabelas) e **Bold 700** (para ênfases de métricas e cabeçalhos de tabela).
- **Uso Estrito:** Todo o corpo do ERP, parágrafos, botões, células de tabelas, campos de formulários, tooltips e metadados.

### Escala Tipográfica Canônica

| Nível Semântico | Classe Tailwind | Tamanho / Altura Linha | Peso & Família | Exemplo de Aplicação |
| :--- | :--- | :--- | :--- | :--- |
| **Título H1** | `text-2xl font-bold font-heading` | 24px / 32px | Kanit 600 | Cabeçalho principal da página |
| **Título H2** | `text-lg font-semibold font-heading` | 18px / 28px | Kanit 600 | Cabeçalho de modais ou seções |
| **Subtítulo H3** | `text-base font-semibold` | 16px / 24px | Montserrat 600 | Título de cards, blocos ou filtros |
| **Texto Base** | `text-sm font-medium` | 14px / 20px | Montserrat 500 | Inputs, selects, células de tabelas |
| **Metadados / Badges** | `text-xs font-semibold` | 12px / 16px | Montserrat 600 | Badges de status, datas, IDs secundários |
| **Micro Labels** | `text-[11px] font-bold` | 11px / 14px | Montserrat 700 | Pílulas em breadcrumb, tags compactas |

---

## 3. Paleta Cromática & Tokens de Cores

As cores oficiais do BrandBook Rede Pronta e os tokens do Tailwind CSS v4 (`resources/css/app.css`):

### 3.1 Cores Institucionais Canônicas
- **Laranja Oficial (Primary Action):**
  - Base: `#FC6714` (`--color-brand-orange`)
  - Hover: `#E0530A` (`--color-brand-orange-hover`)
  - Active: `#C94605` (`--color-brand-orange-active`)
  - Fundo Sutil (Subtle): `rgba(252, 103, 20, 0.12)` (`--color-brand-orange-subtle`)
- **Azul Navy Institucional (Deep Foundation):**
  - Base: `#06064D` (`--color-brand-navy`)
  - Light: `#0B0B68` (`--color-brand-navy-light`)
  - Dark Canvas: `#03032E` (`--color-brand-navy-dark`)
  - Borda Dark: `#14147A` (`--color-brand-navy-border`)

### 3.2 Superfícies e Contrastes por Tema

| Token / Função | Tema Claro (Light) | Tema Escuro (Dark) | Aplicação |
| :--- | :--- | :--- | :--- |
| **Canvas de Fundo** | `#f8fafc` (`slate-50`) | `#03032E` / `#020617` | Fundo principal da janela e do `<main>` |
| **Superfície Card** | `#ffffff` | `#06064D` / `slate-900` | Cards, caixas de breadcrumb, modais |
| **Superfície Hover** | `#f1f5f9` (`slate-100`) | `#0B0B68` / `slate-800` | Linhas de tabelas e menus ao passar o mouse |
| **Bordas Sutis** | `#e2e8f0` (`slate-200`) | `#14147A` / `slate-800` | Divisores de blocos, contornos de cards |
| **Texto Título** | `#06064D` | `#ffffff` | Títulos H1, H2 e números em destaque |
| **Texto Primário** | `#0f172a` (`slate-900`) | `#f8fafc` (`slate-100`) | Textos corridos, inputs, valores |
| **Texto Secundário** | `#64748b` (`slate-500`) | `#94a3b8` (`slate-400`) | Descrições de apoio, labels secundárias |

---

## 4. Escala Canônica de Cantos (Enterprise Sóbrio)

Para garantir elegância corporativa e evitar o aspecto arredondado infantil, o sistema adota cantos mais retos e sóbrios, padronizados no `@theme` do CSS:

```css
--radius-xs: 2px;
--radius-sm: 2px;
--radius-md: 4px;
--radius-lg: 5px;
--radius-xl: 6px;
--radius-2xl: 8px;
--radius-3xl: 10px;
--radius-4xl: 12px;
```

### Regras de Aplicação dos Cantos:
1. **`rounded-sm` / `rounded-xs` (2px):**
   - Checkboxes nativas e customizadas.
   - Radio buttons e pequenos marcadores gráficos.
2. **`rounded-md` (4px):**
   - Badges de status, tags e contadores numéricos de listagens.
   - Indicadores de etapas e chips de filtros selecionados.
   - *Nota:* O CSS global força que elementos com estilo de tag adotem `border-radius: 4px` mesmo com classes genéricas de arredondamento.
3. **`rounded-lg` (5px):**
   - Todos os botões do sistema (`btn-primary`, botões neutros, outlines, ícones).
   - Todos os campos de entrada: `<input>`, `<select>`, `<textarea>`.
   - Menus dropdown, menus de contexto e paginação.
4. **`rounded-xl` (6px):**
   - Cartões de dados (`cards`), contêineres de seções e contêineres de tabelas.
   - **Box canônico do Breadcrumb** (`BaseBreadcrumb`).
5. **`rounded-2xl` (8px):**
   - Modais centrais (`BaseModal`) e gaveta lateral de detalhes (`Drawer`).

---

## 5. Estrutura Canônica de Páginas (Page Shell & Spacing)

Toda tela do ERP Rede Pronta deve respeitar rigorosamente a arquitetura perimetral definida pelo shell mestre:

```
+-----------------------------------------------------------------------------------------+
| Top Bar / AppLayout                                                                     |
+-----------------------------------------------------------------------------------------+
| [Sidebar] | <main class="flex-1 flex flex-col h-full overflow-y-auto pb-12 px-[60px]">  |
| (240px)   |   <div class="py-8 w-full space-y-6">                                       |
|           |                                                                             |
|           |     [Linha 1: Box 100% BaseBreadcrumb]                                      |
|           |     +-----------------------------------------------------------------+     |
|           |     | Início > Módulo > Página Atual              [Badge/Ação Direita]|     |
|           |     +-----------------------------------------------------------------+     |
|           |                                                                             |
|           |     [Linha 2: Cabeçalho Canônico da Tela]                                   |
|           |     +-----------------------------------------------------------------+     |
|           |     | [Ícone] Título da Página H1   [Badge Contador]   [Botões Ação]  |     |
|           |     |         Descrição explicativa da tela                           |     |
|           |     +-----------------------------------------------------------------+     |
|           |                                                                             |
|           |     [Conteúdo Principal: Filtros, Tabela, Gráficos ou Formulários]          |
|           |                                                                             |
|           |   </div>                                                                    |
|           | </main>                                                                     |
+-----------------------------------------------------------------------------------------+
```

### Regras Mandatórias de Espaçamento:
1. **Margem Superior e Inferior Obrigatória (`py-8` = 32px)**:
   - Todo container raiz de uma view Vue **DEVE CONTER**: `<div class="py-8 w-full space-y-6">` (ou `space-y-8`).
   - **PROIBIÇÃO ESTRITA:** O cabeçalho de uma tela **NUNCA DEVE ENCOSTAR NO TETO** da viewport.
2. **Simetria Lateral de 60px**:
   - As margens esquerda e direita são mantidas em **60px** no elemento `<main>` do `AppLayout.vue` (`px-[60px]`).
3. **Ritmo Vertical Entre Blocos**:
   - Utilizar `space-y-6` (24px) para páginas de operação cotidiana, fluxos transacionais e listagens com filtros.
   - Utilizar `space-y-8` (32px) para dashboards analíticos ou páginas de visualização ampla.

---

## 6. Topo de Página Padrão (Linha 1 e Linha 2)

O topo de cada página é composto de duas linhas canônicas indissociáveis:

### Linha 1: Box 100% Horizontal do `BaseBreadcrumb`
O breadcrumb deve ser sempre renderizado através do componente oficial [`BaseBreadcrumb.vue`](file:///var/www/dev.redepronta.com/resources/js/components/common/BaseBreadcrumb.vue):

```html
<BaseBreadcrumb
  :items="[
    { label: 'Início', href: '#people' },
    { label: 'Segurança & Governança', href: '#users' },
    { label: 'Usuários, Papéis & Permissões' },
  ]"
>
  <template #right>
    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-900">
      Controle de Acesso (RBAC)
    </span>
  </template>
</BaseBreadcrumb>
```

- **Características do Box:** Largura 100% (`w-full`), altura compacta com `px-4 py-2.5`, borda suave `border border-slate-200/80 dark:border-slate-800`, cantos `rounded-xl`, superfície em card com sombra sutil `shadow-2xs`.
- **Navegabilidade:** O primeiro item é a raiz; os itens intermediários são links clicáveis com efeito hover no Laranja Institucional (`hover:text-[#FC6714]`); o último item representa a tela ativa em negrito sem link.

### Linha 2: Cabeçalho Canônico da Tela (Header)
Localizado imediatamente abaixo do breadcrumb, composto por:
1. **Ícone Temático em Badge/Box**: Caixa quadrada de `w-11 h-11` com cantos `rounded-xl`, fundo translúcido contextual e ícone Lucide centralizado de `w-6 h-6`.
2. **Título H1**: Fonte Kanit, peso 600, tamanho 24px (`text-2xl font-bold font-heading`), acompanhado opcionalmente de badge contador (`rounded-md text-[11px] font-bold`).
3. **Descrição Curta**: Subtítulo explicativo em `text-xs sm:text-sm text-slate-500 dark:text-slate-400`.
4. **Grupo de Botões de Ação Rápida**: Alinhados à direita no desktop, com ação primária em Laranja `#FC6714` e botões secundários em outline/card.

```html
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
  <div class="flex items-center gap-3">
    <div class="w-11 h-11 rounded-xl bg-orange-500/10 text-[#FC6714] flex items-center justify-center shrink-0 border border-orange-200 dark:border-orange-900/50">
      <Users class="w-6 h-6" />
    </div>
    <div>
      <div class="flex items-center gap-2">
        <h1 class="text-xl sm:text-2xl font-bold font-heading text-slate-900 dark:text-slate-100 tracking-tight">
          Gestão de Usuários & Acessos
        </h1>
        <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
          {{ stats.total }} Usuários
        </span>
      </div>
      <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
        Administração de operadores, perfis de papéis e permissões granulares.
      </p>
    </div>
  </div>

  <div class="flex items-center gap-2.5">
    <button type="button" class="btn-primary inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg shadow-sm">
      <Plus class="w-4 h-4" />
      <span>Novo Usuário</span>
    </button>
  </div>
</div>
```

---

## 7. Componentes de Formulário & Controles de Entrada

### 7.1 Campos de Texto & Inputs
- **Cantos:** `rounded-lg` (5px).
- **Altura Padrão:** `h-10` (40px) para campos comuns; `h-9` (36px) para filtros densos em barras de tabela.
- **Tipografia:** Montserrat 14px (`text-sm font-medium`).
- **Estados:**
  - Normal: `border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100`.
  - Foco: Borda `#FC6714`, anel de foco `focus:ring-2 focus:ring-[#FC6714]/25`.
  - Desabilitado: `bg-slate-100 dark:bg-slate-800 text-slate-400 cursor-not-allowed border-slate-200 dark:border-slate-700`.

### 7.2 Selects Canônicos com Seta Ergonômica
Os seletores nativos possuem styling especial no `app.css`:
- **Remoção de Seta Nativa:** `appearance: none !important;`
- **Seta Customizada SVG:** Embutida como data-URI e posicionada a **1rem (16px)** para dentro da margem direita.
- **Padding Direito Seguro:** `padding-right: 2.75rem !important;` para assegurar que rótulos longos de opções não fiquem sobrepostos pelo ícone da seta.

### 7.3 Checkboxes e Radio Buttons
- **Cor de Destaque:** `accent-color: #FC6714 !important;`
- **Cantos da Caixa:** `rounded-sm` (2px) para checkboxes.

---

## 8. Botões & Ações Interativas

A paleta de botões segue hierarquia semântica estrita:

### 8.1 Botão Primário (`.btn-primary`)
- **Cor de Fundo:** `#FC6714`.
- **Hover:** `#E0530A`.
- **Active:** `#C94605` com escala `transform: scale(0.98)`.
- **Texto:** Branco (`#ffffff`), `font-semibold`, Montserrat.
- **Focus Ring:** Halo de acessibilidade em 3px `rgba(252, 103, 20, 0.5)`.
- **Cantos:** `rounded-lg` (5px).

### 8.2 Botão Secundário / Neutro
- **Estilo:** Superfície em card com borda sutil.
- **Classes:** `px-4 py-2 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-semibold shadow-2xs transition`.

### 8.3 Botão de Ação Destrutiva / Perigo
- **Classes:** `px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white text-sm font-semibold shadow-sm transition`.

---

## 9. Modais, Janelas de Diálogo & Drawers

Gerenciados prioritariamente pelo componente [`BaseModal.vue`](file:///var/www/dev.redepronta.com/resources/js/components/common/BaseModal.vue):

### Características de Modais:
- **Cantos do Diálogo:** `rounded-2xl` (8px).
- **Backdrop:** Escuro com desfoque moderado (`backdrop-blur-xs bg-slate-950/60 dark:bg-slate-950/80`).
- **Anatomia Fixa:**
  - Cabeçalho Fixo com Título H2 (`font-heading font-semibold text-lg`), ícone contextual e botão de fechar (X).
  - Corpo rolável com scroll suave (`overflow-y-auto max-h-[calc(100vh-160px)]`).
  - Rodapé fixo com fundo ligeiramente diferenciado (`bg-slate-50/80 dark:bg-slate-950/40`), divisor superior e botões de ação à direita.
- **Escala de Larguras Suportadas:** `sm` (384px), `md` (448px), `lg` (512px), `xl` (576px), `2xl` (672px), `3xl` (768px), `4xl` (896px), `5xl` (1024px), `6xl` (1152px), `7xl` (1280px) e `fullscreen` (98vw).

---

## 10. Tabelas de Dados & Paginação

As tabelas do sistema representam a espinha dorsal da operação de dados:

### Padrão Construtivo de Tabelas:
- **Container Externo:** Card com cantos `rounded-xl` (6px), borda perimetral e sombra sutil `border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-2xs overflow-hidden`.
- **Cabeçalho (`<thead>`):**
  - Fundo sutil: `bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800`.
  - Tipografia das colunas: `text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 py-3 px-4`.
- **Linhas (`<tbody> <tr>`):**
  - Divisores suaves: `divide-y divide-slate-100 dark:divide-slate-800/80`.
  - Efeito hover interativo: `hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors`.
  - Células: `text-sm py-3.5 px-4 text-slate-700 dark:text-slate-200 font-medium`.
- **Paginação:** Integrada ao rodapé através de [`TablePagination.vue`](file:///var/www/dev.redepronta.com/resources/js/components/common/TablePagination.vue).

---

## 11. Badges, Tags & Indicadores de Status

Badges fornecem retorno rápido sobre situações cadastrais, fiscais e operacionais:

- **Cantos Obrigatórios:** `rounded-md` (4px). *Proibido formato oval/pílula excessivo (`rounded-full` é suprimido pelo CSS).*
- **Tipografia:** `text-xs font-semibold` ou `text-[11px] font-bold`.
- **Tokens Semânticos de Badges:**
  - **Sucesso / Ativo:** `bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800`
  - **Alerta / Pendente:** `bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800`
  - **Perigo / Bloqueado / Inativo:** `bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800`
  - **Informativo / RBAC / Especial:** `bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-900`
  - **Institucional / Destaque Primário:** `bg-orange-50 dark:bg-orange-950/60 text-[#FC6714] dark:text-orange-400 border border-orange-200 dark:border-orange-900/60`
  - **Neutro / Contador:** `bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700`

---

## 12. Acessibilidade, Estados de Foco & Dark Mode

### 12.1 Anéis de Foco Acessíveis (`focus-visible`)
Todos os elementos interativos possuem anel de foco de alto contraste em Laranja Institucional:
```css
*:focus-visible {
  outline: none !important;
  box-shadow: 0 0 0 2px rgba(252, 103, 20, 0.45) !important;
  border-color: #FC6714 !important;
}
```

### 12.2 Scrollbars Discretas
As barras de rolagem utilizam acabamento fino de 8px e polegar suave que ganha destaque no hover com o Laranja Institucional:
```css
::-webkit-scrollbar { width: 8px; height: 8px; }
::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.35); border-radius: 4px; }
::-webkit-scrollbar-thumb:hover { background: rgba(252, 103, 20, 0.5); }
```

### 12.3 Transições & Microinterações
- Transições de cores e estados hover devem ser discretas, com duração de **150ms a 200ms** (`transition duration-150 ease-in-out`).
- Evitar transições que causem deslocamentos estruturais involuntários na tela (*layout shifts*).

---

## 🔗 Referências Cruzadas
- [AGENTS.md](file:///var/www/dev.redepronta.com/AGENTS.md): Diretrizes gerais do agente e regras obrigatórias de commits.
- [App.css](file:///var/www/dev.redepronta.com/resources/css/app.css): Definições de tokens Tailwind v4 e regras CSS globais.
- [DesignSystemView.vue](file:///var/www/dev.redepronta.com/resources/js/views/DesignSystemView.vue): Galeria viva e catálogo de componentes do Design System.
