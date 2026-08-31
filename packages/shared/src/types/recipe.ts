import { SideType } from './machine.js';

export type RecipeMeasurementType = 'ABSOLUTE' | 'INCREMENTAL';

export type RecipeOperationType =
  | 'PUNCH'
  | 'MARK'
  | 'CUT'
  | 'DRILL'
  | 'NOTCH';

export interface ItemRecipeStep {
  id: string;
  stepNumber: number;
  operationType: RecipeOperationType;
  side: SideType;
  xPosition: number; // in mm along length of the part
  yPosition: number; // in mm along flange width
  toolSize?: number; // hole diameter in mm
  toolShape?: 'ROUND' | 'OBLONG' | 'SQUARE';
  markingText?: string;
  markingCassetteIndex?: number;
  isCutOff?: boolean;
  remarks?: string;
}

export interface ItemRecipe {
  id: string;
  itemCode: string;
  itemName: string;
  description?: string;
  angleWidthA: number; // Flange A width in mm (e.g. 75)
  angleWidthB: number; // Flange B width in mm (e.g. 75)
  thickness: number; // Material thickness in mm (e.g. 6)
  totalLength: number; // Total part length in mm (e.g. 1500)
  measurementType: RecipeMeasurementType;
  steps: ItemRecipeStep[];
  isActive: boolean;
  createdAt: string;
  updatedAt: string;
}

export interface CreateItemRecipeDto {
  itemCode: string;
  itemName: string;
  description?: string;
  angleWidthA: number;
  angleWidthB: number;
  thickness: number;
  totalLength: number;
  measurementType?: RecipeMeasurementType;
  steps: Omit<ItemRecipeStep, 'id'>[];
}
