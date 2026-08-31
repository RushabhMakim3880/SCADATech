import React, { useEffect, useState } from 'react';
import { ItemRecipe, ProgramCyclePlan, AlignedOperation } from '@innovance-hmi/shared';
import {
  Layers,
  Play,
  RefreshCw,
  Trash2,
  CheckCircle2,
  TrendingUp,
  Percent,
} from 'lucide-react';

export const NestingAlignmentView: React.FC = () => {
  const [recipes, setRecipes] = useState<ItemRecipe[]>([]);
  const [selectedItems, setSelectedItems] = useState<Array<{ recipeId: string; quantity: number }>>([]);
  const [stockBarLength, setStockBarLength] = useState<number>(6000.0);
  const [plan, setPlan] = useState<ProgramCyclePlan | null>(null);
  const [calculating, setCalculating] = useState(false);
  const [committing, setCommitting] = useState(false);
  const [commitSuccess, setCommitSuccess] = useState(false);

  useEffect(() => {
    fetchRecipes();
  }, []);

  const fetchRecipes = async () => {
    try {
      const res = await fetch('/api/recipes');
      const json = await res.json();
      if (json.success && json.data.length > 0) {
        setRecipes(json.data);
        setSelectedItems([{ recipeId: json.data[0].id, quantity: 3 }]);
      }
    } catch (err) {
      console.error('Failed to load recipes', err);
    }
  };

  const handleAddItem = (recipeId: string) => {
    if (selectedItems.some((i) => i.recipeId === recipeId)) return;
    setSelectedItems([...selectedItems, { recipeId, quantity: 1 }]);
  };

  const handleUpdateQuantity = (recipeId: string, delta: number) => {
    setSelectedItems(
      selectedItems.map((i) =>
        i.recipeId === recipeId ? { ...i, quantity: Math.max(1, i.quantity + delta) } : i
      )
    );
  };

  const handleRemoveItem = (recipeId: string) => {
    setSelectedItems(selectedItems.filter((i) => i.recipeId !== recipeId));
  };

  const calculateNesting = async () => {
    if (selectedItems.length === 0) return;
    setCalculating(true);
    try {
      const res = await fetch('/api/production/align', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          items: selectedItems,
          stockBarLength,
        }),
      });

      const json = await res.json();
      if (json.success) {
        setPlan(json.data);
      }
    } catch (err) {
      console.error('Nesting calculation error', err);
    } finally {
      setCalculating(false);
    }
  };

  const commitToProduction = async () => {
    if (!plan) return;
    setCommitting(true);
    try {
      const res = await fetch('/api/production/commit-cycle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          cycleCode: plan.cycleId,
          stockBarLength: plan.stockBarLength,
          utilizedLength: plan.utilizedLength,
          scrapLength: plan.scrapLength,
          targetBars: 1,
          operations: plan.operationsSequence.map((op) => ({
            sequenceOrder: op.stepIndex,
            recipeId: op.recipeId,
            operationType: op.operationType,
            side: op.side,
            absoluteBarX: op.absoluteBarX,
            yPosition: op.yPosition,
            toolSize: op.toolSize,
            allocatedHeadName: op.allocatedHeadName,
            allocatedHeadOffset: op.allocatedHeadOffset,
            requiredFeedAxisPos: op.requiredFeedAxisPos,
            isCutOff: op.isCutOff,
            markingText: op.markingText,
          })),
        }),
      });

      const json = await res.json();
      if (json.success) {
        setCommitSuccess(true);
        setTimeout(() => setCommitSuccess(false), 3000);
      }
    } catch (err) {
      console.error('Commit error', err);
    } finally {
      setCommitting(false);
    }
  };

  const utilizationPercent = plan
    ? Math.round((plan.utilizedLength / plan.stockBarLength) * 100)
    : 0;

  return (
    <div className="p-6 space-y-6 flex-1 overflow-y-auto">
      {/* Header */}
      <div className="flex items-center justify-between border-b border-scada-750 pb-4">
        <div>
          <h2 className="text-xl font-extrabold text-slate-100 flex items-center gap-2.5">
            <Layers className="w-6 h-6 text-neon-cyan" />
            Bar Nesting & Multi-Item Alignment Studio
          </h2>
          <p className="text-xs text-slate-400 font-mono">
            Algorithmic raw bar optimization, scrap minimization, and forward-feed coordinate alignment.
          </p>
        </div>

        <div className="flex items-center gap-3">
          {commitSuccess && (
            <span className="flex items-center gap-1.5 text-xs font-mono text-neon-emerald bg-emerald-950 px-3.5 py-2 rounded-xl border border-emerald-500/50 shadow-neon-emerald">
              <CheckCircle2 className="w-4 h-4" /> BATCH COMMITTED TO CNC PRODUCTION
            </span>
          )}

          <button
            onClick={calculateNesting}
            disabled={calculating || selectedItems.length === 0}
            className="scada-btn-primary px-5 py-2 text-xs font-mono"
          >
            <RefreshCw className={`w-4 h-4 ${calculating ? 'animate-spin' : ''}`} />
            {calculating ? 'CALCULATING...' : 'OPTIMIZE & ALIGN'}
          </button>

          {plan && (
            <button
              onClick={commitToProduction}
              disabled={committing}
              className="scada-btn-success px-5 py-2 text-xs font-mono"
            >
              <Play className="w-4 h-4" />
              {committing ? 'COMMITTING...' : 'SEND TO CNC HUD'}
            </button>
          )}
        </div>
      </div>

      {/* Top Configuration & Selection Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Available Recipes Selector */}
        <div className="scada-panel p-5 space-y-3">
          <div className="text-xs font-bold text-slate-200 font-mono flex items-center justify-between border-b border-scada-750 pb-2">
            <span>AVAILABLE RECIPE CATALOG</span>
            <span className="text-cyan-400 font-extrabold">{recipes.length} available</span>
          </div>

          <div className="space-y-2 max-h-52 overflow-y-auto pr-1">
            {recipes.map((r) => {
              const isAdded = selectedItems.some((i) => i.recipeId === r.id);
              return (
                <div
                  key={r.id}
                  className="flex items-center justify-between p-3 rounded-xl bg-scada-950 border border-scada-750 text-xs font-mono"
                >
                  <div>
                    <span className="font-extrabold text-neon-cyan">{r.itemCode}</span>
                    <span className="text-slate-400 ml-2">({r.totalLength}mm)</span>
                  </div>
                  <button
                    onClick={() => handleAddItem(r.id)}
                    disabled={isAdded}
                    className={`px-3 py-1.5 rounded-lg text-xs font-extrabold transition-all ${
                      isAdded
                        ? 'bg-scada-800 text-slate-500'
                        : 'bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-slate-950 shadow-neon-cyan'
                    }`}
                  >
                    {isAdded ? 'ADDED' : '+ ADD'}
                  </button>
                </div>
              );
            })}
          </div>
        </div>

        {/* Selected Batch Items & Quantities */}
        <div className="scada-panel p-5 space-y-3">
          <div className="text-xs font-bold text-slate-200 font-mono flex items-center justify-between border-b border-scada-750 pb-2">
            <span>BATCH TARGET CUT LIST</span>
            <span className="text-neon-cyan font-bold">{selectedItems.length} items</span>
          </div>

          <div className="space-y-2 max-h-52 overflow-y-auto pr-1">
            {selectedItems.map((item) => {
              const recipe = recipes.find((r) => r.id === item.recipeId);
              return (
                <div
                  key={item.recipeId}
                  className="flex items-center justify-between p-3 rounded-xl bg-scada-950 border border-scada-750 text-xs font-mono"
                >
                  <div>
                    <div className="font-extrabold text-slate-100">{recipe?.itemCode}</div>
                    <div className="text-[10px] text-slate-400">Length: {recipe?.totalLength}mm</div>
                  </div>

                  <div className="flex items-center gap-2">
                    <button
                      onClick={() => handleUpdateQuantity(item.recipeId, -1)}
                      className="w-7 h-7 bg-scada-800 hover:bg-scada-700 rounded-lg flex items-center justify-center text-slate-100 font-extrabold"
                    >
                      -
                    </button>
                    <span className="font-extrabold text-neon-cyan w-8 text-center text-sm">{item.quantity}</span>
                    <button
                      onClick={() => handleUpdateQuantity(item.recipeId, 1)}
                      className="w-7 h-7 bg-scada-800 hover:bg-scada-700 rounded-lg flex items-center justify-center text-slate-100 font-extrabold"
                    >
                      +
                    </button>
                    <button
                      onClick={() => handleRemoveItem(item.recipeId)}
                      className="p-1.5 text-rose-400 hover:text-rose-300 hover:bg-rose-950/60 rounded-lg ml-1"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Raw Stock Bar Length Selection */}
        <div className="scada-panel p-5 space-y-4">
          <div className="text-xs font-bold text-slate-200 font-mono border-b border-scada-750 pb-2">
            RAW STOCK BAR LENGTH SPEC
          </div>

          <div className="grid grid-cols-3 gap-2 text-xs font-mono">
            {[6000, 9000, 12000].map((len) => (
              <button
                key={len}
                onClick={() => setStockBarLength(len)}
                className={`py-3.5 rounded-xl font-extrabold border transition-all ${
                  stockBarLength === len
                    ? 'bg-gradient-to-b from-cyan-500 to-cyan-700 text-slate-950 border-cyan-300 shadow-neon-cyan'
                    : 'bg-scada-950 text-slate-300 border-scada-750 hover:border-slate-600'
                }`}
              >
                {len / 1000} Meters
                <div className="text-[10px] opacity-80">{len}mm</div>
              </button>
            ))}
          </div>

          {plan && (
            <div className="p-4 bg-scada-950 rounded-xl border border-scada-750 space-y-2 text-xs font-mono">
              <div className="flex justify-between items-center">
                <span className="text-slate-400 flex items-center gap-1.5">
                  <Percent className="w-4 h-4 text-emerald-400" /> UTILIZATION:
                </span>
                <span className="text-neon-emerald font-extrabold text-sm">{utilizationPercent}%</span>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-400">UTILIZED MATERIAL:</span>
                <span className="text-slate-100 font-bold">{plan.utilizedLength} mm</span>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-400">SCRAP REMNANT:</span>
                <span className="text-neon-amber font-extrabold">{plan.scrapLength} mm</span>
              </div>
            </div>
          )}
        </div>
      </div>

      {/* Visual Stock Bar Representation */}
      {plan && (
        <div className="scada-panel p-6 space-y-4">
          <div className="flex items-center justify-between text-xs font-mono">
            <span className="font-extrabold text-slate-100 flex items-center gap-2">
              <TrendingUp className="w-4 h-4 text-neon-cyan" />
              RAW STOCK BAR NESTING TIMELINE ({plan.stockBarLength}mm)
            </span>
            <span className="text-neon-cyan font-bold">ESTIMATED PRODUCTION CYCLE: ~{plan.estimatedCycleTimeSec}s</span>
          </div>

          {/* Bar Diagram */}
          <div className="w-full h-16 bg-scada-950 border border-scada-750 rounded-xl p-2 flex gap-1.5 items-center overflow-hidden shadow-inner-dark">
            {plan.itemsSummary.map((item, idx) => {
              const recipe = recipes.find((r) => r.id === item.recipeId);
              const partLength = recipe?.totalLength || 1500;
              const widthPct = ((partLength * item.count) / plan.stockBarLength) * 100;

              return (
                <div
                  key={idx}
                  style={{ width: `${widthPct}%` }}
                  className="h-full bg-gradient-to-r from-cyan-950 via-scada-800 to-cyan-950 border border-cyan-500/80 rounded-lg flex flex-col items-center justify-center text-[10px] font-mono font-bold text-neon-cyan shadow-sm"
                >
                  <span className="truncate px-2">{item.itemCode} (x{item.count})</span>
                  <span className="text-[9px] text-cyan-300/80">{partLength * item.count}mm</span>
                </div>
              );
            })}

            {plan.scrapLength > 0 && (
              <div
                style={{ width: `${(plan.scrapLength / plan.stockBarLength) * 100}%` }}
                className="h-full bg-rose-950/60 border border-rose-600/80 border-dashed rounded-lg flex items-center justify-center text-[10px] font-mono font-extrabold text-rose-300 shadow-sm"
              >
                SCRAP ({plan.scrapLength}mm)
              </div>
            )}
          </div>
        </div>
      )}

      {/* Operations Sequence Matrix */}
      {plan && (
        <div className="scada-panel overflow-hidden">
          <div className="px-5 py-3 border-b border-scada-750 bg-scada-950/90 flex items-center justify-between">
            <h3 className="text-xs font-extrabold text-slate-100 font-mono uppercase">
              CNC OPTIMIZED EXECUTION SEQUENCE MATRIX ({plan.operationsSequence.length} Steps)
            </h3>
            <span className="text-xs text-neon-emerald font-mono font-bold">
              ● Monotonic Forward Feed ($AX - DX$)
            </span>
          </div>

          <div className="max-h-72 overflow-y-auto">
            <table className="w-full text-left text-xs font-mono">
              <thead className="bg-scada-950 text-slate-400 sticky top-0 border-b border-scada-750">
                <tr>
                  <th className="p-3 pl-4">Step</th>
                  <th className="p-3">Operation</th>
                  <th className="p-3">Side</th>
                  <th className="p-3">Bar Coord (AX)</th>
                  <th className="p-3">Allocated Tool</th>
                  <th className="p-3">Head Offset (DX)</th>
                  <th className="p-3 font-extrabold text-neon-emerald">Target Feed DRO</th>
                  <th className="p-3">Tool Size / Spec</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-scada-750/40">
                {plan.operationsSequence.map((op: AlignedOperation) => (
                  <tr key={op.stepIndex} className="hover:bg-scada-850/60 transition-all">
                    <td className="p-3 pl-4 font-extrabold text-slate-300">#{op.stepIndex}</td>
                    <td className="p-3">
                      <span
                        className={`px-2.5 py-1 rounded-md text-[10px] font-extrabold ${
                          op.operationType === 'PUNCH'
                            ? 'bg-cyan-950 text-neon-cyan border border-cyan-500/50'
                            : op.operationType === 'MARK'
                            ? 'bg-amber-950 text-neon-amber border border-amber-500/50'
                            : 'bg-rose-950 text-rose-300 border border-rose-500/50'
                        }`}
                      >
                        {op.operationType}
                      </span>
                    </td>
                    <td className="p-3">{op.side}</td>
                    <td className="p-3 font-bold text-slate-100">{op.absoluteBarX} mm</td>
                    <td className="p-3 font-bold text-neon-cyan">{op.allocatedHeadName}</td>
                    <td className="p-3 text-slate-400">{op.allocatedHeadOffset} mm</td>
                    <td className="p-3 font-extrabold text-neon-emerald text-sm">{op.requiredFeedAxisPos.toFixed(2)} mm</td>
                    <td className="p-3 text-slate-300">
                      {op.toolSize ? `Ø${op.toolSize}mm` : op.markingText || 'Hydraulic Shear Blade'}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>
  );
};
