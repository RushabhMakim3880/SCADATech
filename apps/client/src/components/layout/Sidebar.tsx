import React from 'react';
import {
  PlayCircle,
  Sliders,
  FileCode,
  Layers,
  Tag,
  AlertOctagon,
  Wrench,
} from 'lucide-react';

export type ActiveTab =
  | 'PRODUCTION'
  | 'MANUAL'
  | 'RECIPES'
  | 'ALIGNMENT'
  | 'MACHINE_SETUP'
  | 'TAGS'
  | 'ALARMS';

interface SidebarProps {
  activeTab: ActiveTab;
  onTabChange: (tab: ActiveTab) => void;
}

export const Sidebar: React.FC<SidebarProps> = ({ activeTab, onTabChange }) => {
  const menuItems = [
    { id: 'PRODUCTION' as const, label: 'Live Production', icon: PlayCircle, badge: 'LIVE' },
    { id: 'MANUAL' as const, label: 'Manual & Jogging', icon: Sliders },
    { id: 'RECIPES' as const, label: 'Item Recipes', icon: FileCode },
    { id: 'ALIGNMENT' as const, label: 'Bar Nesting', icon: Layers },
    { id: 'MACHINE_SETUP' as const, label: 'Machine & Tools', icon: Wrench },
    { id: 'TAGS' as const, label: 'PLC Tag Master', icon: Tag },
    { id: 'ALARMS' as const, label: 'Alarms & Events', icon: AlertOctagon },
  ];

  return (
    <aside className="w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between select-none">
      <div className="p-3 space-y-1.5">
        <div className="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 font-mono">
          Operations & Control
        </div>
        {menuItems.map((item) => {
          const Icon = item.icon;
          const isActive = activeTab === item.id;
          return (
            <button
              key={item.id}
              onClick={() => onTabChange(item.id)}
              className={`w-full flex items-center justify-between px-3.5 py-3 rounded-lg text-sm font-semibold transition-all ${
                isActive
                  ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30 shadow-inner'
                  : 'text-slate-300 hover:bg-slate-800 hover:text-white'
              }`}
            >
              <div className="flex items-center gap-3">
                <Icon className={`w-5 h-5 ${isActive ? 'text-cyan-400' : 'text-slate-400'}`} />
                <span>{item.label}</span>
              </div>
              {item.badge && (
                <span className="text-[10px] px-1.5 py-0.5 rounded bg-emerald-950 text-emerald-400 font-mono font-bold border border-emerald-800">
                  {item.badge}
                </span>
              )}
            </button>
          );
        })}
      </div>

      {/* Operator Session Info */}
      <div className="p-4 border-t border-slate-800 bg-slate-950/50">
        <div className="flex items-center gap-3">
          <div className="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center font-bold text-slate-300 text-xs border border-slate-700">
            OP
          </div>
          <div>
            <div className="text-xs font-bold text-slate-200">Line Operator 1</div>
            <div className="text-[10px] text-slate-400 font-mono">Station #01 • Shift A</div>
          </div>
        </div>
      </div>
    </aside>
  );
};
