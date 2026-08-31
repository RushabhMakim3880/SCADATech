import React, { useEffect, useState } from 'react';
import { MachineDetail } from '@innovance-hmi/shared';
import { Wrench, Save, CheckCircle, Cpu } from 'lucide-react';

export const MachineSetupView: React.FC = () => {
  const [machine, setMachine] = useState<any | null>(null);
  const [heads, setHeads] = useState<MachineDetail[]>([]);
  const [loading, setLoading] = useState(true);
  const [isSaving, setIsSaving] = useState(false);
  const [saveSuccess, setSaveSuccess] = useState(false);

  useEffect(() => {
    fetchMachineConfig();
  }, []);

  const fetchMachineConfig = async () => {
    setLoading(true);
    try {
      const res = await fetch('/api/machines');
      const json = await res.json();
      if (json.success && json.data.length > 0) {
        const m = json.data[0];
        setMachine(m);
        setHeads(m.details || []);
      }
    } catch (err) {
      console.error('Failed to load machine config', err);
    } finally {
      setLoading(false);
    }
  };

  const handleUpdateHead = (index: number, field: keyof MachineDetail, value: any) => {
    const updated = [...heads];
    updated[index] = { ...updated[index], [field]: value };
    setHeads(updated);
  };

  const handleSaveAll = async () => {
    if (!machine) return;
    setIsSaving(true);
    try {
      await Promise.all(
        heads.map((h) =>
          fetch(`/api/machines/head/${h.id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              toolSize: h.toolSize,
              toolShape: h.toolShape,
              xPosition: h.xPosition,
              side: h.side,
              isActive: h.isActive,
            }),
          })
        )
      );
      setSaveSuccess(true);
      setTimeout(() => setSaveSuccess(false), 3000);
      fetchMachineConfig();
    } catch (err) {
      console.error('Failed to save tooling', err);
    } finally {
      setIsSaving(false);
    }
  };

  if (loading) {
    return <div className="p-8 text-center text-slate-400 font-mono">Loading Machine Master...</div>;
  }

  return (
    <div className="p-6 space-y-6 flex-1 overflow-y-auto">
      {/* Header */}
      <div className="flex items-center justify-between border-b border-scada-750 pb-4">
        <div>
          <h2 className="text-xl font-extrabold text-slate-100 flex items-center gap-2.5">
            <Wrench className="w-6 h-6 text-neon-cyan" />
            Machine Tooling Master & Bed Coordinate Offsets ($DX$)
          </h2>
          <p className="text-xs text-slate-400 font-mono">
            Configure punch dies (Ø14 to Ø26), tool profiles, marking cassettes, and mechanical bed offsets.
          </p>
        </div>

        <div className="flex items-center gap-3">
          {saveSuccess && (
            <span className="flex items-center gap-1.5 text-xs font-mono text-neon-emerald bg-emerald-950 px-3.5 py-2 rounded-xl border border-emerald-500/50 shadow-neon-emerald">
              <CheckCircle className="w-4 h-4" /> TOOLING CONFIGURATION SAVED
            </span>
          )}

          <button
            onClick={handleSaveAll}
            disabled={isSaving}
            className="scada-btn-primary px-5 py-2 text-xs font-mono"
          >
            <Save className="w-4 h-4" />
            {isSaving ? 'SAVING...' : 'SAVE CONFIGURATION'}
          </button>
        </div>
      </div>

      {/* Machine Specifications Overview Card */}
      {machine && (
        <div className="scada-panel p-5 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs font-mono">
          <div>
            <div className="text-slate-400 text-[10px] uppercase">MACHINE MODEL</div>
            <div className="font-extrabold text-sm text-slate-100 mt-1 flex items-center gap-2">
              <Cpu className="w-4 h-4 text-cyan-400" />
              {machine.machineName}
            </div>
          </div>
          <div>
            <div className="text-slate-400 text-[10px] uppercase">CNC CONTROLLER</div>
            <div className="font-bold text-slate-200 mt-1">{machine.machineType}</div>
          </div>
          <div>
            <div className="text-slate-400 text-[10px] uppercase">PUNCHING HEADS</div>
            <div className="font-bold text-neon-cyan mt-1">{heads.length} Stations Active</div>
          </div>
          <div>
            <div className="text-slate-400 text-[10px] uppercase">OPC-UA ENDPOINT</div>
            <div className="font-bold text-slate-300 mt-1 truncate">{machine.machineCode}</div>
          </div>
        </div>
      )}

      {/* 6-Head Tooling & Offsets Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 font-mono">
        {heads.map((head, idx) => {
          const isSideA = head.side === 'A';
          return (
            <div
              key={head.id || idx}
              className={`scada-panel p-5 space-y-4 border transition-all ${
                head.isActive
                  ? isSideA
                    ? 'border-cyan-500/40'
                    : 'border-emerald-500/40'
                  : 'opacity-50 border-scada-750'
              }`}
            >
              <div className="flex items-center justify-between border-b border-scada-750 pb-3">
                <div className="flex items-center gap-2.5">
                  <div
                    className={`w-9 h-9 rounded-xl flex items-center justify-center font-black text-sm ${
                      isSideA
                        ? 'bg-cyan-950 text-neon-cyan border border-cyan-500/50 shadow-neon-cyan'
                        : 'bg-emerald-950 text-neon-emerald border border-emerald-500/50 shadow-neon-emerald'
                    }`}
                  >
                    {head.headName}
                  </div>
                  <div>
                    <div className="font-extrabold text-sm text-slate-100">{head.headName} STATION</div>
                    <div className="text-[10px] text-slate-400">FLANGE {head.side}</div>
                  </div>
                </div>

                <label className="relative inline-flex items-center cursor-pointer">
                  <input
                    type="checkbox"
                    checked={head.isActive}
                    onChange={(e) => handleUpdateHead(idx, 'isActive', e.target.checked)}
                    className="sr-only peer"
                  />
                  <div className="w-10 h-5 bg-scada-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-cyan-500"></div>
                </label>
              </div>

              <div className="space-y-3 text-xs">
                <div>
                  <label className="text-[10px] text-slate-400">INSTALLED TOOL DIE SIZE (mm)</label>
                  <div className="flex items-center gap-2 mt-1">
                    <input
                      type="number"
                      value={head.toolSize || 18}
                      onChange={(e) =>
                        handleUpdateHead(idx, 'toolSize', parseFloat(e.target.value) || 0)
                      }
                      className="w-full bg-scada-950 border border-scada-750 rounded-lg px-3 py-2 text-neon-cyan font-extrabold focus:border-cyan-400 focus:outline-none text-sm"
                    />
                    <span className="text-slate-400 font-bold">mm</span>
                  </div>
                </div>

                <div>
                  <label className="text-[10px] text-slate-400">BED OFFSET DX FROM CUTTER (mm)</label>
                  <div className="flex items-center gap-2 mt-1">
                    <input
                      type="number"
                      value={head.xPosition}
                      onChange={(e) =>
                        handleUpdateHead(idx, 'xPosition', parseFloat(e.target.value) || 0)
                      }
                      className="w-full bg-scada-950 border border-scada-750 rounded-lg px-3 py-2 text-slate-100 font-extrabold focus:border-cyan-400 focus:outline-none text-sm"
                    />
                    <span className="text-slate-400 font-bold">mm</span>
                  </div>
                </div>

                <div>
                  <label className="text-[10px] text-slate-400">TOOL SHAPE PROFILE</label>
                  <select
                    value={head.toolShape || 'ROUND'}
                    onChange={(e) =>
                      handleUpdateHead(idx, 'toolShape', e.target.value as 'ROUND' | 'OBLONG' | 'SQUARE')
                    }
                    className="w-full bg-scada-950 border border-scada-750 rounded-lg px-3 py-2 text-slate-100 font-bold focus:border-cyan-400 focus:outline-none mt-1"
                  >
                    <option value="ROUND">ROUND DIE (Standard)</option>
                    <option value="OBLONG">OBLONG / SLOTTED DIE</option>
                    <option value="SQUARE">SQUARE DIE</option>
                  </select>
                </div>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
};
