import React, { useEffect, useState } from 'react';
import { ItemRecipe, ProgramCyclePlan, AlignedOperation } from '@innovance-hmi/shared';
import { DataTable, Column } from '../components/common/DataTable.js';
import {
  Clock,
  Layers,
  Wrench,
  Activity,
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

  const operationColumns: Column<AlignedOperation>[] = [
    {
      key: 'stepIndex',
      header: 'Step',
      width: '60px',
      render: (op) => <span className="font-bold text-slate-700">#{op.stepIndex}</span>,
    },
    {
      key: 'operationType',
      header: 'Operation',
      render: (op) => (
        <span
          className={`px-2 py-0.5 rounded text-xs font-semibold ${
            op.operationType === 'PUNCH'
              ? 'bg-blue-100 text-blue-800'
              : op.operationType === 'MARK'
              ? 'bg-yellow-100 text-yellow-800'
              : 'bg-red-100 text-red-800'
          }`}
        >
          {op.operationType}
        </span>
      ),
    },
    {
      key: 'side',
      header: 'Flange Side',
      render: (op) => `Side ${op.side}`,
    },
    {
      key: 'absoluteBarX',
      header: 'Bar Coordinate (AX)',
      render: (op) => <span className="font-mono">{op.absoluteBarX} mm</span>,
    },
    {
      key: 'allocatedHeadName',
      header: 'Allocated Tool Head',
      render: (op) => <span className="font-bold text-slate-800">{op.allocatedHeadName}</span>,
    },
    {
      key: 'allocatedHeadOffset',
      header: 'Bed Offset (DX)',
      render: (op) => <span className="text-slate-600">{op.allocatedHeadOffset} mm</span>,
    },
    {
      key: 'requiredFeedAxisPos',
      header: 'Required Feed DRO Pos',
      render: (op) => (
        <span className="font-mono text-cyan-700 font-bold">{op.requiredFeedAxisPos.toFixed(2)} mm</span>
      ),
    },
    {
      key: 'toolSize',
      header: 'Tool / Text',
      render: (op) => (op.toolSize ? `Ø${op.toolSize}mm` : op.markingText || 'Shear Blade'),
    },
  ];

  return (
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* 1. Original 4 Color Admin KPI Stat Widgets */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="widget-stats bg-blue">
          <div className="stats-icon"><Clock className="w-12 h-12" /></div>
          <div className="stats-info">
            <h4>Total Cycle Time</h4>
            <p>{plan?.estimatedCycleTimeSec ? `${plan.estimatedCycleTimeSec}s` : '0s'}</p>
          </div>
        </div>

        <div className="widget-stats bg-green">
          <div className="stats-icon"><Layers className="w-12 h-12" /></div>
          <div className="stats-info">
            <h4>Total Items</h4>
            <p>{selectedItems.reduce((acc, i) => acc + i.quantity, 0)}</p>
          </div>
        </div>

        <div className="widget-stats bg-orange">
          <div className="stats-icon"><Wrench className="w-12 h-12" /></div>
          <div className="stats-info">
            <h4>Total Punches</h4>
            <p>{plan?.operationsSequence.filter((o) => o.operationType === 'PUNCH').length || 0}</p>
          </div>
        </div>

        <div className="widget-stats bg-red">
          <div className="stats-icon"><Activity className="w-12 h-12" /></div>
          <div className="stats-info">
            <h4>Total Marking</h4>
            <p>{plan?.operationsSequence.filter((o) => o.operationType === 'MARK').length || 0}</p>
          </div>
        </div>
      </div>

      {/* 2. Top Header & Action Controls */}
      <div className="flex items-center justify-between pb-2 border-b border-slate-300">
        <div>
          <h2 className="text-lg font-bold text-slate-800">Manage Program Align (programAlignMaster)</h2>
          <p className="text-xs text-slate-500">
            Combine multiple part recipes onto raw angle bars to minimize scrap and generate optimal monotonic feed coordinates.
          </p>
        </div>

        <div className="flex items-center gap-2">
          {commitSuccess && (
            <span className="text-xs font-semibold text-emerald-800 bg-emerald-100 px-3 py-1 rounded border border-emerald-300 flex items-center gap-1">
              <CheckCircle2 className="w-4 h-4" /> Batch Sent to Production!
            </span>
          )}

          <button
            onClick={calculateNesting}
            disabled={calculating || selectedItems.length === 0}
            className="btn-ca btn-ca-primary"
          >
            <RefreshCw className={`w-3.5 h-3.5 ${calculating ? 'animate-spin' : ''}`} />
            {calculating ? 'Calculating...' : 'Optimize & Align'}
          </button>

          {plan && (
            <button
              onClick={commitToProduction}
              disabled={committing}
              className="btn-ca btn-ca-success"
            >
              <Play className="w-3.5 h-3.5" />
              {committing ? 'Committing...' : 'Commit to Production'}
            </button>
          )}
        </div>
      </div>

      {/* 3. Program Selection Grid */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        {/* Available Recipes */}
        <div className="panel">
          <div className="panel-heading">
            <span>Available Recipes</span>
            <span className="text-xs text-slate-300">{recipes.length}</span>
          </div>
          <div className="panel-body p-2 max-h-48 overflow-y-auto space-y-1">
            {recipes.map((r) => (
              <div key={r.id} className="flex items-center justify-between p-2 rounded bg-slate-50 border text-xs">
                <div>
                  <span className="font-bold text-slate-800">{r.itemCode}</span>
                  <span className="text-slate-500 ml-2">({r.totalLength}mm)</span>
                </div>
                <button
                  onClick={() => handleAddItem(r.id)}
                  className="btn-ca btn-ca-primary text-xs py-0.5 px-2"
                >
                  + Add
                </button>
              </div>
            ))}
          </div>
        </div>

        {/* Selected Batch Items */}
        <div className="panel">
          <div className="panel-heading">
            <span>Selected Batch Items</span>
            <span className="text-xs text-slate-300">{selectedItems.length}</span>
          </div>
          <div className="panel-body p-2 max-h-48 overflow-y-auto space-y-1">
            {selectedItems.map((item) => {
              const recipe = recipes.find((r) => r.id === item.recipeId);
              return (
                <div key={item.recipeId} className="flex items-center justify-between p-2 rounded bg-slate-50 border text-xs">
                  <div>
                    <div className="font-bold text-slate-800">{recipe?.itemCode}</div>
                    <div className="text-[10px] text-slate-500">{recipe?.totalLength}mm</div>
                  </div>
                  <div className="flex items-center gap-1">
                    <button
                      onClick={() => handleUpdateQuantity(item.recipeId, -1)}
                      className="btn-ca btn-ca-default px-2 py-0.5"
                    >-</button>
                    <span className="font-bold w-6 text-center">{item.quantity}</span>
                    <button
                      onClick={() => handleUpdateQuantity(item.recipeId, 1)}
                      className="btn-ca btn-ca-default px-2 py-0.5"
                    >+</button>
                    <button
                      onClick={() => handleRemoveItem(item.recipeId)}
                      className="btn-ca btn-ca-danger px-1.5 py-0.5 ml-1"
                    ><Trash2 className="w-3 h-3" /></button>
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Raw Stock Bar Length Selection */}
        <div className="panel">
          <div className="panel-heading">
            <span>Raw Stock Bar Length</span>
          </div>
          <div className="panel-body space-y-3 text-xs">
            <div className="grid grid-cols-3 gap-2">
              {[6000, 9000, 12000].map((len) => (
                <button
                  key={len}
                  onClick={() => setStockBarLength(len)}
                  className={`py-2 px-1 text-center rounded font-bold border transition-all ${
                    stockBarLength === len
                      ? 'bg-blue-600 text-white border-blue-600'
                      : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'
                  }`}
                >
                  {len / 1000} M ({len}mm)
                </button>
              ))}
            </div>

            {plan && (
              <div className="p-2.5 bg-slate-100 rounded border border-slate-300 space-y-1 text-xs">
                <div className="flex justify-between">
                  <span className="text-slate-600">Material Utilization:</span>
                  <span className="font-bold text-green-700">{utilizationPercent}%</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-600">Utilized Length:</span>
                  <span className="font-bold text-slate-800">{plan.utilizedLength} mm</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-600">Scrap Remnant:</span>
                  <span className="font-bold text-orange-700">{plan.scrapLength} mm</span>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>

      {/* 4. Visual Nesting Bar Representation */}
      {plan && (
        <div className="panel">
          <div className="panel-heading">
            <span>Nesting Cut-List Visualization ({plan.stockBarLength}mm)</span>
            <span className="text-xs text-slate-300">Estimated: ~{plan.estimatedCycleTimeSec}s</span>
          </div>
          <div className="panel-body">
            <div className="w-full h-12 bg-slate-200 rounded border border-slate-300 p-1 flex gap-1 items-center overflow-hidden">
              {plan.itemsSummary.map((item, idx) => {
                const recipe = recipes.find((r) => r.id === item.recipeId);
                const partLength = recipe?.totalLength || 1500;
                const widthPct = ((partLength * item.count) / plan.stockBarLength) * 100;

                return (
                  <div
                    key={idx}
                    style={{ width: `${widthPct}%` }}
                    className="h-full bg-blue-600 text-white rounded flex flex-col items-center justify-center text-[10px] font-bold"
                  >
                    <span className="truncate px-1">{item.itemCode} (x{item.count})</span>
                    <span className="text-[9px] text-blue-100">{partLength * item.count}mm</span>
                  </div>
                );
              })}

              {plan.scrapLength > 0 && (
                <div
                  style={{ width: `${(plan.scrapLength / plan.stockBarLength) * 100}%` }}
                  className="h-full bg-red-100 border border-red-300 border-dashed text-red-700 rounded flex items-center justify-center text-[10px] font-bold"
                >
                  Scrap ({plan.scrapLength}mm)
                </div>
              )}
            </div>
          </div>
        </div>
      )}

      {/* 5. Aligned Operations Sequence DataTable */}
      {plan && (
        <DataTable
          title="Manage Program Align Operations DataTable"
          columns={operationColumns}
          data={plan.operationsSequence}
          searchKeys={['allocatedHeadName', 'operationType']}
        />
      )}
    </div>
  );
};
