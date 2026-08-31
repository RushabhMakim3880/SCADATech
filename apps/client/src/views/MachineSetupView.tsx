import React, { useEffect, useState } from 'react';
import { MachineDetail } from '@innovance-hmi/shared';
import { Save } from 'lucide-react';
import { HmiAlert } from '../utils/alerts.js';

export const MachineSetupView: React.FC = () => {
  const [machine, setMachine] = useState<any | null>(null);
  const [heads, setHeads] = useState<MachineDetail[]>([]);
  const [isSaving, setIsSaving] = useState(false);

  useEffect(() => {
    fetchMachineConfig();
  }, []);

  const fetchMachineConfig = async () => {
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
      HmiAlert.success('Tooling Configuration Saved!');
      fetchMachineConfig();
    } catch (err) {
      console.error('Failed to save tooling', err);
      HmiAlert.error('Failed to save tooling configuration.');
    } finally {
      setIsSaving(false);
    }
  };

  return (
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* Top Header */}
      <div className="flex items-center justify-between pb-2 border-b border-slate-300">
        <div>
          <h2 className="text-lg font-black text-slate-900">Machine Master & Tooling Settings (MachineMaster)</h2>
          <p className="text-xs text-slate-600 font-medium mt-0.5">
            Configure installed punch die diameters (Ø14 to Ø26), tool profiles, and bed offsets (DX).
          </p>
        </div>

        <div className="flex items-center gap-2">
          <button
            onClick={handleSaveAll}
            disabled={isSaving}
            className="btn-ca btn-ca-success"
          >
            <Save className="w-3.5 h-3.5" /> {isSaving ? 'Saving...' : 'Save Configuration'}
          </button>
        </div>
      </div>

      {/* Machine Info Overview Card */}
      {machine && (
        <div className="panel">
          <div className="panel-heading">
            <span>Machine General Specifications</span>
          </div>
          <div className="panel-body grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div>
              <span className="text-slate-500 block font-semibold">Machine Name</span>
              <span className="font-bold text-slate-800 text-sm">{machine.machineName}</span>
            </div>
            <div>
              <span className="text-slate-500 block font-semibold">Machine Type</span>
              <span className="font-bold text-slate-800">{machine.machineType}</span>
            </div>
            <div>
              <span className="text-slate-500 block font-semibold">Active Stations</span>
              <span className="font-bold text-blue-700">{heads.length} Heads Active</span>
            </div>
            <div>
              <span className="text-slate-500 block font-semibold">OPC-UA Endpoint</span>
              <span className="font-mono text-slate-700">{machine.machineCode}</span>
            </div>
          </div>
        </div>
      )}

      {/* 6-Head Tooling Matrix Table */}
      <div className="panel">
        <div className="panel-heading">
          <span>Machine Tooling Stations & Mechanical Bed Offsets (DX)</span>
        </div>

        <div className="panel-body p-0">
          <table className="table-custom">
            <thead>
              <tr>
                <th>Head Name</th>
                <th>Flange Side</th>
                <th>Installed Die Size (mm)</th>
                <th>Bed Offset DX (mm)</th>
                <th>Tool Shape Profile</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              {heads.map((head, idx) => (
                <tr key={head.id || idx}>
                  <td className="font-bold text-slate-800">
                    <span className="bg-slate-800 text-white px-2 py-0.5 rounded text-xs mr-2">
                      {head.headName}
                    </span>
                    {head.headName} Station
                  </td>
                  <td>Side {head.side}</td>
                  <td>
                    <div className="flex items-center gap-1">
                      <input
                        type="number"
                        value={head.toolSize || 18}
                        onChange={(e) => handleUpdateHead(idx, 'toolSize', parseFloat(e.target.value) || 0)}
                        className="form-control-ca text-xs w-20"
                      />
                      <span className="text-slate-500">mm</span>
                    </div>
                  </td>
                  <td>
                    <div className="flex items-center gap-1">
                      <input
                        type="number"
                        value={head.xPosition}
                        onChange={(e) => handleUpdateHead(idx, 'xPosition', parseFloat(e.target.value) || 0)}
                        className="form-control-ca text-xs w-28"
                      />
                      <span className="text-slate-500">mm</span>
                    </div>
                  </td>
                  <td>
                    <select
                      value={head.toolShape || 'ROUND'}
                      onChange={(e) => handleUpdateHead(idx, 'toolShape', e.target.value as 'ROUND' | 'OBLONG' | 'SQUARE')}
                      className="form-control-ca text-xs w-36"
                    >
                      <option value="ROUND">Round Die</option>
                      <option value="OBLONG">Oblong Die</option>
                      <option value="SQUARE">Square Die</option>
                    </select>
                  </td>
                  <td>
                    <label className="flex items-center gap-2 cursor-pointer text-xs font-semibold">
                      <input
                        type="checkbox"
                        checked={head.isActive}
                        onChange={(e) => handleUpdateHead(idx, 'isActive', e.target.checked)}
                        className="rounded"
                      />
                      {head.isActive ? 'Enabled' : 'Disabled'}
                    </label>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};
