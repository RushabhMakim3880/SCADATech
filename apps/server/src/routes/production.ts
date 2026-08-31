import { FastifyPluginAsync } from 'fastify';
import { prisma } from '../db/prisma.js';
import { AlignmentEngine, NestingItemRequest } from '../services/alignmentEngine.js';

export const productionRoutes: FastifyPluginAsync = async (fastify) => {
  // POST /api/production/align - Calculate and Preview Bar Nesting Alignment
  fastify.post<{
    Body: {
      items: NestingItemRequest[];
      stockBarLength?: number;
      machineId?: string;
    };
  }>('/production/align', async (request, reply) => {
    const { items, stockBarLength = 6000.0, machineId } = request.body;

    try {
      const plan = await AlignmentEngine.calculateAlignment(items, stockBarLength, machineId);
      return reply.send({ success: true, data: plan });
    } catch (err: any) {
      return reply.status(400).send({ success: false, error: err.message });
    }
  });

  // POST /api/production/commit-cycle - Save calculated cycle plan into database
  fastify.post<{
    Body: {
      cycleCode: string;
      jobCardId?: string;
      stockBarLength: number;
      utilizedLength: number;
      scrapLength: number;
      targetBars?: number;
      operations: Array<{
        sequenceOrder: number;
        recipeId: string;
        operationType: string;
        side: string;
        absoluteBarX: number;
        yPosition: number;
        toolSize?: number;
        allocatedHeadName?: string;
        allocatedHeadOffset: number;
        requiredFeedAxisPos: number;
        isCutOff: boolean;
        markingText?: string;
      }>;
    };
  }>('/production/commit-cycle', async (request, reply) => {
    const { cycleCode, jobCardId, stockBarLength, utilizedLength, scrapLength, targetBars = 1, operations } = request.body;

    const cycle = await prisma.programCycle.create({
      data: {
        cycleCode,
        jobCardId,
        stockBarLength,
        utilizedLength,
        scrapLength,
        targetBars,
        operations: {
          create: operations.map((op) => ({
            sequenceOrder: op.sequenceOrder,
            recipeId: op.recipeId,
            operationType: op.operationType,
            side: op.side,
            absoluteBarX: op.absoluteBarX,
            yPosition: op.yPosition,
            toolSize: op.toolSize,
            allocatedHeadName: op.allocatedHeadName,
            allocatedHeadOffset: op.allocatedHeadOffset,
            requiredFeedAxisPos: op.requiredFeedAxisPos,
            isCutOff: op.isCutOff,
            markingText: op.markingText,
          })),
        },
      },
      include: {
        operations: {
          orderBy: { sequenceOrder: 'asc' },
        },
      },
    });

    return reply.status(201).send({ success: true, data: cycle });
  });

  // GET /api/production/jobs - List active Job Cards
  fastify.get('/production/jobs', async (request, reply) => {
    const jobs = await prisma.jobCard.findMany({
      include: {
        items: {
          include: { recipe: true },
        },
        cycles: true,
      },
      orderBy: { createdAt: 'desc' },
    });
    return reply.send({ success: true, data: jobs });
  });

  // POST /api/production/jobs - Create Job Card
  fastify.post<{
    Body: {
      jobCode: string;
      clientName?: string;
      items: Array<{
        recipeId: string;
        requiredQuantity: number;
        priority?: number;
      }>;
    };
  }>('/production/jobs', async (request, reply) => {
    const { jobCode, clientName, items } = request.body;

    const job = await prisma.jobCard.create({
      data: {
        jobCode,
        clientName,
        items: {
          create: items.map((item, idx) => ({
            recipeId: item.recipeId,
            requiredQuantity: item.requiredQuantity,
            priority: item.priority ?? idx + 1,
          })),
        },
      },
      include: {
        items: { include: { recipe: true } },
      },
    });

    return reply.status(201).send({ success: true, data: job });
  });

  // GET /api/production/cycles - List Program Cycles
  fastify.get('/production/cycles', async (request, reply) => {
    const cycles = await prisma.programCycle.findMany({
      include: {
        operations: {
          orderBy: { sequenceOrder: 'asc' },
        },
        bars: true,
      },
      orderBy: { createdAt: 'desc' },
    });
    return reply.send({ success: true, data: cycles });
  });

  // GET /api/production/cycles/:id
  fastify.get<{ Params: { id: string } }>('/production/cycles/:id', async (request, reply) => {
    const cycle = await prisma.programCycle.findUnique({
      where: { id: request.params.id },
      include: {
        operations: {
          orderBy: { sequenceOrder: 'asc' },
        },
        bars: {
          include: { producedUnits: true },
        },
      },
    });

    if (!cycle) {
      return reply.status(404).send({ success: false, error: 'Cycle not found' });
    }

    return reply.send({ success: true, data: cycle });
  });
};
