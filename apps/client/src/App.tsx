import React, { useState, useEffect } from 'react';
import { AppLayout } from './components/layout/AppLayout.js';
import { ActiveTab } from './components/layout/Sidebar.js';
import { DashboardView } from './views/DashboardView.js';
import { LiveProductionView } from './views/LiveProductionView.js';
import { ManualControlView } from './views/ManualControlView.js';
import { RecipeMasterView } from './views/RecipeMasterView.js';
import { NestingAlignmentView } from './views/NestingAlignmentView.js';
import { MachineSetupView } from './views/MachineSetupView.js';
import { ToolingWearView } from './views/ToolingWearView.js';
import { IoDiagnosticsView } from './views/IoDiagnosticsView.js';
import { OeeAnalyticsView } from './views/OeeAnalyticsView.js';
import { TagMasterView } from './views/TagMasterView.js';
import { AlarmsView } from './views/AlarmsView.js';
import { UserManagementView } from './views/UserManagementView.js';
import { MenuConfigView } from './views/MenuConfigView.js';
import { usePlcStore } from './stores/usePlcStore.js';
import { useAuthStore } from './stores/useAuthStore.js';
import { wsClient } from './services/wsClient.js';

export const App: React.FC = () => {
  // Default to central SCADA Home Dashboard (Requirement 4)
  const [activeTab, setActiveTab] = useState<ActiveTab>('DASHBOARD');
  const { setConnected, updateTag, setAlarms } = usePlcStore();
  const { fetchCurrentUser } = useAuthStore();

  useEffect(() => {
    // 1. Fetch initial user session
    fetchCurrentUser();

    // 2. Connect to WebSocket gateway via wsClient
    const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
    const host = window.location.port === '3000' ? 'localhost:5000' : window.location.host;
    const wsUrl = `${protocol}//${host}/ws`;

    wsClient.connect(wsUrl, {
      onOpen: () => {
        setConnected(true, true);
      },
      onMessage: (msg: any) => {
        if (msg.type === 'TAG_UPDATES' && Array.isArray(msg.payload)) {
          msg.payload.forEach((u: any) => updateTag(u));
        } else if (msg.type === 'ACTIVE_ALARMS' && Array.isArray(msg.payload)) {
          setAlarms(msg.payload);
        } else if (msg.type === 'PLC_STATUS') {
          setConnected(msg.payload.connected, msg.payload.isSimulator);
        }
      },
      onClose: () => {
        setConnected(false);
      },
      onError: (err: any) => {
        console.warn('WS Gateway warning', err);
      },
    });

    return () => {
      wsClient.disconnect();
    };
  }, [setConnected, updateTag, setAlarms, fetchCurrentUser]);

  return (
    <AppLayout activeTab={activeTab} onTabChange={setActiveTab}>
      {activeTab === 'DASHBOARD' && <DashboardView onNavigate={setActiveTab} />}
      {activeTab === 'PRODUCTION' && <LiveProductionView />}
      {activeTab === 'OEE_ANALYTICS' && <OeeAnalyticsView />}
      {activeTab === 'MANUAL' && <ManualControlView />}
      {activeTab === 'RECIPES' && <RecipeMasterView />}
      {activeTab === 'ALIGNMENT' && <NestingAlignmentView />}
      {activeTab === 'IO_DIAGNOSTICS' && <IoDiagnosticsView />}
      {activeTab === 'TOOLING_WEAR' && <ToolingWearView />}
      {activeTab === 'MACHINE_SETUP' && <MachineSetupView />}
      {activeTab === 'TAGS' && <TagMasterView />}
      {activeTab === 'ALARMS' && <AlarmsView />}
      {activeTab === 'USER_MANAGEMENT' && <UserManagementView />}
      {activeTab === 'MENU_CONFIG' && <MenuConfigView />}
    </AppLayout>
  );
};
