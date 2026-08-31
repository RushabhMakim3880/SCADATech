import { create } from 'zustand';
import { TagValueUpdate, LiveProductionStatus, ActiveAlarm } from '@innovance-hmi/shared';

interface PlcState {
  isConnected: boolean;
  isSimulator: boolean;
  mode: 'MANUAL' | 'AUTO' | 'SEMI_AUTO' | 'STEP';
  
  // Real-time DRO Values
  feedPositionMm: number;
  feedTargetMm: number;
  feedSpeedMPerMin: number;
  hydraulicPressureBar: number;
  hydraulicPumpRunning: boolean;
  
  // Clamps & Cylinders
  infeedClamp: boolean;
  carriageClamp: boolean;
  outfeedClamp: boolean;
  
  // Head Firing States
  headsFiring: Record<string, boolean>; // e.g. { DA1: false, DA2: false, DB1: false, Marking: false, Cutter: false }
  
  // Safety Interlocks
  eStopOk: boolean;
  guardsOk: boolean;

  // Active Alarms & Production
  activeAlarms: ActiveAlarm[];
  productionStatus?: LiveProductionStatus;

  // Actions
  setConnected: (status: boolean, isSim?: boolean) => void;
  setMode: (mode: 'MANUAL' | 'AUTO' | 'SEMI_AUTO' | 'STEP') => void;
  updateTag: (update: TagValueUpdate) => void;
  batchUpdateTags: (updates: Record<string, any>) => void;
  setAlarms: (alarms: ActiveAlarm[]) => void;
  setProductionStatus: (status: LiveProductionStatus) => void;
}

export const usePlcStore = create<PlcState>((set) => ({
  isConnected: false,
  isSimulator: true,
  mode: 'MANUAL',
  
  feedPositionMm: 0.0,
  feedTargetMm: 0.0,
  feedSpeedMPerMin: 0.0,
  hydraulicPressureBar: 140.0,
  hydraulicPumpRunning: false,
  
  infeedClamp: false,
  carriageClamp: false,
  outfeedClamp: false,
  
  headsFiring: {
    DA1: false,
    DA2: false,
    DA3: false,
    DB1: false,
    DB2: false,
    DB3: false,
    Marking: false,
    Cutter: false,
  },
  
  eStopOk: true,
  guardsOk: true,
  activeAlarms: [],

  setConnected: (status, isSim = true) => set({ isConnected: status, isSimulator: isSim }),
  setMode: (mode) => set({ mode }),
  
  updateTag: (update) => {
    set((state) => {
      // Map tag name or address to state properties
      if (update.tagName.includes('Feed_Axis_Current_Position')) {
        return { feedPositionMm: Number(update.value) };
      }
      if (update.tagName.includes('Hydraulic_Pressure_Bar')) {
        return { hydraulicPressureBar: Number(update.value) };
      }
      if (update.tagName.includes('Hydraulic_Pump_Running')) {
        return { hydraulicPumpRunning: Boolean(update.value) };
      }
      if (update.tagName.includes('Infeed_Clamp')) {
        return { infeedClamp: Boolean(update.value) };
      }
      if (update.tagName.includes('Emergency_Stop_OK')) {
        return { eStopOk: Boolean(update.value) };
      }
      return state;
    });
  },

  batchUpdateTags: (updates) => set((state) => ({ ...state, ...updates })),
  setAlarms: (alarms) => set({ activeAlarms: alarms }),
  setProductionStatus: (status) => set({ productionStatus: status }),
}));
