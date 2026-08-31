import { ItemRecipe, MachineConfig, ProgramCyclePlan, AlignedOperation } from '@innovance-hmi/shared';
import { prisma } from '../db/prisma.js';

export interface NestingItemRequest {
  recipeId: string;
  quantity: number;
}

export class AlignmentEngine {
  public static async calculateAlignment(
    items: NestingItemRequest[],
    stockBarLength: number = 6000.0,
    machineId?: string
  ): Promise<ProgramCyclePlan> {
    // 1. Fetch Machine Details
    const machine = await prisma.machine.findFirst({
      where: machineId ? { id: machineId } : {},
      include: { details: true },
    });

    if (!machine) {
      throw new Error('No machine configured');
    }

    // 2. Fetch Recipes
    const recipeIds = items.map((i) => i.recipeId);
    const recipes = await prisma.itemRecipe.findMany({
      where: { id: { in: recipeIds } },
      include: { steps: { orderBy: { stepNumber: 'asc' } } },
    });

    const recipeMap = new Map<string, ItemRecipe>();
    recipes.forEach((r) => recipeMap.set(r.id, r as unknown as ItemRecipe));

    // 3. Nest items along raw stock bar
    let currentBarX = 0.0;
    const itemsSummary: Array<{ recipeId: string; itemCode: string; count: number }> = [];
    const rawOperations: Array<Omit<AlignedOperation, 'stepIndex'>> = [];

    let itemIndexInBar = 0;

    for (const req of items) {
      const recipe = recipeMap.get(req.recipeId);
      if (!recipe) continue;

      let placedCount = 0;

      for (let q = 0; q < req.quantity; q++) {
        if (currentBarX + recipe.totalLength > stockBarLength) {
          // Exceeds stock bar length
          break;
        }

        itemIndexInBar++;
        placedCount++;

        // Add operations for this part instance
        for (const step of recipe.steps) {
          const absoluteBarX = currentBarX + step.xPosition;

          // Find matching head
          let allocatedHeadName = 'Cutter';
          let allocatedHeadOffset = 0.0;

          if (step.operationType === 'PUNCH') {
            const matchingHead = machine.details.find(
              (d) =>
                d.headType === 'PUNCHING' &&
                d.side === step.side &&
                d.isActive &&
                Math.abs((d.toolSize || 0) - (step.toolSize || 0)) < 0.1
            );

            if (matchingHead) {
              allocatedHeadName = matchingHead.headName;
              allocatedHeadOffset = matchingHead.xPosition;
            } else {
              // Fallback to first active head on that side
              const fallback = machine.details.find(
                (d) => d.headType === 'PUNCHING' && d.side === step.side && d.isActive
              );
              allocatedHeadName = fallback?.headName || (step.side === 'A' ? 'DA1' : 'DB1');
              allocatedHeadOffset = fallback?.xPosition || 200.0;
            }
          } else if (step.operationType === 'MARK') {
            const markingHead = machine.details.find((d) => d.headType === 'MARKING' && d.isActive);
            allocatedHeadName = markingHead?.headName || 'Marking';
            allocatedHeadOffset = markingHead?.xPosition || 50.0;
          } else if (step.operationType === 'CUT' || step.isCutOff) {
            const cutterHead = machine.details.find((d) => d.headType === 'CUTTING' && d.isActive);
            allocatedHeadName = cutterHead?.headName || 'Cutter';
            allocatedHeadOffset = cutterHead?.xPosition || 0.0;
          }

          // Feed DRO Target = Absolute Bar Position - Head Offset
          const requiredFeedAxisPos = Math.max(0, absoluteBarX - allocatedHeadOffset);

          rawOperations.push({
            recipeId: recipe.id,
            itemIndexInBar,
            operationType: step.operationType,
            side: step.side,
            absoluteBarX: Number(absoluteBarX.toFixed(2)),
            yPosition: step.yPosition,
            toolSize: step.toolSize,
            allocatedHeadName,
            allocatedHeadOffset,
            requiredFeedAxisPos: Number(requiredFeedAxisPos.toFixed(2)),
            isCutOff: Boolean(step.isCutOff),
            markingText: step.markingText,
            status: 'PENDING',
          });
        }

        currentBarX += recipe.totalLength;
      }

      if (placedCount > 0) {
        itemsSummary.push({
          recipeId: recipe.id,
          itemCode: recipe.itemCode,
          count: placedCount,
        });
      }
    }

    // 4. Sort and Optimize Operation Sequence
    // Sort primarily by requiredFeedAxisPos ascending to feed bar continuously forward
    rawOperations.sort((a, b) => {
      if (Math.abs(a.requiredFeedAxisPos - b.requiredFeedAxisPos) < 1.0) {
        // If at same feed position, punch before cut
        if (a.isCutOff) return 1;
        if (b.isCutOff) return -1;
      }
      return a.requiredFeedAxisPos - b.requiredFeedAxisPos;
    });

    const operationsSequence: AlignedOperation[] = rawOperations.map((op, idx) => ({
      ...op,
      stepIndex: idx + 1,
    }));

    const utilizedLength = Number(currentBarX.toFixed(2));
    const scrapLength = Number((stockBarLength - currentBarX).toFixed(2));

    return {
      cycleId: `CYC-${Date.now().toString().slice(-6)}`,
      stockBarLength,
      utilizedLength,
      scrapLength,
      itemsSummary,
      operationsSequence,
      estimatedCycleTimeSec: operationsSequence.length * 4 + Math.round(utilizedLength / 100),
    };
  }
}
