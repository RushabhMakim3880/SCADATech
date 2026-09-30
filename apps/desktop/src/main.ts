import { app, BrowserWindow, globalShortcut, screen } from 'electron';
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
  const primaryDisplay = screen.getPrimaryDisplay();
  const { width, height } = primaryDisplay.workAreaSize;

  mainWindow = new BrowserWindow({
    width: Math.min(1920, width),
    height: Math.min(1080, height),
    minWidth: 800,
    minHeight: 500,
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

  // Automatically maximize to fill any screen size (laptop, HDMI external monitor, 4K display)
  mainWindow.maximize();

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

  // Adapt window when external HDMI monitor or display resolution changes
  screen.on('display-metrics-changed', () => {
    if (mainWindow && !mainWindow.isDestroyed()) {
      mainWindow.maximize();
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
