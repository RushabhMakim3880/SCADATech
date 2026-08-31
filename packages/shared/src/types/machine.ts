export type SideType = 'A' | 'B' | 'NA';

export type HeadType = 'PUNCHING' | 'MARKING' | 'CUTTING' | 'DRILLING' | 'NOTCHING';

export interface MachineDetail {
  id: string;
  machineId: string;
  headName: string; // e.g. "DA1", "DA2", "DA3", "DB1", "DB2", "DB3", "Marking", "Cutter"
  headType: HeadType;
  xPosition: number; // offset in mm along machine bed
  side: SideType;
  markingCassettes?: number;
  toolSize?: number; // active installed die/punch tool diameter in mm (e.g. 14, 18, 22, 26)
  toolShape?: 'ROUND' | 'OBLONG' | 'SQUARE';
  maxToolSize?: number;
  isActive: boolean;
}

export interface MachineConfig {
  id: string;
  machineCode: string;
  machineName: string;
  machineType: 'SKIPPER' | 'STANDARD' | 'OTHER';
  headCount: number;
  minAngleSize: number; // e.g. 40mm
  maxAngleSize: number; // e.g. 200mm
  minThickness: number; // e.g. 3mm
  maxThickness: number; // e.g. 20mm
  maxBarLength: number; // e.g. 12000mm
  details: MachineDetail[];
  isActive: boolean;
}

export interface MachineSetupUpdate {
  machineDetailId: string;
  toolSize?: number;
  toolShape?: 'ROUND' | 'OBLONG' | 'SQUARE';
  markingCassetteValues?: Record<number, string>;
}
