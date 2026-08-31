import React, { useEffect, useState } from 'react';
import { wsClient } from '../services/wsClient.js';
import { PlcTagDefinition } from '@innovance-hmi/shared';
import { DataTable, Column } from '../components/common/DataTable.js';
import {
  RefreshCw,
  Edit2,
  Check,
  X,
} from 'lucide-react';

export const TagMasterView: React.FC = () => {
  const [tagDefs, setTagDefs] = useState<PlcTagDefinition[]>([]);
  const [selectedCategory, setSelectedCategory] = useState<string>('ALL');
  const [editingTag, setEditingTag] = useState<string | null>(null);
  const [writeValue, setWriteValue] = useState<string>('');
  const [loading, setLoading] = useState<boolean>(true);

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

  const handleWriteSubmit = (tag: PlcTagDefinition) => {
    let parsed: any = writeValue;
    if (tag.dataType === 'Boolean') {
      parsed = writeValue.toLowerCase() === 'true' || writeValue === '1';
    } else if (tag.dataType === 'Float' || tag.dataType === 'Int16' || tag.dataType === 'Int32') {
      parsed = Number(writeValue);
    }

    wsClient.writeTag(tag.tagName, parsed, tag.dataType);
    setEditingTag(null);
    setWriteValue('');
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
      render: (t) => <span className="text-slate-600 font-mono">{t.dataType}</span>,
    },
    {
      key: 'tagAddress',
      header: 'OPC-UA Node Address',
      render: (t) => <span className="font-mono text-slate-600 text-xs">{t.tagAddress}</span>,
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
      header: 'Write Injector',
      align: 'right',
      sortable: false,
      render: (t) => {
        const isEditing = editingTag === t.id;
        const liveVal = t.currentValue;

        return isEditing ? (
          <div className="flex items-center justify-end gap-1">
            <input
              type="text"
              value={writeValue}
              onChange={(e) => setWriteValue(e.target.value)}
              placeholder="Val"
              className="form-control-ca text-xs w-20 py-0.5 font-mono"
            />
            <button
              onClick={() => handleWriteSubmit(t)}
              className="btn-ca btn-ca-success text-xs py-0.5 px-2"
            >
              <Check className="w-3 h-3" />
            </button>
            <button
              onClick={() => setEditingTag(null)}
              className="btn-ca btn-ca-default text-xs py-0.5 px-2"
            >
              <X className="w-3 h-3" />
            </button>
          </div>
        ) : (
          <button
            onClick={() => {
              setEditingTag(t.id);
              setWriteValue(liveVal !== undefined ? String(liveVal) : '');
            }}
            className="btn-ca btn-ca-primary text-xs py-1 px-2.5"
          >
            <Edit2 className="w-3 h-3" /> Write
          </button>
        );
      },
    },
  ];

  return (
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* Top Header */}
      <div className="flex items-center justify-between pb-2 border-b border-slate-300">
        <div>
          <h2 className="text-lg font-black text-slate-900">PLC & Ui Tag Master (UiTagMaster)</h2>
          <p className="text-xs text-slate-600 font-medium mt-0.5">
            Manage real-time Innovance Modbus TCP / OPC-UA tag registers, data types, read/write access and live value monitors.
          </p>
        </div>

        <button onClick={fetchTags} className="btn-ca btn-ca-default">
          <RefreshCw className={`w-3.5 h-3.5 ${loading ? 'animate-spin' : ''}`} /> Re-sync Tags
        </button>
      </div>

      {/* Category Pills */}
      <div className="flex items-center gap-1.5 overflow-x-auto text-xs pb-1">
        {categories.map((cat) => (
          <button
            key={cat}
            onClick={() => setSelectedCategory(cat)}
            className={`px-3 py-1 rounded font-semibold transition-all ${
              selectedCategory === cat
                ? 'bg-blue-600 text-white shadow-sm font-bold'
                : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'
            }`}
          >
            {cat}
          </button>
        ))}
      </div>

      {/* Data Table */}
      <DataTable
        title="Manage Ui Tag Master DataTable"
        columns={columns}
        data={filteredTags}
        searchKeys={['tagName', 'tagAddress', 'category']}
      />
    </div>
  );
};
