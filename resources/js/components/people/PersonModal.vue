<template>
  <BaseModal
    :model-value="isOpen"
    size="fullscreen"
    :title="isEditing ? 'Editar Cadastro de Pessoa' : 'Novo Cadastro de Pessoa'"
    description="Base canônica centralizada de dados fiscais, localização e personas operacionais"
    @close="close"
  >
    <!-- Badge de Destaque no Cabeçalho -->
    <template #header-badge>
      <span
        :class="form.person_type === 'individual'
          ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800'
          : 'bg-cyan-100 text-cyan-800 dark:bg-cyan-950/80 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800'"
        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold border"
      >
        <span class="w-1.5 h-1.5 rounded-full" :class="form.person_type === 'individual' ? 'bg-emerald-600' : 'bg-cyan-600'"></span>
        {{ form.person_type === 'individual' ? 'Pessoa Física (PF)' : 'Pessoa Jurídica (PJ)' }}
      </span>
    </template>

    <!-- Abas Internas de Navegação no Cabeçalho -->
    <template #tabs>
      <div class="flex items-center gap-2 overflow-x-auto text-sm">
        <button
          v-for="(tab, idx) in tabs"
          :key="tab.id"
          type="button"
          @click="activeTab = tab.id"
          :class="activeTab === tab.id
            ? 'border-blue-600 text-blue-600 dark:text-blue-400 font-bold border-b-2'
            : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-medium'"
          class="px-4 py-3 border-b-2 transition whitespace-nowrap cursor-pointer flex items-center gap-2"
        >
          <component :is="tab.icon" class="w-4 h-4" />
          <span>{{ idx + 1 }}. {{ tab.label }}</span>
        </button>
      </div>
    </template>

    <!-- Formulário Principal -->
    <form @submit.prevent="submit" id="personForm" class="space-y-6">
      <!-- ABA 1: IDENTIFICAÇÃO FISCAL & REGISTRO -->
      <div v-show="activeTab === 'identificacao'" class="space-y-6">
        <!-- Alternador Tipo: PF ou PJ -->
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-950/80 border border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <span class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Natureza Jurídica:</span>
            <div class="flex items-center gap-4">
              <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="radio" value="individual" v-model="form.person_type" class="w-4 h-4 text-blue-600 focus:ring-0" />
                <span :class="form.person_type === 'individual' ? 'font-bold text-blue-600 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 font-medium'">
                  Pessoa Física (PF)
                </span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="radio" value="legal" v-model="form.person_type" class="w-4 h-4 text-blue-600 focus:ring-0" />
                <span :class="form.person_type === 'legal' ? 'font-bold text-blue-600 dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 font-medium'">
                  Pessoa Jurídica (PJ)
                </span>
              </label>
            </div>
          </div>

          <!-- Status do Registro -->
          <div class="flex items-center gap-3">
            <span class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Status:</span>
            <select
              v-model="form.status"
              class="h-9 px-3 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 cursor-pointer"
            >
              <option value="active">● Ativo no Sistema</option>
              <option value="inactive">○ Inativo / Suspenso</option>
            </select>
          </div>
        </div>

        <!-- Linha 1: Nome e Razão Social / Fantasia -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
          <div :class="form.person_type === 'legal' ? 'xl:col-span-2' : 'xl:col-span-3'">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              {{ form.person_type === 'individual' ? 'Nome Completo *' : 'Razão Social *' }}
            </label>
            <input
              type="text"
              v-model="form.name"
              required
              placeholder="Digite o nome ou razão social canônica..."
              class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden text-sm transition"
            />
          </div>

          <div v-if="form.person_type === 'legal'">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nome Fantasia</label>
            <input
              type="text"
              v-model="form.trade_name"
              placeholder="Nome comercial da empresa..."
              class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden text-sm transition"
            />
          </div>
        </div>

        <!-- Linha 2: Documentos e Dados Civis -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              {{ form.person_type === 'individual' ? 'CPF *' : 'CNPJ *' }}
            </label>
            <input
              type="text"
              v-model="form.document_number"
              required
              :placeholder="form.person_type === 'individual' ? '000.000.000-00' : '00.000.000/0000-00'"
              class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden font-mono text-sm transition"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              {{ form.person_type === 'individual' ? 'RG (Registro Geral)' : 'Inscrição Estadual (IE)' }}
            </label>
            <input
              type="text"
              v-model="form.rg_ie"
              placeholder="Número de registro..."
              class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden font-mono text-sm transition"
            />
          </div>

          <!-- Campo Data Canônica: Sempre Dia, Mês e Ano (DD/MM/AAAA) -->
          <div>
            <DateInput
              v-model="form.birth_date"
              :label="form.person_type === 'individual' ? 'Data de Nascimento' : 'Data de Fundação'"
            />
          </div>

          <div v-if="form.person_type === 'individual'">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Gênero</label>
            <select
              v-model="form.gender_id"
              class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden text-sm transition cursor-pointer"
            >
              <option :value="null">Selecione...</option>
              <option v-for="g in genders" :key="g.id" :value="g.id">{{ g.name }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- ABA 2: CONTATOS & COMUNICAÇÃO -->
      <div v-show="activeTab === 'contatos'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              E-mail Principal *
            </label>
            <div class="relative">
              <Mail class="w-4 h-4 absolute left-3.5 top-3 text-slate-400" />
              <input
                type="email"
                v-model="form.email"
                required
                placeholder="nome@dominio.com.br"
                class="w-full h-10 pl-10 pr-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden text-sm transition"
              />
            </div>
            <p class="text-xs text-slate-500 mt-1">Utilizado para faturamento e notificações</p>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              Telefone Fixo / Comercial
            </label>
            <div class="relative">
              <Phone class="w-4 h-4 absolute left-3.5 top-3 text-slate-400" />
              <input
                type="text"
                v-model="form.phone"
                placeholder="(00) 0000-0000"
                class="w-full h-10 pl-10 pr-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden text-sm transition"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              WhatsApp / Celular
            </label>
            <div class="relative">
              <MessageSquare class="w-4 h-4 absolute left-3.5 top-3 text-emerald-500" />
              <input
                type="text"
                v-model="form.whatsapp"
                placeholder="(00) 90000-0000"
                class="w-full h-10 pl-10 pr-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden text-sm transition"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- ABA 3: ENDEREÇOS (Residencial e Comercial com busca dinâmica de 5.570 cidades e lupa na direita) -->
      <div v-show="activeTab === 'endereco'" class="space-y-8">
        <!-- BLOCO 1: ENDEREÇO RESIDENCIAL / PRINCIPAL -->
        <div class="p-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/40 space-y-5">
          <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-950/80 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                <Home class="w-4 h-4" />
              </div>
              <div>
                <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">
                  {{ form.person_type === 'individual' ? 'Endereço Residencial' : 'Endereço da Sede / Principal' }}
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">Localização principal para correspondência e cadastro fiscal</p>
              </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-900">
              Principal
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">CEP</label>
              <input
                type="text"
                v-model="form.postal_code"
                placeholder="00000-000"
                class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden font-mono text-sm transition"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Logradouro / Rua</label>
              <input
                type="text"
                v-model="form.street"
                placeholder="Avenida, Rua, Praça..."
                class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden text-sm transition"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Número</label>
              <input
                type="text"
                v-model="form.number"
                placeholder="123 ou S/N"
                class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden text-sm transition"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Complemento</label>
              <input
                type="text"
                v-model="form.complement"
                placeholder="Apto 42, Bloco B..."
                class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden text-sm transition"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Bairro</label>
              <input
                type="text"
                v-model="form.neighborhood"
                placeholder="Bairro ou Distrito"
                class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden text-sm transition"
              />
            </div>

            <!-- Busca de Cidades com Lupa à Direita -->
            <div>
              <CitySearchSelect
                v-model="form.city_id"
                :initial-city-name="residentialCityInfo.name"
                :initial-state-code="residentialCityInfo.state_code"
                :initial-ibge-code="residentialCityInfo.ibge_code"
                label="Cidade Canônica (IBGE)"
                placeholder="Digite 3 letras da cidade..."
              />
            </div>
          </div>
        </div>

        <!-- DIVISOR COM CHECKBOX INTERATIVO DE ESPELHAMENTO DO ENDEREÇO COMERCIAL -->
        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 shadow-xs flex items-center justify-between gap-4">
          <label class="flex items-center gap-3 cursor-pointer select-none">
            <input
              type="checkbox"
              v-model="form.commercial_same_as_residential"
              class="w-4 h-4 rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 cursor-pointer"
            />
            <div>
              <span class="text-sm font-bold text-slate-800 dark:text-slate-200">
                O endereço Comercial é o mesmo que o Residencial
              </span>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Marque esta opção para espelhar automaticamente os dados do endereço principal no comercial
              </p>
            </div>
          </label>

          <span
            v-if="form.commercial_same_as_residential"
            class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800"
          >
            <Check class="w-3.5 h-3.5" />
            Endereços Sincronizados
          </span>
        </div>

        <!-- BLOCO 2: ENDEREÇO COMERCIAL (Exibido quando não é o mesmo) -->
        <div
          v-if="!form.commercial_same_as_residential"
          class="p-5 rounded-2xl border border-indigo-200 dark:border-indigo-950/80 bg-indigo-50/30 dark:bg-indigo-950/20 space-y-5 transition animate-in fade-in"
        >
          <div class="flex items-center justify-between pb-3 border-b border-indigo-100 dark:border-indigo-900/60">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                <Building2 class="w-4 h-4" />
              </div>
              <div>
                <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">Endereço Comercial / Operacional</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">Local de prestação de serviços, loja, filial ou escritório operacional</p>
              </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/80 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
              Comercial
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">CEP Comercial</label>
              <input
                type="text"
                v-model="form.commercial_postal_code"
                placeholder="00000-000"
                class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden font-mono text-sm transition"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Logradouro / Rua Comercial</label>
              <input
                type="text"
                v-model="form.commercial_street"
                placeholder="Avenida, Rua, Distrito Empresarial..."
                class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden text-sm transition"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Número Comercial</label>
              <input
                type="text"
                v-model="form.commercial_number"
                placeholder="123 ou S/N"
                class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden text-sm transition"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Complemento Comercial</label>
              <input
                type="text"
                v-model="form.commercial_complement"
                placeholder="Galpão 4, Sala 801..."
                class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden text-sm transition"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Bairro Comercial</label>
              <input
                type="text"
                v-model="form.commercial_neighborhood"
                placeholder="Bairro Comercial..."
                class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden text-sm transition"
              />
            </div>

            <!-- Busca de Cidade Comercial com Lupa à Direita -->
            <div>
              <CitySearchSelect
                v-model="form.commercial_city_id"
                :initial-city-name="commercialCityInfo.name"
                :initial-state-code="commercialCityInfo.state_code"
                :initial-ibge-code="commercialCityInfo.ibge_code"
                label="Cidade Comercial (IBGE)"
                placeholder="Digite 3 letras da cidade..."
              />
            </div>
          </div>
        </div>
      </div>

      <!-- ABA 4: PAPÉIS & PERSONAS OPERACIONAIS -->
      <div v-show="activeTab === 'personas'" class="space-y-6">
        <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900 flex items-start gap-3">
          <Users class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 shrink-0" />
          <div class="text-xs text-blue-900 dark:text-blue-200 leading-relaxed">
            <strong>Arquitetura Polimórfica de Personas:</strong> Uma mesma pessoa pode acumular simultaneamente múltiplos papéis na operação (ex: Fornecedor de insumos e Cliente de link dedicado). O cadastro é unificado e nunca duplicado.
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Cliente -->
          <label
            :class="form.is_client ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-950/40 ring-2 ring-blue-500/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950/60'"
            class="p-5 rounded-xl border flex items-start gap-3.5 cursor-pointer transition select-none hover:border-slate-300 dark:hover:border-slate-700"
          >
            <input type="checkbox" v-model="form.is_client" class="w-4 h-4 mt-0.5 rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 cursor-pointer" />
            <div>
              <p class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                <Users class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                Cliente / Assinante
              </p>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                Contratante de planos de internet fibra, serviços de valor adicionado (SVA) ou locações.
              </p>
            </div>
          </label>

          <!-- Fornecedor -->
          <label
            :class="form.is_supplier ? 'border-purple-500 bg-purple-50/50 dark:bg-purple-950/40 ring-2 ring-purple-500/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950/60'"
            class="p-5 rounded-xl border flex items-start gap-3.5 cursor-pointer transition select-none hover:border-slate-300 dark:hover:border-slate-700"
          >
            <input type="checkbox" v-model="form.is_supplier" class="w-4 h-4 mt-0.5 rounded-sm border-slate-300 dark:border-slate-700 text-purple-600 focus:ring-purple-500 cursor-pointer" />
            <div>
              <p class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                <Truck class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                Fornecedor
              </p>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                Empresas parceiras de equipamentos, cabos ópticos, trânsito IP ou insumos WMS.
              </p>
            </div>
          </label>

          <!-- Colaborador -->
          <label
            :class="form.is_employee ? 'border-amber-500 bg-amber-50/50 dark:bg-amber-950/40 ring-2 ring-amber-500/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950/60'"
            class="p-5 rounded-xl border flex items-start gap-3.5 cursor-pointer transition select-none hover:border-slate-300 dark:hover:border-slate-700"
          >
            <input type="checkbox" v-model="form.is_employee" class="w-4 h-4 mt-0.5 rounded-sm border-slate-300 dark:border-slate-700 text-amber-600 focus:ring-amber-500 cursor-pointer" />
            <div>
              <p class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                <Briefcase class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                Colaborador
              </p>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                Técnico de campo (FSM), atendente de suporte ou operador interno do ERP.
              </p>
            </div>
          </label>

          <!-- Solicitante -->
          <label
            :class="form.is_requester ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/40 ring-2 ring-indigo-500/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950/60'"
            class="p-5 rounded-xl border flex items-start gap-3.5 cursor-pointer transition select-none hover:border-slate-300 dark:hover:border-slate-700"
          >
            <input type="checkbox" v-model="form.is_requester" class="w-4 h-4 mt-0.5 rounded-sm border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500 cursor-pointer" />
            <div>
              <p class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                <UserCheck class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                Solicitante
              </p>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                Contato autorizado a abrir requisições, chamados e ordens de serviço.
              </p>
            </div>
          </label>
        </div>
      </div>

      <!-- Mensagens de Erro da API -->
      <div v-if="errorMessage" class="p-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900 rounded-xl text-red-700 dark:text-red-400 text-sm font-medium flex items-center gap-3">
        <AlertCircle class="w-5 h-5 shrink-0" />
        <span>{{ errorMessage }}</span>
      </div>
    </form>

    <!-- Rodapé Fixo do Modal de Tela Cheia -->
    <template #footer>
      <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
        <span>Cadastro Canônico de Pessoas • ERP Rede Pronta (Sprint 1)</span>
      </div>

      <div class="flex items-center gap-3">
        <button
          type="button"
          @click="close"
          class="h-10 px-4 rounded-lg text-sm font-medium border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition cursor-pointer"
        >
          Cancelar
        </button>
        <button
          type="submit"
          form="personForm"
          :disabled="isSubmitting"
          class="h-10 px-5 rounded-lg text-sm font-semibold bg-blue-600 hover:bg-blue-500 text-white transition disabled:opacity-50 shadow-xs flex items-center gap-2 cursor-pointer"
        >
          <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
          <Check v-else class="w-4 h-4" />
          <span>{{ isSubmitting ? 'Salvando...' : (isEditing ? 'Salvar Alterações' : 'Concluir Cadastro de Pessoa') }}</span>
        </button>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, reactive, watch } from 'vue';
import {
  FileText, Mail, MapPin, Tag, Users, Truck, Briefcase, UserCheck,
  Phone, MessageSquare, AlertCircle, Loader2, Check, Home, Building2
} from 'lucide-vue-next';
import axios from 'axios';
import BaseModal from '../common/BaseModal.vue';
import CitySearchSelect from '../common/CitySearchSelect.vue';
import DateInput from '../common/DateInput.vue';

const props = defineProps<{
  isOpen: boolean;
  personToEdit: any | null;
}>();

const emit = defineEmits(['close', 'saved']);

const isEditing = ref(false);
const isSubmitting = ref(false);
const errorMessage = ref('');

const genders = ref<any[]>([]);

// Informações iniciais de cidades para exibição prévia
const residentialCityInfo = reactive({
  name: '',
  state_code: '',
  ibge_code: '',
});

const commercialCityInfo = reactive({
  name: '',
  state_code: '',
  ibge_code: '',
});

// Abas de Navegação Interna do Modal Fullscreen
const activeTab = ref('identificacao');
const tabs = [
  { id: 'identificacao', label: 'Identificação & Documentos', icon: FileText },
  { id: 'contatos', label: 'Contatos & Comunicação', icon: Mail },
  { id: 'endereco', label: 'Endereços', icon: MapPin },
  { id: 'personas', label: 'Papéis & Personas', icon: Tag },
];

const defaultForm = () => ({
  person_type: 'individual',
  name: '',
  trade_name: '',
  document_number: '',
  rg_ie: '',
  birth_date: '',
  gender_id: null,

  // Endereço Residencial / Principal
  city_id: null,
  postal_code: '',
  street: '',
  number: '',
  complement: '',
  neighborhood: '',

  // Endereço Comercial
  commercial_same_as_residential: true,
  commercial_city_id: null,
  commercial_postal_code: '',
  commercial_street: '',
  commercial_number: '',
  commercial_complement: '',
  commercial_neighborhood: '',

  // Contatos
  email: '',
  phone: '',
  whatsapp: '',

  // Personas
  is_employee: false,
  is_supplier: false,
  is_client: false,
  is_requester: false,
  status: 'active',
});

const form = reactive(defaultForm());

const loadReferences = async () => {
  try {
    const gendersRes = await axios.get('/api/v1/genders');
    genders.value = gendersRes.data.data;
  } catch (err) {
    console.error('Erro ao carregar referências', err);
  }
};

watch(() => props.isOpen, (open) => {
  if (open) {
    activeTab.value = 'identificacao';
    errorMessage.value = '';
    loadReferences();

    if (props.personToEdit) {
      isEditing.value = true;
      const p = props.personToEdit;

      const resAddr = p.residential_address || p.address || {};
      const commAddr = p.commercial_address || {};

      residentialCityInfo.name = resAddr.city_name || resAddr.city?.name || '';
      residentialCityInfo.state_code = resAddr.state_code || resAddr.city?.state?.code || '';
      residentialCityInfo.ibge_code = resAddr.ibge_code || resAddr.city?.ibge_code || '';

      commercialCityInfo.name = commAddr.city_name || commAddr.city?.name || '';
      commercialCityInfo.state_code = commAddr.state_code || commAddr.city?.state?.code || '';
      commercialCityInfo.ibge_code = commAddr.ibge_code || commAddr.city?.ibge_code || '';

      Object.assign(form, {
        ...p,
        birth_date: p.birth_date_formatted || p.birth_date || '',
        is_employee: p.personas?.is_employee || false,
        is_supplier: p.personas?.is_supplier || false,
        is_client: p.personas?.is_client || false,
        is_requester: p.personas?.is_requester || false,

        // Residencial
        city_id: resAddr.city_id || null,
        postal_code: resAddr.postal_code || '',
        street: resAddr.street || '',
        number: resAddr.number || '',
        complement: resAddr.complement || '',
        neighborhood: resAddr.neighborhood || '',

        // Comercial
        commercial_same_as_residential: commAddr.same_as_residential ?? true,
        commercial_city_id: commAddr.city_id || null,
        commercial_postal_code: commAddr.postal_code || '',
        commercial_street: commAddr.street || '',
        commercial_number: commAddr.number || '',
        commercial_complement: commAddr.complement || '',
        commercial_neighborhood: commAddr.neighborhood || '',

        // Contato
        email: p.contact?.email || p.email || '',
        phone: p.contact?.phone || p.phone || '',
        whatsapp: p.contact?.whatsapp || p.whatsapp || '',
      });
    } else {
      isEditing.value = false;
      residentialCityInfo.name = '';
      residentialCityInfo.state_code = '';
      residentialCityInfo.ibge_code = '';
      commercialCityInfo.name = '';
      commercialCityInfo.state_code = '';
      commercialCityInfo.ibge_code = '';
      Object.assign(form, defaultForm());
    }
  }
});

const close = () => {
  emit('close');
};

const submit = async () => {
  isSubmitting.value = true;
  errorMessage.value = '';

  try {
    const payload = { ...form };

    // Se comercial for o mesmo que residencial, copia os dados no payload para manter consistência
    if (payload.commercial_same_as_residential) {
      payload.commercial_postal_code = payload.postal_code;
      payload.commercial_street = payload.street;
      payload.commercial_number = payload.number;
      payload.commercial_complement = payload.complement;
      payload.commercial_neighborhood = payload.neighborhood;
      payload.commercial_city_id = payload.city_id;
    }

    if (isEditing.value && props.personToEdit) {
      await axios.put(`/api/v1/people/${props.personToEdit.id}`, payload);
    } else {
      await axios.post('/api/v1/people', payload);
    }
    emit('saved');
    close();
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Erro ao processar formulário.';
  } finally {
    isSubmitting.value = false;
  }
};
</script>
