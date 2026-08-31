import React, { useRef, useEffect, useState } from 'react';
import { ItemRecipe } from '@innovance-hmi/shared';
import { ZoomIn, ZoomOut, Maximize2, Crosshair } from 'lucide-react';

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
  const [scale, setScale] = useState<number>(0.85);
  const [panX, setPanX] = useState<number>(60);
  const [mouseCoord, setMouseCoord] = useState<{ xMm: number; flange: string } | null>(null);

  const totalLength = recipe?.totalLength || 1500;
  const widthA = recipe?.angleWidthA || 75;
  const widthB = recipe?.angleWidthB || 75;
  const steps = recipe?.steps || [];

  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const parent = canvas.parentElement;
    if (parent) {
      canvas.width = parent.clientWidth;
      canvas.height = parent.clientHeight;
    }

    const w = canvas.width;
    const h = canvas.height;

    // 1. Dark CAD Blueprint Background
    ctx.fillStyle = '#06090e';
    ctx.fillRect(0, 0, w, h);

    // 2. High-Tech Grid
    ctx.strokeStyle = 'rgba(29, 44, 66, 0.4)';
    ctx.lineWidth = 1;
    for (let x = 0; x < w; x += 30) {
      ctx.beginPath();
      ctx.moveTo(x, 0);
      ctx.lineTo(x, h);
      ctx.stroke();
    }
    for (let y = 0; y < h; y += 30) {
      ctx.beginPath();
      ctx.moveTo(0, y);
      ctx.lineTo(w, y);
      ctx.stroke();
    }

    const centerY = h / 2;
    const pixelsPerMm = scale * ((w - 140) / Math.max(1000, totalLength));
    const startX = panX;
    const barWidthPx = totalLength * pixelsPerMm;
    const flangeAPx = widthA * pixelsPerMm * 1.6;
    const flangeBPx = widthB * pixelsPerMm * 1.6;

    // 3. Draw Angle Bar (Flange A - Top Wing with Metallic Brushed Steel Gradient)
    const gradA = ctx.createLinearGradient(0, centerY - flangeAPx, 0, centerY);
    gradA.addColorStop(0, '#101926');
    gradA.addColorStop(0.5, '#162233');
    gradA.addColorStop(1, '#0b111a');

    ctx.fillStyle = gradA;
    ctx.fillRect(startX, centerY - flangeAPx, barWidthPx, flangeAPx);

    // Flange A Outer Border & Glow
    ctx.strokeStyle = '#0ea5e9';
    ctx.lineWidth = 2;
    ctx.strokeRect(startX, centerY - flangeAPx, barWidthPx, flangeAPx);

    // 4. Draw Angle Bar (Flange B - Bottom Wing with Metallic Gradient)
    const gradB = ctx.createLinearGradient(0, centerY, 0, centerY + flangeBPx);
    gradB.addColorStop(0, '#0b111a');
    gradB.addColorStop(0.5, '#162233');
    gradB.addColorStop(1, '#101926');

    ctx.fillStyle = gradB;
    ctx.fillRect(startX, centerY, barWidthPx, flangeBPx);

    // Flange B Outer Border & Glow
    ctx.strokeStyle = '#10b981';
    ctx.lineWidth = 2;
    ctx.strokeRect(startX, centerY, barWidthPx, flangeBPx);

    // 5. Bend Heel Axis (Centerline with Neon Amber Glow)
    ctx.strokeStyle = '#ffb703';
    ctx.lineWidth = 2.5;
    ctx.setLineDash([8, 4]);
    ctx.beginPath();
    ctx.moveTo(startX, centerY);
    ctx.lineTo(startX + barWidthPx, centerY);
    ctx.stroke();
    ctx.setLineDash([]);

    // 6. Flange & Coordinate Callouts
    ctx.font = 'bold 11px JetBrains Mono, monospace';
    ctx.fillStyle = '#38bdf8';
    ctx.fillText(`FLANGE A (Width: ${widthA}mm)`, startX + 12, centerY - flangeAPx + 18);
    ctx.fillStyle = '#34d399';
    ctx.fillText(`FLANGE B (Width: ${widthB}mm)`, startX + 12, centerY + flangeBPx - 12);
    ctx.fillStyle = '#ffb703';
    ctx.fillText('BEND HEEL DATUM', startX + barWidthPx - 140, centerY - 8);

    // 7. Precision Millimeter Dimension Ruler along Top
    const rulerY = centerY - flangeAPx - 24;
    ctx.strokeStyle = '#384f6e';
    ctx.lineWidth = 1.5;
    ctx.beginPath();
    ctx.moveTo(startX, rulerY);
    ctx.lineTo(startX + barWidthPx, rulerY);
    ctx.stroke();

    const tickStep = totalLength > 3000 ? 500 : 100;
    ctx.font = '10px JetBrains Mono, monospace';
    ctx.fillStyle = '#94a3b8';

    for (let mm = 0; mm <= totalLength; mm += tickStep) {
      const tx = startX + mm * pixelsPerMm;
      ctx.beginPath();
      ctx.moveTo(tx, rulerY - 6);
      ctx.lineTo(tx, rulerY + 6);
      ctx.stroke();
      if (mm % (tickStep * 2) === 0 || mm === totalLength) {
        ctx.fillText(`${mm}mm`, tx - 16, rulerY - 9);
      }
    }

    // 8. Draw CAD Punch Steps, Markings & Shearing Line
    steps.forEach((step, idx) => {
      const isSelected = selectedStepIndex === idx;
      const stepX = startX + step.xPosition * pixelsPerMm;

      if (step.operationType === 'PUNCH') {
        const isSideA = step.side === 'A';
        const holeRadius = Math.max(5, ((step.toolSize || 18) / 2) * pixelsPerMm * 1.6);
        const gaugeY = isSideA
          ? centerY - step.yPosition * pixelsPerMm * 1.6
          : centerY + step.yPosition * pixelsPerMm * 1.6;

        // Punch Bore Drop Shadow & Depth
        ctx.beginPath();
        ctx.arc(stepX, gaugeY, holeRadius + 2, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(0, 0, 0, 0.9)';
        ctx.fill();

        // Punch Hole Bore Gradient (Metallic Die Hole)
        const holeGrad = ctx.createRadialGradient(
          stepX - 2,
          gaugeY - 2,
          1,
          stepX,
          gaugeY,
          holeRadius
        );
        if (isSelected) {
          holeGrad.addColorStop(0, '#ff0055');
          holeGrad.addColorStop(1, '#88002d');
        } else if (isSideA) {
          holeGrad.addColorStop(0, '#00f0ff');
          holeGrad.addColorStop(1, '#0369a1');
        } else {
          holeGrad.addColorStop(0, '#00ff9d');
          holeGrad.addColorStop(1, '#047857');
        }

        ctx.beginPath();
        ctx.arc(stepX, gaugeY, holeRadius, 0, Math.PI * 2);
        ctx.fillStyle = holeGrad;
        ctx.fill();
        ctx.strokeStyle = isSelected ? '#ffffff' : isSideA ? '#38bdf8' : '#34d399';
        ctx.lineWidth = isSelected ? 2.5 : 1.5;
        ctx.stroke();

        // Center Crosshair
        ctx.strokeStyle = '#ffffff';
        ctx.lineWidth = 1;
        ctx.beginPath();
        ctx.moveTo(stepX - holeRadius - 3, gaugeY);
        ctx.lineTo(stepX + holeRadius + 3, gaugeY);
        ctx.moveTo(stepX, gaugeY - holeRadius - 3);
        ctx.lineTo(stepX, gaugeY + holeRadius + 3);
        ctx.stroke();

        // Callout Tag
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 10px JetBrains Mono, monospace';
        ctx.fillText(`Ø${step.toolSize || 18}`, stepX - 12, gaugeY - holeRadius - 6);
      } else if (step.operationType === 'MARK') {
        // Marking Block Stamp
        const blockW = 80;
        const blockH = 26;
        ctx.fillStyle = isSelected ? '#9f1239' : '#451a03';
        ctx.strokeStyle = '#ffb703';
        ctx.lineWidth = 2;
        ctx.fillRect(stepX - blockW / 2, centerY - blockH / 2, blockW, blockH);
        ctx.strokeRect(stepX - blockW / 2, centerY - blockH / 2, blockW, blockH);

        ctx.fillStyle = '#ffeedd';
        ctx.font = 'bold 11px JetBrains Mono, monospace';
        ctx.fillText(step.markingText || 'STAMP', stepX - blockW / 2 + 8, centerY + 4);
      } else if (step.operationType === 'CUT' || step.isCutOff) {
        // Cut-Off Laser Line
        ctx.strokeStyle = '#ff0055';
        ctx.lineWidth = 3;
        ctx.setLineDash([5, 3]);
        ctx.beginPath();
        ctx.moveTo(stepX, centerY - flangeAPx - 14);
        ctx.lineTo(stepX, centerY + flangeBPx + 14);
        ctx.stroke();
        ctx.setLineDash([]);

        ctx.fillStyle = '#ff0055';
        ctx.font = 'bold 11px JetBrains Mono, monospace';
        ctx.fillText('✂ SHEAR CUT', stepX - 35, centerY + flangeBPx + 28);
      }
    });

    // 9. Real-Time Laser Feed Carriage Cursor (Active in Production & Jog)
    if (activeFeedPosition !== undefined && activeFeedPosition >= 0) {
      const liveX = startX + activeFeedPosition * pixelsPerMm;

      // Laser Line
      ctx.strokeStyle = '#00f0ff';
      ctx.lineWidth = 3;
      ctx.shadowColor = '#00f0ff';
      ctx.shadowBlur = 12;
      ctx.beginPath();
      ctx.moveTo(liveX, 0);
      ctx.lineTo(liveX, h);
      ctx.stroke();
      ctx.shadowBlur = 0; // reset

      // Carriage Cursor Head
      ctx.fillStyle = '#00f0ff';
      ctx.beginPath();
      ctx.moveTo(liveX, 0);
      ctx.lineTo(liveX - 8, 16);
      ctx.lineTo(liveX + 8, 16);
      ctx.closePath();
      ctx.fill();

      // Tooltip Box on Top
      ctx.fillStyle = '#0b111a';
      ctx.strokeStyle = '#00f0ff';
      ctx.lineWidth = 1.5;
      ctx.fillRect(liveX + 8, 8, 120, 24);
      ctx.strokeRect(liveX + 8, 8, 120, 24);

      ctx.fillStyle = '#00f0ff';
      ctx.font = 'bold 11px JetBrains Mono, monospace';
      ctx.fillText(`X: ${activeFeedPosition.toFixed(1)} mm`, liveX + 16, 24);
    }
  }, [recipe, scale, panX, selectedStepIndex, totalLength, widthA, widthB, steps, activeFeedPosition]);

  const handleMouseMove = (e: React.MouseEvent<HTMLCanvasElement>) => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const rect = canvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    const centerY = canvas.height / 2;
    const pixelsPerMm = scale * ((canvas.width - 140) / Math.max(1000, totalLength));
    const xMm = Math.max(0, Math.min(totalLength, Math.round((x - panX) / pixelsPerMm)));
    const flange = y < centerY ? 'Flange A' : 'Flange B';

    setMouseCoord({ xMm, flange });
  };

  return (
    <div className="relative w-full h-full bg-scada-950 rounded-2xl overflow-hidden border border-scada-750 shadow-2xl">
      <canvas
        ref={canvasRef}
        onMouseMove={handleMouseMove}
        onMouseLeave={() => setMouseCoord(null)}
        className="w-full h-full cursor-crosshair"
      />

      {/* Floating Canvas Zoom & View Controls */}
      <div className="absolute top-4 right-4 flex items-center gap-1.5 bg-scada-900/90 border border-scada-750 p-1.5 rounded-xl backdrop-blur-xl shadow-2xl">
        <button
          onClick={() => setScale((s) => Math.min(3.0, s + 0.2))}
          className="p-2 hover:bg-scada-800 rounded-lg text-slate-300 hover:text-white transition-all"
          title="Zoom In"
        >
          <ZoomIn className="w-4 h-4" />
        </button>
        <button
          onClick={() => setScale((s) => Math.max(0.3, s - 0.2))}
          className="p-2 hover:bg-scada-800 rounded-lg text-slate-300 hover:text-white transition-all"
          title="Zoom Out"
        >
          <ZoomOut className="w-4 h-4" />
        </button>
        <button
          onClick={() => {
            setScale(0.85);
            setPanX(60);
          }}
          className="p-2 hover:bg-scada-800 rounded-lg text-slate-300 hover:text-white transition-all"
          title="Fit to Screen"
        >
          <Maximize2 className="w-4 h-4" />
        </button>
      </div>

      {/* Live Coordinate Crosshair Tracker */}
      {mouseCoord && (
        <div className="absolute top-4 left-4 flex items-center gap-2 text-xs font-mono bg-scada-900/90 border border-cyan-500/40 text-neon-cyan px-3 py-1.5 rounded-xl shadow-neon-cyan backdrop-blur-xl">
          <Crosshair className="w-3.5 h-3.5 animate-spin" />
          <span>X: {mouseCoord.xMm}mm</span>
          <span className="text-slate-400">•</span>
          <span className="text-slate-300">{mouseCoord.flange}</span>
        </div>
      )}

      {/* Footer Industrial Legend */}
      <div className="absolute bottom-4 left-4 flex items-center gap-5 text-xs font-mono text-slate-300 bg-scada-900/90 px-4 py-2 rounded-xl border border-scada-750 backdrop-blur-xl shadow-2xl">
        <span className="flex items-center gap-2">
          <span className="w-3 h-3 rounded-full bg-cyan-400 shadow-neon-cyan" /> Flange A Punch
        </span>
        <span className="flex items-center gap-2">
          <span className="w-3 h-3 rounded-full bg-emerald-400 shadow-neon-emerald" /> Flange B Punch
        </span>
        <span className="flex items-center gap-2">
          <span className="w-3 h-3 rounded bg-amber-500 shadow-neon-amber" /> Marking Stamp
        </span>
        <span className="flex items-center gap-2">
          <span className="w-3 h-3 bg-rose-500 shadow-neon-rose" /> Hydraulic Shear
        </span>
      </div>
    </div>
  );
};
