import React, { useState, useEffect } from 'react';
import { useAuthStore } from '../../stores/useAuthStore.js';
import { User, Lock, KeyRound, X, Check, Delete, AlertCircle, ShieldCheck } from 'lucide-react';

export const PinModal: React.FC = () => {
  const {
    isPinModalOpen,
    targetUserForPin,
    users,
    currentUser,
    closePinModal,
    loginWithPin,
    fetchUsers,
  } = useAuthStore();

  const [pin, setPin] = useState<string>('');
  const [selectedUserId, setSelectedUserId] = useState<string>('');
  const [errorMsg, setErrorMsg] = useState<string | null>(null);
  const [isSubmitting, setIsSubmitting] = useState<boolean>(false);

  const visibleUsers = users.filter((u) => u.role !== 'SUPER_ADMIN');

  // Open modal initialization - ONLY runs once when modal opens
  useEffect(() => {
    if (isPinModalOpen) {
      setPin('');
      setErrorMsg(null);
      fetchUsers();

      if (targetUserForPin && targetUserForPin.role !== 'SUPER_ADMIN') {
        setSelectedUserId(targetUserForPin.id);
      } else if (currentUser && currentUser.role !== 'SUPER_ADMIN') {
        setSelectedUserId(currentUser.id);
      }
    }
  }, [isPinModalOpen]);

  // Fallback to select first available user if none is selected yet
  useEffect(() => {
    if (isPinModalOpen && !selectedUserId && visibleUsers.length > 0) {
      const defaultUser = visibleUsers.find((u) => u.role === 'OPERATOR') || visibleUsers[0];
      if (defaultUser) setSelectedUserId(defaultUser.id);
    }
  }, [isPinModalOpen, visibleUsers, selectedUserId]);

  const handleKeyPress = (digit: string) => {
    setPin((prev) => {
      if (prev.length >= 8) return prev;
      const nextPin = prev + digit;
      const matched = users.find((u) => u.pinCode === nextPin);
      if (matched && matched.role !== 'SUPER_ADMIN') {
        setSelectedUserId(matched.id);
      }
      return nextPin;
    });
    setErrorMsg(null);
  };

  const handleBackspace = () => {
    setPin((prev) => prev.slice(0, -1));
    setErrorMsg(null);
  };

  const handleClear = () => {
    setPin('');
    setErrorMsg(null);
  };

  const handleSubmit = async (e?: React.FormEvent) => {
    if (e) e.preventDefault();
    if (!pin) {
      setErrorMsg('Please enter your PIN');
      return;
    }

    setIsSubmitting(true);
    setErrorMsg(null);

    const res = await loginWithPin(pin, selectedUserId || undefined);
    setIsSubmitting(false);

    if (!res.success) {
      setErrorMsg(res.error || 'Incorrect PIN code');
      setPin('');
    }
  };

  // Keyboard navigation & numpad support
  useEffect(() => {
    if (!isPinModalOpen) return;

    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.target instanceof HTMLInputElement || e.target instanceof HTMLTextAreaElement) {
        return;
      }

      if (/^[0-9]$/.test(e.key)) {
        e.preventDefault();
        handleKeyPress(e.key);
      } else if (e.key === 'Backspace') {
        e.preventDefault();
        handleBackspace();
      } else if (e.key === 'Escape') {
        e.preventDefault();
        closePinModal();
      } else if (e.key === 'Enter') {
        e.preventDefault();
        handleSubmit();
      }
    };

    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [isPinModalOpen, pin, selectedUserId, users, isSubmitting]);

  if (!isPinModalOpen) return null;

  const activeTarget = visibleUsers.find((u) => u.id === selectedUserId) || visibleUsers[0] || currentUser;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/85 p-4 select-none backdrop-blur-sm">
      <div className="bg-[#111622] border-2 border-[#222e40] rounded-xl shadow-2xl max-w-md w-full overflow-hidden">
        {/* Header */}
        <div className="bg-[#151c2a] px-5 py-4 border-b border-[#222e40] flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-lg bg-[#1c2637] text-sky-400 border border-[#2b3a52] flex items-center justify-center shrink-0">
              <KeyRound className="w-5 h-5" />
            </div>
            <div>
              <div className="text-sm font-black text-white tracking-wide uppercase">
                SCADA OPERATOR SIGN IN
              </div>
              <div className="text-xs text-slate-400">
                Touch PIN Authentication for SCADA Control
              </div>
            </div>
          </div>
          {currentUser && (
            <button
              onClick={closePinModal}
              className="w-10 h-10 rounded-lg bg-[#1c2637] border border-[#2b3a52] text-slate-400 hover:text-white flex items-center justify-center transition-colors"
            >
              <X className="w-5 h-5" />
            </button>
          )}
        </div>

        {/* Body */}
        <div className="p-5 space-y-4">
          {/* Quick User Selector Buttons */}
          <div>
            <div className="text-[11px] font-black text-slate-400 uppercase tracking-wider mb-2">
              Select Operator / Admin Profile
            </div>
            <div className="grid grid-cols-2 sm:grid-cols-3 gap-2">
              {visibleUsers.map((u) => {
                const isSelected = u.id === selectedUserId;
                const isAdmin = u.role === 'ADMIN';

                return (
                  <button
                    key={u.id}
                    type="button"
                    onClick={() => {
                      setSelectedUserId(u.id);
                      setPin('');
                      setErrorMsg(null);
                    }}
                    className={`p-2.5 rounded-lg border text-left flex flex-col justify-between transition-all min-h-[58px] ${
                      isSelected
                        ? 'bg-[#18283f] border-sky-400 text-white shadow-sm ring-1 ring-sky-400'
                        : 'bg-[#141a25] border-[#222e40] text-slate-300 hover:bg-[#1a2332]'
                    }`}
                  >
                    <div className="flex items-center justify-between mb-1">
                      {isAdmin ? (
                        <ShieldCheck className="w-4 h-4 text-amber-400" />
                      ) : (
                        <User className="w-4 h-4 text-emerald-400" />
                      )}
                      <span className="text-[9px] font-mono font-black text-slate-400">
                        {isAdmin ? 'ADMIN' : 'OPERATOR'}
                      </span>
                    </div>
                    <div className="font-bold text-xs truncate leading-tight">{u.name}</div>
                  </button>
                );
              })}
            </div>
          </div>

          {/* Target User Banner */}
          {activeTarget && (
            <div className="bg-[#0b0e14] p-3 rounded-lg border border-[#1e2736] flex items-center justify-between">
              <div>
                <div className="text-xs font-bold text-white flex items-center gap-1.5">
                  <Lock className="w-3.5 h-3.5 text-sky-400" />
                  Target: <span className="text-sky-300 font-extrabold">{activeTarget.name}</span>
                </div>
                <div className="text-[11px] text-slate-400 mt-0.5">
                  Role: {activeTarget.role === 'ADMIN' ? 'Plant Administrator' : 'Shopfloor Operator'}
                </div>
              </div>
              <span className="font-mono text-xs bg-[#151c27] px-2 py-1 rounded text-slate-300 border border-[#232f42]">
                @{activeTarget.username}
              </span>
            </div>
          )}

          {/* Masked PIN Display */}
          <div className="bg-[#07090e] border-2 border-[#1f2a3a] rounded-lg p-3.5 flex items-center justify-center gap-4">
            {[0, 1, 2, 3].map((idx) => {
              const filled = pin.length > idx;
              return (
                <div
                  key={idx}
                  className={`w-6 h-6 rounded-full transition-all ${
                    filled
                      ? 'bg-sky-400 scale-105 shadow-[0_0_8px_#38bdf8]'
                      : 'border-2 border-slate-700 bg-[#121722]'
                  }`}
                />
              );
            })}
            {pin.length > 4 && (
              <span className="text-sky-400 font-mono text-sm font-bold ml-1">
                +{pin.length - 4}
              </span>
            )}
          </div>

          {/* Error Message */}
          {errorMsg && (
            <div className="bg-[#291010] border border-red-700 rounded-lg p-2.5 text-xs text-red-200 flex items-center gap-2">
              <AlertCircle className="w-4 h-4 text-red-400 shrink-0" />
              <span>{errorMsg}</span>
            </div>
          )}

          {/* Large Touch Keypad (64px Height per Button) */}
          <div className="grid grid-cols-3 gap-2 pt-1">
            {['1', '2', '3', '4', '5', '6', '7', '8', '9'].map((digit) => (
              <button
                key={digit}
                type="button"
                onClick={() => handleKeyPress(digit)}
                className="touch-key-btn"
              >
                {digit}
              </button>
            ))}
            <button
              type="button"
              onClick={handleClear}
              className="touch-key-btn text-xs font-black text-rose-300 bg-[#261317] border-rose-900/60 hover:bg-[#33181d]"
            >
              CLEAR
            </button>
            <button
              type="button"
              onClick={() => handleKeyPress('0')}
              className="touch-key-btn"
            >
              0
            </button>
            <button
              type="button"
              onClick={handleBackspace}
              className="touch-key-btn text-amber-300 bg-[#27190f] border-amber-900/60 hover:bg-[#362112]"
            >
              <Delete className="w-6 h-6" />
            </button>
          </div>

          {/* Confirm Button */}
          <button
            type="button"
            disabled={isSubmitting || pin.length === 0}
            onClick={() => handleSubmit()}
            className="w-full h-14 rounded-lg font-black text-sm tracking-wider uppercase bg-[#1e40af] hover:bg-[#2563eb] text-white border border-[#3b82f6] shadow-lg flex items-center justify-center gap-2 active:translate-y-0.5 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
          >
            <Check className="w-5 h-5" />
            <span>{isSubmitting ? 'VERIFYING...' : 'VERIFY & SIGN IN'}</span>
          </button>

          {/* Quick Credential Hint */}
          <div className="pt-1 flex items-center justify-between text-[11px] text-slate-400 bg-[#0c1017] px-3 py-2 rounded-lg border border-[#1b2331]">
            <span className="text-slate-400">Default PINs:</span>
            <span className="font-mono text-amber-300">Admin: <strong>9999</strong></span>
            <span className="text-slate-600">|</span>
            <span className="font-mono text-emerald-300">Operator: <strong>1234</strong></span>
            <span className="text-slate-600">|</span>
            <span className="font-mono text-rose-300">Super: <strong>7788</strong></span>
          </div>
        </div>
      </div>
    </div>
  );
};
