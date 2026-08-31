import React from 'react';
import { usePlcStore } from '../../stores/usePlcStore.js';
import { Activity, ShieldAlert, Cpu, AlertTriangle } from 'lucide-react';

export const TopHeader: React.FC = () => {
  const { isConnected, isSimulator, mode, setMode, eStopOk, activeAlarms, hydraulicPressureBar } = usePlcStore();

  return (
    <header className="h-16 bg-slate-900 border-b border-slate-800 px-6 flex items-center justify-between select-none">
      {/* Brand & Machine Title */}
      <div className="flex items-center gap-4">
        <div className="w-10 h-10 rounded-lg bg-cyan-600 flex items-center justify-center font-black text-white text-xl tracking-wider shadow-lg shadow-cyan-900/30">
          6H
        </div>
        <div>
          <h1 className="text-base font-bold text-slate-100 flex items-center gap-2">
            INNOVANCE 6-HEAD CNC ANGLE LINE
            <span className="text-xs px-2 py-0.5 rounded bg-cyan-950 text-cyan-400 border border-cyan-800 font-mono">
              HPT-01
            </span>
          </h1>
          <p className="text-xs text-slate-400">Angle Punching, Marking & Shearing HMI</p>
        </div>
      </div>

      {/* Center Operational Mode Selector */}
      <div className="flex items-center bg-slate-950 p-1 rounded-lg border border-slate-800">
        {(['MANUAL', 'SEMI_AUTO', 'AUTO'] as const).map((m) => (
          <button
            key={m}
            onClick={() => setMode(m)}
            className={`px-4 py-1.5 text-xs font-bold rounded-md transition-all ${
              mode === m
                ? m === 'AUTO'
                  ? 'bg-emerald-600 text-white shadow'
                  : 'bg-cyan-600 text-white shadow'
                : 'text-slate-400 hover:text-slate-200'
            }`}
          >
            {m.replace('_', ' ')}
          </button>
        ))}
      </div>

      {/* Right Hardware Telemetry & Safety Status */}
      <div className="flex items-center gap-4 text-xs font-mono">
        {/* Hydraulic Pressure Indicator */}
        <div className="flex items-center gap-2 bg-slate-950 px-3 py-1.5 rounded border border-slate-800">
          <Activity className="w-4 h-4 text-cyan-400" />
          <span className="text-slate-400">HYD:</span>
          <span className="text-cyan-300 font-bold">{hydraulicPressureBar.toFixed(1)} bar</span>
        </div>

        {/* E-Stop Status */}
        <div
          className={`flex items-center gap-1.5 px-3 py-1.5 rounded border font-semibold ${
            eStopOk
              ? 'bg-emerald-950/60 border-emerald-800/80 text-emerald-400'
              : 'bg-rose-950 border-rose-700 text-rose-300 animate-pulse'
          }`}
        >
          <ShieldAlert className="w-4 h-4" />
          <span>{eStopOk ? 'E-STOP OK' : 'E-STOP TRIPPED'}</span>
        </div>

        {/* PLC Gateway Status */}
        <div
          className={`flex items-center gap-1.5 px-3 py-1.5 rounded border ${
            isConnected
              ? 'bg-slate-950 border-slate-800 text-emerald-400'
              : 'bg-amber-950/60 border-amber-800 text-amber-300'
          }`}
        >
          <Cpu className="w-4 h-4" />
          <span>{isConnected ? (isSimulator ? 'SIMULATOR ON' : 'PLC ONLINE') : 'CONNECTING...'}</span>
        </div>

        {/* Active Alarms Badge */}
        {activeAlarms.length > 0 && (
          <div className="flex items-center gap-1 bg-rose-900/60 text-rose-300 px-3 py-1.5 rounded border border-rose-700 animate-pulse">
            <AlertTriangle className="w-4 h-4" />
            <span>{activeAlarms.length} ALARM</span>
          </div>
        )}
      </div>
    </header>
  );
};
