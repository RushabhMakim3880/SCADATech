import { FastifyPluginAsync } from 'fastify';
import { prisma } from '../db/prisma.js';

export const tagRoutes: FastifyPluginAsync = async (fastify) => {
  // GET /api/tags - List all PLC tags with metadata
  fastify.get('/tags', async (request, reply) => {
    const tags = await prisma.plcTag.findMany({
      orderBy: { category: 'asc' },
    });
    return reply.send({ success: true, data: tags });
  });

  // POST /api/tags - Create a new tag
  fastify.post<{
    Body: {
      plcConfigId: string;
      tagName: string;
      tagAddress: string;
      tagDescription?: string;
      dataType: string;
      category: string;
      accessMode?: string;
      unit?: string;
    };
  }>('/tags', async (request, reply) => {
    const tag = await prisma.plcTag.create({
      data: request.body,
    });
    return reply.status(201).send({ success: true, data: tag });
  });

  // DELETE /api/tags/:id
  fastify.delete<{ Params: { id: string } }>('/tags/:id', async (request, reply) => {
    await prisma.plcTag.delete({
      where: { id: request.params.id },
    });
    return reply.send({ success: true, message: 'Tag deleted' });
  });
};
