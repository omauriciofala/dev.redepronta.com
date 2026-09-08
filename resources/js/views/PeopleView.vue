<template>
  <div class="p-6 max-w-7xl w-full mx-auto space-y-5">
    <!-- Cabeçalho Integrado no Conteúdo (Sem Top Bar) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-lg font-semibold text-slate-900 dark:text-slate-100 tracking-tight">
          Gestão de Pessoas
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400">
          Base canônica centralizada de colaboradores, clientes, fornecedores e solicitantes
        </p>
      </div>

      <button
        @click="openCreateModal"
        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium shadow-xs transition"
      >
        <Plus class="w-3.5 h-3.5" />
        <span>Nova Pessoa</span>
      </button>
    </div>

    <!-- Barra de Filtros Minimalista -->
    <div class="p-3 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 text-xs">
      <!-- Busca Textual -->
      <div class="relative flex-1 min-w-[220px]">
        <Search class="w-3.5 h-3.5 absolute left-2.5 top-2.5 text-slate-400" />
        <input
          type="text"
          v-model="search"
          @input="debounceSearch"
          placeholder="Buscar por nome, CPF/CNPJ, e-mail ou telefone..."
          class="w-full h-8 pl-8 pr-3 rounded border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:border-blue-500 text-xs"
        />
      </div>

      <!-- Filtro por Persona -->
      <div class="flex items-center gap-1 bg-slate-50 dark:bg-slate-950 p-0.5 rounded border border-slate-200 dark:border-slate-800">
        <button
          v-for="p in personaFilters"
          :key="p.value"
          @click="selectPersona(p.value)"
          :class="selectedPersona === p.value ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 font-medium shadow-xs' : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200'"
          class="px-2.5 py-1 rounded text-[11px] transition"
        >
          {{ p.label }}
        </button>
      </div>
    </div>

    <!-- Tabela Minimalista com Linhas Finas -->
    <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-950/40 text-[11px] font-semibold text-slate-500 dark:text-slate-400">
              <th class="py-2.5 px-4">Nome / Razão Social</th>
              <th class="py-2.5 px-4">Tipo & Documento</th>
              <th class="py-2.5 px-4">Papéis (Personas)</th>
              <th class="py-2.5 px-4">Contato</th>
              <th class="py-2.5 px-4">Cidade / UF</th>
              <th class="py-2.5 px-4">Status</th>
              <th class="py-2.5 px-4 text-right">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
            <!-- Loading -->
            <tr v-if="loading">
              <td colspan="7" class="py-8 text-center text-slate-400">
                <span class="inline-block animate-spin mr-2">⟳</span> Carregando registros...
              </td>
            </tr>

            <!-- Vazio -->
            <tr v-else-if="people.length === 0">
              <td colspan="7" class="py-8 text-center text-slate-400">
                Nenhuma pessoa encontrada com os filtros selecionados.
              </td>
            </tr>

            <!-- Linha da Tabela -->
            <tr
              v-else
              v-for="person in people"
              :key="person.id"
              class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors"
            >
              <td class="py-2.5 px-4">
                <div class="font-medium text-slate-800 dark:text-slate-200">{{ person.name }}</div>
                <div v-if="person.trade_name" class="text-[10px] text-slate-400">{{ person.trade_name }}</div>
              </td>

              <td class="py-2.5 px-4 font-mono text-[11px]">
                <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-medium mr-1.5 uppercase"
                      :class="person.person_type === 'individual' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400' : 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-400'">
                  {{ person.person_type === 'individual' ? 'PF' : 'PJ' }}
                </span>
                <span>{{ person.document_number || '-' }}</span>
              </td>

              <td class="py-2.5 px-4">
                <div class="flex flex-wrap gap-1">
                  <span v-if="person.personas?.is_employee" class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-400">
                    Colaborador
                  </span>
                  <span v-if="person.personas?.is_supplier" class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-purple-50 text-purple-700 dark:bg-purple-950/50 dark:text-purple-400">
                    Fornecedor
                  </span>
                  <span v-if="person.personas?.is_client" class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                    Cliente
                  </span>
                  <span v-if="person.personas?.is_requester" class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-cyan-50 text-cyan-700 dark:bg-cyan-950/50 dark:text-cyan-400">
                    Solicitante
                  </span>
                </div>
              </td>

              <td class="py-2.5 px-4 text-slate-600 dark:text-slate-400">
                <div>{{ person.contact?.phone || person.contact?.whatsapp || '-' }}</div>
                <div class="text-[10px] text-slate-400">{{ person.contact?.email || '' }}</div>
              </td>

              <td class="py-2.5 px-4 text-slate-600 dark:text-slate-400">
                {{ person.address?.city?.full_name || '-' }}
              </td>

              <td class="py-2.5 px-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium"
                  :class="person.status === 'active' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                >
                  <span class="w-1 h-1 rounded-full" :class="person.status === 'active' ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                  {{ person.status === 'active' ? 'Ativo' : 'Inativo' }}
                </span>
              </td>

              <td class="py-2.5 px-4 text-right">
                <div class="inline-flex items-center gap-1">
                  <button
                    @click="openEditModal(person)"
                    class="p-1 rounded text-slate-400 hover:text-blue-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                    title="Editar"
                  >
                    <Edit2 class="w-3.5 h-3.5" />
                  </button>
                  <button
                    @click="toggleStatus(person)"
                    class="p-1 rounded text-slate-400 hover:text-amber-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                    :title="person.status === 'active' ? 'Inativar' : 'Ativar'"
                  >
                    <Power class="w-3.5 h-3.5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginação Minimalista -->
      <div class="px-4 py-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
        <div>
          Exibindo <strong>{{ people.length }}</strong> de <strong>{{ totalRecords }}</strong> pessoas
        </div>
        <div class="flex items-center gap-1">
          <button
            :disabled="currentPage <= 1"
            @click="changePage(currentPage - 1)"
            class="px-2 py-1 rounded border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 disabled:opacity-40 transition"
          >
            Anterior
          </button>
          <span class="px-2 font-medium">{{ currentPage }} / {{ lastPage }}</span>
          <button
            :disabled="currentPage >= lastPage"
            @click="changePage(currentPage + 1)"
            class="px-2 py-1 rounded border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 disabled:opacity-40 transition"
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
