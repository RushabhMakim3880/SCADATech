import { SideType } from './machine.js';

export type RecipeMeasurementType = 'ABSOLUTE' | 'INCREMENTAL';

export type RecipeOperationType =
  | 'PUNCH'
  | 'MARK'
  | 'CUT'
  | 'DRILL'
  | 'NOTCH';

export type HoleSymbolType =
  | 'HALF_FILLED_13_5'     // 13.5Ø Half-filled circle (M12)
  | 'STANDARD_OPEN_17_5'   // 17.5Ø Standard open circle (M16)
  | 'SOLID_FILLED_22'      // 22Ø Solid circle (M20)
  | 'CONCENTRIC_DOUBLE_26' // 26Ø Concentric double circle (M24)
  | 'CHECKERBOARD_30'      // 30Ø Quadrant checkerboard (M27)
  | 'SQUARE_33'            // 33Ø Square with crosshair (M30)
  | 'TRIANGLE_36'          // 36Ø Triangle with crosshair (M33)
  | 'STEP_HOLE_17_5'       // 17.5Ø Step Hole (Double ring step bolt)
  | 'CUSTOM';

export interface StandardHoleStyle {
  symbolType: HoleSymbolType;
  diameter: number;
  label: string;
  boltStandard: string;
  description: string;
  isStepHole?: boolean;
}

export const STANDARD_HOLE_STYLES: StandardHoleStyle[] = [
  {
    symbolType: 'HALF_FILLED_13_5',
    diameter: 13.5,
    label: '13.5Ø Half-Filled',
    boltStandard: 'M12 Bolt',
    description: 'Grounding wire cleats & light fittings',
  },
  {
    symbolType: 'STANDARD_OPEN_17_5',
    diameter: 17.5,
    label: '17.5Ø Open Circle',
    boltStandard: 'M16 Bolt',
    description: 'Standard structural tower splice & bracings',
  },
  {
    symbolType: 'SOLID_FILLED_22',
    diameter: 22.0,
    label: '22Ø Solid Filled',
    boltStandard: 'M20 Bolt',
    description: 'Main tower leg joints & heavy diagonals',
  },
  {
    symbolType: 'CONCENTRIC_DOUBLE_26',
    diameter: 26.0,
    label: '26Ø Concentric Double',
    boltStandard: 'M24 Bolt',
    description: 'Tension tower legs & heavy dead-end splices',
  },
  {
    symbolType: 'CHECKERBOARD_30',
    diameter: 30.0,
    label: '30Ø Checkerboard',
    boltStandard: 'M27 Bolt',
    description: 'Heavy structural foundation & base joints',
  },
  {
    symbolType: 'SQUARE_33',
    diameter: 33.0,
    label: '33Ø Square Crosshair',
    boltStandard: 'M30 Bolt',
    description: 'Foundation stubs & base shoe connections',
  },
  {
    symbolType: 'TRIANGLE_36',
    diameter: 36.0,
    label: '36Ø Triangle Crosshair',
    boltStandard: 'M33 Bolt',
    description: 'High shear connection nodes',
  },
  {
    symbolType: 'STEP_HOLE_17_5',
    diameter: 17.5,
    label: '17.5Ø Step Hole',
    boltStandard: 'Step Bolt',
    description: 'Tower climbing rungs / step bolt footholds',
    isStepHole: true,
  },
];

export interface ItemRecipeStep {
  id: string;
  stepNumber: number;
  operationType: RecipeOperationType;
  side: SideType;
  xPosition: number; // in mm along length of the part (Absolute coordinate from zero datum)
  incrementalPitch?: number; // pitch spacing (delta X in mm from previous hole/datum)
  yPosition: number; // in mm along flange width (transverse gauge from heel)
  toolSize?: number; // hole diameter in mm
  toolShape?: 'ROUND' | 'OBLONG' | 'SQUARE';
  holeSymbol?: HoleSymbolType; // Standard structural tower hole symbol
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
