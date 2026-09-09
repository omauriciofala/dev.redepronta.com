<template>
  <footer class="fixed bottom-0 left-0 right-0 h-9 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm border-t border-slate-200 dark:border-slate-800 flex items-center justify-between px-5 z-40 text-xs text-slate-600 dark:text-slate-400 select-none transition-colors duration-200">
    <div class="flex items-center gap-3">
      <span class="font-bold text-slate-800 dark:text-slate-200">ERP Rede Pronta</span>
      <span class="text-slate-300 dark:text-slate-700">•</span>
      <span>v1.0.0</span>
      <span class="text-slate-300 dark:text-slate-700">•</span>
      <span>Tenant: <strong class="text-slate-700 dark:text-slate-300">Matriz</strong></span>
    </div>

    <div class="flex items-center gap-3.5">
      <!-- Link para o Repositório do GitHub -->
      <a
        href="https://github.com/omauriciofala/dev.redepronta.com"
        target="_blank"
        rel="noopener noreferrer"
        class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 font-medium text-slate-700 dark:text-slate-300 hover:text-[#FC6714] dark:hover:text-orange-400 transition cursor-pointer"
        title="Acessar repositório no GitHub (abre em nova aba)"
      >
        <Github class="w-3.5 h-3.5 text-slate-800 dark:text-slate-200" />
        <span>GitHub</span>
        <ExternalLink class="w-3 h-3 text-slate-400" />
      </a>

      <span class="text-slate-300 dark:text-slate-700">•</span>

      <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="font-medium text-emerald-700 dark:text-emerald-400">MariaDB Conectado</span>
      </div>
      <span class="text-slate-300 dark:text-slate-700">•</span>
      <span>Debian 13 + PHP 8.4</span>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { Github, ExternalLink } from 'lucide-vue-next';
</script>
