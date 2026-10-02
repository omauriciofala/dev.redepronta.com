import { ref, computed } from 'vue';
import axios from 'axios';

export interface TenantSettings {
  trading_name?: string;
  brand_color?: string;
  slogan?: string;
  logo_url?: string | null;
  favicon_url?: string | null;
  [key: string]: any;
}

export interface TenantData {
  id: number;
  name: string;
  subdomain: string;
  document?: string | null;
  email?: string | null;
  phone?: string | null;
  status: string;
  trading_name: string;
  brand_color: string;
  slogan: string;
  logo_url?: string | null;
  favicon_url?: string | null;
  settings: TenantSettings;
}

const STORAGE_KEY = 'rp_tenant_data';

const savedTenant = (() => {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    return raw ? JSON.parse(raw) : null;
  } catch (e) {
    return null;
  }
})();

const currentTenant = ref<TenantData | null>(savedTenant);
const isLoadingTenant = ref(false);

/**
 * Atualiza dinamicamente o favicon na aba do navegador.
 */
function applyFavicon(faviconUrl: string | null) {
  if (typeof document === 'undefined') return;
  let link: HTMLLinkElement | null = document.querySelector("link[rel*='icon']");
  if (!link) {
    link = document.createElement('link');
    link.type = 'image/x-icon';
    link.rel = 'shortcut icon';
    document.getElementsByTagName('head')[0].appendChild(link);
  }
  link.href = faviconUrl || '/favicon.ico';
}

// Aplica favicon inicial se existir
if (savedTenant?.favicon_url) {
  applyFavicon(savedTenant.favicon_url);
}

export function useTenant() {
  const tenantName = computed(() => currentTenant.value?.trading_name || currentTenant.value?.name || 'Rede Pronta');
  const tenantLogo = computed(() => currentTenant.value?.logo_url || null);
  const tenantFavicon = computed(() => currentTenant.value?.favicon_url || null);

  const fetchTenant = async () => {
    isLoadingTenant.value = true;
    try {
      const res = await axios.get('/api/v1/settings/tenant');
      if (res.data?.data) {
        currentTenant.value = res.data.data;
        try {
          localStorage.setItem(STORAGE_KEY, JSON.stringify(res.data.data));
        } catch (e) {}

        if (res.data.data.favicon_url) {
          applyFavicon(res.data.data.favicon_url);
        }
      }
    } catch (e) {
      // Silencioso se não autenticado ainda
    } finally {
      isLoadingTenant.value = false;
    }
  };

  const setTenantData = (data: TenantData) => {
    currentTenant.value = data;
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    } catch (e) {}
    if (data.favicon_url !== undefined) {
      applyFavicon(data.favicon_url);
    }
  };

  return {
    currentTenant,
    isLoadingTenant,
    tenantName,
    tenantLogo,
    tenantFavicon,
    fetchTenant,
    setTenantData,
    applyFavicon,
  };
}
