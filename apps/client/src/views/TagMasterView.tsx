import React, { useEffect, useState } from 'react';
import { wsClient } from '../services/wsClient.js';
import { PlcTagDefinition } from '@innovance-hmi/shared';
import {
  Search,
  RefreshCw,
  Edit2,
  Check,
  X,
} from 'lucide-react';

export const TagMasterView: React.FC = () => {
  const [tagDefs, setTagDefs] = useState<PlcTagDefinition[]>([]);
  const [searchQuery, setSearchQuery] = useState<string>('');
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
    const matchesCat = selectedCategory === 'ALL' || t.category === selectedCategory;
    const matchesSearch =
      t.tagAddress.toLowerCase().includes(searchQuery.toLowerCase()) ||
      t.tagName.toLowerCase().includes(searchQuery.toLowerCase());
    return matchesCat && matchesSearch;
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

  return (
    <div className="p-4 space-y-4 flex-1 overflow-y-auto">
      {/* Top Header */}
      <div className="flex items-center justify-between pb-2 border-b border-slate-300">
        <div>
          <h2 className="text-lg font-bold text-slate-800">PLC & Ui Tag Master (UiTagMaster)</h2>
          <p className="text-xs text-slate-500">
            Manage real-time OPC-UA tag addresses, data types, read/write access and live value monitors.
          </p>
        </div>

        <button
          onClick={fetchTags}
          className="btn-ca btn-ca-default"
        >
          <RefreshCw className={`w-3.5 h-3.5 ${loading ? 'animate-spin' : ''}`} /> Re-sync Tags
        </button>
      </div>

      {/* Filter Bar */}
      <div className="panel p-3 flex flex-wrap items-center justify-between gap-3">
        <div className="flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded border border-slate-300 w-72">
          <Search className="w-4 h-4 text-slate-400" />
          <input
            type="text"
            placeholder="Search tags or addresses..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="bg-transparent border-none text-xs text-slate-800 placeholder-slate-400 focus:outline-none w-full"
          />
        </div>

        {/* Category Pills */}
        <div className="flex items-center gap-1 overflow-x-auto text-xs">
          {categories.map((cat) => (
            <button
              key={cat}
              onClick={() => setSelectedCategory(cat)}
              className={`px-3 py-1 rounded font-semibold transition-all ${
                selectedCategory === cat
                  ? 'bg-blue-600 text-white shadow-sm'
                  : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
              }`}
            >
              {cat}
            </button>
          ))}
        </div>
      </div>

      {/* Live Tag Table */}
      <div className="panel">
        <div className="panel-heading">
          <span>Manage Ui Tag DataTable</span>
          <span className="text-xs text-slate-300">{filteredTags.length} Tags</span>
        </div>

        <div className="panel-body p-0">
          <table className="table-custom">
            <thead>
              <tr>
                <th>Tag Name</th>
                <th>Category</th>
                <th>Data Type</th>
                <th>OPC-UA Node Address</th>
                <th>Live Value</th>
                <th style={{ textAlign: 'right' }}>Actions</th>
              </tr>
            </thead>
            <tbody>
              {filteredTags.map((tag) => {
                const liveVal = tag.currentValue;
                const isEditing = editingTag === tag.id;

                return (
                  <tr key={tag.id}>
                    <td className="font-bold text-slate-800">{tag.tagName}</td>
                    <td>
                      <span className="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-[11px] font-semibold border">
                        {tag.category}
                      </span>
                    </td>
                    <td>{tag.dataType}</td>
                    <td className="font-mono text-slate-600 text-xs">{tag.tagAddress}</td>
                    <td>
                      <span className="font-mono font-bold text-cyan-700 bg-slate-100 px-2 py-1 rounded border">
                        {liveVal !== undefined
                          ? typeof liveVal === 'boolean'
                            ? liveVal
                              ? 'TRUE'
                              : 'FALSE'
                            : typeof liveVal === 'number'
                            ? liveVal.toFixed(2)
                            : String(liveVal)
                          : '--'}
                        {tag.unit && <span className="text-slate-500 font-normal ml-1">{tag.unit}</span>}
                      </span>
                    </td>
                    <td style={{ textAlign: 'right' }}>
                      {isEditing ? (
                        <div className="flex items-center justify-end gap-1">
                          <input
                            type="text"
                            value={writeValue}
                            onChange={(e) => setWriteValue(e.target.value)}
                            placeholder="Val"
                            className="form-control-ca text-xs w-20 py-0.5"
                          />
                          <button
                            onClick={() => handleWriteSubmit(tag)}
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
                            setEditingTag(tag.id);
                            setWriteValue(liveVal !== undefined ? String(liveVal) : '');
                          }}
                          className="btn-ca btn-ca-primary text-xs py-1 px-2.5"
                        >
                          <Edit2 className="w-3 h-3" /> Write
                        </button>
                      )}
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};
