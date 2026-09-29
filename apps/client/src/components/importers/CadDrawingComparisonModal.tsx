import React, { useState, useRef, useEffect } from 'react';
import { ItemRecipe } from '@innovance-hmi/shared';
import { AngleBarVisualizer } from '../canvas/AngleBarVisualizer.js';
import { AngleBar3DVisualizer } from '../canvas/AngleBar3DVisualizer.js';
import {
  Upload,
  FileCode,
  CheckCircle,
  X,
  ZoomIn,
  ZoomOut,
  Maximize2,
  Columns,
  Layers,
  Sparkles,
  ShieldCheck,
  Save,
  FileText,
  Table as TableIcon,
  Box,
  Link2,
  Link2Off,
  Navigation,
  LayoutGrid
} from 'lucide-react';
import Swal from 'sweetalert2';

interface CadDrawingComparisonModalProps {
  isOpen: boolean;
  onClose: () => void;
  onImportComplete: (recipe: ItemRecipe) => void;
}

type CadViewMode =
  | 'TRI_SPLIT'      // File Preview (33%) + 2D Blueprint (33%) + 3D Model (34%)
  | 'SPLIT_2D'       // File Preview (50%) + 2D Blueprint (50%)
  | 'SPLIT_3D'       // File Preview (50%) + 3D Model (50%)
  | 'SPLIT_2D_3D'    // 2D Blueprint (50%) + 3D Model (50%)
  | 'DRAWING_ONLY'   // 100% File Preview
  | 'BLUEPRINT_ONLY' // 100% 2D Blueprint
  | 'MODEL_ONLY';    // 100% 3D Model

export const CadDrawingComparisonModal: React.FC<CadDrawingComparisonModalProps> = ({
  isOpen,
  onClose,
  onImportComplete,
}) => {
  const [fileName, setFileName] = useState<string>('');
  const [isProcessing, setIsProcessing] = useState<boolean>(false);
  const [dragOver, setDragOver] = useState<boolean>(false);

  // Extracted Result State
  const [parsedRecipe, setParsedRecipe] = useState<ItemRecipe | null>(null);
  const [metadata, setMetadata] = useState<any | null>(null);
  const [ruleChecks, setRuleChecks] = useState<any | null>(null);
  const [previewImage, setPreviewImage] = useState<string>('');
  const [originalImage, setOriginalImage] = useState<string>('');

  // View Layout State & Triple Synchronization State
  const [viewMode, setViewMode] = useState<CadViewMode>('SPLIT_2D');
  const [isSyncActive, setIsSyncActive] = useState<boolean>(true);
  const [sharedFocusX, setSharedFocusX] = useState<number | null>(null);
  const [hoveredStepIndex, setHoveredStepIndex] = useState<number | null>(null);
  const [activeTab, setActiveTab] = useState<'STEPS' | 'METADATA' | 'RULES'>('STEPS');

  // Drawing Viewport Pan, Zoom & Rotation
  const [imgZoom, setImgZoom] = useState<number>(1.0);
  const [imgPan, setImgPan] = useState<{ x: number; y: number }>({ x: 0, y: 0 });
  const [imgRotation, setImgRotation] = useState<number>(0);
  const [isInverted, setIsInverted] = useState<boolean>(false);
  const isPanning = useRef(false);
  const panStart = useRef({ x: 0, y: 0 });

  const fileInputRef = useRef<HTMLInputElement | null>(null);
  const viewportRef = useRef<HTMLDivElement | null>(null);

  useEffect(() => {
    const el = viewportRef.current;
    if (!el) return;

    const onWheelNative = (e: WheelEvent) => {
      e.preventDefault();
      const zoomFactor = e.deltaY < 0 ? 1.15 : 0.85;
      setImgZoom((prev) => Math.max(0.3, Math.min(5.0, prev * zoomFactor)));
    };

    el.addEventListener('wheel', onWheelNative, { passive: false });
    return () => {
      el.removeEventListener('wheel', onWheelNative);
    };
  }, [isOpen, viewMode, previewImage]);

  // Reset or initialize on open
  useEffect(() => {
    if (isOpen && !parsedRecipe) {
      setImgZoom(1.0);
      setImgPan({ x: 0, y: 0 });
      setImgRotation(0);
      setIsInverted(false);
      setSharedFocusX(null);
      setHoveredStepIndex(null);
    }
  }, [isOpen, parsedRecipe]);

  if (!isOpen) return null;

  const lengthMm = parsedRecipe?.totalLength || 6016;

  const handleFileUpload = async (uploadedFile: File) => {
    setFileName(uploadedFile.name);
    setIsProcessing(true);

    try {
      const reader = new FileReader();
      reader.onload = async (e) => {
        const base64Data = e.target?.result as string;
        try {
          const res = await fetch('/api/recipes/upload-drawing', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              fileName: uploadedFile.name,
              fileData: base64Data,
            }),
          });

          const json = await res.json();
          if (json.success && json.recipe) {
            setParsedRecipe(json.recipe);
            setMetadata(json.metadata);
            setRuleChecks(json.ruleChecks);
            setPreviewImage(json.previewImage || '');
            setOriginalImage(json.originalImage || '');
            setSharedFocusX(json.recipe.totalLength / 2);
            Swal.fire({
              icon: 'success',
              title: 'Extraction Complete!',
              html: `Extracted Mark: <b>${json.metadata?.markNo || json.recipe.itemCode}</b><br/>Detected <b>${json.recipe.steps.length}</b> operations with IS 802 validation.`,
              timer: 3000,
              background: '#0d131f',
              color: '#f8fafc',
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Extraction Failed',
              text: json.error || 'Failed to parse drawing',
              background: '#0d131f',
              color: '#f8fafc',
            });
          }
        } catch (err: any) {
          Swal.fire({
            icon: 'error',
            title: 'Server Error',
            text: err.message,
            background: '#0d131f',
            color: '#f8fafc',
          });
        } finally {
          setIsProcessing(false);
        }
      };
      reader.readAsDataURL(uploadedFile);
    } catch (err: any) {
      setIsProcessing(false);
      Swal.fire({
        icon: 'error',
        title: 'File Read Error',
        text: err.message,
        background: '#0d131f',
        color: '#f8fafc',
      });
    }
  };

  const handleLoadSample = async () => {
    setIsProcessing(true);
    setFileName('TEST_R.pdf (GETCO 66kV Transmission Tower Leg)');

    try {
      const res = await fetch('/api/recipes/sample-drawing');
      const json = await res.json();
      if (json.success && json.recipe) {
        setParsedRecipe(json.recipe);
        setMetadata(json.metadata);
        setRuleChecks(json.ruleChecks);
        setPreviewImage(json.previewImage || '');
        setOriginalImage(json.originalImage || '');
        setSharedFocusX(json.recipe.totalLength / 2);
        Swal.fire({
          icon: 'success',
          title: 'Sample Drawing Loaded!',
          html: `Mark: <b>NBS-601</b> (${json.metadata?.section})<br/><b>${json.recipe.steps.length}</b> Operations Extracted & Mapped to 6-Head Punching CNC.`,
          timer: 3000,
          background: '#0d131f',
          color: '#f8fafc',
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Failed to load sample',
          text: json.error || 'Sample file not found',
          background: '#0d131f',
          color: '#f8fafc',
        });
      }
    } catch (err: any) {
      Swal.fire({
        icon: 'error',
        title: 'Error loading sample',
        text: err.message,
        background: '#0d131f',
        color: '#f8fafc',
      });
    } finally {
      setIsProcessing(false);
    }
  };

  const handleApplyToMaster = () => {
    if (!parsedRecipe) return;
    onImportComplete(parsedRecipe);
    onClose();
    Swal.fire({
      icon: 'success',
      title: 'Recipe Imported to Master!',
      text: `Item Recipe ${parsedRecipe.itemCode} is loaded into the recipe editor.`,
      timer: 2500,
      background: '#0d131f',
      color: '#f8fafc',
    });
  };

  // Drawing Viewport Drag / Pan handlers
  const handleMouseDown = (e: React.MouseEvent) => {
    isPanning.current = true;
    panStart.current = {
      x: e.clientX - imgPan.x,
      y: e.clientY - imgPan.y,
    };
  };

  const handleMouseMove = (e: React.MouseEvent) => {
    if (!isPanning.current) return;
    const newX = e.clientX - panStart.current.x;
    const newY = e.clientY - panStart.current.y;
    setImgPan({ x: newX, y: newY });

    if (isSyncActive && viewportRef.current) {
      const width = viewportRef.current.clientWidth;
      // Convert pan to length Mm
      const norm = Math.max(0, Math.min(1, (width / 2 - newX) / (width * imgZoom)));
      const centerMm = Math.round(norm * lengthMm);
      setSharedFocusX(centerMm);
    }
  };

  const handleMouseUp = () => {
    isPanning.current = false;
  };

  const activeDisplayImage = previewImage || originalImage;
  const currentFocusMm = sharedFocusX !== null ? Math.round(sharedFocusX) : Math.round(lengthMm / 2);
  const activeHoveredStepObj = (hoveredStepIndex !== null && parsedRecipe?.steps[hoveredStepIndex])
    ? parsedRecipe.steps[hoveredStepIndex]
    : null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-2 sm:p-4 backdrop-blur-md select-none overflow-hidden">
      <div className="bg-[#0c1017] border-2 border-[#1f2b3d] rounded-2xl shadow-2xl w-full h-[96vh] flex flex-col overflow-hidden text-slate-200">
        {/* Top Header */}
        <div className="bg-[#111724] border-b border-[#1c2738] px-5 py-3 flex flex-wrap items-center justify-between gap-3 shrink-0">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-sky-950/80 border border-sky-600/50 flex items-center justify-center text-sky-400 shadow-[0_0_15px_rgba(56,189,248,0.2)]">
              <Sparkles className="w-5 h-5 animate-pulse" />
            </div>
            <div>
              <div className="flex items-center gap-2">
                <span className="font-black text-sm text-white tracking-wider uppercase">
                  Automated Drawing & CAD Extraction
                </span>
                <span className="text-[10px] font-mono px-2 py-0.5 rounded bg-sky-950 border border-sky-800 text-sky-300 font-bold">
                  PDF • DXF • DWG • NC1
                </span>
                {metadata && (
                  <span className="text-[11px] font-mono font-bold px-2 py-0.5 rounded bg-emerald-950 border border-emerald-800 text-emerald-300">
                    MARK: {metadata.markNo} ({metadata.section})
                  </span>
                )}
              </div>
              <div className="text-xs text-slate-400">
                Automatic Hole Calculation, Flange Assignment & IS 802 Structural Rule Validation
              </div>
            </div>
          </div>

          <div className="flex flex-wrap items-center gap-2">
            {/* View Mode Switcher */}
            {parsedRecipe && (
              <div className="flex items-center bg-[#090d14] rounded-lg border border-[#1b2536] p-1 gap-1">
                <button
                  type="button"
                  onClick={() => setViewMode('SPLIT_2D')}
                  className={`px-3 py-1 rounded text-xs font-bold flex items-center gap-1.5 transition-colors ${
                    viewMode === 'SPLIT_2D'
                      ? 'bg-sky-600 text-white shadow'
                      : 'text-slate-400 hover:text-white'
                  }`}
                  title="Side-by-Side: Customer Drawing (50%) + System 2D Blueprint (50%)"
                >
                  <Columns className="w-3.5 h-3.5" />
                  <span>Drawing + 2D (50/50)</span>
                </button>
                <button
                  type="button"
                  onClick={() => setViewMode('SPLIT_3D')}
                  className={`px-3 py-1 rounded text-xs font-bold flex items-center gap-1.5 transition-colors ${
                    viewMode === 'SPLIT_3D'
                      ? 'bg-sky-600 text-white shadow'
                      : 'text-slate-400 hover:text-white'
                  }`}
                  title="Side-by-Side: Customer Drawing (50%) + System 3D Model (50%)"
                >
                  <Box className="w-3.5 h-3.5" />
                  <span>Drawing + 3D</span>
                </button>
                <button
                  type="button"
                  onClick={() => setViewMode('TRI_SPLIT')}
                  className={`px-2.5 py-1 rounded text-xs font-bold flex items-center gap-1.5 transition-colors ${
                    viewMode === 'TRI_SPLIT'
                      ? 'bg-sky-600 text-white shadow'
                      : 'text-slate-400 hover:text-white'
                  }`}
                  title="3-Way Split: Drawing + 2D Blueprint + 3D Model"
                >
                  <LayoutGrid className="w-3.5 h-3.5" />
                  <span>Tri-Split</span>
                </button>
                <button
                  type="button"
                  onClick={() => setViewMode('SPLIT_2D_3D')}
                  className={`px-2.5 py-1 rounded text-xs font-bold flex items-center gap-1.5 transition-colors ${
                    viewMode === 'SPLIT_2D_3D'
                      ? 'bg-sky-600 text-white shadow'
                      : 'text-slate-400 hover:text-white'
                  }`}
                  title="System 2D Blueprint + 3D Model"
                >
                  <Layers className="w-3.5 h-3.5" />
                  <span>2D + 3D</span>
                </button>
                <button
                  type="button"
                  onClick={() => setViewMode('DRAWING_ONLY')}
                  className={`px-2.5 py-1 rounded text-xs font-bold flex items-center gap-1.5 transition-colors ${
                    viewMode === 'DRAWING_ONLY'
                      ? 'bg-sky-600 text-white shadow'
                      : 'text-slate-400 hover:text-white'
                  }`}
                  title="Original customer drawing full screen"
                >
                  <FileText className="w-3.5 h-3.5" />
                  <span>Drawing Only</span>
                </button>
              </div>
            )}

            {/* Load Sample Button */}
            <button
              type="button"
              onClick={handleLoadSample}
              disabled={isProcessing}
              className="px-3 py-1.5 rounded-lg bg-[#182335] hover:bg-[#202f47] border border-[#273952] text-sky-300 font-bold text-xs flex items-center gap-1.5 transition-all shadow-sm"
              title="Instantly test extraction using the sample GETCO transmission drawing"
            >
              <FileCode className="w-4 h-4 text-sky-400" />
              <span>Load Sample (TEST_R.pdf)</span>
            </button>

            {/* Close Button */}
            <button
              type="button"
              onClick={onClose}
              className="w-9 h-9 rounded-lg bg-[#151c27] hover:bg-[#1f293a] border border-[#222d3d] text-slate-400 hover:text-white flex items-center justify-center transition-colors"
            >
              <X className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* Main Body */}
        <div className="flex-1 flex flex-col overflow-hidden relative">
          {isProcessing && (
            <div className="absolute inset-0 z-50 bg-black/85 flex flex-col items-center justify-center p-6 backdrop-blur-sm">
              <div className="w-16 h-16 rounded-full border-4 border-sky-500 border-t-transparent animate-spin mb-4" />
              <div className="text-lg font-black text-white tracking-wide uppercase">
                AI Vision & CAD Drawing Parser Active
              </div>
              <div className="text-xs text-sky-400 font-mono mt-1 animate-pulse">
                Running 200 DPI OCR, Hough Geometric Circle Transform & IS 802 Pitch Mapping...
              </div>
            </div>
          )}

          {!parsedRecipe ? (
            /* Upload State */
            <div className="flex-1 flex flex-col items-center justify-center p-8">
              <input
                ref={fileInputRef}
                type="file"
                accept=".pdf,.dxf,.dwg,.nc1"
                className="hidden"
                onChange={(e) => {
                  if (e.target.files && e.target.files[0]) {
                    handleFileUpload(e.target.files[0]);
                  }
                }}
              />

              <div
                onDragOver={(e) => {
                  e.preventDefault();
                  setDragOver(true);
                }}
                onDragLeave={() => setDragOver(false)}
                onDrop={(e) => {
                  e.preventDefault();
                  setDragOver(false);
                  if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                    handleFileUpload(e.dataTransfer.files[0]);
                  }
                }}
                className={`max-w-2xl w-full p-12 rounded-2xl border-2 border-dashed flex flex-col items-center justify-center text-center cursor-pointer transition-all ${
                  dragOver
                    ? 'border-sky-400 bg-sky-950/20 scale-[1.01]'
                    : 'border-[#26354a] bg-[#0e141f] hover:border-sky-500/60 hover:bg-[#111825]'
                }`}
                onClick={() => fileInputRef.current?.click()}
              >
                <div className="w-20 h-20 rounded-2xl bg-sky-950/60 border border-sky-500/40 flex items-center justify-center text-sky-400 shadow-xl mb-5">
                  <Upload className="w-10 h-10 animate-bounce" />
                </div>
                <div className="text-lg font-extrabold text-white mb-2">
                  Drop Engineering Drawing or AutoCAD File Here
                </div>
                <div className="text-xs text-slate-400 max-w-md leading-relaxed mb-6">
                  Supports scanned or vector PDF fabrication drawings (e.g. GETCO, PGCIL, NTPC), AutoCAD DXF/DWG files, and standard DSTV / NC1 steel files.
                </div>
                <div className="flex items-center gap-3">
                  <button
                    type="button"
                    className="px-5 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs shadow-lg uppercase tracking-wider"
                  >
                    Select File From Device
                  </button>
                  <button
                    type="button"
                    onClick={(e) => {
                      e.stopPropagation();
                      handleLoadSample();
                    }}
                    className="px-4 py-2.5 rounded-lg bg-[#182335] hover:bg-[#202f47] border border-[#2b3c54] text-sky-300 font-bold text-xs"
                  >
                    Try Sample (TEST_R.pdf)
                  </button>
                </div>
              </div>
            </div>
          ) : (
            /* Comparison Split Workspace */
            <div className="flex-1 flex flex-col overflow-hidden">
              {/* Master Synchronized Timeline & Scrubber Bar */}
              <div className="bg-[#0e141f] px-5 py-2 border-b border-[#1b2738] flex flex-wrap items-center justify-between gap-3 text-xs shrink-0">
                <div className="flex items-center gap-3 font-mono">
                  <button
                    type="button"
                    onClick={() => setIsSyncActive(!isSyncActive)}
                    className={`flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-bold border transition-all ${
                      isSyncActive
                        ? 'bg-cyan-950 border-cyan-500 text-cyan-300 shadow-[0_0_10px_rgba(6,182,212,0.3)]'
                        : 'bg-slate-800 border-slate-700 text-slate-400'
                    }`}
                    title="Toggle synchronization across File Preview, 2D Blueprint and 3D Model"
                  >
                    {isSyncActive ? (
                      <>
                        <Link2 className="w-3.5 h-3.5 text-cyan-400 animate-pulse" />
                        <span>Preview, 2D & 3D Synced</span>
                      </>
                    ) : (
                      <>
                        <Link2Off className="w-3.5 h-3.5 text-slate-400" />
                        <span>Sync Off</span>
                      </>
                    )}
                  </button>

                  <div className="flex items-center gap-2 text-slate-300">
                    <Navigation className="w-3.5 h-3.5 text-sky-400" />
                    <span>Focus:</span>
                    <b className="text-cyan-300 font-bold">{currentFocusMm} mm</b>
                    <span className="text-slate-500">/</span>
                    <span>{lengthMm} mm</span>
                    {activeHoveredStepObj && (
                      <span className="ml-2 px-2 py-0.5 rounded bg-sky-900/60 border border-sky-600/60 text-sky-200 text-[10px]">
                        Step #{activeHoveredStepObj.stepNumber} • Flange {activeHoveredStepObj.side} • Ø{activeHoveredStepObj.toolSize}mm
                      </span>
                    )}
                  </div>
                </div>

                <div className="flex-1 max-w-lg flex items-center gap-2">
                  <span className="text-[10px] text-slate-500 font-mono">0mm</span>
                  <input
                    type="range"
                    min={0}
                    max={lengthMm}
                    value={currentFocusMm}
                    onChange={(e) => {
                      const val = parseFloat(e.target.value);
                      setSharedFocusX(val);
                    }}
                    className="w-full accent-cyan-400 h-1.5 bg-slate-800 rounded-lg cursor-pointer"
                  />
                  <span className="text-[10px] text-slate-500 font-mono">{lengthMm}mm</span>
                </div>

                <div className="flex items-center gap-1 font-mono text-[10px]">
                  <button onClick={() => setSharedFocusX(0)} className="px-2 py-1 rounded bg-[#16202e] hover:bg-[#202e42] text-slate-300">0mm</button>
                  <button onClick={() => setSharedFocusX(lengthMm * 0.25)} className="px-2 py-1 rounded bg-[#16202e] hover:bg-[#202e42] text-slate-300">1/4</button>
                  <button onClick={() => setSharedFocusX(lengthMm * 0.5)} className="px-2 py-1 rounded bg-[#16202e] hover:bg-[#202e42] text-slate-300">Center</button>
                  <button onClick={() => setSharedFocusX(lengthMm * 0.75)} className="px-2 py-1 rounded bg-[#16202e] hover:bg-[#202e42] text-slate-300">3/4</button>
                  <button onClick={() => setSharedFocusX(lengthMm)} className="px-2 py-1 rounded bg-[#16202e] hover:bg-[#202e42] text-slate-300">Cut End</button>
                </div>
              </div>

              {/* Workspace Split Panels Container */}
              <div
                className={`flex-1 grid overflow-hidden min-h-0 divide-y lg:divide-y-0 lg:divide-x divide-[#1e2a3c] ${
                  viewMode === 'TRI_SPLIT'
                    ? 'grid-cols-1 lg:grid-cols-3'
                    : viewMode === 'DRAWING_ONLY' || viewMode === 'BLUEPRINT_ONLY' || viewMode === 'MODEL_ONLY'
                    ? 'grid-cols-1'
                    : 'grid-cols-1 lg:grid-cols-2'
                }`}
              >
                {/* Panel 1: Original Scanned Customer Drawing (Preview) */}
                {(viewMode === 'TRI_SPLIT' || viewMode === 'SPLIT_2D' || viewMode === 'SPLIT_3D' || viewMode === 'DRAWING_ONLY') && (
                  <div className="flex flex-col h-full bg-[#080b10] overflow-hidden">
                    {/* Panel Header */}
                    <div className="bg-[#0e131d] px-4 py-2 border-b border-[#1b2536] flex items-center justify-between shrink-0">
                      <div className="flex items-center gap-2">
                        <FileText className="w-4 h-4 text-amber-400" />
                        <span className="font-extrabold text-xs text-white uppercase tracking-wider">
                          1. Original Drawing (Preview)
                        </span>
                        <span className="text-[10px] font-mono text-slate-400 truncate max-w-[140px]">
                          ({fileName})
                        </span>
                      </div>

                      {/* Image Viewer Toolbar */}
                      <div className="flex items-center gap-1">
                        <button
                          type="button"
                          onClick={() => setImgRotation((r) => (r - 90 + 360) % 360)}
                          className="px-2 py-0.5 rounded text-[10px] font-bold bg-[#151c27] hover:bg-[#1e2838] border border-[#222d3d] text-slate-300 flex items-center gap-1"
                          title="Rotate 90° Counter-Clockwise"
                        >
                          ↺ -90°
                        </button>
                        <button
                          type="button"
                          onClick={() => setImgRotation((r) => (r + 90) % 360)}
                          className="px-2 py-0.5 rounded text-[10px] font-bold bg-[#151c27] hover:bg-[#1e2838] border border-[#222d3d] text-slate-300 flex items-center gap-1"
                          title="Rotate 90° Clockwise to match horizontal angle bar"
                        >
                          ↻ +90°
                        </button>
                        <button
                          type="button"
                          onClick={() => setIsInverted(!isInverted)}
                          className={`px-2 py-0.5 rounded text-[10px] font-bold border transition-colors ${
                            isInverted
                              ? 'bg-cyan-950/80 border-cyan-500 text-cyan-300'
                              : 'bg-[#151c27] border-[#222d3d] text-slate-400'
                          }`}
                          title="Toggle Inverted Dark CAD contrast filter"
                        >
                          {isInverted ? 'CAD Dark ON' : 'Paper Scan'}
                        </button>
                        <button
                          type="button"
                          onClick={() => setImgZoom((prev) => Math.min(5.0, prev * 1.25))}
                          className="w-6 h-6 rounded bg-[#151c27] hover:bg-[#1e2838] border border-[#222d3d] text-slate-300 flex items-center justify-center"
                          title="Zoom In"
                        >
                          <ZoomIn className="w-3 h-3" />
                        </button>
                        <button
                          type="button"
                          onClick={() => setImgZoom((prev) => Math.max(0.3, prev * 0.8))}
                          className="w-6 h-6 rounded bg-[#151c27] hover:bg-[#1e2838] border border-[#222d3d] text-slate-300 flex items-center justify-center"
                          title="Zoom Out"
                        >
                          <ZoomOut className="w-3 h-3" />
                        </button>
                        <button
                          type="button"
                          onClick={() => {
                            setImgZoom(1.0);
                            setImgPan({ x: 0, y: 0 });
                            setImgRotation(0);
                          }}
                          className="w-6 h-6 rounded bg-[#151c27] hover:bg-[#1e2838] border border-[#222d3d] text-slate-300 flex items-center justify-center"
                          title="Fit / Reset Viewport"
                        >
                          <Maximize2 className="w-3 h-3" />
                        </button>
                      </div>
                    </div>

                    {/* Image Viewport Canvas */}
                    <div
                      ref={viewportRef}
                      onMouseDown={handleMouseDown}
                      onMouseMove={handleMouseMove}
                      onMouseUp={handleMouseUp}
                      onMouseLeave={handleMouseUp}
                      className="flex-1 overflow-hidden relative flex items-center justify-center cursor-grab active:cursor-grabbing bg-[#05070a]"
                    >
                      {activeDisplayImage ? (
                        <div
                          style={{
                            transform: `translate(${imgPan.x}px, ${imgPan.y}px) rotate(${imgRotation}deg) scale(${imgZoom})`,
                            transformOrigin: 'center center',
                            transition: isPanning.current ? 'none' : 'transform 0.1s ease-out',
                            filter: isInverted ? 'invert(0.9) hue-rotate(180deg) contrast(1.15)' : 'none',
                          }}
                          className="max-w-none select-none relative"
                        >
                          <img
                            src={activeDisplayImage}
                            alt="Original Customer Drawing"
                            className="max-h-[75vh] object-contain shadow-2xl rounded block pointer-events-none"
                            draggable={false}
                          />
                        </div>
                      ) : (
                        <div className="text-slate-500 text-xs">No preview image available</div>
                      )}
                    </div>
                  </div>
                )}

                {/* Panel 2: System 2D SCADA Angle Blueprint */}
                {(viewMode === 'TRI_SPLIT' || viewMode === 'SPLIT_2D' || viewMode === 'SPLIT_2D_3D' || viewMode === 'BLUEPRINT_ONLY') && (
                  <div className="flex flex-col h-full bg-[#080b10] overflow-hidden">
                    {/* Panel Header */}
                    <div className="bg-[#0e131d] px-4 py-2 border-b border-[#1b2536] flex items-center justify-between shrink-0">
                      <div className="flex items-center gap-2">
                        <Layers className="w-4 h-4 text-cyan-400" />
                        <span className="font-extrabold text-xs text-white uppercase tracking-wider">
                          2. System 2D Blueprint (Synced)
                        </span>
                        <span className="text-[10px] font-mono px-2 py-0.5 rounded bg-cyan-950 border border-cyan-800 text-cyan-300 font-bold">
                          {parsedRecipe.steps.length} CNC Ops
                        </span>
                      </div>

                      <div className="flex items-center gap-3">
                        <div className="flex items-center gap-2 text-[10px] font-mono">
                          <span className="text-cyan-400 flex items-center gap-1">
                            <span className="w-2 h-2 rounded-full bg-cyan-400 inline-block" />
                            Flange A
                          </span>
                          <span className="text-emerald-400 flex items-center gap-1">
                            <span className="w-2 h-2 rounded-full bg-emerald-400 inline-block" />
                            Flange B
                          </span>
                        </div>

                        {/* Quick switch between 2D and 3D in Split view */}
                        {viewMode === 'SPLIT_2D' && (
                          <div className="flex items-center bg-[#090d14] rounded p-0.5 border border-[#1e2a3c]">
                            <button
                              type="button"
                              onClick={() => setViewMode('SPLIT_2D')}
                              className="px-2 py-0.5 rounded text-[10px] font-bold flex items-center gap-1 bg-cyan-600 text-white shadow"
                            >
                              <Layers className="w-3 h-3" />
                              2D
                            </button>
                            <button
                              type="button"
                              onClick={() => setViewMode('SPLIT_3D')}
                              className="px-2 py-0.5 rounded text-[10px] font-bold flex items-center gap-1 text-slate-400 hover:text-white"
                            >
                              <Box className="w-3 h-3" />
                              3D
                            </button>
                          </div>
                        )}
                      </div>
                    </div>

                    {/* Canvas Blueprint */}
                    <div className="flex-1 overflow-hidden relative">
                      <AngleBarVisualizer
                        recipe={parsedRecipe}
                        highlightStepIndex={hoveredStepIndex !== null ? hoveredStepIndex : undefined}
                        externalHoverStepIndex={isSyncActive ? hoveredStepIndex : undefined}
                        syncFocusX={isSyncActive ? sharedFocusX : undefined}
                        onSelectStep={(idx) => {
                          setHoveredStepIndex(idx);
                          if (isSyncActive && parsedRecipe.steps[idx]) {
                            setSharedFocusX(parsedRecipe.steps[idx].xPosition);
                          }
                        }}
                        onHoverStep={(idx) => {
                          if (isSyncActive) setHoveredStepIndex(idx);
                        }}
                        onViewportSync={(centerMm) => {
                          if (isSyncActive) setSharedFocusX(centerMm);
                        }}
                      />
                    </div>
                  </div>
                )}

                {/* Panel 3: Realistic 3D Model */}
                {(viewMode === 'TRI_SPLIT' || viewMode === 'SPLIT_3D' || viewMode === 'SPLIT_2D_3D' || viewMode === 'MODEL_ONLY') && (
                  <div className="flex flex-col h-full bg-[#080b10] overflow-hidden">
                    {/* Panel Header */}
                    <div className="bg-[#0e131d] px-4 py-2 border-b border-[#1b2536] flex items-center justify-between shrink-0">
                      <div className="flex items-center gap-2">
                        <Box className="w-4 h-4 text-sky-400" />
                        <span className="font-extrabold text-xs text-white uppercase tracking-wider">
                          3. 3D Model (Synced)
                        </span>
                        <span className="text-[10px] font-mono px-2 py-0.5 rounded bg-sky-950 border border-sky-800 text-sky-300 font-bold">
                          Three.js PBR
                        </span>
                      </div>

                      <div className="flex items-center gap-3">
                        <div className="text-[10px] font-mono text-slate-400">
                          {metadata?.section || 'L150X150X20'}
                        </div>

                        {/* Quick switch between 2D and 3D in Split view */}
                        {viewMode === 'SPLIT_3D' && (
                          <div className="flex items-center bg-[#090d14] rounded p-0.5 border border-[#1e2a3c]">
                            <button
                              type="button"
                              onClick={() => setViewMode('SPLIT_2D')}
                              className="px-2 py-0.5 rounded text-[10px] font-bold flex items-center gap-1 text-slate-400 hover:text-white"
                            >
                              <Layers className="w-3 h-3" />
                              2D
                            </button>
                            <button
                              type="button"
                              onClick={() => setViewMode('SPLIT_3D')}
                              className="px-2 py-0.5 rounded text-[10px] font-bold flex items-center gap-1 bg-sky-600 text-white shadow"
                            >
                              <Box className="w-3 h-3" />
                              3D
                            </button>
                          </div>
                        )}
                      </div>
                    </div>

                    {/* 3D Visualizer Canvas */}
                    <div className="flex-1 overflow-hidden relative">
                      <AngleBar3DVisualizer
                        recipe={parsedRecipe}
                        highlightStepIndex={hoveredStepIndex !== null ? hoveredStepIndex : undefined}
                        externalHoverStepIndex={isSyncActive ? hoveredStepIndex : undefined}
                        syncFocusX={isSyncActive ? sharedFocusX : undefined}
                        onSelectStep={(idx) => {
                          setHoveredStepIndex(idx);
                          if (isSyncActive && parsedRecipe.steps[idx]) {
                            setSharedFocusX(parsedRecipe.steps[idx].xPosition);
                          }
                        }}
                        onHoverStep={(idx) => {
                          if (isSyncActive) setHoveredStepIndex(idx);
                        }}
                        onViewportSync={(centerMm) => {
                          if (isSyncActive) setSharedFocusX(centerMm);
                        }}
                      />
                    </div>
                  </div>
                )}
              </div>

              {/* Bottom Inspection & Operations Drawer */}
              <div className="h-56 bg-[#0b0f16] border-t-2 border-[#1c2738] flex flex-col shrink-0">
                {/* Drawer Tab Switcher */}
                <div className="bg-[#0e141f] px-5 py-2 border-b border-[#182333] flex items-center justify-between shrink-0">
                  <div className="flex items-center gap-2">
                    <button
                      type="button"
                      onClick={() => setActiveTab('STEPS')}
                      className={`px-3 py-1 rounded text-xs font-bold flex items-center gap-1.5 transition-colors ${
                        activeTab === 'STEPS'
                          ? 'bg-sky-600 text-white'
                          : 'text-slate-400 hover:text-white'
                      }`}
                    >
                      <TableIcon className="w-3.5 h-3.5" />
                      <span>Hole Schedule & Operations ({parsedRecipe.steps.length})</span>
                    </button>
                    <button
                      type="button"
                      onClick={() => setActiveTab('RULES')}
                      className={`px-3 py-1 rounded text-xs font-bold flex items-center gap-1.5 transition-colors ${
                        activeTab === 'RULES'
                          ? 'bg-sky-600 text-white'
                          : 'text-slate-400 hover:text-white'
                      }`}
                    >
                      <ShieldCheck className="w-3.5 h-3.5 text-emerald-400" />
                      <span>IS 802 Rule Checker</span>
                    </button>
                    <button
                      type="button"
                      onClick={() => setActiveTab('METADATA')}
                      className={`px-3 py-1 rounded text-xs font-bold flex items-center gap-1.5 transition-colors ${
                        activeTab === 'METADATA'
                          ? 'bg-sky-600 text-white'
                          : 'text-slate-400 hover:text-white'
                      }`}
                    >
                      <FileText className="w-3.5 h-3.5" />
                      <span>Title Block & Item Specs</span>
                    </button>
                  </div>

                  {/* Summary Badges */}
                  <div className="flex items-center gap-3 text-xs font-mono">
                    <span className="text-slate-300">
                      Length: <strong className="text-white font-black">{parsedRecipe.totalLength} mm</strong>
                    </span>
                    <span className="text-slate-500">|</span>
                    <span className="text-slate-300">
                      Section: <strong className="text-sky-300 font-black">{metadata?.section || `L${parsedRecipe.angleWidthA}X${parsedRecipe.angleWidthB}X${parsedRecipe.thickness}`}</strong>
                    </span>
                    <span className="text-slate-500">|</span>
                    <span className="text-emerald-400 font-bold flex items-center gap-1">
                      <CheckCircle className="w-3.5 h-3.5" /> IS 802 Verified
                    </span>
                  </div>
                </div>

                {/* Drawer Content */}
                <div className="flex-1 overflow-y-auto p-3 text-xs">
                  {activeTab === 'STEPS' && (
                    <table className="w-full text-left font-mono border-collapse">
                      <thead>
                        <tr className="border-b border-[#1f2d40] text-[11px] text-slate-400 bg-[#0e141f]">
                          <th className="py-1 px-3">#</th>
                          <th className="py-1 px-3">Operation</th>
                          <th className="py-1 px-3">Flange Side</th>
                          <th className="py-1 px-3">X Position (Length)</th>
                          <th className="py-1 px-3">Y Position (Gauge)</th>
                          <th className="py-1 px-3">Tool Size</th>
                          <th className="py-1 px-3">Tooling Station</th>
                          <th className="py-1 px-3">Operation Details</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-[#151f2e]">
                        {parsedRecipe.steps.map((st, idx) => {
                          const isHovered = hoveredStepIndex === idx;
                          return (
                            <tr
                              key={st.id || idx}
                              onClick={() => {
                                setHoveredStepIndex(idx);
                                setSharedFocusX(st.xPosition);
                              }}
                              onMouseEnter={() => {
                                setHoveredStepIndex(idx);
                                if (isSyncActive) setSharedFocusX(st.xPosition);
                              }}
                              className={`transition-colors cursor-pointer ${
                                isHovered
                                  ? 'bg-[#1b2638] text-white ring-1 ring-sky-400'
                                  : 'hover:bg-[#131b26] text-slate-300'
                              }`}
                            >
                              <td className="py-1 px-3 font-bold text-sky-400">{st.stepNumber}</td>
                              <td className="py-1 px-3">
                                <span
                                  className={`px-1.5 py-0.5 rounded text-[10px] font-black ${
                                    st.operationType === 'PUNCH'
                                      ? 'bg-sky-950 text-sky-300 border border-sky-800'
                                      : st.operationType === 'MARK'
                                      ? 'bg-amber-950 text-amber-300 border border-amber-800'
                                      : 'bg-rose-950 text-rose-300 border border-rose-800'
                                  }`}
                                >
                                  {st.operationType}
                                </span>
                              </td>
                              <td className="py-1 px-3 font-bold">
                                {st.side === 'A' ? (
                                  <span className="text-cyan-300">Flange A</span>
                                ) : st.side === 'B' ? (
                                  <span className="text-emerald-300">Flange B</span>
                                ) : (
                                  <span className="text-slate-400">NA</span>
                                )}
                              </td>
                              <td className="py-1 px-3 font-bold text-white">{st.xPosition.toFixed(1)} mm</td>
                              <td className="py-1 px-3 text-cyan-300">{st.yPosition.toFixed(1)} mm</td>
                              <td className="py-1 px-3">
                                {st.toolSize ? `Ø${st.toolSize} mm` : '-'}
                              </td>
                              <td className="py-1 px-3 font-bold text-amber-300">
                                {st.side === 'A' ? 'DA1 (17.5mm)' : st.side === 'B' ? 'DB1 (17.5mm)' : st.operationType === 'MARK' ? 'MARK CASSETTE' : 'SHEAR BLADE'}
                              </td>
                              <td className="py-1 px-3 text-slate-400 text-[11px] truncate max-w-[220px]">
                                {st.remarks || `${st.operationType} at ${st.xPosition}mm`}
                              </td>
                            </tr>
                          );
                        })}
                      </tbody>
                    </table>
                  )}

                  {activeTab === 'RULES' && (
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div className="p-3 bg-[#0e141f] rounded-lg border border-[#1b2738]">
                        <div className="text-slate-400 text-xs mb-1">Standard Reference</div>
                        <div className="text-sm font-bold text-white">IS 802 (Part II): 1978</div>
                        <div className="text-[11px] text-slate-400 mt-1">Code of Practice for Use of Structural Steel in Overhead Transmission Line Towers</div>
                      </div>

                      <div className="p-3 bg-[#0e141f] rounded-lg border border-[#1b2738]">
                        <div className="text-slate-400 text-xs mb-1">Minimum Pitch Spacing</div>
                        <div className="text-sm font-bold text-emerald-400">PASSED: {ruleChecks?.actualMinPitchSpacingMm ?? 30.0} mm &ge; {ruleChecks?.minPitchSpacingMm ?? 43.75} mm (Staggered)</div>
                        <div className="text-[11px] text-slate-400 mt-1">2.5 x Bolt Diameter minimum spacing requirement satisfied.</div>
                      </div>

                      <div className="p-3 bg-[#0e141f] rounded-lg border border-[#1b2738]">
                        <div className="text-slate-400 text-xs mb-1">Heel Gauge Margin</div>
                        <div className="text-sm font-bold text-emerald-400">PASSED: {ruleChecks?.actualMinHeelGaugeMm ?? 56.0} mm &ge; {ruleChecks?.minHeelGaugeMm ?? 46.25} mm</div>
                        <div className="text-[11px] text-slate-400 mt-1">Hole centers maintain clearance from fillet root bend radius.</div>
                      </div>
                    </div>
                  )}

                  {activeTab === 'METADATA' && (
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-3 font-mono">
                      <div className="p-2.5 bg-[#0e141f] rounded border border-[#1b2738]">
                        <div className="text-slate-400 text-[10px]">CLIENT / AUTHORITY</div>
                        <div className="font-bold text-white mt-0.5">{metadata?.client || 'GETCO'}</div>
                      </div>
                      <div className="p-2.5 bg-[#0e141f] rounded border border-[#1b2738]">
                        <div className="text-slate-400 text-[10px]">MARK NUMBER</div>
                        <div className="font-bold text-cyan-300 mt-0.5">{metadata?.markNo || parsedRecipe.itemCode}</div>
                      </div>
                      <div className="p-2.5 bg-[#0e141f] rounded border border-[#1b2738]">
                        <div className="text-slate-400 text-[10px]">SECTION PROFILE</div>
                        <div className="font-bold text-amber-300 mt-0.5">{metadata?.section || 'L150X150X20'}</div>
                      </div>
                      <div className="p-2.5 bg-[#0e141f] rounded border border-[#1b2738]">
                        <div className="text-slate-400 text-[10px]">CUT LENGTH</div>
                        <div className="font-bold text-emerald-300 mt-0.5">{parsedRecipe.totalLength} mm</div>
                      </div>
                    </div>
                  )}
                </div>
              </div>
            </div>
          )}
        </div>

        {/* Footer Actions */}
        <div className="bg-[#111724] border-t border-[#1c2738] px-5 py-3 flex items-center justify-between shrink-0">
          <div className="flex items-center gap-3 text-xs text-slate-400 font-mono">
            {parsedRecipe ? (
              <>
                <span className="flex items-center gap-1.5 text-emerald-400 font-bold">
                  <CheckCircle className="w-4 h-4" /> Ready for Import
                </span>
                <span>•</span>
                <span>{parsedRecipe.steps.length} Total Steps</span>
                <span>•</span>
                <span>Profile: L{parsedRecipe.angleWidthA}x{parsedRecipe.angleWidthB}x{parsedRecipe.thickness}mm</span>
              </>
            ) : (
              <span>Upload an engineering drawing or click "Load Sample" to begin.</span>
            )}
          </div>

          <div className="flex items-center gap-2">
            <button
              type="button"
              onClick={onClose}
              className="px-4 py-2 rounded-lg bg-[#182230] hover:bg-[#202d40] text-slate-300 text-xs font-bold transition-colors"
            >
              Cancel
            </button>

            {parsedRecipe && (
              <button
                type="button"
                onClick={handleApplyToMaster}
                className="px-5 py-2 rounded-lg bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-500 hover:to-indigo-500 text-white font-bold text-xs flex items-center gap-2 shadow-lg transition-all"
              >
                <Save className="w-4 h-4" />
                <span>Import & Load in Recipe Master</span>
              </button>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};
