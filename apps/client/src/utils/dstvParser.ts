import { ItemRecipe, ItemRecipeStep, SideType } from '@innovance-hmi/shared';

/**
 * Standard DSTV (.nc1) Structural Steel Angle Bar Parser
 * Formats supported: Standard DSTV (Deutscher Stahlbau-Verband) for L-profiles (Angles)
 */
export function parseDstvNc1(fileContent: string, fileName: string): ItemRecipe {
  const lines = fileContent.split(/\r?\n/).map((l) => l.trim());

  let itemCode = fileName.replace(/\.[^/.]+$/, '').toUpperCase();
  let angleWidthA = 75.0;
  let angleWidthB = 75.0;
  let thickness = 6.0;
  let totalLength = 1500.0;
  let material = 'S355 / Structural Steel';

  const steps: ItemRecipeStep[] = [];
  let currentBlock = '';
  let stepCounter = 1;

  for (let i = 0; i < lines.length; i++) {
    const line = lines[i];
    if (!line) continue;

    // Header block
    if (line === 'ST') {
      currentBlock = 'HEADER';
      continue;
    } else if (line === 'BO') {
      currentBlock = 'HOLES';
      continue;
    } else if (line === 'SI') {
      currentBlock = 'STAMP';
      continue;
    } else if (line === 'IK' || line === 'AK') {
      currentBlock = 'CONTOUR';
      continue;
    } else if (line === 'EN') {
      currentBlock = 'END';
      break;
    }

    if (currentBlock === 'HEADER') {
      // DSTV Header items: Line 4=Piece Code, Line 5=Profile Type (e.g. L 75*75*6), Line 7=Length
      if (line.startsWith('L') || line.includes('*') || line.includes('x')) {
        // Parse L-profile dimensions like "L 75*6" or "L 75*75*6" or "L75x75x6"
        const clean = line.replace(/L\s*/i, '').replace(/x/g, '*');
        const parts = clean.split('*').map((p) => parseFloat(p.trim())).filter((n) => !isNaN(n));
        if (parts.length === 2) {
          angleWidthA = parts[0];
          angleWidthB = parts[0];
          thickness = parts[1];
        } else if (parts.length >= 3) {
          angleWidthA = parts[0];
          angleWidthB = parts[1];
          thickness = parts[2];
        }
      } else if (!isNaN(parseFloat(line)) && parseFloat(line) > 100 && totalLength === 1500.0) {
        totalLength = parseFloat(line);
      }
    } else if (currentBlock === 'HOLES') {
      // BO block line format: Face (v=Side A / Top Flange, u=Side B / Bottom Flange), X-pos, Y-pos, Diameter
      const tokens = line.split(/\s+/);
      if (tokens.length >= 4) {
        const face = tokens[0].toLowerCase();
        const xPos = parseFloat(tokens[1]);
        const yPos = parseFloat(tokens[2]);
        const diameter = parseFloat(tokens[3]);

        if (!isNaN(xPos) && !isNaN(yPos) && !isNaN(diameter)) {
          const side: SideType = face === 'u' ? 'B' : 'A';
          steps.push({
            id: `dstv-step-${stepCounter}`,
            stepNumber: stepCounter++,
            operationType: 'PUNCH',
            side,
            xPosition: xPos,
            yPosition: yPos,
            toolSize: diameter,
            isCutOff: false,
          });
        }
      }
    } else if (currentBlock === 'STAMP') {
      // SI Stamp block line format: Face, X-pos, Y-pos, Angle, Text
      const tokens = line.split(/\s+/);
      if (tokens.length >= 5) {
        const face = tokens[0].toLowerCase();
        const xPos = parseFloat(tokens[1]);
        const yPos = parseFloat(tokens[2]);
        const stampText = tokens.slice(4).join(' ');

        if (!isNaN(xPos) && !isNaN(yPos)) {
          const side: SideType = face === 'u' ? 'B' : 'A';
          steps.push({
            id: `dstv-step-${stepCounter}`,
            stepNumber: stepCounter++,
            operationType: 'MARK',
            side,
            xPosition: xPos,
            yPosition: yPos,
            markingText: stampText,
            isCutOff: false,
          });
        }
      }
    }
  }

  // Ensure there is always an end shear cut
  if (!steps.some((s) => s.operationType === 'CUT')) {
    steps.push({
      id: `dstv-step-${stepCounter}`,
      stepNumber: stepCounter++,
      operationType: 'CUT',
      side: 'NA',
      xPosition: totalLength,
      yPosition: 0,
      isCutOff: true,
    });
  }

  return {
    id: '',
    itemCode,
    itemName: `${material} (DSTV Import)`,
    totalLength,
    angleWidthA,
    angleWidthB,
    thickness,
    measurementType: 'ABSOLUTE',
    isActive: true,
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString(),
    steps,
  };
}
