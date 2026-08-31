import React from 'react';
import { TopHeader } from './TopHeader.js';
import { Sidebar, ActiveTab } from './Sidebar.js';

interface AppLayoutProps {
  activeTab: ActiveTab;
  onTabChange: (tab: ActiveTab) => void;
  children: React.ReactNode;
}

export const AppLayout: React.FC<AppLayoutProps> = ({ activeTab, onTabChange, children }) => {
  return (
    <div className="h-screen w-screen flex flex-col bg-[#d9e0e7] text-slate-900 overflow-hidden select-none font-sans">
      <TopHeader />
      <div className="flex-1 flex overflow-hidden">
        <Sidebar activeTab={activeTab} onTabChange={onTabChange} />
        <main className="flex-1 flex flex-col overflow-hidden bg-[#d9e0e7] text-slate-900">
          {children}
        </main>
      </div>
    </div>
  );
};
