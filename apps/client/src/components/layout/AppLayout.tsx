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
  return (
    <div className="h-screen w-screen flex flex-col bg-[#070b12] text-slate-100 overflow-hidden select-none font-sans">
      <TopHeader
        activeTab={activeTab}
        onNavigateHome={() => onTabChange('DASHBOARD')}
        onTabChange={onTabChange}
      />
      <div className="flex-1 flex overflow-hidden min-h-0">
        <Sidebar activeTab={activeTab} onTabChange={onTabChange} />
        <main className="flex-1 flex flex-col overflow-y-auto bg-[#070b12] text-slate-100 min-h-0">
          {children}
        </main>
      </div>

      {/* Global Touch PIN Keypad Modal */}
      <PinModal />
    </div>
  );
};
