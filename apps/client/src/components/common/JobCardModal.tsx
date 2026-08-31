import React from 'react';
import { ItemRecipe } from '@innovance-hmi/shared';
import {
  FileText,
  Printer,
  X,
  CheckCircle,
} from 'lucide-react';

interface JobCardModalProps {
  isOpen: boolean;
  recipe: ItemRecipe;
  onClose: () => void;
}

export const JobCardModal: React.FC<JobCardModalProps> = ({
  isOpen,
  recipe,
  onClose,
}) => {
  if (!isOpen) return null;

  const handlePrint = () => {
    window.print();
  };

  const weightPerMeter = 14.2;
  const totalWeightKg = Number(((recipe.totalLength / 1000) * weightPerMeter).toFixed(2));

  return (
    <div className="fixed inset-0 bg-slate-900/75 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div className="bg-white rounded-lg shadow-2xl border border-slate-300 w-full max-w-4xl max-h-[92vh] flex flex-col overflow-hidden text-xs">
        {/* Header */}
        <div className="panel-heading bg-slate-800 text-white px-4 py-3 flex items-center justify-between no-print">
          <div className="flex items-center gap-2">
            <FileText className="w-5 h-5 text-cyan-400" />
            <span className="font-bold text-sm">Shopfloor Production Job Card & Cutting Sheet (HPT Standard)</span>
          </div>
          <div className="flex items-center gap-2">
            <button onClick={handlePrint} className="btn-ca btn-ca-primary text-xs py-1 px-3">
              <Printer className="w-3.5 h-3.5" /> Print Job Card
            </button>
            <button onClick={onClose} className="text-slate-300 hover:text-white">
              <X className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* Printable Job Sheet Body */}
        <div className="p-6 overflow-y-auto space-y-4 print:p-0 print:m-0">
          {/* Company & Project Banner */}
          <div className="border-b-2 border-slate-800 pb-3 flex items-start justify-between">
            <div>
              <h1 className="text-xl font-black text-slate-900 tracking-tight">HYDRO POWER TECH ENGINEERING (HPT)</h1>
              <div className="text-xs text-slate-600 font-semibold">CNC 6-Head Angle Punching, Marking & Shearing Production Line</div>
              <div className="text-[11px] text-slate-500">Rajkot, Gujarat • Project: 765kV Power Transmission Tower Line</div>
            </div>

            <div className="text-right">
              <div className="border-2 border-slate-800 p-2 rounded text-center">
                <div className="text-[10px] font-bold text-slate-500">JOB ORDER NUMBER</div>
                <div className="font-mono font-black text-sm text-slate-900">JOB-{Date.now().toString().slice(-6)}</div>
              </div>
            </div>
          </div>

          {/* Work Order Specification Table */}
          <div className="grid grid-cols-4 gap-3 bg-slate-50 p-3 rounded border border-slate-200">
            <div>
              <div className="text-slate-500 font-semibold text-[10px]">TOWER ITEM CODE</div>
              <div className="font-bold text-sm text-blue-900">{recipe.itemCode}</div>
            </div>
            <div>
              <div className="text-slate-500 font-semibold text-[10px]">ANGLE PROFILE (L)</div>
              <div className="font-bold text-sm text-slate-800">L{recipe.angleWidthA} x {recipe.angleWidthB} x {recipe.thickness} mm</div>
            </div>
            <div>
              <div className="text-slate-500 font-semibold text-[10px]">PROGRAM CUT LENGTH</div>
              <div className="font-bold text-sm text-slate-900">{recipe.totalLength} mm</div>
            </div>
            <div>
              <div className="text-slate-500 font-semibold text-[10px]">ESTIMATED PIECE WEIGHT</div>
              <div className="font-bold text-sm text-emerald-800">{totalWeightKg} kg</div>
            </div>
            <div>
              <div className="text-slate-500 font-semibold text-[10px]">STEEL MATERIAL GRADE</div>
              <div className="font-semibold text-slate-800">IS 2062 E350 (High Tensile)</div>
            </div>
            <div>
              <div className="text-slate-500 font-semibold text-[10px]">OPERATIONS COUNT</div>
              <div className="font-semibold text-slate-800">{recipe.steps.length} Operations</div>
            </div>
            <div>
              <div className="text-slate-500 font-semibold text-[10px]">TARGET QUANTITY</div>
              <div className="font-bold text-slate-900">40 Pieces (Shift A)</div>
            </div>
            <div>
              <div className="text-slate-500 font-semibold text-[10px]">QUALITY APPROVAL</div>
              <div className="text-emerald-700 font-bold flex items-center gap-1">
                <CheckCircle className="w-3.5 h-3.5" /> IS 802 Verified
              </div>
            </div>
          </div>

          {/* Tooling Allocation Matrix */}
          <div>
            <div className="font-bold text-slate-800 text-xs mb-1">Installed Tooling Allocation Matrix:</div>
            <div className="grid grid-cols-4 gap-2">
              <div className="p-2 bg-blue-50 border border-blue-200 rounded">
                <span className="font-bold text-blue-900 block text-[11px]">Flange A Tooling:</span>
                <span className="text-[10px] text-slate-700">DA1: Ø18mm • DA2: Ø22mm • DA3: Ø14mm</span>
              </div>
              <div className="p-2 bg-emerald-50 border border-emerald-200 rounded">
                <span className="font-bold text-emerald-900 block text-[11px]">Flange B Tooling:</span>
                <span className="text-[10px] text-slate-700">DB1: Ø18mm • DB2: Ø22mm • DB3: Ø26mm</span>
              </div>
              <div className="p-2 bg-amber-50 border border-amber-200 rounded">
                <span className="font-bold text-amber-900 block text-[11px]">Marking Cassette:</span>
                <span className="text-[10px] text-slate-700">Tag: T1-L15 (8 Chars)</span>
              </div>
              <div className="p-2 bg-red-50 border border-red-200 rounded">
                <span className="font-bold text-red-900 block text-[11px]">Hydraulic Cutter:</span>
                <span className="text-[10px] text-slate-700">Single Diagonal Shear</span>
              </div>
            </div>
          </div>

          {/* Step Operations Schedule */}
          <div>
            <div className="font-bold text-slate-800 text-xs mb-1">Punching & Shearing Sequence Schedule:</div>
            <table className="table-custom border w-full">
              <thead>
                <tr>
                  <th>Seq #</th>
                  <th>Operation</th>
                  <th>Flange</th>
                  <th>Feed Pos (X mm)</th>
                  <th>Gauge Pos (Y mm)</th>
                  <th>Tool Die</th>
                  <th>Details / Stamp Text</th>
                </tr>
              </thead>
              <tbody>
                {recipe.steps.map((s) => (
                  <tr key={s.id || s.stepNumber}>
                    <td className="font-bold text-slate-700">#{s.stepNumber}</td>
                    <td>
                      <span className="font-bold text-slate-900">{s.operationType}</span>
                    </td>
                    <td>Side {s.side}</td>
                    <td className="font-mono font-bold text-blue-900">{s.xPosition} mm</td>
                    <td className="font-mono">{s.yPosition} mm</td>
                    <td>{s.toolSize ? `Ø${s.toolSize} mm` : '-'}</td>
                    <td>{s.markingText || (s.isCutOff ? 'End Shear Cut-off' : 'Through-Hole')}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Signatures Footer */}
          <div className="pt-6 grid grid-cols-3 gap-6 text-center text-xs border-t border-slate-300">
            <div>
              <div className="border-b border-slate-400 pb-8" />
              <span className="text-slate-600 block mt-1 font-semibold">CAD Programmer Sign</span>
            </div>
            <div>
              <div className="border-b border-slate-400 pb-8" />
              <span className="text-slate-600 block mt-1 font-semibold">Machine Operator Sign</span>
            </div>
            <div>
              <div className="border-b border-slate-400 pb-8" />
              <span className="text-slate-600 block mt-1 font-semibold">QC Inspector Sign</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
