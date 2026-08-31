import React, { useEffect, useState } from 'react';
import { ItemRecipe, ProgramCyclePlan, AlignedOperation } from '@innovance-hmi/shared';
import {
  Layers,
  Play,
  RefreshCw,
  Trash2,
  CheckCircle2,
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
        // Default select first recipe with 3 pcs
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
      <div className="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
          <h2 className="text-xl font-bold text-slate-100 flex items-center gap-2">
            <Layers className="w-5 h-5 text-cyan-400" />
            Production Bar Nesting & Multi-Item Alignment Engine
          </h2>
          <p className="text-xs text-slate-400">
            Combine multiple part recipes into raw stock bar batches, minimize scrap, and optimize head firing coordinates.
          </p>
        </div>

        <div className="flex items-center gap-3">
          {commitSuccess && (
            <span className="flex items-center gap-1 text-xs font-mono text-emerald-400 bg-emerald-950/60 px-3 py-1.5 rounded border border-emerald-800">
              <CheckCircle2 className="w-4 h-4" /> BATCH SENT TO PRODUCTION
            </span>
          )}

          <button
            onClick={calculateNesting}
            disabled={calculating || selectedItems.length === 0}
            className="industrial-btn-primary px-4 py-2 text-xs font-mono"
          >
            <RefreshCw className={`w-3.5 h-3.5 ${calculating ? 'animate-spin' : ''}`} />
            {calculating ? 'CALCULATING...' : 'OPTIMIZE & ALIGN'}
          </button>

          {plan && (
            <button
              onClick={commitToProduction}
              disabled={committing}
              className="industrial-btn-success px-4 py-2 text-xs font-mono"
            >
              <Play className="w-3.5 h-3.5" />
              {committing ? 'COMMITTING...' : 'SEND TO PRODUCTION'}
            </button>
          )}
        </div>
      </div>

      {/* Top Configuration & Selection Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Available Recipes Selector */}
        <div className="industrial-card p-4 space-y-3">
          <div className="text-xs font-bold text-slate-300 font-mono flex items-center justify-between">
            <span>AVAILABLE RECIPES</span>
            <span className="text-slate-500">{recipes.length} available</span>
          </div>

          <div className="space-y-1.5 max-h-48 overflow-y-auto">
            {recipes.map((r) => {
              const isAdded = selectedItems.some((i) => i.recipeId === r.id);
              return (
                <div
                  key={r.id}
                  className="flex items-center justify-between p-2.5 rounded bg-slate-950/60 border border-slate-800 text-xs font-mono"
                >
                  <div>
                    <span className="font-bold text-cyan-300">{r.itemCode}</span>
                    <span className="text-slate-400 ml-2">({r.totalLength}mm)</span>
                  </div>
                  <button
                    onClick={() => handleAddItem(r.id)}
                    disabled={isAdded}
                    className={`px-2 py-1 rounded text-[10px] font-bold ${
                      isAdded
                        ? 'bg-slate-800 text-slate-500'
                        : 'bg-cyan-700 hover:bg-cyan-600 text-white shadow'
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
        <div className="industrial-card p-4 space-y-3">
          <div className="text-xs font-bold text-slate-300 font-mono flex items-center justify-between">
            <span>BATCH TARGET ITEMS</span>
            <span className="text-cyan-400">{selectedItems.length} selected</span>
          </div>

          <div className="space-y-2 max-h-48 overflow-y-auto">
            {selectedItems.map((item) => {
              const recipe = recipes.find((r) => r.id === item.recipeId);
              return (
                <div
                  key={item.recipeId}
                  className="flex items-center justify-between p-2.5 rounded bg-slate-950 border border-slate-800 text-xs font-mono"
                >
                  <div>
                    <div className="font-bold text-slate-200">{recipe?.itemCode}</div>
                    <div className="text-[10px] text-slate-400">Length: {recipe?.totalLength}mm</div>
                  </div>

                  <div className="flex items-center gap-2">
                    <button
                      onClick={() => handleUpdateQuantity(item.recipeId, -1)}
                      className="w-6 h-6 bg-slate-800 hover:bg-slate-700 rounded flex items-center justify-center text-slate-200 font-bold"
                    >
                      -
                    </button>
                    <span className="font-bold text-cyan-300 w-6 text-center">{item.quantity}</span>
                    <button
                      onClick={() => handleUpdateQuantity(item.recipeId, 1)}
                      className="w-6 h-6 bg-slate-800 hover:bg-slate-700 rounded flex items-center justify-center text-slate-200 font-bold"
                    >
                      +
                    </button>
                    <button
                      onClick={() => handleRemoveItem(item.recipeId)}
                      className="p-1 text-rose-400 hover:text-rose-300 ml-1"
                    >
                      <Trash2 className="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Raw Stock Bar Length Selection */}
        <div className="industrial-card p-4 space-y-4">
          <div className="text-xs font-bold text-slate-300 font-mono">RAW STOCK BAR LENGTH</div>

          <div className="grid grid-cols-3 gap-2 text-xs font-mono">
            {[6000, 9000, 12000].map((len) => (
              <button
                key={len}
                onClick={() => setStockBarLength(len)}
                className={`py-3 rounded font-bold border transition-all ${
                  stockBarLength === len
                    ? 'bg-cyan-600 text-white border-cyan-500 shadow'
                    : 'bg-slate-950 text-slate-300 border-slate-800 hover:border-slate-700'
                }`}
              >
                {len / 1000} Meters
                <div className="text-[10px] opacity-80">{len}mm</div>
              </button>
            ))}
          </div>

          {plan && (
            <div className="p-3 bg-slate-950/80 rounded border border-slate-800 space-y-1.5 text-xs font-mono">
              <div className="flex justify-between">
                <span className="text-slate-400">UTILIZATION:</span>
                <span className="text-emerald-400 font-bold">{utilizationPercent}%</span>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-400">UTILIZED:</span>
                <span className="text-slate-200">{plan.utilizedLength} mm</span>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-400">REMNANT SCRAP:</span>
                <span className="text-amber-400 font-bold">{plan.scrapLength} mm</span>
              </div>
            </div>
          )}
        </div>
      </div>

      {/* Visual Stock Bar Representation */}
      {plan && (
        <div className="industrial-card p-5 space-y-3">
          <div className="flex items-center justify-between text-xs font-mono">
            <span className="font-bold text-slate-200">RAW STOCK BAR NESTING LAYOUT ({plan.stockBarLength}mm)</span>
            <span className="text-cyan-400">ESTIMATED CYCLE: ~{plan.estimatedCycleTimeSec}s</span>
          </div>

          {/* Bar Diagram */}
          <div className="w-full h-14 bg-slate-950 border border-slate-800 rounded-lg p-1.5 flex gap-1 items-center overflow-hidden">
            {plan.itemsSummary.map((item, idx) => {
              const recipe = recipes.find((r) => r.id === item.recipeId);
              const partLength = recipe?.totalLength || 1500;
              const widthPct = ((partLength * item.count) / plan.stockBarLength) * 100;

              return (
                <div
                  key={idx}
                  style={{ width: `${widthPct}%` }}
                  className="h-full bg-cyan-900/60 border border-cyan-500/80 rounded flex flex-col items-center justify-center text-[10px] font-mono font-bold text-cyan-200"
                >
                  <span className="truncate px-1">{item.itemCode} (x{item.count})</span>
                  <span className="text-[9px] text-cyan-300/80">{partLength * item.count}mm</span>
                </div>
              );
            })}

            {plan.scrapLength > 0 && (
              <div
                style={{ width: `${(plan.scrapLength / plan.stockBarLength) * 100}%` }}
                className="h-full bg-rose-950/40 border border-rose-800/60 border-dashed rounded flex items-center justify-center text-[10px] font-mono text-rose-400"
              >
                SCRAP ({plan.scrapLength}mm)
              </div>
            )}
          </div>
        </div>
      )}

      {/* Operations Sequence Matrix */}
      {plan && (
        <div className="industrial-card overflow-hidden">
          <div className="px-4 py-3 border-b border-slate-800 bg-slate-950/60 flex items-center justify-between">
            <h3 className="text-xs font-bold text-slate-200 font-mono uppercase">
              Optimized Machine Execution Sequence ({plan.operationsSequence.length} Steps)
            </h3>
            <span className="text-xs text-emerald-400 font-mono">
              Monotonic Forward Feed Algorithm Active
            </span>
          </div>

          <div className="max-h-72 overflow-y-auto">
            <table className="w-full text-left text-xs font-mono">
              <thead className="bg-slate-950/90 text-slate-400 sticky top-0 border-b border-slate-800">
                <tr>
                  <th className="p-2.5 pl-4">Step</th>
                  <th className="p-2.5">Operation</th>
                  <th className="p-2.5">Side</th>
                  <th className="p-2.5">Bar Coord (AX)</th>
                  <th className="p-2.5">Allocated Head</th>
                  <th className="p-2.5">Head Offset (DX)</th>
                  <th className="p-2.5 font-bold text-emerald-400">Target Feed DRO</th>
                  <th className="p-2.5">Tool Size / Text</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800/40">
                {plan.operationsSequence.map((op: AlignedOperation) => (
                  <tr key={op.stepIndex} className="hover:bg-slate-800/30">
                    <td className="p-2.5 pl-4 font-bold text-slate-300">#{op.stepIndex}</td>
                    <td className="p-2.5">
                      <span
                        className={`px-2 py-0.5 rounded text-[10px] font-bold ${
                          op.operationType === 'PUNCH'
                            ? 'bg-cyan-950 text-cyan-400 border border-cyan-800'
                            : op.operationType === 'MARK'
                            ? 'bg-amber-950 text-amber-400 border border-amber-800'
                            : 'bg-rose-950 text-rose-400 border border-rose-800'
                        }`}
                      >
                        {op.operationType}
                      </span>
                    </td>
                    <td className="p-2.5">{op.side}</td>
                    <td className="p-2.5 font-bold text-slate-100">{op.absoluteBarX} mm</td>
                    <td className="p-2.5 font-bold text-cyan-300">{op.allocatedHeadName}</td>
                    <td className="p-2.5 text-slate-400">{op.allocatedHeadOffset} mm</td>
                    <td className="p-2.5 font-black text-emerald-400">{op.requiredFeedAxisPos.toFixed(2)} mm</td>
                    <td className="p-2.5 text-slate-300">
                      {op.toolSize ? `Ø${op.toolSize}mm` : op.markingText || 'Cut Blade'}
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
