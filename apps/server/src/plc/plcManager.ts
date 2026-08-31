import { OpcUaService } from './opcuaService.js';
import { SimulatorService } from './simulatorService.js';
import { AlarmManager } from '../services/alarmManager.js';
import { TagValueUpdate, TagDataType, WsServerMessage } from '@innovance-hmi/shared';
import { prisma } from '../db/prisma.js';

export class PlcManager {
  private static instance: PlcManager;
  private opcua: OpcUaService;
  private simulator: SimulatorService;
  public alarmManager: AlarmManager;
  private isSimulator = true;
  private wsBroadcastCallback: ((msg: WsServerMessage) => void) | null = null;
  private recentTagValues = new Map<string, TagValueUpdate>();

  private constructor() {
    this.opcua = new OpcUaService();
    this.simulator = new SimulatorService();
    this.alarmManager = new AlarmManager();

    // Bind tag handlers
    this.simulator.onTagChange((update) => this.handleTagUpdate(update));
    this.opcua.onTagChange((update) => this.handleTagUpdate(update));

    this.alarmManager.onAlarmsChange((alarms) => {
      this.broadcast({
        type: 'ACTIVE_ALARMS',
        timestamp: Date.now(),
        payload: alarms,
      });
    });
  }

  public static getInstance(): PlcManager {
    if (!PlcManager.instance) {
      PlcManager.instance = new PlcManager();
    }
    return PlcManager.instance;
  }

  public setWsBroadcast(fn: (msg: WsServerMessage) => void) {
    this.wsBroadcastCallback = fn;
  }

  public async init(isSimulator: boolean = true, endpointUrl?: string): Promise<void> {
    this.isSimulator = isSimulator;

    if (this.isSimulator) {
      console.log('🤖 Starting High-Fidelity PLC Simulator...');
      this.simulator.start();
    } else if (endpointUrl) {
      console.log(`🔌 Connecting to physical Innovance PLC at ${endpointUrl}...`);
      const connected = await this.opcua.connect(endpointUrl);
      if (connected) {
        // Load tags from DB and subscribe
        const tags = await prisma.plcTag.findMany();
        await this.opcua.subscribeToTags(
          tags.map((t) => ({
            tagId: t.id,
            tagAddress: t.tagAddress,
            tagName: t.tagName,
            dataType: t.dataType as TagDataType,
          }))
        );
      }
    }
  }

  public async writeTag(tagName: string, value: any, dataType?: TagDataType): Promise<boolean> {
    if (this.isSimulator) {
      this.simulator.writeTag(tagName, value);
      return true;
    } else {
      const tag = await prisma.plcTag.findFirst({ where: { tagName } });
      if (tag) {
        return this.opcua.writeTag(tag.tagAddress, value, (dataType || tag.dataType) as TagDataType);
      }
      return false;
    }
  }

  public triggerJog(direction: 'FWD' | 'REV', active: boolean, speedMmPerSec?: number): void {
    if (this.isSimulator) {
      this.simulator.setJog(direction, active, speedMmPerSec);
    } else {
      const tagName = direction === 'FWD' ? 'Feed_Axis_Jog_Forward' : 'Feed_Axis_Jog_Reverse';
      this.writeTag(tagName, active, 'Boolean');
    }
  }

  public toggleValve(valveName: 'infeed' | 'carriage' | 'outfeed' | 'pump'): boolean {
    if (this.isSimulator) {
      if (valveName === 'pump') return this.simulator.toggleHydraulicPump();
      return this.simulator.toggleClamp(valveName);
    }
    return false;
  }

  public triggerHead(headName: string): void {
    if (this.isSimulator) {
      this.simulator.triggerHead(headName);
    } else {
      this.writeTag(`Head_${headName}_Punch_Trigger`, true, 'Boolean');
    }
  }

  private handleTagUpdate(update: TagValueUpdate): void {
    this.recentTagValues.set(update.tagName, update);
    this.alarmManager.evaluateTag(update);

    // Batch broadcast
    this.broadcast({
      type: 'TAG_UPDATES',
      timestamp: Date.now(),
      payload: [update],
    });
  }

  private broadcast(msg: WsServerMessage): void {
    this.wsBroadcastCallback?.(msg);
  }

  public getStatus() {
    return {
      connected: this.isSimulator ? true : this.opcua.getConnected(),
      isSimulator: this.isSimulator,
      protocol: this.isSimulator ? 'VIRTUAL_SIMULATOR' : 'OPC-UA',
      activeAlarms: this.alarmManager.getActiveAlarms(),
    };
  }
}
