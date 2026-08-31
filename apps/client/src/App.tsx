import React, { useState, useEffect } from 'react';
import { AppLayout } from './components/layout/AppLayout.js';
import { ActiveTab } from './components/layout/Sidebar.js';
import { DashboardView } from './views/DashboardView.js';
import { usePlcStore } from './stores/usePlcStore.js';

export const App: React.FC = () => {
  const [activeTab, setActiveTab] = useState<ActiveTab>('PRODUCTION');
  const { setConnected, updateTag } = usePlcStore();

  useEffect(() => {
    // Connect to WebSocket gateway
    const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
    const wsUrl = `${protocol}//${window.location.host}/ws`;
    
    let ws: WebSocket | null = null;
    try {
      ws = new WebSocket(wsUrl);
      
      ws.onopen = () => {
        setConnected(true, true);
        console.log('✅ Connected to HMI WebSocket gateway');
      };

      ws.onmessage = (event) => {
        try {
          const msg = JSON.parse(event.data);
          if (msg.type === 'TAG_UPDATES') {
            msg.payload.forEach((u: any) => updateTag(u));
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
    };
  }, [setConnected, updateTag]);

  return (
    <AppLayout activeTab={activeTab} onTabChange={setActiveTab}>
      {activeTab === 'PRODUCTION' && <DashboardView />}
      {activeTab === 'MANUAL' && <DashboardView />}
      {activeTab !== 'PRODUCTION' && activeTab !== 'MANUAL' && (
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
