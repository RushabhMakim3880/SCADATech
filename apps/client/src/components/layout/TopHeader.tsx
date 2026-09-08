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
} from 'lucide-react';

interface TopHeaderProps {
  activeTab?: ActiveTab;
  onNavigateHome?: () => void;
  onTabChange?: (tab: ActiveTab) => void;
}

export const TopHeader: React.FC<TopHeaderProps> = ({
  activeTab,
  onNavigateHome,
  onTabChange,
}) => {
  const { isConnected, activeAlarms, hydraulicPressureBar, feedPositionMm, mode, setMode, eStopOk } = usePlcStore();
  const { currentUser, openPinModal } = useAuthStore();

  const isSuper = currentUser?.role === 'SUPER_ADMIN';
  const isAdmin = currentUser?.role === 'ADMIN';

  const devClickCount = useRef(0);
  const [isSecretModalOpen, setIsSecretModalOpen] = useState(false);

  const handleLogoClick = () => {
    devClickCount.current += 1;
    if (devClickCount.current >= 5) {
      devClickCount.current = 0;
      setIsSecretModalOpen(true);
    }
  };

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

  return (
    <header className="app-header flex items-center justify-between px-4 select-none z-30 relative bg-[#10151f] border-b-2 border-[#1f2a3a] shadow-lg">
      {/* 1. Left Zone: SCADA Home Button & Machine Identifier */}
      <div className="flex items-center gap-3">
        {/* Generous Glove-Friendly SCADA Home Touch Button */}
        <button
          type="button"
          onClick={() => {
            if (onNavigateHome) onNavigateHome();
            else if (onTabChange) onTabChange('DASHBOARD');
          }}
          className={`h-11 px-4 rounded-lg flex items-center gap-2.5 font-black text-xs transition-all border touch-action-manipulation ${
            activeTab === 'DASHBOARD'
              ? 'bg-[#1e3a8a] border-[#38bdf8] text-white shadow-md'
              : 'bg-[#17202e] border-[#29364a] text-slate-200 hover:bg-[#1f2c3f] active:translate-y-0.5'
          }`}
          title="Return to SCADA Home Launcher"
        >
          <LayoutGrid className="w-5 h-5 text-sky-400" />
          <span className="tracking-wider uppercase font-sans">SCADA HOME</span>
        </button>

        <div className="h-7 w-px bg-slate-700/60 hidden sm:block" />

        {/* Machine Brand Badge with Secret Developer Tap Listener */}
        <div className="flex items-center gap-2.5">
          <div
            onClick={handleLogoClick}
            className="w-9 h-9 rounded bg-[#162130] border border-[#2b3a4f] flex items-center justify-center font-black text-white text-xs tracking-wider cursor-pointer active:scale-95 transition-transform"
            title="HPT Innovance CNC (Tap 5x for Developer Access)"
          >
            HPT
          </div>
          <div className="leading-tight">
            <div className="flex items-center gap-2">
              <span className="font-black text-sm text-white tracking-wide">
                HPT CNC SCADA
              </span>
              <span className="text-[10px] bg-[#1a2636] text-sky-400 font-extrabold px-1.5 py-0.5 rounded border border-[#26374d] font-mono">
                HA-203
              </span>
            </div>
            <span className="text-[11px] text-slate-400 font-medium hidden md:block">
              6-Head Angle Processing System
            </span>
          </div>
        </div>
      </div>

      {/* 2. Center Zone: Mode Switcher & Digital DRO Display */}
      <div className="hidden lg:flex items-center gap-4">
        {/* Machine Mode Switcher (Tactile Glove-Friendly Buttons) */}
        <div className="flex items-center bg-[#090d14] p-1 rounded-lg border border-[#1f293d] gap-1 shadow-inner">
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
                className={`h-9 px-3.5 text-xs font-black rounded flex items-center gap-2 transition-all ${
                  isActive
                    ? m.id === 'AUTO'
                      ? 'bg-[#065f46] text-white border border-[#10b981] shadow-sm'
                      : 'bg-[#1e40af] text-white border border-[#3b82f6] shadow-sm'
                    : 'text-slate-400 hover:bg-[#162030] hover:text-slate-200'
                }`}
              >
                <span
                  className={`led-indicator ${
                    isActive
                      ? m.id === 'AUTO'
                        ? 'led-green'
                        : 'led-amber'
                      : 'led-off'
                  }`}
                />
                <span className="tracking-wide">{m.label}</span>
              </button>
            );
          })}
        </div>

        {/* Safety Interlock Indicator */}
        <div
          className={`hidden xl:flex items-center gap-2 px-3 h-11 rounded-lg border text-xs font-mono font-bold ${
            eStopOk
              ? 'bg-[#0b1612] text-emerald-300 border-emerald-900/60'
              : 'bg-[#291010] text-rose-300 border-rose-900/80 animate-pulse'
          }`}
        >
          <span className={`led-indicator ${eStopOk ? 'led-green' : 'led-red'}`} />
          <span>{eStopOk ? 'SAFETY OK' : 'E-STOP'}</span>
        </div>

        {/* Precision High-Visibility Digital DRO */}
        <div className="digital-dro-box h-11 px-3.5">
          <span className="digital-dro-label">CARRIAGE (X):</span>
          <span className="digital-dro-val text-lg">{feedPositionMm.toFixed(2)} mm</span>
        </div>
      </div>

      {/* 3. Right Zone: Telemetry, Alarms & Operator Profile */}
      <div className="flex items-center gap-3">
        {/* Hydraulic Pressure */}
        <div className="hidden sm:flex items-center gap-2 bg-[#0a0e16] px-3 py-1.5 rounded-lg border border-[#1d2737] text-xs h-10">
          <Activity className="w-4 h-4 text-amber-400 shrink-0" />
          <span className="text-slate-400 font-bold">HPU:</span>
          <span className="font-mono font-bold text-amber-300">{hydraulicPressureBar.toFixed(1)} bar</span>
        </div>

        {/* PLC Connection */}
        <div className="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#0a0e16] border border-[#1d2737] text-xs h-10">
          <Cpu className="w-4 h-4 text-slate-400 shrink-0" />
          <span className="font-bold text-slate-300 hidden sm:inline">
            {isConnected ? 'PLC ONLINE' : 'PLC OFFLINE'}
          </span>
          <span className={`led-indicator ${isConnected ? 'led-green' : 'led-red'}`} />
        </div>

        {/* Active Alarms Button */}
        <button
          type="button"
          onClick={() => onTabChange && onTabChange('ALARMS')}
          className={`relative h-10 px-3 rounded-lg border flex items-center justify-center transition-colors ${
            activeAlarms.length > 0
              ? 'bg-[#3b1212] border-red-700 text-red-200'
              : 'bg-[#151c27] border-[#222d3d] text-slate-300 hover:bg-[#1c2635]'
          }`}
          title="Active Alarms"
        >
          <Bell className="w-4 h-4" />
          {activeAlarms.length > 0 && (
            <span className="ml-1.5 bg-red-600 text-white text-[10px] font-black px-1.5 py-0.2 rounded-full animate-pulse">
              {activeAlarms.length}
            </span>
          )}
        </button>

        {/* Touch Operator Profile with Click-to-Switch PIN Keypad */}
        <button
          type="button"
          onClick={() => openPinModal()}
          className="h-10 pl-2.5 pr-3 rounded-lg bg-[#141b26] border border-[#243042] hover:bg-[#1a2332] transition-colors flex items-center gap-2.5 text-left"
          title="Tap to switch operator / sign in with PIN"
        >
          <div
            className={`w-7 h-7 rounded flex items-center justify-center font-bold text-xs ${
              isSuper
                ? 'bg-rose-950 text-rose-300 border border-rose-800'
                : isAdmin
                ? 'bg-amber-950 text-amber-300 border border-amber-800'
                : 'bg-emerald-950 text-emerald-300 border border-emerald-800'
            }`}
          >
            {isSuper ? (
              <ShieldAlert className="w-3.5 h-3.5" />
            ) : isAdmin ? (
              <ShieldCheck className="w-3.5 h-3.5" />
            ) : (
              <User className="w-3.5 h-3.5" />
            )}
          </div>

          <div className="hidden sm:block leading-tight">
            <div className="font-extrabold text-white text-xs truncate max-w-[120px]">
              {currentUser?.name || 'Line Operator'}
            </div>
            <div className="text-[10px] font-mono font-bold text-sky-400 uppercase">
              {isSuper ? 'OEM DEVELOPER' : isAdmin ? 'PLANT ADMIN' : 'OPERATOR'}
            </div>
          </div>
        </button>

        {/* OEM Super Admin Quick Shortcut (Visible ONLY when logged in as Super Admin) */}
        {isSuper && (
          <button
            type="button"
            onClick={() => onTabChange && onTabChange('MENU_CONFIG')}
            className={`h-10 px-3 rounded-lg border flex items-center gap-1.5 text-xs font-bold transition-all ${
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
