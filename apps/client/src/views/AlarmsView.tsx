import React, { useEffect, useState } from 'react';
import { usePlcStore } from '../stores/usePlcStore.js';
import { AlarmDefinition, ActiveAlarm } from '@innovance-hmi/shared';
import {
  RefreshCw,
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

  return (
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* Top Header */}
      <div className="flex items-center justify-between pb-2 border-b border-slate-300">
        <div>
          <h2 className="text-lg font-bold text-slate-800">Alarm Configuration & Historical Event Logs (AalarmModules)</h2>
          <p className="text-xs text-slate-500">
            Real-time machine fault monitoring, safety trip acknowledgment, and maintenance logs.
          </p>
        </div>

        <button
          onClick={fetchAlarmsData}
          className="btn-ca btn-ca-default"
        >
          <RefreshCw className={`w-3.5 h-3.5 ${loading ? 'animate-spin' : ''}`} /> Refresh Logs
        </button>
      </div>

      {/* Active Triggered Alarms Banner */}
      <div className="panel">
        <div className="panel-heading bg-red-700 text-white">
          <span>Active Machine Alarms ({activeAlarms.length})</span>
          {activeAlarms.length === 0 && (
            <span className="text-xs bg-emerald-700 text-white px-2 py-0.5 rounded font-bold">
              All Interlocks Normal
            </span>
          )}
        </div>

        <div className="panel-body">
          {activeAlarms.length > 0 ? (
            <div className="space-y-2">
              {activeAlarms.map((alarm) => (
                <div
                  key={alarm.id}
                  className="p-3 rounded bg-red-50 border border-red-300 flex items-center justify-between"
                >
                  <div>
                    <div className="flex items-center gap-2">
                      <span className="badge bg-red-600 text-white px-2 py-0.5 rounded text-xs font-bold">
                        {alarm.severity}
                      </span>
                      <span className="font-bold text-sm text-red-900">{alarm.alarmName}</span>
                      <span className="text-xs text-red-700">({alarm.alarmCode})</span>
                    </div>
                    <p className="text-xs text-slate-600 mt-1">{alarm.description}</p>
                  </div>

                  <button
                    onClick={() => handleAcknowledge(alarm)}
                    className="btn-ca btn-ca-primary"
                  >
                    Acknowledge
                  </button>
                </div>
              ))}
            </div>
          ) : (
            <div className="p-4 text-center text-slate-500 text-xs">
              No active alarms or safety interlock trips detected on the machine bus.
            </div>
          )}
        </div>
      </div>

      {/* Alarm Definitions & History Tables */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        {/* Alarm Definitions */}
        <div className="panel">
          <div className="panel-heading">
            <span>Configured Alarm Definitions & Actions</span>
          </div>

          <div className="panel-body p-0 max-h-72 overflow-y-auto">
            <table className="table-custom">
              <thead>
                <tr>
                  <th>Code</th>
                  <th>Alarm Name</th>
                  <th>Severity</th>
                  <th>Corrective Action</th>
                </tr>
              </thead>
              <tbody>
                {definitions.map((def) => (
                  <tr key={def.id}>
                    <td className="font-bold text-slate-800">{def.alarmCode}</td>
                    <td>{def.alarmName}</td>
                    <td>
                      <span className="badge bg-slate-200 text-slate-700 px-2 py-0.5 rounded text-[11px]">
                        {def.severity}
                      </span>
                    </td>
                    <td className="text-xs text-slate-600">{def.correctiveAction || '--'}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        {/* Historical Alarm Log */}
        <div className="panel">
          <div className="panel-heading">
            <span>Historical Alarm Event Logs</span>
          </div>

          <div className="panel-body p-0 max-h-72 overflow-y-auto">
            <table className="table-custom">
              <thead>
                <tr>
                  <th>Code</th>
                  <th>Name</th>
                  <th>Severity</th>
                  <th>Triggered Time</th>
                </tr>
              </thead>
              <tbody>
                {logs.map((log) => (
                  <tr key={log.id}>
                    <td className="font-bold text-slate-800">{log.alarmCode}</td>
                    <td>{log.alarmName}</td>
                    <td>{log.severity}</td>
                    <td className="text-xs text-slate-500">{new Date(log.triggeredAt).toLocaleTimeString()}</td>
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
