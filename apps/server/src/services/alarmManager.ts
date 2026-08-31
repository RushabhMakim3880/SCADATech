import { TagValueUpdate, ActiveAlarm, AlarmSeverity } from '@innovance-hmi/shared';
import { prisma } from '../db/prisma.js';

export class AlarmManager {
  private activeAlarmsMap = new Map<string, ActiveAlarm>();
  private alarmListeners: Array<(alarms: ActiveAlarm[]) => void> = [];

  constructor() {}

  public onAlarmsChange(listener: (alarms: ActiveAlarm[]) => void) {
    this.alarmListeners.push(listener);
  }

  public async evaluateTag(update: TagValueUpdate): Promise<void> {
    // Check if tag is in alarm definition
    const definitions = await prisma.alarmDefinition.findMany({
      where: {
        isActive: true,
        triggerTagAddress: {
          contains: update.tagName,
        },
      },
    });

    let stateChanged = false;

    for (const def of definitions) {
      const isAlarmTriggered = this.checkTrigger(def.expectedValue, update.value);

      if (isAlarmTriggered && !this.activeAlarmsMap.has(def.alarmCode)) {
        // Trigger new alarm
        const activeAlarm: ActiveAlarm = {
          id: def.id,
          alarmCode: def.alarmCode,
          alarmName: def.alarmName,
          description: def.description,
          severity: def.severity as AlarmSeverity,
          triggeredAt: new Date().toISOString(),
        };

        this.activeAlarmsMap.set(def.alarmCode, activeAlarm);
        stateChanged = true;

        // Log into database
        await prisma.alarmLog.create({
          data: {
            alarmDefinitionId: def.id,
            alarmCode: def.alarmCode,
            alarmName: def.alarmName,
            severity: def.severity,
          },
        });
      } else if (!isAlarmTriggered && this.activeAlarmsMap.has(def.alarmCode)) {
        // Alarm cleared
        this.activeAlarmsMap.delete(def.alarmCode);
        stateChanged = true;
      }
    }

    if (stateChanged) {
      const currentAlarms = Array.from(this.activeAlarmsMap.values());
      this.alarmListeners.forEach((fn) => fn(currentAlarms));
    }
  }

  private checkTrigger(expectedValue: string, actualValue: any): boolean {
    if (expectedValue === 'true' || expectedValue === 'false') {
      return String(actualValue).toLowerCase() !== expectedValue.toLowerCase();
    }
    const numExpected = parseFloat(expectedValue);
    const numActual = parseFloat(actualValue);
    if (!isNaN(numExpected) && !isNaN(numActual)) {
      return numActual < numExpected;
    }
    return false;
  }

  public getActiveAlarms(): ActiveAlarm[] {
    return Array.from(this.activeAlarmsMap.values());
  }
}
