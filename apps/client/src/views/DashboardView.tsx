import React, { useState } from 'react';
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
  Play,
  CheckCircle2,
  Lock,
} from 'lucide-react';

interface DashboardViewProps {
  onNavigate: (tab: ActiveTab) => void;
}

export const DashboardView: React.FC<DashboardViewProps> = ({ onNavigate }) => {
  const {
    isConnected,
    feedPositionMm,
    feedSpeedMPerMin,
    hydraulicPressureBar,
    activeAlarms,
    mode,
    eStopOk,
  } = usePlcStore();

  const { currentUser, isSuperAdmin, hasPermission } = useAuthStore();
  const [isSecretModalOpen, setIsSecretModalOpen] = useState(false);

  // 12 Primary CNC SCADA Modules
  const modules = [
    {
      id: 'PRODUCTION' as ActiveTab,
      label: 'MANAGE PRODUCTION',
      subtitle: '2D CAD visualizer & auto cycle execution',
      icon: PlaySquare,
      readout: `X: ${feedPositionMm.toFixed(1)} mm`,
      statusType: 'normal' as const,
      perm: 'menu:production',
    },
    {
      id: 'MANUAL' as ActiveTab,
      label: 'MANUAL CONTROL',
      subtitle: 'Carriage jog, valve test & single stroke',
      icon: SlidersHorizontal,
      readout: `${hydraulicPressureBar.toFixed(0)} BAR`,
      statusType: 'warning' as const,
      perm: 'menu:manual',
    },
    {
      id: 'RECIPES' as ActiveTab,
      label: 'ITEM RECIPES',
      subtitle: 'DSTV/NC1 CAD importer & angle tool setup',
      icon: FileCode2,
      readout: 'TEKLA / NC1',
      statusType: 'normal' as const,
      perm: 'menu:recipes',
    },
    {
      id: 'ALIGNMENT' as ActiveTab,
      label: 'PROGRAM ALIGN & NEST',
      subtitle: 'Multibar linear nesting & IS 802 rules',
      icon: Layers,
      readout: 'SCRAP <1.2%',
      statusType: 'normal' as const,
      perm: 'menu:alignment',
    },
    {
      id: 'OEE_ANALYTICS' as ActiveTab,
      label: 'SHIFT & OEE TELEMETRY',
      subtitle: 'Processed metric tonnage & productivity',
      icon: TrendingUp,
      readout: 'TONNAGE',
      statusType: 'normal' as const,
      perm: 'menu:oee',
    },
    {
      id: 'TOOLING_WEAR' as ActiveTab,
      label: 'TOOLING LIFE & WEAR',
      subtitle: 'Stroke counters for 6 dies & shear blade',
      icon: Activity,
      readout: '6 HEADS OK',
      statusType: 'normal' as const,
      perm: 'menu:wear',
    },
    {
      id: 'IO_DIAGNOSTICS' as ActiveTab,
      label: 'PLC I/O MATRIX',
      subtitle: '64-channel bus monitor (32 In / 32 Out)',
      icon: Cpu,
      readout: '20 Hz BUS',
      statusType: 'normal' as const,
      perm: 'menu:io',
    },
    {
      id: 'ALARMS' as ActiveTab,
      label: 'ALARM CONFIG & LOGS',
      subtitle: 'Real-time safety interlocks & fault history',
      icon: AlertTriangle,
      readout: `${activeAlarms.length} ALERTS`,
      statusType: activeAlarms.length > 0 ? ('critical' as const) : ('normal' as const),
      perm: 'menu:alarms',
    },
    {
      id: 'MACHINE_SETUP' as ActiveTab,
      label: 'MACHINE SETTINGS',
      subtitle: 'HA-203 physical limits & station pitch',
      icon: Wrench,
      readout: 'HA-203',
      statusType: 'normal' as const,
      perm: 'menu:setup',
    },
    {
      id: 'TAGS' as ActiveTab,
      label: 'PLC & UI TAG MASTER',
      subtitle: 'Modbus TCP register mapping & polling',
      icon: Tag,
      readout: 'MODBUS TCP',
      statusType: 'normal' as const,
      perm: 'menu:tags',
    },
    {
      id: 'USER_MANAGEMENT' as ActiveTab,
      label: 'USER PERMISSIONS',
      subtitle: 'Role-based access matrix & operator PINs',
      icon: ShieldCheck,
      readout: 'RBAC MATRIX',
      statusType: 'normal' as const,
      perm: 'menu:users',
    },
    {
      id: (isSuperAdmin() ? 'MENU_CONFIG' : 'OEE_ANALYTICS') as ActiveTab,
      label: isSuperAdmin() ? 'OEM CONFIGURATION' : 'PRODUCTION REPORTS',
      subtitle: isSuperAdmin() ? 'OEM Button Layout & Developer Rights' : 'Daily inspection, shift & batch run reports',
      icon: isSuperAdmin() ? Settings2 : FileCode2,
      readout: isSuperAdmin() ? 'OEM DEV' : 'REPORTS',
      statusType: 'normal' as const,
      perm: isSuperAdmin() ? 'menu:config' : 'menu:oee',
    },
  ];

  return (
    <div className="flex-1 p-3 sm:p-5 md:p-6 lg:p-8 space-y-4 sm:space-y-6 w-full mx-auto max-w-[2400px]">
      {/* 1. HERO OPERATIONAL STATUS BANNER */}
      <div className="bg-gradient-to-r from-[#101726] via-[#141d2f] to-[#101726] border border-[#212f45] rounded-xl sm:rounded-2xl p-3.5 sm:p-5 shadow-xl flex flex-wrap items-center justify-between gap-3 sm:gap-4">
        <div>
          <div className="flex items-center gap-2 sm:gap-3 flex-wrap">
            <h1 className="text-sm sm:text-base md:text-lg lg:text-xl font-black text-white tracking-wide uppercase font-sans">
              HYDRO POWER TECH • MODEL HA-203
            </h1>
            <span className="text-[9px] sm:text-[10px] bg-sky-950 text-sky-300 font-extrabold px-2 py-0.5 rounded border border-sky-800 font-mono">
              6-HEAD ANGLE CNC
            </span>
          </div>
          <p className="text-[11px] sm:text-xs text-slate-400 font-medium mt-1">
            Angle Punching, Character Stamping & Shearing Touchscreen SCADA Center
          </p>
        </div>

        {/* Operating Badges */}
        <div className="flex flex-wrap items-center gap-2 sm:gap-3">
          <div className="flex items-center gap-1.5 sm:gap-2 bg-[#090d16] px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-xl border border-[#1e2a3c] text-xs font-mono">
            <span className={`w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full ${eStopOk ? 'bg-emerald-400 shadow-[0_0_8px_#34d399]' : 'bg-rose-500 animate-pulse'}`} />
            <span className="font-bold text-slate-300">{eStopOk ? 'SAFETY OK' : 'E-STOP'}</span>
          </div>

          <div className="flex items-center gap-1.5 sm:gap-2 bg-[#090d16] px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-xl border border-[#1e2a3c] text-xs font-mono">
            <span className="text-slate-400 font-bold">MODE:</span>
            <span className={`font-black ${mode === 'AUTO' ? 'text-emerald-400' : mode === 'SEMI_AUTO' ? 'text-amber-400' : 'text-sky-400'}`}>
              {mode}
            </span>
          </div>

          <div className="flex items-center gap-1.5 sm:gap-2 bg-[#090d16] px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-xl border border-[#1e2a3c] text-xs font-mono">
            <span className={`w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full ${isConnected ? 'bg-emerald-400' : 'bg-rose-500'}`} />
            <span className="text-slate-300 font-bold">{isConnected ? 'PLC 20Hz' : 'OFFLINE'}</span>
          </div>
        </div>
      </div>

      {/* 2. REAL-TIME HIGH-VISIBILITY PRECISION DRO CARDS */}
      <div className="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
        {/* DRO 1: Feed Position X */}
        <div className="cnc-dro flex flex-col justify-between p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-[#212f45] shadow-lg">
          <div className="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest truncate">CARRIAGE FEED (X)</div>
          <div className="cnc-dro-val text-xl sm:text-2xl lg:text-3xl font-black text-cyan-300 mt-1 sm:mt-2 truncate">
            {feedPositionMm.toFixed(2)} <span className="text-xs sm:text-sm font-bold text-slate-400">mm</span>
          </div>
          <div className="text-[10px] sm:text-[11px] text-slate-400 font-medium mt-1 truncate">Carriage axis encoder</div>
        </div>

        {/* DRO 2: Hydraulic Pressure */}
        <div className="cnc-dro flex flex-col justify-between p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-[#212f45] shadow-lg">
          <div className="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest truncate">HYDRAULIC PRESSURE</div>
          <div className="cnc-dro-val text-xl sm:text-2xl lg:text-3xl font-black text-amber-300 mt-1 sm:mt-2 truncate">
            {hydraulicPressureBar.toFixed(1)} <span className="text-xs sm:text-sm font-bold text-slate-400">BAR</span>
          </div>
          <div className="text-[10px] sm:text-[11px] text-slate-400 font-medium mt-1 truncate">Target: 145.0 Bar nominal</div>
        </div>

        {/* DRO 3: Feed Speed */}
        <div className="cnc-dro flex flex-col justify-between p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-[#212f45] shadow-lg">
          <div className="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest truncate">FEED AXIS SPEED</div>
          <div className="cnc-dro-val text-xl sm:text-2xl lg:text-3xl font-black text-emerald-400 mt-1 sm:mt-2 truncate">
            {feedSpeedMPerMin.toFixed(1)} <span className="text-xs sm:text-sm font-bold text-slate-400">m/min</span>
          </div>
          <div className="text-[10px] sm:text-[11px] text-slate-400 font-medium mt-1 truncate">IS620N Servo Velocity</div>
        </div>

        {/* DRO 4: Active Interlock / Alarms */}
        <div className="cnc-dro flex flex-col justify-between p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-[#212f45] shadow-lg">
          <div className="text-[9px] sm:text-[10px] font-black text-slate-400 uppercase tracking-widest truncate">MACHINE HEALTH</div>
          <div className={`cnc-dro-val text-lg sm:text-xl lg:text-2xl font-black mt-1 sm:mt-2 truncate ${activeAlarms.length > 0 ? 'text-rose-400' : 'text-emerald-400'}`}>
            {activeAlarms.length > 0 ? `${activeAlarms.length} ACTIVE FAULTS` : 'ALL SYSTEMS NORMAL'}
          </div>
          <div className="text-[10px] sm:text-[11px] text-slate-400 font-medium mt-1 truncate">Zero safety trips detected</div>
        </div>
      </div>

      {/* 3. QUICK FLOOR ACTION STRIP */}
      <div className="bg-[#0e1422] border border-[#212f45] p-2.5 sm:p-3.5 rounded-xl sm:rounded-2xl shadow-xl flex flex-wrap items-center justify-between gap-2.5 sm:gap-3">
        <div className="flex items-center gap-2 pl-1">
          <span className="text-xs font-black text-slate-400 uppercase tracking-widest">QUICK FLOOR STRIP:</span>
        </div>

        <div className="flex flex-wrap items-center gap-2 sm:gap-3 flex-1 sm:flex-initial justify-end">
          <button
            onClick={() => onNavigate('PRODUCTION')}
            className="h-10 sm:h-12 px-3.5 sm:px-5 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-black text-xs flex items-center justify-center gap-2 hover:from-emerald-500 hover:to-emerald-600 border border-emerald-400 shadow-md shadow-emerald-950/50 active:scale-95 transition-all flex-1 sm:flex-initial min-w-[140px]"
          >
            <Play className="w-3.5 h-3.5 sm:w-4 sm:h-4 fill-white shrink-0" />
            <span className="whitespace-nowrap">START PRODUCTION</span>
          </button>

          <button
            onClick={() => onNavigate('RECIPES')}
            className="h-10 sm:h-12 px-3.5 sm:px-5 rounded-xl bg-gradient-to-r from-sky-600 to-sky-700 text-white font-black text-xs flex items-center justify-center gap-2 hover:from-sky-500 hover:to-sky-600 border border-sky-400 shadow-md shadow-sky-950/50 active:scale-95 transition-all flex-1 sm:flex-initial min-w-[130px]"
          >
            <FileCode2 className="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" />
            <span className="whitespace-nowrap">RECIPE MASTER</span>
          </button>

          <button
            onClick={() => onNavigate('MANUAL')}
            className="h-10 sm:h-12 px-3.5 sm:px-5 rounded-xl bg-gradient-to-r from-[#222f44] to-[#172030] text-slate-100 font-black text-xs flex items-center justify-center gap-2 hover:bg-[#2c3d59] border border-[#374b68] shadow-md active:scale-95 transition-all flex-1 sm:flex-initial min-w-[140px]"
          >
            <SlidersHorizontal className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-sky-400 shrink-0" />
            <span className="whitespace-nowrap">MANUAL JOG & VALVES</span>
          </button>

          <button
            onClick={() => onNavigate('ALARMS')}
            className={`h-10 sm:h-12 px-3.5 sm:px-5 rounded-xl font-black text-xs flex items-center justify-center gap-2 border shadow-md active:scale-95 transition-all flex-1 sm:flex-initial min-w-[120px] ${
              activeAlarms.length > 0
                ? 'bg-rose-900 border-rose-500 text-rose-100 animate-pulse'
                : 'bg-[#151d2a] border-[#293649] text-slate-300 hover:bg-[#1e293b]'
            }`}
          >
            <AlertTriangle className={`w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0 ${activeAlarms.length > 0 ? 'text-white' : 'text-amber-400'}`} />
            <span className="whitespace-nowrap">{activeAlarms.length > 0 ? `${activeAlarms.length} ALARMS` : 'ALARM LOGS'}</span>
          </button>
        </div>
      </div>

      {/* 4. MAIN 12-MODULE BALANCED TOUCH GRID */}
      <div className="space-y-3">
        <div className="flex flex-wrap items-center justify-between gap-2 px-1">
          <div className="text-xs font-black tracking-wider text-slate-400 uppercase flex items-center gap-2">
            <span className="w-2.5 h-2.5 rounded-full bg-sky-500 shadow-[0_0_8px_#38bdf8]" />
            <span>PRIMARY TOUCH MODULE LAUNCHER</span>
          </div>
          <div className="text-xs text-slate-400 font-mono">
            OPERATOR: <span className="font-bold text-white">{currentUser?.name || 'Line Operator'}</span> [{currentUser?.role || 'OPERATOR'}]
          </div>
        </div>

        {/* Complete 12 Cards Grid: Fully Responsive */}
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-4 gap-3 sm:gap-4">
          {modules.map((item) => {
            const Icon = item.icon;
            const isAllowed = isSuperAdmin() || currentUser?.role === 'ADMIN' || hasPermission(item.perm);

            return (
              <button
                key={item.id}
                type="button"
                disabled={!isAllowed}
                onClick={() => onNavigate(item.id)}
                className={`group relative text-left p-3.5 sm:p-5 rounded-xl sm:rounded-2xl border transition-all select-none touch-action-manipulation active:scale-[0.98] min-h-[140px] sm:min-h-[155px] flex flex-col justify-between shadow-lg ${
                  isAllowed
                    ? 'bg-gradient-to-b from-[#131b28] to-[#0c121c] border-[#212f45] hover:border-sky-400 hover:shadow-sky-500/10 hover:from-[#172233] hover:to-[#0f1624]'
                    : 'bg-[#0f141e]/50 border-[#1a2230] opacity-50 cursor-not-allowed'
                }`}
              >
                {/* Top Row: Icon Container + Readout Pill */}
                <div className="w-full flex items-center justify-between">
                  <div
                    className={`w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center transition-all ${
                      isAllowed
                        ? 'bg-[#1b2537] border border-[#2b3d58] text-slate-100 group-hover:bg-sky-500 group-hover:text-white group-hover:shadow-[0_0_12px_rgba(56,189,248,0.5)]'
                        : 'bg-[#141b26] border border-[#202a3a] text-slate-500'
                    }`}
                  >
                    <Icon className="w-5 h-5 sm:w-6 sm:h-6" />
                  </div>

                  {/* Readout Pill */}
                  <div className="flex items-center gap-1.5 bg-[#080d16] px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg border border-[#1e293b] shadow-inner">
                    <span
                      className={`w-2 h-2 rounded-full ${
                        item.statusType === 'critical'
                          ? 'bg-rose-500 shadow-[0_0_8px_#f43f5e]'
                          : item.statusType === 'warning'
                          ? 'bg-amber-400 shadow-[0_0_8px_#fbbf24]'
                          : 'bg-emerald-400 shadow-[0_0_8px_#34d399]'
                      }`}
                    />
                    <span className="font-mono font-bold text-[11px] sm:text-xs text-slate-300">
                      {item.readout}
                    </span>
                  </div>
                </div>

                {/* Center: Bold Title & Subtitle */}
                <div className="my-2">
                  <div className="font-black text-xs sm:text-sm text-slate-100 tracking-wide group-hover:text-sky-300 transition-colors flex items-center justify-between">
                    <span className="truncate">{item.label}</span>
                    {!isAllowed && <Lock className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-rose-400 shrink-0" />}
                  </div>
                  <div className="text-[11px] sm:text-xs text-slate-400 mt-1 line-clamp-1 leading-snug font-medium">
                    {isAllowed ? item.subtitle : 'Access Restricted by Administrator'}
                  </div>
                </div>

                {/* Bottom Accent Bar */}
                <div className="w-full h-1 bg-[#17202f] rounded-full overflow-hidden">
                  <div
                    className={`h-full transition-all duration-300 ${
                      isAllowed
                        ? 'w-1/3 bg-sky-500 group-hover:w-full group-hover:bg-gradient-to-r group-hover:from-sky-500 group-hover:to-cyan-400'
                        : 'w-full bg-rose-900/60'
                    }`}
                  />
                </div>
              </button>
            );
          })}
        </div>
      </div>

      {/* 5. INDUSTRIAL STATUS FOOTER */}
      <div className="bg-[#090d14] border border-[#1a2538] rounded-xl px-3 sm:px-5 py-2.5 sm:py-3 flex flex-wrap items-center justify-between text-xs text-slate-400 gap-2.5 sm:gap-3">
        <div className="flex flex-wrap items-center gap-2 sm:gap-4">
          <span className="flex items-center gap-2 font-bold text-slate-200">
            <CheckCircle2 className="w-4 h-4 text-emerald-400 shrink-0" />
            <span className="truncate">Innovance EtherCAT / Modbus Gateway</span>
          </span>
          <span className="text-slate-600 hidden sm:inline">|</span>
          <span className="font-mono text-slate-300 hidden md:inline">Scan: 50 ms (20 Hz)</span>
          <span className="text-slate-600 hidden md:inline">|</span>
          <span className="font-mono text-emerald-400 font-bold flex items-center gap-1.5">
            <span className="w-2 h-2 rounded-full bg-emerald-400" />
            SYSTEM NORMAL
          </span>
        </div>

        <div className="flex items-center gap-2 sm:gap-4 font-mono text-[10px] sm:text-[11px]">
          <span className="truncate">Hydro Power Tech Engineering</span>
          <span className="text-slate-600">•</span>
          <span className="text-sky-400 font-bold">SCADA v2.0 PRO</span>
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
