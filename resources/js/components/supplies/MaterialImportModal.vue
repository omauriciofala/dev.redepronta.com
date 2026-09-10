<template>
  <BaseModal
    :model-value="isOpen"
    @update:model-value="handleClose"
    title="Importação de Materiais via Planilha"
    description="Baixe a planilha modelo padrão, preencha o código e nome dos itens e faça o upload para o catálogo"
    size="xl"
  >
    <!-- Header Badge -->
    <template #header-badge>
      <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-50 text-[#FC6714] border border-orange-200 dark:bg-[#FC6714]/15 dark:text-orange-300 dark:border-[#FC6714]/30">
        <FileSpreadsheet class="w-3.5 h-3.5" />
        Importador WMS
      </span>
    </template>

    <div class="space-y-6 text-xs">
      <!-- MENSAGEM DE ERRO GERAL -->
      <div
        v-if="errorMessage"
        class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 font-medium flex items-start gap-3"
      >
        <AlertCircle class="w-5 h-5 shrink-0 text-rose-600 dark:text-rose-400 mt-0.5" />
        <div class="space-y-1">
          <p class="font-bold">Não foi possível processar a importação</p>
          <p class="text-xs">{{ errorMessage }}</p>
        </div>
      </div>

      <!-- PAINEL DE SUCESSO / RELATÓRIO PÓS-IMPORTAÇÃO -->
      <div
        v-if="importResult"
        class="p-5 rounded-xl border transition-all"
        :class="importResult.failed_count === 0
          ? 'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200'
          : 'bg-amber-50/80 dark:bg-amber-950/30 border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200'"
      >
        <div class="flex items-center gap-3 mb-4">
          <div
            class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
            :class="importResult.failed_count === 0
              ? 'bg-emerald-600 text-white'
              : 'bg-amber-500 text-white'"
          >
            <CheckCircle2 v-if="importResult.failed_count === 0" class="w-5 h-5" />
            <AlertTriangle v-else class="w-5 h-5" />
          </div>
          <div>
            <h4 class="text-sm font-bold">
              {{ importResult.failed_count === 0 ? 'Importação finalizada com sucesso!' : 'Importação concluída com observações' }}
            </h4>
            <p class="text-xs opacity-90 mt-0.5">
              Foram processadas {{ importResult.total_rows }} linha(s) da planilha enviada.
            </p>
          </div>
        </div>

        <!-- Indicadores de Resultado -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
          <div class="p-3 rounded-lg bg-white/70 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Novos Cadastrados</span>
            <span class="text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ importResult.imported_count }}</span>
          </div>
          <div class="p-3 rounded-lg bg-white/70 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Atualizados</span>
            <span class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ importResult.updated_count }}</span>
          </div>
          <div class="p-3 rounded-lg bg-white/70 dark:bg-slate-900/60 border border-slate-200/60 dark:border-slate-800">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Linhas com Inconsistência</span>
            <span class="text-xl font-bold text-rose-600 dark:text-rose-400">{{ importResult.failed_count }}</span>
          </div>
        </div>

        <!-- Tabela de Erros por Linha -->
        <div v-if="importResult.errors && importResult.errors.length > 0" class="mt-4 space-y-2">
          <span class="text-xs font-bold text-rose-700 dark:text-rose-300 block">
            Detalhamento de linhas não importadas:
          </span>
          <div class="max-h-40 overflow-y-auto rounded-lg border border-rose-200 dark:border-rose-900/60 bg-white dark:bg-slate-900 p-2 text-xs">
            <div
              v-for="(err, idx) in importResult.errors"
              :key="idx"
              class="py-1 px-2 flex items-center justify-between border-b last:border-0 border-slate-100 dark:border-slate-800 text-slate-700 dark:text-slate-300"
            >
              <span class="font-mono font-bold text-slate-500">Linha {{ err.line }}</span>
              <span v-if="err.code" class="font-mono text-xs text-[#FC6714]">{{ err.code }}</span>
              <span class="text-rose-600 dark:text-rose-400">{{ err.message }}</span>
            </div>
          </div>
        </div>

        <div class="mt-4 flex justify-end">
          <button
            type="button"
            @click="resetFormAndClose"
            class="px-4 py-2 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] text-white font-semibold text-xs transition cursor-pointer shadow-xs"
          >
            Concluir e Ver Materiais
          </button>
        </div>
      </div>

      <!-- ETAPAS DO FLUXO DE IMPORTAÇÃO (VISÍVEL QUANDO NÃO HÁ RESULTADO FINAL) -->
      <div v-else class="space-y-6">
        <!-- PASSO 1: BAIXAR PLANILHA MODELO -->
        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/40 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="space-y-1">
            <div class="flex items-center gap-2 text-slate-800 dark:text-slate-200 font-bold text-xs uppercase tracking-wider">
              <span class="w-5 h-5 rounded-full bg-[#FC6714]/15 text-[#FC6714] flex items-center justify-center font-bold text-[11px]">1</span>
              <span>Planilha Modelo Padrão</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
              Utilize nossa planilha modelo pré-formatada com as colunas obrigatórias <strong class="text-slate-700 dark:text-slate-300">Código</strong> (ou CÓD.) e <strong class="text-slate-700 dark:text-slate-300">Nome do Material</strong> (ou DESCRIÇÃO).
            </p>
          </div>

          <div class="flex items-center gap-2 shrink-0">
            <button
              type="button"
              @click="downloadTemplate('xlsx')"
              :disabled="isDownloading"
              class="inline-flex items-center justify-center gap-2 h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold text-xs shadow-2xs transition cursor-pointer disabled:opacity-60"
              title="Baixar modelo em formato Excel (.xlsx)"
            >
              <Download class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
              <span>Modelo Excel (.xlsx)</span>
            </button>

            <button
              type="button"
              @click="downloadTemplate('csv')"
              :disabled="isDownloading"
              class="inline-flex items-center justify-center gap-2 h-10 px-3.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 font-semibold text-xs shadow-2xs transition cursor-pointer disabled:opacity-60"
              title="Baixar modelo em formato CSV (.csv)"
            >
              <Download class="w-4 h-4 text-[#FC6714]" />
              <span>Modelo (.csv)</span>
            </button>
          </div>
        </div>

        <!-- PASSO 2: UPLOAD DA PLANILHA -->
        <div class="space-y-2">
          <div class="flex items-center gap-2 text-slate-800 dark:text-slate-200 font-bold text-xs uppercase tracking-wider mb-2">
            <span class="w-5 h-5 rounded-full bg-[#FC6714]/15 text-[#FC6714] flex items-center justify-center font-bold text-[11px]">2</span>
            <span>Upload da Planilha Preenchida</span>
          </div>

          <!-- Dropzone -->
          <div
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleFileDrop"
            @click="triggerFileInput"
            class="relative border-2 border-dashed rounded-xl p-6 sm:p-8 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-3"
            :class="isDragging
              ? 'border-[#FC6714] bg-orange-50/50 dark:bg-[#FC6714]/10 scale-[1.01]'
              : selectedFile
                ? 'border-emerald-300 dark:border-emerald-800 bg-emerald-50/30 dark:bg-emerald-950/20'
                : 'border-slate-300 dark:border-slate-700 hover:border-slate-400 dark:hover:border-slate-600 bg-white dark:bg-slate-900/40'"
          >
            <input
              ref="fileInputRef"
              type="file"
              accept=".xlsx,.csv,.txt"
              class="hidden"
              @change="handleFileSelect"
            />

            <!-- Ícone de Estado -->
            <div
              class="w-12 h-12 rounded-xl flex items-center justify-center shadow-2xs"
              :class="selectedFile
                ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400'
                : 'bg-orange-50 text-[#FC6714] dark:bg-[#FC6714]/15 dark:text-orange-400'"
            >
              <FileCheck v-if="selectedFile" class="w-6 h-6" />
              <UploadCloud v-else class="w-6 h-6" />
            </div>

            <!-- Informações do Arquivo ou Chamada -->
            <div v-if="selectedFile" class="space-y-1">
              <p class="font-bold text-slate-800 dark:text-slate-100 text-sm">
                {{ selectedFile.name }}
              </p>
              <p class="text-xs text-slate-500 dark:text-slate-400">
                Tamanho: {{ formatFileSize(selectedFile.size) }} • Arquivo pronto para importação
              </p>
              <button
                type="button"
                @click.stop="clearSelectedFile"
                class="mt-2 inline-flex items-center gap-1 text-xs text-rose-600 hover:text-rose-700 dark:text-rose-400 font-medium cursor-pointer"
              >
                <X class="w-3.5 h-3.5" />
                <span>Substituir arquivo</span>
              </button>
            </div>
            <div v-else class="space-y-1">
              <p class="font-semibold text-slate-700 dark:text-slate-200 text-sm">
                Clique aqui para selecionar ou arraste o arquivo CSV
              </p>
              <p class="text-xs text-slate-400 dark:text-slate-500">
                Formatos aceitos: <strong>.CSV</strong> ou <strong>.TXT</strong> (separado por ponto-e-vírgula ou vírgula) até 10MB
              </p>
            </div>
          </div>
        </div>

        <!-- PRÉVIA DOS DADOS (PREVIEW DAS PRIMEIRAS LINHAS) -->
        <div v-if="previewRows.length > 0" class="space-y-2">
          <div class="flex items-center justify-between">
            <span class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[11px] flex items-center gap-1.5">
              <Eye class="w-3.5 h-3.5 text-[#FC6714]" />
              Pré-visualização do Arquivo (Primeiras {{ previewRows.length }} linhas)
            </span>
            <span class="text-slate-400 text-[11px]">
              Verifique se as colunas foram interpretadas corretamente
            </span>
          </div>

          <div class="rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden bg-white dark:bg-slate-900 shadow-2xs">
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse text-xs">
                <thead>
                  <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900 font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[11px]">
                    <th class="py-2 px-3">Código</th>
                    <th class="py-2 px-3">Nome do Material</th>
                    <th class="py-2 px-3">Categoria</th>
                    <th class="py-2 px-3">Unid.</th>
                    <th class="py-2 px-3 text-center">Serial?</th>
                    <th class="py-2 px-3 text-right">Est. Mín.</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                  <tr v-for="(row, idx) in previewRows" :key="idx" class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30">
                    <td class="py-2 px-3 font-mono font-bold text-[#FC6714]">{{ row.code || '-' }}</td>
                    <td class="py-2 px-3 font-medium text-slate-800 dark:text-slate-200">{{ row.name || '-' }}</td>
                    <td class="py-2 px-3 text-slate-500">{{ row.category || 'Geral / Diversos' }}</td>
                    <td class="py-2 px-3 font-mono text-slate-600 dark:text-slate-300">{{ row.unit || 'UN' }}</td>
                    <td class="py-2 px-3 text-center">
                      <span
                        class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold"
                        :class="row.has_serial ? 'bg-purple-50 text-purple-600 border border-purple-200' : 'bg-slate-100 text-slate-500'"
                      >
                        {{ row.has_serial ? 'SIM' : 'NÃO' }}
                      </span>
                    </td>
                    <td class="py-2 px-3 text-right font-mono">{{ row.min_stock || '0' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- OPÇÃO DE ATUALIZAÇÃO DE MATERIAIS EXISTENTES -->
        <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/70 flex items-center justify-between gap-4">
          <div>
            <label for="chkUpdateExisting" class="font-bold text-slate-800 dark:text-slate-200 text-xs cursor-pointer select-none">
              Atualizar materiais se o Código já existir
            </label>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
              Se marcado, materiais com o mesmo código terão o nome e parâmetros atualizados. Caso desmarcado, serão mantidos como estão.
            </p>
          </div>
          <input
            id="chkUpdateExisting"
            type="checkbox"
            v-model="updateExisting"
            class="w-4.5 h-4.5 rounded border-slate-300 text-[#FC6714] focus:ring-[#FC6714] cursor-pointer"
          />
        </div>
      </div>
    </div>

    <!-- BOTÕES DE AÇÃO DO RODAPÉ -->
    <template #footer>
      <div class="flex items-center justify-end gap-3 w-full">
        <button
          type="button"
          @click="handleClose"
          :disabled="isUploading"
          class="h-10 px-4 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs font-semibold transition cursor-pointer disabled:opacity-60"
        >
          {{ importResult ? 'Fechar' : 'Cancelar' }}
        </button>

        <button
          v-if="!importResult"
          type="button"
          @click="submitImport"
          :disabled="!selectedFile || isUploading"
          class="inline-flex items-center gap-2 h-10 px-5 rounded-lg bg-[#FC6714] hover:bg-[#E0530A] active:bg-[#C94605] text-white text-xs font-semibold shadow-xs transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <Loader2 v-if="isUploading" class="w-4 h-4 animate-spin" />
          <Upload v-else class="w-4 h-4" />
          <span>{{ isUploading ? 'Processando Importação...' : 'Importar Materiais' }}</span>
        </button>
      </div>
    </template>
  </BaseModal>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import BaseModal from '@/components/common/BaseModal.vue';
import {
  FileSpreadsheet,
  Download,
  UploadCloud,
  FileCheck,
  Eye,
  AlertCircle,
  AlertTriangle,
  CheckCircle2,
  Upload,
  Loader2,
  X,
} from 'lucide-vue-next';

const props = defineProps<{
  isOpen: boolean;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'imported', result: any): void;
}>();

const fileInputRef = ref<HTMLInputElement | null>(null);
const selectedFile = ref<File | null>(null);
const isDragging = ref(false);
const isDownloading = ref(false);
const isUploading = ref(false);
const updateExisting = ref(true);
const errorMessage = ref('');
const importResult = ref<any | null>(null);

interface PreviewItem {
  code: string;
  name: string;
  category?: string;
  unit?: string;
  has_serial?: boolean;
  unit_cost?: string;
  min_stock?: string;
}
const previewRows = ref<PreviewItem[]>([]);

function handleClose() {
  if (isUploading.value) return;
  emit('close');
  // Se já concluiu a importação e está fechando, reseta estado após breve intervalo
  if (importResult.value) {
    setTimeout(() => {
      resetState();
    }, 300);
  }
}

function resetFormAndClose() {
  const res = importResult.value;
  emit('close');
  emit('imported', res);
  setTimeout(() => {
    resetState();
  }, 300);
}

function resetState() {
  selectedFile.value = null;
  previewRows.value = [];
  errorMessage.value = '';
  importResult.value = null;
  isDragging.value = false;
  isUploading.value = false;
  if (fileInputRef.value) {
    fileInputRef.value.value = '';
  }
}

function triggerFileInput() {
  fileInputRef.value?.click();
}

function handleFileDrop(e: DragEvent) {
  isDragging.value = false;
  if (e.dataTransfer && e.dataTransfer.files.length > 0) {
    processSelectedFile(e.dataTransfer.files[0]);
  }
}

function handleFileSelect(e: Event) {
  const target = e.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    processSelectedFile(target.files[0]);
  }
}

function processSelectedFile(file: File) {
  errorMessage.value = '';
  importResult.value = null;

  const ext = file.name.split('.').pop()?.toLowerCase();
  if (!ext || !['xlsx', 'csv', 'txt'].includes(ext)) {
    errorMessage.value = 'O arquivo selecionado deve ser no formato Excel (.xlsx) ou CSV (.csv, .txt).';
    selectedFile.value = null;
    previewRows.value = [];
    return;
  }

  selectedFile.value = file;
  if (ext === 'xlsx') {
    previewRows.value = [
      {
        code: 'Planilha Excel (.xlsx)',
        name: 'Arquivo carregado com sucesso. O sistema processará automaticamente as colunas de Código e Nome.',
        category: 'Leitura Direta',
        unit: 'OK',
      },
    ];
  } else {
    parseFilePreview(file);
  }
}

function clearSelectedFile() {
  selectedFile.value = null;
  previewRows.value = [];
  errorMessage.value = '';
  if (fileInputRef.value) {
    fileInputRef.value.value = '';
  }
}

function formatFileSize(bytes: number): string {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

// Faz o parsing das primeiras linhas no cliente para prévia instantânea de CSV
function parseFilePreview(file: File) {
  const reader = new FileReader();
  reader.onload = (e) => {
    try {
      const text = (e.target?.result as string) || '';
      const lines = text.split(/\r\n|\r|\n/).map(l => l.trim()).filter(l => l.length > 0);
      if (lines.length < 2) {
        previewRows.value = [];
        return;
      }

      const firstLine = lines[0];
      const delimiter = firstLine.includes(';') ? ';' : (firstLine.includes('\t') ? '\t' : ',');
      const headers = firstLine.split(delimiter).map(h => h.replace(/^["']|["']$/g, '').trim().toLowerCase());

      const codeIdx = headers.findIndex(h => ['código', 'codigo', 'code', 'sku', 'cód.', 'cód', 'cod'].includes(h));
      const nameIdx = headers.findIndex(h => ['nome do material', 'nome', 'material', 'name', 'descrição', 'descricao', 'desc'].includes(h));
      const catIdx = headers.findIndex(h => ['categoria', 'category', 'cat'].includes(h));
      const unitIdx = headers.findIndex(h => ['unidade', 'unid', 'unit', 'medida'].includes(h));
      const serialIdx = headers.findIndex(h => ['serializado', 'serializado (s/n)', 'serial', 'has_serial'].includes(h));
      const costIdx = headers.findIndex(h => ['custo unitário', 'custo unitario', 'custo', 'unit_cost'].includes(h));
      const minStockIdx = headers.findIndex(h => ['estoque mínimo', 'estoque minimo', 'min_stock'].includes(h));

      const preview: PreviewItem[] = [];
      const rowsToScan = lines.slice(1, 6);

      for (const line of rowsToScan) {
        const cols = line.split(delimiter).map(c => c.replace(/^["']|["']$/g, '').trim());
        const code = codeIdx >= 0 && cols[codeIdx] ? cols[codeIdx] : (cols[0] || '');
        const name = nameIdx >= 0 && cols[nameIdx] ? cols[nameIdx] : (cols[1] || '');

        const serialRaw = serialIdx >= 0 && cols[serialIdx] ? cols[serialIdx].toUpperCase() : '';
        const hasSerial = ['S', 'SIM', 'TRUE', '1', 'Y'].includes(serialRaw);

        preview.push({
          code,
          name,
          category: catIdx >= 0 ? cols[catIdx] : '',
          unit: unitIdx >= 0 ? cols[unitIdx] : '',
          has_serial: hasSerial,
          unit_cost: costIdx >= 0 ? cols[costIdx] : '',
          min_stock: minStockIdx >= 0 ? cols[minStockIdx] : '',
        });
      }

      previewRows.value = preview;
    } catch (err) {
      console.warn('Erro ao gerar preview do arquivo:', err);
      previewRows.value = [];
    }
  };
  reader.readAsText(file.slice(0, 1024 * 50)); // Lê primeiros 50KB para prévia
}

// Download da planilha modelo (XLSX ou CSV)
async function downloadTemplate(format: 'xlsx' | 'csv' = 'xlsx') {
  if (isDownloading.value) return;
  isDownloading.value = true;
  errorMessage.value = '';

  try {
    const response = await fetch(`/api/v1/materials/import-template?format=${format}`);
    if (!response.ok) {
      throw new Error(`Erro ao baixar modelo (Status ${response.status})`);
    }

    const blob = await response.blob();
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `modelo_importacao_materiais.${format}`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
  } catch (err: any) {
    console.error('Falha ao baixar planilha modelo:', err);
    errorMessage.value = 'Não foi possível baixar o modelo de planilha. Tente novamente.';
  } finally {
    isDownloading.value = false;
  }
}

// Envia arquivo para importação
async function submitImport() {
  if (!selectedFile.value || isUploading.value) return;

  isUploading.value = true;
  errorMessage.value = '';
  importResult.value = null;

  try {
    const formData = new FormData();
    formData.append('file', selectedFile.value);
    formData.append('update_existing', updateExisting.value ? '1' : '0');

    // Token CSRF se disponível
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const headers: Record<string, string> = {
      'Accept': 'application/json',
    };
    if (token) {
      headers['X-CSRF-TOKEN'] = token;
    }

    const response = await fetch('/api/v1/materials/import', {
      method: 'POST',
      headers,
      body: formData,
    });

    const json = await response.json();

    if (!response.ok) {
      throw new Error(json.message || `Erro ${response.status} ao processar arquivo`);
    }

    importResult.value = json.data;
    emit('imported', json.data);
  } catch (err: any) {
    console.error('Erro na importação:', err);
    errorMessage.value = err.message || 'Ocorreu um erro ao importar a planilha. Verifique o formato e tente novamente.';
  } finally {
    isUploading.value = false;
  }
}
</script>
