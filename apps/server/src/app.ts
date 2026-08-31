import Fastify, { FastifyInstance } from 'fastify';
import cors from '@fastify/cors';
import sensible from '@fastify/sensible';
import websocket from '@fastify/websocket';
import { machineRoutes } from './routes/machine.js';
import { tagRoutes } from './routes/tags.js';
import { recipeRoutes } from './routes/recipes.js';
import { productionRoutes } from './routes/production.js';
import { alarmRoutes } from './routes/alarms.js';

export function buildApp(): FastifyInstance {
  const app = Fastify({
    logger: true,
  });

  // Plugins
  app.register(cors, {
    origin: '*',
    methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
  });
  app.register(sensible);
  app.register(websocket);

  // Health check
  app.get('/api/health', async () => {
    return {
      status: 'OK',
      system: 'Innovance 6-Head Angle Punching Machine API & Gateway',
      timestamp: new Date().toISOString(),
    };
  });

  // REST API Routes
  app.register(machineRoutes, { prefix: '/api' });
  app.register(tagRoutes, { prefix: '/api' });
  app.register(recipeRoutes, { prefix: '/api' });
  app.register(productionRoutes, { prefix: '/api' });
  app.register(alarmRoutes, { prefix: '/api' });

  // WebSocket Route for Real-time Streaming
  app.register(async (fastify) => {
    fastify.get('/ws', { websocket: true }, (socket, req) => {
      req.log.info('🔌 New WebSocket client connected to HMI gateway');

      socket.send(
        JSON.stringify({
          type: 'PLC_STATUS',
          timestamp: Date.now(),
          payload: {
            connected: true,
            protocol: 'OPC-UA',
            isSimulator: true,
            mode: 'MANUAL',
          },
        })
      );

      socket.on('message', (data: Buffer | string) => {
        try {
          const parsed = JSON.parse(data.toString());
          if (parsed.type === 'PING') {
            socket.send(JSON.stringify({ type: 'PONG', timestamp: Date.now() }));
          }
        } catch (e) {
          req.log.error('Invalid WS message payload');
        }
      });

      socket.on('close', () => {
        req.log.info('🔌 WebSocket client disconnected');
      });
    });
  });

  return app;
}
