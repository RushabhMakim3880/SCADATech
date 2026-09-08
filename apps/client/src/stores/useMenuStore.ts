import { create } from 'zustand';
import { MenuConfigItem, UserRole } from '@innovance-hmi/shared';

interface MenuState {
  menuItems: MenuConfigItem[];
  isLoading: boolean;
  error: string | null;

  fetchMenuItems: () => Promise<void>;
  updateMenuItems: (items: MenuConfigItem[]) => Promise<{ success: boolean; error?: string }>;
  resetToDefaults: () => Promise<{ success: boolean; error?: string }>;
  getVisibleMenuItems: (role?: UserRole) => MenuConfigItem[];
}

export const useMenuStore = create<MenuState>((set, get) => ({
  menuItems: [],
  isLoading: false,
  error: null,

  fetchMenuItems: async () => {
    set({ isLoading: true, error: null });
    try {
      const res = await fetch('/api/menu-config');
      const data = await res.json();
      if (data.success && Array.isArray(data.data)) {
        set({ menuItems: data.data });
      }
    } catch (e: any) {
      console.error('Failed to load menu configuration', e);
      set({ error: e.message });
    } finally {
      set({ isLoading: false });
    }
  },

  updateMenuItems: async (items: MenuConfigItem[]) => {
    set({ isLoading: true });
    try {
      const res = await fetch('/api/menu-config', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ items }),
      });
      const data = await res.json();
      if (data.success && Array.isArray(data.data)) {
        set({ menuItems: data.data, isLoading: false });
        return { success: true };
      }
      set({ isLoading: false });
      return { success: false, error: data.error || 'Failed to update menu' };
    } catch (e: any) {
      set({ isLoading: false });
      return { success: false, error: e.message };
    }
  },

  resetToDefaults: async () => {
    set({ isLoading: true });
    try {
      const res = await fetch('/api/menu-config/reset', { method: 'POST' });
      const data = await res.json();
      if (data.success && Array.isArray(data.data)) {
        set({ menuItems: data.data, isLoading: false });
        return { success: true };
      }
      set({ isLoading: false });
      return { success: false, error: data.error || 'Reset failed' };
    } catch (e: any) {
      set({ isLoading: false });
      return { success: false, error: e.message };
    }
  },

  getVisibleMenuItems: (role?: UserRole) => {
    const { menuItems } = get();
    if (!role) return menuItems.filter((i) => i.isEnabled);
    return menuItems.filter((item) => item.isEnabled && item.roles.includes(role));
  },
}));
