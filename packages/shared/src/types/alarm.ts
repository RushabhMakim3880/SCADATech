export type AlarmSeverity = 'INFO' | 'WARNING' | 'CRITICAL' | 'EMERGENCY';

export interface AlarmDefinition {
  id: string;
  alarmCode: string; // e.g. "ALM_001"
  alarmName: string;
  description: string;
  severity: AlarmSeverity;
  triggerTagAddress: string;
  expectedValue: boolean | number;
  correctiveAction?: string;
  isActive: boolean;
}

export interface ActiveAlarm {
  id: string;
  alarmCode: string;
  alarmName: string;
  description: string;
  severity: AlarmSeverity;
  triggeredAt: string;
  acknowledgedAt?: string;
  acknowledgedBy?: string;
}

export interface AlarmHistoryEntry extends ActiveAlarm {
  clearedAt: string;
  durationSeconds: number;
}
