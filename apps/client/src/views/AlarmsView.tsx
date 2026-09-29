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
      render: (d) => <span className="font-mono font-bold text-sky-400">{d.alarmCode}</span>,
    },
    { key: 'alarmName', header: 'Alarm Description' },
    {
      key: 'severity',
      header: 'Severity',
      render: (d) => (
        <span
          className={`px-2.5 py-1 rounded-lg text-[10px] font-black tracking-wider uppercase border ${
            d.severity === 'EMERGENCY'
              ? 'bg-rose-950/80 text-rose-300 border-rose-600 shadow-[0_0_8px_rgba(244,63,94,0.3)]'
              : d.severity === 'WARNING'
              ? 'bg-amber-950/80 text-amber-300 border-amber-600'
              : 'bg-sky-950/80 text-sky-300 border-sky-600'
          }`}
        >
          {d.severity}
        </span>
      ),
    },
    {
      key: 'correctiveAction',
      header: 'Corrective Action Guidance',
      render: (d) => <span className="text-slate-400 text-xs font-medium">{d.correctiveAction || '--'}</span>,
    },
  ];

  const logColumns: Column<AlarmLogItem>[] = [
    {
      key: 'alarmCode',
      header: 'Code',
      width: '90px',
      render: (l) => <span className="font-mono font-bold text-sky-400">{l.alarmCode}</span>,
    },
    { key: 'alarmName', header: 'Event Message' },
    {
      key: 'severity',
      header: 'Severity',
      render: (l) => (
        <span className="text-xs font-bold text-slate-300">{l.severity}</span>
      ),
    },
    {
      key: 'triggeredAt',
      header: 'Triggered Timestamp',
      render: (l) => (
        <span className="font-mono text-slate-400 text-xs">
          {new Date(l.triggeredAt).toLocaleString()}
        </span>
      ),
    },
  ];

  return (
    <div className="p-4 md:p-6 space-y-4 flex-1 overflow-y-auto bg-[#070b12] text-white">
      {/* Top Header */}
      <div className="bg-[#0e1420] border border-[#1e2a3c] rounded-2xl p-4 shadow-xl flex flex-wrap items-center justify-between gap-4">
        <div>
          <h2 className="text-xl font-black text-white tracking-wide">
            Alarm Configuration & Event Logs
          </h2>
          <p className="text-xs text-slate-400 font-medium mt-1">
            Real-time machine fault monitoring, safety trip acknowledgment, and historical audit logs.
          </p>
        </div>

        <button onClick={fetchAlarmsData} className="cnc-btn cnc-btn-secondary text-xs font-black">
          <RefreshCw className={`w-4 h-4 text-sky-400 ${loading ? 'animate-spin' : ''}`} />
          <span>REFRESH LOGS</span>
        </button>
      </div>

      {/* Active Triggered Alarms Banner */}
      <div className="cnc-card overflow-hidden">
        <div className="cnc-card-header bg-[#160f14] border-b border-rose-950/60">
          <div className="flex items-center gap-2.5">
            <AlertTriangle className="w-5 h-5 text-rose-500 animate-pulse" />
            <span className="text-rose-200 font-black">Active Machine Alarms ({activeAlarms.length})</span>
          </div>
          {activeAlarms.length === 0 && (
            <span className="text-xs bg-emerald-950/80 text-emerald-300 border border-emerald-600 px-3 py-1 rounded-xl font-black flex items-center gap-1.5 shadow-[0_0_8px_rgba(16,185,129,0.2)]">
              <CheckCircle2 className="w-4 h-4 text-emerald-400" />
              <span>All Safety Interlocks Normal</span>
            </span>
          )}
        </div>

        <div className="cnc-card-body">
          {activeAlarms.length > 0 ? (
            <div className="space-y-3">
              {activeAlarms.map((alarm) => (
                <div
                  key={alarm.id}
                  className="p-4 rounded-xl bg-gradient-to-r from-rose-950/50 to-[#181116] border border-rose-800/80 flex flex-wrap items-center justify-between gap-3 shadow-lg"
                >
                  <div>
                    <div className="flex items-center gap-2.5">
                      <span className="bg-rose-600 text-white px-2.5 py-0.5 rounded-lg text-xs font-black shadow-sm">
                        {alarm.severity}
                      </span>
                      <span className="font-black text-sm text-white">{alarm.alarmName}</span>
                      <span className="text-xs font-mono text-rose-400 font-bold">({alarm.alarmCode})</span>
                    </div>
                    <p className="text-xs text-slate-300 mt-1 font-medium">{alarm.description}</p>
                  </div>

                  <button
                    onClick={() => handleAcknowledge(alarm)}
                    className="cnc-btn cnc-btn-danger text-xs font-black"
                  >
                    ACKNOWLEDGE
                  </button>
                </div>
              ))}
            </div>
          ) : (
            <div className="p-6 text-center text-slate-400 text-xs font-medium">
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
