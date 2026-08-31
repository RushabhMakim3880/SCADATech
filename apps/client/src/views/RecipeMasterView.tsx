import React, { useEffect, useState } from 'react';
import { ItemRecipe, ItemRecipeStep, SideType } from '@innovance-hmi/shared';
import { AngleBarVisualizer } from '../components/canvas/AngleBarVisualizer.js';
import {
  FileCode,
  Plus,
  Trash2,
  Save,
  Layers,
  CheckCircle,
} from 'lucide-react';

export const RecipeMasterView: React.FC = () => {
  const [recipes, setRecipes] = useState<ItemRecipe[]>([]);
  const [selectedRecipe, setSelectedRecipe] = useState<ItemRecipe | null>(null);
  const [selectedStepIdx, setSelectedStepIdx] = useState<number | null>(null);
  const [isSaving, setIsSaving] = useState(false);
  const [saveSuccess, setSaveSuccess] = useState(false);

  useEffect(() => {
    fetchRecipes();
  }, []);

  const fetchRecipes = async () => {
    try {
      const res = await fetch('/api/recipes');
      const json = await res.json();
      if (json.success && json.data.length > 0) {
        setRecipes(json.data);
        setSelectedRecipe(json.data[0]);
      }
    } catch (err) {
      console.error('Failed to fetch recipes', err);
    }
  };

  const handleSelectRecipe = (r: ItemRecipe) => {
    setSelectedRecipe(JSON.parse(JSON.stringify(r)));
    setSelectedStepIdx(null);
  };

  const handleAddStep = () => {
    if (!selectedRecipe) return;
    const newStep: ItemRecipeStep = {
      id: `step-${Date.now()}`,
      stepNumber: selectedRecipe.steps.length + 1,
      operationType: 'PUNCH',
      side: 'A',
      xPosition: 100.0,
      yPosition: 35.0,
      toolSize: 18.0,
      isCutOff: false,
    };
    setSelectedRecipe({
      ...selectedRecipe,
      steps: [...selectedRecipe.steps, newStep],
    });
    setSelectedStepIdx(selectedRecipe.steps.length);
  };

  const handleUpdateStep = (index: number, field: keyof ItemRecipeStep, value: any) => {
    if (!selectedRecipe) return;
    const updated = [...selectedRecipe.steps];
    updated[index] = { ...updated[index], [field]: value };
    setSelectedRecipe({ ...selectedRecipe, steps: updated });
  };

  const handleDeleteStep = (index: number) => {
    if (!selectedRecipe) return;
    const updated = selectedRecipe.steps.filter((_, i) => i !== index);
    setSelectedRecipe({ ...selectedRecipe, steps: updated });
    setSelectedStepIdx(null);
  };

  const handleSaveRecipe = async () => {
    if (!selectedRecipe) return;
    setIsSaving(true);
    try {
      const isNew = !selectedRecipe.id;
      const url = isNew ? '/api/recipes' : `/api/recipes/${selectedRecipe.id}`;
      const method = isNew ? 'POST' : 'PUT';

      const res = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(selectedRecipe),
      });

      const json = await res.json();
      if (json.success) {
        setSaveSuccess(true);
        setTimeout(() => setSaveSuccess(false), 3000);
        fetchRecipes();
      }
    } catch (err) {
      console.error('Failed to save recipe', err);
    } finally {
      setIsSaving(false);
    }
  };

  const handleCreateNew = () => {
    const fresh: ItemRecipe = {
      id: '',
      itemCode: `ANG-NEW-${Date.now().toString().slice(-4)}`,
      itemName: 'New Transmission Angle Part',
      totalLength: 2000.0,
      angleWidthA: 75.0,
      angleWidthB: 75.0,
      thickness: 6.0,
      measurementType: 'ABSOLUTE',
      isActive: true,
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString(),
      steps: [
        {
          id: 'step-1',
          stepNumber: 1,
          operationType: 'PUNCH',
          side: 'A',
          xPosition: 150.0,
          yPosition: 35.0,
          toolSize: 18.0,
          isCutOff: false,
        },
        {
          id: 'step-2',
          stepNumber: 2,
          operationType: 'CUT',
          side: 'NA',
          xPosition: 2000.0,
          yPosition: 0,
          isCutOff: true,
        },
      ],
    };
    setSelectedRecipe(fresh);
    setSelectedStepIdx(null);
  };

  return (
    <div className="p-6 space-y-6 flex-1 overflow-y-auto">
      {/* Header */}
      <div className="flex items-center justify-between border-b border-scada-750 pb-4">
        <div>
          <h2 className="text-xl font-extrabold text-slate-100 flex items-center gap-2.5">
            <FileCode className="w-6 h-6 text-neon-cyan" />
            Item Recipe Master & CAD Coordinates Studio
          </h2>
          <p className="text-xs text-slate-400 font-mono">
            Direct CAD/CAM geometric parameter programming with instant 2D canvas simulation.
          </p>
        </div>

        <div className="flex items-center gap-3">
          {saveSuccess && (
            <span className="flex items-center gap-1.5 text-xs font-mono text-neon-emerald bg-emerald-950 px-3.5 py-2 rounded-xl border border-emerald-500/50 shadow-neon-emerald">
              <CheckCircle className="w-4 h-4" /> RECIPE SAVED TO DATABASE
            </span>
          )}

          <button
            onClick={handleCreateNew}
            className="scada-btn-secondary px-4 py-2 text-xs font-mono"
          >
            <Plus className="w-4 h-4 text-cyan-400" />
            NEW RECIPE
          </button>

          <button
            onClick={handleSaveRecipe}
            disabled={isSaving || !selectedRecipe}
            className="scada-btn-primary px-5 py-2 text-xs font-mono"
          >
            <Save className="w-4 h-4" />
            {isSaving ? 'SAVING...' : 'SAVE RECIPE'}
          </button>
        </div>
      </div>

      {/* Main Studio Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {/* Left: Recipe Catalog List */}
        <div className="scada-panel p-4 space-y-3 lg:col-span-1">
          <div className="text-xs font-bold text-slate-300 font-mono flex items-center justify-between border-b border-scada-750 pb-2">
            <span>SAVED PART CATALOG</span>
            <span className="text-cyan-400 font-extrabold">{recipes.length} Parts</span>
          </div>

          <div className="space-y-2 max-h-[500px] overflow-y-auto pr-1">
            {recipes.map((r) => {
              const isCurrent = selectedRecipe?.id === r.id;
              return (
                <div
                  key={r.id || r.itemCode}
                  onClick={() => handleSelectRecipe(r)}
                  className={`p-3.5 rounded-xl cursor-pointer transition-all border font-mono ${
                    isCurrent
                      ? 'bg-gradient-to-r from-cyan-950 to-scada-800 border-cyan-400/80 shadow-neon-cyan'
                      : 'bg-scada-950/70 border-scada-750 hover:border-slate-600'
                  }`}
                >
                  <div className="flex items-center justify-between">
                    <span className="font-extrabold text-sm text-slate-100">{r.itemCode}</span>
                    <span className="text-xs text-neon-cyan font-bold">{r.totalLength}mm</span>
                  </div>
                  <div className="text-[11px] text-slate-400 truncate mt-1">{r.itemName}</div>
                  <div className="text-[10px] text-slate-400 mt-2 flex items-center gap-2">
                    <span className="px-2 py-0.5 rounded bg-scada-800 text-slate-300">
                      L{r.angleWidthA}x{r.angleWidthB}x{r.thickness}
                    </span>
                    <span>{r.steps.length} Steps</span>
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Right: Recipe Editor & Interactive 2D Canvas */}
        <div className="lg:col-span-3 space-y-6">
          {/* Part Physical Geometry Specs */}
          {selectedRecipe && (
            <div className="scada-panel p-5 space-y-4">
              <div className="text-xs font-bold text-slate-200 font-mono border-b border-scada-750 pb-2">
                STRUCTURAL ANGLE SPECIFICATIONS
              </div>

              <div className="grid grid-cols-2 md:grid-cols-5 gap-3 text-xs font-mono">
                <div>
                  <label className="text-[10px] text-slate-400">PART CODE</label>
                  <input
                    type="text"
                    value={selectedRecipe.itemCode}
                    onChange={(e) =>
                      setSelectedRecipe({ ...selectedRecipe, itemCode: e.target.value })
                    }
                    className="w-full bg-scada-950 border border-scada-750 rounded-lg px-3 py-2 text-slate-100 font-bold focus:border-cyan-400 focus:outline-none mt-1"
                  />
                </div>

                <div>
                  <label className="text-[10px] text-slate-400">TOTAL LENGTH (mm)</label>
                  <input
                    type="number"
                    value={selectedRecipe.totalLength}
                    onChange={(e) =>
                      setSelectedRecipe({
                        ...selectedRecipe,
                        totalLength: parseFloat(e.target.value) || 0,
                      })
                    }
                    className="w-full bg-scada-950 border border-scada-750 rounded-lg px-3 py-2 text-neon-cyan font-bold focus:border-cyan-400 focus:outline-none mt-1"
                  />
                </div>

                <div>
                  <label className="text-[10px] text-slate-400">FLANGE A WIDTH (mm)</label>
                  <input
                    type="number"
                    value={selectedRecipe.angleWidthA}
                    onChange={(e) =>
                      setSelectedRecipe({
                        ...selectedRecipe,
                        angleWidthA: parseFloat(e.target.value) || 0,
                      })
                    }
                    className="w-full bg-scada-950 border border-scada-750 rounded-lg px-3 py-2 text-slate-100 font-bold focus:border-cyan-400 focus:outline-none mt-1"
                  />
                </div>

                <div>
                  <label className="text-[10px] text-slate-400">FLANGE B WIDTH (mm)</label>
                  <input
                    type="number"
                    value={selectedRecipe.angleWidthB}
                    onChange={(e) =>
                      setSelectedRecipe({
                        ...selectedRecipe,
                        angleWidthB: parseFloat(e.target.value) || 0,
                      })
                    }
                    className="w-full bg-scada-950 border border-scada-750 rounded-lg px-3 py-2 text-slate-100 font-bold focus:border-cyan-400 focus:outline-none mt-1"
                  />
                </div>

                <div>
                  <label className="text-[10px] text-slate-400">THICKNESS (mm)</label>
                  <input
                    type="number"
                    value={selectedRecipe.thickness}
                    onChange={(e) =>
                      setSelectedRecipe({
                        ...selectedRecipe,
                        thickness: parseFloat(e.target.value) || 0,
                      })
                    }
                    className="w-full bg-scada-950 border border-scada-750 rounded-lg px-3 py-2 text-slate-100 font-bold focus:border-cyan-400 focus:outline-none mt-1"
                  />
                </div>
              </div>
            </div>
          )}

          {/* Interactive 2D Canvas */}
          <div className="h-60">
            <AngleBarVisualizer
              recipe={selectedRecipe}
              selectedStepIndex={selectedStepIdx}
              onSelectStep={(idx) => setSelectedStepIdx(idx)}
            />
          </div>

          {/* Punching / Marking / Cutting Step Matrix */}
          {selectedRecipe && (
            <div className="scada-panel overflow-hidden">
              <div className="px-5 py-3 border-b border-scada-750 bg-scada-950/90 flex items-center justify-between">
                <div className="text-xs font-extrabold text-slate-200 font-mono uppercase flex items-center gap-2">
                  <Layers className="w-4 h-4 text-cyan-400" />
                  CAD HOLE & OPERATION COORDINATES ({selectedRecipe.steps.length} Steps)
                </div>

                <button
                  onClick={handleAddStep}
                  className="scada-btn-primary px-3.5 py-1.5 text-xs font-mono"
                >
                  <Plus className="w-3.5 h-3.5" />
                  + ADD OPERATION
                </button>
              </div>

              <div className="max-h-64 overflow-y-auto">
                <table className="w-full text-left text-xs font-mono">
                  <thead className="bg-scada-950 text-slate-400 sticky top-0 border-b border-scada-750">
                    <tr>
                      <th className="p-3 pl-4">#</th>
                      <th className="p-3">Type</th>
                      <th className="p-3">Flange</th>
                      <th className="p-3">X Offset (mm)</th>
                      <th className="p-3">Y Gauge (mm)</th>
                      <th className="p-3">Die Size (mm)</th>
                      <th className="p-3">Marking Text</th>
                      <th className="p-3 pr-4 text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-scada-750/50">
                    {selectedRecipe.steps.map((step, idx) => {
                      const isSelected = selectedStepIdx === idx;
                      return (
                        <tr
                          key={step.id || idx}
                          onClick={() => setSelectedStepIdx(idx)}
                          className={`cursor-pointer transition-all ${
                            isSelected ? 'bg-cyan-950/70 text-neon-cyan' : 'hover:bg-scada-850/60'
                          }`}
                        >
                          <td className="p-3 pl-4 font-bold text-slate-400">#{step.stepNumber}</td>
                          <td className="p-3">
                            <select
                              value={step.operationType}
                              onChange={(e) => handleUpdateStep(idx, 'operationType', e.target.value)}
                              className="bg-scada-950 border border-scada-750 rounded px-2 py-1 text-slate-100 font-bold focus:outline-none"
                            >
                              <option value="PUNCH">PUNCH</option>
                              <option value="MARK">MARK</option>
                              <option value="CUT">CUT</option>
                            </select>
                          </td>
                          <td className="p-3">
                            <select
                              value={step.side}
                              onChange={(e) => handleUpdateStep(idx, 'side', e.target.value as SideType)}
                              className="bg-scada-950 border border-scada-750 rounded px-2 py-1 text-slate-100 font-bold focus:outline-none"
                            >
                              <option value="A">Flange A</option>
                              <option value="B">Flange B</option>
                              <option value="NA">Center / Both</option>
                            </select>
                          </td>
                          <td className="p-3">
                            <input
                              type="number"
                              value={step.xPosition}
                              onChange={(e) =>
                                handleUpdateStep(idx, 'xPosition', parseFloat(e.target.value) || 0)
                              }
                              className="w-24 bg-scada-950 border border-scada-750 rounded px-2 py-1 text-neon-cyan font-bold focus:outline-none"
                            />
                          </td>
                          <td className="p-3">
                            <input
                              type="number"
                              value={step.yPosition}
                              onChange={(e) =>
                                handleUpdateStep(idx, 'yPosition', parseFloat(e.target.value) || 0)
                              }
                              className="w-20 bg-scada-950 border border-scada-750 rounded px-2 py-1 text-slate-100 font-bold focus:outline-none"
                            />
                          </td>
                          <td className="p-3">
                            <input
                              type="number"
                              value={step.toolSize || 18}
                              onChange={(e) =>
                                handleUpdateStep(idx, 'toolSize', parseFloat(e.target.value) || 18)
                              }
                              className="w-16 bg-scada-950 border border-scada-750 rounded px-2 py-1 text-slate-100 font-bold focus:outline-none"
                            />
                          </td>
                          <td className="p-3">
                            <input
                              type="text"
                              value={step.markingText || ''}
                              onChange={(e) => handleUpdateStep(idx, 'markingText', e.target.value)}
                              placeholder="Stamp Text"
                              className="w-28 bg-scada-950 border border-scada-750 rounded px-2 py-1 text-slate-100 text-[11px] focus:outline-none"
                            />
                          </td>
                          <td className="p-3 pr-4 text-right">
                            <button
                              onClick={(e) => {
                                e.stopPropagation();
                                handleDeleteStep(idx);
                              }}
                              className="p-1.5 text-rose-400 hover:text-rose-300 hover:bg-rose-950/60 rounded"
                            >
                              <Trash2 className="w-4 h-4" />
                            </button>
                          </td>
                        </tr>
                      );
                    })}
                  </tbody>
                </table>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};
