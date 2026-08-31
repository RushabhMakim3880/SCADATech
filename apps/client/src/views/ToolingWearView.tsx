import React, { useState } from 'react';
import {
  Wrench,
  RotateCcw,
  CheckCircle,
  Scissors,
  Bookmark,
} from 'lucide-react';

interface ToolStation {
  id: string;
  headName: string;
  stationType: 'PUNCH_A' | 'PUNCH_B' | 'MARKING' | 'CUTTER';
  flangeSide: 'A' | 'B' | 'NA';
  toolSize: number;
  toolShape: string;
  currentStrokes: number;
  maxStrokesLife: number;
  lastRegrindDate: string;
}

export const ToolingWearView: React.FC = () => {
  const [stations, setStations] = useState<ToolStation[]>([
    { id: '1', headName: 'DA1', stationType: 'PUNCH_A', flangeSide: 'A', toolSize: 18, toolShape: 'ROUND', currentStrokes: 8420, maxStrokesLife: 25000, lastRegrindDate: '2026-08-15' },
    { id: '2', headName: 'DA2', stationType: 'PUNCH_A', flangeSide: 'A', toolSize: 22, toolShape: 'ROUND', currentStrokes: 21850, maxStrokesLife: 25000, lastRegrindDate: '2026-07-10' },
    { id: '3', headName: 'DA3', stationType: 'PUNCH_A', flangeSide: 'A', toolSize: 14, toolShape: 'ROUND', currentStrokes: 3200, maxStrokesLife: 25000, lastRegrindDate: '2026-08-20' },
    { id: '4', headName: 'DB1', stationType: 'PUNCH_B', flangeSide: 'B', toolSize: 18, toolShape: 'ROUND', currentStrokes: 9140, maxStrokesLife: 25000, lastRegrindDate: '2026-08-15' },
    { id: '5', headName: 'DB2', stationType: 'PUNCH_B', flangeSide: 'B', toolSize: 22, toolShape: 'ROUND', currentStrokes: 14300, maxStrokesLife: 25000, lastRegrindDate: '2026-08-01' },
    { id: '6', headName: 'DB3', stationType: 'PUNCH_B', flangeSide: 'B', toolSize: 26, toolShape: 'OBLONG', currentStrokes: 1800, maxStrokesLife: 20000, lastRegrindDate: '2026-08-25' },
    { id: '7', headName: 'Marking', stationType: 'MARKING', flangeSide: 'A', toolSize: 0, toolShape: 'STAMP CASSETTE', currentStrokes: 42300, maxStrokesLife: 100000, lastRegrindDate: '2026-06-01' },
    { id: '8', headName: 'Cutter', stationType: 'CUTTER', flangeSide: 'NA', toolSize: 0, toolShape: 'SHEAR BLADE', currentStrokes: 34100, maxStrokesLife: 40000, lastRegrindDate: '2026-07-20' },
  ]);

  const [resetSuccess, setResetSuccess] = useState<string | null>(null);

  const handleResetCounter = (id: string, headName: string) => {
    if (!window.confirm(`Are you sure you want to reset the stroke counter for ${headName} after tool replacement/regrind?`)) return;
    setStations((prev) =>
      prev.map((s) => (s.id === id ? { ...s, currentStrokes: 0, lastRegrindDate: new Date().toISOString().split('T')[0] } : s))
    );
    setResetSuccess(`Stroke counter for ${headName} reset to 0!`);
    setTimeout(() => setResetSuccess(null), 3000);
  };

  const totalHits = stations.reduce((acc, s) => acc + s.currentStrokes, 0);

  return (
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* Top Header */}
      <div className="flex items-center justify-between pb-2 border-b border-slate-300">
        <div>
          <h2 className="text-lg font-bold text-slate-800">Tooling Life & Stroke Wear Monitor (Peddinghaus/Voortman Standard)</h2>
          <p className="text-xs text-slate-500">
            Real-time punch die stroke tracking, regrind maintenance thresholds, and blade life wear analytics.
          </p>
        </div>

        <div className="flex items-center gap-3">
          <div className="bg-slate-800 text-white px-3 py-1.5 rounded text-xs font-mono">
            <span className="text-slate-400">TOTAL LIFETIME STROKES:</span>
            <span className="font-bold text-[#00ffcc] ml-2 text-sm">{totalHits.toLocaleString()}</span>
          </div>
        </div>
      </div>

      {resetSuccess && (
        <div className="p-3 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded text-xs font-semibold flex items-center gap-2">
          <CheckCircle className="w-4 h-4 text-emerald-600" /> {resetSuccess}
        </div>
      )}

      {/* Grid of Tooling Stations */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {stations.map((st) => {
          const wearPct = Math.min(100, Math.round((st.currentStrokes / st.maxStrokesLife) * 100));
          const isCritical = wearPct >= 85;
          const isWarning = wearPct >= 65 && wearPct < 85;

          return (
            <div
              key={st.id}
              className={`panel p-4 flex flex-col justify-between border-2 transition-all ${
                isCritical
                  ? 'border-red-500 shadow-md bg-red-50/20'
                  : isWarning
                  ? 'border-amber-400 bg-amber-50/20'
                  : 'border-slate-200'
              }`}
            >
              <div>
                {/* Station Badge Header */}
                <div className="flex items-center justify-between mb-2">
                  <div className="flex items-center gap-2">
                    <div className="w-8 h-8 rounded bg-slate-800 text-white font-bold text-xs flex items-center justify-center">
                      {st.stationType === 'CUTTER' ? <Scissors className="w-4 h-4 text-red-400" /> : st.stationType === 'MARKING' ? <Bookmark className="w-4 h-4 text-amber-400" /> : <Wrench className="w-4 h-4 text-cyan-400" />}
                    </div>
                    <div>
                      <div className="font-extrabold text-sm text-slate-800">{st.headName} Station</div>
                      <div className="text-[10px] text-slate-500 font-semibold">
                        {st.flangeSide !== 'NA' ? `Flange Side ${st.flangeSide}` : 'Full Cross Section'}
                      </div>
                    </div>
                  </div>

                  <span
                    className={`px-2 py-0.5 rounded text-[10px] font-black ${
                      isCritical
                        ? 'bg-red-600 text-white animate-pulse'
                        : isWarning
                        ? 'bg-amber-500 text-white'
                        : 'bg-emerald-600 text-white'
                    }`}
                  >
                    {isCritical ? 'REGRIND DUE' : isWarning ? 'WEAR WARNING' : 'HEALTHY'}
                  </span>
                </div>

                {/* Specs */}
                <div className="bg-white p-2.5 rounded border border-slate-200 space-y-1 text-xs my-3">
                  <div className="flex justify-between">
                    <span className="text-slate-500">Installed Die:</span>
                    <span className="font-bold text-slate-800">{st.toolSize > 0 ? `Ø${st.toolSize} mm` : st.toolShape}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-slate-500">Profile:</span>
                    <span className="font-semibold text-slate-700">{st.toolShape}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-slate-500">Last Service:</span>
                    <span className="font-mono text-slate-600">{st.lastRegrindDate}</span>
                  </div>
                </div>

                {/* Wear Progress Bar */}
                <div className="space-y-1.5 my-2">
                  <div className="flex justify-between text-xs font-bold">
                    <span className="text-slate-600">Stroke Wear</span>
                    <span className={isCritical ? 'text-red-700' : isWarning ? 'text-amber-700' : 'text-emerald-700'}>
                      {wearPct}% ({st.currentStrokes.toLocaleString()} / {st.maxStrokesLife.toLocaleString()})
                    </span>
                  </div>
                  <div className="w-full h-3 bg-slate-200 rounded-full overflow-hidden border border-slate-300">
                    <div
                      style={{ width: `${wearPct}%` }}
                      className={`h-full transition-all duration-500 ${
                        isCritical
                          ? 'bg-red-600'
                          : isWarning
                          ? 'bg-amber-500'
                          : 'bg-emerald-500'
                      }`}
                    />
                  </div>
                  <div className="text-[10px] text-slate-500 text-right">
                    Remaining: {(st.maxStrokesLife - st.currentStrokes).toLocaleString()} strokes
                  </div>
                </div>
              </div>

              {/* Reset Button */}
              <button
                onClick={() => handleResetCounter(st.id, st.headName)}
                className="btn-ca btn-ca-default w-full text-xs py-1.5 justify-center mt-2 border-slate-300 hover:bg-slate-100"
              >
                <RotateCcw className="w-3.5 h-3.5" /> Reset Counter After Regrind
              </button>
            </div>
          );
        })}
      </div>
    </div>
  );
};
