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
          // Move towards target
          const step = Math.min(Math.abs(targetPos - currentPos), 15.0);
          const newPos = currentPos < targetPos ? currentPos + step : currentPos - step;
          batchUpdateTags({
            feedPositionMm: newPos,
            feedSpeedMPerMin: 24.0,
          });
        } else {
          // Reached target -> Fire tool head
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
              // Completed bar
              setIsRunning(false);
            }
          }, 450);
        }
      }, 100);
    }

    return () => {
      if (interval) clearInterval(interval);
    };
  }, [isRunning, activeCycle, currentStepIdx, batchUpdateTags]);

  const handleStart = () => {
    if (!eStopOk || !guardsOk) {
      alert('Cannot start: Check E-Stop and Safety Guard doors!');
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
    <div className="p-6 space-y-5 flex-1 overflow-y-auto">
      {/* Top Main Status Bar */}
      <div className="grid grid-cols-1 md:grid-cols-5 gap-4">
        {/* Feed Carriage DRO */}
        <div className="industrial-card p-4 bg-gradient-to-br from-slate-900 via-slate-900 to-emerald-950/20 border-emerald-900/60">
          <div className="flex items-center justify-between text-xs text-slate-400 font-mono mb-1">
            <span className="flex items-center gap-1.5">
              <Compass className="w-4 h-4 text-emerald-400" /> FEED AXIS (X)
            </span>
            <span className="text-emerald-400 font-bold">DRO</span>
          </div>
          <div className="text-3xl font-mono font-extrabold text-emerald-400 tracking-wider">
            {feedPositionMm.toFixed(2)}
            <span className="text-xs text-slate-400 ml-1 font-normal">mm</span>
          </div>
          <div className="text-[11px] text-slate-400 font-mono mt-1">
            TARGET: {activeCycle?.operations[currentStepIdx]?.requiredFeedAxisPos.toFixed(2) || '0.00'} mm
          </div>
        </div>

        {/* Speed */}
        <div className="industrial-card p-4">
          <div className="flex items-center justify-between text-xs text-slate-400 font-mono mb-1">
            <span className="flex items-center gap-1.5">
              <Gauge className="w-4 h-4 text-cyan-400" /> FEED SPEED
            </span>
            <span className="text-cyan-400">SERVO</span>
          </div>
          <div className="text-3xl font-mono font-extrabold text-cyan-400">
            {Math.abs(feedSpeedMPerMin).toFixed(1)}
            <span className="text-xs text-slate-400 ml-1 font-normal">m/min</span>
          </div>
          <div className="text-[11px] text-slate-400 font-mono mt-1">
            STATE: {isRunning ? 'RUNNING' : 'HOLD'}
          </div>
        </div>

        {/* Hydraulic Pressure */}
        <div className="industrial-card p-4">
          <div className="flex items-center justify-between text-xs text-slate-400 font-mono mb-1">
            <span className="flex items-center gap-1.5">
              <Flame className="w-4 h-4 text-amber-400" /> HYD PRESSURE
            </span>
            <span className="text-amber-400">HPU</span>
          </div>
          <div className="text-3xl font-mono font-extrabold text-amber-400">
            {hydraulicPressureBar.toFixed(1)}
            <span className="text-xs text-slate-400 ml-1 font-normal">bar</span>
          </div>
          <div className="text-[11px] text-slate-400 font-mono mt-1">
            PUMP: 1450 RPM
          </div>
        </div>

        {/* Cycle Timer */}
        <div className="industrial-card p-4">
          <div className="flex items-center justify-between text-xs text-slate-400 font-mono mb-1">
            <span className="flex items-center gap-1.5">
              <Clock className="w-4 h-4 text-purple-400" /> CYCLE TIME
            </span>
            <span className="text-purple-400 font-mono">LIVE</span>
          </div>
          <div className="text-3xl font-mono font-extrabold text-purple-400">
            {Math.floor(elapsedSec / 60)}:{(elapsedSec % 60).toString().padStart(2, '0')}
            <span className="text-xs text-slate-400 ml-1 font-normal">min</span>
          </div>
          <div className="text-[11px] text-slate-400 font-mono mt-1">
            PROGRESS: {progressPercent}%
          </div>
        </div>

        {/* Produced Piece Count */}
        <div className="industrial-card p-4">
          <div className="flex items-center justify-between text-xs text-slate-400 font-mono mb-1">
            <span className="flex items-center gap-1.5">
              <CheckCircle2 className="w-4 h-4 text-emerald-400" /> CUT PIECES
            </span>
            <span className="text-emerald-400">OUTPUT</span>
          </div>
          <div className="text-3xl font-mono font-extrabold text-emerald-400">
            {producedPcs}
            <span className="text-xs text-slate-400 ml-1 font-normal">Pcs</span>
          </div>
          <div className="text-[11px] text-slate-400 font-mono mt-1">
            STEP: {currentStepIdx + 1} / {totalSteps}
          </div>
        </div>
      </div>

      {/* Real-time 2D Angle Bar Visualizer with Live Feed Cursor */}
      <div className="industrial-card p-4 space-y-3">
        <div className="flex items-center justify-between text-xs font-mono">
          <span className="font-bold text-slate-200">
            ACTIVE PART: {currentRecipe?.itemCode} ({currentRecipe?.itemName}) • Stock Raw Bar: {activeCycle?.stockBarLength || 6000}mm
          </span>
          <span className="text-emerald-400 font-bold">
            CURRENT ACTION: {activeCycle?.operations[currentStepIdx]?.operationType} on {activeCycle?.operations[currentStepIdx]?.allocatedHeadName}
          </span>
        </div>

        <div className="h-52">
          <AngleBarVisualizer
            recipe={currentRecipe}
            activeFeedPosition={feedPositionMm}
          />
        </div>
      </div>

      {/* Bottom Production Console: Head Matrix + Step Queue + Controls */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Physical 6-Head Firing Indicators */}
        <div className="industrial-card p-5 space-y-3">
          <div className="text-xs font-bold text-slate-200 font-mono flex items-center justify-between">
            <span>TOOLING HEADS REAL-TIME FIRING</span>
            <Zap className="w-4 h-4 text-amber-400" />
          </div>

          <div className="grid grid-cols-4 gap-2 font-mono">
            {['DA1', 'DA2', 'DA3', 'DB1', 'DB2', 'DB3', 'Marking', 'Cutter'].map((h) => {
              const isFiring = headsFiring[h];
              const isAllocated = activeCycle?.operations[currentStepIdx]?.allocatedHeadName === h;

              return (
                <div
                  key={h}
                  className={`p-3 rounded-lg border text-center transition-all ${
                    isFiring
                      ? 'bg-rose-600 border-rose-400 text-white scale-105 shadow-lg shadow-rose-900/60'
                      : isAllocated
                      ? 'bg-cyan-950/80 border-cyan-500 text-cyan-300 animate-pulse'
                      : 'bg-slate-950/60 border-slate-800 text-slate-400'
                  }`}
                >
                  <div className="font-black text-sm">{h}</div>
                  <div className="text-[10px] mt-0.5 font-bold">
                    {isFiring ? 'PUNCHING' : isAllocated ? 'TARGET' : 'READY'}
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Step Sequence Table */}
        <div className="industrial-card p-4 flex flex-col justify-between">
          <div className="text-xs font-bold text-slate-200 font-mono mb-2">
            BATCH OPERATION QUEUE ({currentStepIdx + 1} / {totalSteps})
          </div>

          <div className="space-y-1.5 max-h-36 overflow-y-auto">
            {activeCycle?.operations.map((op, idx: number) => {
              const isCurrent = currentStepIdx === idx;
              const isDone = currentStepIdx > idx;

              return (
                <div
                  key={op.id || idx}
                  className={`flex items-center justify-between p-2 rounded text-xs font-mono transition-all ${
                    isCurrent
                      ? 'bg-cyan-950 border border-cyan-500 text-cyan-200 shadow'
                      : isDone
                      ? 'bg-slate-950/40 text-slate-500'
                      : 'bg-slate-950/80 text-slate-300'
                  }`}
                >
                  <div className="flex items-center gap-2">
                    <span className="font-bold">#{op.sequenceOrder}</span>
                    <span className="font-bold text-slate-100">{op.operationType}</span>
                    <span className="text-cyan-400">({op.allocatedHeadName})</span>
                  </div>
                  <div className="font-bold text-emerald-400">X: {op.requiredFeedAxisPos.toFixed(1)}mm</div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Main Industrial Touch Start/Stop/Pause Buttons */}
        <div className="industrial-card p-5 flex flex-col justify-between space-y-3">
          <div className="text-xs font-bold text-slate-200 font-mono">
            CNC MASTER CYCLE EXECUTION
          </div>

          <div className="grid grid-cols-2 gap-3 flex-1">
            {!isRunning ? (
              <button
                onClick={handleStart}
                className="industrial-btn-success h-20 text-lg font-black flex flex-col items-center justify-center gap-1.5 shadow-xl"
              >
                <Play className="w-7 h-7" />
                <span>START AUTO</span>
              </button>
            ) : (
              <button
                onClick={handlePause}
                className="industrial-btn-amber h-20 text-lg font-black flex flex-col items-center justify-center gap-1.5 shadow-xl"
              >
                <Pause className="w-7 h-7" />
                <span>PAUSE CYCLE</span>
              </button>
            )}

            <button
              onClick={handleStop}
              className="industrial-btn-danger h-20 text-lg font-black flex flex-col items-center justify-center gap-1.5 shadow-xl"
            >
              <Square className="w-7 h-7" />
              <span>ABORT / RESET</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};
