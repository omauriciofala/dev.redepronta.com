# Diretrizes e Memória Técnica - ERP Rede Pronta

## 📌 Identidade do Projeto
- **Produto:** ERP Rede Pronta
- **Domínio:** ERP Integrado, FSM (Field Service Management), Estoque/WMS com serial e Faturamento/Contratos.
- **Arquitetura:** Laravel 13 (PHP 8.4) + Vue 3 + Tailwind CSS v4 + MariaDB.

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
