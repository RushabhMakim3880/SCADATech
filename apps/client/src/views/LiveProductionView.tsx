import React, { useEffect, useState } from 'react';
import { usePlcStore } from '../stores/usePlcStore.js';
import { wsClient } from '../services/wsClient.js';
import { AngleBarVisualizer } from '../components/canvas/AngleBarVisualizer.js';
import { ItemRecipe } from '@innovance-hmi/shared';
import {
  Play,
  Pause,
  Square,
  Clock,
  CheckCircle2,
  Layers,
  Wrench,
  Activity,
  RefreshCw,
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
    hydraulicPressureBar,
    headsFiring,
    eStopOk,
    guardsOk,
    batchUpdateTags,
  } = usePlcStore();

  const [activeCycle, setActiveCycle] = useState<ProductionCycleEntity | null>(null);
  const [recipes, setRecipes] = useState<ItemRecipe[]>([]);
  const [currentStepIdx, setCurrentStepIdx] = useState<number>(0);
  const [isRunning, setIsRunning] = useState<boolean>(false);
  const [elapsedSec, setElapsedSec] = useState<number>(0);
  const [producedPcs, setProducedPcs] = useState<number>(0);

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
  }, [isRunning, activeCycle, currentStepIdx, batchUpdateTags]);

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

  const currentRecipe = recipes.find(
    (r) => r.id === activeCycle?.operations[currentStepIdx]?.recipeId
  ) || recipes[0];

  const totalSteps = activeCycle?.operations.length || 1;

  return (
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* 1. Original 4 Color Admin KPI Stat Widgets */}
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

      {/* 2. Live Machine Control & 2D Angle Bar Visualizer Panel */}
      <div className="panel">
        <div className="panel-heading">
          <span>Manage Production • Live 2D Angle Bar Simulation & Feed DRO</span>
          <div className="flex items-center gap-2">
            <span className="badge bg-primary text-xs px-2 py-0.5 rounded text-white font-bold">
              DRO: {feedPositionMm.toFixed(2)} mm
            </span>
          </div>
        </div>

        <div className="panel-body space-y-4">
          {/* Visualizer */}
          <div className="h-56 border border-slate-300 rounded overflow-hidden">
            <AngleBarVisualizer
              recipe={currentRecipe}
              activeFeedPosition={feedPositionMm}
            />
          </div>

          {/* Machine Control Buttons */}
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
                <Square className="w-4 h-4" /> Reset / Abort
              </button>

              <button
                onClick={fetchLatestCycleAndRecipes}
                className="btn-ca btn-ca-default"
              >
                <RefreshCw className="w-4 h-4" /> Reload
              </button>
            </div>

            {/* Firing Heads Status */}
            <div className="flex items-center gap-1.5 text-xs font-bold">
              {['DA1', 'DA2', 'DA3', 'DB1', 'DB2', 'DB3', 'Marking', 'Cutter'].map((h) => {
                const isFiring = headsFiring[h];
                return (
                  <span
                    key={h}
                    className={`px-2.5 py-1 rounded text-[11px] font-semibold border ${
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

      {/* 3. Manage Production DataTable */}
      <div className="panel">
        <div className="panel-heading">
          <span>Production Operations Sequence DataTable</span>
          <span className="text-xs text-slate-300">Total: {totalSteps} Operations</span>
        </div>

        <div className="panel-body p-0">
          <table className="table-custom">
            <thead>
              <tr>
                <th style={{ width: '60px' }}>#</th>
                <th>Operation Type</th>
                <th>Flange Side</th>
                <th>Bar Absolute Coord (AX)</th>
                <th>Allocated Tooling Head</th>
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
  );
};
