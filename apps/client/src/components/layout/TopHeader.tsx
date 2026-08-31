import React, { useState, useEffect } from 'react';
import { usePlcStore } from '../../stores/usePlcStore.js';
import { Activity, ShieldAlert, Cpu, AlertTriangle, ShieldCheck, Clock } from 'lucide-react';

export const TopHeader: React.FC = () => {
  const { isConnected, isSimulator, mode, setMode, eStopOk, activeAlarms, hydraulicPressureBar } = usePlcStore();
  const [timeStr, setTimeStr] = useState<string>('');

  useEffect(() => {
    const updateTime = () => {
      const now = new Date();
      setTimeStr(now.toLocaleTimeString([], { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' }));
    };
    updateTime();
    const interval = setInterval(updateTime, 1000);
    return () => clearInterval(interval);
  }, []);

  return (
    <header className="h-18 bg-scada-900/90 border-b border-scada-750/80 px-6 py-3 flex items-center justify-between select-none backdrop-blur-xl relative z-20 shadow-2xl">
      {/* Brand & Machine Title with Metallic Badge */}
      <div className="flex items-center gap-4">
        <div className="w-11 h-11 rounded-xl bg-gradient-to-br from-cyan-400 via-cyan-600 to-blue-700 flex items-center justify-center font-black text-slate-950 text-xl tracking-tighter shadow-neon-cyan border border-cyan-300/40">
          6H
        </div>
        <div>
          <div className="flex items-center gap-2.5">
            <h1 className="text-sm font-black tracking-widest text-slate-100 uppercase">
              INNOVANCE CNC ANGLE LINE
            </h1>
            <span className="text-[10px] px-2 py-0.5 rounded-full bg-cyan-950/90 text-neon-cyan border border-cyan-500/40 font-mono font-bold tracking-wider shadow-sm">
              SERIES 6-HEAD
            </span>
          </div>
          <p className="text-[11px] text-slate-400 font-mono tracking-tight flex items-center gap-2">
            <span>STATION: #01</span>
            <span>•</span>
            <span className="text-slate-300">SKIPPER PUNCH & SHEAR HMI</span>
          </p>
        </div>
      </div>

      {/* Center: Hardware Segmented Mode Rocker Selector */}
      <div className="flex items-center bg-scada-950/90 p-1.5 rounded-xl border border-scada-750/90 shadow-inner-dark">
        {(['MANUAL', 'SEMI_AUTO', 'AUTO'] as const).map((m) => {
          const isActive = mode === m;
          return (
            <button
              key={m}
              onClick={() => setMode(m)}
              className={`px-5 py-2 text-xs font-mono font-extrabold rounded-lg transition-all duration-200 flex items-center gap-2 relative ${
                isActive
                  ? m === 'AUTO'
                    ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-slate-950 shadow-neon-emerald border border-emerald-300/50'
                    : 'bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 shadow-neon-cyan border border-cyan-300/50'
                  : 'text-slate-400 hover:text-slate-200 hover:bg-scada-850/60'
              }`}
            >
              <span className={isActive ? (m === 'AUTO' ? 'led-emerald' : 'led-cyan') : 'w-2 h-2 rounded-full bg-slate-700'} />
              <span>{m.replace('_', ' ')}</span>
            </button>
          );
        })}
      </div>

      {/* Right: Live Telemetry HUD & Safety Interlocks */}
      <div className="flex items-center gap-3.5 text-xs font-mono">
        {/* Hydraulic Pressure Widget */}
        <div className="flex items-center gap-2.5 bg-scada-950/90 px-3.5 py-2 rounded-xl border border-scada-750/80 shadow-inner-dark">
          <Activity className="w-4 h-4 text-neon-amber animate-pulse" />
          <div>
            <div className="text-[9px] text-slate-400 font-bold tracking-wider">HPU PRESSURE</div>
            <div className="text-neon-amber font-extrabold text-xs">
              {hydraulicPressureBar.toFixed(1)} <span className="text-[10px] text-slate-400 font-normal">BAR</span>
            </div>
          </div>
        </div>

        {/* E-Stop Safety Status */}
        <div
          className={`flex items-center gap-2 px-3.5 py-2 rounded-xl border font-bold transition-all ${
            eStopOk
              ? 'bg-emerald-950/50 border-emerald-500/40 text-neon-emerald shadow-sm'
              : 'bg-rose-950/80 border-rose-500 text-rose-300 animate-pulse-fast shadow-neon-rose'
          }`}
        >
          {eStopOk ? <ShieldCheck className="w-4 h-4 text-neon-emerald" /> : <ShieldAlert className="w-4 h-4 text-neon-rose" />}
          <span>{eStopOk ? 'E-STOP OK' : 'E-STOP TRIPPED'}</span>
        </div>

        {/* PLC Gateway Watchdog */}
        <div
          className={`flex items-center gap-2 px-3.5 py-2 rounded-xl border ${
            isConnected
              ? 'bg-scada-950/80 border-scada-700 text-neon-cyan'
              : 'bg-amber-950/60 border-amber-500/60 text-amber-300'
          }`}
        >
          <Cpu className="w-4 h-4" />
          <span className="font-bold">{isConnected ? (isSimulator ? 'VIRTUAL PLC' : 'PLC ONLINE') : 'OFFLINE'}</span>
          <span className={isConnected ? 'led-cyan' : 'led-amber'} />
        </div>

        {/* Live Alarms Badge */}
        {activeAlarms.length > 0 && (
          <div className="flex items-center gap-2 bg-rose-950/90 text-rose-200 px-3.5 py-2 rounded-xl border border-rose-500/80 shadow-neon-rose animate-bounce">
            <AlertTriangle className="w-4 h-4 text-neon-rose" />
            <span className="font-black">{activeAlarms.length} ALARM</span>
          </div>
        )}

        {/* Digital Clock */}
        <div className="flex items-center gap-2 bg-scada-950/90 px-3.5 py-2 rounded-xl border border-scada-750/80 text-slate-300 font-extrabold shadow-inner-dark">
          <Clock className="w-3.5 h-3.5 text-cyan-400" />
          <span>{timeStr}</span>
        </div>
      </div>
    </header>
  );
};
