import React from 'react';
import { HoleSymbolType } from '@innovance-hmi/shared';

interface HoleSymbolIconProps {
  symbolType?: HoleSymbolType;
  toolSize?: number;
  remarks?: string;
  className?: string;
  size?: number; // Size in px (default: 20)
  color?: string; // Stroke/fill color
}

/**
 * Resolves the appropriate HoleSymbolType based on toolSize and remarks if not explicitly set
 */
export function resolveHoleSymbol(
  explicitSymbol?: HoleSymbolType,
  toolSize?: number,
  remarks?: string
): HoleSymbolType {
  if (explicitSymbol && explicitSymbol !== 'CUSTOM') {
    return explicitSymbol;
  }
  const isStep = remarks?.toLowerCase().includes('step');
  const d = toolSize || 17.5;

  if (isStep || Math.abs(d - 17.5) < 0.2 && remarks?.toLowerCase().includes('step')) {
    return 'STEP_HOLE_17_5';
  }
  if (Math.abs(d - 13.5) < 0.5) return 'HALF_FILLED_13_5';
  if (Math.abs(d - 17.5) < 0.5) return 'STANDARD_OPEN_17_5';
  if (Math.abs(d - 22.0) < 0.5) return 'SOLID_FILLED_22';
  if (Math.abs(d - 26.0) < 0.5) return 'CONCENTRIC_DOUBLE_26';
  if (Math.abs(d - 30.0) < 0.5) return 'CHECKERBOARD_30';
  if (Math.abs(d - 33.0) < 0.5) return 'SQUARE_33';
  if (Math.abs(d - 36.0) < 0.5) return 'TRIANGLE_36';

  return 'STANDARD_OPEN_17_5';
}

/**
 * Standard CAD Drafting Hole Glyph SVG for UI
 */
export const HoleSymbolIcon: React.FC<HoleSymbolIconProps> = ({
  symbolType,
  toolSize,
  remarks,
  className = '',
  size = 20,
  color = 'currentColor',
}) => {
  const resolved = resolveHoleSymbol(symbolType, toolSize, remarks);
  const cx = size / 2;
  const cy = size / 2;
  const r = size * 0.36;
  const arm = size * 0.48;

  switch (resolved) {
    case 'HALF_FILLED_13_5':
      return (
        <svg width={size} height={size} viewBox={`0 0 ${size} ${size}`} className={className}>
          {/* Half filled circle (right half shaded) */}
          <path
            d={`M ${cx} ${cy - r} A ${r} ${r} 0 0 1 ${cx} ${cy + r} Z`}
            fill={color}
          />
          <circle cx={cx} cy={cy} r={r} fill="none" stroke={color} strokeWidth="1.5" />
          {/* Crosshair */}
          <line x1={cx - arm} y1={cy} x2={cx + arm} y2={cy} stroke={color} strokeWidth="1" />
          <line x1={cx} y1={cy - arm} x2={cx} y2={cy + arm} stroke={color} strokeWidth="1" />
        </svg>
      );

    case 'SOLID_FILLED_22':
      return (
        <svg width={size} height={size} viewBox={`0 0 ${size} ${size}`} className={className}>
          <circle cx={cx} cy={cy} r={r} fill={color} stroke={color} strokeWidth="1.5" />
          {/* Crosshair extending out */}
          <line x1={cx - arm} y1={cy} x2={cx + arm} y2={cy} stroke={color} strokeWidth="1" />
          <line x1={cx} y1={cy - arm} x2={cx} y2={cy + arm} stroke={color} strokeWidth="1" />
        </svg>
      );

    case 'CONCENTRIC_DOUBLE_26':
      return (
        <svg width={size} height={size} viewBox={`0 0 ${size} ${size}`} className={className}>
          <circle cx={cx} cy={cy} r={r} fill="none" stroke={color} strokeWidth="1.5" />
          <circle cx={cx} cy={cy} r={r * 0.55} fill="none" stroke={color} strokeWidth="1.2" />
          <line x1={cx - arm} y1={cy} x2={cx + arm} y2={cy} stroke={color} strokeWidth="1" />
          <line x1={cx} y1={cy - arm} x2={cx} y2={cy + arm} stroke={color} strokeWidth="1" />
        </svg>
      );

    case 'CHECKERBOARD_30':
      return (
        <svg width={size} height={size} viewBox={`0 0 ${size} ${size}`} className={className}>
          {/* Top-Right Quadrant */}
          <path d={`M ${cx} ${cy} L ${cx} ${cy - r} A ${r} ${r} 0 0 1 ${cx + r} ${cy} Z`} fill={color} />
          {/* Bottom-Left Quadrant */}
          <path d={`M ${cx} ${cy} L ${cx} ${cy + r} A ${r} ${r} 0 0 1 ${cx - r} ${cy} Z`} fill={color} />
          <circle cx={cx} cy={cy} r={r} fill="none" stroke={color} strokeWidth="1.5" />
          <line x1={cx - arm} y1={cy} x2={cx + arm} y2={cy} stroke={color} strokeWidth="1" />
          <line x1={cx} y1={cy - arm} x2={cx} y2={cy + arm} stroke={color} strokeWidth="1" />
        </svg>
      );

    case 'SQUARE_33': {
      const halfBox = r * 0.85;
      return (
        <svg width={size} height={size} viewBox={`0 0 ${size} ${size}`} className={className}>
          <rect
            x={cx - halfBox}
            y={cy - halfBox}
            width={halfBox * 2}
            height={halfBox * 2}
            fill="none"
            stroke={color}
            strokeWidth="1.5"
          />
          <line x1={cx - arm} y1={cy} x2={cx + arm} y2={cy} stroke={color} strokeWidth="1" />
          <line x1={cx} y1={cy - arm} x2={cx} y2={cy + arm} stroke={color} strokeWidth="1" />
        </svg>
      );
    }

    case 'TRIANGLE_36': {
      const h = r * 1.5;
      const w = r * 1.732;
      return (
        <svg width={size} height={size} viewBox={`0 0 ${size} ${size}`} className={className}>
          <polygon
            points={`${cx},${cy - h * 0.65} ${cx + w / 2},${cy + h * 0.35} ${cx - w / 2},${cy + h * 0.35}`}
            fill="none"
            stroke={color}
            strokeWidth="1.5"
          />
          <line x1={cx - arm} y1={cy} x2={cx + arm} y2={cy} stroke={color} strokeWidth="1" />
          <line x1={cx} y1={cy - arm} x2={cx} y2={cy + arm} stroke={color} strokeWidth="1" />
        </svg>
      );
    }

    case 'STEP_HOLE_17_5':
      return (
        <svg width={size} height={size} viewBox={`0 0 ${size} ${size}`} className={className}>
          <circle cx={cx} cy={cy} r={r} fill="none" stroke={color} strokeWidth="1.5" />
          <circle cx={cx} cy={cy} r={r * 0.6} fill="none" stroke={color} strokeWidth="1.2" strokeDasharray="2,2" />
          <line x1={cx - arm} y1={cy} x2={cx + arm} y2={cy} stroke={color} strokeWidth="1" />
          <line x1={cx} y1={cy - arm} x2={cx} y2={cy + arm} stroke={color} strokeWidth="1" />
        </svg>
      );

    case 'STANDARD_OPEN_17_5':
    default:
      return (
        <svg width={size} height={size} viewBox={`0 0 ${size} ${size}`} className={className}>
          <circle cx={cx} cy={cy} r={r} fill="none" stroke={color} strokeWidth="1.5" />
          <line x1={cx - arm} y1={cy} x2={cx + arm} y2={cy} stroke={color} strokeWidth="1" />
          <line x1={cx} y1={cy - arm} x2={cx} y2={cy + arm} stroke={color} strokeWidth="1" />
        </svg>
      );
  }
};

/**
 * Draws the authentic CAD drafting hole symbol on an HTML5 canvas
 */
export function drawHoleSymbolOnCanvas(
  ctx: CanvasRenderingContext2D,
  cx: number,
  cy: number,
  radius: number,
  symbolType: HoleSymbolType,
  isDark: boolean,
  strokeColor: string,
  fillColor: string,
  highlightActive: boolean
) {
  const arm = radius + 3.5;
  const lineW = highlightActive ? 2.8 : isDark ? 1.6 : 2.0;

  ctx.lineWidth = lineW;
  ctx.strokeStyle = strokeColor;

  // In paper mode, use solid dark ink for filled CAD glyphs unless highlighted
  const solidFill = highlightActive ? strokeColor : isDark ? strokeColor : '#0f172a';

  switch (symbolType) {
    case 'HALF_FILLED_13_5':
      // Clear base
      ctx.fillStyle = fillColor;
      ctx.beginPath();
      ctx.arc(cx, cy, radius, 0, Math.PI * 2);
      ctx.fill();

      // Right half filled with dark ink or accent
      ctx.fillStyle = solidFill;
      ctx.beginPath();
      ctx.arc(cx, cy, radius, -Math.PI / 2, Math.PI / 2, false);
      ctx.closePath();
      ctx.fill();

      // Outer circle
      ctx.beginPath();
      ctx.arc(cx, cy, radius, 0, Math.PI * 2);
      ctx.stroke();
      break;

    case 'SOLID_FILLED_22':
      ctx.fillStyle = solidFill;
      ctx.beginPath();
      ctx.arc(cx, cy, radius, 0, Math.PI * 2);
      ctx.fill();
      ctx.stroke();
      break;

    case 'CONCENTRIC_DOUBLE_26':
      ctx.fillStyle = fillColor;
      ctx.beginPath();
      ctx.arc(cx, cy, radius, 0, Math.PI * 2);
      ctx.fill();
      ctx.stroke();

      ctx.beginPath();
      ctx.arc(cx, cy, radius * 0.55, 0, Math.PI * 2);
      ctx.stroke();
      break;

    case 'CHECKERBOARD_30':
      ctx.fillStyle = fillColor;
      ctx.beginPath();
      ctx.arc(cx, cy, radius, 0, Math.PI * 2);
      ctx.fill();

      // Quadrant 1 (top-right)
      ctx.fillStyle = solidFill;
      ctx.beginPath();
      ctx.moveTo(cx, cy);
      ctx.arc(cx, cy, radius, -Math.PI / 2, 0, false);
      ctx.closePath();
      ctx.fill();

      // Quadrant 3 (bottom-left)
      ctx.beginPath();
      ctx.moveTo(cx, cy);
      ctx.arc(cx, cy, radius, Math.PI / 2, Math.PI, false);
      ctx.closePath();
      ctx.fill();

      ctx.beginPath();
      ctx.arc(cx, cy, radius, 0, Math.PI * 2);
      ctx.stroke();
      break;

    case 'SQUARE_33': {
      const halfBox = radius * 0.85;
      ctx.fillStyle = fillColor;
      ctx.fillRect(cx - halfBox, cy - halfBox, halfBox * 2, halfBox * 2);
      ctx.strokeRect(cx - halfBox, cy - halfBox, halfBox * 2, halfBox * 2);
      break;
    }

    case 'TRIANGLE_36': {
      const h = radius * 1.5;
      const w = radius * 1.732;
      ctx.fillStyle = fillColor;
      ctx.beginPath();
      ctx.moveTo(cx, cy - h * 0.65);
      ctx.lineTo(cx + w / 2, cy + h * 0.35);
      ctx.lineTo(cx - w / 2, cy + h * 0.35);
      ctx.closePath();
      ctx.fill();
      ctx.stroke();
      break;
    }

    case 'STEP_HOLE_17_5':
      ctx.fillStyle = fillColor;
      ctx.beginPath();
      ctx.arc(cx, cy, radius, 0, Math.PI * 2);
      ctx.fill();
      ctx.stroke();

      // Inner dashed ring
      ctx.save();
      ctx.setLineDash([2, 2]);
      ctx.beginPath();
      ctx.arc(cx, cy, radius * 0.6, 0, Math.PI * 2);
      ctx.stroke();
      ctx.restore();

      // Step text tag with high contrast in paper mode
      ctx.fillStyle = isDark ? '#f59e0b' : '#b45309';
      ctx.font = 'bold 8.5px monospace';
      ctx.fillText('STEP', cx - 12, cy - radius - 4);
      break;

    case 'STANDARD_OPEN_17_5':
    default:
      ctx.fillStyle = fillColor;
      ctx.beginPath();
      ctx.arc(cx, cy, radius, 0, Math.PI * 2);
      ctx.fill();
      ctx.stroke();
      break;
  }

  // Crosshairs
  ctx.lineWidth = highlightActive ? 1.8 : isDark ? 1.0 : 1.3;
  ctx.beginPath();
  ctx.moveTo(cx - arm, cy);
  ctx.lineTo(cx + arm, cy);
  ctx.moveTo(cx, cy - arm);
  ctx.lineTo(cx, cy + arm);
  ctx.stroke();
}
