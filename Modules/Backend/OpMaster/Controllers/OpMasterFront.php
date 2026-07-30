<?php

namespace Modules\Backend\OpMaster\Controllers;

use App\Controllers\ApiBaseController;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\UserPermissionLib;
use App\Libraries\SelfRefDataLib;
use Modules\Backend\PlcMaster\Models\PlcMasterModel;


// use OpenApi\Annotations as OA;
class OpMasterFront extends ApiBaseController
{
    use ResponseTrait;
    protected $PlcMasterModel;

    public function __construct()
    {
        $this->PlcMasterModel = new PlcMasterModel();
    }

    public function initPlc($plcId)
    {
        $plcId = getKey($plcId, "plc");

        if ($plcId === null) {
            return $this->respond(['status' => false, 'message' => 'PLC ID is required'], 400);
        }

        $plc = $this->PlcMasterModel->find($plcId);

        $payload = [
            'plcHost' => $plc->ipAddress,
            'plcPort' => $plc->port,
            'plcProtocol' => $plc->protocol,
        ];

        $data = nodejsApi("post", "/api/initPlc", $payload);

        // prepare and send all tags to nodejs for continues read operations.
        $tags = $this->db->table("plcTagMaster")->where([
            "tenantId" => $this->user->tenantId,
            "plcId" => $plcId,
        ])->get()->getResult();

        $myTagsToRead = [];
        foreach ($tags as $tag) {
            $myTagsToRead[$tag->tagId] = $tag->tagAddress;
        }

        // sleep(1); // Wait for the Node.js service to initialize

        $data = nodejsApi(
            "post",
            "/api/readTagsContinue",
            [
                "tags" => $myTagsToRead
            ]
        );


        return $this->respond(['status' => true, 'message' => 'PLC initialisation command sent'], 200);
    }

    public function syncTags($plcId)
    {
        $plcId = getKey($plcId, "plc");

        if ($plcId === null) {
            return $this->respond(['status' => false, 'message' => 'PLC ID is required'], 400);
        }

        $plc = $this->PlcMasterModel->find($plcId);

        if (!$plc) {
            return $this->respond(['status' => false, 'message' => 'PLC not found'], 404);
        }


        $data = nodejsApi("get", "/api/syncTags");
        $plcTagMaster = [];
        if (@$data['json']['success']) {
            foreach ($data['json']["tags"] as $tag) {

                $tagId = $this->db->table("plcTagMaster")->Select("tagId")->where([
                    "tenantId" => $this->user->tenantId,
                    "tagAddress" => $tag["tagAddress"],
                ])->get()->getRow("tagId");

                $plcTagMaster[] = [
                    "tagId" => $tagId ? $tagId : null,
                    "tenantId" => $this->user->tenantId,
                    "tagName" => $tag["tagName"],
                    "tagAddress" => $tag["tagAddress"],
                    "dataType" => $tag["dataType"],
                    "registerType" => "holding",
                    "readWrite" => "readWrite",
                    "createdAt" => date("Y-m-d H:i:s"),
                    "updatedAt" => date("Y-m-d H:i:s"),
                    "createdBy" => $this->user->userId,
                    "updatedBy" => $this->user->userId,
                    "plcId" => $plcId,
                ];
            }

            $result = $this->syncChildTable("plcTagMaster", "tagId", "plcId", $plcId, $plcTagMaster);

            $message = "Deleted Rows: " . $result["deletedRows"] . ", Updated Rows: " . $result["updatedRows"] . ", Inserted Rows: " . $result["insertedRows"] . ", No Change: " . $result["noChange"];
            return $this->respond(['status' => true, 'message' => $message], 200);
        } else {
            $message = json_encode($data);
            return $this->respond(['status' => false, 'message' => $message], 200);
        }
    }

    public function writeTags()
    {
        $tagMap = $this->request->getJSON(true);

        if (empty($tagMap)) {
            return $this->respond(['status' => false, 'message' => 'No tag values provided'], 400);
        }

        $myTags = [];
        $auditLogData = [];

        foreach ($tagMap as $tagId => $value) {
            $tag = $this->db->table("plcTagMaster")->where([
                "tenantId" => $this->user->tenantId,
                "tagId" => $tagId,
            ])->get()->getRow();


            $tag = $this->db->query("SELECT ut.uiTagId, pt.tagAddress, pt.dataType, ut.minValue, ut.maxValue FROM uiTagMaster ut LEFT JOIN plcTagMaster pt ON ut.tagId = pt.tagId WHERE ut.tenantId = ? AND ut.uiTagId = ?", [
                $this->user->tenantId,
                $tagId
            ])->getRow();

            if (!$tag) {
                return $this->respond(['status' => false, 'message' => 'Tag not found: ' . $tagId], 404);
            }

            // Validate value against minValue and maxValue
            // Only validate if minValue and maxValue are not both null or both zero
            $minSet = isset($tag->minValue) && $tag->minValue !== null && (int)$tag->minValue !== 0;
            $maxSet = isset($tag->maxValue) && $tag->maxValue !== null && (int)$tag->maxValue !== 0;

            if ($minSet && $value < $tag->minValue) {
                return $this->respond(['status' => false, 'errorMessage' => 'Value is below the minimum allowed value: ' . $tag->minValue]);
            }
            if ($maxSet && $value > $tag->maxValue) {
                return $this->respond(['status' => false, 'errorMessage' => 'Value exceeds the maximum allowed value: ' . $tag->maxValue]);
            }

            $myTags[$tag->tagAddress] = [
                "value" => $value,
                "dataType" => $tag->dataType,
            ];

            // Prepare audit log data
            $auditLogData[] = [
                'tagId' => $tagId,
                'value' => (string)$value,
                'userId' => $this->user->userId,
                'writeTime' => date('Y-m-d H:i:s')
            ];
        }

        // Log all write requests to audit table
        if (!empty($auditLogData)) {
            $this->db->table('tagWriteHistory')->insertBatch($auditLogData);
        }


        // Call the Node.js API to write tags
        $response = nodejsApi("post", "/api/writeTags", ["tags" => $myTags]);
        if (@$response['json']['success']) {
            return $this->respond(['status' => true, 'message' => ''], 200);
        } else {
            return $this->respond(['status' => false, 'message' => 'Failed to write tags', 'rawResponse' => $response], 500);
        }
    }

    public function manageNodeApp()
    {
        $input = $this->getInputData();
        $json = $input['jsonInput'];

        $action = $json['action'] ?? 'status';
        $lines = 100;
        $service = 'scada-node.service';

        $result = [
            'action' => $action,
            'status' => false,
            'output' => null
        ];

        switch ($action) {
            case 'start':
                $result['output'] = shell_exec("sudo systemctl start $service 2>&1");
                $result['status'] = true;
                $result['message'] = 'Started successfully';
                break;

            case 'stop':
                $result['output'] = shell_exec("sudo systemctl stop $service 2>&1");
                $result['status'] = true;
                $result['message'] = 'Stopped successfully';
                break;

            case 'restart':
                $result['output'] = shell_exec("sudo systemctl restart $service 2>&1");
                sleep(1);
                $status = trim(shell_exec("sudo systemctl is-active $service"));
                $result['status'] = $status === 'active' ? true : false;
                $result['message'] = $status === 'active' ? 'Restarted successfully' : 'Failed to restart';
                break;

            case 'logs':
                $logs = shell_exec("sudo journalctl -u $service -n $lines --no-pager 2>&1");
                $result['status'] = true;
                $result['logs'] = nl2br(trim($logs));
                break;

            case 'status':
            default:
                $status = trim(shell_exec("sudo systemctl is-active $service 2>&1"));
                $result['status'] = $status === 'active' ? true : false;
                $result['message'] = $status === 'active' ? 'Service is active' : 'Service is inactive';
                break;
        }

        return $this->response->setJSON($result);
    }

    public function pushTagToCi4()
    {
        $input = $this->getInputData();
        $json = $input['jsonInput'];

        if (!isset($json['tagId']) || !array_key_exists('value', $json)) {
            return $this->respond(['status' => false, 'message' => 'Tag ID and value are required'], 400);
        }

        $tagId = $json['tagId'];
        $value = $json['value'];

        updateTagValuesToDb([$tagId => $value]);

        return $this->respond(['status' => true, 'message' => ''], 200);
    }

    public function activeAlarms()
    {

        $alarms = $this->db->query("SELECT AL.*, AC.message, AC.solution, UT.tagName FROM AlarmLog AL 
                        LEFT JOIN AlarmConfig AC ON AL.alarmId = AC.alarmId
                        LEFT JOIN uiTagMaster UT ON AC.uiTagId = UT.uiTagId
                        WHERE UT.isActive = 1 AND AC.isActive = 1 AND AL.isResolved = 0 AND AL.tenantId = " . $this->user->tenantId . "
                        ")->getResult();

        return $this->respond(['status' => true, 'data' => $alarms], 200);
    }

    public function allTagDetails()
    {
        $tags = $this->db->query("SELECT UT.uiTagId, UT.tagName,UT.maxValue,UT.minValue,PT.dataType FROM uiTagMaster UT 
                                    LEFT JOIN plcTagMaster PT ON UT.tagId = PT.tagId
                                    WHERE UT.isActive=1 AND UT.tenantId = " . $this->user->tenantId)->getResult();

        $myTags = [];
        foreach ($tags as $tag) {
            $myTags[$tag->uiTagId] = $tag;
        }
        return $this->respond(['status' => true, 'data' => $myTags], 200);
    }
}
