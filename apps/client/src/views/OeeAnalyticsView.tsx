import React, { useState } from 'react';
import {
  TrendingUp,
  Clock,
  Download,
  Printer,
  Settings,
  Flame,
  Layers,
} from 'lucide-react';

interface ShiftConfig {
  shiftName: string;
  startTime: string;
  endTime: string;
  targetTons: number;
  targetPieces: number;
}

export const OeeAnalyticsView: React.FC = () => {
  const [shifts, setShifts] = useState<ShiftConfig[]>([
    { shiftName: 'Shift A (Morning)', startTime: '06:00', endTime: '14:00', targetTons: 12.0, targetPieces: 85 },
    { shiftName: 'Shift B (Evening)', startTime: '14:00', endTime: '22:00', targetTons: 12.0, targetPieces: 85 },
    { shiftName: 'Shift C (Night)', startTime: '22:00', endTime: '06:00', targetTons: 10.0, targetPieces: 70 },
  ]);

  const [selectedShiftIdx, setSelectedShiftIdx] = useState<number>(0);
  const [isConfigOpen, setIsConfigOpen] = useState<boolean>(false);

  // Live Simulated Telemetry for the active shift
  const totalOperatingHours = 6.8;
  const runningHours = 5.9;
  const idleHours = 0.6;
  const faultHours = 0.3;

  const totalLengthCutMeters = 780.0;
  const averageWeightPerMeterKg = 14.2; // L75x75x6 standard weight is ~6.8kg/m, heavy transmission angle is 14-25kg/m
  const processedTons = Number(((totalLengthCutMeters * averageWeightPerMeterKg) / 1000).toFixed(2));
  const cutPieces = 64;
  const targetPieces = shifts[selectedShiftIdx].targetPieces;
  const targetTons = shifts[selectedShiftIdx].targetTons;

  const availabilityPct = Number(((runningHours / totalOperatingHours) * 100).toFixed(1));
  const performancePct = Number(((processedTons / (targetTons * (runningHours / 8))) * 100).toFixed(1));
  const qualityPct = 99.2; // 0.8% scrap / reject rate
  const overallOee = Number(((availabilityPct * (performancePct / 100) * (qualityPct / 100))).toFixed(1));

  const handleExportCsv = () => {
    const csvContent = `data:text/csv;charset=utf-8,Shift,Target Tons,Actual Tons,Pieces,Running Hrs,Fault Hrs,Availability,Performance,Quality,OEE\n${shifts[selectedShiftIdx].shiftName},${targetTons},${processedTons},${cutPieces},${runningHours},${faultHours},${availabilityPct}%,${performancePct}%,${qualityPct}%,${overallOee}%`;
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `HPT_Shift_Production_Report_${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  return (
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* Header */}
      <div className="flex flex-wrap items-center justify-between pb-2 border-b border-slate-300 gap-3">
        <div>
          <h2 className="text-lg font-bold text-slate-800">Shift Production & Tonnage OEE Analytics (Transmission Tower Standard)</h2>
          <p className="text-xs text-slate-500">
            Real-time metric steel tonnage processed, shift efficiency, machine uptime availability, and OEE metrics.
          </p>
        </div>

        <div className="flex items-center gap-2">
          <button
            onClick={() => setIsConfigOpen(true)}
            className="btn-ca btn-ca-default text-xs py-1.5"
          >
            <Settings className="w-3.5 h-3.5" /> Shift & Target Config
          </button>
          <button
            onClick={handleExportCsv}
            className="btn-ca btn-ca-success text-xs py-1.5"
          >
            <Download className="w-3.5 h-3.5" /> Export Shift CSV
          </button>
          <button
            onClick={() => window.print()}
            className="btn-ca btn-ca-primary text-xs py-1.5"
          >
            <Printer className="w-3.5 h-3.5" /> Print Report
          </button>
        </div>
      </div>

      {/* Shift Selector Pill Tabs */}
      <div className="flex items-center gap-2 bg-white p-2 rounded border border-slate-200 shadow-sm text-xs">
        <span className="font-bold text-slate-600 ml-1">Active Production Shift:</span>
        <div className="flex items-center gap-1">
          {shifts.map((s, idx) => (
            <button
              key={s.shiftName}
              onClick={() => setSelectedShiftIdx(idx)}
              className={`px-3 py-1.5 rounded font-bold transition-all ${
                selectedShiftIdx === idx
                  ? 'bg-blue-600 text-white shadow-sm'
                  : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
              }`}
            >
              {s.shiftName} ({s.startTime} - {s.endTime})
            </button>
          ))}
        </div>
      </div>

      {/* KPI Stats Row */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {/* Metric Tonnage Card */}
        <div className="widget-stats bg-blue">
          <div className="stats-icon"><Flame className="w-12 h-12" /></div>
          <div className="stats-info">
            <h4>Processed Steel Weight</h4>
            <p>{processedTons} Metric Tons</p>
            <span className="text-[11px] text-blue-100 font-semibold">
              Target: {targetTons} Tons ({Math.round((processedTons / targetTons) * 100)}%)
            </span>
          </div>
        </div>

        {/* Piece Output Card */}
        <div className="widget-stats bg-green">
          <div className="stats-icon"><Layers className="w-12 h-12" /></div>
          <div className="stats-info">
            <h4>Tower Angle Pieces</h4>
            <p>{cutPieces} / {targetPieces} Pcs</p>
            <span className="text-[11px] text-emerald-100 font-semibold">
              Total Feed: {totalLengthCutMeters} Meters
            </span>
          </div>
        </div>

        {/* Uptime Hours Card */}
        <div className="widget-stats bg-orange">
          <div className="stats-icon"><Clock className="w-12 h-12" /></div>
          <div className="stats-info">
            <h4>Machine Run Uptime</h4>
            <p>{runningHours}h / {totalOperatingHours}h</p>
            <span className="text-[11px] text-orange-100 font-semibold">
              Idle: {idleHours}h • Faults: {faultHours}h
            </span>
          </div>
        </div>

        {/* Overall OEE Card */}
        <div className="widget-stats bg-purple">
          <div className="stats-icon"><TrendingUp className="w-12 h-12" /></div>
          <div className="stats-info">
            <h4>Overall Equipment OEE</h4>
            <p>{overallOee}%</p>
            <span className="text-[11px] text-purple-100 font-semibold">
              World Class Benchmark: &gt;85%
            </span>
          </div>
        </div>
      </div>

      {/* OEE Component Breakdown & Tonnage Visualizer */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {/* 1. OEE 3-Pillar Breakdown */}
        <div className="panel">
          <div className="panel-heading">
            <span>OEE Core Pillars Breakdown</span>
          </div>
          <div className="panel-body space-y-4 text-xs">
            {/* Availability */}
            <div className="space-y-1">
              <div className="flex justify-between font-bold">
                <span className="text-slate-700">Availability (Uptime vs Planned)</span>
                <span className="text-blue-700">{availabilityPct}%</span>
              </div>
              <div className="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden">
                <div style={{ width: `${availabilityPct}%` }} className="h-full bg-blue-600" />
              </div>
              <div className="text-[10px] text-slate-500">Run: {runningHours}h | Idle: {idleHours}h | Fault: {faultHours}h</div>
            </div>

            {/* Performance */}
            <div className="space-y-1">
              <div className="flex justify-between font-bold">
                <span className="text-slate-700">Performance (Feed Rate Efficiency)</span>
                <span className="text-emerald-700">{performancePct}%</span>
              </div>
              <div className="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden">
                <div style={{ width: `${Math.min(100, performancePct)}%` }} className="h-full bg-emerald-600" />
              </div>
              <div className="text-[10px] text-slate-500">Cycle Feed Speed: 28.5 m/min vs 30.0 target</div>
            </div>

            {/* Quality */}
            <div className="space-y-1">
              <div className="flex justify-between font-bold">
                <span className="text-slate-700">Quality (First Pass Yield)</span>
                <span className="text-purple-700">{qualityPct}%</span>
              </div>
              <div className="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden">
                <div style={{ width: `${qualityPct}%` }} className="h-full bg-purple-600" />
              </div>
              <div className="text-[10px] text-slate-500">Scrap Remnant: &lt;1.1% (IS 802 Compliance)</div>
            </div>
          </div>
        </div>

        {/* 2. Shift Tonnage Target Progress */}
        <div className="panel lg:col-span-2">
          <div className="panel-heading">
            <span>Shift Tonnage & Production Progress Gauge</span>
            <span className="text-xs text-slate-300">Shift #{selectedShiftIdx + 1} Target: {targetTons} MT</span>
          </div>
          <div className="panel-body space-y-4 text-xs">
            <div className="p-4 bg-slate-50 rounded border border-slate-200 space-y-2">
              <div className="flex items-center justify-between font-bold text-sm">
                <span className="text-slate-800">Shift Weight Processed</span>
                <span className="text-blue-700">{processedTons} / {targetTons} Metric Tons</span>
              </div>

              <div className="w-full h-5 bg-slate-200 rounded-full overflow-hidden p-0.5 border border-slate-300">
                <div
                  style={{ width: `${Math.min(100, (processedTons / targetTons) * 100)}%` }}
                  className="h-full bg-gradient-to-r from-blue-600 to-emerald-500 rounded-full flex items-center justify-end pr-2 text-[10px] font-black text-white"
                >
                  {Math.round((processedTons / targetTons) * 100)}%
                </div>
              </div>

              <div className="grid grid-cols-3 gap-2 pt-2 text-center text-xs">
                <div className="p-2 bg-white rounded border">
                  <div className="text-slate-500 text-[10px]">REMAINING TONNAGE</div>
                  <div className="font-bold text-slate-800">{Math.max(0, targetTons - processedTons).toFixed(2)} Tons</div>
                </div>
                <div className="p-2 bg-white rounded border">
                  <div className="text-slate-500 text-[10px]">AVG TONNAGE / HR</div>
                  <div className="font-bold text-emerald-700">{(processedTons / (runningHours || 1)).toFixed(2)} Tons/h</div>
                </div>
                <div className="p-2 bg-white rounded border">
                  <div className="text-slate-500 text-[10px]">ESTIMATED FINISH</div>
                  <div className="font-bold text-blue-700">On Schedule</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Config Modal */}
      {isConfigOpen && (
        <div className="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
          <div className="bg-white rounded-lg shadow-2xl border border-slate-300 w-full max-w-lg overflow-hidden text-xs">
            <div className="panel-heading bg-slate-800 text-white px-4 py-3 flex items-center justify-between">
              <span className="font-bold text-sm">Configure Shift Timings & Tonnage Targets</span>
              <button onClick={() => setIsConfigOpen(false)} className="text-slate-300 hover:text-white">✕</button>
            </div>

            <div className="p-4 space-y-3">
              {shifts.map((s, idx) => (
                <div key={idx} className="p-3 bg-slate-50 rounded border border-slate-200 space-y-2">
                  <div className="font-bold text-slate-800">{s.shiftName}</div>
                  <div className="grid grid-cols-2 gap-2">
                    <div>
                      <label className="text-slate-600 block text-[10px]">Start Time</label>
                      <input
                        type="time"
                        value={s.startTime}
                        onChange={(e) => {
                          const upd = [...shifts];
                          upd[idx].startTime = e.target.value;
                          setShifts(upd);
                        }}
                        className="form-control-ca text-xs py-1"
                      />
                    </div>
                    <div>
                      <label className="text-slate-600 block text-[10px]">End Time</label>
                      <input
                        type="time"
                        value={s.endTime}
                        onChange={(e) => {
                          const upd = [...shifts];
                          upd[idx].endTime = e.target.value;
                          setShifts(upd);
                        }}
                        className="form-control-ca text-xs py-1"
                      />
                    </div>
                    <div>
                      <label className="text-slate-600 block text-[10px]">Target Weight (Tons)</label>
                      <input
                        type="number"
                        value={s.targetTons}
                        onChange={(e) => {
                          const upd = [...shifts];
                          upd[idx].targetTons = parseFloat(e.target.value) || 0;
                          setShifts(upd);
                        }}
                        className="form-control-ca text-xs py-1 font-bold"
                      />
                    </div>
                    <div>
                      <label className="text-slate-600 block text-[10px]">Target Pieces</label>
                      <input
                        type="number"
                        value={s.targetPieces}
                        onChange={(e) => {
                          const upd = [...shifts];
                          upd[idx].targetPieces = parseInt(e.target.value) || 0;
                          setShifts(upd);
                        }}
                        className="form-control-ca text-xs py-1"
                      />
                    </div>
                  </div>
                </div>
              ))}
            </div>

            <div className="p-3 bg-slate-100 border-t border-slate-200 flex justify-end">
              <button onClick={() => setIsConfigOpen(false)} className="btn-ca btn-ca-primary">
                Save Shift Configuration
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
