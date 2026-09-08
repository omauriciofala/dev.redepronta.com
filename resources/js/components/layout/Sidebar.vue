<template>
  <aside class="w-[240px] h-screen flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 select-none pb-12">
    <!-- Topo da Sidebar: Identidade do Produto -->
    <div class="h-16 px-5 flex items-center gap-3 border-b border-slate-100 dark:border-slate-800/80">
      <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
        RP
      </div>
      <div>
        <h1 class="text-xs font-semibold tracking-wider text-slate-900 dark:text-slate-100 uppercase">Rede Pronta</h1>
        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">ERP & Field Service</p>
      </div>
    </div>

    <!-- Navegação Minimalista -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
      <div class="px-2 pb-2 text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
        Cadastros
      </div>

      <!-- Item Pessoas (Ativo) -->
      <a
        href="#"
        class="flex items-center gap-3 px-3 py-2 text-xs font-medium rounded-md bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-400 transition-colors"
      >
        <Users class="w-4 h-4" />
        <span>Pessoas</span>
      </a>

      <!-- Itens dos próximos Sprints (desabilitados) -->
      <div class="pt-4 px-2 pb-2 text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
        Módulos Futuros
      </div>

      <div class="flex items-center gap-3 px-3 py-2 text-xs font-normal text-slate-400 dark:text-slate-600 rounded-md cursor-not-allowed">
        <Package class="w-4 h-4" />
        <span>Suprimentos (WMS)</span>
      </div>

      <div class="flex items-center gap-3 px-3 py-2 text-xs font-normal text-slate-400 dark:text-slate-600 rounded-md cursor-not-allowed">
        <CheckSquare class="w-4 h-4" />
        <span>Operações (FSM)</span>
      </div>

      <div class="flex items-center gap-3 px-3 py-2 text-xs font-normal text-slate-400 dark:text-slate-600 rounded-md cursor-not-allowed">
        <CreditCard class="w-4 h-4" />
        <span>Financeiro</span>
      </div>

      <div class="flex items-center gap-3 px-3 py-2 text-xs font-normal text-slate-400 dark:text-slate-600 rounded-md cursor-not-allowed">
        <MessageSquare class="w-4 h-4" />
        <span>CRM Omnichannel</span>
      </div>
    </nav>

    <!-- Rodapé Interno da Sidebar: Alternar Tema & Usuário -->
    <div class="p-3 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between">
      <div class="flex items-center gap-2 overflow-hidden">
        <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-[10px] font-medium text-slate-700 dark:text-slate-300">
          M
        </div>
        <div class="truncate">
          <p class="text-xs font-medium text-slate-800 dark:text-slate-200 truncate">Matriz</p>
          <p class="text-[10px] text-slate-400 truncate">admin@redepronta.com</p>
        </div>
      </div>

      <!-- Botão Tema -->
      <button
        @click="toggleTheme"
        class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded hover:bg-slate-100 dark:hover:bg-slate-800 transition"
        :title="theme === 'dark' ? 'Mudar para Tema Claro' : 'Mudar para Tema Escuro'"
      >
        <Sun v-if="theme === 'dark'" class="w-4 h-4" />
        <Moon v-else class="w-4 h-4" />
      </button>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { Users, Package, CheckSquare, CreditCard, MessageSquare, Sun, Moon } from 'lucide-vue-next';
import { useTheme } from '../../composables/useTheme';

const { theme, toggleTheme } = useTheme();
</script>
