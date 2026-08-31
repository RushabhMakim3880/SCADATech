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
  isNc: boolean;
  state: boolean;
  forced: boolean;
}

export const IoDiagnosticsView: React.FC = () => {
  const { isConnected, eStopOk, guardsOk, infeedClamp, carriageClamp, outfeedClamp, hydraulicPumpRunning, headsFiring } = usePlcStore();

  const [activeSubTab, setActiveSubTab] = useState<'INPUTS' | 'OUTPUTS' | 'CONFIG'>('INPUTS');
  const [filterCategory, setFilterCategory] = useState<string>('ALL');
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [forceModeEnabled, setForceModeEnabled] = useState<boolean>(false);

  // 32 Digital Inputs for HPT 6-Head Angle Punching Line (Innovance H3U / H5U)
  const [forcedInputs, setForcedInputs] = useState<Record<string, boolean>>({});
  const [forcedOutputs, setForcedOutputs] = useState<Record<string, boolean>>({});

  const rawInputs: Array<Omit<IoChannel, 'state' | 'forced'> & { dynamicState: boolean }> = [
    { address: 'X0', name: 'Carriage (X) Forward Hardware Limit', category: 'CARRIAGE', isNc: true, dynamicState: isConnected ? false : false },
    { address: 'X1', name: 'Carriage (X) Reverse Hardware Limit', category: 'CARRIAGE', isNc: true, dynamicState: isConnected ? false : false },
    { address: 'X2', name: 'Carriage Zero Reference Proximity', category: 'CARRIAGE', isNc: false, dynamicState: isConnected ? false : false },
    { address: 'X3', name: 'Emergency Stop PB Status', category: 'SAFETY', isNc: true, dynamicState: isConnected ? eStopOk : false },
    { address: 'X4', name: 'Safety Guard Door Closed Interlock', category: 'SAFETY', isNc: true, dynamicState: isConnected ? guardsOk : false },
    { address: 'X5', name: 'Main Hydraulic Oil Pressure Switch OK', category: 'HPU', isNc: false, dynamicState: isConnected ? hydraulicPumpRunning : false },
    { address: 'X6', name: 'HPU Oil Temperature High Alarm', category: 'HPU', isNc: true, dynamicState: false },
    { address: 'X7', name: 'HPU Oil Level Low Alarm', category: 'HPU', isNc: true, dynamicState: false },
    { address: 'X10', name: 'Infeed Table Angle Bar Present Sensor', category: 'INFEED', isNc: false, dynamicState: false },
    { address: 'X11', name: 'Infeed Conveyor Clamp Closed Proximity', category: 'INFEED', isNc: false, dynamicState: isConnected ? infeedClamp : false },
    { address: 'X12', name: 'Carriage Gripper Clamp Closed Proximity', category: 'CARRIAGE', isNc: false, dynamicState: isConnected ? carriageClamp : false },
    { address: 'X13', name: 'Outfeed Clamp Closed Proximity', category: 'OUTFEED', isNc: false, dynamicState: isConnected ? outfeedClamp : false },
    { address: 'X14', name: 'DA1 Punch Cylinder Top Home Sensor', category: 'PUNCH_A', isNc: false, dynamicState: false },
    { address: 'X15', name: 'DA1 Punch Cylinder Bottom Done Sensor', category: 'PUNCH_A', isNc: false, dynamicState: isConnected ? Boolean(headsFiring['DA1']) : false },
    { address: 'X16', name: 'DA2 Punch Cylinder Top Home Sensor', category: 'PUNCH_A', isNc: false, dynamicState: false },
    { address: 'X17', name: 'DA2 Punch Cylinder Bottom Done Sensor', category: 'PUNCH_A', isNc: false, dynamicState: isConnected ? Boolean(headsFiring['DA2']) : false },
    { address: 'X20', name: 'DA3 Punch Cylinder Top Home Sensor', category: 'PUNCH_A', isNc: false, dynamicState: false },
    { address: 'X21', name: 'DA3 Punch Cylinder Bottom Done Sensor', category: 'PUNCH_A', isNc: false, dynamicState: isConnected ? Boolean(headsFiring['DA3']) : false },
    { address: 'X22', name: 'DB1 Punch Cylinder Top Home Sensor', category: 'PUNCH_B', isNc: false, dynamicState: false },
    { address: 'X23', name: 'DB1 Punch Cylinder Bottom Done Sensor', category: 'PUNCH_B', isNc: false, dynamicState: isConnected ? Boolean(headsFiring['DB1']) : false },
    { address: 'X24', name: 'DB2 Punch Cylinder Top Home Sensor', category: 'PUNCH_B', isNc: false, dynamicState: false },
    { address: 'X25', name: 'DB2 Punch Cylinder Bottom Done Sensor', category: 'PUNCH_B', isNc: false, dynamicState: isConnected ? Boolean(headsFiring['DB2']) : false },
    { address: 'X26', name: 'DB3 Punch Cylinder Top Home Sensor', category: 'PUNCH_B', isNc: false, dynamicState: false },
    { address: 'X27', name: 'DB3 Punch Cylinder Bottom Done Sensor', category: 'PUNCH_B', isNc: false, dynamicState: isConnected ? Boolean(headsFiring['DB3']) : false },
    { address: 'X30', name: 'Marking Unit Top Home Sensor', category: 'MARKING', isNc: false, dynamicState: false },
    { address: 'X31', name: 'Marking Unit Bottom Stamped Sensor', category: 'MARKING', isNc: false, dynamicState: isConnected ? Boolean(headsFiring['Marking']) : false },
    { address: 'X32', name: 'Hydraulic Shear Blade Top Home Sensor', category: 'SHEAR', isNc: false, dynamicState: false },
    { address: 'X33', name: 'Hydraulic Shear Blade Bottom Cut Done', category: 'SHEAR', isNc: false, dynamicState: isConnected ? Boolean(headsFiring['Cutter']) : false },
    { address: 'X34', name: 'Side Infeed Cross Transfer Up Sensor', category: 'INFEED', isNc: false, dynamicState: false },
    { address: 'X35', name: 'Side Infeed Cross Transfer Down Sensor', category: 'INFEED', isNc: false, dynamicState: false },
    { address: 'X36', name: 'Operator Foot Pedal Punch Trigger', category: 'CONTROLS', isNc: false, dynamicState: false },
    { address: 'X37', name: 'Lubrication Oil Pressure OK Sensor', category: 'LUBRICATION', isNc: false, dynamicState: false },
  ];

  const rawOutputs: Array<Omit<IoChannel, 'state' | 'forced'> & { dynamicState: boolean }> = [
    { address: 'Y0', name: 'Main Hydraulic Pump Motor Contactor', category: 'HPU', isNc: false, dynamicState: isConnected ? hydraulicPumpRunning : false },
    { address: 'Y1', name: 'Head Lubrication Motor Solenoid', category: 'LUBRICATION', isNc: false, dynamicState: false },
    { address: 'Y2', name: 'Oil Circulation Cooling Motor', category: 'HPU', isNc: false, dynamicState: false },
    { address: 'Y3', name: 'DA1 Punch Down Hydraulic Solenoid', category: 'PUNCH_A', isNc: false, dynamicState: isConnected ? Boolean(headsFiring['DA1']) : false },
    { address: 'Y4', name: 'DA2 Punch Down Hydraulic Solenoid', category: 'PUNCH_A', isNc: false, dynamicState: isConnected ? Boolean(headsFiring['DA2']) : false },
    { address: 'Y5', name: 'DA3 Punch Down Hydraulic Solenoid', category: 'PUNCH_A', isNc: false, dynamicState: isConnected ? Boolean(headsFiring['DA3']) : false },
    { address: 'Y6', name: 'DB1 Punch Down Hydraulic Solenoid', category: 'PUNCH_B', isNc: false, dynamicState: isConnected ? Boolean(headsFiring['DB1']) : false },
    { address: 'Y7', name: 'DB2 Punch Down Hydraulic Solenoid', category: 'PUNCH_B', isNc: false, dynamicState: isConnected ? Boolean(headsFiring['DB2']) : false },
    { address: 'Y10', name: 'DB3 Punch Down Hydraulic Solenoid', category: 'PUNCH_B', isNc: false, dynamicState: isConnected ? Boolean(headsFiring['DB3']) : false },
    { address: 'Y11', name: 'Marking Cylinder Down Solenoid', category: 'MARKING', isNc: false, dynamicState: isConnected ? Boolean(headsFiring['Marking']) : false },
    { address: 'Y12', name: 'Hydraulic Shear Blade Down Solenoid', category: 'SHEAR', isNc: false, dynamicState: isConnected ? Boolean(headsFiring['Cutter']) : false },
    { address: 'Y13', name: 'Infeed Angle Bar Clamping Solenoid', category: 'INFEED', isNc: false, dynamicState: isConnected ? infeedClamp : false },
    { address: 'Y14', name: 'Carriage Gripper Clamp Solenoid', category: 'CARRIAGE', isNc: false, dynamicState: isConnected ? carriageClamp : false },
    { address: 'Y15', name: 'Outfeed Hold-Down Clamp Solenoid', category: 'OUTFEED', isNc: false, dynamicState: isConnected ? outfeedClamp : false },
    { address: 'Y16', name: 'Side Infeed Cross Transfer Lift Cylinder', category: 'INFEED', isNc: false, dynamicState: false },
    { address: 'Y17', name: 'Side Infeed Chain Drive Motor FWD', category: 'INFEED', isNc: false, dynamicState: false },
    { address: 'Y20', name: 'Alarm Beacon Red Tower Light', category: 'SIGNAL', isNc: false, dynamicState: false },
    { address: 'Y21', name: 'Cycle Running Green Tower Light', category: 'SIGNAL', isNc: false, dynamicState: isConnected },
    { address: 'Y22', name: 'Standby Amber Tower Light', category: 'SIGNAL', isNc: false, dynamicState: !isConnected },
    { address: 'Y23', name: 'HPU High Pressure Relief Solenoid', category: 'HPU', isNc: false, dynamicState: false },
    { address: 'Y24', name: 'Carriage Brake Release Solenoid', category: 'CARRIAGE', isNc: false, dynamicState: false },
    { address: 'Y25', name: 'Scrap Chute Pusher Cylinder Solenoid', category: 'SHEAR', isNc: false, dynamicState: false },
    { address: 'Y26', name: 'Material Length Stop Gate Cylinder', category: 'INFEED', isNc: false, dynamicState: false },
    { address: 'Y27', name: 'Outfeed Auto Eject Roller Motor', category: 'OUTFEED', isNc: false, dynamicState: false },
  ];

  const inputs: IoChannel[] = rawInputs.map((i) => ({
    address: i.address,
    name: i.name,
    category: i.category,
    isNc: i.isNc,
    state: forceModeEnabled && forcedInputs[i.address] !== undefined ? forcedInputs[i.address] : i.dynamicState,
    forced: Boolean(forceModeEnabled && forcedInputs[i.address] !== undefined),
  }));

  const outputs: IoChannel[] = rawOutputs.map((o) => ({
    address: o.address,
    name: o.name,
    category: o.category,
    isNc: o.isNc,
    state: forceModeEnabled && forcedOutputs[o.address] !== undefined ? forcedOutputs[o.address] : o.dynamicState,
    forced: Boolean(forceModeEnabled && forcedOutputs[o.address] !== undefined),
  }));

  const categories = ['ALL', 'CARRIAGE', 'PUNCH_A', 'PUNCH_B', 'MARKING', 'SHEAR', 'INFEED', 'OUTFEED', 'HPU', 'SAFETY', 'LUBRICATION', 'SIGNAL'];

  const handleToggleForce = (type: 'INPUTS' | 'OUTPUTS', address: string) => {
    if (!forceModeEnabled) return;
    if (type === 'INPUTS') {
      const current = forcedInputs[address] ?? false;
      setForcedInputs((prev) => ({ ...prev, [address]: !current }));
    } else {
      const current = forcedOutputs[address] ?? false;
      const next = !current;
      setForcedOutputs((prev) => ({ ...prev, [address]: next }));
      wsClient.writeTag(`Output_${address}_Force`, next, 'Boolean');
    }
  };

  const handleClearAllForces = () => {
    setForcedInputs({});
    setForcedOutputs({});
  };

  const currentList = activeSubTab === 'INPUTS' ? inputs : outputs;
  const filteredList = currentList.filter((item) => {
    const matchesCategory = filterCategory === 'ALL' || item.category === filterCategory;
    const matchesSearch = item.address.toLowerCase().includes(searchQuery.toLowerCase()) ||
                          item.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
                          item.category.toLowerCase().includes(searchQuery.toLowerCase());
    return matchesCategory && matchesSearch;
  });

  const activeInputsCount = inputs.filter((i) => i.state).length;
  const activeOutputsCount = outputs.filter((o) => o.state).length;

  return (
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* Top Header */}
      <div className="flex flex-wrap items-center justify-between pb-2 border-b border-slate-300 gap-3">
        <div>
          <h2 className="text-lg font-black text-slate-900 flex items-center gap-2">
            PLC Hardware I/O Signal Diagnostics (Innovance H3U/H5U)
          </h2>
          <p className="text-xs text-slate-600 font-medium mt-0.5">
            Real-time visual diagnostic status of 32 Digital Inputs ($X$) and Outputs ($Y$) for instant field troubleshooting.
          </p>
        </div>

        {/* Force Override Mode Controller */}
        <div className="flex items-center gap-3 bg-slate-100 p-1.5 rounded-lg border border-slate-300 text-xs">
          <div className="flex items-center gap-1.5 font-bold">
            <span className="text-slate-600">Field Force Mode:</span>
            <button
              onClick={() => {
                setForceModeEnabled((prev) => !prev);
                if (forceModeEnabled) handleClearAllForces();
              }}
              className={`flex items-center gap-1 px-2.5 py-1 rounded font-bold transition-all ${
                forceModeEnabled
                  ? 'bg-red-600 text-white shadow-sm'
                  : 'bg-slate-200 text-slate-700 hover:bg-slate-300'
              }`}
            >
              {forceModeEnabled ? <ToggleRight className="w-4 h-4" /> : <ToggleLeft className="w-4 h-4" />}
              {forceModeEnabled ? 'ACTIVE (OVERRIDE)' : 'OFF (SAFE)'}
            </button>
          </div>

          {forceModeEnabled && (
            <button
              onClick={handleClearAllForces}
              className="btn-ca btn-ca-danger text-xs py-1 px-2 flex items-center gap-1"
            >
              <RefreshCw className="w-3 h-3" /> Clear All Forces
            </button>
          )}
        </div>
      </div>

      {/* Tabs Row (Inputs vs Outputs vs Hardware Map) */}
      <div className="flex flex-wrap items-center justify-between gap-3 text-xs">
        <div className="flex items-center gap-2">
          <button
            onClick={() => setActiveSubTab('INPUTS')}
            className={`px-4 py-2 rounded-lg font-bold transition-all shadow-sm ${
              activeSubTab === 'INPUTS'
                ? 'bg-blue-600 text-white'
                : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100'
            }`}
          >
            Digital Inputs (X0–X37) • {activeInputsCount}/{inputs.length} Active
          </button>

          <button
            onClick={() => setActiveSubTab('OUTPUTS')}
            className={`px-4 py-2 rounded-lg font-bold transition-all shadow-sm ${
              activeSubTab === 'OUTPUTS'
                ? 'bg-blue-600 text-white'
                : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-100'
            }`}
          >
            Digital Outputs (Y0–Y37) • {activeOutputsCount}/{outputs.length} Active
          </button>
        </div>

        {/* Filter & Search Bar */}
        <div className="flex items-center gap-2">
          <input
            type="text"
            placeholder="Search address or sensor..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="form-control-ca text-xs py-1 px-2.5 w-48"
          />

          <select
            value={filterCategory}
            onChange={(e) => setFilterCategory(e.target.value)}
            className="form-control-ca text-xs py-1"
          >
            {categories.map((c) => (
              <option key={c} value={c}>
                {c === 'ALL' ? 'All Subsystems' : c}
              </option>
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
                  : 'bg-slate-50/80 border-slate-300'
              } ${forceModeEnabled ? 'hover:border-blue-400' : ''}`}
            >
              <div>
                <div className="flex items-center justify-between mb-1.5">
                  <div className="flex items-center gap-2">
                    <span
                      className={`w-3.5 h-3.5 rounded-full border border-slate-400 inline-block shadow-inner ${
                        isActive ? 'bg-emerald-500 animate-pulse' : 'bg-slate-200'
                      }`}
                    />
                    <span className="font-mono font-black text-sm text-slate-900">{ch.address}</span>
                  </div>

                  <span
                    className={`px-2 py-0.5 rounded text-[10px] font-black tracking-wider ${
                      isActive ? 'bg-emerald-700 text-white' : 'bg-slate-200 text-slate-600'
                    }`}
                  >
                    {isActive ? 'HIGH (1)' : 'LOW (0)'}
                  </span>
                </div>

                <div className="font-bold text-xs text-slate-800 line-clamp-2 leading-tight">
                  {ch.name}
                </div>
              </div>

              <div className="flex items-center justify-between mt-3 pt-2 border-t border-slate-200 text-[10px]">
                <span className="font-semibold text-slate-500 uppercase">{ch.category}</span>
                <span className="text-slate-400 font-mono">
                  {ch.isNc ? 'NC Contact' : 'NO Contact'}
                </span>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
};
