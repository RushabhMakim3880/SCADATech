import { contextBridge } from 'electron';

contextBridge.exposeInMainWorld('kioskApi', {
  platform: process.platform,
  version: '1.0.0',
  isKiosk: true,
});
