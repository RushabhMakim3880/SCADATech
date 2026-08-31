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

export const AngleBarVisualizer: React.FC<AngleBarVisualizerProps> = ({
  recipe,
  activeFeedPosition = 0,
  highlightStepIndex,
  onSelectStep,
}) => {
  const canvasRef = useRef<HTMLCanvasElement | null>(null);
  const containerRef = useRef<HTMLDivElement | null>(null);

  // CAD Viewport Transformations
  const [zoom, setZoom] = useState<number>(1.0);
  const [panX, setPanX] = useState<number>(60);
  const [panY, setPanY] = useState<number>(0);
  const [isFlipped, setIsFlipped] = useState<boolean>(false);
  const [showDimensions, setShowDimensions] = useState<boolean>(true);
  const [hoveredStep, setHoveredStep] = useState<any | null>(null);
  const [mousePos, setMousePos] = useState<{ x: number; y: number } | null>(null);

  const lengthMm = recipe?.totalLength || 1500;
  const widthA = recipe?.angleWidthA || 75;
  const widthB = recipe?.angleWidthB || 75;

  const handleReset = () => {
    setZoom(1.0);
    setPanX(60);
    setPanY(0);
    setIsFlipped(false);
  };

  const handleFit = useCallback(() => {
    if (!containerRef.current) return;
    const width = containerRef.current.clientWidth;
    const targetScale = (width - 120) / lengthMm;
    setZoom(Math.max(0.2, Math.min(3.0, targetScale)));
    setPanX(60);
    setPanY(0);
  }, [lengthMm]);

  useEffect(() => {
    handleFit();
  }, [handleFit]);

  // Main Canvas Render Loop
  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // Handle high DPI
    const dpr = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * dpr;
    canvas.height = rect.height * dpr;
    ctx.scale(dpr, dpr);

    const w = rect.width;
    const h = rect.height;

    // 1. Blueprint Dark Background
    ctx.fillStyle = '#0f141a';
    ctx.fillRect(0, 0, w, h);

    // 2. CAD Grid
    const gridSize = 30 * zoom;
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.04)';
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

    // Coordinate System Setup
    const centerY = h / 2 + panY;
    const scale = zoom;
    const startX = panX;

    const renderWidthA = widthA * scale;
    const renderWidthB = widthB * scale;
    const barPixelLength = lengthMm * scale;

    const topFlangeY = centerY - (isFlipped ? renderWidthB : renderWidthA);
    const bottomFlangeY = centerY + (isFlipped ? renderWidthA : renderWidthB);

    // 3. Render Flange A (Top) and Flange B (Bottom) Steel Material
    ctx.fillStyle = 'rgba(15, 34, 56, 0.75)';
    ctx.fillRect(startX, topFlangeY, barPixelLength, (isFlipped ? renderWidthB : renderWidthA));

    ctx.fillStyle = 'rgba(18, 42, 68, 0.75)';
    ctx.fillRect(startX, centerY, barPixelLength, (isFlipped ? renderWidthA : renderWidthB));

    // Outer Perimeter Outline
    ctx.strokeStyle = '#38bdf8';
    ctx.lineWidth = 1.5;
    ctx.strokeRect(startX, topFlangeY, barPixelLength, renderWidthA + renderWidthB);

    // 4. Bend Heel Datum Line (Center Fold of Angle Bar)
    ctx.strokeStyle = '#f59e0b';
    ctx.lineWidth = 1.5;
    ctx.setLineDash([6, 4]);
    ctx.beginPath();
    ctx.moveTo(startX, centerY);
    ctx.lineTo(startX + barPixelLength, centerY);
    ctx.stroke();
    ctx.setLineDash([]);

    // Datum Text
    ctx.font = 'bold 9px monospace';
    ctx.fillStyle = '#f59e0b';
    ctx.fillText('BEND HEEL DATUM', startX + barPixelLength + 8, centerY + 3);

    // 5. Dimension Callouts
    if (showDimensions) {
      ctx.font = 'bold 10px sans-serif';
      ctx.fillStyle = '#7dd3fc';
      ctx.fillText(`FLANGE ${isFlipped ? 'B' : 'A'} (W: ${isFlipped ? widthB : widthA}mm)`, startX + 10, topFlangeY - 8);

      ctx.fillStyle = '#86efac';
      ctx.fillText(`FLANGE ${isFlipped ? 'A' : 'B'} (W: ${isFlipped ? widthA : widthB}mm)`, startX + 10, bottomFlangeY + 16);

      // Total Length Arrow
      ctx.strokeStyle = 'rgba(255, 255, 255, 0.3)';
      ctx.beginPath();
      ctx.moveTo(startX, bottomFlangeY + 30);
      ctx.lineTo(startX + barPixelLength, bottomFlangeY + 30);
      ctx.stroke();

      ctx.fillStyle = '#ffffff';
      ctx.font = 'bold 11px monospace';
      ctx.textAlign = 'center';
      ctx.fillText(`TOTAL LENGTH: ${lengthMm} mm`, startX + barPixelLength / 2, bottomFlangeY + 26);
      ctx.textAlign = 'left';
    }

    // 6. Punch Holes, Markings, and Cut Lines
    if (recipe && recipe.steps) {
      recipe.steps.forEach((step, idx) => {
        const isHighlight = highlightStepIndex === idx;
        const opX = startX + step.xPosition * scale;

        let opY = centerY;
        if (step.side === 'A') {
          opY = isFlipped ? centerY + step.yPosition * scale : centerY - step.yPosition * scale;
        } else if (step.side === 'B') {
          opY = isFlipped ? centerY - step.yPosition * scale : centerY + step.yPosition * scale;
        }

        if (step.operationType === 'CUT' || step.isCutOff) {
          // Hydraulic Shear Cut Line
          ctx.strokeStyle = '#ef4444';
          ctx.lineWidth = isHighlight ? 3 : 2;
          ctx.beginPath();
          ctx.moveTo(opX, topFlangeY - 4);
          ctx.lineTo(opX, bottomFlangeY + 4);
          ctx.stroke();

          ctx.fillStyle = '#ef4444';
          ctx.font = 'bold 9px monospace';
          ctx.fillText('SHEAR CUT', opX - 25, topFlangeY - 8);
        } else if (step.operationType === 'MARK') {
          // Marking Stamp Box
          ctx.fillStyle = isHighlight ? '#fbbf24' : 'rgba(245, 158, 11, 0.85)';
          ctx.strokeStyle = '#ffffff';
          ctx.lineWidth = 1;
          const boxW = 45;
          const boxH = 14;
          ctx.fillRect(opX - boxW / 2, opY - boxH / 2, boxW, boxH);
          ctx.strokeRect(opX - boxW / 2, opY - boxH / 2, boxW, boxH);

          ctx.fillStyle = '#111827';
          ctx.font = 'bold 8px monospace';
          ctx.textAlign = 'center';
          ctx.fillText(step.markingText || 'T1-L15', opX, opY + 3);
          ctx.textAlign = 'left';
        } else {
          // Punch Hole Die
          const radius = Math.max(5, ((step.toolSize || 18) / 2) * scale);

          // Outer Glow / Ring
          ctx.fillStyle = step.side === 'A' ? 'rgba(56, 189, 248, 0.75)' : 'rgba(74, 222, 128, 0.75)';
          ctx.strokeStyle = isHighlight ? '#ffffff' : (step.side === 'A' ? '#38bdf8' : '#4ade80');
          ctx.lineWidth = isHighlight ? 2.5 : 1.5;

          ctx.beginPath();
          ctx.arc(opX, opY, radius, 0, Math.PI * 2);
          ctx.fill();
          ctx.stroke();

          // Crosshair Center
          ctx.strokeStyle = '#0f172a';
          ctx.lineWidth = 1;
          ctx.beginPath();
          ctx.moveTo(opX - radius, opY);
          ctx.lineTo(opX + radius, opY);
          ctx.moveTo(opX, opY - radius);
          ctx.lineTo(opX, opY + radius);
          ctx.stroke();

          // Diameter Text
          ctx.fillStyle = '#ffffff';
          ctx.font = 'bold 8px sans-serif';
          ctx.fillText(`Ø${step.toolSize || 18}`, opX - 8, opY - radius - 3);
        }
      });
    }

    // 7. Live Animated Feed Carriage Laser Indicator
    if (activeFeedPosition >= 0) {
      const laserX = startX + activeFeedPosition * scale;
      ctx.strokeStyle = '#00ffcc';
      ctx.lineWidth = 2;
      ctx.shadowColor = '#00ffcc';
      ctx.shadowBlur = 8;
      ctx.beginPath();
      ctx.moveTo(laserX, 0);
      ctx.lineTo(laserX, h);
      ctx.stroke();
      ctx.shadowBlur = 0;

      // Coordinate Laser Badge
      ctx.fillStyle = '#00ffcc';
      ctx.fillRect(laserX - 35, 10, 70, 18);
      ctx.fillStyle = '#0a0e14';
      ctx.font = 'bold 9px monospace';
      ctx.textAlign = 'center';
      ctx.fillText(`X: ${activeFeedPosition.toFixed(1)}mm`, laserX, 22);
      ctx.textAlign = 'left';
    }
  }, [recipe, activeFeedPosition, highlightStepIndex, zoom, panX, panY, isFlipped, showDimensions, widthA, widthB, lengthMm]);

  // Mouse Move for Tooltip
  const handleMouseMove = (e: React.MouseEvent<HTMLCanvasElement>) => {
    const canvas = canvasRef.current;
    if (!canvas || !recipe || !recipe.steps) return;
    const rect = canvas.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;
    setMousePos({ x: e.clientX, y: e.clientY });

    const centerY = rect.height / 2 + panY;
    const scale = zoom;
    const startX = panX;

    const foundIndex = recipe.steps.findIndex((step) => {
      const opX = startX + step.xPosition * scale;
      let opY = centerY;
      if (step.side === 'A') {
        opY = isFlipped ? centerY + step.yPosition * scale : centerY - step.yPosition * scale;
      } else if (step.side === 'B') {
        opY = isFlipped ? centerY - step.yPosition * scale : centerY + step.yPosition * scale;
      }
      const dist = Math.hypot(mouseX - opX, mouseY - opY);
      return dist < 18;
    });

    if (foundIndex >= 0) {
      setHoveredStep(recipe.steps[foundIndex]);
      if (onSelectStep) onSelectStep(foundIndex);
    } else {
      setHoveredStep(null);
    }
  };

  return (
    <div ref={containerRef} className="relative w-full h-full flex flex-col bg-[#0f141a] rounded overflow-hidden select-none border border-slate-700">
      {/* 1. CAD Visualizer Toolbar (Replicated from Original Program) */}
      <div className="bg-[#182028] px-3 py-1.5 border-b border-slate-700 flex items-center justify-between z-10">
        <div className="flex items-center gap-1">
          <button
            onClick={handleReset}
            title="Reset View"
            className="btn-ca btn-ca-dark text-xs py-1 px-2"
          >
            <RotateCcw className="w-3.5 h-3.5" />
          </button>
          <button
            onClick={() => setPanX((p) => p + 50)}
            title="Pan Left"
            className="btn-ca btn-ca-dark text-xs py-1 px-2"
          >
            <ArrowLeft className="w-3.5 h-3.5" />
          </button>
          <button
            onClick={() => setPanX((p) => p - 50)}
            title="Pan Right"
            className="btn-ca btn-ca-dark text-xs py-1 px-2"
          >
            <ArrowRight className="w-3.5 h-3.5" />
          </button>
          <button
            onClick={handleFit}
            title="Fit to Viewport Width"
            className="btn-ca btn-ca-dark text-xs py-1 px-2"
          >
            <AlignCenter className="w-3.5 h-3.5" /> Fit
          </button>
          <button
            onClick={() => setZoom((z) => Math.min(3.0, z * 1.2))}
            title="Zoom In"
            className="btn-ca btn-ca-dark text-xs py-1 px-2"
          >
            <ZoomIn className="w-3.5 h-3.5" />
          </button>
          <button
            onClick={() => setZoom((z) => Math.max(0.2, z / 1.2))}
            title="Zoom Out"
            className="btn-ca btn-ca-dark text-xs py-1 px-2"
          >
            <ZoomOut className="w-3.5 h-3.5" />
          </button>
          <button
            onClick={() => setIsFlipped((f) => !f)}
            title="Flip Flange A/B"
            className={`btn-ca text-xs py-1 px-2 ${isFlipped ? 'btn-ca-primary' : 'btn-ca-dark'}`}
          >
            <Shuffle className="w-3.5 h-3.5" /> Flip
          </button>
          <button
            onClick={() => setShowDimensions((d) => !d)}
            title="Toggle Dimensions"
            className="btn-ca btn-ca-dark text-xs py-1 px-2"
          >
            {showDimensions ? <Eye className="w-3.5 h-3.5" /> : <EyeOff className="w-3.5 h-3.5" />}
          </button>
        </div>

        {/* Legend */}
        <div className="flex items-center gap-3 text-[11px] font-semibold text-slate-300">
          <div className="flex items-center gap-1.5">
            <span className="w-2.5 h-2.5 rounded-full bg-cyan-400 inline-block" /> Flange A (DA1-3)
          </div>
          <div className="flex items-center gap-1.5">
            <span className="w-2.5 h-2.5 rounded-full bg-emerald-400 inline-block" /> Flange B (DB1-3)
          </div>
          <div className="flex items-center gap-1.5">
            <span className="w-2.5 h-2.5 rounded bg-amber-500 inline-block" /> Marking
          </div>
          <div className="flex items-center gap-1.5">
            <span className="w-3 h-0.5 bg-red-500 inline-block" /> Shear Cut
          </div>
        </div>
      </div>

      {/* 2. Interactive Canvas */}
      <canvas
        ref={canvasRef}
        onMouseMove={handleMouseMove}
        onMouseLeave={() => setHoveredStep(null)}
        className="w-full flex-1 cursor-crosshair"
      />

      {/* 3. Interactive CAD Tooltip */}
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
          <div>Flange Side: <span className="font-semibold text-white">Side {hoveredStep.side}</span></div>
          <div>Coordinates: <span className="font-mono text-emerald-400 font-bold">X: {hoveredStep.xPosition}mm, Y: {hoveredStep.yPosition}mm</span></div>
          {hoveredStep.toolSize && <div>Die Tool: <span className="font-semibold">Ø{hoveredStep.toolSize}mm</span></div>}
        </div>
      )}
    </div>
  );
};
