import React, { useState } from 'react';
import { ItemRecipe } from '@innovance-hmi/shared';
import { AngleBarVisualizer } from './AngleBarVisualizer.js';
import { AngleBar3DVisualizer } from './AngleBar3DVisualizer.js';
import { Monitor, Box, Columns, Link2, Link2Off, Navigation } from 'lucide-react';

interface AngleBarViewerProps {
  recipe?: ItemRecipe | null;
  activeFeedPosition?: number;
  highlightStepIndex?: number;
  onSelectStep?: (stepIndex: number) => void;
  onCanvasClick?: (x: number, y: number, side: 'A' | 'B') => void;
  onStepDrag?: (stepIndex: number, newX: number, newY: number, side: 'A' | 'B') => void;
}

export const AngleBarViewer: React.FC<AngleBarViewerProps> = (props) => {
  const [viewMode, setViewMode] = useState<'2D' | '3D' | 'SPLIT'>('2D');
  const [isSyncEnabled, setIsSyncEnabled] = useState<boolean>(true);
  const [sharedHoverStepIndex, setSharedHoverStepIndex] = useState<number | null>(null);
  const [sharedFocusX, setSharedFocusX] = useState<number | null>(null);

  const lengthMm = props.recipe?.totalLength || 6016;

  const handleSelectStep = (idx: number) => {
    if (props.onSelectStep) {
      props.onSelectStep(idx);
    }
    if (isSyncEnabled && props.recipe?.steps && props.recipe.steps[idx]) {
      setSharedFocusX(props.recipe.steps[idx].xPosition);
    }
  };

  const handleHoverStep = (idx: number | null) => {
    if (isSyncEnabled) {
      setSharedHoverStepIndex(idx);
    }
  };

  const handleViewportSync = (centerMm: number) => {
    if (isSyncEnabled) {
      setSharedFocusX(centerMm);
    }
  };

  const currentFocusMm = sharedFocusX !== null ? Math.round(sharedFocusX) : Math.round(lengthMm / 2);

  return (
    <div className="flex flex-col w-full h-full bg-[#0a0e14] rounded overflow-hidden border border-slate-700">
      {/* Unified Toolbar Header */}
      <div className="bg-[#141b22] border-b border-slate-700 px-3 py-2 flex flex-wrap items-center justify-between gap-2">
        <div className="flex items-center gap-3">
          <span className="text-xs font-bold text-slate-300">
            Visual Inspection {props.recipe?.itemCode ? `• ${props.recipe.itemCode}` : ''}
          </span>

          {viewMode === 'SPLIT' && (
            <button
              type="button"
              onClick={() => setIsSyncEnabled(!isSyncEnabled)}
              className={`flex items-center gap-1.5 px-2.5 py-0.5 rounded text-[11px] font-bold border transition-all ${
                isSyncEnabled
                  ? 'bg-cyan-950/80 border-cyan-500/80 text-cyan-300 shadow-[0_0_8px_rgba(6,182,212,0.3)]'
                  : 'bg-slate-800 border-slate-600 text-slate-400'
              }`}
              title="Click to toggle synchronized pan, zoom, and hole inspection between 2D Blueprint and 3D Model"
            >
              {isSyncEnabled ? (
                <>
                  <Link2 className="w-3 h-3 text-cyan-400 animate-pulse" />
                  <span>2D & 3D Synced</span>
                </>
              ) : (
                <>
                  <Link2Off className="w-3 h-3 text-slate-400" />
                  <span>Sync Off</span>
                </>
              )}
            </button>
          )}
        </div>

        {/* View Mode Switcher */}
        <div className="flex bg-slate-800 rounded overflow-hidden border border-slate-700 shrink-0">
          <button
            onClick={() => setViewMode('2D')}
            className={`flex items-center gap-1.5 px-2.5 sm:px-3 py-1 text-xs font-semibold transition-colors ${
              viewMode === '2D' ? 'bg-[#38bdf8] text-slate-900' : 'text-slate-400 hover:text-slate-200'
            }`}
          >
            <Monitor className="w-3.5 h-3.5" /> <span>2D<span className="hidden sm:inline"> Blueprint</span></span>
          </button>
          <button
            onClick={() => setViewMode('3D')}
            className={`flex items-center gap-1.5 px-2.5 sm:px-3 py-1 text-xs font-semibold transition-colors ${
              viewMode === '3D' ? 'bg-[#38bdf8] text-slate-900' : 'text-slate-400 hover:text-slate-200'
            }`}
          >
            <Box className="w-3.5 h-3.5" /> <span>3D<span className="hidden sm:inline"> Model</span></span>
          </button>
          <button
            onClick={() => setViewMode('SPLIT')}
            className={`flex items-center gap-1.5 px-2.5 sm:px-3 py-1 text-xs font-semibold transition-colors ${
              viewMode === 'SPLIT' ? 'bg-[#38bdf8] text-slate-900' : 'text-slate-400 hover:text-slate-200'
            }`}
          >
            <Columns className="w-3.5 h-3.5" /> <span>Split<span className="hidden sm:inline"> (Synced)</span></span>
          </button>
        </div>
      </div>

      {/* Synchronized Length Scrubber Bar in Split View */}
      {viewMode === 'SPLIT' && (
        <div className="bg-[#0e141e] px-3 sm:px-4 py-1.5 border-b border-slate-800 flex flex-wrap items-center justify-between text-[11px] font-mono gap-2 shrink-0">
          <div className="flex items-center gap-2 text-slate-400">
            <Navigation className="w-3.5 h-3.5 text-cyan-400" />
            <span className="font-sans font-bold text-slate-300">Sync:</span>
            <span className="text-cyan-300 font-bold">{currentFocusMm} mm</span>
            <span className="text-slate-600">/</span>
            <span>{lengthMm} mm</span>
          </div>

          <div className="flex-1 min-w-[140px] max-w-md flex items-center gap-2">
            <input
              type="range"
              min={0}
              max={lengthMm}
              value={currentFocusMm}
              onChange={(e) => {
                const val = parseFloat(e.target.value);
                setSharedFocusX(val);
              }}
              className="w-full accent-cyan-400 h-1.5 bg-slate-700 rounded-lg cursor-pointer"
            />
          </div>

          <div className="flex items-center gap-1">
            <button
              type="button"
              onClick={() => setSharedFocusX(0)}
              className="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px]"
            >
              0mm
            </button>
            <button
              type="button"
              onClick={() => setSharedFocusX(lengthMm / 2)}
              className="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px]"
            >
              Mid
            </button>
            <button
              type="button"
              onClick={() => setSharedFocusX(lengthMm)}
              className="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px]"
            >
              {lengthMm}mm
            </button>
          </div>
        </div>
      )}

      {/* Viewer Area */}
      <div className="flex-1 flex min-h-[300px]">
        {viewMode === '2D' && (
          <div className="w-full h-full">
            <AngleBarVisualizer
              {...props}
              onSelectStep={handleSelectStep}
              onHoverStep={handleHoverStep}
            />
          </div>
        )}
        
        {viewMode === '3D' && (
          <div className="w-full h-full">
            <AngleBar3DVisualizer
              {...props}
              onSelectStep={handleSelectStep}
              onHoverStep={handleHoverStep}
            />
          </div>
        )}
        
        {viewMode === 'SPLIT' && (
          <div className="flex flex-col md:flex-row w-full h-full">
            <div className="w-full md:w-1/2 h-1/2 md:h-full border-b md:border-b-0 md:border-r border-slate-700">
              <AngleBarVisualizer
                {...props}
                externalHoverStepIndex={isSyncEnabled ? sharedHoverStepIndex : undefined}
                syncFocusX={isSyncEnabled ? sharedFocusX : undefined}
                onSelectStep={handleSelectStep}
                onHoverStep={handleHoverStep}
                onViewportSync={handleViewportSync}
              />
            </div>
            <div className="w-full md:w-1/2 h-1/2 md:h-full">
              <AngleBar3DVisualizer
                {...props}
                externalHoverStepIndex={isSyncEnabled ? sharedHoverStepIndex : undefined}
                syncFocusX={isSyncEnabled ? sharedFocusX : undefined}
                onSelectStep={handleSelectStep}
                onHoverStep={handleHoverStep}
                onViewportSync={handleViewportSync}
              />
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
