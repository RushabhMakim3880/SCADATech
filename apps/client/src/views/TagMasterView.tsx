import React, { useEffect, useState } from 'react';
import { wsClient } from '../services/wsClient.js';
import { PlcTagDefinition } from '@innovance-hmi/shared';
import {
  Tag,
  Search,
  RefreshCw,
  Edit2,
  Check,
  X,
  Radio,
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
    <div className="p-6 space-y-6 flex-1 overflow-y-auto">
      {/* Header */}
      <div className="flex items-center justify-between border-b border-scada-750 pb-4">
        <div>
          <h2 className="text-xl font-extrabold text-slate-100 flex items-center gap-2.5">
            <Tag className="w-6 h-6 text-neon-cyan" />
            PLC Tag Master & 20Hz Live OPC-UA Telemetry Hub
          </h2>
          <p className="text-xs text-slate-400 font-mono">
            Direct real-time node diagnostics, subscription inspection, and live OPC-UA write injector.
          </p>
        </div>

        <button
          onClick={fetchTags}
          className="scada-btn-secondary px-4 py-2 text-xs font-mono"
        >
          <RefreshCw className={`w-4 h-4 ${loading ? 'animate-spin' : ''}`} />
          RE-SYNC TAGS
        </button>
      </div>

      {/* Search & Filter Toolbar */}
      <div className="scada-panel p-4 flex flex-wrap items-center justify-between gap-4">
        <div className="flex items-center gap-2 bg-scada-950 px-3.5 py-2 rounded-xl border border-scada-750 flex-1 max-w-md">
          <Search className="w-4 h-4 text-slate-400" />
          <input
            type="text"
            placeholder="Search by tag name, OPC-UA NodeID or code..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="bg-transparent border-none text-xs font-mono text-slate-100 placeholder-slate-500 focus:outline-none w-full"
          />
        </div>

        {/* Category Pills */}
        <div className="flex items-center gap-2 overflow-x-auto py-1">
          {categories.map((cat) => (
            <button
              key={cat}
              onClick={() => setSelectedCategory(cat)}
              className={`px-3.5 py-1.5 rounded-lg text-xs font-mono font-bold transition-all ${
                selectedCategory === cat
                  ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-slate-950 shadow-neon-cyan'
                  : 'bg-scada-950 text-slate-300 border border-scada-750 hover:border-slate-600'
              }`}
            >
              {cat}
            </button>
          ))}
        </div>
      </div>

      {/* Live Tag Table */}
      <div className="scada-panel overflow-hidden">
        <div className="max-h-[550px] overflow-y-auto">
          <table className="w-full text-left text-xs font-mono">
            <thead className="bg-scada-950 text-slate-400 sticky top-0 border-b border-scada-750">
              <tr>
                <th className="p-3.5 pl-5">Tag Name</th>
                <th className="p-3.5">Category</th>
                <th className="p-3.5">Data Type</th>
                <th className="p-3.5">OPC-UA Node Address</th>
                <th className="p-3.5 font-extrabold text-neon-cyan">Current / Live Value</th>
                <th className="p-3.5 pr-5 text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-scada-750/40">
              {filteredTags.map((tag) => {
                const liveVal = tag.currentValue;
                const isEditing = editingTag === tag.id;

                return (
                  <tr key={tag.id} className="hover:bg-scada-850/60 transition-all">
                    <td className="p-3.5 pl-5 font-bold text-slate-200">{tag.tagName}</td>
                    <td className="p-3.5">
                      <span className="px-2 py-0.5 rounded bg-scada-800 text-slate-300 text-[10px] font-bold">
                        {tag.category}
                      </span>
                    </td>
                    <td className="p-3.5 text-slate-400 text-[11px]">{tag.dataType}</td>
                    <td className="p-3.5 text-slate-400 text-[11px] truncate max-w-[200px]">
                      {tag.tagAddress}
                    </td>
                    <td className="p-3.5">
                      <div className="flex items-center gap-2">
                        <Radio className="w-3 h-3 text-neon-cyan animate-pulse" />
                        <span className="font-extrabold text-sm text-neon-cyan tracking-wider">
                          {liveVal !== undefined
                            ? typeof liveVal === 'boolean'
                              ? liveVal
                                ? 'TRUE'
                                : 'FALSE'
                              : typeof liveVal === 'number'
                              ? liveVal.toFixed(2)
                              : String(liveVal)
                            : '--'}
                        </span>
                        {tag.unit && <span className="text-[10px] text-slate-400">{tag.unit}</span>}
                      </div>
                    </td>
                    <td className="p-3.5 pr-5 text-right">
                      {isEditing ? (
                        <div className="flex items-center justify-end gap-1.5">
                          <input
                            type="text"
                            value={writeValue}
                            onChange={(e) => setWriteValue(e.target.value)}
                            placeholder="Val"
                            className="w-16 bg-scada-950 border border-cyan-400 rounded px-2 py-1 text-slate-100 text-xs font-mono"
                          />
                          <button
                            onClick={() => handleWriteSubmit(tag)}
                            className="p-1 bg-emerald-600 hover:bg-emerald-500 rounded text-slate-950 font-bold"
                          >
                            <Check className="w-3.5 h-3.5" />
                          </button>
                          <button
                            onClick={() => setEditingTag(null)}
                            className="p-1 bg-scada-800 hover:bg-scada-700 rounded text-slate-300"
                          >
                            <X className="w-3.5 h-3.5" />
                          </button>
                        </div>
                      ) : (
                        <button
                          onClick={() => {
                            setEditingTag(tag.id);
                            setWriteValue(liveVal !== undefined ? String(liveVal) : '');
                          }}
                          className="px-2.5 py-1 rounded bg-scada-800 hover:bg-cyan-950 hover:text-neon-cyan hover:border hover:border-cyan-500/50 text-slate-300 text-xs flex items-center gap-1 ml-auto"
                        >
                          <Edit2 className="w-3 h-3" /> WRITE
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
