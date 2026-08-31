import React, { useEffect, useState } from 'react';
import { usePlcStore } from '../stores/usePlcStore.js';
import { wsClient } from '../services/wsClient.js';
import { AngleBarVisualizer } from '../components/canvas/AngleBarVisualizer.js';
import { ItemRecipe } from '@innovance-hmi/shared';
import {
  Play,
  Pause,
  Square,
  Compass,
  Gauge,
  Flame,
  CheckCircle2,
  Clock,
  Zap,
  Activity,
  Layers,
  ChevronRight,
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
          // Move towards target with smooth acceleration
          const step = Math.min(Math.abs(targetPos - currentPos), 18.0);
          const newPos = currentPos < targetPos ? currentPos + step : currentPos - step;
          batchUpdateTags({
            feedPositionMm: newPos,
            feedSpeedMPerMin: 28.5,
          });
        } else {
          // Reached target coordinate -> Fire allocated head
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
              // Completed all operations on raw bar
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
      alert('SAFETY INTERLOCK TRIP: Check E-Stop button and machine enclosure doors!');
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
  const progressPercent = Math.round((currentStepIdx / totalSteps) * 100);

  return (
    <div className="p-6 space-y-6 flex-1 overflow-y-auto">
      {/* 1. Hero Digital Readout (DRO) Cluster */}
      <div className="grid grid-cols-1 md:grid-cols-5 gap-4">
        {/* Main Feed Axis X DRO */}
        <div className="scada-panel p-4 border-cyan-500/30 relative overflow-hidden group">
          <div className="flex items-center justify-between text-xs text-slate-400 font-mono mb-2">
            <span className="flex items-center gap-2 font-bold text-slate-200">
              <Compass className="w-4 h-4 text-neon-cyan" /> CARRIAGE FEED (X)
            </span>
            <span className="text-[10px] px-2 py-0.5 rounded bg-cyan-950 text-neon-cyan border border-cyan-500/40 font-bold">
              SERVO DRO
            </span>
          </div>

          <div className="scada-dro-cyan p-3 text-center my-1">
            <span className="text-4xl font-extrabold">{feedPositionMm.toFixed(2)}</span>
            <span className="text-xs text-slate-400 ml-1 font-normal">mm</span>
          </div>

          <div className="flex items-center justify-between text-[11px] font-mono text-slate-400 mt-2">
            <span>TARGET:</span>
            <span className="text-neon-cyan font-bold">
              {activeCycle?.operations[currentStepIdx]?.requiredFeedAxisPos.toFixed(2) || '0.00'} mm
            </span>
          </div>
        </div>

        {/* Feed Velocity */}
        <div className="scada-panel p-4">
          <div className="flex items-center justify-between text-xs text-slate-400 font-mono mb-2">
            <span className="flex items-center gap-2 font-bold text-slate-200">
              <Gauge className="w-4 h-4 text-cyan-400" /> FEED SPEED
            </span>
            <span className="text-[10px] px-2 py-0.5 rounded bg-scada-800 text-slate-300 font-bold">
              INVERTER
            </span>
          </div>

          <div className="scada-dro-cyan p-3 text-center my-1">
            <span className="text-4xl font-extrabold">{Math.abs(feedSpeedMPerMin).toFixed(1)}</span>
            <span className="text-xs text-slate-400 ml-1 font-normal">m/min</span>
          </div>

          <div className="flex items-center justify-between text-[11px] font-mono text-slate-400 mt-2">
            <span>STATE:</span>
            <span className={isRunning ? 'text-neon-emerald font-bold' : 'text-slate-400'}>
              {isRunning ? 'ACCELERATING' : 'STOPPED'}
            </span>
          </div>
        </div>

        {/* Hydraulic Pressure */}
        <div className="scada-panel p-4">
          <div className="flex items-center justify-between text-xs text-slate-400 font-mono mb-2">
            <span className="flex items-center gap-2 font-bold text-slate-200">
              <Flame className="w-4 h-4 text-neon-amber" /> HYD PRESSURE
            </span>
            <span className="text-[10px] px-2 py-0.5 rounded bg-amber-950 text-neon-amber border border-amber-500/40 font-bold">
              HPU MAIN
            </span>
          </div>

          <div className="scada-dro-amber p-3 text-center my-1">
            <span className="text-4xl font-extrabold">{hydraulicPressureBar.toFixed(1)}</span>
            <span className="text-xs text-slate-400 ml-1 font-normal">bar</span>
          </div>

          <div className="flex items-center justify-between text-[11px] font-mono text-slate-400 mt-2">
            <span>PUMP MOTOR:</span>
            <span className="text-neon-amber font-bold">1450 RPM (OK)</span>
          </div>
        </div>

        {/* Cycle Timer */}
        <div className="scada-panel p-4">
          <div className="flex items-center justify-between text-xs text-slate-400 font-mono mb-2">
            <span className="flex items-center gap-2 font-bold text-slate-200">
              <Clock className="w-4 h-4 text-purple-400" /> CYCLE TIME
            </span>
            <span className="text-[10px] px-2 py-0.5 rounded bg-purple-950 text-purple-300 border border-purple-500/40 font-bold">
              TIMER
            </span>
          </div>

          <div className="scada-dro-purple p-3 text-center my-1">
            <span className="text-4xl font-extrabold">
              {Math.floor(elapsedSec / 60)}:{(elapsedSec % 60).toString().padStart(2, '0')}
            </span>
            <span className="text-xs text-slate-400 ml-1 font-normal">min</span>
          </div>

          <div className="flex items-center justify-between text-[11px] font-mono text-slate-400 mt-2">
            <span>PROGRESS:</span>
            <span className="text-purple-300 font-bold">{progressPercent}%</span>
          </div>
        </div>

        {/* Piece Output Counter */}
        <div className="scada-panel p-4">
          <div className="flex items-center justify-between text-xs text-slate-400 font-mono mb-2">
            <span className="flex items-center gap-2 font-bold text-slate-200">
              <CheckCircle2 className="w-4 h-4 text-neon-emerald" /> CUT OUTPUT
            </span>
            <span className="text-[10px] px-2 py-0.5 rounded bg-emerald-950 text-neon-emerald border border-emerald-500/40 font-bold">
              BATCH
            </span>
          </div>

          <div className="scada-dro-emerald p-3 text-center my-1">
            <span className="text-4xl font-extrabold">{producedPcs}</span>
            <span className="text-xs text-slate-400 ml-1 font-normal">Pcs</span>
          </div>

          <div className="flex items-center justify-between text-[11px] font-mono text-slate-400 mt-2">
            <span>STEP:</span>
            <span className="text-neon-emerald font-bold">
              {currentStepIdx + 1} / {totalSteps}
            </span>
          </div>
        </div>
      </div>

      {/* 2. Interactive Real-Time 2D Angle Bar Visualizer */}
      <div className="scada-panel p-5 space-y-3">
        <div className="flex items-center justify-between text-xs font-mono">
          <div className="flex items-center gap-3">
            <span className="font-extrabold text-slate-100 text-sm tracking-wider flex items-center gap-2">
              <Layers className="w-4 h-4 text-cyan-400" />
              ACTIVE BLUEPRINT: {currentRecipe?.itemCode} ({currentRecipe?.itemName})
            </span>
            <span className="px-2.5 py-1 rounded bg-scada-800 border border-scada-700 text-slate-300 text-[10px] font-bold">
              RAW BAR: {activeCycle?.stockBarLength || 6000}mm
            </span>
          </div>

          <div className="flex items-center gap-2">
            <span className="led-emerald" />
            <span className="text-neon-emerald font-bold">
              ACTION: {activeCycle?.operations[currentStepIdx]?.operationType} ON{' '}
              {activeCycle?.operations[currentStepIdx]?.allocatedHeadName}
            </span>
          </div>
        </div>

        <div className="h-56">
          <AngleBarVisualizer
            recipe={currentRecipe}
            activeFeedPosition={feedPositionMm}
          />
        </div>
      </div>

      {/* 3. CNC Tooling Matrix, Operation Queue & Master Touch Controls */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Physical 6-Head Tooling HUD */}
        <div className="scada-panel p-5 space-y-4">
          <div className="flex items-center justify-between text-xs font-mono font-bold text-slate-200 border-b border-scada-750 pb-3">
            <span className="flex items-center gap-2">
              <Zap className="w-4 h-4 text-neon-amber" /> 6-HEAD TOOLING FIRING MATRIX
            </span>
            <span className="text-[10px] text-slate-400">HYDRAULIC ACTUATORS</span>
          </div>

          <div className="grid grid-cols-4 gap-2.5 font-mono">
            {['DA1', 'DA2', 'DA3', 'DB1', 'DB2', 'DB3', 'Marking', 'Cutter'].map((h) => {
              const isFiring = headsFiring[h];
              const isAllocated = activeCycle?.operations[currentStepIdx]?.allocatedHeadName === h;

              return (
                <div
                  key={h}
                  className={`p-3 rounded-xl border text-center transition-all duration-150 relative overflow-hidden ${
                    isFiring
                      ? 'bg-rose-900 border-rose-400 text-white shadow-neon-rose scale-105'
                      : isAllocated
                      ? 'bg-cyan-950/90 border-cyan-400 text-neon-cyan shadow-neon-cyan animate-pulse'
                      : 'bg-scada-950/80 border-scada-750 text-slate-400 hover:border-slate-600'
                  }`}
                >
                  <div className="font-black text-sm tracking-wider">{h}</div>
                  <div className="text-[10px] mt-1 font-bold">
                    {isFiring ? 'STRIKING' : isAllocated ? 'TARGET' : 'READY'}
                  </div>
                  {isFiring && (
                    <span className="absolute inset-0 bg-rose-500/20 animate-ping" />
                  )}
                </div>
              );
            })}
          </div>
        </div>

        {/* Batch Operation Step Queue */}
        <div className="scada-panel p-5 flex flex-col justify-between space-y-3">
          <div className="flex items-center justify-between text-xs font-mono font-bold text-slate-200 border-b border-scada-750 pb-3">
            <span className="flex items-center gap-2">
              <Activity className="w-4 h-4 text-cyan-400" /> BATCH STEP QUEUE ({currentStepIdx + 1} / {totalSteps})
            </span>
            <span className="text-[10px] text-slate-400">MONOTONIC FEED</span>
          </div>

          <div className="space-y-2 max-h-40 overflow-y-auto pr-1">
            {activeCycle?.operations.map((op, idx: number) => {
              const isCurrent = currentStepIdx === idx;
              const isDone = currentStepIdx > idx;

              return (
                <div
                  key={op.id || idx}
                  className={`flex items-center justify-between p-2.5 rounded-xl text-xs font-mono transition-all ${
                    isCurrent
                      ? 'bg-gradient-to-r from-cyan-950 to-scada-800 border border-cyan-400/80 text-neon-cyan shadow-neon-cyan'
                      : isDone
                      ? 'bg-scada-950/40 text-slate-500 border border-transparent'
                      : 'bg-scada-950/80 text-slate-300 border border-scada-800'
                  }`}
                >
                  <div className="flex items-center gap-2.5">
                    <span className="font-extrabold text-slate-300">#{op.sequenceOrder}</span>
                    <span className="font-bold text-slate-100">{op.operationType}</span>
                    <span className="text-cyan-400 text-[10px]">({op.allocatedHeadName})</span>
                  </div>
                  <div className="flex items-center gap-1.5 font-bold text-neon-emerald">
                    <span>X: {op.requiredFeedAxisPos.toFixed(1)}mm</span>
                    {isCurrent && <ChevronRight className="w-3.5 h-3.5 animate-pulse" />}
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Master CNC Execution Station Buttons */}
        <div className="scada-panel p-5 flex flex-col justify-between space-y-4">
          <div className="text-xs font-mono font-bold text-slate-200 border-b border-scada-750 pb-3 flex items-center justify-between">
            <span>CNC MASTER CYCLE CONTROLLER</span>
            <span className={isRunning ? 'led-emerald' : 'led-amber'} />
          </div>

          <div className="grid grid-cols-2 gap-3.5 flex-1">
            {!isRunning ? (
              <button
                onClick={handleStart}
                className="scada-btn-success h-22 text-base font-black flex flex-col items-center justify-center gap-1.5"
              >
                <Play className="w-8 h-8" />
                <span>START AUTO</span>
              </button>
            ) : (
              <button
                onClick={handlePause}
                className="scada-btn-amber h-22 text-base font-black flex flex-col items-center justify-center gap-1.5"
              >
                <Pause className="w-8 h-8" />
                <span>PAUSE CYCLE</span>
              </button>
            )}

            <button
              onClick={handleStop}
              className="scada-btn-danger h-22 text-base font-black flex flex-col items-center justify-center gap-1.5"
            >
              <Square className="w-8 h-8" />
              <span>ABORT / RESET</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};
