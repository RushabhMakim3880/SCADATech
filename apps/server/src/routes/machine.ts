import { FastifyPluginAsync } from 'fastify';
import { prisma } from '../db/prisma.js';

export const machineRoutes: FastifyPluginAsync = async (fastify) => {
  // GET /api/machines - List configured machines
  fastify.get('/machines', async (request, reply) => {
    const machines = await prisma.machine.findMany({
      include: {
        details: {
          orderBy: { xPosition: 'asc' },
        },
      },
    });
    return reply.send({ success: true, data: machines });
  });

  // GET /api/machines/:id - Get machine details with heads & tools
  fastify.get<{ Params: { id: string } }>('/machines/:id', async (request, reply) => {
    const { id } = request.params;
    const machine = await prisma.machine.findUnique({
      where: { id },
      include: {
        details: {
          orderBy: { xPosition: 'asc' },
        },
      },
    });

    if (!machine) {
      return reply.status(404).send({ success: false, error: 'Machine not found' });
    }

    return reply.send({ success: true, data: machine });
  });

  // PUT /api/machines/:id/setup - Update head tooling & cassette setup
  fastify.put<{
    Params: { id: string };
    Body: {
      heads: Array<{
        id: string;
        toolSize?: number;
        toolShape?: string;
        isActive?: boolean;
      }>;
    };
  }>('/machines/:id/setup', async (request, reply) => {
    const { id } = request.params;
    const { heads } = request.body;

    const updates = await prisma.$transaction(
      heads.map((h) =>
        prisma.machineDetail.update({
          where: { id: h.id },
          data: {
            toolSize: h.toolSize,
            toolShape: h.toolShape,
            isActive: h.isActive,
          },
        })
      )
    );

    return reply.send({ success: true, data: updates });
  });
};
