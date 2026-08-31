import React, { useState, useEffect } from 'react';
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

const STORAGE_KEY = 'hpt_tooling_wear_master';

export const ToolingWearView: React.FC = () => {
  const [stations, setStations] = useState<ToolStation[]>([]);
  const [resetSuccess, setResetSuccess] = useState<string | null>(null);

  useEffect(() => {
    fetchMachineTooling();
  }, []);

  const fetchMachineTooling = async () => {
    try {
      const res = await fetch('/api/machines');
      const json = await res.json();
      let savedWear: Record<string, { currentStrokes: number; lastRegrindDate: string }> = {};
      try {
        const local = localStorage.getItem(STORAGE_KEY);
        if (local) savedWear = JSON.parse(local);
      } catch (e) {
        console.warn('Storage read error', e);
      }

      if (json.success && json.data.length > 0 && json.data[0].details) {
        const details = json.data[0].details;
        const loaded: ToolStation[] = details.map((h: any) => {
          const saved = savedWear[h.headName] || { currentStrokes: 0, lastRegrindDate: new Date().toISOString().split('T')[0] };
          let stationType: 'PUNCH_A' | 'PUNCH_B' | 'MARKING' | 'CUTTER' = 'PUNCH_A';
          if (h.headType === 'MARKING') stationType = 'MARKING';
          else if (h.headType === 'CUTTING') stationType = 'CUTTER';
          else if (h.side === 'B') stationType = 'PUNCH_B';

          let maxLife = 25000;
          if (stationType === 'MARKING') maxLife = 100000;
          if (stationType === 'CUTTER') maxLife = 40000;

          return {
            id: h.id || h.headName,
            headName: h.headName,
            stationType,
            flangeSide: h.side || 'NA',
            toolSize: h.toolSize || 0,
            toolShape: h.toolShape || (stationType === 'MARKING' ? 'STAMP CASSETTE' : stationType === 'CUTTER' ? 'SHEAR BLADE' : 'ROUND'),
            currentStrokes: saved.currentStrokes || 0,
            maxStrokesLife: maxLife,
            lastRegrindDate: saved.lastRegrindDate || new Date().toISOString().split('T')[0],
          };
        });
        setStations(loaded);
      }
    } catch (err) {
      console.error('Failed to load machine tooling', err);
    }
  };

  const handleResetCounter = (id: string, headName: string) => {
    if (!window.confirm(`Are you sure you want to reset the stroke counter for ${headName} after tool replacement/regrind?`)) return;
    const updated = stations.map((s) =>
      s.id === id ? { ...s, currentStrokes: 0, lastRegrindDate: new Date().toISOString().split('T')[0] } : s
    );
    setStations(updated);

    const savedWear: Record<string, { currentStrokes: number; lastRegrindDate: string }> = {};
    updated.forEach((s) => {
      savedWear[s.headName] = { currentStrokes: s.currentStrokes, lastRegrindDate: s.lastRegrindDate };
    });
    localStorage.setItem(STORAGE_KEY, JSON.stringify(savedWear));

    setResetSuccess(`Stroke counter for ${headName} reset to 0!`);
    setTimeout(() => setResetSuccess(null), 3000);
  };

  const totalHits = stations.reduce((acc, s) => acc + s.currentStrokes, 0);

  return (
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* Top Header */}
      <div className="flex items-center justify-between pb-2 border-b border-slate-300">
        <div>
          <h2 className="text-lg font-bold text-slate-800">Tooling Life & Stroke Wear Monitor (HPT Standard)</h2>
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
          const wearPct = st.maxStrokesLife > 0 ? Math.min(100, Math.round((st.currentStrokes / st.maxStrokesLife) * 100)) : 0;
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
                        ? 'bg-red-600 text-white'
                        : isWarning
                        ? 'bg-amber-500 text-slate-900'
                        : 'bg-emerald-100 text-emerald-800'
                    }`}
                  >
                    {isCritical ? 'REGRIND DUE' : isWarning ? 'WEAR WARNING' : 'HEALTHY'}
                  </span>
                </div>

                {/* Tool Specs */}
                <div className="space-y-1 my-3 bg-slate-50 p-2.5 rounded border border-slate-200 text-xs">
                  <div className="flex justify-between items-center">
                    <span className="text-slate-500">Installed Die Size:</span>
                    {st.toolSize > 0 ? (
                      <span className="font-bold text-slate-800">Ø{st.toolSize} mm</span>
                    ) : (
                      <span className="font-bold text-slate-700">{st.toolShape}</span>
                    )}
                  </div>
                  <div className="flex justify-between">
                    <span className="text-slate-500">Last Service / Regrind:</span>
                    <span className="font-mono text-slate-700">{st.lastRegrindDate}</span>
                  </div>
                </div>

                {/* Stroke Progress Bar */}
                <div className="space-y-1">
                  <div className="flex justify-between text-xs font-bold">
                    <span className="text-slate-600">Stroke Wear Progress</span>
                    <span className={isCritical ? 'text-red-600' : isWarning ? 'text-amber-600' : 'text-slate-800'}>
                      {wearPct}% ({st.currentStrokes.toLocaleString()} / {st.maxStrokesLife.toLocaleString()})
                    </span>
                  </div>
                  <div className="w-full h-3 bg-slate-200 rounded-full overflow-hidden">
                    <div
                      style={{ width: `${wearPct}%` }}
                      className={`h-full transition-all ${
                        isCritical ? 'bg-red-600' : isWarning ? 'bg-amber-500' : 'bg-blue-600'
                      }`}
                    />
                  </div>
                </div>
              </div>

              {/* Action Button */}
              <div className="pt-4 mt-3 border-t border-slate-200 flex justify-end">
                <button
                  onClick={() => handleResetCounter(st.id, st.headName)}
                  className="btn-ca btn-ca-default text-xs py-1 px-2.5 flex items-center gap-1.5"
                >
                  <RotateCcw className="w-3.5 h-3.5" /> Reset Counter After Regrind
                </button>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
};
