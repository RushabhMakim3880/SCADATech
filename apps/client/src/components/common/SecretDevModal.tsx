import React, { useState } from 'react';
import { ShieldAlert, X, Delete, Check, Lock } from 'lucide-react';
import { useAuthStore } from '../../stores/useAuthStore.js';
import { HmiAlert } from '../../utils/alerts.js';

interface SecretDevModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSuccessNavigate?: () => void;
}

export const SecretDevModal: React.FC<SecretDevModalProps> = ({
  isOpen,
  onClose,
  onSuccessNavigate,
}) => {
  const { loginWithSecretCode } = useAuthStore();
  const [code, setCode] = useState<string>('');
  const [errorMsg, setErrorMsg] = useState<string | null>(null);
  const [isSubmitting, setIsSubmitting] = useState<boolean>(false);

  if (!isOpen) return null;

  const handleKeyPress = (digit: string) => {
    if (code.length < 8) {
      setCode((prev) => prev + digit);
      setErrorMsg(null);
    }
  };

  const handleBackspace = () => {
    setCode((prev) => prev.slice(0, -1));
    setErrorMsg(null);
  };

  const handleClear = () => {
    setCode('');
    setErrorMsg(null);
  };

  const handleSubmit = async (e?: React.FormEvent) => {
    if (e) e.preventDefault();
    if (!code) {
      setErrorMsg('Enter Developer Secret Code');
      return;
    }

    setIsSubmitting(true);
    setErrorMsg(null);

    const res = await loginWithSecretCode(code);
    setIsSubmitting(false);

    if (res.success) {
      HmiAlert.success('OEM Developer Access Granted: Super Admin Panel Unlocked!');
      onClose();
      setCode('');
      if (onSuccessNavigate) {
        onSuccessNavigate();
      }
    } else {
      setErrorMsg('Access Denied: Invalid Developer Code');
      setCode('');
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4 select-none backdrop-blur-sm">
      <div className="bg-[#0e131d] border-2 border-rose-900/80 rounded-xl shadow-2xl max-w-md w-full overflow-hidden">
        {/* Header */}
        <div className="bg-[#171a26] px-5 py-4 border-b border-rose-950/80 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-lg bg-rose-950/60 text-rose-400 border border-rose-800 flex items-center justify-center shrink-0">
              <ShieldAlert className="w-5 h-5" />
            </div>
            <div>
              <div className="text-sm font-black text-white tracking-wider uppercase flex items-center gap-2">
                <span>OEM DEVELOPER ACCESS</span>
                <span className="text-[10px] bg-rose-950 text-rose-300 font-mono px-1.5 py-0.5 rounded border border-rose-800 font-bold">
                  RESTRICTED
                </span>
              </div>
              <div className="text-xs text-slate-400">
                Enter Secret Passcode to Unlock Super Admin
              </div>
            </div>
          </div>
          <button
            type="button"
            onClick={onClose}
            className="w-10 h-10 rounded-lg bg-[#141824] border border-[#232a3d] text-slate-400 hover:text-white flex items-center justify-center transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Body */}
        <div className="p-5 space-y-4">
          <div className="bg-[#080b12] p-3 rounded-lg border border-[#1b2234] text-xs text-slate-300 flex items-center gap-2.5">
            <Lock className="w-4 h-4 text-amber-400 shrink-0" />
            <span>This engineering console is restricted to system developers and OEM personnel only.</span>
          </div>

          {/* Masked PIN Display */}
          <div className="bg-[#06080e] border-2 border-[#1c2438] rounded-lg p-3.5 flex items-center justify-center gap-4">
            {[0, 1, 2, 3].map((idx) => {
              const filled = code.length > idx;
              return (
                <div
                  key={idx}
                  className={`w-6 h-6 rounded-full transition-all ${
                    filled
                      ? 'bg-rose-500 scale-105 shadow-[0_0_10px_#f43f5e]'
                      : 'border-2 border-slate-700 bg-[#101420]'
                  }`}
                />
              );
            })}
            {code.length > 4 && (
              <span className="text-rose-400 font-mono text-sm font-bold ml-1">
                +{code.length - 4}
              </span>
            )}
          </div>

          {/* Error Message */}
          {errorMsg && (
            <div className="bg-[#291010] border border-rose-800 rounded-lg p-2.5 text-xs text-rose-200 flex items-center gap-2">
              <ShieldAlert className="w-4 h-4 text-rose-400 shrink-0" />
              <span>{errorMsg}</span>
            </div>
          )}

          {/* Touch Keypad */}
          <div className="grid grid-cols-3 gap-2 pt-1">
            {['1', '2', '3', '4', '5', '6', '7', '8', '9'].map((digit) => (
              <button
                key={digit}
                type="button"
                onClick={() => handleKeyPress(digit)}
                className="touch-key-btn h-14 bg-[#141b28] hover:bg-[#1c2638] text-white border border-[#232f44] rounded-lg font-mono font-bold text-lg"
              >
                {digit}
              </button>
            ))}
            <button
              type="button"
              onClick={handleClear}
              className="touch-key-btn h-14 text-xs font-black text-rose-300 bg-[#261317] border border-rose-900/60 hover:bg-[#33181d] rounded-lg"
            >
              CLEAR
            </button>
            <button
              type="button"
              onClick={() => handleKeyPress('0')}
              className="touch-key-btn h-14 bg-[#141b28] hover:bg-[#1c2638] text-white border border-[#232f44] rounded-lg font-mono font-bold text-lg"
            >
              0
            </button>
            <button
              type="button"
              onClick={handleBackspace}
              className="touch-key-btn h-14 text-amber-300 bg-[#27190f] border border-amber-900/60 hover:bg-[#362112] rounded-lg flex items-center justify-center"
            >
              <Delete className="w-6 h-6" />
            </button>
          </div>

          {/* Confirm Button */}
          <button
            type="button"
            disabled={isSubmitting || code.length === 0}
            onClick={() => handleSubmit()}
            className="w-full h-14 rounded-lg font-black text-sm tracking-wider uppercase bg-gradient-to-r from-rose-700 to-red-600 hover:from-rose-600 hover:to-red-500 text-white border border-rose-500 shadow-lg flex items-center justify-center gap-2 active:translate-y-0.5 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
          >
            <Check className="w-5 h-5" />
            <span>{isSubmitting ? 'AUTHORIZING...' : 'UNLOCK SUPER ADMIN'}</span>
          </button>
        </div>
      </div>
    </div>
  );
};
