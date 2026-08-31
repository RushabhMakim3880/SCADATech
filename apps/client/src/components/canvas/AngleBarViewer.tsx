import React, { useState } from 'react';
import { ItemRecipe } from '@innovance-hmi/shared';
import { AngleBarVisualizer } from './AngleBarVisualizer.js';
import { AngleBar3DVisualizer } from './AngleBar3DVisualizer.js';
import { Monitor, Box, Columns } from 'lucide-react';

interface AngleBarViewerProps {
  recipe?: ItemRecipe | null;
  activeFeedPosition?: number;
  highlightStepIndex?: number;
  onSelectStep?: (stepIndex: number) => void;
}

export const AngleBarViewer: React.FC<AngleBarViewerProps> = (props) => {
  const [viewMode, setViewMode] = useState<'2D' | '3D' | 'SPLIT'>('2D');

  return (
    <div className="flex flex-col w-full h-full bg-[#0a0e14] rounded overflow-hidden border border-slate-700">
      {/* Unified Toolbar Header */}
      <div className="bg-[#141b22] border-b border-slate-700 px-3 py-2 flex items-center justify-between">
        <div className="text-xs font-bold text-slate-300">
          Visual Inspection {props.recipe?.itemCode ? `• ${props.recipe.itemCode}` : ''}
        </div>
        
        <div className="flex bg-slate-800 rounded overflow-hidden border border-slate-700">
          <button
            onClick={() => setViewMode('2D')}
            className={`flex items-center gap-1.5 px-3 py-1 text-xs font-semibold transition-colors ${
              viewMode === '2D' ? 'bg-[#38bdf8] text-slate-900' : 'text-slate-400 hover:text-slate-200'
            }`}
          >
            <Monitor className="w-3.5 h-3.5" /> 2D Blueprint
          </button>
          <button
            onClick={() => setViewMode('3D')}
            className={`flex items-center gap-1.5 px-3 py-1 text-xs font-semibold transition-colors ${
              viewMode === '3D' ? 'bg-[#38bdf8] text-slate-900' : 'text-slate-400 hover:text-slate-200'
            }`}
          >
            <Box className="w-3.5 h-3.5" /> 3D Model
          </button>
          <button
            onClick={() => setViewMode('SPLIT')}
            className={`flex items-center gap-1.5 px-3 py-1 text-xs font-semibold transition-colors ${
              viewMode === 'SPLIT' ? 'bg-[#38bdf8] text-slate-900' : 'text-slate-400 hover:text-slate-200'
            }`}
          >
            <Columns className="w-3.5 h-3.5" /> Split View
          </button>
        </div>
      </div>

      {/* Viewer Area */}
      <div className="flex-1 flex min-h-[300px]">
        {viewMode === '2D' && (
          <div className="w-full h-full">
            <AngleBarVisualizer {...props} />
          </div>
        )}
        
        {viewMode === '3D' && (
          <div className="w-full h-full">
            <AngleBar3DVisualizer {...props} />
          </div>
        )}
        
        {viewMode === 'SPLIT' && (
          <div className="flex w-full h-full">
            <div className="w-1/2 h-full border-r border-slate-700">
              <AngleBarVisualizer {...props} />
            </div>
            <div className="w-1/2 h-full">
              <AngleBar3DVisualizer {...props} />
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
