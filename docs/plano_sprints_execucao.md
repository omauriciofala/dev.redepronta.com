# PLANO DE SPRINTS & CHECKLIST DE TAREFAS
## PRODUTO: NEXT-GEN FSM, CRM OMNICHANNEL & ERP TELECOM SAAS

> **Estratégia Ágil:** Prompts curtos e modulares produzem código de qualidade infinitamente superior. Execute um Sprint por vez, valide os testes e a interface, e então avance para o próximo.

---

## 📦 CATÁLOGO DE MÓDULOS DO PRODUTO

1. **MÓDULO 0: Cadastros Básicos Canônicos, Core SaaS & Pessoas (Person-Centric)**
   - Estados, Cidades (IBGE), Unidades, Departamentos, Categorias/Motivos de chamados, Pessoas unificadas (`people`), Tenants (`accounts`), Unidades (`organizations`), RBAC e Theming Engine (Dark/Light/White-label).
2. **MÓDULO 1: Suprimentos, Depósitos Físicos & Clusters Regionais (WMS)**
   - Depósitos físicos (Centrais, Bases, Veículos), Clusters de Depósito (Posição Regional), Saldo Virtual Aglutinado e Rastreabilidade Serial (ONUs/Roteadores).
3. **MÓDULO 2: O Funil Operacional (Tarefas -> Chamados -> Acionamentos)**
   - **Tarefas:** Ponto de entrada universal (APIs, IA, rotinas).
   - **Chamados:** Demandas formais de clientes/solicitantes com controle de SLA.
   - **Acionamentos:** Ordens de serviço de campo com mobilização física.
4. **MÓDULO 3: Campo, Deslocamento & Banco de Horas Simplificado**
   - Telemetria de deslocamento (KM odômetro e TMA de viagem), Fechamento RFO com fotos/atenuação dBm/speedtest, Ponto diário e Banco de Horas Simplificado.
5. **MÓDULO 4: ERP Financeiro Completo & Conciliação Automática**
   - Contas a Pagar, Contas a Receber (Boletos/PIX dinâmico), Fluxo de Caixa (Realizado vs Previsto), DRE Gerencial e Conciliação Bancária inteligente (OFX/CNAB).
6. **MÓDULO 5: CRM Omnichannel (Chat Próprio & Webmail Integrado)**
   - Chat unificado (WhatsApp, Telegram, Webchat, Chat Interno) com WebSockets e Webmail IMAP/SMTP com conversão de e-mail em Tarefa/Chamado.
7. **MÓDULO 6: Intranet & Portais Segmentados das 4 Personas**
   - Portais específicos para: Colaboradores, Fornecedores, Clientes e Solicitantes.
8. **MÓDULO 7: Infraestrutura (POPs & Rotas), LPU & Obras (RDO)**
   - Estações POP com contratos de aluguel/energia, Rotas ópticas, LPU com medição de terceiros e RDO para expansão de rede.

---

## 🔒 REGRA DE OURO ARQUITETURAL: INTEGRIDADE RELACIONAL ESTRITA
Todas as Foreign Keys de entidades de negócio utilizam **`ON DELETE RESTRICT`**. É expressamente proibido permitir exclusão física de cadastros básicos que possuam vínculos com chamados, acionamentos, títulos financeiros, depósitos ou estoque. O sistema deve barrar no banco e retornar erro HTTP 422 amigável orientando a inativação (`status = 'inactive'` ou `SoftDeletes`).

---

## 🗓️ CRONOGRAMA DE SPRINTS & PROMPTS CURTOS

---

### 🔹 SPRINT 1: Cadastros Básicos Canônicos, Pessoas & Theming Engine
**Objetivo:** Banco MariaDB estruturado com integridade relacional estrita (`RESTRICT`), base canônica de cidades, cadastro central de Pessoas e Theming Engine.

#### Checklist de Tarefas:
- [ ] Configurar Laravel 13.x no Debian 13 com conexão MariaDB e Sanctum.
- [ ] Criar migrations com `ON DELETE RESTRICT` para os cadastros básicos: `states`, `cities` (IBGE), `units`, `departments`, `ticket_categories`, `ticket_reasons`.
- [ ] Criar migrations e models `accounts` e `organizations` com Global Tenant Scope.
- [ ] Criar migration e model canônica `people` vinculada obrigatoriamente a `cities.id` (flags: colaborador, fornecedor, cliente, solicitante) com `SoftDeletes`.
- [ ] Implementar `PersonService` (com validação estrita que bloqueia exclusão se houver dependências).
- [ ] Criar endpoints RESTful `/api/v1/people` e `/api/v1/cities/search`.
- [ ] Implementar `useTheme.ts` no Vue 3 + Tailwind (Dark/Light Mode e cores personalizadas do tenant).

> #### 💬 Prompt Curto para a IA (Sprint 1):
> ```text
> Atue como Arquiteto Laravel 13.x e Vue 3. Inicie o Sprint 1 do SaaS no Debian 13 + MariaDB:
> 1. Crie as migrations com INTEGRIDADE ESTRITA (ON DELETE RESTRICT obrigatório) para os cadastros básicos canônicos: `states`, `cities` (IBGE), `units`, `departments`, `ticket_categories` e `ticket_reasons`.
> 2. Crie as migrations de `accounts`, `organizations` e a tabela canônica `people` (com chave estrangeira para cities.id, flags de personas, SoftDeletes e Global Tenant Scope).
> 3. Implemente o PersonService barrando exclusões de pessoas com vínculos, os endpoints RESTful /api/v1/people com FormRequests tipados e o composable useTheme.ts no Vue 3 + Tailwind CSS (Dark/Light mode).
> ```

---

### 🔹 SPRINT 2: Suprimentos, Depósitos Físicos & Clusters Regionais (WMS)
**Objetivo:** Mapear depósitos físicos (centrais, bases e veículos), agrupá-los em Clusters Regionais e calcular o Saldo Virtual Aglutinado com controle de seriais e restrição de integridade.

#### Checklist de Tarefas:
- [ ] Migrations `depot_clusters`, `depots` (Central, Base, Veículo) e `materials` com `ON DELETE RESTRICT`.
- [ ] Tabelas `stock_balances` e `stock_serials` (rastreabilidade individual de ONUs).
- [ ] `ClusterStockService` que calcula em tempo real o Saldo Virtual Aglutinado da região.
- [ ] Endpoints e rotinas de transferência de materiais (Central -> Veículo).
- [ ] Componente Vue 3 para visualização da Posição Regional.

> #### 💬 Prompt Curto para a IA (Sprint 2):
> ```text
> Execute o Sprint 2 do SaaS: implemente a camada de suprimentos no Laravel 13.x + MariaDB com integridade ON DELETE RESTRICT.
> 1. Crie as tabelas `depot_clusters`, `depots` (CENTRAL, REGIONAL_BASE, VEHICLE), `materials`, `stock_balances` e `stock_serials`.
> 2. Implemente o `ClusterStockService` que calcula em tempo real o Saldo Virtual Aglutinado da região (somando a base regional e todos os veículos dos técnicos daquele cluster).
> 3. Crie os endpoints de transferência de materiais e o componente Vue 3 RegionalClusterStockView.vue.
> ```

---

### 🔹 SPRINT 3: O Funil Operacional (Tarefas -> Chamados -> Acionamentos)
**Objetivo:** Construir a esteira operacional de 3 etapas com integridade relacional estrita (bloqueando exclusão de cadastros com chamados/acionamentos vinculados).

#### Checklist de Tarefas:
- [ ] Migrations `tasks`, `tickets` e `dispatches` com `ON DELETE RESTRICT` nas chaves de cliente, solicitante, departamento e cidade.
- [ ] `TaskIngestionService` (recebe tarefas de APIs, IA, rotinas ou manual).
- [ ] Endpoint de promoção de Tarefa para Chamado com SLA.
- [ ] `DispatchCoordinationService` para criar Acionamento de campo alocando técnico e veículo.
- [ ] Telas no Vue 3: Inbox de Tarefas, Lista de Chamados e Painel de Despacho.

> #### 💬 Prompt Curto para a IA (Sprint 3):
> ```text
> Execute o Sprint 3 do SaaS: implemente o Funil Operacional em 3 Estágios no Laravel 13.x + MariaDB com integridade referencial estrita.
> 1. Crie as migrations `tasks` (porta de entrada universal), `tickets` (chamados formais com SLA) e `dispatches` (acionamentos de campo com técnico e veículo) garantindo ON DELETE RESTRICT nas Foreign Keys de pessoas e cadastros.
> 2. Implemente o serviço de ingestão de tarefas, promoção para chamado e despacho de equipe.
> 3. Crie as rotas /api/v1 correspondentes e os componentes no Vue 3: Inbox de Tarefas e Painel de Despacho.
> ```

---

### 🔹 SPRINT 4: Campo, Deslocamento, Banco de Horas & Fechamento RFO
**Meta:** Controle da execução física: deslocamento, banco de horas simplificado e laudo RFO em PDF.

#### Checklist de Tarefas:
- [ ] Migrations `worker_clockins`, `worker_time_bank`, `dispatch_rfos` e `dispatch_rfo_evidences`.
- [ ] Endpoints de deslocamento (`/depart` e `/arrive`) com cálculo de TMA de viagem e odômetro KM.
- [ ] `TimeBankService` (apuração de horas e extrato transparente de créditos/débitos).
- [ ] Submissão de RFO com fotos, atenuação óptica (dBm), speedtest, baixa de seriais e assinatura digital.
- [ ] Job assíncrono para geração do Laudo RFO em PDF diagramado.

> #### 💬 Prompt Curto para a IA (Sprint 4):
> ```text
> Execute o Sprint 4 do SaaS: implemente o módulo de Campo e Acionamento no Laravel 13.x.
> 1. Crie o controle de deslocamento (saída, chegada no cliente, fotos de KM odômetro e TMA de viagem).
> 2. Crie o sistema de Ponto e Banco de Horas Simplificado com extrato transparente de créditos/débitos.
> 3. Crie o fechamento de RFO com upload de evidências com watermark, medição óptica em dBm, baixa de seriais do veículo, assinatura na tela e geração de laudo PDF em background.
> ```

---

### 🔹 SPRINT 5: ERP Financeiro Completo & Conciliação Bancária
**Meta:** Contas a pagar, contas a receber, fluxo de caixa e conciliação OFX/CNAB com integridade estrita (proibindo excluir fornecedores/clientes com títulos em aberto).

#### Checklist de Tarefas:
- [ ] Migrations `bank_accounts`, `financial_payables`, `financial_receivables`, `bank_statements` e `bank_reconciliations` (todas com `ON DELETE RESTRICT` para categorias e pessoas).
- [ ] Serviços de Contas a Pagar e Contas a Receber integrados.
- [ ] `OfxParserService` e `SmartReconciliationService` (algoritmo de correspondência automática de extrato).
- [ ] Endpoints de Fluxo de Caixa (Realizado vs Previsto) e DRE em tempo real.
- [ ] Tela Vue 3 de Conciliação em duas colunas (Extrato vs Lançamentos do ERP).

> #### 💬 Prompt Curto para a IA (Sprint 5):
> ```text
> Execute o Sprint 5 do SaaS: implemente o ERP Financeiro Completo no Laravel 13.x + MariaDB com ON DELETE RESTRICT nas Foreign Keys de pessoas e categorias.
> 1. Crie as tabelas e serviços de Contas a Pagar, Contas a Receber, Fluxo de Caixa Diário/Projetado e DRE.
> 2. Implemente o parser de extratos OFX e CNAB 240/400 com algoritmo de conciliação bancária automática inteligente (Smart Matching).
> 3. Desenvolva o componente Vue 3 ReconciliationBoard.vue exibindo o extrato bancário de um lado e as sugestões de conciliação do outro.
> ```

---

### 🔹 SPRINT 6: CRM Omnichannel (Chat Próprio & Webmail Integrado)
**Meta:** Central multicanal ao vivo e cliente de e-mail integrado que converte mensagens em tarefas.

#### Checklist de Tarefas:
- [ ] Migrations `chat_channels`, `chat_conversations`, `chat_messages`, `email_accounts` e `email_messages`.
- [ ] WebSockets com Redis/Laravel Reverb para chat em tempo real.
- [ ] Conector de WhatsApp e Telegram com bot de triagem que gera Tarefas.
- [ ] Sincronização IMAP e envio SMTP de e-mails em threads com botão "Criar Tarefa a partir do E-mail com 1 Clique".
- [ ] Telas Vue 3: Chat Omnichannel estilo WhatsApp Web e Webmail corporativo.

> #### 💬 Prompt Curto para a IA (Sprint 6):
> ```text
> Execute o Sprint 6 do SaaS: implemente o CRM Omnichannel e Webmail no Laravel 13.x.
> 1. Crie o Chat Omnichannel unificado ao vivo (WhatsApp, Telegram, Webchat) com WebSockets em tempo real.
> 2. Crie o cliente de e-mail corporativo IMAP/SMTP com agrupamento por threads e botão 'Criar Tarefa a partir do E-mail com 1 Clique'.
> 3. Crie os componentes Vue 3 para o Inbox do Chat e a caixa de entrada do Webmail.
> ```

---

### 🔹 SPRINT 7: Intranet das 4 Personas, Infraestrutura (POPs) & Faturamento SaaS
**Meta:** Portais dedicados para cada público, gestão de POPs/rotas e faturamento por técnico ativo.

#### Checklist de Tarefas:
- [ ] Migration `intranet_posts` com filtro por persona (`WORKER`, `SUPPLIER`, `CUSTOMER`, `REQUESTER`).
- [ ] Portais no Vue 3: Intranet da Equipe, Portal do Fornecedor, Portal do Assinante e Portal do Solicitante.
- [ ] Módulo de POPs (estações georreferenciadas, contratos de aluguel e rotas ópticas).
- [ ] Tabela de LPU e medição automática de produção de técnicos PJ.
- [ ] Motor de cobrança SaaS (mensalidade base + assentos por técnico ativo).

> #### 💬 Prompt Curto para a IA (Sprint 7):
> ```text
> Execute o Sprint 7 do SaaS: finalize os módulos complementares no Laravel 13.x.
> 1. Crie a Intranet com portais segmentados para as 4 Personas (Colaboradores, Fornecedores, Clientes e Solicitantes).
> 2. Implemente o módulo de POPs com contratos de aluguel e rotas ópticas, e a tabela de LPU com geração automática de títulos a pagar para técnicos PJ.
> 3. Implemente o motor de faturamento SaaS com cobrança por técnico ativo no mês.
> ```
