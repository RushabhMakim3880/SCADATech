import React, { useState } from 'react';
import { usePlcStore } from '../stores/usePlcStore.js';
import { wsClient } from '../services/wsClient.js';
import {
  Sliders,
  ArrowLeft,
  ArrowRight,
  RotateCcw,
} from 'lucide-react';

export const ManualControlView: React.FC = () => {
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
    eStopOk,
    guardsOk,
    batchUpdateTags,
  } = usePlcStore();

  const [stepSize, setStepSize] = useState<number>(10.0); // mm
  const [jogMode, setJogMode] = useState<'CONTINUOUS' | 'STEP'>('CONTINUOUS');

  const handleContinuousJog = (direction: 'FWD' | 'REV', isDown: boolean) => {
    if (isDown) {
      wsClient.jogStart(direction, 50.0);
    } else {
      wsClient.jogStop(direction);
    }
  };

  const handleStepJog = (direction: 'FWD' | 'REV') => {
    const delta = direction === 'FWD' ? stepSize : -stepSize;
    const target = Math.max(0, feedPositionMm + delta);
    wsClient.writeTag('Feed_Axis_Target_Position', target, 'Float');
  };

  const handleHomeAxis = () => {
    wsClient.writeTag('Feed_Axis_Target_Position', 0.0, 'Float');
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
      {/* Header */}
      <div className="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
          <h2 className="text-xl font-bold text-slate-100 flex items-center gap-2">
            <Sliders className="w-5 h-5 text-cyan-400" />
            Manual Axis Jogging & Valve Actuation Console
          </h2>
          <p className="text-xs text-slate-400">
            Push-and-hold continuous servo jog, calibrated incremental stepping, and cylinder valve testing.
          </p>
        </div>

        <div className="flex items-center gap-3">
          <button
            onClick={handleHomeAxis}
            className="industrial-btn-secondary px-4 py-2 text-xs font-mono"
          >
            <RotateCcw className="w-4 h-4 text-cyan-400" />
            GOTO HOME (X: 0.00)
          </button>
        </div>
      </div>

      {/* Main DRO & Status Display */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div className="industrial-card p-5 bg-gradient-to-br from-slate-900 to-emerald-950/30 border-emerald-900/60">
          <div className="text-xs font-mono text-slate-400 mb-1">FEED CARRIAGE POSITION (X)</div>
          <div className="text-5xl font-mono font-extrabold text-emerald-400 tracking-wider">
            {feedPositionMm.toFixed(2)}
            <span className="text-base text-slate-400 ml-2 font-normal">mm</span>
          </div>
          <div className="text-xs font-mono text-slate-400 mt-2 flex justify-between">
            <span>TARGET: {feedTargetMm.toFixed(2)} mm</span>
            <span className="text-cyan-400">VELOCITY: {Math.abs(feedSpeedMPerMin).toFixed(1)} m/min</span>
          </div>
        </div>

        <div className="industrial-card p-5">
          <div className="flex items-center justify-between text-xs font-mono text-slate-400 mb-1">
            <span>HYDRAULIC POWER UNIT (HPU)</span>
            <button
              onClick={toggleHydraulic}
              className={`px-2.5 py-1 text-xs font-bold rounded ${
                hydraulicPumpRunning ? 'bg-emerald-600 text-white' : 'bg-slate-700 text-slate-300'
              }`}
            >
              {hydraulicPumpRunning ? 'PUMP RUNNING' : 'PUMP OFF'}
            </button>
          </div>
          <div className="text-4xl font-mono font-extrabold text-amber-400">
            {hydraulicPressureBar.toFixed(1)}
            <span className="text-base text-slate-400 ml-2 font-normal">bar</span>
          </div>
          <div className="text-xs font-mono text-slate-400 mt-2">
            OPTIMAL RANGE: 130.0 - 160.0 bar
          </div>
        </div>

        <div className="industrial-card p-5">
          <div className="text-xs font-mono text-slate-400 mb-2">SAFETY CIRCUIT INTERLOCKS</div>
          <div className="space-y-2 text-xs font-mono">
            <div className="flex items-center justify-between p-2 rounded bg-slate-950 border border-slate-800">
              <span>EMERGENCY STOP</span>
              <span className={eStopOk ? 'text-emerald-400 font-bold' : 'text-rose-400 font-bold'}>
                {eStopOk ? 'CIRCUIT OK' : 'TRIPPED'}
              </span>
            </div>
            <div className="flex items-center justify-between p-2 rounded bg-slate-950 border border-slate-800">
              <span>SAFETY GUARD DOORS</span>
              <span className={guardsOk ? 'text-emerald-400 font-bold' : 'text-amber-400 font-bold'}>
                {guardsOk ? 'CLOSED' : 'OPEN'}
              </span>
            </div>
          </div>
        </div>
      </div>

      {/* Axis Jogging Console */}
      <div className="industrial-card p-6 space-y-5">
        <div className="flex items-center justify-between border-b border-slate-800 pb-3">
          <h3 className="text-base font-bold text-slate-100 uppercase tracking-wider">
            Servo Feed Axis Controls
          </h3>

          <div className="flex items-center gap-4">
            <div className="flex bg-slate-950 p-1 rounded-lg border border-slate-800 text-xs font-mono">
              <button
                onClick={() => setJogMode('CONTINUOUS')}
                className={`px-3 py-1.5 rounded font-bold transition-all ${
                  jogMode === 'CONTINUOUS' ? 'bg-cyan-600 text-white shadow' : 'text-slate-400'
                }`}
              >
                CONTINUOUS JOG
              </button>
              <button
                onClick={() => setJogMode('STEP')}
                className={`px-3 py-1.5 rounded font-bold transition-all ${
                  jogMode === 'STEP' ? 'bg-cyan-600 text-white shadow' : 'text-slate-400'
                }`}
              >
                INCREMENTAL STEP
              </button>
            </div>

            {jogMode === 'STEP' && (
              <div className="flex items-center gap-1 bg-slate-950 p-1 rounded-lg border border-slate-800 text-xs font-mono">
                {[0.1, 1.0, 10.0, 50.0, 100.0].map((s) => (
                  <button
                    key={s}
                    onClick={() => setStepSize(s)}
                    className={`px-2.5 py-1 rounded font-bold ${
                      stepSize === s ? 'bg-emerald-600 text-white' : 'text-slate-400'
                    }`}
                  >
                    {s}mm
                  </button>
                ))}
              </div>
            )}
          </div>
        </div>

        {/* Large Jog Touch Buttons */}
        <div className="grid grid-cols-2 gap-6">
          {jogMode === 'CONTINUOUS' ? (
            <>
              <button
                onMouseDown={() => handleContinuousJog('REV', true)}
                onMouseUp={() => handleContinuousJog('REV', false)}
                onMouseLeave={() => handleContinuousJog('REV', false)}
                onTouchStart={() => handleContinuousJog('REV', true)}
                onTouchEnd={() => handleContinuousJog('REV', false)}
                className="industrial-btn-secondary h-28 text-lg font-bold flex flex-col items-center justify-center gap-2 active:bg-cyan-800 select-none shadow-lg"
              >
                <ArrowLeft className="w-8 h-8 text-cyan-400" />
                <span>CONTINUOUS JOG REVERSE (&lt;&lt;)</span>
              </button>

              <button
                onMouseDown={() => handleContinuousJog('FWD', true)}
                onMouseUp={() => handleContinuousJog('FWD', false)}
                onMouseLeave={() => handleContinuousJog('FWD', false)}
                onTouchStart={() => handleContinuousJog('FWD', true)}
                onTouchEnd={() => handleContinuousJog('FWD', false)}
                className="industrial-btn-primary h-28 text-lg font-bold flex flex-col items-center justify-center gap-2 active:scale-95 select-none shadow-lg"
              >
                <ArrowRight className="w-8 h-8 text-white" />
                <span>CONTINUOUS JOG FORWARD (&gt;&gt;)</span>
              </button>
            </>
          ) : (
            <>
              <button
                onClick={() => handleStepJog('REV')}
                className="industrial-btn-secondary h-28 text-lg font-bold flex flex-col items-center justify-center gap-2 active:bg-cyan-800 select-none shadow-lg"
              >
                <ArrowLeft className="w-8 h-8 text-cyan-400" />
                <span>STEP REVERSE (-{stepSize}mm)</span>
              </button>

              <button
                onClick={() => handleStepJog('FWD')}
                className="industrial-btn-primary h-28 text-lg font-bold flex flex-col items-center justify-center gap-2 active:scale-95 select-none shadow-lg"
              >
                <ArrowRight className="w-8 h-8 text-white" />
                <span>STEP FORWARD (+{stepSize}mm)</span>
              </button>
            </>
          )}
        </div>
      </div>

      {/* Hydraulic Clamps & Tool Head Actuators */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Clamps */}
        <div className="industrial-card p-5 space-y-4">
          <h3 className="text-sm font-bold text-slate-100 uppercase tracking-wider">
            Material Clamping Cylinders
          </h3>
          <div className="grid grid-cols-3 gap-3">
            {[
              { id: 'infeed' as const, label: 'INFEED CLAMP', state: infeedClamp },
              { id: 'carriage' as const, label: 'CARRIAGE GRIPPER', state: carriageClamp },
              { id: 'outfeed' as const, label: 'OUTFEED CLAMP', state: outfeedClamp },
            ].map((c) => (
              <button
                key={c.id}
                onClick={() => toggleClamp(c.id)}
                className={`p-4 rounded-lg border font-mono text-center transition-all ${
                  c.state
                    ? 'bg-emerald-950 border-emerald-600 text-emerald-300 shadow-md'
                    : 'bg-slate-900 border-slate-800 text-slate-400'
                }`}
              >
                <div className="text-xs font-bold mb-1">{c.label}</div>
                <div className="text-sm font-black">{c.state ? 'CLAMPED' : 'OPEN'}</div>
              </button>
            ))}
          </div>
        </div>

        {/* Head Single Strokes */}
        <div className="industrial-card p-5 space-y-4">
          <h3 className="text-sm font-bold text-slate-100 uppercase tracking-wider">
            Punch & Tool Single-Stroke Test
          </h3>
          <div className="grid grid-cols-4 gap-2 font-mono">
            {['DA1', 'DA2', 'DA3', 'DB1', 'DB2', 'DB3', 'Marking', 'Cutter'].map((h) => {
              const isFiring = headsFiring[h];
              return (
                <button
                  key={h}
                  onClick={() => fireHead(h)}
                  className={`p-2.5 rounded-lg border font-bold text-xs transition-all ${
                    isFiring
                      ? 'bg-rose-600 text-white border-rose-500 scale-105'
                      : 'bg-slate-900 hover:bg-slate-800 text-slate-200 border-slate-800'
                  }`}
                >
                  <div className="text-cyan-400 text-[11px]">{h}</div>
                  <div className="text-[10px] mt-0.5">{isFiring ? 'STROKE' : 'FIRE'}</div>
                </button>
              );
            })}
          </div>
        </div>
      </div>
    </div>
  );
};
