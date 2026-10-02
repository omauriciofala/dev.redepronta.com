<template>
  <aside class="w-[240px] h-screen flex flex-col bg-[#070B14] text-slate-200 border-r border-[#1E293B] select-none pb-12 shadow-xl transition-colors duration-200">
    <!-- Topo da Sidebar: Identidade do Produto com Laranja Institucional e Deep Slate -->
    <div class="h-16 px-5 flex items-center gap-3 border-b border-[#1E293B]">
      <!-- Logo do Tenant ou Fallback RP -->
      <div
        v-if="tenantLogo"
        class="w-9 h-9 rounded-lg overflow-hidden bg-white/5 border border-white/10 flex items-center justify-center shrink-0 p-1"
      >
        <img :src="tenantLogo" :alt="tenantName" class="w-full h-full object-contain" />
      </div>
      <div
        v-else
        class="w-9 h-9 rounded-lg bg-[#FC6714] flex items-center justify-center text-white font-heading font-semibold text-base shadow-sm shrink-0"
      >
        RP
      </div>

      <div class="truncate">
        <h1 class="text-sm font-semibold font-heading tracking-tight text-white uppercase truncate">
          {{ tenantName }}
        </h1>
        <p class="text-[11px] text-slate-400 font-medium truncate">
          {{ currentTenant?.slogan || 'ERP & Field Service' }}
        </p>
      </div>
    </div>

    <!-- Navegação Confortável -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
      <div class="px-3 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
        Cadastros
      </div>

      <!-- Item Pessoas -->
      <button
        type="button"
        @click="setView('people')"
        :class="currentView === 'people'
          ? 'bg-[#FC6714]/15 text-[#FC6714] font-semibold border-l-2 border-[#FC6714]'
          : 'text-slate-400 hover:text-slate-200 hover:bg-white/5 font-medium border-l-2 border-transparent'"
        class="w-full flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer text-left focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
      >
        <Users class="w-5 h-5 shrink-0" :class="currentView === 'people' ? 'text-[#FC6714]' : 'text-slate-400'" />
        <span>Pessoas</span>
      </button>

      <!-- Item Usuários & Acessos (RBAC) -->
      <button
        type="button"
        @click="setView('users')"
        :class="currentView === 'users'
          ? 'bg-[#FC6714]/15 text-[#FC6714] font-semibold border-l-2 border-[#FC6714]'
          : 'text-slate-400 hover:text-slate-200 hover:bg-white/5 font-medium border-l-2 border-transparent'"
        class="w-full flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer text-left focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
      >
        <ShieldCheck class="w-5 h-5 shrink-0" :class="currentView === 'users' ? 'text-[#FC6714]' : 'text-slate-400'" />
        <span>Usuários & Acessos</span>
      </button>

      <!-- Operações & WMS -->
      <div class="pt-4 px-3 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
        Operações & WMS
      </div>

      <!-- Item Tarefas (Inbox & Triagem) -->
      <button
        type="button"
        @click="setView('tasks')"
        :class="currentView === 'tasks'
          ? 'bg-[#FC6714]/15 text-[#FC6714] font-semibold border-l-2 border-[#FC6714]'
          : 'text-slate-400 hover:text-slate-200 hover:bg-white/5 font-medium border-l-2 border-transparent'"
        class="w-full flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer text-left focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
      >
        <CheckSquare class="w-5 h-5 shrink-0" :class="currentView === 'tasks' ? 'text-[#FC6714]' : 'text-slate-400'" />
        <span>Tarefas</span>
      </button>

      <!-- Item Funil Operacional (FSM) -->
      <button
        type="button"
        @click="setView('operations')"
        :class="currentView === 'operations'
          ? 'bg-[#FC6714]/15 text-[#FC6714] font-semibold border-l-2 border-[#FC6714]'
          : 'text-slate-400 hover:text-slate-200 hover:bg-white/5 font-medium border-l-2 border-transparent'"
        class="w-full flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer text-left focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
      >
        <Activity class="w-5 h-5 shrink-0" :class="currentView === 'operations' ? 'text-[#FC6714]' : 'text-slate-400'" />
        <span>Funil Operacional (FSM)</span>
      </button>

      <!-- Item Suprimentos & WMS -->
      <button
        type="button"
        @click="setView('supplies')"
        :class="currentView === 'supplies'
          ? 'bg-[#FC6714]/15 text-[#FC6714] font-semibold border-l-2 border-[#FC6714]'
          : 'text-slate-400 hover:text-slate-200 hover:bg-white/5 font-medium border-l-2 border-transparent'"
        class="w-full flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer text-left focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
      >
        <Package class="w-5 h-5 shrink-0" :class="currentView === 'supplies' ? 'text-[#FC6714]' : 'text-slate-400'" />
        <span>Suprimentos & WMS</span>
      </button>

      <!-- Desenvolvimento & Governança -->
      <div class="pt-4 px-3 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
        Desenvolvimento
      </div>

      <!-- Item Desenvolvedor -->
      <button
        type="button"
        @click="setView('developer')"
        :class="currentView === 'developer'
          ? 'bg-[#FC6714]/15 text-[#FC6714] font-semibold border-l-2 border-[#FC6714]'
          : 'text-slate-400 hover:text-slate-200 hover:bg-white/5 font-medium border-l-2 border-transparent'"
        class="w-full flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer text-left focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
      >
        <Terminal class="w-5 h-5 shrink-0" :class="currentView === 'developer' ? 'text-[#FC6714]' : 'text-slate-400'" />
        <span>Desenvolvedor</span>
      </button>

      <!-- Item Design System -->
      <button
        type="button"
        @click="setView('design-system')"
        :class="currentView === 'design-system'
          ? 'bg-[#FC6714]/15 text-[#FC6714] font-semibold border-l-2 border-[#FC6714]'
          : 'text-slate-400 hover:text-slate-200 hover:bg-white/5 font-medium border-l-2 border-transparent'"
        class="w-full flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer text-left focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
      >
        <Palette class="w-5 h-5 shrink-0" :class="currentView === 'design-system' ? 'text-[#FC6714]' : 'text-slate-400'" />
        <span>Design System</span>
      </button>

      <!-- Item Change-log -->
      <button
        type="button"
        @click="setView('changelog')"
        :class="currentView === 'changelog'
          ? 'bg-[#FC6714]/15 text-[#FC6714] font-semibold border-l-2 border-[#FC6714]'
          : 'text-slate-400 hover:text-slate-200 hover:bg-white/5 font-medium border-l-2 border-transparent'"
        class="w-full flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer text-left focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
      >
        <History class="w-5 h-5 shrink-0" :class="currentView === 'changelog' ? 'text-[#FC6714]' : 'text-slate-400'" />
        <span>Change-log</span>
      </button>

      <!-- Item APIs & Integrações -->
      <button
        type="button"
        @click="setView('integrations')"
        :class="currentView === 'integrations'
          ? 'bg-[#FC6714]/15 text-[#FC6714] font-semibold border-l-2 border-[#FC6714]'
          : 'text-slate-400 hover:text-slate-200 hover:bg-white/5 font-medium border-l-2 border-transparent'"
        class="w-full flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer text-left focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
      >
        <Network class="w-5 h-5 shrink-0" :class="currentView === 'integrations' ? 'text-[#FC6714]' : 'text-slate-400'" />
        <span>APIs & Integrações</span>
      </button>

      <!-- Próximos Módulos -->
      <div class="pt-5 px-3 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
        Módulos Futuros
      </div>

      <div class="flex items-center gap-3 px-3 py-2 text-sm font-normal text-slate-500 rounded-lg cursor-not-allowed">
        <CreditCard class="w-5 h-5 opacity-50 shrink-0" />
        <span>Financeiro</span>
      </div>

      <div class="flex items-center gap-3 px-3 py-2 text-sm font-normal text-slate-500 rounded-lg cursor-not-allowed">
        <MessageSquare class="w-5 h-5 opacity-50 shrink-0" />
        <span>CRM Omnichannel</span>
      </div>
    </nav>

    <!-- Rodapé Interno da Sidebar: Perfil do Usuário, Tema & Logout -->
    <div class="p-3.5 border-t border-[#1E293B] flex items-center justify-between bg-[#05080E]">
      <!-- Perfil do Usuário Clicável -> Navega para Configurações (Meu Perfil) -->
      <button
        type="button"
        @click="goToProfile"
        class="flex items-center gap-2.5 overflow-hidden flex-1 mr-2 p-1.5 -ml-1.5 rounded-lg hover:bg-white/10 transition text-left cursor-pointer group focus:outline-none focus:ring-2 focus:ring-[#FC6714]"
        title="Meu Perfil & Configurações"
      >
        <div class="w-8 h-8 rounded-full overflow-hidden shrink-0 border border-[#FC6714]/40 flex items-center justify-center bg-[#FC6714]/20 text-[#FC6714]">
          <img
            v-if="currentUser?.avatar_url"
            :src="currentUser.avatar_url"
            alt="Avatar"
            class="w-full h-full object-cover"
          />
          <span v-else class="text-xs font-bold font-heading">
            {{ (currentUser?.name || 'M').charAt(0).toUpperCase() }}
          </span>
        </div>
        <div class="truncate flex-1">
          <p class="text-xs font-semibold text-white group-hover:text-[#FC6714] transition truncate leading-tight">
            {{ currentUser?.name || 'Administrador Matriz' }}
          </p>
          <p class="text-[11px] text-slate-400 truncate">
            {{ currentUser?.email || 'admin@redepronta.com' }}
          </p>
        </div>
      </button>

      <div class="flex items-center gap-1 shrink-0">
        <!-- Botão Tema (Dark / Light) -->
        <button
          type="button"
          @click="toggleTheme"
          class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-white/10 transition cursor-pointer focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
          :title="theme === 'dark' ? 'Mudar para Tema Claro' : 'Mudar para Tema Escuro'"
        >
          <Sun v-if="theme === 'dark'" class="w-4 h-4 text-amber-400" />
          <Moon v-else class="w-4 h-4 text-slate-400" />
        </button>

        <!-- Botão Logout (Sair) -->
        <button
          type="button"
          @click="handleLogout"
          class="p-1.5 text-slate-400 hover:text-red-400 rounded-lg hover:bg-white/10 transition cursor-pointer focus:ring-2 focus:ring-red-500 focus:outline-none"
          title="Encerrar sessão (Logout)"
        >
          <LogOut class="w-4 h-4" />
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { Users, Terminal, Palette, History, Network, Package, Activity, CheckSquare, CreditCard, MessageSquare, Sun, Moon, ShieldCheck, LogOut } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { useNavigation } from '../../composables/useNavigation';
import { useAuth } from '../../composables/useAuth';
import { useTenant } from '../../composables/useTenant';

const { theme, toggleTheme } = useTheme();
const { currentView, setView } = useNavigation();
const { currentUser, logout } = useAuth();
const { tenantName, tenantLogo, currentTenant, fetchTenant } = useTenant();

const goToProfile = () => {
  setView('settings', 'profile');
};

const handleLogout = async () => {
  if (confirm('Deseja realmente sair do sistema?')) {
    await logout();
  }
};

onMounted(() => {
  fetchTenant();
});
</script>
