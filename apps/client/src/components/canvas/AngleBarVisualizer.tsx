import React, { useRef, useEffect, useState, useCallback } from 'react';
import { ItemRecipe } from '@innovance-hmi/shared';
import {
  RotateCcw,
  ArrowLeft,
  ArrowRight,
  AlignCenter,
  ZoomIn,
  ZoomOut,
  Shuffle,
  Eye,
  EyeOff,
} from 'lucide-react';

interface AngleBarVisualizerProps {
  recipe?: ItemRecipe | null;
  activeFeedPosition?: number;
  highlightStepIndex?: number;
  onSelectStep?: (stepIndex: number) => void;
}

// Fallback demo recipe if none provided
const DEFAULT_DEMO_RECIPE: ItemRecipe = {
  id: 'demo-recipe',
  itemCode: 'L75x75x6 - 1500mm',
  itemName: 'Transmission Tower Standard Angle',
  totalLength: 1500.0,
  angleWidthA: 75.0,
  angleWidthB: 75.0,
  thickness: 6.0,
  measurementType: 'ABSOLUTE',
  isActive: true,
  createdAt: new Date().toISOString(),
  updatedAt: new Date().toISOString(),
  steps: [
    { id: 's1', stepNumber: 1, operationType: 'PUNCH', side: 'A', xPosition: 120.0, yPosition: 35.0, toolSize: 18.0, isCutOff: false },
    { id: 's2', stepNumber: 2, operationType: 'PUNCH', side: 'A', xPosition: 350.0, yPosition: 35.0, toolSize: 18.0, isCutOff: false },
    { id: 's3', stepNumber: 3, operationType: 'MARK', side: 'A', xPosition: 500.0, yPosition: 35.0, markingText: 'T1-L15', isCutOff: false },
    { id: 's4', stepNumber: 4, operationType: 'PUNCH', side: 'B', xPosition: 200.0, yPosition: 35.0, toolSize: 18.0, isCutOff: false },
    { id: 's5', stepNumber: 5, operationType: 'PUNCH', side: 'B', xPosition: 450.0, yPosition: 35.0, toolSize: 18.0, isCutOff: false },
    { id: 's6', stepNumber: 6, operationType: 'CUT', side: 'NA', xPosition: 1500.0, yPosition: 0, isCutOff: true },
  ],
};

export const AngleBarVisualizer: React.FC<AngleBarVisualizerProps> = ({
  recipe,
  activeFeedPosition = 0,
  highlightStepIndex,
  onSelectStep,
}) => {
  const canvasRef = useRef<HTMLCanvasElement | null>(null);
  const containerRef = useRef<HTMLDivElement | null>(null);

  const activeRecipe = recipe && recipe.steps && recipe.steps.length > 0 ? recipe : DEFAULT_DEMO_RECIPE;

  // Viewport transformations
  const [zoom, setZoom] = useState<number>(1.0);
  const [panX, setPanX] = useState<number>(60);
  const [panY, setPanY] = useState<number>(0);
  const [isFlipped, setIsFlipped] = useState<boolean>(false);
  const [showDimensions, setShowDimensions] = useState<boolean>(true);
  const [hoveredStep, setHoveredStep] = useState<any | null>(null);
  const [mousePos, setMousePos] = useState<{ x: number; y: number } | null>(null);

  const lengthMm = activeRecipe.totalLength || 1500;
  const widthA = activeRecipe.angleWidthA || 75;
  const widthB = activeRecipe.angleWidthB || 75;

  const handleReset = () => {
    setZoom(1.0);
    setPanX(60);
    setPanY(0);
    setIsFlipped(false);
  };

  const handleFit = useCallback(() => {
    if (!containerRef.current) return;
    const width = containerRef.current.clientWidth;
    const targetScale = (width - 140) / lengthMm;
    setZoom(Math.max(0.15, Math.min(2.5, targetScale)));
    setPanX(60);
    setPanY(0);
  }, [lengthMm]);

  useEffect(() => {
    handleFit();
  }, [handleFit]);

  // Main Canvas Render
  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    ctx.scale(dpr, dpr);

    const w = rect.width;
    const h = rect.height;

    // 1. Dark Blueprint Background
    ctx.fillStyle = '#0a0e14';
    ctx.fillRect(0, 0, w, h);

    // 2. Blueprint Grid Lines
    const gridSize = 25 * Math.max(0.6, zoom);
    ctx.strokeStyle = 'rgba(56, 189, 248, 0.07)';
    ctx.lineWidth = 1;
    ctx.beginPath();
    for (let x = (panX % gridSize); x < w; x += gridSize) {
      ctx.moveTo(x, 0);
      ctx.lineTo(x, h);
    }
    for (let y = (h / 2 + panY) % gridSize; y < h; y += gridSize) {
      ctx.moveTo(0, y);
      ctx.lineTo(w, y);
    }
    ctx.stroke();

    // 3. Geometry Setup with comfortable visual flange heights
    const centerY = h / 2 + panY;
    const scaleX = zoom;
    // Keep flange visual height at a readable, proportioned size (at least 60-80px per flange)
    const flangeVisualHeight = Math.max(55, Math.min(100, 75 * Math.max(0.9, zoom)));
    const scaleY = flangeVisualHeight / widthA;

    const startX = panX;
    const barPixelLength = lengthMm * scaleX;

    const topFlangeHeight = (isFlipped ? widthB : widthA) * scaleY;
    const bottomFlangeHeight = (isFlipped ? widthA : widthB) * scaleY;

    const topFlangeY = centerY - topFlangeHeight;
    const bottomFlangeY = centerY + bottomFlangeHeight;

    // 4. Render Steel Angle Bar Flanges (Distinct Metallic Blue-Grey)
    // Top Flange
    const topGradient = ctx.createLinearGradient(0, topFlangeY, 0, centerY);
    topGradient.addColorStop(0, '#1c2836');
    topGradient.addColorStop(1, '#27384c');
    ctx.fillStyle = topGradient;
    ctx.fillRect(startX, topFlangeY, barPixelLength, topFlangeHeight);

    // Bottom Flange
    const bottomGradient = ctx.createLinearGradient(0, centerY, 0, bottomFlangeY);
    bottomGradient.addColorStop(0, '#27384c');
    bottomGradient.addColorStop(1, '#1c2836');
    ctx.fillStyle = bottomGradient;
    ctx.fillRect(startX, centerY, barPixelLength, bottomFlangeHeight);

    // Steel Outer Perimeter Border
    ctx.strokeStyle = '#38bdf8';
    ctx.lineWidth = 2;
    ctx.strokeRect(startX, topFlangeY, barPixelLength, topFlangeHeight + bottomFlangeHeight);

    // 5. Bend Heel Datum Center Fold Line
    ctx.strokeStyle = '#f59e0b';
    ctx.lineWidth = 2;
    ctx.setLineDash([8, 5]);
    ctx.beginPath();
    ctx.moveTo(startX, centerY);
    ctx.lineTo(startX + barPixelLength, centerY);
    ctx.stroke();
    ctx.setLineDash([]);

    // Datum Label
    ctx.font = 'bold 9px monospace';
    ctx.fillStyle = '#f59e0b';
    ctx.fillText('◄ BEND HEEL DATUM ►', startX + barPixelLength + 10, centerY + 3);

    // 6. Dimension Annotations
    if (showDimensions) {
      ctx.font = 'bold 11px sans-serif';
      ctx.fillStyle = '#7dd3fc';
      ctx.fillText(
        `▲ FLANGE ${isFlipped ? 'B' : 'A'} (${isFlipped ? widthB : widthA} mm)`,
        startX + 12,
        topFlangeY - 8
      );

      ctx.fillStyle = '#86efac';
      ctx.fillText(
        `▼ FLANGE ${isFlipped ? 'A' : 'B'} (${isFlipped ? widthA : widthB} mm)`,
        startX + 12,
        bottomFlangeY + 18
      );

      // Total Length Dimension Bar
      const dimY = bottomFlangeY + 34;
      ctx.strokeStyle = '#94a3b8';
      ctx.lineWidth = 1;
      ctx.beginPath();
      // Left tick
      ctx.moveTo(startX, dimY - 6);
      ctx.lineTo(startX, dimY + 6);
      // Right tick
      ctx.moveTo(startX + barPixelLength, dimY - 6);
      ctx.lineTo(startX + barPixelLength, dimY + 6);
      // Horizontal span
      ctx.moveTo(startX, dimY);
      ctx.lineTo(startX + barPixelLength, dimY);
      ctx.stroke();

      ctx.fillStyle = '#ffffff';
      ctx.font = 'bold 11px monospace';
      ctx.textAlign = 'center';
      ctx.fillText(`TOTAL LENGTH: ${lengthMm} mm`, startX + barPixelLength / 2, dimY - 4);
      ctx.textAlign = 'left';
    }

    // 7. Render Punch Holes, Markings, and Cut Lines
    if (activeRecipe.steps) {
      activeRecipe.steps.forEach((step, idx) => {
        const isHighlight = highlightStepIndex === idx;
        const opX = startX + step.xPosition * scaleX;

        let opY = centerY;
        if (step.side === 'A') {
          opY = isFlipped
            ? centerY + step.yPosition * scaleY
            : centerY - step.yPosition * scaleY;
        } else if (step.side === 'B') {
          opY = isFlipped
            ? centerY - step.yPosition * scaleY
            : centerY + step.yPosition * scaleY;
        }

        if (step.operationType === 'CUT' || step.isCutOff) {
          // Hydraulic Shear Cut Line
          ctx.strokeStyle = '#ff3366';
          ctx.lineWidth = isHighlight ? 4 : 2.5;
          ctx.beginPath();
          ctx.moveTo(opX, topFlangeY - 6);
          ctx.lineTo(opX, bottomFlangeY + 6);
          ctx.stroke();

          ctx.fillStyle = '#ff3366';
          ctx.font = 'bold 10px monospace';
          ctx.fillText('SHEAR CUT', opX - 28, topFlangeY - 10);
        } else if (step.operationType === 'MARK') {
          // Marking Stamp
          const boxW = 50;
          const boxH = 18;
          ctx.fillStyle = isHighlight ? '#fbbf24' : '#f59e0b';
          ctx.strokeStyle = '#ffffff';
          ctx.lineWidth = 1.5;
          ctx.fillRect(opX - boxW / 2, opY - boxH / 2, boxW, boxH);
          ctx.strokeRect(opX - boxW / 2, opY - boxH / 2, boxW, boxH);

          ctx.fillStyle = '#0f172a';
          ctx.font = 'bold 9px monospace';
          ctx.textAlign = 'center';
          ctx.fillText(step.markingText || 'T1-L15', opX, opY + 3.5);
          ctx.textAlign = 'left';
        } else {
          // Punch Hole Die
          const radius = Math.max(7, Math.min(16, ((step.toolSize || 18) / 2) * scaleY * 1.3));

          // Outer Glow / Ring
          ctx.fillStyle = step.side === 'A' ? '#00e5ff' : '#00e676';
          ctx.strokeStyle = isHighlight ? '#ffffff' : '#0f172a';
          ctx.lineWidth = isHighlight ? 3 : 2;

          ctx.beginPath();
          ctx.arc(opX, opY, radius, 0, Math.PI * 2);
          ctx.fill();
          ctx.stroke();

          // Crosshair Center
          ctx.strokeStyle = '#0a1017';
          ctx.lineWidth = 1.5;
          ctx.beginPath();
          ctx.moveTo(opX - radius - 2, opY);
          ctx.lineTo(opX + radius + 2, opY);
          ctx.moveTo(opX, opY - radius - 2);
          ctx.lineTo(opX, opY + radius + 2);
          ctx.stroke();

          // Tool Diameter Text
          ctx.fillStyle = '#ffffff';
          ctx.font = 'bold 9px sans-serif';
          ctx.fillText(`Ø${step.toolSize || 18}`, opX - 9, opY - radius - 4);
        }
      });
    }

    // 8. Live Animated Carriage Feed Laser Line
    if (activeFeedPosition >= 0) {
      const laserX = startX + activeFeedPosition * scaleX;
      ctx.strokeStyle = '#00ffcc';
      ctx.lineWidth = 2.5;
      ctx.shadowColor = '#00ffcc';
      ctx.shadowBlur = 10;
      ctx.beginPath();
      ctx.moveTo(laserX, 0);
      ctx.lineTo(laserX, h);
      ctx.stroke();
      ctx.shadowBlur = 0;

      // Laser Tag Badge
      ctx.fillStyle = '#00ffcc';
      ctx.fillRect(laserX - 35, 8, 70, 20);
      ctx.fillStyle = '#0a0e14';
      ctx.font = 'bold 10px monospace';
      ctx.textAlign = 'center';
      ctx.fillText(`X: ${activeFeedPosition.toFixed(1)}mm`, laserX, 22);
      ctx.textAlign = 'left';
    }
  }, [activeRecipe, activeFeedPosition, highlightStepIndex, zoom, panX, panY, isFlipped, showDimensions, widthA, widthB, lengthMm]);

  // Mouse Move Tooltip
  const handleMouseMove = (e: React.MouseEvent<HTMLCanvasElement>) => {
    const canvas = canvasRef.current;
    if (!canvas || !activeRecipe.steps) return;
    const rect = canvas.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;
    setMousePos({ x: e.clientX, y: e.clientY });

    const centerY = rect.height / 2 + panY;
    const scaleX = zoom;
    const flangeVisualHeight = Math.max(55, Math.min(100, 75 * Math.max(0.9, zoom)));
    const scaleY = flangeVisualHeight / widthA;
    const startX = panX;

    const foundIndex = activeRecipe.steps.findIndex((step) => {
      const opX = startX + step.xPosition * scaleX;
      let opY = centerY;
      if (step.side === 'A') {
        opY = isFlipped ? centerY + step.yPosition * scaleY : centerY - step.yPosition * scaleY;
      } else if (step.side === 'B') {
        opY = isFlipped ? centerY - step.yPosition * scaleY : centerY + step.yPosition * scaleY;
      }
      const dist = Math.hypot(mouseX - opX, mouseY - opY);
      return dist < 20;
    });

    if (foundIndex >= 0) {
      setHoveredStep(activeRecipe.steps[foundIndex]);
      if (onSelectStep) onSelectStep(foundIndex);
    } else {
      setHoveredStep(null);
    }
  };

  return (
    <div ref={containerRef} className="relative w-full h-full flex flex-col bg-[#0a0e14] rounded overflow-hidden select-none border border-slate-700">
      {/* 1. CAD Visualizer Toolbar */}
      <div className="bg-[#141b22] px-3 py-1.5 border-b border-slate-700 flex items-center justify-between z-10">
        <div className="flex items-center gap-1">
          <button onClick={handleReset} title="Reset View" className="btn-ca btn-ca-dark text-xs py-1 px-2">
            <RotateCcw className="w-3.5 h-3.5" />
          </button>
          <button onClick={() => setPanX((p) => p + 60)} title="Pan Left" className="btn-ca btn-ca-dark text-xs py-1 px-2">
            <ArrowLeft className="w-3.5 h-3.5" />
          </button>
          <button onClick={() => setPanX((p) => p - 60)} title="Pan Right" className="btn-ca btn-ca-dark text-xs py-1 px-2">
            <ArrowRight className="w-3.5 h-3.5" />
          </button>
          <button onClick={handleFit} title="Fit to Viewport Width" className="btn-ca btn-ca-dark text-xs py-1 px-2">
            <AlignCenter className="w-3.5 h-3.5" /> Fit
          </button>
          <button onClick={() => setZoom((z) => Math.min(2.5, z * 1.25))} title="Zoom In" className="btn-ca btn-ca-dark text-xs py-1 px-2">
            <ZoomIn className="w-3.5 h-3.5" />
          </button>
          <button onClick={() => setZoom((z) => Math.max(0.15, z / 1.25))} title="Zoom Out" className="btn-ca btn-ca-dark text-xs py-1 px-2">
            <ZoomOut className="w-3.5 h-3.5" />
          </button>
          <button onClick={() => setIsFlipped((f) => !f)} title="Flip Flange A/B" className={`btn-ca text-xs py-1 px-2 ${isFlipped ? 'btn-ca-primary' : 'btn-ca-dark'}`}>
            <Shuffle className="w-3.5 h-3.5" /> Flip
          </button>
          <button onClick={() => setShowDimensions((d) => !d)} title="Toggle Dimensions" className="btn-ca btn-ca-dark text-xs py-1 px-2">
            {showDimensions ? <Eye className="w-3.5 h-3.5" /> : <EyeOff className="w-3.5 h-3.5" />}
          </button>
        </div>

        {/* Legend */}
        <div className="flex items-center gap-3 text-[11px] font-semibold text-slate-300">
          <div className="flex items-center gap-1.5">
            <span className="w-2.5 h-2.5 rounded-full bg-[#00e5ff] inline-block" /> Flange A (DA1-3)
          </div>
          <div className="flex items-center gap-1.5">
            <span className="w-2.5 h-2.5 rounded-full bg-[#00e676] inline-block" /> Flange B (DB1-3)
          </div>
          <div className="flex items-center gap-1.5">
            <span className="w-2.5 h-2.5 rounded bg-[#f59e0b] inline-block" /> Marking
          </div>
          <div className="flex items-center gap-1.5">
            <span className="w-3 h-0.5 bg-[#ff3366] inline-block" /> Shear Cut
          </div>
        </div>
      </div>

      {/* 2. Canvas */}
      <canvas
        ref={canvasRef}
        onMouseMove={handleMouseMove}
        onMouseLeave={() => setHoveredStep(null)}
        className="w-full flex-1 cursor-crosshair min-h-[220px]"
      />

      {/* 3. Tooltip */}
      {hoveredStep && mousePos && (
        <div
          style={{
            position: 'fixed',
            left: mousePos.x + 12,
            top: mousePos.y + 12,
            pointerEvents: 'none',
          }}
          className="bg-slate-900 text-white text-xs p-2.5 rounded border border-slate-700 shadow-xl z-50 space-y-1"
        >
          <div className="font-bold text-cyan-400">Step #{hoveredStep.stepNumber} • {hoveredStep.operationType}</div>
          <div>Flange: <span className="font-semibold text-white">Side {hoveredStep.side}</span></div>
          <div>Coordinates: <span className="font-mono text-emerald-400 font-bold">X: {hoveredStep.xPosition}mm, Y: {hoveredStep.yPosition}mm</span></div>
          {hoveredStep.toolSize && <div>Die Tool: <span className="font-semibold">Ø{hoveredStep.toolSize}mm</span></div>}
        </div>
      )}
    </div>
  );
};
