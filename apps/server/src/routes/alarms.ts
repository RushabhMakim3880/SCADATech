import { FastifyPluginAsync } from 'fastify';
import { prisma } from '../db/prisma.js';

export const alarmRoutes: FastifyPluginAsync = async (fastify) => {
  // GET /api/alarms/definitions - List configured alarm rules
  fastify.get('/alarms/definitions', async (request, reply) => {
    const definitions = await prisma.alarmDefinition.findMany({
      orderBy: { severity: 'desc' },
    });
    return reply.send({ success: true, data: definitions });
  });

  // GET /api/alarms/logs - List alarm history logs
  fastify.get('/alarms/logs', async (request, reply) => {
    const logs = await prisma.alarmLog.findMany({
      take: 100,
      orderBy: { triggeredAt: 'desc' },
    });
    return reply.send({ success: true, data: logs });
  });

  // POST /api/alarms/acknowledge - Acknowledge active alarm
  fastify.post<{
    Body: { logId: string };
  }>('/alarms/acknowledge', async (request, reply) => {
    const { logId } = request.body;
    const updated = await prisma.alarmLog.update({
      where: { id: logId },
      data: { acknowledgedAt: new Date() },
    });
    return reply.send({ success: true, data: updated });
  });
};
