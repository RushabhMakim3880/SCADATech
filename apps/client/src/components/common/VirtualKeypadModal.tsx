import React, { useState } from 'react';
import { Delete, Check, X } from 'lucide-react';

interface VirtualKeypadModalProps {
  isOpen: boolean;
  title: string;
  initialValue: number | string;
  unit?: string;
  onClose: () => void;
  onSubmit: (value: number) => void;
}

export const VirtualKeypadModal: React.FC<VirtualKeypadModalProps> = ({
  isOpen,
  title,
  initialValue,
  unit = 'mm',
  onClose,
  onSubmit,
}) => {
  const [valStr, setValStr] = useState<string>(String(initialValue));

  if (!isOpen) return null;

  const handleDigit = (digit: string) => {
    if (valStr === '0') setValStr(digit);
    else setValStr((prev) => prev + digit);
  };

  const handleDot = () => {
    if (!valStr.includes('.')) {
      setValStr((prev) => prev + '.');
    }
  };

  const handleBackspace = () => {
    setValStr((prev) => (prev.length > 1 ? prev.slice(0, -1) : '0'));
  };

  const handleClear = () => {
    setValStr('0');
  };

  const handleToggleSign = () => {
    if (valStr.startsWith('-')) {
      setValStr(valStr.slice(1));
    } else if (valStr !== '0') {
      setValStr('-' + valStr);
    }
  };

  const handleConfirm = () => {
    const num = parseFloat(valStr);
    onSubmit(isNaN(num) ? 0 : num);
    onClose();
  };

  return (
    <div className="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div className="bg-[#1c2530] text-white rounded-xl shadow-2xl border border-slate-700 w-80 flex flex-col overflow-hidden select-none">
        {/* Header */}
        <div className="p-3 bg-[#141b22] border-b border-slate-700 flex items-center justify-between">
          <span className="font-bold text-xs text-slate-300 uppercase tracking-wider">{title}</span>
          <button onClick={onClose} className="text-slate-400 hover:text-white">
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Big High-Visibility Readout Display */}
        <div className="p-4 bg-[#0a0e14] border-b border-slate-700 text-right">
          <div className="font-mono text-3xl font-black text-[#00ffcc] tracking-wider truncate">
            {valStr} <span className="text-sm font-sans text-slate-400 font-bold">{unit}</span>
          </div>
        </div>

        {/* 4x4 Touch Keypad Grid */}
        <div className="p-3 grid grid-cols-4 gap-2 text-lg font-bold">
          {/* Row 1 */}
          <button onClick={() => handleDigit('7')} className="p-3 bg-slate-800 hover:bg-slate-700 active:bg-blue-600 rounded text-center border border-slate-700">7</button>
          <button onClick={() => handleDigit('8')} className="p-3 bg-slate-800 hover:bg-slate-700 active:bg-blue-600 rounded text-center border border-slate-700">8</button>
          <button onClick={() => handleDigit('9')} className="p-3 bg-slate-800 hover:bg-slate-700 active:bg-blue-600 rounded text-center border border-slate-700">9</button>
          <button onClick={handleBackspace} className="p-3 bg-red-900/60 hover:bg-red-800 active:bg-red-700 text-red-200 rounded flex items-center justify-center border border-red-800"><Delete className="w-5 h-5" /></button>

          {/* Row 2 */}
          <button onClick={() => handleDigit('4')} className="p-3 bg-slate-800 hover:bg-slate-700 active:bg-blue-600 rounded text-center border border-slate-700">4</button>
          <button onClick={() => handleDigit('5')} className="p-3 bg-slate-800 hover:bg-slate-700 active:bg-blue-600 rounded text-center border border-slate-700">5</button>
          <button onClick={() => handleDigit('6')} className="p-3 bg-slate-800 hover:bg-slate-700 active:bg-blue-600 rounded text-center border border-slate-700">6</button>
          <button onClick={handleClear} className="p-3 bg-amber-900/60 hover:bg-amber-800 text-amber-200 rounded text-center text-xs font-black border border-amber-800">CLR</button>

          {/* Row 3 */}
          <button onClick={() => handleDigit('1')} className="p-3 bg-slate-800 hover:bg-slate-700 active:bg-blue-600 rounded text-center border border-slate-700">1</button>
          <button onClick={() => handleDigit('2')} className="p-3 bg-slate-800 hover:bg-slate-700 active:bg-blue-600 rounded text-center border border-slate-700">2</button>
          <button onClick={() => handleDigit('3')} className="p-3 bg-slate-800 hover:bg-slate-700 active:bg-blue-600 rounded text-center border border-slate-700">3</button>
          <button onClick={handleToggleSign} className="p-3 bg-slate-700 hover:bg-slate-600 rounded text-center text-sm font-black border border-slate-600">±</button>

          {/* Row 4 */}
          <button onClick={() => handleDigit('0')} className="p-3 bg-slate-800 hover:bg-slate-700 active:bg-blue-600 rounded text-center col-span-2 border border-slate-700">0</button>
          <button onClick={handleDot} className="p-3 bg-slate-800 hover:bg-slate-700 active:bg-blue-600 rounded text-center border border-slate-700">.</button>
          <button onClick={handleConfirm} className="p-3 bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-400 text-white rounded flex items-center justify-center shadow-lg border border-emerald-500"><Check className="w-6 h-6" /></button>
        </div>
      </div>
    </div>
  );
};
