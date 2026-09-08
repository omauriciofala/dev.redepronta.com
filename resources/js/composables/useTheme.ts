import { ref, onMounted } from 'vue';

export type ThemeMode = 'light' | 'dark' | 'auto';

const currentTheme = ref<ThemeMode>('dark');

export function useTheme() {
  const applyTheme = (mode: ThemeMode) => {
    currentTheme.value = mode;
    localStorage.setItem('rp_erp_theme', mode);

    const isDark =
      mode === 'dark' ||
      (mode === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);

    if (isDark) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  };

  const toggleTheme = () => {
    const nextMode = currentTheme.value === 'dark' ? 'light' : 'dark';
    applyTheme(nextMode);
  };

  onMounted(() => {
    const saved = (localStorage.getItem('rp_erp_theme') as ThemeMode) || 'dark';
    applyTheme(saved);
  });

  return {
    theme: currentTheme,
    applyTheme,
    toggleTheme,
  };
}
