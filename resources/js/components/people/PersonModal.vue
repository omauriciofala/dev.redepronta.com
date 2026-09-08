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
              class="h-9 px-3 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100"
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
              {{ form.person_type === 'individual' ? 'RG / Órgão Emissor' : 'Inscrição Estadual (IE)' }}
            </label>
            <input
              type="text"
              v-model="form.rg_ie"
              placeholder="Número de registro..."
              class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden font-mono text-sm transition"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              {{ form.person_type === 'individual' ? 'Data de Nascimento' : 'Data de Fundação' }}
            </label>
            <input
              type="date"
              v-model="form.birth_date"
              class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden text-sm transition"
            />
          </div>

          <div v-if="form.person_type === 'individual'">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Gênero</label>
            <select
              v-model="form.gender_id"
              class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden text-sm transition"
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

      <!-- ABA 3: ENDEREÇO & LOCALIZAÇÃO CANÔNICA -->
      <div v-show="activeTab === 'endereco'" class="space-y-6">
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
              placeholder="Sala 102, Bloco B..."
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

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Cidade Canônica / IBGE</label>
            <select
              v-model="form.city_id"
              class="w-full h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-hidden text-sm transition"
            >
              <option :value="null">Selecione uma Cidade...</option>
              <option v-for="c in cities" :key="c.id" :value="c.id">
                {{ c.name }} / {{ c.state?.uf }} (IBGE: {{ c.ibge_code }})
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- ABA 4: PAPÉIS & PERSONAS OPERACIONAIS -->
      <div v-show="activeTab === 'personas'" class="space-y-6">
        <p class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">
          Selecione uma ou mais personas desempenhadas por este cadastro:
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
          <!-- Cliente -->
          <label
            :class="form.is_client ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-950/40 ring-2 ring-blue-500/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950/60'"
            class="p-5 rounded-xl border flex items-start gap-3.5 cursor-pointer transition select-none hover:border-slate-300 dark:hover:border-slate-700"
          >
            <input type="checkbox" v-model="form.is_client" class="w-4 h-4 mt-0.5 rounded-sm border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500" />
            <div>
              <p class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                <Users class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                Cliente
              </p>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                Contratante de planos de internet, links dedicados ou serviços de telecom.
              </p>
            </div>
          </label>

          <!-- Fornecedor -->
          <label
            :class="form.is_supplier ? 'border-purple-500 bg-purple-50/50 dark:bg-purple-950/40 ring-2 ring-purple-500/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950/60'"
            class="p-5 rounded-xl border flex items-start gap-3.5 cursor-pointer transition select-none hover:border-slate-300 dark:hover:border-slate-700"
          >
            <input type="checkbox" v-model="form.is_supplier" class="w-4 h-4 mt-0.5 rounded-sm border-slate-300 dark:border-slate-700 text-purple-600 focus:ring-purple-500" />
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
            <input type="checkbox" v-model="form.is_employee" class="w-4 h-4 mt-0.5 rounded-sm border-slate-300 dark:border-slate-700 text-amber-600 focus:ring-amber-500" />
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
            <input type="checkbox" v-model="form.is_requester" class="w-4 h-4 mt-0.5 rounded-sm border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500" />
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
  Phone, MessageSquare, AlertCircle, Loader2, Check
} from 'lucide-vue-next';
import axios from 'axios';
import BaseModal from '../common/BaseModal.vue';

const props = defineProps<{
  isOpen: boolean;
  personToEdit: any | null;
}>();

const emit = defineEmits(['close', 'saved']);

const isEditing = ref(false);
const isSubmitting = ref(false);
const errorMessage = ref('');

const cities = ref<any[]>([]);
const genders = ref<any[]>([]);

// Abas de Navegação Interna do Modal Fullscreen
const activeTab = ref('identificacao');
const tabs = [
  { id: 'identificacao', label: 'Identificação & Documentos', icon: FileText },
  { id: 'contatos', label: 'Contatos & Comunicação', icon: Mail },
  { id: 'endereco', label: 'Endereço & Localização', icon: MapPin },
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
  city_id: null,
  email: '',
  phone: '',
  whatsapp: '',
  postal_code: '',
  street: '',
  number: '',
  complement: '',
  neighborhood: '',
  is_employee: false,
  is_supplier: false,
  is_client: false,
  is_requester: false,
  status: 'active',
});

const form = reactive(defaultForm());

const loadReferences = async () => {
  try {
    const [citiesRes, gendersRes] = await Promise.all([
      axios.get('/api/v1/cities'),
      axios.get('/api/v1/genders'),
    ]);
    cities.value = citiesRes.data.data;
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
      Object.assign(form, {
        ...props.personToEdit,
        is_employee: props.personToEdit.personas?.is_employee || false,
        is_supplier: props.personToEdit.personas?.is_supplier || false,
        is_client: props.personToEdit.personas?.is_client || false,
        is_requester: props.personToEdit.personas?.is_requester || false,
        city_id: props.personToEdit.address?.city_id || null,
        postal_code: props.personToEdit.address?.postal_code || '',
        street: props.personToEdit.address?.street || '',
        number: props.personToEdit.address?.number || '',
        complement: props.personToEdit.address?.complement || '',
        neighborhood: props.personToEdit.address?.neighborhood || '',
        email: props.personToEdit.contact?.email || '',
        phone: props.personToEdit.contact?.phone || '',
        whatsapp: props.personToEdit.contact?.whatsapp || '',
      });
    } else {
      isEditing.value = false;
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
    if (isEditing.value && props.personToEdit) {
      await axios.put(`/api/v1/people/${props.personToEdit.id}`, form);
    } else {
      await axios.post('/api/v1/people', form);
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
