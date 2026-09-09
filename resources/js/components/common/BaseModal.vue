<template>
  <Teleport to="body">
    <div v-if="modelValue" class="relative z-50">
      <!-- Backdrop Escuro com Blur Suave -->
      <Transition name="modal-fade">
        <div
          v-if="modelValue"
          class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity duration-200"
          @click="handleBackdropClick"
          aria-hidden="true"
        />
      </Transition>

      <!-- CASO 1: MODAL TELA CHEIA (FULLSCREEN OVERLAY) -->
      <!-- Espaçamento externo perfeitamente igual em todos os lados (topo, base, esquerda, direita) e sem restrição estreita de largura -->
      <Transition v-if="size === 'fullscreen'" name="modal-scale">
        <div
          v-if="modelValue"
          class="fixed inset-3 sm:inset-5 md:inset-6 lg:inset-8 z-50 flex flex-col bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl rounded-2xl overflow-hidden transition-colors duration-200 focus:outline-hidden"
          role="dialog"
          aria-modal="true"
          tabindex="-1"
          @keydown.esc="handleEsc"
        >
          <!-- Header do Modal Fullscreen -->
          <div
            v-if="$slots.header || title"
            class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-4 shrink-0 bg-slate-50/50 dark:bg-slate-900/50"
          >
            <slot name="header">
              <div>
                <h3 class="text-xl font-heading font-semibold text-[#06064D] dark:text-white tracking-tight flex items-center gap-2.5">
                  {{ title }}
                  <slot name="header-badge" />
                </h3>
                <p v-if="description" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
                  {{ description }}
                </p>
              </div>
            </slot>

            <button
              v-if="showCloseButton"
              type="button"
              @click="close"
              class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer shrink-0 focus:outline-hidden focus:ring-2 focus:ring-[#FC6714]/40"
              title="Fechar modal (Esc)"
            >
              <X class="w-5 h-5" />
            </button>
          </div>

          <!-- Abas de Navegação Interna -->
          <div v-if="$slots.tabs" class="border-b border-slate-100 dark:border-slate-800/80 px-6 shrink-0 bg-slate-50/30 dark:bg-slate-900/30">
            <slot name="tabs" />
          </div>

          <!-- Corpo do Modal Fullscreen com Scroll Independente -->
          <div class="flex-1 overflow-y-auto px-6 lg:px-8 py-6 text-sm text-slate-700 dark:text-slate-300 space-y-6">
            <slot />
          </div>

          <!-- Rodapé Fixo de Ações -->
          <div
            v-if="$slots.footer"
            class="px-6 lg:px-8 py-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-3 shrink-0 bg-slate-50/80 dark:bg-slate-900/80"
          >
            <slot name="footer" />
          </div>
        </div>
      </Transition>

      <!-- CASO 2: DRAWER LATERAL (SLIDE-OVER) -->
      <Transition v-else-if="size === 'drawer'" name="drawer-slide">
        <div
          v-if="modelValue"
          class="fixed inset-y-0 right-0 z-50 w-full max-w-lg md:max-w-xl h-full flex flex-col bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 shadow-2xl rounded-l-2xl overflow-hidden transition-colors duration-200 focus:outline-hidden"
          role="dialog"
          aria-modal="true"
          tabindex="-1"
          @keydown.esc="handleEsc"
        >
          <!-- Header do Drawer -->
          <div
            v-if="$slots.header || title"
            class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-4 shrink-0 bg-slate-50/50 dark:bg-slate-900/50"
          >
            <slot name="header">
              <div>
                <h3 class="text-lg font-heading font-semibold text-[#06064D] dark:text-white tracking-tight flex items-center gap-2.5">
                  {{ title }}
                  <slot name="header-badge" />
                </h3>
                <p v-if="description" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
                  {{ description }}
                </p>
              </div>
            </slot>

            <button
              v-if="showCloseButton"
              type="button"
              @click="close"
              class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer shrink-0 focus:outline-hidden focus:ring-2 focus:ring-[#FC6714]/40"
              title="Fechar (Esc)"
            >
              <X class="w-5 h-5" />
            </button>
          </div>

          <div class="flex-1 overflow-y-auto px-6 py-5 text-sm text-slate-700 dark:text-slate-300 space-y-4">
            <slot />
          </div>

          <div
            v-if="$slots.footer"
            class="px-6 py-3.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-3 shrink-0 bg-slate-50/80 dark:bg-slate-900/80"
          >
            <slot name="footer" />
          </div>
        </div>
      </Transition>

      <!-- CASO 3: MODAIS CENTRAIS PADRÃO (sm, md, lg, xl) -->
      <div
        v-else
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-3 sm:p-4 md:p-6"
        @click.self="handleBackdropClick"
      >
        <Transition name="modal-scale">
          <div
            v-if="modelValue"
            class="relative flex flex-col bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl rounded-2xl overflow-hidden transition-colors duration-200"
            :class="modalSizeClasses"
            role="dialog"
            aria-modal="true"
            tabindex="-1"
            @keydown.esc="handleEsc"
          >
            <!-- Header -->
            <div
              v-if="$slots.header || title"
              class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-4 shrink-0 bg-slate-50/50 dark:bg-slate-900/50"
            >
              <slot name="header">
                <div>
                  <h3 class="text-lg font-heading font-semibold text-[#06064D] dark:text-white tracking-tight flex items-center gap-2.5">
                    {{ title }}
                    <slot name="header-badge" />
                  </h3>
                  <p v-if="description" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
                    {{ description }}
                  </p>
                </div>
              </slot>

              <button
                v-if="showCloseButton"
                type="button"
                @click="close"
                class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer shrink-0 focus:outline-hidden focus:ring-2 focus:ring-[#FC6714]/40"
                title="Fechar modal (Esc)"
              >
                <X class="w-5 h-5" />
              </button>
            </div>

            <!-- Corpo -->
            <div class="flex-1 overflow-y-auto px-6 py-5 text-sm text-slate-700 dark:text-slate-300 space-y-4">
              <slot />
            </div>

            <!-- Rodapé -->
            <div
              v-if="$slots.footer"
              class="px-6 py-3.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-3 shrink-0 bg-slate-50/80 dark:bg-slate-900/80"
            >
              <slot name="footer" />
            </div>
          </div>
        </Transition>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { computed, onMounted, onUnmounted } from 'vue';
import { X } from 'lucide-vue-next';

export type ModalSize = 'sm' | 'md' | 'lg' | 'xl' | 'fullscreen' | 'drawer';

const props = withDefaults(
  defineProps<{
    modelValue: boolean;
    title?: string;
    description?: string;
    size?: ModalSize;
    closeOnBackdrop?: boolean;
    closeOnEsc?: boolean;
    showCloseButton?: boolean;
  }>(),
  {
    size: 'lg',
    closeOnBackdrop: true,
    closeOnEsc: true,
    showCloseButton: true,
  }
);

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
  (e: 'close'): void;
}>();

const close = () => {
  emit('update:modelValue', false);
  emit('close');
};

const handleBackdropClick = () => {
  if (props.closeOnBackdrop) {
    close();
  }
};

const handleEsc = () => {
  if (props.closeOnEsc) {
    close();
  }
};

const onKeyDown = (e: KeyboardEvent) => {
  if (e.key === 'Escape' && props.modelValue && props.closeOnEsc) {
    close();
  }
};

onMounted(() => {
  window.addEventListener('keydown', onKeyDown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', onKeyDown);
});

const modalSizeClasses = computed(() => {
  switch (props.size) {
    case 'sm':
      return 'w-full max-w-md my-auto max-h-[85vh]';
    case 'md':
      return 'w-full max-w-xl my-auto max-h-[90vh]';
    case 'lg':
      return 'w-full max-w-3xl my-auto max-h-[90vh]';
    case 'xl':
      return 'w-full max-w-5xl my-auto max-h-[92vh]';
    default:
      return 'w-full max-w-3xl my-auto max-h-[90vh]';
  }
});
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-scale-enter-active,
.modal-scale-leave-active {
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}
.modal-scale-enter-from,
.modal-scale-leave-to {
  opacity: 0;
  transform: scale(0.98) translateY(6px);
}

.drawer-slide-enter-active,
.drawer-slide-leave-active {
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.drawer-slide-enter-from,
.drawer-slide-leave-to {
  transform: translateX(100%);
}
</style>
