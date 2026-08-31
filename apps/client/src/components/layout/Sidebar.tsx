import React from 'react';
import {
  PlayCircle,
  Sliders,
  FileCode,
  Layers,
  Tag,
  AlertOctagon,
  Wrench,
  Radio,
  UserCheck,
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
    { id: 'PRODUCTION' as const, label: 'Live Production', icon: PlayCircle, badge: 'AUTO', badgeColor: 'bg-emerald-950 text-neon-emerald border-emerald-500/50' },
    { id: 'MANUAL' as const, label: 'Manual & Jogging', icon: Sliders, badge: 'JOG', badgeColor: 'bg-cyan-950 text-neon-cyan border-cyan-500/50' },
    { id: 'RECIPES' as const, label: 'Item Recipes CAD', icon: FileCode },
    { id: 'ALIGNMENT' as const, label: 'Bar Nesting Opt.', icon: Layers },
    { id: 'MACHINE_SETUP' as const, label: 'Machine & Tooling', icon: Wrench },
    { id: 'TAGS' as const, label: 'PLC Tag Master', icon: Tag },
    { id: 'ALARMS' as const, label: 'Alarms & Events', icon: AlertOctagon },
  ];

  return (
    <aside className="w-64 bg-scada-900/80 border-r border-scada-750/70 flex flex-col justify-between select-none backdrop-blur-xl relative z-10 shadow-2xl">
      <div className="p-3.5 space-y-2">
        <div className="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest text-slate-400 font-mono flex items-center justify-between">
          <span>NAVIGATION MENU</span>
          <Radio className="w-3.5 h-3.5 text-cyan-400 animate-pulse" />
        </div>

        {menuItems.map((item) => {
          const Icon = item.icon;
          const isActive = activeTab === item.id;
          return (
            <button
              key={item.id}
              onClick={() => onTabChange(item.id)}
              className={`w-full flex items-center justify-between px-3.5 py-3 rounded-xl text-xs font-mono font-bold transition-all duration-200 relative group overflow-hidden ${
                isActive
                  ? 'bg-gradient-to-r from-cyan-950/80 via-scada-800 to-scada-850 text-slate-100 border border-cyan-500/40 shadow-neon-cyan'
                  : 'text-slate-400 hover:text-slate-100 hover:bg-scada-850/60 border border-transparent'
              }`}
            >
              {isActive && (
                <span className="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-cyan-400 to-blue-500 shadow-neon-cyan" />
              )}
              <div className="flex items-center gap-3">
                <div
                  className={`w-8 h-8 rounded-lg flex items-center justify-center transition-all ${
                    isActive
                      ? 'bg-cyan-500/20 text-neon-cyan border border-cyan-400/40'
                      : 'bg-scada-950/60 text-slate-400 group-hover:text-slate-200 border border-scada-750/60'
                  }`}
                >
                  <Icon className="w-4 h-4" />
                </div>
                <span className="tracking-wide">{item.label}</span>
              </div>
              {item.badge && (
                <span className={`text-[9px] px-2 py-0.5 rounded-full font-mono font-extrabold border ${item.badgeColor}`}>
                  {item.badge}
                </span>
              )}
            </button>
          );
        })}
      </div>

      {/* Operator Session & System Telemetry Footer */}
      <div className="p-4 border-t border-scada-750/80 bg-scada-950/80 space-y-3">
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-2.5">
            <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center font-bold text-cyan-300 text-xs border border-scada-700 shadow-inner">
              <UserCheck className="w-4 h-4 text-cyan-400" />
            </div>
            <div>
              <div className="text-xs font-bold text-slate-200 font-mono">OPERATOR 01</div>
              <div className="text-[10px] text-slate-400 font-mono">STATION #1 • SHIFT A</div>
            </div>
          </div>
          <span className="led-emerald" />
        </div>

        {/* Mini PLC Link Quality */}
        <div className="bg-scada-900 p-2.5 rounded-lg border border-scada-800 space-y-1.5 text-[10px] font-mono">
          <div className="flex justify-between text-slate-400">
            <span>BUS CYCLE RATE</span>
            <span className="text-neon-cyan font-bold">20 Hz (50ms)</span>
          </div>
          <div className="w-full bg-scada-950 rounded-full h-1.5 overflow-hidden">
            <div className="bg-gradient-to-r from-cyan-500 to-emerald-400 h-full w-[94%]" />
          </div>
        </div>
      </div>
    </aside>
  );
};
