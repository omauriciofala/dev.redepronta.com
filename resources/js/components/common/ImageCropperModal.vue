<template>
  <BaseModal
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    size="xl"
    :title="modalTitle"
    :description="modalDescription"
  >
    <div class="space-y-5">
      <!-- Container Principal: Área de Recorte e Painel Lateral de Preview -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- Coluna Esquerda (Área do Canvas Interativo) -->
        <div class="lg:col-span-8 flex flex-col space-y-3">
          <div
            ref="viewportContainerRef"
            class="relative w-full h-[280px] sm:h-[340px] rounded-xl bg-slate-950 border border-slate-800 overflow-hidden select-none flex items-center justify-center cursor-grab active:cursor-grabbing"
            @mousedown="onMouseDown"
            @touchstart.passive="onTouchStart"
            @wheel.prevent="onWheel"
          >
            <!-- Fundo Xadrez Sutil para transparência -->
            <div
              class="absolute inset-0 opacity-15 pointer-events-none"
              style="background-image: linear-gradient(45deg, #334155 25%, transparent 25%), linear-gradient(-45deg, #334155 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #334155 75%), linear-gradient(-45deg, transparent 75%, #334155 75%); background-size: 16px 16px; background-position: 0 0, 0 8px, 8px -8px, -8px 0px;"
            ></div>

            <!-- Imagem Manipulável -->
            <img
              v-if="imageLoaded && imageSrc"
              ref="imageElementRef"
              :src="imageSrc"
              alt="Imagem para recorte"
              class="absolute max-w-none pointer-events-none transition-transform duration-75 origin-center"
              :style="imageTransformStyle"
              @load="onImageLoaded"
            />

            <!-- Loading overlay se a imagem estiver carregando -->
            <div v-if="!imageLoaded" class="flex flex-col items-center gap-2 text-slate-400">
              <RefreshCw class="w-6 h-6 animate-spin text-[#FC6714]" />
              <span class="text-xs">Carregando imagem...</span>
            </div>

            <!-- Overlay Escuro ao Redor com Máscara e Moldura de Corte Central -->
            <div
              v-if="imageLoaded"
              class="absolute pointer-events-none border-2 border-[#FC6714] shadow-[0_0_0_9999px_rgba(7,11,20,0.75)] rounded-lg transition-all"
              :style="cropBoxStyle"
            >
              <!-- Linhas Guia da Regra dos Terços -->
              <div class="absolute inset-0 grid grid-cols-3 grid-rows-3 pointer-events-none opacity-25">
                <div class="border-r border-b border-white"></div>
                <div class="border-r border-b border-white"></div>
                <div class="border-b border-white"></div>
                <div class="border-r border-b border-white"></div>
                <div class="border-r border-b border-white"></div>
                <div class="border-b border-white"></div>
                <div class="border-r border-white"></div>
                <div class="border-r border-white"></div>
                <div></div>
              </div>

              <!-- Marcadores de cantos discretos -->
              <div class="absolute -top-1 -left-1 w-3 h-3 border-t-2 border-l-2 border-white"></div>
              <div class="absolute -top-1 -right-1 w-3 h-3 border-t-2 border-r-2 border-white"></div>
              <div class="absolute -bottom-1 -left-1 w-3 h-3 border-b-2 border-l-2 border-white"></div>
              <div class="absolute -bottom-1 -right-1 w-3 h-3 border-b-2 border-r-2 border-white"></div>

              <!-- Indicador de formato no centro superior -->
              <div class="absolute -top-6 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded text-[10px] font-bold bg-[#FC6714] text-white uppercase tracking-wider shadow-sm">
                {{ formatLabel }}
              </div>
            </div>
          </div>

          <!-- Barra de Ferramentas e Controles de Zoom/Rotação -->
          <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3 text-xs">
            <!-- Zoom Controls -->
            <div class="flex items-center gap-2 flex-1 min-w-[200px]">
              <button
                type="button"
                @click="zoomOut"
                title="Diminuir Zoom"
                class="w-7 h-7 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 flex items-center justify-center hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer"
              >
                <ZoomOut class="w-3.5 h-3.5" />
              </button>

              <input
                type="range"
                v-model.number="scale"
                :min="minScale"
                :max="maxScale"
                step="0.02"
                class="flex-1 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-[#FC6714]"
              />

              <button
                type="button"
                @click="zoomIn"
                title="Aumentar Zoom"
                class="w-7 h-7 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 flex items-center justify-center hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer"
              >
                <ZoomIn class="w-3.5 h-3.5" />
              </button>

              <span class="text-[11px] font-mono font-medium text-slate-500 dark:text-slate-400 w-11 text-right">
                {{ Math.round(scale * 100) }}%
              </span>
            </div>

            <!-- Rotação e Reset -->
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                type="button"
                @click="rotateCounterClockwise"
                title="Girar 90° anti-horário"
                class="h-7 px-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 flex items-center gap-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer"
              >
                <RotateCcw class="w-3.5 h-3.5" />
                <span class="hidden sm:inline text-[11px]">90°</span>
              </button>

              <button
                type="button"
                @click="rotateClockwise"
                title="Girar 90° horário"
                class="h-7 px-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 flex items-center gap-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer"
              >
                <RotateCw class="w-3.5 h-3.5" />
                <span class="hidden sm:inline text-[11px]">90°</span>
              </button>

              <button
                type="button"
                @click="resetTransforms"
                title="Restaurar tamanho e posição original"
                class="h-7 px-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-[#FC6714] dark:hover:text-[#FC6714] flex items-center gap-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer"
              >
                <RefreshCw class="w-3.5 h-3.5" />
                <span class="text-[11px]">Reset</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Coluna Direita: Previews em Tempo Real de Aplicação no ERP -->
        <div class="lg:col-span-4 flex flex-col space-y-4">
          <div class="p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 space-y-3">
            <div class="flex items-center gap-2">
              <Eye class="w-4 h-4 text-[#FC6714]" />
              <h4 class="text-xs font-bold font-heading text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                Simulação em Tempo Real
              </h4>
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400">
              Veja exatamente como a imagem ficará renderizada na interface do sistema:
            </p>

            <!-- Preview Contextual 1: Topo da Sidebar (quando for logo) -->
            <div v-if="cropType === 'logo'" class="space-y-2 pt-1">
              <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                Topo da Sidebar (#070B14)
              </div>
              <div class="h-16 px-4 rounded-xl bg-[#070B14] border border-[#1E293B] flex items-center overflow-hidden shadow-inner">
                <canvas
                  ref="sidebarPreviewCanvasRef"
                  class="max-h-11 max-w-[190px] object-contain"
                ></canvas>
              </div>
            </div>

            <!-- Preview Contextual 2: Aba do Navegador (quando for favicon) -->
            <div v-else-if="cropType === 'favicon'" class="space-y-2 pt-1">
              <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                Aba do Navegador
              </div>
              <div class="h-14 rounded-xl bg-slate-200 dark:bg-slate-950 p-2 flex flex-col justify-end">
                <div class="bg-white dark:bg-slate-900 rounded-t-lg px-2.5 py-1.5 flex items-center gap-2 border-t border-x border-slate-300 dark:border-slate-700 shadow-xs max-w-[200px]">
                  <div class="w-4 h-4 rounded shrink-0 flex items-center justify-center overflow-hidden">
                    <canvas
                      ref="faviconPreviewCanvasRef"
                      class="w-full h-full object-contain"
                    ></canvas>
                  </div>
                  <span class="text-[11px] font-medium text-slate-700 dark:text-slate-300 truncate">
                    Rede Pronta ERP
                  </span>
                </div>
              </div>
            </div>

            <!-- Preview Contextual 3: Foto de Perfil (quando for avatar) -->
            <div v-else-if="cropType === 'avatar'" class="space-y-2 pt-1">
              <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                Avatar do Usuário
              </div>
              <div class="p-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center gap-3">
                <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-[#FC6714] shadow-sm shrink-0 bg-slate-100 dark:bg-slate-800">
                  <canvas
                    ref="avatarPreviewCanvasRef"
                    class="w-full h-full object-cover"
                  ></canvas>
                </div>
                <div class="truncate">
                  <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">Usuário Ativo</p>
                  <p class="text-[10px] text-slate-400 truncate">admin@redepronta.com</p>
                </div>
              </div>
            </div>

            <!-- Dicas de ergonomia visual -->
            <div class="p-2.5 rounded-lg bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/50 text-[11px] text-blue-700 dark:text-blue-300 flex items-start gap-2">
              <Sliders class="w-3.5 h-3.5 shrink-0 mt-0.5 text-blue-500" />
              <span>Arraste a imagem para enquadrar. Use a roda do mouse ou a barra de zoom para aproximar.</span>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Rodapé de Ações -->
    <template #footer>
      <div class="flex items-center justify-between w-full">
        <button
          type="button"
          @click="handleCancel"
          :disabled="isProcessing"
          class="h-9 px-4 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold transition cursor-pointer disabled:opacity-50"
        >
          Cancelar
        </button>

        <div class="flex items-center gap-2">
          <button
            type="button"
            @click="handleConfirmCrop"
            :disabled="!imageLoaded || isProcessing"
            class="inline-flex items-center gap-2 h-9 px-5 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-xs font-semibold shadow-2xs transition cursor-pointer disabled:opacity-50"
          >
            <RefreshCw v-if="isProcessing" class="w-3.5 h-3.5 animate-spin" />
            <Check v-else class="w-3.5 h-3.5" />
            <span>{{ isProcessing ? 'Processando...' : 'Aplicar e Salvar' }}</span>
          </button>
        </div>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';
import BaseModal from './BaseModal.vue';
import {
  ZoomIn,
  ZoomOut,
  RotateCcw,
  RotateCw,
  RefreshCw,
  Eye,
  Check,
  Sliders,
} from 'lucide-vue-next';

interface Props {
  modelValue: boolean;
  imageSrc: string;
  aspectRatio?: number; // Ex: 4/1 = 4 para logo, 1/1 = 1 para favicon ou avatar
  cropType?: 'logo' | 'favicon' | 'avatar';
  title?: string;
  description?: string;
  maxOutputWidth?: number;
  isProcessing?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  aspectRatio: 4, // 4:1 padrão retangular para logo
  cropType: 'logo',
  title: '',
  description: '',
  maxOutputWidth: 800,
  isProcessing: false,
});

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
  (e: 'crop', payload: { blob: Blob; file: File; dataUrl: string }): void;
  (e: 'cancel'): void;
}>();

// Títulos e Textos
const modalTitle = computed(() => {
  if (props.title) return props.title;
  if (props.cropType === 'logo') return 'Ajuste e Enquadramento do Logotipo';
  if (props.cropType === 'favicon') return 'Ajuste do Favicon';
  return 'Ajuste da Foto de Perfil';
});

const modalDescription = computed(() => {
  if (props.description) return props.description;
  if (props.cropType === 'logo') {
    return 'Enquadre o logotipo da empresa no formato ideal (4:1) para exibição na Sidebar do ERP.';
  }
  if (props.cropType === 'favicon') {
    return 'Enquadre o ícone quadrado (1:1) para exibição na aba do navegador.';
  }
  return 'Enquadre a sua foto de perfil quadrada (1:1).';
});

const formatLabel = computed(() => {
  if (props.cropType === 'logo') return '4:1 Logotipo Sidebar';
  if (props.cropType === 'favicon') return '1:1 Favicon';
  return '1:1 Avatar';
});

// Elementos do DOM
const viewportContainerRef = ref<HTMLDivElement | null>(null);
const imageElementRef = ref<HTMLImageElement | null>(null);
const sidebarPreviewCanvasRef = ref<HTMLCanvasElement | null>(null);
const faviconPreviewCanvasRef = ref<HTMLCanvasElement | null>(null);
const avatarPreviewCanvasRef = ref<HTMLCanvasElement | null>(null);

// Estado de manipulação da imagem
const imageLoaded = ref(false);
const naturalWidth = ref(1);
const naturalHeight = ref(1);

const scale = ref(1);
const minScale = ref(0.2);
const maxScale = ref(3.5);
const rotation = ref(0); // 0, 90, 180, 270
const translateX = ref(0);
const translateY = ref(0);

// Dimensões dinâmicas do Crop Box dentro do viewport
const cropBoxWidth = ref(280);
const cropBoxHeight = ref(70);

// Estado de Arraste (Drag / Pan)
const isDragging = ref(false);
const dragStartX = ref(0);
const dragStartY = ref(0);
const dragInitialTranslateX = ref(0);
const dragInitialTranslateY = ref(0);

// Estilo de transformação da imagem no viewport
const imageTransformStyle = computed(() => {
  return {
    transform: `translate(${translateX.value}px, ${translateY.value}px) rotate(${rotation.value}deg) scale(${scale.value})`,
  };
});

// Estilo e posicionamento da moldura de corte
const cropBoxStyle = computed(() => {
  return {
    width: `${cropBoxWidth.value}px`,
    height: `${cropBoxHeight.value}px`,
    left: '50%',
    top: '50%',
    transform: 'translate(-50%, -50%)',
  };
});

// Carregamento da Imagem
function onImageLoaded() {
  if (!imageElementRef.value) return;
  naturalWidth.value = imageElementRef.value.naturalWidth || 400;
  naturalHeight.value = imageElementRef.value.naturalHeight || 100;
  imageLoaded.value = true;
  calculateCropBoxDimensions();
  resetTransforms();
  updateLivePreview();
}

// Calcula o tamanho da caixa de corte com base na proporção e no tamanho do viewport
function calculateCropBoxDimensions() {
  if (!viewportContainerRef.value) return;
  const vpWidth = viewportContainerRef.value.clientWidth || 400;
  const vpHeight = viewportContainerRef.value.clientHeight || 300;

  const targetRatio = props.aspectRatio || 4;
  const padding = 40;
  const availWidth = vpWidth - padding * 2;
  const availHeight = vpHeight - padding * 2;

  let w = availWidth;
  let h = w / targetRatio;

  if (h > availHeight) {
    h = availHeight;
    w = h * targetRatio;
  }

  cropBoxWidth.value = Math.round(w);
  cropBoxHeight.value = Math.round(h);
}

// Reset de transformações para ajustar a imagem ao crop box
function resetTransforms() {
  rotation.value = 0;
  translateX.value = 0;
  translateY.value = 0;

  if (!naturalWidth.value || !naturalHeight.value) {
    scale.value = 1;
    return;
  }

  // Define escala inicial para cobrir o crop box
  const scaleX = cropBoxWidth.value / naturalWidth.value;
  const scaleY = cropBoxHeight.value / naturalHeight.value;
  const fitScale = Math.max(scaleX, scaleY, 0.5);

  scale.value = Number(fitScale.toFixed(2));
  minScale.value = Number((fitScale * 0.4).toFixed(2));
  maxScale.value = Number((fitScale * 4.0).toFixed(2));

  nextTick(() => {
    updateLivePreview();
  });
}

// Controles de Zoom
function zoomIn() {
  scale.value = Math.min(maxScale.value, Number((scale.value + 0.1).toFixed(2)));
  updateLivePreview();
}

function zoomOut() {
  scale.value = Math.max(minScale.value, Number((scale.value - 0.1).toFixed(2)));
  updateLivePreview();
}

function onWheel(e: WheelEvent) {
  const delta = e.deltaY > 0 ? -0.05 : 0.05;
  const nextScale = Math.max(minScale.value, Math.min(maxScale.value, scale.value + delta));
  scale.value = Number(nextScale.toFixed(2));
  updateLivePreview();
}

// Rotação
function rotateClockwise() {
  rotation.value = (rotation.value + 90) % 360;
  updateLivePreview();
}

function rotateCounterClockwise() {
  rotation.value = (rotation.value - 90 + 360) % 360;
  updateLivePreview();
}

// Arraste com Mouse
function onMouseDown(e: MouseEvent) {
  isDragging.value = true;
  dragStartX.value = e.clientX;
  dragStartY.value = e.clientY;
  dragInitialTranslateX.value = translateX.value;
  dragInitialTranslateY.value = translateY.value;

  window.addEventListener('mousemove', onMouseMove);
  window.addEventListener('mouseup', onMouseUp);
}

function onMouseMove(e: MouseEvent) {
  if (!isDragging.value) return;
  const deltaX = e.clientX - dragStartX.value;
  const deltaY = e.clientY - dragStartY.value;
  translateX.value = dragInitialTranslateX.value + deltaX;
  translateY.value = dragInitialTranslateY.value + deltaY;
  updateLivePreview();
}

function onMouseUp() {
  isDragging.value = false;
  window.removeEventListener('mousemove', onMouseMove);
  window.removeEventListener('mouseup', onMouseUp);
}

// Arraste com Touch (Mobile/Tablet)
function onTouchStart(e: TouchEvent) {
  if (e.touches.length !== 1) return;
  isDragging.value = true;
  dragStartX.value = e.touches[0].clientX;
  dragStartY.value = e.touches[0].clientY;
  dragInitialTranslateX.value = translateX.value;
  dragInitialTranslateY.value = translateY.value;

  window.addEventListener('touchmove', onTouchMove);
  window.addEventListener('touchend', onTouchEnd);
}

function onTouchMove(e: TouchEvent) {
  if (!isDragging.value || e.touches.length !== 1) return;
  const deltaX = e.touches[0].clientX - dragStartX.value;
  const deltaY = e.touches[0].clientY - dragStartY.value;
  translateX.value = dragInitialTranslateX.value + deltaX;
  translateY.value = dragInitialTranslateY.value + deltaY;
  updateLivePreview();
}

function onTouchEnd() {
  isDragging.value = false;
  window.removeEventListener('touchmove', onTouchMove);
  window.removeEventListener('touchend', onTouchEnd);
}

// Atualização de Previews em Tempo Real via Canvas
function updateLivePreview() {
  if (!imageLoaded.value || !imageElementRef.value) return;

  const activeCanvas =
    props.cropType === 'logo'
      ? sidebarPreviewCanvasRef.value
      : props.cropType === 'favicon'
      ? faviconPreviewCanvasRef.value
      : avatarPreviewCanvasRef.value;

  if (!activeCanvas) return;

  renderCroppedToCanvas(activeCanvas, activeCanvas.clientWidth || 180, activeCanvas.clientHeight || 44);
}

// Renderiza o recorte no Canvas alvo
function renderCroppedToCanvas(
  targetCanvas: HTMLCanvasElement,
  outWidth: number,
  outHeight: number
) {
  const img = imageElementRef.value;
  if (!img) return;

  targetCanvas.width = outWidth;
  targetCanvas.height = outHeight;
  const ctx = targetCanvas.getContext('2d');
  if (!ctx) return;

  ctx.clearRect(0, 0, outWidth, outHeight);
  ctx.imageSmoothingEnabled = true;
  ctx.imageSmoothingQuality = 'high';

  // Relação de proporção entre o canvas de saída e o crop box visual
  const ratio = outWidth / cropBoxWidth.value;

  ctx.save();
  // Centraliza o ponto de transformação no centro do canvas de saída
  ctx.translate(outWidth / 2, outHeight / 2);

  // Aplica o deslocamento relativo escalado
  ctx.translate(translateX.value * ratio, translateY.value * ratio);

  // Aplica rotação em radianos
  ctx.rotate((rotation.value * Math.PI) / 180);

  // Aplica escala
  const finalScale = scale.value * ratio;
  ctx.scale(finalScale, finalScale);

  // Desenha a imagem centralizada no seu próprio centro
  ctx.drawImage(
    img,
    -naturalWidth.value / 2,
    -naturalHeight.value / 2,
    naturalWidth.value,
    naturalHeight.value
  );

  ctx.restore();
}

// Confirmação e Exportação do Recorte Final
async function handleConfirmCrop() {
  if (!imageElementRef.value) return;

  const targetWidth = props.maxOutputWidth || 800;
  const targetHeight = Math.round(targetWidth / (props.aspectRatio || 4));

  const exportCanvas = document.createElement('canvas');
  exportCanvas.width = targetWidth;
  exportCanvas.height = targetHeight;

  renderCroppedToCanvas(exportCanvas, targetWidth, targetHeight);

  exportCanvas.toBlob((blob) => {
    if (!blob) return;

    const fileName =
      props.cropType === 'logo'
        ? 'tenant_logo_cropped.png'
        : props.cropType === 'favicon'
        ? 'tenant_favicon_cropped.png'
        : 'user_avatar_cropped.png';

    const file = new File([blob], fileName, { type: 'image/png' });
    const dataUrl = exportCanvas.toDataURL('image/png');

    emit('crop', { blob, file, dataUrl });
  }, 'image/png');
}

function handleCancel() {
  emit('cancel');
  emit('update:modelValue', false);
}

// Monitora alterações na imagem recebida ou abertura do modal
watch(
  () => props.modelValue,
  (isOpen) => {
    if (isOpen) {
      imageLoaded.value = false;
      nextTick(() => {
        calculateCropBoxDimensions();
      });
    }
  }
);

watch(
  () => [scale.value, rotation.value, translateX.value, translateY.value],
  () => {
    updateLivePreview();
  }
);

function handleResize() {
  if (props.modelValue) {
    calculateCropBoxDimensions();
    updateLivePreview();
  }
}

onMounted(() => {
  window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
  window.removeEventListener('resize', handleResize);
  window.removeEventListener('mousemove', onMouseMove);
  window.removeEventListener('mouseup', onMouseUp);
  window.removeEventListener('touchmove', onTouchMove);
  window.removeEventListener('touchend', onTouchEnd);
});
</script>
