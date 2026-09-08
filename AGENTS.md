# Instruções e Memória do Agente - Novo SaaS RedePronta (FSM / CRM / ERP)

> Documento de diretrizes arquiteturais, contexto técnico permanente e governança do novo produto SaaS.

---

## 📌 Identidade e Propósito do Projeto

- **Nome do Produto:** RedePronta SaaS (Headless / API-First)
- **Domínio:** Field Service Management (FSM), CRM Omnichannel (Chat + E-mail), WMS com Clusters Regionais e ERP Financeiro Completo.
- **Público-Alvo:** Provedores de Internet (ISPs), Operadoras de Telecomunicações e Prestadores de Serviços de Campo.
- **Ambiente Homologado:** Debian 13 (Trixie), MariaDB 10.11+ / 11.x, PHP 8.4, Laravel 13.x e Vue 3 (<script setup>) + Tailwind CSS.

---

## 🏛️ Axiomas Arquiteturais & Diretrizes Inegociáveis

1. **Axioma 1 - Pessoas no Centro (`people`)**:
   - O sistema todo gira em torno de **PESSOAS**, que assumem papéis polimórficos de Colaboradores, Fornecedores, Clientes e Solicitantes.
   - Quatro Portais / Intranets segmentados com autenticação unificada.

2. **Axioma 2 - Suprimentos e Clusters de Depósito (`stock_clusters`)**:
   - Estoque físico organizado em Depósitos (centrais, bases, viaturas de técnicos) agrupados em **Posições Regionais (Clusters)** com **Saldo Virtual Aglutinado**.

3. **Funil Operacional em 3 Estágios**:
   - **Tarefas (`tasks`)**: Ponto de entrada universal (APIs, IA, Webhooks, E-mails, Chat).
   - **Chamados (`tickets`)**: Demandas externas formais de solicitantes/clientes com SLA.
   - **Acionamentos (`dispatches`)**: Ordens de serviço de campo com mobilização física de equipes, veículos (KM odômetro, horários de deslocamento) e Banco de Horas Simplificado.

4. **Integridade Relacional no MariaDB**:
   - **`ON DELETE RESTRICT`** obrigatório em todas as chaves estrangeiras de entidades mestres (`people`, `occur_cities`, `stock_warehouses`, etc.).
   - Proibido uso de `CASCADE` em cadastros essenciais. Exclusões operam por inativação lógica (`status = 'inactive'`) ou SoftDeletes.

5. **Documentação e Roadmap**:
   - Blueprint Técnico Completo: [docs/novo_produto_saas_blueprint.md](file:///var/www/dev.redepronta.com/docs/novo_produto_saas_blueprint.md)
   - Plano de Sprints e Checklists: [docs/plano_sprints_execucao.md](file:///var/www/dev.redepronta.com/docs/plano_sprints_execucao.md)

6. **Commits e Idioma**:
   - Comunicação e mensagens de commit no Git **SEMPRE em Português do Brasil (pt-BR)**.
   - Padrão Conventional Commits (`feat:`, `fix:`, `refactor:`).

---

## ⚡ Comandos e Operação

```bash
# Limpeza de Cache
php artisan optimize:clear

# Execução de Migrations
php artisan migrate

# Testes Automatizados
php artisan test
```
