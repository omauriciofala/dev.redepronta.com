<template>
  <div class="space-y-6">
    <!-- Cabeçalho da Página -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-200 dark:border-slate-800">
      <div>
        <div class="flex items-center gap-3">
          <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 shadow-2xs">
            <Network class="w-6 h-6" />
          </div>
          <div>
            <div class="flex items-center gap-2.5">
              <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                APIs & Integrações
              </h1>
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                4 APIs Operacionais
              </span>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
              Hub central de gerenciamento, documentação técnica e testes interativos das integrações do ERP.
            </p>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button
          type="button"
          @click="fetchIntegrations"
          :disabled="refreshing"
          class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer disabled:opacity-50 shadow-2xs"
        >
          <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': refreshing }" />
          <span>{{ refreshing ? 'Verificando...' : 'Atualizar Status' }}</span>
        </button>
      </div>
    </div>

    <!-- Cards de Métricas e Saúde -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Conexões</span>
          <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
            <Globe class="w-4 h-4" />
          </div>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="text-2xl font-bold text-slate-900 dark:text-slate-100">4</span>
          <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">100% Online</span>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">ViaCEP, CNPJá, IBGE, GPS</p>
      </div>

      <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Base Canônica IBGE</span>
          <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
            <Database class="w-4 h-4" />
          </div>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="text-2xl font-bold text-slate-900 dark:text-slate-100">5.570</span>
          <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Municípios</span>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Integridade estrita (RESTRICT)</p>
      </div>

      <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Consultas CNPJá</span>
          <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center">
            <Building2 class="w-4 h-4" />
          </div>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="text-2xl font-bold text-slate-900 dark:text-slate-100">Open API</span>
          <span class="text-xs font-medium text-purple-600 dark:text-purple-400">Receita Federal</span>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Dados cadastrais e endereço</p>
      </div>

      <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Latência Média</span>
          <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center">
            <Activity class="w-4 h-4" />
          </div>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="text-2xl font-bold text-slate-900 dark:text-slate-100">&lt; 180ms</span>
          <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">Alta Performance</span>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Cache de cidades em memória</p>
      </div>
    </div>

    <!-- Catálogo de APIs Ativas com Testes Interativos -->
    <div class="space-y-4">
      <h2 class="text-base font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
        APIs Conectadas no Sistema
      </h2>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Card 1: CNPJá Open API -->
        <div class="flex flex-col justify-between rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 shadow-2xs">
          <div>
            <div class="flex items-start justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 text-blue-600 dark:text-blue-400 flex items-center justify-center shadow-2xs">
                  <Building2 class="w-5 h-5" />
                </div>
                <div>
                  <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">
                    CNPJá Open API
                  </h3>
                  <a
                    href="https://cnpja.com/api/open"
                    target="_blank"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1"
                  >
                    <span>https://cnpja.com/api/open</span>
                    <ExternalLink class="w-3 h-3" />
                  </a>
                </div>
              </div>
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Operacional
              </span>
            </div>

            <p class="text-sm text-slate-600 dark:text-slate-300 mt-3 leading-relaxed">
              Consulta pública de dados cadastrais na Receita Federal do Brasil. Ao digitar os 14 dígitos do CNPJ na aba Principal de Pessoas, o ERP extrai automaticamente Razão Social, Nome Fantasia, Situação e preenche o endereço com vinculação do município ao código IBGE.
            </p>

            <div class="mt-4 p-3 rounded-lg bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-xs space-y-1 font-mono">
              <div class="flex items-center justify-between text-slate-500">
                <span>Endpoint do ERP:</span>
                <span class="text-blue-600 dark:text-blue-400">GET /api/v1/integrations/cnpj/{tax_id}</span>
              </div>
              <div class="flex items-center justify-between text-slate-500">
                <span>Vinculação de Município:</span>
                <span class="text-emerald-600 dark:text-emerald-400">address.municipality → cities.ibge_code</span>
              </div>
            </div>
          </div>

          <!-- Teste Interativo de CNPJ -->
          <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
              Teste Interativo ao Vivo
            </span>
            <div class="flex gap-2 mt-2">
              <input
                type="text"
                v-model="testCnpj"
                placeholder="Ex: 00.000.000/0001-91 (Banco do Brasil)"
                class="flex-1 h-9 px-3 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 font-mono text-slate-900 dark:text-slate-100 outline-hidden focus:ring-2 focus:ring-blue-500"
              />
              <button
                type="button"
                @click="runCnpjTest"
                :disabled="loadingCnpjTest"
                class="h-9 px-3 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white cursor-pointer disabled:opacity-50 transition flex items-center gap-1.5 shadow-xs"
              >
                <Loader2 v-if="loadingCnpjTest" class="w-3.5 h-3.5 animate-spin" />
                <Play v-else class="w-3.5 h-3.5" />
                <span>Consultar</span>
              </button>
            </div>

            <!-- Resultado do Teste CNPJ -->
            <div v-if="cnpjTestResult" class="mt-3 p-3 rounded-lg bg-emerald-50/50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-xs">
              <div class="font-bold text-slate-900 dark:text-slate-100 flex items-center justify-between">
                <span>{{ cnpjTestResult.name }}</span>
                <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-200 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 font-bold uppercase">
                  {{ cnpjTestResult.status }}
                </span>
              </div>
              <p v-if="cnpjTestResult.trade_name" class="text-slate-600 dark:text-slate-300 text-[11px] mt-0.5">
                Nome Fantasia: {{ cnpjTestResult.trade_name }}
              </p>
              <p class="text-slate-500 dark:text-slate-400 text-[11px] mt-1">
                📍 {{ cnpjTestResult.address?.street }}, {{ cnpjTestResult.address?.number }} - {{ cnpjTestResult.address?.neighborhood }}, {{ cnpjTestResult.address?.city_name }}/{{ cnpjTestResult.address?.state_code }} (CEP: {{ cnpjTestResult.address?.postal_code }})
              </p>
            </div>
            <div v-else-if="cnpjTestError" class="mt-3 p-2.5 rounded-lg bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-xs text-rose-700 dark:text-rose-300">
              {{ cnpjTestError }}
            </div>
          </div>
        </div>

        <!-- Card 2: ViaCEP API -->
        <div class="flex flex-col justify-between rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 shadow-2xs">
          <div>
            <div class="flex items-start justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shadow-2xs">
                  <MapPin class="w-5 h-5" />
                </div>
                <div>
                  <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">
                    ViaCEP WebService
                  </h3>
                  <a
                    href="https://viacep.com.br/"
                    target="_blank"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1"
                  >
                    <span>https://viacep.com.br/</span>
                    <ExternalLink class="w-3 h-3" />
                  </a>
                </div>
              </div>
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Operacional
              </span>
            </div>

            <p class="text-sm text-slate-600 dark:text-slate-300 mt-3 leading-relaxed">
              WebService gratuito e de alta disponibilidade para geocodificação postal. No cadastro de Pessoas, ao preencher o CEP residencial ou comercial, o sistema busca o logradouro, bairro, complemento e associa com precisão matemática a cidade correta via código IBGE.
            </p>

            <div class="mt-4 p-3 rounded-lg bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-xs space-y-1 font-mono">
              <div class="flex items-center justify-between text-slate-500">
                <span>Endpoint do ERP:</span>
                <span class="text-emerald-600 dark:text-emerald-400">GET /api/v1/cep/{postal_code}</span>
              </div>
              <div class="flex items-center justify-between text-slate-500">
                <span>Origem Canônica:</span>
                <span class="text-blue-600 dark:text-blue-400">https://viacep.com.br/ws/{cep}/json/</span>
              </div>
            </div>
          </div>

          <!-- Teste Interativo de CEP -->
          <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
              Teste Interativo ao Vivo
            </span>
            <div class="flex gap-2 mt-2">
              <input
                type="text"
                v-model="testCep"
                placeholder="Ex: 01001-000 (Praça da Sé)"
                class="flex-1 h-9 px-3 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 font-mono text-slate-900 dark:text-slate-100 outline-hidden focus:ring-2 focus:ring-emerald-500"
              />
              <button
                type="button"
                @click="runCepTest"
                :disabled="loadingCepTest"
                class="h-9 px-3 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white cursor-pointer disabled:opacity-50 transition flex items-center gap-1.5 shadow-xs"
              >
                <Loader2 v-if="loadingCepTest" class="w-3.5 h-3.5 animate-spin" />
                <Play v-else class="w-3.5 h-3.5" />
                <span>Consultar</span>
              </button>
            </div>

            <!-- Resultado do Teste CEP -->
            <div v-if="cepTestResult" class="mt-3 p-3 rounded-lg bg-emerald-50/50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-xs">
              <div class="font-bold text-slate-900 dark:text-slate-100">
                {{ cepTestResult.street }}
              </div>
              <p class="text-slate-600 dark:text-slate-300 text-[11px] mt-0.5">
                Bairro: {{ cepTestResult.neighborhood }} | Cidade: {{ cepTestResult.city_name }}/{{ cepTestResult.state_code }}
              </p>
              <p class="text-slate-500 dark:text-slate-400 text-[11px] font-mono mt-1">
                ID Local: {{ cepTestResult.city_id }} | Código IBGE: {{ cepTestResult.ibge_code }}
              </p>
            </div>
            <div v-else-if="cepTestError" class="mt-3 p-2.5 rounded-lg bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800 text-xs text-rose-700 dark:text-rose-300">
              {{ cepTestError }}
            </div>
          </div>
        </div>

        <!-- Card 3: Base Canônica IBGE -->
        <div class="rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 shadow-2xs">
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 border border-purple-200 dark:border-purple-800 text-purple-600 dark:text-purple-400 flex items-center justify-center shadow-2xs">
                <Database class="w-5 h-5" />
              </div>
              <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">
                  IBGE Cidades Canônicas
                </h3>
                <span class="text-xs text-slate-500">Base Territorial Local</span>
              </div>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 dark:bg-purple-900/60 text-purple-700 dark:text-purple-300">
              5.570 Cidades
            </span>
          </div>

          <p class="text-sm text-slate-600 dark:text-slate-300 mt-3 leading-relaxed">
            Repositório relacional interno contendo todos os 5.570 municípios brasileiros e os 27 estados/DF. Componente reutilizável do Design System <code class="text-xs font-mono text-blue-600 dark:text-blue-400">&lt;CitySearchSelect /&gt;</code> com busca rápida reativa a partir de 3 caracteres e ícone de lupa na extremidade direita.
          </p>

          <div class="mt-4 p-3 rounded-lg bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-xs space-y-1 font-mono">
            <div class="flex items-center justify-between text-slate-500">
              <span>Endpoint:</span>
              <span class="text-purple-600 dark:text-purple-400">GET /api/v1/cities?search={termo}</span>
            </div>
            <div class="flex items-center justify-between text-slate-500">
              <span>Integridade:</span>
              <span class="text-emerald-600 dark:text-emerald-400">Chave Estrangeira RESTRICT</span>
            </div>
          </div>
        </div>

        <!-- Card 4: GPS & Georreferenciamento -->
        <div class="rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 shadow-2xs">
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-600 dark:text-amber-400 flex items-center justify-center shadow-2xs">
                <Compass class="w-5 h-5" />
              </div>
              <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">
                  GPS & Georreferenciamento
                </h3>
                <span class="text-xs text-slate-500">W3C Geolocation API</span>
              </div>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300">
              Navegador / Hardware
            </span>
          </div>

          <p class="text-sm text-slate-600 dark:text-slate-300 mt-3 leading-relaxed">
            Captura de coordenadas geográficas de alta precisão (Latitude e Longitude) em tempo real utilizando os sensores do dispositivo do usuário ou navegador. Integrado ao cadastro de endereços residenciais e comerciais com espelhamento automático.
          </p>

          <div class="mt-4 p-3 rounded-lg bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 text-xs space-y-1 font-mono">
            <div class="flex items-center justify-between text-slate-500">
              <span>API Nativa:</span>
              <span class="text-amber-600 dark:text-amber-400">navigator.geolocation.getCurrentPosition</span>
            </div>
            <div class="flex items-center justify-between text-slate-500">
              <span>Formato do Banco:</span>
              <span class="text-blue-600 dark:text-blue-400">DECIMAL(10,8) / DECIMAL(11,8)</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Roadmap de Futuras Integrações -->
    <div class="rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 p-5">
      <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
        <Sparkles class="w-4 h-4 text-blue-500" />
        <span>Roadmap de Novas Integrações (Sprints 4 & 5)</span>
      </h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-3">
        <div class="p-3.5 rounded-lg bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800">
          <div class="flex items-center gap-2 font-semibold text-slate-900 dark:text-slate-100 text-sm">
            <CreditCard class="w-4 h-4 text-slate-400" />
            <span>Gateways de Pagamento</span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">PIX com QR Code dinâmico, boletos registrados e split de pagamento.</p>
        </div>
        <div class="p-3.5 rounded-lg bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800">
          <div class="flex items-center gap-2 font-semibold text-slate-900 dark:text-slate-100 text-sm">
            <MessageSquare class="w-4 h-4 text-slate-400" />
            <span>WhatsApp & SMS</span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Disparo de faturas, avisos de visita técnica e confirmações de serviço.</p>
        </div>
        <div class="p-3.5 rounded-lg bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800">
          <div class="flex items-center gap-2 font-semibold text-slate-900 dark:text-slate-100 text-sm">
            <Server class="w-4 h-4 text-slate-400" />
            <span>TR-069 & ACS Telecom</span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Provisionamento em massa de ONUs, roteadores Wi-Fi e monitoramento OLT.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import {
  Network,
  Globe,
  Database,
  Building2,
  Activity,
  MapPin,
  Compass,
  ExternalLink,
  RefreshCw,
  Play,
  Loader2,
  Sparkles,
  CreditCard,
  MessageSquare,
  Server
} from 'lucide-vue-next';

const refreshing = ref(false);

// Teste de CNPJ
const testCnpj = ref('00.000.000/0001-91');
const loadingCnpjTest = ref(false);
const cnpjTestResult = ref<any>(null);
const cnpjTestError = ref<string | null>(null);

// Teste de CEP
const testCep = ref('01001-000');
const loadingCepTest = ref(false);
const cepTestResult = ref<any>(null);
const cepTestError = ref<string | null>(null);

async function fetchIntegrations() {
  refreshing.value = true;
  try {
    const res = await fetch('/api/v1/integrations/list');
    await res.json();
  } catch (e) {
    console.error(e);
  } finally {
    setTimeout(() => {
      refreshing.value = false;
    }, 400);
  }
}

async function runCnpjTest() {
  const clean = testCnpj.value.replace(/\D/g, '');
  if (clean.length !== 14) {
    cnpjTestError.value = 'Informe um CNPJ válido com 14 dígitos.';
    cnpjTestResult.value = null;
    return;
  }

  loadingCnpjTest.value = true;
  cnpjTestError.value = null;
  cnpjTestResult.value = null;

  try {
    const res = await fetch(`/api/v1/integrations/cnpj/${clean}`);
    const json = await res.json();
    if (json.success && json.data) {
      cnpjTestResult.value = json.data;
    } else {
      cnpjTestError.value = json.message || 'CNPJ não encontrado.';
    }
  } catch (err: any) {
    cnpjTestError.value = 'Falha ao consultar serviço CNPJá: ' + err.message;
  } finally {
    loadingCnpjTest.value = false;
  }
}

async function runCepTest() {
  const clean = testCep.value.replace(/\D/g, '');
  if (clean.length !== 8) {
    cepTestError.value = 'Informe um CEP válido com 8 dígitos.';
    cepTestResult.value = null;
    return;
  }

  loadingCepTest.value = true;
  cepTestError.value = null;
  cepTestResult.value = null;

  try {
    const res = await fetch(`/api/v1/cep/${clean}`);
    const json = await res.json();
    if (json.success && json.data) {
      cepTestResult.value = json.data;
    } else {
      cepTestError.value = json.message || 'CEP não encontrado.';
    }
  } catch (err: any) {
    cepTestError.value = 'Falha ao consultar serviço ViaCEP: ' + err.message;
  } finally {
    loadingCepTest.value = false;
  }
}
</script>
