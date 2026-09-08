<template>
  <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex"
        :class="[
          size === 'drawer' ? 'justify-end items-stretch p-0' : 'items-center justify-center p-3 sm:p-4 md:p-6'
        ]"
        @click.self="handleBackdropClick"
        @keydown.esc="handleEsc"
        tabindex="-1"
      >
        <Transition :name="size === 'drawer' ? 'drawer-slide' : 'modal-scale'">
          <div
            v-if="modelValue"
            class="relative flex flex-col bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl transition-colors duration-200"
            :class="[
              modalSizeClasses,
              size === 'drawer' ? 'rounded-l-2xl border-r-0' : 'rounded-2xl overflow-hidden'
            ]"
            role="dialog"
            aria-modal="true"
          >
            <!-- Header do Modal -->
            <div
              v-if="$slots.header || title"
              class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-4 shrink-0 bg-slate-50/50 dark:bg-slate-900/50"
            >
              <slot name="header">
                <div>
                  <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 tracking-tight flex items-center gap-2.5">
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
                class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer shrink-0"
                title="Fechar modal (Esc)"
              >
                <X class="w-5 h-5" />
              </button>
            </div>

            <!-- Abas Opcionais de Navegação Interna -->
            <div v-if="$slots.tabs" class="border-b border-slate-100 dark:border-slate-800/80 px-6 shrink-0 bg-slate-50/30 dark:bg-slate-900/30">
              <slot name="tabs" />
            </div>

            <!-- Corpo com Rolagem Independente -->
            <div class="flex-1 overflow-y-auto px-6 py-5 text-sm text-slate-700 dark:text-slate-300 space-y-4">
              <slot />
            </div>

            <!-- Rodapé Fixo do Modal -->
            <div
              v-if="$slots.footer"
              class="px-6 py-3.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-3 shrink-0 bg-slate-50/80 dark:bg-slate-900/80"
            >
              <slot name="footer" />
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
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
    case 'fullscreen':
      // Sobrepõe quase toda a área da página no navegador (margem elegante e confortável)
      return 'w-[calc(100vw-1.5rem)] sm:w-[calc(100vw-3rem)] lg:w-[calc(100vw-5rem)] h-[calc(100vh-1.5rem)] sm:h-[calc(100vh-3rem)] lg:h-[calc(100vh-4rem)] my-auto max-w-7xl max-h-[96vh]';
    case 'drawer':
      return 'w-full max-w-lg md:max-w-xl h-full';
    default:
      return 'w-full max-w-3xl my-auto max-h-[90vh]';
  }
});
</script>

<style scoped>
/* Fade do Backdrop */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

/* Escala Suave para Modais Centrais */
.modal-scale-enter-active,
.modal-scale-leave-active {
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}
.modal-scale-enter-from,
.modal-scale-leave-to {
  opacity: 0;
  transform: scale(0.97) translateY(6px);
}

/* Deslize do Drawer Lateral */
.drawer-slide-enter-active,
.drawer-slide-leave-active {
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.drawer-slide-enter-from,
.drawer-slide-leave-to {
  transform: translateX(100%);
}
</style>
