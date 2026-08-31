import React, { useState } from 'react';
import { usePlcStore } from '../stores/usePlcStore.js';
import { wsClient } from '../services/wsClient.js';
import {
  Sliders,
  Power,
  RotateCcw,
  Zap,
  Lock,
  Unlock,
  Gauge,
  Flame,
  ArrowRight,
  ArrowLeft,
} from 'lucide-react';

export const ManualControlView: React.FC = () => {
  const {
    feedPositionMm,
    hydraulicPressureBar,
    hydraulicPumpRunning,
    infeedClamp,
    carriageClamp,
    outfeedClamp,
    headsFiring,
    batchUpdateTags,
  } = usePlcStore();

  const [stepIncrement, setStepIncrement] = useState<number>(10.0);
  const [jogSpeed, setJogSpeed] = useState<number>(30); // %

  const handleStepJog = (direction: 'FWD' | 'REV') => {
    const delta = direction === 'FWD' ? stepIncrement : -stepIncrement;
    const newPos = Math.max(0, feedPositionMm + delta);
    batchUpdateTags({ feedPositionMm: newPos });
    wsClient.writeTag('Carriage_Target_Pos', newPos, 'Float');
  };

  const handleToggleHpu = () => {
    const newState = !hydraulicPumpRunning;
    batchUpdateTags({
      hydraulicPumpRunning: newState,
      hydraulicPressureBar: newState ? 145.0 : 0.0,
    });
    wsClient.writeTag('HPU_Motor_Run', newState, 'Boolean');
  };

  const handleToggleClamp = (clamp: 'infeed' | 'carriage' | 'outfeed') => {
    if (clamp === 'infeed') {
      batchUpdateTags({ infeedClamp: !infeedClamp });
      wsClient.writeTag('Clamp_Infeed_Closed', !infeedClamp, 'Boolean');
    } else if (clamp === 'carriage') {
      batchUpdateTags({ carriageClamp: !carriageClamp });
      wsClient.writeTag('Clamp_Carriage_Closed', !carriageClamp, 'Boolean');
    } else {
      batchUpdateTags({ outfeedClamp: !outfeedClamp });
      wsClient.writeTag('Clamp_Outfeed_Closed', !outfeedClamp, 'Boolean');
    }
  };

  const handleTestHead = (head: string) => {
    batchUpdateTags({
      headsFiring: { ...headsFiring, [head]: true },
    });
    setTimeout(() => {
      batchUpdateTags({
        headsFiring: { ...usePlcStore.getState().headsFiring, [head]: false },
      });
    }, 450);
  };

  return (
    <div className="p-6 space-y-6 flex-1 overflow-y-auto">
      {/* Header */}
      <div className="flex items-center justify-between border-b border-scada-750 pb-4">
        <div>
          <h2 className="text-xl font-extrabold text-slate-100 flex items-center gap-2.5">
            <Sliders className="w-6 h-6 text-neon-cyan" />
            CNC Manual Jogging & Valve Actuators Console
          </h2>
          <p className="text-xs text-slate-400 font-mono">
            Direct axis jogging, hydraulic clamp sequencing, and individual tool head stroke testing.
          </p>
        </div>

        {/* Live Carriage DRO Pill */}
        <div className="flex items-center gap-4 bg-scada-950 px-5 py-2.5 rounded-2xl border border-cyan-500/40 shadow-neon-cyan font-mono">
          <span className="text-xs text-slate-400 font-bold">FEED CARRIAGE:</span>
          <span className="text-2xl font-extrabold text-neon-cyan tracking-wider">
            {feedPositionMm.toFixed(2)} <span className="text-xs text-slate-400 font-normal">mm</span>
          </span>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* 1. Feed Carriage Manual Jog Section */}
        <div className="scada-panel p-6 space-y-5">
          <div className="text-xs font-bold text-slate-200 font-mono flex items-center justify-between border-b border-scada-750 pb-3">
            <span className="flex items-center gap-2">
              <Gauge className="w-4 h-4 text-neon-cyan" /> CARRIAGE AXIS (X) MANUAL JOG
            </span>
            <span className="text-[10px] text-slate-400">PULSE GENERATOR</span>
          </div>

          {/* Calibrated Incremental Step Selector */}
          <div className="space-y-2">
            <label className="text-xs font-mono text-slate-300 font-bold">INCREMENTAL STEP (mm)</label>
            <div className="grid grid-cols-5 gap-2 font-mono">
              {[0.1, 1.0, 10.0, 50.0, 100.0].map((step) => (
                <button
                  key={step}
                  onClick={() => setStepIncrement(step)}
                  className={`py-3 rounded-xl text-xs font-extrabold border transition-all ${
                    stepIncrement === step
                      ? 'bg-gradient-to-b from-cyan-500 to-cyan-700 text-slate-950 border-cyan-300 shadow-neon-cyan'
                      : 'bg-scada-950 text-slate-300 border-scada-750 hover:border-slate-600'
                  }`}
                >
                  {step}mm
                </button>
              ))}
            </div>
          </div>

          {/* Jog Speed Slider */}
          <div className="space-y-2 font-mono">
            <div className="flex justify-between text-xs font-bold text-slate-300">
              <span>MANUAL JOG VELOCITY:</span>
              <span className="text-neon-cyan">{jogSpeed}%</span>
            </div>
            <input
              type="range"
              min="5"
              max="100"
              value={jogSpeed}
              onChange={(e) => setJogSpeed(parseInt(e.target.value))}
              className="w-full h-2 bg-scada-950 rounded-lg appearance-none cursor-pointer accent-cyan-400"
            />
          </div>

          {/* Heavy Duty Step Jog Buttons */}
          <div className="grid grid-cols-2 gap-4 pt-2">
            <button
              onClick={() => handleStepJog('REV')}
              className="scada-btn-secondary h-20 text-base font-extrabold flex flex-col items-center justify-center gap-1"
            >
              <ArrowLeft className="w-6 h-6 text-cyan-400" />
              <span>STEP REV (-{stepIncrement}mm)</span>
            </button>

            <button
              onClick={() => handleStepJog('FWD')}
              className="scada-btn-primary h-20 text-base font-extrabold flex flex-col items-center justify-center gap-1"
            >
              <ArrowRight className="w-6 h-6" />
              <span>STEP FWD (+{stepIncrement}mm)</span>
            </button>
          </div>

          {/* Zero Datum Reset */}
          <button
            onClick={() => {
              batchUpdateTags({ feedPositionMm: 0 });
              wsClient.writeTag('Carriage_Zero_Set', true, 'Boolean');
            }}
            className="w-full scada-btn-secondary py-3 text-xs font-mono text-slate-300 flex items-center justify-center gap-2"
          >
            <RotateCcw className="w-4 h-4 text-cyan-400" />
            SET CARRIAGE ZERO REFERENCE (DATUM 0.00mm)
          </button>
        </div>

        {/* 2. Hydraulic Power Unit & Clamps Console */}
        <div className="scada-panel p-6 space-y-5">
          <div className="text-xs font-bold text-slate-200 font-mono flex items-center justify-between border-b border-scada-750 pb-3">
            <span className="flex items-center gap-2">
              <Flame className="w-4 h-4 text-neon-amber" /> HYDRAULIC POWER UNIT & CLAMPS
            </span>
            <span className={hydraulicPumpRunning ? 'led-emerald' : 'led-rose'} />
          </div>

          {/* HPU Master Toggle */}
          <div className="p-4 bg-scada-950 rounded-2xl border border-scada-750 space-y-3">
            <div className="flex items-center justify-between font-mono">
              <div>
                <div className="font-extrabold text-sm text-slate-100">MAIN HPU MOTOR (15kW)</div>
                <div className="text-[11px] text-slate-400">
                  Pressure: {hydraulicPressureBar.toFixed(1)} bar
                </div>
              </div>
              <button
                onClick={handleToggleHpu}
                className={`px-4 py-2.5 rounded-xl font-mono font-extrabold text-xs flex items-center gap-2 transition-all ${
                  hydraulicPumpRunning
                    ? 'bg-rose-950 text-rose-300 border border-rose-500/60 shadow-neon-rose'
                    : 'bg-emerald-950 text-neon-emerald border border-emerald-500/60 shadow-neon-emerald'
                }`}
              >
                <Power className="w-4 h-4" />
                {hydraulicPumpRunning ? 'STOP HPU' : 'START HPU'}
              </button>
            </div>
          </div>

          {/* Pneumatic / Hydraulic Clamps */}
          <div className="space-y-3 font-mono text-xs">
            <div className="text-slate-300 font-bold">MATERIAL CLAMPING ACTUATORS</div>

            {[
              { id: 'infeed' as const, label: 'INFEED CONVEYOR CLAMP', state: infeedClamp },
              { id: 'carriage' as const, label: 'CARRIAGE GRIPPER JAW', state: carriageClamp },
              { id: 'outfeed' as const, label: 'OUTFEED DISCHARGE CLAMP', state: outfeedClamp },
            ].map((c) => (
              <div
                key={c.id}
                className="flex items-center justify-between p-3.5 rounded-xl bg-scada-950 border border-scada-750"
              >
                <div>
                  <div className="font-bold text-slate-200">{c.label}</div>
                  <div className="text-[10px] text-slate-400">
                    STATUS: {c.state ? 'CLAMPED (LOCKED)' : 'RELEASED (OPEN)'}
                  </div>
                </div>
                <button
                  onClick={() => handleToggleClamp(c.id)}
                  className={`px-3.5 py-2 rounded-lg font-bold text-xs flex items-center gap-1.5 transition-all ${
                    c.state
                      ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-slate-950 shadow-neon-emerald'
                      : 'bg-scada-800 text-slate-300 hover:bg-scada-700'
                  }`}
                >
                  {c.state ? <Lock className="w-3.5 h-3.5" /> : <Unlock className="w-3.5 h-3.5" />}
                  {c.state ? 'CLAMPED' : 'UNCLAMP'}
                </button>
              </div>
            ))}
          </div>
        </div>

        {/* 3. Individual Tooling Head Stroke Test */}
        <div className="scada-panel p-6 space-y-5">
          <div className="text-xs font-bold text-slate-200 font-mono flex items-center justify-between border-b border-scada-750 pb-3">
            <span className="flex items-center gap-2">
              <Zap className="w-4 h-4 text-neon-amber" /> SINGLE STROKE TEST CONSOLE
            </span>
            <span className="text-[10px] text-slate-400">MANUAL OVERRIDE</span>
          </div>

          <div className="grid grid-cols-2 gap-3 font-mono">
            {['DA1', 'DA2', 'DA3', 'DB1', 'DB2', 'DB3', 'Marking', 'Cutter'].map((head) => {
              const isFiring = headsFiring[head];
              return (
                <button
                  key={head}
                  onClick={() => handleTestHead(head)}
                  disabled={!hydraulicPumpRunning}
                  className={`p-4 rounded-xl border flex flex-col items-center justify-center gap-1 transition-all ${
                    isFiring
                      ? 'bg-rose-600 text-white shadow-neon-rose scale-105'
                      : 'bg-scada-950 border-scada-750 text-slate-200 hover:border-cyan-400 hover:bg-scada-850/60'
                  } disabled:opacity-40 disabled:pointer-events-none`}
                >
                  <span className="font-extrabold text-sm">{head}</span>
                  <span className="text-[10px] font-bold text-slate-400">
                    {isFiring ? 'PUNCHING...' : 'TRIGGER STROKE'}
                  </span>
                </button>
              );
            })}
          </div>

          {!hydraulicPumpRunning && (
            <div className="p-3 bg-amber-950/60 border border-amber-500/40 rounded-xl text-xs font-mono text-amber-300 text-center">
              ⚠️ Start HPU pump before testing cylinder strokes.
            </div>
          )}
        </div>
      </div>
    </div>
  );
};
