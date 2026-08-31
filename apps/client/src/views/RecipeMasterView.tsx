import React, { useEffect, useState } from 'react';
import { ItemRecipe, ItemRecipeStep, RecipeOperationType, SideType } from '@innovance-hmi/shared';
import { AngleBarVisualizer } from '../components/canvas/AngleBarVisualizer.js';
import {
  FileCode,
  Plus,
  Trash2,
  Save,
  Edit,
  Search,
  CheckCircle,
  Scissors,
  Stamp,
  CircleDot,
} from 'lucide-react';

export const RecipeMasterView: React.FC = () => {
  const [recipes, setRecipes] = useState<ItemRecipe[]>([]);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedRecipe, setSelectedRecipe] = useState<ItemRecipe | null>(null);
  const [isEditing, setIsEditing] = useState(false);
  const [selectedStepIndex, setSelectedStepIndex] = useState<number | null>(null);
  const [saving, setSaving] = useState(false);
  const [saveSuccess, setSaveSuccess] = useState(false);

  // Form State
  const [formData, setFormData] = useState<Partial<ItemRecipe>>({
    itemCode: '',
    itemName: '',
    description: '',
    angleWidthA: 75,
    angleWidthB: 75,
    thickness: 6,
    totalLength: 1500,
    measurementType: 'ABSOLUTE',
    steps: [],
  });

  useEffect(() => {
    fetchRecipes();
  }, []);

  const fetchRecipes = async () => {
    try {
      const res = await fetch('/api/recipes');
      const json = await res.json();
      if (json.success) {
        setRecipes(json.data);
        if (json.data.length > 0 && !selectedRecipe) {
          setSelectedRecipe(json.data[0]);
          setFormData(json.data[0]);
        }
      }
    } catch (err) {
      console.error('Failed to fetch recipes', err);
    }
  };

  const handleSelectRecipe = (r: ItemRecipe) => {
    setSelectedRecipe(r);
    setFormData(r);
    setIsEditing(false);
    setSelectedStepIndex(null);
  };

  const handleCreateNew = () => {
    const newRecipe: Partial<ItemRecipe> = {
      itemCode: `ANG-${Date.now().toString().slice(-4)}`,
      itemName: 'New Angle Part',
      description: 'Angle Punching Profile',
      angleWidthA: 75,
      angleWidthB: 75,
      thickness: 6,
      totalLength: 1500,
      measurementType: 'ABSOLUTE',
      steps: [
        { id: '1', stepNumber: 1, operationType: 'PUNCH', side: 'A', xPosition: 100, yPosition: 35, toolSize: 18, isCutOff: false },
        { id: '2', stepNumber: 2, operationType: 'PUNCH', side: 'B', xPosition: 100, yPosition: 35, toolSize: 18, isCutOff: false },
        { id: '3', stepNumber: 3, operationType: 'CUT', side: 'NA', xPosition: 1500, yPosition: 0, isCutOff: true },
      ],
    };
    setSelectedRecipe(null);
    setFormData(newRecipe);
    setIsEditing(true);
  };

  const handleAddStep = (type: RecipeOperationType) => {
    const currentSteps = formData.steps || [];
    const nextStepNum = currentSteps.length + 1;
    let newStep: ItemRecipeStep;

    if (type === 'PUNCH') {
      newStep = {
        id: String(Date.now()),
        stepNumber: nextStepNum,
        operationType: 'PUNCH',
        side: 'A',
        xPosition: 200,
        yPosition: 35,
        toolSize: 18,
        toolShape: 'ROUND',
        isCutOff: false,
      };
    } else if (type === 'MARK') {
      newStep = {
        id: String(Date.now()),
        stepNumber: nextStepNum,
        operationType: 'MARK',
        side: 'NA',
        xPosition: 50,
        yPosition: 0,
        markingText: 'PART-01',
        isCutOff: false,
      };
    } else {
      newStep = {
        id: String(Date.now()),
        stepNumber: nextStepNum,
        operationType: 'CUT',
        side: 'NA',
        xPosition: formData.totalLength || 1500,
        yPosition: 0,
        isCutOff: true,
      };
    }

    const updatedSteps = [...currentSteps, newStep];
    setFormData({ ...formData, steps: updatedSteps });
    setSelectedStepIndex(updatedSteps.length - 1);
  };

  const handleDeleteStep = (index: number) => {
    const updated = (formData.steps || []).filter((_, idx) => idx !== index);
    setFormData({ ...formData, steps: updated });
    setSelectedStepIndex(null);
  };

  const handleUpdateStep = (index: number, fields: Partial<ItemRecipeStep>) => {
    const updated = (formData.steps || []).map((s, idx) => (idx === index ? { ...s, ...fields } : s));
    setFormData({ ...formData, steps: updated });
  };

  const handleSave = async () => {
    if (!formData.itemCode || !formData.itemName) {
      alert('Please enter Item Code and Item Name');
      return;
    }
    setSaving(true);
    try {
      const isNew = !selectedRecipe?.id;
      const url = isNew ? '/api/recipes' : `/api/recipes/${selectedRecipe.id}`;
      const method = isNew ? 'POST' : 'PUT';

      const res = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      });

      const json = await res.json();
      if (json.success) {
        setSaveSuccess(true);
        setTimeout(() => setSaveSuccess(false), 3000);
        await fetchRecipes();
        setIsEditing(false);
      }
    } catch (err) {
      console.error('Failed to save recipe', err);
    } finally {
      setSaving(false);
    }
  };

  const filteredRecipes = recipes.filter((r) =>
    r.itemCode.toLowerCase().includes(searchQuery.toLowerCase()) ||
    r.itemName.toLowerCase().includes(searchQuery.toLowerCase())
  );

  return (
    <div className="flex h-full overflow-hidden">
      {/* Left Column: Recipe Catalog List */}
      <div className="w-80 bg-slate-900 border-r border-slate-800 flex flex-col justify-between">
        <div className="p-4 border-b border-slate-800 space-y-3">
          <div className="flex items-center justify-between">
            <h2 className="text-base font-bold text-slate-100 flex items-center gap-2">
              <FileCode className="w-4 h-4 text-cyan-400" />
              Item Recipes
            </h2>
            <button
              onClick={handleCreateNew}
              className="px-2.5 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white rounded text-xs font-bold flex items-center gap-1 shadow"
            >
              <Plus className="w-3.5 h-3.5" /> NEW
            </button>
          </div>

          <div className="relative">
            <Search className="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" />
            <input
              type="text"
              placeholder="Search recipes..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full bg-slate-950 border border-slate-800 rounded pl-8 pr-3 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-cyan-500 font-mono"
            />
          </div>
        </div>

        {/* Recipe List Items */}
        <div className="flex-1 overflow-y-auto p-2 space-y-1.5">
          {filteredRecipes.map((r) => {
            const isSelected = selectedRecipe?.id === r.id;
            return (
              <button
                key={r.id}
                onClick={() => handleSelectRecipe(r)}
                className={`w-full text-left p-3 rounded-lg border transition-all ${
                  isSelected
                    ? 'bg-cyan-950/60 border-cyan-500 text-slate-100 shadow'
                    : 'bg-slate-950/40 border-slate-800/80 text-slate-300 hover:bg-slate-800'
                }`}
              >
                <div className="flex items-center justify-between mb-1 font-mono">
                  <span className="font-bold text-xs text-cyan-300">{r.itemCode}</span>
                  <span className="text-[10px] text-slate-400">{r.totalLength}mm</span>
                </div>
                <div className="text-xs font-semibold truncate text-slate-200">{r.itemName}</div>
                <div className="text-[10px] text-slate-400 font-mono mt-1">
                  L{r.angleWidthA}x{r.angleWidthB}x{r.thickness} • {r.steps?.length || 0} Steps
                </div>
              </button>
            );
          })}
        </div>
      </div>

      {/* Main Column: Recipe CAD Editor & Real-time Canvas */}
      <div className="flex-1 flex flex-col overflow-hidden bg-slate-950">
        {/* Top Control Bar */}
        <div className="h-16 px-6 border-b border-slate-800 flex items-center justify-between bg-slate-900/60">
          <div>
            <h1 className="text-base font-bold text-slate-100 font-mono">
              {formData.itemCode || 'Untitled'} - {formData.itemName || 'New Recipe'}
            </h1>
            <p className="text-xs text-slate-400 font-mono">
              Dimensions: L{formData.angleWidthA}x{formData.angleWidthB}x{formData.thickness}mm • Total Length: {formData.totalLength}mm
            </p>
          </div>

          <div className="flex items-center gap-3">
            {saveSuccess && (
              <span className="flex items-center gap-1 text-xs font-mono text-emerald-400 bg-emerald-950/60 px-3 py-1.5 rounded border border-emerald-800">
                <CheckCircle className="w-4 h-4" /> SAVED
              </span>
            )}

            <button
              onClick={() => setIsEditing(!isEditing)}
              className="industrial-btn-secondary px-3.5 py-2 text-xs font-mono"
            >
              <Edit className="w-3.5 h-3.5" />
              {isEditing ? 'HIDE PARAMS' : 'EDIT PARAMS'}
            </button>

            <button
              onClick={handleSave}
              disabled={saving}
              className="industrial-btn-primary px-5 py-2 text-xs font-mono"
            >
              <Save className="w-4 h-4" />
              {saving ? 'SAVING...' : 'SAVE RECIPE'}
            </button>
          </div>
        </div>

        {/* Parameters Form Drawer (if editing is opened) */}
        {isEditing && (
          <div className="bg-slate-900 border-b border-slate-800 p-4 grid grid-cols-2 md:grid-cols-6 gap-3 text-xs font-mono">
            <div>
              <label className="text-slate-400 block mb-1">ITEM CODE</label>
              <input
                type="text"
                value={formData.itemCode || ''}
                onChange={(e) => setFormData({ ...formData, itemCode: e.target.value })}
                className="w-full bg-slate-950 border border-slate-700 rounded px-2.5 py-1.5 text-slate-200"
              />
            </div>
            <div>
              <label className="text-slate-400 block mb-1">ITEM NAME</label>
              <input
                type="text"
                value={formData.itemName || ''}
                onChange={(e) => setFormData({ ...formData, itemName: e.target.value })}
                className="w-full bg-slate-950 border border-slate-700 rounded px-2.5 py-1.5 text-slate-200"
              />
            </div>
            <div>
              <label className="text-slate-400 block mb-1">FLANGE A (mm)</label>
              <input
                type="number"
                value={formData.angleWidthA || 75}
                onChange={(e) => setFormData({ ...formData, angleWidthA: Number(e.target.value) })}
                className="w-full bg-slate-950 border border-slate-700 rounded px-2.5 py-1.5 text-slate-200"
              />
            </div>
            <div>
              <label className="text-slate-400 block mb-1">FLANGE B (mm)</label>
              <input
                type="number"
                value={formData.angleWidthB || 75}
                onChange={(e) => setFormData({ ...formData, angleWidthB: Number(e.target.value) })}
                className="w-full bg-slate-950 border border-slate-700 rounded px-2.5 py-1.5 text-slate-200"
              />
            </div>
            <div>
              <label className="text-slate-400 block mb-1">THICKNESS (mm)</label>
              <input
                type="number"
                value={formData.thickness || 6}
                onChange={(e) => setFormData({ ...formData, thickness: Number(e.target.value) })}
                className="w-full bg-slate-950 border border-slate-700 rounded px-2.5 py-1.5 text-slate-200"
              />
            </div>
            <div>
              <label className="text-slate-400 block mb-1">CUT LENGTH (mm)</label>
              <input
                type="number"
                value={formData.totalLength || 1500}
                onChange={(e) => setFormData({ ...formData, totalLength: Number(e.target.value) })}
                className="w-full bg-slate-950 border border-slate-700 rounded px-2.5 py-1.5 text-slate-200"
              />
            </div>
          </div>
        )}

        {/* Middle: Interactive 2D CAD Canvas */}
        <div className="flex-1 p-4 min-h-[300px]">
          <AngleBarVisualizer
            recipe={formData}
            selectedStepIndex={selectedStepIndex}
            onSelectStep={(idx) => setSelectedStepIndex(idx)}
          />
        </div>

        {/* Bottom: Step Geometry Sequence Table */}
        <div className="h-64 border-t border-slate-800 bg-slate-900/90 flex flex-col">
          <div className="px-4 py-2 border-b border-slate-800 flex items-center justify-between">
            <div className="text-xs font-bold text-slate-300 font-mono flex items-center gap-2">
              <span>HOLES & OPERATION SEQUENCE</span>
              <span className="px-2 py-0.5 rounded bg-slate-800 text-cyan-400 text-[10px]">
                {formData.steps?.length || 0} OPERATIONS
              </span>
            </div>

            <div className="flex items-center gap-2">
              <button
                onClick={() => handleAddStep('PUNCH')}
                className="px-2.5 py-1 bg-cyan-700 hover:bg-cyan-600 text-white rounded text-xs font-mono font-bold flex items-center gap-1"
              >
                <CircleDot className="w-3.5 h-3.5" /> + PUNCH HOLE
              </button>

              <button
                onClick={() => handleAddStep('MARK')}
                className="px-2.5 py-1 bg-amber-700 hover:bg-amber-600 text-white rounded text-xs font-mono font-bold flex items-center gap-1"
              >
                <Stamp className="w-3.5 h-3.5" /> + MARKING
              </button>

              <button
                onClick={() => handleAddStep('CUT')}
                className="px-2.5 py-1 bg-rose-700 hover:bg-rose-600 text-white rounded text-xs font-mono font-bold flex items-center gap-1"
              >
                <Scissors className="w-3.5 h-3.5" /> + CUT-OFF
              </button>
            </div>
          </div>

          {/* Steps Table */}
          <div className="flex-1 overflow-y-auto">
            <table className="w-full text-left text-xs font-mono">
              <thead className="bg-slate-950/80 text-slate-400 sticky top-0 border-b border-slate-800">
                <tr>
                  <th className="p-2.5 pl-4">#</th>
                  <th className="p-2.5">Type</th>
                  <th className="p-2.5">Side</th>
                  <th className="p-2.5">X Position (mm)</th>
                  <th className="p-2.5">Y Gauge (mm)</th>
                  <th className="p-2.5">Tool Size</th>
                  <th className="p-2.5">Marking Text</th>
                  <th className="p-2.5 text-right pr-4">Action</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800/40">
                {(formData.steps || []).map((step, idx) => {
                  const isSelected = selectedStepIndex === idx;
                  return (
                    <tr
                      key={step.id || idx}
                      onClick={() => setSelectedStepIndex(idx)}
                      className={`cursor-pointer transition-colors ${
                        isSelected ? 'bg-cyan-950/50 text-cyan-200' : 'hover:bg-slate-800/40 text-slate-300'
                      }`}
                    >
                      <td className="p-2.5 pl-4 font-bold">{idx + 1}</td>
                      <td className="p-2.5">
                        <span
                          className={`px-2 py-0.5 rounded text-[10px] font-bold ${
                            step.operationType === 'PUNCH'
                              ? 'bg-cyan-950 text-cyan-400 border border-cyan-800'
                              : step.operationType === 'MARK'
                              ? 'bg-amber-950 text-amber-400 border border-amber-800'
                              : 'bg-rose-950 text-rose-400 border border-rose-800'
                          }`}
                        >
                          {step.operationType}
                        </span>
                      </td>
                      <td className="p-2.5">
                        {step.operationType === 'PUNCH' ? (
                          <select
                            value={step.side}
                            onChange={(e) => handleUpdateStep(idx, { side: e.target.value as SideType })}
                            className="bg-slate-950 border border-slate-700 rounded px-1.5 py-0.5 text-xs text-slate-200"
                          >
                            <option value="A">Side A</option>
                            <option value="B">Side B</option>
                          </select>
                        ) : (
                          <span className="text-slate-500">-</span>
                        )}
                      </td>
                      <td className="p-2.5">
                        <input
                          type="number"
                          value={step.xPosition}
                          onChange={(e) => handleUpdateStep(idx, { xPosition: Number(e.target.value) })}
                          className="w-20 bg-slate-950 border border-slate-700 rounded px-2 py-0.5 text-xs text-slate-100 font-bold"
                        />
                      </td>
                      <td className="p-2.5">
                        {step.operationType === 'PUNCH' ? (
                          <input
                            type="number"
                            value={step.yPosition}
                            onChange={(e) => handleUpdateStep(idx, { yPosition: Number(e.target.value) })}
                            className="w-16 bg-slate-950 border border-slate-700 rounded px-2 py-0.5 text-xs text-slate-100"
                          />
                        ) : (
                          <span className="text-slate-500">-</span>
                        )}
                      </td>
                      <td className="p-2.5">
                        {step.operationType === 'PUNCH' ? (
                          <select
                            value={step.toolSize || 18}
                            onChange={(e) => handleUpdateStep(idx, { toolSize: Number(e.target.value) })}
                            className="bg-slate-950 border border-slate-700 rounded px-1.5 py-0.5 text-xs text-slate-200"
                          >
                            <option value={14}>Ø 14mm</option>
                            <option value={18}>Ø 18mm</option>
                            <option value={22}>Ø 22mm</option>
                            <option value={26}>Ø 26mm</option>
                          </select>
                        ) : (
                          <span className="text-slate-500">-</span>
                        )}
                      </td>
                      <td className="p-2.5">
                        {step.operationType === 'MARK' ? (
                          <input
                            type="text"
                            value={step.markingText || ''}
                            onChange={(e) => handleUpdateStep(idx, { markingText: e.target.value })}
                            className="w-24 bg-slate-950 border border-slate-700 rounded px-2 py-0.5 text-xs text-slate-100 font-bold"
                          />
                        ) : (
                          <span className="text-slate-500">-</span>
                        )}
                      </td>
                      <td className="p-2.5 text-right pr-4">
                        <button
                          onClick={(e) => {
                            e.stopPropagation();
                            handleDeleteStep(idx);
                          }}
                          className="p-1 hover:bg-rose-950/60 rounded text-rose-400 hover:text-rose-300"
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
      </div>
    </div>
  );
};
