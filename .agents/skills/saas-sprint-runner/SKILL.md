---
name: saas-sprint-runner
description: Guia de execução ágil e produtiva de Sprints para o novo SaaS (Debian 13, MariaDB, Laravel 13.x, Vue 3). Use ao implementar qualquer fase do plano_sprints_execucao.md.
---

# SAAS SPRINT RUNNER - GUIA DE EXECUÇÃO ÁGIL

Este guia orienta o assistente e o desenvolvedor a executar as tarefas de cada Sprint do novo produto com máxima assertividade, sem alucinações e com garantia de entrega testável.

---

## 🎯 PROTOCOLO DE EXECUÇÃO DO SPRINT

Ao receber a solicitação de um Sprint (ex: "Execute o Sprint 1"):

### 1. Leitura & Verificação do Escopo
- Consulte o checklist de tarefas correspondente no arquivo `plano_sprints_execucao.md`.
- Isole estritamente as tarefas daquele Sprint. **Não adiante código ou tabelas de sprints futuros**.

### 2. Ordem de Construção Recomendada (Camadas de Baixo para Cima)
1. **Banco de Dados (Migrations):**
   - Criar tabelas com charset `utf8mb4_unicode_ci`.
   - Garantir obrigatoriamente `ON DELETE RESTRICT` em todas as Foreign Keys de negócio.
   - Criar índices apropriados e tipos espaciais (`POINT` com `SPATIAL INDEX`) para GPS.
2. **Models & Scopes:**
   - Implementar Models Eloquent com `Global Tenant Scope` automático.
   - Definir relacionamentos explícitos (`belongsTo`, `hasMany`).
3. **Validação & DTOs (FormRequests):**
   - Criar `FormRequest` tipado para cada mutação (Store/Update).
   - Mensagens de erro em Português do Brasil.
4. **Camada de Serviço (Domain Services):**
   - Encapsular operações que afetam múltiplos registros em `DB::transaction()`.
   - Métodos claros, tipados e com injeção de dependência.
5. **Controllers & Rotas:**
   - Controllers magros de ação única ou RESTful (`/api/v1/...`).
   - Retorno padronizado via `JsonResource` do Laravel.
6. **Frontend (Vue 3 + Tailwind):**
   - Criar componentes com `<script setup>` e Tailwind CSS utilizando variáveis semânticas de tema.
   - Tratar estados de loading, erro e listagem vazia.
7. **Testes Automatizados (Feature Tests):**
   - Escrever ao menos 1 teste de Feature validando a rota feliz e 1 teste validando restrição de dependência.

---

## 🚫 O QUE NUNCA FAZER
- Nunca usar `ON DELETE CASCADE` em cadastros essenciais (Pessoas, Cidades, Materiais, Depósitos).
- Nunca criar queries cruas sem validação de tenant (`account_id`).
- Nunca usar cores fixas hardcoded no Vue (ex: `bg-blue-600`); use sempre tokens semânticos (`bg-primary`).
- Nunca misturar lógica de múltiplos sprints em um único commit.
