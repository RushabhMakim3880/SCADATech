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
  const [princherGoTarget, setPrincherGoTarget] = useState<number>(500.0);

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
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* Top Header */}
      <div className="flex items-center justify-between pb-2 border-b border-slate-300">
        <div>
          <h2 className="text-lg font-bold text-slate-800">Manual Operations Console (OpMaster / manualControl)</h2>
          <p className="text-xs text-slate-500">
            Manual carriage positioning, hydraulic motor controls, lubrication pumps, pneumatic clamps, and single stroke tooling tests.
          </p>
        </div>

        <div className="digital-dro-box">
          <span className="digital-dro-label">PRINCHER (X):</span>
          <span className="digital-dro-val">{feedPositionMm.toFixed(2)} mm</span>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        {/* 1. MOTOR OPERATIONS PANEL (from original manualControl.php) */}
        <div className="panel">
          <div className="panel-heading">
            <span>Motor Operations Console</span>
          </div>
          <div className="panel-body space-y-4">
            <div className="grid grid-cols-3 gap-2 text-center">
              {/* Main Hyd Motor */}
              <button
                onClick={handleToggleHpu}
                className={`btn-industrial ${
                  hydraulicPumpRunning
                    ? 'bg-emerald-600 text-white border-emerald-700'
                    : 'bg-slate-700 text-slate-200 border-slate-800'
                }`}
              >
                <Power className="w-4 h-4 mb-1" />
                <span>MAIN HYD<br />MOTOR</span>
                <span className={`led-indicator mt-1.5 ${hydraulicPumpRunning ? 'led-green' : 'led-off'}`} />
              </button>

              {/* Head Lub Motor */}
              <button
                onClick={handleToggleHeadLub}
                className={`btn-industrial ${
                  headLubRunning
                    ? 'bg-emerald-600 text-white border-emerald-700'
                    : 'bg-slate-700 text-slate-200 border-slate-800'
                }`}
              >
                <Droplets className="w-4 h-4 mb-1" />
                <span>HEAD LUB<br />MOTOR</span>
                <span className={`led-indicator mt-1.5 ${headLubRunning ? 'led-green' : 'led-off'}`} />
              </button>

              {/* Oil Circ Motor */}
              <button
                onClick={handleToggleOilCirc}
                className={`btn-industrial ${
                  oilCircRunning
                    ? 'bg-emerald-600 text-white border-emerald-700'
                    : 'bg-slate-700 text-slate-200 border-slate-800'
                }`}
              >
                <Activity className="w-4 h-4 mb-1" />
                <span>OIL CIRC.<br />MOTOR</span>
                <span className={`led-indicator mt-1.5 ${oilCircRunning ? 'led-green' : 'led-off'}`} />
              </button>
            </div>

            {/* Princher Go MM Row (Exact from manualControl.php) */}
            <div className="p-3 bg-slate-50 border border-slate-300 rounded space-y-2">
              <label className="text-xs font-bold text-slate-700 block">PRINCHER GO TARGET (MM)</label>
              <div className="flex items-center gap-2">
                <input
                  type="number"
                  value={princherGoTarget}
                  onChange={(e) => setPrincherGoTarget(parseFloat(e.target.value) || 0)}
                  className="form-control-ca font-mono font-bold text-sm w-full"
                  placeholder="Target MM"
                />
                <button
                  type="button"
                  onClick={() => setIsKeypadOpen(true)}
                  className="btn-ca btn-ca-default p-2"
                  title="Open Keypad"
                >
                  <Calculator className="w-4 h-4" />
                </button>
                <button
                  onClick={handlePrincherGo}
                  className="btn-ca btn-ca-primary whitespace-nowrap py-1.5 px-3"
                >
                  PRINCHER GO
                </button>
              </div>
            </div>
          </div>
        </div>

        {/* 2. FEED AXIS (X) JOGGING & ZERO SETTING */}
        <div className="panel">
          <div className="panel-heading">
            <span>Carriage Feed Axis (X) Manual Jog</span>
          </div>
          <div className="panel-body space-y-4">
            <div className="space-y-2">
              <label className="text-xs font-bold text-slate-700 block">Incremental Step (mm)</label>
              <div className="grid grid-cols-5 gap-1 text-xs font-semibold">
                {[0.1, 1.0, 10.0, 50.0, 100.0].map((step) => (
                  <button
                    key={step}
                    onClick={() => setStepIncrement(step)}
                    className={`py-1 rounded border ${
                      stepIncrement === step
                        ? 'bg-blue-600 text-white border-blue-600 font-bold'
                        : 'bg-slate-100 text-slate-700 border-slate-300'
                    }`}
                  >
                    {step}
                  </button>
                ))}
              </div>
            </div>

            <div className="grid grid-cols-2 gap-3 pt-2">
              <button
                onClick={() => handleStepJog('REV')}
                className="btn-ca btn-ca-primary justify-center py-2.5"
              >
                <ArrowLeft className="w-4 h-4" /> Step -{stepIncrement}mm
              </button>

              <button
                onClick={() => handleStepJog('FWD')}
                className="btn-ca btn-ca-primary justify-center py-2.5"
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

        {/* 3. CLAMPS & SINGLE STROKE TEST */}
        <div className="panel">
          <div className="panel-heading">
            <span>Clamping Stations & Single Tool Test</span>
          </div>
          <div className="panel-body space-y-4">
            {/* Clamps */}
            <div className="space-y-1.5 text-xs">
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

            {/* 6-Head Single Stroke Test Matrix */}
            <div>
              <label className="text-xs font-bold text-slate-700 block mb-1.5">Single Tool Test Stroke</label>
              <div className="grid grid-cols-4 gap-1 text-xs">
                {['DA1', 'DA2', 'DA3', 'DB1', 'DB2', 'DB3', 'Marking', 'Cutter'].map((head) => {
                  const isFiring = headsFiring[head];
                  return (
                    <button
                      key={head}
                      onClick={() => handleTestHead(head)}
                      disabled={!hydraulicPumpRunning}
                      className={`p-1.5 rounded border flex flex-col items-center justify-center font-bold transition-all ${
                        isFiring
                          ? 'bg-red-600 text-white border-red-700'
                          : 'bg-white text-slate-800 border-slate-300 hover:bg-slate-50'
                      } disabled:opacity-40`}
                    >
                      <span className="text-xs">{head}</span>
                      <span className="text-[9px] text-slate-500">{isFiring ? 'Firing' : 'Stroke'}</span>
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
