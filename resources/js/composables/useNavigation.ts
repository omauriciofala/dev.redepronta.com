import { ref, onMounted, onUnmounted } from 'vue';

export type ActiveView = 'people' | 'design-system';

const currentView = ref<ActiveView>('people');

export function useNavigation() {
  const setView = (view: ActiveView) => {
    currentView.value = view;
    window.location.hash = view;
  };

  const syncHash = () => {
    const hash = window.location.hash.replace('#', '');
    if (hash === 'design-system' || hash === 'people') {
      currentView.value = hash as ActiveView;
    }
  };

  onMounted(() => {
    syncHash();
    window.addEventListener('hashchange', syncHash);
  });

  onUnmounted(() => {
    window.removeEventListener('hashchange', syncHash);
  });

  return {
    currentView,
    setView
  };
}
