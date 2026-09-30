import React, { useState, useEffect, useRef } from 'react';
import { usePlcStore } from '../../stores/usePlcStore.js';
import { useAuthStore } from '../../stores/useAuthStore.js';
import { ActiveTab } from './Sidebar.js';
import { SecretDevModal } from '../common/SecretDevModal.js';
import {
  Bell,
  User,
  Activity,
  Cpu,
  LayoutGrid,
  ShieldCheck,
  ShieldAlert,
  Maximize2,
  Minimize2,
  Menu,
} from 'lucide-react';

interface TopHeaderProps {
  activeTab?: ActiveTab;
  onNavigateHome?: () => void;
  onTabChange?: (tab: ActiveTab) => void;
  onToggleSidebar?: () => void;
}

export const TopHeader: React.FC<TopHeaderProps> = ({
  activeTab,
  onNavigateHome,
  onTabChange,
  onToggleSidebar,
}) => {
  const { isConnected, activeAlarms, hydraulicPressureBar, feedPositionMm, mode, setMode, eStopOk } = usePlcStore();
  const { currentUser, openPinModal } = useAuthStore();

  const isSuper = currentUser?.role === 'SUPER_ADMIN';
  const isAdmin = currentUser?.role === 'ADMIN';

  const devClickCount = useRef(0);
  const [isSecretModalOpen, setIsSecretModalOpen] = useState(false);
  const [isFullscreen, setIsFullscreen] = useState(false);

  const handleLogoClick = () => {
    devClickCount.current += 1;
    if (devClickCount.current >= 5) {
      devClickCount.current = 0;
      setIsSecretModalOpen(true);
    }
  };

  const toggleFullscreen = () => {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen().catch(() => {});
      setIsFullscreen(true);
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen().catch(() => {});
        setIsFullscreen(false);
      }
    }
  };

  useEffect(() => {
    const handleFsChange = () => setIsFullscreen(Boolean(document.fullscreenElement));
    document.addEventListener('fullscreenchange', handleFsChange);
    return () => document.removeEventListener('fullscreenchange', handleFsChange);
  }, []);

  // Keyboard shortcut to open developer modal: Ctrl+Shift+D or Ctrl+Alt+S
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (
        (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'd') ||
        (e.ctrlKey && e.altKey && e.key.toLowerCase() === 's')
      ) {
        e.preventDefault();
        setIsSecretModalOpen(true);
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, []);

  const cycleMode = () => {
    if (mode === 'MANUAL') setMode('SEMI_AUTO');
    else if (mode === 'SEMI_AUTO') setMode('AUTO');
    else setMode('MANUAL');
  };

  return (
    <header className="app-header shrink-0 flex items-center justify-between px-2 sm:px-4 md:px-5 select-none z-30 relative bg-gradient-to-r from-[#090d15] via-[#0f1523] to-[#090d15] border-b-2 border-[#1c2637] shadow-xl min-h-[56px] h-14 sm:h-16 gap-2 sm:gap-3 overflow-hidden">
      {/* 1. Left Zone: Mobile Menu Toggle, SCADA Home Button & Machine Identifier */}
      <div className="flex items-center gap-2 sm:gap-3 shrink-0 min-w-0">
        {/* Mobile/Tablet Drawer Toggle */}
        <button
          type="button"
          onClick={onToggleSidebar}
          className="md:hidden p-2 rounded-lg bg-[#131b28] border border-[#253347] text-slate-300 hover:text-white active:scale-95 transition-all"
          title="Toggle Navigation Menu"
        >
          <Menu className="w-5 h-5 text-sky-400" />
        </button>

        {/* Glove-Friendly SCADA Home Touch Button */}
        <button
          type="button"
          onClick={() => {
            if (onNavigateHome) onNavigateHome();
            else if (onTabChange) onTabChange('DASHBOARD');
          }}
          className={`h-9 sm:h-11 px-2.5 sm:px-3.5 rounded-lg flex items-center gap-2 font-black text-xs transition-all border touch-action-manipulation active:scale-95 shadow-md shrink-0 ${
            activeTab === 'DASHBOARD'
              ? 'bg-gradient-to-b from-sky-600 to-sky-700 border-sky-400 text-white shadow-sky-500/20'
              : 'bg-[#131b28] border-[#253347] text-slate-200 hover:bg-[#1a2536] hover:border-sky-500/50'
          }`}
          title="Return to SCADA Home Launcher"
        >
          <LayoutGrid className="w-4 h-4 sm:w-5 sm:h-5 text-sky-400" />
          <span className="tracking-wider uppercase font-sans hidden md:inline">SCADA HOME</span>
        </button>

        <div className="h-7 w-px bg-slate-700/60 hidden sm:block shrink-0" />

        {/* Machine Brand Badge with Secret Developer Tap Listener */}
        <div className="flex items-center gap-2 sm:gap-2.5 shrink-0">
          <div
            onClick={handleLogoClick}
            className="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-gradient-to-br from-[#1c283a] to-[#0f1724] border border-[#2b3d56] flex items-center justify-center font-black text-white text-xs tracking-wider cursor-pointer active:scale-90 transition-transform shadow-inner shrink-0"
            title="HPT Innovance CNC (Tap 5x for Developer Access)"
          >
            HPT
          </div>
          <div className="leading-tight shrink-0">
            <div className="flex items-center gap-1.5 sm:gap-2 whitespace-nowrap">
              <span className="font-black text-xs sm:text-sm text-white tracking-wide">
                HPT CNC SCADA
              </span>
              <span className="text-[9px] sm:text-[10px] bg-sky-950 text-sky-300 font-extrabold px-1.5 py-0.5 rounded border border-sky-700 font-mono">
                HA-203
              </span>
            </div>
            <span className="text-[10px] sm:text-[11px] text-slate-400 font-medium hidden 2xl:block truncate max-w-[220px]">
              6-Head Angle Punching & Shearing Center
            </span>
          </div>
        </div>
      </div>

      {/* 2. Center Zone: Mode Switcher & Digital DRO Display */}
      <div className="hidden lg:flex items-center gap-2 xl:gap-3 shrink-0 min-w-0">
        {/* Full 3-Button Machine Mode Switcher (Visible on XL screens) */}
        <div className="hidden xl:flex items-center bg-[#06090e] p-1 rounded-xl border border-[#1e293b] gap-1 shadow-inner shrink-0">
          {(
            [
              { id: 'MANUAL', label: 'MANUAL' },
              { id: 'SEMI_AUTO', label: 'SEMI AUTO' },
              { id: 'AUTO', label: 'AUTO RUN' },
            ] as const
          ).map((m) => {
            const isActive = mode === m.id;
            return (
              <button
                key={m.id}
                onClick={() => setMode(m.id)}
                className={`h-9 px-3 text-xs font-black rounded-lg flex items-center gap-1.5 transition-all active:scale-95 whitespace-nowrap ${
                  isActive
                    ? m.id === 'AUTO'
                      ? 'bg-gradient-to-b from-emerald-600 to-emerald-700 text-white border border-emerald-400 shadow-md shadow-emerald-900/40'
                      : m.id === 'SEMI_AUTO'
                      ? 'bg-gradient-to-b from-amber-600 to-amber-700 text-white border border-amber-400 shadow-md shadow-amber-900/40'
                      : 'bg-gradient-to-b from-sky-600 to-sky-700 text-white border border-sky-400 shadow-md shadow-sky-900/40'
                    : 'text-slate-400 hover:bg-[#141c2a] hover:text-slate-200'
                }`}
              >
                <span
                  className={`w-2 h-2 rounded-full shrink-0 ${
                    isActive
                      ? m.id === 'AUTO'
                        ? 'bg-emerald-300 shadow-[0_0_8px_#34d399]'
                        : m.id === 'SEMI_AUTO'
                        ? 'bg-amber-300 shadow-[0_0_8px_#fbbf24]'
                        : 'bg-sky-300 shadow-[0_0_8px_#38bdf8]'
                      : 'bg-slate-600'
                  }`}
                />
                <span className="tracking-wide">{m.label}</span>
              </button>
            );
          })}
        </div>

        {/* Compact Mode Pill (Visible between LG and XL screens to prevent crowding) */}
        <button
          type="button"
          onClick={cycleMode}
          className={`xl:hidden flex items-center gap-1.5 h-9 px-3 rounded-xl border text-xs font-black transition-all active:scale-95 whitespace-nowrap ${
            mode === 'AUTO'
              ? 'bg-emerald-950/80 text-emerald-300 border-emerald-500 shadow-sm'
              : mode === 'SEMI_AUTO'
              ? 'bg-amber-950/80 text-amber-300 border-amber-500 shadow-sm'
              : 'bg-sky-950/80 text-sky-300 border-sky-500 shadow-sm'
          }`}
          title="Click to cycle machine mode (MANUAL / SEMI / AUTO)"
        >
          <span
            className={`w-2 h-2 rounded-full ${
              mode === 'AUTO' ? 'bg-emerald-400' : mode === 'SEMI_AUTO' ? 'bg-amber-400' : 'bg-sky-400'
            }`}
          />
          <span>{mode === 'SEMI_AUTO' ? 'SEMI' : mode}</span>
        </button>

        {/* Safety Interlock Indicator */}
        <div
          className={`hidden 2xl:flex items-center gap-2 px-3 h-9 sm:h-10 rounded-xl border text-xs font-mono font-black shrink-0 whitespace-nowrap ${
            eStopOk
              ? 'bg-[#091811] text-emerald-300 border-emerald-700/80 shadow-[0_0_10px_rgba(16,185,129,0.15)]'
              : 'bg-[#2b0d0d] text-rose-300 border-rose-600 animate-pulse shadow-[0_0_15px_rgba(244,63,94,0.3)]'
          }`}
        >
          <span className={`w-2 h-2 rounded-full ${eStopOk ? 'bg-emerald-400' : 'bg-rose-500'}`} />
          <span>{eStopOk ? 'SAFETY OK' : 'E-STOP TRIPPED'}</span>
        </div>

        {/* Precision High-Visibility Digital DRO */}
        <div className="cnc-dro flex items-center gap-2 h-9 sm:h-10 px-3 rounded-xl shrink-0 whitespace-nowrap">
          <span className="text-[10px] font-black text-slate-400 uppercase tracking-widest hidden sm:inline">
            CARRIAGE (X):
          </span>
          <span className="text-[10px] font-black text-slate-400 uppercase tracking-widest sm:hidden">
            X:
          </span>
          <span className="cnc-dro-val text-base sm:text-lg">{feedPositionMm.toFixed(2)}</span>
          <span className="cnc-dro-unit text-[11px]">mm</span>
        </div>
      </div>

      {/* 3. Right Zone: Telemetry, Alarms, Fullscreen, & Operator Profile */}
      <div className="flex items-center gap-1.5 sm:gap-2.5 shrink-0">
        {/* Hydraulic Pressure (HPU) */}
        <div className="hidden md:flex items-center gap-1.5 sm:gap-2 bg-[#080d14] px-2.5 sm:px-3 h-9 sm:h-11 rounded-xl border border-[#1f2b3e] text-xs shrink-0 whitespace-nowrap">
          <Activity className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-400 shrink-0" />
          <span className="text-slate-400 font-extrabold hidden lg:inline">HPU:</span>
          <span className="font-mono font-black text-amber-300 text-xs sm:text-sm">
            {hydraulicPressureBar.toFixed(1)}
          </span>
          <span className="text-[10px] text-slate-400 font-bold">BAR</span>
        </div>

        {/* PLC Connection */}
        <div className="flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 h-9 sm:h-11 rounded-xl bg-[#080d14] border border-[#1f2b3e] text-xs shrink-0 whitespace-nowrap">
          <Cpu className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-400 shrink-0" />
          <span className="font-extrabold text-slate-200 hidden xl:inline">
            {isConnected ? 'PLC ONLINE' : 'PLC OFFLINE'}
          </span>
          <span
            className={`w-2.5 h-2.5 rounded-full ${
              isConnected ? 'bg-emerald-400 shadow-[0_0_8px_#34d399]' : 'bg-rose-500 animate-ping'
            }`}
          />
        </div>

        {/* Active Alarms Button */}
        <button
          type="button"
          onClick={() => onTabChange && onTabChange('ALARMS')}
          className={`relative h-9 sm:h-11 px-2.5 sm:px-3.5 rounded-xl border flex items-center justify-center transition-all active:scale-95 shrink-0 ${
            activeAlarms.length > 0
              ? 'bg-rose-950/80 border-rose-500 text-rose-200 shadow-[0_0_12px_rgba(244,63,94,0.3)] animate-pulse'
              : 'bg-[#121926] border-[#222e42] text-slate-300 hover:bg-[#1a2538]'
          }`}
          title="Active Alarms"
        >
          <Bell className="w-4 h-4" />
          {activeAlarms.length > 0 && (
            <span className="ml-1 sm:ml-1.5 bg-rose-600 text-white text-[9px] sm:text-[10px] font-black px-1.5 py-0.5 rounded-full">
              {activeAlarms.length}
            </span>
          )}
        </button>

        {/* Fullscreen / HDMI Display Maximize Button */}
        <button
          type="button"
          onClick={toggleFullscreen}
          className="h-9 sm:h-11 px-2.5 sm:px-3 rounded-xl bg-[#121926] border border-[#24334a] hover:bg-[#1b2639] text-slate-300 hover:text-white transition-all flex items-center justify-center active:scale-95 shadow-sm shrink-0"
          title={isFullscreen ? 'Exit Fullscreen' : 'Expand Fullscreen / Fill HDMI Display'}
        >
          {isFullscreen ? <Minimize2 className="w-4 h-4 text-sky-400" /> : <Maximize2 className="w-4 h-4 text-sky-400" />}
        </button>

        {/* Touch Operator Profile with Click-to-Switch PIN Keypad */}
        <button
          type="button"
          onClick={() => openPinModal()}
          className="h-9 sm:h-11 pl-2 pr-2.5 sm:pl-2.5 sm:pr-3.5 rounded-xl bg-[#121926] border border-[#24334a] hover:bg-[#1b2639] transition-all flex items-center gap-2 text-left active:scale-95 shadow-sm shrink-0"
          title="Tap to switch operator / sign in with PIN"
        >
          <div
            className={`w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 ${
              isSuper
                ? 'bg-rose-950 text-rose-300 border border-rose-700'
                : isAdmin
                ? 'bg-amber-950 text-amber-300 border border-amber-700'
                : 'bg-sky-950 text-sky-300 border border-sky-700'
            }`}
          >
            {isSuper ? (
              <ShieldAlert className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
            ) : isAdmin ? (
              <ShieldCheck className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
            ) : (
              <User className="w-3.5 h-3.5 sm:w-4 sm:h-4" />
            )}
          </div>

          <div className="hidden lg:block leading-tight">
            <div className="font-extrabold text-white text-xs truncate max-w-[100px] xl:max-w-[120px]">
              {currentUser?.name || 'Line Operator'}
            </div>
            <div className="text-[9px] sm:text-[10px] font-mono font-bold text-sky-400 uppercase">
              {isSuper ? 'OEM DEVELOPER' : isAdmin ? 'PLANT ADMIN' : 'OPERATOR'}
            </div>
          </div>
        </button>

        {/* OEM Super Admin Quick Shortcut */}
        {isSuper && (
          <button
            type="button"
            onClick={() => onTabChange && onTabChange('MENU_CONFIG')}
            className={`h-9 sm:h-11 px-2.5 sm:px-3.5 rounded-xl border flex items-center gap-1.5 text-xs font-bold transition-all active:scale-95 shrink-0 ${
              activeTab === 'MENU_CONFIG'
                ? 'bg-rose-900 border-rose-400 text-white shadow'
                : 'bg-rose-950/70 border-rose-800/80 text-rose-300 hover:bg-rose-900/80'
            }`}
            title="Super Admin OEM Menu Config"
          >
            <ShieldAlert className="w-4 h-4" />
            <span className="hidden xl:inline">OEM CONFIG</span>
          </button>
        )}
      </div>

      {/* Secret Developer Passcode Access Modal */}
      <SecretDevModal
        isOpen={isSecretModalOpen}
        onClose={() => setIsSecretModalOpen(false)}
        onSuccessNavigate={() => onTabChange && onTabChange('MENU_CONFIG')}
      />
    </header>
  );
};
