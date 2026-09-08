import { FastifyPluginAsync } from 'fastify';
import { prisma } from '../db/prisma.js';

export const DEFAULT_MENU_CONFIG = [
  {
    menuKey: 'DASHBOARD',
    label: 'SCADA Home Dashboard',
    icon: 'LayoutGrid',
    description: 'Central HPT SCADA Touch launcher and machine overview',
    order: 0,
    isEnabled: true,
    roles: ['SUPER_ADMIN', 'ADMIN', 'OPERATOR'],
    category: 'OPERATIONAL',
    badgeText: 'LAUNCHER',
    badgeColor: 'blue',
  },
  {
    menuKey: 'PRODUCTION',
    label: 'Manage Production',
    icon: 'PlaySquare',
    description: 'Real-time 2D CAD visualizer and automated execution',
    order: 1,
    isEnabled: true,
    roles: ['SUPER_ADMIN', 'ADMIN', 'OPERATOR'],
    category: 'OPERATIONAL',
    badgeText: '2D CAD',
    badgeColor: 'emerald',
  },
  {
    menuKey: 'MANUAL',
    label: 'Manual Operations',
    icon: 'SlidersHorizontal',
    description: 'Manual servo feed jog, head firing & hydraulic clamps',
    order: 2,
    isEnabled: true,
    roles: ['SUPER_ADMIN', 'ADMIN', 'OPERATOR'],
    category: 'OPERATIONAL',
    badgeText: 'JOG & CYLINDERS',
    badgeColor: 'amber',
  },
  {
    menuKey: 'RECIPES',
    label: 'Item Recipe Master',
    icon: 'FileCode2',
    description: 'DSTV/NC1 CAD file importer and angle punch coordinate definitions',
    order: 3,
    isEnabled: true,
    roles: ['SUPER_ADMIN', 'ADMIN', 'OPERATOR'],
    category: 'OPERATIONAL',
    badgeText: 'NC1 / DSTV',
    badgeColor: 'cyan',
  },
  {
    menuKey: 'ALIGNMENT',
    label: 'Program Align & Nesting',
    icon: 'Layers',
    description: 'Multibar linear nesting and scrap minimizer (<1.2% waste)',
    order: 4,
    isEnabled: true,
    roles: ['SUPER_ADMIN', 'ADMIN', 'OPERATOR'],
    category: 'OPERATIONAL',
    badgeText: 'SCRAP MIN',
    badgeColor: 'purple',
  },
  {
    menuKey: 'OEE_ANALYTICS',
    label: 'Shift & OEE Telemetry',
    icon: 'TrendingUp',
    description: 'Metric tonnage processed, shift runtimes and OEE efficiency',
    order: 5,
    isEnabled: true,
    roles: ['SUPER_ADMIN', 'ADMIN', 'OPERATOR'],
    category: 'ENGINEERING',
    badgeText: 'TONNAGE',
    badgeColor: 'blue',
  },
  {
    menuKey: 'TOOLING_WEAR',
    label: 'Tooling Wear & Life',
    icon: 'Activity',
    description: 'Stroke counter telemetry for 6 punch dies and shear blade',
    order: 6,
    isEnabled: true,
    roles: ['SUPER_ADMIN', 'ADMIN', 'OPERATOR'],
    category: 'ENGINEERING',
    badgeText: '6 DIES',
    badgeColor: 'orange',
  },
  {
    menuKey: 'IO_DIAGNOSTICS',
    label: 'PLC I/O Diagnostics (X/Y)',
    icon: 'Cpu',
    description: '32 Digital Inputs (X0-X37) and 32 Digital Outputs (Y0-Y37) Matrix',
    order: 7,
    isEnabled: true,
    roles: ['SUPER_ADMIN', 'ADMIN'],
    category: 'ENGINEERING',
    badgeText: '64 SIGNALS',
    badgeColor: 'indigo',
  },
  {
    menuKey: 'ALARMS',
    label: 'Alarm Config & Logs',
    icon: 'AlertTriangle',
    description: 'Live active hardware alarms and timestamped event history log',
    order: 8,
    isEnabled: true,
    roles: ['SUPER_ADMIN', 'ADMIN', 'OPERATOR'],
    category: 'ENGINEERING',
    badgeText: 'HISTORY',
    badgeColor: 'red',
  },
  {
    menuKey: 'MACHINE_SETUP',
    label: 'Machine Master Settings',
    icon: 'Wrench',
    description: 'Physical angle dimensions, head tool sizes and mechanical offsets',
    order: 9,
    isEnabled: true,
    roles: ['SUPER_ADMIN', 'ADMIN'],
    category: 'ADMINISTRATION',
    badgeText: 'HA-203',
    badgeColor: 'slate',
  },
  {
    menuKey: 'TAGS',
    label: 'PLC & Ui Tag Master',
    icon: 'Tag',
    description: 'Modbus TCP register mapping and high-speed polling config',
    order: 10,
    isEnabled: true,
    roles: ['SUPER_ADMIN', 'ADMIN'],
    category: 'ADMINISTRATION',
    badgeText: 'MODBUS 20Hz',
    badgeColor: 'teal',
  },
  {
    menuKey: 'USERS',
    label: 'User & Permissions',
    icon: 'ShieldCheck',
    description: 'Admin user management and operator permission matrix',
    order: 11,
    isEnabled: true,
    roles: ['SUPER_ADMIN', 'ADMIN'],
    category: 'ADMINISTRATION',
    badgeText: 'RBAC',
    badgeColor: 'emerald',
  },
  {
    menuKey: 'MENU_CONFIG',
    label: 'Super Admin Menu Config',
    icon: 'Settings2',
    description: 'OEM menu layout, ordering and role visibility settings (With US only)',
    order: 12,
    isEnabled: true,
    roles: ['SUPER_ADMIN'],
    category: 'ADMINISTRATION',
    badgeText: 'WITH US ONLY',
    badgeColor: 'rose',
  },
];

export const menuConfigRoutes: FastifyPluginAsync = async (fastify) => {
  // GET /api/menu-config - Get all configured menus
  fastify.get('/menu-config', async (request, reply) => {
    try {
      let items = await prisma.menuConfig.findMany({
        orderBy: { order: 'asc' },
      });

      // If no items in DB, initialize with default menu config
      if (items.length === 0) {
        for (const item of DEFAULT_MENU_CONFIG) {
          await prisma.menuConfig.upsert({
            where: { menuKey: item.menuKey },
            update: {},
            create: {
              ...item,
              roles: JSON.stringify(item.roles),
            },
          });
        }
        items = await prisma.menuConfig.findMany({
          orderBy: { order: 'asc' },
        });
      }

      const parsed = items.map((item) => ({
        ...item,
        roles: typeof item.roles === 'string' ? JSON.parse(item.roles || '[]') : item.roles,
      }));

      return reply.send({ success: true, data: parsed });
    } catch (err: any) {
      return reply.status(500).send({ success: false, error: err.message });
    }
  });

  // PUT /api/menu-config - Update menu items (Super Admin only)
  fastify.put<{
    Body: {
      items: Array<{
        menuKey: string;
        label: string;
        icon: string;
        description?: string;
        order: number;
        isEnabled: boolean;
        roles: string[];
        category?: string;
        badgeText?: string;
        badgeColor?: string;
      }>;
    };
  }>('/menu-config', async (request, reply) => {
    try {
      const { items } = request.body;

      if (!Array.isArray(items)) {
        return reply.status(400).send({ success: false, error: 'Expected an array of menu items' });
      }

      const updated = await prisma.$transaction(
        items.map((item) =>
          prisma.menuConfig.upsert({
            where: { menuKey: item.menuKey },
            update: {
              label: item.label,
              icon: item.icon,
              description: item.description,
              order: item.order,
              isEnabled: item.isEnabled,
              roles: JSON.stringify(item.roles || []),
              category: item.category || 'OPERATIONAL',
              badgeText: item.badgeText,
              badgeColor: item.badgeColor,
            },
            create: {
              menuKey: item.menuKey,
              label: item.label,
              icon: item.icon,
              description: item.description,
              order: item.order,
              isEnabled: item.isEnabled,
              roles: JSON.stringify(item.roles || []),
              category: item.category || 'OPERATIONAL',
              badgeText: item.badgeText,
              badgeColor: item.badgeColor,
            },
          })
        )
      );

      const parsed = updated.map((item) => ({
        ...item,
        roles: JSON.parse(item.roles || '[]'),
      }));

      return reply.send({ success: true, data: parsed });
    } catch (err: any) {
      return reply.status(500).send({ success: false, error: err.message });
    }
  });

  // POST /api/menu-config/reset - Reset to factory default configuration
  fastify.post('/menu-config/reset', async (request, reply) => {
    try {
      await prisma.menuConfig.deleteMany();

      for (const item of DEFAULT_MENU_CONFIG) {
        await prisma.menuConfig.create({
          data: {
            ...item,
            roles: JSON.stringify(item.roles),
          },
        });
      }

      const items = await prisma.menuConfig.findMany({
        orderBy: { order: 'asc' },
      });

      const parsed = items.map((item) => ({
        ...item,
        roles: JSON.parse(item.roles || '[]'),
      }));

      return reply.send({ success: true, message: 'Reset to factory defaults', data: parsed });
    } catch (err: any) {
      return reply.status(500).send({ success: false, error: err.message });
    }
  });
};
