<template>
  <div class="py-8 w-full max-w-7xl space-y-6">
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
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition"
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
      <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-950 p-1 rounded-lg border border-slate-200 dark:border-slate-800">
        <button
          v-for="p in personaFilters"
          :key="p.value"
          @click="selectPersona(p.value)"
          :class="selectedPersona === p.value ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 font-semibold shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200'"
          class="px-3.5 py-1.5 rounded-md text-xs font-medium transition"
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
              <th class="py-3.5 px-5">Contato</th>
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
              <td class="py-4 px-5">
                <div class="font-semibold text-slate-900 dark:text-slate-100 text-sm">{{ person.name }}</div>
                <div v-if="person.trade_name" class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ person.trade_name }}</div>
              </td>

              <td class="py-4 px-5 font-mono text-xs">
                <span class="inline-block px-2 py-0.5 rounded text-xs font-bold mr-2 uppercase"
                      :class="person.person_type === 'individual' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-400'">
                  {{ person.person_type === 'individual' ? 'PF' : 'PJ' }}
                </span>
                <span class="text-slate-700 dark:text-slate-300">{{ person.document_number || '-' }}</span>
              </td>

              <td class="py-4 px-5">
                <div class="flex flex-wrap gap-1.5">
                  <span v-if="person.personas?.is_employee" class="px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-950/70 dark:text-blue-300">
                    Colaborador
                  </span>
                  <span v-if="person.personas?.is_supplier" class="px-2 py-0.5 rounded-md text-xs font-semibold bg-purple-100 text-purple-800 dark:bg-purple-950/70 dark:text-purple-300">
                    Fornecedor
                  </span>
                  <span v-if="person.personas?.is_client" class="px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300">
                    Cliente
                  </span>
                  <span v-if="person.personas?.is_requester" class="px-2 py-0.5 rounded-md text-xs font-semibold bg-cyan-100 text-cyan-800 dark:bg-cyan-950/70 dark:text-cyan-300">
                    Solicitante
                  </span>
                </div>
              </td>

              <td class="py-4 px-5 text-sm text-slate-700 dark:text-slate-300">
                <div class="font-medium">{{ person.contact?.phone || person.contact?.whatsapp || '-' }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">{{ person.contact?.email || '' }}</div>
              </td>

              <td class="py-4 px-5 text-sm font-medium text-slate-700 dark:text-slate-300">
                {{ person.address?.city?.full_name || '-' }}
              </td>

              <td class="py-4 px-5">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                  :class="person.status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-400'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="person.status === 'active' ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                  {{ person.status === 'active' ? 'Ativo' : 'Inativo' }}
                </span>
              </td>

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
import { Plus, Search, Edit2, Power } from 'lucide-vue-next';
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

const personaFilters = [
  { label: 'Todos', value: '' },
  { label: 'Colaboradores', value: 'employee' },
  { label: 'Fornecedores', value: 'supplier' },
  { label: 'Clientes', value: 'client' },
  { label: 'Solicitantes', value: 'requester' },
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
