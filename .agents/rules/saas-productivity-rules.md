---
trigger: manual
---

# Regras de Produtividade e Desenvolvimento - Novo SaaS FSM / CRM / ERP

> Este documento estabelece as regras mandatórias de produtividade, integridade arquitetural e conduta da IA durante o desenvolvimento do novo sistema.

---

## 🚀 1. CICLO DE EXECUÇÃO ENXUTO (SPRINTS & PROMPTS CURTOS)

- **Foco Estrito por Sprint**: Cada ciclo de desenvolvimento deve abordar exclusivamente as tarefas do Sprint ativo mapeadas no [plano_sprints_execucao.md](file:///home/mol/.gemini/antigravity-ide/brain/c0134693-1ac6-4110-918a-349e0147d14f/plano_sprints_execucao.md).
- **Sem Prompts Gigantes**: Mantenha prompts curtos, objetivos e orientados aos checklists de tarefas `[ ]`. A IA deve responder com código direto, testável e sem explicações prolixas desnecessárias.
- **Checklist Atualizado**: Ao finalizar um item do sprint, registre a conclusão na lista de verificação antes de passar para o próximo módulo.

---

## 🎯 2. DIRETRIZES KARPATHY DE PROGRAMAÇÃO

1. **Think Before Coding**: Declare premissas de forma explícita antes de criar ou modificar código. Em caso de ambiguidade nos requisitos de negócio, levante a questão em vez de presumir silenciosamente.
2. **Simplicity First**: Escreva a menor quantidade de código necessária para solucionar o problema com excelência técnica. Evite abstrações especulativas para uso único.
3. **Surgical Changes**: Altere estritamente o necessário para cumprir o sprint. Não reformate, não refatore e não exclua partes não relacionadas do projeto.
4. **Goal-Driven Execution**: Cada tarefa deve possuir critério claro de conclusão e validação (migração executada, rota respondendo 200/201, teste passando).

---

## 🛡️ 3. INTEGRIDADE RELACIONAL NO BANCO (MARIADB)

- **Proibição de `CASCADE` em Entidades Mestres**:
  - `ON DELETE RESTRICT` é obrigatório em todas as chaves estrangeiras vinculadas a `people`, `occur_cities`, `stock_warehouses`, `stock_clusters`, `stock_materials`, `cost_centers` e `accounts`.
  - Tentativas de exclusão física no banco de dados que violem integridade relacional devem disparar erro nativo do MariaDB (1451: Cannot delete or update a parent row: a foreign key constraint fails).
- **Inativação Lógica**: Exclusões de cadastros básicos devem operar por inativação lógica (`status = 'inactive'`) ou `SoftDeletes`, nunca remoção destrutiva.

---

## 🔒 4. PADRÕES DE CODIFICAÇÃO (LARAVEL 13.x & VUE 3)

- **Multi-Tenancy Universal**: Toda consulta e persistência a entidades de negócio deve conter a cláusula de `account_id` via `BelongsToTenant` scope.
- **Camada de Serviço Atômica**: Qualquer operação que envolva múltiplos inserts/updates deve residir em `app/Services/{Domain}/` e rodar envelopada por `DB::transaction(...)`.
- **FormRequests Tipados**: Nunca validar requisições dentro do Controller; crie sempre um `FormRequest` dedicado com mensagens em Português do Brasil.
- **Frontend com Theming Dinâmico**: Nunca utilize classes estáticas de cores (ex: `bg-blue-600`); utilize os tokens semânticos CSS do tema (`bg-primary`, `text-primary`, `bg-surface`).

---

## 📝 5. PADRÃO OBRIGATÓRIO DE COMMITS NO GIT

- **Idioma Obrigatório**: Em **TODO commit**, a mensagem DEVE OBRIGATORIAMENTE ser escrita em **Português do Brasil (pt-BR)**.
- **Padrão Semântico (Conventional Commits)**:
  - `feat(modulo): mensagem descritiva em pt-br`
  - `fix(modulo): mensagem descritiva em pt-br`
  - `refactor(modulo): mensagem descritiva em pt-br`
  - `test(modulo): mensagem descritiva em pt-br`
  - `chore(modulo): mensagem descritiva em pt-br`
- **Compromisso de Limpeza**: Nunca finalize uma entrega deixando alterações válidas pendentes no `git status`.
