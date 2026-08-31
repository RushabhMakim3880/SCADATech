import React, { useRef, useEffect, useState } from 'react';
import { ItemRecipe } from '@innovance-hmi/shared';
import { ZoomIn, ZoomOut, Maximize2 } from 'lucide-react';

interface AngleBarVisualizerProps {
  recipe: Partial<ItemRecipe> | null;
  selectedStepIndex?: number | null;
  onSelectStep?: (index: number) => void;
  activeFeedPosition?: number; // Optional live production feed cursor in mm
}

export const AngleBarVisualizer: React.FC<AngleBarVisualizerProps> = ({
  recipe,
  selectedStepIndex,
  activeFeedPosition,
}) => {
  const canvasRef = useRef<HTMLCanvasElement | null>(null);
  const [scale, setScale] = useState<number>(0.8);
  const [panX, setPanX] = useState<number>(40);

  const totalLength = recipe?.totalLength || 1500;
  const widthA = recipe?.angleWidthA || 75;
  const widthB = recipe?.angleWidthB || 75;
  const steps = recipe?.steps || [];

  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // Resize canvas to parent container
    const parent = canvas.parentElement;
    if (parent) {
      canvas.width = parent.clientWidth;
      canvas.height = parent.clientHeight;
    }

    const w = canvas.width;
    const h = canvas.height;

    ctx.clearRect(0, 0, w, h);

    // Draw background grid
    ctx.strokeStyle = '#1e293b';
    ctx.lineWidth = 1;
    for (let x = 0; x < w; x += 40) {
      ctx.beginPath();
      ctx.moveTo(x, 0);
      ctx.lineTo(x, h);
      ctx.stroke();
    }
    for (let y = 0; y < h; y += 40) {
      ctx.beginPath();
      ctx.moveTo(0, y);
      ctx.lineTo(w, y);
      ctx.stroke();
    }

    const centerY = h / 2;
    const pixelsPerMm = scale * ((w - 120) / Math.max(1000, totalLength));
    const startX = panX;
    const barWidthPx = totalLength * pixelsPerMm;
    const flangeAPx = widthA * pixelsPerMm * 1.5;
    const flangeBPx = widthB * pixelsPerMm * 1.5;

    // 1. Draw Flange A (Top Wing)
    ctx.fillStyle = '#0f172a';
    ctx.strokeStyle = '#38bdf8';
    ctx.lineWidth = 2;
    ctx.fillRect(startX, centerY - flangeAPx, barWidthPx, flangeAPx);
    ctx.strokeRect(startX, centerY - flangeAPx, barWidthPx, flangeAPx);

    // 2. Draw Flange B (Bottom Wing)
    ctx.fillStyle = '#0f172a';
    ctx.strokeStyle = '#34d399';
    ctx.lineWidth = 2;
    ctx.fillRect(startX, centerY, barWidthPx, flangeBPx);
    ctx.strokeRect(startX, centerY, barWidthPx, flangeBPx);

    // 3. Draw Angle Bend Center Line
    ctx.strokeStyle = '#f59e0b';
    ctx.lineWidth = 2;
    ctx.setLineDash([6, 4]);
    ctx.beginPath();
    ctx.moveTo(startX, centerY);
    ctx.lineTo(startX + barWidthPx, centerY);
    ctx.stroke();
    ctx.setLineDash([]);

    // 4. Flange Labels
    ctx.font = 'bold 11px JetBrains Mono, monospace';
    ctx.fillStyle = '#38bdf8';
    ctx.fillText(`FLANGE A (Width: ${widthA}mm)`, startX + 10, centerY - flangeAPx + 16);
    ctx.fillStyle = '#34d399';
    ctx.fillText(`FLANGE B (Width: ${widthB}mm)`, startX + 10, centerY + flangeBPx - 10);
    ctx.fillStyle = '#f59e0b';
    ctx.fillText('HEEL / BEND AXIS', startX + barWidthPx - 130, centerY - 6);

    // 5. Draw Length Dimension Ruler along top
    ctx.strokeStyle = '#64748b';
    ctx.fillStyle = '#94a3b8';
    ctx.font = '10px JetBrains Mono, monospace';
    ctx.lineWidth = 1;

    const rulerY = centerY - flangeAPx - 20;
    ctx.beginPath();
    ctx.moveTo(startX, rulerY);
    ctx.lineTo(startX + barWidthPx, rulerY);
    ctx.stroke();

    // Ruler Ticks every 100mm / 500mm
    const tickStep = totalLength > 3000 ? 500 : 100;
    for (let mm = 0; mm <= totalLength; mm += tickStep) {
      const tx = startX + mm * pixelsPerMm;
      ctx.beginPath();
      ctx.moveTo(tx, rulerY - 5);
      ctx.lineTo(tx, rulerY + 5);
      ctx.stroke();
      if (mm % (tickStep * 2) === 0 || mm === totalLength) {
        ctx.fillText(`${mm}mm`, tx - 14, rulerY - 8);
      }
    }

    // 6. Draw Steps (Punch Holes, Marking, Cutter)
    steps.forEach((step, idx) => {
      const isSelected = selectedStepIndex === idx;
      const stepX = startX + step.xPosition * pixelsPerMm;

      if (step.operationType === 'PUNCH') {
        const isSideA = step.side === 'A';
        const holeRadius = Math.max(4, ((step.toolSize || 18) / 2) * pixelsPerMm * 1.5);
        const gaugeY = isSideA
          ? centerY - step.yPosition * pixelsPerMm * 1.5
          : centerY + step.yPosition * pixelsPerMm * 1.5;

        // Hole fill & outline
        ctx.beginPath();
        ctx.arc(stepX, gaugeY, holeRadius, 0, Math.PI * 2);
        ctx.fillStyle = isSelected ? '#f43f5e' : isSideA ? '#0284c7' : '#059669';
        ctx.fill();
        ctx.strokeStyle = isSelected ? '#ffe4e6' : '#ffffff';
        ctx.lineWidth = isSelected ? 3 : 1.5;
        ctx.stroke();

        // Hole Center Crosshair
        ctx.strokeStyle = '#ffffff';
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(stepX - holeRadius - 2, gaugeY);
        ctx.lineTo(stepX + holeRadius + 2, gaugeY);
        ctx.moveTo(stepX, gaugeY - holeRadius - 2);
        ctx.lineTo(stepX, gaugeY + holeRadius + 2);
        ctx.stroke();

        // Label
        ctx.fillStyle = '#f8fafc';
        ctx.font = 'bold 9px JetBrains Mono, monospace';
        ctx.fillText(`Ø${step.toolSize || 18}`, stepX - 10, gaugeY - holeRadius - 4);
      } else if (step.operationType === 'MARK') {
        // Marking Block
        const blockW = 70;
        const blockH = 22;
        ctx.fillStyle = isSelected ? '#be123c' : '#7c2d12';
        ctx.strokeStyle = '#fb923c';
        ctx.lineWidth = 1.5;
        ctx.fillRect(stepX - blockW / 2, centerY - blockH / 2, blockW, blockH);
        ctx.strokeRect(stepX - blockW / 2, centerY - blockH / 2, blockW, blockH);

        ctx.fillStyle = '#ffedd5';
        ctx.font = 'bold 10px JetBrains Mono, monospace';
        ctx.fillText(step.markingText || 'STAMP', stepX - blockW / 2 + 6, centerY + 4);
      } else if (step.operationType === 'CUT' || step.isCutOff) {
        // Cut-off line
        ctx.strokeStyle = '#ef4444';
        ctx.lineWidth = 3;
        ctx.setLineDash([4, 2]);
        ctx.beginPath();
        ctx.moveTo(stepX, centerY - flangeAPx - 10);
        ctx.lineTo(stepX, centerY + flangeBPx + 10);
        ctx.stroke();
        ctx.setLineDash([]);

        ctx.fillStyle = '#ef4444';
        ctx.font = 'bold 11px JetBrains Mono, monospace';
        ctx.fillText('✂ CUT-OFF', stepX - 25, centerY + flangeBPx + 22);
      }
    });

    // 7. Live Production Feed Cursor (if provided)
    if (activeFeedPosition !== undefined && activeFeedPosition >= 0) {
      const liveX = startX + activeFeedPosition * pixelsPerMm;
      ctx.strokeStyle = '#10b981';
      ctx.lineWidth = 3;
      ctx.beginPath();
      ctx.moveTo(liveX, 0);
      ctx.lineTo(liveX, h);
      ctx.stroke();

      ctx.fillStyle = '#10b981';
      ctx.font = 'bold 12px JetBrains Mono, monospace';
      ctx.fillText(`FEED: ${activeFeedPosition.toFixed(1)}mm`, liveX + 6, 24);
    }
  }, [recipe, scale, panX, selectedStepIndex, totalLength, widthA, widthB, steps, activeFeedPosition]);

  return (
    <div className="relative w-full h-full bg-slate-950 rounded-lg overflow-hidden border border-slate-800">
      <canvas ref={canvasRef} className="w-full h-full cursor-crosshair" />

      {/* Floating Canvas Controls */}
      <div className="absolute top-4 right-4 flex items-center gap-1.5 bg-slate-900/90 border border-slate-800 p-1 rounded-lg backdrop-blur shadow-lg">
        <button
          onClick={() => setScale((s) => Math.min(2.5, s + 0.15))}
          className="p-1.5 hover:bg-slate-800 rounded text-slate-300 hover:text-white"
          title="Zoom In"
        >
          <ZoomIn className="w-4 h-4" />
        </button>
        <button
          onClick={() => setScale((s) => Math.max(0.3, s - 0.15))}
          className="p-1.5 hover:bg-slate-800 rounded text-slate-300 hover:text-white"
          title="Zoom Out"
        >
          <ZoomOut className="w-4 h-4" />
        </button>
        <button
          onClick={() => {
            setScale(0.8);
            setPanX(40);
          }}
          className="p-1.5 hover:bg-slate-800 rounded text-slate-300 hover:text-white"
          title="Reset View"
        >
          <Maximize2 className="w-4 h-4" />
        </button>
      </div>

      {/* Footer Legend */}
      <div className="absolute bottom-3 left-4 flex items-center gap-4 text-[11px] font-mono text-slate-400 bg-slate-900/90 px-3 py-1.5 rounded border border-slate-800">
        <span className="flex items-center gap-1.5">
          <span className="w-2.5 h-2.5 rounded-full bg-cyan-500" /> Flange A Punch
        </span>
        <span className="flex items-center gap-1.5">
          <span className="w-2.5 h-2.5 rounded-full bg-emerald-500" /> Flange B Punch
        </span>
        <span className="flex items-center gap-1.5">
          <span className="w-2.5 h-2.5 rounded bg-amber-600" /> Marking Stamp
        </span>
        <span className="flex items-center gap-1.5">
          <span className="w-2.5 h-2.5 bg-rose-500" /> Shear Cut-Off
        </span>
      </div>
    </div>
  );
};
