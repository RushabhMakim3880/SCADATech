import { app, BrowserWindow, globalShortcut } from 'electron';
import path from 'path';
import { fork, ChildProcess } from 'child_process';

let mainWindow: BrowserWindow | null = null;
let serverProcess: ChildProcess | null = null;

function startBackendServer() {
  const serverDist = path.join(__dirname, '../../server/dist/index.js');
  console.log('🚀 Spawning Fastify Backend & PLC Gateway at:', serverDist);

  serverProcess = fork(serverDist, [], {
    env: {
      ...process.env,
      PORT: '5000',
      HOST: '127.0.0.1',
      NODE_ENV: 'production',
    },
    stdio: 'inherit',
  });

  serverProcess.on('error', (err) => {
    console.error('Failed to spawn server process:', err);
  });
}

function createWindow() {
  mainWindow = new BrowserWindow({
    width: 1280,
    height: 800,
    minWidth: 1024,
    minHeight: 768,
    kiosk: false, // Set to true for locked kiosk terminal deployment
    fullscreen: false,
    autoHideMenuBar: true,
    backgroundColor: '#020617',
    webPreferences: {
      preload: path.join(__dirname, 'preload.js'),
      nodeIntegration: false,
      contextIsolation: true,
    },
  });

  const clientDist = path.join(__dirname, '../../client/dist/index.html');
  mainWindow.loadFile(clientDist).catch(() => {
    // If dev mode, fallback to Vite dev server
    mainWindow?.loadURL('http://localhost:3000');
  });

  mainWindow.on('closed', () => {
    mainWindow = null;
  });
}

app.whenReady().then(() => {
  startBackendServer();

  // Wait 1s for Fastify server to bind port
  setTimeout(() => {
    createWindow();
  }, 1000);

  // Global F11 shortcut to toggle kiosk fullscreen
  globalShortcut.register('F11', () => {
    if (mainWindow) {
      const isFullScreen = mainWindow.isFullScreen();
      mainWindow.setFullScreen(!isFullScreen);
    }
  });

  app.on('activate', () => {
    if (BrowserWindow.getAllWindows().length === 0) {
      createWindow();
    }
  });
});

app.on('window-all-closed', () => {
  if (serverProcess) {
    serverProcess.kill();
    serverProcess = null;
  }
  if (process.platform !== 'darwin') {
    app.quit();
  }
});

app.on('will-quit', () => {
  globalShortcut.unregisterAll();
  if (serverProcess) {
    serverProcess.kill();
    serverProcess = null;
  }
});
