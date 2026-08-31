import React, { useState } from 'react';
import { usePlcStore } from '../stores/usePlcStore.js';
import { wsClient } from '../services/wsClient.js';
import {
  RefreshCw,
  ToggleLeft,
  ToggleRight,
} from 'lucide-react';

interface IoChannel {
  address: string;
  name: string;
  category: string;
  isNc: boolean; // Normally Closed or Open
  state: boolean;
  forced: boolean;
}

export const IoDiagnosticsView: React.FC = () => {
  const { eStopOk, guardsOk, infeedClamp, carriageClamp, outfeedClamp, hydraulicPumpRunning, headsFiring } = usePlcStore();

  const [activeSubTab, setActiveSubTab] = useState<'INPUTS' | 'OUTPUTS' | 'CONFIG'>('INPUTS');
  const [filterCategory, setFilterCategory] = useState<string>('ALL');
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [forceModeEnabled, setForceModeEnabled] = useState<boolean>(false);

  // 32 Standard Digital Inputs for HPT 6-Head Angle Punching Line (Innovance H3U / H5U)
  const [inputs, setInputs] = useState<IoChannel[]>([
    { address: 'X0', name: 'Carriage (X) Forward Hardware Limit', category: 'CARRIAGE', isNc: true, state: true, forced: false },
    { address: 'X1', name: 'Carriage (X) Reverse Hardware Limit', category: 'CARRIAGE', isNc: true, state: true, forced: false },
    { address: 'X2', name: 'Carriage Zero Reference Proximity', category: 'CARRIAGE', isNc: false, state: false, forced: false },
    { address: 'X3', name: 'Emergency Stop PB Pressed', category: 'SAFETY', isNc: true, state: eStopOk, forced: false },
    { address: 'X4', name: 'Safety Guard Door Closed Interlock', category: 'SAFETY', isNc: true, state: guardsOk, forced: false },
    { address: 'X5', name: 'Main Hydraulic Oil Pressure Switch OK', category: 'HPU', isNc: false, state: hydraulicPumpRunning, forced: false },
    { address: 'X6', name: 'HPU Oil Temperature High Alarm', category: 'HPU', isNc: true, state: true, forced: false },
    { address: 'X7', name: 'HPU Oil Level Low Alarm', category: 'HPU', isNc: true, state: true, forced: false },
    { address: 'X10', name: 'Infeed Table Angle Bar Present Sensor', category: 'INFEED', isNc: false, state: true, forced: false },
    { address: 'X11', name: 'Infeed Conveyor Clamp Closed Proximity', category: 'INFEED', isNc: false, state: infeedClamp, forced: false },
    { address: 'X12', name: 'Carriage Gripper Clamp Closed Proximity', category: 'CARRIAGE', isNc: false, state: carriageClamp, forced: false },
    { address: 'X13', name: 'Outfeed Clamp Closed Proximity', category: 'OUTFEED', isNc: false, state: outfeedClamp, forced: false },
    { address: 'X14', name: 'DA1 Punch Cylinder Top Home Sensor', category: 'PUNCH_A', isNc: false, state: !headsFiring['DA1'], forced: false },
    { address: 'X15', name: 'DA1 Punch Cylinder Bottom Done Sensor', category: 'PUNCH_A', isNc: false, state: headsFiring['DA1'] || false, forced: false },
    { address: 'X16', name: 'DA2 Punch Cylinder Top Home Sensor', category: 'PUNCH_A', isNc: false, state: !headsFiring['DA2'], forced: false },
    { address: 'X17', name: 'DA2 Punch Cylinder Bottom Done Sensor', category: 'PUNCH_A', isNc: false, state: headsFiring['DA2'] || false, forced: false },
    { address: 'X20', name: 'DA3 Punch Cylinder Top Home Sensor', category: 'PUNCH_A', isNc: false, state: !headsFiring['DA3'], forced: false },
    { address: 'X21', name: 'DA3 Punch Cylinder Bottom Done Sensor', category: 'PUNCH_A', isNc: false, state: headsFiring['DA3'] || false, forced: false },
    { address: 'X22', name: 'DB1 Punch Cylinder Top Home Sensor', category: 'PUNCH_B', isNc: false, state: !headsFiring['DB1'], forced: false },
    { address: 'X23', name: 'DB1 Punch Cylinder Bottom Done Sensor', category: 'PUNCH_B', isNc: false, state: headsFiring['DB1'] || false, forced: false },
    { address: 'X24', name: 'DB2 Punch Cylinder Top Home Sensor', category: 'PUNCH_B', isNc: false, state: !headsFiring['DB2'], forced: false },
    { address: 'X25', name: 'DB2 Punch Cylinder Bottom Done Sensor', category: 'PUNCH_B', isNc: false, state: headsFiring['DB2'] || false, forced: false },
    { address: 'X26', name: 'DB3 Punch Cylinder Top Home Sensor', category: 'PUNCH_B', isNc: false, state: !headsFiring['DB3'], forced: false },
    { address: 'X27', name: 'DB3 Punch Cylinder Bottom Done Sensor', category: 'PUNCH_B', isNc: false, state: headsFiring['DB3'] || false, forced: false },
    { address: 'X30', name: 'Marking Unit Top Home Sensor', category: 'MARKING', isNc: false, state: !headsFiring['Marking'], forced: false },
    { address: 'X31', name: 'Marking Unit Bottom Stamped Sensor', category: 'MARKING', isNc: false, state: headsFiring['Marking'] || false, forced: false },
    { address: 'X32', name: 'Hydraulic Shear Blade Top Home Sensor', category: 'SHEAR', isNc: false, state: !headsFiring['Cutter'], forced: false },
    { address: 'X33', name: 'Hydraulic Shear Blade Bottom Cut Done', category: 'SHEAR', isNc: false, state: headsFiring['Cutter'] || false, forced: false },
    { address: 'X34', name: 'Side Infeed Cross Transfer Up Sensor', category: 'INFEED', isNc: false, state: false, forced: false },
    { address: 'X35', name: 'Side Infeed Cross Transfer Down Sensor', category: 'INFEED', isNc: false, state: true, forced: false },
    { address: 'X36', name: 'Operator Foot Pedal Punch Trigger', category: 'CONTROLS', isNc: false, state: false, forced: false },
    { address: 'X37', name: 'Lubrication Oil Pressure OK Sensor', category: 'LUBRICATION', isNc: false, state: true, forced: false },
  ]);

  // 32 Standard Digital Outputs for HPT 6-Head Line (Innovance H3U / H5U)
  const [outputs, setOutputs] = useState<IoChannel[]>([
    { address: 'Y0', name: 'Main Hydraulic Pump Motor Contactor', category: 'HPU', isNc: false, state: hydraulicPumpRunning, forced: false },
    { address: 'Y1', name: 'Head Lubrication Motor Solenoid', category: 'LUBRICATION', isNc: false, state: false, forced: false },
    { address: 'Y2', name: 'Oil Circulation Cooling Motor', category: 'HPU', isNc: false, state: false, forced: false },
    { address: 'Y3', name: 'DA1 Punch Down Hydraulic Solenoid', category: 'PUNCH_A', isNc: false, state: headsFiring['DA1'] || false, forced: false },
    { address: 'Y4', name: 'DA2 Punch Down Hydraulic Solenoid', category: 'PUNCH_A', isNc: false, state: headsFiring['DA2'] || false, forced: false },
    { address: 'Y5', name: 'DA3 Punch Down Hydraulic Solenoid', category: 'PUNCH_A', isNc: false, state: headsFiring['DA3'] || false, forced: false },
    { address: 'Y6', name: 'DB1 Punch Down Hydraulic Solenoid', category: 'PUNCH_B', isNc: false, state: headsFiring['DB1'] || false, forced: false },
    { address: 'Y7', name: 'DB2 Punch Down Hydraulic Solenoid', category: 'PUNCH_B', isNc: false, state: headsFiring['DB2'] || false, forced: false },
    { address: 'Y10', name: 'DB3 Punch Down Hydraulic Solenoid', category: 'PUNCH_B', isNc: false, state: headsFiring['DB3'] || false, forced: false },
    { address: 'Y11', name: 'Marking Cylinder Down Solenoid', category: 'MARKING', isNc: false, state: headsFiring['Marking'] || false, forced: false },
    { address: 'Y12', name: 'Hydraulic Shear Blade Down Solenoid', category: 'SHEAR', isNc: false, state: headsFiring['Cutter'] || false, forced: false },
    { address: 'Y13', name: 'Infeed Hold-Down Clamp Close Solenoid', category: 'INFEED', isNc: false, state: infeedClamp, forced: false },
    { address: 'Y14', name: 'Carriage Gripper Jaw Close Solenoid', category: 'CARRIAGE', isNc: false, state: carriageClamp, forced: false },
    { address: 'Y15', name: 'Outfeed Hold-Down Clamp Close Solenoid', category: 'OUTFEED', isNc: false, state: outfeedClamp, forced: false },
    { address: 'Y16', name: 'Side Infeed Cross Transfer Lift Up Solenoid', category: 'INFEED', isNc: false, state: false, forced: false },
    { address: 'Y17', name: 'Outfeed Discharger Ejector Solenoid', category: 'OUTFEED', isNc: false, state: false, forced: false },
    { address: 'Y20', name: 'Tower Alarm Hooter & Siren Beacon', category: 'SAFETY', isNc: false, state: false, forced: false },
    { address: 'Y21', name: 'Tower Red Strobe Light (Fault)', category: 'SAFETY', isNc: false, state: false, forced: false },
    { address: 'Y22', name: 'Tower Amber Strobe Light (Standby)', category: 'SAFETY', isNc: false, state: true, forced: false },
    { address: 'Y23', name: 'Tower Green Strobe Light (Auto Cycle Running)', category: 'SAFETY', isNc: false, state: false, forced: false },
    { address: 'Y24', name: 'Carriage Servo Drive Enable (SON)', category: 'CARRIAGE', isNc: false, state: true, forced: false },
    { address: 'Y25', name: 'Carriage Servo Drive Alarm Reset (ARST)', category: 'CARRIAGE', isNc: false, state: false, forced: false },
    { address: 'Y26', name: 'Pneumatic Chip Blow Cleaning Air Nozzle', category: 'LUBRICATION', isNc: false, state: false, forced: false },
    { address: 'Y27', name: 'Hydraulic Oil Cooler Fan Relay', category: 'HPU', isNc: false, state: false, forced: false },
  ]);

  const handleToggleForce = (listType: 'INPUTS' | 'OUTPUTS', address: string) => {
    if (!forceModeEnabled) {
      alert('Simulation Force Mode is disabled. Enable Force Mode switch at the top to manually toggle I/O states for field testing.');
      return;
    }

    if (listType === 'INPUTS') {
      setInputs((prev) =>
        prev.map((ch) => (ch.address === address ? { ...ch, state: !ch.state, forced: true } : ch))
      );
    } else {
      setOutputs((prev) =>
        prev.map((ch) => {
          if (ch.address === address) {
            const nextState = !ch.state;
            wsClient.writeTag(`PLC_Out_${address}`, nextState, 'Boolean');
            return { ...ch, state: nextState, forced: true };
          }
          return ch;
        })
      );
    }
  };

  const handleResetAllForces = () => {
    setInputs((prev) => prev.map((ch) => ({ ...ch, forced: false })));
    setOutputs((prev) => prev.map((ch) => ({ ...ch, forced: false })));
  };

  const currentList = activeSubTab === 'INPUTS' ? inputs : outputs;
  const filteredList = currentList.filter((ch) => {
    const matchCat = filterCategory === 'ALL' || ch.category === filterCategory;
    const matchQuery = ch.address.toLowerCase().includes(searchQuery.toLowerCase()) || ch.name.toLowerCase().includes(searchQuery.toLowerCase());
    return matchCat && matchQuery;
  });

  const categories = ['ALL', 'CARRIAGE', 'PUNCH_A', 'PUNCH_B', 'MARKING', 'SHEAR', 'INFEED', 'OUTFEED', 'HPU', 'SAFETY', 'LUBRICATION'];

  return (
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* Header */}
      <div className="flex flex-wrap items-center justify-between pb-2 border-b border-slate-300 gap-3">
        <div>
          <h2 className="text-lg font-bold text-slate-800">PLC Hardware I/O Signal Diagnostics (Innovance H3U/H5U)</h2>
          <p className="text-xs text-slate-500">
            Real-time visual diagnostic status of 32 Digital Inputs ($X$) and Outputs ($Y$) for instant field troubleshooting.
          </p>
        </div>

        <div className="flex items-center gap-3">
          {/* Simulation Force Mode Switch */}
          <div className="flex items-center gap-2 bg-slate-100 p-1.5 rounded border border-slate-300 text-xs">
            <span className="font-bold text-slate-700">Field Force Mode:</span>
            <button
              onClick={() => setForceModeEnabled((f) => !f)}
              className={`flex items-center gap-1 px-2 py-0.5 rounded font-bold transition-all ${
                forceModeEnabled ? 'bg-amber-500 text-white' : 'bg-slate-300 text-slate-700'
              }`}
            >
              {forceModeEnabled ? <ToggleRight className="w-4 h-4" /> : <ToggleLeft className="w-4 h-4" />}
              {forceModeEnabled ? 'ACTIVE (OVERRIDE)' : 'OFF (SAFE)'}
            </button>
          </div>

          {forceModeEnabled && (
            <button
              onClick={handleResetAllForces}
              className="btn-ca btn-ca-danger text-xs py-1.5"
            >
              <RefreshCw className="w-3.5 h-3.5" /> Release All Forces
            </button>
          )}
        </div>
      </div>

      {/* Tabs & Filters */}
      <div className="flex flex-wrap items-center justify-between gap-3 bg-white p-3 rounded border border-slate-200 shadow-sm">
        <div className="flex items-center gap-1 bg-slate-100 p-1 rounded">
          <button
            onClick={() => setActiveSubTab('INPUTS')}
            className={`px-3 py-1.5 rounded text-xs font-bold transition-all ${
              activeSubTab === 'INPUTS' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            Digital Inputs (X0–X37) • {inputs.filter((i) => i.state).length}/{inputs.length} Active
          </button>
          <button
            onClick={() => setActiveSubTab('OUTPUTS')}
            className={`px-3 py-1.5 rounded text-xs font-bold transition-all ${
              activeSubTab === 'OUTPUTS' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            Digital Outputs (Y0–Y37) • {outputs.filter((o) => o.state).length}/{outputs.length} Active
          </button>
        </div>

        <div className="flex items-center gap-2 text-xs">
          <input
            type="text"
            placeholder="Search address or sensor..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="form-control-ca w-48 py-1"
          />

          <select
            value={filterCategory}
            onChange={(e) => setFilterCategory(e.target.value)}
            className="form-control-ca py-1"
          >
            {categories.map((c) => (
              <option key={c} value={c}>{c === 'ALL' ? 'All Subsystems' : c}</option>
            ))}
          </select>
        </div>
      </div>

      {/* 4x8 Grid of High-Visibility Industrial PLC Terminal Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        {filteredList.map((ch) => {
          const isActive = ch.state;

          return (
            <div
              key={ch.address}
              onClick={() => handleToggleForce(activeSubTab === 'OUTPUTS' ? 'OUTPUTS' : 'INPUTS', ch.address)}
              className={`p-3 rounded-lg border-2 transition-all cursor-pointer select-none flex flex-col justify-between ${
                isActive
                  ? 'bg-emerald-50/70 border-emerald-500 shadow-sm'
                  : 'bg-slate-50 border-slate-200 hover:border-slate-300'
              } ${ch.forced ? 'ring-2 ring-amber-400' : ''}`}
            >
              <div>
                <div className="flex items-center justify-between mb-1.5">
                  <div className="flex items-center gap-2">
                    <span className="w-3 h-3 rounded-full flex items-center justify-center">
                      <span className={`w-2.5 h-2.5 rounded-full ${isActive ? 'bg-emerald-500 shadow-[0_0_8px_#10b981]' : 'bg-slate-300'}`} />
                    </span>
                    <span className="font-mono font-black text-sm text-slate-800">{ch.address}</span>
                  </div>

                  <div className="flex items-center gap-1">
                    {ch.forced && (
                      <span className="px-1.5 py-0.2 bg-amber-500 text-white rounded text-[9px] font-black">
                        FORCED
                      </span>
                    )}
                    <span className={`px-2 py-0.5 rounded text-[10px] font-bold ${
                      isActive ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600'
                    }`}>
                      {isActive ? 'HIGH (1)' : 'LOW (0)'}
                    </span>
                  </div>
                </div>

                <div className="text-xs font-semibold text-slate-800 leading-snug line-clamp-2">
                  {ch.name}
                </div>
              </div>

              <div className="mt-3 pt-2 border-t border-slate-200/80 flex items-center justify-between text-[10px] text-slate-500">
                <span className="font-bold uppercase tracking-wider">{ch.category}</span>
                <span>{ch.isNc ? 'NC Contact' : 'NO Contact'}</span>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
};
