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
    <div className="p-4 space-y-4 flex-1 overflow-y-auto bg-[#0b1019]">
      {/* Top Production Control Header */}
      <div className="flex flex-wrap items-center justify-between pb-3 border-b border-[#223044] gap-3">
        <div>
          <div className="flex items-center gap-3">
            <h2 className="text-xl font-black text-white tracking-wide">
              Manage Live CNC Production Line
            </h2>
            <span
              className={`px-3 py-1 text-xs rounded-full font-extrabold tracking-wider border shadow-sm ${
                isConnected
                  ? 'bg-emerald-950/80 text-emerald-300 border-emerald-500'
                  : 'bg-red-950/80 text-red-300 border-red-500'
              }`}
            >
              {isConnected ? 'PLC ONLINE (20Hz)' : 'PLC OFFLINE'}
            </span>
          </div>
          <p className="text-xs text-slate-300 font-medium mt-1">
            Real-time feed axis servo positioning, 6-head punch sequencing, character stamping, and hydraulic shearing telemetry.
          </p>
        </div>

        {/* Master Cycle Controls */}
        <div className="flex items-center gap-2.5">
          {!isRunning ? (
            <button
              onClick={handleStartAuto}
              className="bg-emerald-600 hover:bg-emerald-500 text-white text-xs py-2.5 px-5 font-extrabold rounded-md shadow-md border border-emerald-400 flex items-center gap-2 transition-all active:scale-[0.98]"
            >
              <Play className="w-4 h-4" /> Start Auto Cycle
            </button>
          ) : (
            <button
              onClick={handlePauseAuto}
              className="bg-red-600 hover:bg-red-500 text-white text-xs py-2.5 px-5 font-extrabold rounded-md shadow-md border border-red-400 flex items-center gap-2 transition-all active:scale-[0.98]"
            >
              <Pause className="w-4 h-4" /> Pause Auto Cycle
            </button>
          )}

          <button
            onClick={handleStepForward}
            className="bg-blue-600 hover:bg-blue-500 text-white text-xs py-2.5 px-4 font-bold rounded-md shadow border border-blue-400 flex items-center gap-1.5 transition-all active:scale-[0.98]"
          >
            <SkipForward className="w-4 h-4" /> Step Next
          </button>

          <button
            onClick={handleResetCycle}
            className="bg-[#1e293b] hover:bg-[#334155] text-slate-200 text-xs py-2.5 px-4 font-bold rounded-md border border-[#3b4d68] flex items-center gap-1.5 transition-all active:scale-[0.98]"
          >
            <RotateCcw className="w-3.5 h-3.5" /> Reset
          </button>
        </div>
      </div>

      {/* Live High-Contrast Digital Readout (DRO) Grid */}
      <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div className="p-3 bg-[#131b27] rounded-lg border border-[#243347] shadow-md">
          <div className="text-[11px] font-extrabold text-slate-300 uppercase tracking-wider">Feed Position (X)</div>
          <div className="font-mono text-2xl font-black text-cyan-300 mt-1">{feedPositionMm.toFixed(2)} mm</div>
          <div className="text-[11px] text-slate-400 font-medium">Target: {currentOp ? currentOp.xPosition.toFixed(2) : '0.00'} mm</div>
        </div>

        <div className="p-3 bg-[#131b27] rounded-lg border border-[#243347] shadow-md">
          <div className="text-[11px] font-extrabold text-slate-300 uppercase tracking-wider">Feed Speed</div>
          <div className="font-mono text-2xl font-black text-emerald-300 mt-1">{feedSpeedMPerMin.toFixed(1)} m/min</div>
          <div className="text-[11px] text-slate-400 font-medium">IS620N Servo Velocity</div>
        </div>

        <div className="p-3 bg-[#131b27] rounded-lg border border-[#243347] shadow-md">
          <div className="text-[11px] font-extrabold text-slate-300 uppercase tracking-wider">Hydraulic Pressure</div>
          <div className="font-mono text-2xl font-black text-amber-300 mt-1">{hydraulicPressureBar.toFixed(1)} Bar</div>
          <div className="text-[11px] text-slate-400 font-medium">Target: 145.0 Bar</div>
        </div>

        <div className="p-3 bg-[#131b27] rounded-lg border border-[#243347] shadow-md">
          <div className="text-[11px] font-extrabold text-slate-300 uppercase tracking-wider">Active Step</div>
          <div className="font-mono text-2xl font-black text-purple-300 mt-1">
            #{selectedRecipe?.steps.length ? currentStepIdx + 1 : 0} / {selectedRecipe?.steps.length || 0}
          </div>
          <div className="text-[11px] text-slate-400 font-medium">{currentOp?.operationType || 'IDLE'}</div>
        </div>

        <div className="p-3 bg-[#131b27] rounded-lg border border-[#243347] shadow-md">
          <div className="text-[11px] font-extrabold text-slate-300 uppercase tracking-wider">Produced Pcs</div>
          <div className="font-mono text-2xl font-black text-green-300 mt-1">{producedPcs} / {targetPcs}</div>
          <div className="text-[11px] text-slate-400 font-medium">Shift Target</div>
        </div>

        <div className="p-3 bg-[#131b27] rounded-lg border border-[#243347] shadow-md">
          <div className="text-[11px] font-extrabold text-slate-300 uppercase tracking-wider">Gripper Clamp</div>
          <div className={`font-mono text-xl font-black mt-1 ${carriageClamp ? 'text-emerald-300' : 'text-slate-400'}`}>
            {carriageClamp ? 'CLAMPED' : 'UNCLAMPED'}
          </div>
          <div className="text-[11px] text-slate-400 font-medium">Cylinder Y10</div>
        </div>
      </div>

      {/* Active Recipe Selector Bar (High-Visibility Professional Slate) */}
      <div className="bg-[#16202e] p-3 rounded-lg border border-[#2b3c54] shadow-md flex flex-wrap items-center justify-between text-xs gap-3">
        <div className="flex items-center gap-3">
          <span className="font-extrabold text-white text-sm">Loaded Recipe:</span>
          <select
            value={activeRecipeId}
            onChange={(e) => {
              setActiveRecipeId(e.target.value);
              setCurrentStepIdx(0);
            }}
            className="bg-[#0b1018] text-white border border-[#3d516e] rounded-md px-3 py-2 font-bold focus:outline-none focus:border-cyan-400 cursor-pointer min-w-[320px]"
          >
            {recipes.map((r) => (
              <option key={r.id} value={r.id} className="bg-[#0b1018] text-white py-1">
                {r.itemCode} — L{r.angleWidthA}x{r.angleWidthB}x{r.thickness} mm (Length: {r.totalLength}mm)
              </option>
            ))}
          </select>
        </div>

        {selectedRecipe && (
          <div className="flex flex-wrap items-center gap-3">
            <span className="bg-[#0b1018] px-3 py-1.5 rounded-md border border-[#28384f] text-slate-200 font-medium">
              Profile: <b className="text-cyan-300 font-extrabold">L{selectedRecipe.angleWidthA}x{selectedRecipe.angleWidthB}x{selectedRecipe.thickness} mm</b>
            </span>
            <span className="bg-[#0b1018] px-3 py-1.5 rounded-md border border-[#28384f] text-slate-200 font-medium">
              Length: <b className="text-emerald-300 font-extrabold">{selectedRecipe.totalLength} mm</b>
            </span>
            <span className="bg-[#0b1018] px-3 py-1.5 rounded-md border border-[#28384f] text-slate-200 font-medium">
              Total Steps: <b className="text-amber-300 font-extrabold">{selectedRecipe.steps.length} Ops</b>
            </span>
          </div>
        )}
      </div>

      {/* Unified 2D/3D Interactive Visualizer */}
      <div className="h-[500px] rounded-lg overflow-hidden border border-[#25354b] shadow-lg">
        <AngleBarViewer
          recipe={selectedRecipe}
          activeFeedPosition={feedPositionMm}
          highlightStepIndex={currentStepIdx}
          onSelectStep={(idx) => setCurrentStepIdx(idx)}
        />
      </div>

      {/* 6-Head Punching Station Live Status Matrix */}
      <div className="bg-[#141c28] rounded-lg border border-[#25354b] shadow-md overflow-hidden">
        <div className="bg-[#1b2636] text-white px-4 py-3 font-extrabold text-sm flex items-center justify-between border-b border-[#27384f]">
          <span className="flex items-center gap-2">
            <span className="w-2.5 h-2.5 rounded-full bg-cyan-400 inline-block" />
            6-Head Punching & Cutting Station Real-time Hardware Telemetry
          </span>
          <span className="text-xs text-slate-300 font-mono">Innovance IS620N / H3U Coils M110–M121</span>
        </div>

        <div className="p-4">
          <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3">
            {/* Flange A Heads */}
            {['DA1', 'DA2', 'DA3'].map((head, i) => {
              const isFiring = Boolean(headsFiring[head]);
              return (
                <div
                  key={head}
                  className={`p-3 rounded-lg border text-center transition-all ${
                    isFiring
                      ? 'bg-cyan-500 text-slate-950 border-2 border-white shadow-xl scale-105 font-black'
                      : 'bg-[#0f1622] border-[#243348] text-white hover:border-cyan-500/40'
                  }`}
                >
                  <div className={`text-[10px] font-extrabold uppercase tracking-wider ${isFiring ? 'text-slate-950' : 'text-cyan-400'}`}>
                    Flange A #{i + 1}
                  </div>
                  <div className="font-mono text-xl font-black mt-0.5">{head}</div>
                  <div className={`text-xs font-bold mt-0.5 ${isFiring ? 'text-slate-900' : 'text-slate-300'}`}>Ø18 mm</div>
                  <span
                    className={`inline-block w-3 h-3 rounded-full mt-2 border border-black/30 ${
                      isFiring ? 'bg-white animate-ping' : 'bg-slate-600'
                    }`}
                  />
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
                    isFiring
                      ? 'bg-emerald-500 text-slate-950 border-2 border-white shadow-xl scale-105 font-black'
                      : 'bg-[#0f1622] border-[#243348] text-white hover:border-emerald-500/40'
                  }`}
                >
                  <div className={`text-[10px] font-extrabold uppercase tracking-wider ${isFiring ? 'text-slate-950' : 'text-emerald-400'}`}>
                    Flange B #{i + 1}
                  </div>
                  <div className="font-mono text-xl font-black mt-0.5">{head}</div>
                  <div className={`text-xs font-bold mt-0.5 ${isFiring ? 'text-slate-900' : 'text-slate-300'}`}>Ø18 mm</div>
                  <span
                    className={`inline-block w-3 h-3 rounded-full mt-2 border border-black/30 ${
                      isFiring ? 'bg-white animate-ping' : 'bg-slate-600'
                    }`}
                  />
                </div>
              );
            })}

            {/* Marking Unit */}
            {(() => {
              const isFiring = Boolean(headsFiring['Marking']);
              return (
                <div
                  className={`p-3 rounded-lg border text-center transition-all ${
                    isFiring
                      ? 'bg-amber-500 text-slate-950 border-2 border-white shadow-xl scale-105 font-black'
                      : 'bg-[#0f1622] border-[#243348] text-white hover:border-amber-500/40'
                  }`}
                >
                  <div className={`text-[10px] font-extrabold uppercase tracking-wider ${isFiring ? 'text-slate-950' : 'text-amber-400'}`}>
                    Stamping
                  </div>
                  <div className="font-mono text-xl font-black mt-0.5">MARK</div>
                  <div className={`text-xs font-bold mt-0.5 ${isFiring ? 'text-slate-900' : 'text-slate-300'}`}>8 Chars</div>
                  <span
                    className={`inline-block w-3 h-3 rounded-full mt-2 border border-black/30 ${
                      isFiring ? 'bg-white animate-ping' : 'bg-slate-600'
                    }`}
                  />
                </div>
              );
            })()}

            {/* Shear Unit */}
            {(() => {
              const isFiring = Boolean(headsFiring['Cutter']);
              return (
                <div
                  className={`p-3 rounded-lg border text-center transition-all ${
                    isFiring
                      ? 'bg-rose-500 text-white border-2 border-white shadow-xl scale-105 font-black'
                      : 'bg-[#0f1622] border-[#243348] text-white hover:border-rose-500/40'
                  }`}
                >
                  <div className={`text-[10px] font-extrabold uppercase tracking-wider ${isFiring ? 'text-white' : 'text-rose-400'}`}>
                    Hydraulic Cut
                  </div>
                  <div className="font-mono text-xl font-black mt-0.5">SHEAR</div>
                  <div className={`text-xs font-bold mt-0.5 ${isFiring ? 'text-slate-200' : 'text-slate-300'}`}>Single Cut</div>
                  <span
                    className={`inline-block w-3 h-3 rounded-full mt-2 border border-black/30 ${
                      isFiring ? 'bg-white animate-ping' : 'bg-slate-600'
                    }`}
                  />
                </div>
              );
            })()}
          </div>
        </div>
      </div>
    </div>
  );
};
