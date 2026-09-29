import React, { useEffect, useState } from 'react';
import {
  ItemRecipe,
  ItemRecipeStep,
  SideType,
  HoleSymbolType,
  STANDARD_HOLE_STYLES,
} from '@innovance-hmi/shared';
import { AngleBarViewer } from '../components/canvas/AngleBarViewer.js';
import { DataTable, Column } from '../components/common/DataTable.js';
import { DstvImporterModal } from '../components/importers/DstvImporterModal.js';
import { CadDrawingComparisonModal } from '../components/importers/CadDrawingComparisonModal.js';
import { VirtualKeypadModal } from '../components/common/VirtualKeypadModal.js';
import { TowerRuleCheckerModal } from '../components/common/TowerRuleCheckerModal.js';
import { JobCardModal } from '../components/common/JobCardModal.js';
import { HoleSymbolIcon, resolveHoleSymbol } from '../components/common/HoleSymbolIcon.js';
import {
  Plus,
  Trash2,
  Save,
  List,
  Edit,
  FileCode,
  Calculator,
  ShieldCheck,
  FileText,
  Sparkles,
  Link,
  Milestone,
} from 'lucide-react';
import { HmiAlert } from '../utils/alerts.js';

const STANDARD_SECTIONS = [
  { label: 'L50×50×5', a: 50, b: 50, t: 5 },
  { label: 'L65×65×6', a: 65, b: 65, t: 6 },
  { label: 'L75×75×6', a: 75, b: 75, t: 6 },
  { label: 'L90×90×8', a: 90, b: 90, t: 8 },
  { label: 'L100×100×10', a: 100, b: 100, t: 10 },
  { label: 'L130×130×12', a: 130, b: 130, t: 12 },
  { label: 'L150×150×16', a: 150, b: 150, t: 16 },
];

export const RecipeMasterView: React.FC = () => {
  const [recipes, setRecipes] = useState<ItemRecipe[]>([]);
  const [activeTab, setActiveTab] = useState<'LIST' | 'FORM'>('LIST');
  const [selectedRecipe, setSelectedRecipe] = useState<ItemRecipe | null>(null);
  const [highlightedStep, setHighlightedStep] = useState<number | undefined>(undefined);
  const [isSaving, setIsSaving] = useState(false);
  const [isDstvModalOpen, setIsDstvModalOpen] = useState(false);
  const [isCadDrawingModalOpen, setIsCadDrawingModalOpen] = useState(false);
  const [isRuleModalOpen, setIsRuleModalOpen] = useState(false);
  const [isJobCardModalOpen, setIsJobCardModalOpen] = useState(false);
  const [entryMode, setEntryMode] = useState<'ABSOLUTE' | 'INCREMENTAL'>('INCREMENTAL');

  // Virtual Keypad State
  const [keypadConfig, setKeypadConfig] = useState<{
    isOpen: boolean;
    title: string;
    value: number;
    onApply: (v: number) => void;
  }>({
    isOpen: false,
    title: '',
    value: 0,
    onApply: () => {},
  });

  const handleDeleteRecipe = async (id: string) => {
    const isConfirmed = await HmiAlert.confirm(
      'Delete Item Recipe?',
      'Are you sure you want to delete this recipe? This cannot be undone.'
    );
    if (!isConfirmed) return;

    try {
      const res = await fetch(`/api/recipes/${id}`, { method: 'DELETE' });
      const json = await res.json();
      if (json.success) {
        HmiAlert.success('Item Recipe deleted successfully!');
        fetchRecipes();
      }
    } catch (err) {
      console.error('Failed to delete recipe', err);
      HmiAlert.error('Failed to delete item recipe.');
    }
  };

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

  const handleCreateNew = () => {
    const fresh: ItemRecipe = {
      id: '',
      itemCode: `ANG-${Date.now().toString().slice(-4)}`,
      itemName: 'Transmission Angle Profile',
      totalLength: 1500.0,
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
          xPosition: 120.0,
          yPosition: 35.0,
          toolSize: 18.0,
          isCutOff: false,
        },
        {
          id: 'step-2',
          stepNumber: 2,
          operationType: 'PUNCH',
          side: 'B',
          xPosition: 250.0,
          yPosition: 35.0,
          toolSize: 18.0,
          isCutOff: false,
        },
        {
          id: 'step-3',
          stepNumber: 3,
          operationType: 'CUT',
          side: 'NA',
          xPosition: 1500.0,
          yPosition: 0,
          isCutOff: true,
        },
      ],
    };
    setSelectedRecipe(fresh);
    setActiveTab('FORM');
  };

  const handleEditRecipe = (r: ItemRecipe) => {
    setSelectedRecipe(JSON.parse(JSON.stringify(r)));
    setActiveTab('FORM');
  };

  const handleDstvImportComplete = (importedRecipe: ItemRecipe) => {
    setSelectedRecipe(importedRecipe);
    setActiveTab('FORM');
    HmiAlert.success('DSTV File Imported! Review and save the recipe.');
  };

  const handleCadDrawingImportComplete = (importedRecipe: ItemRecipe) => {
    setSelectedRecipe(importedRecipe);
    setActiveTab('FORM');
    fetchRecipes();
    HmiAlert.success('CAD / Drawing specifications extracted successfully! Review or save the recipe.');
  };

  const handleAddStep = () => {
    if (!selectedRecipe) return;
    const lastStep = selectedRecipe.steps[selectedRecipe.steps.length - 1];
    const defaultPitch = selectedRecipe.steps.length === 0 ? 25.0 : 30.0;
    const newX = lastStep ? Math.round((lastStep.xPosition + defaultPitch) * 10) / 10 : defaultPitch;
    const newStep: ItemRecipeStep = {
      id: `step-${Date.now()}`,
      stepNumber: selectedRecipe.steps.length + 1,
      operationType: 'PUNCH',
      side: lastStep ? lastStep.side : 'A',
      xPosition: newX,
      incrementalPitch: defaultPitch,
      yPosition: 56.0,
      toolSize: 17.5,
      holeSymbol: 'STANDARD_OPEN_17_5',
      isCutOff: false,
      remarks: 'Flange Punch',
    };
    setSelectedRecipe({
      ...selectedRecipe,
      steps: [...selectedRecipe.steps, newStep],
    });
  };

  const handleUpdatePitch = (index: number, newPitch: number) => {
    if (!selectedRecipe) return;
    const updated = [...selectedRecipe.steps];
    const prevX = index === 0 ? 0 : updated[index - 1].xPosition;
    const newX = Math.round((prevX + newPitch) * 10) / 10;
    const deltaShift = newX - updated[index].xPosition;

    updated[index] = {
      ...updated[index],
      incrementalPitch: newPitch,
      xPosition: newX,
    };

    // Propagate cumulative shift to subsequent steps in chain mode
    for (let k = index + 1; k < updated.length; k++) {
      updated[k] = {
        ...updated[k],
        xPosition: Math.round((updated[k].xPosition + deltaShift) * 10) / 10,
      };
    }
    setSelectedRecipe({ ...selectedRecipe, steps: updated });
  };

  const handleUpdateXPos = (index: number, newX: number) => {
    if (!selectedRecipe) return;
    const updated = [...selectedRecipe.steps];
    const prevX = index === 0 ? 0 : updated[index - 1].xPosition;
    updated[index] = {
      ...updated[index],
      xPosition: newX,
      incrementalPitch: Math.round((newX - prevX) * 10) / 10,
    };
    if (index + 1 < updated.length) {
      updated[index + 1] = {
        ...updated[index + 1],
        incrementalPitch: Math.round((updated[index + 1].xPosition - newX) * 10) / 10,
      };
    }
    setSelectedRecipe({ ...selectedRecipe, steps: updated });
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
        HmiAlert.success(isNew ? 'Item Recipe created successfully!' : 'Item Recipe updated successfully!');
        fetchRecipes();
        setActiveTab('LIST');
      }
    } catch (err) {
      console.error('Failed to save recipe', err);
      HmiAlert.error('Failed to save item recipe.');
    } finally {
      setIsSaving(false);
    }
  };

  const openKeypad = (title: string, currentVal: number, onApply: (v: number) => void) => {
    setKeypadConfig({
      isOpen: true,
      title,
      value: currentVal,
      onApply,
    });
  };

  const columns: Column<ItemRecipe>[] = [
    {
      key: 'itemCode',
      header: 'Item Code',
      render: (r) => <span className="font-bold text-cyan-400 tracking-wide">{r.itemCode}</span>,
    },
    { key: 'itemName', header: 'Material / Description' },
    {
      key: 'angleWidthA',
      header: 'Flange A',
      render: (r) => `${r.angleWidthA} mm`,
    },
    {
      key: 'angleWidthB',
      header: 'Flange B',
      render: (r) => `${r.angleWidthB} mm`,
    },
    {
      key: 'thickness',
      header: 'Thickness',
      render: (r) => `${r.thickness} mm`,
    },
    {
      key: 'totalLength',
      header: 'Program Length',
      render: (r) => <span className="font-bold text-white font-mono">{r.totalLength} mm</span>,
    },
    {
      key: 'steps',
      header: 'Steps Count',
      render: (r) => (
        <span className="badge bg-slate-800/80 border border-slate-700 text-slate-200 px-2 py-0.5 rounded text-xs font-semibold">
          {r.steps?.length || 0} Steps
        </span>
      ),
    },
    {
      key: 'isActive',
      header: 'Status',
      render: (r) => (
        <span
          className={`px-2 py-0.5 rounded text-[11px] font-bold ${
            r.isActive
              ? 'bg-emerald-950/80 text-emerald-300 border border-emerald-600/60'
              : 'bg-rose-950/80 text-rose-300 border border-rose-600/60'
          }`}
        >
          {r.isActive ? 'Active' : 'In Active'}
        </span>
      ),
    },
    {
      key: 'actions',
      header: 'Actions',
      align: 'right',
      sortable: false,
      render: (r) => (
        <div className="flex items-center justify-end gap-1">
          <button
            onClick={(e) => {
              e.stopPropagation();
              handleEditRecipe(r);
            }}
            className="btn-ca btn-ca-primary text-xs py-1 px-2.5"
          >
            <Edit className="w-3.5 h-3.5" /> Edit
          </button>
          <button
            onClick={(e) => {
              e.stopPropagation();
              handleDeleteRecipe(r.id);
            }}
            className="btn-ca btn-ca-danger text-xs py-1 px-2.5"
          >
            <Trash2 className="w-3.5 h-3.5" /> Delete
          </button>
        </div>
      ),
    },
  ];

  return (
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* Top Header Command Strip */}
      <div className="flex flex-wrap items-center justify-between pb-3 border-b border-slate-800 gap-3">
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-lg bg-[#141e2e] border border-[#2b3d56] flex items-center justify-center text-sky-400 shrink-0 shadow-md">
            <FileCode className="w-5 h-5" />
          </div>
          <div>
            <div className="flex items-center gap-2.5">
              <h2 className="text-lg font-black text-white tracking-wide">Item Recipe Master</h2>
              <span className="text-[10px] font-mono font-bold bg-sky-950/80 text-sky-300 border border-sky-800/80 px-2 py-0.5 rounded-full">
                IS 802 STANDARD • CNC ANGLE LINE
              </span>
            </div>
            <p className="text-xs text-slate-400 font-medium mt-0.5">
              Define part geometry, punch hole coordinates, import Tekla DSTV / CAD drawings, and verify structural clearance rules.
            </p>
          </div>
        </div>

        <div className="flex flex-wrap items-center gap-2">
          {/* CAD & Drawing Importer Button */}
          <button
            onClick={() => setIsCadDrawingModalOpen(true)}
            className="cnc-btn bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white text-xs border border-sky-400/40 shadow-md flex items-center gap-2 font-bold"
            title="Upload PDF fabrication drawings or AutoCAD DXF/DWG files with side-by-side comparison"
          >
            <Sparkles className="w-4 h-4 text-cyan-200" />
            <span>Upload Drawing / CAD (PDF • DXF)</span>
          </button>

          {/* DSTV / NC1 Import Button */}
          <button
            onClick={() => setIsDstvModalOpen(true)}
            className="cnc-btn cnc-btn-success text-xs"
          >
            <FileCode className="w-4 h-4" /> Import DSTV (.nc1)
          </button>

          {activeTab === 'LIST' ? (
            <button onClick={handleCreateNew} className="cnc-btn cnc-btn-primary text-xs">
              <Plus className="w-4 h-4" /> Add Item Recipe
            </button>
          ) : (
            <button onClick={() => setActiveTab('LIST')} className="cnc-btn cnc-btn-secondary text-xs">
              <List className="w-4 h-4" /> View All Recipes
            </button>
          )}
        </div>
      </div>

      {/* TAB: LIST */}
      {activeTab === 'LIST' && (
        <DataTable
          title="Manage Item Recipe Master DataTable"
          columns={columns}
          data={recipes}
          searchKeys={['itemCode', 'itemName']}
          onRowClick={handleEditRecipe}
        />
      )}

      {/* TAB: FORM */}
      {activeTab === 'FORM' && selectedRecipe && (
        <div className="space-y-4">
          <div className="cnc-card">
            {/* Header Ribbon */}
            <div className="cnc-card-header">
              <div className="flex items-center gap-3">
                <span className="font-black text-sm text-white">
                  {selectedRecipe.id ? 'Edit Item Recipe' : 'Add Item Recipe Master'}
                </span>
                <span className="cnc-pill cnc-pill-cyan font-mono text-xs">
                  {selectedRecipe.itemCode || 'NEW'}
                </span>
                <span className="text-xs text-slate-400 font-mono hidden sm:inline">
                  (L{selectedRecipe.angleWidthA}×{selectedRecipe.angleWidthB}×{selectedRecipe.thickness} • {selectedRecipe.totalLength}mm)
                </span>
              </div>

              <div className="flex flex-wrap items-center gap-2">
                {/* Upload Drawing Trigger in Form */}
                <button
                  type="button"
                  onClick={() => setIsCadDrawingModalOpen(true)}
                  className="cnc-btn bg-sky-950/80 hover:bg-sky-900 border border-sky-600/70 text-sky-200 text-xs py-1.5 px-3 min-h-[40px] flex items-center gap-1.5 font-bold"
                  title="Upload engineering drawing PDF or AutoCAD file to auto-populate specifications"
                >
                  <Sparkles className="w-3.5 h-3.5 text-cyan-400" /> Upload Drawing (PDF/CAD)
                </button>

                {/* IS 802 Rule Checker Trigger */}
                <button
                  type="button"
                  onClick={() => setIsRuleModalOpen(true)}
                  className="cnc-btn cnc-btn-amber text-xs py-1.5 px-3 min-h-[40px]"
                >
                  <ShieldCheck className="w-3.5 h-3.5" /> Check IS 802 Rules
                </button>

                {/* Job Card Sheet Trigger */}
                <button
                  type="button"
                  onClick={() => setIsJobCardModalOpen(true)}
                  className="cnc-btn cnc-btn-secondary text-xs py-1.5 px-3 min-h-[40px]"
                >
                  <FileText className="w-3.5 h-3.5" /> Print Job Sheet
                </button>

                <button
                  onClick={handleSaveRecipe}
                  disabled={isSaving}
                  className="cnc-btn cnc-btn-success text-xs py-1.5 px-4 min-h-[40px]"
                >
                  <Save className="w-3.5 h-3.5" /> {isSaving ? 'Saving...' : 'Save Recipe'}
                </button>
              </div>
            </div>

            <div className="cnc-card-body space-y-4">
              {/* Structural Angle Profile Quick Preset Selector Bar */}
              <div className="bg-[#0b1019] p-3 rounded-lg border border-[#1e2a3c]">
                <div className="flex items-center justify-between mb-2">
                  <span className="text-[11px] font-black text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span className="w-2 h-2 rounded-full bg-sky-400" /> Quick Structural Angle Presets (IS 808 / IS 2062)
                  </span>
                  <span className="text-[11px] text-slate-500 font-medium">Click to auto-fill flange widths & thickness</span>
                </div>
                <div className="flex flex-wrap items-center gap-2">
                  {STANDARD_SECTIONS.map((sec) => {
                    const isSelected =
                      selectedRecipe.angleWidthA === sec.a &&
                      selectedRecipe.angleWidthB === sec.b &&
                      selectedRecipe.thickness === sec.t;
                    return (
                      <button
                        key={sec.label}
                        type="button"
                        onClick={() => {
                          setSelectedRecipe({
                            ...selectedRecipe,
                            angleWidthA: sec.a,
                            angleWidthB: sec.b,
                            thickness: sec.t,
                            itemName: selectedRecipe.itemName.includes('Profile')
                              ? `IS 2062 Angle Profile (${sec.label})`
                              : selectedRecipe.itemName,
                          });
                        }}
                        className={`px-3 py-1.5 rounded-lg text-xs font-mono font-bold transition-all border ${
                          isSelected
                            ? 'bg-sky-600 border-sky-400 text-white shadow-sm'
                            : 'bg-[#141d2a] border-[#243347] text-slate-300 hover:bg-[#1a2638] hover:text-white'
                        }`}
                      >
                        {sec.label}
                      </button>
                    );
                  })}
                </div>
              </div>

              {/* Form Input Row */}
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <div>
                  <label className="font-bold text-slate-300 flex items-center justify-between">
                    <span>Item / Drawing Mark <span className="text-red-400">*</span></span>
                    <span className="text-[10px] text-cyan-400 font-mono font-bold">MARK NO</span>
                  </label>
                  <input
                    type="text"
                    value={selectedRecipe.itemCode}
                    onChange={(e) => setSelectedRecipe({ ...selectedRecipe, itemCode: e.target.value })}
                    className="cnc-input mt-1.5 font-bold text-cyan-300 font-mono"
                    placeholder="e.g. NBS-601"
                  />
                </div>

                <div>
                  <label className="font-bold text-slate-300 flex items-center justify-between">
                    <span>Flange A Width <span className="text-red-400">*</span></span>
                    <span className="text-[10px] text-cyan-400 font-mono font-bold">TOP (DA1-3)</span>
                  </label>
                  <div className="flex items-center gap-1.5 mt-1.5">
                    <input
                      type="number"
                      value={selectedRecipe.angleWidthA}
                      onChange={(e) => setSelectedRecipe({ ...selectedRecipe, angleWidthA: parseFloat(e.target.value) || 0 })}
                      className="cnc-input font-bold"
                    />
                    <button
                      type="button"
                      onClick={() => openKeypad('Side A Width', selectedRecipe.angleWidthA, (v) => setSelectedRecipe({ ...selectedRecipe, angleWidthA: v }))}
                      className="cnc-btn cnc-btn-secondary h-12 w-12 p-0 shrink-0"
                      title="Open Numeric Keypad"
                    >
                      <Calculator className="w-4 h-4 text-cyan-400" />
                    </button>
                  </div>
                </div>

                <div>
                  <label className="font-bold text-slate-300 flex items-center justify-between">
                    <span>Flange B Width <span className="text-red-400">*</span></span>
                    <span className="text-[10px] text-emerald-400 font-mono font-bold">SIDE (DB1-3)</span>
                  </label>
                  <div className="flex items-center gap-1.5 mt-1.5">
                    <input
                      type="number"
                      value={selectedRecipe.angleWidthB}
                      onChange={(e) => setSelectedRecipe({ ...selectedRecipe, angleWidthB: parseFloat(e.target.value) || 0 })}
                      className="cnc-input font-bold"
                    />
                    <button
                      type="button"
                      onClick={() => openKeypad('Side B Width', selectedRecipe.angleWidthB, (v) => setSelectedRecipe({ ...selectedRecipe, angleWidthB: v }))}
                      className="cnc-btn cnc-btn-secondary h-12 w-12 p-0 shrink-0"
                      title="Open Numeric Keypad"
                    >
                      <Calculator className="w-4 h-4 text-emerald-400" />
                    </button>
                  </div>
                </div>

                <div>
                  <label className="font-bold text-slate-300 flex items-center justify-between">
                    <span>Thickness (t) <span className="text-red-400">*</span></span>
                    <span className="text-[10px] text-slate-400 font-mono font-bold">MM</span>
                  </label>
                  <input
                    type="number"
                    value={selectedRecipe.thickness}
                    onChange={(e) => setSelectedRecipe({ ...selectedRecipe, thickness: parseFloat(e.target.value) || 0 })}
                    className="cnc-input mt-1.5 font-bold"
                    placeholder="Enter Thickness (e.g. 6.0)"
                  />
                </div>

                <div>
                  <label className="font-bold text-slate-300 flex items-center justify-between">
                    <span>Total Cut Length <span className="text-red-400">*</span></span>
                    <span className="text-[10px] text-amber-400 font-mono font-bold">CUT MM</span>
                  </label>
                  <div className="flex items-center gap-1.5 mt-1.5">
                    <input
                      type="number"
                      value={selectedRecipe.totalLength}
                      onChange={(e) => setSelectedRecipe({ ...selectedRecipe, totalLength: parseFloat(e.target.value) || 0 })}
                      className="cnc-input font-bold text-amber-300 font-mono"
                    />
                    <button
                      type="button"
                      onClick={() => openKeypad('Program Length', selectedRecipe.totalLength, (v) => setSelectedRecipe({ ...selectedRecipe, totalLength: v }))}
                      className="cnc-btn cnc-btn-secondary h-12 w-12 p-0 shrink-0"
                      title="Open Numeric Keypad"
                    >
                      <Calculator className="w-4 h-4 text-amber-400" />
                    </button>
                  </div>
                </div>

                <div className="lg:col-span-2">
                  <label className="font-bold text-slate-300">Material Description / Steel Grade</label>
                  <input
                    type="text"
                    value={selectedRecipe.itemName}
                    onChange={(e) => setSelectedRecipe({ ...selectedRecipe, itemName: e.target.value })}
                    className="cnc-input mt-1.5 font-medium"
                    placeholder="e.g. IS 2062 E250 / Tower Leg Diagonal Brace"
                  />
                </div>

                <div>
                  <label className="font-bold text-slate-300">Status</label>
                  <select
                    value={selectedRecipe.isActive ? '1' : '0'}
                    onChange={(e) => setSelectedRecipe({ ...selectedRecipe, isActive: e.target.value === '1' })}
                    className="cnc-input mt-1.5 font-bold"
                  >
                    <option value="1">Active In Production</option>
                    <option value="0">Archived / Standby</option>
                  </select>
                </div>
              </div>

              {/* Unified 2D/3D CAD Visualizer Blueprint */}
              <div className="pt-4 border-t border-slate-800">
                <div className="flex items-center justify-between mb-2">
                  <label className="font-black text-xs text-slate-200 tracking-wide uppercase flex items-center gap-2">
                    <span className="w-2 h-2 rounded bg-sky-400" /> Interactive Blueprint Inspection (2D & 3D Synced)
                  </label>
                  <span className="text-xs text-slate-400 font-medium">Click on flange surface to add a hole at that position</span>
                </div>
                <div className="h-[520px] rounded-lg overflow-hidden border border-slate-700 shadow-xl">
                  <AngleBarViewer
                    recipe={selectedRecipe}
                    highlightStepIndex={highlightedStep}
                    onSelectStep={(idx) => {
                      setHighlightedStep(idx);
                    }}
                    onCanvasClick={(x, y, side) => {
                      const newStep: ItemRecipeStep = {
                        id: `step-${Date.now()}`,
                        stepNumber: selectedRecipe.steps.length + 1,
                        operationType: 'PUNCH',
                        side: side,
                        xPosition: Math.round(x * 10) / 10,
                        yPosition: Math.round(y * 10) / 10,
                        toolSize: 18.0,
                        isCutOff: false,
                      };
                      setSelectedRecipe({
                        ...selectedRecipe,
                        steps: [...selectedRecipe.steps, newStep],
                      });
                    }}
                    onStepDrag={(idx, x, y, side) => {
                      const updated = [...selectedRecipe.steps];
                      updated[idx] = {
                        ...updated[idx],
                        xPosition: Math.round(x * 10) / 10,
                        yPosition: Math.round(y * 10) / 10,
                        side: side,
                      };
                      setSelectedRecipe({ ...selectedRecipe, steps: updated });
                    }}
                  />
                </div>
              </div>

              {/* itemRecipeSteps Table Section */}
              <div className="pt-4 border-t border-slate-800">
                <div className="flex flex-wrap items-center justify-between gap-3 mb-3">
                  <div className="flex flex-wrap items-center gap-3">
                    <div className="flex items-center gap-2">
                      <span className="font-black text-sm text-white uppercase tracking-wide">
                        Operations & Tooling Schedule
                      </span>
                      <span className="cnc-pill cnc-pill-cyan font-mono text-xs">
                        {selectedRecipe.steps.length} STEPS
                      </span>
                    </div>

                    {/* Touch-Friendly Pitch Entry Mode Switcher */}
                    <div className="flex items-center bg-[#070b12] border border-[#1f2d42] p-1 rounded-xl text-xs gap-1 shadow-inner">
                      <button
                        type="button"
                        onClick={() => {
                          setEntryMode('INCREMENTAL');
                          setSelectedRecipe({ ...selectedRecipe, measurementType: 'INCREMENTAL' });
                        }}
                        className={`px-3 py-1.5 rounded-lg font-bold flex items-center gap-2 transition-all min-h-[36px] ${
                          entryMode === 'INCREMENTAL'
                            ? 'bg-amber-600 text-white shadow-md border border-amber-400/50'
                            : 'text-slate-400 hover:text-white hover:bg-slate-800/60'
                        }`}
                        title="Enter incremental pitch chains (+ΔX) directly from drawing callouts"
                      >
                        <Link className="w-3.5 h-3.5 text-amber-200" />
                        <span>Incremental (+ΔX Pitch Chain)</span>
                      </button>
                      <button
                        type="button"
                        onClick={() => {
                          setEntryMode('ABSOLUTE');
                          setSelectedRecipe({ ...selectedRecipe, measurementType: 'ABSOLUTE' });
                        }}
                        className={`px-3 py-1.5 rounded-lg font-bold flex items-center gap-2 transition-all min-h-[36px] ${
                          entryMode === 'ABSOLUTE'
                            ? 'bg-sky-600 text-white shadow-md border border-sky-400/50'
                            : 'text-slate-400 hover:text-white hover:bg-slate-800/60'
                        }`}
                        title="Enter absolute coordinates from Part Zero Datum"
                      >
                        <Milestone className="w-3.5 h-3.5 text-sky-200" />
                        <span>Absolute (0mm Datum)</span>
                      </button>
                    </div>
                  </div>

                  <div className="flex items-center gap-2">
                    <button
                      onClick={() => {
                        if (confirm("Are you sure you want to clear all steps?")) {
                          setSelectedRecipe({ ...selectedRecipe, steps: [] });
                        }
                      }}
                      type="button"
                      className="cnc-btn cnc-btn-secondary text-rose-300 hover:bg-rose-950/60 hover:text-rose-200 text-xs py-1.5 px-3 min-h-[40px]"
                    >
                      <Trash2 className="w-3.5 h-3.5" /> Clear All
                    </button>
                    <button
                      onClick={handleAddStep}
                      type="button"
                      className="cnc-btn cnc-btn-primary text-xs py-1.5 px-4 min-h-[40px]"
                    >
                      <Plus className="w-4 h-4" /> Add Step Operation
                    </button>
                  </div>
                </div>

                <div className="overflow-x-auto rounded-lg border border-slate-700/80 shadow-md">
                  <table className="table-custom border-collapse w-full">
                    <thead>
                      <tr className="bg-[#0f1726] border-b-2 border-slate-700 text-slate-300 text-xs">
                        <th style={{ width: '60px' }} className="py-3 px-3 text-center">Step</th>
                        <th style={{ width: '100px' }} className="py-3 px-3">Operation <span className="text-red-400">*</span></th>
                        <th style={{ width: '110px' }} className="py-3 px-3">Flange</th>
                        <th style={{ minWidth: '190px' }} className="py-3 px-3">CAD Symbol & Type</th>
                        <th className={`py-3 px-3 ${entryMode === 'INCREMENTAL' ? 'bg-amber-950/40 text-amber-300 font-bold border-x border-amber-600/40' : ''}`} style={{ width: '140px' }}>
                          Pitch +ΔX (mm)
                        </th>
                        <th className={`py-3 px-3 ${entryMode === 'ABSOLUTE' ? 'bg-sky-950/40 text-sky-300 font-bold border-x border-sky-600/40' : ''}`} style={{ width: '140px' }}>
                          Absolute X (mm)
                        </th>
                        <th style={{ width: '115px' }} className="py-3 px-3">Gauge Y (mm)</th>
                        <th style={{ width: '85px' }} className="py-3 px-3 text-center">Tool Ø</th>
                        <th className="py-3 px-3">Remarks / Purpose</th>
                        <th style={{ textAlign: 'right', width: '60px' }} className="py-3 px-3">Action</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-800">
                      {selectedRecipe.steps.map((step, idx) => {
                        const calculatedPitch = step.incrementalPitch !== undefined
                          ? step.incrementalPitch
                          : idx === 0
                          ? step.xPosition
                          : Math.round((step.xPosition - selectedRecipe.steps[idx - 1].xPosition) * 10) / 10;

                        return (
                          <tr
                            key={step.id || idx}
                            className={`transition-colors ${
                              highlightedStep === idx
                                ? 'bg-sky-950/50 text-white'
                                : 'hover:bg-slate-800/40'
                            }`}
                            onMouseEnter={() => setHighlightedStep(idx)}
                            onMouseLeave={() => setHighlightedStep(undefined)}
                          >
                            <td className="font-bold text-cyan-400 font-mono text-center py-2 px-3">
                              #{step.stepNumber}
                            </td>
                            <td className="py-2 px-3">
                              <select
                                value={step.operationType}
                                onChange={(e) => handleUpdateStep(idx, 'operationType', e.target.value)}
                                className="cnc-input text-xs py-1.5 px-2 min-h-[36px] font-bold"
                              >
                                <option value="PUNCH">PUNCH</option>
                                <option value="MARK">MARKING</option>
                                <option value="CUT">CUTTING</option>
                              </select>
                            </td>
                            <td className="py-2 px-3">
                              <div className="flex items-center gap-1.5">
                                <span
                                  className={`w-2.5 h-2.5 rounded-full shrink-0 ${
                                    step.side === 'A'
                                      ? 'bg-cyan-400 shadow-[0_0_6px_#00e5ff]'
                                      : step.side === 'B'
                                      ? 'bg-emerald-400 shadow-[0_0_6px_#10b981]'
                                      : 'bg-slate-500'
                                  }`}
                                />
                                <select
                                  value={step.side}
                                  onChange={(e) => handleUpdateStep(idx, 'side', e.target.value as SideType)}
                                  className={`cnc-input text-xs py-1.5 px-2 min-h-[36px] font-bold ${
                                    step.side === 'A'
                                      ? 'text-cyan-300 font-extrabold'
                                      : step.side === 'B'
                                      ? 'text-emerald-300 font-extrabold'
                                      : 'text-slate-300'
                                  }`}
                                >
                                  <option value="A">Flange A (Top)</option>
                                  <option value="B">Flange B (Side)</option>
                                  <option value="NA">Both / Cut</option>
                                </select>
                              </div>
                            </td>

                            {/* Hole Symbol & Style Selector */}
                            <td className="py-2 px-3">
                              {step.operationType === 'PUNCH' ? (
                                <div className="flex items-center gap-2">
                                  <div
                                    className="w-8 h-8 rounded-lg bg-[#070b12] border border-[#202e42] flex items-center justify-center shrink-0 shadow-sm"
                                    title="CAD Drawing Glyph (IS 802 Standard)"
                                  >
                                    <HoleSymbolIcon
                                      symbolType={step.holeSymbol}
                                      toolSize={step.toolSize}
                                      remarks={step.remarks}
                                      size={22}
                                      color={step.side === 'A' ? '#00e5ff' : '#10b981'}
                                    />
                                  </div>
                                  <select
                                    value={step.holeSymbol || resolveHoleSymbol(step.holeSymbol, step.toolSize, step.remarks)}
                                    onChange={(e) => {
                                      const sym = e.target.value as HoleSymbolType;
                                      const matched = STANDARD_HOLE_STYLES.find((s) => s.symbolType === sym);
                                      const updated = [...selectedRecipe.steps];
                                      updated[idx] = {
                                        ...updated[idx],
                                        holeSymbol: sym,
                                        toolSize: matched ? matched.diameter : updated[idx].toolSize,
                                        remarks: matched?.isStepHole ? 'Step Bolt Hole' : updated[idx].remarks,
                                      };
                                      setSelectedRecipe({ ...selectedRecipe, steps: updated });
                                    }}
                                    className="cnc-input text-xs py-1.5 px-2 min-h-[36px] font-mono flex-1 text-slate-200"
                                  >
                                    {STANDARD_HOLE_STYLES.map((st) => (
                                      <option key={st.symbolType} value={st.symbolType}>
                                        {st.label} ({st.boltStandard})
                                      </option>
                                    ))}
                                  </select>
                                </div>
                              ) : step.operationType === 'MARK' ? (
                                <span className="cnc-pill cnc-pill-amber font-mono text-xs">
                                  STAMP CASSETTE
                                </span>
                              ) : (
                                <span className="cnc-pill cnc-pill-red font-mono text-xs">
                                  SHEAR BLADE
                                </span>
                              )}
                            </td>

                            {/* Incremental Pitch Spacing (+ΔX) */}
                            <td className={`py-2 px-3 ${entryMode === 'INCREMENTAL' ? 'bg-amber-950/25 border-x border-amber-500/20' : ''}`}>
                              <div className="flex items-center gap-1.5">
                                <span className="text-amber-400 font-mono text-xs font-bold">+</span>
                                <input
                                  type="number"
                                  value={calculatedPitch}
                                  onChange={(e) => handleUpdatePitch(idx, parseFloat(e.target.value) || 0)}
                                  className={`cnc-input text-xs w-20 py-1.5 px-2 min-h-[36px] font-mono font-bold ${
                                    entryMode === 'INCREMENTAL' ? 'border-amber-500 text-amber-200 bg-amber-950/40' : 'text-slate-300'
                                  }`}
                                  placeholder="+ΔX"
                                />
                                <button
                                  type="button"
                                  onClick={() => openKeypad(`Step #${step.stepNumber} Pitch +ΔX`, calculatedPitch, (v) => handleUpdatePitch(idx, v))}
                                  className="cnc-btn cnc-btn-secondary h-9 w-9 p-0 shrink-0"
                                  title="Enter pitch via keypad"
                                >
                                  <Calculator className="w-3.5 h-3.5 text-amber-400" />
                                </button>
                              </div>
                            </td>

                            {/* Cumulative Absolute X Position */}
                            <td className={`py-2 px-3 ${entryMode === 'ABSOLUTE' ? 'bg-sky-950/25 border-x border-sky-500/20' : ''}`}>
                              <div className="flex items-center gap-1.5">
                                <input
                                  type="number"
                                  value={step.xPosition}
                                  onChange={(e) => handleUpdateXPos(idx, parseFloat(e.target.value) || 0)}
                                  className={`cnc-input text-xs w-22 py-1.5 px-2 min-h-[36px] font-mono font-bold ${
                                    entryMode === 'ABSOLUTE' ? 'border-sky-500 text-sky-200 bg-sky-950/40' : 'text-slate-300'
                                  }`}
                                />
                                <button
                                  type="button"
                                  onClick={() => openKeypad(`Step #${step.stepNumber} Absolute X`, step.xPosition, (v) => handleUpdateXPos(idx, v))}
                                  className="cnc-btn cnc-btn-secondary h-9 w-9 p-0 shrink-0"
                                  title="Enter absolute X via keypad"
                                >
                                  <Calculator className="w-3.5 h-3.5 text-sky-400" />
                                </button>
                              </div>
                            </td>

                            {/* Gauge Y Position */}
                            <td className="py-2 px-3">
                              <div className="flex items-center gap-1.5">
                                <input
                                  type="number"
                                  value={step.yPosition}
                                  onChange={(e) => handleUpdateStep(idx, 'yPosition', parseFloat(e.target.value) || 0)}
                                  className="cnc-input text-xs w-16 py-1.5 px-2 min-h-[36px] font-mono"
                                />
                                <button
                                  type="button"
                                  onClick={() => openKeypad(`Step #${step.stepNumber} Gauge Y`, step.yPosition, (v) => handleUpdateStep(idx, 'yPosition', v))}
                                  className="cnc-btn cnc-btn-secondary h-9 w-9 p-0 shrink-0"
                                >
                                  <Calculator className="w-3.5 h-3.5 text-slate-300" />
                                </button>
                              </div>
                            </td>

                            {/* Tool Size mm */}
                            <td className="py-2 px-3 text-center">
                              <input
                                type="number"
                                step="0.5"
                                value={step.toolSize || 17.5}
                                onChange={(e) => handleUpdateStep(idx, 'toolSize', parseFloat(e.target.value) || 17.5)}
                                className="cnc-input text-xs w-16 py-1.5 px-2 min-h-[36px] font-mono text-center font-bold"
                              />
                            </td>

                            {/* Remarks */}
                            <td className="py-2 px-3">
                              <input
                                type="text"
                                value={step.remarks || ''}
                                onChange={(e) => handleUpdateStep(idx, 'remarks', e.target.value)}
                                className="cnc-input text-xs py-1.5 px-2.5 min-h-[36px] text-slate-300"
                                placeholder={step.operationType === 'MARK' ? 'Stamping text' : 'Optional remarks (e.g. ACD)'}
                              />
                            </td>

                            <td style={{ textAlign: 'right' }} className="py-2 px-3">
                              <button
                                type="button"
                                onClick={() => handleDeleteStep(idx)}
                                className="cnc-btn cnc-btn-secondary text-rose-400 hover:bg-rose-950/70 hover:text-rose-200 h-9 w-9 p-0"
                                title="Delete this operation"
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
        </div>
      )}

      {/* DSTV Importer Modal */}
      <DstvImporterModal
        isOpen={isDstvModalOpen}
        onClose={() => setIsDstvModalOpen(false)}
        onImportComplete={handleDstvImportComplete}
      />

      {/* CAD Drawing Comparison & Extractor Modal */}
      <CadDrawingComparisonModal
        isOpen={isCadDrawingModalOpen}
        onClose={() => setIsCadDrawingModalOpen(false)}
        onImportComplete={handleCadDrawingImportComplete}
      />

      {/* Touchscreen Virtual Keypad Modal */}
      <VirtualKeypadModal
        isOpen={keypadConfig.isOpen}
        title={keypadConfig.title}
        initialValue={keypadConfig.value}
        onClose={() => setKeypadConfig((prev) => ({ ...prev, isOpen: false }))}
        onSubmit={keypadConfig.onApply}
      />

      {/* IS 802 Tower Design Rule Checker Modal */}
      {selectedRecipe && (
        <TowerRuleCheckerModal
          isOpen={isRuleModalOpen}
          recipe={selectedRecipe}
          onClose={() => setIsRuleModalOpen(false)}
        />
      )}

      {/* Printable Job Card Modal */}
      {selectedRecipe && (
        <JobCardModal
          isOpen={isJobCardModalOpen}
          recipe={selectedRecipe}
          onClose={() => setIsJobCardModalOpen(false)}
        />
      )}
    </div>
  );
};
