import React, { useState } from 'react';
import { usePlcStore } from '../stores/usePlcStore.js';
import { wsClient } from '../services/wsClient.js';
import { VirtualKeypadModal } from '../components/common/VirtualKeypadModal.js';
import {
  Power,
  RotateCcw,
  Lock,
  Unlock,
  ArrowRight,
  ArrowLeft,
  Activity,
  Droplets,
  Calculator,
} from 'lucide-react';

export const ManualControlView: React.FC = () => {
  const {
    feedPositionMm,
    hydraulicPumpRunning,
    infeedClamp,
    carriageClamp,
    outfeedClamp,
    headsFiring,
    batchUpdateTags,
  } = usePlcStore();

  const [stepIncrement, setStepIncrement] = useState<number>(10.0);
  const [headLubRunning, setHeadLubRunning] = useState<boolean>(false);
  const [oilCircRunning, setOilCircRunning] = useState<boolean>(false);
  const [princherGoTarget, setPrincherGoTarget] = useState<number>(0.0);

  // Keypad State
  const [isKeypadOpen, setIsKeypadOpen] = useState(false);

  const handleStepJog = (direction: 'FWD' | 'REV') => {
    const delta = direction === 'FWD' ? stepIncrement : -stepIncrement;
    const newPos = Math.max(0, feedPositionMm + delta);
    batchUpdateTags({ feedPositionMm: newPos });
    wsClient.writeTag('Carriage_Target_Pos', newPos, 'Float');
  };

  const handlePrincherGo = () => {
    batchUpdateTags({ feedPositionMm: princherGoTarget });
    wsClient.writeTag('Carriage_Target_Pos', princherGoTarget, 'Float');
  };

  const handleToggleHpu = () => {
    const newState = !hydraulicPumpRunning;
    batchUpdateTags({
      hydraulicPumpRunning: newState,
      hydraulicPressureBar: newState ? 145.0 : 0.0,
    });
    wsClient.writeTag('HPU_Motor_Run', newState, 'Boolean');
  };

  const handleToggleHeadLub = () => {
    const next = !headLubRunning;
    setHeadLubRunning(next);
    wsClient.writeTag('Head_Lub_Motor_Run', next, 'Boolean');
  };

  const handleToggleOilCirc = () => {
    const next = !oilCircRunning;
    setOilCircRunning(next);
    wsClient.writeTag('Oil_Circ_Motor_Run', next, 'Boolean');
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
    <div className="p-4 md:p-6 space-y-4 flex-1 overflow-y-auto bg-[#070b12] text-white">
      {/* Top Header */}
      <div className="bg-[#0e1420] border border-[#1e2a3c] rounded-2xl p-4 shadow-xl flex flex-wrap items-center justify-between gap-4">
        <div>
          <h2 className="text-xl font-black text-white tracking-wide">
            Manual Operations & Tooling Console
          </h2>
          <p className="text-xs text-slate-400 font-medium mt-1">
            Manual carriage positioning, hydraulic motor controls, lubrication pumps, pneumatic clamps, and single stroke tooling tests.
          </p>
        </div>

        <div className="cnc-dro flex items-center gap-3 px-4 py-2 rounded-xl">
          <span className="text-[10px] font-black text-slate-400 uppercase tracking-widest">PRINCHER (X):</span>
          <span className="cnc-dro-val text-xl font-black">{feedPositionMm.toFixed(2)}</span>
          <span className="cnc-dro-unit text-xs">mm</span>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {/* 1. MOTOR OPERATIONS PANEL */}
        <div className="cnc-card flex flex-col justify-between">
          <div className="cnc-card-header">
            <span className="flex items-center gap-2">
              <Power className="w-4 h-4 text-emerald-400" />
              <span>Hydraulic & Lubrication Motors</span>
            </span>
            <span className="text-[10px] font-mono text-slate-400">PUMPS & VALVES</span>
          </div>

          <div className="cnc-card-body space-y-4">
            <div className="grid grid-cols-3 gap-2.5 text-center">
              {/* Main Hyd Motor */}
              <button
                onClick={handleToggleHpu}
                className={`p-3 rounded-xl border flex flex-col items-center justify-center min-h-[90px] transition-all active:scale-95 shadow-md ${
                  hydraulicPumpRunning
                    ? 'bg-gradient-to-b from-emerald-600 to-emerald-700 text-white border-emerald-400 shadow-emerald-950/50'
                    : 'bg-[#121926] text-slate-300 border-[#222e42] hover:bg-[#1a2436]'
                }`}
              >
                <Power className={`w-5 h-5 mb-1.5 ${hydraulicPumpRunning ? 'text-white' : 'text-slate-400'}`} />
                <span className="text-xs font-black leading-tight">MAIN HYD<br />MOTOR</span>
                <span className={`w-2.5 h-2.5 rounded-full mt-2 ${hydraulicPumpRunning ? 'bg-emerald-300 shadow-[0_0_8px_#34d399]' : 'bg-slate-600'}`} />
              </button>

              {/* Head Lub Motor */}
              <button
                onClick={handleToggleHeadLub}
                className={`p-3 rounded-xl border flex flex-col items-center justify-center min-h-[90px] transition-all active:scale-95 shadow-md ${
                  headLubRunning
                    ? 'bg-gradient-to-b from-emerald-600 to-emerald-700 text-white border-emerald-400 shadow-emerald-950/50'
                    : 'bg-[#121926] text-slate-300 border-[#222e42] hover:bg-[#1a2436]'
                }`}
              >
                <Droplets className={`w-5 h-5 mb-1.5 ${headLubRunning ? 'text-white' : 'text-slate-400'}`} />
                <span className="text-xs font-black leading-tight">HEAD LUB<br />MOTOR</span>
                <span className={`w-2.5 h-2.5 rounded-full mt-2 ${headLubRunning ? 'bg-emerald-300 shadow-[0_0_8px_#34d399]' : 'bg-slate-600'}`} />
              </button>

              {/* Oil Circ Motor */}
              <button
                onClick={handleToggleOilCirc}
                className={`p-3 rounded-xl border flex flex-col items-center justify-center min-h-[90px] transition-all active:scale-95 shadow-md ${
                  oilCircRunning
                    ? 'bg-gradient-to-b from-emerald-600 to-emerald-700 text-white border-emerald-400 shadow-emerald-950/50'
                    : 'bg-[#121926] text-slate-300 border-[#222e42] hover:bg-[#1a2436]'
                }`}
              >
                <Activity className={`w-5 h-5 mb-1.5 ${oilCircRunning ? 'text-white' : 'text-slate-400'}`} />
                <span className="text-xs font-black leading-tight">OIL CIRC.<br />MOTOR</span>
                <span className={`w-2.5 h-2.5 rounded-full mt-2 ${oilCircRunning ? 'bg-emerald-300 shadow-[0_0_8px_#34d399]' : 'bg-slate-600'}`} />
              </button>
            </div>

            {/* Princher Go MM Target Row */}
            <div className="p-3.5 bg-[#090d14] border border-[#1e2a3c] rounded-xl space-y-2">
              <label className="text-[11px] font-black text-slate-400 tracking-wider uppercase block">
                PRINCHER GO TARGET (MM)
              </label>
              <div className="flex items-center gap-2">
                <input
                  type="number"
                  value={princherGoTarget}
                  onChange={(e) => setPrincherGoTarget(parseFloat(e.target.value) || 0)}
                  className="cnc-input font-mono font-black text-base w-full"
                  placeholder="Target MM"
                />
                <button
                  type="button"
                  onClick={() => setIsKeypadOpen(true)}
                  className="cnc-btn cnc-btn-secondary h-12 w-12 flex items-center justify-center shrink-0 p-0"
                  title="Open Keypad"
                >
                  <Calculator className="w-5 h-5 text-sky-400" />
                </button>
                <button
                  onClick={handlePrincherGo}
                  className="cnc-btn cnc-btn-primary whitespace-nowrap h-12 px-5 text-xs font-black shrink-0"
                >
                  GO TO POS
                </button>
              </div>
            </div>
          </div>
        </div>

        {/* 2. FEED AXIS (X) JOGGING & ZERO SETTING */}
        <div className="cnc-card flex flex-col justify-between">
          <div className="cnc-card-header">
            <span className="flex items-center gap-2">
              <ArrowRight className="w-4 h-4 text-sky-400" />
              <span>Carriage Feed Axis (X) Manual Jog</span>
            </span>
            <span className="text-[10px] font-mono text-slate-400">IS620N SERVO</span>
          </div>

          <div className="cnc-card-body space-y-4">
            <div className="space-y-2">
              <label className="text-[11px] font-black text-slate-400 tracking-wider uppercase block">
                Jog Increment Step (mm)
              </label>
              <div className="grid grid-cols-5 gap-1.5 text-xs font-black">
                {[0.1, 1.0, 10.0, 50.0, 100.0].map((step) => (
                  <button
                    key={step}
                    onClick={() => setStepIncrement(step)}
                    className={`h-10 rounded-xl border transition-all active:scale-95 ${
                      stepIncrement === step
                        ? 'bg-sky-600 text-white border-sky-400 shadow-md shadow-sky-950/50'
                        : 'bg-[#121926] text-slate-300 border-[#222e42] hover:bg-[#1a2436]'
                    }`}
                  >
                    {step}
                  </button>
                ))}
              </div>
            </div>

            <div className="grid grid-cols-2 gap-3 pt-1">
              <button
                onClick={() => handleStepJog('REV')}
                className="cnc-btn cnc-btn-primary justify-center h-12 text-xs font-black"
              >
                <ArrowLeft className="w-4 h-4" />
                <span>STEP -{stepIncrement}mm</span>
              </button>

              <button
                onClick={() => handleStepJog('FWD')}
                className="cnc-btn cnc-btn-primary justify-center h-12 text-xs font-black"
              >
                <span>STEP +{stepIncrement}mm</span>
                <ArrowRight className="w-4 h-4" />
              </button>
            </div>

            <button
              onClick={() => {
                batchUpdateTags({ feedPositionMm: 0 });
                wsClient.writeTag('Carriage_Zero_Set', true, 'Boolean');
              }}
              className="cnc-btn cnc-btn-secondary w-full justify-center text-xs font-black h-11"
            >
              <RotateCcw className="w-4 h-4 text-amber-400" />
              <span>SET CARRIAGE REFERENCE ZERO (0.00 mm)</span>
            </button>
          </div>
        </div>

        {/* 3. CLAMPS & SINGLE STROKE TEST */}
        <div className="cnc-card flex flex-col justify-between">
          <div className="cnc-card-header">
            <span className="flex items-center gap-2">
              <Lock className="w-4 h-4 text-amber-400" />
              <span>Clamping & Single Tool Firing</span>
            </span>
            <span className="text-[10px] font-mono text-slate-400">STATION TEST</span>
          </div>

          <div className="cnc-card-body space-y-4">
            {/* Clamps */}
            <div className="space-y-2 text-xs">
              {[
                { id: 'infeed' as const, label: 'Infeed Conveyor Clamp', state: infeedClamp },
                { id: 'carriage' as const, label: 'Carriage Gripper Jaw', state: carriageClamp },
                { id: 'outfeed' as const, label: 'Outfeed Discharge Clamp', state: outfeedClamp },
              ].map((c) => (
                <div key={c.id} className="flex items-center justify-between p-2.5 rounded-xl bg-[#090d14] border border-[#1e2a3c]">
                  <div>
                    <div className="font-black text-slate-200">{c.label}</div>
                    <div className="text-[10px] font-mono text-slate-400">{c.state ? 'CLAMPED' : 'UNCLAMPED'}</div>
                  </div>
                  <button
                    onClick={() => handleToggleClamp(c.id)}
                    className={`h-9 px-3.5 rounded-lg font-black text-xs flex items-center gap-1.5 transition-all border ${
                      c.state
                        ? 'bg-emerald-600 text-white border-emerald-400 shadow-sm'
                        : 'bg-[#161f2e] text-slate-300 border-[#2b3a4f] hover:bg-[#1e2a3d]'
                    }`}
                  >
                    {c.state ? <Lock className="w-3.5 h-3.5" /> : <Unlock className="w-3.5 h-3.5" />}
                    <span>{c.state ? 'CLAMPED' : 'UNCLAMP'}</span>
                  </button>
                </div>
              ))}
            </div>

            {/* 6-Head Single Stroke Test Matrix */}
            <div>
              <label className="text-[11px] font-black text-slate-400 tracking-wider uppercase block mb-2">
                Single Tool Stroke Test
              </label>
              <div className="grid grid-cols-4 gap-2 text-xs">
                {['DA1', 'DA2', 'DA3', 'DB1', 'DB2', 'DB3', 'Marking', 'Cutter'].map((head) => {
                  const isFiring = headsFiring[head];
                  return (
                    <button
                      key={head}
                      onClick={() => handleTestHead(head)}
                      disabled={!hydraulicPumpRunning}
                      className={`h-12 rounded-xl border flex flex-col items-center justify-center font-black transition-all active:scale-95 ${
                        isFiring
                          ? 'bg-rose-600 text-white border-rose-400 shadow-[0_0_12px_#f43f5e] scale-105'
                          : 'bg-[#121926] text-white border-[#222e42] hover:border-cyan-400'
                      } disabled:opacity-40 disabled:cursor-not-allowed`}
                    >
                      <span className="text-xs font-mono font-black">{head}</span>
                      <span className="text-[9px] text-slate-400 uppercase">{isFiring ? 'FIRING' : 'TEST'}</span>
                    </button>
                  );
                })}
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Keypad Modal */}
      <VirtualKeypadModal
        isOpen={isKeypadOpen}
        title="Enter Princher Target (MM)"
        initialValue={princherGoTarget}
        onClose={() => setIsKeypadOpen(false)}
        onSubmit={(v) => setPrincherGoTarget(v)}
      />
    </div>
  );
};
