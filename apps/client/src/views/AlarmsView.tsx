import React, { useEffect, useState } from 'react';
import { usePlcStore } from '../stores/usePlcStore.js';
import { AlarmDefinition, ActiveAlarm } from '@innovance-hmi/shared';
import { DataTable, Column } from '../components/common/DataTable.js';
import {
  RefreshCw,
  AlertTriangle,
  CheckCircle2,
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

  const defColumns: Column<AlarmDefinition>[] = [
    {
      key: 'alarmCode',
      header: 'Alarm Code',
      render: (d) => <span className="font-mono font-bold text-slate-800">{d.alarmCode}</span>,
    },
    { key: 'alarmName', header: 'Alarm Description' },
    {
      key: 'severity',
      header: 'Severity',
      render: (d) => (
        <span
          className={`px-2 py-0.5 rounded text-[11px] font-bold ${
            d.severity === 'EMERGENCY'
              ? 'bg-red-100 text-red-800'
              : d.severity === 'WARNING'
              ? 'bg-orange-100 text-orange-800'
              : 'bg-blue-100 text-blue-800'
          }`}
        >
          {d.severity}
        </span>
      ),
    },
    {
      key: 'correctiveAction',
      header: 'Corrective Action Guidance',
      render: (d) => <span className="text-slate-600 text-xs">{d.correctiveAction || '--'}</span>,
    },
  ];

  const logColumns: Column<AlarmLogItem>[] = [
    {
      key: 'alarmCode',
      header: 'Code',
      width: '90px',
      render: (l) => <span className="font-mono font-bold">{l.alarmCode}</span>,
    },
    { key: 'alarmName', header: 'Event Message' },
    {
      key: 'severity',
      header: 'Severity',
      render: (l) => <span className="text-xs font-semibold">{l.severity}</span>,
    },
    {
      key: 'triggeredAt',
      header: 'Triggered Timestamp',
      render: (l) => (
        <span className="font-mono text-slate-600 text-xs">
          {new Date(l.triggeredAt).toLocaleString()}
        </span>
      ),
    },
  ];

  return (
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* Top Header */}
      <div className="flex items-center justify-between pb-2 border-b border-slate-300">
        <div>
          <h2 className="text-lg font-bold text-slate-800">Alarm Configuration & Event Logs (AalarmModules)</h2>
          <p className="text-xs text-slate-500">
            Real-time machine fault monitoring, safety trip acknowledgment, and historical audit logs.
          </p>
        </div>

        <button onClick={fetchAlarmsData} className="btn-ca btn-ca-default">
          <RefreshCw className={`w-3.5 h-3.5 ${loading ? 'animate-spin' : ''}`} /> Refresh Logs
        </button>
      </div>

      {/* Active Triggered Alarms Banner */}
      <div className="panel">
        <div className="panel-heading bg-red-700 text-white">
          <div className="flex items-center gap-2">
            <AlertTriangle className="w-4 h-4" />
            <span>Active Machine Alarms ({activeAlarms.length})</span>
          </div>
          {activeAlarms.length === 0 && (
            <span className="text-xs bg-emerald-700 text-white px-2 py-0.5 rounded font-bold flex items-center gap-1">
              <CheckCircle2 className="w-3.5 h-3.5" /> All Safety Interlocks Normal
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

      {/* Alarm Definitions & Event History DataTables */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <DataTable
          title="Configured Alarm Definitions"
          columns={defColumns}
          data={definitions}
          searchKeys={['alarmCode', 'alarmName']}
        />

        <DataTable
          title="Historical Alarm Audit Event Logs"
          columns={logColumns}
          data={logs}
          searchKeys={['alarmCode', 'alarmName']}
        />
      </div>
    </div>
  );
};
