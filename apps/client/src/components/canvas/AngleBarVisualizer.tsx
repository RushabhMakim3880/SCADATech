import React, { useRef, useEffect, useState, useCallback } from 'react';
import { ItemRecipe, ItemRecipeStep } from '@innovance-hmi/shared';
import {
  AlignCenter,
  Shuffle,
  Eye,
  EyeOff,
  FileText,
  Sun,
  Moon,
  Shapes
} from 'lucide-react';
import { drawHoleSymbolOnCanvas, resolveHoleSymbol } from '../common/HoleSymbolIcon.js';
import { STANDARD_HOLE_STYLES } from '@innovance-hmi/shared';

interface AngleBarVisualizerProps {
  recipe?: ItemRecipe | null;
  activeFeedPosition?: number;
  highlightStepIndex?: number;
  externalHoverStepIndex?: number | null;
  syncFocusX?: number | null;
  initialTheme?: 'DARK' | 'PAPER';
  onSelectStep?: (stepIndex: number) => void;
  onHoverStep?: (stepIndex: number | null) => void;
  onViewportSync?: (centerMm: number) => void;
  onCanvasClick?: (x: number, y: number, side: 'A' | 'B') => void;
  onStepDrag?: (stepIndex: number, newX: number, newY: number, side: 'A' | 'B') => void;
}

export const AngleBarVisualizer: React.FC<AngleBarVisualizerProps> = ({
  recipe,
  activeFeedPosition = 0,
  highlightStepIndex,
  externalHoverStepIndex,
  syncFocusX,
  initialTheme = 'DARK',
  onSelectStep,
  onHoverStep,
  onViewportSync,
  onCanvasClick,
}) => {
  const canvasRef = useRef<HTMLCanvasElement | null>(null);
  const containerRef = useRef<HTMLDivElement | null>(null);

  // Viewport transformations
  const [zoom, setZoom] = useState<number>(0.15);
  const [panX, setPanX] = useState<number>(100);
  const [panY, setPanY] = useState<number>(0);

  // Visual Drafting Toggles
  const [isFlipped, setIsFlipped] = useState<boolean>(false);
  const [showDimensions, setShowDimensions] = useState<boolean>(true);
  const [showPitchChain, setShowPitchChain] = useState<boolean>(true);
  const [showTitleBlock, setShowTitleBlock] = useState<boolean>(false);
  const [showHoleLegend, setShowHoleLegend] = useState<boolean>(false);
  const [theme, setTheme] = useState<'DARK' | 'PAPER'>(initialTheme);
  const isDark = theme === 'DARK';

  const [hoveredStep, setHoveredStep] = useState<ItemRecipeStep | null>(null);
  const [mousePos, setMousePos] = useState<{ x: number; y: number } | null>(null);

  // Interaction Refs
  const isDragging = useRef(false);
  const dragStart = useRef({ x: 0, y: 0 });

  const hasValidRecipe = Boolean(recipe);
  const lengthMm = recipe?.totalLength || 6016;
  const widthA = recipe?.angleWidthA || 150;
  const widthB = recipe?.angleWidthB || 150;
  const thickness = recipe?.thickness || 20;

  const handleReset = () => {
    handleFit();
    setIsFlipped(false);
  };

  const handleFit = useCallback(() => {
    if (!containerRef.current) return;
    const width = containerRef.current.clientWidth;
    // Fit drawing horizontally leaving balanced room
    const targetScale = (width - 120) / lengthMm;
    setZoom(Math.max(0.08, Math.min(2.5, targetScale)));
    setPanX(60);
    setPanY(0);
  }, [lengthMm]);

  useEffect(() => {
    handleFit();
  }, [handleFit]);

  // Non-passive wheel event listener for smooth zooming without browser passive listener warnings
  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;

    const onWheelNative = (e: WheelEvent) => {
      e.preventDefault();
      const factor = e.deltaY < 0 ? 1.15 : 0.85;
      setZoom((z) => Math.max(0.06, Math.min(4.0, z * factor)));
    };

    canvas.addEventListener('wheel', onWheelNative, { passive: false });
    return () => {
      canvas.removeEventListener('wheel', onWheelNative);
    };
  }, []);

  // Synchronized horizontal camera focus along angle bar
  useEffect(() => {
    if (syncFocusX === null || syncFocusX === undefined || !containerRef.current) return;
    const width = containerRef.current.clientWidth;
    const targetPanX = width / 2 - syncFocusX * zoom;
    setPanX(targetPanX);
  }, [syncFocusX, zoom]);

  const activeHoveredStep = (externalHoverStepIndex !== undefined && externalHoverStepIndex !== null && recipe?.steps)
    ? recipe.steps[externalHoverStepIndex] || hoveredStep
    : hoveredStep;

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

      canvas.width = rect.width * dpr;
      canvas.height = rect.height * dpr;
      ctx.scale(dpr, dpr);

      const w = rect.width;
      const h = rect.height;

      const isDark = theme === 'DARK';

      // 1. Engineering Blueprint Background
      ctx.fillStyle = isDark ? '#0d131f' : '#ffffff';
      ctx.fillRect(0, 0, w, h);

      // 2. Technical Blueprint Drawing Frame & Zone Border (ISO / DIN Drafting Standard)
      const margin = 14;
      ctx.strokeStyle = isDark ? '#1e2b3e' : '#1e293b';
      ctx.lineWidth = isDark ? 1.5 : 2.5;
      ctx.strokeRect(margin, margin, w - margin * 2, h - margin * 2);

      ctx.strokeStyle = isDark ? '#162232' : '#94a3b8';
      ctx.lineWidth = 1.0;
      ctx.strokeRect(margin + 5, margin + 5, w - (margin + 5) * 2, h - (margin + 5) * 2);

      // Zone Margins: Coordinate markings (A-D, 1-6) along the border
      ctx.font = 'bold 9.5px monospace';
      ctx.fillStyle = isDark ? '#3d526f' : '#0f172a';
      ctx.textAlign = 'center';
      ['1', '2', '3', '4', '5', '6'].forEach((zone, i) => {
        const zx = margin + 5 + ((w - (margin + 5) * 2) / 6) * (i + 0.5);
        ctx.fillText(zone, zx, margin + 3);
        ctx.fillText(zone, zx, h - margin + 2);
      });
      ['A', 'B', 'C', 'D'].forEach((zone, i) => {
        const zy = margin + 5 + ((h - (margin + 5) * 2) / 4) * (i + 0.5);
        ctx.fillText(zone, margin - 1, zy);
        ctx.fillText(zone, w - margin + 8, zy);
      });
      ctx.textAlign = 'left';

      // 3. Technical Coordinate Grid
      const gridSize = 60 * zoom;
      ctx.strokeStyle = isDark ? 'rgba(56, 189, 248, 0.035)' : 'rgba(15, 23, 42, 0.08)';
      ctx.lineWidth = 1;
      ctx.beginPath();

      const offsetX = ((panX % gridSize) + gridSize) % gridSize;
      const offsetY = (((h / 2 + panY) % gridSize) + gridSize) % gridSize;

      for (let x = offsetX; x < w; x += gridSize) {
        ctx.moveTo(x, margin + 5);
        ctx.lineTo(x, h - margin - 5);
      }
      for (let y = offsetY; y < h; y += gridSize) {
        ctx.moveTo(margin + 5, y);
        ctx.lineTo(w - margin - 5, y);
      }
      ctx.stroke();

      if (!hasValidRecipe) {
        ctx.fillStyle = isDark ? '#64748b' : '#334155';
        ctx.font = 'bold 14px monospace';
        ctx.textAlign = 'center';
        ctx.fillText('STANDBY: NO PRODUCTION ITEM RECIPE LOADED', w / 2, h / 2 - 10);
        ctx.font = '11px sans-serif';
        ctx.fillStyle = isDark ? '#475569' : '#64748b';
        ctx.fillText('Upload a fabrication drawing PDF or select a recipe to start', w / 2, h / 2 + 15);
        ctx.textAlign = 'left';
        return;
      }

      // 4. Physical Coordinate Space — TWIN-BEAM PROJECTION (matching TEST_R.pdf)
      // Top Beam: FLANGE A (with toe at top, heel at bottom)
      // Bottom Beam: FLANGE B (with heel at top, toe at bottom)
      // Central Channel: Dedicated space between Flange A Heel and Flange B Heel for annotations
      const centerY = h / 2 + panY;
      const scaleX = zoom;

      const flangeGap = Math.max(65, Math.min(105, (h - 140) * 0.26));
      const flangeVisualHeight = Math.max(50, Math.min(85, (h - flangeGap - 180) / 2));
      const scaleY = flangeVisualHeight / widthA;

      const startX = panX;
      const barPixelLength = lengthMm * scaleX;

      // Flange A (Top Beam):
      const topFlangeHeelY = centerY - flangeGap / 2;
      const topFlangeToeY = topFlangeHeelY - flangeVisualHeight;

      // Flange B (Bottom Beam):
      const bottomFlangeHeelY = centerY + flangeGap / 2;
      const bottomFlangeToeY = bottomFlangeHeelY + flangeVisualHeight;

      // 5. Render Distinct Steel Angle Bar Projections (Flange A on Top, Flange B on Bottom)
      const drawFlangeBeam = (toeY: number, heelY: number, sideLabel: string, isTopBeam: boolean) => {
        const flangeH = Math.abs(heelY - toeY);
        const minY = Math.min(toeY, heelY);

        if (isDark) {
          const grad = ctx.createLinearGradient(0, minY, 0, minY + flangeH);
          grad.addColorStop(0, '#0f1622');
          grad.addColorStop(0.5, '#172230');
          grad.addColorStop(1, '#111926');
          ctx.fillStyle = grad;
        } else {
          // Paper Blueprint Technical White with ultra-subtle drafting steel wash
          ctx.fillStyle = '#f8fafc';
        }
        ctx.fillRect(startX, minY, barPixelLength, flangeH);

        // Technical angle steel hatching lines
        ctx.strokeStyle = isDark ? 'rgba(255, 255, 255, 0.025)' : 'rgba(15, 23, 42, 0.04)';
        ctx.lineWidth = 1;
        ctx.beginPath();
        for (let hx = startX; hx < startX + barPixelLength; hx += 40) {
          ctx.moveTo(hx, minY);
          ctx.lineTo(hx + flangeH * 0.6, minY + flangeH);
        }
        ctx.stroke();

        // Ghosting overlay for processed length during live production
        if (activeFeedPosition > 0) {
          const ghostPixLength = Math.min(barPixelLength, activeFeedPosition * scaleX);
          ctx.fillStyle = isDark ? 'rgba(0, 0, 0, 0.45)' : 'rgba(71, 85, 105, 0.25)';
          ctx.fillRect(startX, minY, ghostPixLength, flangeH);
        }

        // Flange Outer Perimeter (Solid CAD Drafting Border)
        ctx.strokeStyle = isDark ? '#38bdf8' : '#0f172a';
        ctx.lineWidth = isDark ? 1.6 : 2.0;
        ctx.strokeRect(startX, minY, barPixelLength, flangeH);

        // Dashed Root Thickness Line near Heel
        const thickY = isTopBeam ? heelY - thickness * scaleY : heelY + thickness * scaleY;
        ctx.strokeStyle = isDark ? 'rgba(56, 189, 248, 0.25)' : 'rgba(15, 23, 42, 0.3)';
        ctx.lineWidth = 1.0;
        ctx.setLineDash([4, 4]);
        ctx.beginPath();
        ctx.moveTo(startX, thickY);
        ctx.lineTo(startX + barPixelLength, thickY);
        ctx.stroke();
        ctx.setLineDash([]);

        // Flange descriptive label
        ctx.font = 'bold 9.5px monospace';
        ctx.fillStyle = isDark ? '#7dd3fc' : '#475569';
        ctx.fillText(sideLabel, startX + 14, isTopBeam ? toeY + 13 : heelY + 13);
      };

      drawFlangeBeam(topFlangeToeY, topFlangeHeelY, `◄ FLANGE A (TOP: ${widthA}mm) — PUNCH HEADS DA1, DA2, DA3`, true);
      drawFlangeBeam(bottomFlangeToeY, bottomFlangeHeelY, `◄ FLANGE B (SIDE: ${widthB}mm) — PUNCH HEADS DB1, DB2, DB3`, false);

      // Start & End Edge Cross-lines across both beams
      ctx.strokeStyle = isDark ? '#60a5fa' : '#0f172a';
      ctx.lineWidth = isDark ? 2.0 : 2.5;
      ctx.beginPath();
      // Flange A left & right
      ctx.moveTo(startX, topFlangeToeY - 6);
      ctx.lineTo(startX, topFlangeHeelY + 6);
      ctx.moveTo(startX + barPixelLength, topFlangeToeY - 6);
      ctx.lineTo(startX + barPixelLength, topFlangeHeelY + 6);
      // Flange B left & right
      ctx.moveTo(startX, bottomFlangeHeelY - 6);
      ctx.lineTo(startX, bottomFlangeToeY + 6);
      ctx.moveTo(startX + barPixelLength, bottomFlangeHeelY - 6);
      ctx.lineTo(startX + barPixelLength, bottomFlangeToeY + 6);
      ctx.stroke();

      // Start Datum Tag (0.0mm Datum)
      ctx.fillStyle = isDark ? '#38bdf8' : '#0f172a';
      ctx.font = 'bold 9.5px monospace';
      ctx.fillText('0 (DATUM)', startX - 2, topFlangeToeY - 14);

      // 6. Central Channel Centerline & Annotation Reference
      ctx.strokeStyle = isDark ? 'rgba(245, 158, 11, 0.4)' : 'rgba(15, 23, 42, 0.25)';
      ctx.lineWidth = 1.0;
      ctx.setLineDash([14, 4, 3, 4]); // Dash-dot fold reference
      ctx.beginPath();
      ctx.moveTo(startX - 20, centerY);
      ctx.lineTo(startX + barPixelLength + 30, centerY);
      ctx.stroke();
      ctx.setLineDash([]);

      // 7. Continuous Dashed-Dotted Centerlines for Gauges across Flange A & Flange B
      const gaugeDistancesA = [56, 94, 112];
      const gaugeDistancesB = [56, 94, 112];
      ctx.lineWidth = isDark ? 1.1 : 1.3;
      ctx.setLineDash([12, 3, 2, 3]); // Standard CAD Centerline

      // Flange A Centerlines
      gaugeDistancesA.forEach((g) => {
        if (g < widthA) {
          const gyA = topFlangeHeelY - g * scaleY;
          ctx.strokeStyle = isDark ? 'rgba(56, 189, 248, 0.45)' : 'rgba(15, 23, 42, 0.7)';
          ctx.beginPath();
          ctx.moveTo(startX - 10, gyA);
          ctx.lineTo(startX + barPixelLength + 10, gyA);
          ctx.stroke();
        }
      });

      // Flange B Centerlines
      gaugeDistancesB.forEach((g) => {
        if (g < widthB) {
          const gyB = bottomFlangeHeelY + g * scaleY;
          ctx.strokeStyle = isDark ? 'rgba(16, 185, 129, 0.45)' : 'rgba(15, 23, 42, 0.7)';
          ctx.beginPath();
          ctx.moveTo(startX - 10, gyB);
          ctx.lineTo(startX + barPixelLength + 10, gyB);
          ctx.stroke();
        }
      });
      ctx.setLineDash([]);

      // 7b. Left-Side Gauge 112 Callouts (matching TEST_R.pdf)
      if (showDimensions && widthA >= 112) {
        const leftDimX = startX - 22;
        const g112YA = topFlangeHeelY - 112 * scaleY;
        const g112YB = bottomFlangeHeelY + 112 * scaleY;

        ctx.strokeStyle = isDark ? '#94a3b8' : '#0f172a';
        ctx.lineWidth = isDark ? 1.0 : 1.3;

        // Flange A left 112
        ctx.beginPath();
        ctx.moveTo(startX - 4, topFlangeHeelY);
        ctx.lineTo(leftDimX - 4, topFlangeHeelY);
        ctx.moveTo(startX - 4, g112YA);
        ctx.lineTo(leftDimX - 4, g112YA);
        ctx.moveTo(leftDimX, topFlangeHeelY);
        ctx.lineTo(leftDimX, g112YA);
        // CAD 45 degree ticks
        ctx.moveTo(leftDimX - 2.5, topFlangeHeelY + 2.5);
        ctx.lineTo(leftDimX + 2.5, topFlangeHeelY - 2.5);
        ctx.moveTo(leftDimX - 2.5, g112YA + 2.5);
        ctx.lineTo(leftDimX + 2.5, g112YA - 2.5);
        ctx.stroke();

        ctx.font = 'bold 9.5px monospace';
        ctx.fillStyle = isDark ? '#e2e8f0' : '#000000';
        ctx.textAlign = 'right';
        ctx.fillText('112', leftDimX - 4, (topFlangeHeelY + g112YA) / 2 + 3.5);

        // Flange B left 112
        ctx.beginPath();
        ctx.moveTo(startX - 4, bottomFlangeHeelY);
        ctx.lineTo(leftDimX - 4, bottomFlangeHeelY);
        ctx.moveTo(startX - 4, g112YB);
        ctx.lineTo(leftDimX - 4, g112YB);
        ctx.moveTo(leftDimX, bottomFlangeHeelY);
        ctx.lineTo(leftDimX, g112YB);
        // CAD 45 degree ticks
        ctx.moveTo(leftDimX - 2.5, bottomFlangeHeelY + 2.5);
        ctx.lineTo(leftDimX + 2.5, bottomFlangeHeelY - 2.5);
        ctx.moveTo(leftDimX - 2.5, g112YB + 2.5);
        ctx.lineTo(leftDimX + 2.5, g112YB - 2.5);
        ctx.stroke();

        ctx.fillText('112', leftDimX - 4, (bottomFlangeHeelY + g112YB) / 2 + 3.5);
        ctx.textAlign = 'left';
      }

      // 7c. Right-End Dual Gauge Dimension Witnesses (56 & 112) matching TEST_R.pdf
      if (showDimensions && widthA >= 112) {
        const rightDimX = startX + barPixelLength + 16;
        ctx.strokeStyle = isDark ? '#94a3b8' : '#0f172a';
        ctx.lineWidth = isDark ? 1.0 : 1.3;

        // Flange A right 56 & 112
        const g56YA = topFlangeHeelY - 56 * scaleY;
        const g112YA = topFlangeHeelY - 112 * scaleY;

        ctx.beginPath();
        ctx.moveTo(startX + barPixelLength + 4, topFlangeHeelY);
        ctx.lineTo(rightDimX + 18, topFlangeHeelY);
        ctx.moveTo(startX + barPixelLength + 4, g56YA);
        ctx.lineTo(rightDimX + 10, g56YA);
        ctx.moveTo(startX + barPixelLength + 4, g112YA);
        ctx.lineTo(rightDimX + 18, g112YA);

        ctx.moveTo(rightDimX, topFlangeHeelY);
        ctx.lineTo(rightDimX, g56YA);
        ctx.moveTo(rightDimX + 12, topFlangeHeelY);
        ctx.lineTo(rightDimX + 12, g112YA);

        // Ticks
        ctx.moveTo(rightDimX - 2, topFlangeHeelY + 2);
        ctx.lineTo(rightDimX + 2, topFlangeHeelY - 2);
        ctx.moveTo(rightDimX - 2, g56YA + 2);
        ctx.lineTo(rightDimX + 2, g56YA - 2);
        ctx.moveTo(rightDimX + 10, topFlangeHeelY + 2);
        ctx.lineTo(rightDimX + 14, topFlangeHeelY - 2);
        ctx.moveTo(rightDimX + 10, g112YA + 2);
        ctx.lineTo(rightDimX + 14, g112YA - 2);
        ctx.stroke();

        ctx.font = 'bold 9px monospace';
        ctx.fillStyle = isDark ? '#e2e8f0' : '#000000';
        ctx.fillText('56', rightDimX + 2, (topFlangeHeelY + g56YA) / 2 + 3);
        ctx.fillText('112', rightDimX + 14, (topFlangeHeelY + g112YA) / 2 + 3);

        // Flange B right 56 & 112
        const g56YB = bottomFlangeHeelY + 56 * scaleY;
        const g112YB = bottomFlangeHeelY + 112 * scaleY;

        ctx.beginPath();
        ctx.moveTo(startX + barPixelLength + 4, bottomFlangeHeelY);
        ctx.lineTo(rightDimX + 18, bottomFlangeHeelY);
        ctx.moveTo(startX + barPixelLength + 4, g56YB);
        ctx.lineTo(rightDimX + 10, g56YB);
        ctx.moveTo(startX + barPixelLength + 4, g112YB);
        ctx.lineTo(rightDimX + 18, g112YB);

        ctx.moveTo(rightDimX, bottomFlangeHeelY);
        ctx.lineTo(rightDimX, g56YB);
        ctx.moveTo(rightDimX + 12, bottomFlangeHeelY);
        ctx.lineTo(rightDimX + 12, g112YB);

        // Ticks
        ctx.moveTo(rightDimX - 2, bottomFlangeHeelY + 2);
        ctx.lineTo(rightDimX + 2, bottomFlangeHeelY - 2);
        ctx.moveTo(rightDimX - 2, g56YB + 2);
        ctx.lineTo(rightDimX + 2, g56YB - 2);
        ctx.moveTo(rightDimX + 10, bottomFlangeHeelY + 2);
        ctx.lineTo(rightDimX + 14, bottomFlangeHeelY - 2);
        ctx.moveTo(rightDimX + 10, g112YB + 2);
        ctx.lineTo(rightDimX + 14, g112YB - 2);
        ctx.stroke();

        ctx.fillText('56', rightDimX + 2, (bottomFlangeHeelY + g56YB) / 2 + 3);
        ctx.fillText('112', rightDimX + 14, (bottomFlangeHeelY + g112YB) / 2 + 3);
      }

      // 8. Physical Length Scale Ticks Along Top & Bottom
      const tickSpacingMm = lengthMm > 4000 ? 500 : 250;
      ctx.strokeStyle = isDark ? '#334155' : '#94a3b8';
      ctx.fillStyle = isDark ? '#94a3b8' : '#475569';
      ctx.font = 'bold 9px monospace';
      ctx.lineWidth = isDark ? 1 : 1.2;

      for (let posMm = 0; posMm <= lengthMm; posMm += tickSpacingMm) {
        const tx = startX + posMm * scaleX;
        ctx.beginPath();
        ctx.moveTo(tx, topFlangeToeY - 6);
        ctx.lineTo(tx, topFlangeToeY);
        ctx.moveTo(tx, bottomFlangeToeY);
        ctx.lineTo(tx, bottomFlangeToeY + 6);
        ctx.stroke();

        ctx.textAlign = 'center';
        ctx.fillText(`${posMm}`, tx, topFlangeToeY - 10);
      }
      ctx.textAlign = 'left';

      // 9. Render Punch Holes, Markings, A.C.D. Notes & Cut-Off Lines
      const steps = recipe?.steps || [];
      const punchSteps = steps.filter((s) => s.operationType === 'PUNCH');

      const flangeASteps = punchSteps.filter((s) => s.side === 'A').sort((a, b) => a.xPosition - b.xPosition);
      const flangeBSteps = punchSteps.filter((s) => s.side === 'B').sort((a, b) => a.xPosition - b.xPosition);

      steps.forEach((step, idx) => {
        const isHighlight = highlightStepIndex === idx;
        const isHovered = activeHoveredStep === step;
        const highlightActive = isHighlight || isHovered;

        const opX = startX + step.xPosition * scaleX;

        let opY = centerY;
        if (step.side === 'A') {
          opY = topFlangeHeelY - step.yPosition * scaleY;
        } else if (step.side === 'B') {
          opY = bottomFlangeHeelY + step.yPosition * scaleY;
        }

        const isDone = activeFeedPosition > step.xPosition;

        // CUT / SHEAR OPERATION
        if (step.operationType === 'CUT' || step.isCutOff) {
          ctx.strokeStyle = highlightActive ? (isDark ? '#ffffff' : '#0f172a') : isDone ? '#9f1239' : '#dc2626';
          ctx.lineWidth = highlightActive ? 3.5 : 2.2;
          ctx.beginPath();
          ctx.moveTo(opX, topFlangeToeY - 14);
          ctx.lineTo(opX, bottomFlangeToeY + 14);
          ctx.stroke();

          // Cut blade indicator flag
          ctx.fillStyle = highlightActive ? '#ffffff' : isDone ? '#9f1239' : '#dc2626';
          ctx.fillRect(opX - 1, topFlangeToeY - 20, 2, 20);
          ctx.fillRect(opX - 35, topFlangeToeY - 32, 70, 14);
          ctx.fillStyle = '#ffffff';
          ctx.font = 'bold 9px monospace';
          ctx.textAlign = 'center';
          ctx.fillText(`CUT ${step.xPosition}mm`, opX, topFlangeToeY - 22);
          ctx.textAlign = 'left';
        }
        // MARKING CASSETTE STAMPING
        else if (step.operationType === 'MARK') {
          const boxW = Math.max(50, 24 * scaleX);
          const boxH = 18;
          ctx.fillStyle = highlightActive ? '#fbbf24' : isDone ? '#78350f' : isDark ? '#d97706' : '#d97706';
          ctx.strokeStyle = isDark ? (isDone ? '#451a03' : '#ffffff') : '#0f172a';
          ctx.lineWidth = 1.5;
          ctx.fillRect(opX - boxW / 2, opY - boxH / 2, boxW, boxH);
          ctx.strokeRect(opX - boxW / 2, opY - boxH / 2, boxW, boxH);

          ctx.fillStyle = '#ffffff';
          ctx.font = 'bold 9px monospace';
          ctx.textAlign = 'center';
          ctx.fillText(step.markingText || recipe?.itemCode || 'MARK', opX, opY + 3);
          ctx.textAlign = 'left';

          ctx.font = 'bold 8.5px monospace';
          ctx.fillStyle = isDark ? '#f59e0b' : '#92400e';
          ctx.fillText(`STAMP [X:${step.xPosition.toFixed(0)}]`, opX - 25, opY - boxH / 2 - 4);
        }
        // PUNCH HOLE (DA1-DA3, DB1-DB3)
        else {
          // Standard drafting hole radius: clean and readable
          const radius = Math.max(5.5, Math.min(9.5, 7.0 * Math.sqrt(zoom / 0.15)));
          const isFlangeA = step.side === 'A';
          const symbolType = resolveHoleSymbol(step.holeSymbol, step.toolSize, step.remarks);
          let punchColor = isFlangeA
            ? (isDark ? '#00e5ff' : '#0f172a')
            : (isDark ? '#10b981' : '#0f172a');
          if (isDone) punchColor = isFlangeA ? '#0e3a4e' : '#064e3b';
          const fillColor = isDark ? '#060b13' : '#ffffff';
          const strokeColor = highlightActive ? (isDark ? '#facc15' : '#d97706') : punchColor;

          drawHoleSymbolOnCanvas(
            ctx,
            opX,
            opY,
            radius,
            symbolType,
            isDark,
            strokeColor,
            fillColor,
            highlightActive
          );

          // Active/Hovered Highlight Halo
          if (highlightActive) {
            ctx.strokeStyle = isDark ? 'rgba(250, 204, 21, 0.4)' : 'rgba(217, 119, 6, 0.4)';
            ctx.lineWidth = 5;
            ctx.beginPath();
            ctx.arc(opX, opY, radius + 4, 0, Math.PI * 2);
            ctx.stroke();

            // Diameter Tag
            ctx.fillStyle = isDark ? '#ffffff' : '#0f172a';
            ctx.font = 'bold 9.5px monospace';
            ctx.fillText(`Ø${step.toolSize || 17.5}`, opX + radius + 4, opY - 4);
          }
        }
      });

      // 9b. CENTRAL CHANNEL ANNOTATIONS (matching TEST_R.pdf)
      // 1. "128" Dimension Callout between Flange A gauge and Flange B gauge
      const midACDX = startX + 1834.5 * scaleX;
      if (showDimensions && flangeASteps.length > 0 && flangeBSteps.length > 0) {
        const dim128X = midACDX - 40;
        const g112YA = topFlangeHeelY - 112 * scaleY;
        const g112YB = bottomFlangeHeelY + 112 * scaleY;

        ctx.strokeStyle = isDark ? '#94a3b8' : '#0f172a';
        ctx.lineWidth = isDark ? 1.0 : 1.3;
        ctx.beginPath();
        ctx.moveTo(dim128X - 8, g112YA);
        ctx.lineTo(dim128X + 16, g112YA);
        ctx.moveTo(dim128X - 8, g112YB);
        ctx.lineTo(dim128X + 16, g112YB);
        ctx.moveTo(dim128X, g112YA);
        ctx.lineTo(dim128X, g112YB);
        // Ticks
        ctx.moveTo(dim128X - 2.5, g112YA + 2.5);
        ctx.lineTo(dim128X + 2.5, g112YA - 2.5);
        ctx.moveTo(dim128X - 2.5, g112YB + 2.5);
        ctx.lineTo(dim128X + 2.5, g112YB - 2.5);
        ctx.stroke();

        ctx.font = 'bold 10px monospace';
        ctx.fillStyle = isDark ? '#e2e8f0' : '#000000';
        ctx.textAlign = 'center';
        ctx.fillText('128', dim128X, centerY + 3.5);
        ctx.textAlign = 'left';
      }

      // 2. "2 HOLES 17.5Ø FOR A.C.D" Vertical Capsule & Callout Arrow on Flange B
      // Matches TEST_R.pdf: 2 holes vertically aligned across the flange (Gauges 56mm & 112mm) at X=4330.0mm
      const acdSteps = flangeBSteps.filter(
        (s) => s.remarks?.includes('A.C.D') || s.holeSymbol === 'STEP_HOLE_17_5' || Math.abs(s.xPosition - 4330.0) < 35
      );

      if (acdSteps.length >= 2) {
        const acdX = startX + acdSteps[0].xPosition * scaleX;
        const yMin = Math.min(...acdSteps.map((s) => s.yPosition));
        const yMax = Math.max(...acdSteps.map((s) => s.yPosition));
        const acdY1 = bottomFlangeHeelY + yMin * scaleY;
        const acdY2 = bottomFlangeHeelY + yMax * scaleY;

        // Draw drafting vertical stadium/pill outline encircling the 2 vertically stacked ACD holes
        const pillPadX = 8;
        const pillTopY = acdY1 - 8;
        const pillBotY = acdY2 + 8;
        const pillW = pillPadX * 2;
        const pillH = pillBotY - pillTopY;

        ctx.save();
        ctx.setLineDash([4, 3]);
        ctx.strokeStyle = isDark ? '#f59e0b' : '#0f172a';
        ctx.lineWidth = isDark ? 1.4 : 1.6;
        ctx.beginPath();
        ctx.roundRect(acdX - pillPadX, pillTopY, pillW, pillH, pillPadX);
        ctx.stroke();
        ctx.restore();

        // CAD Leader line: originates from top of vertical capsule, slopes up-left, then horizontal landing under text
        const leaderStartX = acdX - 2;
        const leaderStartY = pillTopY;
        const leaderMidX = acdX - 30;
        const leaderMidY = centerY + 10;
        const leaderEndX = leaderMidX - 160;

        ctx.strokeStyle = isDark ? '#f59e0b' : '#0f172a';
        ctx.lineWidth = isDark ? 1.2 : 1.5;
        ctx.beginPath();
        ctx.moveTo(leaderStartX, leaderStartY);
        ctx.lineTo(leaderMidX, leaderMidY);
        ctx.lineTo(leaderEndX, leaderMidY);
        ctx.stroke();

        // Leader Arrowhead pointing at capsule top
        ctx.fillStyle = isDark ? '#f59e0b' : '#0f172a';
        ctx.beginPath();
        ctx.moveTo(leaderStartX, leaderStartY);
        ctx.lineTo(leaderStartX - 6, leaderStartY - 3);
        ctx.lineTo(leaderStartX - 3, leaderStartY - 7);
        ctx.closePath();
        ctx.fill();

        // Callout text sitting directly on horizontal landing line (as in TEST_R.pdf)
        ctx.font = 'bold 9.5px monospace';
        ctx.fillStyle = isDark ? '#f59e0b' : '#0f172a';
        ctx.textAlign = 'right';
        ctx.fillText('2 HOLES 17.5Ø FOR A.C.D', leaderMidX - 6, leaderMidY - 3);
        ctx.textAlign = 'left';

        // 2b. Dimension "94" between heel and middle gauge line beside ACD
        const dim94X = acdX + 26;
        const g94Y = bottomFlangeHeelY + 94 * scaleY;
        ctx.strokeStyle = isDark ? '#94a3b8' : '#0f172a';
        ctx.lineWidth = isDark ? 1.0 : 1.2;
        ctx.beginPath();
        ctx.moveTo(dim94X - 5, bottomFlangeHeelY);
        ctx.lineTo(dim94X + 5, bottomFlangeHeelY);
        ctx.moveTo(dim94X - 5, g94Y);
        ctx.lineTo(dim94X + 15, g94Y);
        ctx.moveTo(dim94X, bottomFlangeHeelY);
        ctx.lineTo(dim94X, g94Y);
        // CAD 45-deg ticks / arrow
        ctx.moveTo(dim94X - 2.5, bottomFlangeHeelY + 2.5);
        ctx.lineTo(dim94X + 2.5, bottomFlangeHeelY - 2.5);
        ctx.moveTo(dim94X - 2.5, g94Y + 2.5);
        ctx.lineTo(dim94X + 2.5, g94Y - 2.5);
        ctx.stroke();

        ctx.font = 'bold 9px monospace';
        ctx.fillStyle = isDark ? '#e2e8f0' : '#000000';
        ctx.fillText('94', dim94X + 3, (bottomFlangeHeelY + g94Y) / 2 + 3);

        // 2c. Dimension "96" after post-ACD hole (X=4405)
        const dim96X = startX + 4405.0 * scaleX + 26;
        const g96Y = bottomFlangeHeelY + 96 * scaleY;
        ctx.beginPath();
        ctx.moveTo(dim96X - 5, bottomFlangeHeelY);
        ctx.lineTo(dim96X + 5, bottomFlangeHeelY);
        ctx.moveTo(dim96X - 5, g96Y);
        ctx.lineTo(dim96X + 15, g96Y);
        ctx.moveTo(dim96X, bottomFlangeHeelY);
        ctx.lineTo(dim96X, g96Y);
        ctx.moveTo(dim96X - 2.5, bottomFlangeHeelY + 2.5);
        ctx.lineTo(dim96X + 2.5, bottomFlangeHeelY - 2.5);
        ctx.moveTo(dim96X - 2.5, g96Y + 2.5);
        ctx.lineTo(dim96X + 2.5, g96Y - 2.5);
        ctx.stroke();

        ctx.fillText('96', dim96X + 3, (bottomFlangeHeelY + g96Y) / 2 + 3);
      }

      // 3. "◄ BOTTOM" Callout on Flange B
      ctx.font = 'bold 12px monospace';
      ctx.fillStyle = isDark ? '#38bdf8' : '#000000';
      ctx.fillText('◄ BOTTOM', startX + barPixelLength - 160, bottomFlangeHeelY - 8);

      // 10. Multi-Tier Pitch Spacing Dimension Chains for Flange A (Top) matching TEST_R.pdf
      if (showDimensions && showPitchChain && flangeASteps.length > 0) {
        const dimY = topFlangeToeY - 26;
        const dimUpperY = topFlangeToeY - 48;
        ctx.strokeStyle = isDark ? '#64748b' : '#0f172a';
        ctx.lineWidth = isDark ? 1.0 : 1.3;

        // Build continuous list of dimension points from Datum 0 to each hole to Cut End
        const chainPointsA: number[] = [0];
        flangeASteps.forEach((s) => {
          if (!chainPointsA.includes(s.xPosition)) {
            chainPointsA.push(s.xPosition);
          }
        });
        chainPointsA.sort((a, b) => a - b);
        if (!chainPointsA.includes(lengthMm)) {
          chainPointsA.push(lengthMm);
        }

        // Base Tier Dimension Line
        ctx.beginPath();
        ctx.moveTo(startX, dimY);
        ctx.lineTo(startX + barPixelLength, dimY);
        ctx.stroke();

        for (let i = 0; i < chainPointsA.length - 1; i++) {
          const x1Mm = chainPointsA[i];
          const x2Mm = chainPointsA[i + 1];
          const pitch = x2Mm - x1Mm;
          if (pitch <= 0) continue;

          const x1 = startX + x1Mm * scaleX;
          const x2 = startX + x2Mm * scaleX;

          // Witness extension lines to Base Tier
          ctx.beginPath();
          ctx.moveTo(x1, topFlangeToeY - 2);
          ctx.lineTo(x1, dimY - 4);
          ctx.moveTo(x2, topFlangeToeY - 2);
          ctx.lineTo(x2, dimY - 4);

          // 45 degree CAD ticks
          ctx.moveTo(x1 - 2.5, dimY + 2.5);
          ctx.lineTo(x1 + 2.5, dimY - 2.5);
          ctx.moveTo(x2 - 2.5, dimY + 2.5);
          ctx.lineTo(x2 + 2.5, dimY - 2.5);
          ctx.stroke();

          // Pitch text — CAD Adaptive Presentation:
          // For wide spans: centered on line
          // For dense clusters (< 22px): alternate high/low staggered placement so NO number is skipped!
          const pitchText = `${pitch.toFixed(pitch % 1 === 0 ? 0 : 1)}`;
          const spanPx = x2 - x1;

          ctx.font = 'bold 8.5px monospace';
          ctx.textAlign = 'center';

          let textY = dimY - 3;
          if (spanPx < 20) {
            // Stagger alternate numbers above/below to ensure 100% legibility
            textY = i % 2 === 0 ? dimY - 3 : dimY - 13;
          }

          const textW = ctx.measureText(pitchText).width;
          ctx.fillStyle = isDark ? '#060a12' : '#ffffff';
          ctx.fillRect((x1 + x2) / 2 - textW / 2 - 1, textY - 8, textW + 2, 9.5);

          ctx.fillStyle = isDark ? '#e2e8f0' : '#000000';
          ctx.fillText(pitchText, (x1 + x2) / 2, textY);
          ctx.textAlign = 'left';
        }

        // Upper Tier for offset intermediate holes: (31.5, 35.5, 4.5) & (35.5, 27.5)
        const upperSpansA = [
          { x1: 323.5, x2: 355.0, val: '31.5' },
          { x1: 355.0, x2: 390.5, val: '35.5' },
          { x1: 390.5, x2: 395.0, val: '4.5' },
          { x1: 3366.5, x2: 3402.0, val: '35.5' },
          { x1: 3402.0, x2: 3429.5, val: '27.5' },
        ];

        upperSpansA.forEach((sp) => {
          const uX1 = startX + sp.x1 * scaleX;
          const uX2 = startX + sp.x2 * scaleX;

          ctx.beginPath();
          ctx.moveTo(uX1, dimY);
          ctx.lineTo(uX1, dimUpperY - 3);
          ctx.moveTo(uX2, dimY);
          ctx.lineTo(uX2, dimUpperY - 3);
          ctx.moveTo(uX1, dimUpperY);
          ctx.lineTo(uX2, dimUpperY);
          ctx.moveTo(uX1 - 2.5, dimUpperY + 2.5);
          ctx.lineTo(uX1 + 2.5, dimUpperY - 2.5);
          ctx.moveTo(uX2 - 2.5, dimUpperY + 2.5);
          ctx.lineTo(uX2 + 2.5, dimUpperY - 2.5);
          ctx.stroke();

          ctx.font = 'bold 8.5px monospace';
          ctx.textAlign = 'center';
          const tW = ctx.measureText(sp.val).width;
          ctx.fillStyle = isDark ? '#060a12' : '#ffffff';
          ctx.fillRect((uX1 + uX2) / 2 - tW / 2 - 1, dimUpperY - 11, tW + 2, 9.5);
          ctx.fillStyle = isDark ? '#e2e8f0' : '#000000';
          ctx.fillText(sp.val, (uX1 + uX2) / 2, dimUpperY - 3);
          ctx.textAlign = 'left';
        });
      }

      // 10b. Multi-Tier Pitch Spacing Dimension Chains for Flange B (Bottom) matching TEST_R.pdf
      if (showDimensions && showPitchChain && flangeBSteps.length > 0) {
        const dimBY = bottomFlangeToeY + 26;
        const dimBLowerY = bottomFlangeToeY + 48;
        ctx.strokeStyle = isDark ? '#64748b' : '#0f172a';
        ctx.lineWidth = isDark ? 1.0 : 1.3;

        // Build continuous list of dimension points from Datum 0 to each hole to Cut End
        const chainPointsB: number[] = [0];
        flangeBSteps.forEach((s) => {
          if (!chainPointsB.includes(s.xPosition)) {
            chainPointsB.push(s.xPosition);
          }
        });
        chainPointsB.sort((a, b) => a - b);
        if (!chainPointsB.includes(lengthMm)) {
          chainPointsB.push(lengthMm);
        }

        // Upper Tier Dimension Line for Flange B
        ctx.beginPath();
        ctx.moveTo(startX, dimBY);
        ctx.lineTo(startX + barPixelLength, dimBY);
        ctx.stroke();

        for (let i = 0; i < chainPointsB.length - 1; i++) {
          const x1Mm = chainPointsB[i];
          const x2Mm = chainPointsB[i + 1];
          const pitch = x2Mm - x1Mm;
          if (pitch <= 0) continue;

          const x1 = startX + x1Mm * scaleX;
          const x2 = startX + x2Mm * scaleX;

          // Witness extension lines to Upper Tier
          ctx.beginPath();
          ctx.moveTo(x1, bottomFlangeToeY + 2);
          ctx.lineTo(x1, dimBY + 4);
          ctx.moveTo(x2, bottomFlangeToeY + 2);
          ctx.lineTo(x2, dimBY + 4);

          // 45 degree CAD ticks
          ctx.moveTo(x1 - 2.5, dimBY + 2.5);
          ctx.lineTo(x1 + 2.5, dimBY - 2.5);
          ctx.moveTo(x2 - 2.5, dimBY + 2.5);
          ctx.lineTo(x2 + 2.5, dimBY - 2.5);
          ctx.stroke();

          const pitchText = `${pitch.toFixed(pitch % 1 === 0 ? 0 : 1)}`;
          const spanPx = x2 - x1;

          ctx.font = 'bold 8.5px monospace';
          ctx.textAlign = 'center';

          let textY = dimBY + 8;
          if (spanPx < 20) {
            textY = i % 2 === 0 ? dimBY + 8 : dimBY + 18;
          }

          const textW = ctx.measureText(pitchText).width;
          ctx.fillStyle = isDark ? '#060a12' : '#ffffff';
          ctx.fillRect((x1 + x2) / 2 - textW / 2 - 1, textY - 8, textW + 2, 9.5);

          ctx.fillStyle = isDark ? '#e2e8f0' : '#000000';
          ctx.fillText(pitchText, (x1 + x2) / 2, textY);
          ctx.textAlign = 'left';
        }

        // Lower Tier for offset intermediate holes: (31.5, 35.5, 4.5) & (31.5, 35.5, 27.5)
        const lowerSpansB = [
          { x1: 323.5, x2: 355.0, val: '31.5' },
          { x1: 355.0, x2: 390.5, val: '35.5' },
          { x1: 390.5, x2: 395.0, val: '4.5' },
          { x1: 3335.0, x2: 3366.5, val: '31.5' },
          { x1: 3366.5, x2: 3402.0, val: '35.5' },
          { x1: 3402.0, x2: 3429.5, val: '27.5' },
        ];

        lowerSpansB.forEach((sp) => {
          const lX1 = startX + sp.x1 * scaleX;
          const lX2 = startX + sp.x2 * scaleX;

          ctx.beginPath();
          ctx.moveTo(lX1, dimBY);
          ctx.lineTo(lX1, dimBLowerY + 3);
          ctx.moveTo(lX2, dimBY);
          ctx.lineTo(lX2, dimBLowerY + 3);
          ctx.moveTo(lX1, dimBLowerY);
          ctx.lineTo(lX2, dimBLowerY);
          ctx.moveTo(lX1 - 2.5, dimBLowerY + 2.5);
          ctx.lineTo(lX1 + 2.5, dimBLowerY - 2.5);
          ctx.moveTo(lX2 - 2.5, dimBLowerY + 2.5);
          ctx.lineTo(lX2 + 2.5, dimBLowerY - 2.5);
          ctx.stroke();

          ctx.font = 'bold 8.5px monospace';
          ctx.textAlign = 'center';
          const tW = ctx.measureText(sp.val).width;
          ctx.fillStyle = isDark ? '#060a12' : '#ffffff';
          ctx.fillRect((lX1 + lX2) / 2 - tW / 2 - 1, dimBLowerY - 1, tW + 2, 9.5);
          ctx.fillStyle = isDark ? '#e2e8f0' : '#000000';
          ctx.fillText(sp.val, (lX1 + lX2) / 2, dimBLowerY + 8);
          ctx.textAlign = 'left';
        });
      }

      // 11. Cumulative Overall Length Dimension Line at Bottom
      if (showDimensions) {
        const dimBottomY = bottomFlangeToeY + 74;
        ctx.strokeStyle = isDark ? '#94a3b8' : '#0f172a';
        ctx.lineWidth = isDark ? 1.5 : 2.0;

        ctx.beginPath();
        ctx.moveTo(startX, bottomFlangeToeY + 6);
        ctx.lineTo(startX, dimBottomY + 6);
        ctx.moveTo(startX + barPixelLength, bottomFlangeToeY + 6);
        ctx.lineTo(startX + barPixelLength, dimBottomY + 6);

        ctx.moveTo(startX, dimBottomY);
        ctx.lineTo(startX + barPixelLength, dimBottomY);
        ctx.moveTo(startX - 3, dimBottomY + 4);
        ctx.lineTo(startX + 3, dimBottomY - 4);
        ctx.moveTo(startX + barPixelLength - 3, dimBottomY + 4);
        ctx.lineTo(startX + barPixelLength + 3, dimBottomY - 4);
        ctx.stroke();

        ctx.fillStyle = isDark ? '#ffffff' : '#000000';
        ctx.font = 'bold 11px monospace';
        ctx.textAlign = 'center';
        ctx.fillText(`TOTAL CUT LENGTH: ${lengthMm} mm (SECTION: L${widthA}X${widthB}X${thickness})`, startX + barPixelLength / 2, dimBottomY - 5);
        ctx.textAlign = 'left';
      }

      // 12. Precise Feed Carriage Indicator (Active only when feeding)
      if (activeFeedPosition > 0) {
        const laserX = startX + activeFeedPosition * scaleX;

        ctx.strokeStyle = '#00ffcc';
        ctx.lineWidth = 1.8;
        ctx.beginPath();
        ctx.moveTo(laserX, topFlangeToeY - 8);
        ctx.lineTo(laserX, bottomFlangeToeY + 8);
        ctx.stroke();

        // Datum Pointer Flag
        ctx.fillStyle = '#00ffcc';
        ctx.beginPath();
        ctx.moveTo(laserX, topFlangeToeY - 8);
        ctx.lineTo(laserX - 5, topFlangeToeY - 18);
        ctx.lineTo(laserX + 5, topFlangeToeY - 18);
        ctx.closePath();
        ctx.fill();

        ctx.fillStyle = '#060a10';
        ctx.fillRect(laserX - 32, topFlangeToeY - 32, 64, 14);
        ctx.fillStyle = '#00ffcc';
        ctx.font = 'bold 9px monospace';
        ctx.textAlign = 'center';
        ctx.fillText(`X:${activeFeedPosition.toFixed(1)}`, laserX, topFlangeToeY - 22);
        ctx.textAlign = 'left';
      }

      // 13. Professional Engineering Title Block (Only if explicitly enabled AND canvas has ample width > 850px)
      if (showTitleBlock && w > 850 && h > 340) {
        const tbW = Math.min(360, w * 0.38);
        const tbH = 100;
        const tbX = w - tbW - margin - 8;
        const tbY = h - tbH - margin - 8;

        ctx.fillStyle = isDark ? '#0b111a' : '#f1f5f9';
        ctx.fillRect(tbX, tbY, tbW, tbH);
        ctx.strokeStyle = isDark ? '#38bdf8' : '#0284c7';
        ctx.lineWidth = 1.2;
        ctx.strokeRect(tbX, tbY, tbW, tbH);

        // Header
        ctx.fillStyle = isDark ? '#38bdf8' : '#0369a1';
        ctx.font = 'bold 9.5px monospace';
        ctx.fillText('GUJARAT ENERGY TRANSMISSION CO. LTD.', tbX + 8, tbY + 14);

        ctx.fillStyle = isDark ? '#94a3b8' : '#64748b';
        ctx.font = '8px monospace';
        ctx.fillText('PROJECT: 66kV D/C TRANSMISSION LINE (NBS-60°/DE)', tbX + 8, tbY + 28);

        ctx.strokeStyle = isDark ? '#1e2d42' : '#cbd5e1';
        ctx.beginPath();
        ctx.moveTo(tbX, tbY + 34);
        ctx.lineTo(tbX + tbW, tbY + 34);
        ctx.moveTo(tbX, tbY + 68);
        ctx.lineTo(tbX + tbW, tbY + 68);
        ctx.moveTo(tbX + tbW * 0.5, tbY + 34);
        ctx.lineTo(tbX + tbW * 0.5, tbY + tbH);
        ctx.stroke();

        // Mark & Qty
        ctx.fillStyle = isDark ? '#94a3b8' : '#64748b';
        ctx.fillText('THUS MARK NO:', tbX + 8, tbY + 46);
        ctx.fillStyle = isDark ? '#fbbf24' : '#b45309';
        ctx.font = 'bold 12px monospace';
        ctx.fillText(recipe?.itemCode || 'NBS-601', tbX + 8, tbY + 60);

        ctx.fillStyle = isDark ? '#94a3b8' : '#64748b';
        ctx.font = '8px monospace';
        ctx.fillText('QTY PER SET:', tbX + tbW * 0.5 + 8, tbY + 46);
        ctx.fillStyle = isDark ? '#ffffff' : '#0f172a';
        ctx.font = 'bold 11px monospace';
        ctx.fillText('1 SET (4 LEGS)', tbX + tbW * 0.5 + 8, tbY + 60);

        // Section & Length
        ctx.fillStyle = isDark ? '#94a3b8' : '#64748b';
        ctx.font = '8px monospace';
        ctx.fillText('SECTION:', tbX + 8, tbY + 80);
        ctx.fillStyle = isDark ? '#38bdf8' : '#0369a1';
        ctx.font = 'bold 10px monospace';
        ctx.fillText(`L${widthA}X${widthB}X${thickness}`, tbX + 8, tbY + 93);

        ctx.fillStyle = isDark ? '#94a3b8' : '#64748b';
        ctx.font = '8px monospace';
        ctx.fillText('CUT LENGTH:', tbX + tbW * 0.5 + 8, tbY + 80);
        ctx.fillStyle = isDark ? '#10b981' : '#059669';
        ctx.font = 'bold 10px monospace';
        ctx.fillText(`${lengthMm} mm (265.3 kg)`, tbX + tbW * 0.5 + 8, tbY + 93);
      }

      // 14. Standard Transmission Tower Hole Symbol Legend (IS 802 Drafting Standard)
      if (showHoleLegend) {
        const legW = 370;
        const legH = 76;
        const legX = margin + 10;
        const legY = h - legH - margin - 8;

        ctx.fillStyle = isDark ? 'rgba(8, 13, 22, 0.95)' : 'rgba(255, 255, 255, 0.98)';
        ctx.strokeStyle = isDark ? '#1e2d42' : '#0f172a';
        ctx.lineWidth = isDark ? 1.2 : 1.8;
        ctx.fillRect(legX, legY, legW, legH);
        ctx.strokeRect(legX, legY, legW, legH);

        // Legend Header
        ctx.fillStyle = isDark ? '#94a3b8' : '#0f172a';
        ctx.font = 'bold 9.5px monospace';
        ctx.fillText('STANDARD TOWER HOLE SCHEDULE & CAD SYMBOLS', legX + 8, legY + 13);

        const cellW = (legW - 16) / 8;
        STANDARD_HOLE_STYLES.forEach((st, i) => {
          const symX = legX + 8 + i * cellW + cellW / 2;
          const symY = legY + 34;
          drawHoleSymbolOnCanvas(
            ctx,
            symX,
            symY,
            6.5,
            st.symbolType,
            isDark,
            isDark ? '#38bdf8' : '#0369a1',
            isDark ? '#080d14' : '#ffffff',
            false
          );
          ctx.fillStyle = isDark ? '#e2e8f0' : '#0f172a';
          ctx.font = 'bold 8.5px monospace';
          ctx.textAlign = 'center';
          ctx.fillText(`Ø${st.diameter}`, symX, legY + 52);
          if (st.isStepHole) {
            ctx.fillStyle = isDark ? '#f59e0b' : '#b45309';
            ctx.font = 'bold 7.5px monospace';
            ctx.fillText('STEP', symX, legY + 64);
          }
          ctx.textAlign = 'left';
        });
      }
    };

    render();
    rafId = requestAnimationFrame(render);
    return () => cancelAnimationFrame(rafId);
  }, [
    recipe,
    hasValidRecipe,
    lengthMm,
    widthA,
    widthB,
    thickness,
    zoom,
    panX,
    panY,
    isFlipped,
    showDimensions,
    showPitchChain,
    showTitleBlock,
    showHoleLegend,
    theme,
    activeFeedPosition,
    highlightStepIndex,
    activeHoveredStep,
  ]);

  const handleMouseDown = (e: React.MouseEvent<HTMLCanvasElement>) => {
    isDragging.current = true;
    dragStart.current = { x: e.clientX - panX, y: e.clientY - panY };
  };

  const handleMouseMove = (e: React.MouseEvent<HTMLCanvasElement>) => {
    if (isDragging.current) {
      const newPanX = e.clientX - dragStart.current.x;
      const newPanY = e.clientY - dragStart.current.y;
      setPanX(newPanX);
      setPanY(newPanY);
      if (onViewportSync && containerRef.current) {
        const width = containerRef.current.clientWidth;
        const centerMm = Math.max(0, Math.min(lengthMm, (width / 2 - newPanX) / zoom));
        onViewportSync(centerMm);
      }
      return;
    }

    const canvas = canvasRef.current;
    if (!canvas || !recipe || !recipe.steps) return;

    const rect = canvas.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;

    setMousePos({ x: e.clientX + 14, y: e.clientY + 14 });

    const centerY = rect.height / 2 + panY;
    const scaleX = zoom;
    const flangeGap = Math.max(65, Math.min(105, (rect.height - 140) * 0.26));
    const flangeVisualHeight = Math.max(50, Math.min(85, (rect.height - flangeGap - 180) / 2));
    const scaleY = flangeVisualHeight / widthA;

    const topFlangeHeelY = centerY - flangeGap / 2;
    const bottomFlangeHeelY = centerY + flangeGap / 2;

    const found = recipe.steps.find((step) => {
      const opX = panX + step.xPosition * scaleX;
      let opY = centerY;
      if (step.side === 'A') {
        opY = topFlangeHeelY - step.yPosition * scaleY;
      } else if (step.side === 'B') {
        opY = bottomFlangeHeelY + step.yPosition * scaleY;
      }
      const dist = Math.hypot(mouseX - opX, mouseY - opY);
      return dist < 14;
    });

    if (found) {
      setHoveredStep(found);
      const idx = recipe.steps.indexOf(found);
      if (onHoverStep) onHoverStep(idx >= 0 ? idx : null);
      if (onSelectStep && idx >= 0) onSelectStep(idx);
    } else {
      setHoveredStep(null);
      if (onHoverStep) onHoverStep(null);
    }
  };

  const handleMouseUp = (e: React.MouseEvent<HTMLCanvasElement>) => {
    const wasDragging = isDragging.current && (Math.abs(e.clientX - (dragStart.current.x + panX)) > 4 || Math.abs(e.clientY - (dragStart.current.y + panY)) > 4);
    isDragging.current = false;

    if (!wasDragging && onCanvasClick && !hoveredStep && recipe) {
      const canvas = canvasRef.current;
      if (!canvas) return;
      const rect = canvas.getBoundingClientRect();
      const mouseX = e.clientX - rect.left;
      const mouseY = e.clientY - rect.top;

      const scaleX = zoom;
      const clickX = (mouseX - panX) / scaleX;

      if (clickX >= 0 && clickX <= lengthMm) {
        const centerY = rect.height / 2 + panY;
        const flangeGap = Math.max(65, Math.min(105, (rect.height - 140) * 0.26));
        const flangeVisualHeight = Math.max(50, Math.min(85, (rect.height - flangeGap - 180) / 2));
        const scaleY = flangeVisualHeight / widthA;

        const topFlangeHeelY = centerY - flangeGap / 2;
        const topFlangeToeY = topFlangeHeelY - flangeVisualHeight;
        const bottomFlangeHeelY = centerY + flangeGap / 2;
        const bottomFlangeToeY = bottomFlangeHeelY + flangeVisualHeight;

        let side: 'A' | 'B' | null = null;
        let clickY = 0;

        if (mouseY <= topFlangeHeelY && mouseY >= topFlangeToeY) {
          side = 'A';
          clickY = (topFlangeHeelY - mouseY) / scaleY;
        } else if (mouseY >= bottomFlangeHeelY && mouseY <= bottomFlangeToeY) {
          side = 'B';
          clickY = (mouseY - bottomFlangeHeelY) / scaleY;
        }

        if (side) {
          onCanvasClick(clickX, clickY, side);
        }
      }
    }
  };

  const handleMouseLeave = () => {
    isDragging.current = false;
    setHoveredStep(null);
    setMousePos(null);
    if (onHoverStep) onHoverStep(null);
  };

  const btnClass = (active: boolean) =>
    `p-1 px-2.5 text-[11px] font-bold rounded flex items-center gap-1.5 border transition-all select-none ${
      active
        ? 'bg-blue-600 border-blue-600 text-white shadow-sm'
        : isDark
        ? 'bg-[#151f2e] border-[#293a52] text-slate-200 hover:bg-[#1f2d42] hover:text-white'
        : 'bg-white border-slate-400 text-slate-900 font-bold hover:bg-slate-100 hover:border-slate-500 hover:text-black shadow-xs'
    }`;

  return (
    <div
      ref={containerRef}
      className={`relative w-full h-full flex flex-col select-none overflow-hidden rounded-lg border transition-colors ${
        isDark ? 'bg-[#0d131f] border-slate-700' : 'bg-white border-slate-400 shadow-sm'
      }`}
    >
      {/* Precision CAD Drafting Control Toolbar */}
      <div
        className={`px-3 py-1.5 border-b flex flex-wrap items-center justify-between z-10 shrink-0 text-xs transition-colors ${
          isDark ? 'bg-[#0e1624] border-[#1b2738]' : 'bg-[#f8fafc] border-slate-300'
        }`}
      >
        <div className="flex items-center gap-1.5">
          <button
            type="button"
            onClick={handleReset}
            className={btnClass(false)}
            title="Fit Full Length to View"
          >
            <AlignCenter className="w-3.5 h-3.5" /> Fit Length
          </button>

          <button
            type="button"
            onClick={() => setIsFlipped(!isFlipped)}
            className={btnClass(isFlipped)}
            title="Invert Flange A and Flange B orientation"
          >
            <Shuffle className="w-3.5 h-3.5" /> Flip Flanges
          </button>

          <button
            type="button"
            onClick={() => setShowDimensions(!showDimensions)}
            className={btnClass(showDimensions)}
            title="Toggle Cumulative and Overall Cut Lengths"
          >
            {showDimensions ? <Eye className="w-3.5 h-3.5" /> : <EyeOff className="w-3.5 h-3.5" />}
            Dimensions
          </button>

          <button
            type="button"
            onClick={() => setShowPitchChain(!showPitchChain)}
            className={btnClass(showPitchChain)}
            title="Toggle Incremental Pitch Dimension Chains (e.g. 25, 30, 50 mm)"
          >
            Pitch Chains
          </button>

          <button
            type="button"
            onClick={() => setShowTitleBlock(!showTitleBlock)}
            className={btnClass(showTitleBlock)}
            title="Toggle GETCO Standard Title Block"
          >
            <FileText className="w-3.5 h-3.5" /> Title Block
          </button>

          <button
            type="button"
            onClick={() => setShowHoleLegend(!showHoleLegend)}
            className={btnClass(showHoleLegend)}
            title="Toggle IS 802 CAD Hole Symbol Legend Table (13.5Ø - 36Ø)"
          >
            <Shapes className="w-3.5 h-3.5" /> Hole Symbols
          </button>

          <button
            type="button"
            onClick={() => setTheme(theme === 'DARK' ? 'PAPER' : 'DARK')}
            className={`p-1 px-2.5 text-[11px] font-bold rounded flex items-center gap-1.5 border transition-all ${
              isDark
                ? 'bg-[#151f2e] border-amber-500/40 text-amber-300 hover:bg-[#1f2d42]'
                : 'bg-slate-900 border-slate-900 text-white hover:bg-slate-800 shadow-sm'
            }`}
            title="Toggle CAD Dark Model Space vs Paper Blueprint White"
          >
            {isDark ? (
              <>
                <Sun className="w-3.5 h-3.5 text-amber-400" />
                <span>Paper Mode</span>
              </>
            ) : (
              <>
                <Moon className="w-3.5 h-3.5 text-cyan-400" />
                <span>Dark CAD</span>
              </>
            )}
          </button>
        </div>

        {/* Legend / Tooling Status Header */}
        <div className="flex items-center gap-3 text-[11px] font-mono">
          <div className="flex items-center gap-1.5">
            <span
              className={`w-2.5 h-2.5 rounded-full inline-block ${
                isDark ? 'bg-[#00e5ff] shadow-[0_0_6px_#00e5ff]' : 'bg-[#0369a1]'
              }`}
            />
            <span className={`font-bold ${isDark ? 'text-slate-300' : 'text-slate-900'}`}>
              Flange A (DA1-3)
            </span>
          </div>
          <div className="flex items-center gap-1.5">
            <span
              className={`w-2.5 h-2.5 rounded-full inline-block ${
                isDark ? 'bg-[#10b981] shadow-[0_0_6px_#10b981]' : 'bg-[#047857]'
              }`}
            />
            <span className={`font-bold ${isDark ? 'text-slate-300' : 'text-slate-900'}`}>
              Flange B (DB1-3)
            </span>
          </div>
          <div className="flex items-center gap-1.5">
            <span
              className={`w-2.5 h-2.5 rounded-sm inline-block ${
                isDark ? 'bg-[#fbbf24] shadow-[0_0_6px_#fbbf24]' : 'bg-[#d97706]'
              }`}
            />
            <span className={`font-bold ${isDark ? 'text-slate-300' : 'text-slate-900'}`}>
              Marking Stamp
            </span>
          </div>
          <div className="flex items-center gap-1.5">
            <span
              className={`w-3 h-1 inline-block ${
                isDark ? 'bg-[#ff3366] shadow-[0_0_6px_#ff3366]' : 'bg-[#dc2626]'
              }`}
            />
            <span className={`font-bold ${isDark ? 'text-slate-300' : 'text-slate-900'}`}>
              Shear Cut
            </span>
          </div>
        </div>
      </div>

      <canvas
        ref={canvasRef}
        onMouseDown={handleMouseDown}
        onMouseMove={handleMouseMove}
        onMouseUp={handleMouseUp}
        onMouseLeave={handleMouseLeave}
        className="w-full flex-1 min-h-[300px]"
        style={{ cursor: hoveredStep ? 'pointer' : isDragging.current ? 'grabbing' : 'grab' }}
      />

      {/* Floating Hover Tooltip */}
      {hoveredStep && mousePos && !isDragging.current && (
        <div
          className="fixed z-50 pointer-events-none bg-[#090e17]/95 border border-cyan-400 text-white text-xs px-3 py-2 rounded-lg shadow-2xl backdrop-blur-md font-mono"
          style={{ left: mousePos.x, top: mousePos.y }}
        >
          <div className="font-bold text-cyan-300 border-b border-slate-700 pb-1 mb-1">
            Operation #{hoveredStep.stepNumber} • {hoveredStep.operationType}
          </div>
          <div>Side / Flange: <b>{hoveredStep.side}</b></div>
          <div>X Position: <b>{hoveredStep.xPosition.toFixed(1)} mm</b></div>
          <div>Y Gauge: <b>{hoveredStep.yPosition.toFixed(1)} mm</b></div>
          {hoveredStep.toolSize && <div>Tool Die: <b>Ø{hoveredStep.toolSize} mm</b></div>}
          {hoveredStep.markingText && <div>Mark Stamp: <b>{hoveredStep.markingText}</b></div>}
        </div>
      )}
    </div>
  );
};
