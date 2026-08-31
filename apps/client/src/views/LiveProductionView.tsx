import React, { useEffect, useState } from 'react';
import { ItemRecipe } from '@innovance-hmi/shared';
import { AngleBarVisualizer } from '../components/canvas/AngleBarVisualizer.js';
import { usePlcStore } from '../stores/usePlcStore.js';
import { wsClient } from '../services/wsClient.js';
import {
  Play,
  Pause,
  RotateCcw,
  SkipForward,
} from 'lucide-react';

export const LiveProductionView: React.FC = () => {
  const [recipes, setRecipes] = useState<ItemRecipe[]>([]);
  const [activeRecipeId, setActiveRecipeId] = useState<string>('');
  const [producedPcs] = useState<number>(0);
  const [targetPcs] = useState<number>(50);
  const [currentStepIdx, setCurrentStepIdx] = useState<number>(0);

  // PLC Store real telemetry
  const {
    isConnected,
    isSimulator,
    feedPositionMm,
    feedSpeedMPerMin,
    hydraulicPressureBar,
    mode,
    headsFiring,
    carriageClamp,
  } = usePlcStore();

  const isRunning = mode === 'AUTO';

  useEffect(() => {
    fetchLatestCycleAndRecipes();
  }, []);

  const fetchLatestCycleAndRecipes = async () => {
    try {
      const res = await fetch('/api/recipes');
      const recipeJson = await res.json();

      if (recipeJson.success && recipeJson.data.length > 0) {
        setRecipes(recipeJson.data);
        setActiveRecipeId(recipeJson.data[0].id);
      }
    } catch (err) {
      console.error('Failed to load recipes', err);
    }
  };

  const selectedRecipe = recipes.find((r) => r.id === activeRecipeId) || null;

  // Real PLC Commands
  const handleStartAuto = () => {
    wsClient.writeTag('Machine_Auto_Mode', true, 'Boolean');
    wsClient.writeTag('Machine_Cycle_Start', true, 'Boolean');
  };

  const handlePauseAuto = () => {
    wsClient.writeTag('Machine_Auto_Mode', false, 'Boolean');
    wsClient.writeTag('Machine_Cycle_Pause', true, 'Boolean');
  };

  const handleResetCycle = () => {
    wsClient.writeTag('Machine_Cycle_Reset', true, 'Boolean');
    setCurrentStepIdx(0);
  };

  const handleStepForward = () => {
    wsClient.writeTag('Machine_Single_Step_Trigger', true, 'Boolean');
    if (selectedRecipe && currentStepIdx + 1 < selectedRecipe.steps.length) {
      setCurrentStepIdx((i) => i + 1);
    }
  };

  const currentOp = selectedRecipe?.steps[currentStepIdx] || null;

  return (
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* Top Production Control Header */}
      <div className="flex flex-wrap items-center justify-between pb-2 border-b border-slate-300 gap-3">
        <div>
          <h2 className="text-lg font-bold text-slate-800 flex items-center gap-2">
            Manage Live CNC Production Line
            <span className={`px-2 py-0.5 text-xs rounded font-bold ${isConnected ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'}`}>
              {isConnected ? (isSimulator ? 'SIMULATOR ACTIVE' : 'PLC ONLINE (20Hz)') : 'PLC OFFLINE'}
            </span>
          </h2>
          <p className="text-xs text-slate-500">
            Real-time feed axis servo positioning, 6-head punch sequencing, character stamping, and hydraulic shearing telemetry.
          </p>
        </div>

        {/* Master Cycle Controls */}
        <div className="flex items-center gap-2">
          {!isRunning ? (
            <button
              onClick={handleStartAuto}
              className="btn-ca btn-ca-success text-xs py-2 px-4 font-bold shadow-md flex items-center gap-1.5"
            >
              <Play className="w-4 h-4" /> Start Auto Cycle
            </button>
          ) : (
            <button
              onClick={handlePauseAuto}
              className="btn-ca btn-ca-danger text-xs py-2 px-4 font-bold shadow-md flex items-center gap-1.5"
            >
              <Pause className="w-4 h-4" /> Pause Auto Cycle
            </button>
          )}

          <button
            onClick={handleStepForward}
            className="btn-ca btn-ca-primary text-xs py-2 px-3 font-semibold"
          >
            <SkipForward className="w-3.5 h-3.5" /> Step Next
          </button>

          <button
            onClick={handleResetCycle}
            className="btn-ca btn-ca-dark text-xs py-2 px-3"
          >
            <RotateCcw className="w-3.5 h-3.5" /> Reset
          </button>
        </div>
      </div>

      {/* Live Digital Readout (DRO) Grid */}
      <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div className="p-3 bg-slate-900 text-white rounded-lg border border-slate-700 shadow-sm">
          <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Feed Position (X)</div>
          <div className="font-mono text-xl font-black text-cyan-400 mt-1">{feedPositionMm.toFixed(2)} mm</div>
          <div className="text-[10px] text-slate-400">Target: {currentOp ? currentOp.xPosition.toFixed(2) : '0.00'} mm</div>
        </div>

        <div className="p-3 bg-slate-900 text-white rounded-lg border border-slate-700 shadow-sm">
          <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Feed Speed</div>
          <div className="font-mono text-xl font-black text-emerald-400 mt-1">{feedSpeedMPerMin.toFixed(1)} m/min</div>
          <div className="text-[10px] text-slate-400">IS620N Servo Velocity</div>
        </div>

        <div className="p-3 bg-slate-900 text-white rounded-lg border border-slate-700 shadow-sm">
          <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Hydraulic Pressure</div>
          <div className="font-mono text-xl font-black text-amber-400 mt-1">{hydraulicPressureBar.toFixed(1)} Bar</div>
          <div className="text-[10px] text-slate-400">Target: 145.0 Bar</div>
        </div>

        <div className="p-3 bg-slate-900 text-white rounded-lg border border-slate-700 shadow-sm">
          <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Active Step</div>
          <div className="font-mono text-xl font-black text-purple-400 mt-1">
            #{selectedRecipe?.steps.length ? currentStepIdx + 1 : 0} / {selectedRecipe?.steps.length || 0}
          </div>
          <div className="text-[10px] text-slate-400">{currentOp?.operationType || 'IDLE'}</div>
        </div>

        <div className="p-3 bg-slate-900 text-white rounded-lg border border-slate-700 shadow-sm">
          <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Produced Pcs</div>
          <div className="font-mono text-xl font-black text-green-400 mt-1">{producedPcs} / {targetPcs}</div>
          <div className="text-[10px] text-slate-400">Shift Target</div>
        </div>

        <div className="p-3 bg-slate-900 text-white rounded-lg border border-slate-700 shadow-sm">
          <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Gripper Clamp</div>
          <div className={`font-mono text-lg font-black mt-1 ${carriageClamp ? 'text-emerald-400' : 'text-slate-400'}`}>
            {carriageClamp ? 'CLAMPED' : 'UNCLAMPED'}
          </div>
          <div className="text-[10px] text-slate-400">Cylinder Y10</div>
        </div>
      </div>

      {/* Active Recipe Selector Bar */}
      <div className="bg-white p-3 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between text-xs">
        <div className="flex items-center gap-3">
          <span className="font-bold text-slate-700">Loaded Recipe:</span>
          <select
            value={activeRecipeId}
            onChange={(e) => {
              setActiveRecipeId(e.target.value);
              setCurrentStepIdx(0);
            }}
            className="form-control-ca font-bold text-blue-900 py-1"
          >
            {recipes.map((r) => (
              <option key={r.id} value={r.id}>
                {r.itemCode} — L{r.angleWidthA}x{r.angleWidthB}x{r.thickness} mm (Length: {r.totalLength}mm)
              </option>
            ))}
          </select>
        </div>

        {selectedRecipe && (
          <div className="flex items-center gap-4 text-slate-600 font-semibold">
            <span>Profile: <b>L{selectedRecipe.angleWidthA}x{selectedRecipe.angleWidthB}x{selectedRecipe.thickness}</b></span>
            <span>Length: <b>{selectedRecipe.totalLength} mm</b></span>
            <span>Total Steps: <b>{selectedRecipe.steps.length} Ops</b></span>
          </div>
        )}
      </div>

      {/* 2D Angle Bar Visual Blueprint */}
      <div className="h-[340px] rounded-lg overflow-hidden border border-slate-300 shadow-sm">
        <AngleBarVisualizer
          recipe={selectedRecipe}
          activeFeedPosition={feedPositionMm}
          highlightStepIndex={currentStepIdx}
          onSelectStep={(idx) => setCurrentStepIdx(idx)}
        />
      </div>

      {/* 6-Head Punching Station Live Status Matrix */}
      <div className="panel">
        <div className="panel-heading">
          <span>6-Head Punching & Cutting Station Real-time Hardware Telemetry</span>
          <span className="text-xs text-slate-300">Innovance IS620N / H3U Coils M110–M121</span>
        </div>

        <div className="panel-body">
          <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3">
            {/* Flange A Heads */}
            {['DA1', 'DA2', 'DA3'].map((head, i) => {
              const isFiring = Boolean(headsFiring[head]);
              return (
                <div
                  key={head}
                  className={`p-3 rounded-lg border text-center transition-all ${
                    isFiring ? 'bg-cyan-500 text-slate-900 border-cyan-300 shadow-lg scale-105 font-bold' : 'bg-slate-50 border-slate-200'
                  }`}
                >
                  <div className="text-[10px] font-bold uppercase text-slate-500">Flange A #{i + 1}</div>
                  <div className="font-mono text-base font-black text-slate-800">{head}</div>
                  <div className="text-[11px] font-semibold text-slate-600">Ø18 mm</div>
                  <span className={`inline-block w-2.5 h-2.5 rounded-full mt-2 ${isFiring ? 'bg-white animate-ping' : 'bg-slate-300'}`} />
                </div>
              );
            })}

            {/* Flange B Heads */}
            {['DB1', 'DB2', 'DB3'].map((head, i) => {
              const isFiring = Boolean(headsFiring[head]);
              return (
                <div
                  key={head}
                  className={`p-3 rounded-lg border text-center transition-all ${
                    isFiring ? 'bg-emerald-500 text-slate-900 border-emerald-300 shadow-lg scale-105 font-bold' : 'bg-slate-50 border-slate-200'
                  }`}
                >
                  <div className="text-[10px] font-bold uppercase text-slate-500">Flange B #{i + 1}</div>
                  <div className="font-mono text-base font-black text-slate-800">{head}</div>
                  <div className="text-[11px] font-semibold text-slate-600">Ø18 mm</div>
                  <span className={`inline-block w-2.5 h-2.5 rounded-full mt-2 ${isFiring ? 'bg-white animate-ping' : 'bg-slate-300'}`} />
                </div>
              );
            })}

            {/* Marking Unit */}
            <div
              className={`p-3 rounded-lg border text-center transition-all ${
                headsFiring['Marking'] ? 'bg-amber-500 text-slate-900 border-amber-300 shadow-lg scale-105 font-bold' : 'bg-slate-50 border-slate-200'
              }`}
            >
              <div className="text-[10px] font-bold uppercase text-slate-500">Stamping</div>
              <div className="font-mono text-base font-black text-slate-800">MARK</div>
              <div className="text-[11px] font-semibold text-slate-600">8 Chars</div>
              <span className={`inline-block w-2.5 h-2.5 rounded-full mt-2 ${headsFiring['Marking'] ? 'bg-white animate-ping' : 'bg-slate-300'}`} />
            </div>

            {/* Shear Unit */}
            <div
              className={`p-3 rounded-lg border text-center transition-all ${
                headsFiring['Cutter'] ? 'bg-red-500 text-white border-red-300 shadow-lg scale-105 font-bold' : 'bg-slate-50 border-slate-200'
              }`}
            >
              <div className="text-[10px] font-bold uppercase text-slate-500">Hydraulic Cut</div>
              <div className="font-mono text-base font-black text-slate-800">SHEAR</div>
              <div className="text-[11px] font-semibold text-slate-600">Single Cut</div>
              <span className={`inline-block w-2.5 h-2.5 rounded-full mt-2 ${headsFiring['Cutter'] ? 'bg-white animate-ping' : 'bg-slate-300'}`} />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
