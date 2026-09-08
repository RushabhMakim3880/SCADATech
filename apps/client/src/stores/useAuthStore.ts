import { create } from 'zustand';
import { User } from '@innovance-hmi/shared';

interface AuthState {
  currentUser: User | null;
  users: User[];
  isLoading: boolean;
  isPinModalOpen: boolean;
  targetUserForPin: User | null;

  // Actions
  fetchCurrentUser: () => Promise<void>;
  fetchUsers: () => Promise<void>;
  loginWithPin: (pinCode: string, userId?: string) => Promise<{ success: boolean; error?: string }>;
  loginWithSecretCode: (code: string) => Promise<{ success: boolean; error?: string }>;
  switchUser: (user: User) => void;
  openPinModal: (user?: User) => void;
  closePinModal: () => void;
  logout: () => void;

  // Permission & Role Helpers
  hasPermission: (permissionKey: string) => boolean;
  isSuperAdmin: () => boolean;
  isAdmin: () => boolean;
  isOperator: () => boolean;
}

export const useAuthStore = create<AuthState>((set, get) => ({
  currentUser: null,
  users: [],
  isLoading: false,
  isPinModalOpen: false,
  targetUserForPin: null,

  fetchCurrentUser: async () => {
    try {
      // Check session storage first for within-session reloads
      const sessionUserStr = sessionStorage.getItem('hpt_hmi_user');
      if (sessionUserStr) {
        const parsed = JSON.parse(sessionUserStr);
        set({ currentUser: parsed });
        return;
      }

      // Fresh launch: By default when system launches it must launch as Operator only
      const res = await fetch('/api/auth/current');
      const data = await res.json();
      if (data.success && data.data) {
        set({ currentUser: data.data });
        sessionStorage.setItem('hpt_hmi_user', JSON.stringify(data.data));
        return;
      }
    } catch (e) {
      console.warn('Failed to fetch initial user, using operator fallback', e);
      const fallback: User = {
        id: 'operator-default',
        username: 'operator',
        name: 'Shopfloor Line Operator',
        role: 'OPERATOR',
        pinCode: '1234',
        permissions: [
          'menu:dashboard', 'menu:production', 'menu:manual', 'menu:recipes', 'menu:alignment',
          'menu:oee', 'menu:wear', 'menu:alarms',
          'action:start_production', 'action:jog_axis', 'action:fire_heads', 'action:toggle_valves'
        ],
        isActive: true,
      };
      set({ currentUser: fallback });
      sessionStorage.setItem('hpt_hmi_user', JSON.stringify(fallback));
    }
  },

  fetchUsers: async () => {
    set({ isLoading: true });
    try {
      const res = await fetch('/api/users');
      const data = await res.json();
      if (data.success && Array.isArray(data.data)) {
        set({ users: data.data });
      }
    } catch (e) {
      console.error('Failed to fetch users', e);
    } finally {
      set({ isLoading: false });
    }
  },

  loginWithPin: async (pinCode: string, userId?: string) => {
    try {
      const res = await fetch('/api/auth/login-pin', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pinCode, userId }),
      });
      const data = await res.json();
      if (data.success && data.data) {
        set({
          currentUser: data.data,
          isPinModalOpen: false,
          targetUserForPin: null,
        });
        sessionStorage.setItem('hpt_hmi_user', JSON.stringify(data.data));
        if (data.data.role !== 'SUPER_ADMIN') {
          localStorage.setItem('hpt_hmi_user', JSON.stringify(data.data));
        } else {
          localStorage.removeItem('hpt_hmi_user');
        }
        return { success: true };
      }
      return { success: false, error: data.error || 'Incorrect PIN Code' };
    } catch (e: any) {
      return { success: false, error: e.message || 'Network error' };
    }
  },

  loginWithSecretCode: async (code: string) => {
    try {
      const res = await fetch('/api/auth/login-pin', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pinCode: code }),
      });
      const data = await res.json();
      if (data.success && data.data && data.data.role === 'SUPER_ADMIN') {
        set({
          currentUser: data.data,
          isPinModalOpen: false,
          targetUserForPin: null,
        });
        sessionStorage.setItem('hpt_hmi_user', JSON.stringify(data.data));
        localStorage.removeItem('hpt_hmi_user');
        return { success: true };
      }
      return { success: false, error: 'Invalid Developer Secret Code' };
    } catch (e: any) {
      return { success: false, error: e.message || 'Network error' };
    }
  },

  switchUser: (user: User) => {
    set({
      targetUserForPin: user,
      isPinModalOpen: true,
    });
  },

  openPinModal: (user?: User) => {
    set({
      targetUserForPin: user || null,
      isPinModalOpen: true,
    });
  },

  closePinModal: () => {
    set({
      isPinModalOpen: false,
      targetUserForPin: null,
    });
  },

  logout: () => {
    sessionStorage.removeItem('hpt_hmi_user');
    localStorage.removeItem('hpt_hmi_user');
    get().fetchCurrentUser();
  },

  hasPermission: (permissionKey: string) => {
    const user = get().currentUser;
    if (!user) return false;
    if (user.role === 'SUPER_ADMIN') return true;
    if (user.permissions.includes('*')) return true;
    return user.permissions.includes(permissionKey);
  },

  isSuperAdmin: () => {
    return get().currentUser?.role === 'SUPER_ADMIN';
  },

  isAdmin: () => {
    const role = get().currentUser?.role;
    return role === 'ADMIN' || role === 'SUPER_ADMIN';
  },

  isOperator: () => {
    return get().currentUser?.role === 'OPERATOR';
  },
}));
