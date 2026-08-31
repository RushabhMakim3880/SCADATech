import React, { useRef, useEffect, useState, useCallback } from 'react';
import { ItemRecipe } from '@innovance-hmi/shared';
import {
  RotateCcw, AlignCenter, Shuffle, Eye, EyeOff
} from 'lucide-react';

interface AngleBarVisualizerProps {
  recipe?: ItemRecipe | null;
  activeFeedPosition?: number;
  highlightStepIndex?: number;
  onSelectStep?: (stepIndex: number) => void;
  onCanvasClick?: (x: number, y: number, side: 'A' | 'B') => void;
  onStepDrag?: (stepIndex: number, newX: number, newY: number, side: 'A' | 'B') => void;
}

export const AngleBarVisualizer: React.FC<AngleBarVisualizerProps> = ({
  recipe,
  activeFeedPosition = 0,
  highlightStepIndex,
  onSelectStep,
  onCanvasClick,
  onStepDrag,
}) => {
  const canvasRef = useRef<HTMLCanvasElement | null>(null);
  const containerRef = useRef<HTMLDivElement | null>(null);

  // Viewport transformations
  const [zoom, setZoom] = useState<number>(1.0);
  const [panX, setPanX] = useState<number>(50);
  const [panY, setPanY] = useState<number>(0);
  
  const [isFlipped, setIsFlipped] = useState<boolean>(false);
  const [showDimensions, setShowDimensions] = useState<boolean>(true);
  const [hoveredStep, setHoveredStep] = useState<any | null>(null);
  const [mousePos, setMousePos] = useState<{ x: number; y: number } | null>(null);

  // Interaction Refs
  const isDragging = useRef(false);
  const dragStart = useRef({ x: 0, y: 0 });
  const draggedStepIndex = useRef<number | null>(null);

  const hasValidRecipe = Boolean(recipe);
  const lengthMm = recipe?.totalLength || 1500;
  const widthA = recipe?.angleWidthA || 75;
  const widthB = recipe?.angleWidthB || 75;

  const handleReset = () => {
    setZoom(1.0);
    setPanX(50);
    setPanY(0);
    setIsFlipped(false);
  };

  const handleFit = useCallback(() => {
    if (!containerRef.current) return;
    const width = containerRef.current.clientWidth;
    const targetScale = (width - 100) / lengthMm;
    setZoom(Math.max(0.12, Math.min(2.5, targetScale)));
    setPanX(50);
    setPanY(0);
  }, [lengthMm]);

  useEffect(() => {
    handleFit();
  }, [handleFit]);

  // Main Canvas Render Loop
  useEffect(() => {
    let rafId: number;

    const render = () => {
      const canvas = canvasRef.current;
      if (!canvas) return;
      const ctx = canvas.getContext('2d');
      if (!ctx) return;

      const dpr = window.devicePixelRatio || 1;
      const rect = canvas.getBoundingClientRect();
      
      // Prevent blurry text by explicitly setting canvas internal resolution
      canvas.width = rect.width * dpr;
      canvas.height = rect.height * dpr;
      ctx.scale(dpr, dpr);

      const w = rect.width;
      const h = rect.height;

      // 1. Dark Blueprint Background
      ctx.fillStyle = '#0a0e14';
      ctx.fillRect(0, 0, w, h);

      // 2. Blueprint Grid Lines
      const gridSize = 50 * zoom;
      ctx.strokeStyle = 'rgba(56, 189, 248, 0.05)';
      ctx.lineWidth = 1;
      ctx.beginPath();
      
      // Calculate start offsets for grid to stay locked to pan
      const offsetX = (panX % gridSize + gridSize) % gridSize;
      const offsetY = ((h / 2 + panY) % gridSize + gridSize) % gridSize;

      for (let x = offsetX; x < w; x += gridSize) {
        ctx.moveTo(x, 0);
        ctx.lineTo(x, h);
      }
      for (let y = offsetY; y < h; y += gridSize) {
        ctx.moveTo(0, y);
        ctx.lineTo(w, y);
      }
      ctx.stroke();

      if (!hasValidRecipe) {
        ctx.fillStyle = '#64748b';
        ctx.font = 'bold 14px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('STANDBY: NO PRODUCTION RECIPE LOADED', w / 2, h / 2 - 10);
        ctx.font = '11px sans-serif';
        ctx.fillStyle = '#475569';
        ctx.fillText('Select a recipe to start', w / 2, h / 2 + 15);
        ctx.textAlign = 'left';
        return;
      }

      // 3. True Physical Coordinate Setup
      const centerY = h / 2 + panY;
      const scale = zoom; // 1 pixel = 1 mm at zoom 1.0

      const startX = panX;
      const barPixelLength = lengthMm * scale;

      const topFlangeHeight = (isFlipped ? widthB : widthA) * scale;
      const bottomFlangeHeight = (isFlipped ? widthA : widthB) * scale;

      const topFlangeY = centerY - topFlangeHeight;
      const bottomFlangeY = centerY + bottomFlangeHeight;

      // 4. Render Steel Angle Bar Flanges (Ghosting effect based on activeFeedPosition)
      
      const drawFlange = (startY: number, endY: number) => {
        // Draw the full bar base
        const grad = ctx.createLinearGradient(0, startY, 0, endY);
        grad.addColorStop(0, '#1c2836');
        grad.addColorStop(1, '#27384c');
        ctx.fillStyle = grad;
        ctx.fillRect(startX, startY, barPixelLength, Math.abs(endY - startY));

        // Draw ghosting overlay
        if (activeFeedPosition > 0) {
          const ghostPixLength = Math.min(barPixelLength, activeFeedPosition * scale);
          ctx.fillStyle = 'rgba(0, 0, 0, 0.4)'; // Darken processed section
          ctx.fillRect(startX, Math.min(startY, endY), ghostPixLength, Math.abs(endY - startY));
        }
      };

      drawFlange(topFlangeY, centerY);
      drawFlange(centerY, bottomFlangeY);

      ctx.strokeStyle = '#38bdf8';
      ctx.lineWidth = 1.5;
      ctx.strokeRect(startX, topFlangeY, barPixelLength, topFlangeHeight + bottomFlangeHeight);

      // 5. Bend Heel Datum Center Fold Line
      ctx.strokeStyle = '#f59e0b';
      ctx.lineWidth = 1.5;
      ctx.setLineDash([8, 4]);
      ctx.beginPath();
      ctx.moveTo(startX, centerY);
      ctx.lineTo(startX + barPixelLength, centerY);
      ctx.stroke();
      ctx.setLineDash([]);

      ctx.font = 'bold 9px monospace';
      ctx.fillStyle = '#f59e0b';
      ctx.fillText('◄ BEND HEEL', startX + barPixelLength + 10, centerY + 3);

      // 6. Dimension Annotations
      if (showDimensions) {
        ctx.font = 'bold 11px sans-serif';
        ctx.fillStyle = '#7dd3fc';
        ctx.fillText(`▲ FLANGE ${isFlipped ? 'B' : 'A'} (${isFlipped ? widthB : widthA} mm)`, startX + 14, topFlangeY - 10);
        
        ctx.fillStyle = '#86efac';
        ctx.fillText(`▼ FLANGE ${isFlipped ? 'A' : 'B'} (${isFlipped ? widthA : widthB} mm)`, startX + 14, bottomFlangeY + 18);

        const dimY = bottomFlangeY + 40;
        if (dimY < h - 10) {
          ctx.strokeStyle = '#94a3b8';
          ctx.lineWidth = 1;
          ctx.beginPath();
          ctx.moveTo(startX, dimY - 5); ctx.lineTo(startX, dimY + 5);
          ctx.moveTo(startX + barPixelLength, dimY - 5); ctx.lineTo(startX + barPixelLength, dimY + 5);
          ctx.moveTo(startX, dimY); ctx.lineTo(startX + barPixelLength, dimY);
          ctx.stroke();

          ctx.fillStyle = '#ffffff';
          ctx.font = 'bold 11px monospace';
          ctx.textAlign = 'center';
          ctx.fillText(`TOTAL LENGTH: ${lengthMm} mm`, startX + barPixelLength / 2, dimY - 4);
          ctx.textAlign = 'left';
        }
      }

      // 7. Render Punch Holes, Markings, and Cut Lines
      if (recipe && recipe.steps) {
        // First pass: find cuts for collision detection
        const cuts = recipe.steps.filter(s => s.operationType === 'CUT' || s.isCutOff);

        recipe.steps.forEach((step, idx) => {
          const isHighlight = highlightStepIndex === idx;
          const isHovered = hoveredStep === step;
          const highlightActive = isHighlight || isHovered;
          
          const opX = startX + step.xPosition * scale;

          let opY = centerY;
          if (step.side === 'A') {
            opY = isFlipped ? centerY + step.yPosition * scale : centerY - step.yPosition * scale;
          } else if (step.side === 'B') {
            opY = isFlipped ? centerY - step.yPosition * scale : centerY + step.yPosition * scale;
          }

          // Ghosting effect check
          const isDone = activeFeedPosition > step.xPosition;

          if (step.operationType === 'CUT' || step.isCutOff) {
            ctx.strokeStyle = highlightActive ? '#ffffff' : (isDone ? '#9f1239' : '#ff3366');
            ctx.lineWidth = highlightActive ? 3 : 1.5;
            ctx.beginPath();
            ctx.moveTo(opX, topFlangeY - 10);
            ctx.lineTo(opX, bottomFlangeY + 10);
            ctx.stroke();

            ctx.fillStyle = highlightActive ? '#ffffff' : (isDone ? '#9f1239' : '#ff3366');
            ctx.font = 'bold 9px monospace';
            ctx.fillText('SHEAR CUT', opX - 28, topFlangeY - 15);
          } else if (step.operationType === 'MARK') {
            const boxW = Math.max(40, 20 * scale);
            const boxH = Math.max(14, 8 * scale);
            ctx.fillStyle = highlightActive ? '#fbbf24' : (isDone ? '#78350f' : '#f59e0b');
            ctx.strokeStyle = isDone ? '#451a03' : '#ffffff';
            ctx.lineWidth = 1.5;
            ctx.fillRect(opX - boxW / 2, opY - boxH / 2, boxW, boxH);
            ctx.strokeRect(opX - boxW / 2, opY - boxH / 2, boxW, boxH);

            ctx.fillStyle = isDone ? '#d4d4d8' : '#0f172a';
            ctx.font = `bold ${Math.max(9, 5 * scale)}px monospace`;
            ctx.textAlign = 'center';
            ctx.fillText(step.markingText || 'MARK', opX, opY + (boxH * 0.25));
            ctx.textAlign = 'left';
          } else {
            // Collision Detection
            const isCollision = cuts.some(cut => Math.abs(cut.xPosition - step.xPosition) < 20);
            if (isCollision) {
               // Draw warning zone
               ctx.fillStyle = 'rgba(239, 68, 68, 0.3)';
               ctx.fillRect(opX - 15 * scale, topFlangeY, 30 * scale, topFlangeHeight + bottomFlangeHeight);
               
               // Warning Icon/Text
               ctx.fillStyle = '#ef4444';
               ctx.font = 'bold 10px sans-serif';
               ctx.fillText('⚠️', opX - 7, topFlangeY - 2);
            }

            // True physical radius based on zoom (min 3px to stay visible)
            const radius = Math.max(3, ((step.toolSize || 18) / 2) * scale);

            let punchColor = step.side === 'A' ? '#00e5ff' : '#00e676';
            if (isDone) punchColor = step.side === 'A' ? '#083344' : '#064e3b'; // Darker if done

            ctx.fillStyle = punchColor;
            ctx.strokeStyle = highlightActive ? '#ffffff' : '#0f172a';
            ctx.lineWidth = highlightActive ? 3 : 1.5;

            ctx.beginPath();
            ctx.arc(opX, opY, radius, 0, Math.PI * 2);
            ctx.fill();
            ctx.stroke();

            ctx.strokeStyle = isDone ? '#334155' : '#0a1017';
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(opX - radius - 2, opY);
            ctx.lineTo(opX + radius + 2, opY);
            ctx.moveTo(opX, opY - radius - 2);
            ctx.lineTo(opX, opY + radius + 2);
            ctx.stroke();

            if (scale > 0.5 || highlightActive) {
              ctx.fillStyle = isDone ? '#94a3b8' : '#ffffff';
              ctx.font = 'bold 9px sans-serif';
              ctx.fillText(`Ø${step.toolSize || 18}`, opX - 8, opY - radius - 4);
            }
          }
        });
      }

      // 8. Live Real-time Feed Laser
      if (activeFeedPosition >= 0) {
        const laserX = startX + activeFeedPosition * scale;
        
        ctx.strokeStyle = '#00ffcc';
        ctx.lineWidth = 2;
        ctx.shadowColor = '#00ffcc';
        // Add pulse effect using time
        const time = Date.now() / 200;
        ctx.shadowBlur = 8 + Math.sin(time) * 4;
        
        ctx.beginPath();
        ctx.moveTo(laserX, 0);
        ctx.lineTo(laserX, h);
        ctx.stroke();
        ctx.shadowBlur = 0;

        ctx.fillStyle = 'rgba(0, 255, 204, 0.15)';
        ctx.fillRect(laserX - 25, 0, 50, h);

        ctx.fillStyle = '#00ffcc';
        ctx.fillRect(laserX - 35, 6, 70, 18);
        ctx.fillStyle = '#0a0e14';
        ctx.font = 'bold 10px monospace';
        ctx.textAlign = 'center';
        ctx.fillText(`X: ${activeFeedPosition.toFixed(1)}`, laserX, 19);
        ctx.textAlign = 'left';
      }

      // 9. Ruler Axes
      const rulerSize = 20;
      ctx.fillStyle = '#141b22';
      ctx.fillRect(0, 0, w, rulerSize); // Top Ruler
      ctx.fillRect(0, 0, rulerSize, h); // Left Ruler
      ctx.strokeStyle = '#334155';
      ctx.lineWidth = 1;
      ctx.strokeRect(0, 0, w, rulerSize);
      ctx.strokeRect(0, 0, rulerSize, h);

      // X Axis (Top)
      ctx.fillStyle = '#94a3b8';
      ctx.font = '9px sans-serif';
      ctx.textAlign = 'center';
      
      const mmPerTick = scale > 1 ? 10 : (scale > 0.2 ? 50 : 100);
      const startTickX = Math.floor(-panX / scale / mmPerTick) * mmPerTick;
      
      for (let mm = startTickX; mm < startTickX + (w / scale) + mmPerTick; mm += mmPerTick) {
        if (mm < 0 || mm > lengthMm) continue;
        const x = panX + mm * scale;
        if (x < rulerSize) continue; // Don't draw over intersection
        
        ctx.beginPath();
        ctx.moveTo(x, rulerSize);
        ctx.lineTo(x, rulerSize - (mm % (mmPerTick * 2) === 0 ? 8 : 4));
        ctx.stroke();

        if (mm % (mmPerTick * 2) === 0) {
          ctx.fillText(mm.toString(), x, 8);
        }
      }

      // 10. Minimap (Radar)
      const miniW = 150;
      const miniH = 40;
      const miniPadding = 10;
      const miniX = w - miniW - miniPadding;
      const miniY = h - miniH - miniPadding;

      ctx.fillStyle = 'rgba(15, 23, 42, 0.8)';
      ctx.fillRect(miniX, miniY, miniW, miniH);
      ctx.strokeStyle = '#334155';
      ctx.strokeRect(miniX, miniY, miniW, miniH);

      // Draw mini bar
      const miniScaleX = (miniW - 20) / lengthMm;
      ctx.fillStyle = '#38bdf8';
      ctx.fillRect(miniX + 10, miniY + miniH / 2 - 2, lengthMm * miniScaleX, 4);

      // Draw viewport box in minimap
      const viewStartMm = -panX / scale;
      const viewEndMm = (w - panX) / scale;
      const miniViewX = miniX + 10 + Math.max(0, viewStartMm) * miniScaleX;
      const miniViewW = (Math.min(lengthMm, viewEndMm) - Math.max(0, viewStartMm)) * miniScaleX;

      ctx.fillStyle = 'rgba(255, 255, 255, 0.2)';
      ctx.strokeStyle = '#ffffff';
      ctx.lineWidth = 1;
      ctx.fillRect(miniViewX, miniY + 5, miniViewW, miniH - 10);
      ctx.strokeRect(miniViewX, miniY + 5, miniViewW, miniH - 10);

    };

    const loop = () => {
      render();
      rafId = requestAnimationFrame(loop);
    };
    rafId = requestAnimationFrame(loop);

    return () => cancelAnimationFrame(rafId);
  }, [recipe, hasValidRecipe, activeFeedPosition, highlightStepIndex, hoveredStep, zoom, panX, panY, isFlipped, showDimensions, widthA, widthB, lengthMm]);

  // Interaction Handlers (Pan/Zoom)
  const handleWheel = (e: React.WheelEvent<HTMLCanvasElement>) => {
    e.preventDefault();
    const canvas = canvasRef.current;
    if (!canvas) return;
    const rect = canvas.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;

    const zoomFactor = 1.1;
    let newZoom = zoom;

    if (e.deltaY < 0) {
      newZoom *= zoomFactor;
    } else {
      newZoom /= zoomFactor;
    }

    newZoom = Math.max(0.05, Math.min(15, newZoom));

    // Pan adjustment to zoom towards cursor
    const scaleChange = newZoom / zoom;
    const newPanX = mouseX - (mouseX - panX) * scaleChange;
    const newPanY = mouseY - (mouseY - panY) * scaleChange;

    setZoom(newZoom);
    setPanX(newPanX);
    setPanY(newPanY);
  };

  const handleMouseDown = (e: React.MouseEvent<HTMLCanvasElement>) => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const rect = canvas.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;

    if (recipe && recipe.steps) {
      const centerY = rect.height / 2 + panY;
      const scale = zoom;
      const foundIndex = recipe.steps.findIndex((step) => {
        const opX = panX + step.xPosition * scale;
        let opY = centerY;
        if (step.side === 'A') {
          opY = isFlipped ? centerY + step.yPosition * scale : centerY - step.yPosition * scale;
        } else if (step.side === 'B') {
          opY = isFlipped ? centerY - step.yPosition * scale : centerY + step.yPosition * scale;
        }
        const dist = Math.hypot(mouseX - opX, mouseY - opY);
        return dist < Math.max(10, 15 * scale);
      });

      if (foundIndex >= 0 && onStepDrag) {
        draggedStepIndex.current = foundIndex;
        return;
      }
    }

    isDragging.current = true;
    dragStart.current = { x: e.clientX, y: e.clientY };
  };

  const handleMouseMove = (e: React.MouseEvent<HTMLCanvasElement>) => {
    const canvas = canvasRef.current;
    if (!canvas) return;

    if (draggedStepIndex.current !== null && onStepDrag) {
      const rect = canvas.getBoundingClientRect();
      const mouseX = e.clientX - rect.left;
      const mouseY = e.clientY - rect.top;
      
      const scale = zoom;
      const clickX = (mouseX - panX) / scale;
      const centerY = rect.height / 2 + panY;
      
      let side: 'A' | 'B' = recipe?.steps[draggedStepIndex.current].side as 'A' | 'B' || 'A';
      let clickY = 0;

      if (mouseY < centerY) {
        side = isFlipped ? 'B' : 'A';
        clickY = Math.abs(centerY - mouseY) / scale;
      } else {
        side = isFlipped ? 'A' : 'B';
        clickY = Math.abs(mouseY - centerY) / scale;
      }

      const safeX = Math.max(0, Math.min(lengthMm, clickX));
      const maxW = side === 'A' ? widthA : widthB;
      const safeY = Math.max(0, Math.min(maxW, clickY));

      onStepDrag(draggedStepIndex.current, safeX, safeY, side);
      return;
    }

    if (isDragging.current) {
      const dx = e.clientX - dragStart.current.x;
      const dy = e.clientY - dragStart.current.y;
      setPanX((p) => p + dx);
      setPanY((p) => p + dy);
      dragStart.current = { x: e.clientX, y: e.clientY };
    }

    // Hit Testing for Tooltip
    if (recipe && recipe.steps) {
      const rect = canvas.getBoundingClientRect();
      const mouseX = e.clientX - rect.left;
      const mouseY = e.clientY - rect.top;
      
      // Calculate smart tooltip position to avoid clipping
      let ttX = e.clientX + 12;
      let ttY = e.clientY + 12;
      if (ttX + 160 > window.innerWidth) ttX = e.clientX - 160 - 12;
      if (ttY + 80 > window.innerHeight) ttY = e.clientY - 80 - 12;
      
      setMousePos({ x: ttX, y: ttY });

      const centerY = rect.height / 2 + panY;
      const scale = zoom;

      const foundIndex = recipe.steps.findIndex((step) => {
        const opX = panX + step.xPosition * scale;
        let opY = centerY;
        if (step.side === 'A') {
          opY = isFlipped ? centerY + step.yPosition * scale : centerY - step.yPosition * scale;
        } else if (step.side === 'B') {
          opY = isFlipped ? centerY - step.yPosition * scale : centerY + step.yPosition * scale;
        }
        
        const dist = Math.hypot(mouseX - opX, mouseY - opY);
        // Larger hit target if zoomed out
        return dist < Math.max(10, 15 * scale);
      });

      if (foundIndex >= 0) {
        setHoveredStep(recipe.steps[foundIndex]);
        if (onSelectStep && !isDragging.current) onSelectStep(foundIndex);
      } else {
        setHoveredStep(null);
      }
    }
  };

  const handleMouseUp = (e: React.MouseEvent<HTMLCanvasElement>) => {
    const wasDraggingStep = draggedStepIndex.current !== null;
    draggedStepIndex.current = null;
    
    const wasDraggingCanvas = isDragging.current && (Math.abs(e.clientX - dragStart.current.x) > 5 || Math.abs(e.clientY - dragStart.current.y) > 5);
    isDragging.current = false;

    if (!wasDraggingCanvas && !wasDraggingStep && onCanvasClick && !hoveredStep && recipe) {
      const canvas = canvasRef.current;
      if (!canvas) return;
      const rect = canvas.getBoundingClientRect();
      const mouseX = e.clientX - rect.left;
      const mouseY = e.clientY - rect.top;

      const scale = zoom;
      const clickX = (mouseX - panX) / scale;
      
      // Ensure click is within bar length
      if (clickX >= 0 && clickX <= lengthMm) {
        const centerY = rect.height / 2 + panY;
        let side: 'A' | 'B' | null = null;
        let clickY = 0;

        // Check if click is on Top Flange
        if (mouseY < centerY && mouseY >= centerY - (isFlipped ? widthB : widthA) * scale) {
          side = isFlipped ? 'B' : 'A';
          clickY = Math.abs(centerY - mouseY) / scale;
        } 
        // Check if click is on Bottom Flange
        else if (mouseY > centerY && mouseY <= centerY + (isFlipped ? widthA : widthB) * scale) {
          side = isFlipped ? 'A' : 'B';
          clickY = Math.abs(mouseY - centerY) / scale;
        }

        if (side) {
          onCanvasClick(clickX, clickY, side);
        }
      }
    }
  };

  const handleMouseLeave = () => {
    isDragging.current = false;
    draggedStepIndex.current = null;
    setHoveredStep(null);
  };

  return (
    <div ref={containerRef} className="relative w-full h-full flex flex-col bg-[#0a0e14] rounded overflow-hidden select-none border border-slate-700">
      <div className="bg-[#141b22] px-3 py-1.5 border-b border-slate-700 flex items-center justify-between z-10">
        <div className="flex items-center gap-1">
          <button onClick={handleReset} title="Reset View" className="btn-ca btn-ca-dark text-xs py-1 px-2">
            <RotateCcw className="w-3.5 h-3.5" />
          </button>
          <button onClick={handleFit} title="Fit to Viewport Width" className="btn-ca btn-ca-dark text-xs py-1 px-2">
            <AlignCenter className="w-3.5 h-3.5" /> Fit
          </button>
          <button onClick={() => setIsFlipped((f) => !f)} title="Flip Flange A/B" className={`btn-ca text-xs py-1 px-2 ${isFlipped ? 'btn-ca-primary' : 'btn-ca-dark'}`}>
            <Shuffle className="w-3.5 h-3.5" /> Flip
          </button>
          <button onClick={() => setShowDimensions((d) => !d)} title="Toggle Dimensions" className="btn-ca btn-ca-dark text-xs py-1 px-2">
            {showDimensions ? <Eye className="w-3.5 h-3.5" /> : <EyeOff className="w-3.5 h-3.5" />}
          </button>
        </div>

        <div className="flex items-center gap-3 text-[11px] font-semibold text-slate-300">
          <div className="flex items-center gap-1.5"><span className="w-2.5 h-2.5 rounded-full bg-[#00e5ff] inline-block" /> Flange A (DA1-3)</div>
          <div className="flex items-center gap-1.5"><span className="w-2.5 h-2.5 rounded-full bg-[#00e676] inline-block" /> Flange B (DB1-3)</div>
          <div className="flex items-center gap-1.5"><span className="w-2.5 h-2.5 rounded bg-[#f59e0b] inline-block" /> Marking</div>
          <div className="flex items-center gap-1.5"><span className="w-3 h-0.5 bg-[#ff3366] inline-block" /> Shear Cut</div>
        </div>
      </div>

      <canvas
        ref={canvasRef}
        onWheel={handleWheel}
        onMouseDown={handleMouseDown}
        onMouseMove={handleMouseMove}
        onMouseUp={handleMouseUp}
        onMouseLeave={handleMouseLeave}
        className="w-full flex-1 min-h-[300px]"
        style={{ cursor: hoveredStep ? 'pointer' : (isDragging.current ? 'grabbing' : 'grab') }}
      />

      {hoveredStep && mousePos && !isDragging.current && (
        <div
          style={{
            position: 'fixed',
            left: mousePos.x,
            top: mousePos.y,
            pointerEvents: 'none',
          }}
          className="bg-slate-900/95 backdrop-blur-sm text-white text-xs p-3 rounded border border-slate-700 shadow-xl z-50 space-y-1.5 min-w-[140px]"
        >
          <div className="font-black text-cyan-400 border-b border-slate-700 pb-1 mb-1">
            Step #{hoveredStep.stepNumber} • {hoveredStep.operationType}
          </div>
          <div className="grid grid-cols-2 gap-x-2 gap-y-1">
            <span className="text-slate-400">Flange:</span>
            <span className="font-bold text-right">Side {hoveredStep.side}</span>
            <span className="text-slate-400">Pos X:</span>
            <span className="font-mono text-emerald-400 font-bold text-right">{hoveredStep.xPosition}mm</span>
            <span className="text-slate-400">Pos Y:</span>
            <span className="font-mono text-emerald-400 font-bold text-right">{hoveredStep.yPosition}mm</span>
            {hoveredStep.toolSize && (
              <>
                <span className="text-slate-400">Tool:</span>
                <span className="font-bold text-right">Ø{hoveredStep.toolSize}mm</span>
              </>
            )}
          </div>
        </div>
      )}
    </div>
  );
};
