<?php

namespace Modules\Backend\OpMaster\Controllers;

use App\Controllers\ApiBaseController;
use CodeIgniter\API\ResponseTrait;
use Modules\Backend\PlcMaster\Models\PlcMasterModel;

// use OpenApi\Annotations as OA;
class OpMasterBack extends ApiBaseController
{
    use ResponseTrait;
    protected $PlcMasterModel;

    public function __construct()
    {
        $this->PlcMasterModel = new PlcMasterModel();
    }
    public function submitData()
    {
        // get json data from request
        $jsonData = $this->request->getJSON(true);

        // log data to log file
        log_message('info', 'Received data: ' . json_encode($jsonData));

        return $this->respond(['status' => true, 'message' => '', 'data' => $jsonData]);
    }

    public function getConfig()
    {

        $plcId = 1;
        $plc = $this->PlcMasterModel->find($plcId);

        $plcConfig = [
            'host' => $plc->ipAddress,
            'port' => $plc->port,
            'protocol' => $plc->protocol,
        ];

        $tags = $this->db->table("plcTagMaster")->where([
            "plcId" => $plcId,
        ])->get()->getResult();

        $myTagsToRead = [];
        foreach ($tags as $tag) {
            $myTagsToRead[$tag->tagId] = $tag->tagAddress;
        }

        $alarmConfig = $this->db->query("SELECT AC.`alarmId`, AC.`uiTagId` as `tagId`, AC.`loloTheresold`,AC.`loTheresold`,AC.`hiTheresold`,AC.`hihiTheresold`, AC.message , UT.`tagName`
                                        FROM AlarmConfig AC 
                                        LEFT JOIN uiTagMaster UT ON AC.uiTagId = UT.uiTagId 
                                        WHERE AC.tenantId = 1 AND UT.isActive = 1 AND AC.isActive = 1")->getResult();

        foreach ($alarmConfig as $k => $alarm) {
            $alarmConfig[$k]->alarmId = (int)$alarm->alarmId;
            $alarmConfig[$k]->tagId = (int)$alarm->tagId;
            $alarmConfig[$k]->loloTheresold = (float)$alarm->loloTheresold;
            $alarmConfig[$k]->loTheresold = (float)$alarm->loTheresold;
            $alarmConfig[$k]->hiTheresold = (float)$alarm->hiTheresold;
            $alarmConfig[$k]->hihiTheresold = (float)$alarm->hihiTheresold;
            $alarmConfig[$k]->message = (string)$alarm->message;
        }

        $alarmStatus = [];

        //get active alarms.
        $alarms = $this->db->query("SELECT AL.*,AC.message, UT.tagName FROM AlarmLog AL 
                        LEFT JOIN AlarmConfig AC ON AL.alarmId = AC.alarmId
                        LEFT JOIN uiTagMaster UT ON AC.uiTagId = UT.uiTagId
                        WHERE UT.isActive = 1 AND AC.isActive = 1 AND AL.isResolved = 0 AND AL.tenantId = 1")->getResult();

        foreach ($alarms as $alarm) {
            $key = "{$alarm->alarmId}:{$alarm->alarmType}";
            $alarmStatus[$key] = true;
        }


        return $this->respond([
            'status' => true,
            'message' => '',
            'data' => [
                'plcConfig' => $plcConfig,
                'continuesReadTags' => $myTagsToRead,
                'readLoopInterval' => 500, // This can be adjusted as needed
                'alarmConfig' => $alarmConfig,
                'alarmStatus' => $alarmStatus
            ]
        ]);
    }

    public function submitAlarmData()
    {
        $alarmData = $this->request->getJSON();

        if (empty($alarmData)) {
            return $this->respond(['status' => false, 'message' => 'No alarm data provided'], 400);
        }

        $alarmIdDetails = explode(':', $alarmData->alarmId);
        if (count($alarmIdDetails) !== 2) {
            return $this->respond(['status' => false, 'message' => 'Invalid alarm ID format'], 400);
        }
        $alarmId = (int)$alarmIdDetails[0];
        $alarmType = $alarmIdDetails[1];

        //check if alarm is resolved.
        if ($alarmData->action === 'resolve') {
            $ex = $this->db->table('AlarmLog')
                ->where('alarmId', $alarmId)
                ->where('uiTagId', $alarmData->tagId)
                ->where('alarmType', $alarmType)
                ->where('isResolved', 0)
                ->get()
                ->getRow();

            if ($ex) {
                $this->db->table('AlarmLog')
                    ->where('alarmId', $alarmId)
                    ->where('uiTagId', $alarmData->tagId)
                    ->where('alarmType', $alarmType)
                    ->update([
                        'resolveTime' => date('Y-m-d H:i:s'),
                        'isResolved' => 1,
                    ]);
                return $this->respond(['status' => true, 'message' => 'Alarm resolved successfully']);
            }
        } else {
            // Log the alarm data if it's not a resolve action
            $this->db->table('AlarmLog')->insert([
                'alarmId' => $alarmId,
                'tenantId' => 1, // Assuming tenantId is always 1 for this example
                'uiTagId' => $alarmData->tagId,
                'alarmType' => $alarmType,
                'triggerValue' => $alarmData->value,
                'triggerTime' => date('Y-m-d H:i:s'),
                'isResolved' => 0, // Default to not resolved
            ]);

            $logId = $this->db->insertID();
            assignSerialNumber(1, "AlarmLog", "logId", $logId);

            return $this->respond(['status' => true, 'message' => 'Alarm logged successfully']);
        }

        // Log the received alarm data
        log_message('info', 'Received alarm data: ' . json_encode($alarmData));

        // Here you can process the alarm data as needed, e.g., save to database or trigger notifications

        return $this->respond(['status' => true, 'message' => 'Alarm data processed successfully']);
    }
}
