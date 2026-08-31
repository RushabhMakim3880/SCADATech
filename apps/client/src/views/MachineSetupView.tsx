import React, { useEffect, useState } from 'react';
import { MachineConfig, MachineDetail } from '@innovance-hmi/shared';
import { Wrench, Save, CheckCircle, RefreshCw } from 'lucide-react';

export const MachineSetupView: React.FC = () => {
  const [machine, setMachine] = useState<MachineConfig | null>(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [savedSuccess, setSavedSuccess] = useState(false);

  useEffect(() => {
    fetchMachineConfig();
  }, []);

  const fetchMachineConfig = async () => {
    setLoading(true);
    try {
      const res = await fetch('/api/machines');
      const json = await res.json();
      if (json.success && json.data.length > 0) {
        setMachine(json.data[0]);
      }
    } catch (err) {
      console.error('Failed to fetch machine config', err);
    } finally {
      setLoading(false);
    }
  };

  const handleToolSizeChange = (id: string, size: number) => {
    if (!machine) return;
    setMachine({
      ...machine,
      details: machine.details.map((d) => (d.id === id ? { ...d, toolSize: size } : d)),
    });
  };

  const handleToolShapeChange = (id: string, shape: 'ROUND' | 'OBLONG' | 'SQUARE') => {
    if (!machine) return;
    setMachine({
      ...machine,
      details: machine.details.map((d) => (d.id === id ? { ...d, toolShape: shape } : d)),
    });
  };

  const handleToggleHead = (id: string) => {
    if (!machine) return;
    setMachine({
      ...machine,
      details: machine.details.map((d) => (d.id === id ? { ...d, isActive: !d.isActive } : d)),
    });
  };

  const saveSetup = async () => {
    if (!machine) return;
    setSaving(true);
    try {
      const payload = {
        heads: machine.details.map((d) => ({
          id: d.id,
          toolSize: d.toolSize,
          toolShape: d.toolShape,
          isActive: d.isActive,
        })),
      };

      const res = await fetch(`/api/machines/${machine.id}/setup`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });

      if (res.ok) {
        setSavedSuccess(true);
        setTimeout(() => setSavedSuccess(false), 3000);
      }
    } catch (err) {
      console.error('Failed to save setup', err);
    } finally {
      setSaving(false);
    }
  };

  if (loading) {
    return (
      <div className="p-8 flex items-center justify-center h-full text-slate-400 font-mono">
        <RefreshCw className="w-6 h-6 animate-spin mr-2 text-cyan-400" />
        LOADING MACHINE CONFIGURATION...
      </div>
    );
  }

  return (
    <div className="p-6 space-y-6 flex-1 overflow-y-auto">
      {/* Header */}
      <div className="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
          <h2 className="text-xl font-bold text-slate-100 flex items-center gap-2">
            <Wrench className="w-5 h-5 text-cyan-400" />
            Physical Tooling & Head Configuration
          </h2>
          <p className="text-xs text-slate-400">
            Define installed punch die diameters, shapes, marking cassettes, and bed offset DX for each head.
          </p>
        </div>

        <div className="flex items-center gap-3">
          {savedSuccess && (
            <span className="flex items-center gap-1 text-xs font-mono text-emerald-400 bg-emerald-950/60 px-3 py-1.5 rounded border border-emerald-800">
              <CheckCircle className="w-4 h-4" /> SAVED SUCCESSFULLY
            </span>
          )}

          <button
            onClick={saveSetup}
            disabled={saving}
            className="industrial-btn-primary px-5 py-2.5 text-sm"
          >
            <Save className="w-4 h-4" />
            {saving ? 'SAVING...' : 'SAVE TOOLING SETUP'}
          </button>
        </div>
      </div>

      {/* Machine Bed Info Card */}
      {machine && (
        <div className="industrial-card p-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs font-mono">
          <div>
            <div className="text-slate-400">MACHINE MODEL</div>
            <div className="text-sm font-bold text-slate-100">{machine.machineName}</div>
          </div>
          <div>
            <div className="text-slate-400">ANGLE CAPACITY</div>
            <div className="text-sm font-bold text-cyan-300">
              {machine.minAngleSize}mm - {machine.maxAngleSize}mm (T: {machine.minThickness}-{machine.maxThickness}mm)
            </div>
          </div>
          <div>
            <div className="text-slate-400">TOTAL HEADS</div>
            <div className="text-sm font-bold text-emerald-400">6 Punch + Marking + Cut</div>
          </div>
          <div>
            <div className="text-slate-400">MAX BAR STOCK</div>
            <div className="text-sm font-bold text-slate-100">{machine.maxBarLength / 1000} Meters</div>
          </div>
        </div>
      )}

      {/* Heads Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {machine?.details.map((head: MachineDetail) => (
          <div
            key={head.id}
            className={`industrial-card p-5 border transition-all ${
              head.isActive ? 'border-slate-800' : 'opacity-60 border-slate-900'
            }`}
          >
            <div className="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
              <div className="flex items-center gap-2">
                <span className="w-8 h-8 rounded bg-cyan-950 border border-cyan-800 flex items-center justify-center font-mono font-black text-cyan-400 text-sm">
                  {head.headName}
                </span>
                <div>
                  <div className="text-sm font-bold text-slate-100">{head.headName} ({head.headType})</div>
                  <div className="text-[11px] text-slate-400 font-mono">
                    Side {head.side} • Bed Offset DX: {head.xPosition}mm
                  </div>
                </div>
              </div>

              <button
                onClick={() => handleToggleHead(head.id)}
                className={`px-2.5 py-1 rounded text-[11px] font-mono font-bold border transition-all ${
                  head.isActive
                    ? 'bg-emerald-950 text-emerald-300 border-emerald-800'
                    : 'bg-slate-800 text-slate-400 border-slate-700'
                }`}
              >
                {head.isActive ? 'ENABLED' : 'DISABLED'}
              </button>
            </div>

            {head.headType === 'PUNCHING' && (
              <div className="space-y-3 text-xs font-mono">
                <div>
                  <label className="text-slate-400 block mb-1">INSTALLED DIE DIAMETER (Ø mm)</label>
                  <div className="grid grid-cols-4 gap-1.5">
                    {[14, 18, 22, 26].map((size) => (
                      <button
                        key={size}
                        onClick={() => handleToolSizeChange(head.id, size)}
                        className={`py-2 rounded font-bold border transition-all ${
                          head.toolSize === size
                            ? 'bg-cyan-600 text-white border-cyan-500 shadow'
                            : 'bg-slate-950 text-slate-300 border-slate-800 hover:border-slate-700'
                        }`}
                      >
                        Ø {size}mm
                      </button>
                    ))}
                  </div>
                </div>

                <div>
                  <label className="text-slate-400 block mb-1">PUNCH TOOL SHAPE</label>
                  <div className="grid grid-cols-3 gap-2">
                    {(['ROUND', 'OBLONG', 'SQUARE'] as const).map((shape) => (
                      <button
                        key={shape}
                        onClick={() => handleToolShapeChange(head.id, shape)}
                        className={`py-1.5 rounded text-[11px] font-bold border transition-all ${
                          head.toolShape === shape
                            ? 'bg-slate-800 text-cyan-300 border-cyan-600'
                            : 'bg-slate-950 text-slate-400 border-slate-800'
                        }`}
                      >
                        {shape}
                      </button>
                    ))}
                  </div>
                </div>
              </div>
            )}

            {head.headType === 'MARKING' && (
              <div className="space-y-3 text-xs font-mono">
                <div className="text-slate-400">CASSETTE MATRIX: 4 Standard Character Slots</div>
                <div className="grid grid-cols-4 gap-2">
                  {[1, 2, 3, 4].map((c) => (
                    <div key={c} className="p-2 rounded bg-slate-950 border border-slate-800 text-center">
                      <div className="text-[10px] text-slate-500">SLOT {c}</div>
                      <div className="text-sm font-bold text-cyan-300">AUTO</div>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {head.headType === 'CUTTING' && (
              <div className="space-y-2 text-xs font-mono text-slate-400">
                <div>CUTTER TYPE: Hydraulic Dual-Action Angle Shear</div>
                <div>BLADE CLEARANCE: 0.4mm • Max Force: 120 Tons</div>
              </div>
            )}
          </div>
        ))}
      </div>
    </div>
  );
};
