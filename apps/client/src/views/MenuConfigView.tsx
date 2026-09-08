import React, { useState, useEffect } from 'react';
import { useAuthStore } from '../stores/useAuthStore.js';
import { useMenuStore } from '../stores/useMenuStore.js';
import { MenuConfigItem, UserRole } from '@innovance-hmi/shared';
import {
  Settings2,
  Save,
  RotateCcw,
  ArrowUp,
  ArrowDown,
  Eye,
  EyeOff,
  ShieldAlert,
  CheckCircle2,
  LayoutGrid,
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
} from 'lucide-react';

import { SecretDevModal } from '../components/common/SecretDevModal.js';

export const MenuConfigView: React.FC = () => {
  const { isSuperAdmin, currentUser } = useAuthStore();
  const { menuItems, fetchMenuItems, updateMenuItems, resetToDefaults } = useMenuStore();

  const [localItems, setLocalItems] = useState<MenuConfigItem[]>([]);
  const [selectedItemKey, setSelectedItemKey] = useState<string | null>(null);
  const [saveStatus, setSaveStatus] = useState<string | null>(null);
  const [isDevModalOpen, setIsDevModalOpen] = useState(false);

  useEffect(() => {
    fetchMenuItems();
  }, [fetchMenuItems]);

  useEffect(() => {
    if (menuItems.length > 0) {
      const sorted = [...menuItems].sort((a, b) => a.order - b.order);
      setLocalItems(sorted);
      if (!selectedItemKey && sorted.length > 0) {
        setSelectedItemKey(sorted[0].menuKey);
      }
    }
  }, [menuItems, selectedItemKey]);

  if (!isSuperAdmin()) {
    return (
      <div className="flex-1 flex flex-col items-center justify-center p-8 bg-[#0b0f17] text-center select-none">
        <div className="w-16 h-16 rounded-2xl bg-rose-950/60 border border-rose-800 text-rose-400 flex items-center justify-center mb-4 shadow-xl">
          <ShieldAlert className="w-8 h-8" />
        </div>
        <h2 className="text-xl font-black text-white mb-2">ACCESS RESTRICTED: OEM DEVELOPERS ONLY</h2>
        <p className="text-xs text-slate-400 max-w-md mb-4 leading-relaxed">
          The Master Menu Configuration tool is exclusively reserved for the OEM Engineering Developer
          Team ("Super Admin - With US only"). Plant Administrators and Operators do not have authorization to reconfigure system layouts.
        </p>
        <span className="text-[11px] font-mono text-slate-500 bg-slate-900 px-3 py-1 rounded border border-slate-800 mb-4">
          Current Role: {currentUser?.role || 'OPERATOR'}
        </span>

        <button
          type="button"
          onClick={() => setIsDevModalOpen(true)}
          className="px-4 py-2 bg-rose-950/70 border border-rose-800 text-rose-300 hover:bg-rose-900/80 rounded-lg text-xs font-bold transition-all flex items-center gap-2 shadow"
        >
          <ShieldAlert className="w-4 h-4" />
          <span>Enter Developer Secret Code</span>
        </button>

        <SecretDevModal
          isOpen={isDevModalOpen}
          onClose={() => setIsDevModalOpen(false)}
        />
      </div>
    );
  }

  const handleMove = (index: number, direction: 'UP' | 'DOWN') => {
    const targetIdx = direction === 'UP' ? index - 1 : index + 1;
    if (targetIdx < 0 || targetIdx >= localItems.length) return;

    const updated = [...localItems];
    const temp = updated[index];
    updated[index] = updated[targetIdx];
    updated[targetIdx] = temp;

    // Recalculate order indices
    const reordered = updated.map((item, idx) => ({ ...item, order: idx }));
    setLocalItems(reordered);
  };

  const handleToggleEnabled = (key: string) => {
    setLocalItems((prev) =>
      prev.map((item) => (item.menuKey === key ? { ...item, isEnabled: !item.isEnabled } : item))
    );
  };

  const handleToggleRole = (key: string, role: UserRole) => {
    setLocalItems((prev) =>
      prev.map((item) => {
        if (item.menuKey !== key) return item;
        const hasRole = item.roles.includes(role);
        const roles = hasRole ? item.roles.filter((r) => r !== role) : [...item.roles, role];
        return { ...item, roles };
      })
    );
  };

  const handleUpdateField = (key: string, field: keyof MenuConfigItem, value: any) => {
    setLocalItems((prev) =>
      prev.map((item) => (item.menuKey === key ? { ...item, [field]: value } : item))
    );
  };

  const handleSave = async () => {
    setSaveStatus(null);
    const res = await updateMenuItems(localItems);
    if (res.success) {
      setSaveStatus('Menu configuration saved successfully!');
      setTimeout(() => setSaveStatus(null), 3000);
    } else {
      alert(res.error || 'Failed to save menu configuration');
    }
  };

  const handleReset = async () => {
    if (!window.confirm('Reset all menu buttons to factory default configuration?')) return;
    const res = await resetToDefaults();
    if (res.success) {
      setSaveStatus('Reset to factory defaults completed');
      setTimeout(() => setSaveStatus(null), 3000);
    }
  };

  const activeItem = localItems.find((i) => i.menuKey === selectedItemKey) || localItems[0];

  const iconComponents: Record<string, any> = {
    LayoutGrid,
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
  };

  const ActiveIcon = activeItem ? iconComponents[activeItem.icon] || LayoutGrid : LayoutGrid;

  return (
    <div className="flex-1 flex flex-col overflow-hidden bg-[#0b0f17] text-slate-200 select-none">
      {/* Top Banner */}
      <div className="bg-[#121824] border-b border-[#1f293d] px-6 py-4 flex items-center justify-between shadow-lg">
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-600 to-purple-800 text-white flex items-center justify-center shadow-lg shadow-rose-900/40">
            <Settings2 className="w-5 h-5" />
          </div>
          <div>
            <div className="flex items-center gap-2">
              <h1 className="text-base font-black text-white">SUPER ADMIN MENU CONFIGURATION</h1>
              <span className="text-[10px] bg-rose-950 text-rose-300 border border-rose-800 font-extrabold px-2 py-0.5 rounded font-mono">
                WITH US ONLY (OEM)
              </span>
            </div>
            <p className="text-xs text-slate-400">
              Customize tactile SCADA square buttons, ordering, and role visibility
            </p>
          </div>
        </div>

        <div className="flex items-center gap-3">
          <button
            type="button"
            onClick={handleReset}
            className="px-3.5 py-2 rounded-lg bg-[#1e293b] hover:bg-[#283548] text-xs font-bold text-slate-300 border border-slate-700 flex items-center gap-1.5 transition-all"
          >
            <RotateCcw className="w-4 h-4 text-amber-400" />
            <span>FACTORY RESET</span>
          </button>

          <button
            type="button"
            onClick={handleSave}
            className="px-5 py-2 rounded-lg bg-gradient-to-r from-sky-600 to-blue-700 hover:from-sky-500 hover:to-blue-600 text-xs font-extrabold text-white shadow-lg shadow-sky-600/30 flex items-center gap-2 transition-all active:translate-y-0.5"
          >
            <Save className="w-4 h-4" />
            <span>SAVE CONFIGURATION</span>
          </button>
        </div>
      </div>

      {saveStatus && (
        <div className="bg-emerald-950/80 border-b border-emerald-700/80 px-6 py-2 text-xs text-emerald-300 flex items-center gap-2">
          <CheckCircle2 className="w-4 h-4" />
          <span>{saveStatus}</span>
        </div>
      )}

      {/* Main Split Body */}
      <div className="flex-1 flex overflow-hidden">
        {/* LEFT: Menu Items List & Ordering */}
        <div className="w-96 bg-[#101622] border-r border-[#1e293b] flex flex-col">
          <div className="p-3 border-b border-[#1e293b] bg-[#141c2b] text-[11px] font-black text-slate-400 uppercase tracking-wider flex items-center justify-between">
            <span>SCADA BUTTON SEQUENCE ({localItems.length})</span>
            <span>REORDER</span>
          </div>

          <div className="flex-1 overflow-y-auto p-3 space-y-2">
            {localItems.map((item, index) => {
              const isSelected = item.menuKey === selectedItemKey;
              const IconComp = iconComponents[item.icon] || LayoutGrid;

              return (
                <div
                  key={item.menuKey}
                  onClick={() => setSelectedItemKey(item.menuKey)}
                  className={`p-3 rounded-lg border transition-all cursor-pointer flex items-center justify-between gap-2 ${
                    isSelected
                      ? 'bg-sky-950/60 border-sky-400 shadow-md ring-1 ring-sky-400 text-white'
                      : 'bg-[#151d2b] border-[#1e293b] hover:bg-[#1b2536] text-slate-300'
                  }`}
                >
                  <div className="flex items-center gap-2.5 min-w-0">
                    <span className="font-mono text-[10px] text-slate-500 font-bold w-4">
                      {index + 1}.
                    </span>
                    <div className="w-7 h-7 rounded bg-[#1e293b] flex items-center justify-center text-sky-400 shrink-0">
                      <IconComp className="w-4 h-4" />
                    </div>
                    <div className="min-w-0">
                      <div className="text-xs font-bold truncate">{item.label}</div>
                      <div className="text-[10px] text-slate-400 truncate">
                        {item.isEnabled ? (
                          <span className="text-emerald-400">Active</span>
                        ) : (
                          <span className="text-slate-500">Disabled</span>
                        )}{' '}
                        • {item.roles.length} roles
                      </div>
                    </div>
                  </div>

                  {/* Move Up/Down Controls */}
                  <div className="flex items-center gap-1 shrink-0" onClick={(e) => e.stopPropagation()}>
                    <button
                      type="button"
                      disabled={index === 0}
                      onClick={() => handleMove(index, 'UP')}
                      className="p-1 rounded bg-[#1e293b] hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed text-slate-300"
                      title="Move Up"
                    >
                      <ArrowUp className="w-3.5 h-3.5" />
                    </button>
                    <button
                      type="button"
                      disabled={index === localItems.length - 1}
                      onClick={() => handleMove(index, 'DOWN')}
                      className="p-1 rounded bg-[#1e293b] hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed text-slate-300"
                      title="Move Down"
                    >
                      <ArrowDown className="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* RIGHT: Module Configuration Editor & Live Tile Preview */}
        <div className="flex-1 overflow-y-auto p-6 bg-[#0b0f17]">
          {activeItem ? (
            <div className="max-w-3xl mx-auto space-y-6">
              {/* Tile Preview Section */}
              <div className="bg-[#121824] border-2 border-[#1f293d] rounded-xl p-5 shadow-xl">
                <div className="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                  <LayoutGrid className="w-4 h-4 text-sky-400" />
                  <span>DASHBOARD SQUARE BUTTON PREVIEW</span>
                </div>

                <div className="flex flex-col sm:flex-row items-center gap-6">
                  {/* The Square Button Replica */}
                  <div className="touch-tile w-48 h-48 pointer-events-none shrink-0">
                    <div className="w-full flex items-center justify-between mb-3">
                      <div className="w-11 h-11 rounded bg-[#1c2432] border border-[#2b374a] flex items-center justify-center text-slate-200 shrink-0">
                        <ActiveIcon className="w-6 h-6 text-sky-400" />
                      </div>
                      <div className="flex items-center gap-1.5 bg-[#0a0d13] px-2.5 py-1 rounded border border-[#1e2736]">
                        <span className="led-indicator led-green" />
                        <span className="font-mono font-bold text-[11px] text-slate-300">
                          {activeItem.badgeText || 'READY'}
                        </span>
                      </div>
                    </div>

                    <div className="flex-1 flex flex-col justify-center">
                      <div className="font-black text-sm text-slate-100 tracking-wide leading-tight">
                        {activeItem.label}
                      </div>
                      <div className="text-[11px] text-slate-400 mt-1 line-clamp-1 leading-snug">
                        {activeItem.description || 'Module subtitle'}
                      </div>
                    </div>

                    <div className="w-full h-1 bg-[#1c2432] rounded-full mt-3 overflow-hidden">
                      <div className="h-full bg-sky-500 opacity-90" />
                    </div>
                  </div>

                  {/* Quick toggle banner */}
                  <div className="flex-1 space-y-3">
                    <div className="text-sm font-bold text-white flex items-center gap-2">
                      <span>Module Status:</span>
                      <button
                        type="button"
                        onClick={() => handleToggleEnabled(activeItem.menuKey)}
                        className={`px-3 py-1 rounded text-xs font-black tracking-wider flex items-center gap-1.5 transition-all ${
                          activeItem.isEnabled
                            ? 'bg-emerald-950 text-emerald-300 border border-emerald-700'
                            : 'bg-rose-950 text-rose-300 border border-rose-700'
                        }`}
                      >
                        {activeItem.isEnabled ? <Eye className="w-3.5 h-3.5" /> : <EyeOff className="w-3.5 h-3.5" />}
                        <span>{activeItem.isEnabled ? 'ENABLED IN SYSTEM' : 'DISABLED / HIDDEN'}</span>
                      </button>
                    </div>

                    <p className="text-xs text-slate-400 leading-relaxed">
                      Configure button text, description, icon identifier, and which roles can view this
                      module on the Dashboard launcher.
                    </p>
                  </div>
                </div>
              </div>

              {/* Form Settings Card */}
              <div className="bg-[#121824] border-2 border-[#1f293d] rounded-xl p-5 shadow-xl space-y-5">
                <div className="text-xs font-extrabold text-slate-400 uppercase tracking-wider border-b border-[#1f293d] pb-3">
                  BUTTON PROPERTIES
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  {/* Button Title */}
                  <div>
                    <label className="block text-xs font-bold text-slate-300 mb-1">
                      Button Label (Title)
                    </label>
                    <input
                      type="text"
                      value={activeItem.label}
                      onChange={(e) => handleUpdateField(activeItem.menuKey, 'label', e.target.value)}
                      className="w-full bg-[#162032] border border-[#223147] rounded px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500 font-bold"
                    />
                  </div>

                  {/* Badge Text */}
                  <div>
                    <label className="block text-xs font-bold text-slate-300 mb-1">
                      Status Badge Pill Text
                    </label>
                    <input
                      type="text"
                      value={activeItem.badgeText || ''}
                      onChange={(e) => handleUpdateField(activeItem.menuKey, 'badgeText', e.target.value)}
                      placeholder="e.g. 2D CAD"
                      className="w-full bg-[#162032] border border-[#223147] rounded px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500 font-mono"
                    />
                  </div>
                </div>

                {/* Subtitle / Description */}
                <div>
                  <label className="block text-xs font-bold text-slate-300 mb-1">
                    Subtitle Description
                  </label>
                  <input
                    type="text"
                    value={activeItem.description || ''}
                    onChange={(e) => handleUpdateField(activeItem.menuKey, 'description', e.target.value)}
                    placeholder="Short description displayed on square button"
                    className="w-full bg-[#162032] border border-[#223147] rounded px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500"
                  />
                </div>

                {/* Role Authorization Checkboxes */}
                <div className="pt-2 border-t border-[#1f293d]">
                  <label className="block text-xs font-bold text-slate-300 mb-2">
                    Role Visibility & Access Authorization
                  </label>
                  <div className="grid grid-cols-3 gap-3">
                    {(
                      [
                        { role: 'SUPER_ADMIN' as const, label: 'Super Admin (With US only)', color: 'border-rose-800 bg-rose-950/40 text-rose-300' },
                        { role: 'ADMIN' as const, label: 'Plant Administrator', color: 'border-amber-800 bg-amber-950/40 text-amber-300' },
                        { role: 'OPERATOR' as const, label: 'Shopfloor Operator', color: 'border-emerald-800 bg-emerald-950/40 text-emerald-300' },
                      ] as const
                    ).map((r) => {
                      const isAllowed = activeItem.roles.includes(r.role);

                      return (
                        <button
                          key={r.role}
                          type="button"
                          onClick={() => handleToggleRole(activeItem.menuKey, r.role)}
                          className={`p-3 rounded-lg border text-left flex items-center justify-between transition-all ${
                            isAllowed
                              ? 'border-sky-400 bg-[#16263d] text-white shadow-sm ring-1 ring-sky-400'
                              : 'border-[#223147] bg-[#101723] text-slate-500'
                          }`}
                        >
                          <div>
                            <div className="text-xs font-bold">{r.label}</div>
                            <div className="text-[10px] text-slate-400">
                              {isAllowed ? 'Visible & Allowed' : 'Hidden for Role'}
                            </div>
                          </div>
                          <input
                            type="checkbox"
                            checked={isAllowed}
                            onChange={() => {}}
                            className="rounded border-slate-600 text-sky-600 focus:ring-0 cursor-pointer"
                          />
                        </button>
                      );
                    })}
                  </div>
                </div>
              </div>
            </div>
          ) : (
            <div className="flex items-center justify-center h-full text-slate-500 text-sm">
              Select a module from the left list to configure.
            </div>
          )}
        </div>
      </div>
    </div>
  );
};
