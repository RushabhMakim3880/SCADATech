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

  // POST /api/tags/bulk - Bulk create or update tags
  fastify.post<{
    Body: Array<{
      plcConfigId?: string;
      tagName: string;
      tagAddress: string;
      tagDescription?: string;
      dataType: string;
      category: string;
      accessMode?: string;
      unit?: string;
    }>;
  }>('/tags/bulk', async (request, reply) => {
    const tags = request.body;
    if (!Array.isArray(tags) || tags.length === 0) {
      return reply.status(400).send({ success: false, message: 'Invalid payload' });
    }

    const defaultPlc = await prisma.plcConfig.findFirst();
    const fallbackPlcId = defaultPlc?.id || 'default-plc';

    // Using a transaction to insert all tags
    const createdTags = await prisma.$transaction(
      tags.map((tag) => 
        prisma.plcTag.upsert({
          where: { tagName: tag.tagName },
          update: {
            tagAddress: tag.tagAddress,
            tagDescription: tag.tagDescription || '',
            dataType: tag.dataType,
            category: tag.category,
            accessMode: tag.accessMode || 'READ_WRITE',
            unit: tag.unit || '',
          },
          create: {
            plcConfigId: tag.plcConfigId || fallbackPlcId,
            tagName: tag.tagName,
            tagAddress: tag.tagAddress,
            tagDescription: tag.tagDescription || '',
            dataType: tag.dataType,
            category: tag.category,
            accessMode: tag.accessMode || 'READ_WRITE',
            unit: tag.unit || '',
          },
        })
      )
    );

    return reply.status(201).send({ success: true, count: createdTags.length });
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
