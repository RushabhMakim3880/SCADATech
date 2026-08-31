import React, { useEffect, useState } from 'react';
import { wsClient } from '../services/wsClient.js';
import { PlcTagDefinition } from '@innovance-hmi/shared';
import { DataTable, Column } from '../components/common/DataTable.js';
import {
  RefreshCw,
  Plus,
  Edit2,
  Trash2,
  Check,
  X,
  Download,
  CheckCircle,
} from 'lucide-react';

interface TagFormData {
  id?: string;
  tagName: string;
  tagAddress: string;
  tagDescription: string;
  dataType: string;
  category: string;
  accessMode: string;
  unit: string;
}

const EMPTY_TAG_FORM: TagFormData = {
  tagName: '',
  tagAddress: '',
  tagDescription: '',
  dataType: 'Float',
  category: 'AXIS_DRO',
  accessMode: 'READ_WRITE',
  unit: 'mm',
};

export const TagMasterView: React.FC = () => {
  const [tagDefs, setTagDefs] = useState<PlcTagDefinition[]>([]);
  const [selectedCategory, setSelectedCategory] = useState<string>('ALL');
  const [editingWriteTag, setEditingWriteTag] = useState<string | null>(null);
  const [writeValue, setWriteValue] = useState<string>('');
  const [loading, setLoading] = useState<boolean>(true);

  // Modal State for Add / Edit Tag Mapping
  const [isModalOpen, setIsModalOpen] = useState<boolean>(false);
  const [formData, setFormData] = useState<TagFormData>(EMPTY_TAG_FORM);
  const [isSaving, setIsSaving] = useState<boolean>(false);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);

  useEffect(() => {
    fetchTags();
  }, []);

  const fetchTags = async () => {
    setLoading(true);
    try {
      const res = await fetch('/api/tags');
      const json = await res.json();
      if (json.success) {
        setTagDefs(json.data);
      }
    } catch (err) {
      console.error('Failed to load tag definitions', err);
    } finally {
      setLoading(false);
    }
  };

  const categories = ['ALL', ...Array.from(new Set(tagDefs.map((t) => t.category)))];

  const filteredTags = tagDefs.filter((t) => {
    return selectedCategory === 'ALL' || t.category === selectedCategory;
  });

  const handleOpenCreateModal = () => {
    setFormData(EMPTY_TAG_FORM);
    setIsModalOpen(true);
  };

  const handleOpenEditModal = (t: PlcTagDefinition) => {
    setFormData({
      id: t.id,
      tagName: t.tagName,
      tagAddress: t.tagAddress,
      tagDescription: t.tagDescription || '',
      dataType: t.dataType,
      category: t.category,
      accessMode: t.accessMode || 'READ_WRITE',
      unit: t.unit || '',
    });
    setIsModalOpen(true);
  };

  const handleSaveTag = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.tagName || !formData.tagAddress) {
      alert('Tag Identifier Name and PLC Register Address are required.');
      return;
    }

    setIsSaving(true);
    try {
      const isNew = !formData.id;
      const url = isNew ? '/api/tags' : `/api/tags/${formData.id}`;
      const method = isNew ? 'POST' : 'PUT';

      const res = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      });

      const json = await res.json();
      if (json.success) {
        setSuccessMessage(isNew ? `Tag "${formData.tagName}" created successfully!` : `Tag "${formData.tagName}" updated successfully!`);
        setTimeout(() => setSuccessMessage(null), 3000);
        setIsModalOpen(false);
        fetchTags();
      }
    } catch (err) {
      console.error('Failed to save tag mapping', err);
    } finally {
      setIsSaving(false);
    }
  };

  const handleDeleteTag = async (tag: PlcTagDefinition) => {
    if (!window.confirm(`Are you sure you want to delete tag mapping "${tag.tagName}" (${tag.tagAddress})?`)) return;

    try {
      const res = await fetch(`/api/tags/${tag.id}`, { method: 'DELETE' });
      const json = await res.json();
      if (json.success) {
        setSuccessMessage(`Tag "${tag.tagName}" deleted!`);
        setTimeout(() => setSuccessMessage(null), 3000);
        fetchTags();
      }
    } catch (err) {
      console.error('Failed to delete tag', err);
    }
  };

  const handleWriteSubmit = (tag: PlcTagDefinition) => {
    let parsed: any = writeValue;
    if (tag.dataType === 'Boolean') {
      parsed = writeValue.toLowerCase() === 'true' || writeValue === '1';
    } else if (tag.dataType === 'Float' || tag.dataType === 'Int16' || tag.dataType === 'Int32') {
      parsed = Number(writeValue);
    }

    wsClient.writeTag(tag.tagName, parsed, tag.dataType);
    setEditingWriteTag(null);
    setWriteValue('');
  };

  const handleExportCsv = () => {
    let csv = 'data:text/csv;charset=utf-8,Tag Name,Register Address,Category,Data Type,Access Mode,Unit,Description\n';
    tagDefs.forEach((t) => {
      csv += `"${t.tagName}","${t.tagAddress}","${t.category}","${t.dataType}","${t.accessMode || 'READ_WRITE'}","${t.unit || ''}","${t.tagDescription || ''}"\n`;
    });
    const encoded = encodeURI(csv);
    const link = document.createElement('a');
    link.setAttribute('href', encoded);
    link.setAttribute('download', `HPT_PLC_Tag_Mapping_${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  const columns: Column<PlcTagDefinition>[] = [
    {
      key: 'tagName',
      header: 'Tag Identifier',
      render: (t) => <span className="font-bold text-slate-800">{t.tagName}</span>,
    },
    {
      key: 'category',
      header: 'Category',
      render: (t) => (
        <span className="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-[11px] font-semibold border">
          {t.category}
        </span>
      ),
    },
    {
      key: 'dataType',
      header: 'Data Type',
      render: (t) => <span className="text-slate-600 font-mono text-xs">{t.dataType}</span>,
    },
    {
      key: 'tagAddress',
      header: 'PLC Register (Modbus/H3U)',
      render: (t) => (
        <span className="font-mono text-blue-900 font-bold bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
          {t.tagAddress}
        </span>
      ),
    },
    {
      key: 'currentValue',
      header: 'Live Value (20Hz)',
      render: (t) => {
        const liveVal = t.currentValue;
        return (
          <span className="font-mono font-bold text-cyan-700 bg-slate-100 px-2.5 py-1 rounded border">
            {liveVal !== undefined
              ? typeof liveVal === 'boolean'
                ? liveVal
                  ? 'TRUE'
                  : 'FALSE'
                : typeof liveVal === 'number'
                ? liveVal.toFixed(2)
                : String(liveVal)
              : '--'}
            {t.unit && <span className="text-slate-500 font-normal ml-1">{t.unit}</span>}
          </span>
        );
      },
    },
    {
      key: 'actions',
      header: 'Actions & Test Injector',
      align: 'right',
      sortable: false,
      render: (t) => {
        const isEditingWrite = editingWriteTag === t.id;
        const liveVal = t.currentValue;

        return (
          <div className="flex items-center justify-end gap-1.5">
            {isEditingWrite ? (
              <div className="flex items-center gap-1">
                <input
                  type="text"
                  value={writeValue}
                  onChange={(e) => setWriteValue(e.target.value)}
                  placeholder="Val"
                  className="form-control-ca text-xs w-16 py-0.5 font-mono"
                />
                <button onClick={() => handleWriteSubmit(t)} className="btn-ca btn-ca-success text-xs py-0.5 px-2">
                  <Check className="w-3 h-3" />
                </button>
                <button onClick={() => setEditingWriteTag(null)} className="btn-ca btn-ca-default text-xs py-0.5 px-2">
                  <X className="w-3 h-3" />
                </button>
              </div>
            ) : (
              <button
                onClick={(e) => {
                  e.stopPropagation();
                  setEditingWriteTag(t.id);
                  setWriteValue(liveVal !== undefined ? String(liveVal) : '');
                }}
                className="btn-ca btn-ca-primary text-xs py-1 px-2"
                title="Write test value to PLC"
              >
                Write
              </button>
            )}

            <button
              onClick={(e) => {
                e.stopPropagation();
                handleOpenEditModal(t);
              }}
              className="btn-ca btn-ca-default text-xs py-1 px-2 text-slate-700"
              title="Edit Tag Mapping"
            >
              <Edit2 className="w-3.5 h-3.5" />
            </button>

            <button
              onClick={(e) => {
                e.stopPropagation();
                handleDeleteTag(t);
              }}
              className="btn-ca btn-ca-danger text-xs py-1 px-2"
              title="Delete Tag"
            >
              <Trash2 className="w-3.5 h-3.5" />
            </button>
          </div>
        );
      },
    },
  ];

  return (
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* Top Header */}
      <div className="flex flex-wrap items-center justify-between pb-2 border-b border-slate-300 gap-3">
        <div>
          <h2 className="text-lg font-black text-slate-900">PLC & UI Tag Master (UiTagMaster)</h2>
          <p className="text-xs text-slate-600 font-medium mt-0.5">
            Configure real-time Innovance Modbus TCP registers (D, M, X, Y), data types, units, and write test values.
          </p>
        </div>

        <div className="flex items-center gap-2">
          <button onClick={handleExportCsv} className="btn-ca btn-ca-default text-xs py-1.5">
            <Download className="w-3.5 h-3.5" /> Export Tag CSV
          </button>
          <button onClick={fetchTags} className="btn-ca btn-ca-default text-xs py-1.5">
            <RefreshCw className={`w-3.5 h-3.5 ${loading ? 'animate-spin' : ''}`} /> Re-sync Tags
          </button>
          <button onClick={handleOpenCreateModal} className="btn-ca btn-ca-primary text-xs py-1.5 font-bold">
            <Plus className="w-4 h-4" /> Add New PLC Tag
          </button>
        </div>
      </div>

      {successMessage && (
        <div className="p-3 bg-emerald-50 border border-emerald-300 text-emerald-900 rounded text-xs font-semibold flex items-center gap-2">
          <CheckCircle className="w-4 h-4 text-emerald-600" /> {successMessage}
        </div>
      )}

      {/* Category Filter Pills */}
      <div className="flex items-center gap-1.5 overflow-x-auto pb-1">
        {categories.map((c) => (
          <button
            key={c}
            onClick={() => setSelectedCategory(c)}
            className={`px-3 py-1 text-xs font-bold rounded-full transition-all border ${
              selectedCategory === c
                ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-100'
            }`}
          >
            {c}
          </button>
        ))}
      </div>

      {/* Tag Data Table */}
      <DataTable
        title={`Manage UI Tag Master DataTable (${filteredTags.length} records)`}
        columns={columns}
        data={filteredTags}
        searchKeys={['tagName', 'tagAddress', 'category']}
      />

      {/* Add / Edit Tag Mapping Modal */}
      {isModalOpen && (
        <div className="fixed inset-0 bg-slate-900/75 backdrop-blur-sm z-50 flex items-center justify-center p-4">
          <div className="bg-white rounded-lg shadow-2xl border border-slate-300 w-full max-w-lg overflow-hidden text-xs">
            <div className="panel-heading bg-slate-800 text-white px-4 py-3 flex items-center justify-between">
              <span className="font-bold text-sm">{formData.id ? 'Edit PLC Tag Mapping' : 'Add New PLC Tag Register Mapping'}</span>
              <button onClick={() => setIsModalOpen(false)} className="text-slate-300 hover:text-white">
                <X className="w-5 h-5" />
              </button>
            </div>

            <form onSubmit={handleSaveTag} className="p-5 space-y-4">
              <div>
                <label className="font-bold text-slate-700 block">Tag Identifier Name <span className="text-red-500">*</span></label>
                <input
                  type="text"
                  value={formData.tagName}
                  onChange={(e) => setFormData({ ...formData, tagName: e.target.value })}
                  placeholder="e.g. Infeed_Roller_Motor_Speed"
                  className="form-control-ca mt-1 font-bold text-blue-900"
                  required
                />
                <span className="text-[10px] text-slate-500">Unique SCADA tag identifier used by views and alarms</span>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="font-bold text-slate-700 block">PLC Register Address <span className="text-red-500">*</span></label>
                  <input
                    type="text"
                    value={formData.tagAddress}
                    onChange={(e) => setFormData({ ...formData, tagAddress: e.target.value })}
                    placeholder="e.g. D1020, M140, X15, Y22"
                    className="form-control-ca mt-1 font-mono font-bold text-slate-800"
                    required
                  />
                  <span className="text-[10px] text-slate-500">Innovance Modbus register address</span>
                </div>

                <div>
                  <label className="font-bold text-slate-700 block">Data Type</label>
                  <select
                    value={formData.dataType}
                    onChange={(e) => setFormData({ ...formData, dataType: e.target.value })}
                    className="form-control-ca mt-1 font-semibold"
                  >
                    <option value="Float">Float (32-bit Real)</option>
                    <option value="Boolean">Boolean (1-bit Bit / Coil)</option>
                    <option value="Int16">Int16 (16-bit Integer)</option>
                    <option value="Int32">Int32 (32-bit Integer)</option>
                    <option value="String">String</option>
                  </select>
                </div>

                <div>
                  <label className="font-bold text-slate-700 block">Category</label>
                  <select
                    value={formData.category}
                    onChange={(e) => setFormData({ ...formData, category: e.target.value })}
                    className="form-control-ca mt-1"
                  >
                    <option value="AXIS_DRO">AXIS_DRO</option>
                    <option value="HEAD_CONTROL">HEAD_CONTROL</option>
                    <option value="HYDRAULIC">HYDRAULIC</option>
                    <option value="CLAMP">CLAMP</option>
                    <option value="AUTO_CYCLE">AUTO_CYCLE</option>
                    <option value="INTERLOCK">INTERLOCK</option>
                    <option value="SYSTEM">SYSTEM</option>
                    <option value="CUSTOM">CUSTOM</option>
                  </select>
                </div>

                <div>
                  <label className="font-bold text-slate-700 block">Engineering Unit</label>
                  <input
                    type="text"
                    value={formData.unit}
                    onChange={(e) => setFormData({ ...formData, unit: e.target.value })}
                    placeholder="e.g. mm, bar, m/min, °C"
                    className="form-control-ca mt-1"
                  />
                </div>
              </div>

              <div>
                <label className="font-bold text-slate-700 block">Tag Description</label>
                <input
                  type="text"
                  value={formData.tagDescription}
                  onChange={(e) => setFormData({ ...formData, tagDescription: e.target.value })}
                  placeholder="Optional functional description"
                  className="form-control-ca mt-1"
                />
              </div>

              <div className="pt-3 border-t border-slate-200 flex justify-end gap-2">
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="btn-ca btn-ca-default"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  disabled={isSaving}
                  className="btn-ca btn-ca-primary font-bold px-4"
                >
                  {isSaving ? 'Saving...' : formData.id ? 'Update Tag' : 'Create Tag'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
