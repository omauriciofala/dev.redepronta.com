# Diretrizes e Memória Técnica - ERP Rede Pronta

## 📌 Identidade do Projeto
- **Produto:** ERP Rede Pronta
- **Domínio:** ERP Integrado, FSM (Field Service Management), Estoque/WMS com serial e Faturamento/Contratos.
- **Arquitetura:** Laravel 13 (PHP 8.4) + Vue 3 + Tailwind CSS v4 + MariaDB.
- **Design System & BrandBook:** [DESIGN.md](file:///var/www/dev.redepronta.com/DESIGN.md) *(Consulte para tokens visuais, componentes, paleta, tipografia e diretrizes de UI/UX)*.

---

## 🛑 REGRA OBRIGATÓRIA DE COMMITS NO GIT (INEGOCIÁVEL)

1. **Idioma Estritamente em Português do Brasil (pt-BR)**:
   - Em **TODO e QUALQUER commit**, a mensagem de commit **DEVE OBRIGATORIAMENTE ser redigida em Português do Brasil (pt-BR)**.
   - É terminantemente proibido utilizar mensagens em inglês (ex: *fix bug*, *update code*, *add feature*).

2. **Formato Padrão (Conventional Commits)**:
   - Toda mensagem deve seguir a estrutura semântica:
     `tipo(escopo): descrição da mudança em português`
   - Exemplos válidos:
     - `feat(people): adicionar busca por CPF na listagem de pessoas`
     - `fix(modal): corrigir espaçamento perimetral do modal fullscreen`
     - `refactor(auth): separar serviço de autenticação multi-tenant`
     - `docs(changelog): registrar novas regras de commit`
     - `chore(deps): atualizar dependências do vite`

3. **Change-log Vivo por Commit**:
   - Cada commit realizado no repositório reflete diretamente na página **Change-log** (`/#changelog`) do ERP em tempo real.
   - Escreva mensagens autoexplicativas, claras e profissionais, pois elas são consumidas diretamente pelos operadores e gestores do sistema.

---

## 📐 REGRA OBRIGATÓRIA DE LAYOUT DE PÁGINAS (PAGE SHELL & SPACING)

1. **Margem Superior e Inferior Obrigatória (`py-8`)**:
   - Toda e qualquer view Vue renderizada no `<main>` do `AppLayout.vue` **DEVE OBRIGATORIAMENTE** conter no elemento raiz:
     `<div class="py-8 w-full space-y-6">` (ou `space-y-8`).
   - **Proibição Estrita:** O cabeçalho de uma tela NUNCA deve encostar no teto da tela (sem `py-8`). O respiro superior de `32px` (`py-8` / `pt-8`) é mandatório para todas as páginas criadas no sistema.
2. **Simetria Lateral de 60px**:
   - A margem horizontal é garantida exclusivamente pelo layout mestre (`px-[60px]` no `AppLayout.vue`).
3. **Topo de Página Canônico Obrigatório (`BasePageHeader`)**:
   - Toda e qualquer tela criada **DEVE OBRIGATORIAMENTE** utilizar o componente canônico oficial:
     `<BasePageHeader :breadcrumb-items="[...]" title="..." description="..." :icon="..." />`
     ([BasePageHeader.vue](file:///var/www/dev.redepronta.com/resources/js/components/common/BasePageHeader.vue)).
   - O `BasePageHeader` encapsula harmoniosamente:
     - **Linha 1:** O `BaseBreadcrumb` em Box 100% horizontal na área de conteúdo, com borda sutil e cantos `rounded-xl`.
     - **Linha 2:** O cabeçalho com ícone contextual, título H1 em Kanit 600, badge contador e slot `#actions` para botões à direita.
     - **Linha 3 (Opcional):** Slot `#tabs` para módulos que possuem abas de navegação.
   - **Proibição Estrita:** É terminantemente proibido montar topos de tela com divs manuais dispersas ou recriar cabeçalhos fora do componente canônico.
4. **Cantos Menos Arredondados no Layout Inteiro (Escala Enterprise Sóbria)**:
   - Todo o sistema adota cantos mais retos, discretos e sóbrios, evitando arredondamentos excessivos.
   - Escala oficial configurada no `@theme` (`resources/css/app.css`):
     - `rounded-lg` (5px): Botões, inputs, selects, textareas e dropdowns.
     - `rounded-xl` (6px): Cards, containers de dados, tabelas e caixas de breadcrumb.
     - `rounded-2xl` (8px): Modais centrais e drawer lateral.
     - `rounded-md` (4px): Badges, tags e contadores numéricos.
     - `rounded-sm` (2px): Checkboxes e pequenos detalhes.

---

## 🎨 GUIA VISUAL & DESIGN SYSTEM
- Para especificações completas de tokens cromáticos, tipografia (Kanit e Montserrat), componentes de formulário, modais, tabelas e anatomia de telas, consulte o documento normativo:
  👉 **[DESIGN.md](file:///var/www/dev.redepronta.com/DESIGN.md)**





