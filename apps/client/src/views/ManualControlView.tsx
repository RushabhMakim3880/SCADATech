import React, { useState } from 'react';
import { usePlcStore } from '../stores/usePlcStore.js';
import { wsClient } from '../services/wsClient.js';
import {
  Power,
  RotateCcw,
  Lock,
  Unlock,
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
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* Top Header */}
      <div className="flex items-center justify-between pb-2 border-b border-slate-300">
        <div>
          <h2 className="text-lg font-bold text-slate-800">Manual Operations & Jogging Console (OpMaster)</h2>
          <p className="text-xs text-slate-500">
            Manual feed carriage stepping, pneumatic/hydraulic clamp actuation, and single head test firing.
          </p>
        </div>

        <div className="flex items-center gap-2 font-mono text-xs bg-slate-800 text-white px-3 py-1.5 rounded">
          <span className="text-slate-400">CARRIAGE DRO:</span>
          <span className="font-bold text-cyan-400 text-sm">{feedPositionMm.toFixed(2)} mm</span>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        {/* 1. Carriage Jogging Panel */}
        <div className="panel">
          <div className="panel-heading">
            <span>Carriage Feed Axis (X) Manual Jog</span>
          </div>
          <div className="panel-body space-y-4">
            <div className="space-y-2">
              <label className="text-xs font-bold text-slate-700 block">Incremental Step (mm)</label>
              <div className="grid grid-cols-5 gap-1.5 text-xs font-semibold">
                {[0.1, 1.0, 10.0, 50.0, 100.0].map((step) => (
                  <button
                    key={step}
                    onClick={() => setStepIncrement(step)}
                    className={`py-1.5 rounded border ${
                      stepIncrement === step
                        ? 'bg-blue-600 text-white border-blue-600'
                        : 'bg-slate-100 text-slate-700 border-slate-300'
                    }`}
                  >
                    {step}mm
                  </button>
                ))}
              </div>
            </div>

            <div className="grid grid-cols-2 gap-3 pt-2">
              <button
                onClick={() => handleStepJog('REV')}
                className="btn-ca btn-ca-primary justify-center py-3"
              >
                <ArrowLeft className="w-4 h-4" /> Step -{stepIncrement}mm
              </button>

              <button
                onClick={() => handleStepJog('FWD')}
                className="btn-ca btn-ca-primary justify-center py-3"
              >
                <ArrowRight className="w-4 h-4" /> Step +{stepIncrement}mm
              </button>
            </div>

            <button
              onClick={() => {
                batchUpdateTags({ feedPositionMm: 0 });
                wsClient.writeTag('Carriage_Zero_Set', true, 'Boolean');
              }}
              className="btn-ca btn-ca-default w-full justify-center text-xs py-2 mt-2"
            >
              <RotateCcw className="w-3.5 h-3.5" /> Set Carriage Reference Zero (0.00mm)
            </button>
          </div>
        </div>

        {/* 2. HPU & Hydraulic Clamps Panel */}
        <div className="panel">
          <div className="panel-heading">
            <span>Hydraulic Power Unit (HPU) & Clamps</span>
          </div>
          <div className="panel-body space-y-4">
            <div className="flex items-center justify-between p-3 bg-slate-100 rounded border border-slate-300">
              <div>
                <div className="font-bold text-xs text-slate-800">Main Hydraulic Pump</div>
                <div className="text-[11px] text-slate-500">{hydraulicPressureBar.toFixed(1)} bar</div>
              </div>
              <button
                onClick={handleToggleHpu}
                className={`btn-ca ${hydraulicPumpRunning ? 'btn-ca-danger' : 'btn-ca-success'}`}
              >
                <Power className="w-3.5 h-3.5" /> {hydraulicPumpRunning ? 'Stop HPU' : 'Start HPU'}
              </button>
            </div>

            <div className="space-y-2 text-xs">
              <label className="font-bold text-slate-700 block">Pneumatic Clamps Actuation</label>
              {[
                { id: 'infeed' as const, label: 'Infeed Conveyor Clamp', state: infeedClamp },
                { id: 'carriage' as const, label: 'Carriage Gripper Jaw', state: carriageClamp },
                { id: 'outfeed' as const, label: 'Outfeed Discharge Clamp', state: outfeedClamp },
              ].map((c) => (
                <div key={c.id} className="flex items-center justify-between p-2 rounded bg-slate-50 border">
                  <div>
                    <div className="font-semibold text-slate-800">{c.label}</div>
                    <div className="text-[10px] text-slate-500">{c.state ? 'CLAMPED' : 'OPEN'}</div>
                  </div>
                  <button
                    onClick={() => handleToggleClamp(c.id)}
                    className={`btn-ca ${c.state ? 'btn-ca-success' : 'btn-ca-default'} py-1 px-3`}
                  >
                    {c.state ? <Lock className="w-3 h-3" /> : <Unlock className="w-3 h-3" />}
                    {c.state ? 'Clamped' : 'Unclamp'}
                  </button>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* 3. Single Stroke Test Console */}
        <div className="panel">
          <div className="panel-heading">
            <span>Single Stroke Tooling Test</span>
          </div>
          <div className="panel-body space-y-3">
            <div className="grid grid-cols-2 gap-2 text-xs">
              {['DA1', 'DA2', 'DA3', 'DB1', 'DB2', 'DB3', 'Marking', 'Cutter'].map((head) => {
                const isFiring = headsFiring[head];
                return (
                  <button
                    key={head}
                    onClick={() => handleTestHead(head)}
                    disabled={!hydraulicPumpRunning}
                    className={`p-2.5 rounded border flex flex-col items-center justify-center gap-1 font-semibold transition-all ${
                      isFiring
                        ? 'bg-red-600 text-white border-red-700'
                        : 'bg-white text-slate-800 border-slate-300 hover:bg-slate-50'
                    } disabled:opacity-50`}
                  >
                    <span className="font-bold">{head}</span>
                    <span className="text-[10px] text-slate-500">{isFiring ? 'Firing...' : 'Test Stroke'}</span>
                  </button>
                );
              })}
            </div>

            {!hydraulicPumpRunning && (
              <div className="p-2 bg-yellow-50 border border-yellow-300 text-yellow-800 rounded text-xs text-center">
                Please start the HPU pump before testing cylinder strokes.
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};
