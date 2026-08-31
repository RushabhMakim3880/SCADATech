import React, { useState } from 'react';
import { ItemRecipe } from '@innovance-hmi/shared';
import {
  ShieldCheck,
  AlertTriangle,
  CheckCircle2,
  X,
  Settings,
} from 'lucide-react';

interface TowerRuleCheckerModalProps {
  isOpen: boolean;
  recipe: ItemRecipe;
  onClose: () => void;
}

export interface RuleViolation {
  stepNumber: number;
  ruleName: string;
  flangeSide: string;
  actualValue: number;
  requiredValue: number;
  message: string;
  severity: 'ERROR' | 'WARNING';
}

export const TowerRuleCheckerModal: React.FC<TowerRuleCheckerModalProps> = ({
  isOpen,
  recipe,
  onClose,
}) => {
  // Configurable Multipliers (IS 802 / ASTM Standards)
  const [edgeMultiplier, setEdgeMultiplier] = useState<number>(1.5);
  const [pitchMultiplier, setPitchMultiplier] = useState<number>(2.5);
  const [gaugeMultiplier, setGaugeMultiplier] = useState<number>(1.5);
  const [staggerMultiplier, setStaggerMultiplier] = useState<number>(1.5);
  const [isConfigMode, setIsConfigMode] = useState<boolean>(false);

  if (!isOpen) return null;

  const violations: RuleViolation[] = [];

  const punchSteps = recipe.steps.filter((s) => s.operationType === 'PUNCH');

  punchSteps.forEach((step) => {
    const die = step.toolSize || 18;
    const flangeWidth = step.side === 'A' ? recipe.angleWidthA : recipe.angleWidthB;

    // Rule 1: Minimum Edge Distance from Angle Toe Tip (flangeWidth - yPosition)
    const edgeDist = flangeWidth - step.yPosition;
    const minEdge = Number((edgeMultiplier * die).toFixed(1));
    if (edgeDist < minEdge) {
      violations.push({
        stepNumber: step.stepNumber,
        ruleName: 'Minimum Edge Distance (IS 802)',
        flangeSide: `Side ${step.side}`,
        actualValue: edgeDist,
        requiredValue: minEdge,
        message: `Hole at Y=${step.yPosition}mm is too close to the angle tip! Edge distance is ${edgeDist}mm (Min Required: ${minEdge}mm = ${edgeMultiplier}x Ø${die})`,
        severity: 'ERROR',
      });
    }

    // Rule 2: Minimum Gauge Distance from Bend Heel Datum (yPosition)
    const minGauge = Number((gaugeMultiplier * die + recipe.thickness).toFixed(1));
    if (step.yPosition < minGauge) {
      violations.push({
        stepNumber: step.stepNumber,
        ruleName: 'Minimum Heel Gauge Distance',
        flangeSide: `Side ${step.side}`,
        actualValue: step.yPosition,
        requiredValue: minGauge,
        message: `Hole at Y=${step.yPosition}mm is too close to the bend heel fold! Gauge distance is ${step.yPosition}mm (Min Required: ${minGauge}mm)`,
        severity: 'WARNING',
      });
    }
  });

  // Rule 3: Minimum Pitch Spacing between consecutive holes on the same side
  for (let i = 0; i < punchSteps.length; i++) {
    for (let j = i + 1; j < punchSteps.length; j++) {
      const s1 = punchSteps[i];
      const s2 = punchSteps[j];

      if (s1.side === s2.side) {
        const deltaX = Math.abs(s2.xPosition - s1.xPosition);
        const maxDie = Math.max(s1.toolSize || 18, s2.toolSize || 18);
        const minPitch = Number((pitchMultiplier * maxDie).toFixed(1));

        if (deltaX < minPitch && deltaX > 0) {
          violations.push({
            stepNumber: s2.stepNumber,
            ruleName: 'Minimum Pitch Spacing',
            flangeSide: `Side ${s1.side}`,
            actualValue: deltaX,
            requiredValue: minPitch,
            message: `Distance between Step #${s1.stepNumber} and Step #${s2.stepNumber} is ${deltaX}mm (Min Required Pitch: ${minPitch}mm = ${pitchMultiplier}x Ø${maxDie})`,
            severity: 'ERROR',
          });
        }
      }
    }
  }

  const isCompliant = violations.length === 0;

  return (
    <div className="fixed inset-0 bg-slate-900/75 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div className="bg-white rounded-lg shadow-2xl border border-slate-300 w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden text-xs">
        {/* Header */}
        <div className="panel-heading bg-slate-800 text-white px-4 py-3 flex items-center justify-between">
          <div className="flex items-center gap-2">
            <ShieldCheck className="w-5 h-5 text-cyan-400" />
            <span className="font-bold text-sm">Transmission Tower Design Rule Checker (IS 802 / ASTM A394)</span>
          </div>
          <div className="flex items-center gap-2">
            <button
              onClick={() => setIsConfigMode((c) => !c)}
              className="btn-ca btn-ca-dark text-xs py-1 px-2.5 text-slate-300 hover:text-white"
            >
              <Settings className="w-3.5 h-3.5" /> {isConfigMode ? 'View Results' : 'Rule Config'}
            </button>
            <button onClick={onClose} className="text-slate-300 hover:text-white">
              <X className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* Body */}
        <div className="p-4 overflow-y-auto space-y-4">
          {isConfigMode ? (
            /* Rule Configuration Parameters */
            <div className="space-y-3 p-3 bg-slate-50 rounded border border-slate-200">
              <h4 className="font-bold text-slate-800 text-sm">Design Rule Clearance Multipliers</h4>
              <p className="text-slate-500 text-xs">
                Configure standard Indian Standard (IS 802) or client-specific transmission tower rules (KEC / Kalpataru).
              </p>

              <div className="grid grid-cols-2 gap-3 pt-2">
                <div>
                  <label className="font-bold text-slate-700 block">Min Edge Distance Factor ($e_{'{min}'} = k \cdot \varnothing$)</label>
                  <input
                    type="number"
                    step="0.1"
                    value={edgeMultiplier}
                    onChange={(e) => setEdgeMultiplier(parseFloat(e.target.value) || 1.5)}
                    className="form-control-ca mt-1 font-bold"
                  />
                  <span className="text-[10px] text-slate-500">IS 802 Standard: 1.5x hole diameter</span>
                </div>

                <div>
                  <label className="font-bold text-slate-700 block">Min Pitch Spacing Factor ($p_{'{min}'} = k \cdot \varnothing$)</label>
                  <input
                    type="number"
                    step="0.1"
                    value={pitchMultiplier}
                    onChange={(e) => setPitchMultiplier(parseFloat(e.target.value) || 2.5)}
                    className="form-control-ca mt-1 font-bold"
                  />
                  <span className="text-[10px] text-slate-500">IS 802 Standard: 2.5x hole diameter</span>
                </div>

                <div>
                  <label className="font-bold text-slate-700 block">Min Heel Gauge Factor ($g_{'{min}'} = k \cdot \varnothing + t$)</label>
                  <input
                    type="number"
                    step="0.1"
                    value={gaugeMultiplier}
                    onChange={(e) => setGaugeMultiplier(parseFloat(e.target.value) || 1.5)}
                    className="form-control-ca mt-1 font-bold"
                  />
                  <span className="text-[10px] text-slate-500">Includes bar thickness offset</span>
                </div>

                <div>
                  <label className="font-bold text-slate-700 block">Flange A/B Stagger Factor</label>
                  <input
                    type="number"
                    step="0.1"
                    value={staggerMultiplier}
                    onChange={(e) => setStaggerMultiplier(parseFloat(e.target.value) || 1.5)}
                    className="form-control-ca mt-1 font-bold"
                  />
                </div>
              </div>
            </div>
          ) : (
            /* Compliance Results */
            <div className="space-y-3">
              <div
                className={`p-4 rounded-lg border flex items-center justify-between ${
                  isCompliant
                    ? 'bg-emerald-50 border-emerald-300 text-emerald-900'
                    : 'bg-red-50 border-red-300 text-red-900'
                }`}
              >
                <div className="flex items-center gap-3">
                  {isCompliant ? (
                    <CheckCircle2 className="w-8 h-8 text-emerald-600" />
                  ) : (
                    <AlertTriangle className="w-8 h-8 text-red-600" />
                  )}
                  <div>
                    <h3 className="font-bold text-sm">
                      {isCompliant
                        ? '100% IS 802 Tower Design Rule Compliant!'
                        : `${violations.length} Design Rule Violations Detected`}
                    </h3>
                    <p className="text-xs opacity-90">
                      {isCompliant
                        ? 'All edge distances, heel gauges, and pitch spacings conform to Indian Standard structural requirements.'
                        : 'Review the violations below to prevent bolt hole tear-out or structural reject scrap.'}
                    </p>
                  </div>
                </div>

                <div className="text-right font-mono font-bold">
                  <div>Profile: L{recipe.angleWidthA}x{recipe.angleWidthB}x{recipe.thickness}</div>
                  <div>Length: {recipe.totalLength} mm</div>
                </div>
              </div>

              {/* Violations Table */}
              {!isCompliant && (
                <div className="space-y-2">
                  <div className="font-bold text-slate-700">Violation Details:</div>
                  <table className="table-custom border">
                    <thead>
                      <tr>
                        <th>Step #</th>
                        <th>Flange</th>
                        <th>Rule Checked</th>
                        <th>Actual Dist</th>
                        <th>Required Dist</th>
                        <th>Violation Description</th>
                      </tr>
                    </thead>
                    <tbody>
                      {violations.map((v, idx) => (
                        <tr key={idx} className={v.severity === 'ERROR' ? 'bg-red-50/70' : 'bg-amber-50/70'}>
                          <td className="font-bold text-slate-800">#{v.stepNumber}</td>
                          <td className="font-semibold">{v.flangeSide}</td>
                          <td className="font-bold text-slate-800">{v.ruleName}</td>
                          <td className="font-mono text-red-700 font-bold">{v.actualValue} mm</td>
                          <td className="font-mono text-emerald-700 font-bold">{v.requiredValue} mm</td>
                          <td className="text-slate-700">{v.message}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              )}
            </div>
          )}
        </div>

        {/* Footer */}
        <div className="p-3 bg-slate-100 border-t border-slate-300 flex justify-end">
          <button onClick={onClose} className="btn-ca btn-ca-primary">
            Close & Apply
          </button>
        </div>
      </div>
    </div>
  );
};
