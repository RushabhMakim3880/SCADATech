import React, { useEffect, useState } from 'react';
import { usePlcStore } from '../stores/usePlcStore.js';
import { AlarmDefinition, ActiveAlarm } from '@innovance-hmi/shared';
import {
  AlertOctagon,
  ShieldAlert,
  AlertTriangle,
  Info,
  CheckCircle,
  RefreshCw,
  Clock,
  Wrench,
} from 'lucide-react';

interface AlarmLogItem {
  id: string;
  alarmCode: string;
  alarmName: string;
  severity: string;
  triggeredAt: string;
  acknowledgedAt?: string;
  clearedAt?: string;
}

export const AlarmsView: React.FC = () => {
  const { activeAlarms, setAlarms } = usePlcStore();
  const [definitions, setDefinitions] = useState<AlarmDefinition[]>([]);
  const [logs, setLogs] = useState<AlarmLogItem[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchAlarmsData();
  }, []);

  const fetchAlarmsData = async () => {
    setLoading(true);
    try {
      const [defsRes, logsRes] = await Promise.all([
        fetch('/api/alarms/definitions'),
        fetch('/api/alarms/logs'),
      ]);
      const defsJson = await defsRes.json();
      const logsJson = await logsRes.json();

      if (defsJson.success) setDefinitions(defsJson.data);
      if (logsJson.success) setLogs(logsJson.data);
    } catch (err) {
      console.error('Failed to load alarms data', err);
    } finally {
      setLoading(false);
    }
  };

  const handleAcknowledge = async (alarm: ActiveAlarm) => {
    try {
      await fetch('/api/alarms/acknowledge', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ logId: alarm.id }),
      });
      setAlarms(activeAlarms.filter((a) => a.alarmCode !== alarm.alarmCode));
    } catch (err) {
      console.error('Failed to acknowledge alarm', err);
    }
  };

  const getSeverityBadge = (severity: string) => {
    switch (severity) {
      case 'EMERGENCY':
        return (
          <span className="px-3 py-1 rounded-lg bg-rose-950 text-rose-300 border border-rose-500/80 font-mono font-extrabold text-xs flex items-center gap-1.5 shadow-neon-rose">
            <ShieldAlert className="w-3.5 h-3.5" /> EMERGENCY
          </span>
        );
      case 'CRITICAL':
        return (
          <span className="px-3 py-1 rounded-lg bg-rose-950/80 text-rose-400 border border-rose-600 font-mono font-bold text-xs flex items-center gap-1.5">
            <AlertOctagon className="w-3.5 h-3.5" /> CRITICAL
          </span>
        );
      case 'WARNING':
        return (
          <span className="px-3 py-1 rounded-lg bg-amber-950 text-neon-amber border border-amber-500/80 font-mono font-bold text-xs flex items-center gap-1.5 shadow-neon-amber">
            <AlertTriangle className="w-3.5 h-3.5" /> WARNING
          </span>
        );
      default:
        return (
          <span className="px-3 py-1 rounded-lg bg-cyan-950 text-neon-cyan border border-cyan-500/80 font-mono font-bold text-xs flex items-center gap-1.5 shadow-neon-cyan">
            <Info className="w-3.5 h-3.5" /> INFO
          </span>
        );
    }
  };

  return (
    <div className="p-6 space-y-6 flex-1 overflow-y-auto">
      {/* Header */}
      <div className="flex items-center justify-between border-b border-scada-750 pb-4">
        <div>
          <h2 className="text-xl font-extrabold text-slate-100 flex items-center gap-2.5">
            <AlertOctagon className="w-6 h-6 text-neon-rose" />
            Machine Safety Alarms, Interlocks & Diagnostics Hub
          </h2>
          <p className="text-xs text-slate-400 font-mono">
            Real-time safety interlock trip monitor, hydraulic pressure warnings, and fault resolution guides.
          </p>
        </div>

        <button
          onClick={fetchAlarmsData}
          className="scada-btn-secondary px-4 py-2 text-xs font-mono"
        >
          <RefreshCw className={`w-4 h-4 ${loading ? 'animate-spin' : ''}`} />
          REFRESH ALARM AUDIT
        </button>
      </div>

      {/* Active Triggered Alarms Banner */}
      <div className="scada-panel p-6 space-y-4 border-rose-500/40 bg-gradient-to-br from-scada-900 via-scada-900 to-rose-950/30">
        <div className="flex items-center justify-between">
          <h3 className="text-sm font-extrabold text-slate-100 uppercase tracking-wider font-mono flex items-center gap-2.5">
            <ShieldAlert className="w-5 h-5 text-neon-rose" />
            ACTIVE MACHINE FAULTS & TRIPS ({activeAlarms.length})
          </h3>
          {activeAlarms.length === 0 && (
            <span className="flex items-center gap-1.5 text-xs text-neon-emerald font-mono font-bold bg-emerald-950/80 px-3 py-1 rounded-lg border border-emerald-500/40">
              <CheckCircle className="w-4 h-4" /> ALL SAFETY INTERLOCKS NORMAL
            </span>
          )}
        </div>

        {activeAlarms.length > 0 ? (
          <div className="space-y-3">
            {activeAlarms.map((alarm) => (
              <div
                key={alarm.id}
                className="p-4 rounded-xl bg-rose-950/50 border border-rose-500/80 flex items-center justify-between font-mono shadow-neon-rose"
              >
                <div className="space-y-1.5">
                  <div className="flex items-center gap-3">
                    {getSeverityBadge(alarm.severity)}
                    <span className="font-extrabold text-sm text-slate-100">{alarm.alarmName}</span>
                    <span className="text-xs text-neon-rose font-bold">({alarm.alarmCode})</span>
                  </div>
                  <p className="text-xs text-slate-300">{alarm.description}</p>
                </div>

                <button
                  onClick={() => handleAcknowledge(alarm)}
                  className="scada-btn-primary px-5 py-2 text-xs font-mono"
                >
                  ACKNOWLEDGE
                </button>
              </div>
            ))}
          </div>
        ) : (
          <div className="p-8 text-center text-slate-500 font-mono text-xs">
            No active safety trips or machine alarms detected on Innovance PLC gateway.
          </div>
        )}
      </div>

      {/* Configured Alarm Rules & Troubleshooting Matrix */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Definitions & Corrective Actions */}
        <div className="scada-panel p-6 space-y-4">
          <h3 className="text-sm font-extrabold text-slate-100 uppercase tracking-wider font-mono flex items-center gap-2">
            <Wrench className="w-4 h-4 text-cyan-400" />
            Safety Interlock Matrix & Corrective Actions
          </h3>

          <div className="space-y-3 max-h-80 overflow-y-auto pr-1">
            {definitions.map((def) => (
              <div
                key={def.id}
                className="p-3.5 rounded-xl bg-scada-950 border border-scada-750 text-xs font-mono space-y-2"
              >
                <div className="flex items-center justify-between">
                  <span className="font-extrabold text-slate-200">{def.alarmName}</span>
                  {getSeverityBadge(def.severity)}
                </div>
                <div className="text-[11px] text-slate-400">{def.description}</div>
                {def.correctiveAction && (
                  <div className="text-[11px] text-neon-cyan bg-cyan-950/60 p-2.5 rounded-lg border border-cyan-500/40">
                    🔧 FIX: {def.correctiveAction}
                  </div>
                )}
              </div>
            ))}
          </div>
        </div>

        {/* Historical Alarm Log */}
        <div className="scada-panel p-6 space-y-4">
          <h3 className="text-sm font-extrabold text-slate-100 uppercase tracking-wider font-mono flex items-center justify-between">
            <span className="flex items-center gap-2">
              <Clock className="w-4 h-4 text-slate-400" /> Historical Alarm Audit Trail
            </span>
            <span className="text-slate-400 text-xs">{logs.length} Logged Events</span>
          </h3>

          <div className="max-h-80 overflow-y-auto">
            <table className="w-full text-left text-xs font-mono">
              <thead className="bg-scada-950 text-slate-400 sticky top-0 border-b border-scada-750">
                <tr>
                  <th className="p-2.5 pl-3">Code</th>
                  <th className="p-2.5">Name</th>
                  <th className="p-2.5">Severity</th>
                  <th className="p-2.5 pr-3 text-right">Timestamp</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-scada-750/40">
                {logs.map((log) => (
                  <tr key={log.id} className="hover:bg-scada-850/60 transition-all">
                    <td className="p-2.5 pl-3 font-bold text-slate-300">{log.alarmCode}</td>
                    <td className="p-2.5 text-slate-200 truncate max-w-[150px]">{log.alarmName}</td>
                    <td className="p-2.5">{getSeverityBadge(log.severity)}</td>
                    <td className="p-2.5 pr-3 text-right text-slate-400 text-[10px]">
                      {new Date(log.triggeredAt).toLocaleTimeString()}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
};
