import React from 'react';
import {
  Layers,
  FileCode,
  Sliders,
  Tag,
  AlertTriangle,
  Wrench,
  BarChart3,
  User,
} from 'lucide-react';

export type ActiveTab =
  | 'PRODUCTION'
  | 'ALIGNMENT'
  | 'RECIPES'
  | 'MANUAL'
  | 'MACHINE_SETUP'
  | 'TAGS'
  | 'ALARMS';

interface SidebarProps {
  activeTab: ActiveTab;
  onTabChange: (tab: ActiveTab) => void;
}

export const Sidebar: React.FC<SidebarProps> = ({ activeTab, onTabChange }) => {
  const menuSections = [
    {
      header: 'MAIN NAVIGATION',
      items: [
        { id: 'PRODUCTION' as const, label: 'Manage Production', icon: BarChart3 },
        { id: 'ALIGNMENT' as const, label: 'Manage Program Align', icon: Layers },
        { id: 'RECIPES' as const, label: 'Item Recipe Master', icon: FileCode },
        { id: 'MANUAL' as const, label: 'Manual Operations', icon: Sliders },
      ],
    },
    {
      header: 'PLC & SYSTEM MASTER',
      items: [
        { id: 'MACHINE_SETUP' as const, label: 'Machine Master Settings', icon: Wrench },
        { id: 'TAGS' as const, label: 'PLC & Ui Tag Master', icon: Tag },
        { id: 'ALARMS' as const, label: 'Alarm Config & Logs', icon: AlertTriangle },
      ],
    },
  ];

  return (
    <aside className="app-sidebar text-slate-300 flex flex-col justify-between select-none shadow-md z-20">
      <div>
        {/* User Profile Mini Header */}
        <div className="p-4 border-b border-slate-700/60 flex items-center gap-3 bg-[#1e2429]">
          <div className="w-9 h-9 rounded-full bg-slate-600 flex items-center justify-center font-bold text-white text-xs">
            <User className="w-5 h-5 text-slate-300" />
          </div>
          <div>
            <div className="font-bold text-white text-xs">Rushabh Makim</div>
            <div className="text-[11px] text-slate-400">Administrator</div>
          </div>
        </div>

        {/* Menu Items */}
        <div className="py-3">
          {menuSections.map((section, sIdx) => (
            <div key={sIdx} className="mb-4">
              <div className="px-4 py-1 text-[10px] font-bold text-slate-400 tracking-wider">
                {section.header}
              </div>
              {section.items.map((item) => {
                const Icon = item.icon;
                const isActive = activeTab === item.id;
                return (
                  <button
                    key={item.id}
                    onClick={() => onTabChange(item.id)}
                    className={`w-full flex items-center gap-3 px-4 py-2.5 text-xs font-medium transition-all text-left ${
                      isActive
                        ? 'bg-[#348fe2] text-white font-bold shadow-sm'
                        : 'text-slate-300 hover:bg-[#1f252b] hover:text-white'
                    }`}
                  >
                    <Icon className="w-4 h-4 opacity-80" />
                    <span>{item.label}</span>
                  </button>
                );
              })}
            </div>
          ))}
        </div>
      </div>

      {/* Footer System Status */}
      <div className="p-3 bg-[#1e2429] border-t border-slate-700/60 text-[11px] text-slate-400 flex items-center justify-between">
        <span>PLC Rate: 20 Hz</span>
        <span className="text-emerald-400 font-bold">ONLINE</span>
      </div>
    </aside>
  );
};
