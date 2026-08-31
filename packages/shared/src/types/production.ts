import { SideType } from './machine.js';
import { RecipeOperationType } from './recipe.js';

export type JobStatus = 'DRAFT' | 'PENDING' | 'RUNNING' | 'COMPLETED' | 'CANCELLED';

export interface JobCardItem {
  id: string;
  jobCardId: string;
  recipeId: string;
  itemCode: string;
  itemName: string;
  requiredQuantity: number;
  completedQuantity: number;
  priority: number;
}

export interface JobCard {
  id: string;
  jobCode: string;
  clientName?: string;
  targetDate?: string;
  status: JobStatus;
  items: JobCardItem[];
  createdAt: string;
  updatedAt: string;
}

export interface AlignedOperation {
  stepIndex: number;
  recipeId: string;
  itemIndexInBar: number;
  operationType: RecipeOperationType;
  side: SideType;
  absoluteBarX: number; // calculated position along the raw stock bar in mm
  yPosition: number;
  toolSize?: number;
  allocatedHeadName?: string; // e.g. "DA2", "DB1", "Marking", "Cutter"
  allocatedHeadOffset: number; // head DX in mm
  requiredFeedAxisPos: number; // absolute feed carriage position = absoluteBarX - allocatedHeadOffset
  isCutOff: boolean;
  markingText?: string;
  status: 'PENDING' | 'IN_PROGRESS' | 'COMPLETED' | 'SKIPPED';
}

export interface ProgramCyclePlan {
  cycleId: string;
  stockBarLength: number; // e.g. 6000mm, 9000mm, 12000mm
  utilizedLength: number;
  scrapLength: number;
  itemsSummary: Array<{
    recipeId: string;
    itemCode: string;
    count: number;
  }>;
  operationsSequence: AlignedOperation[];
  estimatedCycleTimeSec: number;
}

export interface LiveProductionStatus {
  activeCycleId?: string;
  cycleState: 'IDLE' | 'READY' | 'FEEDING' | 'PUNCHING' | 'MARKING' | 'CUTTING' | 'PAUSED' | 'ALARM_STOP';
  currentBarNumber: number;
  totalBarsInBatch: number;
  currentOperationIndex: number;
  totalOperationsInBar: number;
  liveFeedPosMm: number;
  targetFeedPosMm: number;
  feedSpeedMPerMin: number;
  hydraulicPressureBar: number;
  activeHeadName?: string;
  clampsEngaged: boolean;
  eStopActive: boolean;
  safetyDoorsClosed: boolean;
  cycleStartTime?: string;
  elapsedSeconds: number;
}
