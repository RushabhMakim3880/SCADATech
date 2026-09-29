import React from 'react';
import { useAuthStore } from '../../stores/useAuthStore.js';
import { usePlcStore } from '../../stores/usePlcStore.js';
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

interface MenuItem {
  id: ActiveTab;
  label: string;
  icon: React.ComponentType<{ className?: string }>;
  perm: string;
  badge?: number;
  superOnly?: boolean;
}

interface SidebarProps {
  activeTab: ActiveTab;
  onTabChange: (tab: ActiveTab) => void;
}

export const Sidebar: React.FC<SidebarProps> = ({ activeTab, onTabChange }) => {
  const { isSuperAdmin, hasPermission } = useAuthStore();
  const { activeAlarms, isConnected } = usePlcStore();

  const menuSections: { header: string; items: MenuItem[] }[] = [
    {
      header: 'MAIN SCADA HUB',
      items: [
        { id: 'DASHBOARD', label: 'SCADA Home Dashboard', icon: LayoutGrid, perm: 'menu:dashboard' },
        { id: 'PRODUCTION', label: 'Manage Production', icon: BarChart3, perm: 'menu:production' },
        { id: 'MANUAL', label: 'Manual Operations', icon: Sliders, perm: 'menu:manual' },
        { id: 'RECIPES', label: 'Item Recipe Master', icon: FileCode, perm: 'menu:recipes' },
        { id: 'ALIGNMENT', label: 'Program Align & Nest', icon: Layers, perm: 'menu:alignment' },
        { id: 'OEE_ANALYTICS', label: 'Shift & OEE Telemetry', icon: TrendingUp, perm: 'menu:oee' },
      ],
    },
    {
      header: 'PLC & SYSTEM MASTER',
      items: [
        { id: 'TOOLING_WEAR', label: 'Tooling Wear & Life', icon: Activity, perm: 'menu:wear' },
        { id: 'IO_DIAGNOSTICS', label: 'PLC I/O Diagnostics', icon: Cpu, perm: 'menu:io' },
        { id: 'ALARMS', label: 'Alarm Config & Logs', icon: AlertTriangle, perm: 'menu:alarms', badge: activeAlarms.length > 0 ? activeAlarms.length : undefined },
        { id: 'MACHINE_SETUP', label: 'Machine Master Setup', icon: Wrench, perm: 'menu:setup' },
        { id: 'TAGS', label: 'PLC & UI Tag Master', icon: Tag, perm: 'menu:tags' },
        { id: 'USER_MANAGEMENT', label: 'User & Permissions', icon: ShieldCheck, perm: 'menu:users' },
        { id: 'MENU_CONFIG', label: 'Super Admin Menu Config', icon: Settings2, perm: 'menu:config', superOnly: true },
      ],
    },
  ];

  return (
    <aside className="w-64 md:w-72 bg-[#090d15] text-slate-300 flex flex-col justify-between select-none shadow-2xl z-20 border-r border-[#1a2538]">
      <div className="flex-1 overflow-y-auto py-3 space-y-4">
        {menuSections.map((section, sIdx) => (
          <div key={sIdx} className="space-y-1">
            <div className="px-5 py-1 text-[10px] font-black text-slate-500 tracking-widest uppercase flex items-center gap-2">
              <span className="w-1.5 h-1.5 rounded bg-sky-500/80" />
              <span>{section.header}</span>
            </div>
            <div className="px-2 space-y-0.5">
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
                    className={`w-full flex items-center justify-between px-3.5 min-h-[50px] text-xs font-black transition-all text-left rounded-xl border ${
                      isActive
                        ? 'bg-gradient-to-r from-sky-950/80 to-[#121c2c] text-white border-sky-500/80 shadow-md shadow-sky-950/50'
                        : isAllowed
                        ? 'border-transparent text-slate-300 hover:bg-[#111723] hover:text-white hover:border-[#1e2a3c]'
                        : 'border-transparent text-slate-600 opacity-40 cursor-not-allowed'
                    }`}
                  >
                    <div className="flex items-center gap-3">
                      <div
                        className={`w-8 h-8 rounded-lg flex items-center justify-center transition-colors ${
                          isActive
                            ? 'bg-sky-500 text-white shadow-[0_0_10px_#0284c7]'
                            : 'bg-[#121824] text-slate-400 group-hover:text-slate-200'
                        }`}
                      >
                        <Icon className="w-4 h-4 shrink-0" />
                      </div>
                      <span className="truncate">{item.label}</span>
                    </div>

                    <div className="flex items-center gap-2">
                      {item.badge !== undefined && (
                        <span className="bg-rose-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full animate-pulse shadow-sm">
                          {item.badge}
                        </span>
                      )}
                      {!isAllowed && <Lock className="w-3.5 h-3.5 text-rose-400 shrink-0" />}
                    </div>
                  </button>
                );
              })}
            </div>
          </div>
        ))}
      </div>

      {/* Footer System Status */}
      <div className="p-3 bg-[#06090e] border-t border-[#1a2538] text-xs text-slate-400 flex items-center justify-between">
        <span className="font-mono text-[11px] text-slate-500">PLC SCAN: 50ms (20Hz)</span>
        <span className="text-emerald-400 font-black flex items-center gap-1.5 font-mono text-[11px]">
          <span className={`w-2 h-2 rounded-full ${isConnected ? 'bg-emerald-400 shadow-[0_0_6px_#34d399]' : 'bg-rose-500'}`} />
          {isConnected ? 'ONLINE' : 'OFFLINE'}
        </span>
      </div>
    </aside>
  );
};
