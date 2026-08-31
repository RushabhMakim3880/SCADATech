import React from 'react';
import { usePlcStore } from '../../stores/usePlcStore.js';
import {
  Bell,
  Search,
  User,
  Activity,
  Cpu,
} from 'lucide-react';

export const TopHeader: React.FC = () => {
  const { isConnected, isSimulator, activeAlarms, hydraulicPressureBar, feedPositionMm, mode, setMode } = usePlcStore();

  return (
    <header className="app-header flex items-center justify-between px-4 select-none z-30 relative shadow-sm">
      {/* Brand Logo & Title */}
      <div className="flex items-center gap-3">
        <div className="w-8 h-8 rounded bg-cyan-600 flex items-center justify-center font-bold text-white text-sm">
          HPT
        </div>
        <div className="flex items-center gap-2">
          <span className="font-bold text-sm text-slate-800 tracking-tight">
            HPT Innovance Angle Punching
          </span>
          <span className="text-[10px] bg-slate-200 text-slate-700 font-semibold px-2 py-0.5 rounded">
            v1.0
          </span>
        </div>
      </div>

      {/* Center Live DRO and Mode Controls */}
      <div className="flex items-center gap-4 text-xs font-sans">
        {/* Machine Mode Switcher */}
        <div className="flex items-center bg-slate-100 p-1 rounded border border-slate-300">
          {(['MANUAL', 'SEMI_AUTO', 'AUTO'] as const).map((m) => (
            <button
              key={m}
              onClick={() => setMode(m)}
              className={`px-3 py-1 font-bold text-xs rounded transition-all ${
                mode === m
                  ? m === 'AUTO'
                    ? 'bg-teal-600 text-white shadow-sm'
                    : 'bg-blue-600 text-white shadow-sm'
                  : 'text-slate-600 hover:text-slate-900'
              }`}
            >
              {m.replace('_', ' ')}
            </button>
          ))}
        </div>

        {/* Live DRO Feed Position */}
        <div className="flex items-center gap-2 bg-slate-800 text-white px-3 py-1 rounded font-mono text-xs shadow-inner">
          <span className="text-slate-400">FEED (X):</span>
          <span className="text-cyan-400 font-bold text-sm">{feedPositionMm.toFixed(2)} mm</span>
        </div>

        {/* Hydraulic Pressure */}
        <div className="flex items-center gap-2 bg-slate-100 px-3 py-1 rounded border border-slate-300 text-xs">
          <Activity className="w-3.5 h-3.5 text-amber-600" />
          <span className="text-slate-500 font-semibold">HPU:</span>
          <span className="font-bold text-slate-800">{hydraulicPressureBar.toFixed(1)} bar</span>
        </div>
      </div>

      {/* Right User & System Status Area */}
      <div className="flex items-center gap-4 text-xs">
        {/* Search */}
        <div className="hidden md:flex items-center bg-slate-100 px-2.5 py-1 rounded border border-slate-300">
          <Search className="w-3.5 h-3.5 text-slate-400 mr-2" />
          <input
            type="text"
            placeholder="Search..."
            className="bg-transparent border-none text-xs text-slate-800 placeholder-slate-400 focus:outline-none w-28"
          />
        </div>

        {/* PLC Status Indicator */}
        <div className="flex items-center gap-1.5 px-2.5 py-1 rounded bg-slate-100 border border-slate-300">
          <Cpu className="w-3.5 h-3.5 text-slate-600" />
          <span className="font-bold text-slate-700">
            {isConnected ? (isSimulator ? 'SIMULATOR' : 'OPC-UA LIVE') : 'OFFLINE'}
          </span>
          <span className={`w-2 h-2 rounded-full ${isConnected ? 'bg-emerald-500' : 'bg-rose-500'}`} />
        </div>

        {/* Alarms Bell */}
        <div className="relative cursor-pointer p-1">
          <Bell className="w-4 h-4 text-slate-600 hover:text-slate-900" />
          {activeAlarms.length > 0 && (
            <span className="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
              {activeAlarms.length}
            </span>
          )}
        </div>

        {/* User Profile */}
        <div className="flex items-center gap-2 pl-2 border-l border-slate-300 cursor-pointer">
          <div className="w-7 h-7 rounded-full bg-slate-700 text-white flex items-center justify-center font-bold text-xs">
            <User className="w-4 h-4" />
          </div>
          <span className="font-semibold text-slate-700 hidden sm:inline">Admin User</span>
        </div>
      </div>
    </header>
  );
};
