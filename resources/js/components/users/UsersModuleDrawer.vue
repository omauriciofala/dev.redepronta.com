<template>
  <BaseModal
    v-model="isOpenModel"
    size="drawer"
    title="Gestão de Usuários & Acessos"
    description="Atalhos rápidos, matriz de governança, perfis de papéis e permissões do sistema"
    @close="close"
  >
    <!-- Header Badge -->
    <template #header-badge>
      <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
        {{ totalUsers }} operadores cadastrados
      </span>
    </template>

    <div class="space-y-6 text-xs">
      <!-- 1. RESUMO OPERACIONAL EM DESTAQUE -->
      <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 space-y-3">
        <div class="flex items-center justify-between">
          <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
            Resumo de Governança & Acesso
          </span>
          <span class="text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
            RBAC Ativo
          </span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center">
          <div class="p-2.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
            <div class="text-base font-bold font-heading text-slate-900 dark:text-slate-100">{{ totalUsers }}</div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Total Usuários</div>
          </div>
          <div class="p-2.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
            <div class="text-base font-bold font-heading text-emerald-600 dark:text-emerald-400">{{ activeUsers }}</div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Ativos</div>
          </div>
          <div class="p-2.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
            <div class="text-base font-bold font-heading text-amber-600 dark:text-amber-400">{{ superAdminsCount }}</div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Super Admins</div>
          </div>
          <div class="p-2.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
            <div class="text-base font-bold font-heading text-purple-600 dark:text-purple-400">{{ rolesCount }}</div>
            <div class="text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Papéis</div>
          </div>
        </div>
      </div>

      <!-- 2. NAVEGAÇÃO RÁPIDA POR ABAS / VISÕES -->
      <div class="space-y-2.5">
        <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
          <Layers class="w-3.5 h-3.5 text-[#FC6714]" />
          <span>Visões do Módulo</span>
        </h4>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
          <button
            type="button"
            @click="navigateToTab('users')"
            class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-white dark:bg-slate-900 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center gap-2.5 text-left cursor-pointer group shadow-2xs"
          >
            <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-950/80 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
              <Users class="w-4 h-4" />
            </div>
            <div class="truncate">
              <div class="font-bold text-slate-800 dark:text-slate-200 truncate">Usuários</div>
              <div class="text-[10px] text-slate-400">{{ totalUsers }} operadores</div>
            </div>
          </button>

          <button
            type="button"
            @click="navigateToTab('roles')"
            class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-white dark:bg-slate-900 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center gap-2.5 text-left cursor-pointer group shadow-2xs"
          >
            <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-950/80 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
              <ShieldCheck class="w-4 h-4" />
            </div>
            <div class="truncate">
              <div class="font-bold text-slate-800 dark:text-slate-200 truncate">Papéis (Roles)</div>
              <div class="text-[10px] text-slate-400">{{ rolesCount }} perfis RBAC</div>
            </div>
          </button>

          <button
            type="button"
            @click="navigateToTab('permissions')"
            class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-orange-300 dark:hover:border-[#FC6714]/50 bg-white dark:bg-slate-900 hover:bg-orange-50/50 dark:hover:bg-[#FC6714]/10 transition flex items-center gap-2.5 text-left cursor-pointer group shadow-2xs"
          >
            <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/80 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition">
              <KeyRound class="w-4 h-4" />
            </div>
            <div class="truncate">
              <div class="font-bold text-slate-800 dark:text-slate-200 truncate">Permissões</div>
              <div class="text-[10px] text-slate-400">{{ permissionsCount }} cadastradas</div>
            </div>
          </button>
        </div>
      </div>

      <!-- 3. AÇÕES RÁPIDAS DE GOVERNANÇA -->
      <div class="space-y-2.5">
        <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
          <Zap class="w-3.5 h-3.5 text-amber-500" />
          <span>Ações Rápidas & Comandos</span>
        </h4>
        <div class="space-y-2">
          <button
            type="button"
            @click="triggerAction('new-user')"
            class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition flex items-center justify-between text-left cursor-pointer group shadow-2xs"
          >
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-[#FC6714]/10 text-[#FC6714] flex items-center justify-center shrink-0">
                <UserPlus class="w-4 h-4" />
              </div>
              <div>
                <div class="font-bold text-slate-800 dark:text-slate-200">Cadastrar Novo Usuário</div>
                <div class="text-[11px] text-slate-400">Criar credenciais e vincular à pessoa ou operador</div>
              </div>
            </div>
            <ArrowUpRight class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition" />
          </button>

          <button
            type="button"
            @click="triggerAction('new-role')"
            class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition flex items-center justify-between text-left cursor-pointer group shadow-2xs"
          >
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                <ShieldCheck class="w-4 h-4" />
              </div>
              <div>
                <div class="font-bold text-slate-800 dark:text-slate-200">Cadastrar Novo Papel (Role)</div>
                <div class="text-[11px] text-slate-400">Definir nome, identificador e matriz de permissões</div>
              </div>
            </div>
            <ArrowUpRight class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition" />
          </button>

          <button
            type="button"
            @click="triggerAction('filter-super-admins')"
            class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition flex items-center justify-between text-left cursor-pointer group shadow-2xs"
          >
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <Crown class="w-4 h-4" />
              </div>
              <div>
                <div class="font-bold text-slate-800 dark:text-slate-200">Listar Super Admins (Acesso Livre)</div>
                <div class="text-[11px] text-slate-400">Filtrar operadores com permissão global e irrestrita</div>
              </div>
            </div>
            <ArrowUpRight class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition" />
          </button>

          <button
            type="button"
            @click="triggerAction('refresh-all')"
            class="w-full p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition flex items-center justify-between text-left cursor-pointer group shadow-2xs"
          >
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center shrink-0">
                <RotateCcw class="w-4 h-4" />
              </div>
              <div>
                <div class="font-bold text-slate-800 dark:text-slate-200">Recarregar Todos os Dados</div>
                <div class="text-[11px] text-slate-400">Sincronizar estatísticas, papéis e permissões</div>
              </div>
            </div>
            <ArrowUpRight class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition" />
          </button>
        </div>
      </div>

      <!-- 4. SEGURANÇA E AUDITORIA RBAC -->
      <div class="p-4 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-200 dark:border-indigo-900/60 space-y-2.5">
        <div class="flex items-center justify-between text-indigo-800 dark:text-indigo-300 font-bold">
          <span class="flex items-center gap-1.5">
            <ShieldCheck class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
            Governança & Segurança RBAC
          </span>
          <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 border border-indigo-300 dark:border-indigo-800 font-mono">
            MULTI-TENANT
          </span>
        </div>
        <p class="text-[11px] text-indigo-700/90 dark:text-indigo-400/90 leading-relaxed">
          Usuários com o perfil <strong>Super Admin</strong> possuem bypass automático e permissão irrestrita a todas as rotas e ações do ERP. Usuários comuns são estritamente limitados pelas permissões de seus respectivos papéis.
        </p>
      </div>
    </div>

    <!-- Footer do Drawer -->
    <template #footer>
      <div class="w-full flex items-center justify-between">
        <span class="text-xs text-slate-400 font-medium">ERP Rede Pronta • Segurança & Governança</span>
        <button
          type="button"
          @click="close"
          class="h-9 px-4 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition cursor-pointer"
        >
          Fechar Menu
        </button>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import {
  Layers,
  Users,
  ShieldCheck,
  KeyRound,
  UserPlus,
  Crown,
  Zap,
  ArrowUpRight,
  RotateCcw,
} from 'lucide-vue-next';
import BaseModal from '../common/BaseModal.vue';

const props = defineProps<{
  modelValue: boolean;
  totalUsers: number;
  activeUsers: number;
  superAdminsCount: number;
  rolesCount: number;
  permissionsCount: number;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
  (e: 'navigate', tab: 'users' | 'roles' | 'permissions'): void;
  (e: 'action', action: 'new-user' | 'new-role' | 'filter-super-admins' | 'refresh-all'): void;
}>();

const isOpenModel = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v),
});

function close() {
  emit('update:modelValue', false);
}

function navigateToTab(tab: 'users' | 'roles' | 'permissions') {
  emit('navigate', tab);
  close();
}

function triggerAction(action: 'new-user' | 'new-role' | 'filter-super-admins' | 'refresh-all') {
  emit('action', action);
  close();
}
</script>
