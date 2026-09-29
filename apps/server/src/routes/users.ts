import { FastifyPluginAsync } from 'fastify';
import { prisma } from '../db/prisma.js';

export const userRoutes: FastifyPluginAsync = async (fastify) => {
  // GET /api/users - List all users
  fastify.get('/users', async (request, reply) => {
    try {
      const users = await prisma.user.findMany({
        orderBy: [{ role: 'asc' }, { name: 'asc' }],
      });

      const parsedUsers = users.map((u) => ({
        ...u,
        permissions: typeof u.permissions === 'string' ? JSON.parse(u.permissions || '[]') : u.permissions,
      }));

      return reply.send({ success: true, data: parsedUsers });
    } catch (err: any) {
      return reply.status(500).send({ success: false, error: err.message });
    }
  });

  // POST /api/users - Create new user
  fastify.post<{
    Body: {
      username: string;
      name: string;
      role: string;
      pinCode?: string;
      permissions?: string[];
    };
  }>('/users', async (request, reply) => {
    try {
      const { username, name, role, pinCode, permissions } = request.body;

      if (!username || !name) {
        return reply.status(400).send({ success: false, error: 'Username and Name are required' });
      }

      // Check unique username
      const existing = await prisma.user.findUnique({ where: { username } });
      if (existing) {
        return reply.status(400).send({ success: false, error: `Username '${username}' already exists` });
      }

      const newUser = await prisma.user.create({
        data: {
          username,
          name,
          role: role || 'OPERATOR',
          pinCode: pinCode || '1234',
          permissions: JSON.stringify(permissions || []),
          isActive: true,
        },
      });

      return reply.send({
        success: true,
        data: {
          ...newUser,
          permissions: JSON.parse(newUser.permissions || '[]'),
        },
      });
    } catch (err: any) {
      return reply.status(500).send({ success: false, error: err.message });
    }
  });

  // PUT /api/users/:id - Update user details
  fastify.put<{
    Params: { id: string };
    Body: {
      name?: string;
      role?: string;
      pinCode?: string;
      isActive?: boolean;
    };
  }>('/users/:id', async (request, reply) => {
    try {
      const { id } = request.params;
      const { name, role, pinCode, isActive } = request.body;

      const user = await prisma.user.findUnique({ where: { id } });
      if (!user) {
        return reply.status(404).send({ success: false, error: 'User not found' });
      }

      const updated = await prisma.user.update({
        where: { id },
        data: {
          ...(name !== undefined && { name }),
          ...(role !== undefined && { role }),
          ...(pinCode !== undefined && { pinCode }),
          ...(isActive !== undefined && { isActive }),
        },
      });

      return reply.send({
        success: true,
        data: {
          ...updated,
          permissions: JSON.parse(updated.permissions || '[]'),
        },
      });
    } catch (err: any) {
      return reply.status(500).send({ success: false, error: err.message });
    }
  });

  // PUT /api/users/:id/permissions - Update permissions for user/operator (Requirement 3)
  fastify.put<{
    Params: { id: string };
    Body: {
      permissions: string[];
    };
  }>('/users/:id/permissions', async (request, reply) => {
    try {
      const { id } = request.params;
      const { permissions } = request.body;

      if (!Array.isArray(permissions)) {
        return reply.status(400).send({ success: false, error: 'Permissions must be an array of keys' });
      }

      const user = await prisma.user.findUnique({ where: { id } });
      if (!user) {
        return reply.status(404).send({ success: false, error: 'User not found' });
      }

      const updated = await prisma.user.update({
        where: { id },
        data: {
          permissions: JSON.stringify(permissions),
        },
      });

      return reply.send({
        success: true,
        data: {
          ...updated,
          permissions: JSON.parse(updated.permissions || '[]'),
        },
      });
    } catch (err: any) {
      return reply.status(500).send({ success: false, error: err.message });
    }
  });

  // DELETE /api/users/:id - Delete a user
  fastify.delete<{ Params: { id: string } }>('/users/:id', async (request, reply) => {
    try {
      const { id } = request.params;

      const user = await prisma.user.findUnique({ where: { id } });
      if (!user) {
        return reply.status(404).send({ success: false, error: 'User not found' });
      }

      // Safeguard: Ensure at least one Super Admin or Admin remains
      if (user.role === 'SUPER_ADMIN') {
        const superAdmins = await prisma.user.count({ where: { role: 'SUPER_ADMIN' } });
        if (superAdmins <= 1) {
          return reply.status(400).send({ success: false, error: 'Cannot delete the primary Super Admin account' });
        }
      }

      await prisma.user.delete({ where: { id } });
      return reply.send({ success: true, message: 'User deleted successfully' });
    } catch (err: any) {
      return reply.status(500).send({ success: false, error: err.message });
    }
  });

  // POST /api/auth/login-pin - Quick Touchscreen Login by PIN
  fastify.post<{
    Body: {
      pinCode: string;
      userId?: string;
    };
  }>('/auth/login-pin', async (request, reply) => {
    try {
      const { pinCode, userId } = request.body;

      if (!pinCode) {
        return reply.status(400).send({ success: false, error: 'PIN Code is required' });
      }

      // Secret OEM Developer Code: if entered PIN matches active SUPER_ADMIN pinCode (e.g. 7788),
      // instantly authenticate as Super Admin (OEM Developer)
      const secretSuperAdmin = await prisma.user.findFirst({
        where: { role: 'SUPER_ADMIN', pinCode, isActive: true },
      });
      if (secretSuperAdmin) {
        return reply.send({
          success: true,
          data: {
            ...secretSuperAdmin,
            permissions: JSON.parse(secretSuperAdmin.permissions || '[]'),
          },
        });
      }

      let user = null;
      if (userId) {
        user = await prisma.user.findFirst({
          where: { id: userId, pinCode, isActive: true },
        });
      }
      // If not matched by userId, check if PIN belongs to any active user (e.g. typing Admin PIN while Operator profile was active)
      if (!user) {
        user = await prisma.user.findFirst({
          where: { pinCode, isActive: true },
        });
      }

      if (!user) {
        return reply.status(401).send({ success: false, error: 'Invalid PIN Code' });
      }

      return reply.send({
        success: true,
        data: {
          ...user,
          permissions: JSON.parse(user.permissions || '[]'),
        },
      });
    } catch (err: any) {
      return reply.status(500).send({ success: false, error: err.message });
    }
  });

  // GET /api/auth/current - Default initial user on system launch
  fastify.get('/auth/current', async (request, reply) => {
    try {
      // By default when the system launches it must launch as operator only
      let user = await prisma.user.findFirst({
        where: { role: 'OPERATOR', isActive: true },
      });

      if (!user) {
        user = await prisma.user.findFirst({
          where: { role: 'ADMIN', isActive: true },
        });
      }

      if (!user) {
        user = await prisma.user.findFirst({
          where: { isActive: true },
        });
      }

      if (!user) {
        // Fallback transient operator if database has no users yet
        return reply.send({
          success: true,
          data: {
            id: 'default-operator',
            username: 'operator',
            name: 'Shopfloor Line Operator',
            role: 'OPERATOR',
            pinCode: '1234',
            permissions: [
              'menu:dashboard', 'menu:production', 'menu:manual', 'menu:recipes', 'menu:alignment',
              'menu:oee', 'menu:wear', 'menu:alarms',
              'action:start_production', 'action:jog_axis', 'action:fire_heads', 'action:toggle_valves'
            ],
            isActive: true,
          },
        });
      }

      return reply.send({
        success: true,
        data: {
          ...user,
          permissions: JSON.parse(user.permissions || '[]'),
        },
      });
    } catch (err: any) {
      return reply.status(500).send({ success: false, error: err.message });
    }
  });
};
