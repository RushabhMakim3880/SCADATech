import React, { useState, useEffect } from 'react';
import { useAuthStore } from '../stores/useAuthStore.js';
import { User, SYSTEM_PERMISSIONS, PermissionDefinition } from '@innovance-hmi/shared';
import {
  ShieldCheck,
  UserPlus,
  KeyRound,
  Save,
  Trash2,
  User as UserIcon,
  Search,
  CheckSquare,
  Square,
  AlertCircle,
  Sliders,
  PlaySquare,
  FileCode2,
  Wrench,
} from 'lucide-react';

export const UserManagementView: React.FC = () => {
  const { users, fetchUsers } = useAuthStore();
  const [selectedUser, setSelectedUser] = useState<User | null>(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [activePermissions, setActivePermissions] = useState<string[]>([]);
  const [pinCode, setPinCode] = useState('');
  const [name, setName] = useState('');
  const [isSaving, setIsSaving] = useState(false);
  const [statusMessage, setStatusMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);

  // New User Modal State
  const [isNewUserModalOpen, setIsNewUserModalOpen] = useState(false);
  const [newUsername, setNewUsername] = useState('');
  const [newName, setNewName] = useState('');
  const [newRole, setNewRole] = useState<'ADMIN' | 'OPERATOR'>('OPERATOR');
  const [newPin, setNewPin] = useState('1234');

  useEffect(() => {
    fetchUsers();
  }, [fetchUsers]);

  useEffect(() => {
    if (users.length > 0 && (!selectedUser || selectedUser.role === 'SUPER_ADMIN')) {
      const initial = users.find((u) => u.role === 'OPERATOR') || users.find((u) => u.role !== 'SUPER_ADMIN');
      if (initial) handleSelectUser(initial);
    }
  }, [users, selectedUser]);

  const handleSelectUser = (user: User) => {
    setSelectedUser(user);
    setActivePermissions(Array.isArray(user.permissions) ? [...user.permissions] : []);
    setPinCode(user.pinCode || '');
    setName(user.name);
    setStatusMessage(null);
  };

  const togglePermission = (key: string) => {
    setActivePermissions((prev) =>
      prev.includes(key) ? prev.filter((p) => p !== key) : [...prev, key]
    );
  };

  const applyOperatorPreset = () => {
    const operatorDefaults = [
      'menu:dashboard',
      'menu:production',
      'menu:manual',
      'menu:recipes',
      'menu:alignment',
      'menu:oee',
      'menu:wear',
      'menu:alarms',
      'action:start_production',
      'action:jog_axis',
      'action:fire_heads',
      'action:toggle_valves',
    ];
    setActivePermissions(operatorDefaults);
    setStatusMessage({ type: 'success', text: 'Operator default preset applied (Unsaved)' });
  };

  const applyAdminPreset = () => {
    const adminDefaults = [
      'menu:dashboard',
      'menu:production',
      'menu:manual',
      'menu:recipes',
      'menu:alignment',
      'menu:oee',
      'menu:wear',
      'menu:io',
      'menu:setup',
      'menu:tags',
      'menu:alarms',
      'menu:users',
      'action:start_production',
      'action:jog_axis',
      'action:fire_heads',
      'action:toggle_valves',
      'action:edit_recipe',
      'action:import_dstv',
      'action:reset_tool_wear',
      'action:manage_users',
    ];
    setActivePermissions(adminDefaults);
    setStatusMessage({ type: 'success', text: 'Plant Administrator preset applied (Unsaved)' });
  };

  const selectAll = () => {
    setActivePermissions(SYSTEM_PERMISSIONS.map((p) => p.key));
  };

  const clearAll = () => {
    setActivePermissions(['menu:dashboard']);
  };

  const handleSaveUser = async () => {
    if (!selectedUser) return;
    setIsSaving(true);
    setStatusMessage(null);

    try {
      // 1. Save Basic Details
      const updateRes = await fetch(`/api/users/${selectedUser.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, pinCode }),
      });
      const updateData = await updateRes.json();

      // 2. Save Permissions
      const permRes = await fetch(`/api/users/${selectedUser.id}/permissions`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ permissions: activePermissions }),
      });
      const permData = await permRes.json();

      if (updateData.success && permData.success) {
        setStatusMessage({ type: 'success', text: `Permissions and details saved for ${name}` });
        fetchUsers();
      } else {
        setStatusMessage({ type: 'error', text: updateData.error || permData.error || 'Failed to save' });
      }
    } catch (e: any) {
      setStatusMessage({ type: 'error', text: e.message || 'Network error' });
    } finally {
      setIsSaving(false);
    }
  };

  const handleCreateNewUser = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!newUsername || !newName) return;

    try {
      const defaultPerms =
        newRole === 'ADMIN'
          ? ['menu:dashboard', 'menu:production', 'menu:manual', 'menu:recipes', 'menu:users']
          : ['menu:dashboard', 'menu:production', 'menu:manual', 'action:jog_axis'];

      const res = await fetch('/api/users', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          username: newUsername.toLowerCase().trim(),
          name: newName.trim(),
          role: newRole,
          pinCode: newPin,
          permissions: defaultPerms,
        }),
      });
      const data = await res.json();
      if (data.success && data.data) {
        setIsNewUserModalOpen(false);
        setNewUsername('');
        setNewName('');
        setNewPin('1234');
        await fetchUsers();
        handleSelectUser(data.data);
      } else {
        alert(data.error || 'Failed to create user');
      }
    } catch (e: any) {
      alert(e.message || 'Network error');
    }
  };

  const handleDeleteUser = async (user: User) => {
    if (user.role === 'SUPER_ADMIN') {
      alert('Super Admin cannot be deleted');
      return;
    }
    if (!window.confirm(`Are you sure you want to delete user '${user.name}'?`)) return;

    try {
      const res = await fetch(`/api/users/${user.id}`, { method: 'DELETE' });
      const data = await res.json();
      if (data.success) {
        fetchUsers();
        setSelectedUser(null);
      } else {
        alert(data.error || 'Failed to delete user');
      }
    } catch (e: any) {
      alert(e.message || 'Network error');
    }
  };

  const filteredUsers = users.filter(
    (u) =>
      u.role !== 'SUPER_ADMIN' &&
      (u.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        u.username.toLowerCase().includes(searchTerm.toLowerCase()) ||
        u.role.toLowerCase().includes(searchTerm.toLowerCase()))
  );

  const categories: Array<{ id: PermissionDefinition['category']; label: string; icon: any }> = [
    { id: 'NAVIGATION', label: '1. MENU & MODULE VISIBILITY', icon: PlaySquare },
    { id: 'MACHINE_CONTROL', label: '2. AUTOMATION & SERVO CONTROLS', icon: Sliders },
    { id: 'RECIPES', label: '3. RECIPES & CAD IMPORT', icon: FileCode2 },
    { id: 'MAINTENANCE', label: '4. TOOLING & HARDWARE DIAGNOSTICS', icon: Wrench },
    { id: 'SYSTEM', label: '5. SYSTEM ADMINISTRATION', icon: ShieldCheck },
  ];

  return (
    <div className="flex-1 flex overflow-hidden bg-[#0b0f17] text-slate-200 select-none">
      {/* LEFT: User Directory List */}
      <div className="w-80 bg-[#111723] border-r border-[#1e293b] flex flex-col">
        {/* Header & Add User */}
        <div className="p-4 border-b border-[#1e293b] bg-[#162030] flex items-center justify-between">
          <div className="flex items-center gap-2">
            <ShieldCheck className="w-5 h-5 text-emerald-400" />
            <div>
              <div className="text-sm font-black text-white">USER DIRECTORY</div>
              <div className="text-[10px] text-slate-400">Manage Operators & Roles</div>
            </div>
          </div>
          <button
            onClick={() => setIsNewUserModalOpen(true)}
            className="p-1.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white shadow-sm flex items-center gap-1 text-xs font-bold transition-all"
            title="Add New User"
          >
            <UserPlus className="w-4 h-4" />
            <span>NEW</span>
          </button>
        </div>

        {/* Search */}
        <div className="p-3 border-b border-[#1e293b] bg-[#0e141f]">
          <div className="relative flex items-center">
            <Search className="w-3.5 h-3.5 absolute left-3 text-slate-400" />
            <input
              type="text"
              placeholder="Search operators..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full bg-[#162032] border border-[#223147] rounded-md pl-8 pr-3 py-1.5 text-xs text-slate-200 placeholder-slate-400 focus:outline-none focus:border-sky-500"
            />
          </div>
        </div>

        {/* User Card List */}
        <div className="flex-1 overflow-y-auto p-3 space-y-2">
          {filteredUsers.map((u) => {
            const isSelected = selectedUser?.id === u.id;
            const isSuper = u.role === 'SUPER_ADMIN';
            const isAdmin = u.role === 'ADMIN';

            return (
              <div
                key={u.id}
                onClick={() => handleSelectUser(u)}
                className={`p-3 rounded-lg border cursor-pointer transition-all ${
                  isSelected
                    ? 'bg-gradient-to-r from-sky-950/80 to-[#17253b] border-sky-400 shadow-md ring-1 ring-sky-400 text-white'
                    : 'bg-[#151d2b] border-[#1e293b] hover:bg-[#1b2638] text-slate-300'
                }`}
              >
                <div className="flex items-center justify-between mb-1">
                  <div className="font-extrabold text-xs truncate flex items-center gap-1.5">
                    <UserIcon className="w-3.5 h-3.5 opacity-70" />
                    {u.name}
                  </div>
                  <span
                    className={`text-[9px] font-black px-1.5 py-0.5 rounded uppercase font-mono ${
                      isSuper
                        ? 'bg-rose-950 text-rose-300 border border-rose-800'
                        : isAdmin
                        ? 'bg-amber-950 text-amber-300 border border-amber-800'
                        : 'bg-emerald-950 text-emerald-300 border border-emerald-800'
                    }`}
                  >
                    {isSuper ? 'SUPER ADMIN (US)' : isAdmin ? 'ADMIN' : 'OPERATOR'}
                  </span>
                </div>
                <div className="flex items-center justify-between text-[11px] text-slate-400">
                  <span>@{u.username}</span>
                  <span className="font-mono text-[10px]">PIN: ••••</span>
                </div>
              </div>
            );
          })}
        </div>
      </div>

      {/* RIGHT: User Profile & Granular Permission Matrix */}
      <div className="flex-1 flex flex-col overflow-y-auto bg-[#0b0f17]">
        {selectedUser ? (
          <div className="p-6 max-w-5xl mx-auto w-full space-y-6">
            {/* User Profile Card */}
            <div className="bg-[#121824] border-2 border-[#1f293d] rounded-xl p-5 shadow-xl">
              <div className="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div className="flex items-center gap-4">
                  <div
                    className={`w-14 h-14 rounded-xl flex items-center justify-center font-black text-xl text-white shadow-lg ${
                      selectedUser.role === 'SUPER_ADMIN'
                        ? 'bg-gradient-to-br from-rose-600 to-purple-800 shadow-rose-900/30'
                        : selectedUser.role === 'ADMIN'
                        ? 'bg-gradient-to-br from-amber-600 to-orange-800 shadow-amber-900/30'
                        : 'bg-gradient-to-br from-emerald-600 to-teal-800 shadow-emerald-900/30'
                    }`}
                  >
                    {selectedUser.name.slice(0, 2).toUpperCase()}
                  </div>
                  <div>
                    <div className="flex items-center gap-2">
                      <h2 className="text-lg font-black text-white">{selectedUser.name}</h2>
                      <span className="text-[10px] bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono border border-slate-700">
                        @{selectedUser.username}
                      </span>
                    </div>
                    <div className="text-xs text-slate-400 mt-0.5">
                      Role:{' '}
                      <span className="font-bold text-sky-300">
                        {selectedUser.role === 'SUPER_ADMIN'
                          ? 'Super Admin (With US only)'
                          : selectedUser.role}
                      </span>{' '}
                      • Configured Permissions:{' '}
                      <span className="font-mono text-emerald-400 font-bold">
                        {selectedUser.role === 'SUPER_ADMIN' ? 'FULL UNRESTRICTED' : activePermissions.length}
                      </span>
                    </div>
                  </div>
                </div>

                {/* Edit Credentials & Delete */}
                <div className="flex items-center gap-3 w-full md:w-auto">
                  <div className="flex items-center gap-2 bg-[#080c14] px-3 py-1.5 rounded-lg border border-[#1e293b]">
                    <KeyRound className="w-4 h-4 text-amber-400" />
                    <span className="text-xs text-slate-400 font-bold">PIN:</span>
                    <input
                      type="text"
                      maxLength={6}
                      value={pinCode}
                      onChange={(e) => setPinCode(e.target.value)}
                      placeholder="PIN"
                      className="w-16 bg-transparent text-amber-300 font-mono font-bold text-sm focus:outline-none"
                    />
                  </div>

                  {selectedUser.role !== 'SUPER_ADMIN' && (
                    <button
                      onClick={() => handleDeleteUser(selectedUser)}
                      className="p-2 rounded-lg bg-rose-950/40 hover:bg-rose-900/60 text-rose-300 border border-rose-900/80 transition-colors"
                      title="Delete User"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  )}
                </div>
              </div>

              {/* Status feedback */}
              {statusMessage && (
                <div
                  className={`mt-4 p-2.5 rounded text-xs flex items-center gap-2 ${
                    statusMessage.type === 'success'
                      ? 'bg-emerald-950/60 border border-emerald-700/80 text-emerald-300'
                      : 'bg-rose-950/60 border border-rose-700/80 text-rose-300'
                  }`}
                >
                  <AlertCircle className="w-4 h-4 shrink-0" />
                  <span>{statusMessage.text}</span>
                </div>
              )}
            </div>

            {/* Granular Permission Checklist Header & Presets */}
            <div className="bg-[#121824] border-2 border-[#1f293d] rounded-xl p-5 shadow-xl">
              <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-4 border-b border-[#1f293d]">
                <div>
                  <div className="text-sm font-black text-white flex items-center gap-2">
                    <ShieldCheck className="w-4 h-4 text-sky-400" />
                    GRANULAR PERMISSION MATRIX (REQUIREMENT 3)
                  </div>
                  <div className="text-xs text-slate-400">
                    Administrator authorization control for machine features and navigation
                  </div>
                </div>

                {/* Preset Actions with Glove-Friendly Touch Targets */}
                <div className="flex flex-wrap items-center gap-2">
                  <button
                    type="button"
                    onClick={applyOperatorPreset}
                    className="min-h-[42px] px-3.5 rounded-lg bg-[#18212e] hover:bg-[#202c3d] text-xs font-black text-slate-200 border border-[#2c3b50] active:translate-y-0.5 transition-all"
                  >
                    OPERATOR PRESET
                  </button>
                  <button
                    type="button"
                    onClick={applyAdminPreset}
                    className="min-h-[42px] px-3.5 rounded-lg bg-[#18212e] hover:bg-[#202c3d] text-xs font-black text-slate-200 border border-[#2c3b50] active:translate-y-0.5 transition-all"
                  >
                    ADMIN PRESET
                  </button>
                  <button
                    type="button"
                    onClick={selectAll}
                    className="min-h-[42px] px-3 rounded-lg bg-[#18212e] hover:bg-[#202c3d] text-xs font-black text-sky-400 border border-[#2c3b50] active:translate-y-0.5"
                  >
                    SELECT ALL
                  </button>
                  <button
                    type="button"
                    onClick={clearAll}
                    className="min-h-[42px] px-3 rounded-lg bg-[#18212e] hover:bg-[#202c3d] text-xs font-black text-slate-400 border border-[#2c3b50] active:translate-y-0.5"
                  >
                    CLEAR
                  </button>
                </div>
              </div>

              {/* Categorized Permissions Grid */}
              <div className="space-y-6 pt-5">
                {categories.map((cat) => {
                  const items = SYSTEM_PERMISSIONS.filter((p) => p.category === cat.id);
                  const Icon = cat.icon;

                  return (
                    <div key={cat.id} className="space-y-2">
                      <div className="flex items-center gap-2 text-xs font-black text-sky-400 tracking-wider">
                        <Icon className="w-4 h-4" />
                        <span>{cat.label}</span>
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                        {items.map((perm) => {
                          const isChecked =
                            selectedUser.role === 'SUPER_ADMIN' ||
                            activePermissions.includes(perm.key);
                          const isSuperOnly = perm.key === 'action:super_menu_config' || perm.key === 'menu:config';

                          return (
                            <div
                              key={perm.key}
                              onClick={() => {
                                if (selectedUser.role !== 'SUPER_ADMIN') {
                                  togglePermission(perm.key);
                                }
                              }}
                              className={`p-3 rounded-lg border flex items-start gap-3 transition-all ${
                                isChecked
                                  ? 'bg-[#152336] border-sky-500/60 shadow-sm text-white'
                                  : 'bg-[#0f1622] border-[#1a2333] text-slate-400 hover:border-slate-600'
                              } ${selectedUser.role === 'SUPER_ADMIN' ? 'cursor-default' : 'cursor-pointer'}`}
                            >
                              <div className="pt-0.5">
                                {isChecked ? (
                                  <CheckSquare className="w-4 h-4 text-sky-400 shrink-0" />
                                ) : (
                                  <Square className="w-4 h-4 text-slate-600 shrink-0" />
                                )}
                              </div>
                              <div className="flex-1">
                                <div className="text-xs font-bold leading-tight flex items-center justify-between">
                                  <span>{perm.label}</span>
                                  {isSuperOnly && (
                                    <span className="text-[9px] bg-rose-950 text-rose-300 px-1.5 py-0.2 rounded font-mono border border-rose-800">
                                      WITH US ONLY
                                    </span>
                                  )}
                                </div>
                                <div className="text-[11px] text-slate-400 mt-0.5 leading-snug">
                                  {perm.description}
                                </div>
                              </div>
                            </div>
                          );
                        })}
                      </div>
                    </div>
                  );
                })}
              </div>

              {/* Save Button Bar */}
              <div className="mt-8 pt-4 border-t border-[#1f293d] flex items-center justify-between">
                <span className="text-xs text-slate-400">
                  Changes take effect immediately on next operator login or screen refresh.
                </span>
                <button
                  type="button"
                  disabled={isSaving}
                  onClick={handleSaveUser}
                  className="px-6 py-2.5 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-500 hover:to-teal-600 font-extrabold text-xs text-white shadow-lg shadow-emerald-700/25 flex items-center gap-2 active:translate-y-0.5 transition-all"
                >
                  <Save className="w-4 h-4" />
                  <span>{isSaving ? 'SAVING PERMISSIONS...' : 'SAVE USER PERMISSIONS'}</span>
                </button>
              </div>
            </div>
          </div>
        ) : (
          <div className="flex-1 flex items-center justify-center text-slate-500 text-sm">
            Select a user from the directory to inspect and configure permissions.
          </div>
        )}
      </div>

      {/* NEW USER MODAL */}
      {isNewUserModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
          <div className="bg-[#111827] border-2 border-[#1f293d] rounded-xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div className="flex items-center justify-between border-b border-[#1f293d] pb-3">
              <h3 className="text-sm font-black text-white flex items-center gap-2">
                <UserPlus className="w-4 h-4 text-sky-400" />
                CREATE NEW OPERATOR / ADMIN
              </h3>
              <button
                onClick={() => setIsNewUserModalOpen(false)}
                className="text-slate-400 hover:text-white"
              >
                ✕
              </button>
            </div>

            <form onSubmit={handleCreateNewUser} className="space-y-4">
              <div>
                <label className="block text-xs font-bold text-slate-400 mb-1">Full Name</label>
                <input
                  type="text"
                  required
                  value={newName}
                  onChange={(e) => setNewName(e.target.value)}
                  placeholder="e.g. Line Operator 2"
                  className="w-full bg-[#162032] border border-[#223147] rounded px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500"
                />
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-400 mb-1">Username (Login ID)</label>
                <input
                  type="text"
                  required
                  value={newUsername}
                  onChange={(e) => setNewUsername(e.target.value)}
                  placeholder="e.g. operator2"
                  className="w-full bg-[#162032] border border-[#223147] rounded px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-xs font-bold text-slate-400 mb-1">Role</label>
                  <select
                    value={newRole}
                    onChange={(e: any) => setNewRole(e.target.value)}
                    className="w-full bg-[#162032] border border-[#223147] rounded px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-500"
                  >
                    <option value="OPERATOR">Operator</option>
                    <option value="ADMIN">Plant Administrator</option>
                  </select>
                </div>

                <div>
                  <label className="block text-xs font-bold text-slate-400 mb-1">Security PIN (4 digits)</label>
                  <input
                    type="text"
                    maxLength={6}
                    required
                    value={newPin}
                    onChange={(e) => setNewPin(e.target.value)}
                    className="w-full bg-[#162032] border border-[#223147] rounded px-3 py-2 text-xs font-mono text-amber-300 focus:outline-none focus:border-sky-500"
                  />
                </div>
              </div>

              <div className="pt-3 flex justify-end gap-2 border-t border-[#1f293d]">
                <button
                  type="button"
                  onClick={() => setIsNewUserModalOpen(false)}
                  className="px-4 py-2 rounded text-xs font-bold text-slate-300 hover:bg-slate-800"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="px-4 py-2 rounded bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold shadow"
                >
                  Create User
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
