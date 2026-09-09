# ESPECIFICAÇÃO TÉCNICA E FUNCIONAL DE PRODUTO (SYSTEM BLUEPRINT)
## NEXT-GEN FIELD SERVICE MANAGEMENT (FSM), CRM OMNICHANNEL & ERP TELECOM SAAS
### [Edição Debian 13 + MariaDB + Laravel 13.x API-First + Vue 3 / Tailwind]

> **Finalidade do Documento:** Este blueprint é a especificação arquitetural definitiva para ser consumida por Inteligências Artificiais e engenheiros de software sênior. Ele define um produto SaaS B2B moderno, modular, com **Integridade Relacional Estrita (`ON DELETE RESTRICT`)** em todos os cadastros básicos, operando sobre **Debian 13**, **MariaDB**, **PHP 8.4 + Laravel 13.x (API-First / Headless)** e **Vue 3 (`<script setup>`) + Tailwind CSS**.

---

## 1. DIRETRIZ MANDATÓRIA: INTEGRIDADE RELACIONAL ESTRITA (RESTRICT)

### 1.1 Regra de Ouro contra Corrupção e Órfãos de Dados
- **Zero Exclusões em Cascata em Dados de Negócio:**
  No banco MariaDB, todas as Foreign Keys de entidades de negócio utilizam obrigatoriamente **`ON DELETE RESTRICT`**.
- **Comportamento do Sistema:**
  Se um operador tentar excluir qualquer registro que possua dependências ativas ou históricas (ex: tentar excluir uma Pessoa que possui chamados, uma Cidade que possui clientes, um Material com movimentações ou uma Categoria Financeira com lançamentos), o banco de dados **rejeita a operação imediatamente** e a API retorna código HTTP `422 Unprocessable Entity` com mensagem semântica clara:
  > *"Este registro não pode ser excluído pois possui vínculos ativos com [Entidade Vinculada]. Para desativá-lo, altere seu status para 'Inativo'."*
- **Soft Deletes com Auditoria:** Entidades de cadastro suportam `SoftDeletes` (`deleted_at`), registrando o usuário que solicitou a inativação (`deleted_by_user_id`).

### 1.2 Catálogo de Cadastros Básicos Canônicos do Sistema
1. **Cidades e Estados Canônicos (`states`, `cities`):** Base canônica do IBGE com código oficial, UF e coordenadas centrais. Todos os endereços do sistema vinculam-se a `cities.id`.
2. **Pessoas (`people`):** Cadastro único centralizado (PF/PJ, documentos, contatos, dados bancários).
3. **Unidades de Medida (`units`):** Metros (MT), Unidade (UND), Peça (PC), Bobina (BOB), Kilômetro (KM), Rolo (RL), Par (PAR).
4. **Departamentos e Setores (`departments`):** NOC, Suporte Externo, Almoxarifado, Financeiro, Infraestrutura.
5. **Categorias e Motivos de Atendimento (`ticket_categories`, `ticket_reasons`).**
6. **Centros de Custo e Plano de Contas (`financial_cost_centers`, `financial_categories`).**
7. **Catálogo de Serviços e LPU (`service_catalog`, `lpu_items`).**
8. **Tipos de Equipamentos de Telecom (`equipment_types`):** OLT, Switch, Retificador, No-Break, DIO, Roteador, ONU.
9. **Veículos da Frota (`vehicles`):** Placa, modelo, ano, odômetro atual e vínculo com equipes.

---

## 2. OS DOIS GRANDES AXIOMAS DO SISTEMA

### AXIOMA 1: O Sistema Gira em Torno de PESSOAS (`Person-Centric Engine`)
No centro de todas as operações está a entidade **Pessoa (`people`)**. Uma Pessoa possui dados canônicos únicos (CPF/CNPJ, contatos, endereços) e assume simultaneamente um ou mais papéis dentro da plataforma:
1. **Colaboradores / Técnicos (`Workers / Staff`):** Executam o trabalho de campo ou backoffice, possuem jornada, escalas, presença e banco de horas.
2. **Fornecedores (`Suppliers`):** Fornecem cabos, ferragens, ONUs, equipamentos de rede ou prestam serviços à empresa.
3. **Clientes (`Customers / Subscribers`):** Assinantes residenciais, corporativos (B2B) ou operadoras que contratam a infraestrutura.
4. **Solicitantes (`Requesters`):** Entidades ou pessoas autorizadas a demandar chamados em nome de um cliente ou operadora parceira.

### AXIOMA 2: Suprimentos Gira em Torno de DEPÓSITOS FÍSICOS & CLUSTERS REGIONAIS
- **Depósitos Físicos (`depots`):** Todo local físico onde materiais estão fisicamente guardados (Almoxarifado Central, Bases Regionais, Carros dos Técnicos e Laboratórios).
- **Clusters de Depósito / Posição Regional (`depot_clusters`):** Agrupamento geográfico de depósitos sob a mesma cobertura.
- **Saldo Virtual Aglutinado (`Aggregated Virtual Balance`):** O gestor enxerga o saldo consolidado de toda a região em tempo real (soma da base regional + todos os carros dos técnicos do cluster).

---

## 3. INTRANET CORPORATIVA & CAMADAS DE COMUNICAÇÃO SEGMENTADA

O sistema dispõe de uma **Camada de Comunicação e Intranet** com 4 portais específicos dedicados a cada persona:
1. **Camada Colaboradores:** Mural de avisos, normas de EPIs, espelho de ponto diário e extrato de banco de horas simplificado.
2. **Camada Fornecedores:** Upload de notas fiscais, pedidos de compra, cotações e comprovantes de pagamento.
3. **Camada Clientes (White-label):** Rastreamento de acionamento em tempo real no mapa, 2ª via de boletos/PIX e histórico de chamados.
4. **Camada Solicitantes:** Abertura de chamados em lote, acompanhamento de SLAs contratuais e download de laudos RFO em PDF.

---

## 4. O MÓDULO DE ACIONAMENTO: DESLOCAMENTO, JORNADA & BANCO DE HORAS

O **Acionamento (`dispatches`)** é a Ordem de Serviço de campo com mobilização física de equipe:
1. **Controle de Deslocamento:** Registro de início de viagem (`departed_at`), chegada no cliente (`arrived_at`), fotos de odômetro (KM inicial/final) e cálculo automático do **TMA de Deslocamento** vs **TMA de Atendimento**.
2. **Presença Diária & Ponto Eletrônico:** Ponto no mobile com geolocalização e foto.
3. **Banco de Horas Simplificado:** Apuração diária automática (horas previstas vs horas trabalhadas). Extrato transparente com créditos (extras), débitos (atrasos/saídas) e saldo acumulado atual com opção simples de compensação.
4. **Fechamento RFO:** Checklist com watermark digital, medição óptica em dBm, speedtest, baixa de seriais do carro, assinatura digital na tela e emissão de laudo em PDF.

---

## 5. O NÚCLEO OPERACIONAL: O FUNIL EM 3 ESTÁGIOS

1. **TAREFAS (`tasks`):** Ponto de entrada universal assíncrono (APIs, webhooks, IA, rotinas de POP, e-mails e chat).
2. **CHAMADOS (`tickets`):** Pedidos formais de clientes e operadoras com controle de SLA e diagnóstico remoto.
3. **ACIONAMENTOS (`dispatches`):** Ordens de serviço de campo com mobilização de técnicos, veículos, materiais e RFO.

---

## 6. SISTEMA FINANCEIRO COMPLETO (ERP FINANCEIRO)

- **Contas a Pagar:** Integrado automaticamente à LPU de técnicos, contratos de POPs e compras do almoxarifado. Rateio por centros de custo e alçadas de aprovação.
- **Contas a Receber:** Faturamento de contratos com Boletos e PIX com QR Code dinâmico, além de régua de cobrança automática via WhatsApp e E-mail.
- **Fluxo de Caixa & DRE:** Fluxo diário e projetado (30/60/90 dias) e DRE gerencial em tempo real.
- **Conciliação Bancária Automática:** Importação de extratos OFX e arquivos CNAB 240/400 com algoritmo de Smart Matching e conciliação em 1 clique.

---

## 7. SISTEMA DE TEMAS & WHITE-LABEL (THEMING ENGINE)

- **White-Label por Tenant (`Organization.theme_settings`):** Logotipo, favicon, nome exibido e cor primária da marca. Os links de rastreamento do cliente e os PDFs de laudo RFO saem com a identidade do provedor.
- **Dark Mode & Light Mode Nativo:** Alternância suave com respeito às preferências do sistema operacional.
- **Densidade de Layout:** Modo Compacto (para NOC, financeiro e despachantes) vs Modo Confortável.
- **Design Tokens com Tailwind CSS:** Zero cores fixas hardcoded, tudo controlado via CSS Custom Properties.

---

## 8. MODELO DE DADOS MARIADB COM INTEGRIDADE ESTRITA (`RESTRICT`)

```sql
-- ==============================================================================
-- 1. CADASTROS BÁSICOS CANÔNICOS
-- ==============================================================================

CREATE TABLE states (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code CHAR(2) NOT NULL UNIQUE, -- SP, MG, RJ...
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    state_id INT UNSIGNED NOT NULL,
    ibge_code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    location POINT NOT NULL,
    FOREIGN KEY (state_id) REFERENCES states(id) ON DELETE RESTRICT,
    SPATIAL INDEX idx_city_location (location)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE units (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) NOT NULL UNIQUE, -- MT, UND, PC, BOB, KM, RL, PAR
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE departments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ticket_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    department_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ticket_reasons (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    default_priority ENUM('LOW', 'MEDIUM', 'HIGH', 'CRITICAL') DEFAULT 'MEDIUM',
    FOREIGN KEY (category_id) REFERENCES ticket_categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- 2. CONTAS, PROVEDORES E PESSOAS (RESTRIÇÃO ESTRITA)
-- ==============================================================================

CREATE TABLE accounts (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    trade_name VARCHAR(255) NULL,
    document_number VARCHAR(20) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    status ENUM('trial', 'active', 'past_due', 'suspended', 'canceled') DEFAULT 'trial',
    plan_tier VARCHAR(50) DEFAULT 'starter',
    active_seats_limit INT DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE organizations (
    id CHAR(36) PRIMARY KEY,
    account_id CHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NULL,
    document_number VARCHAR(20) NULL,
    logo_url VARCHAR(500) NULL,
    theme_settings JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE RESTRICT,
    INDEX idx_org_account (account_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE people (
    id CHAR(36) PRIMARY KEY,
    account_id CHAR(36) NOT NULL,
    organization_id CHAR(36) NOT NULL,
    person_type ENUM('INDIVIDUAL', 'LEGAL_ENTITY') DEFAULT 'INDIVIDUAL',
    name VARCHAR(255) NOT NULL,
    trade_name VARCHAR(255) NULL,
    document_number VARCHAR(20) NOT NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(20) NULL,
    city_id INT UNSIGNED NOT NULL, -- Cidade obrigatória e relacional
    address_street VARCHAR(255) NULL,
    is_worker TINYINT(1) DEFAULT 0,
    is_supplier TINYINT(1) DEFAULT 0,
    is_customer TINYINT(1) DEFAULT 0,
    is_requester TINYINT(1) DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    deleted_at TIMESTAMP NULL,
    deleted_by_user_id CHAR(36) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE RESTRICT,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE RESTRICT,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE RESTRICT,
    INDEX idx_person_doc (account_id, organization_id, document_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- 3. SUPRIMENTOS & DEPÓSITOS (RESTRIÇÃO ESTRITA)
-- ==============================================================================

CREATE TABLE depot_clusters (
    id CHAR(36) PRIMARY KEY,
    account_id CHAR(36) NOT NULL,
    organization_id CHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE RESTRICT,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE workers (
    id CHAR(36) PRIMARY KEY,
    account_id CHAR(36) NOT NULL,
    organization_id CHAR(36) NOT NULL,
    person_id CHAR(36) NOT NULL,
    cluster_id CHAR(36) NOT NULL,
    employment_type ENUM('CLT', 'PJ', 'THIRD_PARTY') DEFAULT 'CLT',
    last_location POINT NOT NULL,
    status ENUM('active', 'inactive', 'on_leave') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE RESTRICT,
    FOREIGN KEY (cluster_id) REFERENCES depot_clusters(id) ON DELETE RESTRICT,
    SPATIAL INDEX idx_worker_location (last_location)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE depots (
    id CHAR(36) PRIMARY KEY,
    account_id CHAR(36) NOT NULL,
    organization_id CHAR(36) NOT NULL,
    cluster_id CHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    type ENUM('CENTRAL', 'REGIONAL_BASE', 'VEHICLE', 'LAB_REPAIR') NOT NULL,
    worker_id CHAR(36) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cluster_id) REFERENCES depot_clusters(id) ON DELETE RESTRICT,
    FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE materials (
    id CHAR(36) PRIMARY KEY,
    account_id CHAR(36) NOT NULL,
    organization_id CHAR(36) NOT NULL,
    unit_id INT UNSIGNED NOT NULL,
    code VARCHAR(50) NOT NULL,
    name VARCHAR(255) NOT NULL,
    has_serial TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE RESTRICT,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE stock_balances (
    id CHAR(36) PRIMARY KEY,
    depot_id CHAR(36) NOT NULL,
    material_id CHAR(36) NOT NULL,
    quantity DECIMAL(12,2) DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_depot_material (depot_id, material_id),
    FOREIGN KEY (depot_id) REFERENCES depots(id) ON DELETE RESTRICT,
    FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE stock_serials (
    id CHAR(36) PRIMARY KEY,
    account_id CHAR(36) NOT NULL,
    organization_id CHAR(36) NOT NULL,
    material_id CHAR(36) NOT NULL,
    current_depot_id CHAR(36) NOT NULL,
    serial_number VARCHAR(100) NOT NULL,
    status ENUM('IN_STOCK', 'IN_TRANSIT', 'INSTALLED_CUSTOMER', 'DEFECTIVE', 'DISCARDED') DEFAULT 'IN_STOCK',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_serial_account (account_id, serial_number),
    FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE RESTRICT,
    FOREIGN KEY (current_depot_id) REFERENCES depots(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- 4. FUNIL EM 3 ESTÁGIOS COM RESTRIÇÃO ESTRITA
-- ==============================================================================

CREATE TABLE tasks (
    id CHAR(36) PRIMARY KEY,
    account_id CHAR(36) NOT NULL,
    organization_id CHAR(36) NOT NULL,
    task_number VARCHAR(30) NOT NULL,
    title VARCHAR(255) NOT NULL,
    status ENUM('INBOX', 'TRIAGED', 'PROMOTED_TICKET', 'RESOLVED_INTERNAL', 'CANCELED') DEFAULT 'INBOX',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tickets (
    id CHAR(36) PRIMARY KEY,
    account_id CHAR(36) NOT NULL,
    organization_id CHAR(36) NOT NULL,
    origin_task_id CHAR(36) NULL,
    customer_person_id CHAR(36) NOT NULL,
    requester_person_id CHAR(36) NULL,
    city_id INT UNSIGNED NOT NULL,
    department_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    reason_id INT UNSIGNED NOT NULL,
    protocol VARCHAR(30) NOT NULL,
    location POINT NOT NULL,
    status ENUM('OPEN', 'IN_TRIAGE', 'WAITING_DISPATCH', 'RESOLVED_REMOTE', 'IN_FIELD', 'CLOSED', 'CANCELED') DEFAULT 'OPEN',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_person_id) REFERENCES people(id) ON DELETE RESTRICT,
    FOREIGN KEY (requester_person_id) REFERENCES people(id) ON DELETE RESTRICT,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE RESTRICT,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE RESTRICT,
    FOREIGN KEY (category_id) REFERENCES ticket_categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (reason_id) REFERENCES ticket_reasons(id) ON DELETE RESTRICT,
    FOREIGN KEY (origin_task_id) REFERENCES tasks(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE dispatches (
    id CHAR(36) PRIMARY KEY,
    account_id CHAR(36) NOT NULL,
    organization_id CHAR(36) NOT NULL,
    ticket_id CHAR(36) NOT NULL,
    worker_id CHAR(36) NOT NULL,
    depot_id CHAR(36) NOT NULL,
    dispatch_number VARCHAR(30) NOT NULL,
    status ENUM('DISPATCHED', 'ON_ROUTE', 'ARRIVED_SITE', 'IN_SERVICE', 'RFO_SUBMITTED', 'APPROVED', 'COMPLETED', 'CANCELED', 'FAILED') DEFAULT 'DISPATCHED',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE RESTRICT,
    FOREIGN KEY (worker_id) REFERENCES workers(id) ON DELETE RESTRICT,
    FOREIGN KEY (depot_id) REFERENCES depots(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 9. CADASTRO CENTRALIZADO DE PESSOAS, GEOLOCALIZAÇÃO & INTEGRAÇÃO VIACEP

> Implementação do Módulo Canônico de Pessoas (`Person-Centric Architecture`) conforme diretrizes do Design System e requisitos operacionais do ERP.

### 9.1 Estrutura de Abas e Ergonomia do Modal (Tela Cheia)
O cadastro de pessoas opera em um Modal de Tela Cheia (`fullscreen modal`) com backdrop escurecido e transições suaves, estruturado em abas lógicas:
1. **Principal:** Identificação civil/jurídica, data de cadastro e seleção de papéis.
2. **Contatos:** E-mail, telefones (celular/fixo) e redes sociais/contato profissional.
3. **Endereços:** Gestão de múltiplos endereços (Residencial e Comercial) com geolocalização.
4. **Financeiro:** Informações fiscais, bancárias e limite de crédito.
5. **Observações:** Histórico de anotações internas do cliente/colaborador.

### 9.2 Aba Principal: Simplificação e 7 Papéis Operacionais do Cadastro
Conforme diretriz de ergonomia e simplificação operacional:
- **CNPJ/CPF:** Campo com validação e formatação automática.
- **Razão Social / Nome Completo:** Campo obrigatório (`name`).
- **Nome Fantasia / Nome de Tratamento:** (`trade_name`).
- **Data de Cadastro:** Formato estrito **Dia, Mês e Ano (`DD/MM/AAAA`)**, utilizando o componente reutilizável do Design System `<DateInput />` com máscara reativa e conversão bidirecional para ISO `YYYY-MM-DD` no backend.
- **Grupo Empresarial:** Chave estrangeira relacional com tabela `groups`.
- **Papéis do Cadastro (Flags Booleanas Obrigatórias com Filtros Rápidos na Tabela):**
  1. `is_customer`: Cliente (S/N)
  2. `is_supplier`: Fornecedor (S/N)
  3. `is_employee`: Funcionário (S/N)
  4. `is_outsourced`: Terceirizado (S/N)
  5. `is_seller`: Vendedor (S/N)
  6. `is_driver`: Motorista (S/N)
  7. `is_carrier`: Transportadora (S/N)

Cada papel possui badge semântico colorido na listagem e filtro rápido dedicado no topo da tabela.

---

### 9.3 Gestão de Endereços, Referência de Localização & Coordenadas Geográficas
A aba **3. Endereços** contempla a gestão completa de localização física para logística e atendimento de campo:
- **Endereço Residencial:**
  - CEP (`postal_code`) com busca automática ViaCEP.
  - Logradouro (`street`), Número (`number`), Complemento (`complement`), Bairro (`district`).
  - Cidade canônica (`city_id`) vinculada à base do IBGE.
  - **Referência / Instruções de Localização (`reference`):** Campo de texto para orientações de acesso, pontos de referência de campo, portarias ou especificidades de entrega.
  - **Latitude e Longitude (`latitude`, `longitude`):** Coordenadas geográficas em ponto flutuante de alta precisão (DECIMAL 10,8 e 11,8), com botão integrado de **Captura GPS** (`navigator.geolocation`) que obtém as coordenadas do dispositivo em tempo real.
- **Endereço Comercial & Espelhamento Dinâmico:**
  - Checkbox **"Comercial é o mesmo que o Residencial" (`commercial_same_as_residential`)**: Ao ser ativado, copia e espelha automaticamente em tempo real todos os dados do endereço residencial (incluindo CEP, rua, número, complemento, bairro, cidade, referência e coordenadas) para os campos comerciais, desabilitando a edição manual para evitar divergências e agilizar o cadastro.
  - Campos comerciais independentes: `commercial_postal_code`, `commercial_street`, `commercial_number`, `commercial_complement`, `commercial_district`, `commercial_city_id`, `commercial_reference`, `commercial_latitude`, `commercial_longitude`.

---

### 9.4 Integração Nativa com a API ViaCEP (`https://viacep.com.br/`)
O ERP conta com integração backend e frontend com a API pública ViaCEP:
- **Rota Backend:** `GET /api/v1/cep/{postal_code}` (`GeoController@cep`).
- **Fluxo de Integração:**
  1. O usuário digita o CEP de 8 dígitos no campo residencial ou comercial.
  2. O frontend dispara requisição assíncrona com indicador visual de carregamento (`spinner`).
  3. O backend sanitiza o CEP e consome `https://viacep.com.br/ws/{cep}/json/` com timeout e tratamento de erros.
  4. **Vinculação Canônica com o IBGE:** Como o ViaCEP retorna o código IBGE do município (`ibge`), o backend realiza a busca exata no banco de dados local através de `City::where('ibge_code', $data['ibge'])->first()`. Isso garante que a chave estrangeira `city_id` seja amarrada com 100% de integridade relacional, eliminando erros de digitação de nomes de municípios.
  5. Os campos Logradouro, Bairro, Complemento e Cidade são preenchidos automaticamente na interface.

---

### 9.5 Base Canônica Nacional de Cidades (5.570 Municípios do IBGE) & Componente `<CitySearchSelect />`
- O sistema possui todos os 5.570 municípios do Brasil e seus 27 estados/DF devidamente cadastrados na tabela `cities`, com seus respectivos códigos oficiais do IBGE.
- **Componente Blade/Vue Universal `<CitySearchSelect />`:**
  - Ativado a partir da digitação de 3 caracteres.
  - **Ícone de Lupa (`🔍`) no canto direito** do campo (conforme diretriz obrigatória de UI/UX, substituindo a seta tradicional de dropdown `▼`).
  - Suporte completo a tema escuro e tema claro com destaque de cidade, estado e código IBGE.
  - Disponibilizado e documentado no Catálogo do Design System (`/admin/design-system`).

---

### 9.6 Diretrizes Gerais de Interface, Change-Log & Commits
1. **Espaçamento Horizontal Simétrico de 60px:**
   - O layout possui espaçamento horizontal de exatamente **60px entre o sidebar e a área de conteúdo**, bem como **60px de respiro no lado direito**, garantindo ergonomia e harmonia visual em telas desktop.
2. **Página de Change-Log Integrada (`/admin/changelog`):**
   - Página que analisa os commits Git do repositório em tempo real, agrupando por data e categoria convencional (feat, fix, docs, refactor, chore).
3. **Regra de Ouro para Commits em Português do Brasil:**
   - **TODO e QUALQUER commit no repositório DEVE ter sua mensagem escrita obrigatoriamente em Português do Brasil (pt-BR)** seguindo a convenção semântica.

---

## 10. MÓDULO DE DOCUMENTOS, FILIAÇÃO & HUB CENTRAL DE APIS / INTEGRAÇÕES

> Especificação funcional e relacional dos campos de identificação civil, dados fiscais, filiação e catálogo de integrações.

### 10.1 Aba 2. Documentos & Filiação no Cadastro de Pessoas
A aba **Documentos** foi estruturada para centralizar todos os identificadores oficiais do cidadão ou da pessoa jurídica:

#### Bloco 1: Documentos de Identificação Civil & Fiscal
1. **Nº RG:** Número da Carteira de Identidade Civil (`rg_ie`).
2. **Órgão Emissor:** Sigla do órgão expedidor e UF (ex: `SSP/SP`, `DETRAN/RJ`) (`rg_issuer`).
3. **Data de Emissão:** Formato oficial estrito **Dia, Mês e Ano (`DD/MM/AAAA`)** com máscara automática `<DateInput />` (`rg_issue_date`).
4. **Inscrição Estadual (IE):** Registro de contribuinte ICMS da Unidade da Federação ou "Isento" (`state_registration`).
5. **Inscrição Municipal (IM):** Registro de prestador de serviços na Prefeitura (`municipal_registration`).
6. **CNAE Principal:** Código e descrição da Classificação Nacional de Atividades Econômicas (`cnae`). Preenchido automaticamente via integração CNPJá.
7. **Nº Inscrição SUFRAMA:** Cadastro de incentivos da Superintendência da Zona Franca de Manaus (`suframa_registration`).

#### Bloco 2: Filiação & Dados Biográficos / Corporativos
1. **Nome da Mãe:** Nome completo da genitora (`mother_name`).
2. **Nome do Pai:** Nome completo do genitor (`father_name`).
3. **Data de Fundação ou Nascimento:** Campo versátil que assume semântica de Fundação para Pessoa Jurídica e Nascimento para Pessoa Física, no padrão `DD/MM/AAAA` (`birth_or_foundation_date`).
4. **Capital Social (PJ):** Valor monetário integralizado da empresa (`share_capital`), formatado em moeda brasileira e preenchido pela CNPJá.
5. **Naturalidade:** Cidade e Unidade da Federação de origem do cadastrado (`birth_place`).

---

### 10.2 Validação Matemática e Máscara Dinâmica de CPF / CNPJ (`<CpfCnpjInput />`)
- **Componente Oficial do Design System:** `<CpfCnpjInput />`
- **Máscara Reativa:**
  - Até 11 dígitos numéricos: formata como CPF (`000.000.000-00`).
  - De 12 a 14 dígitos numéricos: formata dinamicamente como CNPJ (`00.000.000/0000-00`).
- **Validação Algorítmica Oficial da Receita Federal:**
  - Validação de dígitos verificadores (Módulo 11) com rejeição de sequências falsas (`111.111.111-11`, etc.).
  - Borda e badge visual: verde com ícone de check (`CPF` ou `CNPJ`) quando válido; vermelho (`Inválido`) quando inválido.
- **Botão Integrado "Puxar CNPJá":**
  - Ao detectar 14 dígitos válidos de CNPJ, exibe botão de ação com ícone de prédio e spinner de busca.

---

### 10.3 Hub de APIs & Menu "APIs / Integrações" (`/admin/integrations`)
Acesso direto pela barra lateral (ícone de rede / conexão), reunindo o inventário de todas as APIs integradas:
1. **CNPJá Open API (`https://cnpja.com/api/open`):**
   - Consulta pública de CNPJs na Receita Federal do Brasil.
   - Puxa Razão Social, Fantasia, CNAE, Capital Social, Data de Fundação, SUFRAMA e Endereço com amarração ao código IBGE.
   - Teste interativo com campo de consulta ao vivo na página `/admin/integrations`.
2. **ViaCEP WebService (`https://viacep.com.br/`):**
   - Geocodificação reversa de CEP nacional para preenchimento de endereços residenciais e comerciais.
   - Teste interativo ao vivo com busca de logradouro e município.
3. **Catálogo Canônico IBGE (5.570 Cidades):**
   - Base territorial local estrita para integridade de dados.
4. **GPS & Georreferenciamento W3C:**
   - Captura de coordenadas geográficas via hardware/satélite.
5. **Roadmap de Expansão:** Gateways de pagamento (PIX/Boleto), mensageria (WhatsApp/SMS) e TR-069 ACS.


### 11. Ergonomia e Tratamento de Dados na Listagem de Pessoas (`#people`)
- **Máscaras Dinâmicas de Documento**:
  - Exibição de documento formatado com máscara canônica de acordo com a tipificação:
    - **Pessoa Física (PF)**: Máscara `000.000.000-00`
    - **Pessoa Jurídica (PJ)**: Máscara `00.000.000/0000-00`
  - Badge semântico de tipo: `PF` em tom âmbar e `PJ` em tom índigo.
  - **Botão de Cópia Rápida com 1 Clique**: Ícone discreto de cópia ao lado do número do documento com feedback visual instantâneo (`Copiado!` e ícone `Check` verde por 2 segundos).
- **Ações Rápidas de Contato (Links Operacionais Diretos)**:
  - Substituição de texto puro por links operacionais com clique único:
    - **Telefone**: Link direto `tel:+55...` com acionamento imediato de discador/softphone e formatação `(XX) XXXXX-XXXX` ou `(XX) XXXX-XXXX`.
    - **WhatsApp**: Link direto `https://wa.me/55...` com ícone dedicado da marca para abertura imediata de chat web ou aplicativo.
    - **E-mail**: Link direto `mailto:...` com ícone de envelope e truncamento elegante com tooltip.
    - **Cópia Rápida de Contato**: Botões discretos de 1 clique para cópia de telefone e e-mail.
- **Badges Semânticos de Papéis (Personas) com Prevenção de Sobrecarga Visual**:
  - Paleta semântica estrita para cada papel/persona:
    - **Cliente**: Verde (`emerald`)
    - **Fornecedor**: Roxo (`purple`)
    - **Colaborador / Funcionário**: Azul (`blue`)
    - **Terceirizado**: Âmbar (`amber`)
    - **Vendedor**: Ciano (`cyan`)
    - **Motorista**: Índigo (`indigo`)
    - **Transportadora**: Fúcsia (`fuchsia`)
    - **Solicitante**: Cinza chumbo (`slate`)
  - **Mecanismo Anti-Sobrecarga**: Quando um registro acumula 3 ou mais papéis, a interface exibe os 2 primeiros papéis com badge completo e um contador compacto `+N` (ex.: `+2`), permitindo expansão inline sob demanda ou consulta em tooltip, mantendo a altura uniforme das linhas da tabela.


### 12. Filtros, Busca, Ordenação e Aba de Documentos Canônica
- **Contadores em Tempo Real nas Abas de Segmentação**:
  - Todas as abas de personas exibem a contagem exata de registros ativos: `Todos (N)`, `Clientes (N)`, `Fornecedores (N)`, `Colaboradores (N)`, `Terceirizados (N)`, `Vendedores (N)`, `Motoristas (N)`, `Transportadoras (N)` e `Solicitantes (N)`.
  - Agregação SQL unificada de alta performance retornada em `meta.counts` da API.
- **Atalhos Globais e Busca Ágil**:
  - Suporte ao atalho global de teclado `Ctrl + K` (ou `Cmd + K`) e `/` para focar imediatamente no campo de busca com badge `<kbd>Ctrl K</kbd>`.
  - Botão de limpeza de pesquisa com 1 clique (`X`) e tecla `Esc`.
- **Filtros Secundários Avançados (Painel Retrátil)**:
  - Gaveta colapsável que não polui a barra principal, contendo:
    - Status: Todos, Ativos e Inativos.
    - Tipo: Todos, Pessoa Física (PF) e Pessoa Jurídica (PJ).
    - Estado (UF): Select canônico de estados federativos.
    - Cidade: Select de cidades filtradas pelo estado selecionado.
    - Botão "Limpar Filtros" e badge contador de filtros ativos no botão principal.
- **Ordenação Clicável nas Colunas (Sorting)**:
  - Cabeçalhos ordenáveis com 1 clique para `Nome` (`name`), `Município` (`city`) e `Data de Cadastro` (`date`).
  - Suporte a ordenação ascendente (`asc`) e descendente (`desc`) com ícones dinâmicos `ArrowUp`, `ArrowDown` e `ArrowUpDown`.
- **Nova Aba "Documentos" no Cadastro de Pessoas**:
  - Aba 2 `Documentos & Filiação` no modal de cadastro e edição de pessoas:
    - Documentos e Registros Fiscais: Nº RG, Órgão Emissor, Data Emissão (DD/MM/AAAA), Inscrição Estadual, Inscrição Municipal, CNAE, Num. Inscr. SUFRAMA.
    - Filiação, Origem e Dados Civis: Nome da Mãe, Nome do Pai, Data de Fundação ou Nascimento (DD/MM/AAAA), Capital Social PJ (R$), Naturalidade.


#### 📋 Estrutura Canônica da Grid de Pessoas

- **Grid Canônica de Pessoas:**
  - **Nome:** Exibição do nome/razão social em tipografia limpa e link de ordenação.
  - **Tipo & Documento:** Formatação de CPF/CNPJ com máscara dinâmica e botão discreto de cópia rápida.
  - **Ações Rápidas de Contato:** Links operacionais diretos para discagem (`tel:`), WhatsApp e e-mail (`mailto:`).
  - **Município:** Localidade e UF da pessoa com suporte a ordenação alfabética.
  - **Cadastro:** Data de cadastro formatada no padrão brasileiro `DD/MM/AAAA`.
  - **Status:** Badge semântica de ativo/inativo.
  - **Ação com Menu de Reticências:** Botão de reticências (`...`) com menu suspenso (dropdown) flutuante, agrupando opções contextuais: Editar pessoa, Alternar status (Ativar/Inativar) e Copiar identificador (#ID).


### 🎨 Manual de Identidade Visual Oficial (BrandBook)

O sistema segue estritamente as diretrizes corporativas do BrandBook para máxima consistência visual, ergonomia e acessibilidade (WCAG AA):

1. **Tipografia Institucional:**
   - **Kanit (peso SemiBold 600):** Utilizada exclusivamente para títulos (`h1`, `h2`, etc.), cabeçalhos de seção e chamadas de destaque.
   - **Montserrat (pesos Medium 500, Bold 700 e Regular 400):** Utilizada para todo o texto corrido, parágrafos, tabelas, rótulos (labels) e campos de formulário.
   - Carregamento de fontes otimizado via Google Fonts com `preconnect` em `resources/views/welcome.blade.php`.

2. **Cores Institucionais:**
   - **Laranja Institucional (`#FC6714`):** Cor de ação principal, botões de cadastro/confirmação, elementos em destaque e abas ativas.
   - **Azul Escuro Institucional (`#06064D`):** Cor estrutural para elementos de contraste (menu lateral / Sidebar, cabeçalhos escuros) e títulos Kanit.

3. **Botões e Estados de Interação:**
   - **Ação Primária:** Fundo `#FC6714`, texto branco, efeitos suaves de hover (`#E0530A`), active (`#C94605`) e sombra suave.
   - **Acessibilidade & Focus Ring:** Estados de foco (`:focus-visible` e `input:focus`) padronizados globalmente com anel sutil na cor Laranja (`box-shadow: 0 0 0 2px rgba(252, 103, 20, 0.45); border-color: #FC6714;`).
   - **Controles Nativos:** Caixas de seleção (checkboxes) e botões de rádio com `accent-color: #FC6714`.
