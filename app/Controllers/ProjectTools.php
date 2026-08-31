<?php

namespace App\Controllers;

use Modules\Backend\productionMaster\Libraries\ProductionLib;

class ProjectTools extends BaseController
{
    public function everyMinuteCrone()
    {
        if (!is_cli()) {
            return $this->response->setStatusCode(403, 'Forbidden - CLI Access Only');
        }

        // Get the current hour and minute
        $currentHour = date('H');
        $currentMinute = date('i');

        $device = "/dev/null";
        // $device = WRITEPATH . "logs/cron.log";

        //run every minute
        exec(getenv("phpPath") . " " . FCPATH . "index.php tools sendAutoAlertEmails  > $device &");

        // run at midnight for cleanup.
        if ($currentHour === '00' && $currentMinute === '00') {
            exec(getenv("phpPath") . " " . FCPATH . "index.php projecttools cleanup  > $device &");
        }

        // run at evening 7 pm.
        if ($currentHour === '19' && $currentMinute === '00') {
        }

        // run every 6 minutes
        if ($currentMinute % 6 === 0) {
        }

        // run every 5 minutes
        if ($currentMinute % 5 === 0) {
            // It's 5 minutes, run the scheduled tasks
        }
    }

    // add project specific cron jobs below. and add the trigger in above everyMinuteCrone method.
    public function test()
    {
        $client = \Config\Services::curlrequest();
        $response = $client->post('http://127.0.0.1:3002/api/write-tags', [
            'headers' => [
                'X-Internal-Token' => getenv('internalApiToken')
            ],
            'json' => [
                'tags' => [
                    'ns=4;s=|var|LicOS-PAC-MC512.Application.X_AXIS.RUN' => true
                ]
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        debug($data);
        if ($data['success']) {
            echo "Write success!";
        } else {
            echo "Error: " . $data['error'];
        }
    }

    public function tt()
    {
        $this->db = \Config\Database::connect();

        $heads = $this->db->query("SELECT MD.*,MS.value FROM `machineDetails` AS MD
                LEFT JOIN (SELECT * FROM machineSetup  WHERE childId = 0 ) MS ON MD.machineDetailId = MS.machineDetailId
                WHERE MD.machineId = " . $this->user->tenantId)->getResult();

        $headPositions = [];

        foreach ($heads as $head) {
            $headPositions[$head->machineDetailId] = $head;
        }

        // Set head positions (mm from ref point)
        $lib = new ProductionLib($headPositions);
        $lib->setStartOffset(100); // Set start offset if needed


        $recipesToProcess = [2, 1];

        foreach ($recipesToProcess as $recipeId) {
            $recipe = $this->db->query("SELECT * FROM `itemRecipeMaster` WHERE `itemRecipeId` = $recipeId")->getRow();
            $recipeSteps = $this->db->query("SELECT * FROM `itemRecipeSteps` WHERE `itemRecipeId` = $recipeId")->getResult();
            $lib->addRecipe("$recipe->itemCode", $recipeSteps, 2);
        }

        $program = $lib->generateAlignedProgram();

        $length = $lib->getTotalBarLength();
        debug($length);

        // foreach ($program as $p) {
        //     echo $p['finalHeadX'] . "<br>";
        // }

        printArrayAsTable($program['program']);


        echo "<pre>";
        print_r($program);
        echo "</pre>";
    }

    public function manageNodeApp()
    {
        $action = $this->request->getGet('action') ?? 'status';
        $lines = $this->request->getGet('lines') ?? 100;
        $service = 'scada-node.service';

        $result = [
            'action' => $action,
            'status' => 'unknown',
            'output' => null
        ];

        switch ($action) {
            case 'start':
                $result['output'] = shell_exec("sudo systemctl start $service 2>&1");
                $result['status'] = 'started';
                break;

            case 'stop':
                $result['output'] = shell_exec("sudo systemctl stop $service 2>&1");
                $result['status'] = 'stopped';
                break;

            case 'restart':
                $result['output'] = shell_exec("sudo systemctl restart $service 2>&1");
                sleep(1);
                $status = trim(shell_exec("sudo systemctl is-active $service"));
                $result['status'] = $status === 'active' ? 'success' : 'error';
                $result['message'] = $status === 'active' ? 'Restarted successfully' : 'Failed to restart';
                break;

            case 'logs':
                $logs = shell_exec("sudo journalctl -u $service -n $lines --no-pager 2>&1");
                $result['status'] = 'logs';
                $result['logs'] = explode("\n", trim($logs));
                break;

            case 'status':
            default:
                $status = trim(shell_exec("sudo systemctl is-active $service 2>&1"));
                $result['status'] = $status;
                break;
        }

        return $this->response->setJSON($result);
    }

    public function cleanup()
    {
        if (!is_cli()) {
            return $this->response->setStatusCode(403, 'Forbidden - CLI Access Only');
        }

        // Configure cleanup period - older than X days will be deleted
        $daysToKeep = 30; // Keep last 30 days by default, change as needed

        $db = \Config\Database::connect();

        // Calculate cutoff date
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysToKeep} days"));
        $cutoffDateOnly = date('Y-m-d', strtotime("-{$daysToKeep} days"));

        $totalDeleted = 0;
        $cleanupLog = [];

        // Define tables to cleanup with their respective date columns
        $tablesToCleanup = [
            'tagWriteHistory' => 'writeTime',
            'timerCounters' => 'endTime',
            'punchCounters' => 'startHour',
            'productionMaster' => 'completedAt',
            'programAlignMaster' => 'updatedAt'
        ];

        foreach ($tablesToCleanup as $tableName => $dateColumn) {
            // Use appropriate date format based on column type
            $cutoffValue = ($dateColumn === 'startHour') ? $cutoffDateOnly : $cutoffDate;

            // Count records to be deleted
            $countToDelete = $db->table($tableName)
                ->where($dateColumn . ' <', $cutoffValue)
                ->countAllResults();

            if ($countToDelete > 0) {
                // Delete records older than cutoff date
                $db->table($tableName)
                    ->where($dateColumn . ' <', $cutoffValue)
                    ->delete();

                // Get actual number of affected rows
                $deletedRows = $db->affectedRows();
                $totalDeleted += $deletedRows;

                echo "{$tableName} cleanup: Deleted {$deletedRows} records older than {$daysToKeep} days\n";
                $cleanupLog[] = "{$tableName}: {$deletedRows} records";
            } else {
                echo "{$tableName} cleanup: No records older than {$daysToKeep} days found\n";
                $cleanupLog[] = "{$tableName}: 0 records";
            }
        }

        // Log summary to file for audit trail
        $logMessage = date('Y-m-d H:i:s') . " - Database cleanup completed. Total deleted: {$totalDeleted} records. Details: " . implode(', ', $cleanupLog) . "\n";
        file_put_contents(WRITEPATH . 'logs/cleanup.log', $logMessage, FILE_APPEND | LOCK_EX);

        echo "Database cleanup completed. Total deleted: {$totalDeleted} records from all tables\n";

        return true;
    }
}
