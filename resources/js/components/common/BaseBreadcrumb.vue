<template>
  <nav aria-label="Breadcrumb" class="flex items-center text-xs font-medium text-slate-500 dark:text-slate-400 select-none">
    <ol class="flex items-center gap-1.5 flex-wrap">
      <li
        v-for="(item, index) in items"
        :key="index"
        class="inline-flex items-center gap-1.5"
      >
        <!-- Item Intermediário / Clicável -->
        <template v-if="index < items.length - 1">
          <a
            :href="item.href || item.to || '#'"
            class="inline-flex items-center gap-1 hover:text-[#FC6714] dark:hover:text-[#FC6714] transition-colors duration-150 focus:outline-none focus:underline"
            :title="item.label"
          >
            <component
              :is="item.icon || (index === 0 && showHomeIcon ? Home : null)"
              v-if="item.icon || (index === 0 && showHomeIcon)"
              class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 group-hover:text-[#FC6714]"
              aria-hidden="true"
            />
            <span>{{ item.label }}</span>
          </a>

          <!-- Separador Suave ChevronRight -->
          <ChevronRight class="w-3.5 h-3.5 text-slate-400/80 dark:text-slate-600 shrink-0" aria-hidden="true" />
        </template>

        <!-- Último Item: Página Atual / Ativa -->
        <template v-else>
          <span
            class="inline-flex items-center gap-1 text-slate-800 dark:text-slate-200 font-semibold"
            aria-current="page"
          >
            <component
              :is="item.icon"
              v-if="item.icon"
              class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400"
              aria-hidden="true"
            />
            <span>{{ item.label }}</span>
          </span>
        </template>
      </li>
    </ol>
  </nav>
</template>

<script setup lang="ts">
import { Home, ChevronRight } from 'lucide-vue-next';

export interface BreadcrumbItem {
  label: string;
  href?: string;
  to?: string;
  icon?: any;
}

withDefaults(
  defineProps<{
    items: BreadcrumbItem[];
    showHomeIcon?: boolean;
  }>(),
  {
    showHomeIcon: true,
  }
);
</script>
