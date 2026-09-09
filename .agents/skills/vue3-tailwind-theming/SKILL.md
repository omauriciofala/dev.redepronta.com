---
name: vue3-tailwind-theming
description: Padrões de engenharia de frontend em Vue 3 (<script setup>), Tailwind CSS e Theming Engine para o novo SaaS. Use ao criar interfaces, componentes, temas e layouts.
---

# VUE 3 + TAILWIND CSS + THEMING ENGINE - DIRETRIZES DE FRONTEND

Este skill estabelece o padrão visual e arquitetural dos componentes Vue 3 para o novo sistema SaaS, garantindo estética premium, modo escuro nativo e suporte a diferentes densidades de tela.

---

## 🎨 1. FILOSOFIA DE DESIGN SYSTEM E THEMING

O sistema NÃO deve utilizar classes estáticas de cores como `bg-blue-600` ou `text-gray-900`. Em vez disso, utilize **Design Tokens semânticos baseados em CSS Custom Properties**:

### Variáveis CSS Semânticas (Definidas em `resources/css/theme.css`):
```css
:root {
  --color-background: 248 250 252;
  --color-surface: 255 255 255;
  --color-surface-hover: 241 245 249;
  --color-border: 226 232 240;
  
  --color-text-primary: 15 23 42;
  --color-text-secondary: 100 116 139;
  --color-text-muted: 148 163 184;

  --color-primary: 37 99 235;
  --color-primary-hover: 29 78 216;
  --color-primary-text: 255 255 255;

  --color-success: 16 185 129;
  --color-warning: 245 158 11;
  --color-danger: 239 68 68;
}

[data-theme='dark'] {
  --color-background: 15 23 42;
  --color-surface: 30 41 59;
  --color-surface-hover: 51 65 85;
  --color-border: 51 65 85;

  --color-text-primary: 248 250 252;
  --color-text-secondary: 203 213 225;
  --color-text-muted: 148 163 184;

  --color-primary: 59 130 246;
  --color-primary-hover: 96 165 250;
  --color-primary-text: 255 255 255;
}
```

---

## 📐 2. COMPOSABLE DE GERENCIAMENTO DE TEMA (`useTheme.ts`)

```typescript
import { ref, watch, onMounted } from 'vue';

export type ThemeMode = 'light' | 'dark' | 'auto';
export type LayoutDensity = 'comfortable' | 'compact';

const currentTheme = ref<ThemeMode>('auto');
const currentDensity = ref<LayoutDensity>('comfortable');

export function useTheme() {
  const setTheme = (mode: ThemeMode) => {
    currentTheme.value = mode;
    localStorage.setItem('rp_theme_mode', mode);
    applyTheme();
  };

  const setDensity = (density: LayoutDensity) => {
    currentDensity.value = density;
    localStorage.setItem('rp_layout_density', density);
    document.documentElement.setAttribute('data-density', density);
  };

  const applyTheme = () => {
    const isDark =
      currentTheme.value === 'dark' ||
      (currentTheme.value === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);

    if (isDark) {
      document.documentElement.classList.add('dark');
      document.documentElement.setAttribute('data-theme', 'dark');
    } else {
      document.documentElement.classList.remove('dark');
      document.documentElement.setAttribute('data-theme', 'light');
    }
  };

  onMounted(() => {
    const savedTheme = (localStorage.getItem('rp_theme_mode') as ThemeMode) || 'auto';
    const savedDensity = (localStorage.getItem('rp_layout_density') as LayoutDensity) || 'comfortable';
    currentTheme.value = savedTheme;
    currentDensity.value = savedDensity;
    applyTheme();
    document.documentElement.setAttribute('data-density', savedDensity);
  });

  return {
    theme: currentTheme,
    density: currentDensity,
    setTheme,
    setDensity
  };
}
```

---

## 🎛️ 3. DENSIDADE DE TELA (NOC / DESPACHO vs OPERACIONAL)

Para telas com altíssimo volume de informações (ex: Despacho de Técnicos, Monitor de Tarefas, Tabela de Ordens de Serviço), o sistema suporta **Densidade Compacta**:

- **Comfortable**: `p-4`, `h-11` inputs, fonte `text-sm`, `gap-4`.
- **Compact**: `p-2`, `h-8` inputs, fonte `text-xs`, `gap-2`.
- Utilize classes semânticas como `density-input`, `density-table-row` ou use as variantes do Tailwind mapeadas no CSS root.

---

## ⚡ 4. PADRÕES OBRIGATÓRIOS EM COMPONENTES VUE 3

1. **Estrutura `<script setup lang="ts">`**: Sempre use a Composition API moderna com TypeScript.
2. **Tratamento de Estados**:
   - **Loading**: Use esqueletos pulsantes (`<div class="animate-pulse bg-surface-hover rounded ..."></div>`) e nunca spinners bloqueantes que escondam a página.
   - **Empty State**: Sempre renderize uma ilustração suave e botão de ação quando a listagem não possuir itens.
   - **Error State**: Banner com botão "Tentar Novamente" e código amigável para suporte.
3. **Ícones**: Utilize a biblioteca padronizada `@lucide/vue` (ex: `CheckCircleIcon`, `SearchIcon`, `UsersIcon`).

---

## 📐 5. PADRÃO OBRIGATÓRIO DE LAYOUT E MARGENS DE PÁGINAS (PAGE SHELL)

1. **Margem Superior e Inferior Obrigatória (`py-8`)**:
   - Toda e qualquer view/página renderizada dentro do `<main>` do `AppLayout.vue` **DEVE OBRIGATORIAMENTE** iniciar com o container raiz:
     ```html
     <template>
       <div class="py-8 w-full space-y-6">
         <!-- Conteúdo da página -->
       </div>
     </template>
     ```
   - **Regra Inegociável:** É expressamente proibido colar o cabeçalho no topo da viewport (sem `py-8`). O respiro superior de `32px` (`py-8` / `pt-8`) é mandatório em 100% das páginas.

2. **Margens Horizontais e Inferior (Simetria de 60px)**:
   - A casca principal (`AppLayout.vue`) define `<main class="... px-[60px] pb-12">`.
   - As views nunca devem redeclarar padding horizontal perimetral que quebre o alinhamento de 60px à esquerda e 60px à direita.

3. **Ritmo Vertical (`space-y-6` ou `space-y-8`)**:
   - Telas operacionais com grids, tabelas e filtros: `space-y-6` (24px).
   - Telas editoriais, relatórios e documentação: `space-y-8` (32px).

