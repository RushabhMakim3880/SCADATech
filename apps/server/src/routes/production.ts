import { FastifyPluginAsync } from 'fastify';
import { prisma } from '../db/prisma.js';

export const productionRoutes: FastifyPluginAsync = async (fastify) => {
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
