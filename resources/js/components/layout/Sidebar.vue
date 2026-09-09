<template>
  <aside class="w-[240px] h-screen flex flex-col bg-[#06064D] text-slate-200 border-r border-[#14147A] select-none pb-12 shadow-lg transition-colors duration-200">
    <!-- Topo da Sidebar: Identidade do Produto com Laranja e Azul Escuro Institucionais -->
    <div class="h-16 px-5 flex items-center gap-3 border-b border-[#14147A]">
      <div class="w-9 h-9 rounded-lg bg-[#FC6714] flex items-center justify-center text-white font-heading font-semibold text-base shadow-sm">
        RP
      </div>
      <div>
        <h1 class="text-sm font-semibold font-heading tracking-tight text-white uppercase">Rede Pronta</h1>
        <p class="text-[11px] text-slate-300 font-medium">ERP & Field Service</p>
      </div>
    </div>

    <!-- Navegação Confortável -->
    <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
      <div class="px-3 pb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
        Cadastros
      </div>

      <!-- Item Pessoas -->
      <button
        type="button"
        @click="setView('people')"
        :class="currentView === 'people'
          ? 'bg-[#FC6714] text-white font-bold shadow-xs'
          : 'text-slate-300 hover:text-white hover:bg-white/10 font-medium'"
        class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm rounded-lg transition-colors cursor-pointer text-left focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
      >
        <Users class="w-5 h-5" :class="currentView === 'people' ? 'text-white' : 'text-slate-300'" />
        <span>Pessoas</span>
      </button>

      <!-- Desenvolvimento & Governança -->
      <div class="pt-4 px-3 pb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
        Desenvolvimento
      </div>

      <!-- Item Design System -->
      <button
        type="button"
        @click="setView('design-system')"
        :class="currentView === 'design-system'
          ? 'bg-[#FC6714] text-white font-bold shadow-xs'
          : 'text-slate-300 hover:text-white hover:bg-white/10 font-medium'"
        class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm rounded-lg transition-colors cursor-pointer text-left focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
      >
        <Palette class="w-5 h-5" :class="currentView === 'design-system' ? 'text-white' : 'text-slate-300'" />
        <span>Design System</span>
      </button>

      <!-- Item Change-log -->
      <button
        type="button"
        @click="setView('changelog')"
        :class="currentView === 'changelog'
          ? 'bg-[#FC6714] text-white font-bold shadow-xs'
          : 'text-slate-300 hover:text-white hover:bg-white/10 font-medium'"
        class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm rounded-lg transition-colors cursor-pointer text-left focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
      >
        <History class="w-5 h-5" :class="currentView === 'changelog' ? 'text-white' : 'text-slate-300'" />
        <span>Change-log</span>
      </button>

      <!-- Item APIs & Integrações -->
      <button
        type="button"
        @click="setView('integrations')"
        :class="currentView === 'integrations'
          ? 'bg-[#FC6714] text-white font-bold shadow-xs'
          : 'text-slate-300 hover:text-white hover:bg-white/10 font-medium'"
        class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm rounded-lg transition-colors cursor-pointer text-left focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
      >
        <Network class="w-5 h-5" :class="currentView === 'integrations' ? 'text-white' : 'text-slate-300'" />
        <span>APIs & Integrações</span>
      </button>

      <!-- Próximos Módulos -->
      <div class="pt-5 px-3 pb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
        Módulos Futuros
      </div>

      <div class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-normal text-slate-400/80 rounded-lg cursor-not-allowed">
        <Package class="w-5 h-5 opacity-60" />
        <span>Suprimentos (WMS)</span>
      </div>

      <div class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-normal text-slate-400/80 rounded-lg cursor-not-allowed">
        <CheckSquare class="w-5 h-5 opacity-60" />
        <span>Operações (FSM)</span>
      </div>

      <div class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-normal text-slate-400/80 rounded-lg cursor-not-allowed">
        <CreditCard class="w-5 h-5 opacity-60" />
        <span>Financeiro</span>
      </div>

      <div class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-normal text-slate-400/80 rounded-lg cursor-not-allowed">
        <MessageSquare class="w-5 h-5 opacity-60" />
        <span>CRM Omnichannel</span>
      </div>
    </nav>

    <!-- Rodapé Interno da Sidebar: Alternar Tema & Perfil -->
    <div class="p-3.5 border-t border-[#14147A] flex items-center justify-between bg-[#03032E]">
      <div class="flex items-center gap-2.5 overflow-hidden">
        <div class="w-7 h-7 rounded-full bg-[#FC6714]/20 text-[#FC6714] border border-[#FC6714]/40 flex items-center justify-center text-xs font-bold">
          M
        </div>
        <div class="truncate">
          <p class="text-xs font-semibold text-white truncate leading-tight">Matriz</p>
          <p class="text-[11px] text-slate-300 truncate">admin@redepronta.com</p>
        </div>
      </div>

      <!-- Botão Tema (Dark / Light) -->
      <button
        type="button"
        @click="toggleTheme"
        class="p-2 text-slate-300 hover:text-white rounded-lg hover:bg-white/10 transition cursor-pointer focus:ring-2 focus:ring-[#FC6714] focus:outline-none"
        :title="theme === 'dark' ? 'Mudar para Tema Claro' : 'Mudar para Tema Escuro'"
      >
        <Sun v-if="theme === 'dark'" class="w-4 h-4 text-amber-400" />
        <Moon v-else class="w-4 h-4 text-slate-300" />
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
