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
      plcConfigId?: string;
      tagName: string;
      tagAddress: string;
      tagDescription?: string;
      dataType: string;
      category: string;
      accessMode?: string;
      unit?: string;
    };
  }>('/tags', async (request, reply) => {
    let plcConfigId = request.body.plcConfigId;
    if (!plcConfigId) {
      const defaultPlc = await prisma.plcConfig.findFirst();
      plcConfigId = defaultPlc?.id || 'default-plc';
    }

    const tag = await prisma.plcTag.create({
      data: {
        plcConfigId,
        tagName: request.body.tagName,
        tagAddress: request.body.tagAddress,
        tagDescription: request.body.tagDescription || '',
        dataType: request.body.dataType,
        category: request.body.category,
        accessMode: request.body.accessMode || 'READ_WRITE',
        unit: request.body.unit || '',
      },
    });
    return reply.status(201).send({ success: true, data: tag });
  });

  // PUT /api/tags/:id - Update existing tag mapping
  fastify.put<{
    Params: { id: string };
    Body: {
      tagName: string;
      tagAddress: string;
      tagDescription?: string;
      dataType: string;
      category: string;
      accessMode?: string;
      unit?: string;
    };
  }>('/tags/:id', async (request, reply) => {
    const updated = await prisma.plcTag.update({
      where: { id: request.params.id },
      data: {
        tagName: request.body.tagName,
        tagAddress: request.body.tagAddress,
        tagDescription: request.body.tagDescription || '',
        dataType: request.body.dataType,
        category: request.body.category,
        accessMode: request.body.accessMode || 'READ_WRITE',
        unit: request.body.unit || '',
      },
    });
    return reply.send({ success: true, data: updated });
  });

  // DELETE /api/tags/:id
  fastify.delete<{ Params: { id: string } }>('/tags/:id', async (request, reply) => {
    await prisma.plcTag.delete({
      where: { id: request.params.id },
    });
    return reply.send({ success: true, message: 'Tag deleted' });
  });
};
