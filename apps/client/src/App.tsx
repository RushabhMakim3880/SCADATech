import React, { useState, useEffect } from 'react';
import { AppLayout } from './components/layout/AppLayout.js';
import { ActiveTab } from './components/layout/Sidebar.js';
import { DashboardView } from './views/DashboardView.js';
import { ManualControlView } from './views/ManualControlView.js';
import { MachineSetupView } from './views/MachineSetupView.js';
import { TagMasterView } from './views/TagMasterView.js';
import { usePlcStore } from './stores/usePlcStore.js';
import { wsClient } from './services/wsClient.js';

export const App: React.FC = () => {
  const [activeTab, setActiveTab] = useState<ActiveTab>('PRODUCTION');
  const { setConnected, updateTag, setAlarms } = usePlcStore();

  useEffect(() => {
    // Connect to WebSocket gateway
    const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
    const host = window.location.port === '3000' ? 'localhost:5000' : window.location.host;
    const wsUrl = `${protocol}//${host}/ws`;

    let ws: WebSocket | null = null;
    try {
      ws = new WebSocket(wsUrl);
      wsClient.setSocket(ws);

      ws.onopen = () => {
        setConnected(true, true);
        console.log('✅ Connected to HMI WebSocket gateway');
      };

      ws.onmessage = (event) => {
        try {
          const msg = JSON.parse(event.data);
          if (msg.type === 'TAG_UPDATES' && Array.isArray(msg.payload)) {
            msg.payload.forEach((u: any) => updateTag(u));
          } else if (msg.type === 'ACTIVE_ALARMS' && Array.isArray(msg.payload)) {
            setAlarms(msg.payload);
          } else if (msg.type === 'PLC_STATUS') {
            setConnected(msg.payload.connected, msg.payload.isSimulator);
          }
        } catch (e) {
          console.error('WS Parse Error', e);
        }
      };

      ws.onclose = () => {
        setConnected(false);
      };
    } catch (err) {
      console.warn('WS Connection failed, running in offline demo mode', err);
      setConnected(true, true);
    }

    return () => {
      ws?.close();
      wsClient.setSocket(null);
    };
  }, [setConnected, updateTag, setAlarms]);

  return (
    <AppLayout activeTab={activeTab} onTabChange={setActiveTab}>
      {activeTab === 'PRODUCTION' && <DashboardView />}
      {activeTab === 'MANUAL' && <ManualControlView />}
      {activeTab === 'MACHINE_SETUP' && <MachineSetupView />}
      {activeTab === 'TAGS' && <TagMasterView />}
      {activeTab !== 'PRODUCTION' &&
        activeTab !== 'MANUAL' &&
        activeTab !== 'MACHINE_SETUP' &&
        activeTab !== 'TAGS' && (
          <div className="p-8 flex flex-col items-center justify-center h-full text-center text-slate-400">
            <div className="text-xl font-bold text-slate-200 mb-2">
              {activeTab.replace('_', ' ')} Module
            </div>
            <p className="text-sm max-w-md">
              This module is being transitioned in the upcoming phase of the TypeScript Full-Stack migration roadmap.
            </p>
          </div>
        )}
    </AppLayout>
  );
};
