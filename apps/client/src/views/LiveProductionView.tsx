import React, { useEffect, useState } from 'react';
import { usePlcStore } from '../stores/usePlcStore.js';
import { wsClient } from '../services/wsClient.js';
import { AngleBarVisualizer } from '../components/canvas/AngleBarVisualizer.js';
import { ItemRecipe } from '@innovance-hmi/shared';
import {
  Play,
  Pause,
  RotateCcw,
  Clock,
  CheckCircle2,
  Layers,
  Wrench,
  Activity,
  StepForward,
  AlertOctagon,
  ArrowRight,
} from 'lucide-react';

interface ProductionCycleEntity {
  id: string;
  cycleCode: string;
  stockBarLength: number;
  utilizedLength: number;
  scrapLength: number;
  operations: Array<{
    id?: string;
    sequenceOrder: number;
    recipeId: string;
    operationType: string;
    side: string;
    absoluteBarX: number;
    yPosition: number;
    allocatedHeadName?: string;
    requiredFeedAxisPos: number;
    isCutOff: boolean;
  }>;
}

export const LiveProductionView: React.FC = () => {
  const {
    feedPositionMm,
    feedSpeedMPerMin,
    hydraulicPressureBar,
    headsFiring,
    eStopOk,
    guardsOk,
    activeAlarms,
    batchUpdateTags,
  } = usePlcStore();

  const [activeCycle, setActiveCycle] = useState<ProductionCycleEntity | null>(null);
  const [recipes, setRecipes] = useState<ItemRecipe[]>([]);
  const [currentStepIdx, setCurrentStepIdx] = useState<number>(0);
  const [isRunning, setIsRunning] = useState<boolean>(false);
  const [elapsedSec, setElapsedSec] = useState<number>(0);
  const [producedPcs, setProducedPcs] = useState<number>(0);
  const [autoStepMode, setAutoStepMode] = useState<boolean>(false);

  useEffect(() => {
    fetchLatestCycleAndRecipes();
  }, []);

  const fetchLatestCycleAndRecipes = async () => {
    try {
      const [cycleRes, recipeRes] = await Promise.all([
        fetch('/api/production/cycles'),
        fetch('/api/recipes'),
      ]);
      const cycleJson = await cycleRes.json();
      const recipeJson = await recipeRes.json();

      if (recipeJson.success) setRecipes(recipeJson.data);
      if (cycleJson.success && cycleJson.data.length > 0) {
        setActiveCycle(cycleJson.data[0]);
      }
    } catch (err) {
      console.error('Failed to load live production cycle', err);
    }
  };

  // Live Auto Execution simulation loop
  useEffect(() => {
    let interval: NodeJS.Timeout | null = null;
    if (isRunning && activeCycle && activeCycle.operations.length > 0) {
      interval = setInterval(() => {
        setElapsedSec((s) => s + 1);

        const currentOp = activeCycle.operations[currentStepIdx];
        if (!currentOp) {
          setIsRunning(false);
          return;
        }

        const targetPos = currentOp.requiredFeedAxisPos;
        const currentPos = usePlcStore.getState().feedPositionMm;

        if (Math.abs(targetPos - currentPos) > 1.0) {
          const step = Math.min(Math.abs(targetPos - currentPos), 18.0);
          const newPos = currentPos < targetPos ? currentPos + step : currentPos - step;
          batchUpdateTags({
            feedPositionMm: newPos,
            feedSpeedMPerMin: 28.5,
          });
        } else {
          batchUpdateTags({ feedSpeedMPerMin: 0.0 });
          const head = currentOp.allocatedHeadName || 'DA1';

          batchUpdateTags({
            headsFiring: { ...usePlcStore.getState().headsFiring, [head]: true },
          });

          setTimeout(() => {
            batchUpdateTags({
              headsFiring: { ...usePlcStore.getState().headsFiring, [head]: false },
            });

            if (currentOp.isCutOff) {
              setProducedPcs((p) => p + 1);
            }

            if (currentStepIdx + 1 < activeCycle.operations.length) {
              setCurrentStepIdx((idx) => idx + 1);
              if (autoStepMode) {
                setIsRunning(false);
              }
            } else {
              setIsRunning(false);
            }
          }, 400);
        }
      }, 100);
    }

    return () => {
      if (interval) clearInterval(interval);
    };
  }, [isRunning, activeCycle, currentStepIdx, autoStepMode, batchUpdateTags]);

  const handleStart = () => {
    if (!eStopOk || !guardsOk) {
      alert('Safety interlock trip: Check Emergency Stop button and safety doors!');
      return;
    }
    setIsRunning(true);
    wsClient.writeTag('Machine_Auto_Mode', true, 'Boolean');
  };

  const handlePause = () => {
    setIsRunning(false);
    batchUpdateTags({ feedSpeedMPerMin: 0 });
  };

  const handleStop = () => {
    setIsRunning(false);
    setCurrentStepIdx(0);
    batchUpdateTags({ feedSpeedMPerMin: 0, feedPositionMm: 0 });
  };

  const handleNextStep = () => {
    if (!activeCycle || currentStepIdx >= activeCycle.operations.length - 1) return;
    const nextOp = activeCycle.operations[currentStepIdx + 1];
    setCurrentStepIdx((idx) => idx + 1);
    batchUpdateTags({ feedPositionMm: nextOp.requiredFeedAxisPos });
  };

  const currentRecipe = recipes.find(
    (r) => r.id === activeCycle?.operations[currentStepIdx]?.recipeId
  ) || recipes[0];

  const totalSteps = activeCycle?.operations.length || 1;
  const stockBarLen = activeCycle?.stockBarLength || 6000;

  const axes = [
    { name: 'PRINCHER (X)', machinePos: feedPositionMm, workPos: feedPositionMm, speed: feedSpeedMPerMin },
    { name: 'FLANGE A (Y1)', machinePos: 35.0, workPos: 35.0, speed: 0.0 },
    { name: 'FLANGE B (Y2)', machinePos: 35.0, workPos: 35.0, speed: 0.0 },
    { name: 'MARKING (Z1)', machinePos: 0.0, workPos: 0.0, speed: 0.0 },
    { name: 'CUTTER (Z2)', machinePos: 150.0, workPos: 0.0, speed: 0.0 },
  ];

  return (
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* 1. KPI Stat Widgets */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="widget-stats bg-blue">
          <div className="stats-icon"><Clock className="w-12 h-12" /></div>
          <div className="stats-info">
            <h4>Total Cycle Time</h4>
            <p>{Math.floor(elapsedSec / 60)}:{(elapsedSec % 60).toString().padStart(2, '0')} min</p>
          </div>
        </div>

        <div className="widget-stats bg-green">
          <div className="stats-icon"><Layers className="w-12 h-12" /></div>
          <div className="stats-info">
            <h4>Cut Pieces Produced</h4>
            <p>{producedPcs} Pcs</p>
          </div>
        </div>

        <div className="widget-stats bg-orange">
          <div className="stats-icon"><Wrench className="w-12 h-12" /></div>
          <div className="stats-info">
            <h4>Punches Completed</h4>
            <p>{currentStepIdx} / {totalSteps}</p>
          </div>
        </div>

        <div className="widget-stats bg-red">
          <div className="stats-icon"><Activity className="w-12 h-12" /></div>
          <div className="stats-info">
            <h4>HPU Hydraulic Pressure</h4>
            <p>{hydraulicPressureBar.toFixed(1)} bar</p>
          </div>
        </div>
      </div>

      {/* Active Alarm Bar if Triggered */}
      {activeAlarms.length > 0 && (
        <div className="p-3 rounded bg-red-600 text-white flex items-center justify-between shadow-md">
          <div className="flex items-center gap-2 text-xs font-bold">
            <AlertOctagon className="w-4 h-4 animate-bounce" />
            <span>ALARM ACTIVE: {activeAlarms[0].alarmName} ({activeAlarms[0].alarmCode})</span>
          </div>
          <button
            onClick={() => wsClient.writeTag('Alarm_Reset_Cmd', true, 'Boolean')}
            className="btn-ca btn-ca-dark text-xs py-1 px-3"
          >
            RESET ALARM
          </button>
        </div>
      )}

      {/* Raw Stock Infeed Buffer Track */}
      <div className="panel p-3">
        <div className="flex items-center justify-between mb-1.5 text-xs font-bold text-slate-700">
          <div className="flex items-center gap-2">
            <ArrowRight className="w-4 h-4 text-blue-600 animate-pulse" />
            <span>RAW STOCK INFEED TRACK & BUFFER MONITOR</span>
          </div>
          <div className="flex items-center gap-3 font-mono text-[11px]">
            <span>Raw Bar: <b className="text-slate-900">{stockBarLen} mm</b></span>
            <span>Feed Position: <b className="text-cyan-600">{feedPositionMm.toFixed(1)} mm</b></span>
            <span>Remaining: <b className="text-emerald-700">{Math.max(0, stockBarLen - feedPositionMm).toFixed(1)} mm</b></span>
          </div>
        </div>

        <div className="w-full h-5 bg-slate-800 rounded overflow-hidden relative border border-slate-700 p-0.5 flex items-center">
          <div
            style={{ width: `${Math.min(100, (feedPositionMm / stockBarLen) * 100)}%` }}
            className="h-full bg-gradient-to-r from-blue-600 to-cyan-400 rounded transition-all duration-300 flex items-center justify-end pr-1 text-[9px] font-black text-slate-900"
          >
            {((feedPositionMm / stockBarLen) * 100).toFixed(0)}%
          </div>
          <div className="absolute right-3 text-[10px] font-bold text-slate-400 font-mono">
            {stockBarLen} mm Total Stock Bar
          </div>
        </div>
      </div>

      {/* 2. 2D Angle Bar Blueprint Visualizer Panel */}
      <div className="panel">
        <div className="panel-heading">
          <span>Manage Production • 2D Angle Bar Blueprint Visualizer & Live Carriage Track</span>
          <div className="flex items-center gap-2">
            <span className="digital-dro-val text-xs text-cyan-300">
              DRO: {feedPositionMm.toFixed(2)} mm
            </span>
          </div>
        </div>

        <div className="panel-body space-y-3">
          <div className="h-[340px] rounded overflow-hidden">
            <AngleBarVisualizer
              recipe={currentRecipe}
              activeFeedPosition={feedPositionMm}
              highlightStepIndex={currentStepIdx}
            />
          </div>

          {/* Machine Control Toolbar */}
          <div className="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-200">
            <div className="flex items-center gap-2">
              {!isRunning ? (
                <button
                  onClick={handleStart}
                  className="btn-ca btn-ca-success"
                >
                  <Play className="w-4 h-4" /> Start Auto Cycle
                </button>
              ) : (
                <button
                  onClick={handlePause}
                  className="btn-ca btn-ca-warning"
                >
                  <Pause className="w-4 h-4" /> Pause Cycle
                </button>
              )}

              <button
                onClick={handleStop}
                className="btn-ca btn-ca-danger"
              >
                <RotateCcw className="w-4 h-4" /> Reset / Abort
              </button>

              <button
                onClick={handleNextStep}
                disabled={isRunning}
                className="btn-ca btn-ca-primary"
              >
                <StepForward className="w-4 h-4" /> Next Opr.
              </button>

              <label className="flex items-center gap-1.5 text-xs font-semibold text-slate-700 ml-2 cursor-pointer">
                <input
                  type="checkbox"
                  checked={autoStepMode}
                  onChange={(e) => setAutoStepMode(e.target.checked)}
                  className="rounded"
                />
                Step-by-Step Mode
              </label>
            </div>

            {/* Firing Heads Matrix */}
            <div className="flex items-center gap-1 text-xs font-bold">
              {['DA1', 'DA2', 'DA3', 'DB1', 'DB2', 'DB3', 'Marking', 'Cutter'].map((h) => {
                const isFiring = headsFiring[h];
                return (
                  <span
                    key={h}
                    className={`px-2 py-0.5 rounded text-[11px] font-semibold border ${
                      isFiring
                        ? 'bg-red-600 text-white border-red-700 animate-pulse'
                        : 'bg-slate-100 text-slate-700 border-slate-300'
                    }`}
                  >
                    {h}
                  </span>
                );
              })}
            </div>
          </div>
        </div>
      </div>

      {/* 3. Multi-Axis DRO Matrix & Operations DataTable */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {/* Multi-Axis DRO Matrix */}
        <div className="panel">
          <div className="panel-heading">
            <span>Position and Speed Matrix (DRO)</span>
          </div>
          <div className="panel-body p-0">
            <table className="table-custom">
              <thead>
                <tr>
                  <th>AXIS</th>
                  <th>MACHINE POS</th>
                  <th>WORK POS</th>
                  <th>SPEED</th>
                </tr>
              </thead>
              <tbody>
                {axes.map((ax, idx) => (
                  <tr key={idx}>
                    <td className="font-bold text-slate-800">{ax.name}</td>
                    <td className="font-mono text-cyan-700 font-bold">{ax.machinePos.toFixed(2)} mm</td>
                    <td className="font-mono text-slate-700">{ax.workPos.toFixed(2)} mm</td>
                    <td className="font-mono text-slate-500">{ax.speed.toFixed(1)} m/min</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        {/* Production Operations Sequence DataTable */}
        <div className="panel lg:col-span-2">
          <div className="panel-heading">
            <span>Production Operations Sequence DataTable</span>
            <span className="text-xs text-slate-300">Total: {totalSteps} Operations</span>
          </div>

          <div className="panel-body p-0 max-h-72 overflow-y-auto">
            <table className="table-custom">
              <thead>
                <tr>
                  <th style={{ width: '50px' }}>#</th>
                  <th>Operation</th>
                  <th>Flange Side</th>
                  <th>Bar Coordinate (AX)</th>
                  <th>Allocated Tool Head</th>
                  <th>Required Feed DRO Pos</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                {activeCycle?.operations.map((op, idx) => {
                  const isCurrent = currentStepIdx === idx;
                  const isDone = currentStepIdx > idx;

                  return (
                    <tr
                      key={op.id || idx}
                      className={isCurrent ? 'bg-blue-50 font-bold' : ''}
                    >
                      <td>#{op.sequenceOrder}</td>
                      <td>
                        <span
                          className={`px-2 py-0.5 rounded text-xs font-semibold ${
                            op.operationType === 'PUNCH'
                              ? 'bg-blue-100 text-blue-800'
                              : op.operationType === 'MARK'
                              ? 'bg-yellow-100 text-yellow-800'
                              : 'bg-red-100 text-red-800'
                          }`}
                        >
                          {op.operationType}
                        </span>
                      </td>
                      <td>{op.side}</td>
                      <td>{op.absoluteBarX} mm</td>
                      <td><span className="font-semibold text-slate-800">{op.allocatedHeadName}</span></td>
                      <td className="font-mono text-cyan-700 font-bold">{op.requiredFeedAxisPos.toFixed(2)} mm</td>
                      <td>
                        {isCurrent ? (
                          <span className="text-blue-600 font-bold flex items-center gap-1">
                            <Activity className="w-3.5 h-3.5 animate-spin" /> In Progress
                          </span>
                        ) : isDone ? (
                          <span className="text-green-600 font-semibold flex items-center gap-1">
                            <CheckCircle2 className="w-3.5 h-3.5" /> Completed
                          </span>
                        ) : (
                          <span className="text-slate-400">Pending</span>
                        )}
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
};
