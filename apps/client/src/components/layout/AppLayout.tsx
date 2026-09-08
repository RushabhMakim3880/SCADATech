import React from 'react';
import { TopHeader } from './TopHeader.js';
import { Sidebar, ActiveTab } from './Sidebar.js';
import { PinModal } from '../common/PinModal.js';

interface AppLayoutProps {
  activeTab: ActiveTab;
  onTabChange: (tab: ActiveTab) => void;
  children: React.ReactNode;
}

export const AppLayout: React.FC<AppLayoutProps> = ({ activeTab, onTabChange, children }) => {
  const isDashboard = activeTab === 'DASHBOARD';

  return (
    <div className="h-screen w-screen flex flex-col bg-[#0b0f17] text-slate-100 overflow-hidden select-none font-sans">
      <TopHeader
        activeTab={activeTab}
        onNavigateHome={() => onTabChange('DASHBOARD')}
        onTabChange={onTabChange}
      />
      <div className="flex-1 flex overflow-hidden">
        {/* On Dashboard, give full focus to the square button launcher; show sidebar on all modules */}
        {!isDashboard && <Sidebar activeTab={activeTab} onTabChange={onTabChange} />}
        <main className="flex-1 flex flex-col overflow-hidden bg-[#0b0f17] text-slate-100">
          {children}
        </main>
      </div>

      {/* Global Touch PIN Keypad Modal */}
      <PinModal />
    </div>
  );
};
