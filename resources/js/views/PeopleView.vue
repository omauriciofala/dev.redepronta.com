<template>
  <div class="py-8 w-full space-y-6">
    <!-- Cabeçalho Confortável Integrado (Sem Top Bar) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100 tracking-tight">
          Gestão de Pessoas
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
          Base canônica centralizada de colaboradores, clientes, fornecedores e solicitantes
        </p>
      </div>

      <button
        @click="openCreateModal"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition active:scale-98"
      >
        <Plus class="w-4 h-4" />
        <span>Nova Pessoa</span>
      </button>
    </div>

    <!-- Barra de Filtros com Fontes Confortáveis -->
    <div class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4 text-sm shadow-2xs transition-colors duration-200">
      <!-- Busca Textual -->
      <div class="relative flex-1 min-w-[280px]">
        <Search class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400" />
        <input
          type="text"
          v-model="search"
          @input="debounceSearch"
          placeholder="Buscar por nome, CPF/CNPJ, e-mail ou telefone..."
          class="w-full h-11 pl-10 pr-4 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-blue-500 text-sm transition"
        />
      </div>

      <!-- Filtro por Persona (Tabs) -->
      <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-950 p-1 rounded-lg border border-slate-200 dark:border-slate-800 overflow-x-auto max-w-full">
        <button
          v-for="p in personaFilters"
          :key="p.value"
          @click="selectPersona(p.value)"
          :class="selectedPersona === p.value ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 font-semibold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'"
          class="px-3 py-1.5 rounded-md text-xs font-medium transition whitespace-nowrap"
        >
          {{ p.label }}
        </button>
      </div>
    </div>

    <!-- Tabela Confortável com Linhas Finas -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs transition-colors duration-200">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
          <thead>
            <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-950/50 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
              <th class="py-3.5 px-5">Nome / Razão Social</th>
              <th class="py-3.5 px-5">Tipo & Documento</th>
              <th class="py-3.5 px-5">Papéis (Personas)</th>
              <th class="py-3.5 px-5">Ações Rápidas de Contato</th>
              <th class="py-3.5 px-5">Cidade / UF</th>
              <th class="py-3.5 px-5">Status</th>
              <th class="py-3.5 px-5 text-right">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
            <!-- Loading -->
            <tr v-if="loading">
              <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                <span class="inline-block animate-spin mr-2">⟳</span> Carregando registros...
              </td>
            </tr>

            <!-- Vazio -->
            <tr v-else-if="people.length === 0">
              <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                Nenhuma pessoa encontrada com os filtros selecionados.
              </td>
            </tr>

            <!-- Linha da Tabela -->
            <tr
              v-else
              v-for="person in people"
              :key="person.id"
              class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors"
            >
              <!-- Nome / Razão Social -->
              <td class="py-4 px-5">
                <div class="font-semibold text-slate-900 dark:text-slate-100 text-sm flex items-center gap-1.5">
                  <span>{{ person.name }}</span>
                </div>
                <div v-if="person.trade_name" class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                  {{ person.trade_name }}
                </div>
                <div v-if="person.group_name && person.group_name !== 'Geral'" class="text-[11px] font-medium text-blue-600 dark:text-blue-400 mt-0.5">
                  Grupo: {{ person.group_name }}
                </div>
              </td>

              <!-- Tipo & Documento com Máscara Dinâmica e Cópia em 1 Clique -->
              <td class="py-4 px-5">
                <div class="flex items-center gap-2">
                  <span
                    class="inline-block px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider border"
                    :class="person.person_type === 'individual'
                      ? 'bg-amber-50 text-amber-700 border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60'
                      : 'bg-indigo-50 text-indigo-700 border-indigo-200/80 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800/60'"
                  >
                    {{ person.person_type === 'individual' ? 'PF' : 'PJ' }}
                  </span>

                  <span class="font-mono text-xs font-medium text-slate-700 dark:text-slate-200 select-all">
                    {{ formatDocument(person.document_number, person.person_type) }}
                  </span>

                  <!-- Botão discreto de cópia rápida com 1 clique -->
                  <button
                    v-if="person.document_number"
                    @click="copyToClipboard(person.document_number, 'doc-' + person.id)"
                    class="p-1 rounded text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition relative group"
                    :title="copiedKey === 'doc-' + person.id ? 'Copiado!' : 'Copiar documento'"
                  >
                    <Check v-if="copiedKey === 'doc-' + person.id" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                    <Copy v-else class="w-3.5 h-3.5" />

                    <!-- Tooltip temporário -->
                    <span
                      v-if="copiedKey === 'doc-' + person.id"
                      class="absolute -top-7 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded bg-slate-900 text-white text-[10px] font-medium shadow-md whitespace-nowrap z-20 pointer-events-none"
                    >
                      Copiado!
                    </span>
                  </button>
                </div>
              </td>

              <!-- Badges de Papéis (Personas) com Paleta Semântica e Prevenção de Sobrecarga -->
              <td class="py-4 px-5">
                <div class="flex flex-wrap items-center gap-1.5 max-w-[260px]">
                  <!-- Exibe os 2 primeiros papéis ativos -->
                  <span
                    v-for="role in getVisibleRoles(person)"
                    :key="role.key"
                    :class="role.badgeClass"
                    class="px-2 py-0.5 rounded text-[11px] font-semibold border shadow-2xs"
                  >
                    {{ role.label }}
                  </span>

                  <!-- Chip expansível para o 3º papel em diante evitando sobrecarga visual -->
                  <div
                    v-if="getHiddenRoles(person).length > 0"
                    class="relative"
                  >
                    <button
                      @click="toggleExpandRoles(person.id)"
                      class="px-1.5 py-0.5 rounded text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 transition flex items-center gap-0.5 shadow-2xs"
                      :title="isRolesExpanded(person.id) ? 'Recolher papéis' : 'Ver todos: ' + getHiddenRoles(person).map(r => r.label).join(', ')"
                    >
                      <span>{{ isRolesExpanded(person.id) ? '−' : `+${getHiddenRoles(person).length}` }}</span>
                    </button>

                    <!-- Papéis extras expandidos inline se clicado -->
                    <div
                      v-if="isRolesExpanded(person.id)"
                      class="flex flex-wrap gap-1.5 mt-1.5"
                    >
                      <span
                        v-for="role in getHiddenRoles(person)"
                        :key="role.key"
                        :class="role.badgeClass"
                        class="px-2 py-0.5 rounded text-[11px] font-semibold border shadow-2xs animate-fadeIn"
                      >
                        {{ role.label }}
                      </span>
                    </div>
                  </div>

                  <!-- Fallback caso não possua nenhum papel selecionado -->
                  <span
                    v-if="getAllRoles(person).length === 0"
                    class="text-xs text-slate-400 italic"
                  >
                    Sem papéis
                  </span>
                </div>
              </td>

              <!-- Ações Rápidas de Contato (Links Operacionais Diretos) -->
              <td class="py-4 px-5 text-sm">
                <div class="space-y-1">
                  <!-- Telefone & WhatsApp Operacionais -->
                  <div v-if="person.contact?.phone || person.contact?.whatsapp" class="flex items-center gap-2">
                    <!-- Link de Discagem Direta (tel:) -->
                    <a
                      :href="'tel:+55' + sanitizePhone(person.contact?.phone || person.contact?.whatsapp)"
                      class="inline-flex items-center gap-1 text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium text-xs transition group"
                      title="Ligar pelo discador"
                    >
                      <Phone class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-500" />
                      <span>{{ formatPhone(person.contact?.phone || person.contact?.whatsapp) }}</span>
                    </a>

                    <!-- Link direto para WhatsApp -->
                    <a
                      :href="'https://wa.me/55' + sanitizePhone(person.contact?.whatsapp || person.contact?.phone)"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="p-1 rounded text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-950/60 transition"
                      title="Abrir conversa no WhatsApp"
                    >
                      <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-10.416c-4.412 0-8 3.588-8 8 0 1.411.367 2.736 1.009 3.886l-1.072 3.921 4.024-1.055c1.112.607 2.384.948 3.739.948 4.412 0 8-3.588 8-8s-3.588-8-8-8z"/>
                      </svg>
                    </a>

                    <!-- Botão de Cópia de Telefone -->
                    <button
                      @click="copyToClipboard(person.contact?.phone || person.contact?.whatsapp, 'phone-' + person.id)"
                      class="p-0.5 rounded text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition"
                      title="Copiar telefone"
                    >
                      <Check v-if="copiedKey === 'phone-' + person.id" class="w-3 h-3 text-emerald-600 dark:text-emerald-400" />
                      <Copy v-else class="w-3 h-3" />
                    </button>
                  </div>
                  <div v-else class="text-xs text-slate-400 italic">Sem telefone</div>

                  <!-- E-mail Operacional (mailto:) -->
                  <div v-if="person.contact?.email" class="flex items-center gap-1.5">
                    <a
                      :href="'mailto:' + person.contact?.email"
                      class="inline-flex items-center gap-1 text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 text-xs transition truncate max-w-[190px] group"
                      :title="'Enviar e-mail para ' + person.contact?.email"
                    >
                      <Mail class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-500 flex-shrink-0" />
                      <span class="truncate">{{ person.contact?.email }}</span>
                    </a>

                    <!-- Botão de Cópia de E-mail -->
                    <button
                      @click="copyToClipboard(person.contact?.email, 'email-' + person.id)"
                      class="p-0.5 rounded text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition flex-shrink-0"
                      title="Copiar e-mail"
                    >
                      <Check v-if="copiedKey === 'email-' + person.id" class="w-3 h-3 text-emerald-600 dark:text-emerald-400" />
                      <Copy v-else class="w-3 h-3" />
                    </button>
                  </div>
                </div>
              </td>

              <!-- Cidade / UF -->
              <td class="py-4 px-5 text-sm font-medium text-slate-700 dark:text-slate-300">
                <div>{{ person.residential_address?.city_name || person.address?.city_name || person.address?.city?.name || '-' }}</div>
                <div v-if="person.residential_address?.state_code || person.address?.state_code" class="text-xs text-slate-400">
                  {{ person.residential_address?.state_code || person.address?.state_code }}
                </div>
              </td>

              <!-- Status -->
              <td class="py-4 px-5">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                  :class="person.status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-400'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="person.status === 'active' ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                  {{ person.status === 'active' ? 'Ativo' : 'Inativo' }}
                </span>
              </td>

              <!-- Ações -->
              <td class="py-4 px-5 text-right">
                <div class="inline-flex items-center gap-1.5">
                  <button
                    @click="openEditModal(person)"
                    class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-slate-800 transition"
                    title="Editar"
                  >
                    <Edit2 class="w-4 h-4" />
                  </button>
                  <button
                    @click="toggleStatus(person)"
                    class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-slate-800 transition"
                    :title="person.status === 'active' ? 'Inativar' : 'Ativar'"
                  >
                    <Power class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginação Confortável -->
      <div class="px-5 py-3.5 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-900/50">
        <div>
          Exibindo <strong>{{ people.length }}</strong> de <strong>{{ totalRecords }}</strong> pessoas
        </div>
        <div class="flex items-center gap-1.5">
          <button
            :disabled="currentPage <= 1"
            @click="changePage(currentPage - 1)"
            class="px-3 py-1.5 rounded-md border border-slate-200 dark:border-slate-800 hover:bg-white dark:hover:bg-slate-800 disabled:opacity-40 font-medium transition"
          >
            Anterior
          </button>
          <span class="px-3 font-semibold text-slate-800 dark:text-slate-200">{{ currentPage }} / {{ lastPage }}</span>
          <button
            :disabled="currentPage >= lastPage"
            @click="changePage(currentPage + 1)"
            class="px-3 py-1.5 rounded-md border border-slate-200 dark:border-slate-800 hover:bg-white dark:hover:bg-slate-800 disabled:opacity-40 font-medium transition"
          >
            Próxima
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Cadastro/Edição -->
    <PersonModal
      :isOpen="isModalOpen"
      :personToEdit="personEditing"
      @close="isModalOpen = false"
      @saved="fetchPeople"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Plus, Search, Edit2, Power, Copy, Check, Phone, Mail } from 'lucide-vue-next';
import axios from 'axios';
import PersonModal from '../components/people/PersonModal.vue';

const people = ref<any[]>([]);
const loading = ref(false);
const search = ref('');
const selectedPersona = ref('');
const currentPage = ref(1);
const lastPage = ref(1);
const totalRecords = ref(0);

const isModalOpen = ref(false);
const personEditing = ref<any | null>(null);

// Estado para controle de cópia rápida
const copiedKey = ref<string | null>(null);

// Controle de expansão de papéis para evitar sobrecarga visual
const expandedRolesMap = ref<Record<number, boolean>>({});

const isRolesExpanded = (personId: number) => {
  return !!expandedRolesMap.value[personId];
};

const toggleExpandRoles = (personId: number) => {
  expandedRolesMap.value[personId] = !expandedRolesMap.value[personId];
};

// Paleta semântica estrita de papéis (Personas)
const ROLE_CONFIGS = [
  {
    key: 'client',
    personaKey: 'is_client',
    label: 'Cliente',
    badgeClass: 'bg-emerald-50 text-emerald-700 border-emerald-200/80 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/60',
  },
  {
    key: 'supplier',
    personaKey: 'is_supplier',
    label: 'Fornecedor',
    badgeClass: 'bg-purple-50 text-purple-700 border-purple-200/80 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800/60',
  },
  {
    key: 'employee',
    personaKey: 'is_employee',
    label: 'Colaborador',
    badgeClass: 'bg-blue-50 text-blue-700 border-blue-200/80 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800/60',
  },
  {
    key: 'outsourced',
    personaKey: 'is_outsourced',
    label: 'Terceirizado',
    badgeClass: 'bg-amber-50 text-amber-700 border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/60',
  },
  {
    key: 'seller',
    personaKey: 'is_seller',
    label: 'Vendedor',
    badgeClass: 'bg-cyan-50 text-cyan-700 border-cyan-200/80 dark:bg-cyan-950/40 dark:text-cyan-300 dark:border-cyan-800/60',
  },
  {
    key: 'driver',
    personaKey: 'is_driver',
    label: 'Motorista',
    badgeClass: 'bg-indigo-50 text-indigo-700 border-indigo-200/80 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800/60',
  },
  {
    key: 'carrier',
    personaKey: 'is_carrier',
    label: 'Transportadora',
    badgeClass: 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200/80 dark:bg-fuchsia-950/40 dark:text-fuchsia-300 dark:border-fuchsia-800/60',
  },
  {
    key: 'requester',
    personaKey: 'is_requester',
    label: 'Solicitante',
    badgeClass: 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
  },
];

const getAllRoles = (person: any) => {
  return ROLE_CONFIGS.filter(cfg => {
    return !!(person.roles?.[cfg.key] || person.personas?.[cfg.personaKey]);
  });
};

const getVisibleRoles = (person: any) => {
  const all = getAllRoles(person);
  return all.slice(0, 2);
};

const getHiddenRoles = (person: any) => {
  const all = getAllRoles(person);
  return all.slice(2);
};

// Função de formatação dinâmica com máscara para CPF e CNPJ
const formatDocument = (doc: string | null | undefined, type: string) => {
  if (!doc) return '-';
  const clean = doc.replace(/\D/g, '');
  if (clean.length === 11) {
    return clean.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
  }
  if (clean.length === 14) {
    return clean.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
  }
  return doc;
};

// Formatação e sanitização de telefone
const sanitizePhone = (phone: string | null | undefined) => {
  return (phone || '').replace(/\D/g, '');
};

const formatPhone = (phone: string | null | undefined) => {
  if (!phone) return '-';
  const clean = sanitizePhone(phone);
  if (clean.length === 11) {
    return clean.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
  }
  if (clean.length === 10) {
    return clean.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
  }
  return phone;
};

// Cópia rápida com 1 clique e feedback visual
const copyToClipboard = async (text: string, key: string) => {
  if (!text) return;
  try {
    await navigator.clipboard.writeText(text);
    copiedKey.value = key;
    setTimeout(() => {
      if (copiedKey.value === key) {
        copiedKey.value = null;
      }
    }, 2000);
  } catch (err) {
    console.error('Erro ao copiar para a área de transferência', err);
  }
};

const personaFilters = [
  { label: 'Todos', value: '' },
  { label: 'Clientes', value: 'client' },
  { label: 'Fornecedores', value: 'supplier' },
  { label: 'Colaboradores', value: 'employee' },
  { label: 'Terceirizados', value: 'outsourced' },
  { label: 'Vendedores', value: 'seller' },
  { label: 'Motoristas', value: 'driver' },
  { label: 'Transportadoras', value: 'carrier' },
];

let searchTimeout: any = null;
const debounceSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentPage.value = 1;
    fetchPeople();
  }, 300);
};

const selectPersona = (val: string) => {
  selectedPersona.value = val;
  currentPage.value = 1;
  fetchPeople();
};

const changePage = (page: number) => {
  currentPage.value = page;
  fetchPeople();
};

const fetchPeople = async () => {
  loading.value = true;
  try {
    const params: any = {
      page: currentPage.value,
      per_page: 15,
    };
    if (search.value) params.search = search.value;
    if (selectedPersona.value) params.persona = selectedPersona.value;

    const res = await axios.get('/api/v1/people', { params });
    people.value = res.data.data;
    currentPage.value = res.data.meta.current_page;
    lastPage.value = res.data.meta.last_page;
    totalRecords.value = res.data.meta.total;
  } catch (err) {
    console.error('Erro ao buscar pessoas', err);
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  personEditing.value = null;
  isModalOpen.value = true;
};

const openEditModal = (person: any) => {
  personEditing.value = person;
  isModalOpen.value = true;
};

const toggleStatus = async (person: any) => {
  try {
    const res = await axios.patch(`/api/v1/people/${person.id}/toggle-status`);
    person.status = res.data.data.status;
  } catch (err) {
    console.error('Erro ao alterar status', err);
  }
};

onMounted(() => {
  fetchPeople();
});
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-2px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.15s ease-out forwards;
}
</style>
