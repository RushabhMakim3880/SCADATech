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
      {/* 1. Left Zone: Brand Emblem & System Identifier */}
      <div className="flex items-center gap-3 min-w-[260px]">
        <div className="w-8 h-8 rounded bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center font-black text-white text-xs shadow-sm tracking-wider">
          HPT
        </div>
        <div>
          <div className="flex items-center gap-1.5 leading-none">
            <span className="font-extrabold text-sm text-slate-800 tracking-tight">
              HPT Innovance SCADA
            </span>
            <span className="text-[10px] bg-slate-200 text-slate-700 font-bold px-1.5 py-0.5 rounded">
              v1.0
            </span>
          </div>
          <span className="text-[10px] text-slate-500 font-medium">6-Head CNC Angle Punching & Shear</span>
        </div>
      </div>

      {/* 2. Center Zone: Mode Switcher & Digital DRO Display */}
      <div className="flex items-center gap-5">
        {/* Machine Mode Selector with LED status */}
        <div className="flex items-center bg-slate-100 p-1 rounded border border-slate-300 gap-1 shadow-inner">
          {(
            [
              { id: 'MANUAL', label: 'MANUAL' },
              { id: 'SEMI_AUTO', label: 'SEMI AUTO' },
              { id: 'AUTO', label: 'AUTO RUN' },
            ] as const
          ).map((m) => {
            const isActive = mode === m.id;
            return (
              <button
                key={m.id}
                onClick={() => setMode(m.id)}
                className={`px-3 py-1 text-xs font-bold rounded flex items-center gap-1.5 transition-all ${
                  isActive
                    ? m.id === 'AUTO'
                      ? 'bg-emerald-600 text-white shadow-sm'
                      : 'bg-blue-600 text-white shadow-sm'
                    : 'text-slate-600 hover:bg-slate-200 hover:text-slate-900'
                }`}
              >
                <span
                  className={`led-indicator ${
                    isActive
                      ? m.id === 'AUTO'
                        ? 'led-green'
                        : 'led-amber'
                      : 'led-off'
                  }`}
                />
                {m.label}
              </button>
            );
          })}
        </div>

        {/* High-Visibility Digital DRO Box */}
        <div className="digital-dro-box">
          <span className="digital-dro-label">CARRIAGE FEED (X):</span>
          <span className="digital-dro-val">{feedPositionMm.toFixed(2)} mm</span>
        </div>
      </div>

      {/* 3. Right Zone: Telemetry, Alarms & Operator Profile */}
      <div className="flex items-center gap-3">
        {/* Search Bar */}
        <div className="hidden xl:flex items-center bg-slate-100 px-2.5 py-1 rounded border border-slate-300 text-xs">
          <Search className="w-3.5 h-3.5 text-slate-400 mr-2" />
          <input
            type="text"
            placeholder="Search tags, jobs..."
            className="bg-transparent border-none text-xs text-slate-800 placeholder-slate-400 focus:outline-none w-28"
          />
        </div>

        {/* Hydraulic Pressure Status */}
        <div className="flex items-center gap-1.5 bg-slate-100 px-2.5 py-1 rounded border border-slate-300 text-xs">
          <Activity className="w-3.5 h-3.5 text-amber-600" />
          <span className="text-slate-500 font-bold">HPU:</span>
          <span className="font-mono font-bold text-slate-800">{hydraulicPressureBar.toFixed(1)} bar</span>
        </div>

        {/* PLC Connection Indicator */}
        <div className="flex items-center gap-1.5 px-2.5 py-1 rounded bg-slate-100 border border-slate-300 text-xs">
          <Cpu className="w-3.5 h-3.5 text-slate-600" />
          <span className="font-bold text-slate-700">
            {isConnected ? (isSimulator ? 'SIMULATOR' : 'OPC-UA LIVE') : 'OFFLINE'}
          </span>
          <span className={`led-indicator ${isConnected ? 'led-green' : 'led-red'}`} />
        </div>

        {/* Active Alarms Bell */}
        <div className="relative cursor-pointer p-1.5 rounded hover:bg-slate-100 transition-colors">
          <Bell className="w-4 h-4 text-slate-600" />
          {activeAlarms.length > 0 && (
            <span className="absolute top-0 right-0 bg-red-600 text-white text-[9px] font-extrabold w-4 h-4 rounded-full flex items-center justify-center animate-pulse">
              {activeAlarms.length}
            </span>
          )}
        </div>

        {/* Operator Profile */}
        <div className="flex items-center gap-2 pl-2 border-l border-slate-300">
          <div className="w-7 h-7 rounded bg-slate-800 text-white flex items-center justify-center font-bold text-xs shadow-inner">
            <User className="w-4 h-4 text-slate-300" />
          </div>
          <div className="hidden lg:block text-left leading-tight">
            <div className="font-bold text-slate-800 text-xs">Rushabh Makim</div>
            <div className="text-[10px] text-slate-500">Administrator</div>
          </div>
        </div>
      </div>
    </header>
  );
};
