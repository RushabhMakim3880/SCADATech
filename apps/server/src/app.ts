import Fastify, { FastifyInstance } from 'fastify';
import cors from '@fastify/cors';
import sensible from '@fastify/sensible';
import websocket from '@fastify/websocket';
import { machineRoutes } from './routes/machine.js';
import { tagRoutes } from './routes/tags.js';
import { recipeRoutes } from './routes/recipes.js';
import { productionRoutes } from './routes/production.js';
import { alarmRoutes } from './routes/alarms.js';
import { PlcManager } from './plc/plcManager.js';
import { WsClientMessage, WsServerMessage } from '@innovance-hmi/shared';

export function buildApp(): FastifyInstance {
  const app = Fastify({
    logger: true,
  });

  const plcManager = PlcManager.getInstance();

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
      plc: plcManager.getStatus(),
      timestamp: new Date().toISOString(),
    };
  });

  // REST API Routes
  app.register(machineRoutes, { prefix: '/api' });
  app.register(tagRoutes, { prefix: '/api' });
  app.register(recipeRoutes, { prefix: '/api' });
  app.register(productionRoutes, { prefix: '/api' });
  app.register(alarmRoutes, { prefix: '/api' });

  // WebSocket Route for Real-time Streaming & Client Control
  app.register(async (fastify) => {
    const clients = new Set<any>();

    // Connect PLC Manager broadcast to all active WS clients
    plcManager.setWsBroadcast((msg: WsServerMessage) => {
      const data = JSON.stringify(msg);
      for (const client of clients) {
        if (client.readyState === 1) {
          // OPEN
          client.send(data);
        }
      }
    });

    fastify.get('/ws', { websocket: true }, (socket, req) => {
      clients.add(socket);
      req.log.info('🔌 Client connected to Real-Time HMI Gateway');

      // Send initial status & active alarms
      socket.send(
        JSON.stringify({
          type: 'PLC_STATUS',
          timestamp: Date.now(),
          payload: plcManager.getStatus(),
        })
      );

      socket.on('message', async (data: Buffer | string) => {
        try {
          const msg: WsClientMessage = JSON.parse(data.toString());

          switch (msg.type) {
            case 'PING':
              socket.send(JSON.stringify({ type: 'PONG', timestamp: Date.now() }));
              break;

            case 'WRITE_TAG':
              if (msg.payload?.tagName) {
                await plcManager.writeTag(msg.payload.tagName, msg.payload.value, msg.payload.dataType);
              }
              break;

            case 'JOG_AXIS_START':
              plcManager.triggerJog(msg.payload.direction, true, msg.payload.speed);
              break;

            case 'JOG_AXIS_STOP':
              plcManager.triggerJog(msg.payload.direction, false);
              break;

            case 'TOGGLE_VALVE':
              plcManager.toggleValve(msg.payload.valve);
              break;

            default:
              req.log.warn({ type: msg.type }, 'Unhandled WS message type');
          }
        } catch (e) {
          req.log.error('Invalid WS payload');
        }
      });

      socket.on('close', () => {
        clients.delete(socket);
        req.log.info('🔌 Client disconnected');
      });
    });
  });

  return app;
}
