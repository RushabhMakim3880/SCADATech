<?php

namespace App\Libraries;

class RecipeParser
{
    /**
     * Parse raw DAT content into itemRecipeMaster + itemRecipeSteps arrays.
     *
     * Assumptions:
     * - First line "P:..." is ignored.
     * - Second line is material type (kept in metadata, not stored to DB unless you add a column).
     * - Widths/thickness come from "SA... SB... TA... TB..." (TA and TB assumed equal).
     * - LP:<len> gives the total bar length (used to append the final Cutting step at x = LP).
     * - DA/DB define a punching context (side, diameter, absolute X, Y).
     * - XI lines are incremental X (relative to last absolute X) and use the current context's side+diameter.
     * - MK& <code>& X<pos> => Marking with y=0.
     * - All output steps use measurementType = 'absolute'.
     *
     * @param string      $content  Full file content
     * @param null|string $itemCode Optional itemCode to set in header array
     * @return array{itemRecipe: array, steps: array, meta: array}
     */
    public function parseDatContent(string $content, ?string $itemCode = null): array
    {
        $lines = preg_split('/\R/u', trim($content));
        if (!$lines || count($lines) === 0) {
            throw new \RuntimeException('Empty DAT content.');
        }

        // Normalized holders
        $sideAWidth = null;
        $sideBWidth = null;
        $sideAThickness = null;
        $sideBThickness = null;
        $totalLength = null;
        $materialType = null;
        $programCodeFromMark = null;

        $steps = [];
        $meta  = [];

        $ordId = 0;

        // Punching context (for XI lines)
        $ctx = [
            'hasContext' => false,
            'side'       => null,   // 'A' | 'B'
            'diameter'   => null,   // int/float
            'lastAbsX'   => null,   // numeric
        ];

        // Helper: add step
        $addStep = function (string $opType, string $side, $opValue, $x, $y, $measurementType) use (&$steps, &$ordId) {
            $steps[] = [
                'tenantId'       => 1,
                'opType'          => $opType,                    // Punching | Marking | Cutting
                'side'            => $side,                      // 'A' | 'B' | 'N/A'
                'opValue'         => (string)($opValue ?? ''),   // diameter for punch, code for marking, '' for cutting
                'xPos'            => (float)$x,
                'yPos'            => (float)$y,
                'measurementType' => $measurementType,
                'ordId'           => $ordId++,
            ];
        };

        // Regex patterns
        $reLP   = '/^\s*LP\s*:\s*(\d+(?:\.\d+)?)\s*$/i';
        $reSSTT = '/\bSA\s*(\d+(?:\.\d+)?)\b.*\bSB\s*(\d+(?:\.\d+)?)\b.*\bTA\s*(\d+(?:\.\d+)?)\b.*\bTB\s*(\d+(?:\.\d+)?)\b/i';
        $reDA   = '/^\s*DA\s*(\d+(?:\.\d+)?)\s*X\s*(\d+(?:\.\d+)?)\s*TR\s*(\d+(?:\.\d+)?)\s*$/i';
        $reDB   = '/^\s*DB\s*(\d+(?:\.\d+)?)\s*X\s*(\d+(?:\.\d+)?)\s*TR\s*(\d+(?:\.\d+)?)\s*$/i';
        $reXI   = '/^\s*XI\s*(\d+(?:\.\d+)?)\s*TR\s*(\d+(?:\.\d+)?)\s*$/i';
        $reMK   = '/^\s*MK&\s*([^\s&]+)\s*&\s*X\s*(\d+(?:\.\d+)?)\s*$/i';
        $reM30  = '/^\s*M30\s*$/i';
        $reEND  = '/^\s*END\s*$/i';
        $reDA_XI = '/^\s*DA\s*(\d+(?:\.\d+)?)\s*XI\s*(\d+(?:\.\d+)?)\s*TR\s*(\d+(?:\.\d+)?)\s*$/i';
        $reDB_XI = '/^\s*DB\s*(\d+(?:\.\d+)?)\s*XI\s*(\d+(?:\.\d+)?)\s*TR\s*(\d+(?:\.\d+)?)\s*$/i';


        // Iterate lines
        foreach ($lines as $idx => $raw) {
            $line = trim($raw);

            // Ignore blank
            if ($line === '') {
                continue;
            }

            // 1) First line P:... => ignore by spec
            if ($idx === 0 && stripos($line, 'P:') === 0) {
                $meta['profile'] = substr($line, 2);
                continue;
            }

            // 2) Second line material type
            if ($idx === 1) {
                $materialType = preg_replace('/^\s*M:\s*/i', '', $line);
                $meta['materialType'] = $materialType;
                continue; // <- prevents it from being recorded as unparsed
            }

            // 3) Total length
            if (preg_match($reLP, $line, $m)) {
                $totalLength = (float)$m[1];
                $meta['totalLength'] = $totalLength;

                continue;
            }

            // 4) SA/SB/TA/TB
            if (preg_match($reSSTT, $line, $m)) {
                $sideAWidth = (float)$m[1];
                $sideBWidth = (float)$m[2];
                $ta         = (float)$m[3];
                $tb         = (float)$m[4];
                $sideAThickness = $ta;
                $sideBThickness = $tb;
                $meta['TA_TB_equal'] = ($ta == $tb);
                continue;
            }

            // 5) Punching absolute (DA side A)
            if (preg_match($reDA, $line, $m)) {
                $diam = (float)$m[1];
                $x    = (float)$m[2];
                $y    = (float)$m[3];

                $addStep('Punching', 'A', $diam, $x, $y, 'Absolute');

                $ctx['hasContext'] = true;
                $ctx['side']       = 'A';
                $ctx['diameter']   = $diam;
                $ctx['lastAbsX']   = $x;
                continue;
            }

            // 6) Punching absolute (DB side B)
            if (preg_match($reDB, $line, $m)) {
                $diam = (float)$m[1];
                $x    = (float)$m[2];
                $y    = (float)$m[3];

                $addStep('Punching', 'B', $diam, $x, $y, 'Absolute');

                $ctx['hasContext'] = true;
                $ctx['side']       = 'B';
                $ctx['diameter']   = $diam;
                $ctx['lastAbsX']   = $x;
                continue;
            }
            // 5b) Punching compact incremental (DA ... XI ... TR ...)
            if (preg_match($reDA_XI, $line, $m)) {
                $diam = (float)$m[1];
                $dx   = (float)$m[2];
                $y    = (float)$m[3];

                if (!$ctx['hasContext']) {
                    throw new \RuntimeException("DA...XI encountered without prior context at line " . ($idx + 1) . ": {$line}");
                }
                // Switch context to A and new diameter, then increment from lastAbsX
                $ctx['side']     = 'A';
                $ctx['diameter'] = $diam;

                // $absX = (float)$ctx['lastAbsX'] + $dx;
                $addStep('Punching', 'A', $diam, $dx, $y, 'Incremental');
                // $ctx['lastAbsX'] = $dx;
                $ctx['hasContext'] = true;
                continue;
            }

            // 6b) Punching compact incremental (DB ... XI ... TR ...)
            if (preg_match($reDB_XI, $line, $m)) {
                $diam = (float)$m[1];
                $dx   = (float)$m[2];
                $y    = (float)$m[3];

                if (!$ctx['hasContext']) {
                    throw new \RuntimeException("DB...XI encountered without prior context at line " . ($idx + 1) . ": {$line}");
                }
                // Switch context to B and new diameter, then increment from lastAbsX
                $ctx['side']     = 'B';
                $ctx['diameter'] = $diam;

                // $absX = (float)$ctx['lastAbsX'] + $dx;
                $addStep('Punching', 'B', $diam, $dx, $y, 'Incremental');
                // $ctx['lastAbsX'] = $absX;
                $ctx['hasContext'] = true;
                continue;
            }


            // 7) Incremental X (XI...) with current context
            if (preg_match($reXI, $line, $m)) {
                if (!$ctx['hasContext']) {
                    throw new \RuntimeException("XI encountered without prior DA/DB context at line " . ($idx + 1) . ": {$line}");
                }
                $dx = (float)$m[1];
                $y  = (float)$m[2];

                $absX = (float)$ctx['lastAbsX'] + $dx;

                $addStep('Punching', $ctx['side'], $ctx['diameter'], $dx, $y, 'Incremental');

                $ctx['lastAbsX'] = $absX;
                continue;
            }

            // 8) Marking
            if (preg_match($reMK, $line, $m)) {
                $code = $m[1];
                $x    = (float)$m[2];
                $addStep('Marking', 'N/A', $code, $x, 0, 'Absolute');

                // Take the FIRST marking code as program code
                if ($programCodeFromMark === null) {
                    $programCodeFromMark = $code;
                    $meta['programCodeDetected'] = $programCodeFromMark;
                }
                continue;
            }

            // 9) File end tokens
            if (preg_match($reM30, $line) || preg_match($reEND, $line)) {
                // We'll handle final Cutting after loop when we know LP
                continue;
            }

            // If you want to be strict, you can throw on unknown lines.
            // For robustness, record in meta instead:
            $meta['unparsed_lines'][] = ['lineNo' => $idx + 1, 'content' => $line];
        }

        if ($totalLength === null) {
            throw new \RuntimeException('Total length (LP:...) not found in file.');
        }
        if ($sideAWidth === null || $sideBWidth === null || $sideAThickness === null || $sideBThickness === null) {
            throw new \RuntimeException('Section line with SA/SB/TA/TB not found or incomplete.');
        }

        // 10) Append Cutting step at x = LP, y = 0
        // $steps[] = [
        //     'tenantId'       => 1,
        //     'opType'          => 'Cutting',
        //     'side'            => 'N/A',
        //     'opValue'         => '50',
        //     'xPos'            => (float)$totalLength,
        //     'yPos'            => 0.0,
        //     'measurementType' => 'absolute',
        // ];

        // Build header for itemRecipeMaster
        $itemRecipe = [
            'tenantId'   => 1,
            'itemCode'   => $itemCode !== null ? $itemCode : ($programCodeFromMark ?? ''),
            'sideAWidth' => (float)$sideAWidth,
            'sideBWidth' => (float)$sideBWidth,
            'sideAThickness'  => (float)$sideAThickness,
            'sideBThickness'  => (float)$sideBThickness,
            'programLength'   => (float)$totalLength,
            'material'   => $materialType ?? '',
        ];

        return [
            'itemRecipe' => $itemRecipe,
            'steps'      => $steps,
            'meta'       => $meta,
        ];
    }
}
