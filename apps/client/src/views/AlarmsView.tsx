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
      // Remove from active store
      setAlarms(activeAlarms.filter((a) => a.alarmCode !== alarm.alarmCode));
    } catch (err) {
      console.error('Failed to acknowledge alarm', err);
    }
  };

  const getSeverityBadge = (severity: string) => {
    switch (severity) {
      case 'EMERGENCY':
        return (
          <span className="px-2.5 py-1 rounded bg-rose-950 text-rose-300 border border-rose-700 font-bold flex items-center gap-1">
            <ShieldAlert className="w-3.5 h-3.5" /> EMERGENCY
          </span>
        );
      case 'CRITICAL':
        return (
          <span className="px-2.5 py-1 rounded bg-rose-950/70 text-rose-400 border border-rose-800 font-bold flex items-center gap-1">
            <AlertOctagon className="w-3.5 h-3.5" /> CRITICAL
          </span>
        );
      case 'WARNING':
        return (
          <span className="px-2.5 py-1 rounded bg-amber-950 text-amber-300 border border-amber-800 font-bold flex items-center gap-1">
            <AlertTriangle className="w-3.5 h-3.5" /> WARNING
          </span>
        );
      default:
        return (
          <span className="px-2.5 py-1 rounded bg-cyan-950 text-cyan-300 border border-cyan-800 font-bold flex items-center gap-1">
            <Info className="w-3.5 h-3.5" /> INFO
          </span>
        );
    }
  };

  return (
    <div className="p-6 space-y-6 flex-1 overflow-y-auto">
      {/* Header */}
      <div className="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
          <h2 className="text-xl font-bold text-slate-100 flex items-center gap-2">
            <AlertOctagon className="w-5 h-5 text-rose-400" />
            Machine Safety Alarms & Diagnostics Hub
          </h2>
          <p className="text-xs text-slate-400">
            Real-time interlock trip monitor, hydraulic pressure warnings, and historical fault logs.
          </p>
        </div>

        <button
          onClick={fetchAlarmsData}
          className="industrial-btn-secondary px-4 py-2 text-xs font-mono"
        >
          <RefreshCw className={`w-4 h-4 ${loading ? 'animate-spin' : ''}`} />
          REFRESH ALARMS
        </button>
      </div>

      {/* Active Triggered Alarms Banner */}
      <div className="industrial-card p-5 space-y-4 border-rose-900/60 bg-gradient-to-br from-slate-900 via-slate-900 to-rose-950/20">
        <div className="flex items-center justify-between">
          <h3 className="text-sm font-bold text-slate-100 uppercase tracking-wider flex items-center gap-2">
            <ShieldAlert className="w-4 h-4 text-rose-400" />
            Active Machine Faults & Trips ({activeAlarms.length})
          </h3>
          {activeAlarms.length === 0 && (
            <span className="flex items-center gap-1 text-xs text-emerald-400 font-mono">
              <CheckCircle className="w-4 h-4" /> ALL SYSTEMS NORMAL
            </span>
          )}
        </div>

        {activeAlarms.length > 0 ? (
          <div className="space-y-3">
            {activeAlarms.map((alarm) => (
              <div
                key={alarm.id}
                className="p-4 rounded-lg bg-rose-950/40 border border-rose-700/80 flex items-center justify-between font-mono"
              >
                <div className="space-y-1">
                  <div className="flex items-center gap-3">
                    {getSeverityBadge(alarm.severity)}
                    <span className="font-bold text-sm text-slate-100">{alarm.alarmName}</span>
                    <span className="text-xs text-rose-400 font-bold">({alarm.alarmCode})</span>
                  </div>
                  <p className="text-xs text-slate-300">{alarm.description}</p>
                </div>

                <button
                  onClick={() => handleAcknowledge(alarm)}
                  className="industrial-btn-primary px-4 py-2 text-xs"
                >
                  ACKNOWLEDGE
                </button>
              </div>
            ))}
          </div>
        ) : (
          <div className="p-8 text-center text-slate-500 font-mono text-xs">
            No active alarms or safety trips detected on Innovance PLC bus.
          </div>
        )}
      </div>

      {/* Configured Alarm Rules & Troubleshooting Matrix */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Definitions & Corrective Actions */}
        <div className="industrial-card p-5 space-y-4">
          <h3 className="text-sm font-bold text-slate-100 uppercase tracking-wider font-mono">
            Safety Interlock Matrix & Corrective Actions
          </h3>

          <div className="space-y-2.5 max-h-72 overflow-y-auto">
            {definitions.map((def) => (
              <div
                key={def.id}
                className="p-3 rounded-lg bg-slate-950 border border-slate-800 text-xs font-mono space-y-1"
              >
                <div className="flex items-center justify-between">
                  <span className="font-bold text-slate-200">{def.alarmName}</span>
                  {getSeverityBadge(def.severity)}
                </div>
                <div className="text-[11px] text-slate-400">{def.description}</div>
                {def.correctiveAction && (
                  <div className="text-[11px] text-cyan-400 bg-cyan-950/40 p-2 rounded border border-cyan-900/60 mt-1">
                    🔧 FIX: {def.correctiveAction}
                  </div>
                )}
              </div>
            ))}
          </div>
        </div>

        {/* Historical Alarm Log */}
        <div className="industrial-card p-5 space-y-4">
          <h3 className="text-sm font-bold text-slate-100 uppercase tracking-wider font-mono flex items-center justify-between">
            <span>Historical Alarm Audit Trail</span>
            <Clock className="w-4 h-4 text-slate-400" />
          </h3>

          <div className="max-h-72 overflow-y-auto">
            <table className="w-full text-left text-xs font-mono">
              <thead className="bg-slate-950/80 text-slate-400 sticky top-0 border-b border-slate-800">
                <tr>
                  <th className="p-2 pl-3">Code</th>
                  <th className="p-2">Name</th>
                  <th className="p-2">Severity</th>
                  <th className="p-2 pr-3 text-right">Timestamp</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800/40">
                {logs.map((log) => (
                  <tr key={log.id} className="hover:bg-slate-800/30">
                    <td className="p-2 pl-3 font-bold text-slate-300">{log.alarmCode}</td>
                    <td className="p-2 text-slate-200 truncate max-w-[140px]">{log.alarmName}</td>
                    <td className="p-2">{getSeverityBadge(log.severity)}</td>
                    <td className="p-2 pr-3 text-right text-slate-400 text-[10px]">
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
