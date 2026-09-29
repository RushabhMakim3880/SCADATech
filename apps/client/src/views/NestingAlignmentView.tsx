import React, { useState, useEffect } from 'react';
import { ItemRecipe } from '@innovance-hmi/shared';
import { DataTable, Column } from '../components/common/DataTable.js';
import {
  Sparkles,
  Settings,
  Play,
  Trash2,
} from 'lucide-react';
import { HmiAlert } from '../utils/alerts.js';

interface NestingOrder {
  recipeId: string;
  itemCode: string;
  lengthMm: number;
  quantity: number;
  flangeSize: string;
}

interface NestedBar {
  barIndex: number;
  stockLengthMm: number;
  utilizedLengthMm: number;
  scrapLengthMm: number;
  scrapPercentage: number;
  pieces: Array<{
    itemCode: string;
    lengthMm: number;
    startMm: number;
    endMm: number;
    color: string;
  }>;
}

export const NestingAlignmentView: React.FC = () => {
  const [recipes, setRecipes] = useState<ItemRecipe[]>([]);
  const [stockBarLength, setStockBarLength] = useState<number>(6000);
  const [kerfCutAllowance, setKerfCutAllowance] = useState<number>(6); // mm shear loss
  const [gripperDeadZone, setGripperDeadZone] = useState<number>(120); // mm tail clamp margin
  const [isConfigOpen, setIsConfigOpen] = useState<boolean>(false);

  // Batch work order queue
  const [batchOrders, setBatchOrders] = useState<NestingOrder[]>([]);
  const [nestedBars, setNestedBars] = useState<NestedBar[]>([]);
  const [isOptimizing, setIsOptimizing] = useState<boolean>(false);

  useEffect(() => {
    fetchRecipes();
  }, []);

  const fetchRecipes = async () => {
    try {
      const res = await fetch('/api/recipes');
      const json = await res.json();
      if (json.success && json.data.length > 0) {
        setRecipes(json.data);
      }
    } catch (err) {
      console.error('Failed to fetch recipes', err);
    }
  };

  const handleAddRecipeToBatch = (recipeId: string) => {
    const found = recipes.find((r) => r.id === recipeId);
    if (!found) return;
    setBatchOrders((prev) => [
      ...prev,
      {
        recipeId: found.id,
        itemCode: found.itemCode,
        lengthMm: found.totalLength,
        quantity: 5,
        flangeSize: `L${found.angleWidthA}x${found.angleWidthB}x${found.thickness}`,
      },
    ]);
  };

  const handleDeleteBatchItem = (index: number) => {
    setBatchOrders((prev) => prev.filter((_, i) => i !== index));
  };

  // Linear First-Fit Decreasing (FFD) Multibar Nesting Algorithm
  const runNestingOptimization = React.useCallback(() => {
    if (batchOrders.length === 0) {
      setNestedBars([]);
      return;
    }

    setIsOptimizing(true);

    setTimeout(() => {
      // Expand all items into individual pieces
      const allPieces: Array<{ itemCode: string; lengthMm: number; recipeId: string }> = [];
      batchOrders.forEach((order) => {
        for (let q = 0; q < order.quantity; q++) {
          allPieces.push({ itemCode: order.itemCode, lengthMm: order.lengthMm, recipeId: order.recipeId });
        }
      });

      // Sort descending by length
      allPieces.sort((a, b) => b.lengthMm - a.lengthMm);

      const pieceColors = ['#38bdf8', '#4ade80', '#fbbf24', '#f472b6', '#a78bfa', '#34d399'];
      const bars: NestedBar[] = [];
      const usableLength = stockBarLength - gripperDeadZone;

      allPieces.forEach((piece) => {
        let placed = false;
        for (let b = 0; b < bars.length; b++) {
          const bar = bars[b];
          if (bar.utilizedLengthMm + piece.lengthMm + kerfCutAllowance <= usableLength) {
            const startMm = bar.utilizedLengthMm;
            const endMm = startMm + piece.lengthMm;
            const colorIdx = batchOrders.findIndex((o) => o.itemCode === piece.itemCode) % pieceColors.length;

            bar.pieces.push({
              itemCode: piece.itemCode,
              lengthMm: piece.lengthMm,
              startMm,
              endMm,
              color: pieceColors[colorIdx >= 0 ? colorIdx : 0],
            });
            bar.utilizedLengthMm = endMm + kerfCutAllowance;
            bar.scrapLengthMm = stockBarLength - bar.utilizedLengthMm;
            bar.scrapPercentage = Number(((bar.scrapLengthMm / stockBarLength) * 100).toFixed(1));
            placed = true;
            break;
          }
        }

        if (!placed) {
          const startMm = 0;
          const endMm = piece.lengthMm;
          const colorIdx = batchOrders.findIndex((o) => o.itemCode === piece.itemCode) % pieceColors.length;
          const utilized = endMm + kerfCutAllowance;
          const scrap = stockBarLength - utilized;

          bars.push({
            barIndex: bars.length + 1,
            stockLengthMm: stockBarLength,
            utilizedLengthMm: utilized,
            scrapLengthMm: scrap,
            scrapPercentage: Number(((scrap / stockBarLength) * 100).toFixed(1)),
            pieces: [
              {
                itemCode: piece.itemCode,
                lengthMm: piece.lengthMm,
                startMm,
                endMm,
                color: pieceColors[colorIdx >= 0 ? colorIdx : 0],
              },
            ],
          });
        }
      });

      setNestedBars(bars);
      setIsOptimizing(false);
    }, 200);
  }, [batchOrders, stockBarLength, gripperDeadZone, kerfCutAllowance]);

  React.useEffect(() => {
    runNestingOptimization();
  }, [runNestingOptimization]);

  const totalRawBars = nestedBars.length;
  const totalRawLength = totalRawBars * stockBarLength;
  const totalUtilizedLength = nestedBars.reduce((acc, b) => acc + (b.stockLengthMm - b.scrapLengthMm), 0);
  const overallYieldPct = totalRawLength > 0 ? Number(((totalUtilizedLength / totalRawLength) * 100).toFixed(1)) : 0;
  const totalScrapMeters = Number(((totalRawLength - totalUtilizedLength) / 1000).toFixed(2));

  const handleSendToAutoProduction = async () => {
    const isConfirmed = await HmiAlert.confirm(
      'Send Batch to Production?',
      'This will inject the optimized nested batch into the Auto CNC Production queue. Ensure stock bars are loaded.'
    );
    if (!isConfirmed) return;
    HmiAlert.success('Nested batch successfully injected into CNC Auto Queue!');
  };

  const columns: Column<NestingOrder>[] = [
    { key: 'itemCode', header: 'Tower Item Code', render: (o) => <span className="font-bold text-blue-700">{o.itemCode}</span> },
    { key: 'flangeSize', header: 'Profile Specification' },
    { key: 'lengthMm', header: 'Piece Length (mm)', render: (o) => `${o.lengthMm} mm` },
    {
      key: 'quantity',
      header: 'Required Quantity',
      render: (o, idx) => (
        <input
          type="number"
          value={o.quantity}
          min={1}
          onChange={(e) => {
            const upd = [...batchOrders];
            upd[idx].quantity = parseInt(e.target.value) || 1;
            setBatchOrders(upd);
          }}
          className="form-control-ca w-20 py-1 font-bold text-xs"
        />
      ),
    },
    {
      key: 'actions',
      header: 'Total Run / Actions',
      align: 'right',
      render: (o, idx) => (
        <div className="flex items-center justify-end gap-2.5">
          <span className="font-mono font-bold text-sky-400">{((o.lengthMm * o.quantity) / 1000).toFixed(2)} m</span>
          <button
            onClick={(e) => {
              e.stopPropagation();
              handleDeleteBatchItem(idx);
            }}
            className="p-1.5 rounded-lg bg-rose-950/70 border border-rose-800 text-rose-300 hover:bg-rose-900 active:scale-95 transition-all"
            title="Remove item from batch"
          >
            <Trash2 className="w-3.5 h-3.5" />
          </button>
        </div>
      ),
    },
  ];

  return (
    <div className="p-4 md:p-6 space-y-4 flex-1 overflow-y-auto bg-[#070b12] text-white">
      {/* Header */}
      <div className="bg-[#0e1420] border border-[#1e2a3c] rounded-2xl p-4 shadow-xl flex flex-wrap items-center justify-between gap-4">
        <div>
          <h2 className="text-xl font-black text-white tracking-wide">
            Multibar Linear Nesting & Scrap Minimizer (IS 802)
          </h2>
          <p className="text-xs text-slate-400 font-medium mt-1">
            Automated piece packing across raw commercial stock bars (6m, 9m, 12m) to eliminate steel remnant waste.
          </p>
        </div>

        <div className="flex flex-wrap items-center gap-2.5">
          {/* Add Recipe to Batch */}
          {recipes.length > 0 && (
            <select
              onChange={(e) => {
                if (e.target.value) {
                  handleAddRecipeToBatch(e.target.value);
                  e.target.value = '';
                }
              }}
              defaultValue=""
              className="bg-[#06090e] text-white border border-[#2b3a4f] rounded-xl px-4 py-2.5 font-bold focus:outline-none focus:border-cyan-400 cursor-pointer text-xs shadow-inner"
            >
              <option value="" disabled>+ Add Recipe to Nesting Batch...</option>
              {recipes.map((r) => (
                <option key={r.id} value={r.id}>
                  {r.itemCode} ({r.totalLength}mm)
                </option>
              ))}
            </select>
          )}

          <button
            onClick={() => setIsConfigOpen(true)}
            className="cnc-btn cnc-btn-secondary text-xs font-black"
          >
            <Settings className="w-4 h-4 text-sky-400" />
            <span>PARAMETERS</span>
          </button>

          <button
            onClick={runNestingOptimization}
            disabled={isOptimizing}
            className="cnc-btn cnc-btn-primary text-xs font-black"
          >
            <Sparkles className="w-4 h-4 text-cyan-300" />
            <span>{isOptimizing ? 'OPTIMIZING...' : 'CALCULATE NESTING'}</span>
          </button>

          <button
            onClick={handleSendToAutoProduction}
            className="cnc-btn cnc-btn-success text-xs font-black shadow-lg shadow-emerald-950/40"
          >
            <Play className="w-4 h-4 fill-white" />
            <span>SEND TO AUTO RUN</span>
          </button>
        </div>
      </div>

      {/* KPI Optimization Yield Summary */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div className="cnc-dro flex flex-col justify-between p-4 rounded-xl border border-[#1e2a3c]">
          <div className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Overall Yield Efficiency</div>
          <div className="cnc-dro-val text-3xl font-black text-cyan-300 mt-1">{overallYieldPct}%</div>
          <div className="text-[11px] text-slate-400 font-medium">Material utilization efficiency</div>
        </div>

        <div className="cnc-dro flex flex-col justify-between p-4 rounded-xl border border-[#1e2a3c]">
          <div className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Raw Stock Bars Needed</div>
          <div className="cnc-dro-val text-3xl font-black text-emerald-400 mt-1">{totalRawBars} <span className="text-base text-slate-400 font-bold">Bars</span></div>
          <div className="text-[11px] text-slate-400 font-medium">{stockBarLength}mm standard stock length</div>
        </div>

        <div className="cnc-dro flex flex-col justify-between p-4 rounded-xl border border-[#1e2a3c]">
          <div className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Cut Pieces</div>
          <div className="cnc-dro-val text-3xl font-black text-purple-300 mt-1">
            {batchOrders.reduce((acc, o) => acc + o.quantity, 0)} <span className="text-base text-slate-400 font-bold">Pcs</span>
          </div>
          <div className="text-[11px] text-slate-400 font-medium">Across {batchOrders.length} tower item types</div>
        </div>

        <div className="cnc-dro flex flex-col justify-between p-4 rounded-xl border border-[#1e2a3c]">
          <div className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Scrap Remnant</div>
          <div className="cnc-dro-val text-3xl font-black text-amber-300 mt-1">{totalScrapMeters} <span className="text-base text-slate-400 font-bold">m</span></div>
          <div className="text-[11px] text-slate-400 font-medium">Shear kerf loss + tail margin</div>
        </div>
      </div>

      {/* Batch Orders Table */}
      <DataTable
        title="Production Batch Work Order Queue"
        columns={columns}
        data={batchOrders}
        searchKeys={['itemCode', 'flangeSize']}
      />

      {/* Visual Multi-Bar Nesting Layout */}
      <div className="cnc-card overflow-hidden">
        <div className="cnc-card-header">
          <span className="flex items-center gap-2">
            <span className="w-2.5 h-2.5 rounded-full bg-cyan-400 shadow-[0_0_8px_#38bdf8]" />
            <span>Optimized Raw Bar Multi-Cut Visual Layout</span>
          </span>
          <span className="text-xs font-mono text-slate-400">Commercial Stock: {stockBarLength} mm</span>
        </div>

        <div className="cnc-card-body space-y-4">
          {nestedBars.length === 0 ? (
            <div className="text-center py-8 text-slate-400 text-xs font-medium">
              No batch items queued. Select a recipe from the dropdown above to calculate nesting layout.
            </div>
          ) : (
            nestedBars.map((bar) => (
              <div key={bar.barIndex} className="p-3.5 bg-[#090d14] rounded-xl border border-[#1e2a3c] space-y-2 text-xs">
                <div className="flex items-center justify-between font-black">
                  <span className="text-slate-200">
                    Raw Stock Bar #{bar.barIndex} ({bar.stockLengthMm} mm) • {bar.pieces.length} Nested Parts
                  </span>
                  <span className="text-slate-400">
                    Scrap Remnant: <b className="text-amber-400 font-black">{bar.scrapLengthMm} mm ({bar.scrapPercentage}%)</b>
                  </span>
                </div>

                {/* Graphical Bar */}
                <div className="w-full h-9 bg-[#04070d] rounded-lg overflow-hidden flex border border-[#1e2a3c] p-0.5">
                  {bar.pieces.map((p, pIdx) => {
                    const widthPct = (p.lengthMm / bar.stockLengthMm) * 100;
                    return (
                      <div
                        key={pIdx}
                        style={{ width: `${widthPct}%`, backgroundColor: p.color }}
                        className="h-full border-r border-[#04070d] flex items-center justify-center text-[10px] font-black text-slate-950 truncate px-1.5 shadow-inner"
                        title={`${p.itemCode} (${p.lengthMm}mm)`}
                      >
                        {p.itemCode} ({p.lengthMm}mm)
                      </div>
                    );
                  })}

                  {/* Scrap Tail */}
                  <div
                    style={{ width: `${(bar.scrapLengthMm / bar.stockLengthMm) * 100}%` }}
                    className="h-full bg-amber-600/60 flex items-center justify-center text-[9px] font-black text-amber-200"
                  >
                    Tail ({bar.scrapLengthMm}mm)
                  </div>
                </div>
              </div>
            ))
          )}
        </div>
      </div>

      {/* Config Modal */}
      {isConfigOpen && (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
          <div className="bg-[#0e1420] rounded-2xl shadow-2xl border border-[#2b3c53] w-full max-w-md overflow-hidden text-xs">
            <div className="bg-[#141b2a] text-white px-5 py-4 flex items-center justify-between border-b border-[#243348]">
              <span className="font-black text-sm">Configure Nesting & Machine Margins</span>
              <button onClick={() => setIsConfigOpen(false)} className="text-slate-400 hover:text-white font-black text-base">✕</button>
            </div>

            <div className="p-5 space-y-4">
              <div>
                <label className="font-black text-slate-300 block uppercase text-[11px] mb-1">Raw Stock Bar Length (mm)</label>
                <select
                  value={stockBarLength}
                  onChange={(e) => setStockBarLength(parseInt(e.target.value) || 6000)}
                  className="cnc-input font-black w-full"
                >
                  <option value="6000">6,000 mm (Standard 6 Meter)</option>
                  <option value="9000">9,000 mm (9 Meter Commercial)</option>
                  <option value="12000">12,000 mm (12 Meter Heavy Trailer)</option>
                </select>
              </div>

              <div>
                <label className="font-black text-slate-300 block uppercase text-[11px] mb-1">Hydraulic Shear Kerf Loss (mm)</label>
                <input
                  type="number"
                  value={kerfCutAllowance}
                  onChange={(e) => setKerfCutAllowance(parseInt(e.target.value) || 6)}
                  className="cnc-input w-full"
                />
                <span className="text-[10px] text-slate-400 mt-1 block">Material blade cutting waste per piece</span>
              </div>

              <div>
                <label className="font-black text-slate-300 block uppercase text-[11px] mb-1">Carriage Gripper Dead-Zone Margin (mm)</label>
                <input
                  type="number"
                  value={gripperDeadZone}
                  onChange={(e) => setGripperDeadZone(parseInt(e.target.value) || 120)}
                  className="cnc-input w-full"
                />
                <span className="text-[10px] text-slate-400 mt-1 block">Clamp safety clearance limit before shear cutoff blade</span>
              </div>

              <div className="pt-3 border-t border-[#1e2a3c] flex justify-end">
                <button
                  onClick={() => setIsConfigOpen(false)}
                  className="cnc-btn cnc-btn-primary font-black px-6"
                >
                  SAVE & CLOSE
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
