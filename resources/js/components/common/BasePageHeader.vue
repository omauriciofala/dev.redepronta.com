<template>
  <!-- ============================================================================= -->
  <!-- COMPONENTE CANÔNICO: 10. TOPO DE PÁGINA PADRÃO (PAGE HEADER & BREADCRUMB)     -->
  <!-- Encapsula harmoniosamente Linha 1 (Breadcrumb 100%) e Linha 2 (Page Header)   -->
  <!-- ============================================================================= -->
  <div class="w-full space-y-4 sm:space-y-5">
    <!-- Linha 1 Canônica Obrigatória: BaseBreadcrumb em Box 100% -->
    <BaseBreadcrumb :items="breadcrumbItems" :show-home-icon="showHomeIcon" />

    <!-- Linha 2 Canônica: Cabeçalho Principal da Página -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <!-- Bloco de Identificação: Ícone + Título H1 + Descrição -->
      <div class="flex items-center gap-3">
        <!-- Ícone / Box Contextual (w-11 h-11 rounded-xl) -->
        <slot name="icon">
          <div
            v-if="icon"
            class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 border shadow-2xs"
            :class="iconColorClass"
          >
            <component :is="icon" class="w-6 h-6" />
          </div>
        </slot>

        <!-- Textos e Metadados -->
        <div>
          <div class="flex items-center gap-2.5 flex-wrap">
            <slot name="title">
              <h1 class="text-xl sm:text-2xl font-bold font-heading text-[#172554] dark:text-slate-100 tracking-tight">
                {{ title }}
              </h1>
            </slot>

            <!-- Badge ou Contador ao lado do título -->
            <slot name="title-extra">
              <span
                v-if="badgeText !== undefined && badgeText !== null && badgeText !== ''"
                class="px-2 py-0.5 rounded-md text-[11px] font-bold"
                :class="badgeVariantClasses[badgeVariant]"
              >
                {{ badgeText }}
              </span>
            </slot>
          </div>

          <!-- Descrição / Subtítulo da Página -->
          <slot name="description">
            <p v-if="description" class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
              {{ description }}
            </p>
          </slot>
        </div>
      </div>

      <!-- Bloco de Ações Rápidas à Direita -->
      <div v-if="$slots.actions" class="flex items-center gap-2.5 flex-wrap shrink-0">
        <slot name="actions" />
      </div>
    </div>

    <!-- Linha 3 Opcional: Barra de Abas do Módulo -->
    <div v-if="$slots.tabs" class="pt-1">
      <slot name="tabs" />
    </div>

    <!-- Slot Opcional Inferior -->
    <div v-if="$slots.bottom">
      <slot name="bottom" />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Component } from 'vue';
import BaseBreadcrumb, { type BreadcrumbItem } from './BaseBreadcrumb.vue';

export type BadgeVariant = 'neutral' | 'success' | 'warning' | 'danger' | 'info' | 'primary';

export interface BasePageHeaderProps {
  // Linha 1: Itens de Navegação do Breadcrumb
  breadcrumbItems: BreadcrumbItem[];
  showHomeIcon?: boolean;

  // Linha 2: Informações do Cabeçalho
  title: string;
  description?: string;
  icon?: Component;
  iconColorClass?: string;

  // Badge opcional ao lado do título
  badgeText?: string | number;
  badgeVariant?: BadgeVariant;
}

const props = withDefaults(defineProps<BasePageHeaderProps>(), {
  showHomeIcon: false,
  iconColorClass: 'bg-indigo-600/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 border-indigo-200/60 dark:border-indigo-800',
  badgeVariant: 'neutral',
});

const badgeVariantClasses: Record<BadgeVariant, string> = {
  neutral: 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700',
  info: 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-900',
  success: 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800',
  warning: 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800',
  danger: 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800',
  primary: 'bg-orange-50 dark:bg-orange-950/60 text-[#FC6714] dark:text-orange-400 border border-orange-200 dark:border-orange-900/60',
};
</script>
