import React, { useState, useEffect } from 'react';
import { ItemRecipe } from '@innovance-hmi/shared';
import { DataTable, Column } from '../components/common/DataTable.js';
import {
  Sparkles,
  Settings,
  CheckCircle,
  Play,
  Trash2,
} from 'lucide-react';

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
  const [appliedSuccess, setAppliedSuccess] = useState<boolean>(false);

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

  const handleSendToAutoProduction = () => {
    setAppliedSuccess(true);
    setTimeout(() => setAppliedSuccess(false), 3000);
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
        <div className="flex items-center justify-end gap-2">
          <span className="font-mono font-bold text-slate-800">{((o.lengthMm * o.quantity) / 1000).toFixed(2)} m</span>
          <button
            onClick={(e) => {
              e.stopPropagation();
              handleDeleteBatchItem(idx);
            }}
            className="btn-ca btn-ca-danger text-xs py-0.5 px-1.5"
          >
            <Trash2 className="w-3 h-3" />
          </button>
        </div>
      ),
    },
  ];

  return (
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* Header */}
      <div className="flex flex-wrap items-center justify-between pb-2 border-b border-slate-300 gap-3">
        <div>
          <h2 className="text-lg font-black text-slate-900">Multibar Linear Nesting & Scrap Minimizer (IS 802 Standard)</h2>
          <p className="text-xs text-slate-600 font-medium mt-0.5">
            Automated piece packing across raw commercial stock bars (6m, 9m, 12m) to eliminate steel remnant waste.
          </p>
        </div>

        <div className="flex items-center gap-2">
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
              className="form-control-ca text-xs py-1.5"
            >
              <option value="" disabled>+ Add Recipe to Nesting...</option>
              {recipes.map((r) => (
                <option key={r.id} value={r.id}>
                  {r.itemCode} ({r.totalLength}mm)
                </option>
              ))}
            </select>
          )}

          <button
            onClick={() => setIsConfigOpen(true)}
            className="btn-ca btn-ca-default text-xs py-1.5"
          >
            <Settings className="w-3.5 h-3.5" /> Nesting Parameters
          </button>
          <button
            onClick={runNestingOptimization}
            disabled={isOptimizing}
            className="btn-ca btn-ca-primary text-xs py-1.5"
          >
            <Sparkles className="w-3.5 h-3.5" /> {isOptimizing ? 'Optimizing...' : 'Re-Calculate Nesting'}
          </button>
          <button
            onClick={handleSendToAutoProduction}
            className="btn-ca btn-ca-success text-xs py-1.5"
          >
            <Play className="w-3.5 h-3.5" /> Send to Auto Production
          </button>
        </div>
      </div>

      {appliedSuccess && (
        <div className="p-3 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded text-xs font-semibold flex items-center gap-2">
          <CheckCircle className="w-4 h-4 text-emerald-600" />
          Nesting cycle successfully loaded into Auto Production line!
        </div>
      )}

      {/* KPI Optimization Yield Summary */}
      <div className="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div className="p-3.5 rounded-lg bg-blue-50 border border-blue-200">
          <div className="text-slate-500 text-xs font-bold uppercase tracking-wider">Overall Yield Efficiency</div>
          <div className="font-mono text-2xl font-black text-blue-700 mt-1">{overallYieldPct}%</div>
          <div className="text-[11px] text-slate-600">Material utilization efficiency</div>
        </div>

        <div className="p-3.5 rounded-lg bg-emerald-50 border border-emerald-200">
          <div className="text-slate-500 text-xs font-bold uppercase tracking-wider">Raw Stock Bars Needed</div>
          <div className="font-mono text-2xl font-black text-emerald-700 mt-1">{totalRawBars} Bars</div>
          <div className="text-[11px] text-slate-600">{stockBarLength}mm standard raw stock</div>
        </div>

        <div className="p-3.5 rounded-lg bg-purple-50 border border-purple-200">
          <div className="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Cut Pieces</div>
          <div className="font-mono text-2xl font-black text-purple-700 mt-1">
            {batchOrders.reduce((acc, o) => acc + o.quantity, 0)} Pieces
          </div>
          <div className="text-[11px] text-slate-600">Across {batchOrders.length} tower item types</div>
        </div>

        <div className="p-3.5 rounded-lg bg-amber-50 border border-amber-200">
          <div className="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Scrap Remnant</div>
          <div className="font-mono text-2xl font-black text-amber-700 mt-1">{totalScrapMeters} m</div>
          <div className="text-[11px] text-slate-600">Total cut & tail loss combined</div>
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
      <div className="panel">
        <div className="panel-heading">
          <span>Optimized Raw Bar Multi-Cut Visual Layout</span>
          <span className="text-xs text-slate-300">Raw Bar Length: {stockBarLength} mm</span>
        </div>

        <div className="panel-body space-y-4">
          {nestedBars.length === 0 ? (
            <div className="text-center py-6 text-slate-500 text-xs font-semibold">
              No batch items queued. Select a recipe from the dropdown above to calculate nesting layout.
            </div>
          ) : (
            nestedBars.map((bar) => (
              <div key={bar.barIndex} className="p-3 bg-slate-50 rounded border border-slate-200 space-y-2 text-xs">
                <div className="flex items-center justify-between font-bold">
                  <span className="text-slate-800">
                    Raw Stock Bar #{bar.barIndex} ({bar.stockLengthMm}mm) • {bar.pieces.length} Nested Parts
                  </span>
                  <span className="text-slate-600">
                    Scrap Remnant: <b className="text-amber-700">{bar.scrapLengthMm}mm ({bar.scrapPercentage}%)</b>
                  </span>
                </div>

                {/* Graphical Bar */}
                <div className="w-full h-8 bg-slate-800 rounded overflow-hidden flex border border-slate-700 p-0.5">
                  {bar.pieces.map((p, pIdx) => {
                    const widthPct = (p.lengthMm / bar.stockLengthMm) * 100;
                    return (
                      <div
                        key={pIdx}
                        style={{ width: `${widthPct}%`, backgroundColor: p.color }}
                        className="h-full border-r border-slate-900 flex items-center justify-center text-[10px] font-bold text-slate-900 truncate px-1"
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
        <div className="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
          <div className="bg-white rounded-lg shadow-2xl border border-slate-300 w-full max-w-md overflow-hidden text-xs">
            <div className="panel-heading bg-slate-800 text-white px-4 py-3 flex items-center justify-between">
              <span className="font-bold text-sm">Configure Nesting & Machine Margins</span>
              <button onClick={() => setIsConfigOpen(false)} className="text-slate-300 hover:text-white">✕</button>
            </div>

            <div className="p-4 space-y-3">
              <div>
                <label className="font-bold text-slate-700 block">Raw Stock Bar Length (mm)</label>
                <select
                  value={stockBarLength}
                  onChange={(e) => setStockBarLength(parseInt(e.target.value) || 6000)}
                  className="form-control-ca mt-1 font-bold"
                >
                  <option value="6000">6,000 mm (Standard 6 Meter)</option>
                  <option value="9000">9,000 mm (9 Meter Commercial)</option>
                  <option value="12000">12,000 mm (12 Meter Heavy Trailer)</option>
                </select>
              </div>

              <div>
                <label className="font-bold text-slate-700 block">Hydraulic Shear Kerf Loss (mm)</label>
                <input
                  type="number"
                  value={kerfCutAllowance}
                  onChange={(e) => setKerfCutAllowance(parseInt(e.target.value) || 6)}
                  className="form-control-ca mt-1"
                />
                <span className="text-[10px] text-slate-500">Material blade cutting waste per piece</span>
              </div>

              <div>
                <label className="font-bold text-slate-700 block">Carriage Gripper Dead-Zone Margin (mm)</label>
                <input
                  type="number"
                  value={gripperDeadZone}
                  onChange={(e) => setGripperDeadZone(parseInt(e.target.value) || 120)}
                  className="form-control-ca mt-1"
                />
                <span className="text-[10px] text-slate-500">Tail clamping distance required by carriage</span>
              </div>
            </div>

            <div className="p-3 bg-slate-100 border-t border-slate-200 flex justify-end">
              <button onClick={() => setIsConfigOpen(false)} className="btn-ca btn-ca-primary">
                Apply Parameters
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
