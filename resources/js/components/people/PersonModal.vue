<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
    <div class="w-full max-w-3xl bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden flex flex-col max-h-[92vh] transition-colors duration-200">
      <!-- Cabeçalho do Modal -->
      <div class="px-6 py-4.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <div>
          <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">
            {{ isEditing ? 'Editar Cadastro de Pessoa' : 'Novo Cadastro de Pessoa' }}
          </h2>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Defina o tipo, identificação, personas e localização canônica
          </p>
        </div>
        <button @click="close" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Formulário com Scroll Confortável -->
      <form @submit.prevent="submit" class="p-6 overflow-y-auto space-y-5 flex-1 text-sm">
        <!-- Alternador Tipo: PF ou PJ -->
        <div class="flex items-center gap-6 p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800">
          <span class="text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Tipo de Registro:</span>
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

        <!-- Campos Principais: Nome / Razão -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              {{ form.person_type === 'individual' ? 'Nome Completo *' : 'Razão Social *' }}
            </label>
            <input
              type="text"
              v-model="form.name"
              required
              class="w-full h-10 px-3.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:outline-none focus:border-blue-500 text-sm transition"
            />
          </div>

          <div v-if="form.person_type === 'legal'">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Nome Fantasia</label>
            <input
              type="text"
              v-model="form.trade_name"
              class="w-full h-10 px-3.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:outline-none focus:border-blue-500 text-sm transition"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              {{ form.person_type === 'individual' ? 'CPF' : 'CNPJ' }}
            </label>
            <input
              type="text"
              v-model="form.document_number"
              :placeholder="form.person_type === 'individual' ? '000.000.000-00' : '00.000.000/0000-00'"
              class="w-full h-10 px-3.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:outline-none focus:border-blue-500 text-sm transition"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
              {{ form.person_type === 'individual' ? 'RG' : 'Inscrição Estadual' }}
            </label>
            <input
              type="text"
              v-model="form.rg_ie"
              class="w-full h-10 px-3.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:outline-none focus:border-blue-500 text-sm transition"
            />
          </div>

          <div v-if="form.person_type === 'individual'">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Gênero</label>
            <select
              v-model="form.gender_id"
              class="w-full h-10 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:outline-none focus:border-blue-500 text-sm transition"
            >
              <option :value="null">Selecione o Gênero...</option>
              <option v-for="g in genders" :key="g.id" :value="g.id">{{ g.name }}</option>
            </select>
          </div>
        </div>

        <!-- Personas / Papéis no Sistema (Axioma 1) -->
        <div class="pt-2">
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2.5">
            Papéis no Sistema (Personas)
          </label>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <label class="flex items-center gap-2.5 p-3 rounded-lg border border-slate-200 dark:border-slate-800 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-950 transition">
              <input type="checkbox" v-model="form.is_employee" class="w-4 h-4 text-blue-600 rounded" />
              <span class="text-sm font-medium">Colaborador</span>
            </label>
            <label class="flex items-center gap-2.5 p-3 rounded-lg border border-slate-200 dark:border-slate-800 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-950 transition">
              <input type="checkbox" v-model="form.is_supplier" class="w-4 h-4 text-blue-600 rounded" />
              <span class="text-sm font-medium">Fornecedor</span>
            </label>
            <label class="flex items-center gap-2.5 p-3 rounded-lg border border-slate-200 dark:border-slate-800 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-950 transition">
              <input type="checkbox" v-model="form.is_client" class="w-4 h-4 text-blue-600 rounded" />
              <span class="text-sm font-medium">Cliente</span>
            </label>
            <label class="flex items-center gap-2.5 p-3 rounded-lg border border-slate-200 dark:border-slate-800 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-950 transition">
              <input type="checkbox" v-model="form.is_requester" class="w-4 h-4 text-blue-600 rounded" />
              <span class="text-sm font-medium">Solicitante</span>
            </label>
          </div>
        </div>

        <!-- Contato -->
        <div class="pt-2">
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2.5">
            Contato
          </label>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">E-mail</label>
              <input
                type="email"
                v-model="form.email"
                class="w-full h-10 px-3.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:outline-none focus:border-blue-500 text-sm transition"
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Telefone</label>
              <input
                type="text"
                v-model="form.phone"
                class="w-full h-10 px-3.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:outline-none focus:border-blue-500 text-sm transition"
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">WhatsApp</label>
              <input
                type="text"
                v-model="form.whatsapp"
                class="w-full h-10 px-3.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:outline-none focus:border-blue-500 text-sm transition"
              />
            </div>
          </div>
        </div>

        <!-- Endereço Canônico -->
        <div class="pt-2">
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2.5">
            Localização Canônica (Cidades IBGE)
          </label>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
              <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">Cidade (IBGE)</label>
              <select
                v-model="form.city_id"
                class="w-full h-10 px-3 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:outline-none focus:border-blue-500 text-sm transition"
              >
                <option :value="null">Selecione a Cidade...</option>
                <option v-for="c in cities" :key="c.id" :value="c.id">{{ c.full_name }} (IBGE: {{ c.ibge_code }})</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">CEP</label>
              <input
                type="text"
                v-model="form.postal_code"
                placeholder="00000-000"
                class="w-full h-10 px-3.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 focus:outline-none focus:border-blue-500 text-sm transition"
              />
            </div>
          </div>
        </div>

        <!-- Mensagens de Erro da API -->
        <div v-if="errorMessage" class="p-3 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900 rounded-lg text-red-700 dark:text-red-400 text-sm font-medium">
          {{ errorMessage }}
        </div>
      </form>

      <!-- Rodapé do Modal -->
      <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-950/70 flex items-center justify-end gap-3">
        <button
          type="button"
          @click="close"
          class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 transition"
        >
          Cancelar
        </button>
        <button
          type="button"
          @click="submit"
          :disabled="isSubmitting"
          class="px-5 py-2 rounded-lg text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white transition disabled:opacity-50 shadow-sm"
        >
          {{ isSubmitting ? 'Salvando...' : (isEditing ? 'Salvar Alterações' : 'Cadastrar Pessoa') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch } from 'vue';
import { X } from 'lucide-vue-next';
import axios from 'axios';

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
