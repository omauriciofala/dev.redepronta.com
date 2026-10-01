<template>
  <div class="space-y-6">
    <!-- Header Informativo do Catálogo OpenAPI -->
    <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-4">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <h2 class="text-base font-bold font-heading text-slate-900 dark:text-slate-100">
              Catálogo Interativo da API v1
            </h2>
            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
              OpenAPI 3.1.0
            </span>
            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
              RESTful
            </span>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Endpoints oficiais, contratos de dados, parâmetros e payloads para integração do ERP Rede Pronta.
          </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
          <button
            type="button"
            @click="copySpecUrl"
            class="h-9 px-3 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition flex items-center gap-1.5 cursor-pointer shadow-2xs"
            title="Copiar URL da especificação JSON"
          >
            <Check v-if="copiedSpecUrl" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
            <Copy v-else class="w-3.5 h-3.5 text-slate-400" />
            <span>{{ copiedSpecUrl ? 'URL Copiada!' : 'Copiar URL Spec' }}</span>
          </button>

          <a
            href="/api/v1/docs/openapi.json"
            download="redepronta_openapi_v1.json"
            class="h-9 px-3.5 text-xs font-semibold rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white transition flex items-center gap-1.5 shadow-xs cursor-pointer"
            title="Baixar arquivo JSON completo da especificação para Postman ou Insomnia"
          >
            <FileDown class="w-3.5 h-3.5" />
            <span>Baixar openapi.json</span>
          </a>
        </div>
      </div>

      <!-- Métricas dos Endpoints -->
      <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 pt-2 border-t border-slate-100 dark:border-slate-800 text-center">
        <div class="p-2.5 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
          <div class="text-xs text-slate-400 font-medium">Total de Endpoints</div>
          <div class="text-lg font-bold font-heading text-slate-900 dark:text-slate-100 mt-0.5">{{ totalEndpoints }}</div>
        </div>
        <div class="p-2.5 rounded-xl bg-blue-50/50 dark:bg-blue-950/20 border border-blue-100/60 dark:border-blue-900/40">
          <div class="text-xs text-blue-600 dark:text-blue-400 font-medium">GET (Consultas)</div>
          <div class="text-lg font-bold font-heading text-blue-700 dark:text-blue-300 mt-0.5">{{ methodCounts.GET || 0 }}</div>
        </div>
        <div class="p-2.5 rounded-xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-100/60 dark:border-emerald-900/40">
          <div class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">POST (Criações)</div>
          <div class="text-lg font-bold font-heading text-emerald-700 dark:text-emerald-300 mt-0.5">{{ methodCounts.POST || 0 }}</div>
        </div>
        <div class="p-2.5 rounded-xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-100/60 dark:border-purple-900/40">
          <div class="text-xs text-purple-600 dark:text-purple-400 font-medium">PUT / PATCH (Edição)</div>
          <div class="text-lg font-bold font-heading text-purple-700 dark:text-purple-300 mt-0.5">{{ (methodCounts.PUT || 0) + (methodCounts.PATCH || 0) }}</div>
        </div>
        <div class="p-2.5 rounded-xl bg-rose-50/50 dark:bg-rose-950/20 border border-rose-100/60 dark:border-rose-900/40">
          <div class="text-xs text-rose-600 dark:text-rose-400 font-medium">DELETE (Exclusões)</div>
          <div class="text-lg font-bold font-heading text-rose-700 dark:text-rose-300 mt-0.5">{{ methodCounts.DELETE || 0 }}</div>
        </div>
      </div>
    </div>

    <!-- Barra de Filtros e Busca Rápida -->
    <div class="p-3.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-2xs space-y-3">
      <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <!-- Campo de Busca -->
        <div class="flex-1 relative">
          <Search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Buscar por rota, método, termo ou descrição (ex: /users, movement, login)..."
            class="w-full pl-9 pr-8 py-2 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-xs focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
          />
          <button
            v-if="searchTerm"
            type="button"
            @click="searchTerm = ''"
            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer"
          >
            <X class="w-3.5 h-3.5" />
          </button>
        </div>

        <!-- Seletor de Método HTTP -->
        <div class="flex items-center gap-1.5 shrink-0">
          <span class="text-xs text-slate-400 font-medium mr-1">Método:</span>
          <button
            v-for="method in ['ALL', 'GET', 'POST', 'PUT', 'PATCH', 'DELETE']"
            :key="method"
            type="button"
            @click="selectedMethod = method"
            :class="selectedMethod === method
              ? 'bg-[#FC6714] text-white font-bold shadow-2xs'
              : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
            class="px-2.5 py-1 text-[11px] font-semibold rounded-md transition cursor-pointer"
          >
            {{ method }}
          </button>
        </div>
      </div>

      <!-- Filtro por Tags / Módulos -->
      <div class="flex items-center gap-1.5 overflow-x-auto pb-1 pt-1 text-xs select-none">
        <button
          type="button"
          @click="selectedTag = 'ALL'"
          :class="selectedTag === 'ALL'
            ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 font-bold'
            : 'bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
          class="px-3 py-1 rounded-md transition whitespace-nowrap cursor-pointer shrink-0"
        >
          Todos os Módulos ({{ flatEndpoints.length }})
        </button>

        <button
          v-for="tag in availableTags"
          :key="tag"
          type="button"
          @click="selectedTag = tag"
          :class="selectedTag === tag
            ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 font-bold'
            : 'bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'"
          class="px-3 py-1 rounded-md transition whitespace-nowrap cursor-pointer shrink-0"
        >
          {{ tag }} ({{ countByTag(tag) }})
        </button>
      </div>
    </div>

    <!-- Lista de Endpoints Interativa -->
    <div class="space-y-3">
      <div
        v-if="filteredEndpoints.length === 0"
        class="p-8 text-center bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 space-y-2"
      >
        <AlertCircle class="w-8 h-8 text-slate-400 mx-auto" />
        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Nenhum endpoint localizado</p>
        <p class="text-xs text-slate-400">Tente ajustar o termo de busca ou o método HTTP selecionado.</p>
      </div>

      <div
        v-for="ep in filteredEndpoints"
        :key="ep.method + ep.path"
        class="rounded-xl bg-white dark:bg-slate-900 border transition shadow-2xs overflow-hidden"
        :class="expandedKey === (ep.method + ep.path)
          ? 'border-indigo-300 dark:border-indigo-800 ring-1 ring-indigo-500/20'
          : 'border-slate-200/90 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700'"
      >
        <!-- Linha do Endpoint (Header do Accordion) -->
        <div
          @click="toggleExpand(ep.method + ep.path)"
          class="p-3.5 flex items-center justify-between gap-3 cursor-pointer select-none"
        >
          <div class="flex items-center gap-3 min-w-0 flex-1">
            <!-- Badge do Método HTTP -->
            <span
              class="w-16 text-center py-1 text-xs font-bold rounded-md uppercase tracking-wider shrink-0 border"
              :class="getMethodBadgeClass(ep.method)"
            >
              {{ ep.method }}
            </span>

            <!-- Caminho da Rota -->
            <span class="font-mono text-xs font-semibold text-slate-900 dark:text-slate-100 truncate">
              <span class="text-slate-400 dark:text-slate-500">/api/v1</span>{{ ep.path }}
            </span>

            <!-- Resumo / Título do Endpoint -->
            <span class="text-xs text-slate-500 dark:text-slate-400 hidden sm:inline truncate">
              — {{ ep.summary }}
            </span>
          </div>

          <div class="flex items-center gap-2 shrink-0">
            <!-- Tag do Módulo -->
            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hidden md:inline">
              {{ ep.tags?.[0] || 'API' }}
            </span>

            <ChevronDown
              class="w-4 h-4 text-slate-400 transition transform duration-200"
              :class="{ 'rotate-180': expandedKey === (ep.method + ep.path) }"
            />
          </div>
        </div>

        <!-- Conteúdo Detalhado do Endpoint (Expandido) -->
        <div
          v-if="expandedKey === (ep.method + ep.path)"
          class="p-4 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-950/40 space-y-4 text-xs"
        >
          <!-- Descrição -->
          <div v-if="ep.description" class="text-slate-600 dark:text-slate-300 leading-relaxed">
            {{ ep.description }}
          </div>

          <!-- Parâmetros de Entrada -->
          <div v-if="ep.parameters && ep.parameters.length > 0" class="space-y-2">
            <h4 class="font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wide text-[11px] flex items-center gap-1.5">
              <span>Parâmetros</span>
              <span class="text-slate-400 font-normal">({{ ep.parameters.length }})</span>
            </h4>
            <div class="rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden">
              <table class="w-full text-left divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-[10px] font-bold uppercase text-slate-400">
                  <tr>
                    <th class="px-3 py-2">Nome</th>
                    <th class="px-3 py-2">Tipo</th>
                    <th class="px-3 py-2">Local</th>
                    <th class="px-3 py-2">Obrigatório</th>
                    <th class="px-3 py-2">Descrição</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                  <tr v-for="param in ep.parameters" :key="param.name">
                    <td class="px-3 py-2 font-mono font-semibold text-indigo-600 dark:text-indigo-400">{{ param.name }}</td>
                    <td class="px-3 py-2 text-slate-500 font-mono text-[11px]">{{ param.schema?.type || 'string' }}</td>
                    <td class="px-3 py-2 text-slate-500">{{ param.in }}</td>
                    <td class="px-3 py-2">
                      <span
                        class="px-1.5 py-0.5 rounded text-[10px] font-bold"
                        :class="param.required ? 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'"
                      >
                        {{ param.required ? 'Sim' : 'Não' }}
                      </span>
                    </td>
                    <td class="px-3 py-2 text-slate-600 dark:text-slate-400">{{ param.description || '—' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Corpo da Requisição (Request Body) -->
          <div v-if="ep.requestBody" class="space-y-2">
            <div class="flex items-center justify-between">
              <h4 class="font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wide text-[11px]">
                Payload da Requisição (JSON)
              </h4>
              <button
                type="button"
                @click="copyPayload(ep.requestBody.content?.['application/json']?.example)"
                class="text-[11px] text-[#FC6714] hover:underline flex items-center gap-1 cursor-pointer font-semibold"
              >
                <Copy class="w-3 h-3" />
                <span>Copiar Payload</span>
              </button>
            </div>
            <pre class="p-3 rounded-lg bg-slate-900 text-slate-100 font-mono text-[11px] overflow-x-auto border border-slate-800"><code>{{ formatJson(ep.requestBody.content?.['application/json']?.example) }}</code></pre>
          </div>

          <!-- Respostas Esperadas -->
          <div v-if="ep.responses" class="space-y-2">
            <h4 class="font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wide text-[11px]">
              Respostas HTTP
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <div
                v-for="(resp, code) in ep.responses"
                :key="code"
                class="p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center gap-2.5"
              >
                <span
                  class="px-2 py-0.5 rounded text-[11px] font-bold font-mono"
                  :class="getStatusCodeClass(code)"
                >
                  {{ code }}
                </span>
                <span class="text-slate-600 dark:text-slate-300 text-xs">{{ resp.description }}</span>
              </div>
            </div>
          </div>

          <!-- Exemplo cURL -->
          <div class="space-y-2 pt-1 border-t border-slate-200/60 dark:border-slate-800">
            <div class="flex items-center justify-between">
              <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Comando cURL de Exemplo:</span>
              <button
                type="button"
                @click="copyCurl(ep)"
                class="text-[11px] text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 cursor-pointer font-semibold"
              >
                <Copy class="w-3 h-3" />
                <span>Copiar cURL</span>
              </button>
            </div>
            <pre class="p-2.5 rounded-lg bg-slate-900 text-emerald-400 font-mono text-[11px] overflow-x-auto border border-slate-800"><code>{{ generateCurl(ep) }}</code></pre>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import {
  Search,
  X,
  Copy,
  Check,
  FileDown,
  ChevronDown,
  AlertCircle,
} from 'lucide-vue-next';

interface EndpointItem {
  path: string;
  method: string;
  summary: string;
  description?: string;
  tags?: string[];
  parameters?: any[];
  requestBody?: any;
  responses?: Record<string, { description: string }>;
}

const props = defineProps<{
  spec: {
    openapi?: string;
    info?: { title: string; version: string; description: string };
    paths?: Record<string, Record<string, any>>;
    tags?: Array<{ name: string; description: string }>;
  };
}>();

const searchTerm = ref('');
const selectedMethod = ref('ALL');
const selectedTag = ref('ALL');
const expandedKey = ref<string | null>(null);
const copiedSpecUrl = ref(false);

const flatEndpoints = computed<EndpointItem[]>(() => {
  if (!props.spec?.paths) return [];

  const items: EndpointItem[] = [];
  for (const [path, methods] of Object.entries(props.spec.paths)) {
    for (const [method, details] of Object.entries(methods)) {
      items.push({
        path,
        method: method.toUpperCase(),
        summary: details.summary || '',
        description: details.description || '',
        tags: details.tags || [],
        parameters: details.parameters || [],
        requestBody: details.requestBody || null,
        responses: details.responses || {},
      });
    }
  }
  return items;
});

const totalEndpoints = computed(() => flatEndpoints.value.length);

const availableTags = computed(() => {
  const set = new Set<string>();
  flatEndpoints.value.forEach((ep) => {
    ep.tags?.forEach((t) => set.add(t));
  });
  return Array.from(set);
});

const methodCounts = computed(() => {
  const counts: Record<string, number> = {};
  flatEndpoints.value.forEach((ep) => {
    counts[ep.method] = (counts[ep.method] || 0) + 1;
  });
  return counts;
});

function countByTag(tag: string): number {
  return flatEndpoints.value.filter((ep) => ep.tags?.includes(tag)).length;
}

const filteredEndpoints = computed(() => {
  const term = searchTerm.value.trim().toLowerCase();
  const method = selectedMethod.value;
  const tag = selectedTag.value;

  return flatEndpoints.value.filter((ep) => {
    if (method !== 'ALL' && ep.method !== method) {
      return false;
    }
    if (tag !== 'ALL' && !ep.tags?.includes(tag)) {
      return false;
    }
    if (term) {
      const matchPath = ep.path.toLowerCase().includes(term);
      const matchSummary = ep.summary.toLowerCase().includes(term);
      const matchDesc = ep.description?.toLowerCase().includes(term) ?? false;
      const matchMethod = ep.method.toLowerCase().includes(term);
      return matchPath || matchSummary || matchDesc || matchMethod;
    }
    return true;
  });
});

function toggleExpand(key: string) {
  expandedKey.value = expandedKey.value === key ? null : key;
}

function getMethodBadgeClass(method: string): string {
  switch (method) {
    case 'GET':
      return 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-900';
    case 'POST':
      return 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-900';
    case 'PUT':
      return 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-900';
    case 'PATCH':
      return 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-900';
    case 'DELETE':
      return 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-900';
    default:
      return 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700';
  }
}

function getStatusCodeClass(code: string): string {
  if (code.startsWith('2')) {
    return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
  }
  if (code.startsWith('4')) {
    return 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300';
  }
  if (code.startsWith('5')) {
    return 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300';
  }
  return 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300';
}

function formatJson(data: any): string {
  if (!data) return '{}';
  return JSON.stringify(data, null, 2);
}

function copyPayload(data: any) {
  if (!data) return;
  navigator.clipboard.writeText(JSON.stringify(data, null, 2));
}

function generateCurl(ep: EndpointItem): string {
  const origin = window.location.origin;
  const url = `${origin}/api/v1${ep.path}`;
  let cmd = `curl -X ${ep.method} "${url}" \\\n  -H "Accept: application/json" \\\n  -H "Authorization: Bearer <SEU_TOKEN>"`;

  if (ep.requestBody?.content?.['application/json']?.example) {
    const jsonStr = JSON.stringify(ep.requestBody.content['application/json'].example);
    cmd += ` \\\n  -H "Content-Type: application/json" \\\n  -d '${jsonStr}'`;
  }
  return cmd;
}

function copyCurl(ep: EndpointItem) {
  navigator.clipboard.writeText(generateCurl(ep));
}

function copySpecUrl() {
  const url = `${window.location.origin}/api/v1/docs/openapi.json`;
  navigator.clipboard.writeText(url);
  copiedSpecUrl.value = true;
  setTimeout(() => {
    copiedSpecUrl.value = false;
  }, 2500);
}
</script>
