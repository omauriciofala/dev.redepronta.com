# ERP Rede Pronta

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="ERP Rede Pronta Logo">
</p>

<p align="center">
  <strong>Plataforma Integrada de Field Service Management (FSM), CRM Omnichannel, Suprimentos e ERP Financeiro para ISPs e Operadoras de Telecomunicações</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/MariaDB-10.11%2B-003545?style=for-the-badge&logo=mariadb&logoColor=white" alt="MariaDB">
  <img src="https://img.shields.io/badge/Debian-13_(Trixie)-A81D33?style=for-the-badge&logo=debian&logoColor=white" alt="Debian 13">
  <img src="https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white" alt="Vue 3">
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Multi--Tenant-Ready-blueviolet?style=for-the-badge" alt="Multi Tenant">
</p>

---

## 📌 Visão Geral

O **ERP Rede Pronta** é uma solução de alto desempenho projetada especificamente para as demandas complexas do setor de telecomunicações e serviços de campo. Construído sobre uma arquitetura **API-First / Headless**, o sistema unifica em uma única plataforma modular:

- Gestão de Atendimento Omnichannel e Webmail Integrado;
- Despacho e Mobilização de Equipes de Campo (FSM);
- Controle Avançado de Almoxarifado com Saldo Regional Aglutinado;
- Gestão Financeira Completa com Conciliação Bancária Inteligente;
- Portais Dedicados e Segmentados para todas as Personas da Operação.

---

## 💎 Os Dois Axiomas Centrais

A arquitetura do **ERP Rede Pronta** foi concebida sob duas premissas estruturais inegociáveis:

### 1. O Sistema Gira em Torno de PESSOAS (`people`)
No ERP Rede Pronta, pessoas são o núcleo relacional universal. Em vez de tabelas isoladas e redundantes, uma única entidade mestre assume papéis polimórficos na operação, viabilizando **4 Portais / Intranets Segmentados**:
- **Colaboradores**: Painel do funcionário com espelho de presença, horas trabalhadas, banco de horas simplificado e solicitações internas.
- **Fornecedores**: Portal para envio de notas fiscais, conferência de pedidos de compra, medições de LPU técnica e acompanhamento de faturamento.
- **Clientes**: Autoatendimento com emissão de 2ª via de boletos, código PIX dinâmico, visualização de contratos e abertura de chamados.
- **Solicitantes**: Canal direto para pessoas físicas ou prepostos credenciados que registram pedidos de serviço e acompanham ordens de trabalho.

```
                     ┌───────────────────────┐
                     │    PESSOA (people)    │
                     │ Identidade & Contatos │
                     └───────────┬───────────┘
         ┌───────────────────────┼───────────────────────┐
         ▼                       ▼                       ▼
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│  COLABORADOR    │     │   FORNECEDOR    │     │     CLIENTE     │
│  LPU / Presença │     │ Compras / Notas │     │ Contratos / SAC │
└─────────────────┘     └─────────────────┘     └─────────────────┘
```

### 2. Suprimentos em Torno de Depósitos e Posições Regionais (`stock_clusters`)
O controle de estoque físico e virtual reflete com precisão a geografia das operadoras:
- **Depósitos Físicos**: Almoxarifado Central, Bases Operacionais Avançadas e Carros/Viaturas das equipes técnicas.
- **Posição Regional (Cluster de Depósitos)**: Agrupamento geográfico de depósitos que calcula em tempo real o **Saldo Virtual Aglutinado** da região, permitindo alocação estratégica de materiais sem necessidade de transferências físicas imediatas.
- **Rastreabilidade Serial Ponta a Ponta**: Acompanhamento unitário de ONUs, roteadores, bobinas de fibra e rádios por Número de Série, MAC Address e histórico de movimentações.

---

## ⚡ Funil Operacional em 3 Estágios

Toda demanda no ERP Rede Pronta percorre um ciclo de vida estruturado, evitando ruídos de comunicação e garantindo rastreabilidade:

```
[ APIs / IA / E-mails / Chat ]
              │
              ▼
   ┌─────────────────────┐
   │ 1. TAREFAS (tasks)  │ ──► Ponto de entrada universal, triagem e roteamento inteligente
   └──────────┬──────────┘
              ▼
   ┌─────────────────────┐
   │2. CHAMADOS (tickets)│ ──► Demandas formais com catálogo de serviços e controle de SLA
   └──────────┬──────────┘
              ▼
   ┌─────────────────────┐
   │  3. ACIONAMENTOS    │ ──► Ordens de serviço de campo, mobilização de veículos e equipes
   │    (dispatches)     │
   └─────────────────────┘
```

1. **Tarefas (`tasks`)**: Ponto de entrada universal. Qualquer requisição oriunda de integrações (APIs), agentes de Inteligência Artificial, e-mails recebidos ou mensagens de chat é convertida em Tarefa para classificação.
2. **Chamados (`tickets`)**: Pedidos formais de clientes, provedores ou solicitantes com prazos de atendimento (SLA), catálogo de serviços e histórico auditável.
3. **Acionamentos (`dispatches`)**: Atividade de campo mobilizada. Controla:
   - **Deslocamento**: Horário de saída, odômetro inicial, horário de chegada ao destino, odômetro final e cálculo do TMA de viagem vs TMA de atendimento;
   - **Presença e Jornada**: Registro de presença da equipe em campo;
   - **Banco de Horas Simplificado**: Cômputo automático de horas trabalhadas em horas normais e excedentes por acionamento.

---

## 📦 Módulos do Sistema

| Módulo | Descrição & Funcionalidades Principais |
| :--- | :--- |
| **CRM Omnichannel** | Chat unificado ao vivo (WhatsApp multi-número, Telegram, Webchat e Chat Interno) e Cliente de E-mail (Webmail) integrado com conversão de mensagens em tarefas/chamados com 1 clique. |
| **FSM & Despacho** | Mobilização de equipes, roteirização geográfica, controle de deslocamento (KM/Horários), fotos de evidência de campo e assinatura digital de OS. |
| **WMS & Suprimentos** | Gestão de Depósitos e Clusters Regionais, saldo virtual aglutinado, rastreabilidade por número de série/MAC, cautelas de ferramental e inventário dinâmico. |
| **ERP Financeiro** | Contas a Pagar (LPU de técnicos, aluguéis de POPs e compras), Contas a Receber, Fluxo de Caixa Realizado vs Previsto, DRE Gerencial e **Conciliação Bancária Automática (OFX/CNAB 240 e 400)**. |
| **Contratos & Cobrança** | Gestão de assinaturas telecom, réguas de cobrança automatizadas via WhatsApp/E-mail, geração de boletos registrados e PIX dinâmico. |
| **Portais & Intranet** | Camada de comunicação e autoatendimento segmentada para Colaboradores, Fornecedores, Clientes e Solicitantes. |
| **NOC & Infraestrutura** | Monitoramento de POPs, torres, racks, caixas de emenda (CTOs) e relatórios de RFO (Relatório de Falha Operacional). |
| **Theming Engine** | Alternância dinâmica de modo Claro/Escuro/Automático e controle de densidade de tela (*Compacto* para operadores de NOC e Despacho vs *Confortável* para telas administrativas). |

---

## 🛡️ Integridade Relacional e Segurança de Dados

O banco de dados do **ERP Rede Pronta** adota uma política rigorosa de integridade relacional nativa no MariaDB:

- **Proibição de `CASCADE` em Entidades Mestres**: Cláusulas `ON DELETE RESTRICT` são mandatórias em chaves estrangeiras vinculadas a pessoas, cidades IBGE, depósitos, materiais e centros de custo.
- **Inativação Lógica e Soft Deletes**: Dados com dependências operacionais históricas não são excluídos fisicamente, preservando a integridade contábil e jurídica da operadora.
- **Isolamento Multi-Tenant**: Toda consulta e persistência aplica automaticamente o escopo de `account_id` através do trait `BelongsToTenant`.

---

## 🛠️ Stack Tecnológico

| Camada | Tecnologia | Detalhes |
| :--- | :--- | :--- |
| **Sistema Operacional** | Debian 13 (Trixie) | Servidor robusto, seguro e padronizado |
| **Linguagem Backend** | PHP 8.4 | Tipagem estrita, performance otimizada e JIT |
| **Framework Web** | Laravel 13.x | Arquitetura API-First com Camada de Serviços e FormRequests |
| **Banco de Dados** | MariaDB 10.11+ / 11.x | Motor InnoDB com `utf8mb4_unicode_ci` e suporte a índices geoespaciais |
| **Frontend** | Vue 3 + Tailwind CSS | Composition API (`<script setup>`), TypeScript e Design Tokens via CSS Variables |
| **Ícones** | Lucide Icons | Pacote moderno e consistente de vetores SVG |
| **Filas & Cache** | Redis / Database Queue | Processamento assíncrono de e-mails, webhooks e mensagens omnichannel |

---

## 🚀 Guia Rápido de Instalação

### Pré-requisitos
- Debian 13 (Trixie) ou distribuição compatível
- PHP 8.4 com extensões: `php-fpm`, `php-mysql`, `php-mbstring`, `php-xml`, `php-curl`, `php-zip`, `php-bcmath`
- MariaDB Server 10.11+
- Composer 2.x
- Node.js 20+ e NPM

### 1. Clonar o Repositório
```bash
git clone git@github.com:omauriciofala/dev.redepronta.com.git /var/www/dev.redepronta.com
cd /var/www/dev.redepronta.com
```

### 2. Configurar o Ambiente
```bash
cp .env.example .env
php artisan key:generate
```

Edite o arquivo `.env` para apontar para a sua instância do MariaDB:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rp_saas_db
DB_USERNAME=rp_user
DB_PASSWORD=sua_senha_segura
```

### 3. Instalar Dependências e Executar Migrations
```bash
# Dependências PHP
composer install --no-dev --optimize-autoloader

# Permissões do Servidor Web
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Execução das Migrations
php artisan migrate --force

# Dependências Frontend e Build
npm install
npm run build
```

---

## �� Documentação Técnica e Roadmap

Para aprofundamento na arquitetura, modelos de dados e execução ágil, consulte a pasta de documentação:

- [**System Blueprint Completo**](docs/novo_produto_saas_blueprint.md): Especificação arquitetural com diagramas DDL completos, models Eloquent, endpoints RESTful e estrutura de temas.
- [**Plano de Sprints e Checklists de Tarefas**](docs/plano_sprints_execucao.md): Divisão do desenvolvimento em 7 Sprints enxutos com critérios de aceitação e prompts objetivos para IA.

---

<p align="center">
  Desenvolvido com excelência técnica pela equipe <strong>Rede Pronta</strong>.
</p>
