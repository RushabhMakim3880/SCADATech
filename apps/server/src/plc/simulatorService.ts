import { TagValueUpdate } from '@innovance-hmi/shared';

export class SimulatorService {
  private isRunning = false;
  private timer: NodeJS.Timeout | null = null;
  private tagChangeCallback: ((update: TagValueUpdate) => void) | null = null;

  // Simulated Machine States
  private feedPos = 0.0;
  private targetFeedPos = 0.0;
  private feedSpeed = 0.0;
  private isJoggingFwd = false;
  private isJoggingRev = false;
  private jogVelocity = 0.0;

  private hydPump = true;
  private hydPressure = 145.0;

  private infeedClamp = true;
  private carriageClamp = true;
  private outfeedClamp = false;

  private eStopOk = true;
  private guardsOk = true;

  private firingHeads: Record<string, boolean> = {
    DA1: false,
    DA2: false,
    DA3: false,
    DB1: false,
    DB2: false,
    DB3: false,
    Marking: false,
    Cutter: false,
  };

  constructor() {}

  public onTagChange(callback: (update: TagValueUpdate) => void) {
    this.tagChangeCallback = callback;
  }

  public start(): void {
    if (this.isRunning) return;
    this.isRunning = true;

    // 50ms simulation tick loop (20Hz)
    this.timer = setInterval(() => {
      this.tick();
    }, 50);
  }

  public stop(): void {
    if (this.timer) {
      clearInterval(this.timer);
      this.timer = null;
    }
    this.isRunning = false;
  }

  public setJog(direction: 'FWD' | 'REV', active: boolean, speedMmPerSec: number = 50.0): void {
    if (direction === 'FWD') {
      this.isJoggingFwd = active;
      this.jogVelocity = active ? speedMmPerSec : 0;
    } else {
      this.isJoggingRev = active;
      this.jogVelocity = active ? -speedMmPerSec : 0;
    }
  }

  public setTargetPosition(targetMm: number): void {
    this.targetFeedPos = Math.max(0, targetMm);
  }

  public toggleClamp(clamp: 'infeed' | 'carriage' | 'outfeed'): boolean {
    if (clamp === 'infeed') {
      this.infeedClamp = !this.infeedClamp;
      this.emitTag('Infeed_Clamp_Engaged', this.infeedClamp);
      return this.infeedClamp;
    }
    if (clamp === 'carriage') {
      this.carriageClamp = !this.carriageClamp;
      this.emitTag('Carriage_Clamp_Engaged', this.carriageClamp);
      return this.carriageClamp;
    }
    if (clamp === 'outfeed') {
      this.outfeedClamp = !this.outfeedClamp;
      this.emitTag('Outfeed_Clamp_Engaged', this.outfeedClamp);
      return this.outfeedClamp;
    }
    return false;
  }

  public toggleHydraulicPump(): boolean {
    this.hydPump = !this.hydPump;
    this.emitTag('Hydraulic_Pump_Running', this.hydPump);
    return this.hydPump;
  }

  public triggerHead(headName: string): void {
    if (this.firingHeads[headName] !== undefined) {
      this.firingHeads[headName] = true;
      this.emitTag(`Head_${headName}_Punch_Trigger`, true);

      // Hydraulic pressure momentarily dips when cylinder extends
      this.hydPressure = Math.max(90, this.hydPressure - 25);

      setTimeout(() => {
        this.firingHeads[headName] = false;
        this.emitTag(`Head_${headName}_Punch_Trigger`, false);
      }, 350);
    }
  }

  public writeTag(tagName: string, value: any): void {
    if (tagName.includes('Jog_Forward')) {
      this.setJog('FWD', Boolean(value));
    } else if (tagName.includes('Jog_Reverse')) {
      this.setJog('REV', Boolean(value));
    } else if (tagName.includes('Target_Position')) {
      this.setTargetPosition(Number(value));
    } else if (tagName.includes('Hydraulic_Pump')) {
      this.hydPump = Boolean(value);
    } else if (tagName.includes('Infeed_Clamp')) {
      this.infeedClamp = Boolean(value);
    } else if (tagName.includes('Carriage_Clamp')) {
      this.carriageClamp = Boolean(value);
    } else if (tagName.includes('Outfeed_Clamp')) {
      this.outfeedClamp = Boolean(value);
    } else if (tagName.includes('Head_DA1')) {
      this.triggerHead('DA1');
    } else if (tagName.includes('Head_DA2')) {
      this.triggerHead('DA2');
    } else if (tagName.includes('Head_DA3')) {
      this.triggerHead('DA3');
    } else if (tagName.includes('Head_DB1')) {
      this.triggerHead('DB1');
    } else if (tagName.includes('Head_DB2')) {
      this.triggerHead('DB2');
    } else if (tagName.includes('Head_DB3')) {
      this.triggerHead('DB3');
    } else if (tagName.includes('Marking_Trigger')) {
      this.triggerHead('Marking');
    } else if (tagName.includes('Shear_Cut')) {
      this.triggerHead('Cutter');
    }
  }

  private tick(): void {
    const dt = 0.05; // 50ms

    // 1. Motion Simulation
    if (this.isJoggingFwd || this.isJoggingRev) {
      this.feedPos += this.jogVelocity * dt;
      this.feedSpeed = (this.jogVelocity * 60) / 1000; // m/min
    } else if (Math.abs(this.targetFeedPos - this.feedPos) > 0.05) {
      const dir = Math.sign(this.targetFeedPos - this.feedPos);
      const maxSpeed = 150.0; // mm/s
      const dist = Math.abs(this.targetFeedPos - this.feedPos);
      const step = Math.min(dist, maxSpeed * dt);
      this.feedPos += dir * step;
      this.feedSpeed = (dir * step * 60) / (dt * 1000);
    } else {
      this.feedSpeed = 0;
    }

    if (this.feedPos < 0) this.feedPos = 0;

    // 2. Hydraulic Pressure Simulation
    if (this.hydPump) {
      const targetP = 145.0 + (Math.random() * 2.0 - 1.0); // minor jitter
      this.hydPressure += (targetP - this.hydPressure) * 0.1;
    } else {
      this.hydPressure = Math.max(0, this.hydPressure - 1.5);
    }

    // 3. Emit live values
    this.emitTag('Feed_Axis_Current_Position', Number(this.feedPos.toFixed(2)));
    this.emitTag('Feed_Axis_Current_Speed', Number(this.feedSpeed.toFixed(2)));
    this.emitTag('Hydraulic_Pressure_Bar', Number(this.hydPressure.toFixed(1)));
    this.emitTag('Hydraulic_Pump_Running', this.hydPump);
    this.emitTag('Emergency_Stop_OK', this.eStopOk);
    this.emitTag('Safety_Guards_Closed', this.guardsOk);
  }

  private emitTag(tagName: string, value: any): void {
    if (this.tagChangeCallback) {
      this.tagChangeCallback({
        tagId: tagName,
        tagName: tagName,
        value: value,
        timestamp: Date.now(),
        quality: 'GOOD',
      });
    }
  }
}
