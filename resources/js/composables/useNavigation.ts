import { ref, onMounted, onUnmounted } from 'vue';

export type ActiveView = 'people' | 'supplies' | 'design-system' | 'changelog' | 'integrations' | 'developer';

const currentView = ref<ActiveView>('people');

export function useNavigation() {
  const setView = (view: ActiveView) => {
    currentView.value = view;
    if (view === 'supplies') {
      try {
        const savedTab = localStorage.getItem('rp_supplies_active_tab');
        if (savedTab && ['materials', 'serials', 'movements'].includes(savedTab)) {
          window.location.hash = `${view}?tab=${savedTab}`;
          return;
        }
      } catch (e) {
        // Fallback para hash limpo
      }
    }
    window.location.hash = view;
  };

  const syncHash = () => {
    const raw = window.location.hash.replace(/^#\/?/, '');
    const cleanView = raw.split('?')[0].split('/')[0].trim();
    const validViews: ActiveView[] = ['design-system', 'people', 'supplies', 'changelog', 'integrations', 'developer'];
    if (validViews.includes(cleanView as ActiveView)) {
      currentView.value = cleanView as ActiveView;
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
