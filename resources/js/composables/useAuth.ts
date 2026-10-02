import { ref, computed } from 'vue';
import axios from 'axios';

export interface UserRole {
  id: number;
  name: string;
  slug: string;
}

export interface AuthUser {
  id: number;
  name: string;
  email: string;
  avatar_url?: string | null;
  account_id: number;
  account_name: string;
  is_super_admin: boolean;
  status: string;
  role: UserRole | null;
  permissions: string[];
  preferences?: {
    theme?: 'system' | 'light' | 'dark';
    table_density?: 'comfortable' | 'compact';
    sound_enabled?: boolean;
    [key: string]: any;
  } | null;
}

const STORAGE_KEY = 'rp_auth_user';

// Carrega usuário salvo previamente do localStorage
const savedUser = (() => {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    return raw ? JSON.parse(raw) : null;
  } catch (e) {
    return null;
  }
})();

const currentUser = ref<AuthUser | null>(savedUser);
const isLoading = ref(false);
const authError = ref<string | null>(null);

export function useAuth() {
  const isAuthenticated = computed(() => !!currentUser.value);
  const isSuperAdmin = computed(() => !!currentUser.value?.is_super_admin);

  const hasPermission = (slug: string): boolean => {
    if (!currentUser.value) return false;
    if (currentUser.value.is_super_admin) return true;
    return currentUser.value.permissions?.includes(slug) ?? false;
  };

  const login = async (credentials: { email: string; password: string; remember?: boolean }) => {
    isLoading.value = true;
    authError.value = null;

    try {
      const response = await axios.post('/api/v1/auth/login', credentials);
      const user = response.data?.user as AuthUser;
      currentUser.value = user;
      try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(user));
      } catch (e) {}
      return { success: true, user };
    } catch (err: any) {
      const message =
        err.response?.data?.errors?.email?.[0] ||
        err.response?.data?.message ||
        'Não foi possível realizar o login. Verifique suas credenciais.';
      authError.value = message;
      return { success: false, message };
    } finally {
      isLoading.value = false;
    }
  };

  const logout = async () => {
    try {
      await axios.post('/api/v1/auth/logout');
    } catch (e) {
      // Ignora erro de rede no logout
    } finally {
      currentUser.value = null;
      try {
        localStorage.removeItem(STORAGE_KEY);
      } catch (e) {}
      window.location.hash = 'login';
    }
  };

  const fetchCurrentUser = async () => {
    try {
      const response = await axios.get('/api/v1/auth/me');
      if (response.data?.user) {
        currentUser.value = response.data.user as AuthUser;
        try {
          localStorage.setItem(STORAGE_KEY, JSON.stringify(currentUser.value));
        } catch (e) {}
      }
    } catch (e) {
      // Se der 401 ou erro, mantém estado offline ou null
    }
  };

  const setCurrentUser = (user: AuthUser) => {
    currentUser.value = user;
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(user));
    } catch (e) {}
  };

  return {
    currentUser,
    isAuthenticated,
    isSuperAdmin,
    isLoading,
    authError,
    login,
    logout,
    fetchCurrentUser,
    setCurrentUser,
    hasPermission,
  };
}
