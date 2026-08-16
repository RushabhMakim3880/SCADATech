<?php

namespace Modules\Backend\productionMaster\Controllers;

use App\Controllers\ApiBaseController;
use CodeIgniter\API\ResponseTrait;
use Modules\Backend\productionMaster\Libraries\ProductionLib;
use Exception;


// use OpenApi\Annotations as OA;
class productionMaster extends ApiBaseController
{
    use ResponseTrait;
    protected $PlcMasterModel;

    public function __construct() {}

    public function pendingJobcards()
    {
        $jobcards = $this->db->query("SELECT JC.*, IRM.itemCode,IRM.sideAWidth,IRM.sideBWidth,IRM.sideAThickness,IRM.sideBThickness,IRM.material,IRM.programLength FROM `productionJobCards` JC
            LEFT JOIN itemRecipeMaster IRM ON JC.itemRecipeId = IRM.itemRecipeId
         WHERE JC.status IN ('waiting','started','partiallyCompleted') AND JC.tenantId = '" . $this->user->tenantId . "'")->getResult();

        $myJobcards = [];
        foreach ($jobcards as $jobcard) {
            if (!isset($myJobcards[$jobcard->itemRecipeId])) {

                $previewButton = "<a href='javascript:;' 
                    class='btn btn-primary ms-1 previewProgram' 
                    data-programid='" . setKey($jobcard->itemRecipeId, "previewProgram") . "' 
                    title='Program Preview'>
                    <i class='fa fa-image'></i>
                </a>";

                $detailsButton = "<a href='javascript:;' 
                    class='btn btn-info ms-1 apiPopup' 
                    data-size='lg' 
                    data-title='Program  Details' 
                    data-endpoint='" . base_url("api/ItemRecipeMaster/itemRecipeDetails/" . setKey($jobcard->itemRecipeId, "Itemrecipemaster")) . "'
                    title='Program Details'>
                    <i class='fa fa-list'></i>
                </a>";

                $selectButton = "<a href='javascript:;' 
                    class='btn btn-success ms-1 selectJobcard' 
                    data-itemname='" . $jobcard->itemCode . "'
                    data-recipeid='" . $jobcard->itemRecipeId . "' 
                    title='Select Jobcard'>
                    <i class='fa fa-check-circle'></i>
                </a>";

                $myJobcards[$jobcard->itemRecipeId] = [
                    'itemRecipeId' => $jobcard->itemRecipeId,
                    'itemCode' => $jobcard->itemCode,
                    'width' => $jobcard->sideAWidth . "," . $jobcard->sideBWidth,
                    'thickness' => $jobcard->sideAThickness,
                    'material' => $jobcard->material,
                    'programLength' => $jobcard->programLength,
                    'requiredQuantity' => $jobcard->requiredQuantity,
                    'completedQuantity' => $jobcard->completedQuantity,
                    'previewButton' => $previewButton,
                    'detailsButton' => $detailsButton,
                    'selectButton' => $selectButton,
                ];
            } else {
                $myJobcards[$jobcard->itemRecipeId]['requiredQuantity'] += ($jobcard->requiredQuantity);
                $myJobcards[$jobcard->itemRecipeId]['completedQuantity'] += ($jobcard->completedQuantity);
            }
        }

        return $this->respond(['status' => true, 'data' => array_values($myJobcards)], 200);
    }

    public function programAlign()
    {
        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        $heads = $this->db->query("SELECT MD.*,MS.value FROM `machineDetails` AS MD
                LEFT JOIN (SELECT * FROM machineSetup  WHERE childId = 0 ) MS ON MD.machineDetailId = MS.machineDetailId
                WHERE MD.machineId = " . $this->user->tenantId)->getResult();

        // debug($heads);
        if (isset($jsonInput["tagValues"]))
            updateTagValuesToDb($jsonInput["tagValues"]);

        $holdDownMm = $jsonInput["tagValues"][583] ?? 0;

        $headPositions = [];
        $cassetsData = [];

        foreach ($heads as $head) {
            $headPositions[$head->machineDetailId] = $head;

            //load marking cassets values
            if ($head->headType === 'Marking') {
                $temp = $this->db->query("SELECT childId,value FROM `machineSetup` WHERE machineDetailId = $head->machineDetailId");
                $cassets = $temp->getResult();
                foreach ($cassets as $casset) {
                    $cassetsData[$casset->childId] = $casset->value;
                }
            }
        }


        $lib = new ProductionLib($headPositions, $cassetsData, $holdDownMm);
        $lib->setStartOffset($jsonInput['leadScrap'] ?? 100); //

        $maxBarLenthLimit = 20000; // Maximum bar length limit
        $expectedLength = 0;

        foreach ($jsonInput['programItems'] as $item) {
            $recipeId = $item['recipeId'];
            $qnt = $item['quantity'];

            if (is_numeric($recipeId) && is_numeric($qnt)) {
                $recipe = $this->db->query("SELECT * FROM `itemRecipeMaster` WHERE `itemRecipeId` = $recipeId")->getRow();
                $recipeSteps = $this->db->query("SELECT * FROM `itemRecipeSteps` WHERE `itemRecipeId` = $recipeId ORDER BY ordId ASC")->getResult();

                $max = 0;
                foreach ($recipeSteps as $step) {
                    $max = max($max, floatval($step->xPos));
                }

                $expectedLength += $max * $qnt;

                if ($expectedLength > $maxBarLenthLimit) {
                    return $this->respond(['status' => false, 'message' => 'Expected length exceeds maximum bar length limit of ' . $maxBarLenthLimit . 'mm'], 400);
                }

                $reverse = ['reverseX'  => false, 'swapSides' => false];
                if (isset($item['isReverse']) && $item['isReverse'] == true) {
                    $reverse = ['reverseX'  => true, 'swapSides' => true];
                }

                $lib->addProgram($recipe, $recipeSteps, intval($qnt), $reverse);
            }
        }

        $program = $lib->generateAlignedProgram();

        if ($program['status'] === false) {
            return $this->respond(['status' => false, 'message' => "Error in machine setup", 'errors' => implode("<br><hr>", $program['errors']), 'data' => $program]);
        }

        // Perform program alignment logic here
        return $this->respond(['status' => true, 'message' => 'Program aligned successfully', 'data' => $program], 200);
    }

    public function loadSettings()
    {
        $settings = $this->db->table('settings')->where('tenantId', $this->user->tenantId)->get()->getResultArray();

        $settingsArray = [];
        foreach ($settings as $setting) {
            $settingsArray[$setting['key']] = $setting['value'];
        }

        // load idle reasons
        $idleReasons = $this->db->table('idleReasons')->where('tenantId', $this->user->tenantId)->where('isActive', 1)->orderBy('serialNo', 'ASC')->get()->getResultArray();
        $settingsArray['idleReasons'] = array_map(function ($reason) {
            return [
                'id' => $reason['idleReasonId'],
                'label' => $reason['label']
            ];
        }, $idleReasons);

        // load pause reasons
        $pauseReasons = $this->db->table('pauseReasons')->where('tenantId', $this->user->tenantId)->where('isActive', 1)->orderBy('serialNo', 'ASC')->get()->getResultArray();
        $settingsArray['pauseReasons'] = array_map(function ($reason) {
            return [
                'id' => $reason['pauseReasonId'],
                'label' => $reason['label']
            ];
        }, $pauseReasons);

        //load last saved program state
        $programState = $this->db->table('programAlignMaster')
            ->where('tenantId', $this->user->tenantId)
            ->orderBy('updatedAt', 'DESC')
            ->get()
            ->getRow();

        if ($programState) {
            $programStateJson = json_decode($programState->fullProgram, true);
        } else {
            $programStateJson = null;
        }

        $settingsArray['lastProgramState'] = $programStateJson;
        $settingsArray['lastProgramId'] = $programState->programId ?? null;

        return $this->respond(['status' => true, 'data' => $settingsArray], 200);
    }

    public function storeProgramState($programId = 0)
    {
        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        // if ($programId)
        //     $programId = getKey($programId, "programAlignMaster");

        $programStateJson = $jsonInput['programState'] ?? null;
        $machineSetup = $jsonInput['machineSetup'] ?? null;

        if ($programId) {
            // Update programAlignMaster with program state
            $this->db->table('programAlignMaster')->where('programId', $programId)->update([
                "tenantId" => $this->user->tenantId,
                "fullProgram" => json_encode($programStateJson),
                "machineSetup" => json_encode($machineSetup),
                "completedCycles" => $programStateJson['completedCycles'] ?? 0,
                "totalItems" => $programStateJson['counters']['totalItems'] ?? 0,
                "totalOperations" => $programStateJson['counters']['totalOperations'] ?? 0,
                "DA1" => $programStateJson['counters']['DA1'] ?? 0,
                "DA2" => $programStateJson['counters']['DA2'] ?? 0,
                "DA3" => $programStateJson['counters']['DA3'] ?? 0,
                "DB1" => $programStateJson['counters']['DB1'] ?? 0,
                "DB2" => $programStateJson['counters']['DB2'] ?? 0,
                "DB3" => $programStateJson['counters']['DB3'] ?? 0,
                "Marking1" => $programStateJson['counters']['Marking1'] ?? 0,
                "Marking2" => $programStateJson['counters']['Marking2'] ?? 0,
                "Marking3" => $programStateJson['counters']['Marking3'] ?? 0,
                "Marking4" => $programStateJson['counters']['Marking4'] ?? 0,
                "cuttings" => $programStateJson['counters']['cuttings'] ?? 0,
                "updatedBy" => $this->user->userId,
                "updatedAt" => date('Y-m-d H:i:s'),
            ]);
        } else {
            $programStateRow = [
                "tenantId" => $this->user->tenantId,
                "fullProgram" => json_encode($programStateJson),
                "machineSetup" => json_encode($machineSetup),
                "completedCycles" => 0,
                "totalItems" => 0,
                "totalOperations" => 0,
                "DA1" => 0,
                "DA2" => 0,
                "DA3" => 0,
                "DB1" => 0,
                "DB2" => 0,
                "DB3" => 0,
                "Marking1" => 0,
                "Marking2" => 0,
                "Marking3" => 0,
                "Marking4" => 0,
                "cuttings" => 0,
                "updatedBy" => $this->user->userId,
                "updatedAt" => date('Y-m-d H:i:s'),
                "createdAt" => date('Y-m-d H:i:s'),
                "createdBy" => $this->user->userId,
            ];

            // Insert new row
            $this->db->table('programAlignMaster')->insert($programStateRow);
            $programId = $this->db->insertID();

            assignSerialNumber($this->user->tenantId, 'programAlignMaster', 'programId', $programId);
        }

        // $programId = setKey($programId, "programAlignMaster");

        return $this->respond(['status' => true, 'data' => $programStateJson, 'programId' => $programId], 200);
    }

    /**
     * Records the completion of a single item in production
     * 
     * This method handles:
     * 1. Finding the oldest eligible jobcard for the item
     * 2. Creating/updating production records per user+program+jobId combination
     * 3. Updating jobcard status and quantities
     * 
     * @return ResponseInterface JSON response with success/error status
     */
    public function recordItemCompletion()
    {
        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        $itemRecipeId = $jsonInput['itemRecipeId'] ?? null;
        $programId = $jsonInput['programId'] ?? null;

        // Validate required fields
        if (!$itemRecipeId || !$programId) {
            return $this->respond(['status' => false, 'message' => 'itemRecipeId and programId are required'], 400);
        }

        // Start transaction
        $this->db->transStart();

        try {
            $currentDateTime = date('Y-m-d H:i:s');

            // Find oldest jobcard for this itemRecipeId where completedQuantity < requiredQuantity
            $jobcard = $this->db->query("
                SELECT * FROM productionJobCards 
                WHERE itemRecipeId = ? 
                AND completedQuantity < requiredQuantity 
                AND status IN ('waiting', 'started')
                AND tenantId = ?
                ORDER BY jobId ASC 
                LIMIT 1
            ", [$itemRecipeId, $this->user->tenantId])->getRow();

            if (!$jobcard) {
                // Handle over-production: find the last completed jobcard for this item
                $lastCompletedJobcard = $this->db->query("
                    SELECT * FROM productionJobCards 
                    WHERE itemRecipeId = ? 
                    AND status = 'completed'
                    AND tenantId = ?
                    ORDER BY updatedAt DESC 
                    LIMIT 1
                ", [$itemRecipeId, $this->user->tenantId])->getRow();

                if ($lastCompletedJobcard) {
                    // Record as over-production in the last completed jobcard
                    $this->db->table('productionJobCards')
                        ->where('jobId', $lastCompletedJobcard->jobId)
                        ->update([
                            'completedQuantity' => $lastCompletedJobcard->completedQuantity + 1,
                            'updatedBy' => $this->user->userId,
                            'updatedAt' => $currentDateTime
                        ]);

                    // Check if production record exists for this user+program+jobId combination
                    $existingOverProduction = $this->db->query("
                        SELECT * FROM productionMaster 
                        WHERE userId = ? AND programId = ? AND jobId = ? AND tenantId = ?
                    ", [$this->user->userId, $programId, $lastCompletedJobcard->jobId, $this->user->tenantId])->getRow();

                    if ($existingOverProduction) {
                        // Update existing production record for over-production
                        $this->db->table('productionMaster')
                            ->where('productionId', $existingOverProduction->productionId)
                            ->update([
                                'quantityProduced' => $existingOverProduction->quantityProduced + 1,
                                'completedAt' => $currentDateTime, // Always update end time
                            ]);
                    } else {
                        // Create new production record for over-production tracking
                        $productionData = [
                            'tenantId' => $this->user->tenantId,
                            'programId' => $programId,
                            'jobId' => $lastCompletedJobcard->jobId,
                            'quantityProduced' => 1,
                            'startedAt' => $currentDateTime,
                            'completedAt' => $currentDateTime,
                            'userId' => $this->user->userId,
                        ];
                        $this->db->table('productionMaster')->insert($productionData);
                        $productionId = $this->db->insertID();
                        assignSerialNumber($this->user->tenantId, 'productionMaster', 'productionId', $productionId);
                    }

                    $this->db->transComplete();

                    return $this->respond([
                        'status' => true,
                        'message' => 'All jobcards completed! This item recorded as over-production.',
                        'allJobcardsCompleted' => true,
                        'data' => [
                            'jobId' => $lastCompletedJobcard->jobId,
                            'itemRecipeId' => $itemRecipeId,
                            'completedQuantity' => $lastCompletedJobcard->completedQuantity + 1,
                            'requiredQuantity' => $lastCompletedJobcard->requiredQuantity,
                            'status' => 'completed',
                            'overProduction' => true
                        ]
                    ], 200);
                } else {
                    $this->db->transRollback();
                    return $this->respond(['status' => false, 'message' => 'No eligible jobcard found for this item'], 404);
                }
            }

            // Check if production record exists for this user+program+jobId combination
            $existingProduction = $this->db->query("
                SELECT * FROM productionMaster 
                WHERE userId = ? AND programId = ? AND jobId = ? AND tenantId = ?
            ", [$this->user->userId, $programId, $jobcard->jobId, $this->user->tenantId])->getRow();

            if ($existingProduction) {
                // Update existing production record
                $this->db->table('productionMaster')
                    ->where('productionId', $existingProduction->productionId)
                    ->update([
                        'quantityProduced' => $existingProduction->quantityProduced + 1,
                        'completedAt' => $currentDateTime, // Always update end time
                        // 'updatedBy' => $this->user->userId,
                        // 'updatedAt' => $currentDateTime
                    ]);
            } else {
                // Create new production record
                $productionData = [
                    'tenantId' => $this->user->tenantId,
                    'programId' => $programId,
                    'jobId' => $jobcard->jobId,
                    'quantityProduced' => 1,
                    'startedAt' => $currentDateTime, // Set start time only for new records
                    'completedAt' => $currentDateTime,
                    'userId' => $this->user->userId,
                    // 'createdBy' => $this->user->userId,
                    // 'createdAt' => $currentDateTime,
                    // 'updatedBy' => $this->user->userId,
                    // 'updatedAt' => $currentDateTime
                ];

                $this->db->table('productionMaster')->insert($productionData);
                $productionId = $this->db->insertID();

                // Assign serial number
                assignSerialNumber($this->user->tenantId, 'productionMaster', 'productionId', $productionId);
            }

            // Update jobcard completedQuantity
            $newCompletedQuantity = $jobcard->completedQuantity + 1;
            $newStatus = $jobcard->status;

            // Prepare update data
            $jobcardUpdateData = [
                'completedQuantity' => $newCompletedQuantity,
                'updatedBy' => $this->user->userId,
                'updatedAt' => $currentDateTime
            ];

            // Update jobcard status and timestamps if needed
            if ($jobcard->status === 'waiting') {
                $newStatus = 'started';
                $jobcardUpdateData['status'] = $newStatus;
                $jobcardUpdateData['startedAt'] = $currentDateTime; // Set startedAt when moving from waiting to started
            } elseif ($newCompletedQuantity >= $jobcard->requiredQuantity) {
                $newStatus = 'completed';
                $jobcardUpdateData['status'] = $newStatus;
                $jobcardUpdateData['completedAt'] = $currentDateTime; // Set completedAt when jobcard is completed
            }

            $this->db->table('productionJobCards')
                ->where('jobId', $jobcard->jobId)
                ->update($jobcardUpdateData);

            // Check if this was the last item and there are no more jobcards for this item
            $remainingJobcards = $this->db->query("
                SELECT COUNT(*) as count FROM productionJobCards 
                WHERE itemRecipeId = ? 
                AND completedQuantity < requiredQuantity 
                AND status IN ('waiting', 'started')
                AND tenantId = ?
            ", [$itemRecipeId, $this->user->tenantId])->getRow();

            $allJobcardsCompleted = ($remainingJobcards->count == 0);

            // Commit transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === FALSE) {
                return $this->respond(['status' => false, 'message' => 'Database transaction failed'], 500);
            }

            $responseMessage = "Item completion recorded successfully";
            if ($newStatus === 'completed') {
                $responseMessage .= ". Jobcard completed!";
            }
            if ($allJobcardsCompleted) {
                $responseMessage .= " ALL JOBCARDS FOR THIS ITEM ARE NOW COMPLETED. Further production will be recorded as over-production.";
            }

            return $this->respond([
                'status' => true,
                'message' => $responseMessage,
                'allJobcardsCompleted' => $allJobcardsCompleted,
                'data' => [
                    'jobId' => $jobcard->jobId,
                    'itemRecipeId' => $itemRecipeId,
                    'completedQuantity' => $newCompletedQuantity,
                    'requiredQuantity' => $jobcard->requiredQuantity,
                    'status' => $newStatus,
                    'allJobcardsCompleted' => $allJobcardsCompleted
                ]
            ], 200);
        } catch (Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in recordItemCompletion: ' . $e->getMessage());
            return $this->respond(['status' => false, 'message' => 'An error occurred while recording item completion'], 500);
        }
    }

    /**
     * Records punch operation completion for hourly punch counting
     * 
     * This method handles:
     * 1. Creating/updating punch count records per user+program+item+hour combination
     * 2. Grouping punch counts by hour (current hour with minutes/seconds as 00:00)
     * 
     * @return ResponseInterface JSON response with success/error status
     */
    public function recordPunchCount()
    {
        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        $itemRecipeId = $jsonInput['itemRecipeId'] ?? null;
        $programId = $jsonInput['programId'] ?? null;

        // Validate required fields
        if (!$itemRecipeId || !$programId) {
            return $this->respond(['status' => false, 'message' => 'itemRecipeId and programId are required'], 400);
        }

        // Start transaction
        $this->db->transStart();

        try {
            $currentDateTime = date('Y-m-d H:i:s');
            // Create current hour timestamp (set minutes and seconds to 00:00)
            $currentHour = date('Y-m-d H:00:00');

            // Check if punch counter record exists for this user+program+item+hour combination
            $existingPunchCounter = $this->db->query("
                SELECT * FROM punchCounters 
                WHERE userId = ? AND programId = ? AND itemRecipeId = ? AND startHour = ? AND tenantId = ?
            ", [$this->user->userId, $programId, $itemRecipeId, $currentHour, $this->user->tenantId])->getRow();

            if ($existingPunchCounter) {
                // Update existing punch counter record
                $this->db->table('punchCounters')
                    ->where('punchId', $existingPunchCounter->punchId)
                    ->update([
                        'punchCount' => $existingPunchCounter->punchCount + 1
                    ]);

                $newPunchCount = $existingPunchCounter->punchCount + 1;
            } else {
                // Create new punch counter record
                $punchCounterData = [
                    'tenantId' => $this->user->tenantId,
                    'programId' => $programId,
                    'itemRecipeId' => $itemRecipeId,
                    'punchCount' => 1,
                    'startHour' => $currentHour,
                    'userId' => $this->user->userId
                ];

                $this->db->table('punchCounters')->insert($punchCounterData);
                $punchId = $this->db->insertID();

                // Assign serial number
                assignSerialNumber($this->user->tenantId, 'punchCounters', 'punchId', $punchId);

                $newPunchCount = 1;
            }

            // Commit transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === FALSE) {
                return $this->respond(['status' => false, 'message' => 'Database transaction failed'], 500);
            }

            return $this->respond([
                'status' => true,
                'message' => 'Punch count recorded successfully',
                'data' => [
                    'itemRecipeId' => $itemRecipeId,
                    'programId' => $programId,
                    'startHour' => $currentHour,
                    'punchCount' => $newPunchCount
                ]
            ], 200);
        } catch (Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in recordPunchCount: ' . $e->getMessage());
            return $this->respond(['status' => false, 'message' => 'An error occurred while recording punch count'], 500);
        }
    }

    /**
     * Logs completed machine state records for timer tracking and efficiency analysis
     * 
     * This method handles storing completed time records with accurate start/end times
     * and durations as calculated by the productionRuntime.js library
     * 
     * @return ResponseInterface JSON response with success/error status
     */
    public function logCompletedStateRecord()
    {
        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];

        $recordType = $jsonInput['recordType'] ?? null;
        $startTime = $jsonInput['startTime'] ?? null;
        $endTime = $jsonInput['endTime'] ?? null;
        $durationSec = $jsonInput['durationSec'] ?? null;
        $reasonId = $jsonInput['reasonId'] ?? null;
        $reasonLabel = $jsonInput['reasonLabel'] ?? null;
        $cycleIndex = $jsonInput['cycleIndex'] ?? null;
        $programId = $jsonInput['programId'] ?? null;

        // Validate required fields
        if (!$recordType || !$startTime || !$endTime || $durationSec === null) {
            return $this->respond(['status' => false, 'message' => 'recordType, startTime, endTime, and durationSec are required'], 400);
        }

        // Validate state enum values
        $validStates = ['RUNNING', 'PAUSED', 'IDLE', 'MANUAL_OP'];
        if (!in_array($recordType, $validStates)) {
            return $this->respond(['status' => false, 'message' => 'Invalid recordType value'], 400);
        }

        // Start transaction
        $this->db->transStart();

        try {
            // Convert duration from seconds to minutes for storage
            $durationMinutes = max(0, floor($durationSec / 60));

            // Create timer entry with completed data
            $timerData = [
                'tenantId' => $this->user->tenantId,
                'state' => $recordType,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'durationInMinutes' => $durationMinutes,
                'userId' => $this->user->userId
            ];

            // Add programId if available and non-zero
            if ($programId && $programId > 0) {
                $timerData['programId'] = $programId;
            }

            // Add reasonId if provided (for PAUSED/IDLE states)
            if ($reasonId) {
                $timerData['reasonId'] = $reasonId;
            }

            $this->db->table('timerCounters')->insert($timerData);
            $timerId = $this->db->insertID();

            // Assign serial number
            assignSerialNumber($this->user->tenantId, 'timerCounters', 'timerId', $timerId);

            // Commit transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === FALSE) {
                return $this->respond(['status' => false, 'message' => 'Database transaction failed'], 500);
            }

            return $this->respond([
                'status' => true,
                'message' => 'State record logged successfully',
                'data' => [
                    'timerId' => $timerId,
                    'recordType' => $recordType,
                    'startTime' => $startTime,
                    'endTime' => $endTime,
                    'durationMinutes' => $durationMinutes,
                    'durationSec' => $durationSec,
                    'programId' => $programId,
                    'reasonId' => $reasonId,
                    'reasonLabel' => $reasonLabel,
                    'cycleIndex' => $cycleIndex
                ]
            ], 200);
        } catch (Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in logCompletedStateRecord: ' . $e->getMessage());
            return $this->respond(['status' => false, 'message' => 'An error occurred while logging state record'], 500);
        }
    }
}
