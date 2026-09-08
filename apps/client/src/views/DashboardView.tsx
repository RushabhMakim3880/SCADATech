import React, { useState, useRef } from 'react';
import { usePlcStore } from '../stores/usePlcStore.js';
import { useAuthStore } from '../stores/useAuthStore.js';
import { ActiveTab } from '../components/layout/Sidebar.js';
import { SecretDevModal } from '../components/common/SecretDevModal.js';
import {
  PlaySquare,
  SlidersHorizontal,
  FileCode2,
  Layers,
  TrendingUp,
  Activity,
  Cpu,
  AlertTriangle,
  Wrench,
  Tag,
  ShieldCheck,
  Settings2,
  Lock,
  Compass,
  Gauge,
  ShieldAlert,
  Radio,
  CheckCircle2,
} from 'lucide-react';

interface DashboardViewProps {
  onNavigate: (tab: ActiveTab) => void;
}

export const DashboardView: React.FC<DashboardViewProps> = ({ onNavigate }) => {
  const {
    isConnected,
    feedPositionMm,
    hydraulicPressureBar,
    activeAlarms,
    mode,
    eStopOk,
  } = usePlcStore();

  const { currentUser, hasPermission, isSuperAdmin } = useAuthStore();
  const [logoError, setLogoError] = useState(false);
  const logoClickCount = useRef(0);
  const [isSecretModalOpen, setIsSecretModalOpen] = useState(false);

  const handleLogoTap = () => {
    logoClickCount.current += 1;
    if (logoClickCount.current >= 5) {
      logoClickCount.current = 0;
      setIsSecretModalOpen(true);
    }
  };

  // Tab mapping
  const tabMap: Record<string, ActiveTab> = {
    PRODUCTION: 'PRODUCTION',
    MANUAL: 'MANUAL',
    RECIPES: 'RECIPES',
    ALIGNMENT: 'ALIGNMENT',
    OEE_ANALYTICS: 'OEE_ANALYTICS',
    TOOLING_WEAR: 'TOOLING_WEAR',
    IO_DIAGNOSTICS: 'IO_DIAGNOSTICS',
    ALARMS: 'ALARMS',
    MACHINE_SETUP: 'MACHINE_SETUP',
    TAGS: 'TAGS',
    USERS: 'USER_MANAGEMENT',
    MENU_CONFIG: 'MENU_CONFIG',
  };

  // Permission mapping
  const permissionMap: Record<string, string> = {
    PRODUCTION: 'menu:production',
    MANUAL: 'menu:manual',
    RECIPES: 'menu:recipes',
    ALIGNMENT: 'menu:alignment',
    OEE_ANALYTICS: 'menu:oee',
    TOOLING_WEAR: 'menu:wear',
    IO_DIAGNOSTICS: 'menu:io',
    ALARMS: 'menu:alarms',
    MACHINE_SETUP: 'menu:setup',
    TAGS: 'menu:tags',
    USERS: 'menu:users',
    MENU_CONFIG: 'menu:config',
  };

  // Uniform precision industrial modules
  const modules = [
    {
      id: 'PRODUCTION',
      label: 'MANAGE PRODUCTION',
      subtitle: '2D CAD & Automatic Processing Cycle',
      icon: PlaySquare,
      readout: `X: ${feedPositionMm.toFixed(1)} mm`,
      status: 'READY',
      statusType: 'normal' as const,
    },
    {
      id: 'MANUAL',
      label: 'MANUAL CONTROL',
      subtitle: 'Feed Axis Jog & Cylinder Valves',
      icon: SlidersHorizontal,
      readout: `${hydraulicPressureBar.toFixed(0)} BAR`,
      status: 'STANDBY',
      statusType: 'warning' as const,
    },
    {
      id: 'RECIPES',
      label: 'ITEM RECIPES',
      subtitle: 'DSTV / NC1 CAD File Importer',
      icon: FileCode2,
      readout: 'TEKLA / NC1',
      status: 'ACTIVE',
      statusType: 'normal' as const,
    },
    {
      id: 'ALIGNMENT',
      label: 'PROGRAM ALIGN',
      subtitle: 'Linear Multibar Nesting & IS 802 Rules',
      icon: Layers,
      readout: 'SCRAP <1.2%',
      status: 'OPTIMIZED',
      statusType: 'normal' as const,
    },
    {
      id: 'OEE_ANALYTICS',
      label: 'SHIFT & OEE TELEMETRY',
      subtitle: 'Metric Tonnage Processed & OEE',
      icon: TrendingUp,
      readout: 'TONNAGE',
      status: 'MONITORING',
      statusType: 'normal' as const,
    },
    {
      id: 'TOOLING_WEAR',
      label: 'TOOLING LIFE & WEAR',
      subtitle: 'Stroke Counters (6 Dies & Shear)',
      icon: Activity,
      readout: '6 HEADS OK',
      status: 'NORMAL',
      statusType: 'normal' as const,
    },
    {
      id: 'IO_DIAGNOSTICS',
      label: 'PLC I/O MATRIX',
      subtitle: '32 Inputs (X0-X37) & 32 Outputs (Y0-Y37)',
      icon: Cpu,
      readout: '20 Hz BUS',
      status: '64 CHANNELS',
      statusType: 'normal' as const,
    },
    {
      id: 'ALARMS',
      label: 'ALARM CONFIG & LOGS',
      subtitle: 'Real-time Fault Interlocks & History',
      icon: AlertTriangle,
      readout: `${activeAlarms.length} FAULTS`,
      status: activeAlarms.length > 0 ? 'ALERT' : 'CLEAR',
      statusType: activeAlarms.length > 0 ? ('critical' as const) : ('normal' as const),
    },
    {
      id: 'MACHINE_SETUP',
      label: 'MACHINE SETTINGS',
      subtitle: 'HA-203 Limits & Tool Station Offsets',
      icon: Wrench,
      readout: 'HA-203',
      status: 'CONFIG',
      statusType: 'normal' as const,
    },
    {
      id: 'TAGS',
      label: 'PLC & UI TAG MASTER',
      subtitle: 'Modbus TCP & High-Speed Tag Regs',
      icon: Tag,
      readout: 'MODBUS TCP',
      status: 'REGISTERED',
      statusType: 'normal' as const,
    },
    {
      id: 'USERS',
      label: 'USER PERMISSIONS',
      subtitle: 'Plant Administrator RBAC Matrix',
      icon: ShieldCheck,
      readout: 'RBAC MATRIX',
      status: 'ADMIN',
      statusType: 'normal' as const,
    },
    {
      id: 'MENU_CONFIG',
      label: 'MENU CONFIGURATION',
      subtitle: 'OEM Button Layout & Role Rights',
      icon: Settings2,
      readout: 'OEM ONLY',
      status: 'WITH US ONLY',
      statusType: 'warning' as const,
    },
  ];

  return (
    <div className="flex-1 flex flex-col justify-between overflow-y-auto bg-[#0b1019] text-white">
      {/* 1. Industrial Machinery Brand Header */}
      <div className="bg-[#141d2b] border-b border-[#233246] px-6 py-4 shadow-md">
        <div className="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-4">
          <div className="flex items-center gap-4">
            {/* HPT Logo slot (Tap 5x for OEM Developer Access) */}
            <div
              onClick={handleLogoTap}
              className="h-12 flex items-center justify-center cursor-pointer active:scale-95 transition-transform"
              title="HPT Innovance CNC (Tap 5x for Developer Access)"
            >
              {!logoError ? (
                <img
                  src="/hpt-logo.png"
                  alt="HPT Logo"
                  onError={() => setLogoError(true)}
                  className="h-12 w-auto object-contain"
                />
              ) : (
                <div className="w-12 h-12 rounded bg-[#18202d] border border-[#2b394d] flex items-center justify-center font-black text-white text-base tracking-wider shadow-inner">
                  HPT
                </div>
              )}
            </div>

            <div>
              <div className="flex items-center gap-2.5">
                <h1 className="text-lg font-black text-white tracking-wide font-sans uppercase">
                  HYDRO POWER TECH ENGINEERING
                </h1>
                <span className="text-[10px] bg-[#162130] text-sky-400 font-extrabold px-2 py-0.5 rounded border border-[#23334a] font-mono">
                  MODEL HA-203
                </span>
              </div>
              <p className="text-xs text-slate-300 font-medium tracking-wide">
                6-Head CNC Angle Punching, Character Stamping & Shearing Touchscreen SCADA
              </p>
            </div>
          </div>

          {/* Precision Industrial Telemetry Readout Bar */}
          <div className="flex flex-wrap items-center gap-2.5">
            {/* E-Stop Status */}
            <div
              className={`flex items-center gap-2 px-3 py-1.5 rounded border text-xs font-bold font-mono ${
                eStopOk
                  ? 'bg-[#0f1f18] text-emerald-300 border-emerald-900/60'
                  : 'bg-[#291010] text-rose-300 border-rose-900/80 animate-pulse'
              }`}
            >
              <ShieldAlert className="w-4 h-4" />
              <span>{eStopOk ? 'INTERLOCK OK' : 'E-STOP ACTIVE'}</span>
            </div>

            {/* Mode */}
            <div className="flex items-center gap-2 bg-[#141b26] px-3 py-1.5 rounded border border-[#232e3e] text-xs font-bold font-mono">
              <Radio className="w-3.5 h-3.5 text-sky-400" />
              <span className="text-slate-400">MODE:</span>
              <span className="text-white font-extrabold">{mode}</span>
            </div>

            {/* Carriage DRO */}
            <div className="flex items-center gap-2 bg-[#090c12] px-3 py-1.5 rounded border border-[#1b2432] text-xs font-mono">
              <Compass className="w-3.5 h-3.5 text-sky-400" />
              <span className="text-slate-400 font-bold">X:</span>
              <span className="font-extrabold text-cyan-400 text-sm">
                {feedPositionMm.toFixed(2)} mm
              </span>
            </div>

            {/* Pressure */}
            <div className="flex items-center gap-2 bg-[#090c12] px-3 py-1.5 rounded border border-[#1b2432] text-xs font-mono">
              <Gauge className="w-3.5 h-3.5 text-amber-400" />
              <span className="text-slate-400 font-bold">HPU:</span>
              <span className="font-extrabold text-amber-300 text-sm">
                {hydraulicPressureBar.toFixed(1)} bar
              </span>
            </div>

            {/* PLC Connection */}
            <div className="flex items-center gap-2 bg-[#141b26] px-3 py-1.5 rounded border border-[#232e3e] text-xs font-bold">
              <span className={`led-indicator ${isConnected ? 'led-green' : 'led-red'}`} />
              <span className="text-slate-300 font-mono">
                {isConnected ? 'PLC 20Hz' : 'PLC OFFLINE'}
              </span>
            </div>
          </div>
        </div>
      </div>

      {/* 2. Main Touchscreen SCADA Grid Launcher */}
      <div className="flex-1 p-6 max-w-7xl w-full mx-auto flex flex-col justify-center">
        {/* Launcher Subtitle Bar */}
        <div className="flex items-center justify-between mb-4 px-1">
          <div className="text-xs font-black tracking-wider text-slate-400 uppercase flex items-center gap-2">
            <span className="w-2 h-2 rounded bg-sky-500" />
            <span>TOUCH MODULE LAUNCHER</span>
          </div>
          <div className="text-xs text-slate-400">
            Current Operator:{' '}
            <span className="font-bold text-white">
              {currentUser?.name || 'Line Operator'}
            </span>{' '}
            <span className="font-mono text-sky-400 font-bold">
              [{currentUser?.role === 'SUPER_ADMIN' ? 'OEM DEVELOPER' : currentUser?.role || 'OPERATOR'}]
            </span>
          </div>
        </div>

        {/* The Precision Monolithic Square Grid */}
        <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
          {modules.map((item) => {
            const Icon = item.icon;
            const permKey = permissionMap[item.id];
            const isMenuConfig = item.id === 'MENU_CONFIG';
            const isUsers = item.id === 'USERS';

            // Strictly hide Super Admin Menu Config from non-super admins (OEM Developer only)
            if (isMenuConfig && !isSuperAdmin()) {
              return null;
            }

            let isAllowed = true;
            if (isMenuConfig) {
              isAllowed = isSuperAdmin();
            } else if (isUsers) {
              isAllowed = hasPermission('menu:users') || currentUser?.role === 'ADMIN' || isSuperAdmin();
            } else if (permKey) {
              isAllowed = hasPermission(permKey);
            }

            const targetTab = tabMap[item.id];

            return (
              <button
                key={item.id}
                type="button"
                disabled={!isAllowed}
                onClick={() => targetTab && onNavigate(targetTab)}
                className="touch-tile group text-left"
              >
                {/* Top Row: Icon Container + Digital Readout */}
                <div className="w-full flex items-center justify-between mb-3">
                  <div className="w-11 h-11 rounded bg-[#1c2432] border border-[#2b374a] flex items-center justify-center text-slate-200 group-hover:text-sky-300 transition-colors shrink-0">
                    <Icon className="w-6 h-6" />
                  </div>

                  {/* Readout Pill */}
                  <div className="flex items-center gap-1.5 bg-[#0a0d13] px-2.5 py-1 rounded border border-[#1e2736]">
                    <span
                      className={`led-indicator ${
                        item.statusType === 'critical'
                          ? 'led-red'
                          : item.statusType === 'warning'
                          ? 'led-amber'
                          : 'led-green'
                      }`}
                    />
                    <span className="font-mono font-bold text-[11px] text-slate-300">
                      {item.readout}
                    </span>
                  </div>
                </div>

                {/* Center: Bold Industrial Label */}
                <div className="flex-1 flex flex-col justify-center">
                  <div className="font-black text-sm text-slate-100 tracking-wide group-hover:text-sky-300 transition-colors flex items-center justify-between">
                    <span>{item.label}</span>
                    {!isAllowed && <Lock className="w-4 h-4 text-rose-400 shrink-0" />}
                  </div>
                  <div className="text-[11px] text-slate-400 mt-1 line-clamp-1 leading-snug">
                    {isAllowed ? item.subtitle : 'Access Restricted by Administrator'}
                  </div>
                </div>

                {/* Bottom Precision Accent Bar */}
                <div className="w-full h-1 bg-[#1c2432] rounded-full mt-3 overflow-hidden">
                  <div
                    className={`h-full transition-colors ${
                      isAllowed
                        ? 'bg-sky-500 group-hover:bg-sky-400'
                        : 'bg-rose-900/60'
                    }`}
                  />
                </div>
              </button>
            );
          })}
        </div>
      </div>

      {/* 3. Industrial Bottom Status Strip */}
      <div className="bg-[#0c1017] border-t border-[#1a2230] px-6 py-3 flex items-center justify-between text-xs text-slate-400">
        <div className="flex items-center gap-4">
          <span className="flex items-center gap-1.5 font-bold text-slate-300">
            <CheckCircle2 className="w-4 h-4 text-emerald-400" />
            <span>Innovance EtherCAT / Modbus TCP Gateway</span>
          </span>
          <span className="text-slate-600">|</span>
          <span className="font-mono">Scan Rate: 50 ms (20 Hz)</span>
          <span className="text-slate-600">|</span>
          <span className="font-mono text-emerald-400 font-bold">SYSTEM NORMAL</span>
        </div>

        <div className="flex items-center gap-4 font-mono text-[11px]">
          <span>Rajkot, Gujarat, India</span>
          <span className="text-slate-600">•</span>
          <span className="text-slate-300 font-bold">SCADA v1.0 PRO TOUCH</span>
        </div>
      </div>

      {/* Secret Developer Passcode Access Modal */}
      <SecretDevModal
        isOpen={isSecretModalOpen}
        onClose={() => setIsSecretModalOpen(false)}
        onSuccessNavigate={() => onNavigate('MENU_CONFIG')}
      />
    </div>
  );
};
