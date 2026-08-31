import React, { useState } from 'react';
import { usePlcStore } from '../stores/usePlcStore.js';
import { wsClient } from '../services/wsClient.js';
import {
  ArrowRight,
  ArrowLeft,
  Flame,
  Compass,
  Gauge,
} from 'lucide-react';

export const DashboardView: React.FC = () => {
  const {
    feedPositionMm,
    feedTargetMm,
    feedSpeedMPerMin,
    hydraulicPressureBar,
    hydraulicPumpRunning,
    infeedClamp,
    carriageClamp,
    outfeedClamp,
    headsFiring,
    batchUpdateTags,
  } = usePlcStore();

  const [jogSpeed, setJogSpeed] = useState<'FAST' | 'SLOW'>('SLOW');

  const handleJog = (direction: 'FWD' | 'REV', isDown: boolean) => {
    const speed = jogSpeed === 'FAST' ? 80.0 : 20.0;
    if (isDown) {
      wsClient.jogStart(direction, speed);
    } else {
      wsClient.jogStop(direction);
    }
  };

  const toggleClamp = (clamp: 'infeed' | 'carriage' | 'outfeed') => {
    wsClient.toggleValve(clamp);
  };

  const toggleHydraulic = () => {
    wsClient.toggleValve('pump');
  };

  const fireHead = (headName: string) => {
    batchUpdateTags({
      headsFiring: { ...headsFiring, [headName]: true },
    });
    const tagName =
      headName === 'Marking'
        ? 'Marking_Trigger'
        : headName === 'Cutter'
        ? 'Shear_Cut_Trigger'
        : `Head_${headName}_Punch_Trigger`;

    wsClient.writeTag(tagName, true, 'Boolean');

    setTimeout(() => {
      batchUpdateTags({
        headsFiring: { ...usePlcStore.getState().headsFiring, [headName]: false },
      });
    }, 400);
  };

  return (
    <div className="p-6 space-y-6 flex-1 overflow-y-auto">
      {/* Top DRO Telemetry Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        {/* Main Feed Axis Position DRO */}
        <div className="industrial-card p-4 border-emerald-900/50 bg-gradient-to-br from-slate-900 via-slate-900 to-emerald-950/20">
          <div className="flex items-center justify-between text-xs text-slate-400 font-mono mb-1">
            <span className="flex items-center gap-1.5">
              <Compass className="w-4 h-4 text-emerald-400" /> FEED AXIS (X)
            </span>
            <span className="text-emerald-400 font-bold">ENCODER DRO</span>
          </div>
          <div className="text-4xl font-mono font-extrabold text-emerald-400 tracking-wider">
            {feedPositionMm.toFixed(2)}
            <span className="text-sm text-slate-400 ml-1 font-normal">mm</span>
          </div>
          <div className="text-xs text-slate-400 mt-2 font-mono flex justify-between">
            <span>TARGET: {feedTargetMm.toFixed(2)} mm</span>
            <span className="text-cyan-400">DELTA: {(feedTargetMm - feedPositionMm).toFixed(2)} mm</span>
          </div>
        </div>

        {/* Feed Carriage Speed */}
        <div className="industrial-card p-4">
          <div className="flex items-center justify-between text-xs text-slate-400 font-mono mb-1">
            <span className="flex items-center gap-1.5">
              <Gauge className="w-4 h-4 text-cyan-400" /> FEED VELOCITY
            </span>
            <span className="text-cyan-400">SERVO V1</span>
          </div>
          <div className="text-3xl font-mono font-extrabold text-cyan-400">
            {Math.abs(feedSpeedMPerMin).toFixed(1)}
            <span className="text-sm text-slate-400 ml-1 font-normal">m/min</span>
          </div>
          <div className="text-xs text-slate-400 mt-2 font-mono">
            STATUS: {Math.abs(feedSpeedMPerMin) > 0 ? (feedSpeedMPerMin > 0 ? 'FORWARD RUN' : 'REVERSE RUN') : 'STANDSTILL'}
          </div>
        </div>

        {/* Hydraulic Station */}
        <div className="industrial-card p-4">
          <div className="flex items-center justify-between text-xs text-slate-400 font-mono mb-1">
            <span className="flex items-center gap-1.5">
              <Flame className="w-4 h-4 text-amber-400" /> HYDRAULIC PRESSURE
            </span>
            <button
              onClick={toggleHydraulic}
              className={`px-2 py-0.5 text-[10px] font-bold rounded ${
                hydraulicPumpRunning ? 'bg-emerald-600 text-white' : 'bg-slate-700 text-slate-300'
              }`}
            >
              {hydraulicPumpRunning ? 'PUMP RUN' : 'PUMP OFF'}
            </button>
          </div>
          <div className="text-3xl font-mono font-extrabold text-amber-400">
            {hydraulicPressureBar.toFixed(1)}
            <span className="text-sm text-slate-400 ml-1 font-normal">bar</span>
          </div>
          <div className="text-xs text-slate-400 mt-2 font-mono">
            SYSTEM: {hydraulicPressureBar > 120 ? 'NORMAL OPERATING' : 'PRESSURE LOW'}
          </div>
        </div>

        {/* Clamps & Carriage Grippers */}
        <div className="industrial-card p-4">
          <div className="text-xs text-slate-400 font-mono mb-2">MATERIAL GRIPPERS & CLAMPS</div>
          <div className="grid grid-cols-3 gap-2 text-[11px] font-bold font-mono">
            <button
              onClick={() => toggleClamp('infeed')}
              className={`p-2 rounded border text-center transition-all ${
                infeedClamp
                  ? 'bg-emerald-950/80 border-emerald-600 text-emerald-300'
                  : 'bg-slate-800 border-slate-700 text-slate-400'
              }`}
            >
              INFEED
              <div className="text-[9px] mt-0.5">{infeedClamp ? 'CLAMPED' : 'OPEN'}</div>
            </button>

            <button
              onClick={() => toggleClamp('carriage')}
              className={`p-2 rounded border text-center transition-all ${
                carriageClamp
                  ? 'bg-emerald-950/80 border-emerald-600 text-emerald-300'
                  : 'bg-slate-800 border-slate-700 text-slate-400'
              }`}
            >
              CARRIAGE
              <div className="text-[9px] mt-0.5">{carriageClamp ? 'CLAMPED' : 'OPEN'}</div>
            </button>

            <button
              onClick={() => toggleClamp('outfeed')}
              className={`p-2 rounded border text-center transition-all ${
                outfeedClamp
                  ? 'bg-emerald-950/80 border-emerald-600 text-emerald-300'
                  : 'bg-slate-800 border-slate-700 text-slate-400'
              }`}
            >
              OUTFEED
              <div className="text-[9px] mt-0.5">{outfeedClamp ? 'CLAMPED' : 'OPEN'}</div>
            </button>
          </div>
        </div>
      </div>

      {/* 6-Head Visual Status & Manual Test Buttons */}
      <div className="industrial-card p-5 space-y-4">
        <div className="flex items-center justify-between border-b border-slate-800 pb-3">
          <div>
            <h2 className="text-sm font-bold text-slate-100 uppercase tracking-wider">
              Physical Tooling Heads (Innovance 6-Head Punching + Marking + Shearing)
            </h2>
            <p className="text-xs text-slate-400">Real-time cylinder positions & test triggers</p>
          </div>
          <div className="flex items-center gap-2 text-xs font-mono">
            <span className="px-2 py-1 rounded bg-slate-800 text-slate-300 border border-slate-700">
              Bed Offset DX: Auto-Calculated
            </span>
          </div>
        </div>

        {/* Heads Layout Matrix */}
        <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3">
          {/* Side A: DA1, DA2, DA3 */}
          {[
            { name: 'DA1', side: 'Side A', size: 'Ø14mm', offset: '200mm' },
            { name: 'DA2', side: 'Side A', size: 'Ø18mm', offset: '400mm' },
            { name: 'DA3', side: 'Side A', size: 'Ø22mm', offset: '600mm' },
            // Side B: DB1, DB2, DB3
            { name: 'DB1', side: 'Side B', size: 'Ø14mm', offset: '200mm' },
            { name: 'DB2', side: 'Side B', size: 'Ø18mm', offset: '400mm' },
            { name: 'DB3', side: 'Side B', size: 'Ø22mm', offset: '600mm' },
            // Marking & Cutter
            { name: 'Marking', side: '4-Cassette', size: 'Text Stamp', offset: '50mm' },
            { name: 'Cutter', side: 'Shear Blade', size: 'Cut-off', offset: '0mm' },
          ].map((head) => {
            const isFiring = headsFiring[head.name];
            return (
              <div
                key={head.name}
                className={`p-3 rounded-lg border flex flex-col justify-between transition-all duration-100 ${
                  isFiring
                    ? 'bg-rose-950/80 border-rose-500 shadow-lg shadow-rose-900/50 scale-105'
                    : 'bg-slate-950/60 border-slate-800'
                }`}
              >
                <div>
                  <div className="flex items-center justify-between mb-1">
                    <span className="text-xs font-black font-mono text-cyan-300">{head.name}</span>
                    <span className="text-[10px] font-mono text-slate-400">{head.side}</span>
                  </div>
                  <div className="text-xs font-bold text-slate-200">{head.size}</div>
                  <div className="text-[10px] text-slate-400 font-mono">DX: {head.offset}</div>
                </div>

                <button
                  onClick={() => fireHead(head.name)}
                  className={`mt-3 py-1.5 text-xs font-bold rounded flex items-center justify-center gap-1 transition-all ${
                    isFiring
                      ? 'bg-rose-600 text-white'
                      : 'bg-slate-800 hover:bg-slate-700 text-slate-300'
                  }`}
                >
                  {isFiring ? 'FIRING...' : 'TEST'}
                </button>
              </div>
            );
          })}
        </div>
      </div>

      {/* Manual Axis Jog Controls */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="industrial-card p-5 space-y-4">
          <div className="flex items-center justify-between">
            <h3 className="text-sm font-bold text-slate-100">FEED CARRIAGE MANUAL JOG</h3>
            <div className="flex bg-slate-950 p-0.5 rounded border border-slate-800 text-xs font-mono">
              <button
                onClick={() => setJogSpeed('SLOW')}
                className={`px-3 py-1 rounded ${
                  jogSpeed === 'SLOW' ? 'bg-cyan-600 text-white' : 'text-slate-400'
                }`}
              >
                SLOW (20mm/s)
              </button>
              <button
                onClick={() => setJogSpeed('FAST')}
                className={`px-3 py-1 rounded ${
                  jogSpeed === 'FAST' ? 'bg-cyan-600 text-white' : 'text-slate-400'
                }`}
              >
                FAST (80mm/s)
              </button>
            </div>
          </div>

          <div className="grid grid-cols-2 gap-4">
            <button
              onMouseDown={() => handleJog('REV', true)}
              onMouseUp={() => handleJog('REV', false)}
              onMouseLeave={() => handleJog('REV', false)}
              onTouchStart={() => handleJog('REV', true)}
              onTouchEnd={() => handleJog('REV', false)}
              className="industrial-btn-secondary h-20 text-base font-bold flex flex-col items-center justify-center gap-1 active:bg-cyan-700 select-none"
            >
              <ArrowLeft className="w-6 h-6 text-cyan-400" />
              <span>JOG REVERSE (&lt;&lt;)</span>
            </button>

            <button
              onMouseDown={() => handleJog('FWD', true)}
              onMouseUp={() => handleJog('FWD', false)}
              onMouseLeave={() => handleJog('FWD', false)}
              onTouchStart={() => handleJog('FWD', true)}
              onTouchEnd={() => handleJog('FWD', false)}
              className="industrial-btn-primary h-20 text-base font-bold flex flex-col items-center justify-center gap-1 select-none"
            >
              <ArrowRight className="w-6 h-6 text-white" />
              <span>JOG FORWARD (&gt;&gt;)</span>
            </button>
          </div>
        </div>

        {/* Quick Recipe & Job Card Overview */}
        <div className="industrial-card p-5 space-y-4">
          <div className="flex items-center justify-between">
            <h3 className="text-sm font-bold text-slate-100">CURRENT PRODUCTION JOB</h3>
            <span className="text-xs px-2 py-0.5 rounded bg-emerald-950 text-emerald-400 border border-emerald-800 font-mono">
              JOB-2026-001
            </span>
          </div>

          <div className="bg-slate-950/70 p-4 rounded border border-slate-800 space-y-2 text-xs font-mono">
            <div className="flex justify-between">
              <span className="text-slate-400">PART RECIPE:</span>
              <span className="text-slate-200 font-bold">ANG-75-L1500 (75x75x6 mm)</span>
            </div>
            <div className="flex justify-between">
              <span className="text-slate-400">TOTAL HOLES / STEPS:</span>
              <span className="text-cyan-300">6 Punches • 1 Mark • 1 Cut</span>
            </div>
            <div className="flex justify-between">
              <span className="text-slate-400">PIECES COMPLETED:</span>
              <span className="text-emerald-400 font-bold">14 / 50 Pcs (28%)</span>
            </div>
            <div className="w-full bg-slate-800 rounded-full h-2 mt-2 overflow-hidden">
              <div className="bg-emerald-500 h-2 rounded-full" style={{ width: '28%' }} />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
