import React, { useEffect, useState } from 'react';
import { ItemRecipe, ItemRecipeStep, SideType } from '@innovance-hmi/shared';
import { AngleBarViewer } from '../components/canvas/AngleBarViewer.js';
import { DataTable, Column } from '../components/common/DataTable.js';
import { DstvImporterModal } from '../components/importers/DstvImporterModal.js';
import { VirtualKeypadModal } from '../components/common/VirtualKeypadModal.js';
import { TowerRuleCheckerModal } from '../components/common/TowerRuleCheckerModal.js';
import { JobCardModal } from '../components/common/JobCardModal.js';
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
} from 'lucide-react';
import { HmiAlert } from '../utils/alerts.js';

export const RecipeMasterView: React.FC = () => {
  const [recipes, setRecipes] = useState<ItemRecipe[]>([]);
  const [activeTab, setActiveTab] = useState<'LIST' | 'FORM'>('LIST');
  const [selectedRecipe, setSelectedRecipe] = useState<ItemRecipe | null>(null);
  const [highlightedStep, setHighlightedStep] = useState<number | undefined>(undefined);
  const [isSaving, setIsSaving] = useState(false);
  const [isDstvModalOpen, setIsDstvModalOpen] = useState(false);
  const [isRuleModalOpen, setIsRuleModalOpen] = useState(false);
  const [isJobCardModalOpen, setIsJobCardModalOpen] = useState(false);

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
      render: (r) => <span className="font-bold text-blue-700">{r.itemCode}</span>,
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
      render: (r) => <span className="font-bold text-slate-800">{r.totalLength} mm</span>,
    },
    {
      key: 'steps',
      header: 'Steps Count',
      render: (r) => (
        <span className="badge bg-slate-100 border text-slate-700 px-2 py-0.5 rounded text-xs font-semibold">
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
            r.isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
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
      {/* Top Header */}
      <div className="flex items-center justify-between pb-2 border-b border-slate-300">
        <div>
          <h2 className="text-lg font-black text-slate-900">Item Recipe Master (HPT Standard)</h2>
          <p className="text-xs text-slate-600 font-medium mt-0.5">
            Define part geometry, punch hole coordinates, import Tekla DSTV files, verify IS 802 tower design rules, and generate job cards.
          </p>
        </div>

        <div className="flex items-center gap-2">
          {/* DSTV / NC1 Import Button */}
          <button
            onClick={() => setIsDstvModalOpen(true)}
            className="btn-ca btn-ca-success text-xs py-1.5"
          >
            <FileCode className="w-4 h-4" /> Import DSTV (.nc1)
          </button>

          {activeTab === 'LIST' ? (
            <button onClick={handleCreateNew} className="btn-ca btn-ca-primary text-xs py-1.5">
              <Plus className="w-4 h-4" /> Add Item Recipe
            </button>
          ) : (
            <button onClick={() => setActiveTab('LIST')} className="btn-ca btn-ca-default text-xs py-1.5">
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
          <div className="panel">
            <div className="panel-heading">
              <span>{selectedRecipe.id ? 'Edit Item Recipe' : 'Add Item Recipe Master'}</span>
              <div className="flex items-center gap-2">
                {/* IS 802 Rule Checker Trigger */}
                <button
                  type="button"
                  onClick={() => setIsRuleModalOpen(true)}
                  className="btn-ca btn-ca-warning text-xs py-1 px-2.5"
                >
                  <ShieldCheck className="w-3.5 h-3.5" /> Check IS 802 Rules
                </button>

                {/* Job Card Sheet Trigger */}
                <button
                  type="button"
                  onClick={() => setIsJobCardModalOpen(true)}
                  className="btn-ca btn-ca-dark text-xs py-1 px-2.5"
                >
                  <FileText className="w-3.5 h-3.5" /> Print Job Sheet
                </button>

                <button
                  onClick={handleSaveRecipe}
                  disabled={isSaving}
                  className="btn-ca btn-ca-success text-xs py-1 px-3"
                >
                  <Save className="w-3.5 h-3.5" /> {isSaving ? 'Saving...' : 'Save Recipe'}
                </button>
              </div>
            </div>

            <div className="panel-body space-y-4">
              {/* Form Input Row */}
              <div className="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
                <div>
                  <label className="font-bold text-slate-700">Item Code <span className="text-red-500">*</span></label>
                  <input
                    type="text"
                    value={selectedRecipe.itemCode}
                    onChange={(e) => setSelectedRecipe({ ...selectedRecipe, itemCode: e.target.value })}
                    className="form-control-ca mt-1 font-bold text-blue-800"
                    placeholder="Enter Item Code"
                  />
                </div>

                <div>
                  <label className="font-bold text-slate-700">Side A Width (mm) <span className="text-red-500">*</span></label>
                  <div className="flex items-center gap-1 mt-1">
                    <input
                      type="number"
                      value={selectedRecipe.angleWidthA}
                      onChange={(e) => setSelectedRecipe({ ...selectedRecipe, angleWidthA: parseFloat(e.target.value) || 0 })}
                      className="form-control-ca"
                    />
                    <button
                      type="button"
                      onClick={() => openKeypad('Side A Width', selectedRecipe.angleWidthA, (v) => setSelectedRecipe({ ...selectedRecipe, angleWidthA: v }))}
                      className="btn-ca btn-ca-default p-1.5"
                    >
                      <Calculator className="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>

                <div>
                  <label className="font-bold text-slate-700">Side B Width (mm) <span className="text-red-500">*</span></label>
                  <div className="flex items-center gap-1 mt-1">
                    <input
                      type="number"
                      value={selectedRecipe.angleWidthB}
                      onChange={(e) => setSelectedRecipe({ ...selectedRecipe, angleWidthB: parseFloat(e.target.value) || 0 })}
                      className="form-control-ca"
                    />
                    <button
                      type="button"
                      onClick={() => openKeypad('Side B Width', selectedRecipe.angleWidthB, (v) => setSelectedRecipe({ ...selectedRecipe, angleWidthB: v }))}
                      className="btn-ca btn-ca-default p-1.5"
                    >
                      <Calculator className="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>

                <div>
                  <label className="font-bold text-slate-700">Thickness (mm) <span className="text-red-500">*</span></label>
                  <input
                    type="number"
                    value={selectedRecipe.thickness}
                    onChange={(e) => setSelectedRecipe({ ...selectedRecipe, thickness: parseFloat(e.target.value) || 0 })}
                    className="form-control-ca mt-1"
                    placeholder="Enter Thickness"
                  />
                </div>

                <div>
                  <label className="font-bold text-slate-700">Program Length (mm) <span className="text-red-500">*</span></label>
                  <div className="flex items-center gap-1 mt-1">
                    <input
                      type="number"
                      value={selectedRecipe.totalLength}
                      onChange={(e) => setSelectedRecipe({ ...selectedRecipe, totalLength: parseFloat(e.target.value) || 0 })}
                      className="form-control-ca font-bold"
                    />
                    <button
                      type="button"
                      onClick={() => openKeypad('Program Length', selectedRecipe.totalLength, (v) => setSelectedRecipe({ ...selectedRecipe, totalLength: v }))}
                      className="btn-ca btn-ca-default p-1.5"
                    >
                      <Calculator className="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>

                <div>
                  <label className="font-bold text-slate-700">Material Description</label>
                  <input
                    type="text"
                    value={selectedRecipe.itemName}
                    onChange={(e) => setSelectedRecipe({ ...selectedRecipe, itemName: e.target.value })}
                    className="form-control-ca mt-1"
                    placeholder="Enter Material / Description"
                  />
                </div>

                <div>
                  <label className="font-bold text-slate-700">Status</label>
                  <select
                    value={selectedRecipe.isActive ? '1' : '0'}
                    onChange={(e) => setSelectedRecipe({ ...selectedRecipe, isActive: e.target.value === '1' })}
                    className="form-control-ca mt-1"
                  >
                    <option value="1">Active</option>
                    <option value="0">In Active</option>
                  </select>
                </div>
              </div>

              {/* Unified 2D/3D CAD Visualizer Blueprint */}
              <div className="pt-3 border-t border-slate-200">
                <label className="font-bold text-xs text-slate-700 mb-2 block">Unified Interactive Visualizer (2D & 3D)</label>
                <div className="h-[500px] rounded overflow-hidden">
                  <AngleBarViewer
                    recipe={selectedRecipe}
                    highlightStepIndex={highlightedStep}
                    onSelectStep={(idx) => {
                      setHighlightedStep(idx);
                      // In a real app, this might scroll the table to the selected step
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

              {/* itemRecipeSteps Table */}
              <div className="pt-3 border-t border-slate-200">
                <div className="flex items-center justify-between mb-2">
                  <label className="font-bold text-xs text-slate-800">Item Recipe Operations Table</label>
                  <div className="flex gap-2">
                    <button
                      onClick={() => {
                        if (confirm("Are you sure you want to clear all steps?")) {
                          setSelectedRecipe({ ...selectedRecipe, steps: [] });
                        }
                      }}
                      type="button"
                      className="btn-ca btn-ca-danger text-xs py-1"
                    >
                      <Trash2 className="w-3.5 h-3.5" /> Clear All
                    </button>
                    <button
                      onClick={handleAddStep}
                      type="button"
                      className="btn-ca btn-ca-primary text-xs py-1"
                    >
                      <Plus className="w-3.5 h-3.5" /> Add Step Operation
                    </button>
                  </div>
                </div>

                <table className="table-custom border">
                  <thead>
                    <tr>
                      <th style={{ width: '70px' }}>Step #</th>
                      <th>Operation <span className="text-red-500">*</span></th>
                      <th>Side / Flange</th>
                      <th>X Pos (mm) <span className="text-red-500">*</span></th>
                      <th>Y Pos (mm)</th>
                      <th>Tool Die (mm)</th>
                      <th>Measurement Type</th>
                      <th style={{ textAlign: 'right', width: '70px' }}>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    {selectedRecipe.steps.map((step, idx) => (
                      <tr
                        key={step.id || idx}
                        className={highlightedStep === idx ? 'bg-blue-50' : ''}
                        onMouseEnter={() => setHighlightedStep(idx)}
                        onMouseLeave={() => setHighlightedStep(undefined)}
                      >
                        <td className="font-bold text-slate-600">#{step.stepNumber}</td>
                        <td>
                          <select
                            value={step.operationType}
                            onChange={(e) => handleUpdateStep(idx, 'operationType', e.target.value)}
                            className="form-control-ca text-xs py-1"
                          >
                            <option value="PUNCH">PUNCH</option>
                            <option value="MARK">MARKING</option>
                            <option value="CUT">CUTTING</option>
                          </select>
                        </td>
                        <td>
                          <select
                            value={step.side}
                            onChange={(e) => handleUpdateStep(idx, 'side', e.target.value as SideType)}
                            className="form-control-ca text-xs py-1"
                          >
                            <option value="A">Side A</option>
                            <option value="B">Side B</option>
                            <option value="NA">Both / NA</option>
                          </select>
                        </td>
                        <td>
                          <div className="flex items-center gap-1">
                            <input
                              type="number"
                              value={step.xPosition}
                              onChange={(e) => handleUpdateStep(idx, 'xPosition', parseFloat(e.target.value) || 0)}
                              className="form-control-ca text-xs w-24 py-1 font-mono font-bold"
                            />
                            <button
                              type="button"
                              onClick={() => openKeypad(`Step #${step.stepNumber} X-Pos`, step.xPosition, (v) => handleUpdateStep(idx, 'xPosition', v))}
                              className="btn-ca btn-ca-default p-1 text-[10px]"
                            >
                              <Calculator className="w-3 h-3" />
                            </button>
                          </div>
                        </td>
                        <td>
                          <div className="flex items-center gap-1">
                            <input
                              type="number"
                              value={step.yPosition}
                              onChange={(e) => handleUpdateStep(idx, 'yPosition', parseFloat(e.target.value) || 0)}
                              className="form-control-ca text-xs w-20 py-1 font-mono"
                            />
                            <button
                              type="button"
                              onClick={() => openKeypad(`Step #${step.stepNumber} Y-Pos`, step.yPosition, (v) => handleUpdateStep(idx, 'yPosition', v))}
                              className="btn-ca btn-ca-default p-1 text-[10px]"
                            >
                              <Calculator className="w-3 h-3" />
                            </button>
                          </div>
                        </td>
                        <td>
                          <input
                            type="number"
                            value={step.toolSize || 18}
                            onChange={(e) => handleUpdateStep(idx, 'toolSize', parseFloat(e.target.value) || 18)}
                            className="form-control-ca text-xs w-20 py-1"
                          />
                        </td>
                        <td>
                          <select
                            value="Absolute"
                            className="form-control-ca text-xs py-1"
                          >
                            <option value="Absolute">Absolute</option>
                            <option value="Incremental">Incremental</option>
                          </select>
                        </td>
                        <td style={{ textAlign: 'right' }}>
                          <button
                            type="button"
                            onClick={() => handleDeleteStep(idx)}
                            className="btn-ca btn-ca-danger text-xs py-1 px-2"
                          >
                            <Trash2 className="w-3.5 h-3.5" />
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
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
