import dotenv from 'dotenv';
import { buildApp } from './app.js';
import { PlcManager } from './plc/plcManager.js';

dotenv.config();

const PORT = parseInt(process.env.PORT || '5000', 10);
const HOST = process.env.HOST || '0.0.0.0';
const IS_SIMULATOR = process.env.SIMULATOR_MODE !== 'false';
const PLC_ENDPOINT = process.env.PLC_ENDPOINT || 'opc.tcp://192.168.1.10:4840';

async function startServer() {
  const app = buildApp();
  const plcManager = PlcManager.getInstance();

  try {
    // Initialize PLC Service (OPC-UA or Simulator)
    await plcManager.init(IS_SIMULATOR, PLC_ENDPOINT);

    await app.listen({ port: PORT, host: HOST });
    console.log(`🚀 Innovance Angle Punching Backend Server running at http://localhost:${PORT}`);
    console.log(`📡 Real-time WebSocket streaming at ws://localhost:${PORT}/ws`);
  } catch (err) {
    app.log.error(err);
    process.exit(1);
  }
}

startServer();
