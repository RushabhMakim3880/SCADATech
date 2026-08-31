import React, { useEffect, useState } from 'react';
import { PlcTagDefinition } from '@innovance-hmi/shared';
import { usePlcStore } from '../stores/usePlcStore.js';
import { wsClient } from '../services/wsClient.js';
import { Tag, Search, RefreshCw, Send, CheckCircle2 } from 'lucide-react';

export const TagMasterView: React.FC = () => {
  const [tags, setTags] = useState<PlcTagDefinition[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState<string>('ALL');
  const [writeValues, setWriteValues] = useState<Record<string, string>>({});
  const [writeFeedback, setWriteFeedback] = useState<Record<string, 'SUCCESS' | 'ERROR'>>({});

  const plcStore = usePlcStore();

  useEffect(() => {
    fetchTags();
  }, []);

  const fetchTags = async () => {
    setLoading(true);
    try {
      const res = await fetch('/api/tags');
      const json = await res.json();
      if (json.success) {
        setTags(json.data);
      }
    } catch (err) {
      console.error('Failed to fetch PLC tags', err);
    } finally {
      setLoading(false);
    }
  };

  const getLiveValue = (tag: PlcTagDefinition) => {
    if (tag.tagName.includes('Feed_Axis_Current_Position')) return plcStore.feedPositionMm.toFixed(2);
    if (tag.tagName.includes('Feed_Axis_Current_Speed')) return plcStore.feedSpeedMPerMin.toFixed(2);
    if (tag.tagName.includes('Hydraulic_Pressure_Bar')) return plcStore.hydraulicPressureBar.toFixed(1);
    if (tag.tagName.includes('Hydraulic_Pump_Running')) return plcStore.hydraulicPumpRunning ? 'TRUE' : 'FALSE';
    if (tag.tagName.includes('Infeed_Clamp')) return plcStore.infeedClamp ? 'TRUE' : 'FALSE';
    if (tag.tagName.includes('Carriage_Clamp')) return plcStore.carriageClamp ? 'TRUE' : 'FALSE';
    if (tag.tagName.includes('Outfeed_Clamp')) return plcStore.outfeedClamp ? 'TRUE' : 'FALSE';
    if (tag.tagName.includes('Emergency_Stop_OK')) return plcStore.eStopOk ? 'TRUE' : 'FALSE';
    if (tag.tagName.includes('Safety_Guards')) return plcStore.guardsOk ? 'TRUE' : 'FALSE';
    return '-';
  };

  const handleWrite = (tag: PlcTagDefinition, explicitVal?: any) => {
    const rawVal = explicitVal !== undefined ? explicitVal : writeValues[tag.tagName];
    if (rawVal === undefined || rawVal === '') return;

    let val: any = rawVal;
    if (tag.dataType === 'Boolean') {
      val = rawVal === 'true' || rawVal === true || rawVal === '1';
    } else if (tag.dataType === 'Float' || tag.dataType === 'Double') {
      val = parseFloat(rawVal);
    } else {
      val = parseInt(rawVal, 10);
    }

    wsClient.writeTag(tag.tagName, val, tag.dataType);

    setWriteFeedback((prev) => ({ ...prev, [tag.tagName]: 'SUCCESS' }));
    setTimeout(() => {
      setWriteFeedback((prev) => {
        const next = { ...prev };
        delete next[tag.tagName];
        return next;
      });
    }, 1500);
  };

  const categories = ['ALL', 'AXIS_DRO', 'HEAD_CONTROL', 'HYDRAULIC', 'CLAMP', 'INTERLOCK', 'AUTO_CYCLE', 'SYSTEM'];

  const filteredTags = tags.filter((t) => {
    const matchesSearch =
      t.tagName.toLowerCase().includes(searchQuery.toLowerCase()) ||
      t.tagAddress.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesCat = selectedCategory === 'ALL' || t.category === selectedCategory;
    return matchesSearch && matchesCat;
  });

  return (
    <div className="p-6 space-y-6 flex-1 overflow-y-auto">
      {/* Header */}
      <div className="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
          <h2 className="text-xl font-bold text-slate-100 flex items-center gap-2">
            <Tag className="w-5 h-5 text-cyan-400" />
            PLC Tag Master & Live Diagnostics
          </h2>
          <p className="text-xs text-slate-400">
            Real-time OPC-UA NodeID dictionary, continuous subscription monitor, and manual read/write testing.
          </p>
        </div>

        <button
          onClick={fetchTags}
          className="industrial-btn-secondary px-4 py-2 text-xs font-mono"
        >
          <RefreshCw className={`w-4 h-4 ${loading ? 'animate-spin' : ''}`} />
          REFRESH TAGS
        </button>
      </div>

      {/* Filters & Search */}
      <div className="flex flex-wrap items-center justify-between gap-4">
        <div className="flex items-center gap-1.5 bg-slate-900 p-1 rounded-lg border border-slate-800 text-xs font-mono overflow-x-auto">
          {categories.map((c) => (
            <button
              key={c}
              onClick={() => setSelectedCategory(c)}
              className={`px-3 py-1.5 rounded transition-all ${
                selectedCategory === c
                  ? 'bg-cyan-600 text-white font-bold'
                  : 'text-slate-400 hover:text-slate-200'
              }`}
            >
              {c.replace('_', ' ')}
            </button>
          ))}
        </div>

        <div className="relative w-72">
          <Search className="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
          <input
            type="text"
            placeholder="Search by tag name or NodeID..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="w-full bg-slate-900 border border-slate-800 rounded-lg pl-9 pr-4 py-2 text-xs text-slate-200 focus:outline-none focus:border-cyan-500 font-mono"
          />
        </div>
      </div>

      {/* Tag Table */}
      <div className="industrial-card overflow-hidden">
        <table className="w-full text-left text-xs font-mono border-collapse">
          <thead>
            <tr className="bg-slate-950/80 border-b border-slate-800 text-slate-400 uppercase text-[11px]">
              <th className="p-3.5">Tag Name</th>
              <th className="p-3.5">NodeID / Address</th>
              <th className="p-3.5">Category</th>
              <th className="p-3.5">Data Type</th>
              <th className="p-3.5 text-center">Live Value</th>
              <th className="p-3.5 text-right">Write / Test</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-800/60">
            {filteredTags.map((tag) => {
              const liveVal = getLiveValue(tag);
              const isBool = tag.dataType === 'Boolean';
              const feedback = writeFeedback[tag.tagName];

              return (
                <tr key={tag.id} className="hover:bg-slate-800/40 transition-colors">
                  <td className="p-3.5 font-bold text-slate-200">{tag.tagName}</td>
                  <td className="p-3.5 text-slate-400">{tag.tagAddress}</td>
                  <td className="p-3.5">
                    <span className="px-2 py-0.5 rounded bg-slate-800 text-cyan-300 border border-slate-700 text-[10px]">
                      {tag.category}
                    </span>
                  </td>
                  <td className="p-3.5 text-slate-300">{tag.dataType}</td>
                  <td className="p-3.5 text-center">
                    <span
                      className={`inline-block px-3 py-1 rounded font-black tracking-wider ${
                        liveVal === 'TRUE'
                          ? 'bg-emerald-950 text-emerald-400 border border-emerald-800'
                          : liveVal === 'FALSE'
                          ? 'bg-slate-900 text-slate-400 border border-slate-800'
                          : 'bg-black/80 text-emerald-400 border border-emerald-900'
                      }`}
                    >
                      {liveVal} {tag.unit ? tag.unit : ''}
                    </span>
                  </td>
                  <td className="p-3.5 text-right">
                    {tag.accessMode !== 'READ' && (
                      <div className="flex items-center justify-end gap-2">
                        {isBool ? (
                          <div className="flex gap-1">
                            <button
                              onClick={() => handleWrite(tag, true)}
                              className="px-2.5 py-1 bg-emerald-700 hover:bg-emerald-600 text-white rounded text-[10px] font-bold"
                            >
                              SET 1
                            </button>
                            <button
                              onClick={() => handleWrite(tag, false)}
                              className="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded text-[10px] font-bold border border-slate-700"
                            >
                              SET 0
                            </button>
                          </div>
                        ) : (
                          <div className="flex items-center gap-1">
                            <input
                              type="text"
                              placeholder="val..."
                              value={writeValues[tag.tagName] || ''}
                              onChange={(e) =>
                                setWriteValues({ ...writeValues, [tag.tagName]: e.target.value })
                              }
                              className="w-16 bg-slate-950 border border-slate-700 rounded px-2 py-1 text-slate-100 text-xs focus:outline-none focus:border-cyan-500"
                            />
                            <button
                              onClick={() => handleWrite(tag)}
                              className="p-1.5 bg-cyan-700 hover:bg-cyan-600 text-white rounded"
                            >
                              <Send className="w-3.5 h-3.5" />
                            </button>
                          </div>
                        )}

                        {feedback === 'SUCCESS' && (
                          <CheckCircle2 className="w-4 h-4 text-emerald-400 animate-bounce" />
                        )}
                      </div>
                    )}
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    </div>
  );
};
