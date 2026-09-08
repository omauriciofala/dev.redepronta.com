<template>
  <div class="flex h-screen w-screen overflow-hidden bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans antialiased">
    <!-- Sidebar Fixa com 240px -->
    <Sidebar />

    <!-- Conteúdo Central: Simetria rigorosa de 60px à esquerda e 60px à direita -->
    <main class="flex-1 flex flex-col h-full overflow-y-auto pb-12 px-[60px]">
      <slot />
    </main>

    <!-- Rodapé Fixo -->
    <FooterBar />
  </div>
</template>

<script setup lang="ts">
import Sidebar from './Sidebar.vue';
import FooterBar from './FooterBar.vue';
</script>
