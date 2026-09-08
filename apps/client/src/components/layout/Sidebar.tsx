import React from 'react';
import { useAuthStore } from '../../stores/useAuthStore.js';
import {
  LayoutGrid,
  BarChart3,
  TrendingUp,
  Layers,
  FileCode,
  Sliders,
  Cpu,
  Activity,
  Wrench,
  Tag,
  AlertTriangle,
  ShieldCheck,
  Settings2,
  Lock,
} from 'lucide-react';

export type ActiveTab =
  | 'DASHBOARD'
  | 'PRODUCTION'
  | 'OEE_ANALYTICS'
  | 'ALIGNMENT'
  | 'RECIPES'
  | 'MANUAL'
  | 'IO_DIAGNOSTICS'
  | 'TOOLING_WEAR'
  | 'MACHINE_SETUP'
  | 'TAGS'
  | 'ALARMS'
  | 'USER_MANAGEMENT'
  | 'MENU_CONFIG';

interface SidebarProps {
  activeTab: ActiveTab;
  onTabChange: (tab: ActiveTab) => void;
}

export const Sidebar: React.FC<SidebarProps> = ({ activeTab, onTabChange }) => {
  const { isSuperAdmin, hasPermission } = useAuthStore();

  const menuSections = [
    {
      header: 'MAIN SCADA HUB',
      items: [
        { id: 'DASHBOARD' as const, label: 'SCADA Home Dashboard', icon: LayoutGrid, perm: 'menu:dashboard' },
        { id: 'PRODUCTION' as const, label: 'Manage Production', icon: BarChart3, perm: 'menu:production' },
        { id: 'MANUAL' as const, label: 'Manual Operations', icon: Sliders, perm: 'menu:manual' },
        { id: 'RECIPES' as const, label: 'Item Recipe Master', icon: FileCode, perm: 'menu:recipes' },
        { id: 'ALIGNMENT' as const, label: 'Program Align & Nest', icon: Layers, perm: 'menu:alignment' },
        { id: 'OEE_ANALYTICS' as const, label: 'Shift & OEE Telemetry', icon: TrendingUp, perm: 'menu:oee' },
      ],
    },
    {
      header: 'PLC & SYSTEM MASTER',
      items: [
        { id: 'TOOLING_WEAR' as const, label: 'Tooling Wear & Life', icon: Activity, perm: 'menu:wear' },
        { id: 'IO_DIAGNOSTICS' as const, label: 'PLC I/O Diagnostics', icon: Cpu, perm: 'menu:io' },
        { id: 'ALARMS' as const, label: 'Alarm Config & Logs', icon: AlertTriangle, perm: 'menu:alarms' },
        { id: 'MACHINE_SETUP' as const, label: 'Machine Master Setup', icon: Wrench, perm: 'menu:setup' },
        { id: 'TAGS' as const, label: 'PLC & Ui Tag Master', icon: Tag, perm: 'menu:tags' },
        { id: 'USER_MANAGEMENT' as const, label: 'User & Permissions', icon: ShieldCheck, perm: 'menu:users' },
        { id: 'MENU_CONFIG' as const, label: 'Super Admin Menu Config', icon: Settings2, perm: 'menu:config', superOnly: true },
      ],
    },
  ];

  return (
    <aside className="app-sidebar text-slate-300 flex flex-col justify-between select-none shadow-2xl z-20 bg-[#0d111a] border-r-2 border-[#1c2534]">
      <div className="flex-1 overflow-y-auto">
        <div className="py-2">
          {menuSections.map((section, sIdx) => (
            <div key={sIdx} className="mb-4">
              <div className="px-4 py-1 text-[11px] font-black text-slate-500 tracking-wider uppercase">
                {section.header}
              </div>
              {section.items.map((item) => {
                const Icon = item.icon;
                const isActive = activeTab === item.id;
                const isSuperOnly = (item as any).superOnly;

                // Hidden for non-super admins ("With US only")
                if (isSuperOnly && !isSuperAdmin()) {
                  return null;
                }

                const isAllowed = isSuperAdmin() || hasPermission(item.perm);

                return (
                  <button
                    key={item.id}
                    type="button"
                    disabled={!isAllowed}
                    onClick={() => onTabChange(item.id)}
                    className={`w-full flex items-center justify-between px-4 min-h-[50px] text-xs font-bold transition-all text-left border-l-4 ${
                      isActive
                        ? 'bg-[#172233] text-white border-sky-400 font-extrabold'
                        : isAllowed
                        ? 'text-slate-300 border-transparent hover:bg-[#131924] hover:text-white'
                        : 'text-slate-600 border-transparent opacity-40 cursor-not-allowed'
                    }`}
                  >
                    <div className="flex items-center gap-3">
                      <Icon className="w-5 h-5 shrink-0 opacity-80" />
                      <span className="truncate">{item.label}</span>
                    </div>
                    {!isAllowed && <Lock className="w-3.5 h-3.5 text-rose-400 shrink-0" />}
                  </button>
                );
              })}
            </div>
          ))}
        </div>
      </div>

      {/* Footer System Status */}
      <div className="p-3.5 bg-[#080b10] border-t border-[#1c2534] text-xs text-slate-400 flex items-center justify-between">
        <span className="font-mono">PLC SCAN: 50ms</span>
        <span className="text-emerald-400 font-black flex items-center gap-1.5 font-mono">
          <span className="w-2 h-2 rounded-full bg-emerald-400" />
          ONLINE
        </span>
      </div>
    </aside>
  );
};
