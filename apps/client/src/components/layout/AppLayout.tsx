import React, { useState, useEffect } from 'react';
import { TopHeader } from './TopHeader.js';
import { Sidebar, ActiveTab } from './Sidebar.js';
import { PinModal } from '../common/PinModal.js';

interface AppLayoutProps {
  activeTab: ActiveTab;
  onTabChange: (tab: ActiveTab) => void;
  children: React.ReactNode;
}

export const AppLayout: React.FC<AppLayoutProps> = ({ activeTab, onTabChange, children }) => {
  const [isSidebarCollapsed, setIsSidebarCollapsed] = useState<boolean>(() => {
    if (typeof window !== 'undefined') {
      const saved = localStorage.getItem('hpt_sidebar_collapsed');
      if (saved !== null) return saved === 'true';
      return window.innerWidth < 1280;
    }
    return false;
  });

  const [isMobileSidebarOpen, setIsMobileSidebarOpen] = useState(false);

  // Auto-collapse on small window resize if not manually toggled
  useEffect(() => {
    const handleResize = () => {
      if (window.innerWidth < 1024) {
        setIsSidebarCollapsed(true);
      }
    };
    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  }, []);

  const handleToggleCollapse = () => {
    setIsSidebarCollapsed((prev) => {
      const next = !prev;
      localStorage.setItem('hpt_sidebar_collapsed', String(next));
      return next;
    });
  };

  const handleToggleMobileSidebar = () => {
    setIsMobileSidebarOpen((prev) => !prev);
  };

  return (
    <div className="h-screen w-screen flex flex-col bg-[#070b12] text-slate-100 overflow-hidden select-none font-sans">
      <TopHeader
        activeTab={activeTab}
        onNavigateHome={() => onTabChange('DASHBOARD')}
        onTabChange={onTabChange}
        onToggleSidebar={handleToggleMobileSidebar}
      />
      <div className="flex-1 flex overflow-hidden min-h-0 relative">
        <Sidebar
          activeTab={activeTab}
          onTabChange={onTabChange}
          isCollapsed={isSidebarCollapsed}
          onToggleCollapse={handleToggleCollapse}
          isMobileOpen={isMobileSidebarOpen}
          onCloseMobile={() => setIsMobileSidebarOpen(false)}
        />
        <main className="flex-1 flex flex-col overflow-y-auto bg-[#070b12] text-slate-100 min-h-0 w-full">
          {children}
        </main>
      </div>

      {/* Global Touch PIN Keypad Modal */}
      <PinModal />
    </div>
  );
};
