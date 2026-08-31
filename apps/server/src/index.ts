import dotenv from 'dotenv';
import { buildApp } from './app.js';

dotenv.config();

const PORT = parseInt(process.env.PORT || '5000', 10);
const HOST = process.env.HOST || '0.0.0.0';

async function startServer() {
  const app = buildApp();

  try {
    await app.listen({ port: PORT, host: HOST });
    console.log(`🚀 Innovance Angle Punching Backend Server running at http://localhost:${PORT}`);
    console.log(`📡 Real-time WebSocket streaming at ws://localhost:${PORT}/ws`);
  } catch (err) {
    app.log.error(err);
    process.exit(1);
  }
}

startServer();
