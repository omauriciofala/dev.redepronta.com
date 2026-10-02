import { ref, onMounted, onUnmounted } from 'vue';

export type ActiveView = 'people' | 'supplies' | 'operations' | 'tasks' | 'users' | 'settings' | 'design-system' | 'changelog' | 'integrations' | 'developer' | 'login';

const currentView = ref<ActiveView>('people');

export function useNavigation() {
  const setView = (view: ActiveView, tab?: string) => {
    currentView.value = view;
    if (view === 'settings') {
      try {
        const targetTab = tab || localStorage.getItem('rp_settings_active_tab') || 'profile';
        window.location.hash = `${view}?tab=${targetTab}`;
        return;
      } catch (e) {
        // Fallback
      }
    }
    if (view === 'tasks') {
      try {
        const savedView = localStorage.getItem('rp_tasks_view_mode');
        if (savedView && ['list', 'kanban'].includes(savedView)) {
          window.location.hash = `${view}?view=${savedView}`;
          return;
        }
      } catch (e) {
        // Fallback para hash limpo
      }
    }
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
    if (view === 'operations') {
      try {
        const savedTab = localStorage.getItem('rp_operations_active_tab');
        if (savedTab && ['tasks', 'tickets', 'dispatches'].includes(savedTab)) {
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
    const validViews: ActiveView[] = ['design-system', 'people', 'supplies', 'operations', 'tasks', 'users', 'settings', 'changelog', 'integrations', 'developer', 'login'];
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
