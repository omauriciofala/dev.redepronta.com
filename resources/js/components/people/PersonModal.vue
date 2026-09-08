<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
    <div class="w-full max-w-2xl bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden flex flex-col max-h-[90vh]">
      <!-- Cabeçalho do Modal -->
      <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <div>
          <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">
            {{ isEditing ? 'Editar Cadastro de Pessoa' : 'Novo Cadastro de Pessoa' }}
          </h2>
          <p class="text-[11px] text-slate-500 dark:text-slate-400">
            Defina o tipo, identificação, persona e localização canônica
          </p>
        </div>
        <button @click="close" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 p-1">
          <X class="w-4 h-4" />
        </button>
      </div>

      <!-- Formulário com Scroll -->
      <form @submit.prevent="submit" class="p-6 overflow-y-auto space-y-4 flex-1 text-xs">
        <!-- Alternador Tipo: PF ou PJ -->
        <div class="flex items-center gap-4 p-2.5 rounded-lg bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800">
          <span class="text-[11px] font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Tipo:</span>
          <label class="flex items-center gap-1.5 cursor-pointer">
            <input type="radio" value="individual" v-model="form.person_type" class="text-blue-600 focus:ring-0" />
            <span :class="form.person_type === 'individual' ? 'font-semibold text-blue-600' : 'text-slate-600 dark:text-slate-400'">
              Pessoa Física (PF)
            </span>
          </label>
          <label class="flex items-center gap-1.5 cursor-pointer">
            <input type="radio" value="legal" v-model="form.person_type" class="text-blue-600 focus:ring-0" />
            <span :class="form.person_type === 'legal' ? 'font-semibold text-blue-600' : 'text-slate-600 dark:text-slate-400'">
              Pessoa Jurídica (PJ)
            </span>
          </label>
        </div>

        <!-- Campos Principais: Nome / Razão -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block text-[11px] font-medium text-slate-700 dark:text-slate-300 mb-1">
              {{ form.person_type === 'individual' ? 'Nome Completo *' : 'Razão Social *' }}
            </label>
            <input
              type="text"
              v-model="form.name"
              required
              class="w-full h-8 px-2.5 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500"
            />
          </div>

          <div v-if="form.person_type === 'legal'">
            <label class="block text-[11px] font-medium text-slate-700 dark:text-slate-300 mb-1">Nome Fantasia</label>
            <input
              type="text"
              v-model="form.trade_name"
              class="w-full h-8 px-2.5 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500"
            />
          </div>

          <div>
            <label class="block text-[11px] font-medium text-slate-700 dark:text-slate-300 mb-1">
              {{ form.person_type === 'individual' ? 'CPF' : 'CNPJ' }}
            </label>
            <input
              type="text"
              v-model="form.document_number"
              :placeholder="form.person_type === 'individual' ? '000.000.000-00' : '00.000.000/0000-00'"
              class="w-full h-8 px-2.5 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500"
            />
          </div>

          <div>
            <label class="block text-[11px] font-medium text-slate-700 dark:text-slate-300 mb-1">
              {{ form.person_type === 'individual' ? 'RG' : 'Inscrição Estadual' }}
            </label>
            <input
              type="text"
              v-model="form.rg_ie"
              class="w-full h-8 px-2.5 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500"
            />
          </div>

          <div v-if="form.person_type === 'individual'">
            <label class="block text-[11px] font-medium text-slate-700 dark:text-slate-300 mb-1">Gênero</label>
            <select
              v-model="form.gender_id"
              class="w-full h-8 px-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500"
            >
              <option :value="null">Selecione...</option>
              <option v-for="g in genders" :key="g.id" :value="g.id">{{ g.name }}</option>
            </select>
          </div>
        </div>

        <!-- Personas / Papéis no Sistema (Axioma 1) -->
        <div class="pt-2">
          <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">
            Papéis no Sistema (Personas)
          </label>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <label class="flex items-center gap-2 p-2 rounded border border-slate-200 dark:border-slate-800 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-950">
              <input type="checkbox" v-model="form.is_employee" class="text-blue-600 rounded" />
              <span class="text-xs">Colaborador</span>
            </label>
            <label class="flex items-center gap-2 p-2 rounded border border-slate-200 dark:border-slate-800 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-950">
              <input type="checkbox" v-model="form.is_supplier" class="text-blue-600 rounded" />
              <span class="text-xs">Fornecedor</span>
            </label>
            <label class="flex items-center gap-2 p-2 rounded border border-slate-200 dark:border-slate-800 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-950">
              <input type="checkbox" v-model="form.is_client" class="text-blue-600 rounded" />
              <span class="text-xs">Cliente</span>
            </label>
            <label class="flex items-center gap-2 p-2 rounded border border-slate-200 dark:border-slate-800 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-950">
              <input type="checkbox" v-model="form.is_requester" class="text-blue-600 rounded" />
              <span class="text-xs">Solicitante</span>
            </label>
          </div>
        </div>

        <!-- Contato -->
        <div class="pt-2">
          <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">
            Contato
          </label>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
              <label class="block text-[11px] font-medium text-slate-700 dark:text-slate-300 mb-1">E-mail</label>
              <input
                type="email"
                v-model="form.email"
                class="w-full h-8 px-2.5 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500"
              />
            </div>
            <div>
              <label class="block text-[11px] font-medium text-slate-700 dark:text-slate-300 mb-1">Telefone</label>
              <input
                type="text"
                v-model="form.phone"
                class="w-full h-8 px-2.5 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500"
              />
            </div>
            <div>
              <label class="block text-[11px] font-medium text-slate-700 dark:text-slate-300 mb-1">WhatsApp</label>
              <input
                type="text"
                v-model="form.whatsapp"
                class="w-full h-8 px-2.5 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500"
              />
            </div>
          </div>
        </div>

        <!-- Endereço Canônico -->
        <div class="pt-2">
          <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">
            Localização Canônica (Cidades IBGE)
          </label>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
              <label class="block text-[11px] font-medium text-slate-700 dark:text-slate-300 mb-1">Cidade (IBGE)</label>
              <select
                v-model="form.city_id"
                class="w-full h-8 px-2 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500"
              >
                <option :value="null">Selecione a Cidade...</option>
                <option v-for="c in cities" :key="c.id" :value="c.id">{{ c.full_name }} (IBGE: {{ c.ibge_code }})</option>
              </select>
            </div>
            <div>
              <label class="block text-[11px] font-medium text-slate-700 dark:text-slate-300 mb-1">CEP</label>
              <input
                type="text"
                v-model="form.postal_code"
                placeholder="00000-000"
                class="w-full h-8 px-2.5 rounded border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500"
              />
            </div>
          </div>
        </div>

        <!-- Mensagens de Erro da API -->
        <div v-if="errorMessage" class="p-2.5 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900 rounded text-red-600 dark:text-red-400 text-xs">
          {{ errorMessage }}
        </div>
      </form>

      <!-- Rodapé do Modal -->
      <div class="px-6 py-3 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 flex items-center justify-end gap-2">
        <button
          type="button"
          @click="close"
          class="px-3 py-1.5 rounded text-xs font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 transition"
        >
          Cancelar
        </button>
        <button
          type="button"
          @click="submit"
          :disabled="isSubmitting"
          class="px-4 py-1.5 rounded text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white transition disabled:opacity-50"
        >
          {{ isSubmitting ? 'Salvando...' : (isEditing ? 'Salvar Alterações' : 'Cadastrar Pessoa') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, watch, onMounted } from 'vue';
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
