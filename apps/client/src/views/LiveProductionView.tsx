import React, { useEffect, useState } from 'react';
import { ItemRecipe } from '@innovance-hmi/shared';
import { AngleBarViewer } from '../components/canvas/AngleBarViewer.js';
import { usePlcStore } from '../stores/usePlcStore.js';
import { wsClient } from '../services/wsClient.js';
import {
  Play,
  Pause,
  RotateCcw,
  SkipForward,
} from 'lucide-react';

// Production Fallback Recipes (Guarantees visualizer and recipes are always populated)
const FALLBACK_RECIPES: ItemRecipe[] = [
  {
    id: 'fb-recipe-1',
    itemCode: 'ISA-100x100x10-BRACE-L1',
    itemName: 'Transmission Tower Diagonal Cross-Brace L1',
    description: 'High-tensile 3200mm angle bar with 6-head staggered punch pattern, automated part stamping, and cut-off.',
    angleWidthA: 100.0,
    angleWidthB: 100.0,
    thickness: 10.0,
    totalLength: 3200.0,
    measurementType: 'ABSOLUTE',
    isActive: true,
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString(),
    steps: [
      { id: 's1', stepNumber: 1, operationType: 'PUNCH', side: 'A', xPosition: 120.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
      { id: 's2', stepNumber: 2, operationType: 'PUNCH', side: 'A', xPosition: 280.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
      { id: 's3', stepNumber: 3, operationType: 'PUNCH', side: 'B', xPosition: 120.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
      { id: 's4', stepNumber: 4, operationType: 'PUNCH', side: 'B', xPosition: 280.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
      { id: 's5', stepNumber: 5, operationType: 'MARK', side: 'NA', xPosition: 550.0, yPosition: 0.0, markingText: 'TWR-A101' },
      { id: 's6', stepNumber: 6, operationType: 'PUNCH', side: 'A', xPosition: 1600.0, yPosition: 50.0, toolSize: 22.0, toolShape: 'ROUND' },
      { id: 's7', stepNumber: 7, operationType: 'PUNCH', side: 'B', xPosition: 1600.0, yPosition: 50.0, toolSize: 22.0, toolShape: 'ROUND' },
      { id: 's8', stepNumber: 8, operationType: 'PUNCH', side: 'A', xPosition: 2920.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
      { id: 's9', stepNumber: 9, operationType: 'PUNCH', side: 'A', xPosition: 3080.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
      { id: 's10', stepNumber: 10, operationType: 'PUNCH', side: 'B', xPosition: 2920.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
      { id: 's11', stepNumber: 11, operationType: 'PUNCH', side: 'B', xPosition: 3080.0, yPosition: 45.0, toolSize: 18.0, toolShape: 'ROUND' },
      { id: 's12', stepNumber: 12, operationType: 'CUT', side: 'NA', xPosition: 3200.0, yPosition: 0.0, isCutOff: true },
    ],
  },
  {
    id: 'fb-recipe-2',
    itemCode: 'ISA-75x75x6-SOLAR-S2',
    itemName: 'Solar Tracker Structure Leg Strut S2',
    description: 'Galvanized 2400mm angle leg strut with slotted & round mounting holes and part stamping.',
    angleWidthA: 75.0,
    angleWidthB: 75.0,
    thickness: 6.0,
    totalLength: 2400.0,
    measurementType: 'ABSOLUTE',
    isActive: true,
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString(),
    steps: [
      { id: 's1', stepNumber: 1, operationType: 'PUNCH', side: 'A', xPosition: 100.0, yPosition: 35.0, toolSize: 14.0, toolShape: 'ROUND' },
      { id: 's2', stepNumber: 2, operationType: 'PUNCH', side: 'A', xPosition: 250.0, yPosition: 35.0, toolSize: 14.0, toolShape: 'ROUND' },
      { id: 's3', stepNumber: 3, operationType: 'PUNCH', side: 'B', xPosition: 100.0, yPosition: 35.0, toolSize: 14.0, toolShape: 'ROUND' },
      { id: 's4', stepNumber: 4, operationType: 'MARK', side: 'NA', xPosition: 400.0, yPosition: 0.0, markingText: 'SOL-P99' },
      { id: 's5', stepNumber: 5, operationType: 'PUNCH', side: 'A', xPosition: 1200.0, yPosition: 35.0, toolSize: 14.0, toolShape: 'ROUND' },
      { id: 's6', stepNumber: 6, operationType: 'PUNCH', side: 'B', xPosition: 1200.0, yPosition: 35.0, toolSize: 14.0, toolShape: 'ROUND' },
      { id: 's7', stepNumber: 7, operationType: 'PUNCH', side: 'A', xPosition: 2280.0, yPosition: 35.0, toolSize: 14.0, toolShape: 'ROUND' },
      { id: 's8', stepNumber: 8, operationType: 'CUT', side: 'NA', xPosition: 2400.0, yPosition: 0.0, isCutOff: true },
    ],
  },
  {
    id: 'fb-recipe-3',
    itemCode: 'ISA-130x130x12-SUB-M1',
    itemName: 'High-Voltage Substation Gantry Column M1',
    description: 'Heavy 4500mm structural angle with dual-gauge Ø22 and Ø26 punch patterns.',
    angleWidthA: 130.0,
    angleWidthB: 130.0,
    thickness: 12.0,
    totalLength: 4500.0,
    measurementType: 'ABSOLUTE',
    isActive: true,
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString(),
    steps: [
      { id: 's1', stepNumber: 1, operationType: 'PUNCH', side: 'A', xPosition: 150.0, yPosition: 45.0, toolSize: 22.0, toolShape: 'ROUND' },
      { id: 's2', stepNumber: 2, operationType: 'PUNCH', side: 'A', xPosition: 150.0, yPosition: 85.0, toolSize: 22.0, toolShape: 'ROUND' },
      { id: 's3', stepNumber: 3, operationType: 'PUNCH', side: 'B', xPosition: 150.0, yPosition: 45.0, toolSize: 22.0, toolShape: 'ROUND' },
      { id: 's4', stepNumber: 4, operationType: 'PUNCH', side: 'B', xPosition: 150.0, yPosition: 85.0, toolSize: 22.0, toolShape: 'ROUND' },
      { id: 's5', stepNumber: 5, operationType: 'MARK', side: 'NA', xPosition: 700.0, yPosition: 0.0, markingText: 'SUB-M01' },
      { id: 's6', stepNumber: 6, operationType: 'PUNCH', side: 'A', xPosition: 2250.0, yPosition: 65.0, toolSize: 26.0, toolShape: 'ROUND' },
      { id: 's7', stepNumber: 7, operationType: 'PUNCH', side: 'B', xPosition: 2250.0, yPosition: 65.0, toolSize: 26.0, toolShape: 'ROUND' },
      { id: 's8', stepNumber: 8, operationType: 'PUNCH', side: 'A', xPosition: 4350.0, yPosition: 45.0, toolSize: 22.0, toolShape: 'ROUND' },
      { id: 's9', stepNumber: 9, operationType: 'PUNCH', side: 'A', xPosition: 4350.0, yPosition: 85.0, toolSize: 22.0, toolShape: 'ROUND' },
      { id: 's10', stepNumber: 10, operationType: 'PUNCH', side: 'B', xPosition: 4350.0, yPosition: 45.0, toolSize: 22.0, toolShape: 'ROUND' },
      { id: 's11', stepNumber: 11, operationType: 'PUNCH', side: 'B', xPosition: 4350.0, yPosition: 85.0, toolSize: 22.0, toolShape: 'ROUND' },
      { id: 's12', stepNumber: 12, operationType: 'CUT', side: 'NA', xPosition: 4500.0, yPosition: 0.0, isCutOff: true },
    ],
  },
];

export const LiveProductionView: React.FC = () => {
  const [recipes, setRecipes] = useState<ItemRecipe[]>(FALLBACK_RECIPES);
  const [activeRecipeId, setActiveRecipeId] = useState<string>(FALLBACK_RECIPES[0].id);
  const [producedPcs] = useState<number>(0);
  const [targetPcs] = useState<number>(50);
  const [currentStepIdx, setCurrentStepIdx] = useState<number>(0);

  // PLC Store real telemetry
  const {
    isConnected,
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

      if (recipeJson.success && Array.isArray(recipeJson.data) && recipeJson.data.length > 0) {
        setRecipes(recipeJson.data);
        setActiveRecipeId(recipeJson.data[0].id);
      }
    } catch (err) {
      console.warn('Using local fallback recipes', err);
    }
  };

  const selectedRecipe = recipes.find((r) => r.id === activeRecipeId) || recipes[0] || null;

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
    <div className="p-4 md:p-6 space-y-4 flex-1 overflow-y-auto bg-[#070b12] text-white">
      {/* Top Production Control Header */}
      <div className="bg-[#0e1420] border border-[#1e2a3c] rounded-2xl p-4 shadow-xl flex flex-wrap items-center justify-between gap-4">
        <div>
          <div className="flex items-center gap-3">
            <h2 className="text-xl font-black text-white tracking-wide">
              Manage Live CNC Production Line
            </h2>
            <span
              className={`px-3 py-1 text-xs rounded-full font-extrabold tracking-wider border shadow-sm flex items-center gap-1.5 ${
                isConnected
                  ? 'bg-emerald-950/80 text-emerald-300 border-emerald-500 shadow-emerald-950/50'
                  : 'bg-rose-950/80 text-rose-300 border-rose-500 shadow-rose-950/50 animate-pulse'
              }`}
            >
              <span className={`w-2 h-2 rounded-full ${isConnected ? 'bg-emerald-400' : 'bg-rose-500'}`} />
              {isConnected ? 'PLC ONLINE (20Hz)' : 'PLC OFFLINE'}
            </span>
          </div>
          <p className="text-xs text-slate-400 font-medium mt-1">
            Real-time feed axis servo positioning, 6-head punch sequencing, character stamping, and hydraulic shearing telemetry.
          </p>
        </div>

        {/* Master Cycle Controls (Tactile 48px+ Touch Buttons) */}
        <div className="flex flex-wrap items-center gap-2.5">
          {!isRunning ? (
            <button
              onClick={handleStartAuto}
              className="cnc-btn cnc-btn-success text-xs font-black shadow-lg shadow-emerald-950/40"
            >
              <Play className="w-4 h-4 fill-white" />
              <span>START AUTO CYCLE</span>
            </button>
          ) : (
            <button
              onClick={handlePauseAuto}
              className="cnc-btn cnc-btn-danger text-xs font-black shadow-lg shadow-rose-950/40 animate-pulse"
            >
              <Pause className="w-4 h-4 fill-white" />
              <span>PAUSE AUTO CYCLE</span>
            </button>
          )}

          <button
            onClick={handleStepForward}
            className="cnc-btn cnc-btn-primary text-xs font-black"
          >
            <SkipForward className="w-4 h-4" />
            <span>STEP NEXT</span>
          </button>

          <button
            onClick={handleResetCycle}
            className="cnc-btn cnc-btn-secondary text-xs font-black"
          >
            <RotateCcw className="w-4 h-4" />
            <span>RESET CYCLE</span>
          </button>
        </div>
      </div>

      {/* Live High-Contrast Digital Readout (DRO) Grid */}
      <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div className="cnc-dro flex flex-col justify-between p-3.5 rounded-xl border border-[#1e2a3c]">
          <div className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Feed Position (X)</div>
          <div className="cnc-dro-val text-2xl font-black text-cyan-300 mt-1">{feedPositionMm.toFixed(2)} mm</div>
          <div className="text-[11px] text-slate-400 font-medium">Target: {currentOp ? currentOp.xPosition.toFixed(2) : '0.00'} mm</div>
        </div>

        <div className="cnc-dro flex flex-col justify-between p-3.5 rounded-xl border border-[#1e2a3c]">
          <div className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Feed Speed</div>
          <div className="cnc-dro-val text-2xl font-black text-emerald-400 mt-1">{feedSpeedMPerMin.toFixed(1)} m/min</div>
          <div className="text-[11px] text-slate-400 font-medium">IS620N Servo Velocity</div>
        </div>

        <div className="cnc-dro flex flex-col justify-between p-3.5 rounded-xl border border-[#1e2a3c]">
          <div className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Hydraulic Pressure</div>
          <div className="cnc-dro-val text-2xl font-black text-amber-300 mt-1">{hydraulicPressureBar.toFixed(1)} Bar</div>
          <div className="text-[11px] text-slate-400 font-medium">Target: 145.0 Bar</div>
        </div>

        <div className="cnc-dro flex flex-col justify-between p-3.5 rounded-xl border border-[#1e2a3c]">
          <div className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active Step</div>
          <div className="cnc-dro-val text-2xl font-black text-purple-300 mt-1">
            #{selectedRecipe?.steps.length ? currentStepIdx + 1 : 0} / {selectedRecipe?.steps.length || 0}
          </div>
          <div className="text-[11px] text-slate-400 font-medium">{currentOp?.operationType || 'IDLE'}</div>
        </div>

        <div className="cnc-dro flex flex-col justify-between p-3.5 rounded-xl border border-[#1e2a3c]">
          <div className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Produced Pcs</div>
          <div className="cnc-dro-val text-2xl font-black text-green-300 mt-1">{producedPcs} / {targetPcs}</div>
          <div className="text-[11px] text-slate-400 font-medium">Shift Target: {targetPcs} pcs</div>
        </div>

        <div className="cnc-dro flex flex-col justify-between p-3.5 rounded-xl border border-[#1e2a3c]">
          <div className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Gripper Clamp</div>
          <div className={`cnc-dro-val text-xl font-black mt-1 ${carriageClamp ? 'text-emerald-400' : 'text-slate-400'}`}>
            {carriageClamp ? 'CLAMPED' : 'UNCLAMPED'}
          </div>
          <div className="text-[11px] text-slate-400 font-medium">Cylinder Valve Y10</div>
        </div>
      </div>

      {/* Active Recipe Selector Bar (High-Visibility Professional Slate) */}
      <div className="bg-[#0e1420] p-3 rounded-2xl border border-[#1e2a3c] shadow-lg flex flex-wrap items-center justify-between text-xs gap-3">
        <div className="flex items-center gap-3">
          <span className="font-black text-white text-xs uppercase tracking-wider pl-1">Loaded Recipe:</span>
          <select
            value={activeRecipeId}
            onChange={(e) => {
              setActiveRecipeId(e.target.value);
              setCurrentStepIdx(0);
            }}
            className="bg-[#06090e] text-white border border-[#2b3a4f] rounded-xl px-4 py-2.5 font-bold focus:outline-none focus:border-cyan-400 cursor-pointer min-w-[320px] shadow-inner"
          >
            {recipes.map((r) => (
              <option key={r.id} value={r.id} className="bg-[#0b1018] text-white py-1">
                {r.itemCode} — L{r.angleWidthA}x{r.angleWidthB}x{r.thickness} mm (Length: {r.totalLength}mm)
              </option>
            ))}
          </select>
        </div>

        {selectedRecipe && (
          <div className="flex flex-wrap items-center gap-2">
            <span className="bg-[#06090e] px-3.5 py-2 rounded-xl border border-[#1e293b] text-slate-300 font-medium">
              Profile: <b className="text-cyan-300 font-black">L{selectedRecipe.angleWidthA}×{selectedRecipe.angleWidthB}×{selectedRecipe.thickness} mm</b>
            </span>
            <span className="bg-[#06090e] px-3.5 py-2 rounded-xl border border-[#1e293b] text-slate-300 font-medium">
              Length: <b className="text-emerald-300 font-black">{selectedRecipe.totalLength} mm</b>
            </span>
            <span className="bg-[#06090e] px-3.5 py-2 rounded-xl border border-[#1e293b] text-slate-300 font-medium">
              Total Steps: <b className="text-amber-300 font-black">{selectedRecipe.steps.length} Ops</b>
            </span>
          </div>
        )}
      </div>

      {/* Unified 2D/3D Interactive Visualizer */}
      <div className="h-[520px] rounded-2xl overflow-hidden border border-[#1e2a3c] shadow-2xl bg-[#090d14]">
        <AngleBarViewer
          recipe={selectedRecipe}
          activeFeedPosition={feedPositionMm}
          highlightStepIndex={currentStepIdx}
          onSelectStep={(idx) => setCurrentStepIdx(idx)}
        />
      </div>

      {/* 6-Head Punching Station Live Status Matrix */}
      <div className="bg-[#0e1420] rounded-2xl border border-[#1e2a3c] shadow-xl overflow-hidden">
        <div className="bg-[#131b2a] text-white px-5 py-3.5 font-black text-xs flex items-center justify-between border-b border-[#1f2b3e]">
          <span className="flex items-center gap-2.5">
            <span className="w-2.5 h-2.5 rounded-full bg-cyan-400 shadow-[0_0_8px_#38bdf8] inline-block" />
            <span>6-HEAD PUNCHING & CUTTING STATION REAL-TIME HARDWARE TELEMETRY</span>
          </span>
          <span className="text-xs text-slate-400 font-mono">Innovance IS620N / H3U Coils M110–M121</span>
        </div>

        <div className="p-4">
          <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3">
            {/* Flange A Heads */}
            {['DA1', 'DA2', 'DA3'].map((head, i) => {
              const isFiring = Boolean(headsFiring[head]);
              return (
                <div
                  key={head}
                  className={`p-3.5 rounded-xl border text-center transition-all ${
                    isFiring
                      ? 'bg-gradient-to-b from-cyan-500 to-cyan-600 text-slate-950 border-2 border-white shadow-[0_0_20px_rgba(6,182,212,0.6)] scale-105 font-black'
                      : 'bg-[#080d15] border-[#1b2738] text-white hover:border-cyan-500/50'
                  }`}
                >
                  <div className={`text-[10px] font-black uppercase tracking-wider ${isFiring ? 'text-slate-950' : 'text-cyan-400'}`}>
                    Flange A #{i + 1}
                  </div>
                  <div className="font-mono text-xl font-black mt-1">{head}</div>
                  <div className={`text-xs font-bold mt-1 ${isFiring ? 'text-slate-900' : 'text-slate-300'}`}>Ø18 mm</div>
                  <div className="flex justify-center mt-2">
                    <span
                      className={`inline-block w-3 h-3 rounded-full border border-black/40 ${
                        isFiring ? 'bg-white animate-ping' : 'bg-slate-700'
                      }`}
                    />
                  </div>
                </div>
              );
            })}

            {/* Flange B Heads */}
            {['DB1', 'DB2', 'DB3'].map((head, i) => {
              const isFiring = Boolean(headsFiring[head]);
              return (
                <div
                  key={head}
                  className={`p-3.5 rounded-xl border text-center transition-all ${
                    isFiring
                      ? 'bg-gradient-to-b from-emerald-500 to-emerald-600 text-slate-950 border-2 border-white shadow-[0_0_20px_rgba(16,185,129,0.6)] scale-105 font-black'
                      : 'bg-[#080d15] border-[#1b2738] text-white hover:border-emerald-500/50'
                  }`}
                >
                  <div className={`text-[10px] font-black uppercase tracking-wider ${isFiring ? 'text-slate-950' : 'text-emerald-400'}`}>
                    Flange B #{i + 1}
                  </div>
                  <div className="font-mono text-xl font-black mt-1">{head}</div>
                  <div className={`text-xs font-bold mt-1 ${isFiring ? 'text-slate-900' : 'text-slate-300'}`}>Ø18 mm</div>
                  <div className="flex justify-center mt-2">
                    <span
                      className={`inline-block w-3 h-3 rounded-full border border-black/40 ${
                        isFiring ? 'bg-white animate-ping' : 'bg-slate-700'
                      }`}
                    />
                  </div>
                </div>
              );
            })}

            {/* Marking Unit */}
            {(() => {
              const isFiring = Boolean(headsFiring['Marking']);
              return (
                <div
                  className={`p-3.5 rounded-xl border text-center transition-all ${
                    isFiring
                      ? 'bg-gradient-to-b from-amber-500 to-amber-600 text-slate-950 border-2 border-white shadow-[0_0_20px_rgba(245,158,11,0.6)] scale-105 font-black'
                      : 'bg-[#080d15] border-[#1b2738] text-white hover:border-amber-500/50'
                  }`}
                >
                  <div className={`text-[10px] font-black uppercase tracking-wider ${isFiring ? 'text-slate-950' : 'text-amber-400'}`}>
                    Stamping
                  </div>
                  <div className="font-mono text-xl font-black mt-1">MARK</div>
                  <div className={`text-xs font-bold mt-1 ${isFiring ? 'text-slate-900' : 'text-slate-300'}`}>8 Chars</div>
                  <div className="flex justify-center mt-2">
                    <span
                      className={`inline-block w-3 h-3 rounded-full border border-black/40 ${
                        isFiring ? 'bg-white animate-ping' : 'bg-slate-700'
                      }`}
                    />
                  </div>
                </div>
              );
            })()}

            {/* Shear Unit */}
            {(() => {
              const isFiring = Boolean(headsFiring['Cutter']);
              return (
                <div
                  className={`p-3.5 rounded-xl border text-center transition-all ${
                    isFiring
                      ? 'bg-gradient-to-b from-rose-500 to-rose-600 text-white border-2 border-white shadow-[0_0_20px_rgba(244,63,94,0.6)] scale-105 font-black'
                      : 'bg-[#080d15] border-[#1b2738] text-white hover:border-rose-500/50'
                  }`}
                >
                  <div className={`text-[10px] font-black uppercase tracking-wider ${isFiring ? 'text-white' : 'text-rose-400'}`}>
                    Hydraulic Cut
                  </div>
                  <div className="font-mono text-xl font-black mt-1">SHEAR</div>
                  <div className={`text-xs font-bold mt-1 ${isFiring ? 'text-slate-200' : 'text-slate-300'}`}>Single Cut</div>
                  <div className="flex justify-center mt-2">
                    <span
                      className={`inline-block w-3 h-3 rounded-full border border-black/40 ${
                        isFiring ? 'bg-white animate-ping' : 'bg-slate-700'
                      }`}
                    />
                  </div>
                </div>
              );
            })()}
          </div>
        </div>
      </div>
    </div>
  );
};
