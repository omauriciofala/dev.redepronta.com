<template>
  <aside class="w-[240px] h-screen flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800/80 select-none pb-12 shadow-xs transition-colors duration-200">
    <!-- Topo da Sidebar: Identidade do Produto -->
    <div class="h-16 px-5 flex items-center gap-3 border-b border-slate-100 dark:border-slate-800/80">
      <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold text-base shadow-sm">
        RP
      </div>
      <div>
        <h1 class="text-sm font-bold tracking-tight text-slate-900 dark:text-slate-100 uppercase">Rede Pronta</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">ERP & Field Service</p>
      </div>
    </div>

    <!-- Navegação Confortável -->
    <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
      <div class="px-3 pb-2 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
        Cadastros
      </div>

      <!-- Item Pessoas -->
      <button
        type="button"
        @click="setView('people')"
        :class="currentView === 'people'
          ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400 font-semibold shadow-2xs'
          : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60'"
        class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm rounded-lg transition-colors cursor-pointer text-left"
      >
        <Users class="w-5 h-5" :class="currentView === 'people' ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400'" />
        <span>Pessoas</span>
      </button>

      <!-- Desenvolvimento & Governança -->
      <div class="pt-4 px-3 pb-2 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
        Desenvolvimento
      </div>

      <!-- Item Design System -->
      <button
        type="button"
        @click="setView('design-system')"
        :class="currentView === 'design-system'
          ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400 font-semibold shadow-2xs'
          : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60'"
        class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm rounded-lg transition-colors cursor-pointer text-left"
      >
        <Palette class="w-5 h-5" :class="currentView === 'design-system' ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400'" />
        <span>Design System</span>
      </button>

      <!-- Item Change-log -->
      <button
        type="button"
        @click="setView('changelog')"
        :class="currentView === 'changelog'
          ? 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-400 font-semibold shadow-2xs'
          : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60'"
        class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm rounded-lg transition-colors cursor-pointer text-left"
      >
        <History class="w-5 h-5" :class="currentView === 'changelog' ? 'text-purple-600 dark:text-purple-400' : 'text-slate-400'" />
        <span>Change-log</span>
      </button>

      <!-- Item APIs & Integrações -->
      <button
        type="button"
        @click="setView('integrations')"
        :class="currentView === 'integrations'
          ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 font-semibold shadow-2xs'
          : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/60'"
        class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm rounded-lg transition-colors cursor-pointer text-left"
      >
        <Network class="w-5 h-5" :class="currentView === 'integrations' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400'" />
        <span>APIs & Integrações</span>
      </button>

      <!-- Próximos Módulos -->
      <div class="pt-5 px-3 pb-2 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
        Módulos Futuros
      </div>

      <div class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-normal text-slate-400 dark:text-slate-500 rounded-lg cursor-not-allowed">
        <Package class="w-5 h-5 opacity-70" />
        <span>Suprimentos (WMS)</span>
      </div>

      <div class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-normal text-slate-400 dark:text-slate-500 rounded-lg cursor-not-allowed">
        <CheckSquare class="w-5 h-5 opacity-70" />
        <span>Operações (FSM)</span>
      </div>

      <div class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-normal text-slate-400 dark:text-slate-500 rounded-lg cursor-not-allowed">
        <CreditCard class="w-5 h-5 opacity-70" />
        <span>Financeiro</span>
      </div>

      <div class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-normal text-slate-400 dark:text-slate-500 rounded-lg cursor-not-allowed">
        <MessageSquare class="w-5 h-5 opacity-70" />
        <span>CRM Omnichannel</span>
      </div>
    </nav>

    <!-- Rodapé Interno da Sidebar: Alternar Tema & Perfil -->
    <div class="p-3.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/50">
      <div class="flex items-center gap-2.5 overflow-hidden">
        <div class="w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300 flex items-center justify-center text-xs font-bold">
          M
        </div>
        <div class="truncate">
          <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 truncate leading-tight">Matriz</p>
          <p class="text-xs text-slate-500 dark:text-slate-400 truncate">admin@redepronta.com</p>
        </div>
      </div>

      <!-- Botão Tema (Dark / Light) -->
      <button
        type="button"
        @click="toggleTheme"
        class="p-2 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100 rounded-lg hover:bg-slate-200/70 dark:hover:bg-slate-800 transition cursor-pointer"
        :title="theme === 'dark' ? 'Mudar para Tema Claro' : 'Mudar para Tema Escuro'"
      >
        <Sun v-if="theme === 'dark'" class="w-5 h-5 text-amber-400" />
        <Moon v-else class="w-5 h-5 text-slate-600" />
      </button>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { Users, Palette, History, Network, Package, CheckSquare, CreditCard, MessageSquare, Sun, Moon } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';
import { useNavigation } from '../../composables/useNavigation';

const { theme, toggleTheme } = useTheme();
const { currentView, setView } = useNavigation();
</script>
