<?php

namespace Modules\Backend\productionMaster\Libraries;

class ProductionLib
{
    protected array $headList = [];
    protected array $cassetsData = [];
    protected array $programQueue = [];
    protected float $scale = 1.0;
    protected float $startOffset = 0.0;

    // defaults for per-program transforms
    protected array $defaultReverse = [
        'reverseX'  => false, // mirror x within item length
        'swapSides' => false  // A<->B
    ];

    public function __construct(array $machineHeads = [], array $cassetsData = [])
    {
        $this->setMachineHeads($machineHeads);
        $this->setCassetsData($cassetsData);
    }

    public function setCassetsData(array $cassetsData): void
    {
        $this->cassetsData = $cassetsData;
    }

    public function setStartOffset(float $offset): void
    {
        $this->startOffset = $offset;
    }

    public function setMachineHeads(array $heads): void
    {
        $this->headList = array_map(function ($h) {
            return [
                'headName'   => trim($h->headName),
                'headType'   => trim($h->headType),
                'side'       => trim($h->side),
                'xPosition'  => floatval($h->xPosition),
                'value'      => isset($h->value) ? trim($h->value) : null,
            ];
        }, $heads);
    }

    // optional global defaults for reverse behavior
    public function setReverseDefaults(array $options): void
    {
        $this->defaultReverse = array_replace($this->defaultReverse, $options);
    }

    // add per-program reverse options (override defaults)
    public function addProgram(object $program, array $programSteps, int $quantity, array $reverse = []): void
    {
        $this->programQueue[] = [
            'programSteps' => $programSteps,
            'quantity'     => $quantity,
            'program'      => $program,
            'reverse'      => array_replace($this->defaultReverse, $reverse),
        ];
    }

    public function resetPrograms(): void
    {
        $this->programQueue = [];
    }

    public function getTotalBarLength(): float
    {
        $total = 0;
        foreach ($this->programQueue as $entry) {
            $length = $entry['program']->programLength ?? 0;
            $total += $length * $entry['quantity'];
        }
        return $total;
    }

    protected function getProgramLength(array $programStep): float
    {
        $max = 0;
        foreach ($programStep as $step) {
            $max = max($max, floatval($step->xPos));
        }
        return $max;
    }

    protected function findMatchingHead(string $type, ?string $side, ?string $value): ?array
    {
        foreach ($this->headList as $head) {
            if (
                $head['headType'] === $type &&
                ($side === 'N/A' || $side == null || $head['side'] === $side) &&
                (!isset($head['value']) || $head['value'] == $value)
            ) {
                if ($head['headType'] === 'Marking') {
                    if (in_array($value, $this->cassetsData)) {
                        $index = array_search($value, $this->cassetsData);
                        $head['cassetId'] = $index;
                        return $head;
                    } else {
                        continue;
                    }
                }
                return $head;
            }

            if ($head['headType'] === $type && $type === 'Cutting') {
                return $head;
            }
        }
        return null;
    }

    public function processIncrements($programSteps)
    {
        $lastPosition = [];
        $lastPosition["A"] = 0;
        $lastPosition["B"] = 0;

        foreach ($programSteps as $k => $step) {
            if (strtolower($step->measurementType) === 'incremental') {
                if ($step->side === 'A') {
                    $lastPosition["A"] += floatval($step->xPos);
                    $step->xPos = $lastPosition["A"];
                } elseif ($step->side === 'B') {
                    $lastPosition["B"] += floatval($step->xPos);
                    $step->xPos = $lastPosition["B"];
                }
            } else {
                if ($step->side === 'A') {
                    $lastPosition["A"] = floatval($step->xPos);
                } elseif ($step->side === 'B') {
                    $lastPosition["B"] = floatval($step->xPos);
                }
            }
            $programSteps[$k] = $step;
        }
        return $programSteps;
    }

    // transform steps per reverse options
    protected function transformSteps(array $steps, float $itemLength, array $reverse): array
    {
        $out = [];
        foreach ($steps as $s) {
            $step = clone $s;

            // swap sides if requested
            if ($reverse['swapSides']) {
                if ($step->side === 'A') $step->side = 'B';
                elseif ($step->side === 'B') $step->side = 'A';
            }

            // mirror x within the item length
            if ($reverse['reverseX']) {
                $x = floatval($step->xPos);
                $step->xPos = max(0.0, round($itemLength - $x, 3));
            }

            $out[] = $step;
        }
        return $out;
    }

    public function generateAlignedProgram(): array
    {
        $errors = [];

        if (empty($this->programQueue)) {
            $errors[] = "No programs in queue.";
        }

        $firstProgram   = $this->programQueue[0]['program'] ?? (object)[];
        $sideAWidth     = $firstProgram->sideAWidth ?? null;
        $sideBWidth     = $firstProgram->sideBWidth ?? null;
        $sideAThickness = $firstProgram->sideAThickness ?? null;
        $sideBThickness = $firstProgram->sideBThickness ?? null;

        foreach ($this->programQueue as $entry) {
            $program = $entry['program'];
            if (
                ($program->sideAWidth ?? null) !== $sideAWidth ||
                ($program->sideBWidth ?? null) !== $sideBWidth
            ) {
                $errors[] = "All programs must have the same Bar Widths.";
            }
        }

        $final = [];
        $currentOffset = 0;
        $itemIndex = 1;

        $db = db_connect();
        $holdDownX = $db->query("SELECT holdDownX FROM machineDetails WHERE machineDetailId = 3")->getRow();
        $holdDownX = $holdDownX ? floatval($holdDownX->holdDownX) : 0;

        $final[] = [
            'itemIndex' => 0,
            'itemCode'  => '-',
            'itemRecipeId' => null,
            'headName'  => 'holdDown',
            'headType'  => '-',
            'headX'     => $holdDownX,
            'finalX'    => $holdDownX,
            'x'         => $holdDownX,
            'y'         => 0,
            'side'      => 'N/A',
            'value'     => null
        ];

        if ($this->startOffset > 0) {
            $head = $this->findMatchingHead('Cutting', 'N/A', null);
            $final[] = [
                'itemIndex' => 0,
                'itemCode'  => 'Start Offset',
                'itemRecipeId' => null,
                'headName'  => $head['headName'] ?? null,
                'headType'  => $head['headType'] ?? null,
                'headX'     => $head['xPosition'] ?? null,
                'finalX'    => ($head['xPosition'] ?? 0) + $this->startOffset,
                'x'         => $this->startOffset,
                'y'         => 0,
                'side'      => 'N/A',
                'value'     => null
            ];
        }

        foreach ($this->programQueue as $entry) {
            $programSteps = $this->processIncrements($entry['programSteps']);

            $qty         = $entry['quantity'];
            $programName = $entry['program']->itemCode;
            $itemRecipeId = $entry['program']->itemRecipeId;
            $itemLength  = floatval($entry['program']->programLength ?? 0);
            $reverse     = $entry['reverse'] ?? $this->defaultReverse;

            // apply reverse transforms after increments are resolved
            $programSteps = $this->transformSteps($programSteps, $itemLength, $reverse);

            for ($i = 0; $i < $qty; $i++) {
                $resolved = [];

                foreach ($programSteps as $step) {
                    $head = $this->findMatchingHead(
                        $step->opType,
                        $step->side,
                        $step->opValue ?? null
                    );

                    if (!$head) {
                        $error = "No matching head found for {$step->opType}, side={$step->side}, with value={$step->opValue} in machine setup.";
                        if (!in_array($error, $errors)) {
                            $errors[] = $error;
                        }
                    }

                    $startOffset = $this->startOffset;
                    if (strtolower($step->opType) === "cutting") {
                        $startOffset = 0;
                    }

                    $resolved[] = [
                        'itemIndex' => $itemIndex,
                        'itemCode'  => $programName,
                        'itemRecipeId' => $itemRecipeId,
                        'headName'  => $head['headName'] ?? null,
                        'headType'  => $head['headType'] ?? null,
                        'headX'     => $head['xPosition'] ?? null,
                        'finalX'    => round(($head['xPosition'] ?? 0) + $currentOffset + floatval($step->xPos) + $startOffset, 2),
                        'x'         => round($currentOffset + floatval($step->xPos) + $startOffset, 2),
                        'y'         => round(floatval($step->yPos), 2),
                        'side'      => $step->side,
                        'value'     => $step->opValue ?? null,
                        'cassetId'  => $head['cassetId'] ?? null
                    ];
                }

                // add cutting at itemLength
                $head = $this->findMatchingHead('Cutting', 'N/A', null);
                $resolved[] = [
                    'itemIndex' => $itemIndex,
                    'itemCode'  => $programName,
                    'itemRecipeId' => $itemRecipeId,
                    'headName'  => $head['headName'] ?? null,
                    'headType'  => $head['headType'] ?? null,
                    'headX'     => $head['xPosition'] ?? null,
                    'finalX'    => round(($head['xPosition'] ?? 0) + $currentOffset + $itemLength + $this->startOffset, 2),
                    'x'         => round($currentOffset + $itemLength + $this->startOffset, 2),
                    'y'         => 0,
                    'side'      => 'N/A',
                    'value'     => null,
                    'cassetId'  => null
                ];

                $final = array_merge($final, $resolved);
                $currentOffset += $itemLength;
                $itemIndex++;
            }
        }

        usort($final, fn($a, $b) => $a['finalX'] <=> $b['finalX']);

        $serialNumber = 1;
        foreach ($final as &$step) {
            $step = array_merge(['serialNo' => $serialNumber++], $step);
        }
        unset($step);

        return [
            "status"         => empty($errors),
            "program"        => $final,
            "barLength"      => $this->getTotalBarLength(),
            "totalItems"     => count($final),
            "totalLength"    => $this->getTotalBarLength() + $this->startOffset,
            "sideAWidth"     => $sideAWidth,
            "sideBWidth"     => $sideBWidth,
            "sideAThickness" => $sideAThickness,
            "sideBThickness" => $sideBThickness,
            "errors"         => $errors
        ];
    }
}
